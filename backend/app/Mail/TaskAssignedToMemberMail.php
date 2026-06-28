<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TaskAssignedToMemberMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $memberName,
        public string $taskTitle,
        public string $taskDueDate
    ) {}

    public function build()
    {
        return $this->subject('Eine neue Aufgabe wurde dir zugewiesen')
            ->view('task-assigned');
    }
}