<?php

namespace App\Models;
use App\User;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
      'question',
      'answer',
      'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
