<?php

namespace App\Http\Controllers;

use App\Repositories\Subscription\SubscriptionRepository;
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
