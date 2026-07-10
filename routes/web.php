<?php

use App\Http\Controllers\RequestController;
use App\Livewire\Communities\CommunityPage;
use App\Livewire\Explore\ExploreCommunities;
use Illuminate\Support\Facades\Route;

// Explore is the focus of this build; the home route sends visitors straight there.
Route::get('/', fn () => redirect()->route('explore'));

Route::livewire('/counter', 'counter');

Route::get('/explore', ExploreCommunities::class)->name('explore');

// Single community (circle) full page. Public for now — permissions later.
Route::get('/communities/{circle}', CommunityPage::class)->name('communities.show');

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
