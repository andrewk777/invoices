
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes, HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'hash',
        'my_company_id',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'main_contact_first_name',
        'main_contact_last_name',
        'main_contact_phone',
        'main_contact_email',
        'ap_first_name',
        'ap_last_name',
        'ap_phone',
        'ap_email',
        'notes',
    ];

    public function myCompany(): BelongsTo
    {
        return $this->belongsTo(MyCompany::class, 'my_company_id', 'id');
    }
}
