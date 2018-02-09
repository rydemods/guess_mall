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

$max=20;
$type=$_POST["type"];
$partner_id=$_POST["partner_id"];
$up_url=$_POST["up_url"];
$up_id=$_POST["up_id"];
$up_passwd=$_POST["up_passwd"];

$onload='';
if($type=="insert" && ord($up_url) && ord($up_id) && ord($up_passwd)) {
	$sql = "SELECT COUNT(*) as cnt FROM tblpartner ";
	$result = pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$partner_cnt=$row->cnt;
	pmysql_free_result($result);
	if($partner_cnt<$max) {
		if (!preg_match("/^[[:alnum:]]+$/", $up_id)) {
			alert_go('ID는 영문/숫자만 입력 가능합니다.',-1);
		} else {
			$sql = "SELECT COUNT(*) as cnt FROM tblpartner ";
			$sql.= "WHERE id = '{$up_id}' ";
			$result = pmysql_query($sql,get_db_conn());
			$row=pmysql_fetch_object($result);
			if ($row->cnt!=0) {
				alert_go('관리ID가 중복되었습니다.',-1);
			}
			pmysql_free_result($result);
		}
		if (ord($onload)==0) {
			$sql = "INSERT INTO tblpartner(
			id		,
			passwd		,
			url		,
			hit_cnt		,
			authkey) VALUES (
			'{$up_id}', 
			'{$up_passwd}', 
			'{$up_url}', 
			0, 
			'')";
			$insert = pmysql_query($sql,get_db_conn());
			if ($insert) $onload="<script>alert('제휴사 등록이 완료되었습니다.');</script>";
		}
	} else {
		$onload="<script>alert('제휴사는 {$max}개 까지 등록이 가능합니다.');</script>";
	}
} else if ($type=="delete" && ord($partner_id)) {
	$sql = "DELETE FROM tblpartner WHERE id='{$partner_id}'";
	pmysql_query($sql,get_db_conn());
	$onload="<script> alert('해당 제휴사가 삭제되었습니다.');</script>\n";
} else if ($type=="init" && ord($partner_id)) {
	$sql = "UPDATE tblpartner SET hit_cnt=0 WHERE id='{$partner_id}'";
	pmysql_query($sql,get_db_conn());
	$onload="<script> alert('해당 제휴사의 총 접속자 수를 0으로 초기화 하였습니다.');</script>\n";
}
?>
<?=$onload?>
<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	if (document.form1.up_url.value.length==0) {
		document.form1.up_url.focus();
		alert("제휴사 URL 또는 식별단어를 입력하세요.");
		return;
	}
	if (document.form1.up_id.value.length==0) {
		document.form1.up_id.focus();
		alert("제휴사 관리 아이디를 입력하세요.");
		return;
	}
	if (CheckLength(document.form1.up_id)>20) {
		document.form1.up_id.focus();
		alert("제휴사 관리 아이디는 20자 까지 입력 가능합니다.");
		return;
	}
	if (document.form1.up_passwd.value.length==0) {
		document.form1.up_passwd.focus();
		alert("제휴사 관리 패스워드를 입력하세요.");
		return;
	}
	if (CheckLength(document.form1.up_passwd)>20) {
		document.form1.up_passwd.focus();
		alert("제휴사 관리 패스워드는 20자 까지 입력 가능합니다.");
		return;
	}
	document.form1.type.value="insert";
	document.form1.submit();
}

function PartnerDelete(id) {
	if(confirm("해당 제휴사를 삭제하시겠습니까?")){
		document.form2.type.value="delete";
		document.form2.partner_id.value=id;
		document.form2.submit();
	}
}
function PartnerInit(id) {
	if(confirm("해당 제휴사 총 접속자 수를 초기화 하시겠습니까?")){
		document.form2.type.value="init";
		document.form2.partner_id.value=id;
		document.form2.submit();
	}
}
function PartnerOrder(id,pw) {
	document.form3.id.value=id;
	document.form3.passwd.value=pw;
	document.form3.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 마케팅지원 &gt;<span>제휴마케팅 관리</span></p></div></div>
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
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">제휴마케팅 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>제휴사 관리 및 제휴배너를 통합 접속자, 주문통계를 확인하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">제휴사 현황</div>
				</td>
            </tr>
            <tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) &quot;초기화&quot; 버튼 클릭시 제휴사를 통한 방문 접속자가 &quot;0&quot;으로 초기화 됩니다.</li>
                            <li>2) &quot;주문조회&quot; 버튼 클릭시 제휴사를 통하여 방문한 고객의 주문조회를 하실 수 있습니다.</li>
                        </ul>
                    </div>                    
            	</td>
            </tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=></col>
				<col width=120></col>
				<col width=80></col>
				<col width=70></col>
				<col width=90></col>
				<col width=90></col>
				<col width=65></col>
				<TR align=center>
					<th>제휴사 URL 또는 식별단어</th>
					<th>관리ID[비밀번호]</th>
					<th>총접속자</th>
					<th>오늘주문</th>
					<th>주문조회</th>
					<th>초기화</th>
					<th>삭제</th>
				</TR>
<?php
				$today=date("Ymd");
				$sql = "SELECT a.id, a.passwd, a.url, a.hit_cnt, count(b.ordercode) as order_cnt ";
				$sql.= "FROM tblpartner a LEFT JOIN tblorderinfo b ON b.ordercode LIKE '{$today}%' ";
				$sql.= "AND b.partner_id=a.id GROUP BY a.id, a.passwd, a.url, a.hit_cnt ";
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$cnt++;
					echo "<TR>\n";
					echo "	<TD><div class=\"ta_l\">{$row->url}</div></TD>\n";
					echo "	<TD><b>{$row->id}</b> (<b><span class=\"font_orange\">{$row->passwd}</span></b>)</TD>\n";
					echo "	<TD>".number_format($row->hit_cnt)."</TD>\n";
					echo "	<TD><span class=\"font_orange\"><b>".number_format($row->order_cnt)."</b></span></TD>\n";
					echo "	<TD><a href=\"javascript:PartnerOrder('{$row->id}','{$row->passwd}');\"><img src=\"images/btn_search1.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:PartnerInit('{$row->id}');\"><img src=\"images/btn_first.gif\" border=\"0\"></a></TD>\n";
					echo "	<TD><a href=\"javascript:PartnerDelete('{$row->id}');\"><img src=\"images/btn_del.gif\" border=\"0\"></a></TD>\n";
					echo "</TR>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan=7 align=center>등록된 제휴사가 존재하지 않습니다..</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">제휴사 신규등록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>제휴사URL 또는 식별단어</span></th>
					<TD><INPUT style="WIDTH:100%" maxLength=100 name=up_url class="input"></TD>
				</TR>
				<TR>
					<th><span>제휴사 관리 아이디</span></th>
					<TD><INPUT maxLength=20 name=up_id class="input"> <span class="font_orange">* 한글 입력 불가.영문,숫자조합</span></TD>
				</TR>
				<TR>
					<th><span>제휴사 관리 패스워드</span></th>
					<TD><INPUT maxLength=20 name=up_passwd class="input"></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
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
							<dt><span>제휴사 실적조회 URL</span></dt>
							<dd>
								<TABLE cellSpacing=0 cellPadding=0 width="95%" border=0>
						<TR>
							<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
							<TD background="images/table_top_line.gif" width="607" ></TD>
						</TR>
						<TR>
							<TD class="table_cell" width="153"><B>제휴사 실적조회 URL</B></TD>
							<TD class="td_con1" width="600"><A href="http://<?=$shopurl.PartnerDir?>index.php" target=_blank><B><span class="font_blue">http://<?=$shopurl.PartnerDir?>index.php</span></B></A></TD>
						</TR>
						<TR>
							<TD background="images/table_top_line.gif" width="153"><img src="images/table_top_line.gif"></TD>
							<TD background="images/table_top_line.gif" width="607"></TD>
						</TR>
						</TABLE>
							</dd>
						</dl>
						<dl>
							<dt><span>제휴사에 알려주셔야 할 실적조회 URL입니다.</span></dt>
							
						</dl>
						<dl>
							<dt><span>발급한 아이디/비번으로 로그인 하면 해당 제휴사를 통하여 방문한 고객의 주문내역을 확인할 수 있습니다.</span></dt>
						</dl>
						<dl>
							<dt><span>제휴사에서의 쇼핑몰 링크방법 안내</span><br><div class="font_orange">http://<?=$shopurl?>?ref=제휴사URL 또는 식별단어<br>
						예) 식별단어가 "partner" 일 경우 http://<?=$shopurl?>?ref=partner<br>
						<b>&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;제휴사URL이 "http://www.partner.com" 일 경우 http://<?=$shopurl?>?ref=http://www.partner.com<br>
						<b>&nbsp;</b>&nbsp;&nbsp;&nbsp;&nbsp;제휴사URL이 "www.partner.com" 일 경우 http://<?=$shopurl?>?ref=www.partner.com</div></dt>
							
						</dl>
						<dl>
							<dt><span> 위 방법과 같은 제휴를 통하여 수익을 창출하고, 그 수익에 대한 수수료를 제휴사에 배분하는 방식으로 운영</span></dt>
							
						</dl>
						
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
			</form>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=partner_id>
			</form>

			<form name=form3 action="http://<?=$shopurl.PartnerDir?>order_search.php" method=post target=_blank>
			<input type=hidden name=id>
			<input type=hidden name=passwd>
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

<?php 
include("copyright.php");
