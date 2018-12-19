<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Brand as BrandModel;

class Brand extends Controller
{
    public function lst()
    {
        $brandRes = BrandModel::order('id desc')->paginate(6);
        $this->assign([
        	"brandRes" => $brandRes
        ]);
        return view();
    }

    public function add()
    {
    	if(request()->isPost()){
    		$data = input("post."); 
    		if(stripos($data["brand_url"],'http://') === false)  //strpos不支持大小写 和 stripos支持大小写
			$data["brand_url"] = "http://".$data["brand_url"]; 
            //验证
            $validate = validate('Brand');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            }          		
            //处理图片上传
    		if($_FILES['brand_img']['tmp_name']){
    			$data['brand_img'] = $this->upload();
    		}   
    		$add = BrandModel::insert($data);
    		if($add){
    			$this->success("增加品牌成功！",'lst');
    		}else{
    			$this->error("增加品牌失败!");
    		}
    		return;
    	}
        return view();
    }

    public function edit($id)
    {	
    	if(request()->isPost()){
    		$data = input('post.');
    		if(stripos($data["brand_url"],'http://') === false)  //strpos不支持大小写 和 stripos支持大小写
			$data["brand_url"] = "http://".$data["brand_url"];
            //验证
            $validate = validate('brand');
            if(!$validate->check($data)){
                $this->error($validate->getError());
               die;
            }   
    		//处理图片上传
    		if($_FILES['brand_img']['tmp_name']){
    			$oldBrandImg = BrandModel::where("id",$data['id'])->value('brand_img');
    			if(file_exists(IMG_UPLOADS.$oldBrandImg)){
    				@unlink(IMG_UPLOADS.$oldBrandImg);
    			}
    			$data['brand_img'] = $this->upload();
    		}     		 
    		$save = BrandModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改品牌成功！",'lst');
    		}else{
    			$this->error("修改品牌失败!");
    		} 		

    	}
        
        $brands = BrandModel::find($id);
        $this->assign([
        	'brands' => $brands
        ]);
        return view();
    }

    public function del($id)
    {
        $oldBrandImg = BrandModel::where("id",$id)->value('brand_img');
    	if(file_exists(IMG_UPLOADS.$oldBrandImg)){
    	   @unlink(IMG_UPLOADS.$oldBrandImg);
    	}
        $del = BrandModel::where("id",$id)->delete();
        if($del){
        	$this->success("删除品牌成功！",'lst');
        }else{
        	$this->error("删除品牌失败！");
        }
        return view();
    }  

	public function upload(){
	    $file = request()->file('brand_img');
	    $info = $info = $file->move( ROOT_PATH.'public'.DS.'static'.DS.'uploads');
	    if($info){	            
		    return $info->getSaveName();
		    }else{
		     echo $file->getError();
		    die;
		}   
	}


}
