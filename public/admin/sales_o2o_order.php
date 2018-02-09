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
$o2o_type     = $_GET["o2o_type"];
$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$selected[o2o_type]      = $o2o_type;

$search_start = $search_start?$search_start:date("Ym")."01";
$search_end = $search_end?$search_end:date("Ymd");
$search_s = $search_start?str_replace("-","",$search_start):"";
$search_e = $search_end?str_replace("-","",$search_end):"";

$tempstart = explode("-",$search_start);
$tempend = explode("-",$search_end);
$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}

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


		//exdebug($subquery);
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">

function searchForm() {
	document.form1.action="sales_o2o_order.php";
    document.form1.method="GET";
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
	document.form1.action="sales_o2o_order_excel.php";
    document.form1.method="POST";
	document.form1.submit();
	document.form1.action="";
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 주문/매출  &gt; 정산관리 &gt; <span>O2O상태별 조회</span></p></div></div>

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
					<div class="title_depth3">O2O상태별 조회</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>O2O상태별 내역을 확인하실 수 있습니다.</span></div>
				</td>
			</tr>
			
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">O2O상태별 조회</span></div>
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
                                </select> 
                            </td>
                        </TR>
<?
}
?>

                        <tr>
							<th><span>O2O구분</span></th>
							<TD class="td_con1">
								<input type="radio" name="o2o_type" value="" <?if($selected[o2o_type]==''){?>checked<?}?>>전체
								<?
								foreach ($arrChainCode as $k=>$v){
									if ($k != '0') {
								?>
								<input type="radio" name="o2o_type" value="<?=$k?>" <?if($selected[o2o_type]."|"==$k."|"){?>checked<?}?>><?=$v?>
								<?
									}
								}
								?>
                            </TD>
						</tr>

						</TABLE>
						</div>
						</td>
					</tr>					
				</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="right"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a>&nbsp;<a href="javascript:OrderExcel();"><img src="images/btn_excel_search.gif" border="0" hspace="1"></a></td>
			</tr>
			</form>
			<tr>
				<td height="20"></td>
			</tr>
			<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<tr>
				<td style="padding-bottom:3pt;">
<?php
		$sql = $subquery;
		$result=pmysql_query($sql,get_db_conn());
		$t_count=pmysql_num_rows($result);
        //echo "sql = ".$sql."<br>";
        //exdebug($sql);
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
				<table border=0 cellpadding=0 cellspacing=1 width=100% style="border:1px solid #d1d1d1">
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
<?php
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
                    <td style="text-align:right;"><?=number_format($row->total_qty1)?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($row->total_price1)?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($row->total_qty2)?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($row->total_price2)?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($row->total_qty3)?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($row->total_price3)?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($row->total_qty4)?>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><?=number_format($row->total_price4)?>&nbsp;&nbsp;</td>
                </tr>
<?
		}
		pmysql_free_result($result);
?>
			    <tr bgcolor="#d1d1d1" onmouseover="this.style.background='#FEFBD1'" onmouseout="this.style.background='#d1d1d1'">
                    <td><b>합계</b></td>
                    <td></td>
                    <td></td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty1)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price1)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty2)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price2)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty3)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price3)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_qty4)?></b>&nbsp;&nbsp;</td>
                    <td style="text-align:right;"><b><?=number_format($total_sum_price4)?></b>&nbsp;&nbsp;</td>
                </tr>

<?
		if($t_count==0) {
			echo "<tr height=28 bgcolor=#FFFFFF><td colspan={$colspan} align=center>조회된 내용이 없습니다.</td></tr>\n";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<input type=hidden name=tot value="<?=$cnt?>">
			</form>

			<form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=type>
			<input type=hidden name=ordercodes>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<input type=hidden name=search_start value="<?=$search_start?>">
			<input type=hidden name=search_end value="<?=$search_end?>">
			<input type=hidden name=sel_vender value="<?=$sel_vender?>">
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