<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\ServiceRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentService
{
    public function createPayment(ServiceRequest $request, string $paymentMethod)
    {
        if ($request->payment()->exists()) {
            throw new \Exception('Payment already exists for this request');
        }

        return DB::transaction(function () use ($request, $paymentMethod) {
            $payment = Payment::create([
                'request_id' => $request->request_id,
                'amount' => $request->total_price,
                'payment_method' => $paymentMethod,
                'transaction_id' => $this->generateTransactionId(),
                'status' => 'pending'
            ]);

            return $payment;
        });
    }

    public function processPayment(Payment $payment)
    {
        // Here you would integrate with a real payment gateway
        // For now, we'll simulate a payment process
        try {
            DB::beginTransaction();

            // Simulate payment processing
            $success = $this->simulatePaymentProcess();

            $payment->update([
                'status' => $success ? 'completed' : 'failed'
            ]);

            if ($success) {
                $payment->serviceRequest->update(['status' => 'completed']);
            }

            DB::commit();
            return $payment;

        } catch (\Exception $e) {
            DB::rollBack();
            $payment->update(['status' => 'failed']);
            throw $e;
        }
    }

    protected function simulatePaymentProcess(): bool
    {
        // Simulate payment processing with 90% success rate
        return rand(1, 100) <= 90;
    }

    protected function generateTransactionId(): string
    {
        return Str::uuid()->toString();
    }

    public function getPaymentStatus(string $transactionId)
    {
        $payment = Payment::where('transaction_id', $transactionId)->firstOrFail();
        return [
            'status' => $payment->status,
            'amount' => $payment->amount,
            'payment_method' => $payment->payment_method,
            'created_at' => $payment->created_at,
            'updated_at' => $payment->updated_at,
        ];
    }
} 