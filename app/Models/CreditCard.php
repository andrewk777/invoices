<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

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
        'cc_exp_month',
        'cc_exp_year',
        'cc_provider_customer_id',
        'cc_provider_card_id',
        'cc_currencies',
        'cc_type',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->hash = (string) Uuid::generate(4);
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
