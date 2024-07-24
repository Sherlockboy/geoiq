<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ZipCodeSearch extends Model
{
    protected $guarded = ['id'];

    /**
     * Interact with the user's first name.
     */
    protected function areaCode(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => explode(',', $value),
            set: fn(array $value) => implode(',', $value),
        );
    }
}
