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
	$PageCode = "co-6";
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

	if ( $mode == "delete" ) {

		$imagepath			= $Dir.DataDir."shopimages/lookbook/";
		$upload_del_file     = new FILE($imagepath);

		$sql  = "SELECT * FROM tbllookbook WHERE no = '".$_POST["no"]."' ";
		$row  = pmysql_fetch_object(pmysql_query($sql));
		
		for ( $i = 0; $i < 1; $i++ ) {
			if ( $i == 0 ) {
				$up_rFile = $row->img;
				$up_mFile = $row->img_m;
				$up_sFile = $row->img_file;
				$up_smFile = $row->img_m_file;
			} else {
				$fieldName = "img" . ($i+1);
				$fieldMName = "img_m" . ($i+1);
				$fieldSName = "img_file" . ($i+1);
				$fieldSmName = "img_m_file" . ($i+1);
				$up_rFile = $row->$fieldName;
				$up_mFile = $row->$fieldMName;
				$up_sFile = $row->$fieldSName;
				$up_smFile = $row->$fieldSmName;
			}

			if ( $up_rFile !="") {
				$upload_del_file->removeFile($up_rFile);
			}else if($up_mFile != ""){
				$upload_del_file->removeFile($up_mFile);
			}else if($up_sFile != ""){
				$upload_del_file->removeFile($up_sFile);
			}else if($up_smFile != ""){
				$upload_del_file->removeFile($up_smFile);
			}
		}
		
		$qry = "DELETE FROM tbllookbook WHERE no ='".$_POST["no"]."'";
		pmysql_query( $qry, get_db_conn() );
		//exdebug($sql);
		callNaver('lookbook', $_POST["no"], 'del');
		echo "<html></head><body onload=\"alert('삭제가 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
	}

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/lookbook/";

#---------------------------------------------------------------
# 검색부분을 정리한다.
#---------------------------------------------------------------
	$qry = "WHERE 1=1 ";
	if(ord($search)) {
// 		$tmpSearch = strtoupper($search);
// 		if ($s_check == 'title') {
// 			$qry.= "AND ( UPPER(title) LIKE '%{$tmpSearch}%' ) ";
// 		} else if ($s_check == 'content') {
// 			$qry.= "AND ( UPPER(content) LIKE '%{$tmpSearch}%' ) ";
// 		}

		$search = trim($search);
		$temp_search = explode("\r\n", $search);
		$cnt = count($temp_search);
		
		$search_arr = array();
		for($i = 0 ; $i < $cnt ; $i++){
			array_push($search_arr, "'%".$temp_search[$i]."%'");
		}
		
		if ($s_check == 'title') {
			$qry.= "AND ( title LIKE any ( array[".implode(",", $search_arr)."] ) ) ";
		} else if ($s_check == 'content') {
			$qry.= "AND ( content LIKE any ( array[".implode(",", $search_arr)."] ) ) ";
		}
	}
	include("header.php");  // 상단부분을 불러온다.

	
#---------------------------------------------------------------
# 검색쿼리 카운트 및 페이징을 정리한다.
#---------------------------------------------------------------
	$listnum = 20;

	$sql = "SELECT COUNT(*) as t_count FROM  tbllookbook {$qry} ";
	$paging = new newPaging($sql,10,$listnum,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;		
	//exdebug($sql);
	
#노출 세팅
$display['N'] = '비노출';
$display['Y'] = '노출';
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
	location.href="community_lookbook_write.php?&mode=modfiy_select&no="+no;
}

function Delete(no) {
    if( confirm("삭제하시겠습니까?") ) {
        document.form_del.mode.value= "delete";
        document.form_del.no.value=no;
		document.form_del.target="processFrame";
        document.form_del.submit();
    }
}

function allCheck(obj) {
    if ( $(obj).is(":checked") ) {
        $("input:checkbox[name='idx[]']").attr("checked", true);
    } else {
        $("input:checkbox[name='idx[]']").attr("checked", false);
    }
}

function Add() {
	location.href="community_lookbook_write.php";
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; LOOKBOOK 관리 &gt; <span>LOOKBOOK 정보관리</span></p></div></div>
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
					<div class="title_depth3">LOOKBOOK 정보관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>LOOKBOOK의 정보를 수정/삭제 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<form name="sForm" method="post">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">LOOKBOOK 검색 선택</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>검색</span></th>
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
				<!--col width="60"></col-->
				<col width="60"></col>
				<col width="140"></col>
				<col width="140"></col>
				<col width=""></col>	
				<col width="40"></col>
				<col width="100"></col>		
				<col width="60"></col>
				<col width="60"></col>
				<col width="60"></col>
				<TR align=center>
					<!--th><input type='checkbox' onClick='javascript:allCheck(this);'></th-->
					<th>번호</th>
					<th>이미지(pc)</th>
					<th>이미지(mobile)</th>
					<th>내용</th>
					<th>노출</th>
					<th>등록일</th>				
					<th>수정</th>
					<th>삭제</th>	
				</TR>

<?php
#---------------------------------------------------------------
# 리스트를 불러온다.
#---------------------------------------------------------------

		if($t_count>0) {
			$sql = "SELECT * FROM tbllookbook {$qry} ";
			$sql.= " ORDER BY brandcd,no desc";
			$sql = $paging->getSql($sql);
			$result=pmysql_query($sql,get_db_conn());

			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				if( is_file($imagepath.$row->img_file) ){ 
					$up_img	= "<img src='".$imagepath.$row->img_file."' style='max-width: 70px; max-height: 100px;' />";
					$up_m_img	= "<img src='".$imagepath.$row->img_m_file."' style='max-width: 70px; max-height: 100px;' />";
				} else {
					$up_img	= "-";
					$up_m_img	= "-";
				}

				$reg_date	= substr($row->regdate,0,4)."-".substr($row->regdate,4,2)."-".substr($row->regdate,6,2);

				$Row_content = stripslashes($row->content);

				// <br>태그 제거
				$arrList = array("/<br\/>/", "/<br>/");
				$Row_content_tmp = trim(preg_replace($arrList, "", $Row_content));

				if ( !empty($Row_content_tmp) ) {
						$Row_content	= str_replace(" ","&nbsp;",nl2br($Row_content));
						$Row_content	= str_replace("<p>","<div>",$Row_content);
						$Row_content	= str_replace("</p>","</div>",$Row_content);
				}

				list($c_count) = pmysql_fetch("SELECT COUNT(*) as c_count FROM tblmagazine_comment WHERE mnum = '".$row->no."' ");

				echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				//echo "	<td align=center><input type='checkbox' name='idx[]' value='" . $row->sno . "' /></td>\n";
				echo "	<td align=center>{$number}</td>\n";
				echo "	<td align=center>{$up_img}</td>\n";
				echo "	<td align=center>{$up_m_img}</td>\n";
				echo "	<td style='text-align:left'><b>".$row->title."</b><br><hr>".$Row_content."</td>\n";
				echo "	<td align=center>".$display[$row->display]."</td>\n";
				echo "	<td align=center>{$reg_date}</td>\n";
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
					<!--a href="javascript:lbEdit()"><img src="images/btn_edit1.gif" border="0"></a-->
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
						
						<dl>
							<dt><span>LOOKBOOK 정보관리</span></dt>
							<dd>- 등록된 LOOKBOOK 리스트와 기본적인 정보사항을 확인할 수 있습니다.
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>

            <input type=hidden name='mode' value='<?=$mode?>'>
			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
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
