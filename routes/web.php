<?php

use App\Models\ChatMessage;
use App\Models\Slide;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'slide' => Slide::orderBy('id', 'desc')->first(),
        'chatMessages' => ChatMessage::all(),
    ]);
});

Route::get('/host', function () {
    return view('host', [
        'slide' => Slide::orderBy('id', 'desc')->first(),
        'chatMessages' => ChatMessage::all(),
    ]);
});
