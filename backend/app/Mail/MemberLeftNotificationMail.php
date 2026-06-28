<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberLeftNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $memberName,
        public string $apartmentName
    ) {}

    public function build()
    {
        return $this->subject('Mitglied verlässt die WG ' . $this->apartmentName)
            ->view('member-left');
    }
}