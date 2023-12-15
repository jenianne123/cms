<?php

namespace App\Http\Controllers;

use App\Models\GenericPage;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GenericController extends Controller

{
    //display generic pages 
    public function table(){
        $page = GenericPage::all();
        $list = array();
        $parent = "";
        foreach($page as $d){
            if($d->page_id){
                $parent_v = GenericPage::find($d->page_id);
                if($parent_v){
                    $parent = $parent_v->title;
                }
            }
            $list[] = [
                'id' => $d->id,
                'title' => $d->title,
                'type' => $this->typeValue($d->type),
                'parent' => $parent,
                'description' => $d->description,
                'content' => $d->content ?? '<p>Content of the editor.</p>',
            ];
        }
        return $list;
        
    }

    public function typeValue($id){
        switch ($id) {
            case 1:
              return "link";
              break;
            case 2:
              return "article";
              break;
            
            default:
              return " ";
          }
    }
    //
    //create faqs into the database
    public function savePage(Request $request){
        if($request->id){
            $new = GenericPage::find($request->id);
        }else{
            $new = new GenericPage;
        }
        $new->title= $request->title;  
        $new->description= $request->description;  
        $new->page_id= $request->page_id;  
        $new->category= $request->category;  
        $new->type= $request->type;   
        $new->content= $request->content;
        $new->user_id = auth()->user()->id;

        $res=$new->save();

        // $list = $this->table();
        return 1;
    }

    //delete pages
    public function deleteContent(Request $request){ 
        // $res= GenericPage::where('id', $request->id)->delete();

        // return 1;
       //  $list = $this->table();
       //  return $list;
       $record = Genericpage::find($request->id);

    if(GenericPage::where('page_id',$record->id)->exists()){
        return response()->json(['message' => 'Cannot delete record. It is being used as a parent menu.'], 400);
      
    } 
    $record->delete();

    return response()->json(['message' => 'Record deleted successfully.']);
    }

    //edit form
    // public function editForm($id){
    //     $d = GenericPage::find($id);
    //     $list = [
    //         'details' => $d,
    //         'parentOption' => $this->getparent($d->category),
    //         'content' => $article->content ?? '<p>Content of the editor.</p>',
         
        
    //     ];
    //     return $list;
    // } 
    public function editForm($id)
    {
        $d = GenericPage::find($id);

        $list = [
            'details' => $d,
            'parentOption' => $this->getParent($d->category),
            'content' => $d->content ?? '<p>Content of the editor.</p>',
        ];

        return $list;
    }

    public function getParent($id){
        // dd($id);
        if($id == 5 || $id == 2){
            $par_cat = 1;
        }else{
            $par_cat = $id-1;
        }
        $parent = GenericPage::where('category',$par_cat)->get();
        // dd($parent);
        return  $parent;
    }

    public function editFormSave(Request $request)
{
    try {
        $id = $request->input('id');
        $d = GenericPage::find($id);

        if (!$d) {
            return response()->json(['message' => 'Record not found.'], 404);
        }

        $d->title = $request->input('title');
        $d->description = $request->input('description');
        $d->page_id = $request->input('page_id');
        $d->category = $request->input('category');
        $d->type = $request->input('type');
        $d->content = $request->input('content');
        $d->user_id = auth()->user()->id;

        $d->save();

        return response()->json(['message' => 'Record updated successfully.']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error updating record.', 'error' => $e->getMessage()], 500);
    }
}


    
}
