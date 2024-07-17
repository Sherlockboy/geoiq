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
        Schema::create('google_geo_targets', function (Blueprint $table) {
            $table->bigInteger('criteria_id')->unsigned()->unique();
            $table->string('name');
            $table->string('canonical_name');
            $table->bigInteger('parent_id')->nullable();
            $table->string('country_code');
            $table->string('target_type');
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('google_geo_targets');
    }
};
