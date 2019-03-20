<?php

namespace App\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
class UserModel extends Model
{
    protected $table='api_user';
    public $timestamps=false;
}
