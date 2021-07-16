<?php

namespace App\Models;

use App\Models\Traits\SerializeDateToIso8601;
use Chelout\RelationshipEvents\Concerns\HasBelongsToManyEvents;
use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use EloquentFilter\Filterable;

class Genre extends Model
{

    use SoftDeletes, Uuid, Filterable, SerializeDateToIso8601, HasBelongsToManyEvents;
    
    protected $fillable = ['name', 'is_active'];
    protected $dates = ['deleted_at'];

    protected $observables = ['belongsToManyAttached'];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean',
    ];

    public $incrementing = false;

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }

}
