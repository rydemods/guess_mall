<?php
/********************************************************************* 
// 파 일 명		: order_catesale.php 
// 설     명		: 카테고리 매출분석
// 상세설명	: 카테고리별로 매출을 보여줌(카테고리별, 기간별, 벤더별)
// 작 성 자		: 2015.11.26 - 김재수
// 수 정 자		: 
// 
// 
/**
 *  deli_gbn은 배송기준의 값이다.
 *  따라서 배송이 된거는 Y, 
 *  배송이 안된 이전 단계는 무조건 미처리다.(미입금 & 발송준비)
 *  그러나, 매출은 입금된 주문 기준이 맞으므로, 미입금건은 제외하기로 한다.
 *  또한, 매출금액은 총금액-쿠폰할인-사용포인트+배송비로 변경한다.
 *  그러나, 상품별/카테고리별 매출조회는 주문기준이 아니므로, 사용포인트나 배송비등의 정보가 나누어져있지 않다.
 *  따라서, 상품별/카테고리별 매출조회는 상품별 판매가 기준으로 표시한다.
 *  2015-12-16 jhjeong
**/
/*********************************************************************/ 
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

####################### 페이지 접근권한 check ###############
$PageCode = "or-3";
$MenuCode = "order";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

//print_r($_POST);
################## 가입경로 쿼리 ################
$referer1 = '';
$ref_qry="select idx,name from tblaffiliatesinfo order by name";
$ref1_result=pmysql_query($ref_qry);
#########################################################

$code_a=$_REQUEST["code_a"];
$code_b=$_REQUEST["code_b"];
$code_c=$_REQUEST["code_c"];
$code_d=$_REQUEST["code_d"];

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

$search_start = $_POST["search_start"];
$search_end = $_POST["search_end"];
$sel_vender = $_POST["sel_vender"];
$com_name = $_POST["com_name"];  // 벤더이름 검색

$search_start = $search_start?$search_start:$period[0];
$search_end = $search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s = $search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e = $search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}



/*SELECT 
b.code_a as code_a,
(select code_name from tblproductcode where code_a=b.code_a and code_b='000' and code_c='000' and code_d='000') as code_a_name, 
b.code_b as code_b, 
(select code_name from tblproductcode where code_a=b.code_a and code_b=b.code_b and code_c='000' and code_d='000') as code_b_name,
b.code_c as code_c, 
(select code_name from tblproductcode where code_a=b.code_a and code_b=b.code_b and code_c=b.code_c and code_d='000') as code_c_name,
b.code_d as code_d, 
(select code_name from tblproductcode where code_a=b.code_a and code_b=b.code_b and code_c=b.code_c and code_d=b.code_d and code_d!='000') as code_d_name,
SUM(CASE WHEN a.deli_gbn='Y' THEN b.option_quantity ELSE 0 END) as ycnt, 
SUM(CASE WHEN a.deli_gbn='N' OR a.deli_gbn='S' THEN b.option_quantity ELSE 0 END) as ncnt, 
SUM(CASE WHEN a.deli_gbn='R' THEN b.option_quantity ELSE 0 END) as rcnt, 
SUM(CASE WHEN a.deli_gbn='Y' THEN (b.price+b.option_price)*b.option_quantity ELSE 0 END) as ysum, 
SUM(CASE WHEN a.deli_gbn='N' OR a.deli_gbn='S' THEN (b.price+b.option_price)*b.option_quantity ELSE 0 END) as nsum, 
SUM(CASE WHEN a.deli_gbn='R' THEN (b.price+b.option_price)*b.option_quantity ELSE 0 END) as rsum 
FROM tblorderinfo a, (select 
SUBSTR(tpl.c_category,1,3) code_a, 
SUBSTR(tpl.c_category,4,3) code_b,
SUBSTR(tpl.c_category,7,3) code_c,
SUBSTR(tpl.c_category,10,3) code_d,
top.*  from tblorderproduct top left join tblproductlink tpl on top.productcode = tpl.c_productcode) b WHERE a.ordercode = b.ordercode 
AND a.ordercode >= '20151101000000' AND a.ordercode <= '20151130235959' 
and b.vender='3' 
GROUP BY code_a, code_b, code_c, code_d 
order by code_a, code_b, code_c, code_d*/




// 테이블 정보
$qry_from  = "tblorderinfo a, ";
$qry_from .= "(select ";
$qry_from .= "SUBSTR(tpl.c_category,1,3) code_a, ";
$qry_from .= "SUBSTR(tpl.c_category,4,3) code_b, ";
$qry_from .= "SUBSTR(tpl.c_category,7,3) code_c, ";
$qry_from .= "SUBSTR(tpl.c_category,10,3) code_d, ";
//$qry_from .= "top.*  from tblorderproduct top left join tblproductlink tpl on top.productcode = tpl.c_productcode) b ";
$qry_from .= "top.*  from tblorderproduct top left join tblproductlink tpl on top.productcode = tpl.c_productcode and tpl.c_maincate = 1) b, tblvenderinfo v ";

$qry.= "WHERE a.ordercode = b.ordercode AND a.deli_gbn IN ('Y','N','R','S') "; 
// 매출이므로 입금된 건만 조회 2015-12-16 jhjeong
$qry.= "and ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000')) ";

//기간 검색시
if(substr($search_s,0,8)==substr($search_e,0,8)) {
	$qry.= "AND sabangnet_idx ='' AND a.ordercode LIKE '".substr($search_s,0,8)."%' ";
} else {
	$qry.= "AND sabangnet_idx ='' AND a.ordercode>='{$search_s}' AND a.ordercode <='{$search_e}' ";
}

if ($code_a) $qry.= "AND b.code_a ='{$code_a}' ";
if ($code_b) $qry.= "AND b.code_b ='{$code_b}' ";
if ($code_c) $qry.= "AND b.code_c ='{$code_c}' ";
if ($code_d) $qry.= "AND b.code_d ='{$code_d}' ";

$qry.="AND	b.vender = v.vender ";
// 벤더 검색시
if($sel_vender || $com_name) {
    if($com_name) $qry.= " AND v.com_name like '%".strtoupper($com_name)."%' ";
    else if($sel_vender) $qry.= " AND v.vender = ".$sel_vender." ";
}

$groupby .= "GROUP BY code_a, code_b, code_c, code_d ";
$orderby .= "order by code_a, code_b, code_c, code_d";

//벤더 정보를 가져온다.
$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
	$result=pmysql_query($sql,get_db_conn());
	while($row=pmysql_fetch_object($result)) {
		$venderlist[$row->vender]=$row;
	}
	pmysql_free_result($result);
}


include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script type="text/javascript" src="codeinit.js.php"></script>
<script type="text/javascript" src="<?=$Dir?>lib/DropDown.admin.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="order_catesale.php";
	document.form1.submit();
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

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 장바구니 및 매출 분석 &gt;<span>카테고리 매출분석</span></p></div></div>
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
					<div class="title_depth3">카테고리 매출분석</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>카테고리별로 매출정보를 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">매출현황 조회</span></div>
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
						<tr>
							<th><span>카테고리 검색</span></th>
							<td>
				<?php
								$sql = "SELECT * FROM tblproductcode WHERE group_code!='NO' ";
								//$sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY code_a,code_b,code_c,code_d DESC ";
                                $sql.= "AND (type!='T' AND type!='TX' AND type!='TM' AND type!='TMX') ORDER BY cate_sort ";
								$i=0;
								$ii=0;
								$iii=0;
								$iiii=0;
								$strcodelist = "";
								$strcodelist.= "<script>\n";
								$result = pmysql_query($sql,get_db_conn());
								$selcode_name="";

								while($row=pmysql_fetch_object($result)) {
									$strcodelist.= "var clist=new CodeList();\n";
									$strcodelist.= "clist.code_a='{$row->code_a}';\n";
									$strcodelist.= "clist.code_b='{$row->code_b}';\n";
									$strcodelist.= "clist.code_c='{$row->code_c}';\n";
									$strcodelist.= "clist.code_d='{$row->code_d}';\n";
									$strcodelist.= "clist.type='{$row->type}';\n";
									$strcodelist.= "clist.code_name='{$row->code_name}';\n";
									if($row->type=="L" || $row->type=="T" || $row->type=="LX" || $row->type=="TX") {
										$strcodelist.= "lista[{$i}]=clist;\n";
										$i++;
									}
									if($row->type=="LM" || $row->type=="TM" || $row->type=="LMX" || $row->type=="TMX") {
										if ($row->code_c=="000" && $row->code_d=="000") {
											$strcodelist.= "listb[{$ii}]=clist;\n";
											$ii++;
										} else if ($row->code_d=="000") {
											$strcodelist.= "listc[{$iii}]=clist;\n";
											$iii++;
										} else if ($row->code_d!="000") {
											$strcodelist.= "listd[{$iiii}]=clist;\n";
											$iiii++;
										}
									}
									$strcodelist.= "clist=null;\n\n";
								}
								pmysql_free_result($result);
								$strcodelist.= "CodeInit();\n";
								$strcodelist.= "</script>\n";

								echo $strcodelist;


								echo "<select name=code_a style=\"width:170px;\" onchange=\"SearchChangeCate(this,1)\">\n";
								echo "<option value=\"\">〓〓 1차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_b style=\"width:170px;\" onchange=\"SearchChangeCate(this,2)\">\n";
								echo "<option value=\"\">〓〓 2차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_c style=\"width:170px;\" onchange=\"SearchChangeCate(this,3)\">\n";
								echo "<option value=\"\">〓〓 3차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<select name=code_d style=\"width:170px;\">\n";
								echo "<option value=\"\">〓〓 4차 카테고리 〓〓</option>\n";
								echo "</select>\n";

								echo "<script>SearchCodeInit(\"".$code_a."\",\"".$code_b."\",\"".$code_c."\",\"".$code_d."\");</script>";
				?>
							</td>
						</tr>
						<TR>
							<th><span>기간선택</span></th>
							<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
							</td>
<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th><span>벤더검색</span></th>
                            <td><select name=sel_vender class="select">
                                <option value="">==== 전체 ====</option>
<?php
                        foreach($venderlist as $key => $val) {
                            echo "<option value=\"{$val->vender}\"";
                            if($sel_vender==$val->vender) echo " selected";
                            echo ">{$val->com_name}</option>\n";
                        }
?>
                                </select> 
                                <input type=text name=com_name value="<?=$com_name?>" style="width:197" class="input">
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
				<td style="padding-top:4pt;" align="center"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<tr>
				<td style="padding-bottom:3pt;">
<?php		
		$sql  = "SELECT ";
		$sql .= "b.code_a as code_a, ";
		$sql .= "(select code_name from tblproductcode where code_a=b.code_a and code_b='000' and code_c='000' and code_d='000') as code_a_name, ";
		$sql .= "b.code_b as code_b, ";
		$sql .= "(select code_name from tblproductcode where code_a=b.code_a and code_b=b.code_b and code_c='000' and code_d='000') as code_b_name, ";
		$sql .= "b.code_c as code_c, ";
		$sql .= "(select code_name from tblproductcode where code_a=b.code_a and code_b=b.code_b and code_c=b.code_c and code_d='000') as code_c_name, ";
		$sql .= "b.code_d as code_d, ";
		$sql .= "(select code_name from tblproductcode where code_a=b.code_a and code_b=b.code_b and code_c=b.code_c and code_d=b.code_d and code_d!='000') as code_d_name, ";
		$sql .= "SUM(CASE WHEN a.deli_gbn='Y' THEN b.option_quantity ELSE 0 END) as ycnt, ";
		//$sql .= "SUM(CASE WHEN a.deli_gbn='N' OR a.deli_gbn='S' THEN b.option_quantity ELSE 0 END) as ncnt, ";
        $sql .= "SUM(CASE WHEN (a.deli_gbn='N' and ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000'))) OR a.deli_gbn='S' THEN b.option_quantity ELSE 0 END) as ncnt, ";
		$sql .= "SUM(CASE WHEN a.deli_gbn='R' THEN b.option_quantity ELSE 0 END) as rcnt, ";
		$sql .= "SUM(CASE WHEN a.deli_gbn='Y' THEN (b.price+b.option_price)*b.option_quantity ELSE 0 END) as ysum, ";
		//$sql .= "SUM(CASE WHEN a.deli_gbn='N' OR a.deli_gbn='S' THEN (b.price+b.option_price)*b.option_quantity ELSE 0 END) as nsum, ";
        $sql .= "SUM(CASE WHEN (a.deli_gbn='N' and ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000'))) OR a.deli_gbn='S' THEN (b.price+b.option_price)*b.option_quantity ELSE 0 END) as nsum, ";
		$sql .= "SUM(CASE WHEN a.deli_gbn='R' THEN (b.price+b.option_price)*b.option_quantity ELSE 0 END) as rsum ";
        $sql.= "FROM {$qry_from} {$qry} ";
		$sql.= "{$groupby} {$orderby} ";
		$result=pmysql_query($sql,get_db_conn());
		$t_count=pmysql_num_rows($result);
        //echo "sql = ".$sql."<br>";
		$colspan=7;
?>
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<table border=0 cellpadding=0 cellspacing=0 width=100%>
				<col width=></col>
				<col width=100></col>
				<col width=150></col>
				<col width=100></col>
				<col width=150></col>
				<col width=100></col>
				<col width=150></col>			
				<TR >
					<th>카테고리</th>
					<th>미처리건수</th>
					<th>미처리금액</th>
					<th>반송건수</th>
					<th>반송금액</th>
					<th>배송건수</th>
					<th>배송금액</th>
				</TR>

<?php

		$cnt			= 0;
		$Ysumtot	= 0;
		$Rsumtot	= 0;
		$Nsumtot	= 0;

		$Ycnttot		= 0;
		$Rcnttot		= 0;
		$Ncnttot		= 0;

		while($row=pmysql_fetch_object($result)) {
			$code_name	= "";
			if ($row->code_a_name) $code_name	.= $row->code_a_name;
			if ($row->code_b_name) $code_name	.= " > ".$row->code_b_name;
			if ($row->code_c_name) $code_name	.= " > ".$row->code_c_name;
			if ($row->code_d_name) $code_name	.= " > ".$row->code_d_name;

			if ($code_name == "") $code_name = "카테고리 없음";

			$Ncnttot+=$row->ncnt;
			$Rcnttot+=$row->rcnt;
			$Ycnttot+=$row->ycnt;

			$Nsumtot+=$row->nsum;
			$Rsumtot+=$row->rsum;
			$Ysumtot+=$row->ysum;

			echo "<tr bgcolor='#FFFFFF'>\n";
			echo "	<td>".$code_name."</td>\n";
			echo "	<td>".number_format($row->ncnt)."건</td>\n";
			echo "	<td>".number_format($row->nsum)."원</td>\n";
			echo "	<td>".number_format($row->rcnt)."건</td>\n";
			echo "	<td>".number_format($row->rsum)."원</td>\n";
			echo "	<td>".number_format($row->ycnt)."건</td>\n";
			echo "	<td>".number_format($row->ysum)."원</td>\n";
			echo "</tr>\n";

			$cnt++;
		}
		pmysql_free_result($result);
		if($cnt==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		} else {

			echo "<tr bgcolor='#FFFFFF'>\n";
			echo "	<td height=50>&nbsp;</td>\n";
			echo "	<td><b>".number_format($Ncnttot)."건</b></td>\n";
			echo "	<td><b>".number_format($Nsumtot)."원</b></td>\n";
			echo "	<td><b>".number_format($Rcnttot)."건</b></td>\n";
			echo "	<td><b>".number_format($Rsumtot)."원</b></td>\n";
			echo "	<td><b>".number_format($Ycnttot)."건</b></td>\n";
			echo "	<td><b>".number_format($Ysumtot)."원</b></td>\n";
			echo "</tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
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
							<dt><span>카테고리 매출분석</span></dt>
							<dd>
								- 주문리스트에 등록되어 있는 주문건을 기준으로 산출되며 배송/반송/미처리로 구분되어 출력됩니다.<br>
								- 카테고리별/기간별/벤더별 검색이 가능합니다.
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
