<?php // hspark
header("Location: order_list_all_order.php");   // 전체주문조회(주문별)로 이동

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

$curDate = date("Ymd");
$curMon = date("Ym");
//$yesterday = date("Y-m-d", strtotime($curDate." -1 days"));

############## 오늘의 할일 (오늘 날짜 이전)
//입금전
$sql = "select count(ordercode) from tblorderinfo where oi_step1 < 1 AND oi_step2 = 0 AND paymethod in ('B', 'OA','QA') ";
list($cnt_00) = pmysql_fetch($sql);
//$url_cnt_00 = "./order_list_all_order.php?s_date=ordercode&search_start=2015-01-01&search_end=".date("Y-m-d")."&oistep1=0&paystate=N&paymethod[]=B&paymethod[]=OA&paymethod[]=QA";
$url_cnt_00 = "./order_list_misu.php";
//exdebug($cnt_00);

//결제완료(배송준비 대기)
$sql = "SELECT count(ordercode) FROM tblorderinfo WHERE ordercode <= '".$curDate."235959' AND (oi_step1 in (1) And oi_step2 = 0) ";
list($cnt_10) = pmysql_fetch($sql);
$url_cnt_10 = "./order_list_all_order.php?s_date=ordercode&search_start=2015-01-01&search_end=".$curDate."&oistep1=1&paystate=Y";
//exdebug($cnt_10);

//배송준비중
$sql = "SELECT count(ordercode) FROM tblorderproduct WHERE ordercode <= '".$curDate."235959' AND op_step = 2 ";
list($cnt_20) = pmysql_fetch($sql);
$url_cnt_20 = "./order_list_delivery.php?s_date=ordercode&search_start=2015-01-01&search_end=".$curDate;
//exdebug($cnt_20);

//배송중
$sql = "SELECT count(ordercode) FROM tblorderproduct WHERE ordercode <= '".$curDate."235959' AND op_step = 3 ";
list($cnt_30) = pmysql_fetch($sql);
$url_cnt_30 = "./order_list_all.php?s_date=ordercode&search_start=2015-01-01&search_end=".$curDate."&oistep1=3";
//exdebug($cnt_30);

//교환신청
$sql = "select count(*) 
        from tblorderproduct op 
        join tblorder_cancel oc on oc.oc_no = op.oc_no 
        where 1=1 
        and op.op_step = 40 
        and op.redelivery_type = 'G' 
        and oc.pickup_state in ('R') 
        AND oc.restore ='N' 
    ";
list($cnt_R) = pmysql_fetch($sql);
//$url_cnt_R = "./order_list_cancel_step34_change_view.php?opstep=40";
$url_cnt_R = "./order_list_cancel_step34_change.php";
//exdebug($cnt_R);

//반품신청
$sql = "select count(*) 
        from tblorderproduct op 
        join tblorder_cancel oc on oc.oc_no = op.oc_no 
        where 1=1 
        and op.op_step = 40 
        and op.redelivery_type = 'Y' 
        and oc.pickup_state in ('R') 
        AND oc.restore ='N' 
    ";
list($cnt_Y) = pmysql_fetch($sql);
$url_cnt_Y = "./order_list_cancel_step34.php";
//exdebug($cnt_Y);

//환불접수
$sql = "select count(*) from (
            select  oc.oc_no 
            from    tblorderproduct op 
            join    tblorder_cancel oc on oc.oc_no = op.oc_no 
            where   1=1 
            and     op.op_step in ('41','42') 
            and     op.redelivery_type != 'G'  
            and     oc.pickup_state in ('N','Y') 
            AND     oc.restore ='N' 
            AND     (oc.cfindt ='' OR (LENGTH(oc.bankaccount) < 9 AND oc.pgcancel = 'N' ))
            group by oc.oc_no
        ) a
    ";
list($cnt_repay) = pmysql_fetch($sql);
$url_cnt_repay = "./order_list_cancel_steprefund.php";
//exdebug($cnt_repay);
###############################################
############## 오늘 처리한 일
//자동입금확인(무통장)
$sql = "select count(*) from tblbank where bkdate = '".$curDate."' and ordercode != '' ";
list($cnt_B_auto) = pmysql_fetch($sql);

//수동입금확인(무통장)
$sql = "select count(*) from tblorderinfo where paymethod in ('B') and bank_date like '".$curDate."%' and ordercode not in (select ordercode from tblbank where bkdate = '".$curDate."' and ordercode != '') ";
list($cnt_B_man) = pmysql_fetch($sql);

//배송준비중
$sql = "SELECT count(ordercode) FROM tblorderproduct_log WHERE regdt LIKE '".$curDate."%' and step_prev = 1 and step_next = 2 ";
list($cnt_200) = pmysql_fetch($sql);
//exdebug($cnt_200);

//배송중
$sql = "SELECT count(ordercode) FROM tblorderproduct WHERE deli_date LIKE '".$curDate."%' ";
list($cnt_300) = pmysql_fetch($sql);
//exdebug($cnt_300);

//배송완료
$sql = "SELECT count(ordercode) FROM tblorderproduct WHERE order_conf_date LIKE '".$curDate."%' AND op_step = 4 ";
list($cnt_400) = pmysql_fetch($sql);
//exdebug($cnt_400);

//교환완료
$sql = "select  count(*) 
        from    tblorderproduct op 
        join    tblorder_cancel oc on oc.oc_no = op.oc_no 
        where   1=1 
        AND     oc.cfindt LIKE '".$curDate."%' 
        and     op.op_step = 44 
        and     op.redelivery_type = 'G' 
        and     oc.pickup_state in ('Y') 
        AND     oc.restore ='N' 
    ";
list($cnt_G44) = pmysql_fetch($sql);
//exdebug($cnt_G44);

//반품완료
$sql = "select 	count(oc.*) 
        from 	tblorder_cancel oc 
        join	tblorderproduct op on oc.oc_no = op.oc_no 
        where	1=1 
        and	    oc.pickup_date LIKE '".$curDate."%' 
        and	    op.op_step in ('42') 
    ";
list($cnt_Y42) = pmysql_fetch($sql);
//exdebug($cnt_Y42);

//환불완료
$sql = "select 	count(oc.*) 
        from 	tblorder_cancel oc 
        join	tblorderproduct op on oc.oc_no = op.oc_no 
        join	tblorderinfo oi on oc.ordercode = oi.ordercode 
        where	1=1 
        and	    oc.cfindt LIKE '".$curDate."%' 
        and	    op.op_step in ('44') 
        and	    oi.oi_step1 > 0 
    ";
list($cnt_repay44) = pmysql_fetch($sql);
//exdebug($cnt_repay44);
#################################################


include("header.php"); 
?>
<style type="text/css">
/* ==================================================
	탭
================================================== */

.tabs-menu {}
	.tabs-menu:after {display:block; clear:both; content:"";}
	.tabs-menu li {float:left; position:relative; width:33%; height: 31px;line-height: 31px;float: left;background-color: #f0f0f0; box-sizing:border-box; border:1px solid #d3d3d3; border-bottom:1px solid #4b4b4b;}
	.tabs-menu li.on {position: relative;background-color: #fff; z-index: 5; border:1px solid #4b4b4b; border-bottom:1px solid #fff; }
	.tabs-menu li.on:after {display:block; position:absolute; top:0; right:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:last-child::after {display:none;}
	.tabs-menu li.on:before {display:block; position:absolute; top:0; left:-2px; width:1px; height:100%; background:#f0f0f0; content:"";}
	.tabs-menu li.on:first-child::before {display:none;}
	.tabs-menu li a {display:block; font-size:0.8rem; font-weight:bold; color:#aaa; text-align:center;}
	.tabs-menu .on a {color: #4b4b4b;}

.tab-content-wrap {background-color: #fff; }
	.tab-content {display: none;}
	.tab-content-wrap > div:first-child { display: block;}
</style>
<script type="text/javascript">
<!--
$(document).ready(function() {
    $(".tabs-menu a").click(function(event) {
        event.preventDefault();
        $(this).parent().addClass("on");
        $(this).parent().siblings().removeClass("on");
        var tab = $(this).attr("href");
        $(".tab-content").not(tab).css("display", "none");
        $(tab).fadeIn();
    });
});
//-->
</script>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function goSearch(step) {

    if(step == "0") {
        //document.form1.oistep1.value = step;
        document.form1.paystate.value = "Y";
    }

    if(step == "1") {
        document.form1.oistep1.value = "1,2,3,4";
    }

    if(step == "66") {
        document.form1.oi_type.value = step;
    }
    document.form1.action = "order_list_all_order.php";
    document.form1.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 주문조회 및 배송관리 &gt; <span>주문 메인</span></p></div></div>

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
			<table cellpadding="0" cellspacing="0" width="80%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">주문 메인</div>
				</td>
			</tr>
			<tr>
				<td height="10"></td>
			</tr>			


			<tr>
                <td>
                    <div id="tabs-container">
                        <!-- <ul class="tabs-menu">
                            <li class="on"><a href="#tab-1">적립금포함</a></li>
                            <li><a href="#tab-2">적립금제외</a></li>
                            <li><a href="#tab-3">쿠폰할인포함</a></li>
                        </ul> -->
                        <div class="tab-content-wrap">
                            <div id="tab-1" class="tab-content"><iframe src="./order_main_tab_view.php?point=y" width="99%" height="220" frameborder=0 scrolling="no"></iframe></div>
                            <!-- <div id="tab-2" class="tab-content"><iframe src="./order_main_tab_view.php?point=n" width="99%" height="220" frameborder=0 scrolling="no"></iframe></div>
                            <div id="tab-3" class="tab-content"><iframe src="./order_main_tab_view.php?coupon=y" width="99%" height="220" frameborder=0 scrolling="no"></iframe></div> -->
                        </div>
                    </div>
                </td>
            </tr>



            <!-- 하단 -->
            <tr>
                <td>
                    <style type="text/css">
                    .half-layout {}
                        .half-layout:after {display:block; clear:both; content:"";}
                        .half-layout .left {float:left; width:50%; padding-right:10px; box-sizing:border-box;}
                        .half-layout .right {float:right; width:50%; padding-left:10px; box-sizing:border-box;}
                    </style>

                    <div class="half-layout">
                        <div class="left">
                            <div class="title_depth3_sub">오늘의 할 일</span></div>
                            <div class="table_style01">
                            <TABLE cellSpacing=0 cellPadding=0 width="90%" border=0>
                                <TR>
                                    <th><span>입금전</span></th>
                                    <TD class="td_con1" align="right"><b><a href="<?=$url_cnt_00?>" target="_blank"><?=number_format($cnt_00)?></b> 건</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>결제완료(배송준비중 대기)</span></th>
                                    <TD class="td_con1" align="right"><b><a href="<?=$url_cnt_10?>" target="_blank"><?=number_format($cnt_10)?></b> 건</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>배송준비중</span></th>
                                    <TD class="td_con1" align="right"><b><a href="<?=$url_cnt_20?>" target="_blank"><?=number_format($cnt_20)?></b> 건</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>배송중</span></th>
                                    <TD class="td_con1" align="right"><b><a href="<?=$url_cnt_30?>" target="_blank"><?=number_format($cnt_30)?></b> 건</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>교환신청</span></th>
                                    <TD class="td_con1" align="right"><b><a href="<?=$url_cnt_R?>" target="_blank"><?=number_format($cnt_R)?></b> 건</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>반품신청</span></th>
                                    <TD class="td_con1" align="right"><b><a href="<?=$url_cnt_Y?>" target="_blank"><?=number_format($cnt_Y)?></b> 건</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>환불신청</span></th>
                                    <TD class="td_con1" align="right"><b><a href="<?=$url_cnt_repay?>" target="_blank"><?=number_format($cnt_repay)?></b> 건</a>&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                            </TABLE>
                            </div>
                        </div>
                        <div class="right">
                            <div class="title_depth3_sub">오늘 처리한 일</span></div>
                            <div class="table_style01">
                            <TABLE cellSpacing=0 cellPadding=0 width="90%" border=0>
                                <TR>
                                    <th><span>수동입금확인/자동입금확인</span></th>
                                    <TD class="td_con1" align="right"><b><?=number_format($cnt_B_man)?> / <?=number_format($cnt_B_auto)?></b> 건&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>배송준비중 처리</span></th>
                                    <TD class="td_con1" align="right"><b><?=number_format($cnt_200)?></b> 건&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>배송중 처리</span></th>
                                    <TD class="td_con1" align="right"><b><?=number_format($cnt_300)?></b> 건&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>배송완료</span></th>
                                    <TD class="td_con1" align="right"><b><?=number_format($cnt_400)?></b> 건&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>교환완료</span></th>
                                    <TD class="td_con1" align="right"><b><?=number_format($cnt_G44)?></b> 건&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>반품완료</span></th>
                                    <TD class="td_con1" align="right"><b><?=number_format($cnt_Y42)?></b> 건&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                                <TR>
                                    <th><span>환불완료</span></th>
                                    <TD class="td_con1" align="right"><b><?=number_format($cnt_repay44)?></b> 건&nbsp;&nbsp;&nbsp;&nbsp;</TD>
                                </TR>
                            </TABLE>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <form name=form1 method=get target="_self">
			<input type=hidden name="oistep1">
			<input type=hidden name="oi_type">
            <input type=hidden name="paystate">
            <input type=hidden name="search_start" value="<?=date('Y-m').'-01'?>">
            <input type=hidden name="search_end" value="<?=date('Y-m-d')?>">
			</form>

			<tr>
				<td height="150"></td>
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