<?php

namespace App\Repositories\Client;

use App\Http\Resources\CreditCard\CreditCardResource;
use App\Models\CreditCard;
use App\Repositories\Base\BaseRepository;

class CreditCardRepository
{
    public function creditCard(): CreditCard
    {
        return new CreditCard();
    }

    public function storeClientCreditCard($request): array
    {
        $inputs = $request->all();
        $inputs['hash'] = BaseRepository::randomCharacters(50, '0123456789ABSDEFGHIJKLMNOPQRSTUVWXYZ');
        $creditCard = $this->creditCard()->create($inputs);

        return [
            'success' => true,
            'credit_card' => new CreditCardResource($creditCard)
        ];
    }

    public function updateCreditCard($request, $hash): array
    {
        $creditCard = $this->creditCard()->where('hash', $hash)->first();
        $inputs = $request->all();
        $creditCard->update($inputs);

        return [
            'success' => true,
            'credit_card' => new CreditCardResource($creditCard)
        ];
    }

    public function deleteCreditCard($hash): array
    {
        $creditCard = $this->creditCard()->where('hash', $hash)->first();
        if (!$creditCard) {
            return [
                'success' => false,
                'error_message' => 'Client not found'
            ];
        }

        $creditCard->delete();

        return [
            'success' => true,
            'message' => 'Client deleted successfully'
        ];
    }
}
