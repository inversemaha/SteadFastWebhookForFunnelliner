<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SteadfastWebhookController;

// Steadfast webhook endpoint
Route::post('/webhooks/steadfast', [SteadfastWebhookController::class, 'handleSteadFastWebhook']);