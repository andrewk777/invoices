<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes, HasFactory;

    protected $guarded = ['id'];

    protected $fillable = [
        'hash',
        'company_id',
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

    public function company(): BelongsTo
    {
        return $this->belongsTo(MyCompany::class, 'company_id', 'id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
