<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include("header.php");

//print_r($_POST);

$mode = $_POST['mode'];
$s_keyword = $_POST['s_keyword'];
$sort_opt = $_POST['sort_opt'];
$gotopage = $_POST['gotopage'];
$mall_type = $_POST['mall_type'];

$imagepath = $Dir.DataDir.'shopimages/brand/'; // 브랜드 이미지 파일 위치
$brand_file = new FILE($imagepath); //파일 클래스 사용
// 메뉴 노출
$display = array('0'=>'N','1'=>'Y');
// 몰 타입
$mallTypeArr = array('0'=>'전체','1'=>'교육몰','2'=>'기업몰');
// 노출 카테고리 타입
$cateTypeArr = array('1'=>'디지털/가전','2'=>'패션/잡화/기타');

$qType = 'insert';
# 브랜드 선택, 삭제, 등록, 수정 
if( $mode == 'select' ){
	$bridx = $_POST['bridx'];

	$selectSql = 'SELECT bridx, brandname, logo_img, display_yn, mall_type, category_type FROM tblproductbrand WHERE bridx = \''.$bridx.'\' ';
	$selectRes = pmysql_query( $selectSql, get_db_conn() );
	$selectRow = pmysql_fetch_row( $selectRes );
	$selectBrand = $selectRow;
	pmysql_free_result( $selectRes );
	$qType = 'modify';
} else if ( $mode == 'delete' ){
	$bridx = $_POST['bridx'];

	$imgDelRes = pmysql_query( "SELECT logo_img FROM tblproductbrand WHERE bridx = '".$bridx."'", get_db_conn() );
	$imgDelRow = pmysql_fetch_object( $imgDelRes );
	if( $imgDelRow->logo_img ) {
		$brand_file->removeFile( $imgDelRow->logo_img );
	}
	pmysql_free_result( $imgDelRes );
	$sql = "DELETE FROM tblproductbrand ";
	$sql.= "WHERE bridx = '{$bridx}' ";
	if(pmysql_query($sql,get_db_conn())) {
		$sql = "UPDATE tblproduct ";
		$sql.= "SET brand = null ";
		$sql.= "WHERE brand = '{$bridx}' ";
		pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('브랜드 삭제가 정상 완료되었습니다.');}</script>";
		DeleteCache("tblproductbrand.cache");
	}
} else if( $mode == 'insert' ) {
	$v_brand_name = $_POST['v_brand_name'];
	$v_brand_icon = $_POST['v_brand_icon'];
	$v_brand_display = $_POST['v_brand_display'];
	if( is_null($v_brand_display) || $v_brand_display == '') $v_brand_display = 0;
	$v_mall_type = $_POST['v_mall_type'];
	$v_category_type = $_POST['v_category_type'];

	if(ord($v_brand_name)) {
		$brand_file = $brand_file->upFiles(); // 파일 가져오기
		$sql = "INSERT INTO tblproductbrand( brandname, logo_img, display_yn, mall_type, category_type ) VALUES ('{$v_brand_name}', '".$brand_file['brand_icon'][0]['v_file']."', '{$v_brand_display}', '{$v_mall_type}', '{$v_category_type}')";
		if(pmysql_query($sql,get_db_conn())) {
			$onload="<script>window.onload=function(){ alert('브랜드 등록이 정상 완료되었습니다.');}</script>";
			DeleteCache("tblproductbrand.cache");
		} else {
			alert_go('동일명이 존재합니다. 다른 브랜드명을 입력해 주세요.',-1);
		}
	} else {
		alert_go('추가할 브랜드명을 입력해 주세요.',-1);
	}
} else if( $mode =='modify' ) {
	$bridx = $_POST['bridx'];
	$v_brand_name = $_POST['v_brand_name'];
	$v_brand_icon = $_POST['v_brand_icon'];
	$v_brand_display = $_POST['v_brand_display'];
	$v_mall_type = $_POST['v_mall_type'];
	$v_category_type = $_POST['v_category_type'];
	if( is_null( $v_brand_display ) ){
		$v_brand_display = 0;
	}

	$up_brand_file = $brand_file->upFiles(); // 파일 가져오기
	if( strlen( $v_brand_icon ) > 0 && is_file( $imagepath.$v_brand_icon) && $up_brand_file['brand_icon'][0]['error'] === false ){
		$brand_file->removeFile( $v_brand_icon );
	}

	if( $up_brand_file['brand_icon'][0]['error'] === false ){
		$up_brand_icon = $up_brand_file['brand_icon'][0]['v_file'];
	} else {
		$up_brand_icon = $v_brand_icon;
	}

	$sql = "UPDATE tblproductbrand SET ";
	$sql.= "brandname	= '{$v_brand_name}', ";
	$sql.= "logo_img    = '".$up_brand_icon."', ";
	$sql.= "display_yn  = '{$v_brand_display}', ";
	$sql.= "mall_type  = '{$v_mall_type}', ";
	$sql.= "category_type  = '{$v_category_type}' ";
	$sql.= "WHERE bridx = '{$bridx}' ";
	if(pmysql_query($sql,get_db_conn())) {
		$onload="<script>window.onload=function(){ alert('브랜드 수정이 정상 완료되었습니다.');}</script>";
		DeleteCache("tblproductbrand.cache");
	} else {
		alert_go('동일명이 존재합니다. 다른 브랜드명을 입력해 주세요.',-1);
	}

}
$addQry = 'WHERE 1=1 ';
if( !is_null($s_keyword) && $s_keyword != '' ){
	$addQry.= 'AND UPPER( brandname ) LIKE UPPER( \'%'.trim($s_keyword).'%\' ) ';
}

if($mall_type) $addQry.= "AND mall_type = {$mall_type} ";

if($sort_opt == "display") $orderby = "display_yn desc, brandname asc";
else $orderby = "brandname asc";


$page_sql = "SELECT COUNT( * ) FROM tblproductbrand ";
$page_sql.= $addQry;
$paging = new newPaging($page_sql, 10, 20);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = "SELECT * FROM tblproductbrand ";
$sql.= $addQry;
$sql.= "ORDER BY {$orderby} ";
$sql = $paging->getSql( $sql );
//echo $sql;
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_array($result)) {
	$band[] = $row;
}
//print_r($band);
?>
<script type="text/javascript" src="lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">

function GoPage(block,gotopage) {
	document.insertForm.mode.value = '';
	document.insertForm.block.value = block;
	document.insertForm.gotopage.value = gotopage;
	document.insertForm.submit();
}

function selectItem( mode, bridx ){
	var checkErr = 0;
    
	if( mode == 'delete'){
		if( confirm( '삭제하시겠습니까?' ) ){
			checkErr = checkFrom( mode );
		} else {
			checkErr++;
		}
	} else if( mode == 'insert' ) {
		if( confirm( '등록하시겠습니까?' ) ){
			checkErr = checkFrom( mode );
		} else {
			checkErr++;
		}
	} else if ( mode == 'modify' ) {
		if( confirm( '수정하시겠습니까?' ) ){
			checkErr = checkFrom( mode );
		} else {
			checkErr++;
		}
	} else if ( mode == 'select' ) {
		
	} else {
		alert('잘못된 선택입니다.');
		checkErr++;
		$("#mode").val( '' );
		$("#bridx").val( '' );
	}

	if ( checkErr == 0 ){
		$("#mode").val( mode );
		$("#bridx").val( bridx );
		$("#insertForm").submit();
	}
	
}

function checkFrom( mode ){
	var checkErr = 0;
	if( mode != 'select' && mode != 'delete' ){
		if( $("#v_brand_name").val().length <= 0 ){
			alert('상품 브랜드명을 입력해주세요.');
			$("#v_brand_name").focus();
			checkErr++;
		}
	}
	return checkErr;
}

function brandSearch(){
    //document.insertForm.gotopage.value = "";
	$("#mode").val( '' );
	$("#bridx").val( '' );
	$("#insertForm").submit();
}

function excel_download() {
	if(confirm("검색된 모든 정보를 다운로드 하시겠습니까?")) {
		document.excelform.submit();
	}
}
</SCRIPT>

<div class="admin_linemap"><div class="line"><p>현재위치 : 상품관리 &gt; 브랜드 관리 &gt;<span>브랜드 관리</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">
	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
		<form name='insertForm' id='insertForm' method='POST' enctype="multipart/form-data">
		<input type='hidden' id='mode' name='mode' value='' />
		<input type='hidden' id='bridx' name='bridx' value='' />
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage>		
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_product.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">브랜드매뉴 관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>브랜드를 등록/수정/삭제 처리를 할 수 있습니다.</span></div>
				</td>
            </tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">브랜드 등록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					
						<col width=140></col>
						<col width=></col>
						<tr>
							<th><span>몰 타입</span></th>
							<TD>
							<select id='v_mall_type' name='v_mall_type'>
								<option value='0' <? if($selectBrand[4] == 0){ echo 'selected'; } ?>>전체</option>
								<option value='1' <? if($selectBrand[4] == 1){ echo 'selected'; } ?>>교육몰</option>
								<option value='2' <? if($selectBrand[4] == 2){ echo 'selected'; } ?>>기업몰</option>
							</select>
							</TD>
						</tr>
						<tr>
							<th><span>상품 브랜드명</span></th>
							<TD>
								<INPUT maxLength=80 size=80 id='v_brand_name' name='v_brand_name' value="<?=$selectBrand[1]?>">
							</TD>
						</tr>
						<tr>
							<th><span>브랜드 로고 이미지</span></th>
							<td class="td_con1" colspan="3" style="position:relative">
								<input type=file name="brand_icon[]" style="WIDTH: 400px"><br>
								<!--<span class="font_orange">(권장이미지 : )</span>-->
								<input type=hidden name="v_brand_icon" value="<?=$selectBrand[2]?>" >
								<div style='margin-top:5px' >
<?php
	if( is_file( $imagepath.$selectBrand[2] ) ){
?>
									<img src='<?=$imagepath.$selectBrand[2]?>' style='max-width: 250px;' />
<?php
	}
?>
								</div>
							</td>
						</tr>
						<tr>
							<th><span>브랜드 노출</span></th>
							<TD>
								<INPUT type='checkbox' id='v_brand_display' name='v_brand_display' value="1" <? if($selectBrand[3] == 1){ echo 'CHECKED'; } ?> >
								&nbsp; * 체크시 노출
							</TD>
						</tr>
						<tr>
							<th><span>메인 노출 위치</span></th>
							<TD>
							<select id='v_category_type' name='v_category_type'>
								<option value='1' <? if($selectBrand[5] == 1){ echo 'selected'; } ?>>디지털/가전</option>
								<option value='2' <? if($selectBrand[5] == 2){ echo 'selected'; } ?>>패션/잡화/기타</option>
							</select>
							</TD>
						</tr>
					</table>
					
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="8" align="center">
					<a href="javascript:selectItem('<?=$qType?>','<?=$bridx?>');">
						<img src="images/btn_confirm_com.gif">	
					</a>
				</td>
			</tr>
			<tr>
				<td>
					<div class="table_style01 pt_20">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tbody>
                                <tr>
                                    <th><span>몰 타입</span></th>
                                    <TD>
                                        <select name='mall_type'>
                                            <option value='0' <? if($mall_type == 0){ echo 'selected'; } ?>>전체</option>
                                            <option value='1' <? if($mall_type == 1){ echo 'selected'; } ?>>교육몰</option>
                                            <option value='2' <? if($mall_type == 2){ echo 'selected'; } ?>>기업몰</option>
                                        </select>
                                    </TD>
                                </tr>
								<tr>
									<th><span>브랜드명으로 검색</span></th>
									<td><input class="w200" type="text" name="s_keyword" value=""></td>
								</tr>
							</tbody>
						</table>
						<p class="ta_c">
							<a href="javascript:brandSearch();"><input type="image" src="img/btn/btn_search01.gif" alt="검색"></a>
						</p>
					</div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<!-- <div class="title_depth3_sub">검색된 메뉴목록</div> -->
                    <table border=0 width="100%">
                        <tr>
                            <td><div class="title_depth3_sub">검색된 메뉴목록</div></td>
                            <td align=right>정렬&nbsp;
                                <select name='sort_opt' onChange="javascript:brandSearch();">
                                    <option value='name' <? if($sort_opt == "name"){ echo 'selected'; } ?>>이름순</option>
                                    <option value='display' <? if($sort_opt == "display"){ echo 'selected'; } ?>>노출순</option>
                                </select>
                            </td>
                        </tr>
                    </table>
				</td>
				
			</tr>
			
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
						<col width='80'>
						<col width='120'>
						<col width='*'>
						<col width='400'>
						<col width='120'>
						<col width='120'>
						<col width='80'>
						<col width='80'>
				</colgroup>
				<TR>
					<th>번호</th>
					<th>몰 타입</th>
					<th>브랜드명</th>
					<th>브랜드 이미지</th>
					<th>메인 노출 위치</th>
					<th>노출</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $band ) > 0 ) {
		$cnt=0;
		foreach( $band as $bCnt=>$bVal ){
			$number = ( $t_count - ( 20 * ( $gotopage - 1 ) ) - $cnt );
?>
				<TR>
					<!-- 번호 -->
					<td>
						<?=$number?>
					</td>
					<!-- 몰타입 -->
					<td>
					<?=$mallTypeArr[$bVal['mall_type']]?>						
					</td>
					<!-- 브랜드명 -->
					<td>
						<?=$bVal['brandname']?>
					</td>
					<!-- 브랜드 이미지 -->
					<td>
						<div id='img_display' >
<?php
			if( is_file( $imagepath.$bVal['logo_img'] ) ){
?>
							<img src='<?=$imagepath.$bVal['logo_img']?>' style='max-width: 125px;' >
<?php
			}
?>
						</div>
					</td>
					<!-- 카테고리 타입 -->
					<td>
					<?=$cateTypeArr[$bVal['category_type']]?>						
					</td>
					<!-- 노출 / 비노출 -->
					<td>
					<?=$display[$bVal['display_yn']]?>						
					</td>
					<!-- 수정 -->
					<td>
						<a href="javascript:selectItem('select','<?=$bVal['bridx']?>');"><img src="images/btn_edit.gif"></a>
					</td>
					<!-- 삭제 -->
					<td>
						<a href="javascript:selectItem('delete','<?=$bVal['bridx']?>');"><img src="images/btn_del.gif"></a>
					</td>
				</TR>
<?php
			$cnt++;
		}
	} else {
?>
				<TR>
					<td colspan='8' > 브랜드가 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $band ) > 0 ) { ?>
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
<?	} ?>
					</div>
				</div>

				</td>
			</tr>
			<tr>
				<td colspan=9 align=right>
                    <a href="javascript:excel_download()"><img src="images/btn_excel1.gif" border="0"></a>
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
    
        <form name=excelform action="brand_excel.php" method=post>
        <input type=hidden name=s_keyword value="<?=$s_keyword?>">
        <input type=hidden name=sort_opt value="<?=$sort_opt?>">
        <input type=hidden name=mall_type value="<?=$mall_type?>">
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
