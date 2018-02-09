<?php
/********************************************************************* 
// 파 일 명		: design_lookbooklist.php
// 설     명		: LOOKBOOK 리스트 관리
// 상세설명	: LOOKBOOK 리스트 관리
// 작 성 자		: 2016.01.22 - 김재수
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

##################### 페이지 접근권한 check #####################
	$PageCode = "de-5";
	$MenuCode = "design";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$mode           = $_POST["mode"];
	$s_check        = $_POST["s_check"];
	$search         = pg_escape_string($_POST["search"]);
    $search_hidden  = $_POST['search_hidden'];

	// 이미지 경로
	$imagepath = $Dir.DataDir."shopimages/lookbook/";

#---------------------------------------------------------------
# 검색부분을 정리한다.
#---------------------------------------------------------------
	$qry = "WHERE 1=1 ";
	if(ord($search)) {
        $tmpSearch = strtoupper($search);
        $qry.= "AND ( UPPER({$s_check}) LIKE '%{$tmpSearch}%' ) ";
	}

    if( !is_null($search_hidden) && $search_hidden != "" ) {
        $qry.= " AND hidden = ${search_hidden} ";
    }

	include("header.php");  // 상단부분을 불러온다.

    $idx            = $_POST['idx'];
    $visible_mode   = $_POST['visible_mode'];

    if ( count($idx) >= 1 ) {
        $whereIdx = implode(",", $idx);
        $sql  = "UPDATE tbllookbook SET hidden = {$visible_mode} WHERE no in ( " . $whereIdx . " ) ";
        pmysql_query($sql, get_db_conn());
    }

#---------------------------------------------------------------
# 검색쿼리 카운트 및 페이징을 정리한다.
#---------------------------------------------------------------
	$sql = "SELECT COUNT(*) as t_count FROM tbllookbook {$qry} ";
	$paging = new Paging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;		
	
	#기본 세팅
	$display['0'] = '비노출';
	$display['1'] = '노출';
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

function lbModify(lbno) {
	location.href="design_lookbook_write.php?mode=modfiy_select&lbno="+lbno;
}

function lbAdd() {
	location.href="design_lookbook_write.php";
}

function allCheck(obj) {
    if ( $(obj).is(":checked") ) {
        $("input:checkbox[name='idx[]']").attr("checked", true);
    } else {
        $("input:checkbox[name='idx[]']").attr("checked", false);
    }
}

function changeVisible(val) {
    // val : 1 => 노출, 0 => 비노출

    if ( $("input[name='idx[]']:checked").length == 0 ) {
        alert('하나 이상을 선택해 주세요.');
    } else {
        if ( val == "1" ) {
            msg = "노출 설정 하시겠습니까?";
        } else {
            msg = "비노출 설정 하시겠습니까?";
        }

        if ( confirm(msg) ) {
            document.pageForm.visible_mode.value = val;
            document.pageForm.submit();
        }
    }
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 배너관리 &gt; LOOKBOOK 정보관리 &gt;<span>LOOKBOOK 정보관리</span></p></div></div>
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
					<select name="s_check" class="select">
					<option value="title" <?php if($s_check=="title")echo"selected";?>>제목으로 검색</option>
					<option value="subtitle" <?php if($s_check=="subtitle")echo"selected";?>>상세 텍스트로 검색</option>
					</select>
                    <input type=text name=search value="<?=str_replace("''", "'", $search)?>" class="w200">
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
			<tr>
				<td>
                    <table cellpadding="0" cellspacing="0" width="100%" border="0">
                    <col width="10%"></col>
                    <col width=></col>
                    <tr>
                        <td>
                            <!-- 소제목 -->
                            <div class="title_depth3_sub">검색된 목록</div>
                        </td>
                        <td align="right">
                            <div style="margin:20px 0 5px; align: left;">
                            사용 :
                            <select name="search_hidden" onChange="javascript:changeSelectHidden(this);">
                                <option value=""  <?php if ($search_hidden == "") echo "selected"; ?>>========전체=======</option>
                                <option value="1" <?php if ($search_hidden == "1") echo "selected"; ?>>노출</option>
                                <option value="0" <?php if ($search_hidden == "0") echo "selected"; ?>>비노출</option>
                            </select>

                            </div>
                        </td>
                    </tr>
                    </table>
                </td>
			</tr>
			<!--tr><td height=20></td></tr-->
			<tr>
			    <form name="pageForm" method="post">
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width="60"></col>
				<col width="60"></col>
				<col width="160"></col>
				<col width=""></col>
				<col width="120"></col>			
				<col width="45"></col>
				<col width="120"></col>
				<TR align=center>
                    <th><input type='checkbox' onClick='javascript:allCheck(this);'></th>
					<th>번호</th>
					<th>이미지</th>
					<th>제목</th>
					<th>상태</th>
					<th>상세</th>
					<th>등록일</th>					
				</TR>

<?php
#---------------------------------------------------------------
# 벤더 정보 리스트를 불러온다.
#---------------------------------------------------------------

		if($t_count>0) {
			$sql = "SELECT * FROM tbllookbook {$qry} ";
			$sql.= " ORDER BY no desc";
			$sql = $paging->getSql($sql);

			$result=pmysql_query($sql,get_db_conn());

			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$i);
				if( is_file($imagepath.$row->img) ){ 
					$lb_img	= "<img src='".$imagepath.$row->img."' style='max-width: 120px;' />";
				} else {
					$lb_img	= "-";
				}

				$reg_date	= substr($row->regdate,0,4)."-".substr($row->regdate,4,2)."-".substr($row->regdate,6,2);

				echo "<tr bgcolor=#FFFFFF onmouseover=\"this.style.background='#FEFBD1'\" onmouseout=\"this.style.background='#FFFFFF'\">\n";
				echo "	<td align=center><input type='checkbox' name='idx[]' value='" . $row->no . "' /></td>\n";
				echo "	<td align=center>{$number}</td>\n";
				echo "	<td align=center>{$lb_img}</td>\n";
				echo "	<td style='text-align:left'>{$row->title}</td>\n";
				echo "	<td align=center>".$display[$row->hidden]."</td>\n";
				echo "	<td align=center><A HREF=\"javascript:lbModify({$row->no})\"><img src=\"images/btn_edit.gif\"></A></td>\n";
				echo "	<td align=center>{$reg_date}</td>\n";
				echo "</tr>\n";
				$i++;
			}
			pmysql_free_result($result);
		} else {
			echo "<tr><td colspan=6 align=center>검색된 정보가 존재하지 않습니다.</td></tr>";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align=right>
					<a href="javascript:changeVisible('1');"><img src="images/btn_visible_set.png" border="0"></a>
					<a href="javascript:changeVisible('0');"><img src="images/btn_visible_unset.png" border="0"></a>
					<a href="javascript:lbAdd()"><img src="images/btn_badd2.gif" border="0"></a>
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

			<input type=hidden name='s_check' value='<?=$s_check?>'>
			<input type=hidden name='search' value='<?=$search?>'>
			<input type=hidden name='search_hidden' value='<?=$search_hidden?>'>
			<input type=hidden name='block' value='<?=$block?>'>
			<input type=hidden name='gotopage' value='<?=$gotopage?>'>
			<input type=hidden name='visible_mode' value='<?=$visible_mode?>'>
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

<script type="text/javascript">
    function changeSelectHidden(obj) {
        var hiddenVal = $(obj).children("option:selected").val();
        
        document.pageForm.search_hidden.value = hiddenVal;
        document.pageForm.submit();
    }
</script>

<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php"); // 하단부분을 불러온다. 
