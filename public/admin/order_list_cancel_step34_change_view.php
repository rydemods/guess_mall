<?php
/*********************************************************************
// 파 일 명		: order_list_regoods.php
// 설     명		: 주문상품 교환 접수
// 상세설명	: 교환접수 내역을 확인/처리하실 수 있습니다.
// 작 성 자		: 2016.02.03 - 김재수
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

##################### 페이지 접근권한 check #####################
	$PageCode = "or-2";
	$MenuCode = "order";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------
	$opstep     = $_REQUEST["opstep"];  // 단계정보 (40 : 배송중, 배송완료일때의 교환신청, 41 : 배송중, 배송완료일때의 교환접수)
	$mode=$_POST["mode"];

	$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

	if ($mode == 'update_40') {
		//exdebug($_POST);
		//exit;
		$oc_no				= explode(",", $_POST['oc_no']);
		$ordercode			= explode(",", $_POST['ordercode']);
		$idxs					= explode(",", $_POST['idxs']);

		$arrResultMsg = array();

		for ($i=0;$i < count($oc_no);$i++) {

			$arrResult = array("주문번호 : ".$ordercode[$i]);

			$fail_cnt	= 0;

			if ($fail_cnt == 0) {

				orderCancelAccept($exe_id, $ordercode[$i], $idxs[$i], $oc_no[$i] );

				sms_autosend( 'mem_cancel', $ordercode[$i], $oc_no[$i], '' );
				sms_autosend( 'admin_cancel', $ordercode[$i], $oc_no[$i], '' );

                // 벤더별 메일 및 SMS 발송 ...2016-06-14 jhjeong
                //SendVenderForm( $shopname, $shopurl, $ordercode[$i], $mail_type, $info_email, 4, $oc_no[$i] );

				array_push($arrResult, " / 처리 : 성공");
				array_push($arrResultMsg, $arrResult);

			} else {
				array_push($arrResult, " / 처리 : 실패");
				array_push($arrResultMsg, $arrResult);
			}
		}

		$canmess="처리가 완료되었습니다.";

		foreach ( $arrResultMsg as $arrData ) {
			$canmess.="\\n";
			foreach ( $arrData as $data ) {
				$canmess.="{$data}";
			}
		}
		//exit;
		echo "<html></head><body onload=\"alert('".$canmess."');parent.location.reload();\"></body></html>";exit;
	} else if ($mode == 'update_41') {
		//exdebug($_POST);
		$oc_no					= explode(",", $_POST['oc_no']);
		$ordercode				= explode(",", $_POST['ordercode']);
		$idxs		= explode(",", $_POST['idxs']);
		$rc_memo		= explode("|!@#|", $_POST['rc_memo']);
		for ($i=0;$i < count($oc_no);$i++) {
			// 메모를 업데이트 한다.

			$memo_sql   = " UPDATE tblorder_cancel SET rdesc = '".$rc_memo[$i]."' WHERE oc_no='".$oc_no[$i]."' ";
			pmysql_query($memo_sql,get_db_conn());

			orderCancelReorderFin($exe_id, $ordercode[$i], $idxs[$i], $oc_no[$i] );

			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='".trim($ordercode[$i])."' ";
			$sql.= "AND idx IN ('".str_replace("|", "','", $idxs[$i])."') ";
			//echo $sql;

			if(pmysql_query($sql,get_db_conn())) {
				//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
				list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($ordercode[$i])."'AND idx NOT IN ('".str_replace("|", "','", $idxs[$i])."') AND deli_gbn != 'C' AND op_step != '44'"));
				if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
					$sql = "UPDATE tblorderinfo SET deli_gbn='C' WHERE ordercode='".trim($ordercode[$i])."' ";
					//echo $sql;
					pmysql_query($sql,get_db_conn());
				}
			}
		}
		echo "<html></head><body onload=\"alert('처리가 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
	}


	/*$ordercode		= "2016010120185008470A";
	$idxs		= "1443053305";
	$oi_step1	="3";
	$oi_step2	="0";
	$paymethod		= "C";
	$code			= "1";
	$memo			= "테스트5";
	$bankcode	= "";
	$bankaccount	= "";
	$bankuser		="";
	$re_type		="C";
	$opt1_changes		="모델::블랙[S3BER]";
	$opt2_changes		="";*/
	//orderCancel($exe_id, $ordercode, $idxs, $oi_step1, $oi_step2, $paymethod, $code, $memo, $bankcode, $bankaccount, $bankuser, $re_type, $opt1_changes, $opt2_changes );


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
	$period[4] = substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);


	$search_start = $_GET["search_start"];
	$search_end = $_GET["search_end"];
	$sel_code = $_GET["sel_code"];
	$s_check = $_GET["s_check"];
	$search = trim($_GET["search"]);
	$sel_vender = $_GET["sel_vender"];
	$com_name = $_GET["com_name"];  // 벤더이름 검색

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

	$qry = "WHERE toc.pickup_state in ('R','Y') AND toc.restore ='N' ";
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

	$qry_from  = " tblorder_cancel toc ";
	$qry_from .= "join tblorderinfo a on toc.ordercode = a.ordercode ";

	$excel_qry_from		= $qry_from;
	$excel_qry				= $qry;

	if($vendercnt>0) {
		if($sel_vender || $com_name) {
			if($com_name) $subqry = " and b.brandname like '%".strtoupper($com_name)."%'";
			else if($sel_vender) $subqry = " and b.vender = ".$sel_vender."";
		}

		$qry_from .= "join (Select p.oc_no, p.vender, pb.brandname 
					from tblorderproduct p 
					left join tblvenderinfo v on p.vender = v.vender 
					left join tblproductbrand pb on p.vender=pb.vender
					left join tblproduct pr on p.productcode=pr.productcode 
					where p.oc_no > 0 
					and p.redelivery_type = 'G' 
					and p.op_step=".$opstep." 
					group by p.oc_no, p.vender, pb.brandname
					) b on toc.oc_no=b.oc_no {$subqry} ";

		$excel_qry_from .= ", (Select p.vender, p.ordercode, p.productcode, p.productname, p.opt1_name, p.opt2_name, p.quantity, p.price, p.option_price,
					p.deli_com, p.deli_num, p.deli_date, p.deli_price, 
					p.coupon_price, p.use_point, p.op_step, p.opt1_change, p.opt2_change, p.oc_no, p.date, p.idx, p.option_price_text_change, p.option_quantity, p.self_goods_code, p.self_goods_code_change, pr.prodcode, pr.colorcode
					FROM tblorderproduct p 
					left join tblvenderinfo v on p.vender = v.vender 
					left join tblproductbrand pb on p.vender=pb.vender
					left join tblproduct pr on p.productcode=pr.productcode  
					where p.oc_no > 0 
					and p.redelivery_type = 'G' 
					and p.op_step=".$opstep."
					) b 
                    ";
		$excel_qry.= "AND toc.oc_no=b.oc_no ";
	} else {
		$qry_from .= "join (Select oc_no from tblorderproduct p where oc_no > 0 and redelivery_type = 'G' and op_step=".$opstep." group by oc_no) b on toc.oc_no=b.oc_no ";

		$excel_qry_from .= ", (Select oc_no from tblorderproduct p where oc_no > 0 and p.redelivery_type = 'G' and op_step=".$opstep." group by oc_no) b ";
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

	$sql = "SELECT COUNT(*) as t_count FROM {$qry_from} {$qry} ";
	//echo $sql ;
	$paging = new newPaging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;

	$excel_sql = "SELECT  b.vender, b.ordercode, b.productcode, b.productname, b.opt1_name, b.opt2_name, b.quantity, b.price, b.option_price,
					b.deli_com, b.deli_num, b.deli_date, b.deli_price, 
					b.coupon_price, b.use_point, b.op_step, b.opt1_change, b.opt2_change, b.oc_no, b.date, b.idx, b.option_price_text_change, b.option_quantity, toc.regdt, toc.code, toc.rdesc,
					a.id, a.sender_name, a.paymethod, a.oi_step1, a.oi_step2, b.self_goods_code, b.self_goods_code_change, b.prodcode, b.colorcode 
			FROM {$excel_qry_from} {$excel_qry} ";
	$excel_sql_orderby = "
			ORDER BY  toc.oc_no {$orderby} 
		";
//exdebug($excel_sql.$excel_sql_orderby);

?>
<html>
<head>
<meta http-equiv='Content-Type' content='text/html;charset=utf-8'>
<link rel="stylesheet" href="style.css" type="text/css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="lib.js.php"></script>
<script src="../js/jquery.js"></script>
<script language="JavaScript">
<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	document.form1.action="order_list_cancel_step34_change_view.php";
	document.form1.submit();
}

function OrderDetailView(ordercode) {
	document.detailform.ordercode.value = ordercode;
	window.open("","orderdetail","scrollbars=yes,width=700,height=600");
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

function GoOrderby(orderby) {
	document.idxform.block.value = "";
	document.idxform.gotopage.value = "";
	document.idxform.orderby.value = orderby;
	document.idxform.submit();
}

function MemberView(id){
	parent.topframe.ChangeMenuImg(4);
	document.member_form.search.value=id;
	document.member_form.submit();
}

function SenderSearch(sender) {
	//document.sender_form.search.value=sender;
	//document.sender_form.submit();
	document.form1.search_start.value="";
	document.form1.search_end.value="";
	document.form1.s_check.value="mn";
	document.form1.search.value=sender;
	document.form1.action="order_list_cancel_step34_change_view.php";
	document.form1.submit();
}

function OrderRechangeSubmit() {
	var chk_len	= $("input[name='chk_oc_no']").length;
	document.exeform.oc_no.value="";
	document.exeform.ordercode.value="";
	document.exeform.idxs.value="";
	document.exeform.rc_memo.value="";
	var k = 0;
	if (chk_len == 1)
	{
		if(document.form2.chk_oc_no.checked) {
			document.exeform.oc_no.value+=document.form2.chk_oc_no.value;
			document.exeform.ordercode.value+=document.form2.ordercode.value;
			document.exeform.idxs.value+=document.form2.idxs.value;
			document.exeform.rc_memo.value+=document.form2.rc_memo.value;
			k++;
		}
	} else {
		for(i=0;i<document.form2.chk_oc_no.length;i++) {
			if(document.form2.chk_oc_no[i].checked) {
				if (k == 0)
				{
					document.exeform.oc_no.value+=document.form2.chk_oc_no[i].value;
					document.exeform.ordercode.value+=document.form2.ordercode[i].value;
					document.exeform.idxs.value+=document.form2.idxs[i].value;
					document.exeform.rc_memo.value+=document.form2.rc_memo[i].value;
				} else {
					document.exeform.oc_no.value+=","+document.form2.chk_oc_no[i].value;
					document.exeform.ordercode.value+=","+document.form2.ordercode[i].value;
					document.exeform.idxs.value+=","+document.form2.idxs[i].value;
					document.exeform.rc_memo.value+="|!@#|"+document.form2.rc_memo[i].value;
				}
				k++;
			}
		}
	}
	if(document.exeform.oc_no.value.length==0) {
		alert("선택하신 내역이 없습니다.");
		return;
	}
	document.exeform.mode.value="update_<?=$opstep?>";
	document.exeform.target="processFrame";
	document.exeform.submit();
}

function OrderExcel() {
	document.downexcelform.oc_no.value="";
	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function OrderRechangeExcel() {
	document.downexcelform.oc_no.value="";
	var chk_len	= $("input[name='chk_oc_no']").length;
	if(chk_len == 1) {
		if(document.form2.chk_oc_no.checked) {
			document.downexcelform.oc_no.value+=document.form2.chk_oc_no.value;
		}
	} else {
		for(i=0;i<document.form2.chk_oc_no.length;i++) {
			if(document.form2.chk_oc_no[i].checked) {
				if(document.downexcelform.oc_no.value!='') document.downexcelform.oc_no.value +=",";
				document.downexcelform.oc_no.value+=document.form2.chk_oc_no[i].value;
			}
		}
	}
	if(document.downexcelform.oc_no.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

	window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}
</script>
</head>
<body>
			<table cellpadding="5" cellspacing="0" width="100%">
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>교환<?if ($opstep=='40') {?>신청<?} else if ($opstep=='41') {?>접수<?}?> 내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>

			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name='opstep' value='<?=$opstep?>'>
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

						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1"><select name="s_check" class="select">
							<option value="cd" <?php if($s_check=="cd")echo"selected";?>>주문코드</option>
							<option value="mn" <?php if($s_check=="mn")echo"selected";?>>구매자성명</option>
							<option value="mi" <?php if($s_check=="mi")echo"selected";?>>구매회원ID</option>
							</select>
							<input type=text name=search value="<?=$search?>" style="width:197" class="input"></TD>
						</tr>
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
<?php
		$sql = "SELECT  toc.*, a.id, a.sender_name, a.paymethod ";
		if($vendercnt>0) $sql.= ", b.vender ";
        $sql.= "FROM {$qry_from} {$qry} ";
		$sql.= "ORDER BY toc.oc_no {$orderby} ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif" border="0"><B>정렬 :
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>주문일자순↑</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?php }?>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02" style="padding-bottom:10px;">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40></col>
				<col width=150></col>
				<col width=150></col>
				<col width=150></col>
				<col width=></col>
				<?php if($vendercnt>0){?>
				<col width=150></col>
				<?php }?>
				<col width=150></col>
				<col width=200></col>
				<TR >
					<th></th>
					<th>주문일자</th>
					<th>교환요청일</th>
					<th>교환사유</th>
					<th>주문번호</th>
					<?php if($vendercnt>0){?>
					<th>브랜드</th>
					<?php }?>
					<th>주문자</th>
					<th>아이디</th>
				</TR>

<?php
		$colspan=7;
		if($vendercnt>0) $colspan++;

		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

			if ($row->pickup_date) {
				 $chk_dis	= " disabled=true";
				 $tr_bgc		= "#EFEFEF";
			} else {
				 $chk_dis	= "";
				 $tr_bgc		= "#FFFFFF";
			}

			$date = substr($row->ordercode,0,4)."-".substr($row->ordercode,4,2)."-".substr($row->ordercode,6,2)." ".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).":".substr($row->ordercode,12,2);
			$regdate = substr($row->regdt,0,4)."-".substr($row->regdt,4,2)."-".substr($row->regdt,6,2)." ".substr($row->regdt,8,2).":".substr($row->regdt,10,2).":".substr($row->regdt,12,2);
			$name=$row->sender_name;
			$stridX='';
			$stridM='';
			if(substr($row->ordercode,20)=="X") {	//비회원
				$stridX = substr($row->id,1,6);
			} else {	//회원
//				$stridM = "<A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
				$stridM = "<A HREF=\"javascript:CrmView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
			}

?>
			<tr bgcolor=<?=$tr_bgc?>>
			<td align="center" style='border:1px solid <?=$tr_bgc?>'><input type=checkbox name="chk_oc_no" value="<?=$row->oc_no?>"<?=$chk_dis?>><input type=hidden name="ordercode" value="<?=$row->ordercode?>"></td>
			<td align="center"style="font-size:8pt;padding:3;line-height:11pt;border:1px solid <?=$tr_bgc?>"><?=$date?></td>
			<td align="center"style="font-size:8pt;padding:3;line-height:11pt;border:1px solid <?=$tr_bgc?>"><?=$regdate?></td>
			<td align="center" style='border:1px solid <?=$tr_bgc?>'><?=$oc_code[$row->code]?></td>
				<td align="center" style='border:1px solid <?=$tr_bgc?>'><A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><FONT COLOR="blue"><?=$row->ordercode?></font></A></td>
<?php
			if($vendercnt>0) {
				list($vender)=pmysql_fetch("SELECT vender from tblorderproduct WHERE oc_no='{$row->oc_no}' ");
?>
					<td style='border:1px solid <?=$tr_bgc?>'><?if (ord($venderlist[$vender]->vender)) {echo "<a href=\"javascript:viewVenderInfo({$vender})\"><FONT COLOR=\"blue\">{$venderlist[$vender]->brandname}</font></a>"; } else {echo "-";}?></td>
<?php
			}
?>
				<td align="center" style='border:1px solid <?=$tr_bgc?>'><A HREF="javascript:SenderSearch('<?=$name?>');"><FONT COLOR="blue"><?=$name?></font></A></td>
				<td align="center" style='border:1px solid <?=$tr_bgc?>'>
<?php

			if(ord($stridX)) {
				echo "주문번호: ".$stridX;
			} else if(ord($stridM)) {
				echo "아이디: ".$stridM;
			}
?>
				</td>
			</tr>
			<tr bgcolor=<?=$tr_bgc?>>
				<td align="center" colspan=<?=$colspan?> style="padding-bottom: 18px;">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=220></col>
				<col width=></col>
				<col width=110></col>
				<col width=110></col>
				<col width=110></col>
				<col width=110></col>
				<col width=110></col>
				<col width=110></col>
				<col width=110></col>
				<tr bgcolor=#EFEFEF>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>상품명</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>판매가격</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>수량</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>옵션</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>쿠폰</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>적립금</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>개별배송비</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-top:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>상품결제가</td>
				</tr>
<?php

			$sql = "SELECT idx, productcode, 
			vender, 
			productname, 
			opt1_name, 
			opt2_name, 
			option_quantity,
			opt1_change, 
			opt2_change, text_opt_subject, text_opt_content, option_price_text, text_opt_subject_change, text_opt_content_change, option_price_text_change, 
			min(delivery_type) as delivery_type, 
			min(store_code) as store_code, 
			min(reservation_date) as reservation_date, 
			SUM( price ) AS sum_price, 
			SUM( quantity ) AS sum_qnt, 
			SUM(option_price * option_quantity) AS sum_opt_price, 
			SUM(coupon_price) AS sum_coupon, 
			SUM(use_point) AS sum_use_point,
			SUM(deli_price) AS sum_deli_price,
			SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_totprice

			FROM tblorderproduct WHERE oc_no='{$row->oc_no}' ";
			$sql.=" GROUP BY idx, productcode, vender, productname, opt1_name, opt2_name, option_quantity, opt1_change, opt2_change, text_opt_subject, text_opt_content, option_price_text, text_opt_subject_change, text_opt_content_change, option_price_text_change ";
			//echo $sql;
			$result2=pmysql_query($sql,get_db_conn());
			$re_tot_price	=0;
			$idxs	= "";

			while($row2=pmysql_fetch_object($result2)) {
				$storeData = getStoreData($row2->store_code);
				if ($idxs == "") {
					$idxs	.= $row2->idx;
				} else {
					$idxs	.= "|".$row2->idx;
				}
				/*$opt_name ="";
				$opt_change ="";
				if ($row2->opt1_name) $opt_name .= $row2->opt1_name;
				if ($opt_name != '' && $row2->opt2_name) $opt_name .= " / ".$row2->opt2_name;
				if ($row2->opt1_change) $opt_change .= $row2->opt1_change;
				if ($opt_change != '' && $row2->opt2_change) $opt_change .= " / ".$row2->opt2_change;*/



				$oc_opt_name	= "";
				if( strlen( trim( $row2->opt1_name ) ) > 0 ) {

					$oc_opt1_name_arr	= explode("@#", $row2->opt1_name);
					$oc_opt2_name_arr	= explode(chr(30), $row2->opt2_name);
					$s_cnt	= 0;
					for($s=0;$s < sizeof($oc_opt1_name_arr);$s++) {
						if ($oc_opt2_name_arr[$s]) {
							if ($s_cnt > 0) $oc_opt_name	.= " / ";
							$oc_opt_name	.= $oc_opt1_name_arr[$s].' : '.$oc_opt2_name_arr[$s];
							$s_cnt++;
						}
					}
				}

				if( strlen( trim( $row2->text_opt_subject ) ) > 0 ) {
					$oc_text_opt_subject_arr	= explode("@#", $row2->text_opt_subject);
					$oc_text_opt_content_arr	= explode("@#", $row2->text_opt_content);

					for($s=0;$s < sizeof($oc_text_opt_subject_arr);$s++) {
						if ($oc_text_opt_content_arr[$s]) {
							if ($oc_opt_name != '') $oc_opt_name	.= " / ";
							$oc_opt_name	.= $oc_text_opt_subject_arr[$s].' : '.$oc_text_opt_content_arr[$s];
						}
					}
				}
				if ($oc_opt_name) {
					$oc_opt_name	.= " >> ";
					$oc_opt_name	.= "<font color='#0074BA'>";
				}

				if( strlen( $row2->opt1_change ) > 0 ) {
					$oc_optc1_name_arr	= explode("@#", $row2->opt1_change);
					$oc_optc2_name_arr	= explode(chr(30), $row2->opt2_change);
					for($ss=0;$ss < sizeof($oc_optc1_name_arr);$ss++) {
						if ($ss > 0) $oc_opt_name	.= " / ";
						$oc_opt_name	.= $oc_optc1_name_arr[$ss].' : '.$oc_optc2_name_arr[$ss];
					}
				}

				if( strlen( trim( $row2->text_opt_subject_change ) ) > 0 ) {
					$oc_text_opt_subject_arr	= explode("@#", $row2->text_opt_subject_change);
					$oc_text_opt_content_arr	= explode("@#", $row2->text_opt_content_change);

					for($s=0;$s < sizeof($oc_text_opt_subject_arr);$s++) {
						if ($oc_text_opt_content_arr[$s]) {
							if ($oc_opt_name != '') $oc_opt_name	.= " / ";
							$oc_opt_name	.= $oc_text_opt_subject_arr[$s].' : '.$oc_text_opt_content_arr[$s];
						}
					}
				}
				if ($oc_opt_name) {
					$oc_opt_name	.= "</font>";
				}

				$opt_price_chn	= 0;
				$tot_opt_price_chn_text	= "";
				$tot_price_chn_text	= "";

				if ($row2->option_price_text_change) {
					$optc_arr	= explode("||", $row2->option_price_text_change);
					for($i-0;$i < count($optc_arr);$i++) {
						$opt_price_chn = $opt_price_chn + $optc_arr[$i];
					}

					$tot_opt_price_chn	= $opt_price_chn * $row2->option_quantity;

					if ($tot_opt_price_chn > 0 && $tot_opt_price_chn != $row2->sum_opt_price) {
						$tot_opt_price_chn_text		= "<br>(<font color='#0074BA'>".number_format($tot_opt_price_chn)."원</font>)";
						$tot_price_chn_text				= "<br>(<font color='#0074BA'>".number_format((($row2->sum_price + $opt_price_chn) * $row2->option_quantity) - $row2->sum_coupon - $row2->sum_use_point + $row2->sum_deli_price)."원</font>)";
					}
				}
?>
				<tr bgcolor=<?=$tr_bgc?>>
					<td align="center" style='text-align:left;padding-left:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>
						<span style="line-height:10pt">
							<?=$row2->productname?>
							<span class="page_screen">
								<a href="/front/productdetail.php?productcode=<?=$row2->productcode?>" target="_blank"><b>[보기]</b></a>
							</span><br><span style="line-height:13pt;"><font color="#EA0095"><?=$oc_opt_name?></font></span>
						</span>
						<?if($storeData['name'] && $row2->delivery_type != '2'){	//2016-10-07 libe90 매장발송 정보표시?>
							<p style = 'color:blue;'>[<?=$arrDeliveryType[$row2->delivery_type]?>] <?=$storeData['name']?> <?if($row2->delivery_type == '2'){?>( 예약일 : <?=$row2->reservation_date?> )<?}?></p>
						<?}else if($row2->delivery_type == '2'){?>
							<p style = 'color:blue;'>[<?=$arrDeliveryType['2']?>] <?=$storeData['name']?></p>
						<?}?>
					</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_price)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_qnt)?>개</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_opt_price)?>원<?=$tot_opt_price_chn_text?></td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_coupon)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_use_point)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_deli_price)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_totprice)?>원<?=$tot_price_chn_text?></td>
				</tr>
<?php
				$re_tot_price = $re_tot_price + $row2->sum_totprice;
			}
			pmysql_free_result($result2);
?>
				<input type=hidden name=idxs value='<?=$idxs?>'>
				<tr bgcolor=#FEF8ED<?if ($opstep == '40') echo " style='display:none;'";?>>
					<td align="center" style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><b>메모</b></td>
					<td align="right" colspan=8 style='text-align:left;padding-right:5px;padding-left:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;border-right:1px solid #cbcbcb;'><textarea name='rc_memo' rows="3" cols="50" style="width:100%"></textarea></td>
				</tr>
				</table>
				</td>
			</tr>
<?php
			$cnt++;
		}
		pmysql_free_result($result);

		if($cnt==0) {
?>
			<tr height=28 bgcolor=#FFFFFF><td colspan=<?=$colspan?> align=center>조회된 내용이 없습니다.</td></tr>
<?php
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
					<td align='left' valign=middle><a href="javascript:OrderRechangeSubmit();"><img src="images/btn_<?if ($opstep=='40') {?>req<?} else if ($opstep=='41') {?>rechange<?}?>.gif" border="0" hspace="0"></a></td>
					<td align='center'>
                    <div id="page_navi01" style='margin:0 0'>
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
					</td>
					<td align='right' valign=middle><a href="javascript:OrderRechangeExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>
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
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name='opstep' value='<?=$opstep?>'>
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="order_cancel_34_change_<?=$opstep?>">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="oc_no">
			</form>

			<form name=exeform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=ordercode>
			<input type=hidden name=mode>
			<input type=hidden name=oc_no>
			<input type=hidden name=idxs>
			<input type=hidden name=rc_memo>
			</form>

            <form name=crmview method="post" action="crm_view.php">
            <input type=hidden name=id>
            </form>

			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>

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


<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
</body>
</html>
