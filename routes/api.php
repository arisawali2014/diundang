<?php

use App\Http\Controllers\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/tokens/create', function (Request $request) {
    $token = User::find(1)->createToken($request->token_name);

    return ['token' => $token->plainTextToken];
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('config', function () {
        return [
            "code" => 200,
            "data" => [
                "name" => "Dewana",
                "can_edit" => true,
                "can_delete" => true,
                "can_reply" => true
            ],
            "error" => null
        ];
    });

    Route::prefix('saweria')->group(function () {
        Route::get('leaderboard', function (Request $request) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://backend.saweria.co/widgets/leaderboard/' . $request->range,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Stream-Key: fd39b673a80ad1be9d51d0394ccdd5a5'
                ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return response()->json([
                ...json_decode($response, true),
                'code' => 200
            ]);
        });
    });

    Route::prefix('comment')->group(function () {
        Route::controller(CommentController::class)->group(function () {
            Route::get('/', 'list');
            Route::post('/', 'add');

            Route::prefix('{id}')->group(function () {
                Route::get('/', 'show');
                Route::put('/', 'update');
                Route::delete('/', 'delete');
                Route::post('/', 'like');
                Route::patch('/', 'unlike');
            });
        });
    });
});
