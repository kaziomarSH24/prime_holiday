<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Continent extends Model
{
    protected $fillable = ['name'];

    public function countries():HasMany
    {
        return $this->hasMany(Country::class);
    }
}
