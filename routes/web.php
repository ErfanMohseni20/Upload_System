<?php

use Illuminate\Support\Facades\Route;


Route::prefix('file')->controller(\App\Http\Controllers\FileController::class)->group(function(){
    Route::get('/','Home')->name('home');
    Route::get('/create','create')->name('file.create');
    Route::post('/upload-file','UploadFile')->name('file.upload');
    Route::get('/show/{file}','showFile')->name('file.show');
    Route::get('/delete/{file}','deleteFile')->name('file.delete');
});