<?php
/********************************************************************* 
// 파 일 명		: design_depth1_tw.php
// 설     명		: 데코앤이 대카테고리 페이지 TODAY/WEEKLY 디자인
// 상세설명	: 데코앤이 대카테고리 페이지의 TODAY/WEEKLY 상품 리스트 관리
// 작 성 자		: 2016.01.18 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	# 파일 클래스 추가
	include_once($Dir."lib/file.class.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "de-2";
	$MenuCode = "design";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

include("header.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	
	$type=$_POST["type"];
	if (!$type) $type=$_GET["type"];
	if (!$type) $type="T";
	if ($type == 'T') {				// TODAY
		$menu_title	= "TODAY 설정";
		$menu_text1	= "월요일";
		$menu_text2	= "화요일";
		$menu_text3	= "수요일";
		$menu_text4	= "목요일";
		$menu_text5	= "금요일";
		$menu_text6	= "토요일";
		$menu_text7	= "일요일";
	} else if ($type == 'W') {		// WEEKLY
		$menu_title	= "WEEKLY 설정";
		$menu_text1	= "BEST 1";
		$menu_text2	= "BEST 2";
		$menu_text3	= "BEST 3";
		$menu_text4	= "BEST 4";
		$menu_text5	= "BEST 5";
		$menu_text6	= "BEST 6";
		$menu_text7	= "BEST 7";
		
	}

	$mode=$_POST["mode"];
	$twno	= $_POST["twno"];
	
	//기본 텍스트
	$title = pg_escape_string($_POST["title"]);
	//관련상품
	$twProduct1 = $_POST['twProduct1'];
	$twProduct2 = $_POST['twProduct2'];
	$twProduct3 = $_POST['twProduct3'];
	$twProduct4 = $_POST['twProduct4'];
	$twProduct5 = $_POST['twProduct5'];
	$twProduct6 = $_POST['twProduct6'];
	$twProduct7 = $_POST['twProduct7'];

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

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------


		
	if($mode=="delete") {
		
		$qry = "DELETE FROM tblproduct_todaynweekly_list WHERE no = '".$twno."' ";
		pmysql_query($qry,get_db_conn());

		if( !pmysql_error() ){
			alert_go('삭제가 완료되었습니다.', $_SERVER['REQUEST_URI']);
		}
		$qry = '';

	} else if($mode=="insert") {
		$qry = "INSERT INTO tblproduct_todaynweekly_list(
		type		,
		cate		,
		title		,
		productcode1		,
		productcode2		,
		productcode3		,
		productcode4		,
		productcode5		,
		productcode6		,
		productcode7) VALUES (
		'{$type}', 
		'{$cate_number}', 
		'{$title}', 
		'".$twProduct1[0]."', 
		'".$twProduct2[0]."', 
		'".$twProduct3[0]."', 
		'".$twProduct4[0]."', 
		'".$twProduct5[0]."', 
		'".$twProduct6[0]."', 
		'".$twProduct7[0]."')";		

		pmysql_query($qry,get_db_conn());
		if(!pmysql_error()){
			// 타이틀을 모두 업데이트를 한다.
			$titleUp_sql	= "UPDATE tblproduct_todaynweekly_list SET title='".$title."' WHERE type='".$type."'";
			pmysql_query($titleUp_sql,get_db_conn());	
			alert_go('등록이 완료되었습니다.', $_SERVER['REQUEST_URI']);
		}else{	
			alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		} 
		$qry = '';
		
	}else if($mode=="modify") {

		$qry = "UPDATE tblproduct_todaynweekly_list SET ";
		$qry.= " type='{$type}', ";
		$qry.= " cate='{$cate_number}', ";
		$qry.= " title='{$title}', ";
		$qry.= " productcode1='".$twProduct1[0]."', ";
		$qry.= " productcode2='".$twProduct2[0]."', ";
		$qry.= " productcode3='".$twProduct3[0]."', ";
		$qry.= " productcode4='".$twProduct4[0]."', ";
		$qry.= " productcode5='".$twProduct5[0]."', ";
		$qry.= " productcode6='".$twProduct6[0]."', ";
		$qry.= " productcode7='".$twProduct7[0]."' ";
		$qry.= "WHERE no='{$twno}' ";

		pmysql_query($qry,get_db_conn());
		if(!pmysql_error()){
			// 타이틀을 모두 업데이트를 한다.
			$titleUp_sql	= "UPDATE tblproduct_todaynweekly_list SET title='".$title."' WHERE type='".$type."'";
			pmysql_query($titleUp_sql,get_db_conn());	
			alert_go('수정이 완료되었습니다.', $_SERVER['REQUEST_URI']);
		}else{	
			alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
		} 
		$qry = '';
	}


#---------------------------------------------------------------
# 카테고리 리스트 script 작성
#---------------------------------------------------------------

$sql = "SELECT code_a, code_b, code_c, code_d, type, code_name FROM tblproductcode WHERE group_code!='NO' ";
$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
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
$display_type = "display:none;";
//$display_type = '';
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


if ($mode=='modify_select') {	
	//수정
	$qType = '1';
	$sql = "SELECT * FROM tblproduct_todaynweekly_list where no='{$twno}' LIMIT 1";
	$result=pmysql_query($sql,get_db_conn());
	$_cdata=pmysql_fetch_object($result);
	if( $_cdata->cate != '' || is_null( $_cdata->cate ) ){
		list($code_a,$code_b,$code_c,$code_d) = sscanf($_cdata->cate,'%3s%3s%3s%3s');
		if(strlen($code_a)!=3) $code_a="000";
		if(strlen($code_b)!=3) $code_b="000";
		if(strlen($code_c)!=3) $code_c="000";
		if(strlen($code_d)!=3) $code_d="000";
	}
	pmysql_free_result($result);
}
# 등록 mode 
if( is_null( $qType ) ){
	$qType = '0';
	$bSelectSql = "SELECT * FROM tblproduct_todaynweekly_list where type = '".$type."' limit 1";
	$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
	$bSelectRow = pmysql_fetch_object( $bSelectRes );
	$_cdata->title	= $bSelectRow->title;
	pmysql_free_result( $bSelectRes );
}

if ($qType == '0') $mode_Text	= "등록";
if ($qType == '1') $mode_Text	= "수정";

$prCateSql = "SELECT code_a, code_b, code_c, code_d, code_a||code_b||code_c||code_d AS cate_code, code_name FROM tblproductcode ORDER BY cate_code ASC ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );

$prCate = array();
$prFirstCate = array();
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prCate[ $prRow['cate_code'] ] = $prRow;

    if ( $prRow['code_b'] == "000" ) {
        $prFirstCate[$prRow['cate_code']] = $prRow['code_name'];
    }
}
pmysql_free_result( $prCateRes );

# 페이징
$page_sql = "SELECT COUNT(*) FROM tblproduct_todaynweekly_list where type = '".$type."' ";
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

# 리스트 불러오기

$twSql = "SELECT * FROM tblproduct_todaynweekly_list where type = '".$type."'  ";
$twSql.= "ORDER BY no ";
$sql = $paging->getSql( $twSql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
    if ( isset($prFirstCate[$row['cate']]) ) {
        unset($prFirstCate[$row['cate']]);
    }

	$twList[] = $row;
}
pmysql_free_result($result);
?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
// 수정 / 삭제 
function changeAction( mode , num ){
	//mode 0 -> insert, 1 -> modify, 2 -> modfiy_select, 3 -> delete

    var cateFreeCount = <?=count($prFirstCate)?>;
	
	if( mode == '0' ){
        if ( cateFreeCount == 0 ) {
            alert('모든 카테고리가 등록되었습니다. 등록이 불가합니다.');
            return;
        }

		if( confirm('등록하시겠습니까?') ){
			$("#mode").val( 'insert' );
		} else {
			return;
		}
	} else if ( mode == '1' ) {

		if( confirm('수정하시겠습니까?') ){
			$("#mode").val( 'modify' );
		} else {
			return;
		}
	} else if ( mode == '2' ) {
		$('#twno').val( num );
		$("#mode").val( 'modify_select' );
	} else if ( mode == '3' ) {
		if( confirm('삭제하시겠습니까?') ){
			$('#twno').val( num );
			$("#mode").val( 'delete' );
		} else {
			return;
		}
	} else {
		alert('잘못된 입력입니다.');
		return;
	}

	if( checkForm() ){
		document.form1.submit();
	}
	
}

// submit 하기전 값을 체크한다
function checkForm( mode ){
	var returnVal = true;
	return returnVal;
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

function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function goList() {
	document.form1.twno.value = "";
	document.form1.submit();
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; 메인 배너관리 &gt;<span><?=$menu_title?>관리</span></p></div></div>
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
			<?php include("menu_design.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<form name=form1 id=form1 method=post enctype="multipart/form-data">
			<input type=hidden name=mode id=mode value='' >
			<input type=hidden name=type value="<?=$type?>">
			<input type=hidden name=twno id=twno value="<?=$_cdata->no?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">		
			<table cellpadding="0" cellspacing="0" width="100%" border="0">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=$menu_title?>관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span><?=$menu_title?> 정보를 변경 할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">검색된 목록</div>
				</td>
			</tr>
			
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
					<col width='50'>
					<col width='150'>
					<col width='*'>
					<col width='60'>
					<col width='60'>
				</colgroup>
				<TR>
					<th>번호</th>
					<th>카테고리</th>				
					<th>타이틀</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $twList ) > 0 ) {
		$cnt=0;
		foreach( $twList as $bCnt=>$bVal ){
			$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
?>
				<TR>
					<!-- 번호 -->
					<td>
						<?=$number?>
					</td>
					<!-- 카테고리 -->
					<td>
<?php
			if( $bVal['cate'] ) echo $prCate[$bVal['cate']]['code_name'];
			else echo '-';
?>
					</td>
					<!-- 타이틀 -->
					<td>
<?php
			if( strlen( $bVal['title'] ) > 0 ) echo $bVal['title'];
			else echo '-';
?>
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
					<td colspan='5' > 목록이 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $twList ) > 0 ) { ?>
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
<?	} ?>
					</div>
				</div>

				</td>
			</tr>
			<!--tr><td height=30></td></tr-->
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><?=$menu_title?>정보 <?=$mode_Text?></div>
				</td>
			</tr>
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<?include("layer_prlistPop.php");?>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>카테고리</span></th>
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
				<tr>
					<th><span>타이틀</span></th>
					<TD><INPUT maxLength=80 size=80 id='title' name='title' value="<?=$_cdata->title?>"></TD>
				</tr>				
				<tr>
					<th><span><?=$menu_text1?></span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','twProduct1');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_twProduct1">	
								<input type="hidden" name="limit_twProduct1" id="limit_twProduct1" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?
									if ($_cdata->productcode1){
										$bSelectSql = "SELECT * FROM tblproduct ";
										$bSelectSql.= "WHERE productcode= '".trim($_cdata->productcode1)."'";
										$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
										$bSelectRow = pmysql_fetch_array( $bSelectRes );

										if(strlen($bSelectRow['tinyimage'])!=0) {
											$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$bSelectRow['tinyimage']);
										}else {
											$imgsrc = $Dir."images/no_img.gif";
										}
								?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 25px; height:25px;" src="<?=$imgsrc?>" border="1"/>
											<input type='hidden' name='twProduct1[]' value='<?=$bSelectRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bSelectRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bSelectRow[productcode]?>','twProduct1');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?
										pmysql_free_result( $bSelectRes );
									}
								?>
								</table>
							</div>
					</td>
				</tr>		
				<tr>
					<th><span><?=$menu_text2?></span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','twProduct2');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_twProduct2">	
								<input type="hidden" name="limit_twProduct2" id="limit_twProduct2" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?
									if ($_cdata->productcode2){
										$bSelectSql = "SELECT * FROM tblproduct ";
										$bSelectSql.= "WHERE productcode= '".trim($_cdata->productcode2)."'";
										$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
										$bSelectRow = pmysql_fetch_array( $bSelectRes );

										if(strlen($bSelectRow['tinyimage'])!=0) {
											$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$bSelectRow['tinyimage']);
										}else {
											$imgsrc = $Dir."images/no_img.gif";
										}
								?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 25px; height:25px;" src="<?=$imgsrc?>" border="1"/>
											<input type='hidden' name='twProduct2[]' value='<?=$bSelectRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bSelectRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bSelectRow[productcode]?>','twProduct2');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?
										pmysql_free_result( $bSelectRes );
									}
								?>
								</table>
							</div>
					</td>
				</tr>	
				<tr>
					<th><span><?=$menu_text3?></span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','twProduct3');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_twProduct3">	
								<input type="hidden" name="limit_twProduct3" id="limit_twProduct3" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?
									if ($_cdata->productcode3){
										$bSelectSql = "SELECT * FROM tblproduct ";
										$bSelectSql.= "WHERE productcode= '".trim($_cdata->productcode3)."'";
										$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
										$bSelectRow = pmysql_fetch_array( $bSelectRes );

										if(strlen($bSelectRow['tinyimage'])!=0) {
											$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$bSelectRow['tinyimage']);
										}else {
											$imgsrc = $Dir."images/no_img.gif";
										}
								?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 25px; height:25px;" src="<?=$imgsrc?>" border="1"/>
											<input type='hidden' name='twProduct3[]' value='<?=$bSelectRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bSelectRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bSelectRow[productcode]?>','twProduct3');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?
										pmysql_free_result( $bSelectRes );
									}
								?>
								</table>
							</div>
					</td>
				</tr>	
				<tr>
					<th><span><?=$menu_text4?></span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','twProduct4');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_twProduct4">	
								<input type="hidden" name="limit_twProduct4" id="limit_twProduct4" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?
									if ($_cdata->productcode4){
										$bSelectSql = "SELECT * FROM tblproduct ";
										$bSelectSql.= "WHERE productcode= '".trim($_cdata->productcode4)."'";
										$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
										$bSelectRow = pmysql_fetch_array( $bSelectRes );

										if(strlen($bSelectRow['tinyimage'])!=0) {
											$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$bSelectRow['tinyimage']);
										}else {
											$imgsrc = $Dir."images/no_img.gif";
										}
								?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 25px; height:25px;" src="<?=$imgsrc?>" border="1"/>
											<input type='hidden' name='twProduct4[]' value='<?=$bSelectRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bSelectRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bSelectRow[productcode]?>','twProduct4');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?
										pmysql_free_result( $bSelectRes );
									}
								?>
								</table>
							</div>
					</td>
				</tr>	
				<tr>
					<th><span><?=$menu_text5?></span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','twProduct5');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_twProduct5">	
								<input type="hidden" name="limit_twProduct5" id="limit_twProduct5" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?
									if ($_cdata->productcode5){
										$bSelectSql = "SELECT * FROM tblproduct ";
										$bSelectSql.= "WHERE productcode= '".trim($_cdata->productcode5)."'";
										$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
										$bSelectRow = pmysql_fetch_array( $bSelectRes );

										if(strlen($bSelectRow['tinyimage'])!=0) {
											$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$bSelectRow['tinyimage']);
										}else {
											$imgsrc = $Dir."images/no_img.gif";
										}
								?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 25px; height:25px;" src="<?=$imgsrc?>" border="1"/>
											<input type='hidden' name='twProduct5[]' value='<?=$bSelectRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bSelectRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bSelectRow[productcode]?>','twProduct5');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?
										pmysql_free_result( $bSelectRes );
									}
								?>
								</table>
							</div>
					</td>
				</tr>	
				<tr>
					<th><span><?=$menu_text6?></span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','twProduct6');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_twProduct6">	
								<input type="hidden" name="limit_twProduct6" id="limit_twProduct6" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?
									if ($_cdata->productcode6){
										$bSelectSql = "SELECT * FROM tblproduct ";
										$bSelectSql.= "WHERE productcode= '".trim($_cdata->productcode6)."'";
										$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
										$bSelectRow = pmysql_fetch_array( $bSelectRes );

										if(strlen($bSelectRow['tinyimage'])!=0) {
											$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$bSelectRow['tinyimage']);
										}else {
											$imgsrc = $Dir."images/no_img.gif";
										}
								?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 25px; height:25px;" src="<?=$imgsrc?>" border="1"/>
											<input type='hidden' name='twProduct6[]' value='<?=$bSelectRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bSelectRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bSelectRow[productcode]?>','twProduct6');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?
										pmysql_free_result( $bSelectRes );
									}
								?>
								</table>
							</div>
					</td>
				</tr>	
				<tr>
					<th><span><?=$menu_text7?></span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','twProduct7');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_twProduct7">	
								<input type="hidden" name="limit_twProduct7" id="limit_twProduct7" value="1"/>								
									<colgroup>
										<col width=50></col>
										<col width=></col>
									</colgroup>
								<?
									if ($_cdata->productcode7){
										$bSelectSql = "SELECT * FROM tblproduct ";
										$bSelectSql.= "WHERE productcode= '".trim($_cdata->productcode7)."'";
										$bSelectRes = pmysql_query( $bSelectSql, get_db_conn() );
										$bSelectRow = pmysql_fetch_array( $bSelectRes );

										if(strlen($bSelectRow['tinyimage'])!=0) {
											$imgsrc = getProductImage($Dir.DataDir.'shopimages/product/',$bSelectRow['tinyimage']);
										}else {
											$imgsrc = $Dir."images/no_img.gif";
										}
								?>	
									<tr align="center">
										<td style='border:0px'>
											<img style="width: 25px; height:25px;" src="<?=$imgsrc?>" border="1"/>
											<input type='hidden' name='twProduct7[]' value='<?=$bSelectRow[productcode]?>'>
										</td>
										<td style='border:0px' align="left"><?=$bSelectRow[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bSelectRow[productcode]?>','twProduct7');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								<?
										pmysql_free_result( $bSelectRes );
									}
								?>
								</table>
							</div>
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>
			<tr><td height=20></td></tr>	
			<tr>
				<td colspan=8 align=center>
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
			<tr><td height=20></td></tr>	
			
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span><?=$menu_title?>관리</span></dt>
							<dd>- <?=$menu_title?> 정보를 변경 할 수 있습니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php 
include("copyright.php");
