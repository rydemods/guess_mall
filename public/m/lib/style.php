<?php
if(basename($_SERVER['SCRIPT_NAME'])===basename(__FILE__)) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
?>
<style type="text/css">
* html {background:url(http://) fixed;};
img {border:none}
td {font-family:"돋움,굴림";color:#4B4B4B;font-size:12px;line-height:17px;}

tr {font-family:"돋움,굴림";color:#4B4B4B;font-size:12px;line-height:17px;}

body {

	scrollbar-face-color: #dddddd;
	scrollbar-shadow-color: #aaaaaa;
	scrollbar-highlight-color: #ffffff;
	scrollbar-3dlight-color: #dadada;
	scrollbar-darkshadow-color: #dadada;
	scrollbar-track-color: #eeeeee;
	scrollbar-arrow-color: #ffffff;
	/*overflow-x:auto;overflow-y:scroll*/
}


BODY,TD,SELECT,DIV,form,TEXTAREA,center,option,pre,blockquote {font-family:돋음;color:666666;font-size:9pt;}


A:link    {color:#635C5A;text-decoration:none;}

A:visited {color:#545454;text-decoration:none;}

A:active  {color:333333;text-decoration:none;}

A:hover  {color:#545454;text-decoration:underline;}
.textarea {border:solid 1;border-color:#e3e3e3;font-family:돋음;font-size:9pt;color:333333;overflow:auto; background-color:transparent}

.input {font-size:12px;BORDER-RIGHT: #DCDCDC 1px solid; BORDER-TOP: #C7C1C1 1px solid; BORDER-LEFT: #C7C1C1 1px solid; BORDER-BOTTOM: #DCDCDC 1px solid; HEIGHT: 18px; BACKGROUND-COLOR: #ffffff;padding-top:2pt; padding-bottom:1pt; height:19px}

.verdana {FONT-SIZE: 9px; FONT-FAMILY: "verdana", "arial"}
.verdana2 {FONT-WEIGHT: bold; FONT-SIZE: 11px; FONT-FAMILY: "verdana", "arial"}

.ndv {background-color:#DDDDDD}
.bdv {background-color:#DDDDDD}
td.blover { background: #F7F7F7; FONT-WEIGHT: bold; FONT-SIZE: 11px; FONT-FAMILY: "verdana", "arial"} /* black:#4A4A4A, gray:#444444, silver:#E0E0E0, white:#F7F7F7 */
td.blout  { background:; FONT-WEIGHT: bold; FONT-SIZE: 11px; FONT-FAMILY: "verdana", "arial"}


.w690 {width:690px; margin-left:10px; margin-top:10px; text-align:left;}

/** 회원가입 **/
h1.type01 {padding:0px 0px 10px 0px; border-bottom:2px solid #4b94db; margin:0px 0px 20px 0px; text-align:left;}
div.table_style {padding:0px 10px;}
div.table_style h2 {font-size:12px; text-align:left; padding-bottom:10px; margin:0px; line-height:1;}
div.table_style table { border-top:1px solid #9e9e9e;}
div.table_style th {padding:10px 0px  10px 10px; text-align:left; background-color:f4f4f4; border-bottom:1px solid #e2e2e2; }
div.table_style td {padding:10px 0px  10px 10px; border-bottom:1px solid #e2e2e2;}
p.btn_c {width:100%; text-align:center; position:relative;}

/** 회원가입 **/
div.cs_center_tap {height:40px;}

/** CS **/
div.faq_wrap {width:100%; text-align:left; }
div.faq_wrap .tap ul {border-bottom:1px solid #5a5a5a;overflow:hidden; padding-left:0px !important; border-left:1px solid #c9c9c9;}
div.faq_wrap .tap li {float:left;}
div.faq_wrap .tap li a {padding:7px 15px; display:block; border-top:1px solid #c9c9c9; border-right:1px solid #c9c9c9;}
div.faq_wrap .tap li a:hover {background-color:#5a5a5a; color:#fff; text-decoration:none;  border-top:1px solid #5a5a5a; border-right:1px solid #5a5a5a;}
div.faq_wrap .tap li a.on {padding:7px 10px; display:block; border-top:1px solid #5a5a5a; border-right:1px solid #5a5a5a; background-color:#5a5a5a; color:#fff;}
div.find_wrap {border:3px solid #f3f3f3; padding:20px; overflow:hidden;}
div.find_wrap .left {text-align:center;}
div.find_wrap .table_form { margin-top:20px;}


<?
$array_menu[0]=array("leftprname","leftcommunity","leftcustomer");
$array_menu[1]=array("mainprname","mainprprice","mainspname","mainspprice","mainnotice","maininfo","mainpoll","mainboard","mainconprice","mainreserve","maintag","mainproduction","mainselfcode");
$array_menu[2]=array("choicecodename","upcodename","subcodename","prname","prprice","prmadein","prproduction","prconsumerprice","prreserve","prtag","praddcode","prsort","choiceprsort","prlist","choiceprlist","prselfcode");

if(strlen($_data->css)==0) {
	$sql = "SELECT * FROM tbltempletinfo WHERE icon_type='".$_shopdata->icon_type."' ";
	$styleresult=pmysql_query($sql,get_db_conn());
	$stylerow=pmysql_fetch_object($styleresult);
	
	$_data->css=$stylerow->default_css;
	pmysql_free_result($styleresult);
}

if(strlen($_data->css)==0) {
	for($i=0;$i<count($array_menu[0]);$i++) {
		$_data->css.="굴림,";
		$_data->css.="9pt,";
		$_data->css.="normal,";
		$_data->css.=",";
		$_data->css.=",";
	}
	$_data->css=substr($_data->css,0,-1)."";
	for($i=0;$i<count($array_menu[1]);$i++) {
		$_data->css.="굴림,";
		$_data->css.="9pt,";
		$_data->css.="normal,";
		$_data->css.=",";
		$_data->css.=",";
	}
	$_data->css=substr($_data->css,0,-1)."";
	for($i=0;$i<count($array_menu[2]);$i++) {
		$_data->css.="굴림,";
		$_data->css.="9pt,";
		$_data->css.="normal,";
		$_data->css.=",";
		$_data->css.=",";
	}
	$_data->css=substr($_data->css,0,-1);
}
$array_val=explode("",$_data->css);

$z=0;
$k=0;
$value=explode(",",$array_val[$z]);
for($i=0;$i<count($array_menu[$z]);$i++) {
	echo ".".$array_menu[$z][$i]." {font-family:".$value[$k++]."; font-size:".$value[$k++]."; font-weight:".$value[$k++]."; ";
	if(strlen($value[$k])>0) echo "text-decoration:".$value[$k]."; ";
	$k++;
	if(strlen($value[$k])>0) echo "color:".$value[$k]."; ";
	$k++;
	echo "}\n";
}

$z=1;
$k=0;
$value=explode(",",$array_val[$z]);
for($i=0;$i<count($array_menu[$z]);$i++) {
	echo ".".$array_menu[$z][$i]." {font-family:".$value[$k++]."; font-size:".$value[$k++]."; font-weight:".$value[$k++]."; ";
	if(strlen($value[$k])>0) echo "text-decoration:".$value[$k]."; ";
	$k++;
	if(strlen($value[$k])>0) echo "color:".$value[$k]."; ";
	$k++;
	echo "}\n";
}

$z=2;
$k=0;
$value=explode(",",$array_val[$z]);
for($i=0;$i<count($array_menu[$z]);$i++) {
	echo ".".$array_menu[$z][$i]." {font-family:".$value[$k++]."; font-size:".$value[$k++]."; font-weight:".$value[$k++]."; ";
	if(strlen($value[$k])>0) echo "text-decoration:".$value[$k]."; ";
	$k++;
	if(strlen($value[$k])>0) echo "color:".$value[$k]."; ";
	$k++;
	echo "}\n";
}
?>
</style>
