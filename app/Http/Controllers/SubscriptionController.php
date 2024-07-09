<?php

namespace App\Http\Controllers;

use App\Http\Resources\Subscription\SubscriptionResource;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Subscription\SubscriptionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    protected SubscriptionRepository $subscription;
    public function __construct(SubscriptionRepository $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $data = $this->subscription->subscription()
                ->with(
                    'company:id,name',
                    'client:id,company_name,company_email,company_address,company_phone',
                )->latest()->get();

            return response()->json([
                'success' => true,
                'subscriptions' => $data,
                'total' => $data->count(),
            ]);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $data = $this->subscription->storeSubscription($request);
            return response()->json($data, $data['success'] ? 200 : 500);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($hash): JsonResponse
    {
        try {
            $data = $this->subscription->subscription()->where('hash', $hash)
                ->with(
                    'company',
                    'client:company_name,company_email,company_address,company_phone,id',
                    'charges',
                )->first();

            return response()->json([
                'success' => true,
                'subscription' => new SubscriptionResource($data),
            ]);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $hash)
    {
        try {
            $data = $this->subscription->updateSubscription($request, $hash);
            return response()->json($data, $data['success'] ? 200 : 500);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $hash)
    {
        try {
            $this->subscription->deleteSubscription($hash);
            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);
        }catch(\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $data = $this->subscription->searchSubscriptions($request);
            return response()->json($data, $data['success'] ? 200 : 500);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }
}
