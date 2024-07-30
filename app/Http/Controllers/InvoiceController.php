<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\InvoiceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

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
            $invoiceData = $this->invoice->invoice()
                ->with(
                    'company:id,name',
                    'client:id,company_name,company_email,company_address,company_phone',
                );

            if(!empty(Auth::user()->client_id) && Auth::user()->role === 'client-user'){
                $invoiceData->where('client_id', Auth::user()->client_id);
            }

            $data = $invoiceData->orderBy('invoice_num', 'desc')->get();

            return response()->json([
                'success' => true,
                'invoices' => InvoiceResource::collection($data),
                'total' => $data->count(),
            ]);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $data = $this->invoice->storeInvoice($request->all());
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
            $invoiceData = $this->invoice->invoice()->with(
                    'company',
                    'client:company_name,company_email,company_address,company_phone,id',
                    'items',
                    'payments',
                )->where('hash', $hash);

                if(!empty(Auth::user()->client_id) && Auth::user()->role === 'client-user'){
                    $invoiceData->where('client_id', Auth::user()->client_id);
                }

               $data = $invoiceData->first();

            return response()->json([
                'success' => true,
                'invoice' => $data ? new InvoiceResource($data) : null,
            ]);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $hash): JsonResponse
    {
        try {
            $data = $this->invoice->updateInvoice($request->all(), $hash);
            return response()->json($data, $data['success'] ? 200 : 500);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $hash): JsonResponse
    {
        try {
            $this->invoice->deleteInvoice($hash);
            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully'
            ]);
        }catch(\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $data = $this->invoice->searchInvoices($request->all());
            return response()->json($data, $data['success'] ? 200 : 500);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function stream($hash)
    {
        try{
            return $this->invoice->generateInvoiceFile($hash);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }
}
