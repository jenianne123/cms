<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickLinks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',  
        'link_category',
        'type',
        'parent_link_id',
        'section',
        'title',
        'filename',
        'link',
    ];

    protected $primaryKey = 'id';
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}