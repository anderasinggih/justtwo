<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'original_file_name',
        'file_path_original',
        'file_path_thumbnail',
        'file_type',
        'file_size_kb',
        'sort_order',
    ];


    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
