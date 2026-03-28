<?php

namespace App\Services;

use Illuminate\Http\Request;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Token;

class StripePaymentService
{
    public function charge(int $amountInCents, Request $request, string $description, array $metadata = []): array
    {
        if ($amountInCents <= 0) {
            throw new \InvalidArgumentException('El importe debe ser mayor que 0.');
        }

        $secret = config('services.stripe.secret');

        if (empty($secret)) {
            throw new \RuntimeException('Stripe no est\u00e1 configurado correctamente.');
        }

        Stripe::setApiKey($secret);

        $token = $request->input('token');

        if (empty($token)) {
            $token = $this->createTokenFromCardData($request);
        }

        $charge = Charge::create([
            'amount' => $amountInCents,
            'currency' => 'eur',
            'source' => $token,
            'description' => $description,
            'metadata' => $metadata,
        ]);

        return $charge->toArray();
    }

    private function createTokenFromCardData(Request $request): string
    {
        $cardNumber = preg_replace('/\s+/', '', (string) $request->input('card'));
        $expMonth = (string) $request->input('month');
        $expYear = (string) $request->input('year');
        $cvc = (string) $request->input('cvv');

        if (empty($cardNumber) || empty($expMonth) || empty($expYear) || empty($cvc)) {
            throw new \InvalidArgumentException('Faltan datos de tarjeta para procesar el cobro.');
        }

        if (strlen($expYear) === 2) {
            $expYear = '20'.$expYear;
        }

        $token = Token::create([
            'card' => [
                'number' => $cardNumber,
                'exp_month' => (int) $expMonth,
                'exp_year' => (int) $expYear,
                'cvc' => $cvc,
            ],
        ]);

        return $token->id;
    }
}
