<?
if($num=strpos($body,"[ONE_CODEA_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$code_a_style=$s_tmp[2];
}
if($num=strpos($body,"[ONE_CODEB_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$code_b_style=$s_tmp[2];
}
if($num=strpos($body,"[ONE_CODEC_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$code_c_style=$s_tmp[2];
}
if($num=strpos($body,"[ONE_CODED_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$code_d_style=$s_tmp[2];
}

if($num=strpos($body,"[ONE_PRLIST_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$prlist_style=$s_tmp[2];
}

if(strlen($code_a_style)==0) $code_a_style="width:150px";
if(strlen($code_b_style)==0) $code_b_style="width:150px";
if(strlen($code_c_style)==0) $code_c_style="width:150px";
if(strlen($code_d_style)==0) $code_d_style="width:150px";
if(strlen($prlist_style)==0) $prlist_style="width:300px";


if(strpos($body,"[IFROYAL]")!==false) {
	$ifroyalnum=strpos($body,"[IFROYAL]");
	$endroyalnum=strpos($body,"[IFENDROYAL]");
	$mainroyal=substr($body,$ifroyalnum+9,$endroyalnum-$ifroyalnum-9);
	$body=substr($body,0,$ifroyalnum)."[ROYALVALUE]".substr($body,$endroyalnum+12);
}

if(strpos($body,"[IFBASKET]")!==false) {
	$ifbasketnum=strpos($body,"[IFBASKET]");
	$endbasketnum=strpos($body,"[IFENDBASKET]");
	$elsebasketnum=strpos($body,"[IFELSEBASKET]");

	$basketstartnum=strpos($body,"[FORBASKET]");
	$basketstopnum=strpos($body,"[FORENDBASKET]");
	$optionstartnum=strpos($body,"[IFOPTION]");
	$optionstopnum=strpos($body,"[IFENDOPTION]");

	$ifbasket=substr($body,$ifbasketnum+10,$basketstartnum-($ifbasketnum+10))."[BASKETVALUE]".substr($body,$basketstopnum+14,$elsebasketnum-($basketstopnum+14));

	$nobasket=substr($body,$elsebasketnum+14,$endbasketnum-$elsebasketnum-14);
	
	$optionbasket=substr($body,$optionstartnum+10,$optionstopnum-$optionstartnum-10);
	$mainbasket=substr($body,$basketstartnum,$optionstartnum-$basketstartnum)."[OPTIONVALUE]".substr($body,$optionstopnum+13,$basketstopnum-$optionstopnum+1);

	$assemblestartnum=strpos($mainbasket,"[IFASSEMBLE]");
	$assemblestopnum=strpos($mainbasket,"[IFENDASSEMBLE]");
	if($assemblestartnum>0) {
		$assemblebasket=substr($mainbasket,$assemblestartnum+12,$assemblestopnum-$assemblestartnum-12);
		$mainbasket=substr($mainbasket,0,$assemblestartnum)."[ASSEMBLEVALUE]".substr($mainbasket,$assemblestopnum+15);
	} else {
		$assemblebasket="";
	}
	
	$packageliststartnum=strpos($mainbasket,"[IFPACKAGELIST]");
	$packageliststopnum=strpos($mainbasket,"[IFENDPACKAGELIST]");
	if($packageliststartnum>0) {
		$packagelistbasket=substr($mainbasket,$packageliststartnum+15,$packageliststopnum-$packageliststartnum-15);
		$mainbasket=substr($mainbasket,0,$packageliststartnum)."[PACKAGELISTVALUE]".substr($mainbasket,$packageliststopnum+18);
	} else {
		$packagelistbasket="";
	}

	$packagestartnum=strpos($mainbasket,"[IFPACKAGE]");
	$packagestopnum=strpos($mainbasket,"[IFENDPACKAGE]");
	if($packagestartnum>0) {
		$packagebasket=substr($mainbasket,$packagestartnum+11,$packagestopnum-$packagestartnum-11);
		$mainbasket=substr($mainbasket,0,$packagestartnum)."[PACKAGEVALUE]".substr($mainbasket,$packagestopnum+14);
	} else {
		$packagebasket="";
	}

	

	$groupstartnum=strpos($mainbasket,"[BASKET_GROUPSTART]");
	$groupstopnum=strpos($mainbasket,"[BASKET_GROUPEND]");
	if($groupstartnum>0) {
		$groupbasket=substr($mainbasket,$groupstartnum,$groupstopnum-$groupstartnum+17);
		$mainbasket=substr($mainbasket,0,$groupstartnum)."[GROUPBASKETVALUE]".substr($mainbasket,$groupstopnum+17);
	} else {
		$groupbasket="";
	}

	$body=substr($body,0,$ifbasketnum)."[ORIGINALBASKET]".substr($body,$endbasketnum+13);
}

include("basket_text.php");

#BASKET_DEL_BIZ = 비즈 스프링용 템프릿 변수 추가
$pattern=array(
	"(\[ONE_START\])",
	"(\[ONE_CODEA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_CODEB((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_CODEC((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_CODED((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_PRLIST((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[ONE_PRIMG\])",
	"(\[ONE_BASKET\])",
	"(\[ONE_END\])",
	"(\[BASKET_MSG\])",
	"(\[ORIGINALBASKET\])",
	"(\[BASKET_ORDER\])",
	"(\[BASKET_SHOPPING\])",
	"(\[BASKET_CLEAR\])",
	"(\[ROYALVALUE\])",
	"(\[BASKET_DEL_BIZ\])"
);
$replace=array($one_start,$one_code_a,$one_code_b,$one_code_c,$one_code_d,$one_prlist,$one_primg,"\"javascript:OneshotBasketIn()\"",$one_end,$basket_msg,$originalbasket,$basket_order,$basket_shopping,$basket_clear,$royalvalue,$basket_del_biz);

$body=preg_replace($pattern,$replace,$body);

echo $body;





















