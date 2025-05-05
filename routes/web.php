<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductImportController;
use App\Http\Controllers\ConformityDetailsController;
use App\Models\DefaultEntry;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DefaultEntryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AnomalyController;
use App\Http\Controllers\FabOrderController;
use App\Http\Controllers\Role;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ChaineController;
use App\Http\Controllers\QualityController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\FabricationController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\QualityCheckController;
use App\Http\Controllers\OfplanningController;


Route::get('/', function () {
    return Auth::check() ? redirect('/home') : redirect('/login');
    });
Route::middleware(['auth'])->group(function () {
    //Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/pir', [HomeController::class, 'pir'])->name('pir');
    Route::get('/home', [FabOrderController::class, 'fabChain'])->name('fab_chain.index');
});
Auth::routes();
//Route::get('/{page}', [AdminController::class, 'index']);
Route::middleware(['auth'])->group(function () {
    //Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home', [FabOrderController::class, 'fabChain'])->name('fab_chain.index');
    Route::get('/fabricationn', [HomeController::class, 'fabricationn'])->name('fabricationn');
    //Route::get('/{page}', [AdminController::class, 'index']);
    //user Routes
    Route::get('/list-users', [UserController::class, 'listUsers'])->name('list-users');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    //roles routes
    Route::get('/roles', [RoleController::class, 'ListRoles'])->name('roles');
    Route::post('/roles-create', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    // chaine Routes
    Route::get('/chaine-management', [ChaineController::class, 'index'])->name('chaine.index');
    Route::post('/chaine-management', [ChaineController::class, 'store'])->name('chaine.store');
    Route::put('/chaine-management/{chaine}', [ChaineController::class, 'update'])->name('chaine.update');
    Route::delete('/chaine-management/{chaine}', [ChaineController::class, 'destroy'])->name('chaine.destroy');
    // Products Route
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    // Comformity details
    Route::get('/fetch-conformity-details', [ConformityDetailsController::class, 'fetchConformityDetails'])->name('fetch-conformity-details');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    // Anomalies Route
    Route::get('/anomalies', [AnomalyController::class, 'index'])->name('anomalies.index');
    Route::post('/anomalies/', [AnomalyController::class, 'store'])->name('anomalies.store');
    Route::put('/anomalies/{anomaly}', [AnomalyController::class, 'update'])->name('anomalies.update');
    Route::delete('/anomalies/{anomaly}', [AnomalyController::class, 'destroy'])->name('anomalies.destroy');
    // Ordre de fabrication route - admin
    Route::middleware(['auth', 'role:Administrateur,"Chef de Chaine"'])->group(function () {
        Route::resource('fab_orders', FabOrderController::class);
        Route::get('/fab_chain', [FabOrderController::class, 'fabChain'])->name('fab_chain.index');
        Route::get('/data-import', [ProductImportController::class, 'showImportForm'])->name('data_import.index');
        Route::post('/import-products', [ProductImportController::class, 'import'])->name('products.import');
    });
    Route::get('/fab_chain', [FabOrderController::class, 'fabChain'])->name('fab_chain.index');
    // Ordre de fabrication route - Chaine
    Route::get('/get-product-components/{productId}', [ProductController::class, 'getProductComponents']);
    Route::get('/fab_chain/{fabOrder}/edit', [FabOrderController::class, 'editFabChain'])->name('fab_chain.edit');
    Route::put('/fab_chain/{fabOrder}', [FabOrderController::class, 'updateFabChain'])->name('fab_chain.update');
    Route::get('/fabrication/history/{OFID}', [FabricationController::class, 'getHistory'])
        ->where('OFID', '.*');
    Route::get('/fabrication/history', [FabricationController::class, 'history'])->name('fabrication.history');
    Route::delete('/fabrication/delete/{id}', [FabricationController::class, 'deleteById'])->name('fabrication.deleteById');

    Route::put('/fabrication/update', [FabricationController::class, 'update'])->name('fabrication.update');
    Route::post('/faborder/update-status/{OFID}', [FabOrderController::class, 'updateStatus'])->name('faborder.updateStatus');


    // Quality  routes
    Route::resource('quality', QualityController::class);
    Route::post('/quality/storeOrUpdate', [QualityController::class, 'storeOrUpdate'])->name('quality.storeOrUpdate');
    Route::get('/default-entries/{anomalie}', function ($anomalie) {
        return response()->json(DefaultEntry::where('anoid', $anomalie)->get());
    });
    // default entries routes
    Route::get('/default-entries/create/{Anoid}', [DefaultEntryController::class, 'create'])->name('default_entries.create'); // GET route
    Route::post('/default-entries/store/{Anoid}', [DefaultEntryController::class, 'store'])->name('default_entries.store'); // ✅ POST route
    Route::get('/default-entries/get/{AnoID}', [DefaultEntryController::class, 'getByAnomaly']);
    Route::put('/default-entries/update/{id}', [DefaultEntryController::class, 'update']);
    Route::get('/get-components', [ConformityDetailsController::class, 'getComponents'])->name('get.components');

    Route::put('/default-entries/update/{id}', [DefaultEntryController::class, 'update'])->name('default_entries.update');


    // Bom Route
    Route::get('/products/bom', [ProductController::class, 'getBOM'])->name('products.bom');
// conformity routes
    //Route::post('/conformity/store/{qualityId}', [ConformityDetailsController::class, 'store'])->name('conformity.store');
    Route::get('/conformity/show/{qualityId}', [ConformityDetailsController::class, 'show']);
    Route::delete('/conformity/delete/{id}', [ConformityDetailsController::class, 'destroy'])->name('conformity.destroy');

// default ressources
    Route::apiResource('default-entries', DefaultEntryController::class);
    Route::post('/defaults/{Anoid}', [DefaultEntryController::class, 'store'])->name('defaults.store');

    Route::post('/conformity-details/store', [ConformityDetailsController::class, 'store'])
        ->name('conformity-details.store');
    // Fabrication order chaine
    Route::get('/fabrication/comparison', [FabricationController::class, 'comparison'])->name('fabrication.comparison');
    Route::get('/fabrication/comparison-sale-order', [FabricationController::class, 'comparison_sale_order'])->name('fabrication.comparison-sale-order');
    Route::post('/fabrication/store', [FabricationController::class, 'store'])->name('fabrication.store');
    // Clients
    Route::resource('clients', ClientController::class);
    Route::get('/chart', [FabricationController::class, 'generateAnomalyCharts'])->name('reportigQuality');
    Route::get('/fetch-label-anomaly-data', [FabricationController::class, 'fetchLabelAnomalyData'])->name('fetchLabelAnomalyData');
    Route::get('/fetch-resp-defaut-data', [FabricationController::class, 'fetchRespDefautData'])->name('fetchRespDefautData');
    //Route::get('/fetch-anomaly-data', [FabricationController::class, 'generateAnomalyChart'])->name('fetchAnomalyData');
    Route::get('/fetch-conformity-chart', [FabricationController::class, 'fetchConformityCharts'])->name('fetchConformityChart');
    Route::get('/fetch-anomaly-summary', [FabricationController::class, 'generateAnomalyChartSummary'])->name('fetchAnomalySummary');
    Route::put('/fabrication/update/{id}', [FabricationController::class, 'updateHistory'])->name('fabrication.updateHistory');
      // Route for updating conformity detail
    Route::put('/conformity-details/update', [ConformityDetailsController::class, 'update'])->name('conformity-details.update');
    Route::get('/dropdown/components', [ConformityDetailsController::class, 'fetchComponentsByRefId'])->name('dropdown.components');
    Route::get('/dropdown/anomalies', [ConformityDetailsController::class, 'fetchAnomalies'])->name('dropdown.anomalies');
    Route::get('/dropdown/default-entries/{anomalyId}', [ConformityDetailsController::class, 'fetchDefaultEntriesByAnomaly'])->name('dropdown.default-entries');
    Route::delete('/conformity-details/{id}', [ConformityDetailsController::class, 'destroy'])->name('conformity-details.destroy');
    Route::post('/fab-orders/import', [FabOrderController::class, 'importExcel'])->name('fab_orders.import');
    // Planning
    Route::get('/planning/add', [PlanningController::class, 'addplanning'])->name('planning.addplanning');
    Route::get('/planning/', [PlanningController::class, 'index'])->name('planning.index');
    Route::put('/planning/{id}', [PlanningController::class, 'update'])->name('planning.update');
    Route::delete('/planning/{id}', [PlanningController::class, 'destroy'])->name('planning.destroy');
    Route::get('/get-sale-orders', [PlanningController::class, 'getSaleOrders'])->name('getSaleOrders');
    Route::post('/planning/store', [PlanningController::class, 'store'])->name('planning.store');

    Route::middleware(['auth'])->group(function () {
        Route::resource('quality_checks', QualityCheckController::class)->only(['index', 'create', 'store']);
    });
    Route::get('/ofplanning', [OfplanningController::class, 'index'])->name('ofplanning.index');
    Route::post('/ofplanning/store', [OfplanningController::class, 'store'])->name('ofplanning.store');
    Route::delete('/ofplanning/{id}', [OfplanningController::class, 'destroy'])->name('ofplanning.destroy');
    Route::get('/ofplanning/{id}', [OfplanningController::class, 'show']);
    Route::get('/supchain', [OfplanningController::class, 'supchainView'])->name('ofplanning.supchain');
    Route::get('/cerigmp', [OfplanningController::class, 'cerigmpView'])->name('ofplanning.cerigmp');

});
