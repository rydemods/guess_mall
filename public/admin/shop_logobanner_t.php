<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-2";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$bannerimagepath = $Dir."skin/img/banner/";

$type=$_POST["type"];
$up_image=$_FILES["up_image"];
$up_url_type=$_POST["up_url_type"];
$up_url=$_POST["up_url"];
$up_target=$_POST["up_target"];

$CurrentTime = date("YmdHis");

if ($type=="bannerdel") {
	if ($up_url) {
		$sql = "SELECT image FROM tblbanneradd ";
		$sql.= "WHERE num = '{$up_url}'";
		$result = pmysql_query($sql,get_db_conn());
		if($row=pmysql_fetch_object($result)) {
			if($row->image && file_exists($bannerimagepath.$row->image)) {
				unlink($bannerimagepath.$row->image);
			}
		}
		pmysql_free_result($result);
		$sql = "DELETE FROM tblbanneradd WHERE date = '{$up_url}'";
		pmysql_query($sql,get_db_conn());
		$onload = "<script>alert('배너 삭제가 완료되었습니다.');</script>";
	}
} else if ($type=="banneradd") {
	if($up_image['name'] && $up_url) {
		if (strpos($up_image['name'],"html") || strpos($up_image['name'],"php") || strpos($up_image['name'],"htm")) $up_image['name'] = $up_image['name']."_";
		$banner_ext= strtolower(substr($up_image['name'],-4));
		if($banner_ext!=".gif" && $banner_ext!=".jpg" && $banner_ext!=".png"){
			$onload = "<script>alert (\"올리실 이미지는 gif파일만 가능합니다.\");</script>";
		} else if ($up_image['size']>153600) {
			$onload = "<script>alert (\"올리실 이미지 용량은 150KB 이하의 파일만 가능합니다.\");</script>";
		} else {
			$sql = "SELECT COUNT(*) as cnt FROM tblbanneradd ";
			$result = pmysql_query($sql,get_db_conn());
			$row = pmysql_fetch_object($result);
			pmysql_free_result($result);
			$cnt=(int)$row->cnt;
			if ($cnt<10) {
				$banner_name = $up_image['name'];
				move_uploaded_file($up_image['tmp_name'],$bannerimagepath.$banner_name); 
				chmod($bannerimagepath.$banner_name,0606);
				$sql = "INSERT INTO tblbanneradd(
				date		,
				image		,
				url_type	,
				url			,
				target) VALUES (
				'{$CurrentTime}', 
				'{$banner_name}', 
				'{$up_url_type}', 
				'{$up_url}',
				'{$up_target}')";
				pmysql_query($sql,get_db_conn());
				$onload="<script>alert('배너 등록이 완료되었습니다.');</script>";
			} else {
				$onload="<script>alert('배너 등록은 최대 10개까지만 등록이 가능합니다.');</script>";
			}
		}
	}
}

?>

<?php include("header.php"); ?>

<script type="text/javascript" src="lib.js.php"></script>
<script>
function BannerDel(date) {
	if(confirm("배너를 삭제하시겠습니까?")) {
		form1.type.value="bannerdel";
		form1.up_url.value = date;
		form1.submit();
	}
}

function BannerAdd() {
	if(!form1.up_image.value){
		alert('배너 이미지를 등록하세요');
		form1.up_image.focus();
		return;
	}
	if(!form1.up_url.value){
		alert('배너에 연결할 URL를 입력하세요. \n(예: www.abc.com)');
		form1.up_url.focus();
		return;
	}
	form1.type.value="banneradd";
	form1.submit();
}

function GoPage(block,gotopage) {
    document.form1.block.value = block;
    document.form1.gotopage.value = gotopage;
    document.form1.submit();
}

function toclip(id){
	if(navigator.appName=="Microsoft Internet Explorer"){
		var idxs = document.getElementById(id);
		if(idxs.value==''){
			return;
		}
		
		idxs.select();
		var clip=idxs.createTextRange();
		//alert();
		clip.execCommand('copy');
		
		alert('이미지 주소가 복사되었습니다. ctrl+V로 붙여넣기 하세요.');
	}else{
		prompt("이 배너의 이미지 링크 소스입니다. Ctrl+C를 눌러 복사하세요", document.getElementById(id).value);		
	}

	/*
	
	*/
}
</script>
 <script type="text/javascript">
    $(function(){
        var offsetX = 20;
        var offsetY = 10;
        
        $('img.imgline').hover(function(e){
            //mouse on
            var href = $(this).attr('src');
			$('<img id="largeImage" src="' + href + '">').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX).appendTo('body');
        }, function(){
            //mouse off
            $('#largeImage').remove();
        });
        
        $('img.imgline').mousemove(function(e){
            $('#largeImage').css('top', e.pageY + offsetY).css('left', e.pageX + offsetX);
        })
        
    });
</script>

<style type="text/css">
            a img {
                border: none;
            } #largeImage {
                position: absolute;
                padding: .5em;
                background: #e3e3e3;
                border: 1px solid;
            }
</style>



<div class="admin_linemap"><div class="line"><p>현재위치 : 상점관리 &gt; 쇼핑몰 환경 설정 &gt;<span>배너 관리</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">배너 관리</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰의 배너를 등록/관리하실 수 있습니다.</span></div>
				</td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=place>
			<input type=hidden name=bannerplace>
			<input type=hidden name=bannerdate>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쇼핑몰 배너 관리</div>
				</td>
			</tr>
			<tr>
				<td>
                <div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th>배너이미지</th>
					<th>링크주소</th>
					<th>이미지 링크 소스</th>					
					<th>복사</th>
					<th>삭제</th>
				</TR>
				
<?php
	$sql0 = "SELECT COUNT(*) as cnt FROM tblbanneradd ";
	$paging = new Paging($sql0,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;		
	
	$result = pmysql_query($sql0,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	$cnt = $row->cnt;

	$sql = "SELECT * FROM tblbanneradd ORDER BY date DESC";
	
	
	$sql = $paging->getSql($sql);
	$result = pmysql_query($sql,get_db_conn());
	$count=1;
	
	while($row=pmysql_fetch_object($result)){
		$image = $row->image;
		$url = $row->url;
		
		$urltype=$row->url_type=="S"?"s":"";
		$imglink="<a href=\"http".$urltype."://".$row->url."\" target=\"".$row->target."\"><img src=\"/skin/img/banner/".$row->image."\"></a>";
?>
				<TR>
					
					<TD><img src="<?=$bannerimagepath.$image?>" class="imgline" border="0"></TD>
					<TD> <a href=http<?=($row->url_type=="S"?"s":"")?>://<?=$url?> target="_blank"><font color=#0000a0>http<?=($row->url_type=="S"?"s":"")?>://<?=$url?></font></a></TD>
					<TD><?=htmlspecialchars($imglink)?></TD>
					
					
					<input type="hidden" id="clip<?=$row->date?>" value="<?=htmlspecialchars($imglink)?>">
					<TD><p align="center"><a href="#" onclick="toclip('clip<?=$row->date?>')">클릭</a></p></TD>
					<TD><p align="center"><a href="javascript:BannerDel('<?=$row->date?>');"><img src="images/btn_del.gif" border="0"></a></p></TD>
				</TR>
<?php
		$count++;
	}
	pmysql_free_result($result);
	if($cnt==0) {
		echo "<TR><td colspan=4 align=center><font color=#383838>등록된 배너가 없습니다.</font></td></tr>";
	}
?>
				
				</TABLE>
                </div>
				</td>
			</tr>
			
			<TR>
				<TD align="center" class="font_size" style="padding-top:10pt; padding-bottom:0pt;">
                	<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                </TD>
            </TR>
			<tr>
            	<td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap">
                        <ul>
                            <li>1) <b>GIF(gif), JPG(jpg), PNG(png)파일만</b> 등록 가능합니다.</li>
                            <li>2) 배너위치가 <b>좌측 하단일 경우 가로 200픽셀</b>을 권장, <b>우측 상단일 경우 가로 180픽셀</b> 권장(세로사이즈 제한 없음).</b></li>
                            <li>3) 이미지 용량 150KB 이하.</li>
                        </ul>
                    </div>
                    
            	</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="100%" bgcolor="#0099CC">
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
						<TD width="100%"><div class="point_title">배너등록하기</div></TD>
					</TR>
                    <tr>
						<td width="100%">
                        <div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>배너 이미지</span></th>
							<TD class="td_con1" ><input type=file name=up_image style="WIDTH: 98%"></TD>
						</TR>
						<TR>
							<th><span>연결 URL</span></th>
							<TD class="td_con1"><select name=up_url_type class="select">
								<option value="H">http://
								<option value="S">https://
							</select> <input type=text name=up_url size=50 maxlength=200 onKeyUp="chkFieldMaxLen(200)" class="input" ></TD>
						</TR>									
						<TR>
							<th><span>Target</span></th>
							<TD class="td_con1">
							Target : <select name=up_target class="select">
<?php 
	$target=array("_blank","_top","_parent","_self");
	for($i=0;$i<4;$i++){
		echo "<option value=\"{$target[$i]}\">".$target[$i];
	}
?>
							</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							</TD>
						</TR>
						</TABLE>
                        </div>
						</td>
					</tr>
					</table>
					</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:3pt;"><p align="center"><a href="javascript:BannerAdd();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
			
			<tr>
				<td height="20">&nbsp;</td>
			</tr>
			<tr>
				<td>
				<!-- 메뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>배너 개별디자인</span></dt>
							<dd>
								- <a href="javascript:parent.topframe.GoMenu(2,'design_eachleftmenu.php');"><span class="font_blue">디자인관리 > 개별디자인 - 메인 및 상하단 > 왼쪽메뉴 꾸미기</span></a> 에서 직접 HTML로 디자인할 수 있습니다.<br>
								- <a href="javascript:parent.topframe.GoMenu(2,'design_easyleft.php');"><span class="font_blue">디자인관리 > Easy 디자인 관리 > Easy 왼쪽 메뉴 관리</span></a> 에서 직접 HTML로 디자인할 수 있습니다.</a>
							</dd>
						</dl>
						<dl>
							<dt><span>Target (새창)</span></dt>
							<dd>
								- <b>Target</b><b>&nbsp;</b>: 정보를 출력할 윈도우나 프레임을 입력하는 속성.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_blank</span> <b>&nbsp;</b>: 연결된 문서를 읽어 새로운 빈 윈도우에 표시한다.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_top</span> &nbsp;&nbsp;<b>&nbsp;&nbsp;</b>: 연결된 문서를 읽어 최상위 윈도우에 표시한다.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_parent</span> : 연결된 문서를 읽어 바로 위 부모창에 표시한다.<br>
								&nbsp;&nbsp;&nbsp;<span class="font_orange">_self</span> <b>&nbsp;&nbsp;&nbsp;</b>: 연결된 문서를 읽어 현재창에 표시한다.<br>
								
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
