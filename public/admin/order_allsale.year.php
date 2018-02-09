<?php // hspark
$qry = "WHERE a.ordercode LIKE '{$date_year}%' ";
if($paymethod!="ALL") $qry.= "AND paymethod LIKE '{$paymethod}%' ";
if($loc!="ALL") {
	if($loc=="기타") $qry.= "AND a.loc is NULL ";
	else $qry.= "AND a.loc = '{$loc}' ";
}

/**
 *  deli_gbn은 배송기준의 값이다.
 *  따라서 배송이 된거는 Y, 
 *  배송이 안된 이전 단계는 무조건 미처리다.(미입금 & 발송준비)
 *  그러나, 매출은 입금된 주문 기준이 맞으므로, 미입금건은 제외하기로 한다.
 *  또한, 매출금액은 총금액-쿠폰할인-사용포인트+배송비로 변경한다.
 *  2015-12-16 jhjeong
**/
$sql = "SELECT ";
//$sql.= "COUNT(CASE WHEN a.deli_gbn='Y' THEN 1 ELSE NULL END) as ycnt, ";
//$sql.= "COUNT(CASE WHEN a.deli_gbn='N' OR a.deli_gbn='S' THEN 1 ELSE NULL END) as ncnt, ";
//$sql.= "COUNT(CASE WHEN a.deli_gbn='R' THEN 1 ELSE NULL END) as rcnt, ";
//$sql.= "SUM(CASE WHEN a.deli_gbn ='Y' THEN a.price ELSE NULL END) as ysum, ";
//$sql.= "SUM(CASE WHEN a.deli_gbn ='N' OR a.deli_gbn='S' THEN a.price ELSE NULL END) as nsum, ";
//$sql.= "SUM(CASE WHEN a.deli_gbn ='R' THEN a.price ELSE NULL END) as rsum, ";
$sql.= "COUNT(CASE WHEN a.deli_gbn='Y' THEN 1 ELSE NULL END) as ycnt, ";
$sql.= "COUNT(CASE WHEN (a.deli_gbn='N' and ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000'))) OR a.deli_gbn='S' THEN 1 ELSE NULL END) as ncnt, ";      // 미처리 
$sql.= "COUNT(CASE WHEN a.deli_gbn='R' THEN 1 ELSE NULL END) as rcnt, ";
$sql.= "SUM(CASE WHEN a.deli_gbn ='Y' THEN a.price-a.dc_price::int-a.reserve+a.deli_price ELSE NULL END) as ysum, ";
$sql.= "SUM(CASE WHEN (a.deli_gbn='N' and ((SUBSTR(a.paymethod,1,1) IN ('B','O','Q') AND LENGTH(a.bank_date)=14) OR (SUBSTR(a.paymethod,1,1) IN ('C','P','M','V') AND a.pay_admin_proc!='C' AND a.pay_flag='0000'))) OR a.deli_gbn='S' THEN a.price-a.dc_price::int-a.reserve+a.deli_price ELSE NULL END) as nsum, "; // 미처리
$sql.= "SUM(CASE WHEN a.deli_gbn ='R' THEN a.price-a.dc_price::int-a.reserve+a.deli_price ELSE NULL END) as rsum, ";
$sql.= "SUBSTR(a.ordercode,1,6) as month FROM tblorderinfo a ";

if($age2>0 || $sex!="ALL") {
	$sql.= ", tblmember b {$qry} AND a.id=b.id ";
	if($age1>0) {
		$start_year = (int)date("Y") - (int)$age2 +1;
		$end_year = (int)date("Y") - (int)$age1 +1;
		$s_year = substr((string)$start_year,2,2);
		$e_year = substr((string)$end_year,2,2);

		if ($start_year < 2000 && $end_year < 2000) {
			 $sql.= "AND ((LEFT(b.resno,2) BETWEEN '{$s_year}' AND '{$e_year}') AND SUBSTR(b.resno,7,1) < '3') ";
		} else if ($start_year < 2000 && $end_year > 1999) {
			 $sql.= "AND (((LEFT(b.resno,2) BETWEEN '{$s_year}' AND '99') AND SUBSTR(b.resno,7,1) < '3') ";
			 $sql.= "OR ((LEFT(b.resno,2) BETWEEN '00' AND '{$e_year}') ";
			 $sql.= "AND SUBSTR(b.resno,7,1) > '2')) ";
		} else if ($start_year > 1999 && $end_year > 1999) {
			$sql.= "AND ((LEFT(b.resno,2) BETWEEN '{$s_year}' AND '{$e_year}') ";
			$sql.= "AND SUBSTR(b.resno,7,1) > '2') ";
		}
	}
	if($sex=="M") {
		$sql.= "AND SUBSTR(b.resno,7,1)='1' ";
	} else if ($sex=="F") {
		$sql.= "AND SUBSTR(b.resno,7,1)='2' ";
	}
} else {
	$sql.= $qry." ";
	if($member=="Y") {
		$sql.= "AND SUBSTR(a.ordercode,21,1)!='X' ";
	} else if($member=="N") {
		$sql.= "AND SUBSTR(a.ordercode,21,1)='X' ";
	}
}
$sql.= "GROUP BY month ";

$MAX_barsize=470;
$Ysumtot=0;
$Rsumtot=0;
$Nsumtot=0;
$maxsum=0;

$Ycnttot=0;
$Rcnttot=0;
$Ncnttot=0;

$result = pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$month=(int)substr($row->month,4,2);
	$Ysum[$month]=$row->ysum;
	$Rsum[$month]=$row->rsum;
	$Nsum[$month]=$row->nsum;
	if($row->ysum>$maxsum) $maxsum=$row->ysum;
	if($row->rsum>$maxsum) $maxsum=$row->rsum;
	if($row->nsum>$maxsum) $maxsum=$row->nsum;

	$Ycnt[$month]=$row->ycnt;
	$Rcnt[$month]=$row->rcnt;
	$Ncnt[$month]=$row->ncnt;

	$Ysumtot+=$row->ysum;
	$Rsumtot+=$row->rsum;
	$Nsumtot+=$row->nsum;

	$Ycnttot+=$row->ycnt;
	$Rcnttot+=$row->rcnt;
	$Ncnttot+=$row->ncnt;
}
pmysql_free_result($result);
?>
<link rel="styleSheet" href="/css/admin.css" type="text/css">
<div class="table_style02" style="margin-top:20">
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
<TR>
	<th>날짜</th>
	<th>매출현황</th>
	<th>해당건수 ( <b><font color="#35353f">배송■</font></b> &nbsp;<b><font color="#b8b8b8">반송■</font></b> &nbsp;&nbsp;<font color="#ff9000"><b>미처리■ </b></font>)</th>
</TR>
<?php
for($i=1;$i<=12;$i++) {
	if(($date_year.$i)==date("Yn")) {
		$tdclass="td_con_orange1";
		$tdclass2="td_con_orange";
	} else {
		$tdclass="td_con2";
		$tdclass2="td_con1a";
	}
	echo "<tr>\n";
	echo "	<TD><p align=\"center\">{$i}월</td>\n";
	echo "	<TD>\n";
	echo "	<div class=\"table_none\">\n";
	echo "	<table cellpadding=\"0\" cellspacing=\"0\" width=\"100%\">\n";
	echo "	<tr>\n";
	echo "		<td width=\"34\"><p><img src=\"images/icon_trans.gif\" border=\"0\"></p></td>\n";
	echo "		<td width=\"111\"><p align=\"right\">".number_format($Ysum[$i])."원</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"34\"><p><img src=\"images/icon_back.gif\" border=\"0\"></p></td>\n";
	echo "		<td width=\"111\"><p align=\"right\">".number_format($Rsum[$i])."원</td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"34\"><p><img src=\"images/icon_not.gif\" border=\"0\"></p></td>\n";
	echo "		<td width=\"111\"><p align=\"right\">".number_format($Nsum[$i])."원</td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</div>\n";
	echo "	</td>\n";
	echo "	<TD>\n";
	echo "	<div class=\"table_none\">\n";
	echo "	<table cellpadding=\"1\" cellspacing=\"0\" width=\"100%\">\n";
	echo "	<tr>\n";
	echo "		<td width=\"\">\n";
	echo "		<div align=\"right\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"".(@round(($Ysum[$i] / $maxsum)*$MAX_barsize)>0?@round(($Ysum[$i] / $maxsum)*$MAX_barsize):"1")."\">\n";
	echo "		<tr>\n";
	echo "			<td width=\"516\" height=\"15\" bgcolor=\"#35353f\"></td>\n";
	echo "		</tr>\n";
	echo "		</table></div>\n";
	echo "		</td>\n";
	echo "		<td width=\"45\"><p align=\"right\">(".number_format($Ycnt[$i])."건)</p></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"\">\n";
	echo "		<div align=\"right\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"".(@round(($Rsum[$i] / $maxsum)*$MAX_barsize)>0?@round(($Rsum[$i] / $maxsum)*$MAX_barsize):"1")."\">\n";
	echo "		<tr>\n";
	echo "			<td width=\"516\" height=\"15\" bgcolor=\"#b8b8b8\"></td>\n";
	echo "		</tr>\n";
	echo "		</table></div>\n";
	echo "		</td>\n";
	echo "		<td width=\"45\"><p align=\"right\">(".number_format($Rcnt[$i])."건)</p></td>\n";
	echo "	</tr>\n";
	echo "	<tr>\n";
	echo "		<td width=\"\">\n";
	echo "		<div align=\"right\"><table cellpadding=\"0\" cellspacing=\"0\" width=\"".(@round(($Nsum[$i] / $maxsum)*$MAX_barsize)>0?@round(($Nsum[$i] / $maxsum)*$MAX_barsize):"1")."\">\n";
	echo "		<tr>\n";
	echo "			<td width=\"516\" height=\"15\" bgcolor=\"#ff9000\"></td>\n";
	echo "		</tr>\n";
	echo "		</table></div>\n";
	echo "		</td>\n";
	echo "		<td width=\"45\"><p align=\"right\">(".number_format($Ncnt[$i])."건)</p></td>\n";
	echo "	</tr>\n";
	echo "	</table>\n";
	echo "	</div>\n";
	echo "	</td>\n";
	echo "	<td></td>\n";
	echo "</tr>\n";

	if($i != 12) {
	}
}
?>
<tr>
	<TD><p align="center">합계</td>
	<TD>
	<div class="table_none">
	<table cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td width="34"><p><img src="images/icon_trans.gif" border="0"></p></td>
		<td width="111"><p align="right"><?=number_format($Ysumtot)?>원</td>
	</tr>
	<tr>
		<td width="34"><p><img src="images/icon_back.gif" border="0"></p></td>
		<td width="111"><p align="right"><?=number_format($Rsumtot)?>원</td>
	</tr>
	<tr>
		<td width="34"><p><img src="images/icon_not.gif" border="0"></p></td>
		<td width="111"><p align="right"><?=number_format($Nsumtot)?>원</td>
	</tr>
	</table>
	</div>
	</td>
	<TD>
	<div class="table_none">
	<table cellpadding="1" cellspacing="0" width="100%">
	<tr>
		<td width="">
		<div align="right"><table cellpadding="0" cellspacing="0" width="<?=(@round(($Ysumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)>0?@round(($Ysumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize):"1")?>">
		<tr>
			<td width="516" height="15" bgcolor="#35353f"></td>
		</tr>
		</table></div>
		</td>
		<td width="45"><p align="right">(<?=number_format($Ycnttot)?>건)</p></td>
	</tr>
	<tr>
		<td width="">
		<div align="right"><table cellpadding="0" cellspacing="0" width="<?=(@round(($Rsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)>0?@round(($Rsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize):"1")?>">
		<tr>
			<td width="516" height="15" bgcolor="#b8b8b8"></td>
		</tr>
		</table></div>
		</td>
		<td width="45"><p align="right">(<?=number_format($Rcnttot)?>건)</p></td>
	</tr>
	<tr>
		<td width="">
		<div align="right"><table cellpadding="0" cellspacing="0" width="<?=(@round(($Nsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)>0?@round(($Nsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize):"1")?>">
		<tr>
			<td width="516" height="15" bgcolor="#ff9000"></td>
		</tr>
		</table></div>
		</td>
		<td width="45"><p align="right">(<?=number_format($Ncnttot)?>건)</p></td>
	</tr>
	</table>
	</td>
	<td></td>
</tr>
</table>
</div>
</div>