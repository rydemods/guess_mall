<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$seachIdx=$_POST["seachIdx"];

if(ord($seachIdx)==0) {
	$seachIdx = "A";
}
?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
<!--
function SearchSubmit(seachIdxval) {
	form = document.form1;
	form.mode.value="";
	form.seachIdx.value = seachIdxval;
	form.submit();
}

function CodeProcessFun(brandselectedIndex,brandcode) {
	if(brandselectedIndex>-1) {
		document.form2.mode.value="";
		document.form2.code.value=brandcode;
		document.form2.target="ListFrame";
		document.form2.action="market_eventbrand.add.php";
		document.form2.submit();
	}
}
//-->
</script>

<STYLE type=text/css>
	#menuBar {}
	#contentDiv {WIDTH: 200;HEIGHT: 320;}
</STYLE>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>브랜드별 이벤트 관리</span></p></div></div>
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
			<input type=hidden name=mode value="">
			<input type=hidden name=seachIdx value="">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">브랜드별 이벤트 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>각 브랜드별 페이지 상단에 이미지 또는 Html 편집을 통해 이벤트를 관리 하실 수 있습니다.</span></div>
				</td>
			</tr>
			<tr>
				<td>

				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="242" valign="top" height="100%">
					<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="232" height="100%" valign="top">
						<table cellpadding="0" cellspacing="0" width="242">
						<tr>
							<td bgcolor="white">
								<!-- 소제목 -->
								<div class="title_depth3_sub">전체 브랜드</div>
							</td>
						</tr>
						<tr>
							<TD valign="top">

							<div class="bd_editer" style="padding:8px; width:260px; ">
								<select name="up_brandlist" size="20" style="width:240px; height:200px;" onchange="CodeProcessFun(this.selectedIndex,this.value);">
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
									$sql.= "WHERE (brandname < 'ㄱ' OR brandname >= 'ㅏ') AND (brandname < '가' OR brandname >= '') AND (brandname < 'a' OR brandname >= '{') AND (brandname < 'A' OR brandname >= '[') ";
									$sql.= "ORDER BY brandname ";
								} else if(preg_match("/^[A-Z]/", $seachIdx)) {
									$sql.= "WHERE brandname LIKE '{$seachIdx}%' OR brandname LIKE '".strtolower($seachIdx)."%' ";	
									$sql.= "ORDER BY brandname ";
								}

								$result=pmysql_query($sql,get_db_conn());
								while($row=pmysql_fetch_object($result)) {
									$brandopt .= "<option value=\"{$row->bridx}\">{$row->brandname}</option>\n";
								}

								if(ord($brandopt)) {
									$brandopt = "<option value=\"{$seachIdx}\">---- {$seachIdx} 브랜드 일괄 적용 ----</option>\n".$brandopt;
								}
								echo $brandopt;
							?>
							</select>
								<table border=0 cellpadding=0 cellspacing=0 width="100%"  bgcolor=f5f5f5>
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
									</tr>
									<tr align="center">
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
								
							<table border=0 cellpadding=0 cellspacing=0 width="100%"  bgcolor=f5f5f5>
							<tr>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㄱ');"><span id="ㄱ">ㄱ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㄴ');"><span id="ㄴ">ㄴ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㄷ');"><span id="ㄷ">ㄷ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㄹ');"><span id="ㄹ">ㄹ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅁ');"><span id="ㅁ">ㅁ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅂ');"><span id="ㅂ">ㅂ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅅ');"><span id="ㅅ">ㅅ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅇ');"><span id="ㅇ">ㅇ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅈ');"><span id="ㅈ">ㅈ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅊ');"><span id="ㅊ">ㅊ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅋ');"><span id="ㅋ">ㅋ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅌ');"><span id="ㅌ">ㅌ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅍ');"><span id="ㅍ">ㅍ</span></a></b></td>
								<td align="center" style="font-size:14px;line-height:21px;"><b><a href="javascript:SearchSubmit('ㅎ');"><span id="ㅎ">ㅎ</span></a></b></td>
								<td align="center" style="line-height:21px;"><b><a href="javascript:SearchSubmit('기타');"><span id="기타">기타</span></a></b></td>
							</tr>
							</table>
							</div>

							</TD>
						</tr>
						</table>
						</td>
					</tr>
					</table>
					</td>
					<td width="15"><img src="images/btn_next1.gif" border="0" hspace="5"><br></td>
					<td width="100%" valign="top" height="100%"><IFRAME name="ListFrame" id="ListFrame" src="market_eventbrand.add.php" width=100% height=300 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME></td>
				</tr>
				</table>
				</td>
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
							<dt><span>브랜드별 이벤트 관리</span></dt>
							<dd>
								- 이미지 또는 Html 편집을 하시면 각 브랜드 상단을 다양하게 꾸미실 수 있습니다.<br>
								- 브랜드별 이벤트는 "상품 브랜드 템플릿" 사용시에만 출력됩니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_blist.php');"><span class="font_blue">디자인 관리 > 템플릿-메인 및 카테고리 > 상품 브랜드 템플릿</span></a><br>
								- 개별 디자인 사용시 "상품 브랜드 화면 꾸미기"에서 해당 매크로를 이용하시면 출력이 가능합니다.<br>
						<b>&nbsp;&nbsp;</b><a href="javascript:parent.topframe.GoMenu(2,'design_eachblist.php');"><span class="font_blue">디자인 관리 > 개별디자인-페이지 본문 > 상품브랜드 화면 꾸미기</span></a>
							</dd>
							
						</dl>
						</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			<form name=form2 action="" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=code>
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
