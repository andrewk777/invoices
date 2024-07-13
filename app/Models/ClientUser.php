<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientUser extends Authenticatable
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $fillable = [
        'hash',
        'client_id',
        'name',
        'email',
        'password',
        'last_login',
        'system_access',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
