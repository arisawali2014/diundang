<?php

use App\Models\User;
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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->boolean('presence')->default(false);
            $table->text('comment')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('parent_id')->nullable();
            $table->boolean('is_admin')->nullable()->default(false);
            $table->string('own')->nullable()->unique();
            $table->timestamps();

            // $table->foreign('parent_id')->references('uuid')->on('comments')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
