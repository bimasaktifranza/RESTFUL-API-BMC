<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('riwayat_darurat', function (Blueprint $table) {
            $table->string('id')->primary(); // Sesuai diagram: String
            $table->string('pasien_no_reg'); // Relasi ke Pasien
            $table->string('bidan_id');      // Relasi ke Bidan
            
            // Sesuai diagram: Attributes
            $table->dateTime('waktu_dibuat');
            $table->dateTime('waktu_selesai')->nullable();
            $table->enum('status', ['PENDING', 'RESOLVED'])->default('PENDING');
            
            // Foreign Keys
            $table->foreign('pasien_no_reg')->references('no_reg')->on('pasien')->onDelete('cascade');
            $table->foreign('bidan_id')->references('id')->on('bidan')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('riwayat_darurat');
    }
};