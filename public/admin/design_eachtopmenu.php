<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "de-4";
$MenuCode = "design";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$code=$_POST["code"];
$top_body=$_POST["top_body"];
$top_height=(int)$_POST["top_height"];
if($top_height==0) $top_height=70;

if(ord($code)==0) $code="ALL";

if($type=="update" && ord($top_body)) {
	//$top_body = str_replace("\"\[","[",$top_body);
	//$top_body = str_replace("]\"","]",$top_body);
	if($code=="ALL") {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesign ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		if($row->cnt==0) {
			/*
			$sql = "INSERT INTO tbldesign(body_top,top_height) VALUES (
			'{$top_body}', 
			'{$top_height}')";
			*/
			$sql = "INSERT INTO tbldesign(body_top) VALUES (
			'{$top_body}')";

			pmysql_query($sql,get_db_conn());
		} else {
			/*
			$sql = "UPDATE tbldesign SET 
			body_top	= '{$top_body}', 
			top_height	= '{$top_height}' ";
			*/
			$sql = "UPDATE tbldesign SET 
			body_top	= '{$top_body}'";
			pmysql_query($sql,get_db_conn());
			
		}
		pmysql_free_result($result);
	} else {
		$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage 
		WHERE type='topmenu' AND code='{$code}' ";
		$result=pmysql_query($sql,get_db_conn());
		$row=pmysql_fetch_object($result);
		if($row->cnt==0) {
			$sql = "INSERT INTO tbldesignnewpage(type,body,code) VALUES (
			'topmenu', 
			'{$top_body}', 
			'{$code}')";
			pmysql_query($sql,get_db_conn());
		} else {
			$sql = "UPDATE tbldesignnewpage SET 
			body		= '{$top_body}' 
			WHERE type='topmenu' AND code='{$code}' ";
			pmysql_query($sql,get_db_conn());
		}
		pmysql_free_result($result);
	}
	
	$onload="<script>window.onload=function(){alert(\"상단메뉴화면 디자인 수정이 완료되었습니다.\");}</script>";
} elseif($type=="delete") {
	if($code=="ALL") {
		$sql = "UPDATE tbldesign SET body_top='' ";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "DELETE FROM tbldesignnewpage WHERE type='topmenu' AND code='{$code}' ";
		pmysql_query($sql,get_db_conn());
	}
	$onload="<script>window.onload=function(){alert(\"상단메뉴화면 디자인 삭제가 완료되었습니다.\");}</script>";
} elseif($type=="clear") {
	$top_body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='topmenu' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$top_body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$top_body="";
	if($code=="ALL") {
		$sql = "SELECT * FROM tbldesign ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$top_body=$row->body_top;
			$top_height=$row->top_height;
		}
		pmysql_free_result($result);
	} else {
		$sql = "SELECT * FROM tbldesignnewpage WHERE type='topmenu' AND code='{$code}' ";
		$result=pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			$top_body=$row->body;
		}
		pmysql_free_result($result);
	}
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.top_body.value.length==0) {
			alert("상단메뉴화면 디자인 내용을 입력하세요.");
			document.form1.top_body.focus();
			return;
		}
		try {
			if(!IsNumeric(document.form1.top_height.value)) {
				alert("상단메뉴 높이는 숫자만 입력 가능합니다.");
				document.form1.top_height.focus();
				return;
			}
		} catch (e) {}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("상단메뉴화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}
}

function change_page(obj) {

	document.form1.type.value="change";
	document.form1.code.value=obj.value;
	document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-메인, 카테고리 &gt;<span>상단메뉴 꾸미기</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">

	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">	<col width=240 id="menu_width"></col>
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
					<div class="title_depth3">상단메뉴 꾸미기</div>
					<!-- 소제목 -->
					<div>
                </td>
            </tr>
            <tr><td height="20">  </td></tr>
            <tr>
            	<td>
                    <table width="100%">
						<tr>
						<td>
                        <div class="help_info01_wrap">
							<ul>
								<li>1) 상단메뉴를 전체페이지(default), 또는 카테고리별, 메뉴별 자유롭게 디자인이 가능합니다.</li>
								<li>2) 개별디자인 적용 후 <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');">디자인관리 > 웹FTP 및 개별적용 선택 > 개별디자인 적용선택</a> 을 해야 적용됩니다. 
									<br />&nbsp;&nbsp;상단+왼쪽 동시 적용 
									<br />&nbsp;&nbsp;상단만 적용 
								</li>
								<li>3) <a href="javascript:parent.topframe.GoMenu(2,'design_easytop.php');">디자인관리 > Easy 디자인 관리 > Easy 상단 메뉴 관리</a> 에서 디자인을 변경할 수 있습니다.</li>
							</ul>
                        </div>
						</td>
						<td><IMG SRC="images/design_eachtop_img.gif" ALT="" align="baseline"></td>
						</tr>
		             </table>
                                                
						</div>
                        
					<!-- 소제목 -->
					<div class="title_depth3_sub">상단메뉴 디자인</div>
                </td>
             </tr>
             <tr>
             	<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됩니다. -> 템플릿 메뉴에서 원하는 템플릿 선택</li>
                                <li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인 해제됩니다.(개별디자인 소스는 보관됨)</li>
							</ul>
					</div>
				</td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=code value="<?=$code?>">
			<tr>
				<td style="padding-top:3pt;">
                	<div class="table_style01">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
						<TR>
							<th><span>해당 페이지 선택</span></th>
							<TD class="td_con1"><select name=plist onchange="change_page(this)" style="width:330" class="select">
						<option value="ALL" <?php if($code=="A")echo"selected";?>>기본 페이지 (Default)</option>
<?php
			$sql = "SELECT code_a, code_name FROM tblproductcode 
			WHERE (type='L' OR type='T' OR type='LX' OR type='TX') ORDER BY sequence DESC ";
			$result=pmysql_query($sql,get_db_conn());
			$i=0;
			while($row=pmysql_fetch_object($result)) {
				$i++;
				echo "<option value=\"{$row->code_a}\" ";
				if($code==$row->code_a) echo "selected";
				echo ">대분류{$i} - {$row->code_name}</option>\n";
			}
			pmysql_free_result($result);

			$page_list=array("메인 페이지 상단메뉴","게시판 관련 상단메뉴","회원 관련 상단메뉴","마이페이지 관련 상단메뉴","주문서 관련 상단메뉴","검색 관련 상단메뉴","브랜드 상품 목록 관련 상단메뉴","브랜드맵 관련 상단메뉴");
			$page_code=array("MAI","BOA","MEM","MYP","ORD","SEA","BRL","BRM");

			for($i=0;$i<count($page_list);$i++) {
				echo "<option value=\"{$page_code[$i]}\" ";
				if($code==$page_code[$i]) echo "selected";
				echo ">{$page_list[$i]}</option>\n";
			}
?>
						</select>
						<?php if($code!="ALL"){?>
						&nbsp; <span class="font_orange"><b>* 원프레임 타입에서만 적용됩니다.</b></span>
						<?php }?>
					</TD>
				</TR>
				<!--TR>
							<th><span>상단메뉴 높이</span></th>
							<TD class="td_con1"><input type=text name=top_height value="<?=$top_height?>" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class="input">픽셀</TD>
				</TR-->
				</TABLE>
				</div>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 style="table-layout:fixed">
				
				<TR>
					<TD colspan="2"><textarea name=top_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$top_body?></textarea></TD>
				</TR>
				</TABLE>
                </div>
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
							<dt><span class="point_c1">상단메뉴 매크로명령어(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></span></dt>
							<dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[VISIT]</td>
							<td class=td_con1 style="padding-left:5;">
							방문자표시, 로그인시 로그아웃 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[VISIT2]</td>
							<td class=td_con1 style="padding-left:5;">
							방문자표시, 로그아웃 표시안됨
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[HOME]</td>
							<td class=td_con1 style="padding-left:5;">
							HOME <FONT class=font_blue>(예:&lt;a href=[HOME]>HOME&lt;/a>)</FONT>
							</td>
						</tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[USEINFO]</td>
							<td class=td_con1 style="padding-left:5;">
							이용안내 <FONT class=font_blue>(예:&lt;a href=[USEINFO]>이용안내&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBER]</td>
							<td class=td_con1 style="padding-left:5;">
							회원가입/수정 <FONT class=font_blue>(예:&lt;a href=[MEMBER]>회원가입/수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGIN]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 <FONT class=font_blue>(예:&lt;a href=[LOGIN]>로그인&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGOUT]</td>
							<td class=td_con1 style="padding-left:5;">
							로그아웃 <FONT class=font_blue>(예:&lt;a href=[LOGOUT]>로그아웃&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MEMBEROUT]</td>
							<td class=td_con1 style="padding-left:5;">
							회원탈퇴 <FONT class=font_blue>(예:&lt;a href=[MEMBEROUT]>회원탈퇴&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGO]</td>
							<td class=td_con1 style="padding-left:5;">
							로고이미지 <FONT class=font_blue>(예:&lt;a href=[HOME]>[LOGO]&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGINFORM]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 폼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[LOGINFORMU]</td>
							<td class=td_con1 style="padding-left:5;">
							로그인 폼 관리에서 등록한 내용 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BASKET]</td>
							<td class=td_con1 style="padding-left:5;">
							장바구니 <FONT class=font_blue>(예:&lt;a href=[BASKET]>장바구니&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER]</td>
							<td class=td_con1 style="padding-left:5;">
							주문조회 <FONT class=font_blue>(예:&lt;a href=[ORDER]>주문조회&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RESERVEVIEW]</td>
							<td class=td_con1 style="padding-left:5;">
							적립금조회 <FONT class=font_blue>(예:&lt;a href=[RESERVEVIEW]>적립금조회&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MYPAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							마이페이지 <FONT class=font_blue>(예:&lt;a href=[MYPAGE]>마이페이지&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[REVIEW]</td>
							<td class=td_con1 style="padding-left:5;">
							사용후기 모음 <FONT class=font_blue>(예:&lt;a href=[REVIEW]>사용후기 모음&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BOARD]</td>
							<td class=td_con1 style="padding-left:5;">
							게시판 <FONT class=font_blue>(예:&lt;a href=[BOARD]>게시판&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[AUCTION]</td>
							<td class=td_con1 style="padding-left:5;">
							경매 <FONT class=font_blue>(예:&lt;a href=[AUCTION]>경매&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[GONGGU]</td>
							<td class=td_con1 style="padding-left:5;">
							공동구매 <FONT class=font_blue>(예:&lt;a href=[GONGGU]>공동구매&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ESTIMATE]</td>
							<td class=td_con1 style="padding-left:5;">
							온라인견적서 <FONT class=font_blue>(예:&lt;a href=[ESTIMATE]>온라인견적서&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[COMPANY]</td>
							<td class=td_con1 style="padding-left:5;">
							회사소개 <FONT class=font_blue>(예:&lt;a href=[COMPANY]>회사소개&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[EMAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							이메일 <FONT class=font_blue>(예:&lt;a href=[EMAIL]>고객센터&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTNEW]</td>
							<td class=td_con1 style="padding-left:5;">
							신규상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTNEW]>신규상품&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTBEST]</td>
							<td class=td_con1 style="padding-left:5;">
							인기상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTBEST]>인기상품&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTHOT]</td>
							<td class=td_con1 style="padding-left:5;">
							추천상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTHOT]>추천상품&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PRODUCTSPECIAL]</td>
							<td class=td_con1 style="padding-left:5;">
							특별상품 <FONT class=font_blue>(예:&lt;a href=[PRODUCTSPECIAL]>특별상품&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[TAG]</td>
							<td class=td_con1 style="padding-left:5;">
							태그바로기 <FONT class=font_blue>(예:&lt;a href=[TAG]>태그&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[RSS]</td>
							<td class=td_con1 style="padding-left:5;">
							RSS 바로가기 <FONT class=font_blue>(예:&lt;a href=[RSS]>RSS&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE1]</td>
							<td class=td_con1 style="padding-left:5;">
							기본 공지사항 모습
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE2]</td>
							<td class=td_con1 style="padding-left:5;">
							공지날짜가가 제목앞에 붙는 모습
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE3]</td>
							<td class=td_con1 style="padding-left:5;">
							앞부분에 이미지 표시
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE4]</td>
							<td class=td_con1 style="padding-left:5;">
							앞부분에 숫자나 날짜표기 안함
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[NOTICE?????_000]</td>
							<td class=td_con1 style="padding-left:5;">
							공지사항
										<br><img width=10 height=0>
										<FONT class=font_orange>? : 위에 제공된 공지사항 타입</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : 타이틀 표시여부(Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : 공지사항 간격(1-9) 미입력시 4픽셀</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : NEW 아이콘 표시여부 (Y/N)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>? : NEW 아이콘 표시기간 (1-9)</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_000 : 표시될 공지사항 길이 (최대 숫자 200까지)</FONT>
										<br>
										<FONT class=font_blue>예) [NOTICE1N5Y1_80]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHFORMSTART]</td>
							<td class=td_con1 style="padding-left:5;">
							검색폼 시작
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHKEYWORD_000]</td>
							<td class=td_con1 style="padding-left:5;">
							검색폼 검색어 입력 텍스트폼 <FONT class=font_orange>(_000:텍스트폼 사이즈[픽셀단위])</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHOK]</td>
							<td class=td_con1 style="padding-left:5;">
							검색확인 버튼 <FONT class=font_blue>(예:&lt;a href=[SEARCHOK]>검색&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#FBDED2 style="padding-right:15">[SEARCHFORMEND]</td>
							<td class=td_con1 style="padding-left:5;">
							검색폼 끝
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BESTSKEY_000_인기검색어구분자_인기검색어텍스트스타일]</td>
							<td class=td_con1 style="padding-left:5;">
							인기검색어 출력 (인기검색어 기능 설정이 되어있어야 가능)
										<br><img width=10 height=0>
										<FONT class=font_orange>_000 : 인기검색어 출력 텍스트 총 길이 (예: 100) - 100바이트 출력 후 "..." 출력</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_인기검색어구분자 : 인기검색어 구분자 (예: "|" 또는 ",") "_"사용불가</FONT>
										<br><img width=10 height=0>
										<FONT class=font_orange>_인기검색어텍스트스타일 : 인기검색어 텍스트 스타일 (예: color:#FFFFFF;font-size:9px) "_"사용불가</FONT>
										<br>
										<FONT class=font_blue>예) [BESTSKEY_100_|_color:#FFFFFF;font-size:9px]</FONT>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td colspan=2 style="padding:10">
							<pre style="line-height:15px">
<B>[검색폼 예]</B>

	<FONT class=font_blue>&lt;table border=0 cellpadding=0 cellspacing=0>
	<B>[SEARCHFORMSTART]</B>
	&lt;tr>
	   &lt;td><B>[SEARCHKEYWORD_120]</B>&lt;/td>
	   &lt;td>&lt;a href=<B>[SEARCHOK]</B>>검색&lt;/a>&lt;/td>
	&lt;/tr>
	<B>[SEARCHFORMEND]</B>
	&lt;/table></FONT></pre>
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