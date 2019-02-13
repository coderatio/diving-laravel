<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    protected $fillable = [
        'name', 'school_id', 'school_name'
    ];

    public function getFee($feeID, $schoolID) {
        return $this->where('id', $feeID)->where('school_id', $schoolID)->first();
    }
}
