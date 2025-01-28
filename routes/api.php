<?php

use App\Http\Controllers\CardController;
use Illuminate\Support\Facades\Route;

Route::post('/process-card', [CardController::class, 'processCard']);
