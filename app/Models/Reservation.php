<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['user_id', 'book_id', 'reservation_date', 'expires_at', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function book() {
        return $this->belongsTo(Book::class);
    }

    protected function casts(): array
    {
        return [
            'reservation_date' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }
}
