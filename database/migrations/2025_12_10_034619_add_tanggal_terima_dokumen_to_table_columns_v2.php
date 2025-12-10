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
        \App\Models\TableColumn::create([
            'key' => 'tanggal_terima_dokumen',
            'label' => 'Tgl Terima Dok',
            'type' => 'date',
            'order' => 10, // Adjusting order to fit generally
            'is_visible' => true
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_columns_v2', function (Blueprint $table) {
            //
        });
    }
};
