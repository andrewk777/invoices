<?php

namespace App\Repositories\Subscription;

use App\Http\Resources\Subscription\SubscriptionIndexResource;
use App\Http\Resources\Subscription\SubscriptionResource;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class SubscriptionRepository
{
    public function subscription(): Subscription
    {
        return new Subscription();
    }

    public function subscriptionItem(): SubscriptionItem
    {
        return new SubscriptionItem();
    }

    public function storeSubscription($request): array
    {
        $inputs = $request->all();
        if($inputs['subscription']['charge_cc'] === true && empty($inputs['subscription']['credit_card_id'])){
            return [
                'success' => false,
                'errors' => ['credit_card' => ['Please select a credit card']],
            ];
        }

        DB::beginTransaction();
        try {
            $subscription = $this->subscription()->create($inputs['subscription']);

            foreach ($inputs['charges'] as $charge){
                $this->subscriptionItem()->create([
                    'subscription_id' => $subscription->id,
                    'description' => $charge['description'],
                    'qty' => $charge['qty'],
                    'rate' => $charge['rate'],
                    'tax' => $charge['tax'] === 'HST',
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Subscription created successfully',
                'subscription' => new SubscriptionResource($subscription),
            ];

        }catch (\Exception $e){
            DB::rollBack();
            BaseRepository::logError($e);
            return [
                'success' => false,
                'errors' => ['server_error' => ['Something went wrong, please try again later.']],
                'server_error' => $e->getMessage(),
            ];
        }
    }

    public function updateSubscription($request, $hash): array
    {
        $inputs = $request->all();
        $inputs['subscription'] = $request->subscription;

        if($inputs['subscription']['charge_cc'] === true && empty($inputs['subscription']['credit_card_id'])){
            return [
                'success' => false,
                'errors' => ['credit_card' => ['Please select a credit card']],
            ];
        }

        $subscription = $this->subscription()->where('hash', $hash)->first();

        DB::beginTransaction();
        try {
            $subscription->update($inputs['subscription']);

            $subscription->charges()->delete();
            foreach ($inputs['charges'] as $item){
                $this->subscriptionItem()->create([
                    'hash' => BaseRepository::randomCharacters(30, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'),
                    'subscription_id' => $subscription->id,
                    'description' => $item['description'],
                    'qty' => $item['qty'],
                    'rate' => $item['rate'],
                    'tax' => $item['tax'] === 'HST',
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Invoice updated successfully',
                'subscription' => new SubscriptionResource($subscription),
            ];

        } catch (\Exception $e){
            DB::rollBack();
            BaseRepository::logError($e);
            return [
                'success' => false,
                'errors' => ['server_error' => ['Something went wrong, please try again later.']],
                'server_error' => $e->getMessage(),
            ];
        }
    }

    public function deleteSubscription($hash): array
    {
        $subscription = $this->subscription()->where('hash', $hash)->first();

        if(!$subscription){
            return [
                'success' => false,
                'errors' => ['server_error' => ['Invoice not found']],
            ];
        }

        DB::beginTransaction();
        try {
            if ($subscription->charges()->exists()){
                $subscription->charges()->delete();
            }
            $subscription->delete();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Subscription deleted successfully',
            ];

        }catch (\Exception $e){
            DB::rollBack();
            BaseRepository::logError($e);
            return [
                'success' => false,
                'errors' => ['server_error' => ['Something went wrong, please try again later.']],
            ];
        }
    }

    public function searchSubscriptions($request): array
    {
        $search = $request->all()['query'];
        Session::forget(['search_inputs', 'search_values']);

        $subscriptions = $this->subscription()->with('company', 'client')->select(

            'my_companies.id as my_company_id',
            'my_companies.name as my_company_name',

            'clients.id AS client_id',
            'clients.company_name',

            'subscriptions.*'

        )
        ->leftjoin('my_companies', 'my_companies.id', '=', 'subscriptions.my_company_id')
        ->leftjoin('clients', 'clients.id', '=', 'subscriptions.client_id')

        ->where(function($query) use ($search){
            $query->when(!empty($search), static function($q) use($search){
                $q->where('subscriptions.name', 'like' , '%'. $search .'%')
                    ->orWhere('subscriptions.total', 'like' , '%'. $search .'%')
                    ->orWhere('subscriptions.subtotal', 'like' , '%'. $search .'%')
                    ->orWhere('subscriptions.next_charge_date', 'like' , '%'. $search .'%')
                    ->orWhere('subscriptions.due_in_days', 'like' , '%'. $search .'%')
                    ->orWhere('clients.company_name', 'like' , '%'. $search .'%')
                    ->orWhere('my_companies.name', 'like' , '%'. $search .'%');
            });

        })->latest()->get();

        // if a result exists return results, else return empty array
        if($subscriptions->count() > 0){
            return [
                'success' => true,
                'subscriptions' => SubscriptionIndexResource::collection($subscriptions),
                'total' => $subscriptions->count(),
                'search_values' => Session::get('search_values')
            ];
        }

        return [
            'success' => true,
            'subscriptions' => [],
            'total' => 0,
            'search_values' => Session::get('search_values')
        ];
    }

    public function chargeSubscriptionCreditCard(){

    }
}
