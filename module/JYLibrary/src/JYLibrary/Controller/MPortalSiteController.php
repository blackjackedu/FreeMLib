<?php
namespace JYLibrary\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;

use Zend\Dom\Query;


use Zend\Http\Header\ContentType;
require_once 'ff.php';
class MPortalSiteController extends AbstractActionController
{
	public function indexAction()
	{
		$view = new ViewModel();
		$view->setVariable('title','MPortalSite');
        return $view;
	}
	
	public function getNewsListAction()
	{
		header('Access-Control-Allow-Origin: *');
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, "http://www.gxjsxy.cn/tsg/Center.aspx?t=2&c=2");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		// 4. 释放curl句柄
		curl_close($ch);
		
		$dom = new Query($output);
		$results = $dom->execute('div#Panel1 li a');
		$count = count($results); 
		$i=0;
		$rs=array();
		foreach ($results as $result) {
			//if($i++<5)$main.=$result->nodeValue.$i.'<br/>';
			if($i++<10)
			{
				$href=$result->getAttribute('href');
				$href=substr($href,strpos($href,'Id=')+3);
				array_push($rs,array("title"=>$result->nodeValue,"id"=>$href));
			}
			else break;
		}
		//echo Json::encode($rs);
		
		//header('Content-type: text/json');
	 
	$fruits = array (
	    "fruits"  => array("a" => "orange", "b" => "banana", "c" => "apple"),
	    "numbers" => array(1, 2, 3, 4, 5, 6),
	    "holes"   => array("first", 5 => "second", "third")
	);
	header('Content-Encoding: plain');
	//header('Content-type: text/json');
	echo Json::encode($rs);
	//echo ch_json_encode($rs);
	//echo json_encode($rs,JSON_UNESCAPED_UNICODE);
	exit();
	}
	
	public function getNewsDetailAction()
	{
		header('Access-Control-Allow-Origin: *');
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, "http://www.gxjsxy.cn/tsg/Contents.aspx?Type=2&Id=".$_GET['id']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		
		// 4. 释放curl句柄
		curl_close($ch);
		$dom = new Query($output);
		$results = $dom->execute('div#ContentsCt1_Content1');
		foreach ($results as $result) {
			$output = $result->ownerDocument->saveXML( $result );
		}
		$output=str_replace('src="/','src="http://www.gxjsxy.cn/',$output);
		echo $output;
		exit();
	}
	
	
	//好书推荐列表
	public function getHSHListAction()
	{
		header('Access-Control-Allow-Origin: *');
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, "http://www.gxjsxy.cn/View/Ztxx/qyds/Center.aspx?c=57");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		// 4. 释放curl句柄
		curl_close($ch);
		
		$dom = new Query($output);
		$results = $dom->execute('div.TitleList li a');
		$count = count($results); 
		$i=0;
		$rs=array();
		foreach ($results as $result) {
			//if($i++<5)$main.=$result->nodeValue.$i.'<br/>';
			if($i++<100)
			{
				$href=$result->getAttribute('href');
				$href=substr($href,strpos($href,'Id=')+3);
				array_push($rs,array("title"=>$result->nodeValue,"id"=>$href));
			}
			else break;
		}
		//echo Json::encode($rs);
		
		//header('Content-type: text/json');

	header('Content-Encoding: plain');
	//header('Content-type: text/json');
	echo Json::encode($rs);
		exit();
	}
	
	public function getHSHDetailAction()
	{
		//http://www.gxjsxy.cn/View/Ztxx/qyds/Contents.aspx?c=57&&Id=4093
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: text/html; charset=utf-8');
		$ch = curl_init();
		// 2. 设置选项，包括URL

		curl_setopt($ch, CURLOPT_URL, "http://www.gxjsxy.cn/View/Ztxx/qyds/Contents.aspx?c=57&Id=".$_GET['id']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		
		// 4. 释放curl句柄
		curl_close($ch);
		$dom = new Query($output);
		$results = $dom->execute('div.rightSideMain2');
		
		foreach ($results as $result) {
			//echo $result->nodeValue;
			$output = $result->ownerDocument->saveXML( $result );
			
		}
		
		//<img src="/Uploadfile/201403/20140324115220735.jpg" alt="">
		//替换成绝对路径
		$output=str_replace('src="/','src="http://www.gxjsxy.cn/',$output);
		//$output=iconv("gbk","utf-8//ignore",$output);
		echo $output;
		exit();
	}
	
	//读者交流
	public function getJLListAction()
	{
		header('Access-Control-Allow-Origin: *');
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, "http://www.gxjsxy.cn/View/Ztxx/qyds/Center.aspx?c=59");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		// 4. 释放curl句柄
		curl_close($ch);
		
		$dom = new Query($output);
		$results = $dom->execute('div.TitleList li a');
		$count = count($results); 
		$i=0;
		$rs=array();
		foreach ($results as $result) {
			//if($i++<5)$main.=$result->nodeValue.$i.'<br/>';
			if($i++<100)
			{
				$href=$result->getAttribute('href');
				$href=substr($href,strpos($href,'Id=')+3);
				array_push($rs,array("title"=>$result->nodeValue,"id"=>$href));
			}
			else break;
		}
		//echo Json::encode($rs);
		
		//header('Content-type: text/json');

	header('Content-Encoding: plain');
	//header('Content-type: text/json');
	echo Json::encode($rs);
		exit();
	}
	
	public function getJLDetailAction()
	{
		//http://www.gxjsxy.cn/View/Ztxx/qyds/Contents.aspx?c=57&&Id=4093
		header('Access-Control-Allow-Origin: *');
		header('Content-Type: text/html; charset=utf-8');
		$ch = curl_init();
		// 2. 设置选项，包括URL

		curl_setopt($ch, CURLOPT_URL, "http://www.gxjsxy.cn/View/Ztxx/qyds/Contents.aspx?c=59&Id=".$_GET['id']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 1);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		
		// 4. 释放curl句柄
		curl_close($ch);
		$dom = new Query($output);
		$results = $dom->execute('div.rightSideMain2');
		
		foreach ($results as $result) {
			//echo $result->nodeValue;
			$output = $result->ownerDocument->saveXML( $result );
			
		}
		
		//<img src="/Uploadfile/201403/20140324115220735.jpg" alt="">
		//替换成绝对路径
		$output=str_replace('src="/','src="http://www.gxjsxy.cn/',$output);
		//$output=iconv("gbk","utf-8//ignore",$output);
		echo $output;
		exit();
	}
	
	
	private function curlGo($url)
	{
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		// 4. 释放curl句柄
		curl_close($ch);
		return $output;
	}
	
	//开放时间
	public function getKFAction()
	{
		header('Access-Control-Allow-Origin: *');
		
		$output=$this->curlGo("http://www.gxjsxy.cn/tsg/Center.aspx?t=1&c=3");
		$dom = new Query($output);
		$results = $dom->execute('div#ContentsCt1_Content1');
		foreach ($results as $result) {
			$output = $result->ownerDocument->saveXML( $result );
		}
		echo $output;
		exit;
	}
	
	//馆藏布局
	public function getBJAction()
	{
		header('Access-Control-Allow-Origin: *');
		
		$output=$this->curlGo("http://www.gxjsxy.cn/tsg/Center.aspx?t=1&c=5");
		$dom = new Query($output);
		$results = $dom->execute('div#ContentsCt1_Content1');
		foreach ($results as $result) {
			$output = $result->ownerDocument->saveXML( $result );
		}
		echo $output;
		exit;
	}
	
	
}

?>