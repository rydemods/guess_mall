<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/premiumbrand.class.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "co-2";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
include("header.php"); 
$imagepath = $Dir.DataDir."shopimages/mainbanner/";
$pb = new PREMIUMBRAND('pb_list');
$pb_list = $pb->pb_list;
?>

<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script> 

<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리  &gt; 프리미엄브랜드 관리 &gt; <span>프리미엄브랜드 리스트</span></p></div></div>
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
					<div class="title_depth3">프리미엄 브랜드 리스트</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>등록된 프리미엄 브랜드를 조회 및 관리 할 수 있습니다</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">프리미엄 브랜드 리스트 </span></div>
				</td>
			</tr>

			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
			
				<TR >
					<th>번호</th>
					<th>로고</th>
					<th>브랜드명</th>
				</TR>
			<?foreach($pb_list as $key=>$val){?>
				 <tr>
					<td width=100><?=$key+1?></td>
					<td><img src='<?=$imagepath.$val->banner_img?>' style='max-width : 70px;' ></td>
					<td><a style="cursor:pointer;" data-index="<?=$val->no?>" class='view_brand'><?=$val->banner_title?></a></td>
				</tr>
			<?}?>

				</TABLE>
				</div>
				</td>
			</tr>

			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php				
	
			
			echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
			echo "<div class=\"page_navi\">";
			echo "<ul>";
			echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "</ul>";
			echo "</div>";
			echo "</div>";
				
		
?>
				</table>
				</td>
			</tr>
			<!-- <input type=hidden name=tot value="<?=$cnt?>"> -->
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
							<dt><span>-</span></dt>
							<dd>-</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr>
				<td height="50"></td>
			</tr>
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

<form name="form1" method=post>
<input type=hidden name=mode value='modify_form'>
<input type=hidden name=block>
<input type=hidden name=gotopage>
</form>

<script>

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function view_brand()
{
	var brand_no = $(this).data('index');
	window.open('premiumbrand_view.php?brand_no='+brand_no,'_blank','width=1000,height=1000,scrollbars=yes');
}

$(document).on("click",".view_brand",view_brand);

</script>

<?=$onload?>
<?php 
include("copyright.php");
?>
