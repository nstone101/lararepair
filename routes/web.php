<?php

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



Route::get('/migrate', function(){
    \Artisan::call('migrate');
    \Artisan::call('update');
    \Artisan::call('config:cache');
    \Artisan::call('config:clear');
});

Route::get('/email/{id}','RepairController@sendEmail');

  
Route::get('/show-signature/{img}', function($img){
    $path = public_path('uploads/signs/').$img;
    remove_whitespace($path);
});


Route::get('/reset', function(){
    \Artisan::call('reset');
});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::get('/backup/{id}/download','BackupController@download');
    Route::get('/repair/export-excel','RepairController@exportExcel');
    Route::get('/repair/export-pdf','RepairController@exportPdf');
    Route::get('/repair/invoice/{id}/{type}','RepairController@invoice');
    Route::get('/pos/view/{id}/{pdf}','PosController@view');
    Route::get('/customer/export-excel','CustomerController@exportExcel');
    Route::get('/customer/export-pdf','CustomerController@exportPdf');
    
    Route::get('/generate-barcode/{text}', function($text){
        $type = request('type') ?? 'C128';
        $data = DNS1D::getBarcodePNG($text, $type,1,33);
        $data = base64_decode($data);
        $im = imagecreatefromstring($data);
        if ($im !== false) {
            // header('Content-Type: image/png');
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
            die();
        }
    });


    Route::get('/generate-qrcode/{text}', function($text){
        $data = QrCode::format('png')->size(50)->margin(0)->generate($text);
        $im = imagecreatefromstring($data);
        if ($im !== false) {
            // header('Content-Type: image/png');
            header('Content-Type: image/png');
            imagepng($im);
            imagedestroy($im);
            die();
        }
    });


});

// Used to get translation in json format for current locale

Route::get('/js/lang', function () {
    if(App::environment('local'))
        Cache::forget('lang.js');

    if(\Cache::has('locale')){
        config(['app.locale' => \Cache::get('locale')]);
    }
    
    $strings = Cache::rememberForever('lang.js', function () {
        $lang = config('app.locale');
        $files   = glob(resource_path('lang/' . $lang . '/*.php'));
        $strings = [];
        foreach ($files as $file) {
            $name           = basename($file, '.php');
            $strings[$name] = require $file;
        }
        return $strings;
    });
    header('Content-Type: text/javascript');
    echo('window.i18n = ' . json_encode($strings) . ';');
    exit();
})->name('assets.lang');


Route::get('/pos', function () {
    return view('pos');
})->name('pos');


Route::get('/{vue?}', function () {
    return view('welcome');
})->where('vue', '^((?!api).)*');

// })->where('vue', '[\/\w\.-]*')->name('welcome');


