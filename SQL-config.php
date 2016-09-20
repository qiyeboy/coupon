<?php
    $mysql_server_name="xxxxxx"; //数据库服务器名称
    $mysql_username="root"; // 连接数据库用户名[默认为root，如果忘记可以通过select * from mysql.user 方式查询]
    $mysql_password="xxxxxxxx"; // 连接数据库密码
    $mysql_database="xxxxxxxxxxxxx"; // 数据库的名字

    $conn=mysql_connect($mysql_server_name, $mysql_username,$mysql_password);
	mysql_query("SET NAMES 'UTF8'"); 
	mysql_query("SET CHARACTER SET UTF8"); 
	mysql_query("SET CHARACTER_SET_RESULTS=UTF8'"); 
	date_default_timezone_set('Asia/Shanghai'); 
?>