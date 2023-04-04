<?php

namespace App\Models;

use App\Resource\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Instance extends Model
{
    protected $fillable = [
        'instance',
        'slot',
    ];
    public function item()
    {
        return Resource::findBy('name', $this->instance);
    }
    public function slots(): HasMany
    {
        return $this->hasMany(InstanceSlot::class);
    }
    public function availableSlot()
    {
        $maxSlot = $this->slots->max('slot');

        for ($i = 1; $i <= $maxSlot + 1; $i++) {
            if ($this->slots->where('slot', $i)->isEmpty()) {
                return $i;
            }
        }
        return null;
    }
}
