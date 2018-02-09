<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/adminlib.php");
include("access.php");


####################### 페이지 접근권한 check ###############
$PageCode = "co-1";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$no=$_POST[no];
$mode=$_POST[mode];

if(!$mode=="cwl_cate_mod"){
	$mode="cwl_cate_ins";
}

$category_row=pmysql_fetch_object(pmysql_query("select * from tblcwlcategory where num={$no}"));


//운영자 레피시 불러오기
$sql0 = "select COUNT(*) as t_count from tblcwlcategory ";
$paging = new newPaging($sql0,10,15);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$query="select * from tblcwlcategory ";
$query.=" order by sort_num";
$query = $paging->getSql($query);
$result=pmysql_query($query);

$imagepath=$Dir.DataDir."shopimages/cwl/category/";

include"header.php"; 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function GoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function cwl_del(no){
	if(confirm("해당 카테고리를 삭제 하시겠습니까?")){
		document.form2.no.value=no;
		document.form2.mode.value="cwl_cate_del";
		document.form2.submit();	
	}

}

function cwl_mod(no){
	document.form1.no.value=no;
	document.form1.mode.value="cwl_cate_mod";
	document.form1.submit();
}

function cwl_indb(no){
	document.form1.no.value=no;
	document.form1.action="cwl_indb.php";
	document.form1.submit();
}

function secret_change(){
	document.form1.mode.value="cwl_cate_secret";
	document.form1.action="cwl_indb.php";
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>COLOR WE LOVE 카테고리 관리</span></p></div></div>
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
			<?php include("menu_community.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">COLOR WE LOVE 카테고리 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>COLOR WE LOVE 카테고리를 변경 및 삭제처리를 할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=no value="<?=$category_row->no?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">			
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">COLOR WE LOVE 카테고리 등록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th><span>카테고리(색상)</span></th>
					<TD><INPUT maxLength=50 size=20 name=category_name class="input_selected" value="<?=$category_row->category_name?>"></TD>
				</tr>
				<tr>
					<th><span>아이콘 이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="icofile" style="WIDTH: 400px"><br>
						<span class="font_orange">(권장이미지 : 30px X 32px)</span>
						<input type=hidden name="vicoImage" value="<?=$category_row->icoimage?>">
	<?php
				if ($category_row) {
					if ( ord($category_row->icoimage) && file_exists($imagepath.$category_row->icoimage) ){
						echo "<br><img src='".$imagepath.$category_row->icoimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$category_row->icoimage}' style=\"width:30px\">";
						echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
					} else {
						echo "<br><img src=images/space01.gif>";
					}
				}
	?>
					</td>
				</tr>
				<tr>
					<th><span>상세 이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="infofile" style="WIDTH: 400px"><br>
						<span class="font_orange">(권장이미지 : 84px X 90px)</span>
						<input type=hidden name="vinfoImage" value="<?=$category_row->infoimage?>">
	<?php
				if ($category_row) {
					if ( ord($category_row->infoimage) && file_exists($imagepath.$category_row->infoimage) ){
						echo "<br><img src='".$imagepath.$category_row->infoimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$category_row->infoimage}' style=\"width:84px\">";
						echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
					} else {
						echo "<br><img src=images/space01.gif>";
					}
				}
	?>
					</td>
				</tr>
				
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center><a href="javascript:cwl_indb('<?=$no?>');">
			<?if($mode=="cwl_cate_ins"){?>
				<img src="images/botteon_add.gif">
			<?}else{?>
				<img src="images/botteon_catemodify.gif">
			
			<?}?>
					
				</a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=50></col>
				<col width=70></col>
				<col width=></col>
				<col width=50></col>
				<col width=50></col>
				<col width=100></col>
				<col width=60></col>
				<col width=60></col>
				<TR align=center>
					<th>번호</th>
					<th>&nbsp;</th>
					<th>카테고리명</th>
					<th>순서</th>
					<th>노출</th>
					<th>등록일</th>
					<th>수정</th>
					<th>삭제</th>
					
				</TR>
<?php
				$cnt=0;
				while($data=pmysql_fetch_object($result)){
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$regdt = substr($data->date,0,4)."-".substr($data->date,4,2)."-".substr($data->date,6,2);
					if($data->secret)$checked[$data->num]="checked";
					if($data->cwl_best=="Y")$checked="checked";
					echo "<tr>";
					echo "<input type='hidden' name='num[]' value='{$data->num}'>";
					echo "<td>{$number}</td>";
					if ( ord($data->icoimage) && file_exists($imagepath.$data->icoimage) ){
						echo "<td><img src='".$imagepath.$data->icoimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/cwl/category/{$data->icoimage}' style=\"width:30px\"></td>";
					} else {
						echo "<td>&nbsp;</td>";
					}
					echo "<td style='text-align:left'>{$data->category_name}</td>";
					echo "<td><input type='text' name='sort_num[{$data->num}]' value='{$data->sort_num}' size='5'></td>";
					echo "<td><input type=checkbox name='secret[]' value='{$data->num}' {$checked[$data->num]}></td>";
					echo "<td>{$regdt}</td>";
					echo "<td><a href=\"javascript:cwl_mod('{$data->num}')\"><img src=\"images/btn_edit.gif\"></a></td>";
					echo "<td><a href=\"javascript:cwl_del('{$data->num}')\"><img src=\"images/btn_del.gif\"></a></td>";
					echo "</tr>";
					
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<TR><TD colspan=\"8\" align=center>등록된 카테고리가 존재하지 않습니다.</TD></TR>";
				}
?>				

				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
			<td>
			<?
			
			echo "<div id=\"page_navi01\" style=\"height:'40px'\">";
			echo "<div class=\"page_navi\">";
			echo "<ul>";
			echo "	".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
			echo "</ul>";
			echo "</div>";
			echo "</div>";
				
			?>
			</td>
			</tr>
			<tr>
				<td colspan=8 align=right><a href="javascript:secret_change();"><img src="images/botteon_save.gif"></a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						
						<dl>
							<dt><span>COLOR WE LOVE 카테고리 관리</span></dt>
							<dd>- <br>
							- <br>
							- 
							</dd>	
						</dl>

					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</form>
			<form name=form2 method=post action="cwl_indb.php">
			<input type=hidden name=no>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=category_name>

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
