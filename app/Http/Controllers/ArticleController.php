<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\User;
use App\Models\Image;


class ArticleController extends Controller  
{
    //
    public function tableArticle(){
        $articles = Article::leftJoin('images','articles.image_id','=','images.id')
        ->select('articles.*', 'images.filename')
        ->get();
        return $articles; 
        // $articles = Article::all();
        // return  $articles;
    }
   

    public function deleteArticle(Request $request)
    {
        $article = Article::find($request->id);

        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }

        // Check if the article has an associated image
        if ($article->image_id) {
            // Find the associated image
            $image = Image::find($article->image_id);
            if ($image) {

                $folder = ($article->type == 2) ? 'news' : (($article->type == 3) ? 'articles' : null);
                if ($folder) {
                    // Delete the associated image file from the correct folder in storage
                    Storage::disk('public')->delete("/$folder/" . $image->filename);
                } 

                // // Delete the associated image file from storage
                // Storage::disk('public')->delete('/news/' . $image->filename);
                // Delete the image record from the database
                $image->delete();
            }
        }

        // Delete the article
        $article->delete();

        return response()->json(['message' => 'Article and associated image deleted'], 200);
    }

    public function editForm($id){
    $d = Article::find($id);
    $article = Article::leftJoin('images', 'articles.image_id', '=', 'images.id')
        ->select('articles.*', 'images.filename', 'images.id as imageid')
        ->where('articles.id', $id)
        ->first();

    $list =[
        'details'=> $article,
        'existingImage' => $article->filename,
        'contents' => $article->contents ?? '<p></p>',
    ];


   
    return $list;

} 
 //create faqs into the database
 public function saveArticle(Request $request){
// dd($request->title);
    // $imageId = $request->input('image_id');
   
    if($request->id){
        $new = Article::find($request->id);
        
    }else{
        $new = new Article;
        
    }
   
    $new->type= $request->type;  
    $new->title= $request->title;  
    $new->description= $request->description;  
    $new->contents= $request->contents;  
    $new->event_date_start= $request->event_date_start;  
    $new->event_date_end= $request->event_date_end;  
    $new->event_time= $request->event_time;  
    $new->user_id = auth()->user()->id;
    $new->image_id = $request->imageid;
   

    $res=$new->save();
  
    // $list = $this->table();
    return 1;
}

public function saveImage(Request $request) {
    $id = 0;

    // Retrieve type from the request
    $type = $request->input('type');

    if ($type != 1) {
        $file = $request->file('image');

        if ($file) {
            $originalFilename = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            
            $filename = md5(explode('.', $file->getClientOriginalName())[0]) . '.' . $ext;

            

            // Determine the folder based on the type
            $folder = ($type == 2) ? 'news' : (($type == 3) ? 'articles' : null);

            if ($folder) {
                // Use the determined folder to store the image
                Storage::disk('public')->put("/$folder/$filename", File::get($file));

                // Continue with the rest of the code
                if ($request->id) {
                    $imageModel = Image::find($request->id);
                    $imageModel->filename = $filename;
                    $imageModel->orig_filename = $originalFilename; // Use the correct variable
                } else {
                    $imageModel = new Image;
                    $imageModel->filename = $filename;
                    $imageModel->orig_filename = $originalFilename; // Use the correct variable
                    $imageModel->user_id = auth()->user()->id;
                }

                $imageModel->save();
                $id = $imageModel->id;
            } else {
                // Log an error if an unexpected type is encountered
                \Log::error('Unexpected type:', ['type' => $type]);
            }
        }
    }

    return $id;
}




 


}
