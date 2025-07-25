<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class School extends Model
{
    use HasFactory;

    protected $guarded = [];
    public function ubicacion(): HasOne
    {
        return $this->hasOne(Ubicacion::class);
    }
    public function servicios(): HasOne
    {
        return $this->hasOne(Servicio::class);
    }
    
    public function ambientes(): HasOne
    {
        return $this->hasOne(Ambiente::class);
    }
    
    public function estadisticas(): HasMany
    {
        return $this->hasMany(Estadistica::class);
    }
}