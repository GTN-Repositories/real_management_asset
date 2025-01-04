<?php

namespace App\Mail;

use App\Models\Notification;
use App\Models\PettyCash;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PettyCashReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $pettyCash;

    public function __construct(PettyCash $pettyCash)
    {
        $this->pettyCash = $pettyCash;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        $content = view('mail.petty_cash_reminder', [
            'projectName' => $this->pettyCash->project->name ?? 'Proyek Tidak Diketahui',
            'amount' => $this->pettyCash->amount,
            'requestDate' => $this->pettyCash->request_date,
        ])->render();
    
        // Create notification with the rendered content
        Notification::create([
            'title' => 'Pengingat Permintaan Petty Cash',
            'body' => $content,
            'type' => 'email',
        ]);
        
        return $this->subject('Pengingat Permintaan Petty Cash')
                    ->view('mail.petty_cash_reminder')
                    ->with([
                        'projectName' => $this->pettyCash->project->name ?? 'Proyek Tidak Diketahui',
                        'amount' => $this->pettyCash->amount,
                        'requestDate' => $this->pettyCash->request_date,
                    ]);
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
