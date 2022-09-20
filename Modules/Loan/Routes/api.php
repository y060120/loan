<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/loan', function (Request $request) {
//     return $request->user();
// });


//protected routes
Route::group(['middleware'=>['auth:sanctum']], function (){
    Route::prefix('loan')->group(function() {     // user routes    
        Route::post('/loanCreate', 'LoanController@create');  // register a loan    
        Route::get('/viewLoanStatus/{loanId}', 'LoanController@viewLoanStatus');   // view loan
        Route::get('/viewRepayment/{loanId}', 'LoanController@viewRepayment');   // view repayment status
        Route::post('/payRepayment/{repayId}/loan/{loanId}', 'LoanController@payRepayment'); // pay customer repayment using 
    });

    Route::prefix('admin')->group(function() {         // admin routes
        Route::get('/viewLoan/{userId}', 'LoanController@showLoan');  // view loan requests by user id     
        Route::post('/approveLoan/{id}', 'LoanController@approveLoan');  // approve loan requests by loan register id from loan register table  
    });
});