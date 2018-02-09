<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");

//exdebug($_POST);
//exdebug($_GET);

header("Content-type: application/vnd.ms-excel");
Header("Content-Disposition: attachment; filename=sales_o2o_order_excel_".date("Ymd",$CurrentTime).".xls"); 
Header("Pragma: no-cache"); 
Header("Expires: 0");
Header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
Header("Content-Description: PHP4 Generated Data");


$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));


//$s_check    = $_POST["s_check"];
$search     = trim($_POST["search"]);
$search_start   = $_POST["search_start"];
$search_end     = $_POST["search_end"];
$o2o_type		= $_POST["o2o_type"];
$sel_vender     = $_POST["sel_vender"];  // 벤더 선택값으로 검색

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start):"";
$search_e = $search_end?str_replace("-","",$search_end):"";

// 검색어
// 브랜드 조건
if($sel_vender) {
   $qry.= " and vender = ".$sel_vender."";
}

// o2o구분
if(ord($o2o_type)) {
	$qry.= "AND o2o_gubun = '{$o2o_type}' ";
}
//echo "qry = ".$qry."<br>";

$t_price=0;

$subquery = "select 
			s_date, 
			vender, 
			brandname, 
			o2o_gubun, 
			sum(total_qty1) as total_qty1, 
			sum(total_price1) as total_price1,
			sum(total_qty2) as total_qty2, 
			sum(total_price2) as total_price2,
			sum(total_qty3) as total_qty3, 
			sum(total_price3) as total_price3,
			sum(total_qty4) as total_qty4, 
			sum(total_price4) as total_price4
			from (select 
				substr(rdt,1,4)||'-'||substr(rdt,5,2)||'-'||substr(rdt,7,2) as s_date, 
				vender, 
				brandname, 
				o2o_gubun,
				case 
				when proc_status_code1!='0' then sum(proc_status_code1)
				else 0
				end as total_qty1, 
				case 
				when proc_status_code1!='0' then sum(sum_price)
				else 0
				end as total_price1,   
				case 
				when proc_status_code2!='0' then sum(proc_status_code2)
				else 0
				end as total_qty2, 
				case 
				when proc_status_code2!='0' then sum(sum_price)
				else 0
				end as total_price2,
				case 
				when proc_status_code3!='0' then sum(proc_status_code3)
				else 0
				end as total_qty3, 
				case 
				when proc_status_code3!='0' then sum(sum_price)
				else 0
				end as total_price3,  
				case 
				when proc_status_code4!='0' then sum(proc_status_code4)
				else 0
				end as total_qty4,
				case 
				when proc_status_code4!='0' then sum(sum_price)
				else 0
				end as total_price4 
				from (
					select 					
					p.date as rdt,
					b.vender,
					b.brandname,
					p.delivery_type as o2o_gubun,
					1 as proc_status_code1,
					case when p.op_step!='44' and store_code!='A1801B' then 1 else 0 end as proc_status_code2,
					case when p.op_step!='44' and p.deli_date is not null then 1 else 0 end as proc_status_code3,
					case when p.op_step='44' then 1 else 0 end as proc_status_code4,
					((p.price+p.option_price)*p.option_quantity) - (p.use_point + p.use_epoint + p.coupon_price)
					as sum_price
					from tblorderproduct p
					join tblproduct t on p.productcode = t.productcode
					join tblproductbrand b on t.brand = b.bridx
					left join (select ordercode, idx, substr(min(regdt),1,8) cha_date from tblorderproduct_store_code where 1=1 group by ordercode, idx) s on p.ordercode=s.ordercode and p.idx=s.idx
					left join (select oc_no, substr(cfindt,1,8) can_date from tblorder_cancel where 1=1 and cfindt !='') c on p.oc_no=c.oc_no
					where 1=1
					and p.delivery_type in ('1','2','3')
					AND p.ordercode not in ('2017052200280135018A','2017052202290002551A','2017052202361917530A','2017052209332677930A','2017052210004564220A','2017052211335760017A')
				) as a
				where 1=1 
				AND	    rdt >= '{$search_s}' and rdt <= '{$search_e}'
				".$qry."
				group by rdt, vender, brandname, o2o_gubun, proc_status_code1, proc_status_code2, proc_status_code3, proc_status_code4 
			) as b
			where 1=1 
			group by s_date, vender, brandname, o2o_gubun 
			order by s_date, brandname, o2o_gubun
        ";
?>

<?php
		$sql = $subquery;
		$result=pmysql_query($sql,get_db_conn());
		$t_count=pmysql_num_rows($result);
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body>

				<table border=1 cellpadding=0 cellspacing=0 width=100%>
				<col width=140></col>
				<col width=120></col>
				<col width=100></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
				<col width=80></col>
			
				<TR bgcolor="#d1d1d1">
                    <td rowspan=2><b>일자<b></td>
                    <td rowspan=2><b>브랜드<b></td>
                    <td rowspan=2><b>O2O구분<b></td>
                    <td colspan=2><b>발생<b></td>
                    <td colspan=2><b>시도<b></td>
                    <td colspan=2><b>성공<b></td>
                    <td colspan=2><b>취소<b></td>
				</TR>
                <TR bgcolor="#d1d1d1">
                    <td><b>건수<b></td>
                    <td><b>매출<b></td>
                    <td><b>건수<b></td>
                    <td><b>매출<b></td>
                    <td><b>건수<b></td>
                    <td><b>매출<b></td>
                    <td><b>건수<b></td>
                    <td><b>매출<b></td>
                </TR>
<?
		$total_sum_qty1		= 0;
		$total_sum_price1	= 0;
		$total_sum_qty2		= 0;
		$total_sum_price2	= 0;
		$total_sum_qty3		= 0;
		$total_sum_price3	= 0;
		$total_sum_qty4		= 0;
		$total_sum_price4	= 0;

		$o2o_gubun_text	= array('1'=>'매장픽업','2'=>'매장택배','3'=>'당일수령');
		$colspan=11;
        $i = 0;
		while($row=pmysql_fetch_object($result)) {

					if($i%2) $thiscolor="#ffeeff";
					else $thiscolor="#FFFFFF";

					$i++;
					$o2o_gubun	= $o2o_gubun_text[$row->o2o_gubun];

					$total_sum_qty1		+= $row->total_qty1;
					$total_sum_price1	+= $row->total_price1;
					$total_sum_qty2		+= $row->total_qty2;
					$total_sum_price2	+= $row->total_price2;
					$total_sum_qty3		+= $row->total_qty3;
					$total_sum_price3	+= $row->total_price3;
					$total_sum_qty4		+= $row->total_qty4;
					$total_sum_price4	+= $row->total_price4;

?>
			    <tr bgcolor=<?=$thiscolor?> onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='<?=$thiscolor?>'">
                    <td><?=$row->s_date?></td>
                    <td><?=$row->brandname?></td>
                    <td><?=$o2o_gubun?></td>
                    <td style="text-align:right;"><?=number_format($row->total_qty1)?></td>
                    <td style="text-align:right;"><?=number_format($row->total_price1)?></td>
                    <td style="text-align:right;"><?=number_format($row->total_qty2)?></td>
                    <td style="text-align:right;"><?=number_format($row->total_price2)?></td>
                    <td style="text-align:right;"><?=number_format($row->total_qty3)?></td>
                    <td style="text-align:right;"><?=number_format($row->total_price3)?></td>
                    <td style="text-align:right;"><?=number_format($row->total_qty4)?></td>
                    <td style="text-align:right;"><?=number_format($row->total_price4)?></td>
                </tr>
<?
		}
		pmysql_free_result($result);
?>
			    <tr bgcolor="#d1d1d1" onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='#d1d1d1'">
                    <td><b>합계</b></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty1)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price1)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty2)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price2)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty3)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price3)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty4)?></b></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price4)?></b></td>
                </tr>
				</TABLE>
</body>
</html>