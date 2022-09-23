<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customer_loan_register', function(Blueprint $table)
        {
            $table->index('user_id');
            $table->index('loan_status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customer_loan_register', function (Blueprint $table)
        {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['loan_status']);
        });
    }
};
