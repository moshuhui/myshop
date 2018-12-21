<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Article as ArticleModel;
use app\admin\model\Cate as CateModel;
use catetree\Catetree;
class Article extends Controller
{
    public function lst()
    {   
        $articleRes = ArticleModel::alias('a')->field("a.*,b.cate_name")->join("cate b",'a.cate_id=b.id')->order('a.id desc')->paginate(6);
        $this->assign([
            "articleRes" => $articleRes
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
            $validate = validate('Article');
            if(!$validate->check($data)){
               $this->error($validate->getError());
               die;
            } 
            //处理图片上传
            if($_FILES['thumb']['tmp_name']){                
                $data['thumb'] = $this->upload();
            }  
            $data['addtime'] = time();          
    		$add = ArticleModel::insert($data);
    		if($add){
    			$this->success("增加文章成功！",'lst');
    		}else{
    			$this->error("增加文章失败!");
    		}
    		return;
    	}        
        $cateRes = CateModel::select();
        $cate = new Catetree();
        $cateRes = $cate->catetree($cateRes);
        $this->assign([
            "cateRes" => $cateRes
        ]);
        return view();
    }

    public function edit($id)
    {	
    	if(request()->isPost()){
    		$data = input('post.');   
            if(stripos($data["link_url"],'http://') === false)  //strpos不支持大小写 和 stripos支持大小写
            $data["link_url"] = "http://".$data["link_url"];              
    		//验证
            $validate = validate('Article');
            if(!$validate->check($data)){
               $this->error($validate->getError());
               die;
            } 	
            //处理图片上传
            if($_FILES['thumb']['tmp_name']){
                $oldthumb = ArticleModel::where("id",$data['id'])->value('thumb');
                if(file_exists(IMG_UPLOADS.$oldthumb)){
                    @unlink(IMG_UPLOADS.$oldthumb);
                }
                $data['thumb'] = $this->upload();
            }             
    		$save = ArticleModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改文章成功！",'lst');
    		}else{
    			$this->error("修改文章失败!");
    		} 		

    	}
        $cateRes = CateModel::select();
        $cate = new Catetree();
        $cateRes = $cate->catetree($cateRes);
        $articles = ArticleModel::find($id);
        $this->assign([
        	'articles' => $articles,
            'cateRes' => $cateRes
        ]);
        return view();
    }

    public function del($id)
    {
       
        $oldthumb = ArticleModel::where("id",$id)->value('thumb');
        if(file_exists(IMG_UPLOADS.$oldthumb)){
           @unlink(IMG_UPLOADS.$oldthumb);
        }
        $del = ArticleModel::where("id",$id)->delete();
        if($del){
            $this->success("删除文章成功！",'lst');
        }else{
            $this->error("删除文章失败！");
        }
        return view();
    } 

    public function upload(){
        $file = request()->file('thumb');
        $info = $info = $file->move( ROOT_PATH.'public'.DS.'static'.DS.'uploads');
        if($info){              
            return $info->getSaveName();
            }else{
             echo $file->getError();
            die;
        }   
    }

    public function imglist(){

        $_files = my_scandir();
        $files = array();
        foreach ($_files as $k => $v) {
            if(is_array($v)){
                foreach ($v as $k1 => $v1) {
                   $v1 = str_replace(UEDITOR, HTTP_UEDITOR, $v1);
                   $files[] = $v1;
                }
            }else{
                 $v = str_replace(UEDITOR, HTTP_UEDITOR, $v);
                 $files[] = $v;
            }
        }
        //dump($files); die;
        $this->assign(["files"=>$files]);
        return view();

    }


    public function delimg(){
       $imgsrc = input("imgsrc");
       $imgsrc = DEL_UEDITOR.$imgsrc;
       if(file_exists($imgsrc)){
          if(@unlink($imgsrc)){
            echo 1;
          }else{
            echo 2;
          }
       }else{
          echo $imgsrc;
       }
    }
}
