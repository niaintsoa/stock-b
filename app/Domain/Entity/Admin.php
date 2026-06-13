<?php

namespace App\Domain\Entity;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['role'];

    public function user()
    {
        return $this->morphOne(User::class, 'profile');
    }
}
