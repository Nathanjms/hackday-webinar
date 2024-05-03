<?php

use App\Events\WebinarChat;
use App\Events\WebinarSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('chat', function (Request $request) {
    WebinarChat::dispatch($request->message, $request->author, now());
    return response()->json();
});

Route::post('slides/next', function (Request $request) {
    $randomSentence = \Faker\Factory::create()->sentence();
    $header = \Faker\Factory::create()->sentence();
    WebinarSlide::dispatch('<h1>' . $header . '</h1>', $randomSentence);
    return response()->json();
});
