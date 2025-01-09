<?php
namespace Botble\Ecommerce\Commands;

use Throwable;
use Illuminate\Console\Command;
use Botble\Base\Helpers\BaseHelper;
use Illuminate\Support\Facades\Mail;
use Botble\Base\Supports\EmailHandler;
use Botble\Ecommerce\Mail\SavedBoatsEmail;
use Symfony\Component\Console\Attribute\AsCommand;
use NaeemAwan\PredefinedLists\Repositories\Interfaces\BoatEnquiryInterface;

#[AsCommand('cms:saved-boats:email', 'Send emails saved boats')]
class SendSavedBoatsEmailCommand extends Command
{
    protected BoatEnquiryInterface $boatenquiryRepository;
    protected EmailHandler $emailHandler;
    public function __construct(BoatEnquiryInterface $boatenquiryRepository, EmailHandler $emailHandler)
    {
        parent::__construct();

        $this->boatenquiryRepository = $boatenquiryRepository;
        $this->emailHandler = $emailHandler;
    }

    public function handle()
    {
        $helper = app(BaseHelper::class);
        $boat_enquiries = $this->boatenquiryRepository->getModel()
            ->with(['boat', 'customer', 'details'])
            ->where('is_finished', 0)
            ->get();

        $count = 0;

        foreach ($boat_enquiries as $boat_enquiry) {
            $email = $boat_enquiry->customer->email;
            if (!$email) {
                continue;
            }

            $variables = [
                'customer_name' => $helper->clean($boat_enquiry->customer->name),
                'boat_id' => $boat_enquiry->boat->id,
                'boat_title' => $boat_enquiry->boat->ltitle,
                'total_price' => format_price($boat_enquiry->total_price),
                'vat_total' => format_price($boat_enquiry->vat_total),
            ];

            try {
                \Log::info('Preparing to send email to: ' . $email);
                \Log::info('Variables:', $variables);

                Mail::to($email)->send(new SavedBoatsEmail($variables));

                $count++;
                \Log::info('Email sent successfully to: ' . $email);
            } catch (Throwable $exception) {
                \Log::error('Failed to send email: ' . $exception->getMessage());
                \Log::error('Stack trace: ' . $exception->getTraceAsString());
            }
        }

        $this->info('Sent ' . $count . ' email' . ($count != 1 ? 's' : '') . ' successfully!');

        return self::SUCCESS;
    }
}
