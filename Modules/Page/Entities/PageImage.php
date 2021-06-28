<?php

namespace Modules\Page\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Traits\HasFactory;

class PageImage extends Model
{
    use HasFactory;

    protected $fillable = [ "page_id", "path" ];
    protected $appends = [ "path_url" ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function getPathUrlAttribute(): ?string
    {
        return $this->getImage();
    }

    public function getImage(): ?string
    {
        if ( !$this->path ) return null;
        return $this->getPath();
    }

    private function getPath(): string
    {
        $file_array = $this->getFileNameArray();
        return Storage::url("{$file_array['folder']}/{$file_array['file']}");
    }

    private function getFileNameArray(): array
    {
        $path_array = explode("/", $this->path);
        $file_name = $path_array[count($path_array) - 1];
        unset($path_array[count($path_array) - 1]);

        return [
            "folder" => implode("/", $path_array),
            "file" => $file_name
        ];
    }
}
