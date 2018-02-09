<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include("header.php");
####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################



$keyword=$_POST["keyword"];
$no = $_POST['no'];
$mode = $_POST['mode'];

if ( count($no) >= 1 ) {
    $whereIdx = "'" . implode("','", $no) . "'";
    
    $sql = "";
    if ( $mode == "visible_set" ) {
        $sql  = "UPDATE tblsignage_promotion SET viewyn = 1 WHERE no in ({$whereIdx}) ";
    } else if ( $mode == "visible_unset" ) {
        $sql  = "UPDATE tblsignage_promotion SET viewyn = 0 WHERE no in ({$whereIdx}) ";
    }

    if ( !empty($sql) ) { $result = pmysql_query($sql); }
}

$where="";
if($keyword){
	$where[]="mtitle='".$keyword."'";
}

$sql="select * from tblsignage_promotion ";
if($where){
	$sql.=" where ".implode(" and ",$where);
}
$sql.=" order by s_date desc, no desc";
$res=pmysql_query($sql);
?>



<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function event_pop(mode,no){

	blockval=$('#block').val();
	gotoval=$('#gotopage').val();

	if(mode=='del'){
		if(confirm('삭제하시겠습니까?')){
			document.location.href="signage_promotion_write.php?mode="+mode+"&no="+no;
		}
	}

	document.location.href="signage_promotion_write.php?mode="+mode+"&no="+no;
}

function GoPage(block,gotopage) {
	document.form1.block.value=block;
	document.form1.gotopage.value=gotopage;
	document.form1.submit();
}

function allCheck(obj) {
    if ( $(obj).is(":checked") ) {
        $("input:checkbox[name='no[]']").attr("checked", true);
    } else {
        $("input:checkbox[name='no[]']").attr("checked", false);
    }
}


function changeStatus(mode) {
    if ( $("input[name='no[]']:checked").length == 0 ) {
        alert('하나 이상을 선택해 주세요.');
        return;
    }

    switch(mode) {
        case 1:
            // 노출 설정
            document.form1.mode.value = "visible_set";
            msg = "노출 설정을 하시겠습니까?";
            break;
        case 2:
            // 비노출 설정
            document.form1.mode.value = "visible_unset";
            msg = "비노출 설정을 하시겠습니까?";
            break;
    }

    if ( confirm(msg) ) {
        document.form1.submit();
    }
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디지털사이니즈 &gt; <span>프로모션 관리</span></p></div></div>
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
			<?php include("menu_signage.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name="form1" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type="hidden" name="mode" value="<?=$mode?>" />
			<input type="hidden" id="block" name="block" value="<?=$_REQUEST['block']?>" />
			<input type="hidden" id="gotopage" name="gotopage" value="<?=$gotopage?>" />

            <tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">프로모션 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>프로모션 검색</span></div>
					<div class="table_style01 pt_20">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th><span>타이틀 검색</span></th>
								<td>
									<input class="w200" type="text" name="keyword" value="<?=$keyword?>"/>
								</td>
								
							</tr>
						</table>
						<p class="ta_c"><a href="#"><input type="image" src="img/btn/btn_search01.gif" alt="검색"></a></p>
					</div>
				</td>
			</tr>

            <tr><td height="10"></td></tr>

			<tr>
				<td>
				<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					    <col width=70>
					    <col width=70>
                        <col width=auto>
					    <col width=150>
                        <col width=100 style="display:none">
					    <col width=100 style="display:none">
                        <col width=70>
                        <col width=70>
                        <col width=100>
                        <col width=100>
						<col width=100>
					<TR align=center>
						<th><input type='checkbox' onClick='javascript:allCheck(this);'></th>
						<th>No</th>
						<th>타이틀</th>
						<th>기간</th>
						<th style="display:none">재참여기간</th>
						<th style="display:none">재당첨기간</th>
						<th>상품수</th>
						<th>사용</th>
						<th>등록일</th>
						<th>수정</th>
						<th>삭제</th>
					</TR>
				<?
				while($row=pmysql_fetch_object($res)) {
					list($content_count)=pmysql_fetch("select count(no) from tblsignage_promotion_content where promotion_no='".$row->no."'");

					if(!$row->re_pare){
						$re_pare="없음";
					}else{
						$re_pare=$row->re_pare."일";
					}
					if(!$row->re_won){
						$re_won="없음";
					}else{
						$re_won=$row->re_won."일";
					}
					$cnt++;
				?>
					<TR>
                    <TD><input type="checkbox" name="no[]" value="<?=$row->no?>"></TD>
					<TD><?=$cnt?></TD>
					<TD style="text-align:center;"><?=$row->mtitle?></TD>
					<TD style="text-align:left;"><?=$row->s_date?> ~ <?=$row->e_date?></TD>
					<TD style="text-align:center; display:none"><?=$re_pare?></TD>
					<TD style="text-align:center; display:none"><?=$re_won?></TD>
					
					<TD><?=$content_count?></TD>
					<TD><?if($row->viewyn=="1"){echo "노출";}else{echo "비노출";}?></TD>
					<TD><?=$row->regdt?></TD>
                    
					<TD><a href="javascript:event_pop('mod','<?=$row->no?>');"><img src="images/btn_edit.gif" border="0"></a></TD>
					<TD><a href="javascript:event_pop('del','<?=$row->no?>');"><img src="images/btn_del.gif" border="0"></a></TD>
				    </TR>
				<?
				}
				pmysql_free_result($res);
				if ($cnt==0) {
					echo "<TR><TD colspan=12 align=center>등록된 목록이 없습니다.</TD></TR>";
				}
?>

				</TABLE>
<?

				echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
				echo "<div class=\"page_navi\">";
				echo "<ul>";
				echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
				echo "</ul>";
				echo "</div>";
				echo "</div>";
?>

				</td>
			</tr>
			<tr>
				<td>
                    <div style="text-align:center;padding-bottom:40px;">
                        <img src="../admin/images/btn_visible_set.png" onclick="javascript:changeStatus(1);"/>
                        <img src="../admin/images/btn_visible_unset.png" onclick="javascript:changeStatus(2);"/>
                        <br/><img src="../admin/images/btn_confirm_com.gif" onclick="javascript:event_pop('ins');"/>
                    </div>
                </td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
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
<?=$onload?>
<?php
include("copyright.php");
