<?php
namespace app\admin\validate;
use think\Validate;
class Category extends Validate
{
    protected $rule =   [
        'cate_name'  => 'require|unique:category',
    ];
    protected $message  =   [
        'cate_name.require' => '分类名称必须填写',
        'cate_name.unique'     => '分类名称必须唯一'
    ];

}
