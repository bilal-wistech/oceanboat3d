<?php
namespace Botble\Ecommerce\Notifications;

use URL;
use EmailHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Support\HtmlString;
use Botble\Ecommerce\Models\Customer;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use NaeemAwan\PredefinedLists\Models\PredefinedList;

class BoatCreated extends Notification
{
    use Queueable;

    protected $boatData;

    public function __construct($boatData)
    {
        $this->boatData = $boatData;
    }

    public function via($notifiable)
    {
        return ['mail']; // Use 'mail' for email notifications
    }

    public function toMail($notifiable)
    {
        // dd($this->boatData);
        $boat = PredefinedList::findOrFail($this->boatData['boat_id']);
        $customer = Customer::findOrFail(auth('customer')->id());
        return (new MailMessage)
            ->subject('New Boat Created')
            ->view(
                'plugins/ecommerce::emails.admin_saved_boat_email', // The view file
                [
                    'boat_id' => $this->boatData['boat_id'],
                    'boat_title' => $boat->ltitle,
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'customer_phone' => $customer->phone,
                    'total_price' => $this->boatData['total_price']
                ]
            );
    }

    public function toArray($notifiable)
    {
        $boat = PredefinedList::findOrFail($this->boatData['boat_id']);
        $customer = Customer::findOrFail($this->boatData['user_id']);
        return [
            'boat_id' => $this->boatData['boat_id'],
            'boat_title' => $boat->ltitle,
            'customer_name' => $customer->name,
            'customer_email' => $customer->email,
            'customer_phone' => $customer->phone,
            'total_price' => $this->boatData['total_price']
        ];
    }
}