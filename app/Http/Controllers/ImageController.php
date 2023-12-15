<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;


class ImageController extends Controller
{
    //display images
    public function tableImages()
    {
        $images = Image::select('images.*', 'images.filename')
            ->latest('created_at') // Retrieve images in descending order of creation (latest first)
            ->get();

        // Add the base URL to the filename to create complete image URLs
        $images->transform(function ($image) {
            $image->url = url('/storage/images/' . $image->filename);
            return $image;
        });

        return $images;
    }

    public function saveDoc(Request $request)
{
    $user = Auth::user();
    $files = $request->file('images');

    // Loop through each uploaded file
    foreach ($files as $file) {
        // Get the original filename
        $originalFilename = $file->getClientOriginalName();

        // Generate a unique filename for storage
        $ext = $file->getClientOriginalExtension();
        $filename = md5(explode('.', $originalFilename)[0]) . '.' . $ext;

        // Save the file to storage
        Storage::disk('public')->put('/images/' . $filename, File::get($file));

        // Create a new Image model in the database for each file
        $imageModel = new Image();
        $imageModel->filename = $filename;
        $imageModel->orig_filename = $originalFilename; // Use the correct variable
        $imageModel->user_id = $user->id;
        $imageModel->save();
    }

    return 1;
}


    public function deleteImage(Request $request){
        $res = Image::where('id', $request->id)->delete();
        return 1;
    }
}
