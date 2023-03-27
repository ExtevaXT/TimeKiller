<?php

namespace App\Models;

use App\Resource\Resource;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    public function item()
    {
        return Resource::find($this->instance);
    }
}
