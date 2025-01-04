<?php

namespace App\Mail;

use App\Models\Asset;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChangeStatusAssetEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $asset;

    public function __construct(Asset $asset)
    {
        $this->asset = $asset;
    }

    public function build()
    {
        $content = view('mail.change_status_asset', [
            'assetName' => $this->asset->name,
            'newStatus' => $this->asset->status,
        ])->render();
    
        // Create notification with the rendered content
        Notification::create([
            'title' => 'Perubahan Status Asset',
            'body' => $content,
            'type' => 'email',
        ]);
        
        return $this->subject('Perubahan Status Asset')
                    ->view('mail.change_status_asset')
                    ->with([
                        'assetName' => $this->asset->name,
                        'newStatus' => $this->asset->status,
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
