<?php
		$url='三国演义';
		//$url=$_GET['word'];
		$url=iconv("utf-8","gbk//ignore",$url);
		$word = urlencode($url);
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
		
		//$dom = new Query($output);
		//$results = $dom->execute('ol');
		//foreach ($results as $result) {
		//	$output = $result->ownerDocument->saveXML($result);
		//}
		
		$dom = new Query($output);
		$results = $dom->execute('li');
		$rs=array();
		//echo count($results);
		foreach ($results as $result) {
			//$href=$result->nodeValue;
			list($title2, $year, $classno) =explode("，", $result->nodeValue);
			list($title,$authors)=explode("／", $title2);	
			
			//http://192.168.4.152:82/cgi-win/tcgid.exe?s66276r20wmysql
			$href=$result->getElementsByTagName('a')->item(0)->getAttribute('href');
			$href=substr($href,strpos($href,'?')+1);
			array_push($rs,array("title"=>$title,"authors"=>$authors,"year"=>$year,"href"=>$href,"classno"=>$classno));
			
		}
		header('Content-Encoding: plain');
		echo json_encode($rs);
		
		//header("Content-type:text/html;charset=utf-8");
		//echo $output;
		
		exit;
?>