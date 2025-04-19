<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Reference to users.id (UUID)
            $table->uuid('user_id');
            $table->string('payment_method'); // 'sslcommerz' or 'stripe'
            $table->string('transaction_id')->unique(); // gateway transaction identifier
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('BDT');
            $table->enum('status', ['Pending','Processing','Completed','Failed','Canceled'])
                  ->default('Pending');
            $table->json('metadata')->nullable(); // store raw gateway response
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')
                  ->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
