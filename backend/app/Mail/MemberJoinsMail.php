<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberJoinsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $memberName,
        public string $apartmentName
    ) {}

    public function build()
    {
        return $this->subject('Neues Mitglied tritt der WG bei ' . $this->apartmentName)
            ->view('member-joins');
    }
}