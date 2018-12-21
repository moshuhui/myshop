<?php
namespace app\admin\controller;
use think\Controller;
use app\admin\model\Config as ConfigModel;
use catetree\Catetree;
class Config extends Controller
{
    public function configlist(){

        if(request()->isPost()){
            $data = input('post.');
            //复选框空选问题暂时解决方法
            $checkFields2D = db('config')->where(array('form_type'=>'checkbox'))->field('ename')->select();
            //转化成一维数组
            $checkFields=array();
            if($checkFields2D){
                foreach ($checkFields2D as $k => $v) {
                    $checkFields[] = $v['ename'];
                }
            }
            //dump($checkFields);die;
            $allFields = array();
            //先处理文字提交效果
            foreach ($data as $k => $v) {
                $allFields[] = $k;
                if(is_array($v)){
                    $value = implode(',',$v);
                    ConfigModel::where(array('ename'=>$k))->update(['config_value'=>$value]);
                }else{
                    ConfigModel::where(array('ename'=>$k))->update(['config_value'=>$v]);
                }
            }
            //对比提交过来的是否有该字段，没有直接改为空值
            foreach ($checkFields as $k => $v) {
                if(!in_array($v, $allFields)){
                   ConfigModel::where(array('ename'=>$v))->update(['config_value'=>'']); 
                }
            }
            //图片上传
            if($_FILES){
                foreach ($_FILES as $k => $v) {
                    if($v['tmp_name']){
                        $oldImgsrc = IMG_UPLOADS.ConfigModel::where(array('ename'=>$k))->value('config_value'); 
                        if(file_exists($oldImgsrc)){
                            @unlink($oldImgsrc);
                        }
                        $imgsrc = $this->upload($k);                        
                        ConfigModel::where(array('ename'=>$k))->update(['config_value'=>$imgsrc]); 
                    }
                }
            }
            //dump($_FILES);
            //dump($data);die;
            $this->success('网站配置成功！');
        }
        $ShopConfigRes = ConfigModel::where(array('config_type'=>1))->order("sort DESC")->select();
        $GoodsConfigRes = ConfigModel::where(array('config_type'=>2))->order("sort DESC")->select();
        $this->assign([
           'ShopConfigRes' => $ShopConfigRes,
           'GoodsConfigRes' => $GoodsConfigRes
        ]);
        return view();
    }
    public function lst()
    {   
        if(request()->isPost()){
           $sort = input('post.');
           $catetree = new Catetree();
           $sortRes = $catetree->cateSort($sort['sort'],db('config'));
           if($sortRes){
             $this->success("排序成功",'lst');
           }
        }
        $configRes = ConfigModel::order('sort DESC')->paginate(6);
        $this->assign([
        	"configRes" => $configRes
        ]);
        return view();
    }

    public function add()
    {
    	if(request()->isPost()){
    		$data = input("post.");     		
            //验证
            $validate = validate('Config');
            if(!$validate->check($data)){
                $this->error($validate->getError());
                die;
            } 
            //将中文逗号换成英文逗号
            if($data["form_type"]=='radio' or $data["form_type"]=='select' or $data["form_type"]=='checkbox'){
                $data["config_values"] = str_replace('，',',' , $data["config_values"]);
                $data["config_value"] = str_replace('，',',' , $data["config_value"]);
            }
    		$add = ConfigModel::insert($data);
    		if($add){
    			$this->success("增加配置项成功！",'lst');
    		}else{
    			$this->error("增加配置项失败!");
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
            $validate = validate('Config');
            if(!$validate->check($data)){
                $this->error($validate->getError());
               die;
            }  
            //将中文逗号换成英文逗号
            if($data["form_type"]=='radio' or $data["form_type"]=='select' or $data["form_type"]=='checkbox'){
                $data["config_values"] = str_replace('，',',' , $data["config_values"]);
                $data["config_value"] = str_replace('，',',' , $data["config_value"]);
            }   		
    		$save = ConfigModel::update($data);
    		if($save!==false){  //在没有值变更的时候，会返回0，所以用false;
    			$this->success("修改配置项成功！",'lst');
    		}else{
    			$this->error("修改配置项失败!");
    		} 		

    	}
        
        $configs = ConfigModel::find($id);
        $this->assign([
        	'configs' => $configs
        ]);
        return view();
    }

    public function del($id)
    {
        
        $del = ConfigModel::where("id",$id)->delete();
        if($del){
        	$this->success("删除配置项成功！",'lst');
        }else{
        	$this->error("删除配置项失败！");
        }
        return view();
    }  

    public function upload($imgname){
        $file = request()->file($imgname);
        $info = $info = $file->move( ROOT_PATH.'public'.DS.'static'.DS.'uploads');
        if($info){              
            return $info->getSaveName();
            }else{
             echo $file->getError();
            die;
        }   
    }

}
