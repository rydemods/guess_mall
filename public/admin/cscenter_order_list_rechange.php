<?php
/*********************************************************************
// 파 일 명		: member_join.php
// 설     명		: 회원가입 정보등록
// 상세설명	: 회원가입시 정보를 등록
// 작 성 자		: 2016.01.07 - 김재수
// 수 정 자		: 2016.07.28 - 김재수
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
	include("calendar.php");
	include_once($Dir."conf/cscenter_ascode.php");

####################### 페이지 접근권한 check ###############
	$PageCode	= "cs-1";
	$MenuCode	= "cscenter";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#############################################################

	$opstep=array("40","41","42","44");
	$redeliverytype=$_GET["redeliverytype"];

	if($redeliverytype=="G"){
		$redeliveryname="교환";
		$redeliverypop="rechange";
	}else if($redeliverytype=="Y"){
		$redeliveryname="반품";
		$redeliverypop="regoods";
	}

	$sql = "SELECT vendercnt FROM tblshopcount ";
	$result=pmysql_query($sql,get_db_conn());
	$row=pmysql_fetch_object($result);
	$vendercnt=$row->vendercnt;
	pmysql_free_result($result);


	$orderby=$_GET["orderby"];
	if(ord($orderby)==0) $orderby="DESC";

	$CurrentTime = time();
	$period[0] = date("Y-m-d",$CurrentTime);
	$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
	$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
	$period[3] = date("Y-m-d",strtotime('-1 month'));
	$period[4] = date("Y-m-d",strtotime('-1 year'));
	//$period[4] = substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);


	$search_start = $_GET["search_start"];
	$search_end = $_GET["search_end"];
	$sel_code = $_GET["sel_code"];
	$s_check = $_GET["s_check"];
	$search = trim($_GET["search"]);
	$sel_vender = $_GET["sel_vender"];
	$com_name = $_GET["com_name"];  // 벤더이름 검색
	$oc_step= $_GET["oc_step"];
	$paymethod= $_GET["paymethod"];
	$ord_flag= $_GET["ord_flag"];
	$reg_type= $_GET["reg_type"];

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

	if ($reg_type[0] == '') {
		$reg_type_def=array("admin","user","api","pg");
		foreach($reg_type_def as $k => $v) $reg_type[$k] = $v;
	}

	if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
	if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);
	if(is_array($reg_type)) $reg_type = implode("','",$reg_type);

	$paymethod_arr  = explode("','",$paymethod);
	$ord_flag_arr  = explode("','",$ord_flag);
	$reg_type_arr  = explode("','",$reg_type);

	

	$qry = "WHERE toc.pickup_state in ('R','Y') AND toc.restore ='N' AND toc.proc_type != 'AS' ";
	if ($search_s != "" || $search_e != "") {
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND toc.regdt LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND toc.regdt>='{$search_s}' AND toc.regdt <='{$search_e}' ";
		}
	}

	if(ord($sel_code)) $qry.= "AND toc.code='{$sel_code}' ";

	if(ord($search)) {
		if($s_check=="cd") $qry.= "AND a.ordercode like '%{$search}%' ";
		else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	}

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
		
		$qry.= " AND a.is_mobile in ('".implode("','",$chk_mb)."') ";
		
	}

	// 접수구분
	if(ord($reg_type)) {
		$chk_rt = array();
		if(count($reg_type_arr)) {
			foreach($reg_type_arr as $k => $v) {
				switch($v) {
					case "admin" : $chk_rt[]	= "admin"; break;
					case "user" : $chk_rt[]	= "user"; break;
					case "api" : $chk_rt[]	= "api"; break;
					case "pg" : $chk_rt[]	= "pg"; break;
				}
			}
		}
		
		$qry.= " AND toc.reg_type in ('".implode("','",$chk_rt)."') ";
		
	}

	if($oc_step!=''){
		 $qrystep= "AND toc.oc_step='{$oc_step}' ";
	}

	if(ord($paymethod))	$qry.= " AND SUBSTRING(a.paymethod,1,1) in('".$paymethod."'/*,'B'*/) ";

	$qry_from  = " tblorder_cancel toc ";
	$qry_from .= "join tblorderinfo a on toc.ordercode = a.ordercode ";
	$qry_from .= "left join tblorderproduct op on (toc.oc_no=op.oc_no) ";
	$qry_from .= "left join tblproduct tp on (op.productcode=tp.productcode) ";
	$qry_from .= "left join tblproductbrand  pb on (tp.brand=pb.bridx) ";
	$qry_from .= "left join tblstore s on (op.store_code=s.store_code)";


	$excel_qry_from		= $qry_from;
	$excel_qry				= $qry;

	if($vendercnt>0) {
		if($sel_vender || $com_name) {
			if($com_name) $subqry = " and b.brandname like '%".strtoupper($com_name)."%'";
			else if($sel_vender) $subqry = " and b.vender = ".$sel_vender."";
		}

		$qry_from .= " join (Select p.oc_no, p.vender, pb.brandname from tblorderproduct p left join tblvenderinfo v on p.vender = v.vender left join tblproductbrand pb on p.vender=pb.vender where p.oc_no > 0 and p.redelivery_type = '".$redeliverytype."' and p.op_step in ('".implode("','",$opstep)."') group by p.oc_no, p.vender, pb.brandname) b on toc.oc_no=b.oc_no {$subqry} ";

		$excel_qry_from .= ", (Select p.vender, p.ordercode, p.productcode, p.productname, p.opt1_name, p.opt2_name, p.quantity, p.price, p.option_price,
					p.deli_com, p.deli_num, p.deli_date, p.deli_price, 
					p.coupon_price, p.use_point, p.use_epoint, p.op_step, p.opt1_change, p.opt2_change, p.oc_no, p.date, p.idx, p.option_price_text_change, p.option_quantity, p.self_goods_code, p.self_goods_code_change 
					FROM tblorderproduct p 
					left join tblvenderinfo v on p.vender = v.vender 
					left join tblproductbrand pb on p.vender=pb.vender 
					where p.oc_no > 0 
					and p.redelivery_type = '".$redeliverytype."' 
					and p.op_step in ('".implode("','",$opstep)."')
					) b 
                    ";
		$excel_qry.= "AND toc.oc_no=b.oc_no ";
	} else {
		$qry_from .= "join (Select oc_no from tblorderproduct p where oc_no > 0 and redelivery_type = '".$redeliverytype."' and op_step in ('".implode("','",$opstep)."') group by oc_no) b on toc.oc_no=b.oc_no ";

		$excel_qry_from .= ", (Select oc_no from tblorderproduct p where oc_no > 0 and p.redelivery_type = '".$redeliverytype."' and op_step in ('".implode("','",$opstep)."') group by oc_no) b ";
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

	$sql = "SELECT COUNT(*) as t_count FROM {$qry_from} {$qry} {$qrystep} ";
	//echo $sql ;
	$paging = new newPaging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$excel_sql = "SELECT  toc.*,b.vender, b.ordercode, b.productcode, b.productname, b.opt1_name, b.opt2_name, b.quantity, b.price, b.option_price,
					b.deli_com, b.deli_num, b.deli_date, b.deli_price, op.redelivery_type,
					b.coupon_price, b.use_point, b.use_epoint, b.op_step, b.opt1_change, b.opt2_change, b.oc_no, b.date, b.idx, b.option_price_text_change, b.option_quantity, toc.regdt, toc.code, toc.rdesc,
					a.id, a.sender_name, a.sender_tel, a.sender_email, a.paymethod, a.oi_step1, a.oi_step2, a.is_mobile, a.receiver_name, b.self_goods_code, b.self_goods_code_change, tp.prodcode, tp.colorcode,op.delivery_type,op.store_code, toc.cfindt as tor_dt, toc.cfindt as toc_dt
			FROM {$excel_qry_from} {$excel_qry} {$qrystep} ";
	$excel_sql_orderby = "
			ORDER BY  toc.oc_no {$orderby} 
		";
//exdebug($excel_sql);
	$sql = "SELECT  toc.*, a.id, a.sender_name, a.paymethod, a.sender_tel,  a.is_mobile, a.oi_step2, a.oi_step1, s.name as store_name, a.pg_ordercode ";
	if($vendercnt>0) $sql.= ", b.vender ";
	$sql.= ", op.op_step, op.redelivery_type, op.order_conf, op.option_quantity, op.text_opt_subject, op.opt1_name, op.opt2_name, tp.productname, tp.tinyimage, tp.productcode, pb.brandname, tp.prodcode, tp.colorcode,op.delivery_type,op.store_code ";
	$sql.= "FROM {$qry_from} {$qry} {$qrystep} ";
	$sql.= "ORDER BY toc.oc_no {$orderby} ";
	$sql = $paging->getSql($sql);
	$result=pmysql_query($sql,get_db_conn());

	#신청 건수 구하기
	list($count_progress_1)=pmysql_fetch("select count(toc.oc_no) from {$qry_from} {$qry} and toc.oc_step='0'");
	#접수 건수 구하기
	list($count_progress_2)=pmysql_fetch("select count(toc.oc_no) from {$qry_from} {$qry} and toc.oc_step='1'");
	#승인 건수 구하기
	list($count_progress_3)=pmysql_fetch("select count(toc.oc_no) from {$qry_from} {$qry} and toc.oc_step='3'");
	#보류 건수 구하기
	list($count_progress_4)=pmysql_fetch("select count(toc.oc_no) from {$qry_from} {$qry} and toc.oc_step='5'");
	#완료 건수 구하기
	list($count_progress_5)=pmysql_fetch("select count(toc.oc_no) from {$qry_from} {$qry} and toc.oc_step='4'");
	#제품도착 건수 구하기
	list($count_progress_6)=pmysql_fetch("select count(toc.oc_no) from {$qry_from} {$qry} and toc.oc_step='2'");

	$colspan=17;

	$chk_mb["0"]	= "PC";
	$chk_mb["1"]	= "MO";
	$chk_mb["2"]	= "AP";
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

});

function searchForm() {
	document.form1.action="cscenter_order_list_rechange.php";
   
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


	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];

}

function GoPage(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}


function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

function CrmView(id) {
	document.crmview.id.value = id;
	window.open("about:blank","crm_view","scrollbars=yes,width=100,height=100,resizable=yes");
    document.crmview.target="crm_view";
	document.crmview.submit();
}

function rechange_pop(oc_no, type) {
	window.open("cscenter_order_cancel_detail.php?type="+type+"&oc_no="+oc_no, "rechange_pop","scrollbars=yes,width=1000,height=700,resizable=yes");
   
}

function OrderExcel() {
	document.downexcelform.ordercodes.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function OrderCheckExcel() {
	document.downexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.downexcelform.ordercodes.value!='') document.downexcelform.ordercodes.value +=",";
			document.downexcelform.ordercodes.value+=document.form2.chkordercode[i].value;
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

function asdel(idx){
	if(confirm("삭제하시겠습니까?")){
		document.delform.receipt_no.value=idx;
		document.delform.submit();	
	}
}

function progress_chage(p_no){
	document.form1.action="cscenter_order_list_rechange.php";
	document.form1.oc_step.value=p_no;
    document.form1.method="GET";
	document.form1.submit();
	
}
function view_as(no)
{
	window.open('cscenter_online_as_pop.php?no='+no,'_blank','width=1000,height=1000,scrollbars=yes');
}
function ProductDetail(prcode) {
	window.open("/front/productdetail.php?productcode="+prcode,"_blank");
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : CS관리  &gt; CS관리 &gt;<span><?=$redeliveryname?></span></p></div></div>

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
					<div class="title_depth3"><?=$redeliveryname?></div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span><?=$redeliveryname?>현황 및 <?=$redeliveryname?>내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><?=$redeliveryname?>현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=oc_step id=oc_step>
			<input type=hidden name=redeliverytype id=redeliverytype value="<?=$redeliverytype?>">

			
			<tr>
				<td>

					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<TR>
							<th><span>기간선택</span></th>
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
						<TR>
							<th>
                                <span>접수구분</span>
                                <font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_reg_type' name="reg_type_all" <?if(count($reg_type_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font>
                            </th>
							<TD class="td_con1">
                                <input type="checkbox" class='admin' name="reg_type[]" value="admin" <?=(in_array('admin',$reg_type_arr)?'checked':'')?>>CS<?=$redeliveryname?></input>
                                <input type="checkbox" class='user' name="reg_type[]" value="user" <?=(in_array('user',$reg_type_arr)?'checked':'')?>>고객<?=$redeliveryname?></input>
                                <input type="checkbox" class='api' name="reg_type[]" value="api" <?=(in_array('api',$reg_type_arr)?'checked':'')?>>API<?=$redeliveryname?></input>
								<input type="checkbox" class='pg' name="reg_type[]" value="pg" <?=(in_array('pg',$reg_type_arr)?'checked':'')?>>PG<?=$redeliveryname?></input>
                            </TD>
						</TR>

						<TR>
                            <th><span>교환사유</span></th>
                            <td><select name=sel_code class="select">
                                <option value="">======== 전체 ========</option>
<?php
                        foreach($oc_code as $key => $val) {
                            echo "<option value=\"{$key}\"";
                            if($sel_code==$key) echo " selected";
                            echo ">{$val}</option>\n";
                        }
?>
                                </select>
                            </td>
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
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
		
			<tr>
				<td style="padding-bottom:3pt;">

				<!-- [D] 20161006 박스추가 -->
				<div class="section-box">
					<ul class="aslist">
						<li><a href="javascript:progress_chage('0');">신청 : <strong><?=$count_progress_1?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('1');">접수 : <strong><?=$count_progress_2?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('2');">제품도착 : <strong><?=$count_progress_6?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('3');">승인 : <strong><?=$count_progress_3?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('5');">보류 : <strong><?=$count_progress_4?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('4');">완료 : <strong><?=$count_progress_5?></strong> 건</a></li>
					</ul>
				</div>
				<!-- // [D] 20161006 박스추가 -->
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
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
				<col width=100></col>
				<col width=100></col>
				<col width=150></col>
				<?if($redeliverytype=="G"){?>
				<col width=150></col>
				<?}?>
				<col width=70></col>
				<col width=></col>
				<col width=60></col>
				<col width=60></col>
				<?if($redeliverytype=="G"){?>
				<col width=100></col>
				<col width=100></col>
				<?}else if($redeliverytype=="Y"){?>
				<col width=100></col>
				<?}?>				
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<!--<col width=60></col>-->
				<input type=hidden name=chkordercode>

				<TR >
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<th>접수번호</th>
					<th><?=$redeliveryname?>요청일</th>
					<th>주문채널</th>
					<th>주문자</th>
					<th>핸드폰</th>
					<th>주문번호</th>
					<?if($redeliverytype=="G"){?>
					<th>교환주문번호</th>
					<?}?>
					<th colspan=2>주문상품</th>
					<th>옵션</th>
					<th>수량</th>
					<?if($redeliverytype=="G"){?>
					<th>출고매장</th>
					<th>회송매장</th>
					<?}else if($redeliverytype=="Y"){?>
					<th>매장정보</th>
					<?}?>	
					<th>O2O정보</th>	                   
					<th>결제수단</th>
					<th>처리상태</th>
					<th>처리일시</th>
					<th>처리요청서</th>
					<!--<th>삭제</th>-->
				</TR>

<?php

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {
			$optStr	= "";
			$option1	 = $row->opt1_name;
			$option2	 = $row->opt2_name;

			if( strlen( trim( $row->opt1_name ) ) > 0 ) {
				$opt1_name_arr	= explode("@#", $row->opt1_name);
				$opt2_name_arr	= explode(chr(30), $row->opt2_name);
				for($g=0;$g < sizeof($opt1_name_arr);$g++) {
					if ($g > 0) $optStr	.= " / ";
					$optStr	.= $opt1_name_arr[$g].' : '.$opt2_name_arr[$g];
				}
			}
			$findate="";
			if ($row->cfindt) $findate = substr($row->cfindt,0,4)."-".substr($row->cfindt,4,2)."-".substr($row->cfindt,6,2)." ".substr($row->cfindt,8,2).":".substr($row->cfindt,10,2).":".substr($row->cfindt,12,2);

			if( strlen( trim( $row->text_opt_subject ) ) > 0 ) {
				$text_opt_subject_arr	= explode("@#", $row->text_opt_subject);
				$text_opt_content_arr	= explode("@#", $row->text_opt_content);

				for($s=0;$s < sizeof($text_opt_subject_arr);$s++) {
					if ($text_opt_content_arr[$s]) {
						if ($optStr != '') $optStr	.= " / ";
						$optStr	.= $text_opt_subject_arr[$s].' : '.$text_opt_content_arr[$s];
					}
				}
			}

			$oc_reg_type="-";
			if ($row->reg_type =='admin') {
				$oc_reg_type="CS".$redeliveryname;
			} else if ($row->reg_type =='user') {
				$oc_reg_type="고객".$redeliveryname;
			} else if ($row->reg_type =='api') {
				$oc_reg_type="API".$redeliveryname;
			} else if ($row->reg_type =='pg') {
				$oc_reg_type="PG".$redeliveryname;
			}

			$regdate = substr($row->regdt,0,4)."-".substr($row->regdt,4,2)."-".substr($row->regdt,6,2)." ".substr($row->regdt,8,2).":".substr($row->regdt,10,2).":".substr($row->regdt,12,2);

			if(substr($row->ordercode,20)=="X") {	//비회원				
				//$stridM = $row->sender_name."&nbsp;(<FONT COLOR=\"blue\" style='font-size:12px;'>비회원</FONT>) / 주문번호: ".substr($row->id,1,6);
				$stridM = "<FONT COLOR=\"blue\">{$row->sender_name}</FONT>&nbsp;<FONT style='font-size:12px;'>(비회원)</FONT>";
			} else {	//회원
				$stridM = "<a href=\"javascript:CrmView('$row->id');\"><FONT COLOR=\"blue\">{$row->sender_name}</FONT><br><FONT style='font-size:12px;'>({$row->id})</FONT></a>";
			}

			$product_img = getProductImage($Dir.DataDir.'shopimages/product/', $row->tinyimage);

			$erp_pc_code	= "&nbsp;&nbsp;[".$row->prodcode."-".$row->colorcode."]";

?>
			    <tr bgcolor=<?=$thiscolor?>>
			        <td align="center"><input type=checkbox name=chkordercode value="<?=$row->ordercode?>"></td>
                    <td align="center"><?=$row->oc_no?$row->oc_no:'-'?></td>
                    <td align="center"><?=$regdate?></td>
                    <td align="center"><?=$chk_mb[$row->is_mobile]?></td>
                    <td align="center"><?=$stridM?></td>
			        <td align="center"><?=$row->sender_tel?></td>
                    <td align="center"><A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$row->ordercode?><br><FONT class=font_orange><?=$row->pg_ordercode?></font></A></td>
					<?if($redeliverytype=="G"){
						list($reordercode)=pmysql_fetch("select oi.ordercode as reordercode from tblorderproduct op left join tblorderinfo oi on op.ordercode=oi.ordercode WHERE oi.oldordno='".$row->ordercode."' and op.productcode='".$row->productcode."'");
					?>
					<td align="center"><A HREF="javascript:OrderDetailView('<?=$reordercode?>')"><?=$reordercode?$reordercode:"-"?></A></td>
					<?}?>
                    <td style="text-align:left;padding:3"><a href="javascript:ProductDetail('<?=$row->productcode?>')"><img src="<?=$product_img?>" alt="" style="width:70px"></a></td>
					<td style="text-align:left;padding:3"><a href="javascript:ProductDetail('<?=$row->productcode?>')"><strong><?=$row->brandname?></strong><br><?=$row->productname?><?=$erp_pc_code?></a></td>
                    <td align=center><?=$optStr?></td>
			        <td align=center><?=$row->option_quantity?></td>
					<?if($redeliverytype=="G"){
						list($restorename)=pmysql_fetch("select name as resotrename from tblstore where store_code='".$row->return_store_code."'");
						?>
					<td align=center><?=$row->store_name?></td>
					<td align=center><?=$restorename?$restorename:"-"?></td>
					<td>
						<?
							echo $arrChainCode[$row->delivery_type];
							//if($row->store_code) {echo '<br>'.$row->store_code;}
						?>
					</td><!-- O2O -->
					<?}else if($redeliverytype=="Y"){?>
					<td align=center><?=$row->store_name?></td>
					<td>
						<?
							echo $arrChainCode[$row->delivery_type];
							if($row->store_code) {echo '<br>'.$row->store_code;}
						?>
					</td><!-- O2O -->
					<?}?>		
					<td align=center style="padding:3"><?=$arpm[$row->paymethod[0]]?>
					<?if($row->paymethod[0] == "O" & $row->imagination_cancel == "Y"){?>
					<br>(환불완료)
					<?} ?>
					</td>
					<td><font color='blue'><?=orderCancelStatusStep($row->redelivery_type, $row->oc_step, $row->hold_oc_step)?></font><br>(<?=$oc_reg_type?>)<?=$ord_status?"<br>".$ord_status:""?><?=$row->accept_status=="Y"?"<br><font color='red'>(접수)</font>":""?></td>
					<td><?=$findate?$findate:'-'?></td>
					<td><input type='button' value='<?=$redeliveryname?>처리' onclick="rechange_pop('<?=$row->oc_no?>', '<?=$redeliverypop?>')" style='padding:2px 5px 1px'></td>
					<!--<td><input type='button' value='삭제' style='padding:2px 5px 1px'></td>-->
				</tr>
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
				<tr><td align='left' valign=middle><a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td></tr>
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


			<form name=detailform method="post" action="order_detail.php" target="orderdetail">
				<input type=hidden name=ordercode>
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
				<input type=hidden name=type>
				<input type=hidden name=ordercodes>
				<input type=hidden name=block value="<?=$block?>">
				<input type=hidden name=gotopage value="<?=$gotopage?>">
				<input type=hidden name=s_check value="<?=$s_check?>">
				<input type=hidden name=search value="<?=$search?>">
				<input type=hidden name=search_start value="<?=$search_start?>">
				<input type=hidden name=search_end value="<?=$search_end?>">
				<input type=hidden name=brandname value="<?=$brandname?>">
				<input type=hidden name=oc_step value="<?=$oc_step?>">
				<input type=hidden name=sel_code value="<?=$sel_code?>">
				<input type=hidden name=sel_vender value="<?=$sel_vender?>">
				<input type=hidden name=com_name value="<?=$com_name?>">
				<input type=hidden name=paymethod value="<?=$paymethod?>">
				<input type=hidden name=ord_flag value="<?=$ord_flag?>">
				<input type=hidden name=reg_type value="<?=$reg_type?>">
				<input type=hidden name=redeliverytype id=redeliverytype value="<?=$redeliverytype?>">
			</form>


			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
				<?if($redeliverytype=="G"){?>
					<input type=hidden name="item_type" value="cs_order_cancel_34_change_41">
				<?}else if($redeliverytype=="Y"){?>
					<input type=hidden name="item_type" value="cs_order_cancel_34_41">
				<?}?>
				
				<!--<input type=hidden name="item_type" value="order_all">-->
				<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
				<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
				<input type=hidden name="oc_step" value="<?=$oc_step?>">
				<input type=hidden name="redeliverytype" id=redeliverytype value="<?=$redeliverytype?>">
				<input type=hidden name="ordercodes">
			</form>

            <form name=crmview method="post" action="crm_view.php">
				<input type=hidden name=id>
			</form>

			<form name=delform method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type=hidden name='mode' value="delete">
				<input type=hidden name='oc_no' id='oc_no'>

			</form>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>

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

<?php
include("copyright.php");
?>