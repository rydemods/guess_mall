<?php

$qry = "WHERE a.ordercode = b.ordercode ";
$qry.= "AND a.ordercode LIKE '".$date_year.$date_month."%' AND b.ordercode LIKE '".$date_year.$date_month."%' ";
$qry.= "AND b.vender='".$_VenderInfo->getVidx()."' ";
if($loc!="ALL") {
	if($loc=="기타") $qry.= "AND a.loc is NULL ";
	else $qry.= "AND a.loc = '".$loc."' ";
}
if(strlen($prcode)>0) {
	$qry.= "AND b.productcode = '".$prcode."' ";
} else if(strlen($code)>=3) {
	$qry.= "AND b.productcode LIKE '".$likecode."%' ";
}

$sql = "SELECT /*SQL_CACHE*/ ";
$sql.= "SUM(CASE WHEN b.deli_gbn='Y' THEN b.quantity ELSE NULL END) as Ycnt, ";
$sql.= "SUM(CASE WHEN b.deli_gbn='N' OR b.deli_gbn='S' THEN b.quantity ELSE NULL END) as Ncnt, ";
$sql.= "SUM(CASE WHEN b.deli_gbn='R' THEN b.quantity ELSE NULL END) as Rcnt, ";
$sql.= "SUM(CASE WHEN b.deli_gbn='Y' THEN b.price*b.quantity ELSE NULL END) as Ysum, ";
$sql.= "SUM(CASE WHEN b.deli_gbn='N' OR b.deli_gbn='S' THEN b.price*b.quantity ELSE NULL END) as Nsum, ";
$sql.= "SUM(CASE WHEN b.deli_gbn='R' THEN b.price*b.quantity ELSE NULL END) as Rsum, ";
$sql.= "SUBSTR(a.ordercode,1,8) as day FROM tblorderinfo a, tblorderproduct b ";

if($age2>0 || $sex!="ALL") {
	$sql.= ", tblmember c ".$qry." AND a.id=c.id ";
	if($age1>0) {
		$start_year = (int)date("Y") - (int)$age2 +1;
		$end_year = (int)date("Y") - (int)$age1 +1;
		$s_year = substr((string)$start_year,2,2);
		$e_year = substr((string)$end_year,2,2);

		if ($start_year < 2000 && $end_year < 2000) {
			 $sql.= "AND (LEFT(c.resno,2) BETWEEN '".$s_year."' AND '".$e_year."') AND SUBSTR(c.resno,7,1) < '3' ";
		} else if ($start_year < 2000 && $end_year > 1999) {
			 $sql.= "AND (((LEFT(c.resno,2) BETWEEN '".$s_year."' AND '99') AND SUBSTR(c.resno,7,1) < '3') ";
			 $sql.= "OR ((LEFT(c.resno,2) BETWEEN '00' AND '".$e_year."') ";
			 $sql.= "AND SUBSTR(c.resno,7,1) > '2')) ";
		} else if ($start_year > 1999 && $end_year > 1999) {
			$sql.= "AND (LEFT(c.resno,2) BETWEEN '".$s_year."' AND '".$e_year."') ";
			$sql.= "AND SUBSTR(c.resno,7,1) > '2') ";
		}
	}
	if($sex=="M") {
		$sql.= "AND SUBSTR(c.resno,7,1)='1' ";
	} else if ($sex=="F") {
		$sql.= "AND SUBSTR(c.resno,7,1)='2' ";
	}
} else {
	$sql.= $qry." ";
	if($member=="Y") {
		$sql.= "AND SUBSTR(a.ordercode,21,1)!='X' ";
	} else if($member=="N") {
		$sql.= "AND SUBSTR(a.ordercode,21,1)='X' ";
	}
}
$sql.= "GROUP BY day ";

$MAX_barsize=420;
$Ysumtot=0;
$Rsumtot=0;
$Nsumtot=0;
$maxsum=0;

$Ycnttot=0;
$Rcnttot=0;
$Ncnttot=0;

$result = pmysql_query($sql,get_db_conn());
while($row=pmysql_fetch_object($result)) {
	$day=(int)substr($row->day,6,2);
	$Ysum[$day]=$row->Ysum;
	$Rsum[$day]=$row->Rsum;
	$Nsum[$day]=$row->Nsum;
	if($row->Ysum>$maxsum) $maxsum=$row->Ysum;
	if($row->Rsum>$maxsum) $maxsum=$row->Rsum;
	if($row->Nsum>$maxsum) $maxsum=$row->Nsum;

	$Ycnt[$day]=$row->Ycnt;
	$Rcnt[$day]=$row->Rcnt;
	$Ncnt[$day]=$row->Ncnt;

	$Ysumtot+=$row->Ysum;
	$Rsumtot+=$row->Rsum;
	$Nsumtot+=$row->Nsum;

	$Ycnttot+=$row->Ycnt;
	$Rcnttot+=$row->Rcnt;
	$Ncnttot+=$row->Ncnt;
}
pmysql_free_result($result);
?>
<table border=0 cellpadding=5 cellspacing=1 width=100% bgcolor="#D4D4D4">
<tr>
	<td width=60 nowrap></td>
	<td width=130 nowrap></td>
	<td width=100%></td>
</tr>
<tr height=35 bgcolor=#F0F0F0>
	<td colspan=3 align=right style="padding-right:5">
	<img src="images/bar_delivery.jpg" width=25 height=15 align=absmiddle> 배송
	<img width=10 height=0>
	<img src="images/bar_return.jpg" width=25 height=15 align=absmiddle> 반송
	<img width=10 height=0>
	<img src="images/bar_delay.jpg" width=25 height=15 align=absmiddle> 미처리
	</td>
</tr>
<?
$totaldays = get_totaldays($date_year,$date_month);
for($i=1;$i<=$totaldays;$i++) {
	$bgcolor="#FFFFFF";
	$nowWeek = date("w", strtotime("$date_year-$date_month-$i"));
	if($nowWeek==0) $bgcolor="#FFD0CE";
	else if($nowWeek==6) $bgcolor="#E1E0FE";
	
	$trbgcolor="#FFFFFF";
	if(($date_year.$date_month.$i)==date("Ymj")) $trbgcolor="#FEF7F1";
	echo "<tr bgcolor=".$trbgcolor.">\n";
	echo "	<td align=center bgcolor=".$bgcolor." nowrap>".$i."일</td>\n";
	echo "	<td align=right style=\"line-height:12pt\">";
	echo "	".number_format($Ysum[$i])."원";
	echo"	<br>";
	echo "	".number_format($Rsum[$i])."원";
	echo "	<br>";
	echo "	".number_format($Nsum[$i])."원";
	echo "	</td>\n";
	echo "	<td style=\"font-size:8pt;color:#868686;line-height:1pt\">";
	echo "	<img src=\"images/bar_delivery.jpg\" width=".@round(($Ysum[$i] / $maxsum)*$MAX_barsize)." height=11 align=absmiddle> (".number_format($Ycnt[$i])."개)";
	echo "	<br><br><br>";
	echo "	<img src=\"images/bar_return.jpg\" width=".@round(($Rsum[$i] / $maxsum)*$MAX_barsize)." height=11 align=absmiddle> (".number_format($Rcnt[$i])."개)";
	echo "	<br><br><br>";
	echo "	<img src=\"images/bar_delay.jpg\" width=".@round(($Nsum[$i] / $maxsum)*$MAX_barsize)." height=11 align=absmiddle> (".number_format($Ncnt[$i])."개)";
	echo "	</td>\n";
	echo "</tr>\n";
}
?>
<tr bgcolor=#FFFFFF>
	<td align=center nowrap><B>합계</B></td>
	<td align=right style="font-weight:bold;line-height:12pt">
	<?=number_format($Ysumtot)?>원
	<br>
	<?=number_format($Rsumtot)?>원
	<br>
	<?=number_format($Nsumtot)?>원
	</td>
	<td style="font-size:8pt;color:#868686;font-weight:bold;line-height:1pt">
	<img src="images/bar_delivery.jpg" width="<?=@round(($Ysumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)?>" height=11 align=absmiddle> (<?=number_format($Ycnttot)?>개)
	<br><br><br>
	<img src="images/bar_return.jpg" width="<?=@round(($Rsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)?>" height=11 align=absmiddle> (<?=number_format($Rcnttot)?>개)
	<br><br><br>
	<img src="images/bar_delay.jpg" width="<?=@round(($Nsumtot / ($Ysumtot+$Rsumtot+$Nsumtot))*$MAX_barsize)?>" height=11 align=absmiddle> (<?=number_format($Ncnttot)?>개)
	</td>
</tr>
</table>