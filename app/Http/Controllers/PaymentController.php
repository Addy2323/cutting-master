<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function showPaymentForm(Appointment $appointment)
    {
        $paymentMethods = PaymentMethod::where('is_active', true)->get();
        return view('frontend.booking.payment', compact('appointment', 'paymentMethods'));
    }

    public function processPayment(Request $request)
    {
        $appointment = Appointment::findOrFail($request->appointment_id);
        
        // Update appointment status
        $appointment->status = 'Confirmed';
        $appointment->save();

        // Redirect to success page with appointment ID
        return redirect()->route('appointment.success', ['appointment_id' => $appointment->id]);
    }

    private function handleCardPayment($appointment)
    {
        $paymentIntent = PaymentIntent::create([
            'amount' => $appointment->amount * 100,
            'currency' => 'tzs',
            'payment_method_types' => ['card'],
            'metadata' => [
                'appointment_id' => $appointment->id
            ]
        ]);

        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_id' => $paymentIntent->id,
            'payment_method' => 'card',
            'amount' => $appointment->amount,
            'currency' => 'TZS',
            'status' => 'pending',
            'payment_details' => [
                'client_secret' => $paymentIntent->client_secret
            ]
        ]);

        return response()->json([
            'success' => true,
            'client_secret' => $paymentIntent->client_secret,
            'payment_id' => $payment->id
        ]);
    }

    private function initiateMpesaPayment(Request $request)
    {
        $appointment = Appointment::findOrFail($request->appointment_id);
        
        // Create payment record
        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_id' => 'MPESA-' . strtoupper(uniqid()),
            'payment_method' => 'mpesa',
            'amount' => $appointment->amount,
            'currency' => 'TZS',
            'status' => 'pending',
            'payment_details' => [
                'phone' => $request->phone
            ]
        ]);

        // Initialize M-Pesa payment
        $response = $this->initiateMpesaSTKPush(
            $request->phone,
            $appointment->amount,
            $appointment->id
        );

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'redirect_url' => $response['redirect_url']
        ]);
    }

    private function initiateTigoPayment(Request $request)
    {
        $appointment = Appointment::findOrFail($request->appointment_id);
        
        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_id' => 'TIGO-' . strtoupper(uniqid()),
            'payment_method' => 'tigo',
            'amount' => $appointment->amount,
            'currency' => 'TZS',
            'status' => 'pending',
            'payment_details' => [
                'phone' => $request->phone
            ]
        ]);

        $response = $this->initiateTigoPesaPayment(
            $request->phone,
            $appointment->amount,
            $appointment->id
        );

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'redirect_url' => $response['redirect_url']
        ]);
    }

    private function initiateAirtelPayment(Request $request)
    {
        $appointment = Appointment::findOrFail($request->appointment_id);
        
        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_id' => 'AIRTEL-' . strtoupper(uniqid()),
            'payment_method' => 'airtel',
            'amount' => $appointment->amount,
            'currency' => 'TZS',
            'status' => 'pending',
            'payment_details' => [
                'phone' => $request->phone
            ]
        ]);

        // Implement Airtel Money payment logic here
        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'redirect_url' => 'https://airtel.com/checkout'
        ]);
    }

    private function initiateYaxMixPayment(Request $request)
    {
        $appointment = Appointment::findOrFail($request->appointment_id);
        
        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_id' => 'YAXMIX-' . strtoupper(uniqid()),
            'payment_method' => 'yaxmix',
            'amount' => $appointment->amount,
            'currency' => 'TZS',
            'status' => 'pending',
            'payment_details' => [
                'phone' => $request->phone
            ]
        ]);

        // Implement Yax-Mix payment logic here
        return response()->json([
            'success' => true,
            'payment_id' => $payment->id,
            'redirect_url' => 'https://yaxmix.com/checkout'
        ]);
    }

    private function handleOtherPayment($appointment, $method)
    {
        $payment = Payment::create([
            'appointment_id' => $appointment->id,
            'payment_id' => strtoupper($method) . '-' . strtoupper(uniqid()),
            'payment_method' => $method,
            'amount' => $appointment->amount,
            'currency' => 'TZS',
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'payment_id' => $payment->id
        ]);
    }

    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('services.stripe.webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $appointment = Appointment::find($paymentIntent->metadata->appointment_id);
                
                if ($appointment) {
                    $appointment->update([
                        'payment_status' => 'completed',
                        'payment_method' => 'stripe',
                        'transaction_id' => $paymentIntent->id,
                        'amount' => $paymentIntent->amount / 100,
                        'payment_details' => json_encode($paymentIntent)
                    ]);
                }
                break;
            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $appointment = Appointment::find($paymentIntent->metadata->appointment_id);
                
                if ($appointment) {
                    $appointment->update([
                        'payment_status' => 'failed',
                        'payment_details' => json_encode($paymentIntent)
                    ]);
                }
                break;
        }

        return response()->json(['status' => 'success']);
    }

    public function confirmPayment(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:payments,id'
        ]);

        $payment = Payment::findOrFail($request->payment_id);
        $payment->status = 'completed';
        $payment->paid_at = now();
        $payment->save();

        $appointment = $payment->appointment;
        $appointment->status = 'Confirmed';
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Payment confirmed successfully'
        ]);
    }

    private function initiateMpesaSTKPush($phone, $amount, $appointmentId)
    {
        // Implement M-Pesa STK Push logic here
        // This is a placeholder - you'll need to implement the actual M-Pesa API integration
        return [
            'success' => true,
            'redirect_url' => 'https://mpesa.com/checkout'
        ];
    }

    private function initiateTigoPesaPayment($phone, $amount, $appointmentId)
    {
        // Implement Tigo Pesa payment logic here
        // This is a placeholder - you'll need to implement the actual Tigo Pesa API integration
        return [
            'success' => true,
            'redirect_url' => 'https://tigopesa.com/checkout'
        ];
    }

    private function processHalotelPayment($phone, $amount, $appointmentId)
    {
        // Implement Halotel payment logic here
        // This is a placeholder - you'll need to implement the actual Halotel API integration
        return [
            'success' => true,
            'redirect_url' => 'https://halotel.com/checkout'
        ];
    }

    public function createPaymentIntent(Request $request)
    {
        try {
            $appointment = Appointment::findOrFail($request->appointment_id);
            
            // Create a PaymentIntent with the order amount and currency
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $appointment->amount * 100, // Convert to cents
                'currency' => 'tzs',
                'metadata' => [
                    'appointment_id' => $appointment->id,
                    'booking_id' => $appointment->booking_id
                ]
            ]);

            return response()->json([
                'clientSecret' => $intent->client_secret
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 