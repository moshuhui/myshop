<?php
namespace app\admin\validate;
use think\Validate;
class Memberlevel extends Validate
{
    protected $rule =   [
        'level_name'  => 'require|unique:memberlevel'
    ];
    protected $message  =   [
        'level_name.require' => '会员级别名称必须填写',
        'level_name.unique'     => '会员级别必须唯一'
    ];

}
