<?php

namespace ZoomService\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomOauth extends Model
{
    protected $fillable = ['provider','provider_value'];
}
