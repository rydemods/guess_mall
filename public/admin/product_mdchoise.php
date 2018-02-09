<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");

##################### 페이지 접근권한 check #####################
$PageCode = "co-6";
$MenuCode = "community";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#################################################################

include("header.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
//exdebug($_REQUEST);
//exdebug($_REQUEST);
//exdebug($_FILES);
//exit;

$mode = $_REQUEST["mode"];
if(!$mode) $mode = $_REQUEST["mode"];

// 이미지 경로
$imagepath = $Dir.DataDir."shopimages/lookbook/";
// 이미지 파일
$imagefile = new FILE($imagepath);

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------
$no = $_REQUEST["no"];
 
if($mode=="insert") {
    
	
    if (!$display) $display = "N";
    else $display = "Y";

    $v_up_imagefile	    = $_REQUEST["v_up_imagefile"];
    $v_up_image	    = $_REQUEST["v_up_image"];

    $up_imagefile = $imagefile->upFiles();
//     exdebug($up_imagefile);
//     exit;

    $regdt = date("YmdHis");

	
	$relationProductArr	= $_REQUEST["relationProduct"];
	$relationProduct	= implode(",",$relationProductArr);

    if($mode=="insert") {
        $sql = "UPDATE tblproductbrand SET mdchoise = '{$relationProduct}' WHERE brandcd = '{$_REQUEST[brandcd]}' ";
        pmysql_query($sql,get_db_conn());
		
//         exdebug($sql);


    }
    if(!pmysql_error()){
	    if($mode=="insert") {
	    	
	        echo "<html></head><body onload=\"alert('등록이 완료되었습니다.');parent.goBackList();\"></body></html>";exit;
	    }
    }else{
    	exdebug($sql);
    	alert_go('오류가 발생하였습니다.', $_SERVER['REQUEST_URI']);
    }
}


#---------------------------------------------------------------
# 브랜드시즌
#---------------------------------------------------------------

$prCateSql = "select brandcd, brandname from tblproductbrand order by brandname  ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );
$ii=0;
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prBrand[$ii] = $prRow;
	$ii++;
}

$prCateSql = "select season,season_eng_name from tblproductseason order by season_eng_name desc  ";
$prCateRes = pmysql_query( $prCateSql, get_db_conn() );
$ii=0;
while( $prRow = pmysql_fetch_array( $prCateRes ) ){
	$prSeason[$ii] = $prRow;
	$ii++;
}


pmysql_free_result( $prCateRes );




$no = $_REQUEST['brandcd'];
$mSelectSql = "SELECT * FROM tblproductbrand WHERE brandcd ='".$no."'";
$mSelectRes = pmysql_query( $mSelectSql, get_db_conn() );
$mSelectRow = pmysql_fetch_array( $mSelectRes );
$mSelect = $mSelectRow;
pmysql_free_result( $mSelectRes );


?>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown2.admin.js.php"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm(mode, no) {

	$(".procode").val("");
	$(".prList").each(function(){
		var num	= $(this).attr("alt");
		$(this).find(".relationProduct"+num).each(function(){
			$(".productcodes" + num).val($(this).val()); 
		});
	});


	document.form1.mode.value="insert";
	document.form1.target="processFrame";
	document.form1.submit();
}

function goPage(brandcd){
	location.href='?brandcd='+brandcd;
}



</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 커뮤니티  &gt; MD's CHOICE 관리 &gt; <span>LOOKBOOK <?=$qType_text?></span></p></div></div>
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
			<?php include("menu_product.php"); ?>
			</td>
			<td></td>
			<td valign="top">	
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
			<input type=hidden name=tb value="MD's CHOICE">
			<input type=hidden name=mode>
            <input type=hidden name=no value="<?=$no?>">
			<table cellpadding="0" cellspacing="0" width="100%">
			<?include("layer_prlistPop.php");?>
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">MD's CHOICE <?=$qType_text?></div>

					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>MD's CHOICE <?=$qType_text?>할 수 있습니다.</span></div>
				</td>
            </tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">MD's CHOICE 기본정보</div>
				</td>
			</tr>
			
			<tr><td height=3></td></tr>
			<tr>
				<td>
				<?#include("layer_prlistPop.php");?>
				<div class="table_style01">					
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>브랜드</span></th>
					<TD>
						브랜드:
						<select name="brandcd" onchange="goPage(this.value)">
							<option value="">==선택==</option>
							<?for($ii=0; $ii<count($prBrand); $ii++){ ?>
							<option value="<?=$prBrand[$ii][0]?>" <?if($mSelect['brandcd']==$prBrand[$ii][0]){echo "selected";}?>><?=$prBrand[$ii][1]?></option>
							<?}?>
						</select>
					</TD>
				</tr>
				
				<tr>
					<th>
					<span>관련상품</span>&nbsp;&nbsp;
					<a href="javascript:T_layer_open('layer_product_sel','relationProduct');"><img src="./images/btn_search2.gif" style='vertical-align:middle;padding-top:3px;padding-bottom:7px;'/></a>
					</th>
					<td align="left">
							<div style="margin-top:0px; margin-bottom: 0px;">							
								<table border=0 cellpadding=0 cellspacing=0 style='border:0px' name="prList" id="check_relationProduct">	
								<input type="hidden" name="limit_relationProduct" id="limit_relationProduct" value=""/>								
									<colgroup>
										<col width=20></col>
										<col width=50></col>
										<col width=></col>
									</colgroup>

								<?

								if($mSelect['mdchoise']!=""){
								$relationProductArr = explode(",",$mSelect['mdchoise']);
								for($ii=0; $ii < count($relationProductArr); $ii++){
									$relationProductArr_serialze .= "'".$relationProductArr[$ii]."',";

									$insetSeq_sql = "select * from tblproduct where productcode = '".$relationProductArr[$ii]."'";
									$insertSeq_result = pmysql_query($insetSeq_sql);
									$insertSeq_row = pmysql_fetch_object( $insertSeq_result );
									$tinyimage = $insertSeq_row->tinyimage;
									$productname = $insertSeq_row->productname;
									$productcode = $insertSeq_row->productcode;
								?>
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
											
											<img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $tinyimage );?>" border="1"/>
											<input type='hidden' name='relationProduct[]' value='<?=$productcode?>'>
										</td>
										<td style='border:0px' align="left"><?=$productname?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$productcode?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
										</td>
									</tr>
								
								<?
								}
								pmysql_free_result( $mSelectRes );

								}
								?>
								</table>
							</div>
					</td>
				</tr>
<tr>
				<td colspan=8 align=center>
					<a href="javascript:CheckForm('<?=$qType?>', '<?=$mSelect['idx']?>' );"><img src="img/btn/btn_input02.gif" alt="등록하기"></a>
				</td>
			</tr>
				

		
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

</script>
 
<script type="text/javascript" src="../js/admin_layer_product_sel.js" ></script>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
