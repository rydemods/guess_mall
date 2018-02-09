<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=order_excel_cancel_step44_view_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$ordercodes     = $_POST["ordercodes"];
$oistep         = $_POST["oistep"];
$orderby        = $_POST["orderby"];
if(ord($orderby)==0) $orderby = "DESC";

$s_check        = $_POST["s_check"];
$search         = trim($_POST["search"]);
$s_date         = "ordercode";
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$search_reg_start   = $_POST["search_reg_start"];
$search_reg_end     = $_POST["search_reg_end"];
$search_fin_start   = $_POST["search_fin_start"];
$search_fin_end     = $_POST["search_fin_end"];
$paymethod      = $_POST["paymethod"];
$ord_flag       = $_POST["ord_flag"]?$_POST["ord_flag"]:"AA"; // 유입경로

// 결제 상태 전부 체크된 상태로 만들기 위해 기본값으로 넣자..2016-04-19 jhjeong
//exdebug("cnt = ".count($paymethod));
if(count($paymethod) == 0) {
    $paymethod = array("B","CA","VA","OA","ME","QA","YF");
}

if(is_array($paymethod)) $paymethod = implode("','",$paymethod);

$paymethod_arr  = explode("','",$paymethod);

$sel_vender     = $_POST["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_POST["brandname"];  // 벤더이름 검색

$search_start = $search_start?$search_start:"";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

$search_reg_start = $search_reg_start?$search_reg_start:"";
$search_reg_end = $search_reg_end?$search_reg_end:"";
$search_reg_s = $search_reg_start?str_replace("-","",$search_reg_start."000000"):"";
$search_reg_e = $search_reg_end?str_replace("-","",$search_reg_end."235959"):"";

$search_fin_start = $search_fin_start?$search_fin_start:"";
$search_fin_end = $search_fin_end?$search_fin_end:"";
$search_fin_s = $search_fin_start?str_replace("-","",$search_fin_start."000000"):"";
$search_fin_e = $search_fin_end?str_replace("-","",$search_fin_end."235959"):"";

// 기본 검색 조건
$qry_from = "tblorderproduct a ";
$qry_from.= " join tblorderinfo b on a.ordercode = b.ordercode ";
$qry_from.= " left join (select oc_no, CASE WHEN pickup_state = 'Y' THEN pickup_date ELSE regdt END as reg_dt, cfindt as fin_dt from tblorder_cancel) c on a.oc_no = c.oc_no ";
$qry.= "WHERE 1=1 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
	}
}

// 환불접수일자
if ($search_reg_s != "" || $search_reg_e != "") { 
	if(substr($search_reg_s,0,8)==substr($search_reg_e,0,8)) {
		$qry.= "AND c.reg_dt LIKE '".substr($search_reg_s,0,8)."%' ";
	} else {
		$qry.= "AND c.reg_dt >= '{$search_reg_s}' AND c.reg_dt <= '{$search_reg_e}' ";
	}
}

// 환불완료일자
if ($search_fin_s != "" || $search_fin_e != "") { 
	if(substr($search_fin_s,0,8)==substr($search_fin_e,0,8)) {
		$qry.= "AND c.fin_dt LIKE '".substr($search_fin_s,0,8)."%' ";
	} else {
		$qry.= "AND c.fin_dt >= '{$search_fin_s}' AND c.fin_dt <= '{$search_fin_e}' ";
	}
}

// 검색어
if(ord($search)) {
	if($s_check=="oc") $qry.= "AND a.ordercode like '%{$search}%' ";
    //else if($s_check=="dv") $qry.= "AND a.deli_num = '{$search}' ";
    else if($s_check=="on") $qry.= "AND b.sender_name = '{$search}' ";
    else if($s_check=="oi") $qry.= "AND b.id = '{$search}' ";
    else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="op") $qry.= "AND b.ip = '{$search}' ";
    else if($s_check=="sn") $qry.= "AND b.bank_sender = '{$search}' ";
    else if($s_check=="rn") $qry.= "AND b.receiver_name = '{$search}' ";
    else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="ra") $qry.= "AND b.receiver_addr like '%{$search}%' ";
    else if($s_check=="nm") $qry.= "AND (b.sender_name = '{$search}' OR b.bank_sender = '{$search}' OR b.receiver_name = '{$search}') ";
}

// 주문상태 
if($oistep == "2") {
    $qry.= " AND (b.oi_step1 in (1,2,3,4) And a.op_step = 44) "; //결제완료,배송준비중에서의 환불완료
    $qry.= " AND a.redelivery_type = 'N' ";
}elseif($oistep == "34") {
    $qry.= " AND (b.oi_step1 in (2,3,4) And a.op_step = 44) "; //배송중, 배송완료에서의 환불완료
    $qry.= " AND a.redelivery_type = 'Y' ";
    $qry.= " AND ((coalesce(a.opt1_change, '') = '' And coalesce(a.opt2_change, '') = '')) ";
}elseif($oistep == "342") {
    $qry.= " AND (b.oi_step1 in (2,3,4) And a.op_step = 44) "; //배송중, 배송완료에서의 교환완료
    $qry.= " AND a.redelivery_type = 'G' ";
} elseif($oistep == "1234") { 
    $qry.= " AND (b.oi_step1 in (1,2,3,4) And a.op_step = 44) "; // 결제완료, 배송준비, 배송중, 배송완료에서의 환불완료
    $qry.= " AND a.redelivery_type != 'G' ";
}

// 결제타입 조건
if(ord($paymethod))	$qry.= "AND b.paymethod in ('".$paymethod."')";

// 유입경로 조건
if(ord($ord_flag)) {
    if($ord_flag != "AA") {
        if($ord_flag == "PC") $chk_mb = "0";
        if($ord_flag == "MO") $chk_mb = "1";
        if($ord_flag == "AP") $chk_mb = "2";

        $qry.= "AND b.is_mobile in ('".$chk_mb."')";
    }
}

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    $qry_from.= " join tblproductbrand v on a.vender = v.vender ".$subqry."";
} else {
    $qry_from.= " join tblproductbrand v on a.vender = v.vender ";
}

if(ord($ordercodes)) $ordercodes="'".str_replace(",","','",$ordercodes)."'";
if($ordercodes) $ordercodes= str_replace(",''","",$ordercodes);

if(ord($ordercodes)) $qry.= " AND a.idx IN (".$ordercodes.") ";

##### 벤더 리스트 S
$venderlist=array();
#$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
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
##### 벤더 리스트 E

$t_price=0;

$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰","Y"=>"PAYCO");
$sql = "SELECT  a.vender, v.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, a.price, 
                a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
                a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, 
                b.id, b.sender_name, b.paymethod, b.oi_step1, b.oi_step2, a.redelivery_type, b.is_mobile, c.reg_dt, c.fin_dt
        FROM {$qry_from} {$qry} 
        ORDER BY c.fin_dt {$orderby} , a.idx
        ";
$result=pmysql_query($sql,get_db_conn());
echo "sql = ".$sql."<br>";
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=40></col>
				<col width=120></col>
				<col width=120></col>
				<col width=120></col>
				<col width=150></col>
				<col width=150></col>
				<col width=70></col>
				<col width=200></col>
                <col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=90></col>
				<col width=100></col>
				<col width=80></col>
			
				<TR >
					<th>번호</th>
					<th>환불접수일</th>
					<th>환불완료일</th>
					<th>주문일자</th>
					<th>주문번호</th>
					<th>주문자 정보</th>
					<th>브랜드</th>
					<th>상품명</th>
                    <th>결제방법</th>
					<th>금액</th>
					<th>수량</th>
					<th>쿠폰할인</th>
					<th>사용포인트</th>
					<th>개별배송비</th>
					<th>실결제금액</th>
					<th>처리단계</th>
					<th>비고</th>
				</TR>
<?
		$colspan=14;
		if($vendercnt>0) $colspan++;

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {

			$number = $cnt+1;

			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")";
			$reg_date = substr($row->reg_dt,0,4)."/".substr($row->reg_dt,4,2)."/".substr($row->reg_dt,6,2)." (".substr($row->reg_dt,8,2).":".substr($row->reg_dt,10,2).")";
			$fin_date = substr($row->fin_dt,0,4)."/".substr($row->fin_dt,4,2)."/".substr($row->fin_dt,6,2)." (".substr($row->fin_dt,8,2).":".substr($row->fin_dt,10,2).")";
			$name = $row->sender_name;
			$stridM='';
			if(substr($row->ordercode,20)=="X") {	//비회원
				$stridM = "주문번호: ".substr($row->id,1,6);
			} else {	//회원
				$stridM = "ID: <FONT COLOR=\"blue\">{$row->id}</FONT>";
			}
			if($thisordcd!=$row->ordercode) {
				$thisordcd=$row->ordercode;
				if($thiscolor=="#FFFFFF") {
                    $thiscolor="#ffeeff";
				} else {
					$thiscolor="#FFFFFF";
				}
			}

            $status = $o_step[$row->oi_step1][$row->op_step];
?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
			        <td align="center"><?=$number?></td>
                    <td align="center"><?=$reg_date?></td>
                    <td align="center"><?=$fin_date?></td>
                    <td align="center"><?=$date?></td>
                    <td align="center"><?=$row->ordercode?></td>
			        <td style="font-size:8pt;padding:3;line-height:11pt">
			            주문자: <FONT COLOR="blue"><?=$name?></font>
				        <br> <?=$stridM?>
                    </td>
					<td style='text-align:left'><?=$venderlist[$row->vender]->brandname?></td>
                    <td style='text-align:left'><?=$row->productname?></td>
			        <td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->price)?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->quantity)?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->coupon_price)?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->use_point)?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format($row->deli_price)?></td>
                    <td align=right style="font-size:8pt;padding:3"><?=number_format(($row->price*$row->quantity)-$row->coupon_price-$row->use_point+$row->deli_price)?></td>
                    <td align=center style="font-size:8pt;padding:3"><?=$status?></td>
                    <td align=center style="font-size:8pt;padding:3"><?=$arr_mobile[$row->is_mobile]?></td>
                </tr>
<?
            $cnt++;
        }
        pmysql_free_result($result);
?>
				</TABLE>
</body>
</html>