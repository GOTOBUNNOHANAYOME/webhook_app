<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'line_user_id',
        'line_user_name',
        'language',
        'icon_path'
    ];
}
