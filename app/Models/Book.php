<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;


use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = ['category_id', 'title', 'isbn', 'editorial', 'published_year', 'description'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function authors() {
        return $this->belongsToMany(Author::class); // Muchos a muchos
    }

    public function copies() {
        return $this->hasMany(Copy::class);
    }

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
}
