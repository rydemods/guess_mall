<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###########
$PageCode = "st-1";
$MenuCode = "counter";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//print_r($_POST);
################## 가입경로 쿼리 ########################
$referer2 = '';
$ref_qry="select idx,name from tblaffiliatesinfo order by name";
$ref2_result=pmysql_query($ref_qry);
#########################################################

################## 발전기금 세팅값 ######################
$fund_sql = "select amt_s, amt_e, per from tbldeveloperfund order by idx asc";
$fund_ret = pmysql_query($fund_sql);
while($f_ret = pmysql_fetch_object($fund_ret)){
    $fund[] = $f_ret;
}
//print_r($fund);
#########################################################

$s_date=$_POST["s_date"];
if(ord($s_date)==0) $s_date="ordercode";
if(!preg_match("/^(bank_date|deli_date|ordercode)$/", $s_date)) {
	$s_date="ordercode";
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));
$period[4] = date("Y-m-d",strtotime('-3 month'));
$period[5] = date("Y-m-d",strtotime('-6 month'));

$search_start = $_POST["search_start"];
$search_end = $_POST["search_end"];
$referer2 = $_POST["referer2"];
$selected[referer2][$referer2]='selected';

$search_start = $search_start?$search_start:$period[3];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

$qry_from = "
        FROM  tblorderinfo a 
        JOIN tblmember b on a.id = b.id 
        LEFT join tblaffiliatesinfo c on b.mb_referrer2 = c.idx::varchar 
            ";
$qry = "WHERE a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";

//입금
$qry .= "AND ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";

if($referer2) {
    $qry .= " AND c.idx = {$referer2} ";
}

include("header.php"); 


$query = "SELECT COUNT(*) 
            From (
                SELECT count(a.ordercode) as cnt, sum(a.price) as price, sum(a.deli_price) as deli_price, sum(a.dc_price::integer) as dc_price, sum(a.reserve) as use_point, c.name 
                ".$qry_from."
                ".$qry."
                group by c.name
            ) a 
        ";
$paging = new Paging($query,10,30);
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

$sql = "SELECT count(a.ordercode) as cnt, sum(a.price) as price, sum(a.deli_price) as deli_price, sum(a.dc_price::integer) as dc_price, sum(a.reserve) as use_point, c.name 
        ".$qry_from."
        ".$qry."
        group by c.name 
        ORDER BY c.name asc
        ";
$sql = $paging->getSql($sql);
$result=pmysql_query($sql,get_db_conn());
//echo "sql = ".$sql."<br>";
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="counter_developerfund.php";
	document.form1.submit();
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

function OrderExcel() {
    //document.form1.target = "_blank";
	document.form1.action="counter_developerfund_excel.php";
	document.form1.submit();
	document.form1.action="";
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 통계분석  &gt; 발전기금 &gt;<span>발전기금</span></p></div></div>
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
			<?php include("menu_counter.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">발전기금 통계</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>일자별 발전기금 현황을 확인/처리하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">발전기금현황 조회</span></div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
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
								<!-- <img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)"> -->
                                <img src=images/orderlist_1month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
                                <img src=images/orderlist_3month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
                                <img src=images/orderlist_6month.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(5)">
							</td>

                        <TR>
                            <th><span>적립경로</span></th>
                            <TD>
                                <select name=referer2 class="select">
                                    <option value="">==== 전체 ====</option>
<?
                                while($ref2_data=pmysql_fetch_object($ref2_result)){?>
                                    <option value="<?=$ref2_data->idx?>" <?=$selected[referer2][$ref2_data->idx]?>><?=$ref2_data->name?></option>
<?}?>
                                </select>&nbsp;
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
				<td style="padding-top:4pt;" align="center"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel1.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>

			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td style="padding-bottom:3pt;">
<?php


		$colspan=10;
		if($vendercnt>0) $colspan++;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="372">&nbsp;</td>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<!-- <col width=40></col>
				<col width=80></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<col width=100></col>
				<input type=hidden name=chkordercode> -->
			
				<TR >
					<th>번호</th>
					<th>적립경로</th>
					<th>구매건수</th>
					<th>총금액</th>
					<th>쿠폰할인</th>
					<th>사용포인트</th>
					<th>배송비</th>
					<th>실결제금액</th>
					<th>발전기금</th>
				</TR>

<?php
		$colspan=10;

		$cnt=0;
		$thisordcd="";
		$thiscolor="#FFFFFF";
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

            if( ($number%2)==0 ) $thiscolor="#FEF8ED";
            else $thiscolor="#FFFFFF";

			$tot_price		= $row->price-$row->dc_price-$row->use_point+$row->deli_price;
			$fund_price	= GetFundAmt($tot_price);
			
?>
                <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=number_format($number)?></td>
                    <td><?=$row->name?></td>
                    <td><?=number_format($row->cnt)?></td>
                    <td><?=number_format($row->price)?></td>
                    <td><?=number_format($row->dc_price)?></td>
                    <td><?=number_format($row->use_point)?></td>
                    <td><?=number_format($row->deli_price)?></td>
                    <td><?=number_format($tot_price)?></td>
                    <td><?=number_format($fund_price)?></td>
                </tr>
<?
			$cnt++;
		}
		pmysql_free_result($result);
		if($t_count==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td align="center">
				<table cellpadding="0" cellspacing="0" width="100%">
<?php				
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\"><p align=\"center\">\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=s_date value="<?=$s_date?>">
            <input type=hidden name=referer2 value="<?=$referer2?>">
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
							<dt><span>-</span></dt>
							<dd>-</dd>
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
<?
function GetFundAmt($actualamt) {
    global $fund;
	$return_amt	= 0;
    foreach($fund as $k => $v) {
		if ($return_amt == 0 && $actualamt > $v->amt_s && $actualamt <= $v->amt_e) $return_amt	= (0.01 * $v->per) * $actualamt;			
    }
	return $return_amt;
}
?>
<?=$onload?>
<?php 
include("copyright.php");
?>