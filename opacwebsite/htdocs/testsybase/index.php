<?php 
//phpinfo();
   $link = @sybase_connect('lltang2000', 'sa', '******')
			or die("Could not connect !");  //�������ݿ⣬��һ�ձ���д�������ƣ�������ip;
  echo "Connected successfully<br>";
  $db = @sybase_select_db("sulcmis",$link) //�������ݿ�
			or  die("���ݿ�û��ѡ��");
  echo "���ݿ�ѡ��ɹ�<br>";
  $sql= "select ctrlNo,F200,F210c from b_brief where ctrlNo<100";
   $rs = sybase_query($sql,$link); //��ѯ��
   if (!$rs)
	   {
			echo "SQL:".$sql."ִ��ʧ�ܣ�";
			exit;
	   }
	   //$sybase = sybase_fetch_array($rs);
	   //print_r($sybase);//����
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

