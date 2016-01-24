<?php
function wphp_urlencode($data)
	{
		if(is_array($data)||is_object($data))
		{
			foreach($data as $k=>$v)
			{
				if(is_scalar($v))
				{
					if(is_array($data))
					{
						$data[$k]=urlencode($v);
					}else if(is_object($data))
					{
						$data->$k=urlencode($v);
					}
				}else if(is_array($data))
				{
					$data[$k]=wphp_urlencode($v);
				}else if(is_object($data))
				{
					$data->$k=wphp_urlencode($v);
				}
			}
		}
		return $data;
	}
	function ch_json_encode($data)
	{
		$ret=wphp_urlencode($data);
		$ret=json_encode($ret);
		return urldecode($ret);
	}
?>