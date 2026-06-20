<?php

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\MercadoPagoWebhookController;
use App\Http\Controllers\PremiumController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\ThreadsAuthController;
use App\Http\Controllers\ThreadsWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/data-deletion', [LegalController::class, 'dataDeletion'])->name('legal.data-deletion');
Route::get('/health', HealthController::class)->name('health');

Route::prefix('auth/threads')->name('auth.threads.')->group(function () {
    Route::get('/redirect', [ThreadsAuthController::class, 'redirect'])->name('redirect');
    Route::get('/callback', [ThreadsAuthController::class, 'callback'])->name('callback');
});

Route::post('/webhooks/meta/deauthorize', [ThreadsWebhookController::class, 'deauthorize'])
    ->name('webhooks.meta.deauthorize');

Route::get('/resultado', [ResultController::class, 'show'])->name('result.show');
Route::post('/resultado/recalcular', [ResultController::class, 'recalculate'])->name('result.recalculate');
Route::get('/u/{username}', [ResultController::class, 'public'])->name('result.public');

Route::get('/premium', [PremiumController::class, 'show'])->name('premium.show');
Route::post('/checkout/pix', [CheckoutController::class, 'createPix'])->name('checkout.pix');
Route::get('/checkout/status/{order}', [CheckoutController::class, 'status'])->name('checkout.status');

Route::post('/webhooks/mercado-pago', [MercadoPagoWebhookController::class, 'handle'])
    ->name('webhooks.mercado-pago');
