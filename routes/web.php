<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BidController;
use App\Http\Controllers\AuctionController;
use Inertia\Inertia;

Route::inertia('/', 'welcome')->name('home');

// Public: browse and view auctions
Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');

// Authenticated: create, edit, cancel, bid
Route::middleware('auth')->group(function () {
    Route::get('/auctions/create', [AuctionController::class, 'create'])->name('auctions.create');
    Route::post('/auctions', [AuctionController::class, 'store'])->name('auctions.store');
    Route::get('/auctions/{auction}/edit', [AuctionController::class, 'edit'])->name('auctions.edit');
    Route::put('/auctions/{auction}', [AuctionController::class, 'update'])->name('auctions.update');
    Route::post('/auctions/{auction}/cancel', [AuctionController::class, 'cancel'])->name('auctions.cancel');
    Route::post('/auctions/{auction}/bids', [BidController::class, 'store'])->name('bids.store');
});

// Public: view single auction — MUST be after /auctions/create
Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';