<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'cc_provider_customer_id',
        'cc_provider_client_id',
        'cc_currencies',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
