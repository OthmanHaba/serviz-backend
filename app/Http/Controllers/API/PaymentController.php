<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createPayment(Request $request, ServiceRequest $serviceRequest)
    {
        // Verify user owns this request
        if (auth()->id() !== $serviceRequest->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $payment = $this->paymentService->createPayment($serviceRequest, $request->payment_method);
            return response()->json($payment, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function processPayment(Payment $payment)
    {
        // Verify user owns this payment's request
        if (auth()->id() !== $payment->serviceRequest->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        try {
            $result = $this->paymentService->processPayment($payment);
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getStatus(string $transactionId)
    {
        try {
            $status = $this->paymentService->getPaymentStatus($transactionId);
            return response()->json($status);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Payment not found'], 404);
        }
    }
} 