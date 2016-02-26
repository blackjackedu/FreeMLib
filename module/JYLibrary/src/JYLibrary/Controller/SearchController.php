<?php
// module/Album/src/Album/Controller/AlbumController.php:
namespace JYLibrary\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use JYLibrary\Model\BookSearch;
use Zend\Dom\Query;
use JYLibrary\llt\Zend\Paginator\AdapterSybase;
use JYLibrary\llt\Zend\Crypt\lltCrypt;
use Zend\Session\Container;
use Zend\Http\Header\ContentType;

class SearchController extends AbstractActionController
{		
	public function indexAction()
	{
		echo "<h1>JYLibrary安装成功.<h1>";
		exit();
	}
	public function unifyAction()
	{
		$vm=new ViewModel();
		$request=$this->getRequest();
		if($request->isPost())
		{
			$qtxt=$request->getPost('suchen_word');
		}else{
			$qtxt=$this->params()->fromRoute('page');
			//$qtxt=lltCrypt::decrypt($qtxt);
		}
		if($qtxt!=''){

			$adapter=new AdapterSybase();
			$adapter->qtxt=$qtxt;			
			$paginator=new \Zend\Paginator\Paginator($adapter);
			$page=$this->params()->fromRoute('id');
			
			if(!isset($page))$page=1;
			$paginator->setCurrentPageNumber($page);
			$paginator->setItemCountPerPage(10);
			$vm->setVariable('paginator',$paginator);
			//$qtxt=lltCrypt::encrypt($qtxt);
			$vm->setVariable('qtxt',$qtxt);
			
		}
		
			
		return $vm;
	}
	
	public function queryAction()
	{
		$request=$this->getRequest();
		$qtxt=(string)$request->getPost('qtxt');		
		$vm=new ViewModel();
		$vm->setVariable('qtxt',$qtxt);
		
		return $vm;
	}
	
	public function detailAction()
	{
		$ctrlno=$this->params()->fromRoute('id');
		$rs="详细信息\n";
		if(isset($ctrlno))
		{
			$BookSearch=new BookSearch();
			$rs.=$BookSearch->getDetail($ctrlno);
			
		}
		$vm=new ViewModel();
		$vm->setVariable('id',$rs);
		$vm->setVariable('bookno',$ctrlno);
		return $vm;
	}	
	public function mobileAction()
	{
		$view = new ViewModel();
		$view->setTerminal(true);
        return $view;
	}
	public function OPACAction()
	{
		$view = new ViewModel();
		$view->setTerminal(true);
        return $view;
	}
	
	public function iechoAction()
	{
		$qtxt=$this->getRequest()->getPost('suchen_word');
		$qtxt="php";
		$BookSearch=new BookSearch();
		//$data=$BookSearch->findBookByPage($qtxt,1,10);
		$data=$BookSearch->findBook($qtxt);
		//$rs=array(array("title"=>"sfsdf","classno"=>"fsdf"),array("title"=>"32434","classno"=>"fsfsdfwee"));
		$rs=array();
		print_r("data:".$data);
		foreach($data as $d)
		{
		
			array_push($rs,array("title"=>$d->title,"classno"=>$d->classno));
		};
		$json= Json::encode($rs);
		
		echo $json ;
		exit();
	}
	public function getNewsAction()
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
			if($i++<5)
			{
				$href=$result->getAttribute('href');
				$href=substr($href,strpos($href,'Id=')+3);
				array_push($rs,array("title"=>$result->nodeValue,"href"=>$href));
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
	echo json_encode($rs);
		exit();
	}
	
	public function getNewsDetailAction()
	{
		header('Access-Control-Allow-Origin: *');
		$ch = curl_init();
		// 2. 设置选项，包括URL
		curl_setopt($ch, CURLOPT_URL, "http://www.gxjsxy.cn/tsg/Contents.aspx?Type=2&Id=".$_GET['Id']);
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
		echo $output;
		exit();
	}
    
	public function getNPAction()
	{
		header('Access-Control-Allow-Origin: *');
		$url=$_GET['readerurl'];
		
		if(isset($url))
		{
		//echo($url);
			$ch = curl_init();
			// 2. 设置选项，包括URL
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_USERAGENT, "Apache-HttpClient/UNAVAILABLE (java 1.4)");

			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			curl_close($ch);
		}		
		header('Content-Encoding: plain');
		echo $output;
		exit();
	}
	
	public function getCXAction()
	{
		header('Access-Control-Allow-Origin: *');
		$url=$_GET['word'];
		$type=$_GET['type'];
		//echo $type;
		$url=urldecode($url);
		//$url=iconv("gbk","utf-8//ignore",$url);
		$url=iconv("utf-8","gbk//ignore",$url);
		$word = urlencode($url);
		//echo 'word='.$word;
		if($type=='book')
		{
			$ch = curl_init();
			// 2. 设置选项，包括URL
			curl_setopt($ch, CURLOPT_URL, "http://book.m.5read.com/search?channel=search&sw=".$word."&json=mjson&Pages=1&fenleiID=&Field=&searchtype=0&Sort=0&ecode=GBK&jpagesize=0");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_COOKIE , 'JSESSIONID=93CBA41F58CCC8A099C8437620684EA2.irdmblhome72a; mgid=274; maid=26; msign_dsr=1383181567469; mduxiu=musername%2c%3dblmobile%2c%21muserid%2c%3d1000086%2c%21mcompcode%2c%3d1015%2c%21menc%2c%3d1004A7243EE4874349443B9F1A45CDAE; xc=4; mmr_enc=C29455C9E656BEA8FAC4A5B4334C5A6E; mmr_userid=87695; 3gemail=10465069%40qq%2ecom; JSESSIONID=F8A830816A7074CACC2BEBBC694DDF07.tomcat1');
			//curl_setopt($ch, CURLOPT_COOKIE , 'JSESSIONID=6A15B0F9F365B5CF296BA06535E854FA.irdmblhome72a;JSESSIONID=A7D37743CE1860B61EE58D788B5852BD.tomcat1');
			curl_setopt($ch, CURLOPT_USERAGENT, "Apache-HttpClient/UNAVAILABLE (java 1.4)");

			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			curl_close($ch);
			// 4. 释放curl句柄
			//%E5%B1%B1%E4%B8%9C%E5%A4%A7%E5%AD%A6
			//http://book.m.5read.com/search?channel=search&sw=%C9%BD%B6%AB%B4%F3%D1%A7&json=mjson&Pages=1&fenleiID=&Field=&searchtype=0&Sort=0&ecode=GBK&jpagesize=0
			//http://book.m.5read.com/search?channel=search&sw=%C9%BD%B6%AB%B4%F3%D1%A7&json=mjson&Pages=1&fenleiID=&Field=&searchtype=0&Sort=0&ecode=GBK&jpagesize=0
			//JSESSIONID=93CBA41F58CCC8A099C8437620684EA2.irdmblhome72a; mgid=274; maid=26; msign_dsr=1383181567469; mduxiu=musername%2c%3dblmobile%2c%21muserid%2c%3d1000086%2c%21mcompcode%2c%3d1015%2c%21menc%2c%3d1004A7243EE4874349443B9F1A45CDAE; xc=4; mmr_enc=C29455C9E656BEA8FAC4A5B4334C5A6E; mmr_userid=87695; 3gemail=10465069%40qq%2ecom; JSESSIONID=F8A830816A7074CACC2BEBBC694DDF07.tomcat1
		}else if($type=='newspaper')
		{
		//http://newspaper.m.5read.com/searchNP?channel=searchNP&sw=%C4%CF%B9%FA&json=mjson&Pages=1&fenleiID=&Field=&searchtype=0&isort=&ecode=GBK&jpagesize=0
			$ch = curl_init();
			// 2. 设置选项，包括URL
			curl_setopt($ch, CURLOPT_URL, "http://newspaper.m.5read.com/searchNP?channel=searchNP&sw=".$word."&json=mjson&Pages=1&fenleiID=&Field=&searchtype=0&isort=&ecode=GBK&jpagesize=0");
			//curl_setopt($ch, CURLOPT_URL, "http://newspaper.m.5read.com/searchNP?channel=searchNP&sw=%C4%CF%B9%FA&json=mjson&Pages=1&fenleiID=&Field=&searchtype=0&isort=&ecode=GBK&jpagesize=0");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_COOKIE , 'JSESSIONID=F1AB611181CFE54E2D2AE18A91964A47.irdmblhome72a; mgid=274; maid=26; msign_dsr=1383194450564; mduxiu=musername%2c%3dblmobile%2c%21muserid%2c%3d1000086%2c%21mcompcode%2c%3d1015%2c%21menc%2c%3dC1F015273230F02B48E5E10C85D083B5; xc=4; mmr_enc=C29455C9E656BEA8FAC4A5B4334C5A6E; mmr_userid=87695; 3gemail=10465069%40qq%2ecom');
			//curl_setopt($ch, CURLOPT_COOKIE , 'JSESSIONID=6A15B0F9F365B5CF296BA06535E854FA.irdmblhome72a;JSESSIONID=A7D37743CE1860B61EE58D788B5852BD.tomcat1');
			curl_setopt($ch, CURLOPT_USERAGENT, "Apache-HttpClient/UNAVAILABLE (java 1.4)");

			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			curl_close($ch);
		}
		header('Content-Encoding: plain');
		//header('Content-type: text/json');
		//$output=file_get_contents("http://book.m.5read.com/search?channel=search&sw=%C9%BD%B6%AB%B4%F3%D1%A7&json=mjson&Pages=1&fenleiID=&Field=&searchtype=0&Sort=0&ecode=GBK&jpagesize=0");
		echo $output;
		exit();
	}

	//WX用
	public function getBookWXAction()
	{
		//http://192.168.4.152:82/cgi-win/tcgi.exe
		//header('Access-Control-Allow-Origin: *');
		//$url='西游记';
		$url=$_GET['word'];
		$url=urldecode($url);
		$url=iconv("utf-8","gbk//ignore",$url);//按GBK来做URL编码
		$word = urlencode($url);
		$word=$url;
		//echo $word;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/tcgi.exe");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		//$curlPost = "title=%C8%FD%B9%FA%D1%DD%D2%E5&subject1=&subject2=&subject3=&author=&classfi=&cltype=690&publisher=&library=0&rectype=-1&fromyear=&toyear=&lang=0&maxrow=50&query=%BC%EC%CB%F7";		
		$curlPost = "title=".$word."&subject1=&subject2=&subject3=&author=&classfi=&cltype=690&publisher=&library=0&rectype=-1&fromyear=&toyear=&lang=0&maxrow=50&query=%BC%EC%CB%F7";		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$output = curl_exec($ch);
		curl_close($ch);
		
		$dom = new Query($output);
		$results = $dom->execute('li');
		$rs=array();
		//echo count($results);
		$maxRow=5;
		$m=1;
		foreach ($results as $result) {
			//$href=$result->nodeValue;
			list($title2, $year, $classno) =explode("，", $result->nodeValue);
			list($title,$authors)=explode("／", $title2);	
			
			//http://192.168.4.152:82/cgi-win/tcgid.exe?s66276r20wmysql
			if(!$result->getElementsByTagName('a')->item(0))
			{
				array_push($rs,array("title"=>"无记录","authors"=>"","year"=>"","href"=>"","classno"=>""));
				break;
			}
			$href=$result->getElementsByTagName('a')->item(0)->getAttribute('href');
			$href=substr($href,strpos($href,'?')+1);
			if($classno)
			{
				array_push($rs,array("title"=>$title,"authors"=>$authors,"year"=>$year,"href"=>$href,"classno"=>$classno));
				if($m++>=$maxRow)break;
			}
			
		}
		
		
		header('Content-Type: application/json');
		echo json_encode($rs);
		
		
		exit;
	}
	//馆藏查询
	public function getBookAction()
	{
		//http://192.168.4.152:82/cgi-win/tcgi.exe
		//header('Access-Control-Allow-Origin: *');
		//$url='惊奇';
		$url=$_GET['word'];
		//$url=urldecode($url);
		$url=iconv("utf-8","gbk//ignore",$url);//按GBK来做URL编码
		$word = urlencode($url);
		$word=$url;
		//echo $word;
		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/tcgi.exe");
		curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/s3trs.exe");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		//$curlPost = "title=%C8%FD%B9%FA%D1%DD%D2%E5&subject1=&subject2=&subject3=&author=&classfi=&cltype=690&publisher=&library=0&rectype=-1&fromyear=&toyear=&lang=0&maxrow=50&query=%BC%EC%CB%F7";		
		//$curlPost = "title=".$word."&subject1=&subject2=&subject3=&author=&classfi=&cltype=690&publisher=&library=0&rectype=-1&fromyear=&toyear=&lang=0&maxrow=50&query=%BC%EC%CB%F7";		
		//$curlPost = "word1=".$word."&word2=&word3=&s=%BC%EC%CB%F7";		
		$curlPost="word1=%C8%FD%B9%FA%D1%DD%D2%E5&word2=&word3=&s=%BC%EC%CB%F7";
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$output = curl_exec($ch);
		curl_close($ch);
		
		//$dom = new Query($output);
		//$results = $dom->execute('ol');
		//foreach ($results as $result) {
		//	$output = $result->ownerDocument->saveXML($result);
		//}
		
		//翻页的GET http://192.168.4.152:82/cgi-win/s3trs.exe?三国演义&31
		
		$dom = new Query($output);
		$results = $dom->execute('li');
		$rs=array();
		//echo count($results);
		foreach ($results as $result) {
			//$href=$result->nodeValue;
			list($title2, $year, $classno) =explode("，", $result->nodeValue);
			list($title,$authors)=explode("／", $title2);	
			
			//http://192.168.4.152:82/cgi-win/tcgid.exe?s66276r20wmysql
			if(!$result->getElementsByTagName('a')->item(0))
			{
				array_push($rs,array("title"=>"无记录","authors"=>"","year"=>"","href"=>"","classno"=>""));
				break;
			}
			$href=$result->getElementsByTagName('a')->item(0)->getAttribute('href');
			$href=substr($href,strpos($href,'?')+1);
			if($classno)array_push($rs,array("title"=>$title,"authors"=>$authors,"year"=>$year,"href"=>$href,"classno"=>$classno));
			
		}
		
		
		//header('Content-Encoding: plain');
		header('Content-Type: application/json');
		echo 'jsonpCallback('.json_encode($rs).')';
		
		//header("Content-type:text/html;charset=utf-8");
		//echo $output;
		
		exit;
	}
	
	//馆藏查询，获取详细
	public function getBookDetailAction()
	{
		header('Access-Control-Allow-Origin: *');
		$url=$_GET['id'];
		$ch = curl_init();
		//http://192.168.4.152:82/cgi-win/tcgid.exe?s66276r20wmysql
		curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/tcgid.exe?".$url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);		
		$output = curl_exec($ch);
		curl_close($ch);
		
		$dom = new Query($output);
		$results = $dom->execute('body');
		$rs=array();
		//echo count($results);
		foreach ($results as $result) {
			$output=$result->ownerDocument->saveXML( $result );
		}
		header('Content-Encoding: plain');
		echo $output;
		exit();
	}
	
	public function login0Action()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		$container = new Container('namespace');
		if(isset($container->auth))
		{
			unset($container->auth);
			echo ($container->un.'已登录'.$container->pw);
			exit();
		}
		
		
		$un=$_REQUEST['un'];
		$pw=$_REQUEST['pw'];
		//echo "登录信息：".$un."/".$pw."===================";
		if(isset($un)&&isset($pw))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&query='.urlencode('query').'';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			$output = curl_exec($ch);
			curl_close($ch);
			$dom = new Query($output);
			$results = $dom->execute('body');
			foreach ($results as $result) {
				$output=$result->nodeValue;
			} 
			//echo $output;
			if(strpos($output,'口令错误')||strpos($output,'没有您的读者帐户'))
			{
				echo "用户名/密码有误";
			}else if(strpos($output,'退出登录'))
			{
				$container->auth=1;
				$container->un=$un;
				$container->pw=$pw;
				//echo print_r($container->pw);
				//echo ";登录后信息：un=".$un.",pw=".$pw.",ok";
				echo "ok";
			}else echo "登录失败2";
		}else echo "登录失败1";
		exit();
	}
	public function login1Action()
	{		
		header('Content-Type: application/json');		
		
		$container = new Container('namespace');
		if(isset($container->auth))
		{
			unset($container->auth);
			$rs = array('message'=>$container->un.'已登录'.$container->pw);
			echo 'jsonpCallback('.json_encode($rs).')';
			exit();
		}	
		
		$un=$_REQUEST['un'];
		$pw=$_REQUEST['pw'];
		
		//echo "登录信息：".$un."/".$pw."===================";
		if(isset($un)&&isset($pw))
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&query='.urlencode('query').'';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			$output = curl_exec($ch);
			curl_close($ch);
			$dom = new Query($output);
			$results = $dom->execute('body');
			foreach ($results as $result) {
				$output=$result->nodeValue;
			} 
			//echo $output;
			if(strpos($output,'口令错误')||strpos($output,'没有您的读者帐户'))
			{
				$rs = array('message'=>'用户名/密码有误');
				echo 'jsonpCallback('.json_encode($rs).')';
			}else if(strpos($output,'退出登录'))
			{
				$container->auth=1;
				$container->un=$un;
				$container->pw=$pw;
				//echo print_r($container->pw);
				//echo ";登录后信息：un=".$un.",pw=".$pw.",ok";
				$rs = array('message'=>'ok');
				echo 'jsonpCallback('.json_encode($rs).')';
			}else 
			{
				$rs = array('message'=>"登录失败2");
				echo 'jsonpCallback('.json_encode($rs).')';
			}
		}else
		{
			$rs = array('message'=>"登录失败1");
			echo 'jsonpCallback('.json_encode($rs).')';
		}
		exit();
	}
	public function getLoanRecordAction()
	{	
		
		$container = new Container('namespace');
		//echo print_r($container);
		if(isset($container->auth))
		{
			$un=$container->un;
			$pw=$container->pw;
		}else
		{
			echo '未登录';
			exit();
		}
		//$un='J00425';
		//$pw='104673317';
		//tang A209798,123456
		$ch = curl_init();
		// 2. 设置选项，包括URL
		//curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/s3trs.exe");
		curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&uname=&newpass1=&newpass2=&query=query';
		//$curlPost = 'cardno='.$un.'&pass='.$pw.'&uname=&newpass1=&newpass2=&query=query';
		//cardno=J00425&pass=104673317&uname=&newpass1=&newpass2=&query=query
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		// 3. 执行并获取HTML文档内容
		$output = curl_exec($ch);
		// 4. 释放curl句柄
		curl_close($ch);
		//echo $output;
		
		
		$dom = new Query($output);
		$results = $dom->execute('table');
		$rs=array();
		//echo "结果:".count($results);
		foreach ($results as $result) {
			
			$t=$result->getAttribute('bordercolordark');
			if($t=="#eeeeee")
			{
				$tr=$result->getElementsByTagName('tr');
				for($c = 0; $c<$tr->length; $c++){
					//var_dump($tr->item($c)->nodeValue);
					array_push($rs,array("title"=>$tr->item($c)->nodeValue));

				} 
				//echo "结果:".$tr;
				//$output = $result->ownerDocument->saveXML($result );
				//$output=$result->nodeValue;
			}
			
		}   
		
		
		header('Content-Type: application/json');
		
		echo 'jsonpCallback('.json_encode($rs).')';
			
		exit();
	}
	
	private function echoJSData($str)
	{
		return 'var data="'.$str.'"';
	}
	public function getJSAction()
	{	
		echo 'var str = "ok";';
		exit();
	}
	
	public function crossDomainTestAction()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		$a = array('ajax'=>'ajaxPhp1');
		$a = json_encode($a);
		echo 'callback('.$a.')';//回调函数callbackFunction
		exit();
	}
	
	}
?>