<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class PayTechService
{
    protected $api_key;
    protected $secret_key;
    protected $mode;
    protected $base_url;

    public function __construct()
    {
        $this->api_key = env('PAYTECH_API_KEY');
        $this->secret_key = env('PAYTECH_SECRET_KEY');
        $this->mode = env('PAYTECH_MODE');
        $this->base_url = $this->mode === 'live'
            ? 'https://api.paytech.sn'
            : '';
    }

    public function initiatePayment($amount, $reference, $callback_url, $success_url, $cancel_url)
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post("{$this->base_url}/payment/request-payment", [
            'item_name' => 'Paiement sur mon site',
            'item_price' => $amount,
            'command_name' => $reference,
            'currency' => 'XOF',
            'ref_command' => $reference,
            'ipn_url' => $callback_url,
            'success_url' => $success_url,
            'cancel_url' => $cancel_url,
            'api_key' => $this->api_key,
            'secret' => $this->secret_key
        ]);

        return $response->json();
    }
}
