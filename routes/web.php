<?php

use App\Enum\RoleEnum;

use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\web;
use App\Http\Controllers\admin;
use App\Http\Controllers\client;
use App\Http\Controllers\pharmacy;
use App\Http\Controllers\Auth\RegisterPharmacyController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\admin\AdController;
use App\Http\Controllers\advertisement\advertisementController;
use App\Http\Controllers\advertisement\AdvertisementController as AdvertisementAdvertisementController;

use App\Http\Controllers\pharmacy\PharmacyController;
use Illuminate\Support\Facades\Route;

use Barryvdh\Debugbar\Facades\Debugbar;

// TODO
// disable Debug
Debugbar::disable();
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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Route::get('/profile', function () {
//   return view('client.profile');
// });


Route::controller(web\HomeController::class)->group(function () {
  Route::get('/', 'index')->name('home');
  Route::get('/pharmacies', 'showPharmacies')->name('pharmacies');
  Route::get('/pharmacies/profile/{id}', 'showPharmacy')->name('pharmacy.profile')->middleware('verified');


});

/*
|--------------------------------------------------------------------------
| General Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->name('setting.')->group(function () {
  Route::post('/change/password', [ChangePasswordController::class, 'updatePassword'])->name('update.password');
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
// change-password 
Route::get('/change-password', [App\Http\Controllers\HomeController::class, 'changePassword'])->name('change-password');

Route::post('/change-password', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('update-password');


// pharmacies
Route::resource('/pharmacies', PharmacyController::class);

  Route::controller(SettingController::class)->group(function () {

    Route::post('/update/logo', 'updateLogo')->name('update.logo')->middleware('role:' . RoleEnum::PHARMACY);
    Route::get('/setting', 'index')->name('index');
    Route::post('/setting', 'updateAccount')->name('update.account');
    Route::post('/update/avatar', 'updateAvatar')->name('update.avatar');
  });
});

/*
|--------------------------------------------------------------------------
| Notifications Routes
|--------------------------------------------------------------------------
*/

Route::controller(NotificationController::class)->group(function () {
  Route::get('/notification', 'getAll')->name('notification');
  Route::post('/read/notification', 'read')->name('notification.read');
});

/*
|--------------------------------------------------------------------------
| Register Pharmacy Routes
|--------------------------------------------------------------------------
*/
Route::controller(RegisterPharmacyController::class)->group(function () {
  Route::get('/register/pharmacy', 'index')->name('register.pharmacy');
  Route::post('/register/pharmacy', 'store')->name('register.pharmacy.store');
});

/*
|--------------------------------------------------------------------------
| Pharmacies Routes
|--------------------------------------------------------------------------
*/

// TODO only for debugging
Route::prefix('/pharmacy')
  // ->middleware(['auth', 'role:' . RoleEnum::PHARMACY, 'verified'])
  ->name('pharmacy.')->group(function () {
    // Route::resource('/', pharmacy\PharmacyController::class);
    // Route::view('/', 'pharmacy.dashboard.setting')->name('dashboard');

    Route::controller(pharmacy\DashboardController::class)->group(function () {
      // profile
      Route::get('/', 'index')->name('index');
      Route::get('/profile', 'profile')->name('profile');
      Route::get('/messages', 'messages')->name('messages');
      Route::get('/account-settings', 'accountSettings')->name('account-settings');
    });


    Route::view('/', 'pharmacy.dashboard.setting')->name('dashboard');

    /*------------------------------ orders ------------------------------*/
//     Route::get('/orders', [pharmacy\OrderController::class, 'index'])
//   ->name('orders');
//   });
    Route::controller(pharmacy\OrderController::class)
      ->prefix('/orders')->name('orders.')->group(function () {
        Route::get('/', 'getAll')->name('index');
        Route::get('/refusal/{id}', 'orderRefusal')->name('refusal');
      });


    Route::controller(pharmacy\QuotationController::class)
      ->prefix('/quotation')->name('quotation.')->group(function (){

        Route::get('/', 'getAll')->name('index');
        Route::get('/{id}', 'createQuotation')->name('create');

      });

  });
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('/admin')
  ->name('admin.')
  ->middleware(['auth', 'role:' . RoleEnum::SUPER_ADMIN])
  ->group(function () {

    Route::get('/', [admin\AdminController::class, 'index'])->name('index');

    // admin profile
    Route::get('profile', [admin\AdminProfileController::class, 'index'])
      ->name('profile');

    Route::put('profile', [admin\AdminProfileController::class, 'updateProfile'])
      ->name('update-profile');
    /*------------------------------ ads ------------------------------*/
    Route::resource('/ads', admin\AdController::class);

    /*------------------------------ website content ------------------------------*/
    Route::prefix('site')->controller(admin\SiteController::class)
      ->group(function () {
        Route::get('/', 'index')->name('site');
        Route::put('/about-us', 'updateAboutUs')
          ->name('updateAboutUs');

        Route::post('/services', 'addService')->name('addService');
        Route::put('/services/{service}', 'updateService')->name('updateService');
        Route::delete('/services/{service}', 'deleteService')->name('deleteService');

        Route::put('/contact-us', 'updateContactUs')->name('updateContactUs');

        Route::put('/social', 'updateSocial')->name('updateSocial');
      });

    /*------------------------------ clients ------------------------------*/
    Route::controller(admin\ClientController::class)->group(function () {
      Route::get('/clients', 'index')
        ->name('clients');

      Route::post('/clients/toggle/{id}',  'clientToggle')
        ->name('clients.toggle');
    });

    /*------------------------------ orders ------------------------------*/
    Route::get('/orders', [admin\OrderController::class, 'index'])
      ->name('admin.orders'); // TODO

    // pharmacies
    Route::controller(admin\PharmacyController::class)->group(function () {
      Route::get('/pharmacies',  'index')
        ->name('pharmacies');

      Route::post('/pharmacies/toggle/{id}',  'pharmacyToggle')
        ->name('pharmacies.toggle');
    });
  });

/*
|--------------------------------------------------------------------------
| Client Routes
|--------------------------------------------------------------------------
*/
Route::prefix('/clients')->name('clients.')->middleware(['auth', 'role:' . RoleEnum::CLIENT, 'verified'])->group(function () {
  Route::get('/', [client\ClientProfileController::class, 'index'])
    ->name('profile');

  Route::post('/', [client\ClientProfileController::class, 'updateProfile'])
    ->name('update-profile');

  Route::view('/', 'pharmacy.dashboard.setting')->name('dashboard');


  Route::resource('/addresses', client\AddressController ::class);

//   Route::get('/orders', [client\OrderController::class, 'index'])->name('orders');



  Route::controller(client\OrderController::class)
    ->prefix('/orders')->name('order.')->group(function (){

    Route::get('/', 'getAll')->name('index');
    Route::post('/', 'storeOrder')->name('store');
    Route::get('/{id}', 'showOrder')->name('show');

  });
});

// TESTING
Route::view('/clients/order', '0-testing.create-order');

Auth::routes(['verify' => true]);
