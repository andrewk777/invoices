
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCard extends Model
{
    use SoftDeletes, HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'id',
        'hash',
        'client_id',
        'cc_provider',
        'cc_number',
        'cvv',
        'expiration_month',
        'expiration_year',
        'name_on_card',
        'address',
        'state',
        'country',
        'cc_currencies'
    ];
}
