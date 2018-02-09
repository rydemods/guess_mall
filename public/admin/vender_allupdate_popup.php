<?php
/********************************************************************* 
// 파 일 명		: vender_allupdate_popup.php 
// 설     명		: 배송료 및 수수료 설정
// 상세설명	: 관리자 입점관리의 입점업체 관리에서 일괄로 배송료 및 수수료 설정
// 작 성 자		: 2016.04.15 - 김재수
// 수 정 자		: 
// 
// 
*********************************************************************/ 
?>
<?php
#---------------------------------------------------------------
# 기본정보 설정파일을 가져온다.
#---------------------------------------------------------------
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$vender=$_POST["vender"];
	$mode=$_POST["mode"];
	$up_type=$_POST["up_type"];


	if(ord($_ShopInfo->getId())==0 || ord($vender)==0){
		echo "<script>window.close();</script>";
		exit;
	}

#---------------------------------------------------------------
# DB를 처리한다
#---------------------------------------------------------------

	if($mode=="update") {		
		if ($up_type == 'deli_and_rate') {
			#배송비 선정방법 변경 2016-02-16 유동혁
			$basefeetype_select = $_POST['basefeetype_select']; // 배송료 선택 0 - 무료 / 1 - 유료
			if( $basefeetype_select == '1' ){
				$deli_select = $_POST['deli_select']; // 지불방법 0 - 선불, 1 - 착불, 2 - 구매자(선불/착불) 선택
				$basefee_select = $_POST['basefee_select']; // 배송료
				$minprice_select = $_POST['minprice_select']; // 배송료 지불 기준값 ( 미만 )
			} else {
				$deli_select = 0; // 지불방법 0 - 선불, 1 - 착불, 2 - 구매자(선불/착불) 선택
				$basefee_select = 0; // 배송료
				$minprice_select = 0; // 배송료 지불 기준값 ( 미만 )
			}

			$up_deli_price		= $basefee_select; //배송료
			$up_deli_pricetype	= $basefeetype_select; // 배송료 선택
			$up_deli_mini			= $minprice_select; // 배송료 지불 기준값
			$up_deli_select		= $deli_select; // 지불방법
			$up_rate					= $_POST['up_rate']; // 수수료율

			$sql = "UPDATE tblvenderinfo SET ";
			$sql.= "deli_price		= '".$up_deli_price."', ";
			$sql.= "deli_pricetype	= '".$up_deli_pricetype."', ";
			$sql.= "deli_mini		= '".$up_deli_mini."', ";
			$sql.= "deli_select		= '".$up_deli_select."', ";
			$sql.= "rate		= '".$up_rate."' ";
			$sql.= "WHERE vender in ('".str_replace(",","','", $vender)."') ";

			if(pmysql_query($sql,get_db_conn())) {
				// 수수료율에 따른 상품 전체 수수료율 변경
				$sql = "UPDATE tblproduct SET ";
				$sql.= "rate	= '{$up_rate}' ";
				$sql.= "WHERE vender in ('".str_replace(",","','", $vender)."') ";
				pmysql_query($sql,get_db_conn());

				$log_content = "## 입점업체 배송료 및 수수료율 수정(일괄) ## - 벤더 : ".$vender." - 배송료 선택 : ".$up_deli_pricetype." - 지불방법 : ".$up_deli_select." - 배송료 : ".$basefee_select." - 배송료 지불 기준값 : ".$up_deli_mini." - 수수료율 : ".$up_rate;
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
				echo "<html></head><body onload=\"alert('배송료 및 수수료 설정이 완료되었습니다.');opener.location.reload();self.close();\"></body></html>";exit;
			} else {
				echo "<html></head><body onload=\"alert('배송료 및 수수료 설정중 오류가 발생하였습니다.')\"></body></html>";exit;
			}
		} else if ($up_type == 'staff_rate' ) {
			$up_staff_rate = $_POST['up_staff_rate']; // 임직원 할인율

			$sql = "UPDATE tblproductbrand SET ";
			$sql.= "staff_rate		= '".$up_staff_rate."' ";
			$sql.= "WHERE vender in ('".str_replace(",","','", $vender)."') ";

			if(pmysql_query($sql,get_db_conn())) {

				$log_content = "## 임직원 할인율 수정(일괄) ## - 벤더 : ".$vender." - 할인율 : ".$up_staff_rate;
				ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);
				echo "<html></head><body onload=\"alert('임직원 할인율 설정이 완료되었습니다.');opener.location.reload();self.close();\"></body></html>";exit;
			} else {
				echo "<html></head><body onload=\"alert('임직원 할인율 설정중 오류가 발생하였습니다.')\"></body></html>";exit;
			}
		}
	}
	
	$brand_names_text	= "";
	$br_sql	= "SELECT b.brandname FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender WHERE a.vender in ('".str_replace(",","','", $vender)."') ORDER BY a.vender DESC ";

	//echo $br_sql;
    $br_result = pmysql_query($br_sql);
	while ( $br_row = pmysql_fetch_object($br_result) ) {
		$brand_names_text	.= $brand_names_text?", ".$br_row->brandname:$br_row->brandname;
	}
	pmysql_free_result($br_result);
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>일괄 설정</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<script src="../js/jquery.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	var up_type	= form.up_type.value;
	if (up_type == 'deli_and_rate') {
		//배송료 책정방법 변경 2016-02-16 유동혁
		if( $('input[name="basefeetype_select"]:checked').val() == '1' ){
			if( $('input[name="basefee_select"]').val().length == 0 ){
				alert("배송료를 입력하세요.");
				$('input[name="basefee_select"]').focus();
				return;
			} else if( isNaN( $('input[name="basefee_select"]').val() ) ){
				alert("배송료는 숫자만 입력 가능합니다.");
				$('input[name="basefee_select"]').focus();
				return;
			} else if( parseInt( $('input[name="basefee_select"]').val() ) <= 0 ){
				alert("배송료는 0원 이상 입력하셔야 합니다.");
				$('input[name="basefee_select"]').focus();
				return;
			}

			if( isNaN( $('input[name="minprice_select"]').val() ) ){
				$('input[name="minprice_select"]').val( 0 );
			} else if( parseInt( $('input[name="minprice_select"]').val() ) < 0 ){
				$('input[name="minprice_select"]').val( 0 );
			}
		}
	} else if (up_type == 'staff_rate') {
		if( isNaN( $('input[name="up_staff_rate"]').val() ) ){
			alert("임직원 할인율은 숫자만 입력 가능합니다.");
			$('input[name="up_staff_rate"]').focus();
		}
	}

	if(confirm("변경하신 내용을 저장하시겠습니까?")) {
		form.mode.value="update";
		//form.target="processFrame";
		form.submit();
	}
}

function PageResize() {
	var oWidth = document.all.tabs_content.clientWidth + 16;
	var oHeight = document.all.tabs_content.clientHeight + 140;

	window.resizeTo(oWidth,oHeight);
}

</script>
<style type="text/css">
/* ==================================================
	탭
================================================== */

.tabs-menu {}
	.tabs-menu:after {display:block; clear:both; content:"";}
	.tabs-menu li {float:left; position:relative; width:50%; height: 31px;line-height: 31px;float: left;background-color: #f0f0f0; box-sizing:border-box; border:1px solid #d3d3d3; border-bottom:1px solid #4b4b4b;}
	.tabs-menu li.on {position: relative;background-color: #fff; z-index: 5; border:1px solid #4b4b4b; border-bottom:1px solid #fff; }
	.tabs-menu li.on:after {display:block; position:absolute; top:0; right:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:last-child::after {display:none;}
	.tabs-menu li.on:before {display:block; position:absolute; top:0; left:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:first-child::before {display:none;}
	.tabs-menu li a {display:block; font-size:0.8rem; font-weight:bold; color:#aaa; text-align:center;}
	.tabs-menu .on a {color: #4b4b4b;}

.tab-content-wrap {background-color: #fff; }
	.tab-content {display: none;}
	.tab-content-wrap > div:first-child { display: block;}
</style>
<script type="text/javascript">
<!--
$(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
		$(".tabs-menu li").removeClass("on");
		$(this).parent().addClass("on");
        var up_type = $(this).attr("alt");
		$(".tab-con-area").hide();
		$(".tab-con-area."+up_type).show();
		document.form1.up_type.value	= up_type;
    });
});
//-->
</script>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>일괄 설정</p></div>
<div id="tabs_content" style="width:700px">
<div style='display: block;margin:10px 15px;width:100%;'>
<p style='padding:0 0;margin:0 0 5px 0px;'><strong>브랜드명</strong></p>
<p style='padding:0 0;margin:0 0;line-height:18px'><?=$brand_names_text?></p>
</div>
<div id="tabs-container">
<ul class="tabs-menu">
	<li class="on"><a href="javascript:;" alt='deli_and_rate'>배송료 및 수수료 설정</a></li>
	<li><a href="javascript:;" alt='staff_rate'>임직원 할인율</a></li>
</ul>
<div class="tab-content-wrap">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post enctype="multipart/form-data">
<input type=hidden name=mode>
<input type=hidden name=up_type value='deli_and_rate'>
<input type=hidden name=vender value="<?=$vender?>">
<TABLE WIDTH="700" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body class='tab-con-area deli_and_rate'>
	<tr>
		<TD style="padding-top:10pt; padding-right:10pt; padding-bottom:5pt; padding-left:10pt;">
			<table cellpadding="0" cellspacing="0" width="670" align="center" style="table-layout:fixed">
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				
				<tr>
					<th><span>배송료 선택</span></th>
					<td>
						<input type='radio' name='basefeetype_select' id='basefeetype_0' value='0' checked>
						<label for='basefeetype_0'>배송료 <font color='#0000FF'><b>무료</b></font></label>
						<input type='radio' name='basefeetype_select' id='basefeetype_1' value='1'>
						<label for='basefeetype_1' >배송료 <font color='#FF0000'><b>유료</b></font></label>
					</td>
				</tr>
				<tr>
					<th><span>지불방법</span></th>
					<td>
						<input type='radio' name='deli_select' id='deli_0' value='0' checked >
						<label for='deli_0' >배송료 <font color='#CC3D3D'><b>선불</b></font></label>
						<input type='radio' name='deli_select' id='deli_1' value='1'>
						<label for='deli_1' >배송료 <font color='#47C83E'><b>착불</b></font></label>
						<input type='radio' name='deli_select' id='deli_2' value='2'>
						<label for='deli_2' >배송료 <font color='#4374D9'><b>구매자( 선불/착불 ) 선택</b></font></label>
					</td>
				</tr>
				<tr>
					<th><span>배송료</span></th>
					<td>
						배송료 <input type='text' name='basefee_select' value='0' style='text-align: right;'> 원
						<div style='margin-top : 3px; padding : 5px 5px 0px 0px;' >
						<TABLE cellSpacing='0' cellPadding='0' width="100%" border='0' >
							<tr>
								<td align="center" style="border : 3px #57B54A solid; padding : 5px; ">
									구매금액 <input type='text' name='minprice_select' size='10' maxlength='10' value="0" class="input" style="text-align:right;">
									원 미만일 경우 배송비가 청구됩니다.<br>
									<span style="font-size:8pt;color:#2A97A7;line-height:11pt;letter-spacing:-0.5pt;">
										* 구매금액 0 원 입력시 모든 금액에 배송비가 부과됩니다.
									</span>
								</td>
							</tr>
						</table>
						</div>
					</td>
				</tr>
				<tr>
					<th><span>수수료율</span></th>
					<td>
						<input type=text name=up_rate value="0" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
					</td>
				</tr>
				</table>
				<script>
					//배송료 변환 script
					$(document).ready( function(){
						var basefeetype = $('input[name="basefeetype_select"]:checked');
						if( basefeetype.val() == '0'){
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).attr( 'disabled', 'true' );
							});
							$('input[name="basefee_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
							$('input[name="minprice_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
						} else {
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).removeAttr( 'disabled' );
							});
							$('input[name="basefee_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
							$('input[name="minprice_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
						}
					});

					$(document).on( 'click', 'input[name="basefeetype_select"]', function( event ){
						if( $(this).val() == '0' ){
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).attr( 'disabled', 'true' );
							});
							$('input[name="basefee_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
							$('input[name="minprice_select"]').attr( 'disabled', 'true' ).css( 'background-Color', '#EFEFEF' );
						} else {
							$('input[name="deli_select"]').each( function( deli_idx, deli_obj ){
								$(this).removeAttr( 'disabled' );
							});
							$('input[name="basefee_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
							$('input[name="minprice_select"]').removeAttr( 'disabled' ).css( 'background-Color', '' );
						}
					});
				</script>
				</div>
				</td>
			</tr>	
			<tr><td height=10></td></tr>	
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
		</table></td>
	</tr>
</table>
<TABLE WIDTH="700" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;display:none;" id=table_body2 class='tab-con-area staff_rate'>
	<tr>
		<TD style="padding-top:10pt; padding-right:10pt; padding-bottom:5pt; padding-left:10pt;">
			<table cellpadding="0" cellspacing="0" width="670" align="center" style="table-layout:fixed">
			<tr>
				<td>
				<div class="table_style01">				
				
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=140></col>
				<col width=></col>
				<tr>
					<th><span>할인율</span></th>
					<td>
						<input type=text name=up_staff_rate value="0" size=3 maxlength=3 onkeyup="strnumkeyup(this)" class=input>%
					</td>
				</tr>
				</table>
				</div>
				</td>
			</tr>	
			<tr><td height=10></td></tr>	
			<tr>
				<td align=center><a href="javascript:CheckForm();"><img src="images/botteon_save.gif" border="0"></a></td>
			</tr>
		</table></td>
	</tr>
</table>
</form>
</div>
</div>
</div>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
</body>
</html>