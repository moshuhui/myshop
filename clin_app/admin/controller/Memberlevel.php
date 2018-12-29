<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Memberlevel as MemberlevelModel;
class Memberlevel extends Controller
{
    public function lst()
    {
        $memberlevelRes = MemberlevelModel::order('id desc')->paginate(6);
        $this->assign([
        	"memberlevelRes" => $memberlevelRes
        ]);
        return view();
    }

    public function add()
    {
    	if(request()->isPost()){
    		$data = input("post.");     		
            //验证
            $validate = validate('Memberlevel');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            }  
    		$add = MemberlevelModel::insert($data);
    		if($add){
    			$this->success("增加会员级别成功！",'lst');
    		}else{
    			$this->error("增加会员级别失败!");
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
            $validate = validate('Memberlevel');
            if(!$validate->check($data)){
                $this->error($validate->getError());
               die;
            }  
    		$save = MemberlevelModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改会员级别成功！",'lst');
    		}else{
    			$this->error("修改会员级别失败!");
    		} 		

    	}
        
        $memberlevels = MemberlevelModel::find($id);
        $this->assign([
        	'memberlevels' => $memberlevels
        ]);
        return view();
    }

    public function del($id)
    {
               
        $del = MemberlevelModel::where("id",$id)->delete();
        if($del){
        	$this->success("删除会员级别成功！",'lst');
        }else{
        	$this->error("删除会员级别失败！");
        }
        return view();
    }  

}
