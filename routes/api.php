<?php

use App\Events\WebinarChat;
use App\Events\WebinarLoading;
use App\Events\WebinarRestart;
use App\Events\WebinarSlide;
use App\Models\ChatMessage;
use App\Models\GptMessageHistory;
use App\Models\Slide;
use App\Services\OpenAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        'slide_id' => Slide::max('id'),
    ]);

    WebinarChat::dispatch($request->message, $request->author, now());
    return response()->json();
});

Route::post('webinar/begin', function (Request $request) {
    WebinarLoading::dispatch();
    Slide::truncate();
    GptMessageHistory::truncate();

    $request->validate([
        'topic' => 'required|string|max:100',
    ]);
    $openAiService = new OpenAiService;
    $response = $openAiService->sendMessage(json_encode([
        'topic' => $request->topic,
        'slide' => 1,
    ]));

    try {
        $response = json_decode($response, true);
        $script = $response['script'];
        $mp3 = $openAiService->getAudio($script);
        $html = $response['html'];
    } catch (\Throwable $th) {
        report($th);
        $script = '';
        $html = '<h1>AI Has Failed, we tried out best :c</h1>';
        $mp3 = null;
    }
    $slide = Slide::create([
        'html' => $html,
        'script' => $script,
        'mp3' => $mp3,
    ]);
    WebinarSlide::dispatch($slide);
    return response()->json();
});

Route::post('slides/next', function (Request $request) {
    WebinarLoading::dispatch();
    $openAiService = new OpenAiService;
    $response = $openAiService->sendMessage(json_encode([
        'slide' => Slide::max('id') + 1,
        'topic' => $request->topic,
        'userMessages' => ChatMessage::where('slide_id', Slide::max('id'))->get()
            ->map(fn ($message) => [
                'message' => $message->message,
                'author' => $message->author,
            ])
    ]));

    try {
        $response = json_decode($response, true);
        $script = $response['script'];
        $mp3 = $openAiService->getAudio($script);
        $html = $response['html'];
    } catch (\Throwable $th) {
        report($th);
        $script = '';
        $html = '<h1>AI Has Failed, we tried out best :c</h1>';
        $mp3 = null;
    }
    $slide = Slide::create([
        'html' => $html,
        'script' => $script,
        'mp3' => $mp3,
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

Route::get('slides/mp3', function (Request $request) {
    return Slide::orderBy('id', 'desc')->first()?->mp3;
});
