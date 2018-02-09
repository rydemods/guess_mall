<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

$item_type				= $_POST['item_type'];
$excel_sql				= $_POST["excel_sql"];
$excel_sql_orderby	= $_POST["excel_sql_orderby"];
$oc_step	= $_POST["oc_step"];
$redeliverytype=$_POST["redeliverytype"];
$ordercodes			= $_POST["ordercodes"];
$idxs						= $_POST["idxs"];
$oc_no					= $_POST["oc_no"];

if($ordercodes) {
	$excel_sql_add		= " AND a.ordercode IN ('".str_replace(",","','", $ordercodes)."') ";
} else if($idxs) {
	$excel_sql_add		= " AND a.idx IN ('".str_replace(",","','", $idxs)."') ";
} else if($oc_no) {
	$excel_sql_add		= " AND b.oc_no IN (".$oc_no.") ";
}

if($oc_step!=''){
	 $qrystep= "AND toc.oc_step='{$oc_step}' ";
}

$excel_sql				= $excel_sql.$excel_sql_add.$qrystep.$excel_sql_orderby;

if(ord($_ShopInfo->getId())==0 || ord($excel_sql)==0 || ord($item_type)==0){
	echo "<script>alert('잘못된 접근입니다.');window.close();</script>";
	exit;
}

$excel_info = "";
if ($item_type == 'order_all' || $item_type == 'order_all_0' || $item_type == 'order_all_1' || $item_type == 'order_all_2' || $item_type == 'order_all_3' || $item_type == 'order_all_4' || $item_type == 'cs_order_all') {
	$down_type	= "orderinfo";
	$excel_info		= "vnum,date,ordercode,sender_name,idnum,sender_tel,sender_email,product,paymethod,price,coupon_price,usereserve,deli_price,totprice,receiver_name,deli_gbn,is_mobile,delivery_type";
} else if ($item_type == 'order_product_all' || $item_type == 'order_product_all_0' || $item_type == 'order_product_all_3' || $item_type == 'order_product_all_4' || $item_type == 'order_misu') {
	$down_type	= "orderproduct";
	$excel_info		= "vnum,date,ordercode,sender_name,idnum,sender_tel,sender_email,brand,product,option,prodcode,colorcode,self_goods_code,paymethod,price,quantity,coupon_price,usereserve,usepoint,deli_price,totprice,deli_gbn,is_mobile,staff_sale_price,delivery_type";
/*
} else if ($item_type == 'order_misu') {
	$down_type	= "orderinfo";
	$excel_info		= "vnum,date,ordercode,sender_name,idnum,sender_tel,sender_email,product,paymethod,price,coupon_price,usereserve,deli_price,totprice,deli_gbn";
*/
} else if ($item_type == 'order_product_all_2' || $item_type == 'order_product_all_1') {
	$down_type	= "orderproduct";
	$excel_info		= "vnum,date,ordercode,sender_name,idnum,sender_tel,sender_email,brand,product,option,prodcode,colorcode,self_goods_code,paymethod,price,quantity,coupon_price,usereserve,usepoint,deli_price,totprice,deli_gbn,is_mobile,staff_sale_price,post2,addr,delivery_type";

} else if ($item_type == 'order_delivery') {
	$down_type	= "orderproduct";
	//$excel_info		= "idx,ordercode,bank_date,product,productcode,option,prodcode,colorcode,self_goods_code,paymethod,sell_price,quantity,receiver_name,post2,addr,receiver_tel1,receiver_tel2,ord_msg2,sender_name,idnum,sender_post,sender_addr,sender_tel2,sender_tel,deli_company,deli_num";
	$excel_info		= "idx,ordercode,bank_date,product,productcode,online_goods_code,tag_style_no,colorcode,sizecode,paymethod,sell_price,quantity,receiver_name,post2,addr,receiver_tel1,receiver_tel2,ord_msg2,store_regdt,sender_name,regdt,idnum,sender_post,sender_addr,sender_tel2,sender_tel,deli_company,deli_num,delivery_type,store_code,first_online_code,sumqty,A1801_qty,A1770_qty,A1771_qty";
} else if ($item_type == 'order_cancel' || $item_type == 'order_cancel_0') {
	$down_type	= "orderproduct";
	$excel_info		= "vnum,date,ordercode,sender_name,idnum,sender_tel,sender_email,brand,product,prodcode,colorcode,self_goods_code,paymethod,price,quantity,coupon_price,usereserve,deli_price,totprice,deli_gbn2,delivery_type";
	if ($item_type == 'order_cancel_0') $excel_info	   .= ",is_mobile";
} else if ($item_type == 'order_cancel_41_2' || $item_type == 'order_cancel_41_34' ||  $item_type == 'order_cancel_41_1234') {
	$down_type	= "ordercancel";
	$excel_info		= "vnum,rg_date,date,occode,ordercode,brand,sender_name,idnum,can_ord_quantity,paymethod,product,prodcode,colorcode,self_goods_code,price,quantity,option_price,coupon_price,usereserve,deli_price,totprice,ref_tot_price,ref_bankcode,ref_bankaccount,ref_bankuser,ref_rfee,ref_rprice,pg_cancel";
} else if ($item_type == 'cs_order_cancel_41_2') {
	$down_type	= "ordercancel";
	$excel_info		= "vnum,rg_date,date,toca_dt,occode,ordercode,brand,sender_name,idnum,paymethod,product,prodcode,colorcode,self_goods_code,price,quantity,option_price,coupon_price,usereserve,deli_price,totprice,ref_tot_price,ref_bankcode,ref_bankaccount,ref_bankuser,ref_rfee,ref_rprice,pg_cancel";
} else if ($item_type == 'order_cancel_44_2' || $item_type == 'order_cancel_44_34' || $item_type == 'order_cancel_44_342' ||  $item_type == 'order_cancel_44_1234') {
	$down_type	= "orderproduct";
	$excel_info		= "vnum,rg_date,fin_date,date,ordercode,sender_name,idnum,sender_tel,sender_email,brand,product,prodcode,colorcode,self_goods_code,paymethod,price,quantity,coupon_price,usereserve,deli_price,totprice,deli_gbn2,is_mobile";
} else if ($item_type == 'order_cancel_34_40' || $item_type == 'order_cancel_34_41' || $item_type == 'cs_order_cancel_34_41') {
	$down_type	= "ordercancel";
	$excel_info		= "vnum,date,rg_date2,toc_dt,occode,ordercode,brand,sender_name,idnum,paymethod,product,prodcode,colorcode,self_goods_code,price,quantity,option_price,coupon_price,usereserve,deli_price,totprice,,delivery_type,cancel_type";
} else if ($item_type == 'order_cancel_34_change_40' || $item_type == 'order_cancel_34_change_41' || $item_type == 'cs_order_cancel_34_change_41') {
	$down_type	= "ordercancel";
	$excel_info		= "vnum,date,rg_date3,tor_dt,occode,ordercode,brand,sender_name,idnum,product,option,prodcode,colorcode,self_goods_code,change_option,change_prodcode,change_colorcode,change_self_goods_code,price,quantity,option_price,change_option_price,coupon_price,usereserve,deli_price,totprice,change_totprice";
	if ($item_type == 'order_cancel_34_change_41') $excel_info	   .= ",rdesc";
}
$ei_arr	= explode(",", $excel_info);

$save_item=array();
$sql = "SELECT * FROM tblexcelinfo where mem_id='".$_ShopInfo->getId()."' and item_type='".$item_type."' order by regdt desc";
$result=pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$save_item[]=$row;
}
pmysql_free_result($result);

$fields = parse_ini_file("./order_excel_download_conf.ini", true);

$fp = fopen('php://temp', 'w+');

$arritem = array();
foreach ( $fields as $key => $arr ){
	$arrtmp	= array();
	if ( $arr['down'] == 'Y' && in_array($key, $ei_arr) ) {
		$arrtmp['text']		= $arr['text'];
		$arrtmp['val']			= $key;
		$numkey				= array_search($key, $ei_arr);
		$arritem[$numkey]	= $arrtmp;
	}
}
ksort($arritem);
//exdebug($arritem);
//exit;
?>

<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<title>엑셀 다운로드</title>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<link rel="stylesheet" href="style.css" type="text/css">
<script src="../js/jquery.js"></script>
<SCRIPT LANGUAGE="JavaScript">
<!--
document.onkeydown = CheckKeyPress;
document.onkeyup = CheckKeyPress;


$(document).ready(function() {
    $('#btn-up').bind('click', function() {
        $('#est option:selected').each( function() {
            var newPos = $('#est option').index(this) - 1;
            if (newPos > -1) {
                $('#est option').eq(newPos).before("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });

    $('#btn-down').bind('click', function() {
        var countOptions = $('#est option').size();
        $('#est option:selected').each( function() {
            var newPos = $('#est option').index(this) + 1;
            if (newPos < countOptions) {
                $('#est option').eq(newPos).after("<option value='"+$(this).val()+"' selected='selected'>"+$(this).text()+"</option>");
                $(this).remove();
            }
        });
    });
});

function CheckKeyPress() {
	ekey = event.keyCode;

	if(ekey == 38 || ekey == 40 || ekey == 112 || ekey ==17 || ekey == 18 || ekey == 25 || ekey == 122 || ekey == 116) {
		event.keyCode = 0;
		return false;
	}
}

function PageResize() {
	var oWidth = document.all.table_body.clientWidth + 15;
	var oHeight = document.all.table_body.clientHeight + 130;

	window.resizeTo(oWidth,oHeight);
}
function CheckForm(type) {
    if ( $("#est option").size() === 1 && $("#est option").val() === "" ) {
        alert("다운로드 항목을 하나 이상 선택해 주세요.");
        return false;
    }

    $("#est option").prop('selected', true);

	document.form1.mode.value = type;
	document.form1.action = 'order_excel_download_indb_v3.php';
	//document.form1.target = "HiddenFrame";
	document.form1.submit();
}
function itemSave() {
    if ( $("#est option").size() === 1 && $("#est option").val() === "" ) {
        alert("저장할 다운로드 항목을 하나 이상 선택해 주세요.");
        return false;
    }

    if ( $("input[name=save_item_name]").val() == "" ) {
        alert("저장할 항목명을 입력해 주세요.");
		$("input[name=save_item_name]").focus();
        return false;
    }

    $("#est option").prop('selected', true);
	document.form1.mode.value = 'ins';
	document.form1.action = 'ajax.excel_item.php';
	document.form1.target = "HiddenFrame";
	document.form1.submit();
}
function itemDel() {
    if ( $("select[name=sel_save_item] option:selected").val() == "" ) {
        alert("삭제할 저장항목을 선택해 주세요.");
		$("input[name=sel_save_item]").focus();
        return false;
    }
	
	if(!confirm("해당 저장항목을 삭제하시겠습니까?")) return;

    $("#est option").prop('selected', true);
	document.form1.mode.value = 'del';
	document.form1.action = 'ajax.excel_item.php';
	document.form1.target = "HiddenFrame";
	document.form1.submit();
}
function SendMode(mode) {
	if (mode =='insert') {// 선택
		var cnt =0;
		$("select[name='noest'] option:selected").each(function() {
			var value = $(this).val();
			var name = $(this).text();
			//alert(value);
			// 추가
			$("select[name='est[]']").append("<option value='"+value+"'>"+name+"</option>");
			cnt++;
		});
		if (cnt > 0)
		{
			$("select[name='noest'] option:selected").attr('disabled',true).attr('selected',false);
			$("select[name='est[]'] option[value='']").remove();
		} else {
			alert("추가할 항목을 선택해 주세요.");
		}
	} else if (mode =='delete') {
		var cnt =0;
		$("select[name='est[]'] option:selected").each(function() {
			var value = $(this).val();
			$("select[name='noest'] option[value='"+value+"']").attr('disabled',false).attr('selected',false);
			cnt++;
		});
		if (cnt > 0)
		{
			$("select[name='est[]'] option:selected").remove();
			var est_cnt	= $("select[name='est[]'] option").size();
			if (est_cnt == 0) {
				$("select[name='est[]']").append("<option value='' disabled>추가해 주세요.</option>");
			}
		} else {
			alert("삭제할 항목을 선택해 주세요.");
		}
	}
}

function saveSendMode() {
	$("#est option").prop('selected', true);
	SendMode('delete');
	var send_est	= $("select[name=sel_save_item] option:selected").attr('item');
	if(send_est != '') {
		var send_est_arr	= send_est.split(',');
		for (i = 0; i < send_est_arr.length; i++) {
			var value	= send_est_arr[i];
			var name	= $("select[name='noest'] option[value='"+value+"']").text();
			$("select[name='est[]']").append("<option value='"+value+"'>"+name+"</option>");
			$("select[name='noest'] option[value='"+value+"']").attr('disabled', true);
		}
		$("select[name='est[]'] option[value='']").remove();
	}	
}

function move(mode) {
	if (mode == 'up')
	{
	}
}
//-->
</SCRIPT>
</head>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 oncontextmenu="return false" style="overflow-x:hidden;overflow-y:hidden;" ondragstart="return false" onselectstart="return false" oncontextmenu="return false" onLoad="PageResize();">
<div class="pop_top_title"><p>엑셀 다운로드</p></div>
<form name="form1" action="member_excel_new.php" method="post">
<input type=hidden name="mode" value="">
<input type=hidden name="item_type" value="<?=$item_type?>">
<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
<input type=hidden name="redeliverytype" value="<?=$redeliverytype?>">

<TABLE WIDTH="700" BORDER=0 CELLPADDING=0 CELLSPACING=0 style="table-layout:fixed;" id=table_body>
	<tr>
		<TD style="padding-top:10pt; padding-right:10pt; padding-bottom:5pt; padding-left:10pt;">
			<table cellpadding="0" cellspacing="0" width="670" align="center" style="table-layout:fixed">
				<tr>
					<td style="padding-top:5pt;padding-bottom:10pt;">					
					<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
					<colgroup>
					<col width='290'>
					<col width='*'>
					<col width='290'>
					</colgroup>
					<TR>
						<TD><div class="point_title02">다운로드 가능한 리스트 항목</div></TD>
						<TD>&nbsp;</TD>
						<TD><div class="point_title03">다운로드 되는 리스트 항목</div></TD>
					</TR>
					<TR>
						<TD bgcolor="#A3A3A3" align="center" valign="top" style="padding:3pt;">
						<select name="noest" id="noest" size="17" style="width:100%;" class="select" multiple>
	<?php
						foreach ($arritem as $key => $val) {
								echo "<option value=\"".$val['val']."\">".$val['text']."</option>\n";
						}
	?>
						</select></TD>
						<TD width="55" align="center"><a href="javascript:SendMode('insert');"><img src="images/icon_nero1.gif" border="0" vspace="2"></a><br><br><a href="javascript:SendMode('delete');"><img src="images/icon_nero2.gif" border="0" vspace="2"></a></TD>
						<TD  align="center" valign="top" bgcolor="#2286DC">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<TD style="padding-top:3px;padding-left:3px;padding-bottom:3px;">
							<table cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td>
								<select name="est[]" id="est" size="17" style="width:100%;" class="select" multiple>
			<?php
										echo "<option value=\"\" disabled>추가해 주세요.</option>\n";

			?>
								</select>
								</td>
							</tr>
							</table>
							</TD>
							<TD noWrap align=middle width=50 align="center"><a href="javascript:;" id="btn-up"><img src="images/code_up.gif" border="0" vspace="0"></a><br><img src="images/code_sort.gif" border="0" vspace="2"><br><a href="javascript:;" id="btn-down"><img src="images/code_down.gif" border="0" vspace="0"></a></TD>
						</TR>
						</TABLE>
						</TD>
					</TR>
					<TR>
						<TD>
						<div style='background-color:#777;padding:3px;'>
						<table cellpadding="2" cellspacing="0" width="100%">
							<tr>
								<td align=center><span style='color:#fff; font-weight:bold;'>저장항목</span></td>
								<td align=right><select name='sel_save_item' style="width:190px;height:22px;" valign='top' onChange='javascript:saveSendMode()'>
								<option value='' item=''>=======선택해 주세요.=======</option>
								<?
								foreach($save_item as $key => $val) {
									echo "<option value='".$val->eid."' item='".$val->item."'>".$val->item_name."</option>";
								}
								?>
								</select></td>
								<td align=right><a href="javascript:itemDel()"><img src='./images/btn_cate_del.gif' border="0" vspace="0" valign='top'></a></td>
							</tr>
						</table>
						</div></TD>
						<TD>&nbsp;</TD>
						<TD>
						<div style='background-color:#0c71c6;padding:3px;'>
						<table cellpadding="2" cellspacing="0" width="100%">
							<tr>
								<td align=center><span style='color:#fff; font-weight:bold;'>항목저장</span></td>
								<td align=right><input type='text' name='save_item_name' width=50 maxlength=50 valign='top' style="width:192px;height:22px;"></td>
								<td align=right><a href="javascript:itemSave()"><img src='./images/btn_save.gif' border="0" vspace="0" valign='top'></a></td>
							</tr>
						</table>
						</div></TD>
					</TR>
					</TABLE>
					</td>
				</tr>
				<tr>
					<td width="100%" align=center><a href="javascript:CheckForm('<?=$down_type?>')"><img src="images/btn_excel1.gif" border="0"></a></td>
				</tr>
			</table>
				
		</TD>
	</tr>
	<TR>
		<TD height="20" valign=top><hr align="center" size="1" color="#EBEBEB"></TD>
	</TR>
	<TR>
		<TD align=center><a href="javascript:window.close()"><img src="images/btn_close.gif" border="0" vspace="0" border=0 hspace="2"></a></TD>
	</TR>
</TABLE>
</form>

<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name="item_type" value="<?=$item_type?>">
<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
<input type=hidden name="redeliverytype" value="<?=$redeliverytype?>">
</form>

<IFRAME name="HiddenFrame" width=0 height=0 frameborder=0 scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
</body>
</html>
