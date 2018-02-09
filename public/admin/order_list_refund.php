<?php
/********************************************************************* 
// 파 일 명		: order_list_refund.php 
// 설     명		: 주문상품 환불 접수
// 상세설명	: 환불접수 내역을 확인/처리하실 수 있습니다.
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
	include("access.php");
	include("calendar.php");

##################### 페이지 접근권한 check #####################
	$PageCode = "or-4";
	$MenuCode = "order";
	if (!$_usersession->isAllowedTask($PageCode)) {
		include("AccessDeny.inc.php");
		exit;
	}
#################################################################

	//pg정보
	$pgid_info=GetEscrowType($_shopdata->card_id);
	$pg_type=$pgid_info["PG"];

#---------------------------------------------------------------
# 넘어온 값들을 정리한다.
#---------------------------------------------------------------

	$mode=$_POST["mode"];

	$exe_id		= $_ShopInfo->getId()."|".$_ShopInfo->getName()."|admin";	// 실행자 아이디|이름|타입

	if ($mode == 'update') {
		//exdebug($_POST);
		$oc_no					= explode(",", $_POST['oc_no']);
		$ordercode				= explode(",", $_POST['ordercode']);
		$idxs						= explode(",", $_POST['idxs']);
		$bankcode				= explode(",", $_POST['bankcode']);
		$bankaccount			= explode(",", $_POST['bankaccount']);
		$bankuser				= explode(",", $_POST['bankuser']);
		$rfee						= explode(",", $_POST['rfee']);
		$coupon_status		= explode(",", $_POST['coupon_status']);
		for ($i=0;$i < count($oc_no);$i++) {

			orderCancelFin($exe_id, $ordercode[$i], $idxs[$i], $oc_no[$i], '', $bankcode[$i], $bankaccount[$i], $bankuser[$i], $rfee[$i], $coupon_status );

			$sql = "UPDATE tblorderproduct SET deli_gbn='C' ";
			$sql.= "WHERE ordercode='".trim($ordercode[$i])."' ";
			$sql.= "AND idx IN ('".str_replace("|", "','", $idxs[$i])."') ";
			//echo $sql;

			if(pmysql_query($sql,get_db_conn())) {
				//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
				list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($ordercode[$i])."'AND idx NOT IN ('".str_replace("|", "','", $idxs[$i])."') AND deli_gbn != 'C' AND op_step != '44'"));
				if ($op_deli_gbn_cnt == 0 ) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
					//PG취소가 완료된 수수료를 가져온다.
					list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($ordercode[$i])."' AND pgcancel = 'Y' GROUP BY ordercode"));
					$sql = "UPDATE tblorderinfo SET deli_gbn='C' ";
					if (($op_rfee_amt + $rfee[$i]) == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
						$sql .= ", pay_admin_proc='C' ";
					}
					$sql .= "WHERE ordercode='".trim($ordercode[$i])."' ";
					//echo $sql;
					pmysql_query($sql,get_db_conn());
				}
			}
		}
		echo "<html></head><body onload=\"alert('처리가 완료되었습니다.');parent.location.reload();\"></body></html>";exit;
	} else if ($mode == 'pgcancel') { // PG상태값 업데이트 (신용카드)
		//exdebug($_POST);
		$oc_no					= $_POST['oc_no'];
		$ordercode				= $_POST['ordercode'];
		$rfee						= $_POST['rfee'];
			
		//취소완료상태가 아니고 주문취소도 아닌 카운트를 가져온다.
		list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(deli_gbn) as op_deli_gbn_cnt from tblorderproduct WHERE ordercode='".trim($ordercode)."' AND oc_no != '".$oc_no."' AND deli_gbn != 'C' AND op_step != '44'"));			
		if ($op_deli_gbn_cnt == 0) { // 주문취소가 아닌 상품들 모두 취소상태일 경우
			//PG취소가 완료된 수수료를 가져온다.
			list($op_rfee_amt)=pmysql_fetch_array(pmysql_query("select SUM(rfee) as op_rfee_amt from tblorder_cancel WHERE ordercode='".trim($ordercode)."' AND pgcancel = 'Y' GROUP BY ordercode"));
			if (($op_rfee_amt + $rfee) == 0) { // 수수료가 없으면 PG결제상태를 취소로 변경한다.
				$sql = "UPDATE tblorderinfo SET pay_admin_proc='C' ";
				$sql.= "WHERE ordercode='".trim($ordercode)."' ";
				pmysql_query($sql,get_db_conn());
			}
		}

		

		//환불 금액을 가져온다. - 상품당 금액 가져오는 부분
		list($sum_price)=pmysql_fetch_array(pmysql_query("select SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_price from tblorderproduct  WHERE ordercode='{$ordercode}' and oc_no='{$oc_no}' group by ordercode"));

		// 최종 환불금액을 계산한다. (실결제금액 - 환불수수료)
		if ($rfee > 0) {
			$rprice	= $sum_price - $rfee;
		} else {
			$rprice	= $sum_price;
		}

		// 취소테이블의 취소상태를 완료로 변경한다.
		$sql   = " UPDATE tblorder_cancel SET pgcancel='Y', rprice = '{$rprice}' ";
		if ($rfee) $sql  .= " , rfee='{$rfee}' ";
		$sql.= "WHERE oc_no='".$oc_no."' ";
		pmysql_query($sql,get_db_conn());

		echo "<html></head><body onload=\"alert('결제 취소가 완료되었습니다. 주문을 취소처리해 주시기 바랍니다.');parent.location.reload();\"></body></html>";exit;
	} else if ($mode == 'pgcancel_reg') { // PG사 취소요청 (계좌이체 - 매매보호)
		//exdebug($_POST);
		$oc_no					= $_POST['oc_no'];
		$ordercode				= $_POST['ordercode'];
		$bankcode				= $_POST['bankcode'];
		$bankaccount			= $_POST['bankaccount'];
		$bankuser				= $_POST['bankuser'];
		$rfee						= $_POST['rfee'];

		$ord_sql		= "SELECT * FROM tblorderinfo WHERE ordercode='".$ordercode."' ";
		$ord_result	= pmysql_query($ord_sql,get_db_conn());
		$_ord			= pmysql_fetch_object($ord_result);
		pmysql_free_result($ord_result);

		/************* 에스크로 결제 환불(가상계좌) 또는 취소(신용카드) ***************/
		if(strstr("QP", $_ord->paymethod[0])) {
			
			if(strstr("Q", $_ord->paymethod[0])) { // 결제 환불(가상계좌)
                /*
				#환불계좌 은행=>kcp은행코드 array (2016.01.27 - 김재수)
				$re_bankcode	= array(
									1	=>"39",
									2	=>"34",
									3	=>"04",
									4	=>"03",
									5	=>"11",
									6	=>"31",
									8	=>"32",
									9	=>"02",
									11	=>"45",
									12	=>"07",
									13	=>"48",
									14	=>"88",
									15	=>"05",
									16	=>"20",
									17	=>"71",
									18	=>"37",
									19	=>"35",
									20	=>"81",
									21	=>"27",
									22	=>"54",
									23	=>"23"
				);

				$kcp_bankcode = $re_bankcode[$bankcode];
                */
                $kcp_bankcode = $bankcode;      // lib.php에 나이스 은행코드로 전체 통일.2016-09-08

				//계좌를 업데이트 한다.
				$sql = "UPDATE tblpvirtuallog SET refund_bank_code='{$kcp_bankcode}', refund_account='{$bankaccount}', refund_name='{$bankuser}' ";
				$sql.= "WHERE ordercode='".trim($ordercode)."' ";
				pmysql_query($sql,get_db_conn());
			}

			//Q(가상계좌 매매보호)일 경우엔 우선 환불대기 후 환불되면 자동 취소처리된다.

			//pg정보
			$pgid_info=GetEscrowType($_shopdata->escrow_id);
			$pg_type=$pgid_info["PG"];

			if($pg_type=="A") {			#KCP
				$query="sitecd={$pgid_info["ID"]}&sitekey={$pgid_info["KEY"]}&ordercode=".$ordercode;
			} elseif($pg_type=="B") {	#LG데이콤
				$query="mid={$pgid_info["ID"]}&mertkey={$pgid_info["KEY"]}&ordercode=".$ordercode;
			} elseif($pg_type=="C") {  #올더게이트
				$query="storeid={$pgid_info["ID"]}&ordercode=".$ordercode;
			} elseif($pg_type=="D") {  #이니시스
				$query="sitecd={$pgid_info["EID"]}&ordercode=".$ordercode."&curgetid=".$_ShopInfo->getId();
			}

			$fail_cnt	= 0;
			
			// 취소로 보낸다.
			$cancel_data=SendSocketPost($_SERVER['HTTP_HOST'],str_replace($_SERVER['HTTP_HOST'],"",$_ShopInfo->getShopurl())."paygate/".$pg_type."/escrow_cancel.php",$query);

			$cancel_data=substr($cancel_data,strpos($cancel_data,"RESULT=")+7);
			if (substr($cancel_data,0,2)!="OK") {
				$tempdata=explode("|",$cancel_data);
				$errmsg="취소처리가 정상 완료 되지 못 했습니다.\\n\\n잠시후 다시 실행하시기 바랍니다.";
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					$alert_text	= $errmsg;
					$fail_cnt++;
				}
			} else {
				$tempdata=explode("|",$cancel_data);
				if(ord($tempdata[1])) $errmsg=$tempdata[1];
				if(ord($errmsg)) {
					$alert_text	= $errmsg;					
				} else {
					$alert_text	= "취소 요청이 완료되었습니다.";
				}
			}
			
			if ($fail_cnt == 0) {
				//환불 금액을 가져온다. - 상품당 금액 가져오는 부분
				list($sum_price)=pmysql_fetch_array(pmysql_query("select SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_price from tblorderproduct  WHERE ordercode='{$ordercode}' and oc_no='{$oc_no}' group by ordercode"));

				// 최종 환불금액을 계산한다. (실결제금액 - 환불수수료)
				if ($rfee > 0) {
					$rprice	= $sum_price - $rfee;
				} else {
					$rprice	= $sum_price;
				}
				
				// 취소테이블의 총합계와 수수료를 변경한다.
				$sql   = " UPDATE tblorder_cancel SET rprice = '{$rprice}' ";
				if ($rfee) $sql  .= " , rfee='{$rfee}' ";
				$sql.= "WHERE oc_no='".$oc_no."' ";
				pmysql_query($sql,get_db_conn());

				// 취소테이블의 취소상태를 대기로 변경한다.
				$sql   = " UPDATE tblorder_cancel SET pgcancel='R', bankcode='{$bankcode}', bankaccount='{$bankaccount}', bankuser='{$bankuser}' ";
				$sql.= "WHERE oc_no='".$oc_no."' OR (ordercode='{$ordercode}' AND restore != 'Y') ";
				pmysql_query($sql,get_db_conn());

				// 환불대기상태로 변경해준다.
				$sql = "UPDATE tblorderinfo SET  deli_gbn='E' ";
				$sql.= "WHERE ordercode='{$ordercode}' ";
				pmysql_query($sql,get_db_conn());
			}

			echo "<html></head><body onload=\"alert('{$alert_text}');parent.location.reload();\"></body></html>";exit;
		}

	}

	include("header.php");

/*$ordercode		= "2016010421472194021A";
$idxs		= "1425355787|1446514813";
$oi_step1	="2";
$oi_step2	="0";
$paymethod		= "B";
$code			= "2";
$memo			= "테스트2";
$bankcode	= "3";
$bankaccount	= "101-112231-1342341";
$bankuser		="서성원";*/
//orderCancel($exe_id, $ordercode, $idxs, $oi_step1, $oi_step2, $paymethod, $code, $memo, $bankcode, $bankaccount, $bankuser );
//orderStepUpdate($exe_id, $ordercode, $step1, $step2 ); // 주문코드, 주문상태코드, 주문취소상태코드


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


	$search_start = $_GET["search_start"];
	$search_end = $_GET["search_end"];
	$paymethod = $_GET["paymethod"];
	$s_check = $_GET["s_check"];
	$search = $_GET["search"];
	$sel_vender = $_GET["sel_vender"];
	$com_name = $_GET["com_name"];  // 벤더이름 검색

	$search_start = $search_start?$search_start:"";
	$search_end = $search_end?$search_end:"";
	$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
	$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

	$tempstart = explode("-",$search_start);
	$tempend = explode("-",$search_end);
	$termday = (strtotime($search_end)-strtotime($search_start))/86400;
	if ($termday>367) {
		alert_go('검색기간은 1년을 초과할 수 없습니다.');
	}

	//$qry = "WHERE toc.pickup_state in ('N','Y') AND toc.restore ='N' AND toc.cfindt ='' ";
	$qry = "WHERE toc.pickup_state in ('N','Y') AND toc.restore ='N' AND (toc.cfindt ='' OR (LENGTH(toc.bankaccount) < 9 AND toc.pgcancel = 'N' AND a.paymethod IN ('CA'))) ";
	if ($search_s != "" || $search_e != "") { 
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND toc.regdt LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$qry.= "AND toc.regdt>='{$search_s}' AND toc.regdt <='{$search_e}' ";
		}
	}

	if(is_array($paymethod)) $paymethod = implode("','",$paymethod);

	$paymethod_arr = explode("','",$paymethod);

	if(ord($paymethod))	$qry.= "AND a.paymethod in('".$paymethod."')";

	if(ord($search)) {
		if($s_check=="cd") $qry.= "AND a.ordercode='{$search}' ";
		else if($s_check=="mn") $qry.= "AND a.sender_name='{$search}' ";
		else if($s_check=="mi") $qry.= "AND a.id='{$search}' ";
	}

	$qry_from  = " tblorder_cancel toc ";
	$qry_from .= "join tblorderinfo a on toc.ordercode = a.ordercode ";

	if($vendercnt>0) {
		if($sel_vender || $com_name) {
			if($com_name) $subqry = " and b.brandname like '%".strtoupper($com_name)."%'";
			else if($sel_vender) $subqry = " and b.vender = ".$sel_vender."";
		}

		$qry_from .= "join (Select p.oc_no, p.vender, pb.brandname from tblorderproduct p left join tblvenderinfo v on p.vender = v.vender left join tblproductbrand pb on p.vender=pb.vender where p.oc_no > 0  and p.redelivery_type != 'G' and p.op_step in ('41','42') group by p.oc_no, p.vender, pb.brandname) b on toc.oc_no=b.oc_no {$subqry} ";
	} else {
		$qry_from .= "join (Select oc_no from tblorderproduct p where oc_no > 0 and redelivery_type != 'G' and op_step in ('41','42') group by oc_no) b on toc.oc_no=b.oc_no ";
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
	echo $sql ;
	$paging = new Paging($sql,10,20);
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function comma(str) {
    str = String(str);
    return str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g, '$1,');
}

function numkeyCheck(e) {
	var keyValue = event.keyCode;
	if( ((keyValue >= 48) && (keyValue <= 57)) ) {
		return true;
	} else {
		return false;
	}
}

function rpiceCheck(obj) {
	var rfee		= parseInt($(obj).val());
	var tprice	= parseInt($(obj).parent().parent().find("input[name='tprice']").val());

	$(obj).parent().parent().find(".rpice").html(comma(tprice - rfee));

	//alert($(obj).val());

}



<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	document.form1.action="order_list_refund.php";
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
	document.form1.action="order_list_refund.php";
	document.form1.submit();
}


function OrderRefundSubmit() {
	var chk_len	= $("input[name='chk_oc_no']").length;
	document.exeform.oc_no.value="";
	document.exeform.ordercode.value="";
	document.exeform.idxs.value="";
	document.exeform.bankcode.value="";
	document.exeform.bankaccount.value="";
	document.exeform.bankuser.value="";
	document.exeform.rfee.value="";
	var k = 0;
	var p = 0;
	if (chk_len == 1)
	{
		if(document.form2.chk_oc_no[i].checked) {
			document.exeform.oc_no.value+=document.form2.chk_oc_no.value;
			document.exeform.ordercode.value+=document.form2.ordercode.value;
			document.exeform.idxs.value+=document.form2.idxs.value;
			document.exeform.bankcode.value+=document.form2.bankcode.value;
			document.exeform.bankaccount.value+=document.form2.bankaccount.value;
			document.exeform.bankuser.value+=document.form2.bankuser.value;
			document.exeform.rfee.value+=document.form2.rfee.value;		
			if (document.form2.payment.value == 'C' && document.form2.pgcancel.value == 'N')
			{
				p++;
			}
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
					document.exeform.bankcode.value+=document.form2.bankcode[i].value;
					document.exeform.bankaccount.value+=document.form2.bankaccount[i].value;
					document.exeform.bankuser.value+=document.form2.bankuser[i].value;
					document.exeform.rfee.value+=document.form2.rfee[i].value;
				} else {
					document.exeform.oc_no.value+=","+document.form2.chk_oc_no[i].value;
					document.exeform.ordercode.value+=","+document.form2.ordercode[i].value;
					document.exeform.idxs.value+=","+document.form2.idxs[i].value;
					document.exeform.bankcode.value+=","+document.form2.bankcode[i].value;
					document.exeform.bankaccount.value+=","+document.form2.bankaccount[i].value;
					document.exeform.bankuser.value+=","+document.form2.bankuser[i].value;
					document.exeform.rfee.value+=","+document.form2.rfee[i].value;
				}
				if (document.form2.payment[i].value == 'C' && document.form2.pgcancel[i].value == 'N')
				{
					p++;
				}
				k++;
			}
		}
	}
	if(document.exeform.oc_no.value.length==0) {
		alert("선택하신 내역이 없습니다.");
		return;
	}
	if(p > 0) {
		if(!confirm("처리건중 카드결제 취소가 안된 주문이 있습니다.\n계속진행 하시겠습니까?")) {
			return;
		}
	}
	if(confirm("처리건중 사용쿠폰에 대해 복구하시겠습니까?\n(선택된 처리건에 대해 일괄 적용됩니다.)")) {
		document.exeform.coupon_status.value="";		
	} else {		
		document.exeform.coupon_status.value="N";
	}
	document.exeform.mode.value="update";
	document.exeform.target="processFrame";
	document.exeform.submit();
}

function OrderExcel() {
    //alert("excel");
	document.form1.action="order_excel_refund.php";
    document.form1.method="POST";
	document.form1.submit();
	document.form1.action="";
}

function OrderRefundExcel() {
	document.checkexcelform.oc_no.value="";
	var k = 0;
	for(i=0;i<document.form2.chk_oc_no.length;i++) {
		if(document.form2.chk_oc_no[i].checked) {
			if (k == 0)
			{
				document.checkexcelform.oc_no.value+=document.form2.chk_oc_no[i].value;
			} else {
				document.checkexcelform.oc_no.value+=","+document.form2.chk_oc_no[i].value;
			}
			k++;
		}
	}
	if(document.checkexcelform.oc_no.value.length==0) {
		alert("선택하신 내역이 없습니다.");
		return;
	}
	document.checkexcelform.submit();
}

function pg_cancel(obj , oc_no, ordercode, pc_type, pg_type) {
	var tprice			= parseInt($(obj).parent().parent().find("input[name='tprice']").val());
	var rfee				= parseInt($(obj).parent().parent().find("input[name='rfee']").val());
	var each_price	= tprice - rfee;
	if (rfee > 0) pc_type	= 'PART';
	if(!confirm("최종 환불금액은 "+comma(each_price)+"원 입니다.\n계속진행 하시겠습니까?")) {
		return;
	}

    if ( pg_type != undefined ) {
        // 실제 pg타입을 넘겨준 경우

        if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {
            document.exeform.oc_no.value=oc_no;
            document.exeform.ordercode.value=ordercode;
            document.exeform.idxs.value="";
            document.exeform.bankcode.value="";
            document.exeform.bankaccount.value="";
            document.exeform.bankuser.value="";
            document.exeform.rfee.value=rfee;

            $(obj).parent().parent().find(".pg_cancel_btn").html("PG사와 통신중 입니다. 잠시만 기다려 주세요.");
            $.post("<?=$Dir?>paygate/" + pg_type + "/cancel.ajax.php",{ordercode:ordercode,pc_type:pc_type,mod_mny:each_price},function(data){
                //alert(data.type+"\n\n"+data.msg);
                $(obj).parent().parent().find(".pg_cancel_btn").html("처리중 입니다. 잠시만 기다려 주세요.");
                if(data.type == 1){	
                    document.exeform.mode.value="pgcancel";
                    document.exeform.target="processFrame";
                    document.exeform.submit();
                } else {
                    alert(data.msg);
                }
            },"json");
        }

    } else {

<?php if($pg_type=="A"){?>
	if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {

		document.exeform.oc_no.value=oc_no;
		document.exeform.ordercode.value=ordercode;
		document.exeform.idxs.value="";
		document.exeform.bankcode.value="";
		document.exeform.bankaccount.value="";
		document.exeform.bankuser.value="";
		document.exeform.rfee.value=rfee;

		sitecd = "<?=$pgid_info['ID']?>";
		sitekey = "<?=$pgid_info['KEY']?>";	
		$(obj).parent().parent().find(".pg_cancel_btn").html("PG사와 통신중 입니다. 잠시만 기다려 주세요.");
		$.post("<?=$Dir?>paygate/A/cancel.ajax.php",{sitecd:sitecd, sitekey:sitekey, ordercode:ordercode, pc_type:pc_type,mod_mny:each_price},function(data){
			//alert(data.type+"\n\n"+data.msg);
			$(obj).parent().parent().find(".pg_cancel_btn").html("처리중 입니다. 잠시만 기다려 주세요.");
			if(data.type == 1){	
				document.exeform.mode.value="pgcancel";
				document.exeform.target="processFrame";
				document.exeform.submit();
			} else {
				alert(data.msg);
			}
		},"json");
	}
<?php }elseif($pg_type=="B"){?>
<?php }elseif($pg_type=="C"){?>
<?php }elseif($pg_type=="D"){?>
<?php }?>

    }
}

function pg_cancel_reg(obj , oc_no, ordercode) {
	var tprice					= parseInt($(obj).parent().parent().find("input[name='tprice']").val());
	var rfee						= parseInt($(obj).parent().parent().find("input[name='rfee']").val());
	var bankcode				= $(obj).parent().parent().find("select[name='bankcode'] option:selected").val();
	var bankaccount			= $(obj).parent().parent().find("input[name='bankaccount']").val();
	var bankuser				= $(obj).parent().parent().find("input[name='bankuser']").val();
	var each_price	= tprice - rfee;

	if (bankcode == '7' || bankcode == '10') {
		alert('해당은행은 가상계좌(매매보호) 환불은행으로 사용할 수 없습니다.');
		return;
	}
	
	if(!confirm("최종 환불금액은 "+comma(each_price)+"원 입니다.\n계속진행 하시겠습니까?")) {
		return;
	}

	if(confirm("취소처리 후 다시 되돌릴 수 없습니다.\n\n정말 취소처리를 하시겠습니까?")) {

		document.exeform.oc_no.value=oc_no;
		document.exeform.ordercode.value=ordercode;
		document.exeform.idxs.value="";
		document.exeform.bankcode.value=bankcode;
		document.exeform.bankaccount.value=bankaccount;
		document.exeform.bankuser.value=bankuser;
		document.exeform.rfee.value=rfee;

		$(obj).parent().parent().find(".pg_cancel_btn").html("처리중 입니다. 잠시만 기다려 주세요.");

		document.exeform.mode.value="pgcancel_reg";
		document.exeform.target="processFrame";
		document.exeform.submit();
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>환불접수 내역 관리</span></p></div></div>
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
			<?php include("menu_order.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">환불접수 내역 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>환불접수 내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
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
							</td>
                        </TR>

                        <TR>
							<th><span>결제타입</span></th>
							<TD class="td_con1">
<?php
							$arrPaymethod=array("B:무통장입금","CA:신용카드","VA:계좌이체","OA:가상계좌","MA:휴대폰","QA:가상계좌(매매보호)");
							for($i=0;$i<count($arrPaymethod);$i++) {
								$tmpPaymethod=explode(":",$arrPaymethod[$i]);
								$selPaymethod='';
								if(in_array($tmpPaymethod[0],$paymethod_arr)>0)$selPaymethod="checked";
?>
								<input type="checkbox" name="paymethod[]" value="<?=$tmpPaymethod[0]?>" <?=$selPaymethod?>><?=$tmpPaymethod[1]?>
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
		$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰","Y"=>"PAYCO");

		$sql = "SELECT  toc.*, a.id, a.sender_name, a.paymethod, a.oldordno ";
		if($vendercnt>0) $sql.= ", b.vender ";
        $sql.= "FROM {$qry_from} {$qry} ";
		$sql.= "ORDER BY toc.oc_no {$orderby} ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());
         echo "sql = ".$sql."<br>";
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
				<col width=></col>
				<?php if($vendercnt>0){?>
				<col width=150></col>
				<?php }?>
				<col width=150></col>
				<col width=200></col>
				<col width=150></col>
				<col width=100></col>			
				<TR >
					<th></th>
					<th>주문일자</th>
					<th>주문취소일</th>
					<th>주문번호</th>
					<?php if($vendercnt>0){?>
					<th>브랜드</th>
					<?php }?>
					<th>주문자</th>
					<th>아이디</th>
					<th>취소수량/주문수량</th>
                    <th>결제수단</th>
				</TR>

<?php
		$colspan=8;
		if($vendercnt>0) $colspan++;

		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

			if ($row->rfindt) {
				$chk_dis	= " disabled=true"; 
				$sel_dis		= " disabled=true"; 
				$input_dis	= " readonly"; 
				$tr_bgc		= "#EFEFEF";
				$in_bg		= "#EFEFEF";
			} else {
				if ($row->pgcancel == 'Y') {
					$chk_dis	= ""; 
					$sel_dis		= " disabled=true"; 
					$input_dis	= " readonly"; 
					$in_bg		= "#EFEFEF";
				} else if ($row->pgcancel == 'R') {
					$chk_dis	= " disabled=true"; 
					$sel_dis		= " disabled=true"; 
					$input_dis	= " readonly"; 
					$in_bg		= "#EFEFEF";
				} else {
					$chk_dis	= ""; 
					$sel_dis		= ""; 
					$input_dis	= ""; 
					$in_bg		= "#E9FFB3";
				}
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

			list($oc_count)=pmysql_fetch("SELECT count(*) as oc_count from tblorderproduct WHERE oc_no='{$row->oc_no}' ");			
			list($tot_count)=pmysql_fetch("SELECT count(*) as tot_count from tblorderproduct WHERE ordercode = '{$row->ordercode}' ");			
?>
			<tr bgcolor=<?=$tr_bgc?>>
			<td align="center" style='border:1px solid <?=$tr_bgc?>'><input type=checkbox name="chk_oc_no" value="<?=$row->oc_no?>"<?=$chk_dis?>><input type=hidden name="ordercode" value="<?=$row->ordercode?>"></td>
			<td align="center"style="font-size:8pt;padding:3;line-height:11pt;border:1px solid <?=$tr_bgc?>"><?=$date?></td>
			<td align="center"style="font-size:8pt;padding:3;line-height:11pt;border:1px solid <?=$tr_bgc?>"><?=$regdate?></td>
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
			<td align="center" style='border:1px solid <?=$tr_bgc?>'><?=$oc_count?>/<?=$tot_count?></td>
			<td align="center" style='border:1px solid <?=$tr_bgc?>'><?=$arpm[$row->paymethod[0]]?><input type=hidden name="payment" value="<?=$row->paymethod[0]?>"><input type=hidden name="pgcancel" value="<?=$row->pgcancel?>"></td>
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
			SUM( price ) AS sum_price, 
			SUM( quantity ) AS sum_qnt, 
			SUM(option_price * option_quantity) AS sum_opt_price, 
			SUM(coupon_price) AS sum_coupon, 
			SUM(use_point) AS sum_use_point,
			SUM(deli_price) AS sum_deli_price,
			SUM( ((price + option_price) * option_quantity) - coupon_price - use_point + deli_price ) AS sum_totprice

			FROM tblorderproduct WHERE oc_no='{$row->oc_no}' ";
			$sql.=" GROUP BY idx, productcode, vender, productname";
			//echo $sql;
			$result2=pmysql_query($sql,get_db_conn());
			$re_tot_price	=0;
			$idxs	= "";

			while($row2=pmysql_fetch_object($result2)) {
				if ($idxs == "") {
					$idxs	.= $row2->idx;
				} else {
					$idxs	.= "|".$row2->idx;
				}
?>
				<tr bgcolor=<?=$tr_bgc?>>
					<td align="center" style='text-align:left;padding-left:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>
					<span style="line-height:10pt">
					<?=strip_tags($row2->productname)?> 
					<span class="page_screen">
						<a href="/front/productdetail.php?productcode=<?=$row2->productcode?>" target="_blank">
							<b>[보기]</b>
						</a>
					</span>
					</span></td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_price)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_qnt)?>개</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_opt_price)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_coupon)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_use_point)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_deli_price)?>원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><?=number_format($row2->sum_totprice)?>원</td>
				</tr>		
<?php
				$re_tot_price = $re_tot_price + $row2->sum_totprice;
			}
			pmysql_free_result($result2);
?>
				<input type=hidden name=idxs value='<?=$idxs?>'>
				<tr bgcolor=#EFEFEF>
					<td align="center" style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>환불예정금액(상품결제단가)</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>환불계좌 정보</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>환불 수수료</td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2>최종 환불금액 <font size=1>(실결제금액 - 환불수수료)</font></td>
					<td align="center" style='border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=3>취소처리</td>
				</tr>			
				<tr bgcolor=#FEF8ED>
					<td align="center" style='border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'><b><?=number_format($re_tot_price)?>원</b><input type=hidden name="tprice" value="<?=$re_tot_price?>"></td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;'>
						<select name=bankcode class="select" style='background:<?=$in_bg?>;'<?=$sel_dis?>>
                        <option value="">==== 은행선택 ====</option>
<?php
                        foreach($oc_bankcode as $key => $val) {
                            echo "<option value=\"{$key}\"";
                            if($row->bankcode==$key) echo " selected";
                            echo ">{$val}</option>\n";
                        }
?>
						</select> <input class="input" type="text" name="bankaccount" style='border: 1px solid #cbcbcb;width:220px;background:<?=$in_bg?>;' value='<?=$row->bankaccount?>'<?=$input_dis?>> / 예금주 <input class="input" type="text" name="bankuser" style='border: 1px solid #cbcbcb;width:100px;background:<?=$in_bg?>;' value='<?=$row->bankuser?>'<?=$input_dis?>></td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=2><input class="input" type="text" name="rfee" style='border: 1px solid #cbcbcb;width:150px;background:<?=$in_bg?>;' value='<?=$row->rfee?>' onKeyPress="return numkeyCheck(event)"  onchange="rpiceCheck(this)"<?=$input_dis?> > 원</td>
					<td align="right" style='text-align:right;padding-right:5px;border-left:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;color:red;font-weight:bold;' colspan=2><span class='rpice'><?if ($row->rprice > 0) { echo number_format($row->rprice); } else { echo number_format($re_tot_price - $row->rfee);}?></span>원</td>
					<td align="center" style='text-align:center;padding-right:5px;border-left:1px solid #cbcbcb;border-right:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb;' colspan=3 class="pg_cancel_btn">
<?php
					if ($row->paymethod[0] == 'C') {		// 카드결제일 경우
						if ($row->oldordno == '') {
							if($oc_count == $tot_count) {			// 전체취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'ALL');"><b>[카드결제 전체취소]</b></a>
<?php							
								} else if ($row->pgcancel == 'Y') {
									echo "전체취소 완료";
								}
							} else {										// 부분취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'PART');"><b>[카드결제 부분취소]</b></a>
<?php
								} else if ($row->pgcancel == 'Y') {
									echo "부분취소 완료";
								}
							}
						} else {// 교환 재주문 상품일 경우
							echo "<font color='red'>교환 재주문 상품입니다.</font> <a href ='https://testadmin8.kcp.co.kr/' target='_blank'><b>[PG 관리자]</b></a>";
						}
					} else if ($row->paymethod[0] == 'Q') {		// 가상계좌(매매보호)결제일 경우
							$q_idx	= explode("|", $idxs);
							//취소하려는 상품의 상태값을 가져온다.
							list($q_deli_gbn, $q_op_step, $q_pickup_state)=pmysql_fetch_array(pmysql_query("select op.deli_gbn, op.op_step, oc.pickup_state from tblorderproduct op left join tblorder_cancel oc on op.oc_no=oc.oc_no WHERE op.ordercode='".$row->ordercode."'AND op.idx = '".$q_idx[0]."'"));

							//취소하려는 상태값과 다른 카운트를 체크한다.
							list($op_deli_gbn_cnt)=pmysql_fetch_array(pmysql_query("select count(op.deli_gbn) as op_deli_gbn_cnt from tblorderproduct op left join tblorder_cancel oc on op.oc_no=oc.oc_no WHERE op.ordercode='".$row->ordercode."'AND op.idx NOT IN ('".str_replace("|", "','", $idxs)."') AND op.deli_gbn != '{$q_deli_gbn}' AND op.op_step != '{$q_op_step}' AND oc.pickup_state != '{$q_pickup_state}'"));
							
							if($oc_count == $tot_count || $op_deli_gbn_cnt ==0) {			// 전체취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel_reg(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>');"><b>[취소요청]</b></a>
<?php							
								} else if ($row->pgcancel == 'R') {
									echo "환불대기 (환불완료후 자동 취소처리)";
								} else if ($row->pgcancel == 'Y') {
									echo "취소 완료";
								}
							} else {
								echo "가상계좌(매매보호) 부분취소 상품입니다.";
							}
					} else if ($row->paymethod[0] == 'M') {		// 핸드폰결제인 경우
						if ($row->oldordno == '') {
							if($oc_count == $tot_count) {			// 전체취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'ALL', 'E');"><b>[핸드폰결제 전체취소]</b></a>
<?php							
								} else if ($row->pgcancel == 'Y') {
									echo "전체취소 완료";
								}
							} else {										// 부분취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'PART', 'E');"><b>[핸드폰결제 부분취소]</b></a>
<?php
								} else if ($row->pgcancel == 'Y') {
									echo "부분취소 완료";
								}
							}
						} else {// 교환 재주문 상품일 경우
							echo "<font color='red'>교환 재주문 상품입니다.</font> <a href ='https://testadmin8.kcp.co.kr/' target='_blank'><b>[PG 관리자]</b></a>";
						}
					} else if ($row->paymethod[0] == 'Y') {		// PAYCO결제인 경우
						if ($row->oldordno == '') {
							if($oc_count == $tot_count) {			// 전체취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'ALL', 'F');"><b>[PAYCO결제 전체취소]</b></a>
<?php							
								} else if ($row->pgcancel == 'Y') {
									echo "전체취소 완료";
								}
							} else {										// 부분취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'PART', 'F');"><b>[PAYCO결제 부분취소]</b></a>
<?php
								} else if ($row->pgcancel == 'Y') {
									echo "부분취소 완료";
								}
							}
						} else {// 교환 재주문 상품일 경우
							echo "<font color='red'>교환 재주문 상품입니다.</font> <a href ='https://testadmin8.kcp.co.kr/' target='_blank'><b>[PG 관리자]</b></a>";
						}
					} else if ($row->paymethod[0] == 'V') {		// 계좌이체결제일 경우
						if ($row->oldordno == '') {
							if($oc_count == $tot_count) {			// 전체취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'ALL');"><b>[계좌이체결제 전체취소]</b></a>
<?php							
								} else if ($row->pgcancel == 'Y') {
									echo "전체취소 완료";
								}
							} else {										// 부분취소시
								if ($row->pgcancel == 'N') {
?>
								<a href="javascript:;" onClick="javascript:pg_cancel(this, '<?=$row->oc_no?>', '<?=$row->ordercode?>', 'PART');"><b>[계좌이체결제 부분취소]</b></a>
<?php
								} else if ($row->pgcancel == 'Y') {
									echo "부분취소 완료";
								}
							}
						} else {// 교환 재주문 상품일 경우
							echo "<font color='red'>교환 재주문 상품입니다.</font> <a href ='https://testadmin8.kcp.co.kr/' target='_blank'><b>[PG 관리자]</b></a>";
						}
					} else { // 그외
						echo "카드결제건이 아닙니다.";
					}

?>					
					</td>
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
				<td style="padding-top:4pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<col width=130></col>
				<col width=></col>
				<col width=130></col>
				<tr>
					<td align='left'><a href="javascript:OrderRefundSubmit();"><img src="images/btn_refund.gif" border="0" hspace="0"></a></td>
					<td align='center'>
					<table cellpadding="0" cellspacing="0" width="100%">
<?php				
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
					</table></td>
					<td align='right'><a href="javascript:OrderRefundExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>
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
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=checkexcelform action="order_excel_refund.php" method=post>
			<input type=hidden name=oc_no>
			</form>

			<form name=exeform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=ordercode>
			<input type=hidden name=mode>
			<input type=hidden name=coupon_status>
			<input type=hidden name=oc_no>
			<input type=hidden name=idxs>
			<input type=hidden name=bankcode>
			<input type=hidden name=bankaccount>
			<input type=hidden name=bankuser>
			<input type=hidden name=rfee>
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
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<iframe name="processFrame" src="about:blank" width="0" height="0" scrolling=no frameborder=no></iframe>
<?=$onload?>
<?php 
include("copyright.php");
