<?php

namespace App\Repositories\Subscription;

use App\Models\Subscription;
use App\Models\SubscriptionItem;
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

    public function storeSubscription(){

    }

    public function searchSubscriptions($request): array
    {
        $search = $request->all()['query'];
        Session::forget(['search_inputs', 'search_values']);

        $clients = $this->subscription()->with('company')->select(

            'my_companies.id as my_company_id',
            'my_companies.name as my_company_name',
            'clients.*'

        )->leftjoin('my_companies', 'my_companies.id', '=', 'clients.my_company_id')
            ->where(function($query) use ($search){
                $query->when(!empty($search), static function($q) use($search){
                    $q->where('clients.company_name', 'like' , '%'. $search .'%')
                        ->orWhere('clients.company_email', 'like' , '%'. $search .'%')
                        ->orWhere('clients.company_address', 'like' , '%'. $search .'%')
                        ->orWhere('clients.company_phone', 'like' , '%'. $search .'%')
                        ->orWhere('clients.main_contact_first_name', 'like' , '%'. $search .'%')
                        ->orWhere('clients.main_contact_last_name', 'like' , '%'. $search .'%');
                });

            })->latest()->get();

        // if a result exists return results, else return empty array
        if($clients->count() > 0){
            return [
                'success' => true,
                'clients' => ClientResource::collection($clients),
                'total' => $clients->count(),
                'search_values' => Session::get('search_values')
            ];
        }

        return [
            'success' => true,
            'clients' => [],
            'total' => 0,
            'search_values' => Session::get('search_values')
        ];
    }
}
