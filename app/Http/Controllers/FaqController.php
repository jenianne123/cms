<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Redirect;

use App\Models\Faq;
use App\Models\User;

class FaqController extends Controller{

    //display faqs
    public function table(){
        $faqs = Faq::all();
        return $faqs;
    }

    //create faqs into the database
    public function saveFaq(Request $request){
        if($request->id){
            $new = Faq::find($request->id);
        }else{
            $new = new Faq;
        }
        $new->question= $request->question;  
        $new->answer= $request->answer;  
        $new->user_id = auth()->user()->id;

        $res=$new->save();

        // $list = $this->table();
        return 1;
    }

    public function deleteFaqs(Request $request){
         $res= Faq::where('id', $request->id)->delete();

         return 1;
        //  $list = $this->table();
        //  return $list;
    }

    public function editForm($id){
        $faq = Faq::find($id);
        return $faq;

    } 

    public function editFaqSave(Request $request){
        $edit = Faq::find($request->id);
        $edit->question = $request->input('question');
        $edit->answer = $request->input('answer');
        $edit->user_id = auth()->user()->id;
        $res= $edit->save();

        if($res){
            return response()->json(['success'=>true]);
        }else{
            return response()->json(['success'=>false]);
        }

    }

    // public function editFaqSave(Request $request, $id)
    // {
    //     $edit = Faq::find($id);

    //     if (!$edit) {
    //         // Handle case where FAQ with given ID is not found
    //         return response()->json(['success' => false, 'message' => 'FAQ not found'], 404);
    //     }

    //     $edit->question = $request->input('question');
    //     $edit->answer = $request->input('answer');
    //     $edit->user_id = auth()->user()->id;

    //     $res = $edit->save();

    //     if ($res) {
    //         return response()->json(['success' => true]);
    //     } else {
    //         return response()->json(['success' => false, 'message' => 'Update failed']);
    //     }
    // }


    


   
}





