<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webpatser\Uuid\Uuid;

class Invoice extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'hash',
        'my_company_id',
        'client_id',
        'invoice_num',
        'invoice_type',
        'status',
        'currency',
        'invoice_date',
        'invoice_due',
        'na',
        'can_pay_with_cc',
        'subtotal',
        'taxes',
        'total',
        'total_paid',
        'balance',
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->hash = (string) Uuid::generate(4);
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(MyCompany::class, 'my_company_id', 'id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class, 'invoice_id', 'id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InvoicePayment::class, 'invoice_id', 'id');
    }
}
