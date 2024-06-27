<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoicePayment extends Model
{
    protected $guarded = ['id'];
    public $timestamps = ['created_at'];

    protected $fillable = [
        'hash',
        'invoice_id',
        'amount',
        'date',
        'type',
        'note',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
