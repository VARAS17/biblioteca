<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fine extends Model
{
    protected $fillable = ['loan_id', 'user_id', 'amount', 'days_late', 'status', 'paid_at'];

    public function loan() {
        return $this->belongsTo(Loan::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
