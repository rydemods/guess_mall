<?php // hspark
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

$scheck         = $_REQUEST["scheck"];
$search         = $_REQUEST["search"];
$reply_status   = $_REQUEST["reply_status"];

$mode=$_POST["mode"];
$up_personal=$_POST["up_personal"];
$idxs=$_POST["idxs"];

if($mode=="update" && ord($up_personal)) {
	$sql = "UPDATE tblshopinfo SET personal_ok='{$up_personal}' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert(\"1:1 고객 게시판 사용여부 설정이 변경되었습니다.\");}</script>";
} elseif($mode=="delete" && ord($idxs)) {
	$sql = "DELETE FROM tblpersonal WHERE idx IN ({$idxs}) ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert(\"선택하신 게시물을 삭제하였습니다.\");}</script>";
}

$sql = "SELECT personal_ok FROM tblshopinfo ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
pmysql_free_result($result);

$personal_ok=$row->personal_ok;
include("header.php");
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function Search(form) {
	if(form.search.value.length==0) {
		alert("검색어를 입력하세요.");
		form.search.focus();
		return;
	}

	form.submit();
}

function search_default(){
	form2.scheck.value = "";
	form2.search.value = "";
    $("#idx_reply_status0").attr("checked", true);
	form2.submit();
}

function CheckAll(){
	chkval=document.form2.allcheck.checked;
	try {
		cnt=document.form2.delcheck.length;
		for(i=1;i<=cnt;i++){
			document.form2.delcheck[i].checked=chkval;
		}
	} catch(e) {}
}

function CheckDelete(form) {
	try {
		idxs="";
		for(i=1;i<form.delcheck.length;i++) {
			if(form.delcheck[i].checked) {
				idxs+=","+form.delcheck[i].value;
			}
		}
		if(idxs.length==0) {
			alert("삭제할 게시물을 선택하세요.");
			return;
		}
		if(confirm("선택하신 게시물을 삭제하시겠습니까?")) {
			idxs=idxs.substring(1,idxs.length);
			form.mode.value="delete";
			form.idxs.value=idxs;
			form.submit();
		}
	} catch(e){}
}

function update_submit(form) {
	form.mode.value='update';
	form.submit();
}

function Search_id(id) {
	document.form2.scheck.selectedIndex=0;
	document.form2.search.value=id;
	document.form2.submit();
}

function ViewPersonal(idx) {
	window.open("about:blank","personal_pop","width=780,height=570,scrollbars=yes");
	document.form3.idx.value=idx;
	document.form3.submit();
}

function selectReplyStatus(obj) {
//    document.form2.reply_status.value = $(obj).val();
    document.form2.submit();
}


//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>1:1고객 게시판 관리</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">1:1 고객 게시판 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>1:1 고객문의 게시판 설정 및 문의에 대한 답변 관리를 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="title_depth3_sub">1:1 고객 게시판 사용여부
					</div>                                                  
				</td>
            </tr>
            <tr>
            	<td style="padding-top:5; padding-bottom:5">
                <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) 1:1 고객 게시판은 질문한 고객과 쇼핑몰 운영자만 볼 수 있는 게시판입니다.</li>
                            <li>2) 1:1 고객 게시판은 회원제로 운영되며, MY페이지에서 확인 가능합니다.</li>
                        </ul>
                    </div> 
                </td>
            </tr>
            <tr>
            	<td>
				<div class="table_style01">
					<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<th><span>1:1 고객 게시판 사용</span></th>
							<td>
								<INPUT id=idx_personal0 type=radio value=Y name=up_personal <?php if($personal_ok=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_personal0>1:1 고객 게시판 사용</LABEL>&nbsp;&nbsp;
								<INPUT id=idx_personal1 type=radio value=N name=up_personal <?php if($personal_ok=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_personal1>1:1 고객 게시판 사용안함</LABEL>
							</td>
						</tr>
					</table>
				</div>
				</td>
			</form>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:update_submit(document.form1);"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr><td height=10></td></tr>
            <tr>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=idxs>

            	<td>
				<div class="table_style01">
					<table WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0>
						<tr>
							<th><span>답변여부</span></th>
							<td>
								<INPUT onChange="javascript:selectReplyStatus(this);" id="idx_reply_status0" type=radio value="" name="reply_status" <?php if($reply_status=="")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_personal0>전체보기</LABEL>&nbsp;&nbsp;
								<INPUT onChange="javascript:selectReplyStatus(this);" id="idx_reply_status1" type=radio value="N" name="reply_status" <?php if($reply_status=="N")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_reply_status1>답변 전</LABEL>&nbsp;&nbsp;
								<INPUT onChange="javascript:selectReplyStatus(this);" id="idx_reply_status2" type=radio value="Y" name="reply_status" <?php if($reply_status=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none"><LABEL onmouseover="style.textDecoration='underline'" style="CURSOR: hand; TEXT-DECORATION: none" onmouseout="style.textDecoration='none'" for=idx_reply_status2>답변 완료</LABEL>&nbsp;&nbsp;
							</td>
						</tr>
                        <tr>
							<th><span>검색</span></th>
                            <td>
                            <table cellpadding="0" cellspacing="0" bgcolor="#DBDBDB" align="left">
                            <tr>
                                <td align=center bgcolor="white">
                                <SELECT name=scheck class="select" style="width:80px;height:32px;vertical-align:middle;">
                                <OPTION value=id <?php if($scheck=="id")echo"selected";?>>아이디</OPTION>
                                <OPTION value=name <?php if($scheck=="name")echo"selected";?>>이 름</OPTION>
                                </SELECT>
                                <!--  
                                <INPUT type="text" name=search value="<?=$search?>" class="input">
                                -->
                                <textarea rows="2" cols="10" class="w200" name="search" id="search" style="resize:none;vertical-align:middle;"><?=$search?></textarea>
                                <A href="javascript:Search(document.form2);"><img src="images/icon_search.gif" alt=검색 align=absMiddle border=0></a>
                                <A href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align=absMiddle border=0  hspace="2"></A>
                                </td>
                            </tr>
                            </table>
                            </td>
                        </tr>
					</table>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			</table>
<?php
			$colspan=6;
            $arrWhere = array();
            if ( $reply_status != "" ) {
                if ( $reply_status == "Y" ) {
                    array_push($arrWhere, "length(re_date) = 14");
                } elseif ( $reply_status == "N" ) {
                    array_push($arrWhere, "re_date is null");
                }
            }

			if(ord($scheck) && ord($search)) {
				$search = trim($search);
				$temp_search = explode("\r\n", $search);
				$cnt = count($temp_search);
				
				$search_arr = array();
				for($i = 0 ; $i < $cnt ; $i++){
					array_push($search_arr, "'%".$temp_search[$i]."%'");
					//$t_search .= $temp_search[$i]."\r\n";
				}
                array_push($arrWhere, "{$scheck} LIKE any ( array[".implode(",", $search_arr)."] ) ");
                //$sql.= "WHERE {$scheck} LIKE '%{$search}%' ";
            }

			$sql = "SELECT COUNT(*) as t_count FROM tblpersonal ";
            if ( count($arrWhere) > 0 ) {
                $sql .= "WHERE " . implode(" AND ", $arrWhere);
            }
            //exdebug($sql);

			$paging = new Paging($sql,10,15,"{$_SERVER['PHP_SELF']}?scheck={$scheck}&search={$search}&reply_status=${reply_status}");
			$t_count = $paging->t_count;	
			$gotopage = $paging->gotopage;		
?>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">1:1 고객 게시판 게시물 목록</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td>&nbsp;</td>
					<td align=right><img src="images/icon_8a.gif"  border="0">총 게시물 : <B><?=number_format($t_count)?></B>건, &nbsp; <img src="images/icon_8a.gif"  border="0">현재 <B><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></B> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40></col>
				<col width=40></col>
				<col width=></col>
				<col width=100></col>
				<col width=120></col>
				<col width=120></col>
				<col width=85></col>
				<tr>
					<th><INPUT onclick=CheckAll() type=checkbox name=allcheck></th>
					<th>NO</th>
					<th>제목</th>
					<th>회원명</th>
					<th>고객 등록 날짜</th>
					<th>답변 등록 날짜</th>
					<th>답변여부</th>
				</tr>
				<input type=hidden name=delcheck>
<?php
				$sql = "SELECT idx,id,name,email,ip,subject,date,re_date FROM tblpersonal ";
                if ( count($arrWhere) > 0 ) {
                    $sql .= "WHERE " . implode(" AND ", $arrWhere);
                }
//				if(ord($scheck) && ord($search)) $sql.= "WHERE {$scheck} LIKE '%{$search}%' ";
				$sql.= " ORDER BY idx DESC ";
				$sql = $paging->getSql($sql);
//                exdebug($sql);

				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					if($date){
						$date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."(".substr($row->date,8,2).":".substr($row->date,10,2).")";
					}else{
						$date = "-";
					}
					if($row->re_date){
						$re_date = substr($row->re_date,0,4)."/".substr($row->re_date,4,2)."/".substr($row->re_date,6,2)."(".substr($row->re_date,8,2).":".substr($row->re_date,10,2).")";
					}else{
						$re_date = "-";
					}
					echo "<TR>\n";
					echo "	<TD><input type=checkbox name=delcheck value=\"{$row->idx}\"></TD>\n";
					echo "	<TD>{$number}</TD>\n";
					echo "	<TD><div class=\"ta_l\">&nbsp;<A HREF=\"javascript:ViewPersonal('{$row->idx}');\">".strip_tags($row->subject)."</A></div></TD>\n";
					echo "	<TD><A HREF=\"javascript:Search_id('{$row->id}');\">{$row->name}</A></TD>\n";
					echo "	<TD>{$date}</TD>\n";
					echo "	<TD>{$re_date}</TD>\n";
					echo "	<TD>";
					if(strlen($row->re_date)==14) {
						echo "<img src=\"images/icon_finish.gif\"  border=\"0\">";
					} else {
						echo "<img src=\"images/icon_nofinish.gif\"  border=\"0\">";
					}
					echo "	</TD>\n";
					echo "</TR>\n";
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center height=30>검색된 정보가 존재하지 않습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td><a href="javascript:CheckDelete(document.form2);"><img src="images/btn_del2.gif"  border="0" vspace="3" hspace="3"></a></td>
			</tr>
			<tr>
				<td colspan=<?=$colspan?> align=center style='font-size:11px;'>
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" class="main_sfont_non">&nbsp;</td>
				</tr>
				<!--tr>
					<td width="100%" class="main_sfont_non">
					<table cellpadding="10" cellspacing="1" bgcolor="#DBDBDB" align="center">
					<tr>
						<td align=center bgcolor="white">
						<SELECT name=scheck class="select">
						<OPTION value=id <?php if($scheck=="id")echo"selected";?>>아이디</OPTION>
						<OPTION value=name <?php if($scheck=="name")echo"selected";?>>이 름</OPTION>
						</SELECT>
						<INPUT type="text" name=search value="<?=$search?>" class="input">
						<A href="javascript:Search(document.form2);"><img src="images/icon_search.gif" alt=검색 align=absMiddle border=0></a>
						<A href="javascript:search_default();"><IMG src="images/icon_search_clear.gif" align=absMiddle border=0  hspace="2"></A>
						</td>
					</tr>
					</table>
					</td>
				</tr-->
				</form>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>1:1 고객 게시판 관리</span></dt>
							<dd>
								- 제목 클릭시 질문에 대한 답변을 할 수 있으며, 답변 처리된 항목은 답변완료로 표기됩니다.<br>
								- 답변내용 재수정이 가능하며, 재수정시 재수정일 기준으로 답변날짜가 갱신됩니다.<br>
								- 회원명을 클릭하면 해당 회원의 아이디로 검색됩니다.<br>
								- 1:1 고객 게시판 미사용시 고객은 문의글 등록이 되지 않으며, 설정 변경시 재 적용됩니다.(미사용 체크시에도 기존 등록 내용은 유지됩니다.)
							</dd>
						</dl>
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
<form name=form3 action="community_personal_pop.php" method=post target="personal_pop">
<input type=hidden name=idx>
</form>
</table>
<?=$onload?>
<?php 
include("copyright.php");
