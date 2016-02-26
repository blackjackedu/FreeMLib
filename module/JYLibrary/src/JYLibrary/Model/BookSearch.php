<?php
namespace JYLibrary\Model;
class BookSearch
{
	protected function getDB()
	{
		
		$link = @sybase_connect('sulcmis3', 'sa', '123456') or die("不能连接数据库!");
		//$link = @sybase_connect('sulcmis3', 'sa', '123456') or die("Could not connect !");  //连接数据库，第一空必须写服务名称，不能是ip;
		$db = @sybase_select_db("sulcmis",$link) or  die("数据库没有选择");
		echo "getDB";
		echo $link;
		return $link;
	}
	public function getDetail($ctrlno)
	{
		$link=$this->getDB();
		$rs=sybase_query("exec B_DetailSeek ".$ctrlno,$link);
		$data="";
		while ($row = sybase_fetch_array($rs)) {
			$data.=iconv("gbk","utf-8//ignore",$row["subfld"]).";<br />";
		}
		sybase_free_result($rs);
		sybase_close($link);
		return $data;
	}
	public function findBook($qtxt)
	 {
		
		$link=$this->getDB();
			$qtxt=iconv("utf-8","gbk//ignore",$qtxt);
			$rs=sybase_query("exec llt_search "."'".$qtxt."'",$link);
			$data=array();
		
			while ($row = sybase_fetch_array($rs)) {
				$book=new Book();
				$book->no = $row["ctrlno"];
				$book->title = iconv("gbk","utf-8",$row["title"]);
				$book->authors=iconv("gbk","utf-8",$row["authors"]);
				$book->publisher=iconv("gbk","utf-8",$row["publisher"]);
				$book->pubdate=iconv("gbk","utf-8",$row["pubdate"]);
				$book->barcode=$row["barcode"];
				$book->status=iconv("gbk","utf-8",$row["status"]);
				$book->classno=iconv("gbk","utf-8",$row["classno"]);
				array_push($data,$book);
			}
			sybase_free_result($rs);
			sybase_close($link);
			return $data;
	}
	 
	public function findBookByPage($qtxt,$offset,$itemCountPerPage)
	{	
		$link=$this->getDB();
		$qtxt=iconv("utf-8","gbk//ignore",$qtxt);
		$rs=sybase_query("exec llt_searchByPage "."'".$qtxt."',".$offset.",".$itemCountPerPage,$link);
		$data=array();
		while ($row = sybase_fetch_array($rs)) {
				$book=new Book();
				$book->no = $row["ctrlno"];
				$book->title = iconv("gbk","utf-8//ignore",$row["title"]);
				$book->authors=iconv("gbk","utf-8//ignore",$row["authors"]);
				$book->publisher=iconv("gbk","utf-8//ignore",$row["publisher"]);
				$book->pubdate=iconv("gbk","utf-8//ignore",$row["pubdate"]);
				$book->barcode=$row["barcode"];
				$book->status=iconv("gbk","utf-8//ignore",$row["status"]);
				$book->classno=iconv("gbk","utf-8//ignore",$row["classno"]);
				array_push($data,$book);
			}
			sybase_free_result($rs);
			sybase_close($link);
			return $data;
	}
	public function getQueryCount($qtxt)
	{
		$link=$this->getDB();
		$qtxt=iconv("utf-8","gbk//ignore",$qtxt);
		$query=sybase_query("exec llt_searchByPageCount "."'".$qtxt."'",$link);
		$row = sybase_fetch_array($query);
		return $row[0];
	}
}
?>