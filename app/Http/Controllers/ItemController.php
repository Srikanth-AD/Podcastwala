<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function markAsRead(Request $request)
    {
        $request->validate(['itemId' => 'required|integer']);

        $item = Item::where('id', $request->input('itemId'))
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) {
            return response()->json(['status' => 0, 'message' => 'Item not found']);
        }

        $item->is_mark_as_read = true;
        $item->save();

        return response()->json(['status' => 1, 'message' => 'This item has been marked as read']);
    }

    public function markAllPrevAsRead(Request $request)
    {
        $request->validate(['itemId' => 'required|integer']);

        $item = Item::where('id', $request->input('itemId'))
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) {
            return response()->json(['status' => 0, 'message' => 'Item not found']);
        }

        $updatedIds = Item::where('user_id', Auth::id())
            ->where('podcast_id', $item->podcast_id)
            ->where('is_mark_as_read', false)
            ->where('published_at', '<', $item->published_at)
            ->pluck('id')
            ->toArray();

        Item::whereIn('id', $updatedIds)->update(['is_mark_as_read' => true]);

        return response()->json([
            'status' => 1,
            'data' => $updatedIds,
            'message' => 'Previous items in this podcast have been marked as read',
        ]);
    }

    public function markAsFavorite(Request $request)
    {
        $request->validate(['itemId' => 'required|integer']);

        $item = Item::where('id', $request->input('itemId'))
            ->where('user_id', Auth::id())
            ->first();

        if (!$item) {
            return response()->json(['status' => 0, 'message' => 'Item not found']);
        }

        $item->is_mark_as_favorite = !$item->is_mark_as_favorite;
        $item->save();

        return response()->json([
            'status' => 1,
            'currentValue' => $item->is_mark_as_favorite,
            'message' => 'This item has been updated',
        ]);
    }

    public function search(Request $request)
    {
        $query = strip_tags($request->input('query', ''));

        $items = Item::with('podcast')
            ->where('user_id', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->get();

        return view('items.searchresults', compact('items', 'query'));
    }
}
