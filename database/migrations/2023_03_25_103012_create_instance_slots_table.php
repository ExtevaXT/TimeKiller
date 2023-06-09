<?php

use App\Models\Instance;
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
        Schema::create('instance_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Instance::class)->constrained();
            $table->integer('slot');
            $table->integer('item');
            $table->integer('amount');
            $table->dateTime('loaded');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instance_slots');
    }
};
