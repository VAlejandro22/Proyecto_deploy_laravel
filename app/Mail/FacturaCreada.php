<?php

namespace App\Mail;

use App\Models\Factura;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaCreada extends Mailable
{
    use Queueable, SerializesModels;

    public $factura;

    public function __construct(Factura $factura)
    {
        $this->factura = $factura;
    }

    public function build()
    {
        $pdf = Pdf::loadView('facturas.pdf', ['factura' => $this->factura]);

        return $this->subject('Tu Factura - FacturaPro')
                    ->markdown('emails.factura')
                    ->attachData($pdf->output(), "factura-{$this->factura->numero_factura}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
    }
}
