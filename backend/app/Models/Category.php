<?php

namespace App\Models;

use App\Models\Traits\SerializeDateToIso8601;
use App\Traits\Uuid;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{

    use SoftDeletes, Uuid, Filterable, SerializeDateToIso8601;

    protected $fillable = ['name', 'description', 'is_active'];
    protected $dates = ['deleted_at'];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];

    public $incrementing = false;

    public function modelFiler()
    {
        return $this->provideFilter(CategoryFilter::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class)->withTrashed();
    }

}
