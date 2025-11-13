@component('mail::message')
# ¡Gracias por tu compra, {{ $factura->cliente->nombre }}!

Te adjuntamos tu factura número **{{ $factura->numero_factura }}** en formato PDF.

Si tienes preguntas o necesitas ayuda, no dudes en contactarnos.

@component('mail::button', ['url' => route('facturas.show', $factura)])
Ver Factura
@endcomponent

Gracias,<br>
**{{ config('app.name') }}**
@endcomponent
