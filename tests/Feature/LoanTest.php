<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoanTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    // testing for loan registration
    public function test_customer_loan_registration()
    {
        $token = env('USER_TOKEN');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/loan/loanCreate', [
            'loan_type' => 'personal',
            'amount' => 10,
            'term' => 3,
            'loan_status' => 'PENDING']);

        $response->assertStatus(200);
    }

    // testing for admin for viewing all users
    public function test_admin_view_all_user()
    {
        $token = env('USER_TOKEN');        

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/admin/viewUser');

        $response->assertStatus(200);
    }

    // testing for admin for viewing pending loan
    public function test_admin_view_loan_pending()
    {
        $token = env('USER_TOKEN');
        $userId = 2;                // user id from users table

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/admin/showPendingLoan/' . $userId);

        $response->assertStatus(200);
    }

    // testing for admin to approve pending loan
    public function test_admin_approve_pending_loan()
    {
        $token = env('USER_TOKEN');
        $id = 1; // id column from customer_loan_register table

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/admin/approveLoan/' . $id);

        $response->assertStatus(200);
    }
    // customer view their loan status
    public function test_user_view_loan_status()
    {
        $token = env('USER_TOKEN');
        $loanId = 1; // id column from customer_loan_register table

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/loan/viewLoanStatus/' . $loanId);

        $response->assertStatus(200);
    }
    // customer views their repayment status
    public function test_user_view_repayment_status()
    {
        $token = env('USER_TOKEN');
        $loanId = 1; // id column from customer_loan_register table

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('api/loan/viewRepayment/' . $loanId);

        $response->assertStatus(200);
    }

    // testing for user paying their repayment
    public function test_user_pay_repayment()
    {
        $token = env('USER_TOKEN');
        $loanId = 1; // id column from customer_loan_register table
        $repayId = 6;  // id column from customer_loan_repayment table
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('api/loan/payRepayment/' . $repayId . '/loan/' . $loanId, [
            'amount' => 34,
        ]);

        $response->assertStatus(200);
    }

}
