<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Cate as CateModel;
use app\admin\model\Article as ArticleModel;
use catetree\Catetree;
class Cate extends Controller
{
    public function lst()
    {   
        $cate = new Catetree();
        if(request()->isPost()){
            $sort = input('post.');
            $sortRes = $cate->cateSort($sort['sort'],db('cate'));
            if($sortRes){
                $this->success("排序成功",'lst');
            }       
        }        
        $cateRes = CateModel::order("sort DESC")->select();

        
        $cateRes = $cate->catetree($cateRes);

        $this->assign([
        	"cateRes" => $cateRes
        ]);
        return view();
    }

    public function add()
    {
    	if(request()->isPost()){
    		$data = input("post.");  
            if(in_array($data['pid'], [1,3])){
                $this->error('系统分类不能作为上级栏目!');
            }
            if($data["pid"]==2){
                $data["cate_type"]=3;
            }
            //验证
            $validate = validate('Cate');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            } 
    		$add = CateModel::insert($data);
    		if($add){
    			$this->success("增加分类成功！",'lst');
    		}else{
    			$this->error("增加分类失败!");
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
            if(in_array($data['pid'], [1,3])){
                $this->error('系统分类不能作为上级栏目!');
            }
            if($data["pid"]==2){
                $data["cate_type"]=3;
            }  		
    		//验证
            $validate = validate('Cate');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            } 	 
    		$save = CateModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改分类成功！",'lst');
    		}else{
    			$this->error("修改分类失败!");
    		} 		

    	}
        $cateRes = CateModel::select();
        $cate = new Catetree();
        $cateRes = $cate->catetree($cateRes);
        $cates = CateModel::find($id);
        $this->assign([
        	'cates' => $cates,
            'cateRes' => $cateRes
        ]);
        return view();
    }

    public function del($id)
    {
       
        $cate = db("cate");
        $cateTree = new Catetree();
        $cateTreeId = $cateTree->childrenids($id,$cate);
        $cateTreeId[] = intval($id);
        $arr_sys = [1,2,3];  
        $arrRes = array_intersect($arr_sys,$cateTreeId);  //判断数组之间的交集    
        //dump($cateTreeId); die;
        if($arrRes){
            $this->error("系统内置文章分类不允许删除!");
            die;
        }
        //删除文章前应该先删除该分类下的文章和缩略图        
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

        $del = CateModel::where('id','in',$cateTreeId)->delete();
        if($del){
            $this->success("删除分类成功！",'lst');
        }else{
            $this->error("删除分类失败！");
        }
        
        return view();
    } 


}
