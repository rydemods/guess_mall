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
$seachIdx=$_POST["seachIdx"];

if(ord($seachIdx)==0) {
	$seachIdx = "전체";
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function SearchSubmit(seachIdxval) {
	form = document.form1;
	form.mode.value="";
	form.seachIdx.value = seachIdxval;
	form.submit();
}

function design_preview(design) {
	document.all["preview_img"].src="images/sample/brand"+design+".gif";
}

function CodeProcessFun(brandselectedIndex,brandcode) {
	if(brandselectedIndex>-1) {
		document.form2.mode.value="";
		document.form2.code.value=brandcode;
		document.form2.target="MainPrdtFrame";
		document.form2.action="design_eachblist.list.php";
		document.form2.submit();
	}
}
</script>
<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 300;HEIGHT: 250;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-페이지 본문 &gt;<span>상품브랜드 꾸미기</span></p></div></div>
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
					<div class="title_depth3">상품브랜드 꾸미기</div>
					<div class="title_depth3_sub"><span>상품 브랜드별 화면 디자인을 자유롭게 디자인 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">상품브랜드별 개별디자인</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=seachIdx value="">
			<tr>
				<td>
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<TD>
					<div class="point_title02">전체 브랜드</div>
                    </TD>
					<TD>&nbsp;</TD>
					<TD><div class="point_title03">현재 상품 브랜드별 템플릿</span></div></TD>
				</TR>
				<TR>
					<TD bgcolor="#f8f8f8" valign="top" style="padding:8pt;" width="48%">
					<table border=0 cellpadding=0 cellspacing=0 width="100%">
					<tr>
						<td style="padding:5px;padding-left:2px;padding-right:2px;">
						<table border=0 cellpadding=0 cellspacing=0 width="100%">
						<tr align="center">
							<td><b><a href="javascript:SearchSubmit('A');"><span id="A">A</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('B');"><span id="B">B</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('C');"><span id="C">C</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('D');"><span id="D">D</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('E');"><span id="E">E</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('F');"><span id="F">F</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('G');"><span id="G">G</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('H');"><span id="H">H</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('I');"><span id="I">I</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('J');"><span id="J">J</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('K');"><span id="K">K</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('L');"><span id="L">L</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('M');"><span id="M">M</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('N');"><span id="N">N</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('O');"><span id="O">O</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('P');"><span id="P">P</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('Q');"><span id="Q">Q</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('R');"><span id="R">R</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('S');"><span id="S">S</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('T');"><span id="T">T</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('U');"><span id="U">U</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('V');"><span id="V">V</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('W');"><span id="W">W</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('X');"><span id="X">X</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('Y');"><span id="Y">Y</span></a></b></td>
							<td><b><a href="javascript:SearchSubmit('Z');"><span id="Z">Z</span></a></b></td>
						</TR>
						</table>
						</td>
						<td width="40" align="center" nowrap><b><a href="javascript:SearchSubmit('전체');"><span id="전체">전체</span></a></b></td>
					</tr>
					<tr>
						<!-- 상품브랜드 목록 -->
						
						<td width="100%"><select name="up_brandlist" size="16" style="width:100%;" onchange="CodeProcessFun(this.selectedIndex,this.value);">
					<?php
						$sql = "SELECT * FROM tblproductbrand ";
						if(preg_match("/^[ㄱ-ㅎ]/", $seachIdx)) {
							if($seachIdx == "ㄱ") $sql.= "WHERE (brandname >= 'ㄱ' AND brandname < 'ㄴ') OR (brandname >= '가' AND brandname < '나') ";
							if($seachIdx == "ㄴ") $sql.= "WHERE (brandname >= 'ㄴ' AND brandname < 'ㄷ') OR (brandname >= '나' AND brandname < '다') ";
							if($seachIdx == "ㄷ") $sql.= "WHERE (brandname >= 'ㄷ' AND brandname < 'ㄹ') OR (brandname >= '다' AND brandname < '라') ";
							if($seachIdx == "ㄹ") $sql.= "WHERE (brandname >= 'ㄹ' AND brandname < 'ㅁ') OR (brandname >= '라' AND brandname < '마') ";
							if($seachIdx == "ㅁ") $sql.= "WHERE (brandname >= 'ㅁ' AND brandname < 'ㅂ') OR (brandname >= '마' AND brandname < '바') ";
							if($seachIdx == "ㅂ") $sql.= "WHERE (brandname >= 'ㅂ' AND brandname < 'ㅅ') OR (brandname >= '바' AND brandname < '사') ";
							if($seachIdx == "ㅅ") $sql.= "WHERE (brandname >= 'ㅅ' AND brandname < 'ㅇ') OR (brandname >= '사' AND brandname < '아') ";
							if($seachIdx == "ㅇ") $sql.= "WHERE (brandname >= 'ㅇ' AND brandname < 'ㅈ') OR (brandname >= '아' AND brandname < '자') ";
							if($seachIdx == "ㅈ") $sql.= "WHERE (brandname >= 'ㅈ' AND brandname < 'ㅊ') OR (brandname >= '자' AND brandname < '차') ";
							if($seachIdx == "ㅊ") $sql.= "WHERE (brandname >= 'ㅊ' AND brandname < 'ㅋ') OR (brandname >= '차' AND brandname < '카') ";
							if($seachIdx == "ㅋ") $sql.= "WHERE (brandname >= 'ㅋ' AND brandname < 'ㅌ') OR (brandname >= '카' AND brandname < '타') ";
							if($seachIdx == "ㅌ") $sql.= "WHERE (brandname >= 'ㅌ' AND brandname < 'ㅍ') OR (brandname >= '타' AND brandname < '파') ";
							if($seachIdx == "ㅍ") $sql.= "WHERE (brandname >= 'ㅍ' AND brandname < 'ㅎ') OR (brandname >= '파' AND brandname < '하') ";
							if($seachIdx == "ㅎ") $sql.= "WHERE (brandname >= 'ㅎ' AND brandname < 'ㅏ') OR (brandname >= '하' AND brandname < '') ";
							$sql.= "ORDER BY brandname ";
						} else if($seachIdx == "기타") {
							$sql.= "WHERE (brandname < 'ㄱ' OR brandname >= 'ㅏ') AND (brandname < '가' OR brandname >= '') AND (brandname < 'a' OR brandname >= '{') AND (brandname < 'A' OR brandname >= '[') 
							ORDER BY brandname ";
						} else if(preg_match("/^[A-Z]/i", $seachIdx)) {
							$sql.= "WHERE brandname LIKE '{$seachIdx}%' OR brandname LIKE '".strtolower($seachIdx)."%' 
							ORDER BY brandname ";
						} else {
							$sql.= "ORDER BY brandname ";
						}
						

						$result=pmysql_query($sql,get_db_conn());
						while($row=pmysql_fetch_object($result)) {
							$brandopt .= "<option value=\"{$row->bridx}\">{$row->brandname}</option>\n";
						}

						if(ord($brandopt && $seachIdx == "전체")) {
							//$brandopt = "<option value=\"{$seachIdx}\">------------ {$seachIdx} 브랜드 일괄 개별디자인 ------------</option>\n".$brandopt;
						}
						echo $brandopt;
					?>
						</select></td>
						<td width="40" align="center" nowrap valign="top">
						<table border=0 cellpadding=0 cellspacing=0 width="100%">
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㄱ');"><span id="ㄱ">ㄱ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㄴ');"><span id="ㄴ">ㄴ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㄷ');"><span id="ㄷ">ㄷ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㄹ');"><span id="ㄹ">ㄹ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅁ');"><span id="ㅁ">ㅁ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅂ');"><span id="ㅂ">ㅂ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅅ');"><span id="ㅅ">ㅅ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅇ');"><span id="ㅇ">ㅇ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅈ');"><span id="ㅈ">ㅈ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅊ');"><span id="ㅊ">ㅊ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅋ');"><span id="ㅋ">ㅋ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅌ');"><span id="ㅌ">ㅌ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅍ');"><span id="ㅍ">ㅍ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('ㅎ');"><span id="ㅎ">ㅎ</span></a></b></td></tr>
						<tr><td align="center"><b><a href="javascript:SearchSubmit('기타');"><span id="기타">기타</span></a></b></td></tr>
						</table>
						</td>
					</tr>
					</table>
					</TD>
					<TD align="center" width="55"><img src="images/btn_next1.gif" border="0"></TD>
					<TD  align="center" bgcolor="#f8f8f8" style="padding:8,8,0,8" width="48%">
					<p align="center"><img id="preview_img" style="display:none" border="0" vspace="0" class="imgline"></p>
					<br><p align="left"><b>&quot;전체 브랜드 일괄 개별디자인&quot; </b>을 적용할 경우 개별 디자인 사용중인 브랜드를 제외한 템플릿을 사용하는 모든 브랜드가 개별디자인으로 일괄 변경됩니다.</p></TD>
				</TR>
				</TABLE>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td><IFRAME name="MainPrdtFrame" Id="MainPrdtFrame" src="design_eachblist.list.php" width=100% height=350 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
				<div class="sub_manual_wrap">
					<div class="title"><p>매뉴얼</p></div>
					<dl>
					  <dt><span>상품브랜드 화면 매크로명령어</span></B>(해당 매크로명령어는 다른 페이지 디자인 작업시 사용이 불가능함)</p></dt>
                      <dd>
						<table border=0 cellpadding=0 cellspacing=0 width=100%>
						<col width=150></col>
						<col width=></col>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDNAME]</td>
							<td class=td_con1 style="padding-left:5;">
							현재 브랜드/카테고리명
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDNAVI??????_??????]</td>
							<td class=td_con1 style="padding-left:5;">
							브랜드 네비게이션 
									<br><img width=10 height=0>
									<FONT class=font_orange>앞?????? : 홈 또는 현재 브랜드 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
									<br><img width=10 height=0>
									<FONT class=font_orange>뒤?????? : 현재 브랜드 또는 현재 브랜드가 속한 카테고리 색상</FONT> - <FONT COLOR="red">"#"제외</FONT>
									<br>
									<FONT class=font_blue>예) [BRANDNAVI] or [BRANDNAVI000000_FF0000]</FONT>
							</td>
						</tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[CLIPCOPY]</td>
							<td class=td_con1 style="padding-left:5;">
							현재주소 복사 버튼 <FONT class=font_blue>(예:&lt;a href=[CLIPCOPY]>주소복사&lt;/a>)</font>
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDEVENT]</td>
							<td class=td_con1 style="padding-left:5;">
							브랜드별 이벤트 이미지/html
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right style="padding-right:15">[BRANDGROUP]</td>
							<td class=td_con1 style="padding-left:5;">
							상품 브랜드 카테고리 그룹
							</td>
						</tr>
						<tr><td colspan=2 height=1 bgcolor=#dddddd></td></tr>
						<tr>
							<td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">상품 브랜드 카테고리 그룹 관련 스타일 정의</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
										<img width=10 height=0>
										<FONT class=font_orange>#group1_td - 상위카테고리 TD 스타일 정의 (사이즈 및 백그라운드컬러)</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>예) #group1_td { background-color:#E6E6E6;width:25%; }</FONT>
										<br><img width=0 height=7><br><img width=10 height=0>
										<FONT class=font_orange>#group2_td - 하위카테고리 TD 스타일 정의 (사이즈 및 백그라운드컬러)</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>예) #group2_td { background-color:#EFEFEF; }</FONT>
										<br><img width=0 height=7><br><img width=10 height=0>
										<FONT class=font_orange>#group_line - 상위그룹과 상위그룹 사이의 가로라인 셀 스타일 정의</FONT>
										<br><img width=100 height=0>
										<FONT class=font_blue>예) #group_line { background-color:#FFFFFF;height:1px; }</FONT>
				<pre style="line-height:15px">
<B>[사용 예]</B> - 내용 본문에 아래와 같이 정의하시면 됩니다.

<FONT class=font_blue>&lt;style>
  #group1_td { background-color:#E6E6E6;width:25%; }
  #group2_td { background-color:#EFEFEF; }
  #group_line { background-color:#FFFFFF;height:1px; }
&lt;/style></FONT></pre>
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
							<td class=table_cell align=right bgcolor=#E9A74E style="padding-right:15">상품목록 스타일 정의</td>
							<td class=td_con1 bgcolor=#FEEEE2 style="padding-left:5;">
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
                    	<dt><span>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!</dt>
                    </dl>
                </div>				
                </td>
			</tr>
			<tr>
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
<form name=form2 action="" method=post>
<input type=hidden name=mode>
<input type=hidden name=code>
</form>
</table>
<script language="javascript">
<!--
<?php
	if(ord($seachIdx)) {
		echo "document.getElementById(\"$seachIdx\").style.color=\"#FF4C00\";";
	} else {
		echo "document.getElementById(\"TTL\").style.color=\"#FF4C00\";";
	}
?>
//-->
</script>
<?=$onload?>
<?php 
include("copyright.php");