<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
# 파일 클래스 추가
include_once($Dir."lib/file.class.php");
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
// 20170412 

$brand_code = $_POST['brand_code'];
$mode = $_POST['mode'];
$bno = $_POST['bno'];

$idx            = $_POST['idx'];
$visible_mode   = $_POST['visible_mode'];

$story_content = $_POST['story_content'];
$story_content = str_replace("\r\n","<br/>",$story_content);

$concept_content = $_POST['concept_content'];
$concept_content = str_replace("\r\n","<br/>",$concept_content);

$view_type = $_POST['view_type'];
$view_number = $_POST['view_number'];
$banner_sort = $_POST['banner_sort'];
$brand_bridx = $_POST['brand_bridx'];
$select_from = $_POST['select_from'];
$brand_link = $_POST['brand_link'];

$v_banner_img = $_POST['v_banner_img'];

//관련상품
$relationProduct = $_POST['relationProduct'];

// 이미지 업로드 
//$imagepath = $cfg_img_path['hotdeal'];
$imagepath = $Dir.DataDir."shopimages/mainbanner/";
$filedata= new FILE($imagepath);	// 클래스lib 상속받아야한다 [include_once($Dir."lib/file.class.php");]
$errmsg = $filedata->chkExt();

if($errmsg==''){
	$up_file = $filedata->upFiles();
}
if($banner_sort == '' || !(preg_replace("/[^0-9]*/s", "", $banner_sort))){
	$banner_sort = 0;
}
if($view_number == '' || !(preg_replace("/[^0-9]*/s", "", $view_number))){
	$view_number = 1;
}

$top_img_pc = $up_file["banner_img"][0]["v_file"];
$top_img_mobile = $up_file["banner_img"][1]["v_file"];
$gnb_img_pc = $up_file["banner_img"][2]["v_file"];
$gnb_img_mobile = $up_file["banner_img"][3]["v_file"];
// 모드상태 별 입력,수정,삭제 기능
if($mode == "insert"){
	// 입력할경우 [table = tblmainbrand]

	if($view_type == 1) {
		$qry = "SELECT COUNT(*) as cnt FROM tblmainbrand WHERE brand_status = 0 AND view_type = 1 AND brand_bridx = '".$brand_code."'";
		$result = pmysql_query($qry,get_db_conn());
		$count = pmysql_fetch_array($result);
		if($count['cnt'] > 0){
			msg('이미 노출 설정한 브랜드 입니다.','brand_main.php');
			return;
		}
	}
	
	
	$brand_sql = "SELECT bridx,brandname,vender FROM tblproductbrand WHERE bridx = '".$brand_code."'";
	$list_result = pmysql_query($brand_sql,get_db_conn());
	while($row=pmysql_fetch_object($list_result)){
		$temp_bridx = $row->bridx;
		$temp_brandname = $row->brandname;
		$temp_vender = $row->vender;
	}
	
	$qry = "INSERT 
				INTO 
					tblmainbrand (
					brand_bridx,
					brand_name,
					brand_vender,
					top_banner_img_pc,
					top_banner_img_mobile,
					story_content,
					concept_content,
					banner_sort,
					gnb_banner_img_pc,
					gnb_banner_img_mobile,
					view_img,
					view_type,
					view_number,
					brand_link,
					brand_status,
					udpdt,
					regdt)
				VALUES (
					'".$temp_bridx."',
					'".$temp_brandname."',
					'".$temp_vender."',
					'".$top_img_pc."',
					'".$top_img_mobile."',
					'".$story_content."',
					'".$concept_content."',
					'".$banner_sort."',
					'".$gnb_img_pc."',
					'".$gnb_img_mobile."',
					null,
					'".$view_type."',
					'".$view_number."',
					'".$brand_link."',
					'0',
					'now()',
					'now()'
				)";
	
	$qry.= " RETURNING bno ";
	$result = pmysql_query($qry,get_db_conn());
	
	if($row = pmysql_fetch_object($result)){
		
		if($relationProduct){
			for($i=0;$i<count($relationProduct);$i++){
				$relationProduct_sql = "INSERT INTO tblmainbrand_product ";
				$relationProduct_sql.= "(tblmainbrand_bno, productcode, date) ";
				$relationProduct_sql.= "VALUES (".$row->bno.", '".$relationProduct[$i]."', '".date("YmdHis")."') ";
				pmysql_query($relationProduct_sql,get_db_conn());
			}
		}
		
		msg('등록되었습니다.','brand_main.php');
	}else{
		msg('등록실패','brand_main.php');
	}
	$qry = '';
} else if ($mode == "modify"){
	// 수정할경우
	$modi_qry = "SELECT * FROM tblmainbrand WHERE brand_status = 0 AND bno = '".$bno."'";
	$modi_result = pmysql_query($modi_qry,get_db_conn());
	$modi_row = pmysql_fetch_array($modi_result);
	
	$brand_code = $modi_row['brand_bridx'];
	$story_content = str_replace("<br/>","\r\n",$modi_row['story_content']);
	$concept_content = str_replace("<br/>","\r\n",$modi_row['concept_content']);

	//////////////////////////
	$bProductSql = "SELECT a.productcode,b.productname,b.sellprice,b.tinyimage ";
	$bProductSql.= "FROM tblmainbrand_product a ";
	$bProductSql.= "JOIN tblproduct b ON a.productcode=b.productcode ";
	$bProductSql.= "WHERE a.tblmainbrand_bno = ".trim($bno);
	$bProductResult = pmysql_query($bProductSql,get_db_conn());
	while($bProductRow = pmysql_fetch_array($bProductResult)){
		$thisBannerProduct[] = $bProductRow;
	}
	pmysql_free_result( $bProductResult );
	/////////////////////////
	$modi_qry = '';
} else if ($mode == "modifyok") {

	if($view_type == 1) {
		$qry = "SELECT COUNT(*) as cnt FROM tblmainbrand WHERE brand_status = 0 AND view_type = 1 AND brand_bridx = '".$brand_code."' and bno!=".$bno;
		$result = pmysql_query($qry,get_db_conn());
		$count = pmysql_fetch_array($result);
		if($count['cnt'] > 0){
			msg('이미 노출 설정한 브랜드 입니다.','brand_main.php');
			return;
		}
	}
	
	$brand_sql = "SELECT bridx,brandname,vender FROM tblproductbrand WHERE bridx = '".$brand_code."'";
	$list_result = pmysql_query($brand_sql,get_db_conn());
	while($row=pmysql_fetch_object($list_result)){
		$temp_bridx = $row->bridx;
		$temp_brandname = $row->brandname;
		$temp_vender = $row->vender;
	}

	for($u=0;$u<5;$u++) {
		if( strlen( $up_file["banner_img"][$u]["v_file"] ) > 0 ){
			// 파일삭제 불가 권한문제인뜻
// 			if( is_file( $imagepath.$v_banner_img[$u] ) > 0 ){
// 				$up_file->removeFile( $v_banner_img[$u] );
// 			}
			if ($u == 0) $where[] = "top_banner_img_pc='".$up_file["banner_img"][$u]["v_file"]."'";
			if ($u == 1) $where[] = "top_banner_img_mobile='".$up_file["banner_img"][$u]["v_file"]."'";
			if ($u == 2) $where[] = "gnb_banner_img_pc='".$up_file["banner_img"][$u]["v_file"]."'";
			if ($u == 3) $where[] = "gnb_banner_img_mobile='".$up_file["banner_img"][$u]["v_file"]."'";
		} else if( strlen( $up_file["v_banner_img"][$u]["v_file"] ) > 0 ){
			if ($u == 0) $where[] = "top_banner_img_pc='".$up_file["v_banner_img"][$u]."'";
			if ($u == 1) $where[] = "top_banner_img_mobile='".$up_file["v_banner_img"][$u]."'";
			if ($u == 2) $where[] = "gnb_banner_img_pc='".$up_file["v_banner_img"][$u]."'";
			if ($u == 3) $where[] = "gnb_banner_img_mobile='".$up_file["v_banner_img"][$u]."'";
		}
	}
	
	$where[]="brand_bridx='".$temp_bridx."'";
	$where[]="brand_name='".$temp_brandname."'";
	$where[]="brand_vender='".$temp_vender."'";
	$where[]="story_content='".$story_content."'";
	$where[]="concept_content='".$concept_content."'";
	$where[]="banner_sort='".$banner_sort."'";
	$where[]="view_type='".$view_type."'";
	$where[]="view_number='".$view_number."'";
	$where[]="brand_link='".$brand_link."'";
	$where[]="udpdt=now()";
	
	$qry = "UPDATE tblmainbrand SET ";
	$qry.=implode(", ",$where);
	$qry.=" WHERE bno='".$bno."' ";
	
	pmysql_query($qry,get_db_conn());
	if(!pmysql_error()){
		if($relationProduct){
			$relationProduct_del = "DELETE FROM tblmainbrand_product WHERE tblmainbrand_bno = '".$bno."' ";
			pmysql_query($relationProduct_del,get_db_conn());
			for($i=0;$i<count($relationProduct);$i++){
				$relationProduct_sql = "INSERT INTO tblmainbrand_product ";
				$relationProduct_sql.= "(tblmainbrand_bno, productcode, date) ";
				$relationProduct_sql.= "VALUES (".$bno.", '".$relationProduct[$i]."', '".date("YmdHis")."') ";
				pmysql_query($relationProduct_sql,get_db_conn());
			}
		}
		msg('수정이 완료되었습니다.','brand_main.php');
	
	}else{
		msg('오류가 발생하였습니다.', 'brand_main.php');
	}
	$qry = '';
	
} else if ($mode == "visible_mode"){
	if ( count($idx) >= 1 ) {
		if($visible_mode == 1) {
			foreach ($idx as $key => $value) {
				//echo "{$key} => {$value} <br>";
				$temp_str = explode ("|", $value);
				$qry = "SELECT COUNT(*) as cnt FROM tblmainbrand WHERE brand_status = 0 AND view_type = 1 AND brand_bridx = '".$temp_str[1]."'";
				$result = pmysql_query($qry,get_db_conn());
				$count = pmysql_fetch_array($result);
				if($count['cnt'] == 0){
					$sql  = "UPDATE tblmainbrand SET view_type = 1 WHERE bno = ".$temp_str[0];
					pmysql_query($sql);
				}
			}
			msg('설정 완료 되었습니다.','brand_main.php');
		} else {
			//$whereIdx = implode(",", $idx);
			foreach ($idx as $key => $value) {
				$temp_str = explode ("|", $value);
				$whereIdx .= $temp_str[0].",";
			}
			$whereIdx = substr($whereIdx, 0, strlen($whereIdx) -1);
			$sql  = "UPDATE tblmainbrand SET view_type = {$visible_mode} WHERE bno in ( " . $whereIdx . " ) ";
			
			if(pmysql_query($sql)){
				msg('설정 완료 되었습니다.','brand_main.php');
			}else{
				msg('설정 실패','brand_main.php');
			}
		}
	}
// 	echo "visible_mode";
// 	exit();
} else if ($mode == "delete"){
	// 삭제할 경우
	$qry = "UPDATE
				tblmainbrand
					SET
						brand_status = 1
				WHERE bno = ".$bno;
	
	$relationProduct_del = "DELETE FROM tblmainbrand_product WHERE tblmainbrand_bno = '".$bno."' ";
	pmysql_query($relationProduct_del,get_db_conn());
	
	if(pmysql_query($qry)){
		msg('삭제되었습니다.','brand_main.php');
	}else{
		msg('삭제실패','brand_main.php');
	}
} 

# 배너 페이징
$page_sql = "SELECT COUNT(*) as t_count FROM tblmainbrand WHERE brand_status = 0";

$paging = new newPaging($page_sql, 10, 10);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

if($select_from != "all" && $select_from != ""){
	$where = " AND brand_bridx = ".$select_from;
}

// 리스트 목록
$list_sql = "SELECT bno,brand_bridx,brand_name,top_banner_img_pc,top_banner_img_mobile,banner_sort,gnb_banner_img_pc,gnb_banner_img_mobile,view_type,view_number
 				FROM tblmainbrand WHERE brand_status = 0 ".$where." ORDER BY bno desc";

// echo $list_sql;
// exit();

$sql = $paging->getSql( $list_sql );
$result = pmysql_query( $sql, get_db_conn() );
while( $row = pmysql_fetch_array( $result ) ){
	$data_brand[] = $row;
}

// 브랜드 코드
$brand_sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname
				FROM    tblvenderinfo a
				JOIN    tblproductbrand b on a.vender = b.vender
				ORDER BY b.brandname ASC";
$brand_result = pmysql_query($brand_sql,get_db_conn());

$brand_option = '';
while($ref2_data=pmysql_fetch_object($brand_result)){
	if($ref2_data->bridx == $brand_code){
		$brand_option .= '<option value="'.$ref2_data->bridx.'" selected="selected">'.$ref2_data->brandname.'</option><br>';
	} else {
		$brand_option .= '<option value="'.$ref2_data->bridx.'" >'.$ref2_data->brandname.'</option><br>';
	}
}
// 브랜드 끝

// 브랜드별 검색
$brand_result = pmysql_query($brand_sql,get_db_conn());
$brand_option2 = '';
while($ref2_data=pmysql_fetch_object($brand_result)){
	if($ref2_data->bridx == $select_from){
		$brand_option2 .= '<option value="'.$ref2_data->bridx.'" selected="selected">'.$ref2_data->brandname.'</option><br>';
	} else {
		$brand_option2 .= '<option value="'.$ref2_data->bridx.'" >'.$ref2_data->brandname.'</option><br>';
	}
}

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

function successForm(){
	if(check_form()){
		if( confirm('등록하시겠습니까?') ){
			$("#mode").val( 'insert' );
			document.eventform.submit();
		} else {
			return;
		}

	}

}

function check_form() {
	var procSubmit = true;
	$(".required_value").each(function(){
		if(!$(this).val()){
			if($(this).attr('label') == ""){
				alert($(this).attr('label')+"를 정확히 입력해 주세요");
			} else {
				alert($(this).attr('label')+" 은(는) 필수입력 값 입니다.");
			}
				
			$(this).focus();
			procSubmit = false;
			return false;
		}
	})

	if(procSubmit){
		return true;
	}else{
		return false;
	}
}

function deleteForm (no){
	$("#mode").val('delete');
	$("#bno").val(no);
	document.eventform.submit();
}

function modifyForm (no,brand_bridx) {
	$("#mode").val('modify');
	$("#bno").val(no);
	$("#brand_bridx").val(brand_bridx);
	document.eventform.submit();
}

function modifyFormOK (no) {
	if(check_form()){

// 		var img_cnt	= 0;
// 		if ($("input[name='banner_img[0]']").val() != '') img_cnt++;

// 		if (img_cnt == 0)
// 		{
// 			alert('이미지를 하나이상 등록해야 합니다.');
// 			return;
// 		}
		
		$("#mode").val('modifyok');
		$("#bno").val(no);
		document.eventform.submit();
	}
}

function changeVisible(val) {
    // val : 1 => 노출, 0 => 비노출

    if ( $("input[name='idx[]']:checked").length == 0 ) {
        alert('하나 이상을 선택해 주세요.');
    } else {
        if ( val == "1" ) {
            msg = "노출 설정 하시겠습니까?";
        } else {
            msg = "비노출 설정 하시겠습니까?";
        }

        if ( confirm(msg) ) {
        	$("#mode").val('visible_mode');
            document.eventform.visible_mode.value = val;
            document.eventform.submit();
        }
    }
}

function selectForm (val){
    document.eventform.select_from.value = val;
    document.eventform.submit();
}
</script>

<div class="admin_linemap"><div class="line"><p>현재위치 : 디자인관리 &gt; 브랜드 관리 &gt;<span>브랜드별 메인관리</span></p></div></div>
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
			<?php //include("menu_market.php"); ?>
			<?php include("menu_design.php"); ?>
			</td>

			<td></td>

			<td valign="top">

<div class="title_depth3">등록/수정</div>

<form name="eventform" method="post" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data" onsubmit="return chkform('<?=$mode?>');">
<?include("layer_prlistPop.php");?>
	<input type="hidden" name="mode" id="mode" value="<?=$mode?>">
	<input type="hidden" name="bno" id="bno" value="<?=$sno?>">
	<!-- <input type="hidden" name="layermode" id="layermode" value="hotdeal"> -->	<!-- layermode = "hotdeal" 넘길시 조회불가 -->
	<input type="hidden" name="visible_mode" id="visible_mode" value="">
	<input type="hidden" name="brand_bridx" id="brand_bridx" value="">
	<input type="hidden" name="select_from" id="select_from" value="">

				<!-- 테이블스타일01 -->
				<div class="table_style01 pt_20" style="position:relative">
					<div id="img_view_div" style="position:absolute;top:150px;left:400px;"><img style="display:none;width:500px" ></div>
					<table cellpadding=0 cellspacing=0 border=0 width=100%>
						<tr>
							<th><span>브랜드 선택 *</span></th>
							<td>
								 <select name="brand_code" class="required_value" label="브랜드 선택">
								 	<option value="">=====브랜드선택=====</option>
									<?php echo $brand_option;?>
								 </select>
							</td>
						</tr>
						<tr>
							<th rowspan="2"><span>상단배너</span></th>
							<td>
								<span>배경 이미지(PC)</span><div class="font_orange">(이미지 : 1920X900) <input type=file name="banner_img[0]" style="WIDTH: 400px"></div>
								<?
									if(is_file($imagepath.$modi_row['top_banner_img_pc'])){
								?>
									<span><br><img src="<?=$imagepath?><?=$modi_row['top_banner_img_pc']?>" style='max-width: 125px;' class="img_view_sizeset"></span>
								<?
									}
								?>
								<input type=hidden name="v_banner_img[0]" value="<?=$modi_row['top_banner_img_pc']?>" >
							</td>
						</tr>
						<tr>
							<td>
								<span>배경 이미지(MOBILE)</span><div class="font_orange">(이미지 : 640x1354) <input type="file" name="banner_img[1]" alt="썸네일 이미지" value="<?=$modi_row['top_banner_img_mobile']?>"/></div>
								<?
									if(is_file($imagepath.$modi_row['top_banner_img_mobile'])){
								?>
									<span><br><img src="<?=$imagepath?><?=$modi_row['top_banner_img_mobile']?>" style='max-width: 125px;' class="img_view_sizeset"></span>
								<?
									}
								?>
								<input type=hidden name="v_banner_img[1]" value="<?=$modi_row['top_banner_img_mobile']?>" >
							</td>
						</tr>
						<tr>
							<th><span>브랜드 링크 </span></th>
							<td>
								<input type="text" name="brand_link" value="<?=$modi_row['brand_link'] ?>"/>
							</td>
						</tr>
						<tr>
							<th><span>STORY 문구 *</span></th>
							<td>
								<textarea name=story_content rows="7" cols="70" id="story_content" wrap=off style="resize:none;" class="required_value" label="STORY 문구" ><?= $story_content;?></textarea>
							</td>
						</tr>
						<tr>
							<th><span>CONCEPT 문구 *</span></th>
							<td>
								<textarea name=concept_content rows="7" cols="70" id="story_content" wrap=off style="resize:none;" class="required_value" label="CONCEPT 문구"><?=$concept_content ?></textarea>
							</td>
						</tr>
						<tr>			<!-- javascript:T_layer_open('layer_product_sel','relationProduct'); |||||  javascript:T_layer_open('layer_product_sel','pdt_code');-->
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
									<?foreach($thisBannerProduct as $bannerProductKey=>$bannerProduct){?>	
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
                                                    <img style="width: 40px; height:40px;" src="<?=getProductImage($Dir.DataDir.'shopimages/product/', $bannerProduct['tinyimage'] );?>" border="1"/>
												<input type='hidden' name='relationProduct[]' value='<?=$bannerProduct[productcode]?>'>
											</td>
											<td style='border:0px' align="left"><?=$bannerProduct[productname]?>&nbsp;&nbsp;<img src="images/icon_del1.gif" onclick="javascript:T_relationPrDel('<?=$bannerProduct[productcode]?>','relationProduct');" border="0" style="cursor: hand;vertical-align:middle;" />
											</td>
										</tr>
									<?}?>
									</table>
								</div>
							</td>
						</tr>
						<tr>
							<th rowspan="2"><span>GNB배너</span></th>
							<td>
								PC&nbsp; &nbsp; &nbsp; &nbsp;: <input type="file" name="banner_img[2]" alt="썸네일 이미지" />
								<?
									if(is_file($imagepath.$modi_row['gnb_banner_img_pc'])){
								?>
									<span><br><img src="<?=$imagepath?><?=$modi_row['gnb_banner_img_pc']?>" style='max-width: 125px;' class="img_view_sizeset"></span>
								<?
									}
								?>
								<input type=hidden name="v_banner_img[2]" value="<?=$modi_row['gnb_banner_img_pc']?>" >
							</td>
						</tr>
						<tr>
							<td>
								MOBILE : <input type="file" name="banner_img[3]" alt="썸네일 이미지" />
								<?
									if(is_file($imagepath.$modi_row['gnb_banner_img_mobile'])){
								?>
									<span><br><img src="<?=$imagepath?><?=$modi_row['gnb_banner_img_mobile']?>" style='max-width: 125px;' class="img_view_sizeset"></span>
								<?
									}
								?>
								<input type=hidden name="v_banner_img[3]" value="<?=$modi_row['gnb_banner_img_mobile']?>" >
							</td>
						</tr>
						<tr>
							<th><span>노출여부 *</span></th>
							<td>
					<?php if($modi_row['view_type'] == 0) {?>
								<input type="radio" name="view_type" value="0" alt="비노출" checked="checked"/> 비노출
								<input type="radio" name="view_type" value="1" alt="노출" /> 노출
					<?php } else if($modi_row['view_type'] == 1){?>
								<input type="radio" name="view_type" value="0" alt="비노출" /> 비노출
								<input type="radio" name="view_type" value="1" alt="노출" checked="checked"/> 노출
					<?php }?>
							</td>
						</tr>
						<tr>
							<th><span>노출순서</span></th>
							<TD><INPUT maxLength=10 size=10 id='view_number' name='view_number' value="<?=$modi_row['view_number']?>" ></TD>
						</tr>
					</table>
				</div>
				<div style="width:100%;text-align:center">
<?php 
				if($mode == "modify"){
?>
				 	<a href="javascript:modifyFormOK('<?=$bno ?>');"><span class="btn-point">수정</span></a>
<?php 
				} else {
?>
					<a href="javascript:successForm();"><span class="btn-point">등록</span></a>
<?php 
				}
?>
					<a href="brand_main.php"><span class="btn-basic">목록으로</span></a>
				</div>
				<div style="width:100%;text-align:right;">
					브랜드 : 
					<select onchange="selectForm(this.value)">
						<option value="all">=====전체=====</option>
						<?php echo $brand_option2;?>
					</select>
				</div>
				<div class="table_style02">
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<colgroup>
							<col width='50'>
							<col width='50'>
							<col width='150'>
							<col width='200'>
							<col width='200'>
							<col width='70'>
							<col width='200'>
							<col width='200'>
							<col width='100'>
							<col width='100'>
							<col width='100'>
							<col width='100'>
						</colgroup>
						<tr>
							<th><input type="checkbox" id="allCheck" onClick="CheckAll()";></th>
							<th>번호</th>
							<th>브랜드</th>
							<th>상단배너PC</th>
							<th>상단배너MOBILE</th>
							<th>진열수</th>
							<th>GNB배너PC</th>
							<th>GNB배너MOBILE</th>
							<th>노출순서</th>
							<th>노출여부</th>
							<th>수정</th>
							<th>삭제</th>
						</tr>
						<!-- list -->
<?php 
					$cnt=0;
// 					while($row=pmysql_fetch_object($list_result)){	
					foreach($data_brand as $row){
						$temp_top_banner_img_pc = getProductImage($imagepath, $row['top_banner_img_pc'] );
						$temp_top_banner_img_mobile = getProductImage($imagepath, $row['top_banner_img_mobile']);
						$temp_gnb_banner_img_pc = getProductImage($imagepath, $row['gnb_banner_img_pc']);
						$temp_gnb_banner_img_mobile = getProductImage($imagepath, $row['gnb_banner_img_mobile']);
						if( count( $row ) > 0 ) {
							$number = ( $t_count - ( 10 * ( $gotopage - 1 ) ) - $cnt );
							list($product_cnt) = pmysql_fetch("SELECT count(*) as product_cnt  FROM tblmainbrand_product WHERE tblmainbrand_bno = '".$row['bno']."'");
?>
						<tr>
							<td><input type="checkbox" name="idx[]" value="<?=$row['bno']."|".$row['brand_bridx']?>"></td>
							<td><?=$number?></td>
							<td><?=$row['brand_name']?></td>
							<?php if($row['top_banner_img_pc'] != '') {?>
							<td><img src="<?=$imagepath.$row['top_banner_img_pc'] ?>" style="max-width : 70px;" border=1></td>
							<?php } else if($row['top_banner_img_pc'] == ''){?>
							<td><img src="<?=$temp_top_banner_img_pc."?v".date("His")?>?>" style="max-width : 70px;" border=1></td>
							<?php }?>
							<?php if($row['top_banner_img_mobile'] != '') {?>
							<td><img src="<?=$imagepath.$row['top_banner_img_mobile'] ?>" style="max-width : 70px;" border=1></td>
							<?php } else if($row['top_banner_img_mobile'] == ''){?>
							<td><img src="<?=$temp_top_banner_img_mobile."?v".date("His")?>?>" style="max-width : 70px;" border=1></td>
							<?php }?>
							<td><?=$product_cnt?>개</td>
							<?php if($row['gnb_banner_img_pc'] != '') {?>
							<td><img src="<?=$imagepath.$row['gnb_banner_img_pc'] ?>" style="max-width : 70px;" border=1></td>
							<?php } else if($row['gnb_banner_img_pc'] == ''){?>
							<td><img src="<?=$temp_gnb_banner_img_pc."?v".date("His")?>?>" style="max-width : 70px;" border=1></td>
							<?php }?>
							<?php if($row['gnb_banner_img_mobile'] != '') {?>
							<td><img src="<?=$imagepath.$row['gnb_banner_img_mobile'] ?>" style="max-width : 70px;" border=1></td>
							<?php } else if($row['gnb_banner_img_mobile'] == ''){?>
							<td><img src="<?=$temp_gnb_banner_img_mobile."?v".date("His")?>?>" style="max-width : 70px;" border=1></td>
							<?php }?>
							<td><?=$row['view_number']?></td>
							<?php if($row['view_type'] == 1) {?>
							<td>노출</td>
							<?php } else if($row['view_type'] == 0){?>
							<td>비노출</td>
							<?php }?>
							<td><a href="javascript:modifyForm('<?=$row['bno']?>','<?=$row['brand_bridx']?>');"><img src="images/btn_edit.gif"></td>
							<td><a href="javascript:deleteForm('<?=$row['bno']?>');"><img src="images/btn_del.gif"></td>
						</tr>
<?php
							$cnt++;
						} else {
							
?>
						<tr>
							<td colspan='11' >목록이 존재하지 않습니다.</td>
						</tr>
<?php 
						}
 					}			
?>
					</TABLE>
				</div>
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
						<ul><?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?></ul>
					</div>
				</div>
				<div style="width:100%;text-align:center">
					<a href="javascript:changeVisible('1');"><span class="btn-point">노출 설정</span></a>
                    <a href="javascript:changeVisible('0');"><span class="btn-basic">비노출 설정</span></a>
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

<form name="idxform" method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">

<script language="javascript">
function GoPage(block,gotopage) {
	document.idxform.block.value=block;
	document.idxform.gotopage.value=gotopage;
	document.idxform.submit();
}
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
