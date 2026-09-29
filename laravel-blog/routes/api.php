<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::apiResource('posts', PostController::class)->names([
    'index' => 'api.posts.index',
    'store' => 'api.posts.store',
    'show' => 'api.posts.show',
    'update' => 'api.posts.update',
    'destroy' => 'api.posts.destroy',
]);
