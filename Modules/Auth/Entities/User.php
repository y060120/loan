<?php

namespace Modules\Auth\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Loan\Entities\LoanRegister;
use Modules\Loan\Entities\LoanRepayment;

class User extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];
    
    protected $table = 'users';

    public function loan(){
        return $this->hasOne(LoanRegister::class);
    }

    public function repayment(){
        return $this->hasMany(LoanRepayment::class);
    }
}
