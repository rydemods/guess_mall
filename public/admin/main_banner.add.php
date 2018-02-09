<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/file.class.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/mainbanner/";

$mode=$_POST["mode"];
$code=$_POST["code"]?$_POST["code"]:"main_rolling";
$banner_img=$_POST["banner_img"];
$banner_sort=$_POST["banner_sort"];
$banner_link=$_POST["banner_link"];
$banner_link_m=$_POST["banner_link_m"];
$banner_title=$_POST["banner_title"];
$banner_hidden=$_POST["banner_hidden"];
$banner_t_link=$_POST["banner_t_link"];
# 노출type 추가
$banner_type = $_POST['banner_type'];

list($cate_use) = pmysql_fetch(pmysql_query("SELECT cate_use_yn FROM tblmainbanner WHERE title='{$code}' "));

#####카테고리 설정
if($cate_use=="Y"){
	$code_a=$_POST["code_a"];
	$code_b=$_POST["code_b"];
	$code_c=$_POST["code_c"];
	$code_d=$_POST["code_d"];
	
	if(!$code_a || $code_a=='000'){
		$code_a=$code_b=$code_c=$code_d="";
	}elseif(!$code_b || $code_b=='000'){
		$code_b=$code_c=$code_d="000";
	}elseif(!$code_c || $code_c=='000'){
		$code_c=$code_d="000";
	}elseif(!$code_d || $code_d=='000'){
		$code_d="000";
	}
	$cate_number = $code_a.$code_b.$code_c.$code_d;
}


$add_check_code	= array(
								"main_rolling",
								"promo_banner",
								"main_ad_banner",
								"menu_cate_banner"

);

in_array($code, $add_check_code) ? $addDelMode = "on" : $addDelMode = "";
//텍스트 베너 체크
$add_text_cehck = array(
	'main_text_rolling',
	'v_main_text_rolling'
);
in_array($code, $add_text_cehck) ? $addtextMode = "on" : $addtextMode = "";

$query = "select * from tblmainbanner where title='".$code."'";
$result = pmysql_query($query,get_db_conn());
$row = pmysql_fetch_object($result);
//echo $query;
#$title_name : 배너 타이틀
#$img_title : 배너에 속한 이미지 타이틀 
$title_name[$row->title]=$row->titlename;

$img_title["login_banner"][1]="우측 롤링이미지1";
$img_title["login_banner"][2]="우측 롤링이미지2";
$img_title["login_banner"][3]="우측 롤링이미지3";
$img_title["login_banner"][4]="하단 왼쪽이미지";
$img_title["login_banner"][5]="하단 오른쪽이미지";
$img_title["cate_list_top"] = array(
									1 => "상단 첫번째줄 배너"
									,2 => "상단 두번째줄<br />왼쪽 큰배너"
									,3 => "상단 두번째줄<br />오른쪽 작은배너 1"
									,4 => "상단 두번째줄<br />오른쪽 작은배너 2"
									,5 => "상단 두번째줄<br />오른쪽 작은배너 3"
									,6 => "상단 두번째줄<br />오른쪽 작은배너 4"
								);
/*
$banner_memo["top_banner"]="탑 배너";
$banner_memo["top_rolling"]="탑 롤링";
$banner_memo["maintop_rolling"]="메인상단롤링";
$banner_memo["maintop_banner"]="메인 상단 배너";
$banner_memo["mainmid_banner"]="메인 중단 배너";
$banner_memo["mdnew_banner"]="MDs Pick/New Shoes 배너";
$banner_memo["brand_banner"]="브랜드 배너";
$banner_memo["szone_banner"]="스페셜존 배너";
$banner_memo["right_banner"]="우측 고정바 배너";
$banner_memo["event_banner"]="이벤트 배너";
*/
$banner_memo["mobile_main_rolling"]="640 * 460 비율의 이미지를 넣어야 합니다.";
$banner_file = new FILE($imagepath);

if($mode=="delete") {
	if ($addDelMode == "on") {
		$chk_size	= sizeof($_POST['banner_chk']);
		for($i=0;$i<$chk_size;$i++){
			$select_qry="select * from tblmainbannerimg where no='".$_POST['banner_chk'][$i]."'";
			$select_result=pmysql_query($select_qry);
			$select_num=pmysql_num_rows($select_result);
			$select_data=pmysql_fetch_object($select_result);

			$banner_file->removeFile($select_data->banner_img);

			$del_qry="delete from tblmainbannerimg where no='".$_POST['banner_chk'][$i]."'";
			pmysql_query($del_qry);
		}

		$select_qry2="select * from tblmainbannerimg where banner_no='".$row->no."' order by no asc";
		$select_result2=pmysql_query($select_qry2);
		$select_num2=pmysql_num_rows($select_result2);
		
		for($i=0;$i<$select_num2;$i++){
			$select_data2	= pmysql_fetch_object($select_result2);

			$upqry1="update tblmainbannerimg set banner_number = '".$i."' where no='".$select_data2->no."'";
			pmysql_query($upqry1,get_db_conn());
		}

		$upqry="update tblmainbanner set img_number = '".$select_num2."' where title='".$code."'";
		pmysql_query($upqry,get_db_conn());

		$query	= "select * from tblmainbanner where title='".$code."'";
		$result	= pmysql_query($query,get_db_conn());
		$row		= pmysql_fetch_object($result);

	} else {
		for($i=0;$i<$row->img_number;$i++){
			$select_qry="select * from tblmainbannerimg where banner_no='".$row->no."' and banner_number='".$i."'";
			$select_result=pmysql_query($select_qry);
			$select_num=pmysql_num_rows($select_result);
			$select_data=pmysql_fetch_object($select_result);
		
			$banner_file->removeFile($select_data->banner_img);
		}
		$del_qry="delete from tblmainbannerimg where banner_no='".$row->no."'";
		pmysql_query($del_qry);
	}
	
	$onload="<script>alert(\"{$title_name[$code]} 삭제가 완료되었습니다.\");</script>";
	
} else if($mode=="modify") {

	$banner_img=$banner_file->upFiles();
	
	if ($addDelMode == "on" || $addtextMode == "on" ) {
		if ($_POST['bannerTotalCnt'] > $row->img_number) {
			$upqry="update tblmainbanner set img_number = '".$_POST['bannerTotalCnt']."' where title='".$code."'";
			pmysql_query($upqry,get_db_conn());

			$query	= "select * from tblmainbanner where title='".$code."'";
			$result	= pmysql_query($query,get_db_conn());
			$row		= pmysql_fetch_object($result);
		}
	}
	for($i=0;$i<$row->img_number;$i++){

		$select_qry="select * from tblmainbannerimg where banner_no='".$row->no."' and banner_number='".$i."' and banner_category='".$cate_number."'";
		$select_result=pmysql_query($select_qry);
		$select_num=pmysql_num_rows($select_result);
		$select_data=pmysql_fetch_object($select_result);
		
		$banner_hidden[$i]=$banner_hidden[$i]?$banner_hidden[$i]:"0";
				
		if($select_num){
			$where="";
			if($banner_img["banner_img"][$i]["v_file"]){
				$banner_file->removeFile($select_data->banner_img);
				$where[]="banner_img='".$banner_img["banner_img"][$i]["v_file"]."'";
			}
			
			$where[]="banner_sort='".$banner_sort[$i]."'";
			$where[]="banner_title='".$banner_title[$i]."'";
			$where[]="banner_link='".$banner_link[$i]."'";
			$where[]="banner_link_m='".$banner_link_m[$i]."'";
			$where[]="banner_hidden='".$banner_hidden[$i]."'";
			$where[]="banner_t_link='".$banner_t_link[$i]."'";
			$where[]="banner_type='".$banner_type[$i]."'";
			
			$qry="update tblmainbannerimg set ";
			$qry.=implode(", ",$where);
			$qry.=" where banner_no='".$row->no."' and banner_number='".$i."' and banner_category='".$cate_number."'";

		}else{
			
			$qry="insert into tblmainbannerimg (
			banner_no, 
			banner_img, 
			banner_sort, 
			banner_date, 
			banner_title, 
			banner_link, 
			banner_link_m, 
			banner_hidden,
			banner_number,
			banner_name,
			banner_category,
			banner_t_link,
			banner_type
			)values(
			'".$row->no."',
			'".$banner_img["banner_img"][$i]["v_file"]."',
			'".$banner_sort[$i]."',
			'now()',
			'".$banner_title[$i]."',
			'".$banner_link[$i]."',
			'".$banner_link_m[$i]."',
			'".$banner_hidden[$i]."',
			'".$i."',
			'".$row->title."',
			'".$cate_number."',
			'".$banner_t_link[$i]."',
			'".$banner_type[$i]."'
			)";
		
		}
		pmysql_query($qry,get_db_conn());
		
	}
	
	$onload="<script>alert(\"{$title_name[$code]} 수정이 완료되었습니다.\");</script>";
}

if($title_type=="image") {
	$imgtype="update"; // 이미지 수정모드
} else {
	$imgtype="";
}
echo $onload;
?>

<?php include("header.php"); ?>
<style>
.btn_gray{
	display:inline-block;border:1px solid #d7d7d7;color:#868686;font-size:13px;padding:8px 14px 6px 10px;border-radius:3px;text-align:center;font-weight:bolder;text-decoration: none;
	background: #ffffff; /* Old browsers */
	background: -moz-linear-gradient(top,  #ffffff 0%, #f5f5f5 100%); /* FF3.6+ */
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#ffffff), color-stop(100%,#f5f5f5)); /* Chrome,Safari4+ */
	background: -webkit-linear-gradient(top,  #ffffff 0%,#f5f5f5 100%); /* Chrome10+,Safari5.1+ */
	background: -o-linear-gradient(top,  #ffffff 0%,#f5f5f5 100%); /* Opera 11.10+ */
	background: -ms-linear-gradient(top,  #ffffff 0%,#f5f5f5 100%); /* IE10+ */
	background: linear-gradient(to bottom,  #ffffff 0%,#f5f5f5 100%); /* W3C */
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#f5f5f5',GradientType=0 ); /* IE6-9 */
}
.btn_gray:hover {
	text-decoration: none;
}
.add_banner_btn {
	cursor:pointer;
	margin-right:5px;
}
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<!--
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
-->
<script type="text/javascript">
 function init(){
  var doc= document.getElementById("divName");
  if(doc.offsetHeight!=0){
   pageheight = doc.offsetHeight;
   parent.document.getElementById("ListFrame").height=pageheight+"px";
   }
 }

 window.onload=function(){
  init();
 }
</script>

<SCRIPT LANGUAGE="JavaScript">
<!--

function Save() {
	if(confirm("수정하시겠습니까?")) {
		document.form1.mode.value="modify";
		document.form1.submit();
	}
}

function TitleDelete() {
	var chk_submit	= "Y";
<?if ($addDelMode == 'on') {?>
	var chk	= $('input:checkbox[name="banner_chk[]"]:checked').length;  
	if (chk == 0) {
		alert("선택된 배너가 없습니다.\n최소 한개의 배너를 선택해 주시기 바랍니다. ");
		chk_submit	= "N";
	} else {
		//$("input:checkbox[name='banner_chk[]']:checked").each(function(){
			//alert(this.value); //checked 된 값
		//});		
	}
<?}?>
	if (chk_submit == 'Y') {
		if(confirm("삭제하시겠습니까?")) {
			document.form1.mode.value="delete";
			document.form1.submit();
		}
	}
}

function change_cate(obj,n){
	SearchChangeCate(obj,n)
	document.form1.mode.value="";
	document.form1.submit();
}

function add_banner(){
	// 배너추가 (20150223)
	var code		= $("input[name=code]").val();
	var nexNum	= Number($(".add_banner_btn").attr('alt'));
	var preNum	= nexNum - 1;
	var add_html	= "";
	var chk_rowspan	= 5;
	add_html	+= "<tr class='banner_box"+preNum+"'><th rowspan='"+chk_rowspan+"' style='text-align:center;vertical-align:top; border-top:1px solid #b9b9b9;'>&nbsp;</th></tr>";
	add_html	+= "<TR class='banner_box"+preNum+"'>";
	add_html	+= "	<th style='border-top:1px solid #b9b9b9;' ><span>이미지파일 "+nexNum+"</span></th>";
	add_html	+= "	<TD class='td_con1' style='border-top:1px solid #b9b9b9;' ><INPUT type=file size=38 name=banner_img["+preNum+"] style='width:100%' ></TD>";
	add_html	+= "	<th style='border-top:1px solid #b9b9b9;' ><span>순서</span></th>";
	add_html	+= "	<TD class='td_con1' style='border-top:1px solid #b9b9b9;' ><INPUT type=text size=5 name=banner_sort["+preNum+"] value='"+nexNum+"'></TD>";
	add_html	+= "</TR>";
	add_html	+= "<tr class='banner_box"+preNum+"'>";
	add_html	+= "	<th><span>링크</span></th>";
	add_html	+= "	<TD class='td_con1' colspan=3'>";
	add_html	+= "		<INPUT type=text size=5 name=banner_link["+preNum+"] value='' style='width:100%'>";
	add_html	+= "	</TD>";
	add_html	+= "</tr>";
	add_html	+= "<tr class='banner_box"+preNum+"'>";
	add_html	+= "	<th><span>노출</span></th>";
	add_html	+= "	<TD class='td_con1'><INPUT type=checkbox name=banner_hidden["+preNum+"] value='1'></TD>	";
	add_html	+= "	<th><span>노출 형태</span></th>";
	add_html	+= "	<TD class='td_con1' >";
	add_html	+= "		<input type='radio' name='banner_type[" + preNum + "]' value='0' > 전체";
	add_html	+= "		<input type='radio' name='banner_type[" + preNum + "]' value='1' > 교육몰";
	add_html	+= "		<input type='radio' name='banner_type[" + preNum + "]' value='2' > 기업몰";
	add_html	+= "	</TD>";
	add_html	+= "</tr>";
	add_html	+= "<TR class='banner_box"+preNum+"'>";
	add_html	+= "	<TD colspan=4 align='center' style='border-left:1px solid #b9b9b9;'>";
	add_html	+= "		<img src='images/code_eventnoimg.gif' border=0 align=absmiddle>";
	add_html	+= "	</TD>";
	add_html	+= "</TR>";
	add_html	+= '<tr><td colspan="' + chk_rowspan + '" style="margin-top: 5px; border: 1px solid #b9b9b9;" ></td></tr>';

	$("#bannerList").append(add_html);
	
	$(".add_banner_btn").attr('alt',nexNum + 1);
	$("#bannerTotalCnt").val(nexNum);

	var doc= document.getElementById("divName");
	if(doc.offsetHeight!=0){
		pageheight = doc.offsetHeight;
		parent.document.getElementById("ListFrame").height=pageheight+"px";
	}

}

function add_textBanner(){
	// text 배너추가 (20151023)
	var code	= $("input[name=code]").val();
	var nexNum	= Number($(".add_banner_btn").attr('alt'));
	var preNum	= nexNum - 1;
	var add_html	= "";
	var chk_rowspan	= 5;

	add_html += '<tr><td colspan="4" style="margin-top: 5px; border: 1px solid #b9b9b9;" ></td></tr>'; 
	add_html += '<tr class="banner_box' + preNum + '">';
	add_html += 	'<th><span>타이틀</span></th>';
	add_html += 	'<td class="td_con1">';
	add_html += 		'<input type="text" size="5" name="banner_title[' + preNum + ']" value="" style="width:98%">';
	add_html += 		'</td>';
	add_html += 	'<th><span>노출</span></th>';
	add_html += 	'<td class="td_con1"><input type="checkbox" name="banner_hidden[' + preNum + ']" value="1"></td>';
	add_html += '</tr>';
	add_html += '<tr>';
	add_html += 	'<th><span>타이틀 링크</span></th>';
	add_html += 	'<td class="td_con1"><input type="text" size="5" name="banner_t_link[' + preNum + ']" value="" style="width:98%"></td>';
	add_html += 	'<th><span>순서</span></th>';
	add_html += 	'<td class="td_con1"><input type="text" size="5" name="banner_sort[' + preNum + ']" value="' + nexNum + '"></td>';
	add_html += '</tr>';
	add_html += '<tr>';
	add_html += '	<th><span>노출 형태</span></th>';
	add_html += '	<TD class="td_con1" colspan="3" >';
	add_html += "		<input type='radio' name='banner_type[" + preNum + "]' value='0' > 전체";
	add_html += "		<input type='radio' name='banner_type[" + preNum + "]' value='1' > 교육몰";
	add_html += "		<input type='radio' name='banner_type[" + preNum + "]' value='2' > 기업몰";
	add_html += '	</TD>';
	add_html += '</tr>';

	$("#bannerList").append(add_html);
	
	$(".add_banner_btn").attr('alt',nexNum + 1);
	$("#bannerTotalCnt").val(nexNum);

	var doc= document.getElementById("divName");
	if(doc.offsetHeight!=0){
		pageheight = doc.offsetHeight;
		parent.document.getElementById("ListFrame").height=pageheight+"px";
	}
}
//-->
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">

<div id="divName">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
<table cellpadding="0" cellspacing="0" width="100%">
<input type=hidden name=imgtype value="<?=$imgtype?>">
<input type=hidden name=mode>
<input type=hidden name=code value="<?=$code?>">
<tr>
	<td width="100%">
	<table cellpadding="0" cellspacing="0" width="100%" height="100%">
	<tr>
		<td width="100%" bgcolor="white">
			<div class="title_depth3_sub"><?=$title_name[$code]?></div>
			<IMG SRC="images/line_blue.gif" WIDTH=100% HEIGHT=2 ALT=""></td>
	</tr>
	<tr>
		<td width="100%" height="100%" valign="top" style="border-bottom-width:2px; border-bottom-color:rgb(0,153,204); border-bottom-style:solid;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
            <div class="table_style01">
			<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
            <col width="200" />
            <col width="" />
<?
			
			
if($cate_use=="Y"){
			
?>
			<TR>
				<TD colspan=2 style="border-left:1px solid #b9b9b9;">
				<div class="title_depth3_sub">카테고리 선택</div>
				
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
				<col width="150" />
					<tr>
						<th><span>카테고리</span></th>
						<td>
						<!--
							<select name=code_b style="width:170px;" onchange="javascript:change_cate();">
							<option value="">〓〓 카테고리  〓〓</option>
				<?php
						$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
						$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') AND code_a='004' AND code_b!='000' AND code_c='000' ORDER BY cate_sort ";
						$result = pmysql_query($sql);
						while($cate_data=pmysql_fetch_object($result)){
							$selected[$code_b]="selected";
				?>
							<option value="<?=$cate_data->code_b?>" <?=$selected[$cate_data->code_b]?>><?=$cate_data->code_name?></option>
				
				
				<?		}	?>
							</select>
						-->	


<?php
	$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
	$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
	$i=0;
	$ii=0;
	$iii=0;
	$iiii=0;
	$strcodelist = "";
	$strcodelist.= "<script>\n";
	$result = pmysql_query($sql,get_db_conn());
	$selcode_name="";

	while($row_cate=pmysql_fetch_object($result)) {
		$strcodelist.= "var clist=new CodeList();\n";
		$strcodelist.= "clist.code_a='{$row_cate->code_a}';\n";
		$strcodelist.= "clist.code_b='{$row_cate->code_b}';\n";
		$strcodelist.= "clist.code_c='{$row_cate->code_c}';\n";
		$strcodelist.= "clist.code_d='{$row_cate->code_d}';\n";
		$strcodelist.= "clist.type='{$row_cate->type}';\n";
		$strcodelist.= "clist.code_name='{$row_cate->code_name}';\n";
		if($row_cate->type=="L" || $row_cate->type=="T" || $row_cate->type=="LX" || $row_cate->type=="TX") {
			$strcodelist.= "lista[{$i}]=clist;\n";
			$i++;
		}
		if($row_cate->type=="LM" || $row_cate->type=="TM" || $row_cate->type=="LMX" || $row_cate->type=="TMX") {
			if ($row_cate->code_c=="000" && $row_cate->code_d=="000") {
				$strcodelist.= "listb[{$ii}]=clist;\n";
				$ii++;
			} else if ($row_cate->code_d=="000") {
				$strcodelist.= "listc[{$iii}]=clist;\n";
				$iii++;
			} else if ($row_cate->code_d!="000") {
				$strcodelist.= "listd[{$iiii}]=clist;\n";
				$iiii++;
			}
		}
		$strcodelist.= "clist=null;\n\n";
	}
	pmysql_free_result($result);
	$strcodelist.= "CodeInit();\n";
	$strcodelist.= "</script>\n";

	echo $strcodelist;


	echo "<select name=code_a style=\"width:170px;\" onchange=\"change_cate(this,1)\">\n";
	echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name=code_b style=\"width:170px;\" onchange=\"change_cate(this,2)\">\n";
	echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name=code_c style=\"width:170px;\" onchange=\"change_cate(this,3)\">\n";
	echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name=code_d style=\"width:170px;\">\n";
	echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
?>
							</td>
						</tr>
				</table>
				</td>
			</tr>
<?
}
			
if($row->img_number && ($code!="category" || $cate_number)){

	if ($addDelMode == "on") {
		$chk_col		= "<col width='40' />";
		//$totalCnt	= $row->img_number + 1;
		$totalCnt	= $row->img_number;
	} else {
		$chk_col	= "";
		$totalCnt	= $row->img_number;
	}

?>
			
			<TR>
				<TD colspan=2 style="border-left:1px solid #b9b9b9;">
				<div class="title_depth3_sub">이미지</div>
				<div class="title_depth3_sub"><span><?=$banner_memo[$code]?></span></div>
				<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed" id='bannerList'>
				<input type='hidden' name='bannerTotalCnt' id='bannerTotalCnt' value='<?=$totalCnt?>'>
				<?=$chk_col?>
				<col width="150" />
            	<col width="" />
				<col width="150" />
<?
	$cnt=1;	
	for($i=0;$i<$totalCnt;$i++){
		$img_qry="select * from tblmainbannerimg where banner_no='".$row->no."' and banner_number='".$i."' and banner_category='".$cate_number."'";
		$img_result=pmysql_query($img_qry);
		$img_data=pmysql_fetch_object($img_result);

		
		$checkd[$i]["1"]="checked";
		
		//이미지 타이틀 
		if($img_title[$code][$cnt]){
			$imgtitle = $img_title[$code][$cnt];
		}else{
			$imgtitle = "이미지파일 ".$cnt;
		}
		$code == "top_rolling" ? $chk_rows	= 6 : $chk_rows	= 5;
		$addDelMode == "on" ? $chk_tr	= "<tr class='banner_box".$i."'><th rowspan='".$chk_rows."' style='text-align:center;vertical-align:top;'><br><br><br><input type='checkbox' name='banner_chk[]' value='".$img_data->no."'></th></tr>" : $chk_tr	= "";
		echo $chk_tr;
		if( $code!="main_text_rolling" && $code!="v_main_text_rolling" ) {
?>
				<TR class='banner_box<?=$i?>'>
					<th><span><?=$imgtitle?></span></th>
					<TD class="td_con1"><INPUT type=file size=38 name=banner_img[<?=$i?>] style="width:100%" <?=$disabled?>></TD>
					<th><span>순서</span></th>
					<TD class="td_con1"><INPUT type=text size=5 name=banner_sort[<?=$i?>] value='<?=$img_data->banner_sort?$img_data->banner_sort:$cnt?>'></TD>
				</TR>
				<tr class='banner_box<?=$i?>'>
					<th><span>링크</span></th>
					<TD class="td_con1" colspan=3">
						<INPUT type=text size=5 name=banner_link[<?=$i?>] value="<?=$img_data->banner_link?>"style="width:100%">
					</TD>
				</tr>
<?
		}
		if($code=="top_rolling"){
?>
				<tr class='banner_box<?=$i?>'>
					<th><span>모바일 링크</span></th>
					<TD class="td_con1" colspan="3"><INPUT type=text size=5 name=banner_link_m[<?=$i?>] value="<?=$img_data->banner_link_m?>" style="width:100%"></TD>
				</tr>
<?
		}
?>

				<tr class='banner_box<?=$i?>'>
<?
		if($code=='main_text_rolling' || $code=='v_main_text_rolling' ){
?>
				<tr><td colspan="4" style="margin-top: 5px; border: 1px solid #b9b9b9;" ></td></tr>

				<tr class='banner_box<?=$i?>'>
					<th><span>타이틀</span></th>
					<TD class="td_con1">
						<INPUT type=text size=5 name=banner_title[<?=$i?>] value="<?=$img_data->banner_title?>"style="width:98%" <?if($code=="issue"){echo "readonly";}?>>
					</TD>
					<th><span>노출</span></th>
					<TD class="td_con1" ><INPUT type=checkbox name=banner_hidden[<?=$i?>] value='1' <?=$checkd[$i][$img_data->banner_hidden]?>></TD>
				
				</tr>

				<tr>
					<th><span>타이틀 링크</span></th>
					<TD class="td_con1" ><INPUT type=text size=5 name=banner_t_link[<?=$i?>] value="<?=$img_data->banner_t_link?>"style="width:98%"></TD>
					<th><span>순서</span></th>
					<TD class="td_con1"><INPUT type=text size=5 name=banner_sort[<?=$i?>] value='<?=$img_data->banner_sort?$img_data->banner_sort:$cnt?>'></TD>
				</tr>
				<tr>
					<th><span>노출 형태</span></th>
					<TD class="td_con1" colspan='3' >
						<input type='radio' name='banner_type[<?=$i?>]' value='0' <? if( $img_data->banner_type == '0' || $img_data->banner_type == '' || is_null( $img_data->banner_type ) ){ echo "CHECKED"; } ?> > 전체
						<input type='radio' name='banner_type[<?=$i?>]' value='1' <? if( $img_data->banner_type == '1' ){ echo "CHECKED"; } ?> > 교육몰
						<input type='radio' name='banner_type[<?=$i?>]' value='2' <? if( $img_data->banner_type == '2' ){ echo "CHECKED"; } ?> > 기업몰
					</TD>
				</tr>
<?
		} else {
?>
				<tr class='banner_box<?=$i?>'>
					</TD>
					<th><span>노출</span></th>
					<TD class="td_con1" ><INPUT type=checkbox name=banner_hidden[<?=$i?>] value='1' <?=$checkd[$i][$img_data->banner_hidden]?>></TD>
					<th><span>노출 형태</span></th>
					<TD class="td_con1" >
						<input type='radio' name='banner_type[<?=$i?>]' value='0' <? if( $img_data->banner_type == '0' || $img_data->banner_type == '' || is_null( $img_data->banner_type ) ){ echo "CHECKED"; } ?> > 전체
						<input type='radio' name='banner_type[<?=$i?>]' value='1' <? if( $img_data->banner_type == '1' ){ echo "CHECKED"; } ?> > 교육몰
						<input type='radio' name='banner_type[<?=$i?>]' value='2' <? if( $img_data->banner_type == '2' ){ echo "CHECKED"; } ?> > 기업몰
					</TD>
				</tr>
<?
		}
?>
<?		
		if ($code!="main_text_rolling" && $code!="v_main_text_rolling" ){ 
?>
				<TR class='banner_box<?=$i?>'>
					<TD colspan=4 align="center" style="border-left:1px solid #b9b9b9;">
<?php		
			if($img_data->banner_img) {
?>
						<img src="<?=$imagepath.$img_data->banner_img?>" style='max-width: 450px;' border=0 align=absmiddle>
<?			
			} else {
?>
						<img src="images/code_eventnoimg.gif" border=0 align=absmiddle>
<?
			}
?>
					</TD>
				</TR>
				<tr><td colspan="5" style="margin-top: 5px; border: 1px solid #b9b9b9;" ></td></tr>
<?		
		} 
?>
<?php	if($code == "greeting"){ ?>
				<tr>
						<th><span>태그입력 </span></th>
						<TD class="td_con1" colspan="3"><textarea name="banner_t_link[<?=$i?>]" style="width: 100%;height: 300px;"><?=$img_data->banner_t_link?></textarea></TD>
				</tr>
<?php	} ?>
<?		
		$cnt++;
	}
?>
				</TABLE>				
				
				
				</TD>
			</TR>
<?
}
?>
<?
if ($addDelMode == "on") {
?>			
			<tr><td colspan="2" style="border-left:1px solid #b9b9b9;text-align:right;"><a href="javascript:add_banner();" class="add_banner_btn btn_gray" alt="<?=$row->img_number + 1?>">+ 배너추가</a></td></tr>
<?
}
if ( $code=="main_text_rolling" || $code=='v_main_text_rolling' ) {
?>
			<tr><td colspan="2" style="border-left:1px solid #b9b9b9;text-align:right;"><a href="javascript:add_textBanner();" class="add_banner_btn btn_gray" alt="<?=$row->img_number + 1?>">+ 배너추가</a></td></tr>
<?
}
?>
			</TABLE>
            </div>
			</td>
		</tr>
		<tr>
			<td align=center style="padding-top:2pt; padding-bottom:2pt;" height="22">
<?
if($row->img_number){
?>
				<a href="javascript:Save();"><img src="images/btn_edit2.gif" border="0" hspace="0" vspace="4"></a>
				<a href="javascript:TitleDelete();"><img src="images/btn_del3.gif" border="0" hspace="2" vspace="4"></a>
<?
}
?>
<?
if($row->product_type){
	if($row->title=="new") $pagehref="product_mainlist.php";
	else if($row->title=="issue") $pagehref="product_issuelist.php";
	else if($row->title=="category") $pagehref="product_codelist.php";
?>
				<a href="javascript:parent.location.href='<?=$pagehref?>'"><img src="img/btn/btn_product_reg.gif" border="0" hspace="2" vspace="4"></a>
<?
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
</form>
</div>

