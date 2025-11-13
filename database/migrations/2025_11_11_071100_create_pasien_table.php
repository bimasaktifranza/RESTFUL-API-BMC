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
        Schema::create('pasien', function (Blueprint $table) {
            $table->string('no_reg', 25)->primary(); // Primary key, input manual
            $table->string('username', 25)->unique();
            $table->string('nama', 100);
            $table->string('password');
            $table->string('alamat', 60);
            $table->string('umur', 3); // umur sebagai string, max 3 karakter
            $table->string('gravida', 3); // jumlah kehamilan
            $table->string('paritas', 3); // jumlah persalinan
            $table->string('abortus', 3); // jumlah keguguran
            $table->string('bidan_id', 25)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasien');
    }
};
