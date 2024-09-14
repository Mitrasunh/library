<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'title', 'author', 'stock'];
    public $timestamps = false;

    public function borrowedBooks()
    {
        return $this->hasMany(BorrowedBook::class);
    }
}
