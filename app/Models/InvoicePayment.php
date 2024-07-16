<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webpatser\Uuid\Uuid;

class InvoicePayment extends Model
{
    use HasFactory;

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

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->hash = (string) Uuid::generate(4);
        });
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id', 'id');
    }
}
