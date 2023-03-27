<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TalukController;
use Ynotz\EasyAdmin\Services\RouteHelper;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\FeeCollectionController;

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
    return view('welcome');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Route::get('/admin/districts/select-ids', [DistrictController::class, 'selectIds'])->name('districts.selectIds');
    // Route::get('/admin/districts/suggest-list', [DistrictController::class, 'suggestlist'])->name('districts.suggestlist');
    // Route::get('/admin/districts/download', [DistrictController::class, 'download'])->name('districts.download');
    // Route::resource('districts', DistrictController::class);

    RouteHelper::getEasyRoutes(modelName: "Tradeunion");
    Route::get('/district/{id}/taluks', [DistrictController::class, 'getTaluks'])
        ->name('district.taluks');
    Route::get('/taluks/{id}/villages', [TalukController::class, 'getVillages'])
        ->name('taluks.villages');
    RouteHelper::getEasyRoutes(modelName: "District");
    Route::get('/members/search', [MemberController::class, 'search'])
        ->name('members.search');
    Route::get('/members/suggestionslist', [MemberController::class, 'suggestionslist'])
        ->name('members.suggestionslist');
    Route::get('/members/fetch/{id}', [MemberController::class, 'fetch'])
        ->name('members.fetch');
    Route::get('/members/annual-fees-period/{id}', [MemberController::class, 'annualFeesPeriod'])
        ->name('members.annual_fees.fromto');
    Route::post('/members/{id}/fees-collection', [MemberController::class, 'storeFeesCollection'])
        ->name('members.fees.store');
    Route::post(
        '/members/{id}/old-fees-collection',
        [MemberController::class, 'storeOldFeesCollection']
    )->name('members.old_fees.store');
    RouteHelper::getEasyRoutes(modelName: "Member");
    RouteHelper::getEasyRoutes(modelName: "User");
    RouteHelper::getEasyRoutes(modelName: "Role");
    RouteHelper::getEasyRoutes(modelName: "Permission");
    Route::get(
        '/fees-collections/create-old',
        [FeeCollectionController::class, 'createOld']
    )->name('feecollections.old.create');
    Route::get('feecollection/get/{id}', [FeeCollectionController::class, 'fetch'])
        ->name('receipt.fetch');
    RouteHelper::getEasyRoutes(modelName: "FeeCollection");
});


require __DIR__.'/auth.php';
