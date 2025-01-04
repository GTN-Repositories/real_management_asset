<?php

namespace App\Mail;

use App\Models\Ipb;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FuelStockAddedEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $fuelStock;

    public function __construct(Ipb $fuelStock)
    {
        $this->fuelStock = $fuelStock;
    }

    public function build()
    {
        $content = view('mail.fuel_stock_added', [
            'projectName' => $this->fuelStock->management_project->name ?? 'Proyek Tidak Diketahui',
            'qty' => $this->fuelStock->issued_liter,
            'dateAdded' => $this->fuelStock->created_at,
        ])->render();
    
        // Create notification with the rendered content
        Notification::create([
            'title' => 'Penambahan Stok Bahan Bakar',
            'body' => $content,
            'type' => 'email',
        ]);
        
        return $this->subject('Penambahan Stok Bahan Bakar')
                    ->view('mail.fuel_stock_added')
                    ->with([
                        'projectName' => $this->fuelStock->management_project->name ?? 'Proyek Tidak Diketahui',
                        'qty' => $this->fuelStock->issued_liter,
                        'dateAdded' => $this->fuelStock->created_at,
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
