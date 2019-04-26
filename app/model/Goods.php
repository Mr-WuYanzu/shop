<?php

namespace App\model;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $table="p_wx_goods";
    protected $primaryKey='goods_id';
    public $timestamps = false;
}
