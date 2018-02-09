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
$intro=$_POST["intro"];

$intropath=$Dir.DataDir."design/intro.htm";

if ($type=="insert" && ord($intro)) {
	########################### TEST 쇼핑몰 확인 ##########################
	DemoShopCheck("데모버전에서는 테스트가 불가능 합니다.", $_SERVER['PHP_SELF']);
	#######################################################################

	$intro = stripslashes($intro);
	file_put_contents($intropath,$intro);
	$onload="<script>window.onload=function(){alert(\"인트로 화면 디자인이 완료되었습니다.\");}</script>";
} elseif($type=="delete" && file_exists($intropath)) {
	unlink($intropath);
	$onload="<script>window.onload=function(){alert(\"인트로 페이지 삭제가 완료되었습니다.\");}</script>";
}

$intro="";
if(file_exists($intropath)){
	$intro = file_get_contents($intropath);
}

include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(type) {
	if(type=="insert") {
		if(document.form1.intro.value.length==0) {
			alert("인트로 페이지 내용을 입력하세요.");
			document.form1.intro.focus();
			return;
		}
		document.form1.type.value=type;
		document.form1.submit();
	} else if(type=="delete") {
		if(confirm("인트로 페이지를 삭제하시겠습니까?")) {
			document.form1.type.value=type;
			document.form1.submit();
		}
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 개별디자인-메인, 카테고리 &gt;<span>인트로 화면 꾸미기</span></p></div></div>

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
					<div class="title_depth3">인트로 화면 꾸미기</div>
                </td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>인트로 페이지를 관리하실 수 있습니다.</span></div>
				</td>
			</tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">
						인트로 화면 꾸미기
					</div>
				</td>
			</tr>
			<tr>
				<td>
						<div class="help_info01_wrap">
							<ul>
								<li>1) 인트로 화면 : 홈페이지 또는 쇼핑몰 메인 전에 별도의 소개페이지 입니다. 플래시나 홈페이지 타입의 내용으로 디자인을 할 수 있습니다.</li>
								<li>2) 인트로 디자인을 적용하면 곧바로 쇼핑몰 앞에 입력한 디자인 내용이 출력됩니다.<br />&nbsp;&nbsp;
    입력란에 스페이스바로 공백처리만 하고 [적용하기]를 하면 내용이 없는 것처럼 보이나 인트로에 빈 화면으로 처리됩니다. 이 경우 반드시 [삭제하기] 를 클릭해주세요.</li>
                                <li>3) [삭제하기]는 디자인내용이 별도 보관되지 않으니 필요한 경우 삭제전 소스를 복사하여 따로 보관하시기 바랍니다.
</li>
								<li>4) 인트로 화면 꾸미기는 성인쇼핑몰 사용시 적용되지 않습니다.
</li>
							</ul>
						</div>
				</td>
			</tr>
			<tr><td height="3"></td></tr>

			<tr><td height="3"></td></tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
<tr>
				<td style="padding-top:3px;"><textarea name=intro style="WIDTH: 100%; HEIGHT: 600px" class="textarea"><?=$intro?></TEXTAREA></td>
			</tr>
			<tr><td height=10></td></tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm('insert');"><img src="images/botteon_save.gif" border="0"></a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:CheckForm('delete');"><img src="images/botteon_del.gif" border="0" hspace="2"></a></td>
			</tr>
			</form>
			<tr><td height=20></td></tr>
			<tr>
				<td>
                <!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
                        <dt>인트로 페이지에서  쇼핑몰 메인페이지로 링크 방법 : 쇼핑몰 주소/index.php</dt>
                        <dd><b>- 링크태그 : </b><font color="#FF6000">&lt;a href="http://<?=$_ShopInfo->getShopurl()?>index.php"&gt;쇼핑몰 메인</a></font>
                        </dd>
                        </dl>
                        <dl>
                        <dt>쇼핑몰에서 제공되는 플래시출력 자바스크립트 사용을 할 경우 아래의 태그를 추가 후 사용해 주세요.
                        </dt>
                        <dd>
                        <table>
					<tr>
						<td width="796" class="space_top">
						  &nbsp;&nbsp;&nbsp;<b>ㆍ플래시 간단 출력 방법</b><br><span class="font_blue">
						    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;script&gt;<br>
						    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;flash_show("플래시파일경로","가로크기","세로크기");<br>
						    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/script&gt;</span>
						  </td>
					</tr>
					<tr>
					  <td width="796" class="space_top">
					    &nbsp;&nbsp;&nbsp;<b>ㆍ플래시 상세 출력 방법(파라미터 추가)</b><br><span class="font_blue">
					      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;script&gt;<br>
					      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj=new embedcls();<br>
					      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.init("플래시파일경로","가로크기","세로크기");<br>
					      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.setparam("파라미터명","파라미터값");<br>
					      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.setparam("파라미터명","파라미터값");<br>
					      <span style="line-height:5px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
					        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
					        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
					        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br>
					        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.<br></span>
					      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;embedobj.show();<br>
					      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&lt;/script&gt;</span>
					    </td>
					  </tr>

					</table>
                    </dd>
                    </dl>
                    <dl>
                    <dt>
                    웹FTP로 intro.htm 이라는 파일을 제작하여 /data/design/ 폴더에 업로드 해도 인트로 페이지가 작동됩니다.</dt></dl>
                    <dl><dt>인트로 페이지에 사용할 이미지는 웹FTP로 업로드하여 사용하시면 됩니다.</dt></dl>
                    <dl><dt>나모,드림위버등의 에디터로 작성시 이미지경로등 작업내용이 틀려질 수 있으니 주의하세요!
                    </dt></dl>
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