<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineAuthentication extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_account_id',
        'link_token',
        'nonce'
    ];

    public function lineAccount()
    {
        return $this->belongsTo(LineAccount::class);
    }
}
