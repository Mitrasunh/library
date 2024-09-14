<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'name'];
    public $timestamp = false;

    public function borrowedBooks()
    {
        return $this->hasMany(BorrowedBook::class);
    }
}
