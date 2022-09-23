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

class LoanController extends Controller
{
    use Reuse;
    public function create(Request $request):string
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
            $value = $this->createLoanTrait($fields);
            if ($value) {
                return response(trans('loan::messages.loanSubmit'), 200);
            } else {
                return response(trans('loan::messages.error'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }
    // view pending loans
    public function showPendingLoan(int $userId)
    {
        try {
            // fetch pending for approval records based on user id
            $loanData = $this->showPendingLoanTrait($userId);
            if ($loanData) {
                return response($loanData, 200);
            }
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

    public function viewLoanStatus($loanId) // view loan approval status
    {
        try {
            $loanStatus = $this->viewLoanStatus($loanId); 
            if (count($loanStatus) > 0) {
                return response($loanStatus, 200);
            } else {
                return response(trans('loan::messages.noloan'), 400);
            }
        } catch (\Exception $e) {
            return response($e, 401);
        }
    }

    public function viewRepayment($loanId) // view repayment status
    {
        try {
            $repayment = $this->viewRepayment($loanId); 
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
