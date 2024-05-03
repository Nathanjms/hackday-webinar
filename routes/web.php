<?php

use App\Models\ChatMessage;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome', [
        'slide' => Slide::orderBy('id', 'desc')->first(),
        'chatMessages' => ChatMessage::getAllFormattedForWebinar(),
    ]);
});

Route::get('/host', function (Request $request) {
    if ($request->host_key !== config('app.host_key')) {
        abort(403);
    }
    return view('host', [
        'slide' => Slide::orderBy('id', 'desc')->first(),
        'chatMessages' => ChatMessage::getAllFormattedForWebinar(),
    ]);
});
