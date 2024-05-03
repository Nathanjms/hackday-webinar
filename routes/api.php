<?php

use App\Events\WebinarChat;
use App\Events\WebinarRestart;
use App\Events\WebinarSlide;
use App\Models\ChatMessage;
use App\Models\Slide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('chat', function (Request $request) {
    $request->validate([
        'message' => 'required|string|max:255',
        'author' => 'required|string|max:255',
    ]);

    ChatMessage::create([
        'message' => $request->message,
        'author' => $request->author,
    ]);

    WebinarChat::dispatch($request->message, $request->author, now());
    return response()->json();
});

Route::post('slides/next', function (Request $request) {
    $randomSentence = \Faker\Factory::create()->sentence();
    $header = \Faker\Factory::create()->sentence();

    $slide = Slide::create([
        'html' => '<h1>' . $header . '</h1>',
        'script' => $randomSentence
    ]);
    WebinarSlide::dispatch($slide);
    return response()->json();
});

Route::post('restart', function (Request $request) {
    // Delete all chat messages:
    ChatMessage::truncate();
    Slide::truncate();
    WebinarRestart::dispatch();
    return response()->json();
});
