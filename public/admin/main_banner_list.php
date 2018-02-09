<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-2";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include("header.php");


# POST 기본값 세팅
$menu_type = $_POST['menu_type'];
$mode = $_POST['mode'];
//배너 타입
$in_type = $_POST['in_type'];
$v_in_type = $_POST['v_in_type'];
//배너 이미지(PC)
$v_banner_img = $_POST['v_banner_img'];
//배너 이미지(MOBILE)
$v_banner_img_m = $_POST['v_banner_img_m'];
//배너 노출순서
$banner_sort=$_POST["banner_sort"];
//배너 링크
$banner_link=$_POST["banner_link"];
$banner_link_m=$_POST["banner_link_m"];
//배너 타이틀
$banner_title=$_POST["banner_title"];
//배너 노출
$banner_hidden=$_POST["banner_hidden"];
if( $banner_hidden == '' || is_null( $banner_hidden ) ){
	$banner_hidden = '0';
}
//배너 타이틀 링크
$banner_t_link=$_POST["banner_t_link"];
# 노출type 추가
$banner_type = $_POST['banner_type'];
# open 위치 추가
$banner_target = $_POST['banner_target'];
# 카테고리 추가
$code_a = $_POST['code_a'];
$code_b = $_POST['code_b'];
$code_c = $_POST['code_c'];
$code_d = $_POST['code_d'];
if( $code_a ){
	if( $code_b=="" || is_null( $code_b ) ) $code_b = '000';
	if( $code_c=="" || is_null( $code_c ) ) $code_c = '000';
	if( $code_d=="" || is_null( $code_d ) ) $code_d = '000';
}
$cate_number = $code_a.$code_b.$code_c.$code_d;
# 이미지 경로
$imagepath = $Dir.DataDir."shopimages/mainbanner/";

#배너 입력 수정 삭제
// 이미지 파일
$banner_file = new FILE($imagepath);
// 배너 어드민 정보
if( strlen( $mode ) > 0 && ( strlen( $in_type ) > 0 || strlen( $v_in_type ) > 0 )){
	if( strlen( $in_type ) > 0 ){
		$bAdminNo = $in_type;
	} else {
		$bAdminNo = $v_in_type;
	}
	$bAdminSql = "SELECT mb.no, mb.title, mb.img_number, ";
	$bAdminSql.= "mb.titlename, mb.cate_use_yn, mbi.banner_cnt, mbi.banner_maxnum ";
	$bAdminSql.= "FROM tblmainbanner mb ";
	$bAdminSql.= "LEFT JOIN (SELECT banner_no, COUNT( banner_number ) as banner_cnt, MAX( banner_number ) as banner_maxnum FROM tblmainbannerimg WHERE banner_no = ".$bAdminNo." GROUP BY banner_no ) mbi ";
	$bAdminSql.= "ON mbi.banner_no = mb.no ";
	$bAdminSql.= "WHERE no = '".$bAdminNo."' ";
	$bAdminRes = pmysql_query( $bAdminSql, get_db_conn() );
	$bAdminRow = pmysql_fetch_object( $bAdminRes);
	if( $bAdminRow->banner_maxnum == '' || is_null( $bAdminRow->banner_maxnum ) ) {
		$bAdminRow->banner_maxnum = 0;
	}
	$bAdmin = $bAdminRow;
	pmysql_free_result( $bAdminRes );
}
if($mode=="delete") {
	# 배너 넘버
	$img_no = $_POST['img_no'];
	if( strlen( $v_banner_img ) > 0 && is_file( $imagepath.$v_banner_img ) ){
		$banner_file->removeFile( $v_banner_img );
	}
	if( strlen( $v_banner_img_m ) > 0 && is_file( $imagepath.$v_banner_img_m ) ){
		$banner_file->removeFile( $v_banner_img_m );
	}
	$qry = "DELETE FROM tblmainbannerimg WHERE no='".$img_no."' ";
	pmysql_query( $qry, get_db_conn() );
	if( !pmysql_error() ){
			$admUpDate = "UPDATE tblmainbanner SET img_number = img_number - 1 WHERE no='".$bAdmin->no."' ";
			pmysql_query( $admUpDate, get_db_conn() );
	}
	$qry = '';
	//$onload="<script>alert(\"삭제가 완료되었습니다.\");</script>";
	alert_go('삭제가 완료되었습니다.', $_SERVER['PHP_SELF']);

} else if($mode=="modify") {
	# 배너 넘버
	$img_no = $_POST['img_no'];
	$banner_img=$banner_file->upFiles();
	$where="";

	if( $bAdmin->cate_use_yn == 'Y' ){
		$where[]="banner_category='".$cate_number."'";
	}	

	if( strlen( $banner_img["banner_img"][0]["v_file"] ) > 0 ){
		if( is_file( $imagepath.$v_banner_img ) > 0 ){
			$banner_file->removeFile( $v_banner_img );
		}
		$where[]="banner_img='".$banner_img["banner_img"][0]["v_file"]."'";
	}

	if( strlen( $banner_img["banner_img"][1]["v_file"] ) > 0 ){
		if( is_file( $imagepath.$v_banner_img_m ) > 0 ){
			$banner_file->removeFile( $v_banner_img_m );
		}
		$where[]="banner_img_m='".$banner_img["banner_img"][1]["v_file"]."'";
	}
	
	$where[]="banner_sort='".$banner_sort."'";
	$where[]="banner_title='".$banner_title."'";
	$where[]="banner_link='".$banner_link."'";
	$where[]="banner_link_m='".$banner_link_m."'";
	$where[]="banner_hidden='".$banner_hidden."'";
	$where[]="banner_t_link='".$banner_t_link."'";
	$where[]="banner_type='".$banner_type."'";
	$where[]="banner_target='".$banner_target."'";
	
	$qry="UPDATE tblmainbannerimg SET ";
	$qry.=implode(", ",$where);
	$qry.=" WHERE no='".$img_no."' ";
	pmysql_query( $qry, get_db_conn() );
	
	$qry = '';
	//$onload="<script>alert(\"수정이 완료되었습니다.\");</script>";
	alert_go('수정이 완료되었습니다.', $_SERVER['PHP_SELF']);
} else if( $mode == 'insert' ){
	$banner_img=$banner_file->upFiles();
	exdebug( $banner_img );
	if( $bAdmin->cate_use_yn == 'N' ){
		$cate_number = '';
	}
	$qry="insert into tblmainbannerimg (
		banner_no, 
		banner_img, 
		banner_img_m, 
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
		banner_type,
		banner_target
		)values(
		'".$in_type."',
		'".$banner_img["banner_img"][0]["v_file"]."',
		'".$banner_img["banner_img"][1]["v_file"]."',
		'".$banner_sort."',
		'now()',
		'".$banner_title."',
		'".$banner_link."',
		'".$banner_link_m."',
		'".$banner_hidden."',
		'".( $bAdmin->banner_maxnum + 1 )."',
		'".$bAdmin->title."',
		'".$cate_number."',
		'".$banner_t_link."',
		'".$banner_type."',
		'".$banner_target."'
	)";
	pmysql_query( $qry, get_db_conn() );
	if( !pmysql_error() ){
		$admUpDate = "UPDATE tblmainbanner SET img_number = img_number + 1 WHERE no='".$bAdmin->no."' ";
		pmysql_query( $admUpDate, get_db_conn() );
	}
	$qry = '';
	$onload="<script>alert(\"등록이 완료되었습니다.\");</script>";
	alert_go('등록이 완료되었습니다.', $_SERVER['PHP_SELF']);
}


#배너 기본 세팅
$display['0'] = '비노출';
$display['1'] = '노출';
$menuTypeArr['0'] = '전체';
$menuTypeArr['1'] = '교육몰';
$menuTypeArr['2'] = '기업몰';
$prCateSql = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d AS cate_code, code_name FROM tblproductcode ORDER BY cate_code ASC ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prCate[ $prRow['cate_code'] ] = $prRow;
}
pmysql_free_result( $prCateRes );

# 배너 메뉴 불러오기

$menuRes = pmysql_query( "SELECT no, title, titlename, sort, cate_use_yn FROM tblmainbanner ORDER BY sort ", get_db_conn() );
while( $menuRow = pmysql_fetch_array( $menuRes) ){
	$menuCate[] = $menuRow;
	$menuArr[$menuRow['no']] = $menuRow['titlename'];
}
pmysql_free_result( $menuRes );

# 메뉴타입별 불러오기
if( !is_null($menu_type) && $menu_type!='' && strlen( $menu_type ) > 0 ){
	$bannerQry = " AND banner_no = '".$menu_type."'";
}

# 배너 페이징
$page_sql = "SELECT COUNT(*) FROM tblmainbannerimg WHERE 1=1 {$bannerQry} ";
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

# 배너 리스트 불러오기

$bannerSql = "SELECT * FROM tblmainbannerimg WHERE 1=1 ";
$bannerSql.= $bannerQry;
$bannerSql.= "ORDER BY banner_no, banner_sort ";
$sql = $paging->getSql( $bannerSql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$bannerList[] = $row;
}


#---------------------------------------------------------------
# 카테고리 리스트 script 작성
#---------------------------------------------------------------

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY sequence DESC ";
$i=0;
$ii=0;
$iii=0;
$iiii=0;
$strcodelist = "";
$strcodelist.= "<script>\n";
$result = pmysql_query($sql,get_db_conn());
$selcode_name="";

while($row=pmysql_fetch_object($result)) {
	$strcodelist.= "var clist=new CodeList();\n";
	$strcodelist.= "clist.code_a='{$row->code_a}';\n";
	$strcodelist.= "clist.code_b='{$row->code_b}';\n";
	$strcodelist.= "clist.code_c='{$row->code_c}';\n";
	$strcodelist.= "clist.code_d='{$row->code_d}';\n";
	$strcodelist.= "clist.type='{$row->type}';\n";
	$strcodelist.= "clist.code_name='{$row->code_name}';\n";
	if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
		$strcodelist.= "lista[{$i}]=clist;\n";
		$i++;
	}
	if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
		if ($row->code_c=="000" && $row->code_d=="000") {
			$strcodelist.= "listb[{$ii}]=clist;\n";
			$ii++;
		} else if ($row->code_d=="000") {
			$strcodelist.= "listc[{$iii}]=clist;\n";
			$iii++;
		} else if ($row->code_d!="000") {
			$strcodelist.= "listd[{$iiii}]=clist;\n";
			$iiii++;
		}
	}
	$strcodelist.= "clist=null;\n\n";
}
pmysql_free_result($result);
//$display_type = "display:none;";
$display_type = '';
$strcodelist.= "CodeInit();\n";
$strcodelist.= "</script>\n";


$codeA_list = "<select name=code_a id=code_a style=\"width:150px; height:150px\" onchange=\"SearchChangeCate(this,1)\" {$disabled} Multiple>\n";
$codeA_list.= "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
$codeA_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다 display:none;
$codeB_list = "<select name=code_b id=code_b style=\"width:150px; height:150px; ".$display_type."\" onchange=\"SearchChangeCate(this,2)\" {$disabled} Multiple>\n";
$codeB_list.= "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
$codeB_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다display:none;
$codeC_list = "<select name=code_c id=code_c style=\"width:150px; height:150px; ".$display_type."\" onchange=\"SearchChangeCate(this,3)\" {$disabled} Multiple>\n";
$codeC_list.= "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
$codeC_list.= "</select>\n";
// 메인 배너롤링은 1차까지만 가져온다display:none;
$codeD_list = "<select name=code_d id=code_d style=\"width:150px; height:150px; display:none;\" {$disabled} Multiple>\n";
$codeD_list.= "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
$codeD_list.= "</select>\n";

$codeSelect = "<span style=\"display:\" name=\"changebutton\"><input type=\"button\" value=\"선택\" style=\"height : 20px;\" onclick=\"javascript:exec_add()\"></span>";


# 수정할 배너 불러오기
if( $mode == 'modfiy_select' ){
	$img_no = $_POST['img_no'];
	$bSelectSql = "SELECT * FROM tblmainbannerimg WHERE no ='".trim($img_no)."' ";
	$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
	$bSelectRow = pmysql_fetch_array( $bSelectRes );
	$mSelect = $bSelectRow;
	pmysql_free_result( $bSelectRes );
	//수정
	$qType = '1';
	if( $mSelect['banner_category'] != '' || is_null( $mSelect['banner_category'] ) ){
		list($code_a,$code_b,$code_c,$code_d) = sscanf($mSelect['banner_category'],'%3s%3s%3s%3s');
		if(strlen($code_a)!=3) $code_a="000";
		if(strlen($code_b)!=3) $code_b="000";
		if(strlen($code_c)!=3) $code_c="000";
		if(strlen($code_d)!=3) $code_d="000";
	}
}

# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
}

# banner target array
$baerrTargetText = array(
	'_blank'=>'새창',
	'_self'=>'현재위치'
);

?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">

function GoPage(block,gotopage) {
	document.insertForm.mode.value = "";
	document.insertForm.block.value = block;
	document.insertForm.gotopage.value = gotopage;
	document.insertForm.submit();
}
// 우측 메뉴 선택
function changeMenu() {
	$("#menu_change").val( $('#menu_type').val() );
	$("#insertForm").submit();
}

//배너 카테고리
function exec_add()
{

	var ret;
	var str = new Array();
	var code_a=document.form1.code_a.value;
	var code_b=document.form1.code_b.value;
	var code_c=document.form1.code_c.value;
	var code_d=document.form1.code_d.value;

	if(!code_a) code_a="000";
	if(!code_b) code_b="000";
	if(!code_c) code_c="000";
	if(!code_d) code_d="000";
	sumcode=code_a+code_b+code_c+code_d;
	$.ajax({
		type: "POST",
		url: "product_register.ajax.php",
		data: "code_a="+code_a+"&code_b="+code_b+"&code_c="+code_c+"&code_d="+code_d
	}).done(function(msg) {
		if(msg=='nocate'){
			alert("상품카테고리 선택이 잘못되었습니다.");

		}else if(msg=='nolowcate'){
			alert("하위카테고리가 존재합니다.");
		}else{
			document.form1.code.value=sumcode;
			var code_a=document.getElementById("code_a");
			var code_b=document.getElementById("code_b");
			var code_c=document.getElementById("code_c");
			var code_d=document.getElementById("code_d");
		}

	});
}
// 수정 / 삭제 
function changeAction( mode , num ){
	//mode 0 -> insert, 1 -> modify, 2 -> modfiy_select, 3 -> delete
	
	if( mode == '0' ){
		if( confirm('등록하시겠습니까?') ){
			if( $('#banner_sort').val() == '' || $('#banner_sort').val() < 0 ){
				alert('노출순서를 입력해야 합니다.');
				return;
			}
			$("#mode").val( 'insert' );
		} else {
			return;
		}
	} else if ( mode == '1' ) {
		if( confirm('수정하시겠습니까?') ){
			if( $('#banner_sort').val() == '' || $('#banner_sort').val() < 0 ){
				alert('노출순서를 입력해야 합니다.');
				return;
			}
			$("#mode").val( 'modify' );
		} else {
			return;
		}
	} else if ( mode == '2' ) {
		$('#img_no').val( num );
		$("#mode").val( 'modfiy_select' );
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			$('#img_no').val( num );
			$("#mode").val( 'delete' );
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}

	if( checkForm() ){
		$("#insertForm").submit();
	}
	
}

// submit 하기전 값을 체크한다
function checkForm( mode ){
	var returnVal = true;
	return returnVal;
}

function typeChange(){
	var type = $("#in_type").val();
	var alt = '';
	$.each( $("#in_type").find('option') , function( index, obj ) {
		if( $(this).val() == type ){
			alt = $(this).attr('alt');
		}
	});

	if( alt == 'Y' ){
		$("#ID_trCate").show();
	} else {
		$("#ID_trCate").hide();
	}

	if( bannerImgCate.indexOf( type ) > -1 ){
		$("#ID_trImg").show();
		$("#ID_trImg_m").show();
		$("#ID_trLink").show();
		$("#ID_trTitle").hide();
		$("#ID_trT_Link").hide();
	} else if( bannerTextCate.indexOf( type ) > -1 ) {
		$("#ID_trTitle").show();
		$("#ID_trT_Link").show();
		$("#ID_trImg").hide();
		$("#ID_trImg_m").hide();
		$("#ID_trLink").hide();
	} else {
		$("#ID_trTitle").hide();
		$("#ID_trT_Link").hide();
		$("#ID_trImg").hide();
		$("#ID_trImg_m").hide();
		$("#ID_trLink").hide();
	}
}

function goList() {
	$('#banner_no').val('');
	$('#img_no').val('');
	$("#insertForm").submit();
}
var bannerImgCate = ['77','78','79','80','87']; // 이미지형 배너
var bannerTextCate = ['85']; // 텍스트형 배너

// 배너 타입에 따른 이벤트 처리
$(document).ready(function(){
	typeChange();

	$("#in_type").on('change', function(){
		typeChange();
	})
});
</SCRIPT>

<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>헤더매뉴 관리</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<form name='insertForm' id='insertForm' method='POST' enctype="multipart/form-data">
		<input type='hidden' name='mode' id='mode' value='' >
		<input type='hidden' name='banner_no' id='banner_no' value='' >
		<input type='hidden' name='img_no' id='img_no' value='<?=$mSelect['no']?>' >
		<input type=hidden name='menu_type' id='menu_change' value='' >
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">		
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">배너 관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>배너를 등록/수정/삭제 처리를 할 수 있습니다.</span></div>
				</td>
            </tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">배너 등록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					
						<col width=140></col>
						<col width=></col>
						<tr>
							<th><span>배너타입</span></th>
							<TD>
								<select id="in_type" name="in_type" <? if( $mSelect['banner_no'] ) { echo 'disabled'; }  ?> >
									<option value="">=====선택해 주세요====</option>
<?php
	foreach( $menuCate as $mKey=>$mVal ){
?>
									<option value="<?=$mVal['no']?>" <? if( $mVal['no'] == $mSelect['banner_no'] ){ echo 'SELECTED'; } ?> alt='<?=$mVal['cate_use_yn']?>' ><?=$mVal['titlename']?></option>
<?php
	}
?>								
								</select>
								<input type='hidden' name='v_in_type' value='<?=$mSelect['banner_no']?>'>
							</TD>
						</tr>
						
						<tr id='ID_trCate' style='display: none;' >
							<th><span>배너 카테고리</span></th>
							<td>
<?php
	//카테고리 SELECT BOX를 불러온다
	echo $codeA_list;
	echo $codeB_list;
	echo $codeC_list;
	echo $codeD_list;
	//카테고리 SELECT 버튼을 불러온다
	//echo $codeSelect;
	//카테고리 스크립트 실행
	echo $strcodelist;
	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";					
?>
							</td>
						</tr>

						<tr id='ID_trTitle' style='display: none;' >
							<th><span>타이틀</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_title' name='banner_title' value="<?=$mSelect['banner_title']?>"></TD>
						</tr>


						<tr id='ID_trImg' style='display: none;' >
							<th><span>배너 이미지(PC)</span></th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="banner_img[]" style="WIDTH: 400px"><br>
								<!--<span class="font_orange">(권장이미지 : )</span>-->
								<input type=hidden name="v_banner_img" value="<?=$mSelect['banner_img']?>" >
								<div style='margin-top:5px' >
<?	if( is_file($imagepath.$mSelect['banner_img']) ){ ?>
									<img src='<?=$imagepath.$mSelect['banner_img']?>' style='max-width: 125px;' />
<?	} ?>
								</div>
							</td>
						</tr>


						<tr id='ID_trImg_m' style='display: none;' >
							<th><span>배너 이미지(MOBILE)</span></th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="banner_img[]" style="WIDTH: 400px"><br>
								<!--<span class="font_orange">(권장이미지 : )</span>-->
								<input type=hidden name="v_banner_img_m" value="<?=$mSelect['banner_img_m']?>" >
								<div style='margin-top:5px' >
<?	if( is_file($imagepath.$mSelect['banner_img_m']) ){ ?>
									<img src='<?=$imagepath.$mSelect['banner_img_m']?>' style='max-width: 125px;' />
<?	} ?>
								</div>
							</td>
						</tr>

						<tr>
							<th><span>위치</span></th>
							<td>
								<select name='banner_target' >
									<option value='_blank' <? if( $mSelect['banner_target'] == '_blank' ) { echo 'SELECTED'; } ?> > 새창 </option>
									<option value='_self' <? if( $mSelect['banner_target'] == '_self' ) { echo 'SELECTED'; } ?> >현재위치</option>
								</select>
							</td>
						</tr>
						<tr id='ID_trT_Link' style='display: none;' >
							<th><span>타이틀 링크</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_t_link' name='banner_t_link' value="<?=$mSelect['banner_t_link']?>" ></TD>
						</tr>
						<tr id='ID_trLink' style='display: none;' >
							<th><span>링크</span></th>
							<TD><INPUT maxLength=80 size=80 id='banner_link' name='banner_link' value="<?=$mSelect['banner_link']?>" ></TD>
						</tr>
						<tr>
							<th><span>노출순서</span></th>
							<TD><INPUT maxLength=10 size=10 id='banner_sort' name='banner_sort' value="<?=$mSelect['banner_sort']?>" ></TD>
						</tr>
						<tr>
							<th><span>노출</span></th>
							<TD><INPUT type='checkbox' id='banner_hidden' name='banner_hidden' value="1" <? if( $mSelect['banner_hidden'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 노출됩니다. </TD>
						</tr>
						<tr>
							<th><span>노출형태</span></th>
							<td>
								<input type='radio' name='banner_type' value='0' <? if( $mSelect['banner_type'] == '0' || $mSelect['banner_type'] == '' || is_null( $mSelect['banner_type'] ) ){ echo "CHECKED"; } ?> > 전체
								<input type='radio' name='banner_type' value='1' <? if( $mSelect['banner_type'] == '1' ){ echo "CHECKED"; } ?> > 교육몰
								<input type='radio' name='banner_type' value='2' <? if( $mSelect['banner_type'] == '2' ){ echo "CHECKED"; } ?> > 기업몰
							</td>
						</tr>
					</table>
					
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="8" align="center">
<?php
	if( $qType == '0' ){
?>
					<a href="javascript:changeAction('<?=$qType?>', '<?=$mSelect['banner_no']?>' );">
						<img src="images/btn_confirm_com.gif">	
					</a>
<?php
	} else {
?>
					<a href="javascript:javascript:changeAction('<?=$qType?>', '<?=$mSelect['banner_no']?>' );">
						<img src="images/btn_edit2.gif">
					</a>
					<a href="javascript:javascript:goList();">
						<img src="img/btn/btn_list.gif" >
					</a>
<?php
	}
?>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">검색된 메뉴목록</div>
				</td>
				
			</tr>
			
			<tr>
				<td>
				<div class="btn_right">
					<select id='menu_type' onchange='javscript:changeMenu();' >
						<option value='' >==== 전체 ====</option>
<?php
	foreach( $menuCate as $mKey=>$mVal ){
?>
						<option value="<?=$mVal['no']?>" <? if( $mVal['no'] == $menu_type ){ echo 'SELECTED'; } ?> ><?=$mVal['titlename']?></option>
<?php
	}
?>
					</select>
				</div>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
						<col width='50'>
						<col width='80'>
						<col width='100'>
						<col width='150'>
						<col width='175'>
						<col width='175'>
						<col width='*'>
						<col width='250'>
						<col width='55'>
						<col width='60'>
						<col width='60'>
						<col width='60'>
						<col width='60'>
				</colgroup>
				<TR>
					<th>번호</th>
					<th>노출형태</th>
					<th>배너타입</th>
					<th>배너 카테고리</th>
					<th>PC 이미지</th>
					<th>MOBILE 이미지</th>
					<th>타이틀</th>
					<th>링크</th>
					<th>노출순서</th>
					<th>위치</th>
					<th>사용</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $bannerList ) > 0 ) {
		$cnt=0;
		foreach( $bannerList as $bCnt=>$bVal ){
			$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
?>
				<TR>
					<!-- 번호 -->
					<td>
						<?=$number?>
					</td>
					<!-- 메뉴타입 -->
					<td>
						<?=$menuTypeArr[$bVal['banner_type']]?>
					</td>
					<!-- 배너타입 -->
					<td>
						<?=$menuArr[$bVal['banner_no']]?>
					</td>
					<!-- 메뉴 카테고리 -->
					<td>
<?php
			if( $bVal['banner_category'] ){
				echo $prCate[$bVal['banner_category']]['code_name'];
				echo '<br>'.$prCate[$bVal['banner_category']]['cate_code'];

			} else {
				echo '-';
			}
?>
					</td>
					<!-- 이미지 -->
					<td>
						<div id='img_display' >
<?php
			if( is_file($imagepath.$bVal['banner_img']) ){
?>
							<img src='<?=$imagepath.$bVal['banner_img']?>' style='max-width : 125px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
					<!-- 이미지 -->
					<td>
						<div id='img_display2' >
<?php
			if( is_file($imagepath.$bVal['banner_img_m']) ){
?>
							<img src='<?=$imagepath.$bVal['banner_img_m']?>' style='max-width : 125px;' >
<?php
			} else {
				echo '-';
			}
?>	
						</div>
					</td>
					<!-- 타이틀 -->
					<td>
<?php
			if( strlen( $bVal['banner_title'] ) > 0 ) echo $bVal['banner_title'];
			else echo '-';
?>
					</td>
					<!-- link -->
					<td>
						<? if ($bVal['banner_link']) echo $bVal['banner_link'];?>
						<? if ($bVal['banner_link'] && $bVal['banner_t_link']) echo "<br>";?>
						<? if ($bVal['banner_t_link']) echo $bVal['banner_t_link'];?>
					</td>
					<!-- 노출 순서 -->
					<td>
						<?=$bVal['banner_sort']?>
					</td>
					<!-- 위치 -->
					<td>
						<?=$baerrTargetText[$bVal['banner_target']]?>
					</td>
					<!-- 노출 / 비노출 -->
					<td>
						<?=$display[$bVal['banner_hidden']]?>
					</td>
					<!-- 수정 -->
					<td>
						<a href="javascript:changeAction( '2' ,'<?=$bVal["no"]?>' );"><img src="images/btn_edit.gif"></a>
					</td>
					<!-- 삭제 -->
					<td>
						<a href="javascript:changeAction('3', '<?=$bVal["no"]?>' );"><img src="images/btn_del.gif"></a>
					</td>
				</TR>
<?php
			$cnt++;
		}
	} else {
?>
				<TR>
					<td colspan='13' > 헤더매뉴가 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $bannerList ) > 0 ) { ?>
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
<?	} ?>
					</div>
				</div>

				</td>
			</tr>
			
			<tr>
				<td height="30">&nbsp;</td>
			</tr>

			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>  정보</span></dt>
							<dd>
							- <b>번호</b> : <span style="letter-spacing:-0.5pt;">.</span><br>
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</td>
		</tr>
		</form>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<?=$onload?>
<?php
include("copyright.php");
