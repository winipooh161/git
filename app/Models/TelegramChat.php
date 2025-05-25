<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramChat extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_id',
        'state',
        'data',
        'step',
        'last_command'
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
