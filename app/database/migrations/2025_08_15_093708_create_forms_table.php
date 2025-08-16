<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function down()
    {
        Schema::dropIfExists('forms');
    }

    public function up()
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id'); // Foreign key to users table
            $table->json('name'); // Array for i18n
            $table->json('description'); // Array for i18n
            $table->boolean('is_active')->default(true);
            $table->json('configuration')->nullable(); // Locale list and other
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('user_id');
        });
    }
};
