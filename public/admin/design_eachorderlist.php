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
$body=$_POST["body"];
$intitle=$_POST["intitle"];

if($type=="update" && ord($body)) {
	if($intitle=="Y") {
		$leftmenu="Y";
	} else {
		$leftmenu="N";
	}
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='orderlist' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,leftmenu,body) VALUES (
		'orderlist', 
		'주문리스트 화면', 
		'{$leftmenu}', 
		'{$body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		leftmenu	= '{$leftmenu}', 
		body		= '{$body}' 
		WHERE type='orderlist' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$sql = "UPDATE tblshopinfo SET design_orderlist='U' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert(\"마이페이지 주문리스트 화면 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='orderlist' ";
	pmysql_query($sql,get_db_conn());

	$sql = "UPDATE tblshopinfo SET design_orderlist='001' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert(\"마이페이지 주문리스트 화면 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear") {
	$intitle="";
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='orderlist' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$body="";
	$intitle="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='orderlist' ";
	$result = pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
		$intitle=$row->leftmenu;
	} else {
		$intitle="Y";
	}
	pmysql_free_result($result);
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.body.value.length==0) {
			alert("마이페이지 주문리스트 화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("마이페이지 주문리스트 화면 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}
}

//-->
</SCRIPT>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>주문리스트 화면 꾸미기</span></p></div></div>

<table cellpadding="0" cellspacing="0" width="98%" style="table-layout:fixed">
<tr>
	<td valign="top">

	<table cellpadding="0" cellspacing="0" width=100% style="table-layout:fixed">
	<tr>
		<td>
		<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">		<col width=240 id="menu_width"></col>
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
					<div class="title_depth3">주문리스트 화면 꾸미기</div>
					<div class="title_depth3_sub"><span>마이페이지 주문리스트 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">마이페이지 주문리스트 화면 개별디자인</div>
				</td>
			</tr>
			<tr>
				<td>
					<div class="help_info01_wrap">
							<ul>
								<li>1) 매뉴얼의 매크로명령어를 참조하여 디자인 하세요.</li>
								<li>2) [기본값복원]+[적용하기], [삭제하기]하면 기본템플릿으로 변경(개별디자인 소스 삭제)됨 -> 템플릿 메뉴에서 원하는 템플릿 선택.</li>
								<li>3) 기본값 복원이나 삭제하기 없이도 템플릿 선택하면 개별디자인은 해제됩니다.(개별디자인 소스는 보관됨)</li>
							</ul>
					</div>			
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td style="padding-top:2px;"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea><br><input type=checkbox name=intitle value="Y" <?php if($intitle=="Y")echo"checked";?> style="BORDER-RIGHT: medium none; BORDER-TOP: medium none; BORDER-LEFT: medium none; BORDER-BOTTOM: medium none;"> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
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
							<dt><span class="point_c1">주문리스트 화면 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell colspan=2 align=center>
							<B>마이페이지 메뉴관련 매크로 정의</B>
							</td>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYHOME]</td>
							<td class=td_con1 style="padding-left:5;">
							마이페이지 홈 <FONT class=font_blue>(예:&lt;a href=[MENU_MYHOME]>마이페이지 홈&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYORDER]</td>
							<td class=td_con1 style="padding-left:5;">
							주문내역 <FONT class=font_blue>(예:&lt;a href=[MENU_MYORDER]>주문내역&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYPERSONAL]</td>
							<td class=td_con1 style="padding-left:5;">
							1:1고객게시판 <FONT class=font_blue>(예:&lt;a href=[MENU_MYPERSONAL]>1:1고객게시판&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYWISH]</td>
							<td class=td_con1 style="padding-left:5;">
							위시리스트 <FONT class=font_blue>(예:&lt;a href=[MENU_MYWISH]>위시리스트&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYRESERVE]</td>
							<td class=td_con1 style="padding-left:5;">
							적립금 <FONT class=font_blue>(예:&lt;a href=[MENU_MYRESERVE]>적립금&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYCOUPON]</td>
							<td class=td_con1 style="padding-left:5;">
							쿠폰 <FONT class=font_blue>(예:&lt;a href=[MENU_MYCOUPON]>쿠폰&lt;/a>)</font>
							</td>
						</tr>
						<?php if(getVenderUsed()) { ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYCUSTSECT]</td>
							<td class=td_con1 style="padding-left:5;">
							단골매장 <FONT class=font_blue>(예:&lt;a href=[MENU_MYCUSTSECT]>단골매장&lt;/a>)</font>
							</td>
						</tr>
						<?php } ?>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYINFO]</td>
							<td class=td_con1 style="padding-left:5;">
							회원정보수정 <FONT class=font_blue>(예:&lt;a href=[MENU_MYINFO]>회원정보수정&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[MENU_MYOUT]</td>
							<td class=td_con1 style="padding-left:5;">
							회원탈퇴 <FONT class=font_blue>(예:&lt;a href=[MENU_MYOUT]>회원탈퇴&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr><td colspan=2 height=10></td></tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SEARCH_BTN1]</td>
							<td class=td_con1 style="padding-left:5;">
							기간별 조회 "오늘" 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[SEARCH_BTN1]>오늘&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SEARCH_BTN2]</td>
							<td class=td_con1 style="padding-left:5;">
							기간별 조회 "15일" 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[SEARCH_BTN2]>15일&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SEARCH_BTN3]</td>
							<td class=td_con1 style="padding-left:5;">
							기간별 조회 "1개월" 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[SEARCH_BTN3]>1개월&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SEARCH_BTN4]</td>
							<td class=td_con1 style="padding-left:5;">
							기간별 조회 "3개월" 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[SEARCH_BTN4]>3개월&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SEARCH_BTN5]</td>
							<td class=td_con1 style="padding-left:5;">
							기간별 조회 "6개월" 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[SEARCH_BTN5]>6개월&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SEARCH_DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							일자별 조회 날짜선택
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[SEARCH_OK]</td>
							<td class=td_con1 style="padding-left:5;">
							검색버튼 <FONT class=font_blue>(예:&lt;a href=[SEARCH_OK]>검색&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORD1_기본이미지URL_선택된이미지URL]</td>
							<td class=td_con1 style="padding-left:5;">
							전체조회 이미지 버튼
							<br><FONT class=font_blue>(예:[ORD1_/<?=RootPath.DataDir?>design/menu01off.gif_/<?=RootPath.DataDir?>design/menu01on.gif])</font>
							<br>
							이미지 URL에 "_" 사용불가
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORD2_기본이미지URL_선택된이미지URL]</td>
							<td class=td_con1 style="padding-left:5;">
							주문내역조회 이미지 버튼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORD3_기본이미지URL_선택된이미지URL]</td>
							<td class=td_con1 style="padding-left:5;">
							취소내역조회 이미지 버튼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORD4_기본이미지URL_선택된이미지URL]</td>
							<td class=td_con1 style="padding-left:5;">
							반품,교환내역조회 이미지 버튼
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[IFORDER]<br>[IFELSEORDER]<br>[IFENDORDER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							주문리스트에 주문내역이 있을 경우와 없을 경우
							<pre style="line-height:15px">
<font class=font_blue>   <B>[IFORDER]</B>
      주문내역이 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEORDER]</B>
      주문내역이 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDORDER]</B></font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell width=180 align=right style="padding-right:15" nowrap>[FORORDER]<br>[FORENDORDER]</td>
							<td class=td_con1 width=100% style="padding-left:5;">
							[FORORDER] 주문내역 하나에 대한 내용 [FORENDORDER]
							<pre style="line-height:15px">
<font class=font_blue>   [IFORDER]
       <B>[FORORDER]</B>주문내역 하나에 대한 내용 기술<B>[FORENDORDER]</B>
   [IFELSEORDER]
       해당 내역이 없습니다.
   [IFENDORDER]</font></pre>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[FORPRODUCT]<br>[FORENDPRODUCT]</td>
							<td class=td_con1 style="padding-left:5;">
							[FORPRODUCT] 주문내역 하나에 대한 상품목록 및 배송정보 내용 [FORENDPRODUCT]
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DELISTAT]</td>
							<td class=td_con1 style="padding-left:5;">
							배송상태 - [FORPRODUCT] [FORENDPRODUCT] 내용에 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[IFDELISEARCH]<br>[IFELSEDELISEARCH]<br>[IFENDDELISEARCH]</td>
							<td class=td_con1 style="padding-left:5;">
							배송 완료된 상품의 배송정보가 있을 경우와 없을 경우 - [FORPRODUCT] [FORENDPRODUCT]에 사용
			<pre style="line-height:15px">
<font class=font_blue>   <B>[IFDELISEARCH]</B>
       배송 완료된 상품의 배송정보가 <FONT COLOR="red"><B>있을</B></FONT> 경우의 내용
   <B>[IFELSEDELISEARCH]</B>
       배송 완료된 상품의 배송정보가 <FONT COLOR="red"><B>없을</B></FONT> 경우의 내용
   <B>[IFENDDELISEARCH]</B></font></pre>
							</td>
						</tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DELICOM]</td>
							<td class=td_con1 style="padding-left:5;">
							택배사명 - 위 [IFDELISEARCH]내 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DELISEARCH]</td>
							<td class=td_con1 style="padding-left:5;">
							배송추적 버튼 (예:&lt;a href=[ORDER_DELISEARCH]>배송추적&lt;/a>) - 위 [IFDELISEARCH]내 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_NAME]</td>
							<td class=td_con1 style="padding-left:5;">
							주문상품명 - [FORPRODUCT] [FORENDPRODUCT] 내용에 사용
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DATE]</td>
							<td class=td_con1 style="padding-left:5;">
							주문일자
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_METHOD]</td>
							<td class=td_con1 style="padding-left:5;">
							결제방법
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_PRICE]</td>
							<td class=td_con1 style="padding-left:5;">
							결제금액 (예:[ORDER_PRICE]원)
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[ORDER_DETAIL]</td>
							<td class=td_con1 style="padding-left:5;">
							주문상세 버튼 함수 <FONT class=font_blue>(예:&lt;a href=[ORDER_DETAIL]>주문상세조회&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[PAGE]</td>
							<td class=td_con1 style="padding-left:5;">
							페이지 표시
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
