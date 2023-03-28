<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Resource\Items\Instances\Machines\Instances\Instances\Instances\Machine;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public function Energy()
    {
        $energy = 0;
        //$this->instances()
        foreach ($this->instances as $instance){
            if(($machine = $instance->item()) instanceof Machine){
                 $energy += $machine->voltage; // * $instance->loaded
            }
        }
        return $energy;
    }
    public function EnergyVoltage()
    {
        $energy = 0;
        //$this->instances()
        foreach ($this->instances as $instance){
            if(($machine = $instance->item()) instanceof Machine){
                $energy += $machine->voltage;
            }
        }
        return $energy;
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
