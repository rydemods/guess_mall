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
	$sql = "SELECT COUNT(*) as cnt FROM tbldesignnewpage WHERE type='search' ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	if($row->cnt==0) {
		$sql = "INSERT INTO tbldesignnewpage(type,subject,leftmenu,body) VALUES (
		'search', 
		'상품검색 결과화면', 
		'{$leftmenu}', 
		'{$body}')";
		pmysql_query($sql,get_db_conn());
	} else {
		$sql = "UPDATE tbldesignnewpage SET 
		leftmenu	= '{$leftmenu}', 
		body		= '{$body}' 
		WHERE type='search' ";
		pmysql_query($sql,get_db_conn());
	}
	pmysql_free_result($result);

	$sql = "UPDATE tblshopinfo SET design_search='U' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert(\"상품검색 결과화면 디자인 수정이 완료되었습니다.\"); }</script>";
} elseif($type=="delete") {
	$sql = "DELETE FROM tbldesignnewpage WHERE type='search' ";
	pmysql_query($sql,get_db_conn());

	$sql = "UPDATE tblshopinfo SET design_search='001' ";
	pmysql_query($sql,get_db_conn());
	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){ alert(\"상품검색 결과화면 디자인 삭제가 완료되었습니다.\"); }</script>";
} elseif($type=="clear") {
	$intitle="";
	$body="";
	$sql = "SELECT body FROM tbldesigndefault WHERE type='search' ";
	$result=pmysql_query($sql,get_db_conn());
	if($row=pmysql_fetch_object($result)) {
		$body=$row->body;
	}
	pmysql_free_result($result);
}

if($type!="clear") {
	$body="";
	$intitle="";
	$sql = "SELECT leftmenu,body FROM tbldesignnewpage WHERE type='search' ";
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
			alert("상품검색 결과화면 디자인 내용을 입력하세요.");
			document.form1.body.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("상품검색 결과화면 디자인을 삭제하시겠습니까?")) {
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
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>상품검색 결과화면 꾸미기</span></p></div></div>

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
					<div class="title_depth3">상품검색 결과화면 꾸미기</div>
					<div class="title_depth3_sub"><span>상품검색 결과화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품검색 결과화면 개별디자인</div>
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
				<td style="padding-top:2px;"><textarea name=body style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$body?></textarea><br><input type=checkbox name=intitle value="Y" <?php if($intitle=="Y")echo"checked";?>> <b><span style="letter-spacing:-0.5pt;"><span class="font_orange">기본 타이틀 이미지 유지 - 타이틀 이하 부분부터 디자인 변경</span>(미체크시 기존 타이틀 이미지 없어짐으로 직접 편집하여 사용)</b></span></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('clear');"><img src="images/botteon_bok.gif" border="0" hspace="2"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="0"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span class="point_c1">상품검색 결과화면 매크로명령어</span><span>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</span></dt>
							<dd>
			
					    <table border=0 cellpadding=0 cellspacing=0 width=100%>
					      <col width=160></col>
					      <col width=></col>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[CODEA_선택박스 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          1차카테고리 선택박스 <FONT class=font_blue>(예:[CODEA_width:150px;color:#000000;font-size:11px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[CODEB_선택박스 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          2차카테고리 선택박스 <FONT class=font_blue>(예:[CODEB_width:150px;color:#000000;font-size:11px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[CODEC_선택박스 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          3차카테고리 선택박스 <FONT class=font_blue>(예:[CODEC_width:150px;color:#000000;font-size:11px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[CODED_선택박스 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          4차카테고리 선택박스 <FONT class=font_blue>(예:[CODED_width:150px;color:#000000;font-size:11px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[MINPRICE_입력폼 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          최저가격 입력폼 <FONT class=font_blue>(예:[MINPRICE_width:120px;color:#000000;font-size:11px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[MAXPRICE_입력폼 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          최고가격 입력폼 <FONT class=font_blue>(예:[MAXPRICE_width:120px;color:#000000;font-size:11px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SCHECK_선택박스 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          검색방법 선택박스 <FONT class=font_blue>(예:[SCHECK_width:100px;color:#000000;font-size:11px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[KEYWORD_입력폼 스타일]</td>
					        <td class=td_con1 style="padding-left:5;">
					          검색어 입력폼 <FONT class=font_blue>(예:[KEYWORD_width:200px])</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SEARCHOK]</td>
					        <td class=td_con1 style="padding-left:5;">
					          검색버튼 <FONT class=font_blue>(예:&lt;a href=[SEARCHOK]>[검색]&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[TOTAL]</td>
					        <td class=td_con1 style="padding-left:5;">
					          총 상품수 <FONT class=font_blue>(예:총 [TOTAL]건)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTPRODUCTUP]</td>
					        <td class=td_con1 style="padding-left:5;">
					          제조사 ㄱㄴㄷ순 정렬  <FONT class=font_blue>(예:&lt;a href=[SORTPRODUCTUP]>제조사순▲&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTPRODUCTDN]</td>
					        <td class=td_con1 style="padding-left:5;">
					          제조사 ㄷㄴㄱ순 정렬 <FONT class=font_blue>(예:&lt;a href=[SORTPRODUCTDN]>제조사순▼&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTNAMEUP]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품명 ㄱㄴㄷ순 정렬 <FONT class=font_blue>(예:&lt;a href=[SORTNAMEUP]>상품명순▲&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTNAMEDN]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품명 ㄷㄴㄱ순 정렬 <FONT class=font_blue>(예:&lt;a href=[SORTNAMEDN]>상품명순▼&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTPRICEUP]</td>
					        <td class=td_con1 style="padding-left:5;">
					          낮은 상품가격순 <FONT class=font_blue>(예:&lt;a href=[SORTPRICEUP]>가격순▲&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTPRICEDN]</td>
					        <td class=td_con1 style="padding-left:5;">
					          높은 상품가격순 <FONT class=font_blue>(예:&lt;a href=[SORTPRICEDN]>가격순▼&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTRESERVEUP]</td>
					        <td class=td_con1 style="padding-left:5;">
					          낮은 적립금순 <FONT class=font_blue>(예:&lt;a href=[SORTRESERVEUP]>적립금순▲&lt;/a>)</font>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[SORTRESERVEDN]</td>
					        <td class=td_con1 style="padding-left:5;">
					          높은 적립금순 <FONT class=font_blue>(예:&lt;a href=[SORTRESERVEDN]>적립금순▼&lt;/a>)</font>
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
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[PRLIST1??]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품목록 - 이미지A형
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[PRLIST2??]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품목록 - 이미지B형
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[PRLIST????????_??]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품목록 - 이미지A형/이미지B형
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 위에 제공된 상품목록 형태 (1:이미지A형, 2:이미지B형)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 라인별 상품갯수(1~8)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1-8)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 사이의 세로라인 표시여부(Y/N/L)</FONT> (L은 상품에 맞추어 길게 표시됨)
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 사이의 가로라인 표시여부(Y/N)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 시중가격 표시여부(Y/N)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 적립금 표시여부(Y/N)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>_?? : 상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
					          <br>
					          <FONT class=font_blue>예) [PRLIST142NNYN2_10], [PRLIST222LYYY2_5]</FONT>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[PRLIST3??]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품목록 - 리스트형
					          <br><img width=10 height=0>
					          <FONT class=font_orange>?? : 상품목록 진열갯수 (01~20)</FONT>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[PRLIST3???????]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품목록 - 리스트형
					          <br><img width=10 height=0>
					          <FONT class=font_orange>?? : 상품 진열갯수 (01~20)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 이미지 표시여부 (Y/N)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 제조사 표시여부 (Y/N)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 시중가격 표시여부(Y/N)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 적립금 표시여부(Y/N)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 상품 태그 표시갯수(0-9) : 0일 경우 표시안함</FONT>
					          <br>
					          <FONT class=font_blue>예) [PRLIST304YYYY4]</FONT>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">[PRLIST4??_??]</td>
					        <td class=td_con1 style="padding-left:5;">
					          상품목록 - 공동구매형
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 라인별 상품갯수(2~4)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>? : 몇라인으로 진열을 할건지 숫자입력(1~8)</FONT>
					          <br><img width=10 height=0>
					          <FONT class=font_orange>_?? : 상품사이(위아래) 간격 최대 99픽셀 (미입력시 5픽셀)</FONT>
					          <br>
					          <FONT class=font_blue>예) [PRLIST423_5]</FONT>
					          </td>
					        </tr>
					      <tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
					      <tr>
					        <td class=table_cell align=right style="padding-right:15">상품목록 스타일 정의</td>
					        <td class=td_con1 style="padding-left:5;">
					          <img width=15 height=0><FONT class=font_orange>#prlist_colline - 이미지/리스트형의 가로라인 셀 스타일 정의</FONT>
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