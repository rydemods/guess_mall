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

//카테고리
function codeListScript(){
	$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
	$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ";
	$sql.= "order by code_a asc, cate_sort asc";
	$result  = pmysql_query($sql,get_db_conn());
	$i=0;
	$ii=0;
	$iii=0;
	$iiii=0;
	$strcodelist = "";
	$strcodelist.= "<script>\n";
	$selcode_name="";

	while($row=pmysql_fetch_object($result)) {
		$strcodelist.= "var clist=new CodeList();\n";
		$strcodelist.= "clist.code_a='{$row->code_a}';\n";
		$strcodelist.= "clist.code_b='{$row->code_b}';\n";
		$strcodelist.= "clist.code_c='{$row->code_c}';\n";
		$strcodelist.= "clist.code_d='{$row->code_d}';\n";
		$strcodelist.= "clist.type='{$row->type}';\n";
		$strcodelist.= "clist.code_name='{$row->code_name}';\n";
		if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
			$strcodelist.= "lista[{$i}]=clist;\n";
			$i++;
		}
		if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
			if ($row->code_c=="000" && $row->code_d=="000") {
				$strcodelist.= "listb[{$ii}]=clist;\n";
				$ii++;
			} else if ($row->code_d=="000") {
				$strcodelist.= "listc[{$iii}]=clist;\n";
				$iii++;
			} else if ($row->code_d!="000") {
				$strcodelist.= "listd[{$iiii}]=clist;\n";
				$iiii++;
			}
		}
		$strcodelist.= "clist=null;\n\n";
	}
	pmysql_free_result($result);
	$strcodelist.= "CodeInit();\n";
	$strcodelist.= "</script>\n";

	echo $strcodelist;

	echo "<select name='code_a' id='code_a' style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
	echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='code_b' id='code_b' style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
	echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='code_c' id='code_c' style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
	echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<select name='code_d' id='code_d' style=\"width:170px;\">\n";
	echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
	echo "</select>\n";

	echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
}




$no=$_POST[no];
$mode=$_POST[mode];

if(!$mode=="cwl_mod"){
	$mode="cwl_ins";
}

$board_row=pmysql_fetch_object(pmysql_query("select tcb.*, tp.productname, tp.tinyimage  from tblcwlboard as tcb left join tblproduct as tp on tcb.productcode=tp.productcode where tcb.num={$no}"));


//운영자 레피시 불러오기
$sql0 = "select COUNT(*) as t_count from tblcwlboard as tcb left join tblcwlcategory as tcc on tcb.category_num=tcc.num left join tblproduct as tp on tcb.productcode=tp.productcode ";
$paging = new newPaging($sql0,10,15,'GotoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$query="select tcb.*, tcc.category_name, tcc.icoimage, tp.productname, tp.tinyimage  from tblcwlboard as tcb left join tblcwlcategory as tcc on tcb.category_num=tcc.num left join tblproduct as tp on tcb.productcode=tp.productcode ";
$query.=" order by date DESC";
$query = $paging->getSql($query);
$result=pmysql_query($query);

$imagepath=$Dir.DataDir."shopimages/cwl/board/";
$proimagepath=$Dir.DataDir."shopimages/product/";

include"header.php"; 
?>
<style type="text/css">
	.layer {display:none; position:fixed; _position:absolute; top:0; left:0; width:100%; height:100%; z-index:100;}
	.layer .bg {position:absolute; top:0; left:0; width:100%; height:100%; background:#000; opacity:.5; filter:alpha(opacity=50);}
	.layer .pop-layer {display:block;}

	.pop-layer {display:none; position: absolute; top: 50%; left: 50%; width: 900px; height:500px;  background-color:#fff; border: 5px solid #3571B5; z-index: 10; overflow-y: scroll;}	
	.pop-layer .pop-container {padding: 20px 25px;}
	.pop-layer p.ctxt {color: #666; line-height: 25px;}
	.pop-layer .btn-r {
			/*width: 100%; margin:10px 0 20px; padding-top: 10px; border-top: 1px solid #DDD; text-align:right;*/
			position: fixed; margin-left: 843px; margin-top: -35;
	}

	a.cbtn {display:inline-block; height:25px; padding:0 14px 0; border:1px solid #304a8a; background-color:#3f5a9d; font-size:13px; color:#fff; line-height:25px;}	
	a.cbtn:hover {border: 1px solid #091940; background-color:#1f326a; color:#fff;}
	
	
	li.prListOn { position:relative; float:left; margin-right:15px; margin-bottom:5px; width:100px; height: 150px;}
	li.prListOn:before {display:block; width:1px; height:100%; content:""; background:#dbdbdb; position:absolute; top:0px; left:105px;}
</style>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
function GotoPage(block,gotopage) {
	document.form1.mode.value = "";
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

function cwl_del(no){
	if(confirm("해당 게시물을 삭제 하시겠습니까?")){
		document.form2.no.value=no;
		document.form2.mode.value="cwl_del";
		document.form2.submit();	
	}

}

function cwl_mod(no){
	document.form1.no.value=no;
	document.form1.mode.value="cwl_mod";
	document.form1.submit();
}

function cwl_indb(no){
	document.form1.no.value=no;
	document.form1.action="cwl_indb.php";
	document.form1.submit();
}

function secret_change(){
	document.form1.mode.value="cwl_secret";
	document.form1.action="cwl_indb.php";
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티 &gt; 커뮤니티 관리 &gt;<span>COLOR WE LOVE 관리</span></p></div></div>
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
					<div class="title_depth3">COLOR WE LOVE 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>COLOR WE LOVE 리스트를 변경 및 삭제처리를 할 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type="hidden" name="listMode" id="listMode" value=""/>
			<input type=hidden name=type>
			<input type=hidden name=mode value="<?=$mode?>">
			<input type=hidden name=no value="<?=$board_row->no?>">
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">			
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">COLOR WE LOVE 등록</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="layer">
					<div class="bg"></div>
					<div id="layer2" class="pop-layer">
						<div class="btn-r">
							<a href="#" class="cbtn">Close</a>
						</div>
						<div class="pop-container">
							<div class="pop-conts">
								<!--content //-->
								<p class="ctxt mb20" style="font-size:15px; font-weight: 700;">상품 선택<br>
									<?=codeListScript()?><br>
									<div>
										<input type="text" name="s_keyword" id="s_keyword" value="" style="width: 250px;"/>
										<a href="javascript:productListSearch();"><img src="images/btn_search.gif" style="position: absolute; padding-left: 5px;"/></a>
									</div>
								</p>
								<div id="productList">
									
								</div>
								<!--// content-->
							</div>
						</div>
					</div>
				</div>
				<div class="table_style01">
				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th><span>카테고리(색상)</span></th>
					<TD><select id="category_num" name="category_num">
					<option value="">======================</option>
					<?php
						$category_sql			= "select * from tblcwlcategory where secret =1 order by sort_num asc";		
						$category_result		= pmysql_query($category_sql,get_db_conn());
						while($category_row=pmysql_fetch_object($category_result)) {
							$cate_checked ="";
							if($category_row->num == $board_row->category_num) $cate_checked =" selected";
							echo "<option value=\"{$category_row->num}\"".$cate_checked.">".$category_row->category_name."</option>\n";
						}
						pmysql_free_result($category_result);
					?></select></TD>
				</tr>
				<tr>
					<th><span>제목</span></th>
					<TD><INPUT maxLength=80 size=80 name=title value="<?=$board_row->title?>"></TD>
				</tr>
				<tr>
					<th><span>이미지</span></th>
					<td class="td_con1" colspan="3" style="position:relative">
						<input type=file name="titlefile" style="WIDTH: 400px"><br>
						<!--<span class="font_orange">(권장이미지 : )</span>-->
						<input type=hidden name="vtitleImage" value="<?=$board_row->image?>">
	<?php
				if ($board_row) {
					if ( ord($board_row->image) && file_exists($imagepath.$board_row->image) ){
						echo "<br><img src='".$imagepath.$board_row->image."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."product/{$board_row->image}' style=\"width:100px\">";
						echo "&nbsp;<a href=\"JavaScript:DeletePrdtImg('2')\"><img src=\"images/icon_del1.gif\" align=bottom border=0></a>";
					} else {
						echo "<br><img src=images/space01.gif>";
					}
				}
	?>
					</td>
				</tr>			
				<TR id='ID_RepProductChange'>
					<th><span>대표상품</span></th>
					<td>
						<p align="left">
							<div style="margin-top:10px; margin-bottom: 10px;">
								<ul id="checkRepProduct" style="">
								<?if($board_row->productcode){?>
									<li class='prListOn' id='RepProduct'>
										<img src='<?=$proimagepath.$board_row->tinyimage?>' style='width:100px' ><br>
										<a href='javascript:rapPrDel("<?=$board_row->productcode?>");'><?=$board_row->productname?></a>
									</li>
								<?}?>
								</ul>
							</div>
							<INPUT type='hidden' name='productcode' id="productcode" value="<?=$board_row->productcode?>">
							<a href="javascript:layer_open('layer2','repProduct');"><img src="./images/btn_search2.gif"/></a>
						</p>
					</td>
				</TR>	
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td colspan=8 align=center><a href="javascript:cwl_indb('<?=$no?>');">
			<?if($mode=="cwl_ins"){?>
				<img src="images/btn_confirm_com.gif">
			<?}else{?>
				<img src="images/btn_modify_com.gif">
			
			<?}?>
					
				</a></td>
			</tr>
			<tr><td height=20></td></tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=50></col>
				<col width=150></col>
				<col width=></col>
				<col width=200></col>
				<col width=300></col>
				<col width=50></col>
				<col width=100></col>
				<col width=60></col>
				<col width=60></col>
				<TR align=center>
					<th>번호</th>
					<th>카테고리명</th>
					<th>제목</th>
					<th>이미지</th>
					<th>대표상품</th>
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
					echo "<tr>";
					echo "<input type='hidden' name='num[]' value='{$data->num}'>";
					echo "<td>{$number}</td>";
					echo "<td>{$data->category_name}</td>";
					echo "<td style='text-align:left;'>{$data->title}</td>";
					if ( ord($data->image) && file_exists($imagepath.$data->image) ){
						echo "<td><img src='".$imagepath.$data->image."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/cwl/board/{$data->icoimage}' style=\"height:80px\"></td>";
					} else {
						echo "<td>&nbsp;</td>";
					}
					if ( ord($data->tinyimage) && file_exists($proimagepath.$data->tinyimage) ){
						echo "<td><img src='".$proimagepath.$data->tinyimage."' border=1 alt='URL : http://".$_ShopInfo->getShopurl().DataDir."shopimages/product/{$data->tinyimage}' style=\"height:60px\"><br>".$data->productname."</td>";
					} else {
						echo "<td>&nbsp;</td>";
					}
					echo "<td><input type=checkbox name='secret[]' value='{$data->num}' {$checked[$data->num]}></td>";
					echo "<td>{$regdt}</td>";
					echo "<td><a href=\"javascript:cwl_mod('{$data->num}')\"><img src=\"images/btn_edit.gif\"></a></td>";
					echo "<td><a href=\"javascript:cwl_del('{$data->num}')\"><img src=\"images/btn_del.gif\"></a></td>";
					echo "</tr>";
					
					$cnt++;
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<TR><TD colspan=\"9\" align=center>등록된 리스트가 존재하지 않습니다.</TD></TR>";
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
				<td colspan=9 align=right><a href="javascript:secret_change();"><img src="images/botteon_save.gif"></a></td>
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
			<input type=hidden name=title>

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
<script>


function layer_open(el,onMode){

	var temp = $('#' + el);
	var bg = temp.prev().hasClass('bg');	//dimmed 레이어를 감지하기 위한 boolean 변수
	switch(onMode){
		case 'repProduct' :
			$('#listMode').val('repProduct');
			break;
		case 'relationProduct' :
			$('#listMode').val('relationProduct');
			break;
		default :
			$('#listMode').val('');
			break;
	}
	
	if(bg){
		$('.layer').fadeIn();	//'bg' 클래스가 존재하면 레이어가 나타나고 배경은 dimmed 된다. 
	}else{
		temp.fadeIn();
	}

	layerResize(el);

	temp.find('a.cbtn').click(function(e){
		if(bg){
			$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
			outLayer();
		}else{
			temp.fadeOut();
			outLayer();
		}
		e.preventDefault();
	});

	$('.layer .bg').click(function(e){	//배경을 클릭하면 레이어를 사라지게 하는 이벤트 핸들러
		$('.layer').fadeOut();
		outLayer();
		e.preventDefault();
	});

}

function layerResize(el){
	var temp = $('#' + el);
	// 화면의 중앙에 레이어를 띄운다.
	if (temp.outerHeight() < $(document).height() ) temp.css('margin-top', '-'+temp.outerHeight()/2+'px');
	else temp.css('top', '0px');
	if (temp.outerWidth() < $(document).width() ) temp.css('margin-left', '-'+temp.outerWidth()/2+'px');
	else temp.css('left', '0px');
	
	//console.log(temp.outerHeight());
}

function outLayer(){
	$("#s_keyword").val("");
	$("#productList").html("");
	$('#listMode').val("");
	//$("#checkProduct").html("");
}

function GoPage(block,gotopage){
	var code_a = $("#code_a").val();
	var code_b = $("#code_b").val();
	var code_c = $("#code_c").val();
	var code_d = $("#code_d").val();
	var s_keyword = $("#s_keyword").val();
	var listMode = $("#listMode").val();
	$.post(
		"community_brandboard_prlistPost.php",
		{
			code_a:code_a,
			code_b:code_b,
			code_c:code_c,
			code_d:code_d,
			listMode:listMode,
			s_keyword:s_keyword,
			block:block,
			gotopage:gotopage
		},
		function(data){
			$("#productList").html(data);
			layerResize('layer2');
		}
	);
}

function productListSearch(){
	var code_a = $("#code_a").val();
	var code_b = $("#code_b").val();
	var code_c = $("#code_c").val();
	var code_d = $("#code_d").val();
	var s_keyword = $("#s_keyword").val();
	var listMode = $("#listMode").val();
	$.post(
		"community_brandboard_prlistPost.php",
		{
			code_a:code_a,
			code_b:code_b,
			code_c:code_c,
			code_d:code_d,
			s_keyword:s_keyword,
			listMode:listMode
		},
		function(data){
			$("#productList").html(data);
			layerResize('layer2');
		}
	);
}

function onProductcode(prname,prcode,primg){
	var appHtml = "";
	if(confirm('해당 상품을 대표상품으로 입력하시겠습니까?')){
		$("#productcode").val(prcode);
		appHtml = "<li class='prListOn' id='RepProduct'>";
		appHtml+= "<img src='"+primg+"' style='width:100px' ><br>";
		appHtml+= "<a href='javascript:rapPrDel(\""+prcode+"\");'>"+prname+"</a>";
		appHtml+= "</li> ";
		$("#checkRepProduct").html(appHtml);
		$('.layer').fadeOut(); //'bg' 클래스가 존재하면 레이어를 사라지게 한다. 
		outLayer();
	}
}

function rapPrDel(prcode){
	if(confirm('대표상품을 삭제 하시겠습니까?')){
		$("#RepProduct").remove();
		$("#productcode").val("");
	}
}


</script>
<?=$onload?>
<?php 
include("copyright.php");
