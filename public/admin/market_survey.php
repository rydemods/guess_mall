<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-1";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$mode=$_POST["mode"];
$survey_code=$_POST["survey_code"];
$up_display=$_POST["up_display"];
$up_survey_content=$_POST["up_survey_content"];
$up_survey_select1=$_POST["up_survey_select1"];
$up_survey_select2=$_POST["up_survey_select2"];
$up_survey_select3=$_POST["up_survey_select3"];
$up_survey_select4=$_POST["up_survey_select4"];
$up_survey_select5=$_POST["up_survey_select5"];
$up_ip_yn=$_POST["up_ip_yn"];
$up_grant_write=$_POST["up_grant_write"];
$up_grant_comment=$_POST["up_grant_comment"];
$currentdate=date("YmdHis");

if($type=="insert" && ord($up_survey_content)) {
	$grant_type = $up_grant_write.$up_grant_comment;

	$sql = "UPDATE tblsurveymain SET display = 'N' WHERE display = 'Y' ";
	pmysql_query($sql,get_db_conn());

	$sql = "INSERT INTO tblsurveymain(
	survey_code	,
	time_start	,
	time_end	,
	display		,
	ip_yn		,
	grant_type	,
	survey_content	,
	survey_select1	,
	survey_select2	,
	survey_select3	,
	survey_select4	,
	survey_select5) VALUES (
	'{$currentdate}', 
	'".time()."', 
	'0', 
	'Y', 
	'{$up_ip_yn}', 
	'{$grant_type}', 
	'{$up_survey_content}', 
	'{$up_survey_select1}', 
	'{$up_survey_select2}', 
	'{$up_survey_select3}', 
	'{$up_survey_select4}', 
	'{$up_survey_select5}')";
	$insert=pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('온라인투표 등록이 완료되었습니다.'); }</script>\n";
} else if ($type=="modify" && ord($survey_code)) {
	if ($mode=="result") {
		if($up_display=='Y') {
			$sql = "UPDATE tblsurveymain SET display = 'N' WHERE display = 'Y' ";
			pmysql_query($sql,get_db_conn());
		}
		$grant_type = $up_grant_write.$up_grant_comment;
		$sql = "UPDATE tblsurveymain SET ";
		$sql.= "display			= '{$up_display}', ";
		$sql.= "ip_yn			= '{$up_ip_yn}', ";
		$sql.= "grant_type		= '{$grant_type}', ";
		$sql.= "survey_content	= '{$up_survey_content}', ";
		$sql.= "survey_select1	= '{$up_survey_select1}', ";
		$sql.= "survey_select2	= '{$up_survey_select2}', ";
		$sql.= "survey_select3	= '{$up_survey_select3}', ";
		$sql.= "survey_select4	= '{$up_survey_select4}', ";
		$sql.= "survey_select5	= '{$up_survey_select5}' ";
		$sql.= "WHERE survey_code = '{$survey_code}' ";
		$update=pmysql_query($sql,get_db_conn());
		$onload="<script>window.onload=function(){ alert('온라인투표 수정이 완료되었습니다.'); }</script>\n";
		$type='';
		$mode='';
		$survey_code='';
	} else {
		$sql = "SELECT * FROM tblsurveymain WHERE survey_code='{$survey_code}'";
		$result = pmysql_query($sql,get_db_conn());
		$data = pmysql_fetch_object($result);
		pmysql_free_result($result);
		if (!$data) {
			$onload="<script>window.onload=function(){ alert('수정하려는 투표가 존재하지 않습니다.'); }<script>";
			$type='';
			$survey_code='';
		} else {
			$grant_write=$data->grant_type[0];
			$grant_comment=$data->grant_type[1];
		}
	}
} else if ($type=="delete" && ord($survey_code)) {
	$sql = "DELETE FROM tblsurveymain WHERE survey_code = '{$survey_code}' ";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblsurveyresult WHERE survey_code = '{$survey_code}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert('해당 온라인투표 삭제가 완료되었습니다.'); }</script>\n";
	$type='';
	$survey_code='';
}

if (ord($type)==0) $type="insert";
if (ord($grant_write)==0) $grant_write="Y";
if (ord($grant_comment)==0) $grant_comment="Y";
if (ord($data->ip_yn)==0) $data->ip_yn="N";

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(document.form1.up_survey_content.value.length==0) {
		document.form1.up_subject.focus();
		alert("투표 제목을 입력하세요");
		return;
	}
	if(type=="modify") {
		if(!confirm("해당 투표를 수정하시겠습니까?")) {
			return;
		}
		document.form1.mode.value="result";
	} else if (type=="insert") {
		if(!confirm("온라인투표를 등록하시겠습니까?")) {
			return;
		}
	}
	document.form1.type.value=type;
	document.form1.submit();
}
function SurveySend(type,code) {
	if(type=="delete") {
		if(!confirm("해당 투표를 삭제하시겠습니까?")) return;
	}
	document.form1.type.value=type;
	document.form1.survey_code.value=code;
	document.form1.submit();
}
function ViewSurvey(code) {
	var url;
	url="<?=$Dir.FrontDir?>survey.php?type=view&survey_code="+code;
	window.open (url,"survey","width=450,height=400,scrollbars=yes");
}
function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 마케팅지원 &gt;<span>온라인투표 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=mode>
			<input type=hidden name=survey_code value="<?=$survey_code?>">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">온라인투표 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>온라인투표 관리메뉴 등록/수정/삭제 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">등록된 온라인투표 리스트</div>
				</td>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=160></col>
				<col width=></col>
				<col width=50></col>
				<col width=60></col>
				<col width=60></col>
				<col width=60></col>
				<TR align=center>
					<th>등록일자</th>
					<th>투표제목</th>
					<th>투표수</th>
					<th>진행여부</th>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan=6;
				$sql = "SELECT COUNT(*) as t_count FROM tblsurveymain ";
				$paging = new Paging($sql,10,20);
				$t_count = $paging->t_count;
				$gotopage = $paging->gotopage;

				$sql = "SELECT * FROM tblsurveymain ORDER BY survey_code DESC ";
				$sql = $paging->getSql($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$str_date = substr($row->survey_code,0,4)."/".substr($row->survey_code,4,2)."/".substr($row->survey_code,6,2)." ".substr($row->survey_code,8,2).":".substr($row->survey_code,10,2).":".substr($row->survey_code,12,2);
					$sel_tot=$row->survey_cnt1+$row->survey_cnt2+$row->survey_cnt3+$row->survey_cnt4+$row->survey_cnt5;
					if ($row->display=="Y") $display="<span class=\"font_orange\"><b>진행중</b></span>";
					else $display="종료";
					echo "<TR>\n";
					echo "	<TD>{$str_date}</TD>\n";
					echo "	<TD><div class=\"ta_l\"><A HREF=\"javascript:ViewSurvey('{$row->survey_code}');\">{$row->survey_content}</A></div></TD>\n";
					echo "	<TD>{$sel_tot}</TD>\n";
					echo "	<TD>{$display}</TD>\n";
					echo "	<TD><a href=\"javascript:SurveySend('modify','{$row->survey_code}');\"><img src=\"images/btn_edit.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:SurveySend('delete','{$row->survey_code}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";

					$cnt++;
				}
				pmysql_free_result($result);

				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>등록된 온라인투표가 존재하지 않습니다..</td></tr>";
				}
?>

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align=center class="font_size">
					<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">온라인투표 등록/수정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>투표제목</span></th>
					<TD><INPUT style="WIDTH:60%" name=up_survey_content class="input" value="<?=$data->survey_content?>"></TD>
				</TR>
				<TR>
					<th><span>보기1</span></th>
					<TD><INPUT style="WIDTH:40%" name=up_survey_select1 class="input" value="<?=$data->survey_select1?>"></TD>
				</TR>
				<TR>
					<th><span>보기2</span></th>
					<TD><INPUT style="WIDTH:40%" name=up_survey_select2 class="input" value="<?=$data->survey_select2?>"></TD>
				</TR>
				<TR>
					<th><span>보기3</span></th>
					<TD><INPUT style="WIDTH:40%" name=up_survey_select3 class="input" value="<?=$data->survey_select3?>"></TD>
				</TR>
				<TR>
					<th><span>보기4</span></th>
					<TD><INPUT style="WIDTH:40%" name=up_survey_select4 class="input" value="<?=$data->survey_select4?>"></TD>
				</TR>
				<tr>
					<th><span>보기5</span></th>
					<TD><INPUT style="WIDTH:40%" name=up_survey_select5 class="input" value="<?=$data->survey_select5?>"></TD>
				</tr>
				<tr>
					<th><span>IP 공개여부</span></th>
					<TD>
					<INPUT type=radio value=Y name=up_ip_yn <?php if($data->ip_yn=="Y") echo "checked" ?>>코멘트 작성자 IP 공개
					&nbsp;&nbsp;&nbsp;&nbsp;
					<INPUT type=radio value=N name=up_ip_yn <?php if($data->ip_yn=="N") echo "checked" ?>>코멘트 작성자 IP 숨김
					</TD>
				</tr>
				<tr>
					<th><span>투표 접근권한</span></th>
					<TD>
					설문작성 : 
					<SELECT name=up_grant_write class="select">
					<OPTION value=Y <?php if($grant_write=="Y") echo "selected"?>>누구나 가능</OPTION>
					<OPTION value=N <?php if($grant_write=="N") echo "selected"?>>회원만 가능</OPTION>
					</SELECT>
					&nbsp;&nbsp;&nbsp; 코멘트작성 : 
					<SELECT name=up_grant_comment class="select">
					<OPTION value=Y <?php if($grant_comment=="Y") echo "selected"?>>누구나 가능</OPTION>
					<OPTION value=N <?php if($grant_comment=="N") echo "selected"?>>회원만 가능</OPTION>
					</SELECT>
					
					</TD>
				</tr>
				<?php if($type=="modify"){?>
				<tr>
					<th><span>투표 진행여부</span>
					<TD>
					<INPUT type=radio value=Y name=up_display <?php if($data->display=="Y") echo "checked" ?>>투표를 진행합니다.
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					<INPUT type=radio value=N name=up_display <?php if ($data->display=="N") echo "checked" ?>>투표 진행을 중단합니다.
					</TD>
				</tr>
				<?php }?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('<?=$type?>');"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>온라인투표 관리</span></dt>
							<dd>
								- 온라인 투표는 메인화면 템플릿에서 메인 우측에 기본으로 출력되게 설정돼 있습니다.<br>
								- 온라인 투표 중단은 해당 투표의 수정모드에서 투표 진행여부를 선택하면 됩니다.<br>
								- 진행되지 않는 온라인 투표는 되도록 삭제 하세요.
							</dd>
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
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
