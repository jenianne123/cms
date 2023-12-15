<?php
 
 namespace App\Models;
 use App\User;
 
 use Illuminate\Database\Eloquent\Factories\HasFactory;
 use Illuminate\Database\Eloquent\Model;
 
 class GenericPage extends Model
 {
     use HasFactory;
 
 
     protected $fillable = [
         'user_id',
         'title',
         'description',
         'page_id',
         'category',
         'type',
         'content',
 
       ];
   
       public function user()
       {
           return $this->belongsTo(User::class);
       }

      

       
   
    //    public function parent(){
    //         return $this->hasOne(GenericPage::class, 'page_id', 'id');
    //    }


 }
 