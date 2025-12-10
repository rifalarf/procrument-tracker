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
        Schema::create('procurement_items', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->nullable();
            $table->string('mat_code')->nullable();
            $table->text('nama_barang')->nullable();
            $table->integer('qty')->nullable();
            $table->string('um')->nullable();
            $table->decimal('nilai', 15, 2)->nullable();
            $table->string('pg')->nullable();
            $table->string('user_requester')->nullable();
            $table->string('bagian')->nullable();
            $table->date('tanggal_terima_dokumen')->nullable();
            $table->string('proc_type')->nullable();
            $table->string('buyer')->nullable();
            $table->string('status')->nullable();
            $table->date('tanggal_status')->nullable();
            $table->text('emergency')->nullable();
            $table->string('no_po')->nullable();
            $table->string('nama_vendor')->nullable();
            $table->date('tanggal_po')->nullable();
            $table->date('tanggal_datang')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('last_updated_at')->nullable();
            $table->string('last_updated_by')->nullable();
            $table->json('extra_attributes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_items');
    }
};
