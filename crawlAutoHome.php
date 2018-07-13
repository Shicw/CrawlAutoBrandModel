<?php
class Crawl{
	public function curl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		$SSL = substr($url, 0, 8) == "https://" ? true : false;
		if ($SSL) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名
        }
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
    public function index(){
    	$pdo =new PDO("mysql:host=localhost;dbname=local;port=3306;charset=utf8","root","root");
    	$brandJson = $this->curl("https://www.autohome.com.cn/ashx/AjaxIndexCarFind.ashx?type=11");
    	$brandJson = iconv("GB2312","UTF-8",$brandJson);//转成utf8
	    $brandJson = json_decode($brandJson,1);
	    if($brandJson['returncode']==0){
	    	$brandItems = $brandJson['result']['branditems'];
            //循环将品牌插入数据表并获取品牌下的型号
	    	foreach ($brandItems as $brand){
	    		$id = $brand['id'];
	    		$name = $brand['name'];
	    		$firstLetter = $brand['bfirstletter'];
	    		echo $name."\r\n";
                //查询数据表中是否已存在该品牌,不存在才写入
                $findBrand = $pdo->query('select count(*) from auto_brand where id='.$id);
                $findBrand = $findBrand->fetch();
                $findBrand = $findBrand[0];//查询结果的条数
    		    if(!$findBrand){
    		    	$insertBrand = $pdo->prepare("insert into auto_brand values (:id,:name,:firstLetter)");
    		    	$insertBrand->execute([
    		    		":id" => $id,
    		    		":name" => $name,
    		    		":firstLetter" => $firstLetter
    		    	]);
    		    }
                //获取品牌下的车厂和型号列表
    		    $modelJson = $this->curl("https://www.autohome.com.cn/ashx/AjaxIndexCarFind.ashx?type=13&value=".$id);
                $modelJson = iconv("GB2312","UTF-8",$modelJson);//转成utf8
                $modelJson = json_decode($modelJson,1);
                $modelJson = $modelJson['result']['factoryitems'];//获取json中的车厂以及对应的型号信息
                //循环工厂名
                foreach ($modelJson as $factory){
                    //循环每个工厂下的型号
                	foreach ($factory['seriesitems'] as $model){
                		$modelId = $model['id'];
                		$modelName = $model['name'];
                		$modelFirstLetter = $model['firstletter'];
                		echo $modelName."\r\n";
                		$findModel = $pdo->query('select count(*) from auto_model where id='.$modelId);
                        $findModel = $findModel->fetch();
                        $findModel = $findModel[0];//查询结果的条数
                		if(!$findModel){
                			$insertModel = $pdo->prepare("insert into auto_model values (:id,:brand_id,:name,:firstLetter)");
                			$insertModel->execute([
                				':id'=>$modelId,
                				':brand_id'=>$id,
                				':name'=>$modelName,
                				':firstLetter'=>$modelFirstLetter
                			]);
                		}
                	}
                }
            }
        }else{
        	echo '数据获取失败';
        }
    }
}
$crawl = new Crawl;
$crawl->index();