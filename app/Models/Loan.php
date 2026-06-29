<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id', 'copy_id', 'loan_date', 
        'return_date', 'actual_return_date', 'status'
    ];

    protected $casts = [
        'loan_date' => 'datetime',
        'return_date' => 'datetime',
        'actual_return_date' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function copy() {
        return $this->belongsTo(Copy::class);
    }

    public function fine() {
        return $this->hasOne(Fine::class); // Un préstamo puede generar una multa
    }
}
