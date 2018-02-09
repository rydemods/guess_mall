<?php
/********************************************************************* 
// 파 일 명		: cscenter_order_list_cancel.php
// 설     명		: CS관리 취소(결제환불) 리스트
// 상세설명	: CS관리 취소(결제환불) 리스트
// 작 성 자		: 2016.10.11 - 김재수
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
    include_once($Dir."lib/shopdata.php");
	include("access.php");
	include("calendar.php");

####################### 페이지 접근권한 check ###############
	$PageCode	= "cs-1";
	$MenuCode	= "cscenter";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#############################################################

#---------------------------------------------------------------
# 변수를 정리한다.
#---------------------------------------------------------------

	//exdebug($_POST);
	//exdebug($_GET);


	$sql = "SELECT vendercnt FROM tblshopcount ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$vendercnt=$row->vendercnt;
	pmysql_free_result($result);

    $oistep     = '2';  // 단계정보 (2 : 결제완료,배송준비중일때, 34 : 배송중, 배송완료일때의 환불접수(원래 4일경우는 없어야 됨.배송완료와 동시에 구매확정이므로))
	$orderby=$_GET["orderby"];
	if(ord($orderby)==0) $orderby="DESC";
	$prog_type=$_GET["prog_type"];
	if(ord($prog_type)==0) $prog_type="A";

	$CurrentTime = time();
	$period[0] = date("Y-m-d",$CurrentTime);
	$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
	$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
	$period[3] = date("Y-m-d",strtotime('-1 month'));
	$period[4] = date("Y-m-d",strtotime('-1 year'));
//	$period[4] = substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);


	$search_start   = $_GET["search_start"];
	$search_end     = $_GET["search_end"];
	$paymethod      = $_GET["paymethod"];
	$s_check        = $_GET["s_check"];
	$search         = trim($_GET["search"]);
	$ord_flag       = $_GET["ord_flag"]; // 유입경로

    // 결제 상태 전부 체크된 상태로 만들기 위해 기본값으로 넣자..2016-04-19 jhjeong
    //exdebug("cnt = ".count($paymethod));
    if(count($paymethod) == 0) {
		foreach(array_keys($arpm) as $k => $v) {
			$paymethod[$k] = $v;
		}
    }

	if ($ord_flag[0] == '') {
		$ord_flag_def=array("PC","MO","AP");
		foreach($ord_flag_def as $k => $v) $ord_flag[$k] = $v;
	}

	if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
	if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);

	$paymethod_arr  = explode("','",$paymethod);
	$ord_flag_arr  = explode("','",$ord_flag);

	$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
	$brandname      = $_GET["brandname"];  // 벤더이름 검색

	$search_start = $search_start?$search_start:$period[4];
	$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
	$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
	$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

	$tempstart = explode("-",$search_start);
	$tempend = explode("-",$search_end);
	$termday = (strtotime($search_end)-strtotime($search_start))/86400;
	if ($termday>367) {
		alert_go('검색기간은 1년을 초과할 수 없습니다.');
	}

	//$qry = "WHERE toc.pickup_state in ('N','Y') AND toc.restore ='N' AND toc.cfindt ='' ";
	//$qry = "WHERE toc.pickup_state in ('N','Y') AND toc.restore ='N' AND (toc.cfindt ='' OR (LENGTH(toc.bankaccount) < 9 AND toc.pgcancel = 'N' AND a.paymethod IN ('CA'))) ";
    $step_qry = "";
	// 쿠폰 복구 알럿 체크
	$cancel_coupon_alert_yn	= $cancel_restore_yn;
	// 결제완료, 배송준비중에서의 환불접수
	$step_qry = "AND    a.oi_step1 in (1,2,3,4) ";

    /*$qry = "WHERE   1=1 
            ".$step_qry."
            AND     toc.pickup_state in ('N','Y') 
            AND     toc.restore ='N' 
            AND     (toc.cfindt ='' OR (LENGTH(toc.bankaccount) < 9 AND toc.pgcancel = 'N' AND a.paymethod IN ('CA'))) 
            ";*/
    $qry = "WHERE   1=1 
            ".$step_qry."
            AND     toc.pickup_state in ('N','Y') 
            AND     toc.restore ='N'
            AND     toc.proc_type != 'AS' 
            ";

	if ($search_s != "" || $search_e != "") {
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND toc.regdt LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND toc.regdt>='{$search_s}' AND toc.regdt <='{$search_e}' ";
		}
	}

	if(ord($paymethod))	$qry.= " AND SUBSTRING(a.paymethod,1,1) in('".$paymethod."'/*,'B'*/) ";

	// 유입경로 조건
	if(ord($ord_flag)) {
		$chk_mb = array();
		if(count($ord_flag_arr)) {
			foreach($ord_flag_arr as $k => $v) {
				switch($v) {
					case "PC" : $chk_mb[]	= "0"; break;
					case "MO" : $chk_mb[]	= "1"; break;
					case "AP" : $chk_mb[]	= "2"; break;
				}
			}
		}
		if(count($subWhere)) {
			 $qry.= " AND a.is_mobile in ('".implode("','",$chk_mb)."') ";
		}
	}

	if(ord($search)) {
		if($s_check=="cd") $qry.= "AND a.ordercode like '%{$search}%' ";
		else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	}

	$qry_from  = " tblorder_cancel toc ";
	$qry_from .= "join tblorderinfo a on toc.ordercode = a.ordercode ";

    $opstep_qry = "";
	// 결제완료, 배송준비중에서의 환불
	$opstep_qry = "AND    p.op_step in ('41','42','44') and p.redelivery_type = 'N' ";

	$excel_qry_from		= $qry_from;
	$excel_qry				= $qry;

	if($vendercnt>0) {
		if($sel_vender || $com_name) {
			if($com_name) $subqry = " and b.brandname like '%".strtoupper($com_name)."%'";
			else if($sel_vender) $subqry = " and b.vender = ".$sel_vender."";

			$excel_qry	.= $subqry;
		}

		$qry_from .= "  join 
                        (
                            Select  p.oc_no, MIN(p.vender) AS vender, MIN(pb.brandname) AS brandname, MIN(p.op_step) AS op_step, MIN(p.redelivery_type) AS redelivery_type, MIN(p.order_conf) AS order_conf, MIN(pr.prodcode) AS prodcode, MIN(pr.colorcode) AS colorcode  
                            from    tblorderproduct p 
                            left join tblvenderinfo v on p.vender = v.vender 
                            left join tblproductbrand pb on p.vender=pb.vender 
                            left join tblproduct pr on p.productcode=pr.productcode 
                            where   p.oc_no > 0  
                            ".$opstep_qry."
                            group by p.oc_no
                        ) b on toc.oc_no=b.oc_no {$subqry} 
                    ";
        //and     p.op_step in ('41','42','44')

		$excel_qry_from .= ", (Select p.idx as op_idx, p.vender, p.ordercode, p.productcode, p.productname, p.opt1_name, p.opt2_name, p.quantity, p.price, p.option_price,
					            p.deli_com, p.deli_num, p.deli_date, p.deli_price, 
					            p.coupon_price, p.use_point, p.use_epoint, p.op_step, p.opt1_change, p.opt2_change, p.oc_no, p.date, p.idx, p.option_price_text_change, p.option_quantity, p.self_goods_code, pr.prodcode, pr.colorcode 
                        FROM    tblorderproduct p 
                        left join tblvenderinfo v on p.vender = v.vender 
                        left join tblproductbrand pb on p.vender=pb.vender 
                        left join tblproduct pr on p.productcode=pr.productcode 
                        where p.oc_no > 0  
                        and p.redelivery_type != 'G' 
                        ".$opstep_qry."
                        ) b 
                    ";
		$excel_qry.= "AND toc.oc_no=b.oc_no ";
	} else {
		$qry_from .= "  join 
                        (
                            Select  oc_no 
                            from    tblorderproduct p 
                            where   oc_no > 0 
                            ".$opstep_qry."
                            group by oc_no
                        ) b on toc.oc_no=b.oc_no 
                    ";
        //and     op_step in ('41','42','44')
		$excel_qry_from .= ", (Select oc_no from tblorderproduct p where oc_no > 0 and ".$opstep_qry." group by oc_no) b ";
		$excel_qry.= "AND toc.oc_no=b.oc_no ";
	}



	if($vendercnt>0){
		$venderlist=array();
		//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";

		$sql = "SELECT  a.vender,a.id,a.com_name, a.delflag, b.brandname 
				FROM    tblvenderinfo a 
				JOIN    tblproductbrand b on a.vender = b.vender 
				ORDER BY b.brandname
				";
		$result=pmysql_query($sql,get_db_conn());
		while($row=pmysql_fetch_object($result)) {
			$venderlist[$row->vender]=$row;
		}
		pmysql_free_result($result);
	}

	include("header.php"); 

	$sql = "SELECT COUNT(a.*) as t_count ";
	if ($prog_type == 'A') {
		$sql .= " FROM (SELECT * FROM {$qry_from} {$qry}) a ";
	} else if ($prog_type == 'R') {
		$sql .= " FROM (SELECT * FROM {$qry_from} {$qry}) a where a.cfindt ='' ";
		$excel_qry .= " AND toc.cfindt ='' ";
	} else if ($prog_type == 'Y') {
		$sql .= " FROM (SELECT * FROM {$qry_from} {$qry}) a where a.cfindt !='' ";
		$excel_qry .= " AND toc.cfindt !='' ";
	}
	//echo $sql ;
	$paging = new newPaging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$excel_sql = "SELECT  b.vender, b.ordercode, b.productcode, b.productname, b.opt1_name, b.opt2_name, b.quantity, b.price, b.option_price,
					b.deli_com, b.deli_num, b.deli_date, b.deli_price, 
					b.coupon_price, b.use_point, b.use_epoint, b.op_step, b.opt1_change, b.opt2_change, b.oc_no, b.date, b.idx, b.option_price_text_change, b.option_quantity, toc.regdt, toc.code, toc.bankcode, toc.bankaccount, toc.bankuser, toc.rfee, toc.rprice, toc.pgcancel, toc.cfindt as toca_dt,
					a.id, a.sender_name, a.paymethod, a.oi_step1, a.oi_step2, toc.regdt, b.self_goods_code, toc.regdt as reg_dt, b.prodcode, b.colorcode
			FROM {$excel_qry_from} {$excel_qry} ";
	$excel_sql_orderby = "
			ORDER BY  toc.regdt {$orderby} , b.op_idx, b.productcode, b.vender, b.productname 
		";
//exdebug($excel_sql.$excel_sql_orderby);

?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".chk_all").click(function() {
		var chk_cn	= $(this).attr('chk');
		 if($(this).prop("checked")){
			$("."+chk_cn).attr("checked", true);
		 } else {
			$("."+chk_cn).attr("checked", false);
		 }
	});
	$(".detail_area_tr").show();
});

function searchForm() {
	document.form1.action="cscenter_order_list_cancel.php";
    document.form1.method="GET";
    document.form1.prog_type.value="A";
	document.form1.submit();
}

function progress_chage(prog_type) {
	document.form1.action="cscenter_order_list_cancel.php";
    document.form1.method="GET";
    document.form1.prog_type.value=prog_type;
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600,resizable=yes");
	document.detailform.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	
    //if(val < 4) {
	    pForm.search_start.value = period[val];
	    pForm.search_end.value = period[0];
    //}else{
	//    pForm.search_start.value = '';
	//    pForm.search_end.value = '';
    //}
}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chk_oc_no[i].checked=chkval;
   }
}

function CrmView(id) {
	document.crmview.id.value = id;
	window.open("about:blank","crm_view","scrollbars=yes,width=100,height=100,resizable=yes");
    document.crmview.target="crm_view";
	document.crmview.submit();
}

function OrderExcel() {
	document.downexcelform.ordercodes.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function OrderCheckExcel() {
	document.downexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chk_oc_no.length;i++) {
		if(document.form2.chk_oc_no[i].checked) {
			if(document.downexcelform.ordercodes.value!='') document.downexcelform.ordercodes.value +=",";
			document.downexcelform.ordercodes.value+=document.form2.chk_oc_no[i].value;
		}
	}
	if(document.downexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function detail_open_all(chk) {
	$(".detail_open_sel").val(chk);
	if (chk == 'Y') {
		$(".detail_area_tr").show();
	} else if (chk == 'N') {
		$(".detail_area_tr").hide();
	}
}

function detail_open(obj, num, ordercode) {
	var chk	= obj.value;
	if (chk == 'Y') {
		/*$.ajax({
			type: "POST",
			url: "ajax_cs_orderproduct_list.php",
			data: "ordercode="+ordercode,
			dataType:"html",
			success: function(data){
				if (data)
				{
					$("#ord_prod_"+ordercode).html(data);*/
					$(".detail_area_"+num).show();
				/*}
			},
			complete: function(data){
			},
			error:function(xhr, status , error){
				alert("에러발생");
			}
		});*/
	} else if (chk == 'N') {
		$(".detail_area_"+num).hide();
	}
}

// 주문취소
$(document).on("click", ".ord_cancel", function(e) {
	var can_type	= $(this).attr('can_type');
	var oc_no		= $(this).attr('oc_no');

	var popup_url	= "cscenter_order_cancel_detail.php?type="+can_type+"&oc_no="+oc_no;
	var popup_name	= "cscenter_order_cancel_detail_view_"+can_type+"_"+oc_no;

	window.open(popup_url, popup_name, "scrollbars=yes,width=1000,height=700,resizable=yes");

});
function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"_blank");
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : CS 관리  &gt; CS 관리 &gt;<span>취소</span></p></div></div>

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
			<?php include("menu_cscenter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">취소</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>취소현황 및 취소내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">취소현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
            <input type=hidden name=oistep value="<?=$oistep?>">
            <input type=hidden name=prog_type value="<?=$prog_type?>">
			<tr>
				<td>

					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>환불접수일자</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
                        </TR>

                        <TR>
							<th><span>결제타입</span>
							<font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_paymethod' name="paymethod_all" value="<?=$k?>" <?if(count($paymethod_arr) == count($arpm)) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
<?php
							foreach($arpm as $k => $v) {
								$selPaymethod='';
								if(in_array($k,$paymethod_arr)>0)$selPaymethod="checked";
?>
								<input type="checkbox" class='chk_paymethod' name="paymethod[]" value="<?=$k?>" <?=$selPaymethod?>><?=$v?>
<?
							}
?>
							</TD>
						</TR>

						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1"><select name="s_check" class="select">
							<option value="cd" <?php if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?php if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:197" class="input"></TD>
						</tr>

                        <TR>
							<th>
                                <span>유입경로</span>
                                <font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font>
                            </th>
							<TD class="td_con1">
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="PC" <?=(in_array('PC',$ord_flag_arr)?'checked':'')?>>PC</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="MO" <?=(in_array('MO',$ord_flag_arr)?'checked':'')?>>MOBILE</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="AP" <?=(in_array('AP',$ord_flag_arr)?'checked':'')?>>APP</input>
                            </TD>
						</TR>
<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th><span>브랜드검색</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
<?php
                        foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->vender}\"";
                            if($sel_vender==$val->vender) echo " selected";
                            echo ">{$val->brandname}</option>\n";
                        }
?>
                                </select>
                                <input type=text name=com_name value="<?=$com_name?>" style="width:197" class="input"></TD>
                            </td>
                        </TR>
<?
}
?>
						</TABLE>
						</div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="10"></td>
			</tr>
<?php

		list($count_progress_1)=pmysql_fetch_array(pmysql_query("SELECT count(a.*) as cnt1 FROM (SELECT * FROM {$qry_from} {$qry}) a where a.cfindt ='' "));
		list($count_progress_2)=pmysql_fetch_array(pmysql_query("SELECT count(a.*) as cnt1 FROM (SELECT * FROM {$qry_from} {$qry}) a where a.cfindt !='' "));

		if ($prog_type == 'A') {
		} else if ($prog_type == 'R') {
			$qry .= " AND toc.cfindt ='' ";
		} else if ($prog_type == 'Y') {
			$qry .= " AND toc.cfindt !='' ";
		}
		$sql = "SELECT  toc.*, a.id, a.sender_name, a.sender_tel, a.paymethod, a.is_mobile, a.oldordno, a.oi_step1, a.oi_step2, b.op_step, b.redelivery_type, b.order_conf, a.pg_ordercode ";
		if($vendercnt>0) $sql.= ", b.vender ";
        $sql.= "FROM {$qry_from} {$qry} ";
		$sql.= "ORDER BY toc.regdt {$orderby} ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
		//echo "sql = ".$sql."<br>";
        //exdebug($sql);
?>
			<tr>
				<td style="padding-bottom:3pt;">
				<!-- [D] 20161006 박스추가 -->
				<div class="section-box">
					<ul class="aslist">
						<li><a href="javascript:progress_chage('R');">접수 : <strong><?=number_format($count_progress_1)?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('Y');">완료 : <strong><?=number_format($count_progress_2)?></strong> 건</a></li>
					</ul>
				</div>
				<!-- // [D] 20161006 박스추가 -->
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
            <input type=hidden name=oistep value="<?=$oistep?>">
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right">
					<img src="images/icon_8a.gif" border="0">전체 상세 
					<select name='sel_detail_open_all' onChange="javascript:detail_open_all(this.value);">
					<option value="Y">열기</option>
					<option value="N">닫기</option>
					</select>&nbsp;&nbsp;
					<img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40></col>
				<col width=60></col>
				<col width=100></col>
				<col width=60></col>
				<col width=150></col>
				<col width=150></col>
				<col width=200></col>
				<col width=></col>
                <col width=80></col>
                <col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=100></col>
				<input type=hidden name=chk_oc_no>
			
				<TR >
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>접수번호</th>
					<th>취소요청일</th>
					<th>유입경로</th>
					<th>주문자</th>
					<th>핸드폰</th>
					<th>주문번호</th>
					<th>상품</th>
					<th>상세</th>
					<th>결제수단</th>
					<th>처리상태</th>
					<th>처리일시</th>
					<th>처리요청서</th>
				</TR>

<?php
		$colspan=13;
		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			
			$regdate = substr($row->regdt,0,4)."-".substr($row->regdt,4,2)."-".substr($row->regdt,6,2)." ".substr($row->regdt,8,2).":".substr($row->regdt,10,2).":".substr($row->regdt,12,2);
			$findate="";
			if ($row->cfindt) $findate = substr($row->cfindt,0,4)."-".substr($row->cfindt,4,2)."-".substr($row->cfindt,6,2)." ".substr($row->cfindt,8,2).":".substr($row->cfindt,10,2).":".substr($row->cfindt,12,2);
			$name=$row->sender_name;
			$mobile=$row->sender_tel;
			$stridX='';
			$stridM='';
			if(substr($row->ordercode,20)=="X") {	//비회원
				//$stridM = $row->sender_name."&nbsp;(<FONT COLOR=\"blue\" style='font-size:12px;'>비회원</FONT>) / 주문번호: ".substr($row->id,1,6);
				$stridM = "<FONT COLOR=\"blue\">{$row->sender_name}</FONT>&nbsp;<FONT style='font-size:12px;'>(비회원)</FONT>";
			} else {	//회원
				$stridM = "<a href=\"javascript:CrmView('$row->id');\"><FONT COLOR=\"blue\">{$row->sender_name}</FONT><br><FONT style='font-size:12px;'>({$row->id})</FONT></a>";
			}

			list($oc_count)=pmysql_fetch("SELECT count(*) as oc_count from tblorderproduct WHERE oc_no='{$row->oc_no}' ");
			list($tot_count)=pmysql_fetch("SELECT count(*) as tot_count from tblorderproduct WHERE ordercode = '{$row->ordercode}' ");

			// 결제완료, 배송준비중에서의 환불접수
			$refund_type = "cancel";

			$thiscolor="#FFFFFF";
			$thiscolor2="#FFFFFF";

			$productname = "상품 ".$oc_count."종";

			$oc_reg_type="-";
			if ($row->reg_type =='admin') {
				$oc_reg_type="CS";
			} else if ($row->reg_type =='user') {
				$oc_reg_type="고객";
			} else if ($row->reg_type =='api') {
				$oc_reg_type="API";
			}
?>
			    <tr bgcolor=<?=$thiscolor?>>
			        <td align="center">
                        <input type=checkbox name=chk_oc_no value="<?=$row->oc_no?>"><br>
                    </td>
                    <td align="center"><?=$row->oc_no?><input type=hidden name="ordercode" value="<?=$row->ordercode?>"><input type=hidden name="refund_type" value="<?=$refund_type?>"></td>
                    <td align="center"><?=$regdate?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=$arr_mobile2[$row->is_mobile]?></td>
			        <td align="center"><?=$stridM?></td>
                    <td align="center"><?=$mobile?></td>
                    <td style='text-align:center'><A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$row->ordercode?><br><FONT class=font_orange><?=$row->pg_ordercode?></font></A></td>
                    <td style='text-align:left'><a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')"><?=$productname?>&nbsp;<img src="images/newwindow.gif" border=0 align=absmiddle></a></td>
                    <td align="center">
					<select name='detail_open_<?=$number?>' class='detail_open_sel' onChange="detail_open(this, '<?=$number?>', '<?=$row->oc_no?>');">
					<option value="Y">열기</option>
					<option value="N">닫기</option>
					</select>
					</td>
			        <td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?>
			        <?if($row->paymethod[0] == "O" & $row->imagination_cancel == "Y"){?>
			        <br>(환불완료)
			        <?} ?>
			        </td>
                    <td align=center style="font-size:8pt;padding:3"><font color='blue'><?=str_replace("환불", "취소", GetStatusOrder("p", $row->oi_step1, $row->oi_step2, $row->op_step, $row->redelivery_type, $row->order_conf))?></font><br>(<?=$oc_reg_type?>)<?=$ord_status?"<br>".$ord_status:""?></td>
                    <td align=center style="font-size:8pt;padding:3"><?=$findate?$findate:'-'?></td>
                    <td align=center style="padding:3">
<?
					if ($row->oi_step1 < 3) {
						if ($row->oi_step1 == 0) {
							$add_can_type	= "cancel";
						} else {
							$add_can_type	= "refund";
						}
						if($oc_count == $tot_count) {		// 전체취소시
							$pc_type		="ALL";
						} else {									// 부분취소시
							$pc_type		="PART";
						}
?>
					<input type='button' value='취소처리' class='btn_blue ord_cancel' style='padding:2px 5px 1px' oc_no = "<?=$row->oc_no?>" can_type="<?=$add_can_type?>">
<?
					} else {
						echo "-";
					}
?>
					</td>
				</tr>
				<tr bgcolor=<?=$thiscolor2?> class='detail_area_tr detail_area_<?=$number?>' style='display:none;'><td colspan=<?=$colspan?> align=center height=40 style='padding:0 0;'>
				<div id='ord_prod_<?=$row->oc_no?>'>				
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=100></col>
				<col width=200></col>
				<col width=80></col>
				<col width=></col>
				<col width=150></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=120></col>			
				<tr bgcolor="#EFEFEF">
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>접수번호</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>주문번호</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>상품</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>옵션</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>판매가</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>O2O정보</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>수량</td>
					<td style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>총금액</td>
					<td style='border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>매장정보</td>
				</tr>
			<?
				#주문상품
				$prod_sql = "SELECT 
								a.productcode, a.productname, a.price, a.reserve, a.opt1_name, a.opt2_name, a.text_opt_subject, a.text_opt_content, a.option_price_text, 
								a.tempkey, a.addcode, a.quantity, a.order_prmsg, a.selfcode,
								a.package_idx, a.assemble_idx, a.assemble_info, b.tinyimage, 
								b.minimage, a.option_type, a.option_price, a.option_quantity, 
								a.coupon_price, a.deli_price, a.deli_gbn, a.deli_com, a.deli_num, 
								a.deli_date, a.receive_ok, a.order_conf, a.redelivery_type, a.redelivery_date, a.redelivery_reason,
								a.idx, a.vender, a.op_step, a.vender, b.option1, b.option2, b.sellprice, b.consumerprice,  b.brand, pb.brandname, a.use_point, a.use_epoint, b.option1_tf, option2_tf, option2_maxlen, 
								a.delivery_type, a.store_code, a.reservation_date, a.oc_no, b.prodcode, b.colorcode 
							FROM 
								tblorderproduct a LEFT JOIN tblproduct b on a.productcode=b.productcode left join tblproductbrand pb on b.brand=pb.bridx 
							WHERE 
								a.ordercode='".$row->ordercode."' 
								AND  a.oc_no='{$row->oc_no}'
							ORDER BY a.vender, a.idx ";

				$prod_result	= pmysql_query($prod_sql,get_db_conn());
				$pr_idxs		= "";
				$t_op_price						=  0;
				$t_op_dc_coupon_price	=  0;
				$t_op_dc_use_point			=  0;
				$t_op_dc_use_epoint			=  0;
				$t_op_dc_price				=  0;
				$t_op_deli_price				=  0;
				$t_op_total_price				=  0;
				$t_op_total_quantity			=  0;

				while($prod_row=pmysql_fetch_object($prod_result)) {
					if ($pr_idxs == '') {
						$pr_idxs		.= $prod_row->idx;
					} else {
						$pr_idxs		.= "|".$prod_row->idx;
					}

					//배송비로 인한 보여지는 가격 재조정
					$can_deli_price	= 0;
					$can_total_price	= (($prod_row->price + $prod_row->option_price) * $prod_row->option_quantity) - ($prod_row->coupon_price + $prod_row->use_point + $prod_row->use_epoint) + $prod_row->deli_price;

					list($od_deli_price, $product)=pmysql_fetch_array(pmysql_query("select deli_price, product from tblorder_delivery WHERE ordercode='".trim($ordercode)."' and product LIKE '%".$prod_row->productcode."%'"));
					//echo $od_deli_price;
					if ($od_deli_price) { //배송료 상세정보에 배송료가 있으면
						// 주문건 묶여있는 상품들중에 현재 주문상품을 제외한것중 1개를 가져온다.
						list($op_idx)=pmysql_fetch_array(pmysql_query("SELECT idx FROM tblorderproduct where ordercode='".trim($ordercode)."' and productcode in ('".str_replace(",","','", $product)."') and idx != '".$prod_row->idx."' and op_step < 40 limit 1"));
						if ($op_idx) { // 상품이 있으면
							if ($prod_row->deli_price > 0) $can_total_price	= $can_total_price - $od_deli_price;
						} else {
							$can_deli_price	= $od_deli_price;
						}
					}

					$t_op_price			+=  ($prod_row->price + $prod_row->option_price) * $prod_row->option_quantity;
					$t_op_dc_coupon_price	+=  $prod_row->coupon_price;
					$t_op_dc_use_point			+=  $prod_row->use_point;
					$t_op_dc_use_epoint			+=  $prod_row->use_epoint;
					$t_op_dc_price	+=  $prod_row->coupon_price + $prod_row->use_point + $prod_row->use_epoint;
					if ($pc_type == 'ALL') {
						$t_op_deli_price	+=  $prod_row->deli_price;
						$t_op_total_price	+=  (($prod_row->price + $prod_row->option_price) * $prod_row->option_quantity) - ($prod_row->coupon_price + $prod_row->use_point + $prod_row->use_epoint) + $prod_row->deli_price;
					} else if ($pc_type == 'PART') {
						$t_op_deli_price	+=  $can_deli_price;
						$t_op_total_price	+=  $can_total_price;
					}
					$t_op_total_quantity	+=  $prod_row->option_quantity;

					$file = getProductImage($Dir.DataDir.'shopimages/product/', $prod_row->tinyimage);

					$optStr	= "";
					$option1	 = $prod_row->opt1_name;
					$option2	 = $prod_row->opt2_name;

					if( strlen( trim( $prod_row->opt1_name ) ) > 0 ) {
						$opt1_name_arr	= explode("@#", $prod_row->opt1_name);
						$opt2_name_arr	= explode(chr(30), $prod_row->opt2_name);
						for($g=0;$g < sizeof($opt1_name_arr);$g++) {
							if ($g > 0) $optStr	.= " / ";
							$optStr	.= $opt1_name_arr[$g].' : '.$opt2_name_arr[$g];
						}
					}

					if( strlen( trim( $prod_row->text_opt_subject ) ) > 0 ) {
						$text_opt_subject_arr	= explode("@#", $prod_row->text_opt_subject);
						$text_opt_content_arr	= explode("@#", $prod_row->text_opt_content);

						for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
							if ($text_opt_content_arr[$s]) {
								if ($optStr != '') $optStr	.= " / ";
								$optStr	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
							}
						}
					}

					$erp_pc_code	= "&nbsp;&nbsp;[".$prod_row->prodcode."-".$prod_row->colorcode."]";
					
					$storeData = getStoreData($prod_row->store_code);
			?>
				<tr>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$prod_row->oc_no?$prod_row->oc_no:'-'?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=$row->ordercode?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><a href="javascript:ProductDetail('<?=$prod_row->productcode?>')"><img src="<?=$file?>" style="width:70px" border="1" alt="<?=$prod_row->productname?>"></a></td>
					<td style='padding:5px;border-bottom:1px solid #cbcbcb;text-align:left'><a href="javascript:ProductDetail('<?=$prod_row->productcode?>')"><strong>[<?=$prod_row->brandname?>]</strong><br><?=$prod_row->productname?><?=$erp_pc_code?></a></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:left'><?=$optStr?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:right'><?=number_format($prod_row->price)?>원</td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>
					<?
						echo '<strong>'.$arrChainCode[$prod_row->delivery_type].'</strong>';
						if( $prod_row->reservation_date ){
							echo '<br>'.substr($prod_row->reservation_date, 0, 4).".".substr($prod_row->reservation_date, 5, 2).".".substr($prod_row->reservation_date, 8, 2);
						}
						if($prod_row->store_code){
							echo '<br>'.$storeData["name"];
							echo '<br>'.$prod_row->store_code;
						}
					?>
					</td><!-- O2O 배송 -->
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($prod_row->option_quantity)?></td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;text-align:right'><?=number_format($prod_row->price * $prod_row->option_quantity)?>원</td>
					<td style='padding:5px;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=($storelist[$row->store_code]->name !='')?$storelist[$row->store_code]->name:'-'?></td>
				</tr>
			<?
				}
			?>
				</table>
				<input type=hidden name=pr_idxs value="<?=$pr_idxs?>">
				</div>
				<div style="font=align:center;padding:10px; line-height:140%;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;">
                <strong><?=number_format($t_op_price	)?>원 + 배송비 <?=number_format($t_op_deli_price)?>원 = <?=number_format($t_op_price + $t_op_deli_price)?>원</strong>
                <br>
                쿠폰 <?=number_format($t_op_dc_coupon_price)?>원  / 포인트 <?=number_format($t_op_dc_use_point)?>원 / E포인트 <?=number_format($t_op_dc_use_epoint)?>원 
				<br>
                <br>
				<strong style="color:#FF0000">최종결제금액 <?=number_format(($t_op_price-$t_op_dc_price)+$t_op_deli_price)?>원 </strong>
				</div>
				</td></tr>
<?


			$cnt++;
		}
		pmysql_free_result($result);
		if($cnt==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td style="padding-bottom:20px">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=130></col>
				<col width=></col>
				<col width=130></col>
				<tr>
					<td align='left' valign=middle><a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>
				</tr>
				<tr>

					<td align='center'>
                    <div id="page_navi01" style='margin:0 0'>
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
					</td>
				
				<tr>
				</table>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">

			</form>

			<!-- <form name=detailform method="post" action="order_detail_v2.php" target="orderdetail"> -->
            <form name=detailform method="post" action="order_detail.php" target="orderdetail">
			<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=type>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
            <input type=hidden name=ord_flag value="<?=$ord_flag?>">
            <input type=hidden name=oistep value="<?=$oistep?>">
            <input type=hidden name=prog_type value="<?=$prog_type?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="cs_order_cancel_41_<?=$oistep?>">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="ordercodes">
			</form>

			<!-- <form name=mailform action="member_mailsend.php" method=post>
			<input type=hidden name=rmail>
			</form> -->

			<!-- <form name=form_reg action="product_register.php" method=post>
			<input type=hidden name=code>
			<input type=hidden name=prcode>
			<input type=hidden name=popup>
			</form> -->

			<!-- <form name=smsform action="sendsms.php" method=post target="sendsmspop">
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			</form> -->

            <form name=stepform action="order_state_indb.php" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=idx>
			<input type=hidden name=ordercodes>
			</form>
            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

            <form name=crmview method="post" action="crm_view.php">
			<input type=hidden name=id>
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
							<dt>-</dt>
							<dd>
								-
							</dd>
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
<?=$onload?>
<?php 
include("copyright.php");
?>