<?php

namespace Botble\Ecommerce\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SavedBoatsEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $variables;

    /**
     * Create a new message instance.
     *
     * @param array $variables
     * @return void
     */
    public function __construct(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('plugins/ecommerce::emails.saved_boats')
            ->with($this->variables)
            ->subject('Your Boat Booking Details');
    }
}