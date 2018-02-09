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
$engine=$_POST["engine"];

if($type=="update") {
	$success = WriteEngine($engine, "engineinfo.db");
	if($success) {
		$onload="<script>window.onload=function(){ alert('정상적으로 적용 됐습니다.'); }</script>";
	} else {
		alert_go('예기치 못한 오류로 인해서 저장되지 못 했습니다.',-1);
	}
}

$engineval = ReadEngine("engineinfo.db");
?>

<?php include("header.php"); ?>
<script>try {parent.topframe.ChangeMenuImg(7);}catch(e){}</script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="calendar.js.php"></script>
<script language="JavaScript">

function CheckForm(type) {
	if(confirm("가격비교페이지 관리 내용을 적용하겠습니까?"))
	{
		document.form1.type.value=type;
		document.form1.submit();
	}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 마케팅지원 &gt;<span>가격비교페이지 관리</span></p></div></div>
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
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">가격비교 페이지 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>가격비교 서비스 업체에 제공할 상품 정보 페이지를 관리합니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">가격비교 페이지목록<span>가격비교 서비스 업체에 제공할 페이지를 선택해 주세요.</span></div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=40></col>
				<col width=40></col>
				<col width=200></col>
				<col width=></col>
				<col width=80></col>
				<TR align="center">
					<th>No</th>
					<th>사용</th>
					<th>가격비교 업체명</th>
					<th>가격비교 페이지 주소</th>
					<th>미리보기</th>
				</TR>
<?php
				$colspan=5;
				
				$engine_unique = array("omi","naver","naversub","nawayo","yahoo","danawae","danawap","enuri","mymargin","bestbuyer","yavis","shopbinder","linkprice","plusmall","gaenawa");
				$engine_data = array(
				"오미"						=> "http://{$shopurl}shopping/omi_ufo.php",
				"네이버(전체)"				=> "http://{$shopurl}shopping/naver.php",
				"네이버(요약)"				=> "http://{$shopurl}shopping/naver_sub.php",
				"나와요"					=> "http://{$shopurl}shopping/nawayo.php",
				"야후"						=> "http://{$shopurl}shopping/yahoo.php",
				"다나와-가전,비가전"		=> "http://{$shopurl}shopping/danawa_elec.php",
				"다나와-PC"					=> "http://{$shopurl}shopping/danawa_pc.php",
				"에누리"					=> "http://{$shopurl}shopping/enuri.php",
				"마이마진"					=> "http://{$shopurl}shopping/mymargin.php",
				"베스트바이어"				=> "http://{$shopurl}shopping/bestbuyer.php",
				"야비스"					=> "http://{$shopurl}shopping/yavis.php",
				"샵바인더"					=> "http://{$shopurl}shopping/shopbinder.php",
				"링크프라이스"				=> "http://{$shopurl}shopping/linkprice.php",
				"플러스몰"					=> "http://{$shopurl}shopping/plusmall.php",
				"개나와(애견)"				=> "http://{$shopurl}shopping/gaenawa.php"
				);


				$cnt=0;
				while(list($key, $value) = each($engine_data)) {
					echo "<tr align=\"center\">\n";
					echo "	<td>".($cnt+1)."</td>\n";
					echo "	<td><input type=\"checkbox\" name=\"engine[{$engine_unique[$cnt]}]\" value=\"checked\" {$engineval[$engine_unique[$cnt]]}></td>\n";
					echo "	<td>{$key}</td>\n";
					echo "	<td style=\"".(ord($engineval[$engine_unique[$cnt]])?"color:#00A0D5;font-weight:bold;":"")."\"><div class=\"ta_l\">&nbsp;&nbsp;{$value}</div></td>\n";
					echo "	<td>&nbsp;".(ord($engineval[$engine_unique[$cnt]])?"<a href=\"{$value}\" target=\"_blank\" style=\"".(ord($engineval[$engine_unique[$cnt]])?"color:#00A0D5;font-weight:bold;":"")."\">[미리보기]</a>":"")."</td>\n";
					echo "</tr>\n";
					$cnt++;
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<tr>
				<td align=center><a href="javascript:CheckForm('update');"><img src="images/botteon_save.gif" border="0"></a></td>
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
							<dt><span>가격비교페이지 관리</span></dt>
							<dd>
								- 사용에 체크된 가격비교 업체 페이지만 사용이 가능합니다.<br>
								- 일부 가격비교 페이지의 경우 미리보기 데이타가 일부 출력이 안되는 부분이 있습니다. 서비스 이용과는 무관합니다.<br>
								- 가격비교 서비스는 해당 업체와 추가로 계약을 해야만 정상서비스가 이뤄집니다.
							</dd>
							
						</dl>
						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
