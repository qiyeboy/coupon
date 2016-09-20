<style>
.success{color:#5eb95e;}
.error{color:#d7342e;}
.warning{color:#444;}
</style>
<?php
require('../queryList/autoload.php');
use QL\QueryList;
include "SQL-config.php";
mysql_query("SET NAMES 'UTF8'");
mysql_select_db($mysql_database,$conn);
date_default_timezone_set("Asia/Shanghai");
$datetime = date('Y-m-d H:i:s');

//任务
$obj_1 = QueryList("http://www.huim.com/miaoquan/?t=1");
$obj_2 = QueryList("http://www.huim.com/miaoquan/?t=2");
$obj_3 = QueryList("http://www.huim.com/miaoquan/?t=3");
$obj_4 = QueryList("http://www.huim.com/miaoquan/?t=4");
$obj_5 = QueryList("http://www.huim.com/miaoquan/?t=5");
$obj_6 = QueryList("http://www.huim.com/miaoquan/?t=6");
$obj_100 = QueryList("http://www.huim.com/miaoquan/?t=100");
insertSQL($obj_1,"1");
insertSQL($obj_2,"2");
insertSQL($obj_3,"3");
insertSQL($obj_4,"4");
insertSQL($obj_5,"5");
insertSQL($obj_6,"6");
insertSQL($obj_100,"100");


function QueryList($page){
	$rules = array(
	   'title' => array('.quan_item_tit>a','text'),
	   'pict_url' => array('.quan_item_img img','src'),
	   'zk_final_price' => array('.quan_item_price .num','text'),//券后价
	   'reserve_price' => array('.quan_item_price .list','text','',function($content){
			$content=str_replace("原价￥","",$content);
			return $content;
	   }),//原价
	   'coupon_value' => array('.item_btn_box .num','text','',function($content){
			$content=str_replace("元券","",$content);
			return $content;
	   }),//优惠券面额
	   'coupon_url' => array('.item_btn_box .item_coupon','href'),//优惠券链接
	   'item_url' => array('.item_btn_box .item_btn','href'),//优惠券链接
	);
	$ql = QueryList::Query($page,$rules);
	$obj = $ql->getData();
	return $obj;
}
//$obj = json_encode($obj);
//print_r($obj);

//数据库部分
function insertSQL($obj,$category){//数据和分类
	global $conn;
	global $datetime;
	$i=0;
	$num=count($obj); //data长度
	for ($i;$i<$num;$i++){
		$title[]=$obj[$i]["title"];
		$pict_url[]=$obj[$i]["pict_url"];
		$pic_check = strpos($pict_url[$i],"pic_bg.png");
		if($pic_check){
			echo "<p class='error'>缺省图片：".$title[$i]."</p>";
			break;
		}
		$zk_final_price[]=$obj[$i]["zk_final_price"];
		$reserve_price[]=$obj[$i]["reserve_price"];
		$coupon_value[]=$obj[$i]["coupon_value"];
		$coupon_url[]=$obj[$i]["coupon_url"];
		$item_url[]=$obj[$i]["item_url"];
		$repeatCheck[$i] = "SELECT title FROM coupon WHERE title='{$title[$i]}'";
		$repeatCheckNum = mysql_num_rows(mysql_query($repeatCheck[$i]));
		if($repeatCheckNum=="0"){//标题不重复
			//图片保留相对路径
			$pict_url[$i] = str_replace(array('!/both/300x300/unsharp/true','http://i.huim.com'),"",$pict_url[$i]);
			//优惠券链接转换
			$coupon_url[$i] = str_replace(array('https://taoquan.taobao.com/coupon/unify_apply.htm?'),"",$coupon_url[$i]);
			parse_str($coupon_url[$i],$couponId);
			$coupon_url[$i] = "https://taoquan.taobao.com/coupon/unify_apply.htm?activityId=".$couponId['activity_id']."&sellerId=".$couponId['seller_id']; 
			//淘宝客链接转换
			$item_check = strpos($item_url[$i],"s.click.taobao.com");
			if($item_check){
				$url_2 = get_redirect_url($item_url[$i]);
				$url_3 = unescape($url_2);
				$url_4 = substr($url_3, 34);
				$refer = $url_2;
				$url_5 = curl_get_redirects($url_4,$refer);
				$st1 = mb_strpos($url_5,"?id=")+4;
				$st2 = mb_strpos($url_5,'&ali_trackid');
				$num_iid = mb_substr($url_5,$st1,$st2-$st1);
				$item_url[$i] = "https://detail.tmall.com/item.htm?id=".$num_iid;
				echo $item_url[$i];
			}
			//判断优惠券链接有效
			if($couponId['seller_id']){
				$insertsql= "insert into coupon(title,pict_url,zk_final_price,reserve_price,coupon_value,coupon_url,item_url,category,datetime) values('$title[$i]','$pict_url[$i]','$zk_final_price[$i]','$reserve_price[$i]','$coupon_value[$i]','$coupon_url[$i]','$item_url[$i]','$category','$datetime')";
				mysql_query($insertsql, $conn); //执行
				echo "<p class='success'>写入成功：".$title[$i]."</p>";
			}else{
				echo "<p class='error'>链接异常：".$title[$i]."</p>";
			}
		}else{
			echo "<p class='warning'>有重复：".$title[$i]."</p>";
		}
	};
}

//模拟普通的302跳转
function get_redirect_url($url){
	$header = get_headers($url, 1);
	if (strpos($header[0], '301') !== false || strpos($header[0], '302') !== false) {
		if(is_array($header['Location'])) {
			return $header['Location'][count($header['Location'])-1];
		}else{
			return $header['Location'];
		}
	}else {
		return $url;
	}
}
//模拟JavaScript解码过程
function unescape($str) {
	$str = rawurldecode ( $str );
	preg_match_all ( "/%u.{4}|&#x.{4};|&#\d+;|.+/U", $str, $r );
	$ar = $r [0];
	foreach ( $ar as $k => $v ) {
		if (substr ( $v, 0, 2 ) == "%u")
			$ar [$k] = iconv ( "UCS-2", "GBK", pack ( "H4", substr ( $v, - 4 ) ) );
		elseif (substr ( $v, 0, 3 ) == "&#x")
			$ar [$k] = iconv ( "UCS-2", "GBK", pack ( "H4", substr ( $v, 3, - 1 ) ) );
		elseif (substr ( $v, 0, 2 ) == "&#") {
			$ar [$k] = iconv ( "UCS-2", "GBK", pack ( "n", substr ( $v, 2, - 1 ) ) );
		}
	}
	return join ( "", $ar );
}
//模拟需要refer传递的url跳转
function curl_get_redirects($url,$refer){
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_FAILONERROR, true);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_REFERER, $refer);
	curl_setopt($curl, CURLOPT_HEADER, true);
	curl_setopt($curl, CURLOPT_NOBODY, true);
	curl_setopt($curl, CURLOPT_TIMEOUT, 30);
	$result = curl_exec($curl);
	curl_close($curl);
	if (preg_match("!Location: (.*)!", $result, $matches)) {
		//echo ": redirects to $matches[1]\n"."<br>";
		return $matches[1];
	} else {
		//echo ": no redirection\n"."<br>";
		return false;
	}
}
function url_format($url){
	$search = '~^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?~i';
	$url = trim($url);
	preg_match_all($search, $url ,$rr);
	//var_dump($rr);
	return $rr;
}


?> 
