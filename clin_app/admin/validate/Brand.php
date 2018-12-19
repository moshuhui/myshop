<?php
namespace app\admin\validate;
use think\Validate;
class Brand extends Validate
{
    protected $rule =   [
        'brand_name'  => 'require|unique:brand',
        'brand_url'   => 'url',
        'brand_description' => 'min:10',   
    ];
    protected $message  =   [
        'brand_name.require' => '品牌名称必须填写',
        'brand_name.unique'     => '品牌名称必须唯一',
        'brand_url.url'   => '品牌的网址不正确',
        'brand_description.min'  => '描述最少要10个字符'
    ];

}
