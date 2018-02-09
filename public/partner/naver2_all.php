<?php
set_time_limit(0);
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

include("../dbconn.php");
include("../lib/lib.func.php");
include("../conf/config.php");
include("../conf/config.pay.php");

// 카테고리정보 모두 가지고 오기
$query = "select catnm,category from ".GD_CATEGORY;
$result = mysql_query($query);
$ar_category=array();
while($row = mysql_fetch_array($result))
{
	$ar_category[$row['category']]=$row['catnm'];
}
$query = "select sno,brandnm from ".GD_GOODS_BRAND;
$result = mysql_query($query);
$ar_brand=array();
while($row = mysql_fetch_array($result))
{
	$ar_brand[$row['sno']]=$row['brandnm'];
}

$query = "select
a.goodsno , a.category ,c.totstock as stock,
c.goodsnm,c.img_m,c.brandno,c.origin,c.maker,c.launchdt,c.delivery_type,c.goods_delivery,c.use_emoney,c.usestock,
d.price,d.reserve
from
(select goodsno,category from ".GD_GOODS_LINK." where hidden=0 group by goodsno) as a
left join ".GD_GOODS." as c on a.goodsno=c.goodsno
,".GD_GOODS_OPTION." as d
where a.goodsno=d.goodsno and d.link=1 and c.open=1 and c.runout=0";
$result = mysql_query($query);
while($row = mysql_fetch_assoc($result))
{
	if($row['usestock']=='o' && $row['stock']==0)
	{
		continue;
	}

	$ar_data=array();
	$ar_data['begin']='';
	$ar_data['mapid']=$row['goodsno'];
	$ar_data['pname']=$row['goodsnm'];
	$ar_data['price']=$row['price'];
	$ar_data['pgurl']="http://".$cfg[shopUrl].$cfg[rootDir]."/goods/goods_view.php?goodsno={$row['goodsno']}&inflow=naver";
	$ar_data['igurl']="http://".$cfg[shopUrl].$cfg[rootDir]."/data/goods/".$row['img_m'];

	$length = strlen($row['category'])/3;
	for($i=1;$i<=$length;$i++)
	{
		$tmp=substr($row['category'],0,$i*3);
		$ar_data['cate'.$i]=$ar_category[$tmp];
		$ar_data['caid'.$i]=$tmp;
	}
	if($row['brand']) $ar_data['brand']=$ar_brand[$row['brand']];
	if($row['maker']) $ar_data['maker']=$row['maker'];
	if($row['origin']) $ar_data['origi']=$row['origin'];
	if($row['launchdt'] && $row['launchdt']!='0000-00-00') $ar_data['pdate']=$row['launchdt'];


	switch($row['delivery_type']) {
		case "0":
			if($set['delivery']['free'] <= $row['price']) $ar_data['deliv']=5;
			else $ar_data['deliv']=$set['delivery']['default'];
			break;
		case "1":
			$ar_data['deliv']=0;
			break;
		case "2":
			$ar_data['deliv'] = $row['goods_delivery'];
			break;
		case "3":
			$ar_data['deliv'] = -1;
			break;
	}

	if($row['use_emoney']=='0')
	{
		if( !$set['emoney']['chk_goods_emoney'] ){
			if( $set['emoney']['goods_emoney'] ) {
				$dc=$set['emoney']['goods_emoney'];
				$tmp_price = $row['price'];
				if( $set['emoney']['cut'] ) $po = pow(10,$set['emoney']['cut']);
				else $po = 100;
				$tmp_price = (substr($dc,-1)=="%") ? $tmp_price * substr($dc,0,-1) / 100 : $dc;
				$ar_data['point'] =  floor($tmp_price / $po) * $po;

			}
		}else{
			$ar_data['point']	= $set['emoney']['goods_emoney'];
		}
	}
	else
	{
		$ar_data['point']=$row['reserve'];
	}

	$ar_data['ftend']='';

	foreach($ar_data as $key=>$value)
	{
		echo '<<<'.$key.'>>>'.$value."\n";
	}

}


?>

