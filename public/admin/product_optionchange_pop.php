<?php
/********************************************************************* 
// 파 일 명		: product_optionchange_pop.php 
// 설     명		: 상품 옵션 선택 팝업
// 상세설명	: 해당 상품의 옵션을 선택한다.(현재 주무상품 교환시 사용)
// 작 성 자		: 2016.02.05 - 김재수
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
	include_once($Dir."lib/adminlib.php");
#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$ordercode		= $_REQUEST['ordercode'];
	$idx				= $_REQUEST['idx'];
	$productcode	= $_REQUEST['productcode'];
	$place			= $_REQUEST['place'];

	//ERP 상품을 쇼핑몰에 업데이트한다.
	getUpErpProductUpdate($productcode);

	list($productname, $option1, $option_type, $option1_tf, $option2, $option2_tf, $option2_maxlen)=pmysql_fetch("SELECT productname, option1, option_type, option1_tf, option2, option2_tf, option2_maxlen FROM tblproduct WHERE productcode='".$productcode."'");
	list($opt2_name, $text_opt_content)=pmysql_fetch("SELECT opt2_name, text_opt_content FROM tblorderproduct WHERE ordercode='".$ordercode."' AND idx='".$idx."' AND productcode='".$productcode."'");
?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>선택상품 옵션 변경</title>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
function jsSetComa(str_result){
 var reg = /(^[+-]?\d+)(\d{3})/;   // 정규식
 str_result += '';  // 숫자를 문자열로 변환
 while (reg.test(str_result)){
  str_result = str_result.replace(reg, '$1' + ',' + '$2');
 }
}
//2차 옵션
function option_change(productcode, option_depth, option_totalDepth, option_code) {	

	var sel_option	="<option value=''>============선택============</option>";

	for (var i=option_depth; i < option_totalDepth; i++)
	{
		$("select[name='sel_option"+i+"']").find("option").remove();
		$("select[name='sel_option"+i+"']").append(sel_option);
	}
	if (option_code != '')
	{
		option_code_arr	= option_code.split("|!@#|");
		$.ajax({
			type: "POST",
			url: "ajax.product_option.php",
			data: "productcode="+productcode+"&option_code="+option_code_arr[0]+"&option_depth="+option_depth,
			dataType:"JSON",
			success: function(data){
				var sel_option	="";
				var soldout	="";
				if (data)
				{
					$.each(data, function(){
						if (this.price > 0) {
							var option_price		= "(+"+jsSetComa(this.price)+"원)";
						} else {
							var option_price		= "";
						}
						if (this.soldout == 1)
						{
							soldout = '&nbsp;[품절]';
						} else {
							soldout = '';
						}
						sel_option += "<option value='"+this.code+"'>"+this.code+option_price+soldout+"</option>";
					});
					$("select[name='sel_option"+option_depth+"']").append(sel_option);
				}
			},
			complete: function(data){
			},
			error:function(xhr, status , error){
				alert("에러발생");
			}
		});
	}	
}

//chr(30)처리를 위한 함수
 function chr(code) 
{ 
    return String.fromCharCode(code); 
}

//옵션 변경 전달 함수
function changeSubmit(place) {
	var option1						= document.form1.option1.value;
	var option2						= document.form1.option2.value;
	var option1_arr				= option1.split("@#");
	var option2_arr				= option2.split("@#");
	var sel_option_text			= "";
	var sel_option_text2			= "";
	var sel_option_val			= "";
	var sel_option_text_val		= "";
	var sel_option_price_val	= "";
	var sel_text_option_val		= "";
	var sel_cnt						= 0;
	var sel_text_cnt				= 0;
	var sel_chk					= "Y";
	// 필수 체크
	$(".opt_chk").each(function(){
		if($(this).attr('alt') == 'essential' && $(this).val() == '') {
			sel_chk = "N";
		}
	});

	if (sel_chk == "N")	{
		alert("옵션을 선택 및 입력해 주세요.");
		return;
	} else {
<?if ($option1) {?>
		$(".opt_sel").each(function(){
			var option_code		= $(this).val();
			var option_code_arr	= option_code.split("|!@#|");
			if (sel_option_text == '') {
				sel_option_text	= option1_arr[sel_cnt]+" : "+option_code_arr[0];
				sel_option_val		= option_code_arr[0];
				sel_option_price_val = option_code_arr[1];
			} else {
				sel_option_text	+= " / "+option1_arr[sel_cnt]+" : "+option_code_arr[0];
				sel_option_val		+= chr(30)+option_code_arr[0];
				sel_option_price_val += "||"+option_code_arr[1];
			}
			sel_cnt++;
		});
<?}?>
<?if ($option2) {?>
		$(".opt_text").each(function(){
			if (sel_option_text2 == '') {
				sel_option_text2		= option2_arr[sel_text_cnt]+" : "+$(this).val();
				sel_option_text_val	= $(this).val();
			} else {
				sel_option_text2		+= " / "+option2_arr[sel_text_cnt]+" : "+$(this).val();
				sel_option_text_val	+= "@#"+$(this).val();
			}
			sel_text_cnt++;
		});
<?}?>
		if (sel_option_text2 !='')
		{
			if (sel_option_text == '') {
				sel_option_text	+= sel_option_text2;
			} else {
				sel_option_text	+= " / "+sel_option_text2;
			}
		}

		$("#orderCancelForm", opener.document).find("input[name='sel_option1[]']").eq(place).val(option1);
		$("#orderCancelForm", opener.document).find("input[name='sel_option2[]']").eq(place).val(sel_option_val);
		$("#orderCancelForm", opener.document).find("input[name='sel_text_opt_subject[]']").eq(place).val(option2);
		$("#orderCancelForm", opener.document).find("input[name='sel_text_opt_content[]']").eq(place).val(sel_option_text_val);
		$("#orderCancelForm", opener.document).find("input[name='sel_option_price_text[]']").eq(place).val(sel_option_price_val);
		$("#orderCancelForm", opener.document).find(".sel_optionname").eq(place).html(sel_option_text);
		self.close();
	}

}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 10;
	var oHeight = document.all.table_body.clientHeight + 120;

	window.resizeTo(oWidth,oHeight);
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>선택상품 옵션 변경</p></div>
<TABLE WIDTH="100%" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
<TR>
	<TD style="padding:3pt;">
	<table align="center" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%" class='table_style1'>
		<tr>
			<td align=center width="100" bgcolor='#EFEFEF'><b>상품명</b></td>
			<td align=left><?=$productname?></td>
		</tr>
		</table>
		</td>
	</tr>
	<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
	<input type=hidden name=type>
	<input type=hidden name=block>
	<input type=hidden name=gotopage>
	<input type=hidden name=id value="<?=$id?>">
	<input type=hidden name=date>
	<input type=hidden name=option1 value="<?=$option1?>">
	<input type=hidden name=option2 value="<?=$option2?>">
<?
		if ($option1) {
?>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">기본옵션을 선택해 주세요.</span></td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%" class='table_style1'>
<?
			$option1_arr	= explode("@#", $option1);
			$option1_tf_arr	= explode("@#", $option1_tf);
			$option1_cnt	= count($option1_arr);
			if ($option_type == '0') {							// 조합형
				$option_arr		= get_option( $productcode );
			} else if ($option_type == '1') {					// 독립형
				$option_arr		= get_alone_option( $productcode );
			}

			
			for($s=0;$s < sizeof($option1_arr);$s++) {
				$sel_est			= "essential";
				$sel_est_text	= " *필수";
				if ($option_type == '1' && $option1_tf_arr[$s] == 'F') {// 독립형 옵션이 필수가 아닐경우
					$sel_est			= "";
					$sel_est_text	= "";
				}
?>
		<tr>
			<td align=center width="100" bgcolor='#EFEFEF'><b><?=$option1_arr[$s]?></b></td>
			<td align=left>
<?
				if ($option_type == '0') {							// 조합형
?>
			<select name="sel_option<?=$s?>" class='opt_chk opt_sel select'<?if(($s + 1) != $option1_cnt) {?> onChange="javascript:option_change('<?=$productcode?>','<?=($s+1)?>', '<?=$option1_cnt?>', this.value)"<?}?> alt='<?=$sel_est?>' data-p="0">
				<option value=''>============선택============</option>
<?				
					if ($s == 0) {
						foreach($option_arr as $key => $val) {
							$disabled_on	= "";
							if ($val['price'] > 0) {
								$option_price		= "(+".number_format($val['price'])."원)";
							} else {
								$option_price		= "";
							}

							if($val['soldout'] == 1) {
								$disabled_on = ' disabled';
								$soldout = '&nbsp;[품절]';
							} else {
								$disabled_on = '';
								$soldout = '';
							}
?>
				<option value="<?=$val['code']?>|!@#|<?=$val['price']?>"<?=$disabled_on?>><?=$val['code'].$option_price.$soldout?></option>
<?
						}
					}
			
?>			
			
			</select><?=$sel_est_text?>
<?

				} else if ($option_type == '1') {					// 독립형
?>
			<select name="sel_option<?=$s?>" class='opt_chk opt_sel select' alt='<?=$sel_est?>'>
				<option value=''>============선택============</option>
<?				
					$oa_cnt	= 0;
					foreach($option_arr[$option1_arr[$s]] as $key => $val) {	
						$option_code_arr		= explode( chr(30), $val->option_code);
						$option_code			= $option_code_arr[1];
						if ($val->option_price > 0) {
							$option_price		= " (+".number_format($val->option_price)."원)";
						} else {
							$option_price		= "";
						}
?>
				<option value="<?=$option_code?>|!@#|<?=$val->option_price?>"><?=$option_code.$option_price?></option>
<?
						$oa_cnt++;
					}	
?>			
			
			</select><?=$sel_est_text?>
<?
				}
?>
			</td>
		</tr>
<?
			}
?>
		</table>
		</td>
	</tr>

<?
		}
?>
<?

		if ($option2) {
?>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;" class="font_size"><span style="letter-spacing:-0.5pt;">추가옵션을 입력해 주세요.</span></td>
	</tr>
	<tr>
		<td style="padding-top:2pt; padding-bottom:2pt;">
		<table cellpadding="0" cellspacing="0" width="100%" class='table_style1'>

<?
			$option2_arr				= explode("@#", $option2);
			$option2_cnt				= count($option2_arr);

			$option2_tf_arr				= explode("@#", $option2_tf);
			$option2_maxlen_arr	= explode("@#", $option2_maxlen);

			$text_opt_content_arr	= explode("@#", $text_opt_content);

			for($s=0;$s < sizeof($option2_arr);$s++) {
				$sel_est			= "essential";
				$sel_est_text	= " *필수";
				if ($option2_tf_arr[$s] == 'F') {// 독립형 옵션이 필수가 아닐경우
					$sel_est			= "";
					$sel_est_text	= "";
				}
?>
		<tr>
			<td align=center width="100" bgcolor='#EFEFEF'><b><?=$option2_arr[$s]?></b></td>
			<td align=left>
				<input name="text_option<?=$s?>" value="<?=$text_opt_content_arr[$s]?>" size="45" maxlength="<?=$option2_maxlen_arr[$s]?>" class="opt_chk opt_text input" alt='<?=$sel_est?>'><?=$sel_est_text?>
			</td>
		</tr>
<?
			}
?>
		</table>
		</td>
	</tr>
<?
		}
?>
	</table>
	</TD>
</TR>
<TR>
	<TD align=center><a href="javascript:changeSubmit('<?=$place?>')"><img src="images/btn_ok3.gif"border="0" vspace="2" border=0></a> <a href="javascript:self.close()"><img src="images/btn_close.gif"border="0" vspace="2" border=0></a></TD>
</TR>
</TABLE>
</body>
</html>
