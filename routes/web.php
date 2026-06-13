<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/password-reset/{token}', function () {
    return 'Espace client en cours de construction. Vous pourrez définir votre mot de passe ici prochainement.';
})->name('password.reset');
