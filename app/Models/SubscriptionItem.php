
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionItem extends Model
{
    protected $guarded = ['id'];
    //no timestamps
    public $timestamps = false;
    protected $fillable = [
        'hash',
        'subscription_id',
        'description',
        'qty',
        'rate',
        'tax',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class, 'subscription_id', 'id');
    }
}
