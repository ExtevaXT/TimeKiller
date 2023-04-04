<?php

namespace App\Models;

use App\Resource\Resource;
use Illuminate\Database\Eloquent\Model;

class InstanceSlot extends Model
{
    protected $fillable = [
        'item',
        'amount',
        'slot',
        'loaded',
    ];

    public function item()
    {
        return Resource::find($this->item);
    }
}
