<?php
function substring($str,$start,$end)
{			
	$rs=substr($str,$start+1);
	$str=substr($rs,0,$end);
	return $rs;
}

function substring2($str,$ch1,$ch2)
{
	$pos=strpos($str,$ch1);				
	$rs=substr($str,$pos+strlen($ch1));
	if($ch2!=null)
	{
		$pos=strpos($rs,$ch2);
		$rs=substr($rs,0,$pos);
	}
	return $rs;
}
?>