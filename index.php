<!DOCTYPE html >
<html lang="en">
<head>
    <title>优惠券实时更新 | 最新最全的淘宝天猫店铺优惠券信息</title>
    <meta name="Description" content="收集淘宝天猫最新的优惠券和超值单品信息，节省选购时间，方便大家下单购买。"/>
    <meta name="Keywords" content="天猫优惠券,天猫店铺优惠券,内部优惠券,淘宝优惠券,优惠券"/>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">  
    <link rel="stylesheet" type="text/css" href="css/coupon.css" />
    <script src="http://cdn.chaozhi.hk/resources/js/vue.min.js" type="text/javascript" charset="utf-8"></script>
	<link rel="shortcut icon" href="../favicon.ico">
</head>

<body>
<?php
$category=isset( $_GET['t'] ) ? $_GET['t'] : '';
//搜索
$seach=isset( $_GET['s'] ) ? $_GET['s'] : '';
if($seach=="9"){ $changeUrl="on"; }else{$changeUrl="off";};

include "SQL-config.php";
mysql_query("SET NAMES 'UTF8'");
mysql_select_db($mysql_database,$conn);

if($seach=="9"||empty($seach)){
	if($category && $seach ){ //搜索不为空&子分类
		$selectSql = "SELECT * FROM coupon WHERE category = '".$category."'  AND zk_final_price<='10' order by id desc limit 16";
	}else if($category && empty($seach) ){ //搜索为空&子分类
		$selectSql = "SELECT * FROM coupon WHERE category = '".$category."' order by id desc limit 16";
	}else if( empty($category) && empty($seach) ){//搜索为空&全部分类
		$selectSql = "SELECT * FROM coupon order by id desc limit 16";
		//echo $selectSql;
	}else if( empty($category) && $seach ){//搜索不为空&全部分类
		$selectSql = "SELECT * FROM coupon  WHERE zk_final_price<='10' order by id desc limit 16";
	}
}else{ //关键词搜索
	$selectSql = "SELECT * FROM coupon  WHERE title LIKE '%".$seach."%' order by id desc limit 16";
}

$selectResult = mysql_query($selectSql, $conn);
$selectNum = mysql_num_rows($selectResult);
$selectArray = mysql_fetch_row($selectResult);
mysql_data_seek($selectResult, 0); //指针
$data = array();
while ( $selectArray = mysql_fetch_row($selectResult) ){
	$data[] = array("title" => $selectArray[1],"pict_url"=>$selectArray[2],"zk_final_price"=>$selectArray[3],"reserve_price"=>$selectArray[4],"coupon_value"=>$selectArray[5],"coupon_url"=>$selectArray[6],"item_url"=>$selectArray[7],"category"=>$selectArray[8]);
};
$data = json_encode($data);
//print_r($data);
//当天更新数量
$updataNum =mysql_num_rows( mysql_query('select * from coupon where to_days(datetime) = to_days(now())', $conn) ); 
?>
<!-- 主内容 -->
<div class="container grid_auto">

    <div class="quan_area">
        <div class="quan_title cf">
            <h4><a href="/coupon/">优惠券&好货实时更新</a></h4>
            <div class="tit_update">今日新更新<span class="update_num"><?php echo $updataNum; ?></span>条</div>
        </div>        

        <div class="quan_leimu cf">
            <ul id="category_menu">
                <li><a href="/coupon/<?php if($changeUrl=="on"){ echo "?s=9";}; ?>">全部分类</a></li>
                <li><a href="/coupon/?t=1<?php if($changeUrl=="on"){ echo "&s=9";}; ?>">美妆个护</a></li>
				<li><a href="/coupon/?t=2<?php if($changeUrl=="on"){ echo "&s=9";}; ?>">家居日用</a></li>
                <li><a href="/coupon/?t=3<?php if($changeUrl=="on"){ echo "&s=9";}; ?>">食品酒水</a></li>
                <li><a href="/coupon/?t=4<?php if($changeUrl=="on"){ echo "&s=9";}; ?>">鞋包服饰</a></li>
                <li><a href="/coupon/?t=5<?php if($changeUrl=="on"){ echo "&s=9";}; ?>">数码家电</a></li>
                <li><a href="/coupon/?t=6<?php if($changeUrl=="on"){ echo "&s=9";}; ?>">母婴玩具</a></li>
                <li><a href="/coupon/?t=100<?php if($changeUrl=="on"){ echo "&s=9";}; ?>">其他</a></li>
            </ul>
            <span class="s_line"></span>
            <form action="" method="get" id="search">
            	<input type="text" name="s" id="s" value="<?php echo $seach; ?>" />
            	<input type="submit" class="btn" value="搜索" />
            </form>
            <a class="selc_quan <?php if($changeUrl=="on"){ echo "selc_current"; }?>" href="<?php if($changeUrl=="on"){ echo "/coupon/?t=".$category; }else{echo "/coupon/?t=".$category."&s=9";}; ?>" title="点击选择">
                <span class="selc_quan_icon"></span>
                <span class="selc_quan_nav">9块9封顶</span>
            </a>

        </div>

        <div class="quan_items cf">
            <ul id="content_item">
<!--start-->
<li v-for="item in items" v-cloak>
    <div class="quan_item_img">
        <a isconvert="1" href="{{ item.item_url }}" target="_blank">
            <img src="http://c.chaozhi.hk{{ item.pict_url }}	
?imageView2/2/w/300/h/300/format/jpg/interlace/1/q/80"  alt="{{ item.title }}" />
        </a>
    </div>
    <div class="quan_item_con">
        <p class="quan_item_tit"><a isconvert="1" href="{{ item.item_url }}" target="_blank"> {{ item.title }}</a></p>
        <div class="quan_item_price">
            <span class="pir">¥</span><span class="num">{{ item.zk_final_price }}</span><span class="pri_font">券后价</span><span class="list">原价￥{{ item.reserve_price }}</span>
        </div>
        <div class="item_btn_box cf">
            <a  isconvert="1"  href="{{ item.coupon_url }}" target="_blank" class="item_coupon">
                <span class="quan">领</span><span class="num">{{ item.coupon_value }}元券</span>
            </a>
            <a isconvert="1" href="{{ item.item_url }}" target="_blank" class="item_btn">       
                前往抢购           
                <span class="arr_right"></span>
            </a>    
        </div>
    </div>
    
</li>
<!--end-->
            </ul>
       </div>
    </div>
    
</div>

<script type="text/javascript" src="http://cdn.chaozhi.hk/resources/js/jquery-1.12.4.min.js" charset="utf-8"></script>
<script charset="utf-8">var local_items=<?php echo $data; ?>;</script>
<script type="text/javascript" src="js/coupon.js"></script>

<div style="display:none;">
<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1255591768'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/stat.php%3Fid%3D1255591768' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
</body>
</html>
