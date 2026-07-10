<?php

use App\Http\Controllers\Communities\CommunityController;
use App\Http\Controllers\Explore\ExploreController;
use App\Http\Controllers\Explore\NearestPlaceController;
use App\Http\Controllers\Explore\SearchController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

// Explore is the focus of this build; the home route sends visitors straight there.
Route::get('/', fn () => redirect()->route('explore'));

/*
|--------------------------------------------------------------------------
| Explore (Inertia/React)
|--------------------------------------------------------------------------
*/
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');
Route::get('/communities/{circle}', [CommunityController::class, 'show'])->name('communities.show');

// JSON endpoints for the dynamic pieces (search overlay, geolocation suggestion).
Route::get('/explore/search', SearchController::class)->name('explore.search');
Route::get('/explore/nearest', NearestPlaceController::class)->name('explore.nearest');

Route::livewire('/counter', 'counter');

/*
|--------------------------------------------------------------------------
| External request approval (public, token-based — no auth)
|--------------------------------------------------------------------------
*/

Route::get('/requests/confirm/{token}', [RequestController::class, 'show'])
    ->name('requests.confirm');

Route::post('/requests/confirm/{token}/approve', [RequestController::class, 'approve'])
    ->name('requests.confirm.approve');

Route::post('/requests/confirm/{token}/deny', [RequestController::class, 'deny'])
    ->name('requests.confirm.deny');
