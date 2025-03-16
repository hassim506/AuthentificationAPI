<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayTechService;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $payTechService;

    public function __construct(PayTechService $payTechService)
    {
        $this->payTechService = $payTechService;
    }

    public function pay(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:100']);

        $reference = 'CMD-' . time();
        $callback_url = route('paytech.callback');
        $success_url = 'https://votre-site.com/success';
        $cancel_url = 'https://votre-site.com/cancel';

        $response = $this->payTechService->initiatePayment(
            $request->amount, $reference, $callback_url, $success_url, $cancel_url
        );

        if (!empty($response['redirect_url'])) {
            Transaction::create([
                'user_id' => Auth::id(),
                'method' => 'card',
                'amount' => $request->amount,
                'status' => 'pending',
                'transaction_id' => $reference
            ]);

            return response()->json(['payment_url' => $response['redirect_url']]);
        }

        return response()->json(['error' => 'Échec du paiement'], 500);
    }

    public function paytechCallback(Request $request)
    {
        $transaction = Transaction::where('transaction_id', $request->ref_command)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction non trouvée'], 404);
        }

        if ($request->status === 'completed') {
            $transaction->update(['status' => 'success']);
        } else {
            $transaction->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'Paiement mis à jour']);
    }

    public function listTransactions()
    {
        return response()->json(Transaction::latest()->get());
    }
}

