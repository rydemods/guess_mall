<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "or-1";
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
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*3)); // 3일
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[3] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[4] = date("Y-m-d",strtotime('-1 month'));
$period[4] = date("Y-m-d",strtotime('-1 year'));
//$period[5] = substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);

################## 배송업체 쿼리 ################
$dvcode = '';
$ref_qry="select code, company_name from tbldelicompany order by company_name";
$ref1_result=pmysql_query($ref_qry);
while($row=pmysql_fetch_object($ref1_result)) {
	$delicomlist[]=$row;
}
//exdebug($delicomlist);
#########################################################


$orderby    = $_GET["orderby"];
if(ord($orderby)==0) $orderby = "DESC";

$s_check    = $_GET["s_check"];
$search     = trim($_GET["search"]);
$s_date     = $_GET["s_date"];
if(ord($s_date)==0) $s_date = "ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
	$s_date = "ordercode";
}


$search_start   = $_GET["search_start"];
$search_end     = $_GET["search_end"];
$s_prod         = $_GET["s_prod"];
$search_prod    = $_GET["search_prod"];
$staff_order    = $_GET["staff_order"]?$_GET["staff_order"]:"A"; //스테프관련 추가 (2016.05.11 - 김재수)
$dvcode         = $_GET["dvcode"];
$oistep1        = $_GET["oistep1"];
$oi_type        = $_GET["oi_type"];
$paystate       = $_GET["paystate"]?$_GET["paystate"]:"A";
$paymethod      = $_GET["paymethod"];
$sel_op_step        = 2;
$delivery_type      = $_GET["delivery_type"];
$selected[delivery_type]      = $delivery_type;
$ord_flag       = $_GET["ord_flag"]; // 유입경로
$com_name       = $_GET["com_name"];  // 벤더이름 검색


// 상품구분
$pr_select = $_GET["pr_select"];

// 경매 주문 구분
$auction_chk = $_GET["auction_chk"];

if ($oistep1[0] == '' && $oi_type[0] == '') {
	$oistep1_def=array("0","1","2","3","4");
	foreach($oistep1_def as $k => $v) $oistep1[$k] = $v;
	$oi_type_def=array("44","67","61","62","68","63","64","65","66");
	foreach($oi_type_def as $k => $v) $oi_type[$k] = $v;
}

if ($paymethod[0] == '') {
	foreach(array_keys($arpm) as $k => $v) {
		$paymethod[$k] = $v;
	}
}

if ($ord_flag[0] == '') {
	$ord_flag_def=array("PC","MO","AP");
	foreach($ord_flag_def as $k => $v) $ord_flag[$k] = $v;
}

if(is_array($oistep1)) $oistep1 = implode(",",$oistep1);
if(is_array($oi_type)) $oi_type = implode(",",$oi_type);
if(is_array($paymethod)) $paymethod = implode("','",$paymethod);
if(is_array($ord_flag)) $ord_flag = implode("','",$ord_flag);

$oistep1_arr   = explode(",",$oistep1);
$oi_type_arr   = explode(",",$oi_type);
$paymethod_arr = explode("','",$paymethod);
$ord_flag_arr  = explode("','",$ord_flag);

$selected[s_check][$s_check]    = 'selected';
$selected[s_date][$s_date]      = 'selected';
$selected[s_prod][$s_prod]      = 'selected';
$selected[staff_order][$staff_order] = 'checked'; //스테프관련 추가 (2016.05.11 - 김재수)
$selected[dvcode][$dvcode]      = 'selected';
$selected[paystate][$paystate]  = 'checked';

$search_start = $search_start?$search_start:$period[5];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	//alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

// 기본 검색 조건
$qry_from = "(
					SELECT op.*, p.prodcode, p.colorcode, p.tag_style_no, p.season FROM tblorderproduct op LEFT JOIN tblproduct p 
					ON op.productcode=p.productcode
					) a ";
$qry_from.= " join tblorderinfo b on a.ordercode = b.ordercode ";
$qry.= "WHERE 1=1 ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") {
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
	}
}

// 기본옵션만 검색 (2016-03-08 김재수 막음 - 추가옵션도 있어서..)
//$qry.= "AND a.option_type = 0 ";

// 검색어
if(ord($search)) {
	/* if($s_check=="oc") $qry.= "AND a.ordercode like '%{$search}%' ";
    else if($s_check=="dv") $qry.= "AND a.deli_num = '{$search}' ";
    else if($s_check=="on") $qry.= "AND b.sender_name = '{$search}' ";
    else if($s_check=="oi") $qry.= "AND b.id = '{$search}' ";
    else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="op") $qry.= "AND b.ip = '{$search}' ";
    else if($s_check=="sn") $qry.= "AND b.bank_sender = '{$search}' ";
    else if($s_check=="rn") $qry.= "AND b.receiver_name = '{$search}' ";
    else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="ra") $qry.= "AND b.receiver_addr like '%{$search}%' ";
    else if($s_check=="nm") $qry.= "AND (b.sender_name = '{$search}' OR b.bank_sender = '{$search}' OR b.receiver_name = '{$search}') ";
	else if($s_check=="al") {
        $or_qry[] = " a.ordercode like '%{$search}%' ";
        $or_qry[] = " b.sender_name = '{$search}' ";
        $or_qry[] = " b.id = '{$search}' ";
        $or_qry[] = " replace(b.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " b.ip = '{$search}' ";
        $or_qry[] = " b.bank_sender = '{$search}' ";
        $or_qry[] = " b.receiver_name = '{$search}' ";
        $or_qry[] = " replace(b.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
        $or_qry[] = " b.receiver_addr like '%{$search}%' ";
        $qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
    } */
    
	$search = trim($search);
	$temp_search = explode("\r\n", $search);
	$cnt = count($temp_search);
	
	$search_arr = array();
	for($i = 0 ; $i < $cnt ; $i++){
		array_push($search_arr, "'%".$temp_search[$i]."%'");
	}
	
    if($s_check=="oc") $qry.= "AND a.ordercode LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="dv") $qry.= "AND a.deli_num LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="on") $qry.= "AND b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="oi") $qry.= "AND b.id LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="oh") $qry.= "AND replace(b.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    else if($s_check=="op") $qry.= "AND b.ip LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="sn") $qry.= "AND b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="rn") $qry.= "AND b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="rh") $qry.= "AND replace(b.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    else if($s_check=="ra") $qry.= "AND b.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] ) ";
    else if($s_check=="nm") $qry.= "AND (b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) OR b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) OR b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] )) ";
    else if($s_check=="al") {
    	$or_qry[] = " a.ordercode LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.sender_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.id LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " replace(b.sender_tel, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    	$or_qry[] = " b.ip LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.bank_sender LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " b.receiver_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$or_qry[] = " replace(b.receiver_tel2, '-', '') LIKE any ( array[".implode(",", str_replace("-", "", $search_arr))."] ) ";
    	$or_qry[] = " b.receiver_addr LIKE any ( array[".implode(",", $search_arr)."] ) ";
    	$qry.= " AND ( ".implode( ' OR ', $or_qry )." ) ";
    }
}

// 상품 조건
if(ord($search_prod)) {
	if($s_prod=="pn") $qry.= "AND upper(a.productname) like upper('%{$search_prod}%') ";
	else if($s_prod=="pc") $qry.= "AND upper(a.productcode) like upper('%{$search_prod}%') ";
	else if($s_prod=="sc") $qry.= "AND upper(a.selfcode) like upper('%{$search_prod}%') ";
	else if($s_prod=="st") $qry.= "AND upper(a.style) like upper('%{$search_prod}%') ";
}

// 주문구분 조건 (2016.05.11 - 김재수)
if(ord($staff_order))	{
	if($staff_order != "A") $qry.= "AND b.staff_order = '{$staff_order}' ";
}


// 배송업체 조건
if(ord($dvcode)) $qry.= "AND a.deli_com = '{$dvcode}' ";

// 발생구분 조건
if(ord($delivery_type)) $qry.= "AND a.delivery_type = '{$delivery_type}' ";


// 주문상태별 조건
$qry.= "AND a.op_step = ".$sel_op_step ;

// 결제타입 조건
if(ord($paymethod))	$qry.= " AND SUBSTRING(b.paymethod,1,1) in ('".$paymethod."') ";

// 유입경로 조건
if(ord($ord_flag)) {
	$chk_mb = array();
	if(count($ord_flag_arr)) {
		foreach($ord_flag_arr as $k => $v) {
			switch($v) {
				case "PC" : $chk_mb[] = "0"; break;
				case "MO" : $chk_mb[] = "1"; break;
				case "AP" : $chk_mb[] = "2"; break;
			}
		}
	}
	//if(count($subWhere)) {
		$qry.= " AND b.is_mobile in ('".implode("','",$chk_mb)."') ";
	//}
}

// 브랜드 조건
if($sel_vender || $com_name) {
    if($com_name) $subqry = " and v.com_name like '%".strtoupper($com_name)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    $qry_from.= " left join tblvenderinfo v on a.vender = v.vender ".$subqry."";
} else {
    $qry_from.= " left join tblvenderinfo v on a.vender = v.vender ";
}
$qry_from.= " left join tblproductbrand pb on a.vender = pb.vender ";

##################### 벤더정보
$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
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
}
##############################

include("header.php");

$sql = "SELECT COUNT(a.ordercode) as t_count FROM {$qry_from} {$qry} {$subqry}";
$paging = new newPaging($sql,10,20,'GoPage');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$excel_sql = "SELECT  a.vender , a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, 
					a.text_opt_subject, a.text_opt_content, a.price as sell_price, 
                    (a.price+a.option_price) as price, a.option_quantity, 
                    a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
					a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, b.id, 
					b.order_msg2, b.bank_date,
					b.sender_name, b.sender_tel, b.sender_tel2, b.sender_email, m.home_post, m.home_addr,
					b.receiver_name, b.receiver_tel1, b.receiver_tel2, b.receiver_addr, 
					b.paymethod, b.oi_step1, b.oi_step2, b.is_mobile, a.self_goods_code, b.order_msg2, a.delivery_type, a.store_code, a.reservation_date, a.prodcode, a.colorcode, a.staff_order, a.cooper_order, a.tag_style_no,b.regdt,(z.regdt) as store_regdt
                FROM {$qry_from} left join tblmember m on b.id = m.id 
				LEFT JOIN
				(SELECT idx,max(regdt) as regdt 
				FROM tblorderproduct_store_code  
				WHERE store_code != 'A1801B' 
					/*and ordercode LIKE any ( array['%%'] ) */
					group by idx 
					ORDER BY regdt DESC 
				) z ON z.idx = a.idx 
				{$qry} ";
$excel_sql_orderby = "
                ORDER BY a.ordercode {$orderby}, a.vender DESC 
                ";

?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
$(document).ready(function(){
	$(".chk_all").click(function() {
		var chk_cn = $(this).attr('chk');
		 if($(this).prop("checked")){
			$("."+chk_cn).attr("checked", true);
		 } else {
			 
			$("."+chk_cn).attr("checked", false);
		 }
	});
});

<?php if($vendercnt>0){?>
function viewVenderInfo(vender) {
	window.open("about:blank","vender_infopop","width=100,height=100,scrollbars=yes");
	document.vForm.vender.value=vender;
	document.vForm.target="vender_infopop";
	document.vForm.submit();
}
<?php }?>

function searchForm() {
	document.form1.action="order_list_delivery.php";
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
	period[5] = "<?=$period[5]?>";


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
	document.form1.s_check.value="on";
	document.form1.search.value=sender;
	document.form1.action="order_list_delivery.php";
	document.form1.submit();
}

function CheckAll(){
   chkval=document.form2.allcheck.checked;
   cnt=document.form2.tot.value;
   for(i=1;i<=cnt;i++){
      document.form2.chkordercode[i].checked=chkval;
   }
}

/*
function OrderSendSMS() {
	document.smsform.ordercodes.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			document.smsform.ordercodes.value+="'"+document.form2.chkordercode[i].value.substring(1)+"',";
		}
	}
	if(document.smsform.ordercodes.value.length==0) {
		alert("SMS를 발송할 주문서를 선택하세요.");
		return;
	}
	window.open("about:blank","sendsmspop","width=220,height=350,scrollbars=no");
	document.smsform.type.value="order";
	document.smsform.submit();
}
*/

function InsertDVcodes() {
    if ( $("input[name='chkordercode']:checked").length == 0 ) {
        alert("하나 이상을 선택해 주세요.");
        return;
    }

    var frm = document.dvsform;

    $(frm).find("input[name='idxs[]']").remove();
    $(frm).find("input[name='ordercodes[]']").remove();
    $(frm).find("input[name='delicoms[]']").remove();
    $(frm).find("input[name='delinames[]']").remove();
    $(frm).find("input[name='dvcodes[]']").remove();

    var failCount = 0;
    $("input[name='chkordercode']:checked").each(function(idx){
        var idx = $(this).val();
        var ordercode = $(this).parent().attr("ids");
        var delicom = $("#chkdeli_com_"+idx).val();
        var deliname = $("#chkdeli_com_"+idx+" option:selected").text();
        var dvcode = $("#dv_code_"+idx).val();
//        alert(idx + " / " + ordercode + " / " + delicom + " / " + deliname + " / " + dvcode);

        if ( delicom == "" ) {
            alert(idx + "/" + ordercode + " => 택배사를 지정해 주세요.");
            failCount++;
        } else if ( dvcode == "" ) {
            alert(idx + "/" + ordercode + " => 송장번호를 입력해 주세요.");
            failCount++;
        } else {
            $(frm).append('<input type="hidden" name="idxs[]" value="' + idx + '">');
            $(frm).append('<input type="hidden" name="ordercodes[]" value="' + ordercode + '">');
            $(frm).append('<input type="hidden" name="delicoms[]" value="' + delicom + '">');
            $(frm).append('<input type="hidden" name="delinames[]" value="' + deliname + '">');
            $(frm).append('<input type="hidden" name="dvcodes[]" value="' + dvcode + '">');
        }
    });

    if ( failCount == 0 ) {
        // 모두 정상적으로 입력된 경우에만 submit
        document.dvsform.action = "dvcode_all_select_indb.php";

        if(confirm("송장정보 입력후에는 배송중으로 상태가 변경됩니다.\r\n\r\n진행하시겠습니까?")) {
            //document.dvform.target = "HiddenFrame";
            //document.dvform.target = "_blank";
            document.dvsform.submit();
        }
    }
}

function InsertDvcode(idx, ordercode) {
    
    var delicom = $("#chkdeli_com_"+idx).val();
    var deliname = $("#chkdeli_com_"+idx+" option:selected").text();
    var dvcode = $("#dv_code_"+idx).val();

    document.dvform.action = "dvcode_indb.php";
    document.dvform.mode.value = "updatedvcode";
    document.dvform.idx.value = idx;
    document.dvform.ordercode.value = ordercode;
    document.dvform.delicom.value = delicom;
    document.dvform.deliname.value = deliname;
    document.dvform.dvcode.value = dvcode;

    if(confirm("송장정보 입력후에는 배송중으로 상태가 변경됩니다.\r\n\r\n진행하시겠습니까?")) {
        document.dvform.target = "HiddenFrame";
        //document.dvform.target = "_blank";
        document.dvform.submit();
    }
}

function submit_data() {
    if ( $("#csv_file").val() == "" ) {
        alert("csv파일을 선택해 주세요.");
        return false;
    }
}

function CharacterCheck( character ){
	var temp_character = '';
	var character_type = false;
	for( var i = 0; i < character.length; i++ ){
		if( $.isNumeric( character[i] ) ){
			temp_character += character[i];
		} else {
			character_type = true;
		}
	}

	if( character_type ) alert( '숫자만 입력이 가능합니다.' );

	return temp_character;
}

$(document).on( 'keyup', 'input[name="dv_code"]', function( event ){
	 $(this).val( CharacterCheck(  $(this).val() ) );
});

function OrderExcel() {
	document.downexcelform.idxs.value="";
	window.open("about:blank","excelselpop","width=700,height=501,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function OrderCheckExcel() {
	document.downexcelform.idxs.value="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			if(document.downexcelform.idxs.value!='') document.downexcelform.idxs.value +=",";
			document.downexcelform.idxs.value+=document.form2.chkordercode[i].value;
		}
	}
	if(document.downexcelform.idxs.value.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

	window.open("about:blank","excelselpop","width=700,height=501,scrollbars=no");
	document.downexcelform.target="excelselpop";
	document.downexcelform.submit();
}

function store_pass(idx){
	if(confirm("해당 주문건을 매장입찰 하시겠습니까?")){
		document.form2.queryIdx.value=idx+",";
		
		$("#frmList").attr('method', 'POST');
		$("#frmList").attr('action', '/admin/order_check_deli_flag.php');
		$("#frmList").submit();
		
	}
}

function OrderDeliveryAll() {
	//sendErporderShopChange($ordercode, $idxs) // 매장변경시 전송
	if(confirm("선택한 주문건을 매장입찰 하시겠습니까?")){
		for(i=1;i<document.form2.chkordercode.length;i++) {
			if(document.form2.chkordercode[i].checked) {
				document.form2.queryIdx.value+=document.form2.chkordercode[i].value.substring(0)+",";
			}
		}
		if(document.form2.queryIdx.value.length==0) {
			alert("선택하신 주문서가 없습니다.");
			return;
		}else{
			$("#frmList").attr('method', 'POST');
			$("#frmList").attr('action', '/admin/order_check_deli_flag.php');
			$("#frmList").submit();
		}
	}
}


function market_send(){
	var pr_idx="";
	for(i=1;i<document.form2.chkordercode.length;i++) {
		if(document.form2.chkordercode[i].checked) {
			//alert(document.form2.chkordercode[i].value);
			pr_idx+=document.form2.chkordercode[i].value+",";
		}
	}
	if(pr_idx.length==0) {
		alert("선택하신 주문서가 없습니다.");
		return;
	}

	if(confirm("선택하신 상품을 매장입찰로 전송하시겠습니까?")) {
		//매장변경시 erp전송 추가필요
		//sendErporderShopChange($ordercode, $idxs) // 매장변경시 전송
		alert("띵!");
	}
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt;<span>배송준비중 리스트</span></p></div></div>

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
					<div class="title_depth3">배송준비중 리스트</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>입금일별, 배송일자별, 주문일자별 주문현황 및 주문내역을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>

			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">주문현황 조회</span></div>
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
						<tr>
							<th><span>검색어</span></th>
							<TD class="td_con1">
								<select name="s_check" class="select" style="width:80px;height:32px;vertical-align:middle;">
									<option value="al" <?=$selected[s_check]["al"]?>>전체</option>
                                    <option value="oc" <?=$selected[s_check]["oc"]?>>주문코드</option>
                                    <!-- <option value="dv" <?=$selected[s_check]["dv"]?>>송장번호</option> -->
                                    <option value="">----------------------</option>
                                    <option value="on" <?=$selected[s_check]["on"]?>>주문자명</option>
                                    <option value="oi" <?=$selected[s_check]["oi"]?>>주문자ID</option>
                                    <option value="oh" <?=$selected[s_check]["oh"]?>>주문자HP</option>
                                    <option value="op" <?=$selected[s_check]["op"]?>>주문자IP</option>
                                    <option value="">----------------------</option>
                                    <option value="sn" <?=$selected[s_check]["sn"]?>>입금자명</option>
                                    <option value="rn" <?=$selected[s_check]["rn"]?>>수령자명</option>
                                    <option value="rh" <?=$selected[s_check]["rh"]?>>수령자HP</option>
                                    <option value="ra" <?=$selected[s_check]["ra"]?>>배송지주소</option>
                                    <option value="">----------------------</option>
                                    <option value="nm" <?=$selected[s_check]["nm"]?>>주문자명,입금자명,수령자명</option>
								</select>
								<!-- 
								<input type=text name=search value="<?=$search?>" style="width:197" class="input">
								 -->
								<textarea rows="2" cols="10" class="w200" name="search" id="search" style="resize:none;vertical-align:middle;"><?=$search?></textarea>
							</TD>
						</tr>

						<TR>
							<th><span>기간선택</span></th>
							<td>
								<select name="s_date" class="select">
									<option value="ordercode" <?=$selected[s_date]["ordercode"]?>>주문일</option>
									<!-- <option value="deli_date" <?=$selected[s_date]["deli_date"]?>>배송일</option> -->
									<option value="bank_date" <?=$selected[s_date]["bank_date"]?>>입금일</option>
                                    <!--<option value="delivery_end_date" <?=$selected[s_date]["delivery_end_date"]?> >배송완료일</option>-->
								</select>
								<input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
						</TR>

						<tr>
							<th><span>상품</span></th>
							<TD class="td_con1">
								<select name="s_prod" class="select">
									<!--<option value="st" <?=$selected[s_prod]["st"]?>>스타일</option>-->
									<option value="pn" <?=$selected[s_prod]["pn"]?>>상품명</option>
									<option value="pc" <?=$selected[s_prod]["pc"]?>>상품코드</option>
									<option value="sc" <?=$selected[s_prod]["sc"]?>>진열코드</option>
								</select>
								<input type=text name=search_prod value="<?=$search_prod?>" style="width:197" class="input">
							</TD>
						</tr>
<? /*
						<TR>
							<th><span>배송업체</span></th>
							<TD>
								<select name=dvcode class="select">
									<option value="">==== 전체 ====</option>
<? while($ref1_data=pmysql_fetch_object($ref1_result)){ ?>
									<option value="<?=$ref1_data->code?>" <?=$selected[dvcode][$ref1_data->code]?>><?=$ref1_data->company_name?></option>
<? } ?>
								</select>&nbsp;
							</TD>
						</TR>
*/ ?>

						<TR>
							<th><span>주문구분</span></th>
							<TD class="td_con1">
								<input type="radio" name="staff_order" value="A" <?=$selected[staff_order]["A"]?>>전체</input>
								<input type="radio" name="staff_order" value="N" <?=$selected[staff_order]["N"]?>>일반</input>
								<input type="radio" name="staff_order" value="Y" <?=$selected[staff_order]["Y"]?>>임직원</input>
							</TD>
						</TR>
<!--
						<TR>
							<th><span>주문상태</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_oistep' name="oistep_all" value="<?=$k?>" <?if(count($oistep1_arr) == 5 && count($oi_type_arr) == 9) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
							<TD class="td_con1">
<?
		foreach ($oi_step1 as $k=>$v){
?>
								<input type="checkbox" class='chk_oistep' name="oistep1[]" value="<?=$k?>" <?=( in_array($k, $oistep1_arr )?'checked':'')?>><?=$v?></input>
<?
		}
?>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="44" <?=(in_array(44,$oi_type_arr)?'checked':'')?>>입금전취소완료</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="67" <?=(in_array(67,$oi_type_arr)?'checked':'')?>>교환신청</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="61" <?=(in_array(61,$oi_type_arr)?'checked':'')?>>교환접수</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="62" <?=(in_array(62,$oi_type_arr)?'checked':'')?>>교환완료</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="68" <?=(in_array(68,$oi_type_arr)?'checked':'')?>>반품신청</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="63" <?=(in_array(63,$oi_type_arr)?'checked':'')?>>반품접수</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="64" <?=(in_array(64,$oi_type_arr)?'checked':'')?>>반품완료</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="65" <?=(in_array(65,$oi_type_arr)?'checked':'')?>>환불접수</input>
								<input type="checkbox" class='chk_oistep' name="oi_type[]" value="66" <?=(in_array(66,$oi_type_arr)?'checked':'')?>>환불완료</input>
							</TD>
						</TR>
-->
						<TR>
							<th><span>발생구분</span></th>
							<TD class="td_con1">
								<input type="radio" name="delivery_type" value="" <?if($selected[delivery_type]==''){?>checked<?}?>>전체
								<?foreach ($arrChainCode as $k=>$v){?>
								<input type="radio" name="delivery_type" value="<?=$k?>" <?if($selected[delivery_type]."|"==$k."|"){?>checked<?}?>><?=$v?>
								<?}?>
							</TD>
						</TR>
<!--
						<TR style="display:none">
							<th><span>결제상태</span></th>
							<TD class="td_con1">
								<input type="radio" name="paystate" value="A" <?=$selected[paystate]["A"]?>>전체</input>
								<input type="radio" name="paystate" value="N" <?=$selected[paystate]["N"]?>>입금전</input>
								<input type="radio" name="paystate" value="Y" <?=$selected[paystate]["Y"]?>>입금완료(결제완료)</input>
							</TD>
						</TR>
-->
						<TR>
							<th><span>결제타입</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_paymethod' name="paymethod_all" value="<?=$k?>" <?if(count($paymethod_arr) == 6) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
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
							<th><span>유입경로</span><font style="font-family: '굴림,굴림';font-weight: normal;color:#777777;padding-left:50px"><input type="checkbox"  style="vertical-align: middle;" class='chk_all' chk='chk_ord_flag' name="ord_flag_all" value="<?=$k?>" <?if(count($ord_flag_arr) == 3) { echo 'checked'; } else { echo ''; }?>>전체선택</input></font></th>
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
								</select>
								
							</td>
						</TR>

                        <TR style="display:none">
                            <th><span>상품구분</span></th>
                            <td>
                                <select name='pr_select' class='select' >
                                    <option value="" <?if( $pr_select == '' ){ echo 'selected'; }?> >=== 전체 ===</option>
                                    <option value="SJ" <?if( $pr_select == 'SJ' ){ echo 'selected'; }?> >세정상품</option>
                                    <option value="VN" <?if( $pr_select == 'VN' ){ echo 'selected'; }?> >입점상품</option>
                                </select>
                            </td>
                        </TR>

                        <TR style="display:none">
                            <th><span>옥션주문</span></th>
                            <td>
                                <select name='auction_chk' class='select' >
                                    <option value="" <?if( $auction_chk == '' ){ echo 'selected'; }?> >=== 전체 ===</option>
                                    <option value="N" <?if( $auction_chk == 'N' ){ echo 'selected'; }?> >일반주문</option>
                                    <option value="Y" <?if( $auction_chk == 'Y' ){ echo 'selected'; }?> >경매주문</option>
                                </select>
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
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="#;" onclick="OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 id = 'frmList' action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type = 'hidden' id = 'queryIdx' name = 'query_idx' value = ''>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		$sql = "SELECT  a.vender, v.com_name, pb.brandname, a.ordercode, a.productcode, a.productname, a.opt1_name, a.opt2_name, a.quantity, a.price, a.colorcode,
                        a.deli_com, a.deli_num, a.deli_date, a.deli_price, 
                        a.coupon_price, a.use_point, a.op_step, a.opt1_change, a.opt2_change, a.oc_no, a.date, a.idx, 
                        b.id, b.sender_name, b.receiver_name, b.paymethod, b.pg_ordercode, b.regdt, b.oi_step1, b.oi_step2, a.delivery_type, a.store_code, a.reservation_date, a.staff_order, a.cooper_order, a.season
                FROM {$qry_from} {$qry} {$subqry}
		        ORDER BY a.ordercode {$orderby}, a.vender DESC 
                ";
		$sql = $paging->getSql($sql);
		$result=pmysql_query($sql,get_db_conn());

		$colspan=13;
		if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372"><img src="images/icon_8a.gif" border="0"><B>정렬 :
					<?php if($orderby=="DESC"){?>
					<A HREF="javascript:GoOrderby('ASC');"><B><FONT class=font_orange>주문일자순↑</FONT></B></A>
					<?php }else{?>
					<A HREF="javascript:GoOrderby('DESC');"><B><FONT class=font_orange>주문일자순↓</FONT></B></A>
					<?php }?>
					&nbsp;주문번호(붉은색) : <B><FONT class=font_orange>PG 주문번호</FONT></B>
					</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=150></col>
				<col width=140></col>
				<col width=30></col>
				<?php if($vendercnt>0){?>
				<col width=90></col>
				<?php }?>
				<col width=*></col>
				<col width=100></col>
				<col width=60></col>
				<col width=40></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=160></col>
				<input type=hidden name=chkordercode>

				<TR >
					<th>주문일자</th>
					<th>주문자 정보</th>
					<th><input type=checkbox name=allcheck onclick="CheckAll()"></th>
					<?php if($vendercnt>0){?>
					<th>브랜드</th>
					<?php }?>
					<th>상품명</th>
					<th>O2O구분</th>
					<th>결제방법</th>
					<th>수량</th>
					<th>실결제금액</th>
					<th>처리단계</th>
					<th>배송주체</th>
					<th>창고재고</th>
					<th>송장번호</th>
				</TR>

<?php
		$curdate = date("YmdHi",strtotime('-2 hour'));
		$curdate5 = date("Ymd",strtotime('-5 day'));
		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {
			$thisordcd = $row->ordercode;
			if($thisordcd == $row->ordercode) {
			}
			$arrListOrderCount[$row->ordercode] += 1;
			$arrListOrder[] = $row;
		}

		foreach($arrListOrder as $key => $row) {

			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			$name = $row->sender_name;

			$date = substr($row->ordercode,0,4)."/".substr($row->ordercode,4,2)."/".substr($row->ordercode,6,2)." (".substr($row->ordercode,8,2).":".substr($row->ordercode,10,2).")<br>".$row->ordercode;
			$stridM='';
			$member_text="";
			if(substr($row->ordercode,20)=="X") { //비회원
				$member_text=" (비회원)";
				$stridM = "주문번호: ".substr($row->id,1,6);
			} else { //회원
				//$stridM = "ID: <A HREF=\"javascript:MemberView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
				$stridM = "ID: <A HREF=\"javascript:CrmView('{$row->id}');\"><FONT COLOR=\"blue\">{$row->id}</FONT></A>";
			}
			if($thisordcd!=$row->ordercode) {
				$thisordcd=$row->ordercode;
				if($thiscolor=="#FFFFFF") {
					//$thiscolor="#FEF8ED";
                    $thiscolor="#ffeeff";
				} else {
					$thiscolor="#FFFFFF";
				}
			}
			//list($style, $color)=pmysql_fetch("SELECT style, color FROM tblproduct WHERE productcode='".$row->productcode."'");
			
			$status = $op_step[$row->op_step];

			$storeData = getStoreData($row->store_code);

			$opt_name="";
			if($row->colorcode){
				$opt_name.="COLOR : ".$row->colorcode." / ";
			}
			if( strlen( $row->opt1_name ) > 0  || strlen( $row->text_opt_subject ) > 0 ){
				$tmp_opt_subject = explode( '@#', $row->opt1_name );
				$tmp_opt_content = explode( chr(30), $row->opt2_name );
				foreach( $tmp_opt_subject as $subjectKey=>$subjectVal ){
					$opt_name.=$subjectVal.' : '.$tmp_opt_content[$subjectKey];
				} // opt_subject foreach

				if( strlen( $row->text_opt_subject ) > 0 ){
					$tmp_text_opt_subject = explode( '@#', $row->text_opt_subject );
					$tmp_text_opt_content = explode( '@#', $row->text_opt_content );
					foreach( $tmp_text_opt_subject as $subjectKey=>$subjectVal ){
						$opt_name.=' [ '.$subjectVal.' : '.$tmp_text_opt_content[$subjectKey];
					} // opt_subject foreach
				}
			} else {
				$opt_name.="-";
			}

?>
			    <tr bgcolor=<?=$thiscolor?>>
					<?if($thisordcd2!=$row->ordercode) {?>
						<?$thisordcd2=$row->ordercode;?>
						<td align="center" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>">
							<A HREF="javascript:OrderDetailView('<?=$row->ordercode?>')"><?=$date?></A><br><br><FONT class=font_orange><?=$row->pg_ordercode ?></font>
							<input type='hidden' name='regdt' id='regdt' value='<?=$row->regdt?>'>
						</td>

						<td style="font-size:8pt;padding:3;line-height:11pt" rowspan = "<?=$arrListOrderCount[$row->ordercode]?>">
							주문자: <A HREF="javascript:SenderSearch('<?=$name?>');"><FONT COLOR="blue"><?=$name.$member_text?></font></A>
							<br> <?=$stridM?>
							<br> 구매자 : <FONT class=font_orange><?if($row->staff_order=="Y"){echo "임직원";}else if($row->cooper_order=="Y"){echo "협력사";}else{echo "일반";}?></font>				
						</td>
					<?}?>

					<td align="center" ids="<?=$row->ordercode?>">
						<input type=checkbox name=chkordercode value="<?=$row->idx?>"><br>
						<!-- <?=$number?> -->
					</td>
<?
			if($vendercnt>0){
?>
					<td style='text-align:left'>
						<a href="javascript:viewVenderInfo(<?=$row->vender?>)"><?=$row->brandname?></a>
					</td>
<?
			}
?>
					<td style='text-align:left'><a href="JavaScript:OrderDetailView('<?=$row->ordercode?>')">
						<?=$row->productname?><img src="images/newwindow.gif" border=0 align=absmiddle></a>
						<br><?=$opt_name?>
						
					</td>
					<td align=center style="font-size:8pt;padding:3;line-height:12pt">
						<div>
							<?
								echo '<strong>'.$arrChainCode[$row->delivery_type].'</strong>';
								if( $row->reservation_date ){
									echo '<br>'.substr($row->reservation_date, 0, 4).".".substr($row->reservation_date, 5, 2).".".substr($row->reservation_date, 8, 2);
								}
								if($row->store_code){
									echo '<br>'.$storeData["name"];
									echo '<br>'.$row->store_code;
								}
							?>
						</div>
					</td>
					<td align=center style="font-size:8pt;padding:3;line-height:12pt"><?=$arpm[$row->paymethod[0]]?></td>
					<td align=center style="font-size:8pt;padding:3"><?=number_format($row->quantity)?></td>
					<td align=right style="font-size:8pt;padding:3"><?=number_format((($row->price+$row->option_price)*$row->quantity)-$row->coupon_price-$row->use_point-$row->use_epoint)?></td>
                    <!--<td align=center style="font-size:8pt;padding:3"><?=$row->oi_step1." / ".$row->oi_step2." / ".$row->op_step?><br><?=$status?><!-- <?=$op_step[$row->op_step]?> </td>-->
					<td align=center style="font-size:8pt;padding:3"><?=$status?> </td>
					<td align=center style="font-size:8pt;padding:3">
						<?if($row->delivery_type==0 || $row->delivery_type==3){    //택배발송 주문의 경우 배송주체 나오도록 작업

							list($prodcode)=pmysql_fetch("select prodcode from tblproduct where productcode='".$row->productcode."'");
							//$rtn=getErpPriceNStock($prodcode, $row->colorcode, $row->opt2_name, $sync_bon_code);

							//if(!$rtn[sumqty]){
								//$rtn[sumqty]=0;
							//}
							
// 20170824 매장재고 없을시 매장입찰 생성 안되게 수정
							$prodcd				= $prodcode;
							$colorcd			= $row->colorcode;
							$sizecd				= $row->opt2_name;

							$res = getErpProdShopStock($prodcd, $colorcd, $sizecd, $row->delivery_type);
//							$res = getErpProdShopStock_New($prodcd, $colorcd, $sizecd, $delivery_type);

							$sum = 0;
							$sum_a = 0;
							if ($res) {
								foreach($res["shopnm"] as $key => $val) {
									if ($res["availqty"][$key] > 0) {
										if($res["shopcd"][$key] != "A1801B"){
											$sum += $res["availqty"][$key];
										}else{
											$sum_a += $res["availqty"][$key];
										}
									}
								}
							}


							
							?>
							<span class="page_screen">
								<?if($row->delivery_type==0){?>
									본사발송<br>(재고:<?=$sum_a?>)
								<?}else if($row->delivery_type==3){?>
									매장발송
								<?}?>
								<?//if(($row->delivery_type==0 && ($row->op_step == 1 || $row->op_step == 2)) && $row->season!="K"){?>
								<?if(($row->delivery_type==0 && ($row->op_step == 1 || $row->op_step == 2)) && $row->season!="K" && $sum > 0){?>
								<br><input type="button" value="매장입찰" class="btn_blue" style="padding:2px 5px 1px" onclick="javascript:store_pass('<?=$row->idx?>')">
								<?}?>
								
							</span>
                       
						<?}else{?>
								-
						<?}?>

					</td>
					<td align=center style="font-size:8pt;padding:3">
						<?if($row->delivery_type==0){
							
							list($prodcode)=pmysql_fetch("select prodcode from tblproduct where productcode='".$row->productcode."'");
							
							$res = getErpProdOnlineShopStock($prodcode, $row->colorcode, $row->opt2_name);
							$online_shop_stock	= "";
							if ($res['p_err_code'] == '0') {
								if ($res['p_data']) {
									foreach($res['p_data'] as $key => $val) {
										$online_shop_stock .= ($online_shop_stock)?"<br>".$key." : ".$val:$key." : ".$val;
									}
								} else {									
									$online_shop_stock .='-';
								}
							} else {
								$online_shop_stock .='-';
							}
							
							?>
							<span class="page_screen">
								<?=$online_shop_stock?>								
							</span>
                       
						<?}else{?>
								-
						<?}?>

					</td>
                    <td align=right style="font-size:8pt;padding:3">
                        <select name=chkdeli_com class="input" style="font-size:8pt;padding:3" id="chkdeli_com_<?=$row->idx?>">
                            <option value="">==== 전체 ====</option>
<?
                        foreach($delicomlist as $k=>$v){
?>
                            <option value="<?=$delicomlist[$k]->code?>" <?if(trim($delicomlist[$k]->code)=="01"){echo "selected";}?>><?=$delicomlist[$k]->company_name?></option>
<?
                        }
?>
                        </select>
                        <input type=text name=dv_code style="width:80" class="input" id="dv_code_<?=$row->idx?>">
                        <a href="javascript:InsertDvcode(<?=$row->idx?>, '<?=$row->ordercode?>');"><img style="padding-top: 3px; padding-bottom: 7px; vertical-align: middle;" src="images/btn_input.gif" border="0"></a>
                    </td>
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
					<td align='left' valign=middle>
					<a href="javascript:OrderCheckExcel();"><img src="images/btn_excel_select.gif" border="0" hspace="1"></a>&nbsp;
					<a href="javascript:OrderDeliveryAll();"><img src="images/market_send.gif" border="0" hspace="1"></a>&nbsp;
					</td>
				</tr>
				<tr>
					<!--<td align='left' valign=middle><a href="javascript:InsertDVcodes();"><img src="images/btn_product_reg_all.gif" border="0" hspace="0" alt="일괄수정"></a></td>-->
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
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=staff_order value="<?=$staff_order?>"> <!-- 스테프관련 추가 (2016.05.11 - 김재수) -->
			<input type=hidden name=s_date value="<?=$s_date?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=com_name value="<?=$com_name?>">
			<input type=hidden name=staff_order value="<?=$staff_order?>"> <!-- 스테프관련 추가 (2016.05.11 - 김재수) -->
			<input type=hidden name=s_prod value="<?=$s_prod?>">
			<input type=hidden name=search_prod value="<?=$search_prod?>">
			<input type=hidden name=dvcode value="<?=$dvcode?>">
			<input type=hidden name=oistep1 value="<?=$oistep1?>">
			<input type=hidden name=oi_type value="<?=$oi_type?>">
			<input type=hidden name=paystate value="<?=$paystate?>">
			<input type=hidden name=delivery_type value="<?=$delivery_type?>">
			<input type=hidden name=ord_flag value="<?=$ord_flag?>">
			<input type=hidden name=paymethod value="<?=$paymethod?>">
			</form>

			<form name=member_form action="member_list.php" method=post>
			<input type=hidden name=search>
			</form>

			<form name=sender_form action="order_namesearch.php" method=post>
			<input type=hidden name=search>
			</form>

            <form name=allexcelform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=orderby value="<?=$orderby?>">
			<input type=hidden name=s_check value="<?=$s_check?>">
			<input type=hidden name=search value="<?=$search?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=staff_order value="<?=$staff_order?>"> <!-- 스테프관련 추가 (2016.05.11 - 김재수) -->
			<input type=hidden name=s_date value="<?=$s_date?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
			<input type=hidden name=com_name value="<?=$com_name?>">
			</form>

			<form name=downexcelform action="order_excel_sel_popup.php" method=post>
			<input type=hidden name="item_type" value="order_delivery">
			<input type=hidden name="excel_sql" value="<?=$excel_sql?>">
			<input type=hidden name="excel_sql_orderby" value="<?=$excel_sql_orderby?>">
			<input type=hidden name="idxs">
			</form>

			<?php if($vendercnt>0){?>
			<form name=vForm action="vender_infopop.php" method=post>
			<input type=hidden name=vender>
			</form>
			<?php }?>

            <form name=dvform action="dvcode_indb.php" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=idx>
			<input type=hidden name=ordercode>
			<input type=hidden name=delicom>
			<input type=hidden name=deliname>
			<input type=hidden name=dvcode>
			</form>

			<form name=dvsform action="dvcode_all_select_indb.php" method=post>
			<input type=hidden name=mode>
			<input type=hidden name=idxs>
			<input type=hidden name=ordercodes>
			<input type=hidden name=delicoms>
			<input type=hidden name=delinames>
			<input type=hidden name=dvcodes>
			</form>

            <form name=crmview method="post" action="crm_view.php">
            <input type=hidden name=id>
            </form>
<?if($_SERVER["REMOTE_ADDR"] == "218.234.32.36"){?>
            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
<?}else{?>
            <IFRAME name="HiddenFrame" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
<?}?>
            <?php
                // =======================================================================
                // 송장정보 일괄 업데이트
                // =======================================================================
            ?>
            <tr>
                <td background="images/counter_blackline_bg.gif" class="font_white" align="center" height="40">
                <!--td align="center" height="40"-->
                    <form name="dv_all_form" action="dvcode_all_indb_v2.php" enctype="multipart/form-data" method="post" onSubmit="return submit_data();">
                        <input type="hidden" name="mode" value="updatedvcode" >
                        송장정보 일괄 업데이트 <input type="file" name="csv_file" id="csv_file" alt="csv파일" accept=".csv">
                        <input type="submit" value="일괄 업데이트" >
                    </form>
                </td>
            </tr>

			<tr>
				<td height=20></td>
			</tr>

			<tr>
				<td>
				<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>배송준비중 주문리스트</span></dt>
							<dd>
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
