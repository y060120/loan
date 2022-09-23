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
use Modules\Auth\Entities\User;
use Modules\Loan\Http\Requests\LoanRegisterRequest;

class LoanController extends Controller
{
    use Reuse;
    public function create(LoanRegisterRequest $request):string  // custom form request created
    {  
        try {
            // create new Loan for the user            
            $dataArray = LoanRegister::create([
                'user_id' => auth()->user()->id,
                'loan_type' => $request->loan_type,
                'amount' => $request->amount,
                'term' => $request->term,
                'loan_status' => $request->loan_status
            ]);
            if ($dataArray) {
                return response(trans('loan::messages.loanSubmit'), 200);
            } else {
                return response(trans('loan::messages.error'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }
    // view pending loans
    public function showPendingLoan($userId)
    {
        try {
            // fetch pending for approval records based on user id
            $loanData = $this->showPendingLoanTrait($userId);
            if ($loanData) {
                return response($loanData, 200);
            }

            return $userId;
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }
    //Approve Loan request
    public function approveLoan(int $id):string
    {
        try {
            $approveResponse = $this->approveLoanTrait($id);
            if ($approveResponse === trans('loan::messages.loanApproved')) {
                return response(trans('loan::messages.loanApproved'), 200);
            } else {
                return response(trans('loan::messages.nothing'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }
    // view all users and you can use the userId for sending requests
    public function viewUser()
    {
        try {
            $userData = User::all();
            if (count($userData) > 0) {
                return response($userData, 200);
            } else {
                return response(trans('loan::messages.nouser'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }

    public function viewLoanStatus(LoanRegister $loanId) // view loan approval status used Model binding
    {
        try {
            if(!is_array($loanId)){
                return $loanId;
            }            
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }

    public function viewRepayment($loanId) // view repayment status
    {
        try {
            $repayment = $this->viewRepaymentTrait($loanId); 
            if (count($repayment) > 0) {
                return response($repayment, 200);
            } else {
                return response(trans('loan::messages.noloan'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }
    // customer loan repayment
    public function payRepayment(Request $request, $repayId, $loanId): string
    {
        try {
            $userId = auth()->user()->id;
            $amount = $request->amount; // fetch customer amount

            $repayment = $this->payRepaymentTrait($userId, $amount, $repayId, $loanId);
            return $repayment;

        } catch (\Exception $e) {
            return response($e, 401);
        }
    }
}
