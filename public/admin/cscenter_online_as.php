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

#---------------------------------------------------------------
# 변수를 정리한다.
#---------------------------------------------------------------

	# 기간검색
	$CurrentTime	= time();
	$period[0]		= date("Y-m-d",$CurrentTime);
	$period[1]		= date("Y-m-d",$CurrentTime-(60*60*24*7));
	$period[2]		= date("Y-m-d",$CurrentTime-(60*60*24*14));
	$period[3]		= date("Y-m-d",strtotime('-1 month'));
	$period[4]		= substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);

	$mode=$_POST["mode"];

	$s_check		= $_GET["s_check"]; //키워드
	$search			= trim($_GET["search"]); //검색
	$as_type			= trim($_GET["as_type"]); //구분
	$progress_type  = $_GET["progress_type"]; //진행상태
	$search_start		= $_GET["search_start"]; // 시작기간
	$search_end		= $_GET["search_end"]; // 종료기간
	$s_prod				= $_GET["s_prod"]; // 접수매장
	$paymethod		= $_GET["paymethod"]; // 결제수단
	$ord_flag			= $_GET["ord_flag"]; // 주문방법
	$brandname		= $_GET["brandname"];  // 벤더이름
	$progress_code = $_GET["progress_code"];
	

	if ($paymethod[0] == '') {
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
	if(is_array($progress_type)) $progress_type = implode("','",$progress_type);

	$paymethod_arr	= explode("','",$paymethod);
	$ord_flag_arr		= explode("','",$ord_flag);
	$progress_type_arr		= explode("','",$progress_type);


	$search_start	= $search_start?$search_start:$period[1];
	$search_end	= $search_end?$search_end:date("Y-m-d",$CurrentTime);
	$search_s		= $search_start?str_replace("-","",$search_start."000000"):"";
	$search_e		= $search_end?str_replace("-","",$search_end."235959"):"";

#---------------------------------------------------------------
# 검색조건을 정리한다.
#---------------------------------------------------------------

	//tblcsasreceiptinfo
	$column[]="c.no, c.regdt, c.as_code, c.as_type, c.receipt_store, c.complete_type, c.step_code, c.end_step, c.cash_type, c.complete_cost, c.complete_delinumber, c.complete_delicode";
	$table[]="tblcsasreceiptinfo c";

	//tblorderinfo
	$column[]="o.id, o.paymethod, o.ordercode, o.receiver_name, o.receiver_tel2, o.receiver_addr, o.oi_step2, o.oi_step1, o.sender_name, o.sender_email, o.sender_tel";
	$table[]="left join tblorderinfo o on(c.as_ordercode=o.ordercode)";

	//tblorderproduct
	$column[]="op.deli_gbn,  op.opt2_name, op.op_step, op.redelivery_type, op.order_conf, op.price, op.quantity, op.deli_num, op.deli_com";
	$table[]="left join tblorderproduct op on (o.ordercode=op.ordercode and op.idx=c.as_idx)";

	//tblproduct
	$column[]="p.consumerprice, p.tinyimage, p.productname, p.productcode, p.prodcode, p.colorcode";
	$table[]="left join tblproduct p on(op.productcode=p.productcode)";

	//tblproductbrand
	$column[]="pb.brandname";
	$table[]="left join tblproductbrand pb on (p.brand=pb.bridx)";

	//tblstore
	$column[]="s.name as storename";
	$table[]="left join tblstore s on (c.receipt_store=s.sno::varchar)";

	$where="";
	$where[]= "WHERE deltype='N'";

	//현제상태 검색(탭)
	if($progress_code){
		$where_tap=" and c.step_code='".$progress_code."'";
	}

	// 키워드검색
	if(ord($search)) {
		if($s_check=="on") $where[]= "o.sender_name like '%{$search}%' ";
		else if($s_check=="sn") $where[]= "o.receiver_name like '%{$search}%' ";
		else if($s_check=="ar") $where[]= "c.as_code like '%{$search}%' ";
		else if($s_check=="or") $where[]= "o.ordercode like '%{$search}%' ";
		else if($s_check=="co") $where[]= "op.productcode like '%{$search}%' ";
		else if($s_check=="mo") $where[]= "o.sender_tel = '{$search}' ";
		else if($s_check=="pn") $where[]= "p.productname = '{$search}' ";

		else if($s_check=="al") {
			$or_qry[] = " o.sender_name like '%{$search}%' ";
			$or_qry[] = " o.receiver_name like '%{$search}%' ";
			$or_qry[] = " c.as_code like '%{$search}%' ";
			$or_qry[] = " o.ordercode like '%{$search}%' ";
			$or_qry[] = " op.productcode like '%{$search}%' ";
			$or_qry[] = " o.sender_tel = '{$search}' ";
			$or_qry[] = " p.productname = '{$search}' ";
			$where[]= " AND ( ".implode( ' OR ', $or_qry )." ) ";
		}
	}

	//구분
	if($as_type) $where[]= "c.as_type='".$as_type."'";

	// 결제수단
	if(ord($progress_type))	$where[]= " c.step_code in ('".$progress_type."') ";

	// 기간선택 조건
	if ($search_s != "" || $search_e != "") {
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$where[]= "c.regdt LIKE '".substr($search_s,0,8)."%' ";
		} else {
			$where[]= "c.regdt>='{$search_s}' AND c.regdt <='{$search_e}' ";
		}
	}

	// 접수매장
	if($s_prod) $where[]= " c.receipt_store = '".$s_prod."'";

	// 결제수단
	if(ord($paymethod))	$where[]= " SUBSTRING(o.paymethod,1,1) in ('".$paymethod."') ";

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
		 $where[]= " o.is_mobile in ('".implode("','",$chk_mb)."') ";
	}

	$query="select ".implode(", ",$column)." from ".implode(" ", $table)." ".implode(" and ", $where)." ".$where_tap."  order by c.regdt desc";



	if($mode=="delete" && $_POST["receipt_no"]) {	//as신청서 삭제
		
		$receipt_no=$_POST["receipt_no"];
		pmysql_query("update tblcsasreceiptinfo set deltype='Y' WHERE no ='".$receipt_no."'",get_db_conn());

	}

	include("header.php");

	$sql="select count(c.no) from ".implode(" ", $table)." ".implode(" and ", $where)." ".$where_tap;
	//$paging = new Paging($sql,10,20);
	//exdebug($sql);

	$paging = new newPaging($sql,10,20,'GoPage');
	$t_count = $paging->t_count;
	$gotopage = $paging->gotopage;


	$excel_sql = "select 
	c.no, c.as_type, c.as_ordercode as ordercode, c.regdt, c.as_code, o.paymethod, c.receipt_type, c.depreciation_type, c.requests_text, c.complete_delinumber, c.delivery_cost, c.delivery_receipt, c.step_code, c.complete_type, c.complete_detail, c.complete_cost, c.cash_detail_type, c.repairs_type, c.cash_type, c.cash_detail_num, c.complete_store, c.c_reviewreturn, c.place_type, c.place_addr, c.place_zipcode, 
	o.sender_name, o.sender_tel, o.receiver_addr, 
	op.store_code, op.opt2_name, op.quantity, op.price, op.coupon_price,
	s.name as storename,
	p.brand, p.productname, p.productcode, p.prodcode, p.colorcode

	from ".implode(" ", $table)." ".implode(" and ", $where)." ".$where_tap;
	$excel_sql_orderby = "order by c.regdt desc";


	#매장정보 가져오기
	$store_sql="select * from tblstore order by name";
	$store_result=pmysql_query($store_sql);

	# 배송업체를 불러온다.
	$del_sql="SELECT * FROM tbldelicompany ORDER BY company_name ";
	$del_result=pmysql_query($del_sql,get_db_conn());
	$delicomlist=array();
	while($del_data=pmysql_fetch_object($del_result)) {
		$delicomlist[trim($del_data->code)]=$del_data;
	}

	#체크 처리
	if ($progress_type_arr[0] == '') {
		foreach($as_progress as $checkap=>$checkapp){
			$checked["progress"][$checkap]="checked";
		}
	}else{

		foreach($progress_type_arr as $checkap=>$checkapp){
			$checked["progress"][$checkapp]="checked";
		}
	}

	#접수 건수 구하기
	list($count_progress_1)=pmysql_fetch("select count(c.no) from ".implode(" ", $table)." ".implode(" and ", $where)." and c.step_code='progress_a01'");
	#제품도착 건수 구하기
	list($count_progress_2)=pmysql_fetch("select count(c.no) from ".implode(" ", $table)." ".implode(" and ", $where)." and c.step_code='progress_a02'");
	#수신처발송 건수 구하기
	list($count_progress_3)=pmysql_fetch("select count(c.no) from ".implode(" ", $table)." ".implode(" and ", $where)." and c.step_code='progress_a03'");

	$selected[s_check][$s_check]		= 'selected';
	$selected["s_prod"][$s_prod] = 'selected';

	$checked["gubun"][""]="checked";


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
	document.form1.action="cscenter_online_as.php";
    document.form1.method="GET";
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

function OrderExcel() {

	//window.open("about:blank","excelselpop","width=350,height=350,scrollbars=no");
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

function progress_chage(p_code){
	document.form1.action="cscenter_online_as.php";
	document.form1.progress_code.value=p_code;
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
<div class="admin_linemap"><div class="line"><p>현재위치 : CS관리  &gt; CS관리 &gt;<span>온라인 AS</span></p></div></div>

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
					<div class="title_depth3">온라인 AS</div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					&nbsp;
<!--					<div class="title_depth3_sub"></span></div>-->
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=progress_code id=progress_code>
			<tr>
				<td>

					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<th><span>키워드검색</span></th>
							<TD class="td_con1">
                                <select name="s_check" class="select">
                                    <!--<option value="al" <?=$selected[s_check]["al"]?>>전체</option>-->
									<option value="on" <?=$selected['s_check']["on"]?>>고객명</option>
									<option value="sn" <?=$selected['s_check']["sn"]?>>수령자</option>
									<option value="ar" <?=$selected['s_check']["ar"]?>>접수번호</option>
									<option value="or" <?=$selected['s_check']["or"]?>>주문번호</option>
									<option value="co" <?=$selected['s_check']["co"]?>>상품코드</option>
									<option value="mo" <?=$selected['s_check']["mo"]?>>휴대폰번호</option>
									<option value="pn" <?=$selected['s_check']["pn"]?>>제품명</option>
                                </select>
							    <input type=text name=search value="<?=$search?>" style="width:197" class="input">
                            </TD>
						</tr>

						<TR>
							<th><span>구분</span></th>
							<td>
								<input type="radio" name="as_type" value="" <?=$checked["gubun"][$ag]?>>전체&nbsp;
                                <?foreach($as_gubun as $ag=>$agv){?>
									<input type="radio" name="as_type" value="<?=$ag?>" <?=$checked["gubun"][$ag]?>><?=$agv?>&nbsp;
								<?}?>
							</td>
						</TR>

						<TR>
							<th><span>진행상태<font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='as_progress' name="progress_type_all" <?if(count($checked["progress"]) == count($as_progress)) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></span></th>
							<td>
								<!-- 퍼블 폼 추가 -->
								<div class="form-set-wrap">
									<ul class="form-list">
										<?foreach($as_progress_sort as $ap=>$apv){?>
										<li>
											<span><?=$ap?></span>
											<div class="radio-set">
												<?foreach($apv as $app=>$appv){?>
													<input type="checkbox" name="progress_type[]" id="radio<?=$app?>" class="as_progress" value="<?=$appv?>" <?=$checked["progress"][$appv]?>><label for="radio<?=$app?>"><?=$as_progress[$appv]?></label>&nbsp;
												<?}?>
											</div>
										</li>
										<?}?>
										<!--
										<li>
											<span>기본</span>
											<div class="radio-set">
												<input id="radio01" type="radio" name="" value="" checked="">
												<label for="radio01">AS접수</label>
												<input id="radio02" type="radio" name="" value="">
												<label for="radio02">제품도착</label>
												<input id="radio03" type="radio" name="" value="">
												<label for="radio03">수선처 발송</label>
												<input id="radio04" type="radio" name="" value="">
												<label for="radio04">회송</label>
											</div>
										</li>
										<li>
											<span>수선</span>
											<div class="radio-set">
												<input id="radio05" type="radio" name="" value="" checked="">
												<label for="radio05">수선중</label>
												<input id="radio06" type="radio" name="" value="">
												<label for="radio06">수선완료</label>
												<input id="radio07" type="radio" name="" value="">
												<label for="radio07">고객발송</label>
											</div>
										</li>
										<li>
											<span>심의</span>
											<div class="radio-set">
												<input id="radio08" type="radio" name="" value="" checked="">
												<label for="radio08">심의중</label>
												<input id="radio09" type="radio" name="" value="">
												<label for="radio09">AS반품</label>
												<input id="radio10" type="radio" name="" value="">
												<label for="radio10">교환처리</label>
												<input id="radio11" type="radio" name="" value="">
												<label for="radio11">반품처리</label>
												<input id="radio12" type="radio" name="" value="">
												<label for="radio12">심의회송</label>
											</div>
										</li>
										<li>
											<span>외부심의</span>
											<div class="radio-set">
												<input id="radio13" type="radio" name="" value="" checked="">
												<label for="radio13">외부심의중</label>
												<input id="radio14" type="radio" name="" value="">
												<label for="radio14">외부심의반품</label>
												<input id="radio15" type="radio" name="" value="">
												<label for="radio15">반품처리</label>
												<input id="radio16" type="radio" name="" value="">
												<label for="radio16">반품등록</label>
												<input id="radio17" type="radio" name="" value="">
												<label for="radio18">로케이션이동</label>
												<input id="radio19" type="radio" name="" value="">
												<label for="radio19">외부심의회송</label>
											</div>
										</li>
										-->
									</ul>
								</div>
								<!-- // 퍼블 폼 추가 -->

								<!-- //기존소스 주석 <table>
								<?foreach($as_progress_sort as $ap=>$apv){?>
								<tr>
									<th><?=$ap?></th>
									<td>
										<?foreach($apv as $app=>$appv){?>
											<input type="checkbox" name="progress_type[]" class="as_progress" value="<?=$appv?>" <?=$checked["progress"][$appv]?>><?=$as_progress[$appv]?>&nbsp;
										<?}?>
									</td>
								</tr>
								<?}?>
								</table> // 기존소스 주석-->
							</td>
						</TR>
<!--
						<TR>
							<th><span>처리이력<font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" value="<?=$k?>" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></span></th>
							<td>

                                <?foreach($as_progress as $ap=>$apv){?>
									<input type="checkbox" name="as_progress" value="<?=$ap?>" <?=$checked["gubun"][$ag]?>><?=$apv?>&nbsp;
								<?}?>
							</td>
						</TR>
-->
						<TR>
							<th><span>기간</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
						</TR>

						<tr>
							<th><span>접수매장</span></th>
							<TD class="td_con1">
                                <select name="s_prod" class="select">
									<option value="">전체</option>
                                    <?while($store_data=pmysql_fetch_array($store_result)){?>
										<option value="<?=$store_data["sno"]?>" <?=$selected["s_prod"][$store_data["sno"]]?>><?=$store_data["name"]?></option>
									<?}?>
                                </select>

                            </TD>
						</tr>



                        <TR>
							<th><span>결제수단</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_paymethod' name="paymethod_all" value="<?=$k?>" <?if(count($paymethod_arr) == count($arpm)) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
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

                        <TR>
							<th><span>주문방법</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" value="<?=$k?>" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="PC" <?=(in_array('PC',$ord_flag_arr)?'checked':'')?>>PC</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="MO" <?=(in_array('MO',$ord_flag_arr)?'checked':'')?>>MOBILE</input>
                                <input type="checkbox" class='chk_ord_flag' name="ord_flag[]" value="AP" <?=(in_array('AP',$ord_flag_arr)?'checked':'')?>>APP</input>
                            </TD>
						</TR>


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
/*
		$sql="select
			c.regdt, c.as_code, c.as_type, c.receipt_store, c.complete_type, c.step_code, c.end_step, c.cash_type, c.complete_cost, c.complete_delinumber, c.complete_delicode,
			o.id, o.paymethod, o.ordercode, o.receiver_name, o.receiver_tel2, o.receiver_addr, o.oi_step2, o.oi_step1, o.sender_name, o.sender_email, o.sender_tel,
			op.deli_gbn,  op.opt2_name, op.op_step, op.redelivery_type, op.order_conf, op.price, op.quantity, op.deli_num, op.deli_com,
			p.consumerprice, p.tinyimage, p.productname,
			pb.brandname,
			s.name as storename
			from
			tblcsasreceiptinfo c
			left join tblorderinfo o on(c.as_ordercode=o.ordercode)
			left join tblorderproduct op on (o.ordercode=op.ordercode and op.idx=c.as_idx)
			left join tblproduct p on(op.productcode=p.productcode)
			left join tblproductbrand pb on (p.brand=pb.bridx)
			left join tblstore s on (c.receipt_store=s.sno::varchar)
			order by c.regdt desc
			";
			*/
		$query = $paging->getSql($query);
		$result=pmysql_query($query,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);

///echo $sql;
		$colspan=17;
?>
				<!-- [D] 20161006 박스추가 -->
				<div class="section-box">
					<ul class="aslist">
						<li><a href="javascript:progress_chage('progress_a01');">접수 : <strong><?=$count_progress_1?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('progress_a02');">제품도착 : <strong><?=$count_progress_2?></strong> 건</a></li>
						<li><a href="javascript:progress_chage('progress_a03');">수선처발송 : <strong><?=$count_progress_3?></strong> 건</a></li>
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
				<!--<col width=40></col>-->
				<col width=80></col>
				<col width=80></col>
				<col width=120></col>
				<col width=80></col>
				<col width=80></col>
				<col width=130></col>
				<col width=100></col>
				<col width=100></col>
				<col width=></col>
				<col width=90></col>
				<col width=90></col>
				<col width=60></col>
				<col width=100></col>
				<col width=70></col>
				<col width=60></col>
				<col width=60></col>
				<col width=60></col>
				<input type=hidden name=chkordercode>

				<TR >
					<!--<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>-->
					<th>접수일</th>
					<th>접수번호</th>
					<th>AS구분</th>
					<th>주문번호</th>
					<th>결제</th>
					<th>접수매장</th>
					<th>고객명</th>
					<th colspan=2>AS요청상품</th>
					<th>처리내용</th>
                    <th>진행상태</th>
					<th>처리결과</th>
					<th>출고송장</th>
					<th>유상수선비</th>
					<th>현금<BR>영수증</th>
					<th>CS처리</th>
					<th>삭제</th>
				</TR>

<?php

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {

			$insdate=substr($row->regdt,'0','4')."-".substr($row->regdt,'4','2')."-".substr($row->regdt,'6','2');

			if(substr($row->ordercode,20)=="X") {	//비회원
				$stridM = $row->sender_name."&nbsp;(<FONT COLOR=\"blue\" style='font-size:12px;'>비회원</FONT>) / 주문번호: ".substr($row->id,1,6);
			} else {	//회원
				$stridM = "<a href=\"javascript:CrmView('$row->id');\"><FONT COLOR=\"blue\">{$row->sender_name}</FONT>&nbsp;<FONT style='font-size:12px;'>({$row->id})</FONT></a>";
			}


			$product_img = getProductImage($Dir.DataDir.'shopimages/product/', $row->tinyimage);

			if($row->end_step) $end_step="완료";
			else $end_step="대기";

			$erp_pc_code	= "&nbsp;&nbsp;[".$row->prodcode."-".$row->colorcode."]";

?>
			    <tr bgcolor=<?=$thiscolor?>>
			        <!--<td align="center"><input type=checkbox name=chkordercode value="<?=$row->ordercode?>"></td>-->
                    <td align="center"><?=$insdate?></td>
                    <td align="center"><?=$row->as_code?></td>
                    <td align="center"><?=$as_gubun[$row->as_type]?></td>
                    <td align="center"><A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$row->ordercode?></A></td>
			        <td align="center"><?=$arpm[$row->paymethod[0]]?></td>
                    <td align="center"><?=$row->storename?></td>
                    <td align="center"><?=$stridM?></td>
                    <td style="text-align:right;padding:3"><a href="javascript:ProductDetail('<?=$row->productcode?>')"><img src="<?=$product_img?>" alt="" style="width:70px"></a></td>
					<td style="text-align:left;padding:3"><a href="javascript:ProductDetail('<?=$row->productcode?>')"><strong><?=$row->brandname?></strong><br><?=$row->productname?><?=$erp_pc_code?></a></td>
                    <td align=center><?=$as_complete[$row->complete_type]?></td>
			        <td align=center><?=$as_progress[$row->step_code]?></td>
                    <td align=center><?=$end_step?></td>

                    <td align=center style="padding:3"><?=$delicomlist[$row->complete_delicode]->company_name?><br><?=$row->complete_delinumber?></td>
					<td><?=$row->complete_cost?></td>
					<td><?=$row->cash_type?></td>
					<td><input type='button' value='AS처리' onclick="javascript:view_as(<?=$row->no?>);" style='padding:2px 5px 1px'></td>
					<td><input type='button' value='삭제' onclick="javascript:asdel(<?=$row->no?>);" style='padding:2px 5px 1px'></td>
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
				<tr>
					<td align='left' valign=middle>&nbsp;</td>
					<td align='center'>
                    <div id="page_navi01" style='margin:0 0'>
                        <div class="page_navi">
                            <ul>
                                <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                            </ul>
                        </div>
                    </div>
					</td>
					<td align='right' valign=middle>&nbsp;</td>
					<!--<td align='right' valign=middle><a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>-->
					
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
				<input type=hidden name=as_type value="<?=$as_type?>">
				<input type=hidden name=progress_type value="<?=$progress_type?>">
				<input type=hidden name=search_start value="<?=$search_start?>">
				<input type=hidden name=search_end value="<?=$search_end?>">
				<input type=hidden name=s_prod value="<?=$s_prod?>">
				<input type=hidden name=paymethod value="<?=$paymethod?>">
				<input type=hidden name=ord_flag value="<?=$ord_flag?>">
				<input type=hidden name=brandname value="<?=$brandname?>">
				<input type=hidden name=progress_code value="<?=$progress_code?>">
			</form>


			<form name=downexcelform action="cscenter_online_as_excel.php" method=post>
				<input type=hidden name="mode" value="onlineas_list">
				<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
				<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			</form>

            <form name=crmview method="post" action="crm_view.php">
				<input type=hidden name=id>
			</form>

			<form name=delform method="post" action="<?=$_SERVER['PHP_SELF']?>">
				<input type=hidden name='mode' value="delete">
				<input type=hidden name='receipt_no' id='receipt_no'>

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