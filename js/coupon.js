(function(win,doc){
	var s = doc.createElement("script"), h = doc.getElementsByTagName("head")[0];
	if (!win.alimamatk_show) {
		s.charset = "gbk";
		s.async = true;
		s.src = "http://a.alimama.cn/tkapi.js";
		h.insertBefore(s, h.firstChild);
	};
	var o = {
		pid: "mm_33578781_16572921_61632232",
		appkey: "23462007",
		unid: "coupon",
		type: "click" 
	};
	win.alimamatk_onload = win.alimamatk_onload || [];
	win.alimamatk_onload.push(o);
})(window,document);

var itemList = new Vue({
  el: '#content_item',
  data: {
    parentMessage: '',
    items : local_items
  }
})
//导航高亮
c_url();
function c_url(){
	var thisUrl;
	var locationSearch = location.search;
	var menu_obj = $("#category_menu>li>a");
if(locationSearch==""||locationSearch=="?t="||locationSearch=="?t=&s=9"){
		menu_obj.eq(0).parent("li").addClass("current");
	}else{
		menu_obj.each(function(){
			thisUrl = $(this).attr("href").replace("/coupon/","");
			//console.log(thisUrl);
			if(locationSearch==thisUrl){
				$(this).parent("li").addClass("current");
			}
		});
	}
}

//加载样式
var loadingStr = '<div class="spinner"><div class="spinner-container container1"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-container container2"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div><div class="spinner-container container3"><div class="circle1"></div><div class="circle2"></div><div class="circle3"></div><div class="circle4"></div></div></div>';

//加载更多
var stop=true; 
var page=1;
var location_href=location.href;
var category=GetQueryString(location_href,"t");
var search=GetQueryString(location_href,"s");
var maxPage="";
$(window).scroll(function(){ 
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop()) + parseFloat("300"); 
    if($(document).height() <= totalheight){ 
        if(stop==true){
            stop=false; 
			page=page+1;
            $("#content_item").after(loadingStr);
			setTimeout(function(){
				loadMore(category,search,page);
			},500)
       };
    };
});

function loadMore(t,s,p){
	//console.log("t:"+t+"\ns:"+s+"\np:"+p);
	var jsondata;
	$.ajax({
		type:"get",
		url:"loadlMore.php",
		async:false,
		dataType: 'json',
		data:{t:t,s:s,p:p},
		success:function(data){
			//console.log(data);
			jsondata = data;
		},
		error:function(){
			console.log("失败");
		}
	})
	for(var i=0;jsondata.length>i;i++){
		itemList.items.push(jsondata[i]);
	}
	$(".spinner").remove();
	if(jsondata.length<=0){
		stop=false; 
		$("#content_item").after("<p class='tip_text'>加载完，没有了</p>");
	}else{
		stop=true; 
	}
} 

function GetQueryString(url,name){
	url = url.split('?')[1];
	if(url==undefined){ 
		return null ;
	}else{
	    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	    var r = url.match(reg);
	    if(r!=null){
	    	 return  unescape(r[2]);
	    }else{
	    	return null;
	    }
	}
};