<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SummerNoteController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TestController;

use Illuminate\Http\Request;


Auth::routes();

Route::get('/', [FrontendController::class, 'index'])->name('home');

Route::middleware(['auth'])->group(function () {

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    //user
    Route::resource('user',UserController::class)->middleware('permission:users.view| users.create | users.edit | users.delete');
    //update user password

    //profile page
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('profile-update/{user}',[ProfileController::class,'profileUpdate'])->name('user.profile.update');
    Route::patch('user/pasword-update/{user}',[UserController::class,'password_update'])->name('user.password.update');
    Route::put('user/profile-pic/{user}',[UserController::class,'updateProfileImage'])->name('user.profile.image.update');

    //delete profile image
    Route::patch('delete-profile-image/{user}',[UserController::class,'deleteProfileImage'])->name('delete.profile.image');
    //trash view for users
    Route::get('user-trash', [UserController::class, 'trashView'])->name('user.trash');
    Route::get('user-restore/{id}', [UserController::class, 'restore'])->name('user.restore');
    //deleted permanently
    Route::delete('user-delete/{id}', [UserController::class, 'force_delete'])->name('user.force.delete');

    Route::get('settings', [SettingController::class, 'index'])->name('setting')->middleware('permission:setting update');
    Route::post('settings/{setting}', [SettingController::class, 'update'])->name('setting.update');


    Route::resource('category', CategoryController::class)->middleware('permission:categories.view| categories.create | categories.edit | categories.delete');


    // Services
    Route::resource('service', ServiceController::class)->middleware('permission:services.view| services.create | services.edit | services.delete');
    Route::get('service-trash', [ServiceController::class, 'trashView'])->name('service.trash');
    Route::get('service-restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');
    //deleted permanently
    Route::delete('service-delete/{id}', [ServiceController::class, 'force_delete'])->name('service.force.delete');

    // Add this line for the services index route
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

    //summernote image
    Route::post('summernote',[SummerNoteController::class,'summerUpload'])->name('summer.upload.image');
    Route::post('summernote/delete',[SummerNoteController::class,'summerDelete'])->name('summer.delete.image');


    //employee
    // Route::resource('user',UserController::class);
    Route::get('employee-booking',[UserController::class,'EmployeeBookings'])->name('employee.bookings');
    Route::get('my-booking/{id}',[UserController::class,'show'])->name('employee.booking.detail');

    // employee profile self data update
    Route::patch('employe-profile-update/{employee}',[ProfileController::class,'employeeProfileUpdate'])->name('employee.profile.update');

    //employee bio
    Route::put('employee-bio/{employee}',[EmployeeController::class,'updateBio'])->name('employee.bio.update');





    Route::get('test',function(Request $request){
        return view('test',  [
            'request' => $request
        ]);
    });



    Route::post('test', function (Request $request) {
        dd($request->all())->toArray();
    })->name('test');

    // Payment Routes
    Route::post('/payment/process', [PaymentController::class, 'processPayment'])->name('payment.process');
    Route::post('/payment/confirm', [PaymentController::class, 'confirmPayment'])->name('payment.confirm');
    Route::post('/payment/create-intent', [PaymentController::class, 'createPaymentIntent'])->name('payment.create-intent');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/appointment/success', [AppointmentController::class, 'success'])->name('appointment.success');

    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index')->middleware('permission:appointments.view| appointments.create | services.appointments | appointments.delete');

});



//frontend routes
//fetch services from categories
Route::get('/categories', [FrontendController::class, 'categories'])->name('categories.index');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
Route::get('/categories/{category}/services', [FrontendController::class, 'getServices'])->name('get.services');
Route::get('/services/{service}/employees', [FrontendController::class, 'getEmployees'])->name('get.employees');
Route::get('/employees/{employee}/availability/{date?}', [FrontendController::class, 'getEmployeeAvailability'])->name('employee.availability');

// Booking routes
Route::post('/bookings', [AppointmentController::class, 'store'])->name('bookings.store');
Route::get('/appointment/success', [AppointmentController::class, 'success'])->name('appointment.success');

// Admin routes
Route::middleware(['auth'])->group(function () {
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments')->middleware('permission:appointments.view| appointments.create | services.appointments | appointments.delete');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show')->middleware('permission:appointments.view| appointments.create | services.appointments | appointments.delete');
    Route::post('/appointments/update-status', [AppointmentController::class, 'updateStatus'])->name('appointments.update.status');
    Route::post('/update-status', [DashboardController::class, 'updateStatus'])->name('dashboard.update.status');
    
    // Other admin routes...
});

Route::view('/about', 'frontend.about')->name('about');
Route::view('/contact', 'frontend.contact')->name('contact');

Route::get('/test/geocoding', [TestController::class, 'testGeocoding']);

Route::get('/test/config', function() {
    return response()->json([
        'google_maps_api_key' => config('services.google.maps_api_key'),
        'env_value' => env('GOOGLE_MAPS_API_KEY'),
        'config_path' => config_path('services.php')
    ]);
});


