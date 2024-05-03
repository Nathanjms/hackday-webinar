<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'message',
        'author',
        'slide_id'
    ];

    public static function getAllFormattedForWebinar()
    {
        return ChatMessage::orderBy('id', 'desc')->get()->map(fn ($m) => [
            'message' => $m->message,
            'author' => $m->author,
            'createdAt' => $m->created_at->setTimezone('Europe/London')->format('H:i'),
        ]);
    }
}
