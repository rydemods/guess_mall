<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include_once($Dir."lib/file.class.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$imagepath=$Dir.DataDir."shopimages/homebanner/";

$mode=$_POST["mode"];
if(!$_POST["code"]) $_POST["code"] = $_GET["code"];  
$code=$_POST["code"]?$_POST["code"]:"home_main";  
$banner_img=$_POST["banner_img"];
$banner_sort=$_POST["banner_sort"];
$banner_link=$_POST["banner_link"];
$banner_link_m=$_POST["banner_link_m"];
$banner_title=$_POST["banner_title"];
$banner_hidden=$_POST["banner_hidden"];
$banner_t_link=$_POST["banner_t_link"];

$title_name["home_roll_top"]="홈페이지 메인 상단 배너 롤링";
$title_name["home_roll_bottom"]="홈페이지 메인 하단 배너 롤링 ";
$title_name["home_about"]="홈페이지 ABOUT";
$title_name["home_history"]="홈페이지 HISTORY";
$title_name["home_business"]="홈페이지 BUSINESS";
$title_name["home_brand"]="홈페이지 BRAND";
$title_name["home_recruit"]="홈페이지 RECRUIT";

// homepage 에서 사용하는 타이틀
$title_name["home_header"]="홈페이지 HEADER";
$title_name["home_sub_header"]="홈페이지 서브 HEADER";
$title_name["home_footer"]="홈페이지 FOOTER";
$title_name["home_main"]="홈페이지 MAIN";
$title_name["home_toolfarm"]="홈페이지 TOOLFARM";
$title_name["home_toolfarm_dts"]="홈페이지 TOOLFARM";
$title_name["home_toolfarm_vfarm"]="홈페이지 TOOLFARM V-Farm";
$title_name["home_toolfarm_plugin"]="홈페이지 TOOLFARM PLUG-IN";
$title_name["home_company"]="홈페이지 COMPANY";
$title_name["home_company_eng"]="홈페이지 COMPANY (영어)";
$title_name["home_company_organization"]="홈페이지 COMPANY 조직도";
$title_name["home_company_organization_eng"]="홈페이지 COMPANY 조직도 (영어)";
$title_name["home_company_history"]="홈페이지 COMPANY 회사연혁";
$title_name["home_company_history_top"]="홈페이지 COMPANY 회사연혁 상단";
$title_name["home_company_history_eng"]="홈페이지 COMPANY 회사연혁 (영어)";
//$title_name["home_company_history_top_eng"]="홈페이지 COMPANY 회사연혁 상단 (영어)";
$title_name["home_company_map"]		="홈페이지 COMPANY 오시는 길";
$title_name["home_company_map_bottom"]		="홈페이지 COMPANY 오시는 길 하단";
$title_name["home_company_map_eng"]	="홈페이지 COMPANY 오시는길 (영어)";
$title_name["home_company_map_bottom_eng"]	="홈페이지 COMPANY 오시는길 하단 (영어)";

$title_name["home_reference"]="홈페이지 REFERENCE";
$title_name["home_reference_top"]="홈페이지 REFERENCE 상단";

$title_name["home_customer_support"]="홈페이지 CUSTOMER SUPPORT";

//$addImage = array('home_roll_top', 'home_roll_bottom');
$addImage = array('home_header','home_sub_header','home_footer','home_main','home_toolfarm', 'home_toolfarm_dts', 'home_toolfarm_vfarm','home_toolfarm_plugin'
	,'home_company','home_company_eng'
	,'home_company_organization','home_company_organization_eng'
	,'home_company_history','home_company_history_top','home_company_history_eng','home_company_history_top_eng'
	,'home_company_map','home_company_map_bottom','home_company_map_eng','home_company_map_bottom_eng'
,'home_reference'
,'home_customer_support');
//$addImageAndTitle = array('home_history', 'home_business', 'home_brand');

?>

<?php include("header.php"); ?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript">
	<!--
	function Save() {
		if(confirm("수정하시겠습니까?")) {
			document.form1.mode.value="modify";
			document.form1.submit();
		}
	}

	function TitleDelete() {
		if(confirm("삭제하시겠습니까?")) {
			document.form1.mode.value="delete";
			document.form1.submit();
		}
	}

	function change_cate(){
		document.form1.mode.value="";
		document.form1.submit();
	}

	function change_color(flag){
		if(flag == "l"){
			document.getElementById("l_color").value = document.getElementById("left_bgcolor").value;
		}
		else if(flag == "r"){
			document.getElementById("r_color").value = document.getElementById("right_bgcolor").value;
		}
		else{
			alert("error");
		}
	}
	$(document).ready(function(){
		$(document).on("click", ".CLS_btnAddRows", function(){
			var banner_type = $("input[name='banner_type']").val();
			var deleteHeight = 0;
			if(banner_type == 'image'){
				deleteHeight = 104;
			}else if(banner_type == 'title'){
				deleteHeight = 134;
			}
			$.get( "./home_banner.add.template.php?banner_type="+banner_type, function( html ) {
				$( ".CLS_tableBody" ).append( html );
				var dynamicHeight = parseInt($("#ListFrame", parent.document).css('height').replace('px', ''), 10);
				$("#ListFrame", parent.document).css('height', dynamicHeight + deleteHeight);
			});
		})




		$(document).on("click", ".CLS_btnDelRows", function(){
			var banner_type = $("input[name='banner_type']").val();
			var deleteHeight = 0;

			$(this).parent().parent().prev().prev().remove();
			$(this).parent().parent().prev().remove();
			$(this).parent().parent().next().next().remove();
			$(this).parent().parent().next().remove();
			$(this).parent().parent().remove();

			if(banner_type == 'image'){
				deleteHeight = 104;
			}else if(banner_type == 'title'){
				deleteHeight = 134;
			}


			var dynamicHeight = parseInt($("#ListFrame", parent.document).css('height').replace('px', ''), 10);
			$("#ListFrame", parent.document).css('height', dynamicHeight - deleteHeight);

			var delNumber = $(this).next().val();
			if(delNumber){
				$("input[name='deleteRows']").val($("input[name='deleteRows']").val()+delNumber+"|");
			}
		})








		$(document).on("mouseover", ".CLS_viewLargeImg", function(e){
			$('.CLS_viewLargeImgLayer', parent.document).show();
			$('.CLS_viewLargeImgLayer', parent.document).html("<img src = '"+$(this).attr('src')+"' width = '100%'>");
			
			var windowWidth = $( window ).width();
			var mouseX = e.pageX - 60;
			var mouseY = e.pageY - ($('.CLS_viewLargeImgLayer').height() - 50);
			if(mouseX > 1000){
				mouseX = $(this).offset().left - $(this).width() - 650;
			}

			$('.CLS_viewLargeImgLayer', parent.document).css('left', mouseX).css('top', mouseY);
		}).on("mouseout", ".CLS_viewLargeImg", function(e){
			$('.CLS_viewLargeImgLayer', parent.document).hide();
			$('.CLS_viewLargeImgLayer', parent.document).html("");
		});



		function init(){
			var doc= document.getElementById("divName");
			if(doc.offsetHeight!=0){
				pageheight = doc.offsetHeight;
				$("#ListFrame", parent.document).css('height', pageheight);
			}
		}
		init();
	})
//-->
</SCRIPT>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<style>
	.CLS_blank{
		height:5px;
	}
</style>
<div id="divName">
<form name='form1' action="./home_banner.indb.php" method='post' enctype="multipart/form-data" target = '_self'>
<input type='hidden' name='imgtype' value="<?=$imgtype?>">
<input type='hidden' name='mode'>
<input type='hidden' name='deleteRows'>
<input type='hidden' name='code' value="<?=$code?>">
<table cellpadding="0" cellspacing="0" width="100%">
<?if(in_array($code, $addImage)){?>
	<input type='hidden' name='banner_type' value="image">
<?}else if(in_array($code, $addImageAndTitle)){?>
	<input type='hidden' name='banner_type' value="title">
<?}else{?>
	<input type='hidden' name='banner_type' value="one">
<?}?>
<tr>
	<td width="100%">
	<table cellpadding="0" cellspacing="0" width="100%" height="100%">
	<tr>
		<td width="100%" bgcolor="white">
			<div class="title_depth3_sub">
				<?=$title_name[$code]?>
				<?if(in_array($code, $addImage) || in_array($code, $addImageAndTitle)){?>				
					<a href = 'javascript:;' class = 'CLS_btnAddRows'><img src = '../img/button/customer_notice_write_add_btn.gif' align = 'absmiddle' width = '60'></a>
				<?}?>
			</div>
			<IMG SRC="images/line_blue.gif" WIDTH=100% HEIGHT=2 ALT="">
		</td>
	</tr>
	<tr>
		<td width="100%" height="100%" valign="top" style="border-bottom-width:2px; border-bottom-color:rgb(0,153,204); border-bottom-style:solid;">
		<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="100%">
				<div class="table_style01">
					<table cellSpacing=0 cellPadding=0 width="100%" border=0>
						<col width="200" /><col width="" />
						<tr>
							<td colspan=2 style="border-left:1px solid #b9b9b9;">

								<table border=0 cellpadding=0 cellspacing=0 width=100% style="table-layout:fixed">
									<col width="150" /><col width="" /><col width="150" />
									<?if(in_array($code, $addImage)){?>

										<?
											$img_result = pmysql_query("select * from tblhomebannerimg where banner_name='".$code."' order by banner_sort");
											while($img_data=pmysql_fetch_object($img_result)){
												$selected = array();
												$selected['banner_hidden'][$img_data->banner_hidden] = "selected";
										?>
												<tr>
													<th><span>이미지</span></th>
													<td class='td_con1'>
														<INPUT type='file' size='38' name='banner_img[]' value  style='width:80%'>
														<?if(is_file($imagepath.$img_data->banner_img)){?>
															<img src="<?=$imagepath.$img_data->banner_img?>" width = '50' height = '25' border='0' align='absmiddle' class = 'CLS_viewLargeImg'>
														<?}else{?>
														<?}?>
													</td>
													<th><span>순서</span></th>
													<td class='td_con1'>
														<INPUT type='text' size='5' name='banner_sort[]' value = '<?=$img_data->banner_sort?>'>
														<INPUT type='hidden' name='banner_mode[]' value = 'upd'>
													</td>
												</tr>
												<tr>
													<th><span>링크</span></th>
													<td class='td_con1' colspan='3'>
														<INPUT type='text' size='5' name='banner_link[]' value = '<?=$img_data->banner_link?>' style='width:90%'>
														<a href = 'javascript:;' class = 'CLS_btnDelRows'><img src = '../img/button/btn_bbs_del.gif' align = 'absmiddle' width = '60'></a>
														<INPUT type='hidden' name='banner_no[]' value = '<?=$img_data->no?>'>
													</td>
												</tr>
												<tr>
													<th><span>노출</span></th>
													<td class="td_con1">
														<select name='banner_hidden[]'>
															<option value = '1' <?=$selected['banner_hidden']['1']?>>노출</option>
															<option value = '0' <?=$selected['banner_hidden']['0']?>>미노출</option>
														</select>
													</td>
<!--													<?if($img_data->banner_name == "home_main"){?>
														<th><span>높이</span></th>
														<td class='td_con1'>
															<INPUT type='text' size='4' name='banner_height[]' value = '<?=$img_data->height?>'>
															px
														</td>
													<?}?>-->
												</tr>
												<tr>
													<td colspan = '4' class = 'CLS_blank'></td>
												</tr>
										<?
											}
											
										?>



										<tbody class = 'CLS_tableBody'>
										</tbody>



									<?}else if(in_array($code, $addImageAndTitle)){?>


										<?
											$img_result = pmysql_query("select * from tblhomebannerimg where banner_name='".$code."' order by banner_sort");
											while($img_data=pmysql_fetch_object($img_result)){
												$selected = array();
												$selected['banner_hidden'][$img_data->banner_hidden] = "selected";
										?>
												<tr>
													<th><span>타이틀 기본 이미지</span></th>
													<td class='td_con1'>
														<INPUT type='file' size='38' name='banner_img_title_on[]' style='width:80%'>
														<?if(is_file($imagepath.$img_data->banner_img_title_on)){?>
															<img src="<?=$imagepath.$img_data->banner_img_title_on?>" width = '50' height = '25' border='0' align='absmiddle' class = 'CLS_viewLargeImg'>
														<?}else{?>
														<?}?>
													</td>
													<th><span>타이틀 선택 이미지</span></th>
													<td class='td_con1'>
														<INPUT type='file' size='38' name='banner_img_title_out[]' style='width:80%'>
														<?if(is_file($imagepath.$img_data->banner_img_title_out)){?>
															<img src="<?=$imagepath.$img_data->banner_img_title_out?>" width = '50' height = '25' border='0' align='absmiddle' class = 'CLS_viewLargeImg'>
														<?}else{?>
														<?}?>
													</td>
												</tr>
												<tr>
													<th><span>이미지</span></th>
													<td class='td_con1'>
														<INPUT type='file' size='38' name='banner_img[]' value  style='width:80%'>
														<?if(is_file($imagepath.$img_data->banner_img)){?>
															<img src="<?=$imagepath.$img_data->banner_img?>" width = '50' border='0' height = '25' align='absmiddle' class = 'CLS_viewLargeImg'>
														<?}else{?>
														<?}?>
													</td>
													<th><span>순서</span></th>
													<td class='td_con1'>
														<INPUT type='text' size='5' name='banner_sort[]' value = '<?=$img_data->banner_sort?>'>
														<INPUT type='hidden' name='banner_mode[]' value = 'upd'>
													</td>
												</tr>
												<tr>
													<th><span>링크</span></th>
													<td class='td_con1' colspan='3'>
														<INPUT type='text' size='5' name='banner_link[]' value = '<?=$img_data->banner_link?>' style='width:90%'>
														<a href = 'javascript:;' class = 'CLS_btnDelRows'><img src = '../img/button/btn_bbs_del.gif' align = 'absmiddle' width = '60'></a>
														<INPUT type='hidden' name='banner_no[]' value = '<?=$img_data->no?>'>
													</td>
												</tr>
												<tr>
													<th><span>노출</span></th>
													<td class="td_con1">
														<select name='banner_hidden[]'>
															<option value = '1' <?=$selected['banner_hidden']['1']?>>노출</option>
															<option value = '0' <?=$selected['banner_hidden']['0']?>>미노출</option>
														</select>
													</td>
												</tr>
												<tr>
													<td colspan = '4' class = 'CLS_blank'></td>
												</tr>
										<?
											}
											
										?>
										<tbody class = 'CLS_tableBody'>
										</tbody>













									<?}else{?>
										<?
											$img_data = pmysql_fetch_object(pmysql_query("select * from tblhomebannerimg where banner_name='".$code."' order by banner_sort"));

											$selected = array();
											$selected['banner_hidden'][$img_data->banner_hidden] = "selected";
											if($img_data->no){
												$banner_mode = "upd";
											}else{
												$banner_mode = "ins";
											}
										?>

										<tr>
											<th><span>이미지</span></th>
											<td class='td_con1'>
												<INPUT type='file' size='38' name='banner_img[]' value  style='width:80%'>
												<?if(is_file($imagepath.$img_data->banner_img)){?>
													<img src="<?=$imagepath.$img_data->banner_img?>" width = '50' border='0' align='absmiddle' class = 'CLS_viewLargeImg'>
												<?}else{?>
												<?}?>
											</td>
											<th><span>순서</span></th>
											<td class='td_con1'>
												<INPUT type='text' size='5' name='banner_sort[]' value = '<?=$img_data->banner_sort?>'>
												<INPUT type='hidden' name='banner_mode[]' value = '<?=$banner_mode?>'>
											</td>
										</tr>
										<tr>
											<th><span>링크</span></th>
											<td class='td_con1' colspan='3'>
												<INPUT type='text' size='5' name='banner_link[]' value = '<?=$img_data->banner_link?>' style='width:98%'>
												<INPUT type='hidden' name='banner_no[]' value = '<?=$img_data->no?>'>
											</td>
										</tr>
										<tr>
											<th><span>노출</span></th>
											<td class="td_con1">
												<select name='banner_hidden[]'>
													<option value = '1' <?=$selected['banner_hidden']['1']?>>노출</option>
													<option value = '0' <?=$selected['banner_hidden']['0']?>>미노출</option>
												</select>
											</td>
										</tr>
										<tr>
											<td colspan = '4' class = 'CLS_blank'></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
						<tr>
							<td align=center style="padding-top:2pt; padding-bottom:2pt;" height="22">
								<a href="javascript:Save();"><img src="images/btn_edit2.gif" border="0" hspace="0" vspace="4"></a>
								<a href="javascript:TitleDelete();"><img src="images/btn_del3.gif" border="0" hspace="2" vspace="4"></a>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<!--<tr>
			<td align=center style="padding-top:2pt; padding-bottom:2pt;" height="22">
				<a href="javascript:Save();"><img src="images/btn_edit2.gif" border="0" hspace="0" vspace="4"></a>
				<a href="javascript:TitleDelete();"><img src="images/btn_del3.gif" border="0" hspace="2" vspace="4"></a>
			</td>
		</tr> -->
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
</form>
</div>

