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
$main_body=$_POST["main_body"];

if($type=="update" && ord($main_body)) {
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage 
	WHERE type='mainpage' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,body) VALUES(
		'mainpage', 
		'메인페이지', 
		'{$main_body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		body		= '{$main_body}' 
		WHERE type='mainpage' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);
	$onload="<script>window.onload=function(){alert(\"메인 본문 디자인 수정이 완료되었습니다.\");}</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='mainpage' ";
	pmysql_query($sql,get_db_conn());
	$onload="<script>window.onload=function(){alert(\"메인 본문 디자인 삭제가 완료되었습니다.\");}</script>";
} elseif($type=="clear") {
	$main_body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='mainpage' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$main_body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$main_body="";
	$sql = "SELECT body FROM tbldesignnewpage WHERE type='mainpage' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$main_body=$row->body;
	}
	pmysql_free_result($result);
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="update") {
		if(document.form1.main_body.value.length==0) {
			alert("메인 본문 디자인 내용을 입력하세요.");
			document.form1.main_body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("메인 본문 디자인을 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	} else if(type=="clear") {
		alert("기본값 복원 후 [적용하기]를 클릭하세요. 클릭 후 페이지에 적용됩니다.");
		document.form1.type.value=type;
		document.form1.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-메인, 카테고리 &gt;<span>메인 본문 꾸미기</span></p></div></div>

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
					<div class="title_depth3">메인 본문 꾸미기</div>
					<!-- 소제목 -->
					<div>
                </td>
            </tr>
            <tr><td height="20"></td></tr>
            <tr>
            	<td>
                    <table width="100%">
                    		<tr>
                            <td>
                            <div class="help_info01_wrap">
							<ul>
								<li>1) 쇼핑몰 메인본문(메인중앙+우측메뉴를 모두 포함)을 자유롭게 디자인이 가능합니다. 
  
 </li>
                                <li>2) 개별디자인 적용 후 <a href="javascript:parent.topframe.GoMenu(2,'design_option.php');">디자인관리 > 웹FTP 및 개별적용 선택 > 개별디자인 적용선택</a> 을 해야 적용됩니다. 
   <br />&nbsp;&nbsp;메인본문 적용+전체페이지 왼쪽메뉴 출력 
   <br />&nbsp;&nbsp;메인본문 적용+전체페이지 왼쪽메뉴 미출력 </li>
								<li>3) <a href="javascript:parent.topframe.GoMenu(2,'design_easycss.php');">디자인관리 > Easy 디자인 관리 > Easy 텍스트 속성 관리</a> 에서 각 메뉴들의 텍스트속성을 변경합니다.
</li>
							</ul>
                            </div>
                            </td>
                            <td><IMG SRC="images/design_eachmain_img.gif" ALT="" align="baseline"></td>
                            </tr>
                    </table>
                </td>
            </tr>
            <tr>
            	<td>    
					<!-- 소제목 -->
					<div class="title_depth3_sub">
						메인 본문 꾸미기</div>
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
			<tr><td height=3></td></tr>
			<tr>
				<td><textarea name=main_body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$main_body?></textarea></td>
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
							<dt><span>각각의 속성 설정</span></dt>
                            <dd>- 메인상품 진열타입 / 우측 게시글 표시개수 지정 : <a href="javascript:parent.topframe.GoMenu(1,'shop_mainproduct.php');"><span class="font_blue">상점관리 > 쇼핑몰 환경 설정 > 상품 진열수/화면설정</span></a><br />- 우측 타이틀 배경색 : <a href="javascript:parent.topframe.GoMenu(2,'design_eachtitleimage.php');"><span class="font_blue">디자인관리 > 개별 디자인- 메인 및 상하단 > 타이틀 이미지 관리</span></a><br />- 텍스트 속성관리 : <a href="javascript:parent.topframe.GoMenu(2,'design_easycss.php');"><span class="font_blue">디자인관리 > Easy 디자인 관리 > Easy 텍스트 속성 관리</span></a></dd>
                        </dl>
                        <dl>
                        	<dt><span>메인 본문 매크로명령어</span></dt>
                            <dd>
                                <table border=0 cellpadding=0 cellspacing=0 width=100%>
                                <col width=150></col>
                                <col width=></col>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[SHOPINTRO]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    쇼핑몰 인사말 표시 - 직접 입력해도 무관
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
                                    <td class=table_cell align=right style="padding-right:15">[NEWITEM1??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    신규상품 - 이미지A형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[NEWITEM2??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    신규상품 - 이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[NEWITEM?????????_??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    신규상품 - 이미지A형/이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 위에 제공된 신규상품 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_?? : 신규상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [NEWITEM142NNNYN2_10], [NEWITEM222YLYYY2_5]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[NEWITEM3??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    신규상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 신규상품 진열갯수 (01~20)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[NEWITEM3???????]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    신규상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 신규상품 진열갯수 (01~20)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 제조사 표시여부 (Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 신규상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [NEWITEM304YYYY4]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BESTITEM1??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    인기상품 - 이미지A형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BESTITEM2??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    인기상품 - 이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BESTITEM?????????_??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    인기상품 - 이미지A형/이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 위에 제공된 인기상품 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_?? : 인기상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [BESTITEM142NNNYN2_10], [BESTITEM222YLYYY2_5]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BESTITEM3??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    인기상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 인기상품 진열갯수 (01~20)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BESTITEM3???????]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    인기상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 인기상품 진열갯수 (01~20)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 제조사 표시여부 (Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 인기상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [BESTITEM304YYYY4]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[HOTITEM1??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    추천상품 - 이미지A형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[HOTITEM2??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    추천상품 - 이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[HOTITEM?????????_??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    추천상품 - 이미지A형/이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 위에 제공된 추천상품 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_?? : 추천상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [HOTITEM142NNNYN2_10], [HOTITEM222YLYYY2_5]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[HOTITEM3??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    추천상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 추천상품 진열갯수 (01~20)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[HOTITEM3???????]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    추천상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 추천상품 진열갯수 (01~20)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천사 표시여부 (Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 추천상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [HOTITEM304YYYY4]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[SPEITEM0?????]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    특별상품 - 기존방식의 특별상품 나열
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 진열상품갯수(1-9)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 시중가격 표시여부(Y/N) </FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [SPEITEM05YYY2]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[SPEITEM1??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    특별상품 - 이미지A형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[SPEITEM2??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    특별상품 - 이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[SPEITEM?????????_??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    특별상품 - 이미지A형/이미지B형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 위에 제공된 특별상품 형태 (1:이미지A형, 2:이미지B형)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 사이의 가로라인 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_?? : 특별상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [SPEITEM142NNNYN2_10], [SPEITEM222YLYYY2_5]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[SPEITEM3??]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    특별상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 특별상품 진열갯수 (01~20)</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[SPEITEM3???????]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    특별상품 - 리스트형
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>?? : 특별상품 진열갯수 (01~20)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 제조사 표시여부 (Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 시중가격 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 적립금 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 특별상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [SPEITEM304YYYY4]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">진열상품(신규/인기/추천/특별) 스타일 정의</td>
                                    <td class=td_con1 style="padding-left:5;">
                                                <FONT class=font_orange>#prlist_colline - 이미지/리스트형의 가로라인 셀 스타일 정의</FONT>
                                                <br><img width=100 height=0>
                                                <FONT class=font_blue>예) #prlist_colline { background-color:#f4f4f4;height:1px; }</FONT>
                                                <br><img width=0 height=7><br><img width=10 height=0>
                                                <FONT class=font_orange>#prlist_colline - 이미지/리스트형의 가로라인 셀 스타일 정의</FONT>
                                                <br><img width=100 height=0>
                                                <FONT class=font_blue>예) #prlist_rowline { background-color:#f4f4f4;width:1px; }</FONT>
                                    <pre style="line-height:15px">
        <B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.
        
           <FONT class=font_blue>&lt;style>
              #prlist_colline { background-color:#f4f4f4;height:1px; }
              #prlist_rowline { background-color:#f4f4f4;width:1px; }
           &lt;/style></FONT></pre>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[GONGGU]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    공동구매 메인화면 표시
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[GONGGUN]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    공동구매 메인화면 표시(타이틀 없음)
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[AUCTION]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    경매 메인화면 표시
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[AUCTIONN]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    경매 메인화면 표시(타이틀 없음)
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
                                    <td class=table_cell align=right style="padding-right:15">[INFO1]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    기본 컨텐츠정보 모습
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[INFO2]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    게시날짜가가 제목앞에 붙는 모습
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[INFO3]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    앞부분에 이미지 표시
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[INFO4]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    앞부분에 숫자나 날짜표기 안함
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[INFO???_000]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    컨텐츠정보
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 위에 제공된 컨텐츠정보 타입</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 타이틀 표시여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 컨텐츠정보 간격(1-9) 미입력시 4픽셀</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_000 : 표시될 컨텐츠정보 길이 (최대 숫자 200까지)</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [INFO1N5_80]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BANNER1]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    현재 배너타입
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BANNER2]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    가로로 표시되는 타입
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[POLL]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    기존 투표방식
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[POLL_TITLE]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    세부적인 개별디자인시 제목
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[POLL_TITLE2]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    세부적인 개별디자인시 제목-타이틀 이미지 없음
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[POLL_CHOICE]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    세부적인 개별디자인시 투표항목
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[POLL_BTN1]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    투표하기 링크 <FONT class=font_blue>(예:&lt;a href=[POLL_BTN1]>투표&lt;/a>)</font>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[POLL_BTN2]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    결과보기 링크 <FONT class=font_blue>(예:&lt;a href=[POLL_BTN2]>결과보기&lt;/a>)</font>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[REVIEW??????_000]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    상품평 표시
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 목록 정렬 방법 (0:최근등록순, 1:높은평점순)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 상품평 글 링크 방법(0:팝업으로 상품평 출력, 1:상품평 상품 상세페이지)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 작성일자 표시방법 (0:게시일자미표시, 1:월/일, 2:년/월/일)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 메인에 표시할 상품평글 갯수(1-9)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 상품평 글 사이의 간격(0-9)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 평점 표시 여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_000 : 표시될 게시글 길이 (최대 숫자 200까지)</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [REVIEW10154Y_80], [REVIEW01254N_50]</FONT>
                                    </td>
                                </tr>
                                <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
                                <tr>
                                    <td class=table_cell align=right style="padding-right:15">[BOARD?????_000_?]</td>
                                    <td class=td_con1 style="padding-left:5;">
                                    게시판 표시
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 1,2,3,4,5,6 여섯개의 게시판에 대해서 최근 게시물 추출</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 게시일자 표시방법 (0:게시일자미표시, 1:월/일, 2:년/월/일)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 메인에 표시할 게시글 갯수(1-9)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 게시판 글 사이의 간격(0-9)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>? : 답변글 추출 여부(Y/N)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_000 : 표시될 게시글 길이 (최대 숫자 200까지)</FONT>
                                                <br><img width=10 height=0>
                                                <FONT class=font_orange>_? : 게시판 코드 (해당 게시판에 부여된 고유코드)</FONT>
                                                <br>
                                                <FONT class=font_blue>예) [BOARD1154N_80_<?=$_ShopInfo->getId()?>], [BOARD2154Y_50_<?=$_ShopInfo->getId()?>]</FONT>
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
                                </table>
						</dd>
						</dl>
						<dl>
							<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</span></dt>
						</dl>
					</div>
				</td>
			</tr>
			</table>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>

</table>
<?=$onload?>
<?php 
include("copyright.php");