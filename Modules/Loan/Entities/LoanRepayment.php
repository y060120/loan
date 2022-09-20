<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Auth\Entities\User;
use Modules\Loan\Entities\LoanRegister;

class LoanRepayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_id',
        'payment_date',
        'term_amount',
        'total_terms',
        'repayment_status'
    ];
    
    protected $table = 'customer_loan_repayment';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function loan()
    {
        return $this->belongsTo(LoanRegister::class, 'loan_id', 'id');
    } 
}
