<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");
include("header.php");
####################### 페이지 접근권한 check ###############
$PageCode = "co-2";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/product/";

$display_type=$_POST[display_type];
$s_date=$_POST[s_date];
$e_date=$_POST[e_date];
$code_type=$_POST[code_type];
$s_keyword=$_POST[s_keyword];
$mode=$_POST[mode];
$no = $_POST[no];



if ( count($no) >= 1 ) {
    $whereIdx = "'" . implode("','", $no) . "'";
    
    $sql = "";
    if ( $mode == "visible_set" ) {
        $sql  = "UPDATE tblsignage_calendar SET s_viewyn = 'Y' WHERE no in ({$whereIdx}) ";
    } else if ( $mode == "visible_unset" ) {
        $sql  = "UPDATE tblsignage_calendar SET s_viewyn = 'N' WHERE no in ({$whereIdx}) ";
    }

    if ( !empty($sql) ) { $result = pmysql_query($sql); }
}

if($display_type) $where[]="s_viewyn='".$display_type."'";
if($s_date) $where[]="s_date>='".$s_date."'";
if($e_date) $where[]="s_date<'".$e_date."'";
if($s_keyword) {
	if($code_type=="name") $where[]= "lower( b.productname ) LIKE lower( '%{$s_keyword}%' ) ";
	else if($code_type=="code") $where[]= "lower( b.productcode ) LIKE lower( '%{$s_keyword}%' ) ";
}


$page_sql = "SELECT COUNT(*) FROM tblsignage_calendar ";
if(count($where)){
	$page_sql.=" where ".implode(" and ",$where);
}
//echo $page_sql;
$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql="select * from tblsignage_calendar a left join tblproduct b on (a.s_productcode=b.productcode) ";
if(count($where)){
	$sql.=" where ".implode(" and ",$where);
}
$sql.="order by s_date desc";

$sql = $paging->getSql($sql);

$res=pmysql_query($sql);
?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">


function event_ins(mode,idx,seq){
	if(mode=='del'){
		if(confirm('삭제하시겠습니까?')){
			document.location.href="signage_calendar_write.php?mode="+mode+"&no="+idx;
		}
	}else{
		document.location.href="signage_calendar_write.php?mode="+mode+"&no="+idx;
	}
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 디지털사이니즈 &gt; <span>캘린더 설정 관리</span></p></div></div>
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
			<input type="hidden" id="type" name="type" />
			<input type="hidden" id="num" name="num" value="<?=$num?>" />
			<input type="hidden" id="htmlmode" name="htmlmode" value='wysiwyg' />
			<input type="hidden" id="block" name="block" value="<?=$_REQUEST['block']?>" />
			<input type="hidden" id="gotopage" name="gotopage" value="<?=$gotopage?>" />
			<input type="hidden" id="board" name="board" value=<?=$board?> />
			<input type='hidden' id='disabled_idx' name='disabled_idx' value='' />
            <input type=hidden name='s_checklist' value='<?=$s_checklist?>'>
            <input type=hidden name='s_notchecklist' value='<?=$s_notchecklist?>'>
            <input type=hidden name='search_event_type' value='<?=$search_event_type?>'>
            <input type=hidden name='search_is_gnb' value='<?=$search_is_gnb?>'>
            <input type=hidden name='search_hidden' value='<?=$search_hidden?>'>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">캘린더 설정 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>캘린더 검색</span></div>
					<div class="table_style01 pt_20">
						<table cellpadding="0" cellspacing="0" border="0" width="100%">
							<tr>
								<th><span>상품검색</span></th>
								<td colspan=3>
									<select name="code_type">
										<option value="name" <?if($code_type=="name")echo"selected";?>>상품명</option>
										<option value="code" <?if($code_type=="code")echo"selected";?>>상품코드</option>
									</select>
									<input class="w200" type="text" name="s_keyword" value="<?=$s_keyword?>">
								</td>
							</tr>
							<tr>
								<th><span>날짜</span></th>
								<td>
									<INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=s_date value="<?=$s_date?>" class="input_bd_st01">
									~ <INPUT style="TEXT-ALIGN: center" onfocus=this.blur(); onclick=Calendar(event) size=15 name=e_date value="<?=$e_date?>" class="input_bd_st01">
								</td>
								
							</tr>
							<tr>
								<th><span>노출상태</span></th>
								
								<td>
									<select name="display_type" id="display_type">
										<option value="" <?php if ($display_type == "") echo "selected" ?> >선택</option>
										<option value="Y" <?php if ($display_type == "Y") echo "selected" ?> >노출</option>
										<option value="N" <?php if ($display_type == "N") echo "selected" ?> >비노출</option>
										
									</select>
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
						<col width=100>
						<col width=auto>
						<col width=100>
						
                        <col width=300>
                        
					    
                        <col width=100>
                        <col width=80>
						<col width=80>
					<TR align=center>
						<th><input type='checkbox' onClick='javascript:allCheck(this);'></th>
						<th>No</th>
						<th>노출일</th>
						<th>매장명</th>
						<th>이미지</th>
						<th>상품명</th>
						
						
						<th>노출상태</th>
						<th>수정</th>
						<th>삭제</th>
					</TR>
				<?
				while($row=pmysql_fetch_object($res)) {
					if($row->s_viewyn=="Y") $viewyn="노출";
					else $viewyn="비노출";

					$store_no="";
					$store_name="";
					if($row->s_storetype=="1"){
						$storetype="공통";
					}else{
						$store_ex=explode("@#",$row->s_store);
						foreach($store_ex as $k){
							$store_no[]=$k;
						}
						$s_qry="select * from tblsignage_store where no in (".implode(",",$store_no).") order by name";
						$s_result=pmysql_query($s_qry);

						while($s_date=pmysql_fetch_object($s_result)){
							$store_name[]=$s_date->name;
						}

						$storetype=implode(",",$store_name);
					}
					
					$cnt++;

				?>
					<TR>
                    <TD><input type="checkbox" name="no[]" value="<?=$row->no?>"></TD>
					<TD><?=$cnt?></TD>
					<TD><?=$row->s_date?></TD>
					<TD style="text-align:left;"><?=$storetype?></TD>
					<td>
						<?if(file_exists($Dir.DataDir."shopimages/product/".$row->minimage) && $row->minimage){?>
							<img src="<?=$imagepath.$row->minimage."?v".date("His")?>" style="width:70px" border=1>
						<?}else if($row->minimage){?>
							<img src="<?=$row->minimage?>" style="width:70px" border=1>
						<?}else{?>
							-
						<?}?>
					</td>
					<TD style="text-align:left;"><?=$row->productname?></TD>

					
					
					<TD><?=$viewyn?></TD>
                    
					<TD><a href="javascript:event_ins('mod','<?=$row->no?>');"><img src="images/btn_edit.gif" border="0"></a></TD>
					<TD><a href="javascript:event_ins('del','<?=$row->no?>');"><img src="images/btn_del.gif" border="0"></a></TD>
				    </TR>
				<?
				}
				pmysql_free_result($res);
				if ($cnt==0) {
					echo "<TR><TD colspan=12 align=center>등록된 목록이 없습니다.</TD></TR>";
				}
?>

				</TABLE>

				 <div class="list_search" style="width:100%;text-align:right;padding-top:20px; display:none;">

					<select class="option" name="skey">
						<option value="title" <?=$selected['skey']['title']?>>타이틀</option>
					</select>
					<input type="text" class="bar" name="keyword2222" value="<?=$_POST['keyword']?>"/>
					<input type="image" src="../admin/images/btn_search_com.gif" style="vertical-align:middle">
				 </div>
				</div>
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
					<!--
						<img src="../admin/images/btn_gnb_reg.png" onclick="javascript:changeStatus(1);"/>
                        <img src="../admin/images/btn_gnb_cancel.png" onclick="javascript:changeStatus(2);"/>-->
                        <img src="../admin/images/btn_visible_set.png" onclick="javascript:changeStatus(1);"/>
                        <img src="../admin/images/btn_visible_unset.png" onclick="javascript:changeStatus(2);"/>
                        <br/>
                       <img src="../admin/images/btn_confirm_com.gif" onclick="javascript:event_ins('ins');"/>
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
<script language="javascript">
    function popup_window(src,width,height)
    {
        window.open(src,'','width='+width+',height='+height+',scrollbars=1');
    }
    </script>
<?=$onload?>
<?php
include("copyright.php");
