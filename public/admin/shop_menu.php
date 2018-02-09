<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
//$PageCode = "me-1";
$PageCode = "sh-2";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include("header.php");

$mode = $_POST['mode'];
$no = $_POST['no'];
$menu_type = $_POST['menu_type'];
$menu_idx = $_POST['menu_idx'];
$selectIdx = $_POST['selectIdx'];

// 메뉴 노출
$display = array('0'=>'사용안함','1'=>'사용');
// 메뉴 타입
$menuTypeArr = array('1'=>'좌측','2'=>'우측');
$qType = "insert";

$in_menu_type   = $_POST['in_menu_type'];
$in_title       = $_POST['in_title'];
$in_url         = $_POST['in_url'];
$in_sort        = $_POST['in_sort'];
$in_display     = $_POST['in_display']?$in_display=$_POST['in_display']:$in_display=0;
$related_urls   = $_POST['related_urls'];

if( $mode == 'insert' ){ // 입력
	
	$sql = "INSERT INTO tblmainmenu ( menu_type, menu_title, menu_url, menu_sort, menu_display, related_urls) ";
	$sql.= "VALUES ('{$in_menu_type}', '{$in_title}', '".pmysql_escape_string($in_url)."', '{$in_sort}', '{$in_display}', '" . pmysql_escape_string($related_urls) . "' ) ";
	pmysql_query( $sql, get_db_conn() );
	$onload = "<script> alert('등록되었습니다.'); </script>";

} else if( $mode == 'modify' && ord( $menu_idx ) ) { //수정
	
	$sql = "UPDATE tblmainmenu ";
	$sql.= " SET menu_type = '{$in_menu_type}', menu_title = '{$in_title}', ";
	$sql.= " menu_url = '".pmysql_escape_string( $in_url )."', menu_sort = '{$in_sort}', menu_display = '{$in_display}', ";
    $sql.= " related_urls = '" . pmysql_escape_string($related_urls) . "' ";
	$sql.= " WHERE menu_idx = '{$menu_idx}' ";
	//echo $sql;
	//exit;

	pmysql_query( $sql, get_db_conn() );
	$onload = "<script> alert('수정되었습니다.'); </script>";

} else if ( $mode == 'delete' && ord( $menu_idx ) ) { //삭제
	$sql = " DELETE FROM tblmainmenu WHERE menu_idx = {$menu_idx} ";
	$res = pmysql_query( $sql, get_db_conn() );
	$onload = "<script> alert('삭제되었습니다.'); </script>";
}

//수정 data
if( ord( $selectIdx ) ){
	$mSql = "SELECT * FROM tblmainmenu WHERE menu_idx = {$selectIdx} ";
	$mRes = pmysql_query( $mSql, get_db_conn() );
	if( $mRow = pmysql_fetch_array( $mRes ) ){
		$mSelect = $mRow;
	}
	pmysql_free_result( $mRes );
	$qType = 'modify';
}
//게시판 리스트 type에 따른 추가 쿼리
$addQry = "";
$addOrderBy = '';
if( ord($menu_type) ){
	$addQry = " AND menu_type = {$menu_type} ";
	$addOrderBy = ' ORDER BY menu_sort ';
} else {
	$addOrderBy = " ORDER BY menu_sort ";
}
$page_sql = "SELECT COUNT(*) FROM tblmainmenu WHERE 1=1 {$addQry} ";
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;
// 리스트 data
$list_sql = "SELECT * FROM tblmainmenu WHERE 1=1 {$addQry} {$addOrderBy} ";
$sql = $paging->getSql( $list_sql );
$list_res = pmysql_query( $list_sql, get_db_conn() );

while( $list_row = pmysql_fetch_array( $list_res ) ){
	$menu_list[] = $list_row;
}
pmysql_free_result( $list_res );

?>
<script type="text/javascript" src="lib.js.php"></script>

<SCRIPT LANGUAGE="JavaScript">

function checkForm(mode, del_idx ){
	var confirmText = "";
	var errorChk = 0;
	if( mode == 'insert' ){
		errorChk = checkValue();
		if( errorChk > 0 ) {
			return;
		} else {
			confirmText = '등록하시겠습니까?';
		}
	} else if( mode == 'modify'){
		errorChk = checkValue();
		if( errorChk > 0 ) {
			return;
		} else {
			confirmText = '수정하시겠습니까?';
		}
	} else if ( mode == 'delete' ) {
		if( del_idx.length > 0 ) {
			confirmText = '삭제 하시겠습니까?';
			$('#menu_idx').val( del_idx );
		} else {
			return;
		}

	} else {
		alert('잘못된 명령입니다.');
		return;
	}

	if( confirm( confirmText ) ){
		$("#mode").val(mode);
		$("#insertForm").submit();
	}
}

function modifyChk( mIdx ) {
	$('#selectIdx').val( mIdx );
	$("#insertForm").submit();

}

function checkValue(){
	var errorChk = 0;
	if( $("#in_menu_type").val().length == 0 ) {
		alert('메뉴타입을 선택해주세요.');
		errorChk++;
		$("#in_menu_type").focus();
		return errorChk;
	}

	if( $("#in_title").val().length == 0 ) {
		alert('타이틀을 입력해주세요.');
		errorChk++;
		$("#in_title").focus();
		return errorChk;
	}

	if( $("#in_url").val().length == 0 ) {
		alert('URL을 입력해주세요.');
		errorChk++;
		$("#in_url").focus();
		return errorChk;
	}

	if( $("#in_sort").val().length == 0 ) {
		alert('노출 순서를 입력해주세요.');
		errorChk++;
		$("#in_sort").focus();
		return errorChk;
	}

}

function GotoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function changeMenu(){
	$("#mode").val('');
	$('#menu_idx').val('');
	$("#insertForm").submit();
}

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
		<input type='hidden' id='mode' name='mode' value='' />
		<input type='hidden' id='menu_idx' name='menu_idx' value='<?=$mSelect['menu_idx']?>' />
		<input type='' name='selectIdx' id='selectIdx' value='' />
		<input type=hidden name=block value="<?=$block?>">
		<input type=hidden name=gotopage value="<?=$gotopage?>">		
		<col width=240 id="menu_width"></col>
		<col width=10></col>
		<col width=></col>
		<tr>
			<td valign="top">
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">헤더매뉴 관리</div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>메뉴를 등록/수정/삭제 처리를 할 수 있습니다.</span></div>
				</td>
            </tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">메뉴 등록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					
						<col width=220></col>
						<col width=></col>
						<tr>
							<th><span>메뉴타입</span></th>
							<TD>
								<select id="in_menu_type" name="in_menu_type" >
								<option value='1' <? if( $mSelect['menu_type'] == '1' ) { echo "SELECTED"; } ?> > 좌측 </option>
								<option value='2' <? if( $mSelect['menu_type'] == '2' ) { echo "SELECTED"; } ?> > 우측 </option>
								</select>
							</TD>
						</tr>
						<tr>
							<th><span>타이틀</span></th>
							<TD><INPUT maxLength=80 size=80 id='in_title' name='in_title' value="<?=$mSelect['menu_title']?>"></TD>
						</tr>
						<tr>
							<th><span>URL</span></th>
							<TD><INPUT maxLength=80 size=80 id='in_url' name='in_url' value='<?=$mSelect['menu_url']?>' ></TD>
						</tr>
						<tr>
							<th><span>관련URL (상단GNB on 처리용)</span></th>
							<TD><textarea cols="100" rows="5" id='related_urls' name='related_urls'><?=$mSelect['related_urls']?></textarea> (세미콜론(;)로 구분)</TD>
						</tr>
						<tr>
							<th><span>노출순서</span></th>
							<TD><INPUT maxLength=10 size=10 id='in_sort' name='in_sort' value="<?=$mSelect['menu_sort']?>" ></TD>
						</tr>
						<tr>
							<th><span>사용</span></th>
							<TD><INPUT type='checkbox' id='in_display' name='in_display' value="1" <? if( $mSelect['menu_display'] == '1' ) { echo "CHECKED"; } ?> > * 체크시 사용 </TD>
						</tr>
					</table>
					
				</div>
				</td>
			</tr>
			<tr>
				<td colspan="8" align="center">
					<a href="javascript:checkForm('<?=$qType?>');">
						<img src="images/btn_confirm_com.gif">	
					</a>
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
					<select name='menu_type' onchange='javscript:changeMenu();' >
						<option value='' >============</option>
						<option value='1' <? if( $menu_type == '1' ) { echo 'SELECTED'; } ?> > 좌측 </option>
						<option value='2' <? if( $menu_type == '2' ) { echo 'SELECTED'; } ?> > 우측 </option>
					</select>
				</div>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<colgroup>
						<col width='80'>
						<col width='80'>
						<col width='*'>
						<col width='350'>
						<col width='50'>
						<col width='80'>
						<col width='80'>
						<col width='80'>
				</colgroup>
				<TR>
					<th>번호</th>
					<th>메뉴타입</th>
					<th>타이틀</th>
					<th>URL</th>
					<th>노출순서</th>
					<th>사용</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
	if( count( $menu_list ) > 0 ) {
		$cnt=0;
		foreach( $menu_list as $menuCnt=>$menuVal ){
			$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
?>
				<TR>
					<!-- 번호 -->
					<td>
						<?=$number?>
					</td>
					<!-- 메뉴타입 -->
					<td>
						<?=$menuTypeArr[$menuVal['menu_type']]?>
					</td>
					<!-- 타이틀 -->
					<td>
						<?=$menuVal['menu_title']?>
					</td>
					<!-- URL -->
					<td>
						<?=$menuVal['menu_url']?>
					</td>
					<!-- 노출 순서 -->
					<td>
						<?=$menuVal['menu_sort']?>
					</td>
					<!-- 노출 / 비노출 -->
					<td>
						<?=$display[$menuVal['menu_display']]?>
					</td>
					<!-- 수정 -->
					<td>
						<a href="javascript:modifyChk( '<?=$menuVal["menu_idx"]?>' );"><img src="images/btn_edit.gif"></a>
					</td>
					<!-- 삭제 -->
					<td>
						<a href="javascript:checkForm('delete', '<?=$menuVal["menu_idx"]?>' );"><img src="images/btn_del.gif"></a>
					</td>
				</TR>
<?php
			$cnt++;
		}
	} else {
?>
				<TR>
					<td colspan='8' > 헤더매뉴가 존재하지 않습니다.</td>
				</TR>
<?php
	}
?>

				</TABLE>
				</div>

				<!--페이징-->
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
<?	if( count( $menu_list ) > 0 ) { ?>
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
