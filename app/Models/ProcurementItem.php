<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'extra_attributes' => 'array',
        'tanggal_terima_dokumen' => 'date',
        'tanggal_status' => 'date',
        'tanggal_po' => 'date',
        'tanggal_datang' => 'date',
        'tanggal_datang' => 'date',
        'nilai' => 'decimal:2',
        'buyer' => \App\Enums\BuyerEnum::class,
        'status' => \App\Enums\ProcurementStatusEnum::class,
    ];

    public function logs()
    {
        return $this->hasMany(Log::class);
    }
}
