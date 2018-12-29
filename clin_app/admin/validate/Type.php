<?php
namespace app\admin\validate;
use think\Validate;
class Type extends Validate
{
    protected $rule =   [
        'type_name'  => 'require|unique:type'
    ];
    protected $message  =   [
        'type_name.require' => '商品类型名称必须填写',
        'type_name.unique'     => '商品类型必须唯一'
    ];

}
