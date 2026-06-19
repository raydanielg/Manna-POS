<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileCabinetFolder extends Model
{
    protected $fillable = ['user_id', 'parent_id', 'name', 'color'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(FileCabinet::class, 'folder_id');
    }
}
