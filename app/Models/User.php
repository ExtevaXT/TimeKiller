<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Resource\Items\Instances\Machine;
use App\Resource\Items\Instances\Machines\Generator;
use App\Resource\Items\Instances\Machines\Processor;
use App\Resource\Items\Instances\Storage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function energy()
    {
        $energy = 0;
        //$this->instances()
        foreach ($this->instances as $instance){
            if(($machine = $instance->item()) instanceof Generator){
                 $energy += $machine->production; // * $instance->loaded
            }
            if(($machine = $instance->item()) instanceof Processor){
                $energy -= $machine->consumption; // * $instance->loaded
            }
            // TODO Check energy storage
        }
        return $energy;
    }

    public function capacity()
    {
        $capacity = 0;
        foreach ($this->instances as $instance){
            if($instance->item() instanceof Storage and $instance->item()->contain == 'Items') {
                $capacity += $instance->item()->capacity;
            }
        }
        return $capacity;
    }
    public function freeSlots()
    {
        $freeSlots = $this->capacity();
        foreach ($this->instances as $instance){
            if($instance->item() instanceof Storage and $instance->item()->contain == 'Items') {
                $freeSlots -= $instance->slots->count();
            }
        }
        return $freeSlots;
    }
    public function items(){
        $instances = $this->instances->filter(fn ($instance) => $instance->item() instanceof Storage);
        //TODO merge $instances?
        return $instances;
    }
    public function storages(){
        return $this->instances->filter(fn ($instance) => $instance->item() instanceof Storage);
    }
    public function addItem($id, $amount = 1)
    {
        if ($this->freeSlots() > 0) {
            $instance = $this->instances->filter(
                fn ($instance) => $instance->item() instanceof Storage && $instance->availableSlot())
                ->first();
            //if($slot = $instance->availableForStackSlot($id))
            //    $slot->update(['amount'=>$slot->amount++]);
            //else
                $instance->slots()->create([
                    'slot'=>$instance->availableSlot(),
                    'item'=>$id,
                    'amount' =>$amount,
                    'loaded'=>Carbon::now()
                ]);
        }
    }
    public function instances(): HasMany
    {
        return $this->hasMany(Instance::class);
    }

    #region Default
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    #endregion
}
