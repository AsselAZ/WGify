<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApartmentInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $inviteCode,
        public string $apartmentName
    ) {}

    public function build()
    {
        return $this->subject('Einladung zur WG ' . $this->apartmentName)
            ->view('apartment-invite');
    }
}