<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayTechService;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{


    public function pay(Request $request)
    {
        // Validation du montant
        $request->validate([
            'amount' => 'required|numeric|min:100',
        ]);

        // Vérification de l'utilisateur authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Utilisateur non authentifié'], 401);
        }

        // Création d'une référence unique pour la commande
        $reference = 'CMD-' . time();

        // Récupérer les URLs depuis .env
        $callback_url = env('PAYMENT_CALLBACK_URL');
        $success_url = env('PAYMENT_SUCCESS_URL', 'https://127.0.0.1:8000/success');
        $cancel_url = env('PAYMENT_CANCEL_URL', 'https://127.0.0.1:8000/cancel');

        try {
            // Construire les données pour la requête à PayTech
            $postData = [
                'item_name'    => 'Commande ' . $reference,
                'item_price'   => $request->amount,
                'currency'     => 'xof',
                'ref_command'  => $reference,
                'command_name' => 'Paiement Commande',
                'env'          => env('PAYMENT_ENV', 'production'),
                'success_url'  => $success_url,
                'ipn_url'      => $callback_url,
                'cancel_url'   => $cancel_url,
                'custom_field' => 'Custom Data',  // Vous pouvez personnaliser ce champ
            ];

            // Faire la requête à PayTech
            $response = Http::withHeaders([
                'API_KEY' => env('PAYTECH_API_KEY'),
                'API_SECRET' => env('PAYTECH_API_SECRET'),
            ])
                ->asForm()  // Envoi des données sous forme de formulaire
                ->post('https://paytech.sn/api/payment/request-payment', $postData);

            // Vérification de la réponse de PayTech
            if ($response->successful()) {
                $responseBody = $response->json();

                // Vérifier si PayTech renvoie une URL de paiement
                if (isset($responseBody['redirect_url'])) {
                    // Enregistrer la transaction en base de données
                    Transaction::create([
                        'user_id' => Auth::id(),
                        'method' => 'card',
                        'amount' => $request->amount,
                        'status' => 'pending',
                        'transaction_id' => $reference
                    ]);

                    // Retourner l'URL de redirection pour le paiement
                    return response()->json(['payment_url' => $responseBody['redirect_url']]);
                } else {
                    // Si aucune URL de redirection n'est retournée, afficher l'erreur
                    return response()->json(['error' => 'Échec de la demande de paiement', 'details' => $responseBody], 500);
                }
            } else {
                // Si la requête à PayTech échoue, renvoyer l'erreur
                return response()->json(['error' => 'Erreur de communication avec PayTech', 'details' => $response->body()], 500);
            }
        } catch (\Exception $e) {
            // Gestion des erreurs générales
            return response()->json(['error' => 'Une erreur est survenue', 'details' => $e->getMessage()], 500);
        }
    }

    public function paytechCallback(Request $request)
    {
        // Recherche de la transaction basée sur la référence de commande
        $transaction = Transaction::where('transaction_id', $request->ref_command)->first();

        if (!$transaction) {
            return response()->json(['error' => 'Transaction non trouvée'], 404);
        }

        // Mise à jour du statut de la transaction en fonction du résultat du paiement
        try {
            $status = $request->status === 'completed' ? 'success' : 'failed';
            $transaction->update(['status' => $status]);

            return response()->json(['message' => 'Paiement mis à jour', 'status' => $status]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du statut du paiement: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la mise à jour du statut'], 500);
        }
    }

    public function listTransactions()
    {
        $transactions = Transaction::latest()->get();
        return response()->json($transactions);
    }
}

