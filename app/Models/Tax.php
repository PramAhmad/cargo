<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $table = "taxes";
    protected $fillable = [
        "name",
        "type",
        "value",
        "is_active",
    ];

}
