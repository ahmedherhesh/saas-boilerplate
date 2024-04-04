<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\PaymentMethods\StripeController;
use App\Http\Controllers\PaymentMethods\PayPalController;
use App\Http\Controllers\PlansController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\ImageCountMiddleware;
use App\Jobs\AutoRenewal;
use App\Jobs\RenewSubscriptionInfo;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Stripe\Stripe;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return inertia('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return inertia('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::group(['middleware' => 'subscription.checker'], function () {
        Route::get('plans', [PlansController::class, 'index'])->name('plans');
        Route::get('images/editor', [ImageController::class, 'index'])->name('images.editor')->middleware(ImageCountMiddleware::class);
        Route::post('increment-edited-image', [ImageController::class, 'incrementEditedImageCount'])->name('increment.edited.image');
    });

    Route::group(['prefix' => 'stripe', 'as' => 'stripe.', 'controller' => StripeController::class], function () {
        Route::post('checkout', 'checkout')->name('checkout');
        Route::get('success', 'success')->name('success');
        Route::post('auto-renewal-disable', 'autoRenewalDisable')->name('unsubscribe')->middleware('subscription.checker');
    });
    Route::group(['prefix' => 'paypal', 'as' => 'paypal.', 'controller' => PayPalController::class], function () {
        Route::post('/', 'index')->name('checkout');
        Route::put('subscription/continue', 'continue')->name('subscription.continue');
        Route::post('auto-renewal-disable', 'autoRenewalDisable')->name('unsubscribe')->middleware('subscription.checker');
    });
});
Route::post('stripe/webhook', [StripeController::class, 'webhook'])->name('stripe.webhook');
Route::post('paypal/webhook', [PayPalController::class, 'webhook'])->name('paypal.webhook');


Route::get('test', function () {
   AutoRenewal::dispatch(auth()->user());
});
require __DIR__ . '/auth.php';
