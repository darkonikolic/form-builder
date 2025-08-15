<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function down()
    {
        Schema::dropIfExists('fields');
    }

    public function up()
    {
        Schema::create('fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('form_id');
            $table->json('configuration'); // Field config + i18n
            $table->json('validation_rules')->nullable(); // Validation rules + i18n
            $table->timestamps();

            $table->foreign('form_id')->references('id')->on('forms')->onDelete('cascade');
            $table->index('form_id');
        });
    }
};
