<?php
namespace JYLibrary\llt\Zend\Crypt;
use Zend\Crypt\BlockCipher;
class lltCrypt
{
	private static $key='!!lltangtest@';
	public static function encrypt($str)
	{
		$bCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		$bCipher->setKey('encryption key');
		$rs = $bCipher->encrypt($str);
		return $rs;
	}
	public static function decrypt($str)
	{
		$bCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
		$bCipher->setKey('encryption key');
		$rs = $bCipher->decrypt($str);
		return $rs;
	}
}
?>