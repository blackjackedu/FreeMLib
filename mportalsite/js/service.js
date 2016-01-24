// JavaScript Document


//var site="http://192.168.4.170/";
//var ss="http://192.168.4.157:801/";
var ss="http://58.59.136.166:801/";
var site=ss+"mportalsite/";
var site2=ss+"opacsearch/";

function getNewsList(successHandle)
{
	$.ajax({
            type: "get",
            url: site+"getNewsList",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

function getNewsDetail(id,successHandle)
{
	$.ajax({
            type: "get",
			data:{id:id},
            url: site+"getNewsDetail",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

function getHSHList(successHandle)
{
	$.ajax({
            type: "get",
            url: site+"getHSHList",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}
function getHSHDetail(id,successHandle)
{
	$.ajax({
            type: "get",
			data:{id:id},
            url: site+"getHSHDetail",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

function getJLList(successHandle)
{
	$.ajax({
            type: "get",
            url: site+"getJLList",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}
function getJLDetail(id,successHandle)
{
	$.ajax({
            type: "get",
			data:{id:id},
            url: site+"getJLDetail",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}


function getKF(successHandle)
{
	$.ajax({
            type: "get",
            url: site+"getKF",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

function getBJ(successHandle)
{
	$.ajax({
            type: "get",
            url: site+"getBJ",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

//馆藏检索
function getSearchList(key,successHandle)
{
	$.ajax({
            type: "get",
			data:{key:key},
            url: site2+"getBook",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}
function getSearchDetail(id,successHandle)
{
	ldb=new localDB();
	sid=ldb.getSID();
	$.ajax({
            type: "get",
			data:{id:id},
            url: site2+"getBookDetail?PHPSESSID="+sid,
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

function login(un,pw,successHandle,errorHandle)
{
	$.ajax({
            type: "get",
			data:{un:un,pw:pw},
            url: site2+"login",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle,
			error:errorHandle
		});
}
function logout(successHandle,errorHandle)
{
	$.ajax({
            type: "get",
            url: site2+"logout",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle,
			error:errorHandle
		});
	ldb=new localDB();
	ldb.setSID("nono");
}

function changePW(da,successHandle)
{
	$.ajax({
            type: "get",
			data:da,
            url: site2+"changePW",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
	
}

function getLoanRecord(successHandle)
{
	ldb=new localDB();
	sid=ldb.getSID();
	$.ajax({
            type: "get",
            url: site2+"getLoanRecord?PHPSESSID="+sid,
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

//预约
function reserve(resv,successHandle)
{
	ldb=new localDB();
	sid=ldb.getSID();
	$.ajax({
            type: "post",
			data:{resv:resv},
            url: ss+"opacsearch/reserve?PHPSESSID="+sid,
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}
function getMyReserve(successHandle)
{
	
	ldb=new localDB();
	sid=ldb.getSID();
	//alert(sid);
	$.ajax({
            type: "get",
            url: site2+"getMyReserve?PHPSESSID="+sid,
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}
//取消预约
function cancReserve(param,successHandle)
{
	ldb=new localDB();
	sid=ldb.getSID();
	$.ajax({
            type: "get",
			data:param,
            url: ss+"opacsearch/cancReserve?PHPSESSID="+sid,
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}
//续订
//取消预约
function renew(param,successHandle)
{
	ldb=new localDB();
	sid=ldb.getSID();
	$.ajax({
            type: "get",
			data:param,
            url: ss+"opacsearch/renew?PHPSESSID="+sid,
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}
function getEbook1List(successHandle)
{
	$.ajax({
            type: "get",
            url: ss+"ebookserver1/getEbookList",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

function getEbook1Content(id,successHandle)
{
	$.ajax({
            type: "get",
			data:{id:id},
            url: ss+"ebookserver1/getEBookContent",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

function getEbook1Detail(id,successHandle)
{
	$.ajax({
            type: "get",
			data:{id:id},
            url: ss+"ebookserver1/getEBookDetail",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}

//检查登录


//新书通报
function getXSTBList(param,successHandle)
{
	$.ajax({
            type: "get",
			data:param,
            url: ss+"opacsearch/getXSTB",
            crossDomain :true,
			cache: false,
            //dataType:"json",           
            success: successHandle
		});
}


function getURLParameter(url, name) {
    return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
}


//本地存储
function localDB(){};
localDB.prototype.getAccount=function()
{
	return JSON.parse(localStorage.getItem('account'));
}
localDB.prototype.setAccount=function(data)
{
	localStorage.setItem('account',JSON.stringify(data));
}

localDB.prototype.getSID=function()
{
	return JSON.parse(localStorage.getItem('SID'));
}
localDB.prototype.setSID=function(data)
{
	localStorage.setItem('SID',JSON.stringify(data));
}

localDB.prototype.getCardNo=function()
{
	return JSON.parse(localStorage.getItem('CardNo'));
}
localDB.prototype.setCardNo=function(data)
{
	localStorage.setItem('CardNo',JSON.stringify(data));
}

localDB.prototype.getUserName=function()
{
	return JSON.parse(localStorage.getItem('UserName'));
}
localDB.prototype.setUserName=function(data)
{
	localStorage.setItem('UserName',JSON.stringify(data));
}

