<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-5";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//exdebug($_POST);
//exdebug($_GET);

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


//$s_check    = $_GET["s_check"];
$search     = trim($_GET["search"]);
$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_GET["brandname"];  // 벤더이름 검색

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

// 검색어
if(ord($search)) {
	$qry.= "AND o.ordercode like '%{$search}%' ";
    //$qry.="and	o.ordercode in ('2016032212123826082A', '2016032212210188149A') ";
}

// 검색어
// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $qry.= " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $qry.= " and v.vender = ".$sel_vender."";
}

//echo "qry = ".$qry."<br>";

$t_price=0;

$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
    $sql = "SELECT  a.vender,a.id,a.com_name,a.delflag, b.bridx, b.brandname 
            FROM    tblvenderinfo a 
            JOIN    tblproductbrand b on a.vender = b.vender 
            ORDER BY lower(b.brandname) ASC
            ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}

include("header.php"); 

$subquery = "
           SELECT o.ordercode, op.idx,
 min(v.brandname) as brandname,
 min(op.store_code) as store_code,
 min(ts.name) as store_name,
 1 as deli_cnt,
 min(o.deli_price) as deli_price,
 min(o.oi_step1) as oi_step1,
 min(o.oi_step2) as oi_step2,
 min(op.op_step) as op_step,
 min(op.redelivery_type) as redelivery_type,
 min(op.order_conf) as order_conf,
 min(op.deli_date) as deli_date
            FROM    tblorderinfo o 
            JOIN    tblorderproduct op on o.ordercode = op.ordercode 
            JOIN    tblproductbrand v on op.vender = v.vender 
            left JOIN tblstore ts on op.store_code=ts.store_code
            WHERE   1=1 
            AND	    op.deli_date >= '{$search_s}' and op.deli_date <= '{$search_e}' 
            AND	    o.oi_step1 in ('1', '2', '3', '4') 
            AND 	(o.oi_step2 >= 0 and o.oi_step2 < 45) 
             AND op.delivery_type IN ('1','2','3')
             AND op.deli_date IS NOT NULL
			AND o.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A')
            ".$qry." 
           GROUP BY op.idx, o.ordercode
        ";


		//exdebug($subquery);
//            AND	    o.id = 'ikazeus'
// 2016-04-18 jhjeong 환불완료시의 수수료는 환불쪽의 쿠폰금액에 포함시켜달라고 함. by 조세진
// sum(op.coupon_price) as op_coupon ==> sum(op.coupon_price + oc.rfee) as op_coupon
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="sales_price_o2o_deli.php";
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	
    if(val < 4) {
	    pForm.search_start.value = period[val];
	    pForm.search_end.value = period[0];
    }else{
	    pForm.search_start.value = '';
	    pForm.search_end.value = '';
    }
}

function OrderExcel() {
    //alert("excel");
	document.form1.action="sales_price_o2o_deli_excel.php";
    document.form1.method="POST";
    //document.form1.target="_blank";
	document.form1.submit();
	document.form1.action="";
}

function OrderExcel2() {
    //alert("excel");
	document.form1.action="sales_price_o2o_deli_report_excel.php";
    document.form1.method="POST";
    //document.form1.target="_blank";
	document.form1.submit();
	document.form1.action="";
}

/*
function OrderDeliPrint() {
	alert("운송장 출력은 준비중에 있습니다.");
}
*/
function OrderCheckPrint() {
	document.printform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.printform.ordercodes.value+=document.form2.chkordercode[i].value.substring(1)+",";
		}
	}
	if(document.printform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
	if(confirm("소비자용 주문서로 출력하시겠습니까?")) {
		document.printform.gbn.value="N";
	} else {
		document.printform.gbn.value="Y";
	}
	document.printform.target="hiddenframe";
	document.printform.submit();
}
/*
function OrderCheckExcel() {
	document.checkexcelform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.checkexcelform.ordercodes.value+=document.form2.chkordercode[i].value+",";
		}
	}
	if(document.checkexcelform.ordercodes.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}
    //document.checkexcelform.target="_blank";
	document.checkexcelform.action="order_excel_all_order.php";
	document.checkexcelform.submit();
}
*/
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 정산관리 &gt; <span>O2O 배송현황 조회</span></p></div></div>

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
					<div class="title_depth3">O2O 배송현황 조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>O2O 배송현황 내역을 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">O2O 배송현황 조회</span></div>
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
							<td>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <!-- <img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)"> -->
							</td>
						</TR>

<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th><span>브랜드</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
<?php
                            foreach($venderlist as $key => $val) {
                                echo "<option value=\"{$val->vender}\"";
                                if($sel_vender==$val->vender) echo " selected";
                                echo ">{$val->brandname}</option>\n";
                            }
?>
                                </select></TD>
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
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><span class="btn-point">조회하기</span></a> &nbsp;<a href="javascript:OrderExcel();"><span class="btn-point blk">검색결과 엑셀 다운로드</span></a> &nbsp;<a href="javascript:OrderExcel2();"><span class="btn-point blk">검색결과 엑셀 다운로드(자료형태)</span></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		$sql = "SELECT 	* 
                FROM
                (
                    ".$subquery."
                ) a 
                ORDER BY deli_date asc, brandname asc, store_code asc, ordercode asc, idx asc
                ";
		$result=pmysql_query($sql,get_db_conn());
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);
?>

<?php
        $cnt = 0;   
		$deli_list	= array();
		while($row=pmysql_fetch_object($result)) {

            $deli_list[$cnt]['ordercode'] = $row->ordercode;
            $deli_list[$cnt]['idx'] = $row->idx;
            $deli_list[$cnt]['brandname'] = $row->brandname;
            $deli_list[$cnt]['store_code'] = $row->store_code;
            $deli_list[$cnt]['store_name'] = $row->store_name;
            $deli_list[$cnt]['deli_cnt'] = $row->deli_cnt;
            $deli_list[$cnt]['deli_price'] = $row->deli_price;
            $deli_list[$cnt]['oi_step1'] = $row->oi_step1;
            $deli_list[$cnt]['oi_step2'] = $row->oi_step2;
            $deli_list[$cnt]['op_step'] = $row->op_step;
            $deli_list[$cnt]['redelivery_type'] = $row->redelivery_type;
            $deli_list[$cnt]['order_conf'] = $row->order_conf;
            $deli_list[$cnt]['deli_date'] = $row->deli_date;
			$cnt++;
		}

		pmysql_free_result($result);

        //$t_count = count($sales);
        $t_count = $cnt;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건<!-- , &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지 --></td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=1 width=100% style="border:1px solid #d1d1d1">
				<col width=180></col>
				<col width=140></col>
				<col width=></col>
				<col width=90></col>
				<col width=80></col>
				<col width=180></col>
				<col width=140></col>
				<col width=80></col>
				<input type=hidden name=chkordercode>
			
				<TR bgcolor="#d1d1d1">
					<td><b>브랜드<b></td>
                    <td><b>매장코드<b></td>
                    <td><b>매장명<b></td>
                    <td><b>배송건수<b></td>
                    <td><b>금액<b></td>
                    <td><b>주문번호<b></td>
                    <td><b>배송일<b></td>
                    <td><b>상태<b></td>
				</TR>
<?
		$colspan=8;
        $i = 0;
        foreach($deli_list as $k => $v) {

            if($i%2) $thiscolor="#ffeeff";
            else $thiscolor="#FFFFFF";
			$i++;
			
			$d	= $v['deli_date'];
			$date = substr($d, 0, 4)."/".substr($d, 4, 2)."/".substr($d, 6, 2)." (".substr($d, 8, 2).":".substr($d, 10, 2).")";
?>

			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$v['brandname']?></td>
                    <td><?=$v['store_code']?></td>
                    <td><?=$v['store_name']?></td>
                    <td><?=number_format($v['deli_cnt'])?></td>
                    <td><?=number_format($v['deli_price'])?></td>
                    <td><?=$v['ordercode']?></td>
                    <td><?=$date?></td>
                    <td><?=GetStatusOrder("p", $v['oi_step1'], $v['oi_step2'], $v['op_step'], $v['redelivery_type'], $v['order_conf'])?></td>
                </tr>
<?
        }
?>

<?
		if($t_count==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<!-- <tr>
				<td style="padding-top:4pt;"><a href="javascript:OrderCheckPrint();"><img src="images/btn_juprint.gif" border="0" hspace="0"></a>&nbsp;<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a></td>
			</tr> -->
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
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=brandname value="<?=$brandname?>">
			</form>

			<form name=printform action="order_print_pop.php" method=post target="ordercheckprint">
			<input type=hidden name=ordercodes>
			<input type=hidden name=gbn>
			</form>

			<form name=checkexcelform action="order_excel_new.php" method=post>
			<input type=hidden name=ordercodes>
			</form>

            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>

			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<!-- <dl>
							<dt><span>배송/입금일별 주문조회</span></dt>
							<dd>
								- 입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.<br>
								- 주문번호를 클릭하면 <b>주문상세내역</b>이 출력되며, 주문내역 확인 및 주문 처리가 가능합니다.<br>
								- 에스크로(결제대금 예치제) 결제의 경우는 주문후 미입금시 5일뒤에 삭제가 가능합니다.<br>
								- 카드실패 주문건은 2시간후에 삭제가 가능합니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 부가기능</span></dt>
							<dd>
								- 엑셀다운로드 : 체크된 주문건을 엑셀파일 형식으로 다운로드 받습니다.
							</dd>
						</dl>
						<dl>
							<dt><span>배송/입금일별 주문조회 주의사항</span></dt>
							<dd>- 배송/입금별 주문조회 기간은 1달을 초과할 수 없습니다.</dd>
						</dl> -->
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