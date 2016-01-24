// JavaScript Document

//var site="http://crscloud.sinaapp.com";
var site="http://192.168.1.168:8080/crscloud";
//var site="http://192.168.1.12:8080/mlearning";
var mode="LS";

var messager;
var ldb;
var classroom;

function initClassRoom()
{
	classroom=new ClassRoom();
	ldb=new localDB();
	classroom.sid=ldb.getSID();
}

function initMessager()
{
	messager=getMessager(); 
	ldb=new localDB();
	messager.sid=ldb.getSID();
}

function login(userID,pw,successHandle,errorHandle)
{
	$.ajax({
            type: "post",
            data:{id:userID,password:pw},
            url: site+"/login",
            crossDomain :true,
			cache: false,
            dataType:"json",           
            success: successHandle,
			error:errorHandle
		});
}
function getMessager()
{
	var messager;
	if(mode=="LS")
	{
		messager=new LSMessager();
	}else if(mode=="CLOUD")
	{
		messager=new CloudMessager();
	}
	
	
	return messager;
}


function getURLParameter(url, name) {
    return (RegExp(name + '=' + '(.+?)(&|$)').exec(url)||[,null])[1];
}
		

/*
interface messager{
	function connect(succHandle);
	function disconnect();
	function subscribe(succHandle);
	function send(Message m,succHandle);
	function getLastMessages(succHandle);
}
*/
function LSMessager(){};
LSMessager.prototype.connect=function(successHandle)
{
	$.ajax({
            type: "post",
            url: site+"/lsmessage/connect;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}
LSMessager.prototype.getLastMessages=function(successHandle)
{
	$.ajax({
            type: "post",
            url: site+"/lsmessage/getLastMessages;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}
LSMessager.prototype.subscribe=function(successHandle)
{
	/*
	(function getMessages(){
            	$.ajax({
                	url: site+"/lsmessage/subscribe;jsessionid="+this.sid,
                	type:"POST",
					crossDomain :true,
            		dataType:"json",            		
                	success: successHandle
            	}).always(function(){
                	getMessages();
            	});
			})(); */
			
			$.ajax({
                	url: site+"/lsmessage/subscribe;jsessionid="+this.sid,
                	type:"POST",
					crossDomain :true,
            		dataType:"json",            		
                	success: successHandle
            	})			
	
}
LSMessager.prototype.send=function(message,successHandle)
{
	$.ajax({
        type: "POST",
        data:message,
        url: site+"/lsmessage/send;jsessionid="+this.sid,
        crossDomain :true,            		
        success: successHandle
	});
}
LSMessager.prototype.getMessages=function(otherID,successHandle)
{
	$.ajax({
            type: "post",
			data:{destID:otherID},
            url: site+"/lsmessage/getMessages;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}


LSMessager.prototype.sid="";
LSMessager.prototype.channel="";


function ClassRoom(){};
ClassRoom.prototype.getClassRoomList=function(successHandle)
{
	$.ajax({
            type: "get",
            url: site+"/classroom/getClassRoomList;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}
ClassRoom.prototype.enter=function(crid,successHandle)
{
	$.ajax({
            type: "post",
			data:{crid:crid},
            url: site+"/classroom/enter;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}
//订阅课堂消息
ClassRoom.prototype.subscribe=function(crid,successHandle)
{
	$.ajax({
            type: "post",
			data:{crid:crid},
            url: site+"/classroom/subscribe;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}

//发送试题
ClassRoom.prototype.sendItem=function(crid,iid,successHandle)
{
	$.ajax({
            type: "post",
			data:{crid:crid,iid:iid},
            url: site+"/classroom/sendItem;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}

//发送回答
ClassRoom.prototype.sendAnswer=function(crid,iid,answer,successHandle)
{
	$.ajax({
            type: "post",
			data:{crid:crid,iid:iid,answer:answer},
            url: site+"/classroom/sendAnswer;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}


ClassRoom.prototype.getClassRoomAIStatus=function(crid,iid,successHandle)
{
	$.ajax({
            type: "post",
			data:{crid:crid,iid:iid},
            url: site+"/classroom/getClassRoomAIStatus;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}


ClassRoom.prototype.sid="";

/*=============================================================



*/
//云版
function CloudMessager(){};
CloudMessager.prototype.connect=function(successHandle)
{
	$.ajax({
            type: "post",
            url: site+"/saemessage/connect;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}
CloudMessager.prototype.getLastMessages=function(successHandle)
{
	$.ajax({
            type: "post",
            url: site+"/saemessage/getLastMessages;jsessionid="+this.sid,
            cache: false,
			crossDomain :true,
            dataType:"json",           
            success: successHandle
	});
}

//对于SAE，不需要。
CloudMessager.prototype.subscribe=function(successHandle)
{
	(function getMessages(){
            	$.ajax({
                	url: site+"/lsmessage/subscribe;jsessionid="+this.sid,
                	type:"POST",
					crossDomain :true,
            		dataType:"json",            		
                	success: successHandle
            	}).always(function(){
                	getMessages();
            	});
			})(); 			
	
}
CloudMessager.prototype.send=function(message,successHandle)
{
	$.ajax({
        type: "POST",
        data:message,
        url: site+"/saemessage/send;jsessionid="+this.sid,
        crossDomain :true,            		
        success: successHandle
	});
}
CloudMessager.prototype.sid="";
CloudMessager.prototype.channel="";









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

