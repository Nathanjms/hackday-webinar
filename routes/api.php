<?php

use App\Events\WebinarChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('chat', function (Request $request) {
    WebinarChat::dispatch($request->message, $request->author, now());
    return response()->json();
});
