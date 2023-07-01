<?php

namespace Bashmohandes7\ZoomService\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomOauth extends Model
{
    protected $fillable = ['provider','provider_value'];
}