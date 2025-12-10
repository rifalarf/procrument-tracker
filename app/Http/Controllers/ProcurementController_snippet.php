<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProcurementController extends Controller
{
    // ... existing index ...

    // ... existing updateStatus (can trigger quickUpdate internally or keep separate) ...

    public function quickUpdate(Request $request, $id)
    {
        $item = \App\Models\ProcurementItem::findOrFail($id);

        $request->validate([
            'field' => 'required|in:status,bagian,pg',
            'value' => 'nullable', // Validation refined below
        ]);

        $field = $request->input('field');
        $value = $request->input('value');
        $oldValue = $item->{$field};

        // Specific Validation
        if ($field === 'status') {
            if (!$value) return response()->json(['success' => false, 'message' => 'Status required'], 422);
            // Verify Enum
            if (!\App\Enums\ProcurementStatusEnum::tryFrom($value)) {
                 return response()->json(['success' => false, 'message' => 'Invalid status'], 422);
            }
        }
        if ($field === 'bagian') {
             // Bagian can be nullable? Assuming strict Enum required if provided.
             if ($value && !\App\Enums\BagianEnum::tryFrom($value)) {
                 return response()->json(['success' => false, 'message' => 'Invalid bagian'], 422);
             }
        }

        if ($oldValue != $value) {
            $item->update([
                $field => $value,
                'last_updated_by' => auth()->user()->email ?? 'System',
                'last_updated_at' => now(),
                // If status changed, update tanggal_status
                'tanggal_status' => ($field === 'status') ? now() : $item->tanggal_status,
            ]);

            // Log
            \App\Models\Log::create([
                'procurement_item_id' => $item->id,
                'changed_by' => auth()->user()->email ?? 'System',
                'change_detail' => ucfirst($field) . " quick changed from '" . ($oldValue instanceof \UnitEnum ? $oldValue->value : $oldValue) . "' to '{$value}'",
            ]);
        }

        return response()->json(['success' => true]);
    }
    
    // ... rest of controller ...
}
