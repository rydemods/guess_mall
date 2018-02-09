<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가

include("calendar.php");
include("header.php");

####################### 페이지 접근권한 check ###############
$PageCode = "ma-2";
$MenuCode = "market";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################
$block=$_REQUEST['blockval'];
$gotopage=$_REQUEST['gotoval'];
$sno=$_REQUEST['sno'];
$board='event_admin_ns';
$mode=($_REQUEST['mode'])?$_REQUEST['mode']:'ins';

$imagepath      = $cfg_img_path['hotdeal'];

$qry="select *,to_char(edate,'YYYY-MM-DD') as edt, to_char(sdate,'YYYY-MM-DD') as sdt from tblhotdeal where sno='".$sno."'";
$res=pmysql_query($qry);
$hotdeal_row=pmysql_fetch_array($res);
pmysql_free_result($res);

if($sno){

	$checked['view_type'][$hotdeal_row['view_type']]='checked';
	//$checked['rolling_type'][$hotdeal_row['rolling_type']]='checked';
}else{
	$checked['view_type'][0]='checked';
	//$checked['rolling_type'][0]='checked';
}

//상품 정보 쿼리
$pdt_qry="select * from tblproduct where productcode='".$hotdeal_row['productcode']."'";
$pdt_res=pmysql_query($pdt_qry);
$pdt_row=pmysql_fetch_array($pdt_res);
pmysql_free_result($pdt_res);


?>

<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="../SE2/js/HuskyEZCreator.js" charset="utf-8"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>


<script language="JavaScript">
	
function chkform(mode){
	var pdt_code=$("input[name='pdt_code[]']");
	
	//var sHTML = oEditors.getById["ir1"].getIR();
	//document.eventform.content.value=sHTML;

	if(!pdt_code.val()){
		alert('상품을 선택해주세요.');
		return false;
	}else if(!$("#title").val()){
		alert('이벤트명을 입력해 주세요.');
		return false;
	}else if(!$("#sdate").val()){
		alert('시작일을 입력해 주세요.');
		return false;
	}/*else if(!$("#edate").val()){
		alert('종료일을 입력해 주세요.');
		return false;
	}*/

}
$(document).ready(function(){
	$(".img_view_sizeset").on('mouseover',function(){

		$("#img_view_div").offset({top:($(document).scrollTop()+200)});
		$("#img_view_div").find('img').attr('src',($(this).attr('src')));
		$("#img_view_div").find('img').css('display','block');
		


	});

	$(".img_view_sizeset").on('mouseout',function(){
		$("#img_view_div").find('img').css('display','none'); 
	});	
});
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 이벤트/사은품 기능 설정 &gt;<span>핫딜 관리</span></p></div></div>
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

<div class="title_depth3">핫딜 등록/수정</div>

<form name="eventform" method="post" action="hotdeal_ins.php" enctype="multipart/form-data" onsubmit="return chkform('<?=$mode?>');">
<?include("layer_prlistPop.php");?>
	<input type="hidden" name="mode" value="<?=$mode?>">
	<input type="hidden" name="sno" value="<?=$sno?>">
	<input type="hidden" name="layermode" id="layermode" value="hotdeal">

				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20" style="position:relative">
					<div id="img_view_div" style="position:absolute;top:150px;left:400px;"><img style="display:none;width:500px" ></div>
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>상품선택</span>&nbsp;&nbsp;<a href="javascript:T_layer_open('layer_product_sel','pdt_code');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a></th>
							<td align="left">
								<div style="margin-top:0px; margin-bottom: 0px;">							
									<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_pdt_code">	
									<input type="hidden" name="limit_pdt_code" id="limit_pdt_code" value="1"/>								
										<colgroup>
											<col width=20></col>
											<col width=50></col>
											<col width=></col>
										</colgroup>
									<?if($pdt_row[productcode]){?>	
										<tr align="center">
											<td style='border:0px'>
												<a name="pro_upChange" style="cursor: hand;">
													<img src="images/btn_plus.gif" border="0" style="margin-bottom: 3px;" />
												</a>
												<br>
												<a name="pro_downChange" style="cursor: hand;">
													<img src="images/btn_minus.gif" border="0" style="margin-top: 3px;" />
												</a>
											</td>
											<td style='border:0px'>
												<!-- <img style="width: 40px; height:40px;" src="<?=$Dir.DataDir."shopimages/product/".$bannerProduct['tinyimage']?>" border="1"/> -->
												<img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $pdt_row['tinyimage'] );?>" border="1"/>
												<input type='hidden' name='pdt_code[]' value='<?=$pdt_row[productcode]?>'>
											</td>
											<td style='border:0px' align="left"><?=$pdt_row[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$pdt_row[productcode]?>','pdt_code');" border="0" style="cursor: hand;vertical-align:middle;" />
											</td>
										</tr>
									<?}?>
									</table>
								</div>
							</td>
						</tr>
						
						<tr>
							<th><span>이벤트명</span></th>
							<td><input type="text" name="title" id="title" style="width:50%" value="<?=$hotdeal_row['title']?>" alt="이벤트명" /></td>
						</tr>
						
						<tr>
							<th><span>시작일</span></th>
							<td><input type="text" name="sdate" id="sdate" OnClick="Calendar(event)"  value="<?=$hotdeal_row['sdate']?>" alt="시작일" /><span style="color:red;">달력 선택 후 시간,분,초를 정확한 형식으로 입력해주세요. ex) <?=date('Y-m-d H:i:s')?></span></td>
						</tr>
						<!--
						<tr>
							<th><span>종료일</span></th>
							<td><input type="text" name="edate" id="edate" OnClick="Calendar(event)"  value="<?=$hotdeal_row['edate']?>" alt="종료일" /><span style="color:red;">달력 선택 후 시간,분,초를 정확한 형식으로 입력해주세요. ex) <?=date('Y-m-d H:i:s')?></span></td>
						</tr>-->
						<tr>
							<th><span>이미지</span></th>
							<td>
								<input type="file" name="view_img[]" id='view_img' alt="썸네일 이미지" />
								<?
									if($hotdeal_row['view_img']){
								?>
									<span><br><img src="<?=$imagepath?><?=$hotdeal_row['view_img']?>" style="height:30px;" class="img_view_sizeset"></span>
								<?
									}
								?>
							</td>
						</tr>
						<tr>
							<th><span>이미지(MOBILE)</span></th>
							<td>
								<input type="file" name="view_img_m[]" id='view_img_m' alt="썸네일 이미지" />
								<?
									if($hotdeal_row['view_img_m']){
								?>
									<span><br><img src="<?=$imagepath?><?=$hotdeal_row['view_img_m']?>" style="height:30px;" class="img_view_sizeset"></span>
								<?
									}
								?>
							</td>
						</tr>
						<tr>
							<th><span>하단이미지</span></th>
							<td>
								<input type="file" name="bottom_img[]" id='bottom_img' alt="썸네일 이미지" />
								<?
									if($hotdeal_row['bottom_img']){
								?>
									<span><br><img src="<?=$imagepath?><?=$hotdeal_row['bottom_img']?>" style="height:30px;" class="img_view_sizeset"></span>
								<?
									}
								?>
							</td>
						</tr>
						<tr>
							<th><span>하단이미지(MOBILE)</span></th>
							<td>
								<input type="file" name="bottom_img_m[]" id='bottom_img_m' alt="썸네일 이미지" />
								<?
									if($hotdeal_row['bottom_img_m']){
								?>
									<span><br><img src="<?=$imagepath?><?=$hotdeal_row['bottom_img_m']?>" style="height:30px;" class="img_view_sizeset"></span>
								<?
									}
								?>
							</td>
						</tr>
						<!--
						<tr>
							<th><span>내용</span></th>
							<td><TEXTAREA style="DISPLAY: yes; WIDTH: 100%" name=content rows="17" id="ir1" wrap=off><?=$hotdeal_row['content']?></textarea></td>
						</tr>
						-->
						<tr>
							<th><span>핫딜노출</span></th>
							<td>
								<input type="radio" name="view_type" value="0" alt="핫딜노출" <?=$checked['view_type'][0]?>/> 비노출
								<input type="radio" name="view_type" value="1" alt="핫딜노출" <?=$checked['view_type'][1]?>/> 노출
								&nbsp; <span style="color:red;">*노출 변경시 기존에 노출되있던 핫딜은 비노출로 변경됩니다.</span>
							</td>
						</tr>
						
					</table>
				</div>
				<div style="width:100%;text-align:center">
					<input type="image" src="../admin/images/btn_confirm_com.gif">
					<img src="../admin/images/btn_list_com.gif" onclick="document.location.href='hotdeal_list.php?gotopage=<?=$gotopage?>&block=<?=$block?>'">
				</div>


</form>

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

</script>
<script type="text/javascript">
var oEditors = [];

nhn.husky.EZCreator.createInIFrame({
	oAppRef: oEditors,
	elPlaceHolder: "ir1",
	sSkinURI: "../SE2/SmartEditor2Skin.html",	
	htParams : {
		bUseToolbar : true,				// 툴바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseVerticalResizer : true,		// 입력창 크기 조절바 사용 여부 (true:사용/ false:사용하지 않음)
		bUseModeChanger : true,			// 모드 탭(Editor | HTML | TEXT) 사용 여부 (true:사용/ false:사용하지 않음)
		//aAdditionalFontList : aAdditionalFontSet,		// 추가 글꼴 목록
		fOnBeforeUnload : function(){
		}
	}, 
	fOnAppLoad : function(){
	},
	fCreator: "createSEditor2"
});

</script>
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<?=$onload?>
<?php
include("copyright.php");
