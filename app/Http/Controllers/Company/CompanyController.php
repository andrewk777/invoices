<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Repositories\Base\BaseRepository;
use App\Repositories\Company\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    protected CompanyRepository $company;
    public function __construct(CompanyRepository $company)
    {
        $this->company = $company;
    }

    public function index(): JsonResponse
    {
        try {
            $data = $this->company->company()->get();
            return response()->json([
                'success' => true,
                'companies' => $data
            ]);

        }catch (\Exception $e) {
            return BaseRepository::tryCatchException($e);
        }
    }
}
