<?php

namespace App\Http\Controllers;

use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Resources\Invoice\InvoiceResource;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Invoice\InvoiceRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
                    'client:id,company_name,company_email,company_address,company_phone',
                )->orderBy('invoice_num')->get();

            return response()->json([
                'success' => true,
                'invoices' => $data,
                'total' => $data->count(),
            ]);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $data = $this->invoice->searchInvoices($request);
            return response()->json($data, $data['success'] ? 200 : 500);

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
            $data = $this->invoice->storeInvoice($request);
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
            $data = $this->invoice->invoice()->where('hash', $hash)
                ->with(
                    'company',
                    'client:company_name,company_email,company_address,company_phone,id',
                    'items',
                    'payments',
                )->first();

            return response()->json([
                'success' => true,
                'invoice' => new InvoiceResource($data),
            ]);

        }catch (\Exception $e){
            return BaseRepository::tryCatchException($e);
        }
    }

    public function receipt($hash): Response|JsonResponse
    {
        try {
            return $this->invoice->generateInvoiceReceipt($hash);

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
            $data = $this->invoice->updateInvoice($request, $hash);
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
}
