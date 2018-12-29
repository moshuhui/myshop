<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Category as CategoryModel;
//use app\admin\model\Article as ArticleModel;
use catetree\Catetree;
class Category extends Controller
{
    public function lst()
    {   
        $cate = new Catetree();
        if(request()->isPost()){
            $sort = input('post.');
            $sortRes = $cate->cateSort($sort['sort'],db('category'));
            if($sortRes){
                $this->success("排序成功",'lst');
            }       
        }        
        $categoryRes = CategoryModel::order("sort DESC")->select();

        
        $categoryRes = $cate->catetree($categoryRes);

        $this->assign([
        	"categoryRes" => $categoryRes
        ]);
        return view();
    }

    public function add()
    {
    	if(request()->isPost()){
    		$data = input("post."); 
            //验证
            $validate = validate('Category');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            } 
            //处理图片上传
            if($_FILES['cate_img']['tmp_name']){                
                $data['cate_img'] = $this->upload();
            }  
    		$add = CategoryModel::insert($data);
    		if($add){
    			$this->success("增加商品分类成功！",'lst');
    		}else{
    			$this->error("增加商品分类失败!");
    		}
    		return;
    	}        
        $categoryRes = CategoryModel::select();
        $cate = new Catetree();
        $categoryRes = $cate->catetree($categoryRes);
        $this->assign([
            "categoryRes" => $categoryRes
        ]);
        return view();
    }

    public function edit($id)
    {	
    	if(request()->isPost()){
    		$data = input('post.');
    		//验证
            $validate = validate('Category');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            } 	 
            //处理图片上传
            if($_FILES['cate_img']['tmp_name']){
                $oldthumb = CategoryModel::where("id",$data['id'])->value('cate_img');
                if(file_exists(IMG_UPLOADS.$oldthumb)){
                    @unlink(IMG_UPLOADS.$oldthumb);
                }
                $data['cate_img'] = $this->upload();
            }      
    		$save = CategoryModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改商品分类成功！",'lst');
    		}else{
    			$this->error("修改商品分类失败!");
    		} 		

    	}
        $cateRes = CategoryModel::select();
        $cate = new Catetree();
        $cateRes = $cate->catetree($cateRes);
        $cates = CategoryModel::find($id);
        $this->assign([
        	'cates' => $cates,
            'cateRes' => $cateRes
        ]);
        return view();
    }

    public function del($id)
    {
       
        $cate = db("category");
        $cateTree = new Catetree();
        $cateTreeId = $cateTree->childrenids($id,$cate);
        $cateTreeId[] = intval($id);
        //$arr_sys = [1,2,3];  
        //$arrRes = array_intersect($arr_sys,$cateTreeId);  //判断数组之间的交集    
        //dump($cateTreeId); die;
        //if($arrRes){
            //$this->error("系统内置文章分类不允许删除!");
           // die;
        //}
        //删除文章前应该先删除该分类下的文章和缩略图     
        /*   
            foreach ($cateTreeId as $k => $v) {
                $artRes = ArticleModel::field('id,thumb')->where(array('cate_id'=>$v))->select();
                foreach ($artRes as $k1 => $v1) {
                    $thumbSrc = IMG_UPLOADS.$v1["thumb"];
                    if(file_exists($thumbSrc)){
                        @unlink($thumbSrc);
                    }
                    ArticleModel::where(array('id'=>$v1['id']))->delete();
                }
            }
        */
        //删除掉关联子类的缩略图
        foreach ($cateTreeId as $k => $v) {
            $oldcate_img = CategoryModel::where("id",$v)->value('cate_img');
            if(file_exists(IMG_UPLOADS.$oldcate_img)){
               @unlink(IMG_UPLOADS.$oldcate_img);
            }
        }
        $del = CategoryModel::where('id','in',$cateTreeId)->delete();
        if($del){
            $this->success("删除分类成功！",'lst');
        }else{
            $this->error("删除分类失败！");
        }
        
        return view();
    }


    public function upload(){
        $file = request()->file('cate_img');
        $info = $info = $file->move( ROOT_PATH.'public'.DS.'static'.DS.'uploads');
        if($info){              
            return $info->getSaveName();
            }else{
             echo $file->getError();
            die;
        }   
    } 


}
