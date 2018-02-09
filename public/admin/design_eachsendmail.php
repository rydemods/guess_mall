<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-5";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$mail_type=$_POST["mail_type"];
$subject=$_POST["subject"];
$body=$_POST["body"];

if($type=="update" && ord($mail_type) && ord($body) && ord($subject)) {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='{$mail_type}' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,body) VALUES (
		'{$mail_type}', 
		'{$subject}', 
		'{$body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		subject		= '{$subject}', 
		body		= '{$body}' 
		WHERE type='{$mail_type}' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){ alert(\"해당 메일화면 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete" && ord($mail_type)) {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='{$mail_type}' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){ alert(\"해당 메일화면 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear" && ord($mail_type)) {
	if($mail_type=="joinmail") {
		$subject="[SHOP] 가입 축하 메일입니다.";
	} elseif($mail_type=="ordermail") {
		$subject="[SHOP] 주문내역서 확인 메일입니다.";
	} elseif($mail_type=="delimail") {
		$subject="[SHOP] 상품 발송 메일입니다.";
	} elseif($mail_type=="bankmail") {
		$subject="[SHOP] 입금 확인 메일입니다.";
	} elseif($mail_type=="passmail") {
		$subject="[SHOP] 패스워드 안내메일입니다.";
	} elseif($mail_type=="authmail") {
		$subject="[SHOP] 회원 인증 메일입니다.";
	}
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='{$mail_type}' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$subject="";
	$body="";
	if(ord($mail_type)) {
		$sql = "SELECT subject,body FROM tbldesignnewpage WHERE type='{$mail_type}' ";
		$result = pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$subject=$row->subject;
			$body=$row->body;
		}
		pmysql_free_result($result);
	}
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.mail_type.value.length==0) {
			alert("해당 메일화면을 선택하세요.");
			document.form1.mail_type.focus();
			return;
		}
		if(document.form1.subject.value.length==0) {
			alert("해당 메일제목을 입력하세요.");
			document.form1.subject.focus();
			return;
		}
		if(document.form1.body.value.length==0) {
			alert("해당 메일화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("해당 메일화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}
}

function change_page(val) {
	document.form1.type.value="change";
	document.form1.submit();
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>메일 화면 꾸미기</span></p></div></div>

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
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8">
            </td></tr>
			<tr>
				<td>
                    <!-- 페이지 타이틀 -->
					<div class="title_depth3">메일 화면 꾸미기</div>
					<div class="title_depth3_sub"><span>메일 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">메일 화면 개별디자인</div>					
				</td>
			</tr>
			<tr>
				<td>
                	<div class="help_info01_wrap">
						<ul>
							<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요. - 메일본분 내용만 변경 가능합니다.</li>
							<li>2) [기본값복원]+[적용하기] 하면 기본 템플릿의 디자인으로 변경됩니다.</li>
							<li>3) [삭제하기] -> 기존 사용하던 메인 템플릿에 속한 디자인으로 변경됩니다.(메일화면은 별도의 템플릿이 제공되지 않습니다.)</li>
                            <li>4) 메일 제목 필수, 이미지 경로 사용시 쇼핑몰 주소 반드시 입력(예 : http://www.abc.co.kr/design/상점ID/이미지명.gif 또는 http://[URL]/design/상점ID/이미지명.gif)</li>
						</ul>
					</div>		
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:3pt;">
                	<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<TR>
							<th><span>메일 화면 선택</span></th>
							<TD class="td_con1">
                            <select name=mail_type onchange="change_page(options.value)" style="width:330px;" class="select">
							<option value="">메일 화면을 선택하세요.</option>
<?php
			$mail_list=array("신규 회원가입 축하 메일","주문 신청 확인 메일","주문 발송 메일","주문 입금 확인 메일","패스워드 안내 메일","회원인증 메일 (B2B 인증시에만)");
			$mail_code=array("joinmail","ordermail","delimail","bankmail","passmail","authmail");
			for($i=0;$i<count($mail_list);$i++) {
				echo "<option value=\"{$mail_code[$i]}\" ";
				if($mail_type==$mail_code[$i]) echo "selected";
				echo ">{$mail_list[$i]}</option>\n";
			}
?>
							</select></TD>
						</TR>
						<TR>
						<th><span>메일 제목</span></th>
							<TD class="td_con1"><input type=text name=subject value="<?=$subject?>" size=70 class="input" style="width:98%"></TD>
						</TR>
					</TABLE>
					</div>
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
					<TR>
					<TD colspan="2"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea></TD>
					</TR>
					</TABLE>
				</td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					<dt><span class="point_c1">메일 화면 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
					<dd>
					<table border=0 cellpadding=0 cellspacing=0 width=100%>
                        <col width=150></col>
                        <col width=></col>
                        <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                        <tr>
							<td class=table_cell colspan=2 align=center>
							<B>신규 회원가입 축하 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							가입 회원 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MESSAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							신규 회원가입 축하 메세지 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>주문 신청 확인 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							가입 회원 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자 - 메일 제목에만 사용가능 예)2006년 05월 03일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CURDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자 - 메일 내용에만 사용가능 예)2006년 05월 03일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MAILDATA]</td>
							<td class=td_con1 style="padding-left:5;">
							주문서 내역 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MESSAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문 메세지 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>주문 발송 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYURL]</td>
							<td class=td_con1 style="padding-left:5;">
							송장추적 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYNUM]</td>
							<td class=td_con1 style="padding-left:5;">
							택배 송장번호 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYCOMPANY]</td>
							<td class=td_con1 style="padding-left:5;">
							택배 회사명 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[DELIVERYDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							배송날짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDERDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문날짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15;line-height:17px">[IFDELICHANGE][ELSEDELICHANGE]   [ENDDELICHANGE]</td>
							<td class=td_con1 style="padding-left:5;line-height:17px">
							[IFDELICHANGE]물품 발송 후 송장정보만 변경된 경우 메세지[ELSEDELICHANGE]
							<br>물품발송 메세지[ENDDELICHANGE]
							<br>- 메일 내용에만 사용가능 
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15;line-height:17px">[IFDELINUM] [ENDDELINUM]</td>
							<td class=td_con1 style="padding-left:5;">
							송장번호가 존재할경우 메세지 입력 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15;line-height:17px">[IFDELIURL][ELSEDELIURL]   [ENDDELIURL]</td>
							<td class=td_con1 style="padding-left:5;line-height:17px">
							[IFDELIURL]배송추적시스템을 제공할경우 메세지[ELSEDELIURL]
							<br>배송추적시스템을 제공하지 않을경우 메세지[ENDDELIURL]
							<br>- 메일 내용에만 사용가능 
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>주문 입금 확인 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BANKDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							입금확인 일짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDERDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자 - 메일 내용에만 사용가능 예)2006년 05월 03일
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>아이디/패스워드 안내 메일</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 아이디 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PASSWORD]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 비밀번호 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=5></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>회원인증 메일 (B2B에서 관리자가 회원인증시 발송)</B>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SHOP]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 이름 - 메일 제목 및 내용에 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ID]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 아이디 - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[OKDATE]</td>
							<td class=td_con1 style="padding-left:5;">
							회원 인증일짜 - 메일 내용에만 사용가능 예)2006/05/03
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[URL]</td>
							<td class=td_con1 style="padding-left:5;">
							쇼핑몰 URL - 메일 내용에만 사용가능
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					</table>
					</dd>
					</dl>
					<dl>
						<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
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
</table>
<?=$onload?>
<?php 
include("copyright.php");