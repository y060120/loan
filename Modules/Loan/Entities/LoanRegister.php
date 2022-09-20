<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Entities\User;
use Modules\Loan\Entities\LoanRepayment;

class LoanRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_type',
        'amount',
        'term',
        'loan_status'
    ];

    protected $table = 'customer_loan_register';

    /**
     * Get the user details
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function repayment()
    {
        return $this->hasMany(LoanRepayment::class);
    }
}
