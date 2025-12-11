<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1️⃣ Buat enum type di PostgreSQL
        DB::statement("CREATE TYPE protein_enum AS ENUM ('-', '+', '++', '+++')");
        DB::statement("CREATE TYPE aseton_enum AS ENUM ('-', '+')");
        DB::statement("CREATE TYPE molase_enum AS ENUM ('0', '1', '2', '3')");
        DB::statement("CREATE TYPE airketuban_enum AS ENUM ('J', 'U', 'M','D','K')");

        // 2️⃣ Buat table
        Schema::create('catatan_partograf', function (Blueprint $table) {
            $table->string('id', 25)->primary();
            $table->string('waktu_catat', 25)->nullable();
            $table->integer('djj')->nullable();
            $table->integer('pembukaan_servik')->nullable();
            $table->integer('penurunan_kepala')->nullable();
            $table->integer('nadi_ibu')->nullable();
            $table->decimal('suhu_ibu')->nullable();
            $table->integer('sistolik')->nullable();
            $table->integer('diastolik')->nullable();
            $table->enum('aseton', ['-', '+'])->nullable();  // Laravel enum tetap bisa
            $table->enum('protein', ['-', '+', '++', '+++'])->nullable();
            $table->decimal('volume_urine')->nullable();
            $table->string('obat_cairan', 100)->nullable();
            $table->enum('air_ketuban', ['J', 'U', 'M','D','K'])->nullable();
            $table->enum('molase', ['0', '1', '2', '3'])->nullable();
            $table->integer('kontraksi_frekuensi')->nullable();
            $table->integer('kontraksi_durasi')->nullable();
            $table->string('partograf_id', 25)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_partograf');
        DB::statement("DROP TYPE IF EXISTS protein_enum");
        DB::statement("DROP TYPE IF EXISTS aseton_enum");
    }
};
