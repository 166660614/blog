<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/26 0026
 * Time: 11:04
 */
namespace App\Http\Controllers\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use App\Http\Controllers\Controller;
class AliPay extends Controller{
    public $app_id;
    public $gate_way;
    public $rsaPrivateKey='./priv.key';
    public $rsaPublicKey='./pub.key';
}