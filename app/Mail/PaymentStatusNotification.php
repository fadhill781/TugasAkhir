<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentStatusNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $pesanan;
    public $kategori;
    public $paketHari;
    public $status;

    /**
     * Create a new message instance.
     */
    public function __construct($pesanan, $kategori, $paketHari, $status)
    {
        $this->pesanan = $pesanan;
        $this->kategori = $kategori;
        $this->paketHari = $paketHari;
        $this->status = $status;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = 'Status Pembayaran - ' . $this->pesanan->order_id;
        
        if ($this->status === 'confirmed') {
            $subject = 'Pembayaran Berhasil - ' . $this->pesanan->order_id;
        } elseif ($this->status === 'cancelled') {
            $subject = 'Pembayaran Dibatalkan - ' . $this->pesanan->order_id;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-status',
        );
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
