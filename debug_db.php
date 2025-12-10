<?php

use App\Models\ProcurementItem;

$item = ProcurementItem::latest()->first();

if ($item) {
    echo "ID: " . $item->id . "\n";
    echo "Mat Code: " . $item->mat_code . "\n";
    echo "Nama Barang: " . $item->nama_barang . "\n";
    echo "Bagian: " . $item->bagian . "\n";
    echo "Buyer: " . ($item->buyer instanceof \UnitEnum ? $item->buyer->value : $item->buyer) . "\n";
    echo "Status: " . ($item->status instanceof \UnitEnum ? $item->status->value : $item->status) . "\n";
    echo "Raw Attributes: " . print_r($item->getAttributes(), true) . "\n";
} else {
    echo "No items found.\n";
}
