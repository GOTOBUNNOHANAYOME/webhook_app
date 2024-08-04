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
        'name',
        'language',
        'status',
        'icon_path',
        'is_enable'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lineAuthentications()
    {
        return $this->hasMany(LineAuthentication::class);
    }
}
