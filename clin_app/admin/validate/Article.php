<?php
namespace app\admin\validate;
use think\Validate;
class Article extends Validate
{
    protected $rule =   [
        'title'  => 'require|unique:article|max:180',
        'cate_id'  => 'require',
        //'email'  => 'email',
        //'link_url'  => 'url',
    ];
    protected $message  =   [
        'title.require' => '标题必须填写',
        'title.unique'     => '标题必须唯一',
        'title.max'     => '标题过长',
        'cate_id.require'     => '请选择相关栏目',
    ];

}
