<?php
namespace JYLibrary\services;
	interface InterfaceLibService
	{
		public function getBook($strQuery);
		public function getBookDetails($bookID);
		public function getLoanRecord($UID);
		public function changePassWord($UI,$newPW,$oldPW);
		public function opacLogout();
		public function opacLogin($UID,$PW);
		public function reserveBook();
		public function getMyReserveList($UID);
		public function cancReserveBook($ID);
		public function renewBook($ID);
		public function getNewBook();
	}
?>