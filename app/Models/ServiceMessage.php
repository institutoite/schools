<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceMessage extends Model
{
    use HasFactory;
    protected $fillable = ['message', 'service', 'boton', 'whatsapp'];
}
