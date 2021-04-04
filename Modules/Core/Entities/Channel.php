<?php

namespace Modules\Core\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Channel extends Model
{
    public static $SEARCHABLE = [ 'code', 'name', 'description', 'theme', 'hostname' ];
    protected $fillable = [ 'code', 'name', 'description', 'theme', 'hostname', 'default_locale_id', 'base_currency_id' ];

    // Get Channel Locales
    public function locales()
    {
        return $this->belongsToMany(Locale::class, 'channel_locales');
    }

    // Get the default Locale
    public function default_locale()
    {
        return $this->belongsTo(Locale::class);
    }

    // Get Channel Currencies
    public function currencies()
    {
        return $this->belongsToMany(Currency::class, 'channel_currencies');
    }

    // Get the base Currency
    public function base_currency()
    {
        return $this->belongsTo(Currency::class);
    }

    // Get the root Category
    public function root_category()
    {
        return $this->belongsTo(Category::class, 'root_category_id');
    }

    // Get URL from Storage
    private function get_url($path)
    {
        return $path ? Storage::url($path) : null;
    }

    // Get the logo URL
    public function getLogoUrlAttribute()
    {
        return $this->get_url($this->logo);
    }

    // Get favicon URL
    public function getFaviconUrlAttribute()
    {
        return $this->get_url($this->favicon);
    }
}
