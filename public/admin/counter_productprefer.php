<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$searchdate=$_POST["searchdate"];
$print=$_POST["print"];

if(ord($type)==0) $type="d";
if(ord($searchdate)==0) $searchdate=date("Ymd");
if($type!="m" && strlen($searchdate)!=8) $searchdate=date("Ymd");
if($type=="d" && $searchdate==date("Ymd")) $timeview="NO";

$month= date("m");
$len=20;
if($type=="d") {
	$sql ="SELECT * FROM tblcounterproduct WHERE date='{$searchdate}' ORDER BY cnt DESC LIMIT ".$len;
} else if($type=="w") {
	$prevdate=date("Ymd",strtotime('-7 day'));
	$sql ="SELECT SUM(cnt) as cnt,productcode FROM tblcounterproduct 
	WHERE (date<='{$searchdate}' AND date>='{$prevdate}') 
	GROUP BY productcode ORDER BY cnt DESC LIMIT ".$len;
} else if($type=="m") {
	$date=substr($searchdate,0,6);  
	if ($date==date("Ym")) {
		$sql ="SELECT SUM(cnt) as cnt,productcode FROM tblcounterproduct 
		WHERE date LIKE '{$date}%' GROUP BY productcode ORDER BY cnt DESC LIMIT ".$len;
	} else {
		$sql ="SELECT cnt,productcode FROM tblcounterproductmonth 
		WHERE date='{$date}' ORDER BY cnt DESC LIMIT ".$len;
	}
}

$sum=0;
$result = pmysql_query($sql,get_db_conn());
$count=0;
while($row = pmysql_fetch_object($result)) {
	$time[$count]=$row->cnt;
	$productcode[$count]=$row->productcode;
	if($max<$row->cnt) $max=$row->cnt;
	$sum+=$row->cnt;

	$count++;
}
pmysql_free_result($result);

$productinfo = array();
if($count>0) {	
	$prlist = implode("','",$productcode);
	$arrayproduct = array();
	if(ord($prlist)) {
		$sql ="SELECT productname,productcode,tinyimage FROM tblproduct 
		WHERE productcode IN ('{$prlist}') ORDER BY FIELD(productcode,'{$prlist}') ";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$arrayproduct[]=$row->productname."||".$row->tinyimage;
		}
		pmysql_free_result($result);
	}
	
	$countproduct=count($arrayproduct)-$count;
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function search_date(type) {
	document.form1.type.value=type;
	document.form1.submit();
}

function view_printpage(){
	window.open("about:blank","popviewprint","height=550,width=700,scrollbars=yes");
	document.form2.print.value="Y";
	document.form2.submit();
}

</script>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<?php if($print!="Y"){?>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석 &gt; 고객 선호도 분석 &gt;<span>상품 선호도</span></p></div></div>
<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>

	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_counter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
<?php } else {?>
			<table cellpadding="5" cellspacing="0" width="100%" style="table-layout:fixed">
<?php }?>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">상품 선호도</div>
				</td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align=center>

				<table cellpadding="0" cellspacing="0" width="100%">
				<form name=form1 method=post action="<?=$_SERVER['PHP_SELF']?>">
				<input type=hidden name=type>
				<input type=hidden name=print value="<?=$print?>">
				<tr>
					<td style="font-size:11px;">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
					<tr>
						<td>
<?php
						if($timeview=="NO") {
							echo "* <b><font color=\"#FF6633\">".date("Y년 m월 d일 H시 i분")."</font></b> 현재";
						} else {
							echo "* <b><font color=\"#FF6633\">".substr($searchdate,0,4)."년 ".substr($searchdate,4,2)."월 ".($type!="m"?substr($searchdate,6,2)."일":"")."</font></b> 전체";
						}
						echo " 상품 선호도 입니다.";
?>
						</td>
						<td align=right>
						<A HREF="javascript:search_date('d')"><img src="images/counter_tab_day_<?=($type=="d"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('w')"><img src="images/counter_tab_week_<?=($type=="w"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						<A HREF="javascript:search_date('m')"><img src="images/counter_tab_month_<?=($type=="m"?"on":"off")?>.gif" width="74" height="20" border="0"></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td>
                    <div class="table_style02">
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
                    <TR>
                    	<th>NO</th>
                        <th>&nbsp;</th>
                        <th>상품명</th>
                        <th>방문자수</th>
                        <th>퍼센트</th>
					</TR>
<?php
					$len=count($time); 
					for($i=0;$i<$len;$i++){
						$percent[$i]=$time[$i]/$sum*100;
						if($pos=strpos($percent[$i],".")) {
							$percent[$i]=substr($percent[$i],0,$pos+3);
						}
						if(ord($arrayproduct[$i])==0) $arrayproduct[$i]="삭제된 상품||";
						$prinfo=explode("||",$arrayproduct[$i]);
						$prname=$prinfo[0];
						$primage=$prinfo[1];
						$prname="<A HREF=\"http://{$shopurl}/front/productdetail.php?productcode={$productcode[$i]}\" target=\"_blank\">{$prname}</A>";
						/*if(ord($primage)) $primage="<img src=\"http://".$shopurl.DataDir."shopimages/product/{$primage}\" width=40 border=0>";*/
						if(ord($primage)) $primage="<img src='".getProductImage($Dir.DataDir.'shopimages/product/',$primage)."' width=40 border=0>";
						echo "<tr>\n";
						echo "	<TD>".($i+1)."</td>\n";
						echo "	<TD>{$primage}&nbsp;</td>\n";
						echo "	<TD><div class=\"ta_l\">{$prname}</div></td>\n";
						echo "	<TD><FONT color=\"#00769D\">".number_format($time[$i])."</FONT></td>\n";
						echo "	<TD>{$percent[$i]}%</td>\n";
						echo "</tr>\n";

					}
					if($len==0){
						echo "<tr bgcolor=#FFFFFF><td colspan=5><font color=#3D3D3D>해당 자료가 없습니다.</font></td></tr>\n";
					}
?>
					</table>
                    </div>
					</td>
				</tr>
				<?php if($print!="Y"){?>
				<TR>
					<TD width="100%" background="images/counter_blackline_bg.gif" height="30" align=right>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td class="font_white" align=right>
						<?php if($type=="d") {?>
						지난 접속통계 
						<select name=searchdate onchange="search_date('d')">
<?php
						for($i=59;$i>=0;$i--) {
							$date=date("Ymd",strtotime("-{$i} day"));
							echo "<option value=\"{$date}\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월 ".substr($date,6,2)."일</option>\n";
						}
?>
						</select>
						<?php }?>
						<?php if($type=="m") {?>
						지난 접속통계 
						<select name=searchdate onchange="search_date('m')">
<?php
						$cnt=11;  
						for($i=0;$i<=$cnt;$i++) {
							$date=date("Ym",strtotime("-{$i} month"));
							echo "<option value=\"{$date}\"";
							if($date==$searchdate) echo " selected";
							echo ">".substr($date,0,4)."년 ".substr($date,4,2)."월</option>\n";
						}
?>
						</select>
						<?php }?>
						</td>
						<td align=right style="padding:0,5,0,5"><A HREF="javascript:view_printpage()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
					</tr>
					</table>
					</TD>
				</TR>
				<?php } else {?>
				<TR>
					<td align=right style="padding:20,20,0,5"><A HREF="javascript:print()"><img src="images/counter_btn_print.gif" width="90" height="20" border="0"></A></td>
				</TR>
				<?php }?>
				</form>
				</table>

				</td>
			</tr>
			<tr><td height="20"></td></tr>
<?php if($print!="Y"){?>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>쇼핑몰을 방문한 고객이 어떤 특정 상품에 많은 관심을 보였는 지 알 수 있습니다.</span></dt>
                    </dl>
                    <dl>
                    	<dt><span>쇼핑몰에 등록되어 있는 많은 상품중에서 고객이 선호하는 상품을 개별적으로 확인할 수 있습니다.<br />
쇼핑몰을 방문한 고객의 개별 상품 선호도 등 고객속성을 파악할 수 있습니다. </span></dt>
                    </dl>
                    <dl>
                    	<dt><span>당사의 쇼핑몰의 많은 상품들 중에서 고객들이 가장 많은 관심을 보인 상품을 파악하여, 이 제품들에 대한 재고관리 및 판매 프로모션에 접목할 수 있습니다. </span></dt>
                    </dl>
                </div>				
                </td>
			</tr>
			<tr><td height="50"></td></tr>
<?php }?>
			</table>
<?php if($print!="Y"){?>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>

<form name=form2 method=post action="<?=$_SERVER['PHP_SELF']?>"  target=popviewprint>
<input type=hidden name=print>
<input type=hidden name=type value=<?=$type?>>
<input type=hidden name=searchdate value=<?=$searchdate?>>
</form>
</table>
<?=$onload?>
<?php 
include("copyright.php"); 
}