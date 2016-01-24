<?php
namespace JYLibrary\llt\Zend\Paginator;
use JYLibrary\Model\BookSearch;

class AdapterSybase implements \Zend\Paginator\Adapter\AdapterInterface
{
	public function count()
	{
		$BookSearch=new BookSearch();
		$rs=$BookSearch->getQueryCount($this->qtxt);
		return $rs;
	}
	public $qtxt;
	public function getItems($offset,$itemCountPerPage)
	{
	/*
		$rs=array();
		
		for($i=$offset;$i<$offset+$itemCountPerPage;$i++)
		{
			array_push($rs,"item"+$i);
		}
		return $rs;*/
		$BookSearch=new BookSearch();
		$rs=$BookSearch->findBookByPage($this->qtxt,$offset,$itemCountPerPage);
		return $rs;
	}
}

?>