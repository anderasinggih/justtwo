<?php

namespace App\Traits;

use App\Models\Relationship;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToRelationship
{
    protected static function bootBelongsToRelationship()
    {
        static::creating(function (Model $model) {
            if (!$model->relationship_id && Auth::check()) {
                $relationship = Auth::user()->relationship;
                if ($relationship) {
                    $model->relationship_id = $relationship->id;
                }
            }
        });

        static::addGlobalScope('relationship', function (Builder $builder) {
            if (Auth::check()) {
                $relationship = Auth::user()->relationship;
                if ($relationship) {
                    $builder->where('relationship_id', $relationship->id);
                } else {
                    // If no relationship, user shouldn't see anything for scoped models
                    $builder->whereRaw('1 = 0');
                }
            }
        });
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }
}
