<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', \App\Livewire\LandingPage::class)->name('home');

Route::get('/webinars', \App\Livewire\Webinars\Index::class)->name('webinars.index');
Route::get('/webinars/{slug}', \App\Livewire\Webinars\Show::class)->name('webinars.show');
Route::get('/checkout/{type}/{slug}', \App\Livewire\Checkout::class)->name('checkout');

Route::get('/pelatihan/{slug}', \App\Livewire\TrainingsShow::class)->name('trainings.show');
Route::get('/checkout/pelatihan/{slug}', \App\Livewire\TrainingCheckout::class)->name('checkout.training');
Route::post('/webhook/midtrans', [\App\Http\Controllers\Webhook\MidtransController::class, 'handle']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
