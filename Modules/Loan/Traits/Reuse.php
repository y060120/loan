<?php

namespace Modules\Loan\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Loan\Entities\LoanRegister;
use Modules\Loan\Entities\LoanRepayment;
use Modules\Auth\Entities\User;
use DB;
use Illuminate\Support\Facades\Hash;

trait Reuse
{
    public function calculateRepayment($loanAmount, $termPlan, $userId, $loanId)
    {
        $loanValue = bcdiv($loanAmount, (float) $termPlan, 10);
        $term = bcmul($loanValue, $termPlan, 10); // multiply divided amount with term plan

        $subtraction = bcsub($loanAmount, $term, 10);

        for ($i = 0; $i < $termPlan; $i++) {
            $dayCount = ($i + 1) * 7; // mutiplying day count by 7 for weekly payments
            $datePlusSevenDays = date('Y-m-d', strtotime("+$dayCount days"));

            if ($i === $termPlan - 1 && $subtraction > 0) { // checking condition for last element in an array and subtracted amount greater than 0
                $loanValue = bcadd($loanValue, $subtraction, 10); // adding the remaining value from subtraction at the last payment
            }

            $repayment[] = array(
                'user_id' => $userId,
                'loan_id' => $loanId,
                'payment_date' => $datePlusSevenDays,
                'term_amount' => (float) $loanValue,
                'total_terms' => $i + 1,
                'repayment_status' => 'Not Paid',
            );
        }
        return $repayment;
    }

   public function createLoanTrait(array $fields): string
   {
       // create new Loan for the user
       $dataArray = LoanRegister::create([
           'user_id' => auth()->user()->id,
           'loan_type' => $fields['loan_type'],
           'amount' => $fields['amount'],
           'term' => $fields['term'],
           'loan_status' => $fields['loan_status']
       ]);
       return true;
   }

   public function showPendingLoanTrait(int $userId): array
   {
       // fetch pending for approval records based on user id
       $loanData = LoanRegister::where([
           'user_id' => $userId,
           'loan_status' => 'PENDING',
       ])->get()->toArray();
       return $loanData;
   }

   public function approveLoanTrait(int $id): string
   {
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

           return trans('loan::messages.loanApproved');
       } else {
           return trans('loan::messages.nothing');
       }
   }
   public function viewLoanStatusTrait($loanId): array
   {
       $userId = auth()->user()->id;

       $loanData = LoanRegister::where([
           'id' => $loanId,
           'user_id' => $userId,
       ])->get()->toArray();

       return $loanData;
   }

   public function viewRepaymentTrait($loanId): array
   {
       $userId = auth()->user()->id;

       $loanData = LoanRepayment::where([
           'loan_id' => $loanId,
           'user_id' => $userId,
       ])->orderBy('id', 'ASC')->get()->toArray();

       return $loanData;
   }

   public function payRepaymentTrait($userId, $amount, $repayId, $loanId): string
   {
       $repayment = LoanRepayment::find($repayId);
       $dataTerm = LoanRegister::find($loanId); // fetching to check all terms are paid or not
    
       if (!is_null($dataTerm) && !is_null($repayment)) {
           if ($repayment->repayment_status != 'Paid') {
               $repayAmount = $repayment->term_amount; // fetch amount to be paid

               if ($amount >= $repayAmount) { // check if its greater or equal
                   $balanceAmount = bcsub($amount, $repayAmount, 10); // fetch the balance amount for storing database
                   $repayment->balance_amount = $balanceAmount;
                   $repayment->repayment_status = 'Paid';
                   $repayment->save();
               } else {
                   return response(trans('loan::messages.amountLarge', ['amount' => $repayment->term_amount]), 200); // return if amount is low
               }
               $termCount = $dataTerm->term;

               $loanCount = LoanRepayment::where([
                   'loan_id' => $loanId,
                   'user_id' => $userId,
                   'repayment_status' => 'Paid'
               ])->get()->toArray();

               $totalBalance = array_sum(array_column($loanCount, 'balance_amount')); // sum all balance amount

               if ($termCount === count($loanCount)) { // Check for whether all repayments are paid
                   $dataTerm->loan_status = 'Fully Paid';
                   $dataTerm->balance_amount = $totalBalance; // store balance amount in db if paid higher
                   $dataTerm->save();
                   return response(trans('loan::messages.allRepay'), 200);
               }

               return response(trans('loan::messages.repay'), 200);
           } else {
               return response(trans('loan::messages.alreadyPaid'), 400);
           }
       } else {
           return response(trans('loan::messages.invalid'), 400);
       }
   }
   public function doLogin($fields){
        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad credentials',
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token,
        ];

        return response($response, 200);
   }
}
