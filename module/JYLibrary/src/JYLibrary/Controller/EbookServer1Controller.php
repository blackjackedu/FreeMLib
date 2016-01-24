<?php

namespace JYLibrary\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Dom\Query;
use JYLibrary\Model\BookSearch;
use Zend\Session\Container;

class EbookServer1Controller extends AbstractActionController
{
	public function indexAction()
	{
		$view = new ViewModel();
		$view->setVariable('title','EbookServer1');
        return $view;
	}
	
	//获取图书清单
	//书名，分类，网址
	public function getEbookListAction()
	{
		header('Access-Control-Allow-Origin: *');		
		header('Content-Encoding: plain');
		
		$rs=array();
		array_push($rs,array("title"=>"文学作品","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"张爱玲传-余斌","category"=>"文学作品","id"=>"c/26/016"));
			array_push($rs,array("title"=>"平凡的世界","category"=>"文学作品","id"=>"c/14/025"));
		array_push($rs,array("title"=>"古典文学","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"红楼梦","category"=>"古典文学","id"=>"c/52/001"));
		array_push($rs,array("title"=>"成功励志","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"动机与人格","category"=>"古典文学","id"=>"a/7/568"));
			array_push($rs,array("title"=>"人性的弱点全集","category"=>"古典文学","id"=>"a/7/573"));
			array_push($rs,array("title"=>"好妈妈胜过好老师","category"=>"古典文学","id"=>"a/7/596"));
			array_push($rs,array("title"=>"国富论-斯密","category"=>"古典文学","id"=>"a/7/546"));
		array_push($rs,array("title"=>"外国名著","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"蒙田随笔全集","category"=>"古典文学","id"=>"a/2/0895"));
			array_push($rs,array("title"=>"挪威的森林","category"=>"外国文学","id"=>"a/2/0734"));
			array_push($rs,array("title"=>"苏菲的世界","category"=>"外国文学","id"=>"a/2/0855"));
			array_push($rs,array("title"=>"幸福之路","category"=>"外国文学","id"=>"a/2/0605"));
		array_push($rs,array("title"=>"经典名著","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"蒙麦田里的守望者","category"=>"古典文学","id"=>"c/60/035"));
		array_push($rs,array("title"=>"人文历史","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"论语-论语集注","category"=>"古典文学","id"=>"c/75/08"));
			array_push($rs,array("title"=>"易中天品三国","category"=>"人文历史","id"=>"c/68/009"));
		array_push($rs,array("title"=>"武侠小说","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"射雕英雄传","category"=>"武侠小说","id"=>"c/73/003"));
		array_push($rs,array("title"=>"言情小说","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"心有千千结-琼瑶","category"=>"武侠小说","id"=>"c/02/46"));
			array_push($rs,array("title"=>"烟雨朦朦-琼瑶","category"=>"武侠小说","id"=>"c/02/51"));
		array_push($rs,array("title"=>"科幻小说","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"追踪流浪星","category"=>"武侠小说","id"=>"a/71/305"));
			array_push($rs,array("title"=>"星际迷航","category"=>"武侠小说","id"=>"a/71/275"));
			array_push($rs,array("title"=>"时间旅行者的妻子","category"=>"武侠小说","id"=>"a/71/237"));
		array_push($rs,array("title"=>"心理哲学","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"中国哲学简史-冯友兰","category"=>"心理哲学","id"=>"china/054/002"));
			array_push($rs,array("title"=>"西方哲学史-英-罗素","category"=>"心理哲学","id"=>"a/3/03/64"));
			array_push($rs,array("title"=>"物种起源","category"=>"心理哲学","id"=>"a/3/03/41"));
		array_push($rs,array("title"=>"人物传记","category"=>"C","id"=>"174"));
			array_push($rs,array("title"=>"富兰克林自传","category"=>"心理哲学","id"=>"a/11/155"));
			array_push($rs,array("title"=>"居里夫人传","category"=>"心理哲学","id"=>"a/11/156"));
		echo Json::encode($rs);
		exit();
	}
	
	//获取目录
	public function getEBookContentAction()
	{
		header('Access-Control-Allow-Origin: *');		
		header('Content-Encoding: plain');
		$id=$_GET['id'];
		if(!isset($id)&&$id=="")exit();
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.uus8.org/".$id."/");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		$output = curl_exec($ch);
		curl_close($ch);
		
		//echo $output;
		$dom = new Query($output);
		$results = $dom->execute('div#youli2 li a');		
		$rs=array();
		//echo count($results);
		foreach ($results as $result) {
			array_push($rs,array("title"=>$result->nodeValue,"id"=>$id."/".$result->getAttribute("href")));
		}		
		//$output=mb_convert_encoding($output, "utf-8", "gb2312");//解析完后再转码，要不然，解析不对。
		echo Json::encode($rs);
		
		exit();
	}
	
	//获取内容页
	public function getEBookDetailAction()
	{
		header('Access-Control-Allow-Origin: *');		
		header('Content-Encoding: plain');
		$id=$_GET['id'];
		if(!isset($id)&&$id=="")exit();
		
		//http://www.uus8.org/a/2/0687/000.htm
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://www.uus8.org/".$id."?r=dfsd");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		$output = curl_exec($ch);
		curl_close($ch);
		
		$pos=strpos($output,'<p id="zoom">');
		$output=substr($output,$pos);
		$pos=strpos($output,'<div id=');
		$output=substr($output,0,$pos);
		//echo $pos;
		//$output=substr($output,0,$pos);
		//$dom = new Query($output);
		//$results = $dom->execute('p#zoom');	
		
		//foreach ($results as $result) {
		//	$output = $result->ownerDocument->saveXML( $result );
		//}
		
		$output=mb_convert_encoding($output, "utf-8", "gb2312");
		echo $output;
		exit();
	}
	
	
}
?>