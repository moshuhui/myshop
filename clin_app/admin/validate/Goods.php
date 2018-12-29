<?php
namespace app\admin\validate;
use think\Validate;
class Goods extends Validate
{
    protected $rule =   [
        'goods_name'  => 'require|unique:goods'
    ];
    protected $message  =   [
        'goods_name.require' => '商品类型名称必须填写',
        'goods_name.unique'     => '商品类型必须唯一'
    ];

}
