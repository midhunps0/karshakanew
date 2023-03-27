<?php
use Illuminate\Support\Facades\Route;
use Ynotz\EasyAdmin\Http\Controllers\DashboardController;
use Ynotz\EasyAdmin\Http\Controllers\MasterController;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'admin'], function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('eaasyadmin/fetch/{service}/{method}', [MasterController::class, 'fetch'])->name('easyadmin.fetch');
});
?>

