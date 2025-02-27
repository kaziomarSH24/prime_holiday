<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    protected $fillable = ['name'];

    public function blog()
    {
        return $this->hasMany(Blog::class, 'category_id');
    }
}
