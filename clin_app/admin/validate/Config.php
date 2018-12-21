<?php
namespace app\admin\validate;
use think\Validate;
class Config extends Validate
{
    protected $rule =   [
        'ename'  => 'require|unique:config',
        'cname'   => 'require|unique:config',
    ];
    protected $message  =   [
        'ename.require' => '设置项英文名称必须填写',
        'ename.unique'     => '设置项英文名称必须唯一',
        'cname.require' => '设置项中文名称必须填写',
        'cname.unique'     => '设置项中文称必须唯一',
    ];

}
