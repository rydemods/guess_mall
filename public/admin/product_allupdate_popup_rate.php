<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "pr-1";
$MenuCode = "product";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

include_once("../lib/adminlib.php");
include_once("../conf/config.php");

//exdebug($_POST);

$mode               = $_POST['mode'];
$margin_rate        = trim($_POST['d_rate']);
$arrProdList        = $_POST['prod_list'];
$start_date         = str_replace("-", "", $_POST['start_date']);
$end_date           = str_replace("-", "", $_POST['end_date']);
$ridx               = 0;


if ($mode == "update_rate") {

    if($start_date) {
        $ret_dup = CheckDupPeriod($arrProdList, $start_date, $end_date);

        if(!$ret_dup) {
            alert_close("기존 기간 설정 적용된 상품과 기간이 중복되는 상품이 있습니다!!");
            exit;
        }
    } else {
        $ret_prod = CheckDupProcut($arrProdList);
    
        if(!$ret_prod) {
            alert_close("기존 기간 설정 적용중인 상품과 중복된 상품이 있습니다!!");
            exit;
        }
    }
}

// 마진율 %로 적용
if ( $mode == "update_rate" ) {

    if($start_date) $ridx = GetRidx();

    foreach ( $arrProdList as $productcode) {
        $sql  = "SELECT rate FROM tblproduct WHERE productcode = '${productcode}' ";
        list($old_rate) = pmysql_fetch($sql);

        $flagResult = true;

        BeginTrans();
        try {
            if($start_date == "" || $start_date == date("Ymd")) {
                // 마진율 업데이트
                $sql  = "UPDATE tblproduct ";
                $sql .= "SET rate = {$margin_rate}, modifydate = now() ";
                $sql .= "WHERE productcode = '${productcode}'";
                $result = pmysql_query($sql, get_db_conn());
                //echo "sql = ".$sql."<br>";
                if ( empty($result) ) {
                    throw new Exception('Insert Fail');
                }
            }

            // 로그 남기기
            $sql  = "INSERT INTO tblbatchapplyrate_log ";
            $sql .= "( productcode, old_rate, new_rate, id, start_date, end_date, ridx, date ) VALUES ";
            $sql .= "( '{$productcode}', {$old_rate}, {$margin_rate}, '".$_ShopInfo->id."', '{$start_date}', '{$end_date}', {$ridx}, '".date("YmdHis")."' )";
            $result = pmysql_query($sql, get_db_conn());
            //echo "sql = ".$sql."<br>";
            if ( empty($result) ) {
                throw new Exception('Insert Fail');
            }
        } catch (Exception $e) {
            $flagResult = false;
            RollbackTrans();
        }
        CommitTrans();

    }
?>

<script type="text/javascript">
    alert("적용되었습니다.");
    window.opener.location.reload();
    window.close();
</script>

<?php
    exit;
}

// 기간설정된 상품들의 그룹코드 구하기.
function GetRidx() {
    list($ridx) = pmysql_fetch("Select max(ridx) from tblbatchapplyrate_log");
    if($ridx == 0) $ridx = 100001;
    else $ridx += 1;

    return $ridx;
}

// 기간 등록시 기존 기간설정된 내역 있는지 중복체크
function CheckDupPeriod($arrProdList, $p_start, $p_end) {
    
    $product = "('".implode("','", $arrProdList)."')";

    $sql = "select  count(*)  
            from    tblbatchapplyrate_log 
            where   productcode in ".$product."  
            and	    ridx > 0 
            and     ( (start_date <= '".$p_start."' and end_date >= '".$p_start."') or (start_date <= '".$p_end."' and end_date >= '".$p_end."') or (start_date >= '".$p_start."' and end_date <= '".$p_end."')  )
            ";
    list($cnt) = pmysql_fetch($sql);
    exdebug($sql);

    if($cnt > 0) return false;
    else return true;
}

// 즉시 업데이트시, 기간 설정된 내역과 중복되는 상품이 있는지 체크
function CheckDupProcut($arrProdList) {
    
    $product = "('".implode("','", $arrProdList)."')";

    $sql = "select  count(*)  
            from    tblbatchapplyrate_log 
            where   productcode in ".$product."  
            and	    ridx > 0 
            and     (start_date <= '".date("Ymd")."' and end_date >= '".date("Ymd")."') 
            ";
    list($cnt) = pmysql_fetch($sql);
    exdebug($sql);

    if($cnt > 0) return false;
    else return true;
}

?>

<link rel="stylesheet" href="style.css">
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">
    $(document).ready(function(){

    });

    // 마진율 % 로 적용
    function btn_submit() {
        var margin_rate = $("#margin_rate").val().trim();
        var start_date = $("#start_date").val().trim();
        var end_date = $("#end_date").val().trim();

        if ( margin_rate == "" ) {
            alert("마진율을 입력해주세요.");
            $("#margin_rate").val("").focus();
            return false;
        }

        var pattern = /^[0-9]+(.[0-9]+)?$/; 
        if ( pattern.test(margin_rate) === false ) {
            alert("정수나 소수만 입력 가능합니다.");
            $("#margin_rate").val("").focus();
            return false;
        }

        if(start_date != "" || end_date != "") {
            if(start_date == "") {
                alert("시작기간을 입력해주세요.");
                $("#start_date").val("").focus();
                return false;
            }

            if(end_date == "") {
                alert("종료기간을 입력해주세요.");
                $("#end_date").val("").focus();
                return false;
            }
        }

        document.form1.mode.value = "update_rate";
        document.form1.d_rate.value = margin_rate;
        document.form1.start_date.value = start_date;
        document.form1.end_date.value = end_date;
        document.form1.submit();
    }
</script>
<!-- 라인맵 -->

	<table cellpadding="10" cellspacing="0" width="100%" style="table-layout:fixed">
    <tr><td height="20"></td></tr>
	<tr>
		<td>
			<div class="table_style02">
				<table width=98% cellpadding=1 cellspacing=1 border=0 style="border-collapse:collapse; border:1px gray solid;">
					<colgroup>
						<col width="20%" />
						<col width="auto" />
                        <col width="200" />
                        <col width="80" />
					</colgroup>
					<tr>
						<th>항목</th>
						<th>내용</th>
                        <th>기간설정</th>
                        <th>적용</th>
					</tr>
					<tr>
						<td style="border:1px gray solid;">마진율 ( % )</td>
						<td style="border:1px gray solid; text-align:left; padding-left:5px;"><input type="text" name="margin_rate" id="margin_rate" style="border:1px gray solid; text-align:right; padding-right:5px;"/> %로 적용</td>
                        <td style="border:1px gray solid;text-align:center;">
                            <input class="input_bd_st01" type="text" name="start_date" id="start_date" OnClick="Calendar(event)"/> ~ <input class="input_bd_st01" type="text" name="end_date" id="end_date" OnClick="Calendar(event)"/>
                        </td>
                        <td style="border:1px gray solid;text-align:center;"><a href="javascript:;" onClick="javascript:btn_submit();"><img src="images/btn_cate_reg.gif" border="0"></a></td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
    <tr>
        <td>
            <iframe src="./product_allupdate_period_rate.php" width="100%" height="630" frameborder=0 scrolling="auto"></iframe>
        </td>
    </tr>
	</table>

<form name="form1" id="form1" action="<?=$_SERVER['PHP_SELF']?>" method="POST"> 
    <input type="hidden" name="mode" >
    <input type="hidden" name="d_rate" >
    <input type="hidden" name="start_date" >
    <input type="hidden" name="end_date" >

    <?php foreach ( $arrProdList as $prodCode ) { ?>
        <input type="hidden" name="prod_list[]" value="<?=$prodCode?>">
    <?php } ?>
</form>

