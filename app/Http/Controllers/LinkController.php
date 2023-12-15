<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use App\Models\Link;
use App\Models\User;

class LinkController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request.
        $request->validate([
            'quick_link' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Get the image file.
        $quick_link = $request->file('quick_link');

        // Generate a unique filename for the image.
        $filename = time() . '.' . $quick_link->getClientOriginalExtension();

        // Save the image file to the server.
        $quick_link->storeAs('public/articles', $filename);

        // Create a new Image model in the database.
        $linkModel = new Quick_Link();
        $linkModel->name = $filename;
        $linkModel->path = 'public/images/' . $filename;
        $linkModel->category = $request->get('category');
        $linkModel->status = $request->get('status');
        $linkModel->save();

        // Return a success response.
        return response()->json([
            'message' => 'Image uploaded successfully!',
        ]);
    }

    public function saveDoc(Request $request)
    {
        $user = Auth::user();
        $file = $request->image;
        $ext = $file->getClientOriginalExtension();
        
        $filename = md5(explode('.', $file->getClientOriginalName())[0]).'.'.$ext;
        Storage::disk('public')->put('/news/'.$filename, File::get($file));

        $imageModel = new Images();
        $imageModel->filename = $filename;
        $imageModel->user_id = $user->id;
        $imageModel->category = $request->category;
        $imageModel->status = $request->status;
        $imageModel->save();

        return 1;
    }
}
