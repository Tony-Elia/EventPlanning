<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::dropIfExists('venues');
        Schema::table('services', function (Blueprint $table) {
            $table->string('type')->default('service')->after('name');
            $table->integer('capacity')->nullable()->after('location');
            $table->text('address')->nullable()->after('capacity');
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->json('amenities')->nullable()->after('longitude');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            //
        });
    }
};
