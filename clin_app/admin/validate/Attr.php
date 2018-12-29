<?php
namespace app\admin\validate;
use think\Validate;
class Attr extends Validate
{
    protected $rule =   [
        'attr_name'  => 'require',
        'attr_values'   => 'require',
        'type_id'   => 'require'
    ];
    protected $message  =   [
        'attr_name.require' => '属性名称必须填写',
        'attr_values.require' => '属性值必须填写',
        'type_id.require' => '属性类型必须选择'
    ];

}
