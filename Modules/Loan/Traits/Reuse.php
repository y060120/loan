<?php
namespace Modules\Loan\Traits;

trait Reuse
{

    public function calculateRepayment($loanAmount, $termPlan, $userId, $loanId)
    {
        $loanValue = bcdiv($loanAmount, (float) $termPlan,10);
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

}
