<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FlexibilityItem;

class FlexibilityItemController extends Controller
{
    public function index()
    {
        return view('flexibility-items.index');
    }

    public function data()
    {
        return FlexibilityItem::latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string',
            'point_cost' => 'required|integer',
            'type' => 'required|in:LATE,ALPHA',
            'max_late_minutes' => 'nullable|integer',
            'stock_limit' => 'nullable|integer'
        ]);

        FlexibilityItem::create([
            'item_name' => $request->item_name,
            'point_cost' => $request->point_cost,
            'type' => $request->type,
            'max_late_minutes' => $request->max_late_minutes,
            'stock_limit' => $request->stock_limit
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil ditambahkan'
        ]);
    }

    public function show($id)
    {
        return FlexibilityItem::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $item = FlexibilityItem::findOrFail($id);

        $request->validate([
            'item_name' => 'required|string',
            'point_cost' => 'required|integer',
            'type' => 'required|in:LATE,ALPHA',
            'max_late_minutes' => 'nullable|integer',
            'stock_limit' => 'nullable|integer'
        ]);

        $item->update([
            'item_name' => $request->item_name,
            'point_cost' => $request->point_cost,
            'type' => $request->type,
            'max_late_minutes' => $request->max_late_minutes,
            'stock_limit' => $request->stock_limit
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item berhasil diupdate'
        ]);
    }

    public function destroy($id)
    {
        FlexibilityItem::destroy($id);
        return response()->json(['success' => true]);
    }
}