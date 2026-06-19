<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileCabinet extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'file_path', 'file_name',
        'file_type', 'file_extension', 'file_size', 'category',
        'related_id', 'related_type', 'visibility'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(FileCabinetFolder::class, 'folder_id');
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2) . ' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return number_format($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function getIconAttribute()
    {
        return match($this->file_extension) {
            'pdf' => 'pdf',
            'doc', 'docx' => 'doc',
            'xls', 'xlsx' => 'xls',
            'jpg', 'jpeg', 'png', 'gif', 'webp' => 'image',
            'zip', 'rar', '7z' => 'zip',
            default => 'file',
        };
    }
}
