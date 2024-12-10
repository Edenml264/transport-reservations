<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentController extends Controller
{
    protected $paypal;

    public function __construct()
    {
        $this->paypal = new PayPalClient;
        $this->paypal->setApiCredentials(config('paypal'));
    }

    public function create(Reservation $reservation)
    {
        if ($reservation->payment) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Esta reservación ya tiene un pago registrado.');
        }

        try {
            $this->paypal->getAccessToken();
            
            $order = $this->paypal->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => 'MXN',
                            'value' => $reservation->total_price
                        ],
                        'description' => 'Reservación #' . $reservation->id,
                    ]
                ],
                'application_context' => [
                    'return_url' => route('payment.success') . '?reservation=' . $reservation->id,
                    'cancel_url' => route('payment.cancel') . '?reservation=' . $reservation->id,
                ]
            ]);

            return redirect($order['links'][1]['href']);

        } catch (\Exception $e) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation);
        
        try {
            $this->paypal->getAccessToken();
            $result = $this->paypal->capturePaymentOrder($request->token);

            if ($result['status'] === 'COMPLETED') {
                // Crear registro de pago
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'amount' => $reservation->total_price,
                    'payment_method' => 'paypal',
                    'paypal_transaction_id' => $result['id'],
                    'status' => 'completed',
                    'payment_date' => now(),
                ]);

                // Actualizar estado de la reservación
                $reservation->update(['status' => 'confirmed']);

                return redirect()->route('reservations.show', $reservation)
                    ->with('success', 'Pago procesado correctamente.');
            }

        } catch (\Exception $e) {
            return redirect()->route('reservations.show', $reservation)
                ->with('error', 'Error al procesar el pago: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request)
    {
        $reservation = Reservation::findOrFail($request->reservation);
        
        return redirect()->route('reservations.show', $reservation)
            ->with('error', 'El pago ha sido cancelado.');
    }

    public function handleWebhook(Request $request)
    {
        // Procesar webhooks de PayPal
        // Implementar lógica de verificación y procesamiento
        logger()->info('PayPal Webhook:', $request->all());
        
        return response()->json(['status' => 'OK']);
    }

    public function show(Payment $payment)
    {
        $this->authorize('view', $payment->reservation);
        
        $payment->load('reservation.schedule.route');
        return view('payments.show', compact('payment'));
    }
}
