<?php

namespace App\Http\Controllers;

use App\Models\EditedImageCount;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $images = $request->user?->images();
        return view('image-editor', compact('images'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function incrementEditedImageCount()
    {
        EditedImageCount::create([
            'user_id' => auth()->id(),
            'count' => 1,
            'subscription_id' => auth()->user()->subscribed->id
        ]);
    }
}
