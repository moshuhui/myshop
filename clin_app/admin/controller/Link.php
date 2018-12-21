<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Link as LinkModel;
use catetree\Catetree;
class Link extends Controller
{
    public function lst()
    {
        if(request()->isPost()){
           $sort = input('post.');
           $catetree = new Catetree();
           $sortRes = $catetree->cateSort($sort['sort'],db('link'));
           if($sortRes){
             $this->success("排序成功",'lst');
           }
        }
        $linkRes = LinkModel::order('id desc')->paginate(6);
        $this->assign([
        	"linkRes" => $linkRes
        ]);
        return view();
    }

    public function add()
    {
    	if(request()->isPost()){
    		$data = input("post."); 
    		if(stripos($data["link_url"],'http://') === false)  //strpos不支持大小写 和 stripos支持大小写
			$data["link_url"] = "http://".$data["link_url"]; 
            //验证
            $validate = validate('Link');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            }          		
            //处理图片上传
    		if($_FILES['link_img']['tmp_name']){
    			$data['link_img'] = $this->upload();
    		}   
    		$add = LinkModel::insert($data);
    		if($add){
    			$this->success("增加友情链接成功！",'lst');
    		}else{
    			$this->error("增加友情链接失败!");
    		}
    		return;
    	}
        return view();
    }

    public function edit($id)
    {	
    	if(request()->isPost()){
    		$data = input('post.');
    		if(stripos($data["link_url"],'http://') === false)  //strpos不支持大小写 和 stripos支持大小写
			$data["link_url"] = "http://".$data["link_url"];
            //验证
            $validate = validate('Link');
            if(!$validate->check($data)){
                $this->error($validate->getError());
               die;
            }   
    		//处理图片上传
    		if($_FILES['link_img']['tmp_name']){
    			$oldLinkImg = LinkModel::where("id",$data['id'])->value('link_img');
    			if(file_exists(IMG_UPLOADS.$oldLinkImg)){
    				@unlink(IMG_UPLOADS.$oldLinkImg);
    			}
    			$data['link_img'] = $this->upload();
    		}     		 
    		$save = LinkModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改友情链接成功！",'lst');
    		}else{
    			$this->error("修改友情链接失败!");
    		} 		

    	}
        
        $links = LinkModel::find($id);
        $this->assign([
        	'links' => $links
        ]);
        return view();
    }

    public function del($id)
    {
        $oldlinkImg = LinkModel::where("id",$id)->value('link_img');
    	if(file_exists(IMG_UPLOADS.$oldlinkImg)){
    	   @unlink(IMG_UPLOADS.$oldlinkImg);
    	}
        $del = LinkModel::where("id",$id)->delete();
        if($del){
        	$this->success("删除友情链接成功！",'lst');
        }else{
        	$this->error("删除友情链接失败！");
        }
        return view();
    }  

	public function upload(){
	    $file = request()->file('link_img');
	    $info = $info = $file->move( ROOT_PATH.'public'.DS.'static'.DS.'uploads');
	    if($info){	            
		    return $info->getSaveName();
		    }else{
		     echo $file->getError();
		    die;
		}   
	}


}
