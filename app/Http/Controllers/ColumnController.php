<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ColumnController extends Controller
{
    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $id) {
            \App\Models\TableColumn::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['success' => true]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'label' => 'required|string',
            'type' => 'required|in:text,number,date,select',
            'options' => 'nullable|array',
        ]);

        $key = \Illuminate\Support\Str::slug($data['label'], '_');

        \App\Models\TableColumn::create(array_merge($data, [
            'key' => 'extra_' . $key,
            'is_dynamic' => true,
            'order' => \App\Models\TableColumn::max('order') + 1,
        ]));

        return back()->with('success', 'Column added.');
    }
}
