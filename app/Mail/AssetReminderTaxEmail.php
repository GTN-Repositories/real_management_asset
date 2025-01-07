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

class AssetReminderTaxEmail extends Mailable
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

    /**
     * Get the message envelope.
     */
    public function build()
    {
        $content = view('mail.asset_tax_reminder', [
            'assetName' => $this->asset->name,
            'taxPeriod' => $this->asset->tax_period,
        ])->render();
    
        // Create notification with the rendered content
        Notification::create([
            'title' => 'Pengingat Perpanjangan Pajak',
            'body' => $content,
            'type' => 'email',
        ]);
        
        return $this->subject('Pengingat Perpanjangan Pajak')
                    ->view('mail.asset_tax_reminder')
                    ->with([
                        'assetName' => $this->asset->name,
                        'taxPeriod' => $this->asset->tax_period,
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
