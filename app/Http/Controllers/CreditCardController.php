<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreditCard\StoreCreditCardRequest;
use App\Models\CreditCard;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Client\ClientRepository;
use App\Repositories\Client\CreditCardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CreditCardController extends Controller
{
    private ClientRepository $client;
    private CreditCardRepository $creditCard;

    public function __construct(
        CreditCardRepository $creditCard,
        ClientRepository $client
    )
    {
        $this->client = $client;
        $this->creditCard = $creditCard;
    }

    /**
     * Display a listing of the resource.
     */
    public function index($hash): JsonResponse
    {
        try {
            $client = $this->client->client()
                ->with('creditCards:cc_last_4_digits,cc_type')
                ->where('hash', $hash)->first();
            return response()->json([
                'success' => true,
                'client_credit_cards' => $client->creditCards ?? []
            ]);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreditCardRequest $request): JsonResponse
    {
        try {
            $creditCard = $this->creditCard->storeClientCreditCard($request->all());
            return response()->json($creditCard);
        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
