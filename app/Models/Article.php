<?php

namespace App\Models;
use App\User; 
use App\Models\Image; 

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{

    protected $fillable = [
        'user_id',
        'image_id',
        'type',
        'title',
        'description',
        'contents',
        'event_date_start',
        'event_date_end',
        'event_time'

      ];
   
      public function user()
      {
          return $this->belongsTo(User::class);
      }

      public function image()
      {
          return $this->belongsTo(Image::class, 'image_id');
      }


    // use HasFactory;
}
