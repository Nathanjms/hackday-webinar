<?php

use App\Events\TestEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('test', function (Request $request) {
    TestEvent::dispatch();
    return response()->json();
});
