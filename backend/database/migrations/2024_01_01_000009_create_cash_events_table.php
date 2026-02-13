<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_events', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['inflow', 'outflow']);
            $table->decimal('amount', 12, 2);
            $table->string('reference_type', 50);
            $table->unsignedBigInteger('reference_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['date', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_events');
    }
};
