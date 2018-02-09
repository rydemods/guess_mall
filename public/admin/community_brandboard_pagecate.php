<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$up_board_code = $_POST["up_board_code"];
$board_code = $_POST["board_code"];
$mode = $_POST["mode"];
$page_name = $_POST["page_name"];
$use_yn = $_POST["use_yn"];
if ( !$board_code && !$up_board_code ) {
	$board_code = 3;
} else if ( $up_board_code ) {
	$board_code = $up_board_code;
}

if ( $mode == "InsertCate") {
	$sql = "INSERT INTO tblbrand_boardpage ( ";
	$sql.= "board_code, page_name, date, use_yn ";
	$sql.= ") VALUES ( ";
	$sql.= $board_code.", '".pmysql_escape_string($page_name)."', '".date("YmdHis")."',";
	if( !$use_yn ){
		$sql.=" 'N' ";
	} else {
		$sql.=" 'Y' ";
	}
	$sql.= ") ";
	
	//exdebug($sql);
	pmysql_query( $sql, get_db_conn() );
	if( !pmysql_error() ){
		echo "<script>";
		echo "	alert('등록되었습니다.');";
		echo "	location.replace('community_brandboard_pagecate.php')";
		echo "</script>";
		exit;
	}
	
}

if ( $mode == "CateDel" ) {
	$delPageCode = $_POST['del_code'];
	if( $delPageCode ){
		$sql = "DELETE FROM tblbrand_boardpage WHERE page_code = ".$delPageCode;
		pmysql_query( $sql, get_db_conn() );
		if( !pmysql_error() ){
			echo "<script>";
			echo "	alert('삭제되었습니다.');";
			echo "	location.replace('community_brandboard_pagecate.php')";
			echo "</script>";
			exit;
		}
	} 
	
	echo "<script>";
	echo "	alert('오류가 발생했습니다.');";
	echo "	location.replace('community_brandboard_pagecate.php')";
	echo "</script>";
	exit;
}

if( $mode == "CateUpdate" ){
	$upCode = $_POST['up_code'];
	$m_page_name = $_POST['m_page_name'];
	$m_use_yn = $_POST['m_use_yn'];
	$page_code = $_POST['page_code'];
	
	if( $page_code[$upCode] ){
		$sql = "UPDATE tblbrand_boardpage SET ";
		$sql.= "page_name = '".pmysql_escape_string($m_page_name[$upCode])."', ";
		if ( !$m_use_yn[$upCode] ) {
			$sql.= "use_yn = 'N' ";
		} else {
			$sql.= "use_yn = 'Y' ";
		}
		$sql.= "WHERE page_code = ".$page_code[$upCode];
		pmysql_query( $sql, get_db_conn() );
		if( !pmysql_error() ){
			echo "<script>";
			echo "	alert('수정되었습니다.');";
			echo "	location.replace('community_brandboard_pagecate.php')";
			echo "</script>";
			exit;
		}
	}
	
	echo "<script>";
	echo "	alert('오류가 발생했습니다.');";
	echo "	location.replace('community_brandboard_pagecate.php')";
	echo "</script>";
	exit;
}
include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
	function updateCate(){
		if(cateCehck()){
			return false;
		}
		$("#form2_mode").val("InsertCate");
		$("#form2").submit();
	}
	
	function cateCehck(){
		var falseChk = false;
		if($("#page_name").val().length <= 0){
			alert("카테고리 이름을 입력해 주세요.");
			falseChk = true;
		}
		
		return falseChk;
	}
	
	function cateDel(page_code){
		if(confirm('해당 카테고리를 삭제 하시겠습니까?')){
			$("#form1_mode").val("CateDel");
			$("#del_code").val(page_code);
			$("#form1").submit();
		}
	}
	
	function cateModify(up_code){
		if(confirm('해당 카테고리를 수정 하시겠습니까?')){
			$("#form1_mode").val("CateUpdate");
			$("#up_code").val(up_code);
			$("#form1").submit();
		}
	}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 브랜드 게시판 &gt;<span>게시판 카테고리 관리</span></p></div></div>
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
			<?php include("menu_community.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">게시판 카테고리 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>게시판 카테고리를 관리할 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub">등록된 게시판 카테고리 관리를 수정/삭제</div>                                      
				</td>
			</tr>
			<!--<tr>
            	<td style="padding-top:5; padding-bottom:5">
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 한줄 공지사항 : 게시판 최상단에 위치하며, 고객에게 알리는 간단한 문구를 등록할 수 있습니다.</li>
                            <li>2) 공지사항 : 한줄 공지사항 아래쪽에 위치하며, 고객에게 알리는 상세한 내용을 등록할 수 있습니다.</li>
                        </ul>
                    </div>
                </td>
            </tr>-->
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<form name="form1" id="form1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type="hidden" name="mode" id="form1_mode" value=""/>
				<input type="hidden" name="up_code" id="up_code" value=""/>
				<input type="hidden" name="del_code" id="del_code" value=""/>
				<TR>
					<th><span>게시판 목록</span></th>
					<TD>
					<SELECT onchange="this.form.submit();" name="board_code" class="select">
<?php
					$sql = "SELECT * FROM tblbrand_boardadmin WHERE board_code IN (3,5) ORDER BY date ASC ";
					$result=pmysql_query($sql,get_db_conn());
					$cnt=0;
					while($row=pmysql_fetch_object($result)) {
						$cnt++;
						if($board_code==$row->board_code) {
							echo "<option value=\"{$row->board_code}\" selected>{$row->board_name}</option>\n";
							$one_notice=$row->notice;
						} else {
							echo "<option value=\"{$row->board_code}\">{$row->board_name}</option>\n";
						}
					}
					pmysql_free_result($result);
?>
					</SELECT>
					</TD>
				</TR>
				<TR>
					<th><span>등록된 카테고리</span></th>
					<TD>
					<div class="table_none">
					<table cellpadding="0" cellspacing="0" width="100%">
<?
					$onCateSql = "SELECT * FROM tblbrand_boardpage WHERE board_code = ".$board_code;
					$onCateRes = pmysql_query($onCateSql,get_db_conn());
					$onCateCnt = 0;
					while ( $onCateRow = pmysql_fetch_array($onCateRes) ) {

?>					
					<tr>
						<td><INPUT style="WIDTH: 100%" name="m_page_name[<?=$onCateCnt?>]" value="<?=$onCateRow[page_name]?>" class="input"> </td>
						<TD width="50"><INPUT type="checkbox" name="m_use_yn[<?=$onCateCnt?>]" id="m_use_yn" value="1" <? if( $onCateRow[use_yn] == "Y" ) { echo "checked"; } ?> >공개</TD>
						<td width="106"><input type="hidden" name="page_code[<?=$onCateCnt?>]" value="<?=$onCateRow[page_code]?>" >
							<p align="right"><a href="javascript:cateModify('<?=$onCateCnt?>');"><img src="images/btn_edit.gif" border="0" hspace="2"></a><a href="javascript:cateDel('<?=$onCateRow[page_code]?>');"><img src="images/btn_del.gif" border="0"></a></td>
						
					</tr>
<?
						$onCateCnt++;
					}
?>
					</table>
					</div>
					</TD>
				</TR>
				</form>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">카테고리 신규등록 및 수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<form name=form2 id="form2" action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type="hidden" name="up_board_code" value="<?=$board_code?>"/>
				<input type="hidden" name="mode" id="form2_mode" value="" />
				<TR>
					<th><span>카테고리 이름</span></th>
					<TD><INPUT maxLength="28" name="page_name" id="page_name" value="" class="input" size="27"></TD>
				</TR>
				<TR>
					<th><span>공개여부</span></th>
					<TD><INPUT type="checkbox" name="use_yn" id="use_yn" value="1" >공개</TD>
				</TR>
				</form>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:updateCate();"><img src="images/botteon_save.gif"  border="0"></a></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<!--<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>한줄 공지사항</span></dt>
							<dd>
								- 게시판 상단에 한줄로 간단한 내용을 알릴수 있는 한줄 공지 가능입니다.<br>
- 공지사항보다 위쪽에 출력되며, 등록은 게시판 별로 1개만 가능합니다.
							</dd>
						</dl>
						<dl>
							<dt><span>공지사항</span></dt>
							<dd>
							- 게시판 상단에 공지글을 등록할 수 있는 기능으로 일반 게시물과 동일하게 상세하게 등록 가능합니다.<br>
							- 공지사항의 등록 개수는무제한이며, 등록된 순서대로 상단에 출력됩니다.
							</dd>
						</dl>
						<dl>
							<dt><span>등록방법</span></dt>
							<dd>
							① 등록을 원하는 게시판 선택합니다.<br>
							② 한줄 공지사항 또는 공지사항을 등록/수정/삭제 하시면 됩니다.
							</dd>
						</dl>-->
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
<?=$onload?>
<?php 
include("copyright.php");
