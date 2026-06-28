<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberRemovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $memberName,
        public string $apartmentName
    ) {}

    public function build()
    {
        return $this->subject('Du wurdest aus der WG ' . $this->apartmentName . ' entfernt')
            ->view('member-removed');
    }
}