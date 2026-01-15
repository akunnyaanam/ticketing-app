<?php

namespace App\Models;

use App\Concerns\DefaultGuarded;
use App\Concerns\GenericFillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    /** @use HasFactory<\Database\Factories\EventFactory> */
    use HasFactory, SoftDeletes, DefaultGuarded;
}
