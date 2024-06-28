<?php

namespace App\Http\Controllers;

use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\InvoiceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    protected InvoiceRepository $invoice;
    public function __construct(InvoiceRepository $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $data = $this->invoice->invoice()
                ->with(
                    'company:id,name',
                    'client:main_contact_first_name,main_contact_last_name,id',
                )->latest()->paginate(12);
            return response()->json([
                'success' => true,
                'invoices' => $data,
                'total' => $data->total(),
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
