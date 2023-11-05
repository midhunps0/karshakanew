<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TalukController;
use Ynotz\EasyAdmin\Services\RouteHelper;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\Allowances\EducationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeeCollectionController;
use App\Http\Controllers\Accounting\LedgerController;
use App\Http\Controllers\Accounting\TransactionController;
use App\Http\Controllers\Accounting\AccountGroupController;
use App\Http\Controllers\Accounting\AccountsReportsController;
use App\Http\Controllers\Allowances\AllowanceController;
use App\Http\Controllers\Allowances\MarriageController;
use App\Http\Controllers\Allowances\MaternityController;
use App\Http\Controllers\Allowances\MedicalController;
use App\Http\Controllers\Allowances\PostDeathController;
use App\Http\Controllers\Allowances\SuperAnnuationController;
use App\Http\Controllers\MemberTransferController;

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
    return view('auth.login');
})->name('home');

// Route::get('/dashboard', [DashboardController::class, 'dashboard'])->middleware(['auth', 'verified'])->name('dashboard');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin'], function () {
    Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
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
    RouteHelper::getEasyRoutes(modelName: "Taluk");
    RouteHelper::getEasyRoutes(modelName: "Village");
    Route::post('/members/verify/{aadhaarNo}', [MemberController::class, 'verifyAadhaar'])
        ->name('members.verify_aadhaar');
    Route::get('/members/sync', [MemberController::class, 'sync'])
        ->name('members.sync');
    Route::get('/members/reports', [MemberController::class, 'reportsIndex'])
        ->name('members.reports_home');
    Route::get('/members/report', [MemberController::class, 'report'])
        ->name('members.report');
    Route::get('/members/report/gender', [MemberController::class, 'reportGenders'])
        ->name('members.report.gender');
    Route::get('/members/report/new', [MemberController::class, 'reportNew'])
        ->name('members.report.new');
    Route::get('/members/report/status', [MemberController::class, 'reportStatus'])
        ->name('members.report.status');
    Route::post('/members/fetch-old-data', [MemberController::class, 'fetchMemberFromOld'])
        ->name('members.fetch.old');
    Route::get('/members/search', [MemberController::class, 'search'])
        ->name('members.search');
    Route::get('/members/unapproved', [MemberController::class, 'unapprovedMembers'])
        ->name('members.unapproved');
    Route::get('/members/suggestionslist', [MemberController::class, 'suggestionslist'])
        ->name('members.suggestionslist');
    Route::get('/members/fetch/{id}', [MemberController::class, 'fetch'])
        ->name('members.fetch');
    Route::get('/members/annual-fees-period/{id}', [MemberController::class, 'annualFeesPeriod'])
        ->name('members.annual_fees.fromto');
    Route::post('/members/{id}/fees-collection', [MemberController::class, 'storeFeesCollection'])
        ->name('members.fees.store');
    Route::post('/members/fees-collection-bulk', [MemberController::class, 'storeBulkFees'])
        ->name('members.fees.store_bulk');
    Route::post(
        '/members/{id}/old-fees-collection',
        [MemberController::class, 'storeOldFeesCollection']
    )->name('members.old_fees.store');
    RouteHelper::getEasyRoutes(modelName: "Member");
    Route::get('/members/transfer/requests', [MemberTransferController::class, 'transferRequests'])
        ->name('members.transfer_requests');
    Route::get('/members/transfer/{id}', [MemberTransferController::class, 'transferForm'])
        ->name('members.transfer.create');
    Route::get('/members/transfer/edit/{id}', [MemberTransferController::class, 'transferEditForm'])
        ->name('members.transfer.edit');
    Route::post('/members/transfer/approve/{id}', [MemberTransferController::class, 'approve'])
        ->name('members.transfer.approve');
    RouteHelper::getEasyRoutes(modelName: "MemberTransfer");
    RouteHelper::getEasyRoutes(modelName: "User");
    RouteHelper::getEasyRoutes(modelName: "Role");
    RouteHelper::getEasyRoutes(modelName: "Permission");
    Route::get(
        '/fees-collections/search',
        [FeeCollectionController::class, 'search']
    )->name('feecollections.search');
    Route::get(
        '/fees-collections/create-old',
        [FeeCollectionController::class, 'createOld']
    )->name('feecollections.old.create');
    Route::get(
        '/fees-collections/create-bulk',
        [FeeCollectionController::class, 'createBulk']
    )->name('feecollections.bulk.create');
    Route::get('feecollection/get/{id}', [FeeCollectionController::class, 'fetch'])
        ->name('receipt.fetch');
    Route::get('feecollection/report', [FeeCollectionController::class, 'reportForm'])
        ->name('feecollections.report');
    Route::get('feecollection/full-report', [FeeCollectionController::class, 'fullReport'])
        ->name('feecollections.fullreport');
    Route::get('feecollection/report-dowload', [FeeCollectionController::class, 'download'])
        ->name('feecollections.report.download');
    // Route::post('feecollection/report', [FeeCollectionController::class, 'reportData'])
    //     ->name('feecollections.report.data');
    RouteHelper::getEasyRoutes(modelName: "FeeCollection");
    Route::post('/admin/roles/permission-update', [RoleController::class, 'permissionUpdate'])->name('roles.permission');
    Route::get('/allowances/pending', [AllowanceController::class, 'pending'])->name('allowances.pending');
    Route::get('/allowances/report', [AllowanceController::class, 'report'])->name('allowances.report');
    Route::get('allowances/full-report', [AllowanceController::class, 'fullReport'])
        ->name('allowances.fullreport');
    Route::get('allowances/report-dowload', [AllowanceController::class, 'download'])
        ->name('allowances.report.download');
    Route::post('/allowances/approve/{id}', [AllowanceController::class, 'approve'])->name('allowances.approve');

    Route::get('/allowances/education/show/{id}', [EducationController::class, 'show'])->name('allowances.education.show');
    Route::get('/allowances/education/create', [EducationController::class, 'create'])->name('allowances.education.create');
    Route::get('/allowances/education/edit/{id}', [EducationController::class, 'edit'])->name('allowances.education.edit');
    Route::post('/allowances/education/store', [EducationController::class, 'store'])->name('allowances.education.store');
    Route::post('/allowances/education/update/{id}', [EducationController::class, 'update'])->name('allowances.education.update');

    Route::get('/allowances/postdeath/show/{id}', [PostDeathController::class, 'show'])->name('allowances.postdeath.show');
    Route::get('/allowances/postdeath/create', [PostDeathController::class, 'create'])->name('allowances.postdeath.create');
    Route::get('/allowances/postdeath/edit/{id}', [PostDeathController::class, 'edit'])->name('allowances.postdeath.edit');
    Route::post('/allowances/postdeath/store', [PostDeathController::class, 'store'])->name('allowances.postdeath.store');
    Route::post('/allowances/postdeath/update/{id}', [PostDeathController::class, 'update'])->name('allowances.postdeath.update');

    Route::get('/allowances/marriage/show/{id}', [MarriageController::class, 'show'])->name('allowances.marriage.show');
    Route::get('/allowances/marriage/create', [MarriageController::class, 'create'])->name('allowances.marriage.create');
    Route::get('/allowances/marriage/edit/{id}', [MarriageController::class, 'edit'])->name('allowances.marriage.edit');
    Route::post('/allowances/marriage/store', [MarriageController::class, 'store'])->name('allowances.marriage.store');
    Route::post('/allowances/marriage/update/{id}', [MarriageController::class, 'update'])->name('allowances.marriage.update');

    Route::get('/allowances/maternity/show/{id}', [MaternityController::class, 'show'])->name('allowances.maternity.show');
    Route::get('/allowances/maternity/create', [MaternityController::class, 'create'])->name('allowances.maternity.create');
    Route::get('/allowances/maternity/edit/{id}', [MaternityController::class, 'edit'])->name('allowances.maternity.edit');
    Route::post('/allowances/maternity/store', [MaternityController::class, 'store'])->name('allowances.maternity.store');
    Route::post('/allowances/maternity/update/{id}', [MaternityController::class, 'update'])->name('allowances.maternity.update');

    Route::get('/allowances/medical/show/{id}', [MedicalController::class, 'show'])->name('allowances.medical.show');
    Route::get('/allowances/medical/create', [MedicalController::class, 'create'])->name('allowances.medical.create');
    Route::get('/allowances/medical/edit/{id}', [MedicalController::class, 'edit'])->name('allowances.medical.edit');
    Route::post('/allowances/medical/store', [MedicalController::class, 'store'])->name('allowances.medical.store');
    Route::post('/allowances/medical/update/{id}', [MedicalController::class, 'update'])->name('allowances.medical.update');

    Route::get('/allowances/super-annuation/show/{id}', [SuperAnnuationController::class, 'show'])->name('allowances.super_annuation.show');
    Route::get('/allowances/super-annuation/create', [SuperAnnuationController::class, 'create'])->name('allowances.super_annuation.create');
    Route::get('/allowances/super-annuation/edit/{id}', [SuperAnnuationController::class, 'edit'])->name('allowances.super_annuation.edit');
    Route::post('/allowances/super-annuation/store', [SuperAnnuationController::class, 'store'])->name('allowances.super_annuation.store');
    Route::post('/allowances/super-annuation/update/{id}', [SuperAnnuationController::class, 'update'])->name('allowances.super_annuation.update');

    Route::get('/allowances/search', [AllowanceController::class, 'search'])->name('allowances.search');
    Route::post('/allowances/search', [AllowanceController::class, 'getAllowance'])->name('allowances.search');

    Route::get('/account-group-all', [AccountGroupController::class, 'index']);
    Route::get('/account-group-show/{id}', [AccountGroupController::class, 'show']);
    Route::post('/account-group-create', [AccountGroupController::class, 'store']);
    Route::post('/account-group-update/{id}', [AccountGroupController::class, 'update']);
    Route::post('/account-group-delete/{id}', [AccountGroupController::class, 'delete']);

    RouteHelper::getEasyRoutes(
        modelName: "LedgerAccount",
        controller: 'App\Http\Controllers\Accounting\LedgerController'
    );

    RouteHelper::getEasyRoutes(
        modelName: "WelfareScheme"
    );

    /*
    Route::post('/ledger-create', [LedgerController::class, 'create']);
    Route::post('/ledger-store', [LedgerController::class, 'store']);
    Route::post('/ledger-update/{id}', [LedgerController::class, 'update']);
    Route::post('/ledger-delete/{id}', [LedgerController::class, 'delete']);
    Route::get('/ledger-show/{id}', [LedgerController::class, 'show']);
    */
    Route::get('/ledger-list', [LedgerController::class, 'index']);
    // Route::get('/ledger-cashbankaccounts', [LedgerController::class, 'cashBankAccounts']);

    Route::get('/transaction-create', [TransactionController::class, 'create'])->name('transaction.create');
    Route::get('/transation-create-journal', [TransactionController::class, 'createJournal'])->name('transaction.create.journal');
    Route::get('/transaction-create-receipt', [TransactionController::class, 'createReceipt'])->name('transaction.create.receipt');
    Route::get('/transaction-create-voucher', [TransactionController::class, 'createPayment'])->name('transaction.create.voucher');
    Route::post('/transaction-store', [TransactionController::class, 'store'])->name('transaction.store');
    Route::post('/transaction-edit/{id}', [TransactionController::class, 'update']);
    Route::post('/transaction-delete/{id}', [TransactionController::class, 'delete']);
    Route::get('/transaction-show/{id}', [TransactionController::class, 'show']);
    Route::get('/transaction-index', [TransactionController::class, 'index'])->name('transaction.index');

    Route::get('/accounts-chart', [AccountsReportsController::class, 'accountsChart'])->name('accounts.chart');
    Route::get('/account-statement', [AccountsReportsController::class, 'accountStatement'])->name('accounts.account.statement');
    Route::get('/journal-statement', [AccountsReportsController::class, 'journalStatement'])->name('accounts.journal.statement');
    Route::get('/transaction-types', [AccountsReportsController::class, 'transactionTypes']);
});


require __DIR__.'/auth.php';
