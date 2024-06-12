
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $guarded = ['id'];
    // no timestamps
    public $timestamps = false;
}
