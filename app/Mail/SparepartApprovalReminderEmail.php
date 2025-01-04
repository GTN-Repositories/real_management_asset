<?php

namespace App\Mail;

use App\Models\ItemStock;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SparepartApprovalReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $request;

    public function __construct(ItemStock $request)
    {
        $this->request = $request;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        $content = view('mail.sparepart_approval_reminder', [
            'itemName' => $this->request->item->name ?? 'Item Tidak Diketahui',
            'quantity' => $this->request->stock,
            'requestDate' => $this->request->created_at,
        ])->render();
    
        // Create notification with the rendered content
        Notification::create([
            'title' => 'Pengingat Persetujuan Permintaan Sparepart',
            'body' => $content,
            'type' => 'email',
        ]);

        return $this->subject('Pengingat Persetujuan Permintaan Sparepart')
                    ->view('mail.sparepart_approval_reminder')
                    ->with([
                        'itemName' => $this->request->item->name ?? 'Item Tidak Diketahui',
                        'quantity' => $this->request->stock,
                        'requestDate' => $this->request->created_at,
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
