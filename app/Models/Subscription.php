<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes, HasFactory;
    protected $guarded = ['id'];

    protected $fillable = [
        'hash',
        'name',
        'my_company_id',
        'client_id',
        'tags',
        'currency',
        'credit_card_id',
        'next_charge_date',
        'due_in_days',
        'frequency_day',
        'frequency_month',
        'can_pay_with_cc',
        'starting_date',
        'expiration_date',
        'charge_cc',
        'email_invoice',
        'email_template_id',
        'subtotal',
        'taxes',
        'total',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(MyCompany::class, 'my_company_id', 'id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function creditCard(): BelongsTo
    {
        return $this->belongsTo(CreditCard::class, 'credit_card_id', 'id');
    }

    public function charges(): HasMany
    {
        return $this->hasMany(SubscriptionItem::class, 'subscription_id', 'id');
    }

}
