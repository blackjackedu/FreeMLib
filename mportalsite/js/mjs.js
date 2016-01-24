function showLoader() {  
		$.mobile.loading('show', {  
			text: '加载中...',   
			textVisible: true, 
			theme: 'b',         
			textonly: false,     
			html: ""            
		});  
	}  
	function hideLoader()  
	{      
		$.mobile.loading('hide');  
	}  
