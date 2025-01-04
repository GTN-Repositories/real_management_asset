<?php

namespace App\Mail;

use App\Models\Item;
use App\Models\ItemStock;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LowStockReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * Get the message envelope.
     */
    public function build()
    {
        $content = view('mail.low_stock_reminder', [
            'itemName' => $this->item->name ?? 'Item Tidak Diketahui',
            'quantity' => $this->item->stock ?? 0,
            'minimumStock' => $this->item->minimum_stock ?? 0,
        ])->render();
    
        // Create notification with the rendered content
        Notification::create([
            'title' => 'Pengingat Stok Barang Rendah',
            'body' => $content,
            'type' => 'email',
        ]);
        
        return $this->subject('Pengingat Stok Barang Rendah')
                    ->view('mail.low_stock_reminder')
                    ->with([
                        'itemName' => $this->item->name ?? 'Item Tidak Diketahui',
                        'quantity' => $this->item->stock ?? 0,
                        'minimumStock' => $this->item->minimum_stock ?? 0,
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
