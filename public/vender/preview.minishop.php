<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/venderlib.php");

$isaccesspass=true;
include("access.php");

######################## 미리보기 ########################
#1. 왼쪽/상단 디자인 (preview_type => design)
#2. 메인 상단/이벤트 관리 (preview_type => main_topevent)
#3. 분류 상단/이벤트 관리 (preview_type => code_topevent)
##########################################################

$sellvidx=$_VenderInfo->getVidx();

$_MiniLib=new _MiniLib($sellvidx);
$_MiniLib->_MiniInit();

if(!$_MiniLib->isVender) {
	Header("Location:".$Dir.MainDir."main.php");
	exit;
}
$_minidata=$_MiniLib->getMiniData();

$tgbn="10";
$code="000000";

$top_eventimagepath=$Dir.DataDir."shopimages/vender/MAIN_".$_minidata->vender.".gif";
$top_eventimageurl=$Dir.DataDir."shopimages/vender/MAIN_".$_minidata->vender.".gif";

$preview_type=$_POST["preview_type"];
if($preview_type=="info") {						#미니샵 기본정보
	$up_brand_name=$_POST["up_brand_name"];
	$up_description=$_POST["up_description"];
	$image_path=$_POST["image_path"];
	$upfile=$_FILES["upfile"];
	if(strlen($upfile["name"])>0 && $upfile["size"]>0) {
		$_minidata->logo=$image_path;
	}
	$_minidata->brand_name=$up_brand_name;
	$_minidata->brand_description=$up_description;
} else if($preview_type=="design") {				#왼쪽/상단 디자인
	$top_skin_seq=$_POST["top_skin_seq"];
	$top_skin_rgb=$_POST["top_skin_rgb"];
	$top_skin_image=$_POST["top_skin_image"];
	$top_font_color=$_POST["top_font_color"];
	$left_color_seq=$_POST["left_color_seq"];
	$left_color_rgb=$_POST["left_color_rgb"];
	$left_font_color=$_POST["left_font_color"];
	$skin_upload_img=$_FILES["skin_upload_img"];
	$image_path=$_POST["image_path"];
	$skin_upload_flag=$_POST["skin_upload_flag"];

	$_minidata->top_fontcolor=$top_font_color;
	$_minidata->color=$left_color_rgb;
	$_minidata->fontcolor=$left_font_color;


	if($skin_upload_flag=="N") {
		$_minidata->top_backimg=$Dir."images/minishop/title_skin/".$top_skin_rgb."_".$top_skin_image;
	} else if($skin_upload_flag=="Y") {
		$_minidata->top_backimg=$image_path;
	}
} else if($preview_type=="main_topevent") {	#메인 상단/이벤트 관리
	$toptype=$_POST["toptype"];
	$topdesign=$_POST["topdesign"];
	$upfileimage=$_FILES["upfileimage"];
	$image_path=$_POST["image_path"];

	$_minidata->main_toptype=$toptype;
	$_minidata->main_topdesign=$topdesign;
	if($_minidata->main_toptype=="image") {
		$top_eventimagepath=$upfileimage[tmp_name];
		$top_eventimageurl=$image_path;
	}
} else if($preview_type=="code_topevent") {	#분류 상단/이벤트 관리
	$toptype=$_POST["toptype"];
	$topdesign=$_POST["topdesign"];
	$upfileimage=$_FILES["upfileimage"];
	$image_path=$_POST["image_path"];

	$select_code=$_POST["select_code"];
	$select_tgbn=$_POST["select_tgbn"];

	$tgbn=$select_tgbn;
	$code=$select_code;
	$code_a=substr($code,0,3);
	$code_b=substr($code,3,3);
	if(strlen($code_b)!=3) $code_b="000";
	$code=$code_a.$code_b;

	$_minidata->main_toptype=$toptype;
	$_minidata->main_topdesign=$topdesign;
	if($_minidata->main_toptype=="image") {
		$top_eventimagepath=$upfileimage[tmp_name];
		$top_eventimageurl=$image_path;
	}

	$_minidata->new_used="0";
	$_minidata->new_dispseq="";

	$sql = "SELECT hot_used,hot_dispseq,hot_linktype FROM tblvendercodedesign ";
	$sql.= "WHERE vender='".$_minidata->vender."' ";
	$sql.= "AND code='".substr($code,0,3)."' AND tgbn='".$tgbn."' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$_minidata->hot_used=$row->hot_used;
		$_minidata->hot_dispseq=$row->hot_dispseq;
		$_minidata->hot_linktype=$row->hot_linktype;
	} else {
		$_minidata->hot_used="0";
	}
	pmysql_free_result($result);
}

$_MiniLib->getCode($tgbn,$code);
$_MiniLib->getThemecode($tgbn,$code);

?>

<HTML>
<HEAD>
<TITLE>미니샵 미리보기</TITLE>
<META http-equiv="CONTENT-TYPE" content="text/html; charset=EUC-KR">
<script type="text/javascript" src="<?=$Dir?>lib/lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.js.php"></script>
<?include($Dir."lib/style.php")?>
<SCRIPT LANGUAGE="JavaScript">
<!--
function GoItem(a) {}
function GoSection(a,b,c) {}
function GoNoticeList(a) {}
function GoNoticeView(a,b) {}
//-->
</SCRIPT>
</HEAD>

<body topmargin=0 leftmargin=0 rightmargin=0 marginheight=0 marginwidth=0>

<?
$prdataA=$_MiniLib->prdataA;
$prdataB=$_MiniLib->prdataB;
$themeprdataA=$_MiniLib->themeprdataA;
$themeprdataB=$_MiniLib->themeprdataB;
?>
<table border=0 width="<?=$_minidata->shop_width?>" cellpadding=0 cellspacing=0 style="table-layout:fixed">
<tr>
	<td>
	<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
	<col width=200></col>
	<col width=></col>
	<!-- 상단 타이틀부분 들어가는 곳 -->
	<tr height=130>
		<td background="<?=$_minidata->top_backimg?>" style="background-repeat:no-repeat;background-position:left top">
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td height=120 style="padding:5,0,5,5">
			<table border=0 cellpadding=0 cellspacing=0 width=170 style="table-layout:fixed">
			<tr>
				<td width=195 height=120 bgcolor=#ffffff>
				<table width=100% height=100% border=0 cellspacing=0 cellpadding=0 style="table-layout:fixed">
				<tr>
					<td height=88 align=center valign=middle><img src="<?=$_minidata->logo?>" width=185 height=80 border=0></td>
				</tr>
				<tr>
					<td height=40 align=center valign=top style="padding-top:3px"><img src="<?=$Dir?>images/minishop/dangol.gif" border=0></td>
				</tr>
				</table>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>

		<td valign=top background="<?=$_minidata->top_backimg?>" style="background-repeat:no-repeat;background-position:right top">
		<table border=0 cellpadding=0 cellspacing=0 width=100%>
		<tr>
			<td valign=top>
			<table border=0 cellpadding=0 cellspacing=0 width=430>
			<tr>
				<td style="color:#<?=$_minidata->top_fontcolor?>;padding:20,0,0,30">
				<FONT style="font-size:18"><B><?=$_minidata->brand_name?></B></font> <B>/</B> <?=$_minidata->prdt_cnt?>개 상품 <B>/</B> <?=$_minidata->id?></FONT>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			<tr>
				<td style="color:#<?=$_minidata->top_fontcolor?>;padding:0,0,0,30">
				<?=$_minidata->brand_description?>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr><td height=3></td></tr>
<tr>
	<td width=100% valign=top>
	<table border=0 cellpadding=0 cellspacing=0 width=100% height=100%>
	<tr>
		<td width=200 valign=top nowrap>

		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout" border=0>
		<tr>
			<td>
			<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
			<tr>
				<td height=97 valign=top background="<?=$Dir?>images/minishop/search_skin/<?=$_minidata->color?>_search.gif" style=padding:0,7;padding-top:34>
				<table width=100% border=0 cellspacing=5 cellpadding=0 style="table-layout:fixed">
				<form name="MinishopSearchForm">
				<input type="hidden" name="sellvidx" value="">
				<tr>
					<td>
					<select name="search_gbn" style=width:100%>
					<option value="store">이 미니샵 상품</option>
					<option value="all">쇼핑몰 전체 상품</option>
					</select>
					</td>
				</tr>
				<tr>
					<td><input type=text name="search" size=16 value=""> <img src="<?=$Dir?>images/minishop/btn_search.gif" border=0 align=absmiddle style="cursor:hand"></td>
				</tr>
				</form>
				</table>
				</td>
			</tr>
			</table>

			</td>
		</tr>
		<tr><td height=5></td></tr>
<?
		if(count($prdataA)>0) {
?>
		<tr>
			<td align=center  bgcolor="<?=$_minidata->color?>">
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr height=5><td></td></tr>
			<tr height=25>
				<td>&nbsp;&nbsp;<FONT COLOR="<?=$_minidata->fontcolor?>"><B><?=$_minidata->brand_name?> 카테고리</B></FONT></td>
			</tr>
			<tr>
				<td align=center>
				<table width=190 cellspacing=0 cellpadding=0 border=0 bgcolor=ffffff>
				<tr>
					<td style='padding:10'>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
					for($i=0;$i<count($prdataA);$i++) {
						$tmpcode=$prdataA[$i]->code_a."000";
						if($i>0) echo "<tr><td height=10></td></tr>\n";
						echo "<tr>\n";
						echo "	<td><img src=\"".$Dir."images/minishop/icon_catedot.gif\" border=0> ";
						if($tgbn!="10" || $code!=$tmpcode) {
							echo "<A HREF=\"javascript:GoSection('".$_minidata->vender."','10','".$tmpcode."')\"><B>".$prdataA[$i]->code_name."</B></A>";
						} else {
							echo "<FONT style=\"text-decoration: underline;\"><B>".$prdataA[$i]->code_name."</B></font>";
						}
						echo "	</td>\n";
						echo "</tr>\n";
						$strprdata='';
						$_[] = array();
						foreach($prdataB[$prdataA[$i]->code_a] as $code) {
							$tmpcode=$code->code_a.$code->code_b;
							if($tgbn!="10" || $code!=$tmpcode) {
								$_[]="<A HREF=\"javascript:GoSection('".$_minidata->vender."','10','".$tmpcode."')\">".$prdataB[$prdataA[$i]->code_a][$j]->code_name."</A>";
							} else {
								$_[]="<FONT style=\"text-decoration: underline;\">".$prdataB[$prdataA[$i]->code_a][$j]->code_name."</FONT>";
							}
						}
						$strprdata = implode(" | ",$_);
						if(strlen($strprdata)>0) {
							echo "<tr>\n";
							echo "	<td style=\"padding:5,0,0,15\">".$strprdata."</td>\n";
							echo "</tr>\n";
						}
					}
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			</table>
			</td>
		</tr>
		<tr><td height=5></td></tr>
<?
		}

		if(count($themeprdataA)>0) {
?>
		<tr>
			<td align=center  bgcolor="<?=$_minidata->color?>">
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr height=5><td></td></tr>
			<tr height=25>
				<td>&nbsp;&nbsp;<FONT COLOR="<?=$_minidata->fontcolor?>"><B>테마 카테고리</B></FONT></td>
			</tr>
			<tr>
				<td align=center>
				<table width=190 cellspacing=0 cellpadding=0 border=0 bgcolor=ffffff>
				<tr>
					<td style='padding:10'>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
					for($i=0;$i<count($themeprdataA);$i++) {
						$tmpcode=$themeprdataA[$i]->code_a."000";
						if($i>0) echo "<tr><td height=10></td></tr>\n";
						echo "<tr>\n";
						echo "	<td><img src=\"".$Dir."images/minishop/icon_catedot.gif\" border=0> ";
						if($tgbn!="20" || $code!=$tmpcode) {
							echo "<A HREF=\"javascript:GoSection('".$_minidata->vender."','20','".$tmpcode."')\"><B>".$themeprdataA[$i]->code_name."</B></A>";
						} else {
							echo "<FONT style=\"text-decoration: underline;\"><B>".$themeprdataA[$i]->code_name."</B></font>";
						}
						echo "	</td>\n";
						echo "</tr>\n";
						$strprdata='';
						$_=array();
						foreach($themeprdataB[$themeprdataA[$i]->code_a] as $code) {
							$tmpcode=$code->code_a.$code->code_b;
							if($tgbn!="20" || $code!=$tmpcode) {
								$[]="<A HREF=\"javascript:GoSection('".$_minidata->vender."','20','".$tmpcode."')\">".$themeprdataB[$themeprdataA[$i]->code_a][$j]->code_name."</A>";
							} else {
								$[]="<FONT style=\"text-decoration: underline;\">".$themeprdataB[$themeprdataA[$i]->code_a][$j]->code_name."</FONT>";
							}
						}
						$strprdata.=implode(" | ",$_);
						if(strlen($strprdata)>0) {
							echo "<tr>\n";
							echo "	<td style=\"padding:5,0,0,15\">".$strprdata."</td>\n";
							echo "</tr>\n";
						}
					}
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			</table>
			</td>
		</tr>
		<tr><td height=5></td></tr>

		<?}?>

		<tr>
			<td align=center  bgcolor="<?=$_minidata->color?>">
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr height=5><td></td></tr>
			<tr>
				<td align=center>
				<table width=190 cellspacing=0 cellpadding=0 border=0 bgcolor=ffffff>
				<tr>
					<td style='padding-top:5'>
					<table border=0 cellpadding=0 cellspacing=0 width=100% background="<?=$Dir?>images/minishop/menu_linebg.gif">
					<tr height=30>
						<td style="padding-left:10">
						<img src="<?=$Dir?>images/minishop/menu_notice.gif" border=0>
						</td>
						<td align=right style="padding-right:5">
						<A HREF="javascript:GoNoticeList('<?=$_minidata->vender?>')"><img src="<?=$Dir?>images/minishop/btn_more.gif" border=0></A>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:10'>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
<?
					$sql = "SELECT date,subject FROM tblvendernotice WHERE vender='".$_minidata->vender."' ";
					$sql.= "ORDER BY date DESC LIMIT 5 ";
					$result=pmysql_query($sql,get_db_conn());
					while($row=pmysql_fetch_object($result)) {
						echo "<tr><td><span style=word-break:break-all;height:16;overflow:hidden;><A HREF=\"javascript:GoNoticeView('".$_minidata->vender."','".$row->date."')\"><B>·</B> ".titleCut(23,strip_tags($row->subject))."</A></span></td></tr>\n";
						echo "<tr><td height=3></td></tr>\n";
					}
					pmysql_free_result($result);
?>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			</table>
			</td>
		</tr>
		<tr><td height=5></td></tr>

		<tr>
			<td align=center  bgcolor="<?=$_minidata->color?>">
			<table width=100% cellspacing=0 cellpadding=0 border=0>
			<tr height=5><td></td></tr>
			<tr>
				<td align=center>
				<table width=190 cellspacing=0 cellpadding=0 border=0 bgcolor=ffffff>
				<tr>
					<td style='padding-top:5'>
					<table border=0 cellpadding=0 cellspacing=0 width=100% background="<?=$Dir?>images/minishop/menu_linebg.gif">
					<tr height=30>
						<td style="padding-left:10">
						<img src="<?=$Dir?>images/minishop/menu_cust.gif" border=0>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				<tr>
					<td style='padding:10'>

					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr> 
						<td height=18><B>문의전화</B></td>
					</tr>
					<tr><td height=3></td></tr>
					<tr> 
						<td height=17><img src="<?=$Dir?>images/minishop/icon_phone01.gif" border=0 align=absmiddle width="13" height="14"> <?=$_minidata->custdata["TEL"]?></td>
					</tr>
					<tr> 
						<td height=17><img src="<?=$Dir?>images/minishop/icon_fax01.gif" border=0 align=absmiddle width="13" height="14"> <?=$_minidata->custdata["FAX"]?></td>
					</tr>
					<tr> 
						<td height=17 style="word-break:break-all"><img src="<?=$Dir?>images/minishop/icon_email01.gif" border=0 align=absmiddle width="13" height="14"> <?=$_minidata->custdata["EMAIL"]?></td>
					</tr>
					</table>
					<table width=100% border=0 cellspacing=0 cellpadding=0>
					<tr> 
						<td height=10></td>
					</tr>
					<tr> 
						<td><B>고객상담시간</B></td>
					</tr>
					<tr><td height=3></td></tr>
					<tr> 
						<td>평일 : <?=$_minidata->custdata["TIME1"]?></td>
					</tr>
					<tr> 
						<td>토요일 : <?=$_minidata->custdata["TIME2"]?></td>
					</tr>
					<tr> 
						<td>일ㆍ공휴일 : <?=$_minidata->custdata["TIME3"]?></td>
					</tr>
					</table>

					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr><td height=5></td></tr>
			</table>
			</td>
		</tr>
		<tr><td height=5></td></tr>

		</table>

		</td>
		<td width="<?=($_minidata->shop_width-200)?>" align=center valign=top nowrap>

		<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
		<tr>
			<td align=center style="padding:5">

			<!-- 메인화면 상단 자유디자인 -->
<?
			if($_minidata->main_toptype=="image") {
				if(file_exists($top_eventimagepath)) {
					echo "<table width=100% border=0 cellpadding=0 cellspacing=0>\n";
					echo "<tr>\n";
					echo "	<td align=center><img src=\"".$top_eventimageurl."\" border=0 align=absmiddle></td>\n";
					echo "</tr>\n";
					echo "<tr><td height=5></td></tr>\n";
					echo "</table>\n";
				}
			} else if($_minidata->main_toptype=="html") {
				if(strlen($_minidata->main_topdesign)>0) {
					echo "<table width=100% border=0 cellpadding=0 cellspacing=0>\n";
					echo "<tr>\n";
					echo "	<td align=center>";
					if (strpos(strtolower($_minidata->main_topdesign),"<table")!==false)
						echo $_minidata->main_topdesign;
					else
						echo nl2br($_minidata->main_topdesign);
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr><td height=5></td></tr>\n";
					echo "</table>\n";
				}
			}
?>

			<!-- HOT 추천상품 -->
<?
			if($_minidata->hot_used=="1") {
				$hot_disptype='';
				$hot_dispcnt=0;
				$hot_prcode='';
				$specialprlist=array();
				$sql = "SELECT disptype, dispcnt FROM tblvendersectdisplist WHERE seq='".$_minidata->hot_dispseq."' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$hot_disptype=$row->disptype;
					$hot_dispcnt=$row->dispcnt;
				}
				pmysql_free_result($result);
				if(strlen($hot_disptype)>0 && $hot_dispcnt>0) {
					$sql = "SELECT productcode,productname,sellprice,quantity,consumerprice,reserve,reservetype,production, ";
					$sql.= "option_price, tag, minimage, tinyimage, etctype, option_price FROM tblproduct WHERE 1=1 ";
					if($_minidata->hot_linktype=="2") {
						$sql2 = "SELECT special_list FROM ";
						if($preview_type=="code_topevent") {
							$sql2.= "tblvenderspecialcode ";
						} else {
							$sql2.= "tblvenderspecialmain ";
						}
						$sql2.= "WHERE vender='".$_minidata->vender."' ";
						if($preview_type=="code_topevent") {
							$sql2.= "AND code='".substr($code,0,3)."' AND tgbn='".$tgbn."' ";
						}
						$sql2.= "AND special='3' ";
						$result2=pmysql_query($sql2,get_db_conn());
						if($row2=pmysql_fetch_object($result2)) {
							$hot_prcode=str_replace(',',"','",$row2->special_list);
						}
						pmysql_free_result($result2);
						if(strlen($hot_prcode)>0) {
							$sql.= "AND productcode IN ('".$hot_prcode."') ";
						} else {
							$isnot_hotspecial=true;
						}
					} else if($preview_type=="code_topevent") {
						$sql.= "AND productcode LIKE '".substr($code,0,3)."%' ";
					}
					$sql.= "AND vender='".$_minidata->vender."' AND display='Y' ";
					if($_minidata->hot_linktype=="1" || $isnot_hotspecial) {
						$sql.= "ORDER BY sellcount DESC ";
					} else if($_minidata->hot_linktype=="2") {
						$sql.= "ORDER BY FIELD(productcode,'".$hot_prcode."') ";
					}
					$sql.= "LIMIT ".$hot_dispcnt." ";
					$result=pmysql_query($sql,get_db_conn());
					$yy=1;
					while($row=pmysql_fetch_object($result)) {
						$specialprlist[$yy]=$row;
						$yy++;
					}
					pmysql_free_result($result);
				}
				if(count($specialprlist)>0) {
					echo "<table width=100% border=0 cellspacing=0 cellpadding=0>\n";
					echo "<tr>\n";
					echo "	<td bgcolor=\"#ffffff\" style=\"padding-left:10\" height=\"25\"><img src=\"".$Dir."images/minishop/title_hot.gif\" border=0></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td height=10></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td valign=top>\n";
					include ($Dir.TempletDir."minisect/".$hot_disptype.".php");
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td height=15></td>\n";
					echo "</tr>\n";
					echo "</table>\n";
				}
			}
?>

			<!-- NEW 신상품 -->
<?
			if($_minidata->new_used=="1") {
				$new_disptype='';
				$new_dispcnt=0;
				$specialprlist=array();
				$sql = "SELECT disptype, dispcnt FROM tblvendersectdisplist WHERE seq='".$_minidata->new_dispseq."' ";
				$result=pmysql_query($sql,get_db_conn());
				if($row=pmysql_fetch_object($result)) {
					$new_disptype=$row->disptype;
					$new_dispcnt=$row->dispcnt;
				}
				pmysql_free_result($result);
				if(strlen($new_disptype)>0 && $new_dispcnt>0) {
					$sql = "SELECT productcode,productname,sellprice,quantity,consumerprice,reserve,reservetype,production, ";
					$sql.= "option_price, tag, minimage, tinyimage, etctype, option_price FROM tblproduct ";
					$sql.= "WHERE 1=1 ";
					if($preview_type=="code_topevent") {
						$sql.= "AND productcode LIKE '".substr($code,0,3)."%' ";
					}
					$sql.= "AND vender='".$_minidata->vender."' AND display='Y' ";
					$sql.= "ORDER BY regdate DESC ";
					$sql.= "LIMIT ".$new_dispcnt." ";
					$result=pmysql_query($sql,get_db_conn());
					$yy=1;
					while($row=pmysql_fetch_object($result)) {
						$specialprlist[$yy]=$row;
						$yy++;
					}
					pmysql_free_result($result);
				}
				if(count($specialprlist)>0) {
					echo "<table width=100% border=0 cellspacing=0 cellpadding=0>\n";
					echo "<tr>\n";
					echo "	<td bgcolor=\"#ffffff\" style=\"padding-left:10\" height=\"25\"><img src=\"".$Dir."images/minishop/title_new.gif\" border=0></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td height=10></td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td valign=top>\n";
					include ($Dir.TempletDir."minisect/".$new_disptype.".php");
					echo "	</td>\n";
					echo "</tr>\n";
					echo "<tr>\n";
					echo "	<td height=15></td>\n";
					echo "</tr>\n";
					echo "</table>\n";
				}
			}
?>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	</table>

	</td>
</tr>
</table>

</BODY>
</HTML>
