<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Type as TypeModel;
use app\admin\model\Attr as AttrModel;
class Type extends Controller
{
    public function lst()
    {
        $typeRes = TypeModel::order('id desc')->paginate(6);
        $this->assign([
        	"typeRes" => $typeRes
        ]);
        return view();
    }

    public function add()
    {
    	if(request()->isPost()){
    		$data = input("post.");     		
            //验证
            $validate = validate('Type');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            }  
    		$add = TypeModel::insert($data);
    		if($add){
    			$this->success("增加商品类型成功！",'lst');
    		}else{
    			$this->error("增加商品类型失败!");
    		}
    		return;
    	}
        return view();
    }

    public function edit($id)
    {	
    	if(request()->isPost()){  
            $data = input('post.');  		
            //验证
            $validate = validate('Type');
            if(!$validate->check($data)){
                $this->error($validate->getError());
               die;
            }  
    		$save = TypeModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改商品类型成功！",'lst');
    		}else{
    			$this->error("修改商品类型失败!");
    		} 		

    	}
        
        $types = TypeModel::find($id);
        $this->assign([
        	'types' => $types
        ]);
        return view();
    }

    public function del($id)
    {
        //删除之前，先删除掉属性值
        AttrModel::where('type_id',$id)->delete();
        
        $del = TypeModel::where("id",$id)->delete();
        if($del){
        	$this->success("删除商品类型成功！",'lst');
        }else{
        	$this->error("删除商品类型失败！");
        }
        return view();
    }  

}
