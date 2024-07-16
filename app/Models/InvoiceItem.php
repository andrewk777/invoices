<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webpatser\Uuid\Uuid;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    // no timestamps
    public $timestamps = false;
    protected $fillable = [
        'hash',
        'invoice_id',
        'description',
        'qty',
        'rate',
        'tax',
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
