<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'descriptions',
        'img',
        'categoria_id',
        'user_id'
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
