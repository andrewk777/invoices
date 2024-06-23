
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MyCompany extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $fillable = [
      'name',
      'hash'
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(User::class, 'client_id', 'id');
    }

}
