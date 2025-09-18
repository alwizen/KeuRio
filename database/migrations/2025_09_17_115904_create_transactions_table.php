<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['income', 'expense']); // pemasukan atau pengeluaran
                        $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
           $table->date('transaction_date');
            $table->timestamps();
            
            // Index untuk performa query
                        $table->index(['type', 'transaction_date', 'category_id']);
            // $table->index(['type', 'transaction_date']);
            // $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};