<?php
include "SQL-config.php";
mysql_query("SET NAMES 'UTF8'");
mysql_select_db($mysql_database,$conn);

$category=isset( $_GET['t'] ) ? $_GET['t'] : '';
$seach=isset( $_GET['s'] ) ? $_GET['s'] : '';
$page=isset( $_GET['p'] ) ? $_GET['p'] : '1';
$start=$page*16;
$end=$start+16;
//echo $start,$end;
if($seach=="9"||empty($seach)){
	if($category && $seach ){ //搜索不为空&子分类
		$selectSql = "SELECT * FROM coupon WHERE category = '".$category."'  AND zk_final_price<='10' order by id desc limit $start,$end";
	}else if($category && empty($seach) ){ //搜索为空&子分类
		$selectSql = "SELECT * FROM coupon WHERE category = '".$category."' order by id desc limit $start,$end";
	}else if( empty($category) && empty($seach) ){//搜索为空&全部分类
		$selectSql = "SELECT * FROM coupon order by id desc limit $start,$end";
	}else if( empty($category) && $seach ){//搜索不为空&全部分类
		$selectSql = "SELECT * FROM coupon  WHERE zk_final_price<='10' order by id desc limit $start,$end";
	}
}else{ //关键词搜索
	$selectSql = "SELECT * FROM coupon  WHERE title LIKE '%".$seach."%' order by id desc limit $start,$end";
}
//echo $selectSql."<br/>";  //检验sql语句

$selectResult = mysql_query($selectSql, $conn);
$selectNum = mysql_num_rows($selectResult);
$selectArray = mysql_fetch_row($selectResult);
mysql_data_seek($selectResult, 0); //指针

$data = array();
while ( $selectArray = mysql_fetch_row($selectResult) ){
	$data[] = array("title" => $selectArray[1],"pict_url"=>$selectArray[2],"zk_final_price"=>$selectArray[3],"reserve_price"=>$selectArray[4],"coupon_value"=>$selectArray[5],"coupon_url"=>$selectArray[6],"item_url"=>$selectArray[7],"category"=>$selectArray[8]);
};

$data = json_encode($data);
print_r($data);

// 释放资源
mysql_free_result($selectResult);
// 关闭连接
mysql_close();  
?>