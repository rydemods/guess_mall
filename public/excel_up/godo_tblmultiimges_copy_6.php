<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$ecnt=0;
$imagepath=$Dir.DataDir."shopimages/product/";
$imagepath2=$Dir.DataDir."shopimages/multi/";

$qry="select img_l,productcode from tblproduct where goodsno is not null";
$result=pmysql_query($qry);
while($data=pmysql_fetch_object($result)){
	$img_l=explode("|",$data->img_l);
	$where1="";
	$where2="";
	$size="";
	$fullsize="";
	if(count($img_l)>=2){
		$i=1;
		foreach($img_l as $k){
		$leftno=str_pad($i, 2, "0", STR_PAD_LEFT);
		
		copy($imagepath.$k,$imagepath2.$leftno."_".$k);
		copy($imagepath.$k,$imagepath2."s".$leftno."_".$k);
		
		$where1[]="primg{$leftno}";
		$where2[]="{$leftno}_{$k}";
		$size[]="550X550";
		$i++;}	
		
		for($in=0;$in<10;$in++){
			if($size[$in]==""){
				$fullsize[]="";
			}else{
				$fullsize[]=$size[$in];
			}
		}
		
		$ins_qry="insert into tblmultiimages (productcode,".implode(",",$where1).",size) values ('".$data->productcode."','".implode("','",$where2)."','".implode("",$fullsize)."')";
		pmysql_query($ins_qry);

echo $ins_qry;
echo "<br/>";

	}

}
?>