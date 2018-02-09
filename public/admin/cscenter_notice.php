<?php
/********************************************************************* 
// 파 일 명		: community_magazine_list.php
// 설     명		: 룩북 리스트 관리
// 작 성 자		: 2016.09.23 - 김대엽
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
	include_once($Dir."lib/file.class.php");
	include("access.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "co-2";
	$MenuCode = "community";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}

#################################################################
	//exdebug($_POST);
#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode				= $_POST["mode"];
	$s_check			= $_POST["s_check"];
	$search				= $_POST["search"];
	$viewyn				= $_POST["viewyn"];
	$notice_category				= $_POST["notice_category"];
	$store_no			= $_POST["store_no"];


	if ( $mode == "delete" ) {

		$imagepath			= $Dir.DataDir."shopimages/cscenter/";
		$upload_del_file     = new FILE($imagepath);

		$sql  = "SELECT * FROM tblcsnotice WHERE no = '".$_POST["no"]."' ";
		$row  = pmysql_fetch_object(pmysql_query($sql));

		$upload_del_file->removeFile($row->notice_file);
		
		$qry = "DELETE FROM tblcsnotice WHERE no ='".$_POST["no"]."'";
		pmysql_query( $qry, get_db_conn() );

		//exdebug($sql);
		echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
	}

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/cscenter/";

#---------------------------------------------------------------
# 검색부분을 정리한다.
#---------------------------------------------------------------
	$where="";
	if(ord($search)) {
// 		$tmpSearch = strtoupper($search);
// 		if ($s_check == 'title') {
// 			$where[]= "( UPPER(title) LIKE '%{$tmpSearch}%' ) ";
// 		} else if ($s_check == 'content') {
// 			$where[]= "( UPPER(content) LIKE '%{$tmpSearch}%' ) ";
// 		}

		$search = trim($search);
		$temp_search = explode("\r\n", $search);
		$cnt = count($temp_search);
		
		$search_arr = array();
		for($i = 0 ; $i < $cnt ; $i++){
			array_push($search_arr, "'%".$temp_search[$i]."%'");
		}
		
		if ($s_check == 'title') {
			$where[]= "( title LIKE any ( array[".implode(",", $search_arr)."] ) ) ";
		} else if ($s_check == 'content') {
			$where[]= "( content LIKE any ( array[".implode(",", $search_arr)."] ) ) ";
		}
		
	}
	if($viewyn) $where[]= "viewyn='".$viewyn."'";
	if($notice_category) $where[]= "notice_category='".$notice_category."'";
	if($store_no) $where[]= "store_no='".$store_no."'";
	
	include("header.php");  // 상단부분을 불러온다.

	
#---------------------------------------------------------------
# 검색쿼리 카운트 및 페이징을 정리한다.
#---------------------------------------------------------------
	$listnum = 20;

	$sql = "SELECT COUNT(*) as t_count FROM  tblcsnotice ";
	if($where)$sql.= " where ".implode(" and ", $where);
	$paging = new newPaging($sql,10,$listnum,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;		
	//exdebug($sql);
	
#매장정보 가져오기
$store_qry="select * from tblstore order by name";
$store_result=pmysql_query($store_qry);


#노출 세팅
$selected["viewyn"][$viewyn]="selected";
$selected["notice_category"][$notice_category]="selected";
$selected["store_no"][$store_no]="selected";
$display["Y"]="게시";
$display["N"]="미게시";
$notice_type["1"]="공지";
$notice_type["2"]="일반";
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function Searchlb() {
	document.sForm.submit();
}

function GoPage(block,gotopage) {
	document.pageForm.block.value=block;
	document.pageForm.gotopage.value=gotopage;
	document.pageForm.submit();
}

function Modify(no) {
	location.href="cscenter_notice_write.php?&mode=modfiy_select&no="+no;
}

function Delete(no) {
    if( confirm("삭제하시겠습니까?") ) {
        document.form_del.mode.value= "delete";
        document.form_del.no.value=no;
		document.form_del.target="processFrame";
        document.form_del.submit();
    }
}

function Add() {
	location.href="cscenter_notice_write.php";
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : CS관리  &gt; CS관리 &gt; <span>CS공지사항</span></p></div></div>
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
			<?php include("menu_cscenter.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">CS공지사항</div>
					
				</td>
			</tr>
			<form name="sForm" method="post">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">CS공지사항 검색</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>구분</span></th>
					<td>
					<select name="notice_category" class="select">
					<option value="">전체</option>
					<option value="1" <?=$selected["notice_category"]['1']?>>공통1</option>
					<option value="2" <?=$selected["notice_category"]['2']?>>공통2</option>
					</select>
					</td>
				</tr>
				<tr>
					<th><span>게시여부</span></th>
					<td>
					<select name="viewyn" class="select">
					<option value="">전체</option>
					<option value="Y" <?=$selected["viewyn"]['Y']?>>게시</option>
					<option value="N" <?=$selected["viewyn"]['N']?>>미게시</option>
					</select>
					</td>
				</tr>
				<tr>
					<th><span>매장선택</span></th>
					<TD>
					<select name="store_no">
						<option value=''>전체</option>
						<?while($store_data=pmysql_fetch_array($store_result)){?>
							<option value="<?=$store_data['sno']?>" <?=$selected["store_no"][$store_data['sno']]?>><?=$store_data["name"]?></option>
						<?}?>
					</select>
					</TD>
				</tr>
				<tr>
					<th><span>검색조건</span></th>
					<td>
					<select name="s_check" class="select" style="width:100px;height:32px;vertical-align:middle;">
					<option value="title" <?php if($s_check=="title")echo"selected";?>>제목으로 검색</option>
					<option value="content" <?php if($s_check=="content")echo"selected";?>>내용으로 검색</option>
					</select>
					<!--  
					<input type=text name=search value="<?=str_replace("''", "'", $search)?>" class="w200">
					-->
					<textarea rows="2" cols="10" class="w200" name="search" id="search" style="resize:none;vertical-align:middle;"><?=$search?></textarea>
					</td>
				</tr>
				
				</table>
				</div>
				</td>
			</tr>
			</form>
			<tr>
				<td colspan=8 align=center><a href="javascript:Searchlb();"><img src="images/btn_search01.gif"></a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])==0?'1':ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
        		<form name="pageForm" method="post">
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="60"></col>
				<col width="140"></col>
				<col width=""></col>	
				<col width="200"></col>
				<col width="100"></col>
				<col width="100"></col>		
				<col width="60"></col>
				<col width="60"></col>
				
				<TR align=center>
					<th>번호</th>
					<th>구분</th>
					<th>제목</th>
					<th>매장명</th>
					<th>게시여부</th>
					<th>작성일</th>
					<th>수정</th>
					<th>삭제</th>	
				</TR>

<?php
#---------------------------------------------------------------
# 리스트를 불러온다.
#---------------------------------------------------------------

		if($t_count>0) {

			#공지사항을 먼저 노출시키기위한 UNION 2016-09-27
			$sql = "SELECT no, title, content, '1' as notice, notice_date as sort_date, regdt, notice_category, viewyn, store_no FROM tblcsnotice where notice_type='Y'";
			if($where)$sql.=' and '.implode(" and ", $where);
			$sql.=" union ";
			$sql.="select no, title, content, '2' as notice, regdt as sort_date, regdt, notice_category, viewyn, store_no from tblcsnotice where notice_type='N'";
			if($where)$sql.=' and '.implode(" and ", $where);
			$sql.= " ORDER BY notice asc, sort_date desc";
			$sql = $paging->getSql($sql);
			//exdebug($sql);
			$result=pmysql_query($sql,get_db_conn());

			$i=0;
			while($row=pmysql_fetch_object($result)) {
				list($store_name)=pmysql_fetch("select name from tblstore where sno='".$row->store_no."'");
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				$reg_date	= substr($row->regdt,0,4)."-".substr($row->regdt,4,2)."-".substr($row->regdt,6,2);

				echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				echo "	<td align=center>".$number."</td>\n";
				echo "	<td align=center>".$notice_type[$row->notice]."</td>\n";
				echo "	<td style='text-align:left'>".$row->title."</td>\n";
				echo "	<td align=center>".$store_name."</td>\n";
				echo "	<td align=center>".$display[$row->viewyn]."</td>\n";
				echo "	<td align=center>".$reg_date."</td>\n";
				echo "	<td align=center><A HREF=\"javascript:Modify({$row->no})\"><img src=\"images/btn_edit.gif\"></A></td>\n";
				echo "	<td align=center><A HREF=\"javascript:Delete({$row->no})\"><img src=\"images/btn_del.gif\"></A></td>\n";
				echo "</tr>\n";
				$i++;
			}
			pmysql_free_result($result);
		} else {
			echo "<tr><td colspan=11 align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
		}
// 		exdebug($sql);
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align=right>
					<a href="javascript:Add()"><img src="images/btn_badd2.gif" border="0"></a>
				</td>
			</tr>
			<tr>
			<td>
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
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>

            <input type=hidden name='mode' value='<?=$mode?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='viewyn' value='<?=$viewyn?>'>
			<input type=hidden name='notice_category' value='<?=$notice_category?>'>
			<input type=hidden name='store_no' value='<?=$store_no?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
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

<form name="form_del" action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name='mode'>
<input type=hidden name="no">
</form>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php"); // 하단부분을 불러온다. 
?>
