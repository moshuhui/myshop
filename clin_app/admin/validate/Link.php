<?php
namespace app\admin\validate;
use think\Validate;
class Link extends Validate
{
    protected $rule =   [
        'link_name'  => 'require|unique:link',
        'link_url'   => 'url',
        'link_description' => 'min:10',   
    ];
    protected $message  =   [
        'link_name.require' => '链接名称必须填写',
        'link_name.unique'     => '链接名称必须唯一',
        'link_url.url'   => '链接的网址不正确',
        'link_description.min'  => '描述最少要10个字符'
    ];

}
