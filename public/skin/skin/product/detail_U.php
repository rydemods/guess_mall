<?php
$codename=$_cdata->code_name;
$clipcopy="\"javascript:ClipCopy('http://".$_ShopInfo->getShopurl2()."?".$_SERVER['QUERY_STRING']."')\"";

$codenavi="";
if($num=strpos($body,"[CODENAVI")) {
	$s_tmp=explode("_",substr($body,$num+9,13));
	$codenavi=($brandcode>0?getBCodeLoc($brandcode,$code,$s_tmp[0],$s_tmp[1]):getCodeLoc($code,$s_tmp[0],$s_tmp[1]));
}

$coupon1="";
$coupon2="";
if(strpos($body,"[COUPON1]")) {
	$coupon1=$couponbody1;
} else if(strpos($body,"[COUPON2]")) {
	$coupon2=$couponbody2;
}

if(strpos($body,"[IFOPTION]")!=0) {
	$ifoptionnum=strpos($body,"[IFOPTION]");
	$endoptionnum=strpos($body,"[IFENDOPTION]");
	$bodyoption=substr($body,$ifoptionnum+10,$endoptionnum-$ifoptionnum-10);
	$body=substr($body,0,$ifoptionnum)."[OPTIONVALUE]".substr($body,$endoptionnum+13);
}

if(strpos($body,"[IFPACKAGE]")!=0) {
	$ifpackagenum=strpos($body,"[IFPACKAGE]");
	$endpackagenum=strpos($body,"[IFENDPACKAGE]");
	$bodypackage=substr($body,$ifpackagenum+11,$endpackagenum-$ifpackagenum-11);
	$body=substr($body,0,$ifpackagenum)."[PACKAGEVALUE]".substr($body,$endpackagenum+14);
}

if(strpos($body,"[IFVENDER]")!=0) {
	$ifvendernum=strpos($body,"[IFVENDER]");
	$endvendernum=strpos($body,"[IFENDVENDER]");
	$bodyvender=substr($body,$ifvendernum+10,$endvendernum-$ifvendernum-10);
	$body=substr($body,0,$ifvendernum)."[VENDERVALUE]".substr($body,$endvendernum+13);
}

$review_average_color1="CACACA";
$review_average_color2="000000";
if($num=strpos($body,"[REVIEW_AVERAGE")) {
	$s_tmp=explode("_",substr($body,$num+15,13));
	if(strlen($s_tmp[0])==6) $review_average_color1=$s_tmp[0];
	if(strlen($s_tmp[1])==6) $review_average_color2=$s_tmp[1];
}

$reviewname_style="width:60";
$reviewarea_style="width:95%;height:40";
if($num=strpos($body,"[REVIEW_NAME_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$reviewname_style=$s_tmp[2];
}
if($num=strpos($body,"[REVIEW_AREA_")) {
	$s_tmp=explode("_",substr($body,$num+1,strpos($body,"]",$num)-$num-1));
	$reviewarea_style=$s_tmp[2];
}

$review_marks_color="000000";
if($num=strpos($body,"[REVIEW_MARKS")) {
	$s_tmp=substr($body,$num+13,6);
	if(strlen($s_tmp)==6) $review_marks_color=$s_tmp;
}

include("productdetail_text.php");

if((strlen($uspecname1)==0 && strlen($uspecvalue1)==0) && strpos($body,"[IFUSPEC1]")!=0) {
	$ifuspecnum1=strpos($body,"[IFUSPEC1]");
	$enduspecnum1=strpos($body,"[IFENDUSPEC1]");
	$body=substr($body,0,$ifuspecnum1).substr($body,$enduspecnum1+13);
}

if((strlen($uspecname2)==0 && strlen($uspecvalue12)==0) && strpos($body,"[IFUSPEC2]")!=0) {
	$ifuspecnum2=strpos($body,"[IFUSPEC2]");
	$enduspecnum2=strpos($body,"[IFENDUSPEC2]");
	$body=substr($body,0,$ifuspecnum2).substr($body,$enduspecnum2+13);
}

if((strlen($uspecname3)==0 && strlen($uspecvalue3)==0) && strpos($body,"[IFUSPEC3]")!=0) {
	$ifuspecnum3=strpos($body,"[IFUSPEC3]");
	$enduspecnum3=strpos($body,"[IFENDUSPEC3]");
	$body=substr($body,0,$ifuspecnum3).substr($body,$enduspecnum3+13);
}

if((strlen($uspecname4)==0 && strlen($uspecvalue4)==0) && strpos($body,"[IFUSPEC4]")!=0) {
	$ifuspecnum4=strpos($body,"[IFUSPEC4]");
	$enduspecnum4=strpos($body,"[IFENDUSPEC4]");
	$body=substr($body,0,$ifuspecnum4).substr($body,$enduspecnum4+13);
}

if((strlen($uspecname5)==0 && strlen($uspecvalue5)==0) && strpos($body,"[IFUSPEC5]")!=0) {
	$ifuspecnum5=strpos($body,"[IFUSPEC5]");
	$enduspecnum5=strpos($body,"[IFENDUSPEC5]");
	$body=substr($body,0,$ifuspecnum5).substr($body,$enduspecnum5+13);
}

$pattern=array(
	"(\[STARTFORM\])",
	"(\[ENDFORM\])",
	"(\[PRNAME\])",
	"(\[CODENAME\])",
	"(\[CODENAVI([0-9a-fA-F_]{0,13})\])",
	"(\[CLIPCOPY\])",
	"(\[COUPON1\])",
	"(\[COUPON2\])",
	"(\[PREV\])",
	"(\[NEXT\])",
	"(\[PRINFO\])",
	"(\[GONGTABLE\])",
	"(\[GONGINFO\])",
	"(\[PRIMAGE\])",
	"(\[SELLPRICE\])",
	"(\[GONGPRICE\])",
	"(\[DOLLAR\])",
	"(\[PRODUCTION\])",
	"(\[MADEIN\])",
	"(\[MODEL\])",
	"(\[BRAND\])",
	"(\[BRANDLINK\])",
	"(\[OPENDATE\])",
	"(\[SELFCODE\])",
	"(\[ADDCODE\])",
	"(\[USPECNAME1\])",
	"(\[USPECNAME2\])",
	"(\[USPECNAME3\])",
	"(\[USPECNAME4\])",
	"(\[USPECNAME5\])",
	"(\[USPECVALUE1\])",
	"(\[USPECVALUE2\])",
	"(\[USPECVALUE3\])",
	"(\[USPECVALUE4\])",
	"(\[USPECVALUE5\])",
	"(\[IFUSPEC1\])",
	"(\[IFENDUSPEC1\])",
	"(\[IFUSPEC2\])",
	"(\[IFENDUSPEC2\])",
	"(\[IFUSPEC3\])",
	"(\[IFENDUSPEC3\])",
	"(\[IFUSPEC4\])",
	"(\[IFENDUSPEC4\])",
	"(\[IFUSPEC5\])",
	"(\[IFENDUSPEC5\])",
	"(\[CONSUMPRICE\])",
	"(\[RESERVE\])",
	"(\[QUANTITY\])",
	"(\[QUANTITY_UP\])",
	"(\[QUANTITY_DN\])",
	"(\[OPTIONVALUE\])",
	"(\[VENDERVALUE\])",
	"(\[DETAIL\])",
	"(\[BASKETIN\])",
	"(\[WISHIN\])",
	"(\[BARO\])",
	"(\[TAGLIST\])",
	"(\[TAGREGINPUT((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[TAGREGOK\])",
	"(\[COLLECTION\])",
	"(\[DELIINFO\])",
	"(\[REVIEW_STARTFORM\])",
	"(\[REVIEWALL\])",
	"(\[REVIEW_WRITE\])",
	"(\[REVIEW_HIDE_START\])",
	"(\[REVIEW_SHOW_START\])",
	"(\[REVIEW_NAME((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[REVIEW_AREA((\_){0,1})([0-9a-zA-Z\.\-\:\;\%\#\ ]){0,}\])",
	"(\[REVIEW_MARKS([0-9a-fA-F]{0,6})\])",
	"(\[REVIEW_MARK1\])",
	"(\[REVIEW_MARK2\])",
	"(\[REVIEW_MARK3\])",
	"(\[REVIEW_MARK4\])",
	"(\[REVIEW_MARK5\])",
	"(\[REVIEW_RESULT\])",
	"(\[REVIEW_HIDE_END\])",
	"(\[REVIEW_SHOW_END\])",
	"(\[REVIEW_TOTAL\])",
	"(\[REVIEW_AVERAGE([0-9a-fA-F_]{0,13})\])",
	"(\[REVIEW_LIST\])",
	"(\[REVIEW_ENDFORM\])",
	"(\[QNA_ALL\])",
	"(\[QNA_WRITE\])",
	"(\[QNA_LIST\])",
	"(\[ASSEMBLETABLE\])",
	"(\[PACKAGETABLE\])",
	"(\[PACKAGEVALUE\])"
);

$startform="<form name=form1 method=post action=\"".$Dir.FrontDir."basket.php\">";

$endform = $detailhidden;
$endform.="<input type=hidden name=code value=\"".$code."\">\n";
$endform.="<input type=hidden name=productcode value=\"".$productcode."\">\n";
$endform.="<input type=hidden name=ordertype>\n";
$endform.="<input type=hidden name=opts>\n";
$endform.=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"");
$endform.="</form>\n";

$review_startform ="<form name=reviewform method=post action=\"".$_SERVER[PHP_SELF]."\">";
$review_startform.="<input type=hidden name=mode>\n";
$review_startform.="<input type=hidden name=code value=\"".$code."\">\n";
$review_startform.="<input type=hidden name=productcode value=\"".$productcode."\">\n";
$review_startform.="<input type=hidden name=sort value=\"".$sort."\">\n";
$review_startform.=($brandcode>0?"<input type=hidden name=brandcode value=\"".$brandcode."\">\n":"");
$review_endform="</form>\n";

$replace=array($startform,$endform,$prname,$codename,$codenavi,$clipcopy,$coupon1,$coupon2,$prev,$next,$prinfo,$gongtable,$gonginfo,$primage,$sellprice,$gongprice,$dollar,$production,$madein,$model,$brand,$brandlink,$opendate,$selfcode,$addcode,$uspecname1,$uspecname2,$uspecname3,$uspecname4,$uspecname5,$uspecvalue1,$uspecvalue2,$uspecvalue3,$uspecvalue4,$uspecvalue5,"","","","","","","","","","",$consumprice,$reserve,$quantity,$quantity_up,$quantity_dn,$optionvalue,$vendervalue,$detail,$basketin,$wishin,$baro,$taglist,$tagreginput,$tagregok,$collection,$deli_info,$review_startform,$reviewall,$review_write,$review_hide_start,$review_show_start,$review_name,$review_area,$review_marks,$review_mark1,$review_mark2,$review_mark3,$review_mark4,$review_mark5,$review_result,$review_hide_end,$review_show_end,$review_total,$review_average,$review_list,$review_endform,$qna_all,$qna_write,$qna_list,$assembletable,$packagetable,$packagevalue);

$body=preg_replace($pattern,$replace,$body);

echo $body;
