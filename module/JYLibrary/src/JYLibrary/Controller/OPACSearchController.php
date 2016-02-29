<?php

namespace JYLibrary\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Json\Json;
use Zend\Dom\Query;
use JYLibrary\Model\BookSearch;
use Zend\Session\Container;
use JYLibrary\Model\LoginModel;

require_once 'utils.php';

class OPACSearchController extends AbstractActionController
{
	public function indexAction()
	{
		$view = new ViewModel();
		$view->setVariable('title','OPACSearch');
        return $view;
	}
	
	public function getBookAction()
	{
		header('Access-Control-Allow-Origin: *');
		$word=$_GET['key'];
		$word=iconv("utf-8","gbk//ignore",$word); //WebSite那端接受的是gbk的encode码。
		$word=urlencode($word);
		//echo '参数'.$word;
		//header("Content-type:text/html;charset=utf-8");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/s3trs.exe");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		//$curlPost="word1=%C8%FD%B9%FA%D1%DD%D2%E5&word2=&word3=&s=%BC%EC%CB%F7";
		$curlPost="word1=".$word."&word2=&word3=&s=%BC%EC%CB%F7";
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$output = curl_exec($ch);
		curl_close($ch);
		$output=mb_convert_encoding($output, "utf-8", "gb2312");
		//echo "消费结算";
		//echo $output;
		//$output=iconv("utf-8","gbk//ignore",$output);
		
		$arrBR=explode('<br>',$output);		
		$rs=array();
		foreach($arrBR as $row)
		{
			//<a href="/cgi-win/tcgid.exe?s2495r20w三国演义">三国演义 上 [专著]·上／罗贯中著</a>
			//1995 I242.4/14 [7]
			if(strpos($row,'tcgid.exe'))//排降掉页数的链接
			{
				//preg_match('&gt;a href="/cgi-win/tcgid.exe\??',$row, $matches);
				$pos1=strpos($row,'>');				
				$str=substr($row,$pos1+1);
				$pos2=strpos($str,"</a>");
				$str=substr($str,0,$pos2);
				list($title,$authors)=explode("／", $str);
				
				$pos1=strpos($row,'</a>');
				$str=substr($row,$pos1+5);
				list($year,$classno)=explode(" ", $str);
				//echo $str;
				
				$pos1=strpos($row,'>');
				$str=substr($row,0,$pos1);
				$pos2=strpos($str,'?')+1;
				$href=substr($str,$pos2);
				
				
				if($classno!=""&&$classno!="[订购]"&&$classno!=$lastcno)
				{
					array_push($rs,array("title"=>$title,"authors"=>$authors,"year"=>$year,"classno"=>$classno,"href"=>$href));
					$lastcno=$classno;//上一条记录的分类号。用于排除SULCMIS3 website同索书号的显示bug
				}
			}
		}
		
		header('Content-Encoding: plain');
		//header('Content-type: text/json');
		echo Json::encode($rs);
		exit();
	}
	
	public function getBookDetailAction()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		$word=$_GET['id'];
		//echo $word;
		if(!isset($word)||$word=="")exit();
		$ch = curl_init();	
		
		//对于已登录的，先登录，再检索，这样能得到预约信息
		$lm=LoginModel::getLoginModel();
		
		if($lm->auth)
		{		
			$un=$lm->id;
			$pw=$lm->password;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&query='.urlencode('query').'';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			curl_setopt($ch,CURLOPT_COOKIEJAR,'/tmp/cookie'); 
			$output = curl_exec($ch);
		}
		
		curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/tcgid.exe?".$word);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch,CURLOPT_COOKIEFILE,'/tmp/cookie'); 
		curl_setopt($ch, CURLOPT_POST, 1);
		$output = curl_exec($ch);
		curl_close($ch);
		//echo $output;
		$output=mb_convert_encoding($output, "utf-8", "gb2312");
		
		$arr=explode("<hr>",$output);
		//$arr[1]包含内容信息,arr[2]是馆藏表格
		//$str=$arr[2];
		$str=substr($arr[2],0,strpos($arr[2],"</center>")+8);
		//echo $str;
		//按<br>分隔不了，要用\n回车来分隔。
		$rs['t']=$str;//藏书情况表
		$rs['title']=$arr[1];//书目
		//去掉HTML标签
		//$preg = "/<\/?[^>]+>/i";//去掉所有HTML标签
		$preg ="/(<(?:\/a|a)[^>]*>)/i"; //去掉A标签
		$rs['title']= preg_replace($preg,'',$rs['title']);
		
		/*
		\r
		\n
		I242.4<BR><BR>　三国演义 下[专著]·下&nbsp;/&nbsp;罗贯中著. -&nbsp;北京:&nbsp;<a href="/cgi-win/tcgif.exe?s287g210210r20">京华出版社</a>,&nbsp1995<BR>　418页;&nbsp;32开<BR>　<BR>　ISBN 7-80600-096-8（精装）:&nbsp;CNY16.00<BR><BR>　I.&nbsp;①三国演义 下　II.&nbsp;①<a href="/cgi-win/tcgif.exe?s1958g700722r20">罗贯中</a>　IV.&nbsp;①<a href="/cgi-win/tcgif.exe?s430g690690r20">I242.4</a> 
		*/
		$arrBF=explode("<BR>",$arr[1]);
		//print_r($arrBF);
		$rs['bookid']=$arrBF[0];  //此处只有分类号，没有索书号。
		$rs['label']=$arrBF[2];
		$rs['year']=$arrBF[3];
		$rs['isbn']=$arrBF[5];
		
		//藏书表格第二行第二单元格，索书号
		//$dom = new Query($rs['t'])；
		//$td = $dom->execute('tr')->item(1)->getElementsByTagName('td');
		//$rs['bookid']=$td->item(1)->nodeValue;
		
		$dom = new Query($arr[2]);
		$results = $dom->execute('input');
		//echo count($results);
		$str="";
		foreach ($results as $result) {
			$str.=$result->getAttribute("name")."=".$result->getAttribute("value")."&";
		}
		$rs['resv']=$str;
		
		//print_r($rs);
		//echo $arr[1]."<br/>".$arr[2];
		//echo strpos($arr[2],"<center>");
		//echo $arr[2];
		echo Json::encode($rs);
		exit();
	}

	public function loginAction()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');

		$lm=LoginModel::getLoginModel();
		$rs=Array();
		if($lm->auth)
		{
			$rs['status']="2";
			$rs['message']='已登录';
			$rs['username']=$lm->username;
			//$rs['password']=$lm->password;
			echo Json::encode($rs);
			exit();
		}
		
		$un=$_REQUEST['un'];
		$pw=$_REQUEST['pw'];	
		
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
			//echo $output;
			$output=mb_convert_encoding($output, "utf-8", "gb2312");			
			
			if(strpos($output,'口令错误')||strpos($output,'没有您的读者帐户'))
			{
				//用户名与密码相同表示是首次登录，需要先修改默认密码
				if($un==$pw) $rs['message']="请先修改默认的密码"; 
				else $rs['message']="用户名或密码有误";				
				
			}else if(strpos($output,'口令与证号相同'))
			{
				$rs['message']="您的密码与证号相同，请先修改密码";
			}else if(strpos($output,'退出登录'))
			{
				$rs['status']="1";
				$pos=strpos($output,'姓名');
				$name=substr($output,$pos);
				$pos=strpos($name,'类型');
				$name=substr($name,0,$pos);
				$pos=strpos($name,'<u>');
				$name=substr($name,$pos+3);
				$pos=strpos($name,'</u>');
				$name=substr($name,0,$pos);
				
				$rs['username']=$name;
				$rs['sid']=session_id();
				$rs['message']="登录成功".date('Y-m-d H:i:s',time());
				LoginModel::setLoginModel("",$un,$pw,true,date('Y-m-d H:i:s',time()));
				
			}else
			{
				$rs['message']='未知错误';
			}
			
			
			//echo $output;
		}else
		{
			$rs['message']="请输入用户名及密码";
		}
		
		echo Json::encode($rs);
		exit();
	}

	public function getLoanRecordAction()
	{
		$this->checkLogin();
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');

		$rs=Array();
		$lm=LoginModel::getLoginModel();
		
		
			$un=$lm->id;
			$pw=$lm->password;		
			
			$ch = curl_init();
			// 2. 设置选项，包括URL
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&uname=&newpass1=&newpass2=&query=query';
			
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			// 4. 释放curl句柄
			curl_close($ch);
			//$output=mb_convert_encoding($output, "utf-8", "gb2312"); //不要在解析DOM时转码。
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
					for($c = 1; $c<$tr->length; $c++){
						//var_dump($tr->item($c)->nodeValue);
						//$title=$tr->item($c)->nodeValue;
						$cols=$tr->item($c)->getElementsByTagName('td');
						
						//0续借，1最迟应还期，2题名/著者，3卷期，4图书类型，5登录号，6借期
						
						$arr=array();
						
						$arr["title"]=$cols->item(2)->nodeValue;
						$arr["bno"]=$cols->item(5)->nodeValue;
						$arr["data1"]=trim($cols->item(1)->nodeValue);
						$arr["data2"]=$cols->item(6)->nodeValue;
						$arr["xu"]=$cols->item(0)->nodeValue;
						$cb=$cols->item(0)->firstChild;	
						$ty=get_class($cb);
						
						if($ty=='DOMElement'){
							//echo $ty;
							
							$arr["renewValue"]=$cb->getAttribute("value");
							$arr["renewName"]=$cb->getAttribute("name");
							if(!strpos($arr["renewName"],"new"))continue;
						}
						array_push($rs,$arr);

					} 
					//echo "结果:".$tr;
					//$output = $result->ownerDocument->saveXML($result );
					//$output=$result->nodeValue;
				}				
 
			}
			echo Json::encode($rs);			
			
		
		exit();
	}
	
	
	//修改密码,图书证号，旧密码，新密码
	public function changePWAction()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		//密码字段出现在一个不安全的页面中（http://）。这是一个可能导致用户登录凭据被窃取的安全风险。
				
		//参数检查
		$cardno=$_REQUEST['cardno'];
		if(!isset($cardno)||$cardno=="")exit();
		$pass=$_REQUEST['pass'];
		if(!isset($pass)||$pass=="")exit();
		$uname=$_REQUEST['uname'];
		$uname=iconv("utf-8","gbk//ignore",$uname); //WebSite那端接受的是gbk的encode码。

		if(!isset($uname)||$uname=="")exit();
		$newpass1=$_REQUEST['newpass1'];
		if(!isset($newpass1)||$newpass1=="")exit();
		$newpass2=$_REQUEST['newpass2'];
		if(!isset($newpass2)||$newpass2=="")exit();
		//echo 'cardno='.$cardno.',pass='.$pass.',';
		
		$rs=array();
		
		if($newpass1!=$newpass2)
		{
			$rs['message']="两次输入的新密码不匹配";
			$rs['status']="0";
			echo Json::encode($rs);	
			exit();
		}
		
		$ch = curl_init();
			// 2. 设置选项，包括URL
			//curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/s3trs.exe");
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($cardno).'&pass='.urlencode($pass).'&uname='.urlencode($uname).'&newpass1='.urlencode($newpass1).'&newpass2='.urlencode($newpass2).'&query=query';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			// 4. 释放curl句柄
			curl_close($ch);
			$output=mb_convert_encoding($output, "utf-8", "gb2312"); //不要在解析DOM时转码。
			//echo $output;
			//姓名不符，不能修改口令!
			//口令错误!
			//口令修改成功!
			//新口令与旧口令相同!
			if(strpos($output,'姓名不符'))
			{
				$rs['status']="0";
				$rs['message']="姓名不符，不能修改密码!";
			}else if(strpos($output,'口令错误'))
			{
				$rs['status']="0";
				$rs['message']="口令错误";
			}
			else if(strpos($output,'口令修改成功'))
			{
				$rs['status']="1";
				$rs['message']="密码修改成功";
				$rs['sid']=session_id();
				
			}
			else if(strpos($output,'新口令与旧口令'))
			{
				$rs['status']="0";
				$rs['message']="新密码与旧密码相同";
			}
			else
			{
				$rs['status']="0";
				$rs['message']="未知错误";
			}
			
			//$rs['message']=$output;
		
		echo Json::encode($rs);	
		exit();
	}
	
	public function logoutAction()
	{
		LoginModel::logout();
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		$rs=array();
		$rs['status']="0";
		$rs['message']='已注销';
		echo Json::encode($rs);
		exit();
	}
	
	//检查登录状态
	public function checkLogin()
	{
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		$rs=Array();
		$lm=LoginModel::getLoginModel();
		
		if(!$lm->auth)
		{
			$rs['status']="0";
			$rs['message']='未登录';
			echo Json::encode($rs);
			exit();
		}
		
	}
	public function reserveAction()
	{
		$this->checkLogin();
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		$resv=$_POST['resv'];		
		
		
		
		//先登录，再预约，这样能得到预约信息
		$lm=LoginModel::getLoginModel();
	
		if($lm->auth && isset($resv) && $resv!="")
		{
			//echo $resv;	
			$un=$lm->id;
			$pw=$lm->password;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&query='.urlencode('query').'';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			curl_setopt($ch,CURLOPT_COOKIEJAR,'/tmp/cookie'); 
			$output = curl_exec($ch);
			//echo $output;
			
			//预约http://192.168.4.152:82/cgi-win/tcgir.exe post vol1=&volbooktype1=b1b&bktype0=0&org0=0&ctrlno=151270&vol=1&org=0&bktype=0
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/tcgir.exe");
			//curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/tcgid.exe?s2495r20w%C8%FD%B9%FA%D1%DD%D2%E5");
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_COOKIEFILE,'/tmp/cookie'); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $resv);
			curl_setopt($ch, CURLOPT_POST, 1);
			$output = curl_exec($ch);
			curl_close($ch);
			
			$output=mb_convert_encoding($output, "utf-8", "gb2312");
			//本次预约未能成功, 您已预约本书
			//本次预约未能成功, 您对此类型图书的预约种数已达上限: (中文图书)
			$rs=array();
			if(strpos($output,'您已预约本书'))
			{
				$rs['message']="本次预约未能成功, 您已预约本书";
			}else if(strpos($output,'预约种数已达上限'))
			{
				$rs['message']="本次预约未能成功, 您对此类型图书的预约种数已达上限: (中文图书)";
			}
			else
			{
				$rs['message']="预约成功";
			}
			//echo $output;
			echo Json::encode($rs);

		}		
		
		exit();
	}
	
	
	//我的订单
	public function getMyReserveAction()
	{
		$this->checkLogin();
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');

		$rs=Array();
		$lm=LoginModel::getLoginModel();
		
		
			$un=$lm->id;
			$pw=$lm->password;		
			
			$ch = curl_init();
			// 2. 设置选项，包括URL
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&uname=&newpass1=&newpass2=&query=query';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			// 4. 释放curl句柄
			curl_close($ch);
			//$output=mb_convert_encoding($output, "utf-8","gb2312"); //不要在解析DOM时转码。
			//echo $output;
			
			$arrCenter=explode("<center>",$output);
			//echo $arrCenter[2]; //2代表预约表格
			if(isset($arrCenter[2]))
			{
				//$arrCenter[2]=mb_convert_encoding($arrCenter[2], "gb2312","utf-8");
				$dom = new Query('<html><meta http-equiv="Content-Type" content="text/html;charset=gb2312">">'.$arrCenter[2].'</html>');
				$results = $dom->execute('table');
				foreach ($results as $result) {
					$tr=$result->getElementsByTagName('tr');
					for($c = 1; $c<$tr->length; $c++){
						//var_dump($tr->item($c)->nodeValue);
						//$title=$tr->item($c)->nodeValue;
						$cols=$tr->item($c)->getElementsByTagName('td');
						
						//0取消，1最迟借阅期，2题名/著者，3卷期，4图书类型，5到书日期，6预约日期
						
						$arr=array();
						$href=$cols->item(2)->firstChild->getAttribute("href");
						$href=substring2($href,"tcgid.exe?",null);
						$arr["href"]=$href;
						$arr["title"]=$cols->item(2)->nodeValue;
						//$arr["title"]=iconv("utf-8","gbk//ignore",$arr["title"]);
						$arr["bno"]=$cols->item(5)->nodeValue;
						$arr["data1"]=trim($cols->item(1)->nodeValue);
						$arr["data2"]=$cols->item(6)->nodeValue;
						$arr["xu"]=$cols->item(0)->nodeValue;
						
						$cb=$cols->item(0)->firstChild;
						
						$arr["resvrecValue"]=$cb->getAttribute("value");
						$arr["resvrecName"]=$cb->getAttribute("name");
						//<input type="hidden" value="1" name="resvrow">行数
						$str=substr($str,7);
						$arr["resvrow"]=$str ;
						array_push($rs,$arr);
					}
				}
			}
			echo Json::encode($rs);			
			
		
		exit();
	}
	
	//取消预订
	public function cancReserveAction()
	{
		$resvrecName=$_REQUEST['resvrecName'];
		$resvrecValue=$_REQUEST['resvrecValue'];
		$resvrow=$_REQUEST['resvrow'];
		$this->checkLogin();
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		
		
		
		//先登录，再预约，这样能得到预约信息
		$lm=LoginModel::getLoginModel();
	
		if($lm->auth)
		{
			//echo $resv;	
			$un=$lm->id;
			$pw=$lm->password;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&query='.urlencode('query').'';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			curl_setopt($ch,CURLOPT_COOKIEJAR,'/tmp/cookie'); 
			$output = curl_exec($ch);
			
			$dom = new Query($output);
			$results = $dom->execute('table');
			
			//获得预约表格的行数，用于取消预约resvrow
			//$tr=$results.count();
			//print_r($results->childNodes[1]);
			//$ch = curl_init();
			// 2. 设置选项，包括URL
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = $resvrecName.'='.urlencode($resvrecValue).'&query=canc&resvrow='.$resvrow;
			echo $curlPost;
			curl_setopt($ch,CURLOPT_COOKIEFILE,'/tmp/cookie'); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			// 4. 释放curl句柄
			curl_close($ch);
			//$output=mb_convert_encoding($output, "utf-8", "gb2312");
			//echo $output;
		}
			exit();
	}
	
	//续借
	//email=&htmlwbd=&renew54=175553&loanrec=55&query=renew
	//email=&htmlwbd=&renew53=130679&loanrec=55&query=renew
	//email=&htmlwbd=&renew2=182722&loanrec=55&query=renew
	//175553续借成功！
	public function renewAction()
	{
		$renewName=$_REQUEST['renewName'];
		$renewValue=$_REQUEST['renewValue'];
		$loanrec=$_REQUEST['loanrec'];
		$this->checkLogin();
		header('Access-Control-Allow-Origin: *');
		header('Content-Encoding: plain');
		
		
		
		//先登录，再预约，这样能得到预约信息
		$lm=LoginModel::getLoginModel();
	
		if($lm->auth)
		{
			//echo $resv;	
			$un=$lm->id;
			$pw=$lm->password;
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = 'cardno='.urlencode($un).'&pass='.urlencode($pw).'&query='.urlencode('query').'';
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			curl_setopt($ch,CURLOPT_COOKIEJAR,'/tmp/cookie'); 
			$output = curl_exec($ch);
			
			$dom = new Query($output);
			$results = $dom->execute('table');
			
			//获得预约表格的行数，用于取消预约resvrow
			//$tr=$results.count();
			//print_r($results->childNodes[1]);
			//$ch = curl_init();
			// 2. 设置选项，包括URL
			curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/service.exe");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_POST, 1);
			$curlPost = $renewName.'='.urlencode($renewValue).'&query=renew&loanrec='.$loanrec;
			echo $curlPost;
			curl_setopt($ch,CURLOPT_COOKIEFILE,'/tmp/cookie'); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			// 3. 执行并获取HTML文档内容
			$output = curl_exec($ch);
			// 4. 释放curl句柄
			curl_close($ch);
			//$output=mb_convert_encoding($output, "utf-8", "gb2312");
			//echo $output;
		}
			exit();
	}
	
	
	//新书通报
	public function getXSTBAction()
	{
		header('Access-Control-Allow-Origin: *');	
		header('Content-Encoding: plain');	
		$sdate=$_REQUEST['sdate'];
		if(!isset($sdate)||$sdate=="")exit();
		$cclass=$_REQUEST['cclass'];
		if(!isset($cclass)||$cclass=="")exit();
		//2014/1/1-2014/4/31
		$sdate=urldecode($sdate);
		
		$arrdate=explode("-",$sdate);
		$arrdate2=explode("/",$arrdate[0]);
		$sdatey=$arrdate2[0];
		$sdatem=$arrdate2[1];
		$sdated=$arrdate2[2];
		$arrdate2=explode("/",$arrdate[1]);
		$edatey=$arrdate2[0];
		$edatem=$arrdate2[1];
		$edated=$arrdate2[2];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "http://192.168.4.152:82/cgi-win/web_xstb.exe");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);		
		//$curlPost="title=&sdatey=2014&sdatem=1&sdated=1&author=&edatey=2014&edatem=4&edated=24&publisher=&rowcount=500&class=tu&query=检索";
		$curlPost="title=&sdatey=".$sdatey."&sdatem=".$sdatem."&sdated=".$sdated."&author=&edatey=".$edatey."&edatem=".$edatem."&edated=".$edated."&publisher=&rowcount=500&class=".$cclass."&query=%BC%EC%CB%F7";
		//echo $curlPost;
		curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
		$output = curl_exec($ch);
		curl_close($ch);
		//$output=mb_convert_encoding($output, "utf-8", "gb2312");
		$rs=array();	
		$dom = new Query('<meta http-equiv="Content-Type" content="text/html;charset=gb2312">'.$output);
		$results = $dom->execute('table');
		foreach ($results as $result) {
			$tr=$result->getElementsByTagName('tr');			
			for($c = 1; $c<$tr->length; $c++){
				$cols=$tr->item($c)->getElementsByTagName('td');
				$classno=$cols->item(0)->textContent;
				$title=$cols->item(1)->textContent;
				$href=$cols->item(1)->firstChild->getAttribute("href");
				$href=substr($href,strpos($href,'?')+1);
				array_push($rs,array("title"=>$title,"authors"=>"","year"=>"","classno"=>$classno,"href"=>$href));
			}
		}
		//echo $output;
		echo Json::encode($rs);
		exit();
	}
}
?>