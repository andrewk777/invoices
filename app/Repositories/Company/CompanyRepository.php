<?php

namespace App\Repositories\Company;

use App\Models\MyCompany;

class CompanyRepository
{
    public function company(): MyCompany
    {
        return new MyCompany();
    }
}
