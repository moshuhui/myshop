<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Attr as AttrModel;
use app\admin\model\Type as TypeModel;
class Attr extends Controller
{
    public function lst()
    {
        $type_id = input('type_id');
        $attrRes = AttrModel::alias('a')->field('a.*,t.type_name')->join('type t','a.type_id=t.id')->where(array('a.type_id'=>$type_id))->order('a.id desc')->paginate(6);
        $this->assign([
        	"attrRes" => $attrRes,
            "type_id" => $type_id
        ]);
        return view();
    }

    public function add()
    {
    	$type_id = input('type_id');

        if(request()->isPost()){
    		$data = input("post.");     		
            //验证
            $validate = validate('Attr');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            }
            //将输入的值中有中文逗号的转换一下
            $data['attr_values'] = str_replace('，', ',', $data['attr_values']);

    		$add = AttrModel::insert($data);
    		if($add){
    			$this->success("增加类型属性成功！",url('lst',['type_id'=>$data['type_id']]));
    		}else{
    			$this->error("增加类型属性失败!");
    		}
    		return;
    	}
        $typeRes = TypeModel::select();
        $this->assign([
            "type_id" => $type_id,
            "typeRes" => $typeRes
        ]);
        return view();
    }

    public function edit($id)
    {	

        if(request()->isPost()){  
            $data = input('post.');  		
            //验证
            $validate = validate('Attr');
            if(!$validate->check($data)){
                $this->error($validate->getError());
               die;
            } 
            //将输入的值中有中文逗号的转换一下
            $data['attr_values'] = str_replace('，', ',', $data['attr_values']); 
    		$save = AttrModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改类型属性成功！",url('lst',['type_id'=>$data['type_id']]));
    		}else{
    			$this->error("修改类型属性失败!");
    		} 		

    	}
        $typeRes = TypeModel::select();
        $attrs = AttrModel::find($id);
        $this->assign([
        	'attrs' => $attrs,
            'typeRes' => $typeRes

        ]);
        return view();
    }

    public function del($id,$type_id)
    {
        $del = AttrModel::where("id",$id)->delete();
        if($del){
        	$this->success("删除类型属性成功！",url('lst',['type_id'=>$type_id]));
        }else{
        	$this->error("删除类型属性失败！");
        }
        return view();
    }  

}
