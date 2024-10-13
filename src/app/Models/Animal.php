<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

abstract class Animal extends Model
{
    use HasFactory;
    abstract public function getSoundFile();
}
