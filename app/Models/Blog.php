<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = ['title', 'content', 'image', 'category_id'];

    public function Category()
    {
        return $this->belongsTo(BlogCategory::class);
    }
}
