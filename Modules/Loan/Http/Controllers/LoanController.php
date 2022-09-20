<?php

namespace Modules\Loan\Http\Controllers;

use DB;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Loan\Entities\LoanRegister;
use Modules\Loan\Entities\LoanRepayment;
use Modules\Loan\Traits\Reuse;

class LoanController extends Controller
{
    use Reuse;
    
    public function create(Request $request)
    {
        // validate loan registration
        $fields = $request->validate([
            'loan_type' => 'required|string|max:100',
            'amount' => 'required|integer',
            'term' => 'required|integer',
            'loan_status' => 'required|string',
        ]);
        try {
            // create new Loan for the user
            LoanRegister::create([
                'user_id' => auth()->user()->id,
                'loan_type' => $fields['loan_type'],
                'amount' => $fields['amount'],
                'term' => $fields['term'],
                'loan_status' => $fields['loan_status'],
            ]);

            return response(trans('loan::messages.loanSubmit'), 200);
        } catch (\Exception$e) {
            return response($e, 401);
        }
    }
// view pending loans
    public function showLoan($userId)
    {
        try {
            // fetch pending for approval records based on user id
            $loanData = LoanRegister::where([
                'user_id' => $userId,
                'loan_status' => 'PENDING',
            ])->get();

            return response($loanData, 200);
        } catch (\Exception$e) {
            return response($e, 401);
        }
    }

//Approve Loan request
    public function approveLoan($id)
    {
        try {
            $dataTerm = LoanRegister::find($id);

            if (!empty($dataTerm) && $dataTerm->loan_status == 'PENDING') {
                $termPlan = $dataTerm->term; // fetch total no. of terms for calculation
                $loanAmount = $dataTerm->amount;
                $userId = $dataTerm->user_id;
                $loanId = $dataTerm->id;
                $currDate = date('Y-m-d');

                $dataTerm->loan_status = 'APPROVED';
                $dataTerm->loan_approved_date = $currDate;
                $dataTerm->save(); // update loan table with approved status

                $repayment = $this->calculateRepayment($loanAmount, $termPlan, $userId, $loanId); // calling an reusable trait to calculate

                DB::table('customer_loan_repayment')->insert($repayment);

                return response(trans('loan::messages.loanApproved'), 200);
            } else {
                return response(trans('loan::messages.nothing'), 400);
            }

        } catch (\Exception$e) {
            return response($e, 401);
        }
    }

    public function viewLoanStatus($loanId) // view loan approval status
    {
        try {
            $userId = auth()->user()->id;
            $loanData = LoanRegister::where([
                'id' => $loanId,
                'user_id' => $userId,
            ])->get();

            if (count($loanData) > 0) {
                return response($loanData, 200);
            } else {
                return response(trans('loan::messages.noloan'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }

    public function viewRepayment($loanId)  // view repayment status
    {
        try {
            $userId = auth()->user()->id;
            $loanData = LoanRepayment::where([
                'loan_id' => $loanId,
                'user_id' => $userId,
            ])->orderBy('id','ASC')
            ->get();
            
            if (count($loanData) > 0) {
                return response($loanData, 200);
            } else {
                return response(trans('loan::messages.noloan'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }    
    // customer loan repayment 
    public function payRepayment(Request $request, $repayId, $loanId) 
    {
        try{
            $userId = auth()->user()->id;
            $amount = $request->amount;    // fetch customer amount

            $repayment = LoanRepayment::find($repayId);
            $dataTerm = LoanRegister::find($loanId);  // fetching to check all terms are paid or not
            
            if(!is_null($dataTerm) && !is_null($repayment)){

                if($repayment->repayment_status != 'Paid'){

                    $repayAmount = $repayment->term_amount; // fetch amount to be paid

                    if($amount >= $repayAmount){  // check if its greater or equal
                        $balanceAmount = bcsub($amount, $repayAmount, 10); // fetch the balance amount for storing database
                        $repayment->balance_amount = $balanceAmount;
                        $repayment->repayment_status = 'Paid';
                        $repayment->save();
                    }  
                    $termCount = $dataTerm->term;
        
                   $loanCount = LoanRepayment::where([
                        'loan_id' => $loanId,
                        'user_id' => $userId,
                        'repayment_status' => 'Paid'
                    ])->get()->toArray();
                   
                   $totalBalance = array_sum(array_column($loanCount, 'balance_amount')); // sum all balance amount
        
                    if($termCount === count($loanCount)){      // Check for whether all repayments are paid
                        $dataTerm->loan_status = 'Fully Paid';
                        $dataTerm->balance_amount = $totalBalance;  // store balance amount in db if paid higher
                        $dataTerm->save();
                        return response(trans('loan::messages.allRepay'), 200);
                    }
        
                    return response(trans('loan::messages.repay'), 200);
                }else{
                    return response(trans('loan::messages.alreadyPaid'), 400);
                }
            }else{
                return response(trans('loan::messages.invalid'), 400);
            }            
            
        }catch (\Exception $e) {
            return response($e, 401);
        }
    }
}
