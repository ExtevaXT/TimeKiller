<?php

namespace App\Models;

use App\Resource\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instance extends Model
{
    public function item()
    {
        return Resource::find($this->instance);
    }
    public function slots(): HasMany
    {
        return $this->hasMany(InstanceSlot::class);
    }
}
