<?php

use Illuminate\Support\Facades\Route;
use App\Services\ApiResponse;

Route::fallback(function(){
    return ApiResponse::error('Endpoint not found', 404);
});