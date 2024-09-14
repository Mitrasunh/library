<?php

use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\BorrowBookController;
use App\Http\Controllers\API\MemberController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/borrow', [BorrowBookController::class, 'borrow']);
Route::post('/return', [BorrowBookController::class, 'return']);
Route::get('/books', [BookController::class, 'index']);
Route::get('/members', [MemberController::class, 'index']);
