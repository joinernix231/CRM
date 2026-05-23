<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('/dashboard');
});

Route::get('/login', fn () => view('login'));
Route::redirect('/app', '/login');

Route::get('/dashboard', fn () => view('dashboard'));

Route::get('/clients', fn () => view('clients'));
Route::get('/clients/create', fn () => view('client'));
Route::get('/clients/{id}', fn () => view('client'))->whereNumber('id');
