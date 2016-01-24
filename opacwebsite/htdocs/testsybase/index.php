<?php 
//phpinfo();
   $link = @sybase_connect('lltang2000', 'sa', '******')
			or die("Could not connect !");  //连接数据库，第一空必须写服务名称，不能是ip;
  echo "Connected successfully<br>";
  $db = @sybase_select_db("sulcmis",$link) //连接数据库
			or  die("数据库没有选择");
  echo "数据库选择成功<br>";
  $sql= "select ctrlNo,F200,F210c from b_brief where ctrlNo<100";
   $rs = sybase_query($sql,$link); //查询表
   if (!$rs)
	   {
			echo "SQL:".$sql."执行失败！";
			exit;
	   }
	   //$sybase = sybase_fetch_array($rs);
	   //print_r($sybase);//结束
	   echo '<table border="1"><tr><td>CtrlNO</td><td>F200</td><td>F210c</td>';
	   while ($row = sybase_fetch_array($rs)) {
			$id = $row["ctrlNo"];
			$F200 = $row["F200"];	
			$F210c=$row["F210c"];
			echo '<tr><td>'.$id.'</td><td>'.$F200.'</td><td>'.$F210c.'</td></tr>';
	   }
	   echo '</table>';
	   sybase_free_result($rs);
	   sybase_close($link);
?>

