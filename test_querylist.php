<?php
require('../vendor/autoload.php');
use QL\QueryList;
$page = 'http://shop.m.taobao.com/shop/coupon.htm?sellerId=2273169350&activityId=6d3429aef0aa46e3b6e4bbf460456469&clk1=4aaea1f5f933141c98e9558d1d689529&upsid=4aaea1f5f933141c98e9558d1d689529';
$rules = array(
   'title' => array('.coupon-info>dl>dt','text')
);
$ql = QueryList::Query($page,$rules);
$data = $ql->getData();
//$data = json_encode($data);
print_r($data);

?> 
