<?
#print_r($_GET);
#print_r($_POST);
$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
$period[3] = date("Y-m-d",strtotime('-1 month'));

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
$dvcode         = $_GET["dvcode"];
$oistep1        = $_GET["oistep1"];
$oi_type        = $_GET["oi_type"];
$paystate       = $_GET["paystate"]?$_GET["paystate"]:"A";
$paymethod      = $_GET["paymethod"];

if(is_array($oistep1)) $oistep1 = implode(",",$oistep1);
if(is_array($oi_type)) $oi_type = implode(",",$oi_type);
if(is_array($paymethod)) $paymethod = implode("','",$paymethod);

$oistep1_arr  = explode(",",$oistep1);
$oi_type_arr  = explode(",",$oi_type);
$paymethod_arr  = explode("','",$paymethod);

$sel_vender     = $_GET["sel_vender"];  // 벤더 선택값으로 검색
$brandname      = $_GET["brandname"];  // 벤더이름 검색


$selected[s_check][$s_check]    = 'selected';
$selected[s_date][$s_date]      = 'selected';
$selected[s_prod][$s_prod]      = 'selected';
$selected[dvcode][$dvcode]      = 'selected';
$selected[paystate][$paystate]  = 'checked';

$search_start = $search_start?$search_start:"";
$search_end = $search_end?$search_end:"";
$search_s = $search_start?str_replace("-","",$search_start."000000"):"";
$search_e = $search_end?str_replace("-","",$search_end."235959"):"";

// 기본 검색 조건
$qry_from = "tblorderinfo a ";
$qry_from.= " join tblorderproduct b on a.ordercode = b.ordercode ";
$qry.= "WHERE 1=1 ";
$qry.= "AND a.id = '".$mem_id."' ";

// 기간선택 조건
if ($search_s != "" || $search_e != "") { 
	if(substr($search_s,0,8)==substr($search_e,0,8)) {
		$qry.= "AND a.{$s_date} LIKE '".substr($search_s,0,8)."%' ";
	} else {
		$qry.= "AND a.{$s_date}>='{$search_s}' AND a.{$s_date} <='{$search_e}' ";
	}
}

// 기본옵션만 검색
//$qry.= "AND b.option_type = 0 ";

// 검색어
if(ord($search)) {
	if($s_check=="oc") $qry.= "AND a.ordercode = '{$search}' ";
    else if($s_check=="dv") $qry.= "AND a.deli_num = '{$search}' ";
    else if($s_check=="on") $qry.= "AND a.sender_name = '{$search}' ";
    else if($s_check=="oi") $qry.= "AND a.id = '{$search}' ";
    else if($s_check=="oh") $qry.= "AND replace(a.sender_tel, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="op") $qry.= "AND a.ip = '{$search}' ";
    else if($s_check=="sn") $qry.= "AND a.bank_sender = '{$search}' ";
    else if($s_check=="rn") $qry.= "AND a.receiver_name = '{$search}' ";
    else if($s_check=="rh") $qry.= "AND replace(a.receiver_tel2, '-', '') = '".str_replace("-", "", $search)."' ";
    else if($s_check=="ra") $qry.= "AND a.receiver_addr like '%{$search}%' ";
    else if($s_check=="nm") $qry.= "AND (a.sender_name = '{$search}' OR a.bank_sender = '{$search}' OR a.receiver_name = '{$search}') ";
}

// 상품 조건
if(ord($search_prod)) {
	if($s_prod=="pn") $qry.= "AND upper(b.productname) like upper('%{$search_prod}%') ";
    else if($s_prod=="pc") $qry.= "AND upper(b.productcode) like upper('%{$search_prod}%') ";
    else if($s_prod=="sc") $qry.= "AND upper(b.selfcode) like upper('%{$search_prod}%') ";
}

// 배송업체 조건
if(ord($dvcode))	$qry.= "AND a.deli_com = '{$dvcode}' ";

// 결제상태 조건
if(ord($paystate)) {
    if($paystate == "N") $qry.="AND a.oi_step1 < 1";
    else if($paystate == "Y") $qry.="AND a.oi_step1 > 0";
}

// 주문상태별 조건
if( $oistep1_arr[0] == '' ) $oistep1_arr = array();
if( count($oistep1_arr) || count($oi_type_arr) ) {
    $subWhere = array();

    if(count($oistep1_arr)) {
        $subWhere[] = " (a.oi_step1 in (".$oistep1.") And a.oi_step2 = 0) ";
    }

    if(count($oi_type_arr)) {
        foreach($oi_type_arr as $k => $v) {
            switch($v) {
                case 44 : $subWhere[] = " (a.oi_step1 = 0 And a.oi_step2 = 44) "; break;    //입금전취소완료
                //case 61 : $subWhere[] = " (a.oi_step1 in (3,4) And (coalesce(b.opt1_change, '') != '' OR coalesce(b.opt2_change, '') != '') And b.op_step = 41) "; break;   //교환접수
                //case 62 : $subWhere[] = " (a.oi_step1 in (3,4) And (coalesce(b.opt1_change, '') != '' OR coalesce(b.opt2_change, '') != '') And b.op_step = 44) "; break;   //교환완료
                // 2016-02-12 jhjeong redelivery_type = 'G' 추가..옵션없는 상품의 교환일 경우 구분할수 있는 값이 없어서 추가함.
                case 61 : $subWhere[] = " (b.redelivery_type = 'G' And b.op_step = 41) "; break;   //교환접수
                case 62 : $subWhere[] = " (b.redelivery_type = 'G' And b.op_step = 44) "; break;   //교환완료
                case 63 : $subWhere[] = " (a.oi_step1 in (3,4) And (coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '') And b.op_step = 41) "; break;    //반품접수
                case 64 : $subWhere[] = " (a.oi_step1 in (3,4) And a.oi_step2 = 42) "; break;   //반품완료(배송중 이상이면서 환불접수단계)
                case 65 : $subWhere[] = " (a.bank_date is not null And ((a.oi_step1 in (1,2) and b.op_step = 41) OR b.op_step = 42) And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = '')))"; break;  //환불접수
                case 66 : $subWhere[] = " (a.oi_step1 > 0 And b.op_step = 44 And ((coalesce(b.opt1_change, '') = '' And coalesce(b.opt2_change, '') = ''))) "; break;  //환불완료
            }
        }
    }

    //exdebug($subWhere);
    if(count($subWhere)) {
        $sub = " (".implode(" OR ", $subWhere)." ) ";
    }
}
//exdebug($sub);
if($sub) $qry.= " AND ".$sub;


// 결제타입 조건
if(ord($paymethod))	$qry.= "AND a.paymethod in ('".$paymethod."')";

// 브랜드 조건
if($sel_vender || $brandname) {
    if($brandname) $subqry = " and v.brandname like '%".strtoupper($brandname)."%'";
    else if($sel_vender) $subqry = " and v.vender = ".$sel_vender."";

    $qry_from.= " join tblproductbrand v on b.vender = v.vender ".$subqry."";
} else {
    $qry_from.= " join tblproductbrand v on b.vender = v.vender ";
}


$sql = "SELECT vendercnt FROM tblshopcount ";
$result=pmysql_query($sql,get_db_conn());
$row=pmysql_fetch_object($result);
$vendercnt=$row->vendercnt;
pmysql_free_result($result);

if($vendercnt>0){
	$venderlist=array();
	//$sql = "SELECT vender,id,com_name,delflag FROM tblvenderinfo ORDER BY com_name ASC ";
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

$sql = "SELECT COUNT(*) as t_count FROM (SELECT COUNT(a.ordercode) FROM {$qry_from} {$qry} GROUP BY a.ordercode) a ";
//$paging = new Paging($sql,10,20);
#print_r($sql);
$paging = new newPaging($sql,10,5,'GoPageOrder');
$t_count = $paging->t_count;
$gotopage = $paging->gotopage;

########### 주문정보 구하기
$arpm=array("B"=>"무통장","V"=>"계좌이체","O"=>"가상계좌","Q"=>"가상계좌(매매보호)","C"=>"신용카드","P"=>"신용카드(매매보호)","M"=>"핸드폰");

$sql = "SELECT  a.ordercode, min(a.id) as id, min(a.price) as price, min(a.deli_price) as deli_price, min(a.dc_price) as dc_price, min(a.reserve) as reserve, min(a.paymethod) as paymethod, 
                min(a.sender_name) as sender_name, min(a.receiver_name) as receiver_name, min(a.oi_step1) as oi_step1, min(a.oi_step2) as oi_step2, min(a.redelivery_type) as redelivery_type, 
                min(productname) as productname, (select count(*) from tblorderproduct op where op.ordercode = a.ordercode) prod_cnt 
        FROM {$qry_from} {$qry} 
        GROUP BY a.ordercode 
        ORDER BY a.ordercode {$orderby} 
        ";
$sql = $paging->getSql($sql);
$ret_order = pmysql_query($sql, get_db_conn());
$cnt_ord = pmysql_num_rows($ret_order);
//print_r($sql);
?>
<script type="text/javascript">
<!--
function searchForm() {
	//document.form1.action="order_list_all_order.php";
	document.ord_frm.submit();
}

function OnChangePeriod(val) {
    //alert(val);
	var pForm = document.ord_frm;
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

function GoPageOrder(block,gotopage) {
	document.idxform.block.value = block;
	document.idxform.gotopage.value = gotopage;
	document.idxform.submit();
}
//-->
</script>

			<div class="contentsBody">

                <form name="ord_frm" action="<?=$_SERVER['PHP_SELF']?>" method=GET>
                <input type="hidden" name="id" value="<?=$mem_id?>">
                <input type="hidden" name="menu" value="order">
				<h3 class="table-title">전체주문 조회</h3>
				<table class="th-left">
					<caption>전체주문 조회</caption>
					<colgroup>
						<col style="width:18%"><col style="width:82%">
					</colgroup>
					<tbody>

						<tr>
							<th scope="row"><label for="keyword01">검색어</label></th>
							<td>
                                <div>
                                    <select name="s_check">
                                        <option value="oc" <?=$selected[s_check]["oc"]?>>주문코드</option>
                                        <option value="">----------------------</option>
                                        <!-- <option value="on" <?=$selected[s_check]["on"]?>>주문자명</option> -->
                                        <!-- <option value="oi" <?=$selected[s_check]["oi"]?>>주문자ID</option> -->
                                        <!-- <option value="oh" <?=$selected[s_check]["oh"]?>>주문자HP</option>
                                        <option value="op" <?=$selected[s_check]["op"]?>>주문자IP</option> -->
                                        <!-- <option value="">----------------------</option> -->
                                        <option value="sn" <?=$selected[s_check]["sn"]?>>입금자명</option>
                                        <option value="rn" <?=$selected[s_check]["rn"]?>>수령자명</option>
                                        <option value="rh" <?=$selected[s_check]["rh"]?>>수령자HP</option>
                                        <option value="ra" <?=$selected[s_check]["ra"]?>>배송지주소</option>
                                        <option value="">----------------------</option>
                                        <option value="nm" <?=$selected[s_check]["nm"]?>>주문자명,입금자명,수령자명</option>
                                    </select>
                                    <input type=text name=search value="<?=$search?>" class="w400">
                                </div>
                            </TD>
						</tr>

						<TR>
							<th scope="row">기간</th>
							<td>
                                <div class="date-choice">
                                    <select name="s_date" class="select">
                                        <option value="ordercode" <?=$selected[s_date]["ordercode"]?>>주문일</option>
                                        <option value="deli_date" <?=$selected[s_date]["deli_date"]?>>배송일</option>
                                        <option value="bank_date" <?=$selected[s_date]["bank_date"]?>>입금일</option>
                                    </select>
                                    <input class="w100" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="w100" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
                                    <button OnClick="javascript:OnChangePeriod(0);" class="btn-line" type="button"><span>오늘</span></button>
                                    <button OnClick="OnChangePeriod(1)" class="btn-line" type="button"><span>7일</span></button>
                                    <button OnClick="OnChangePeriod(2)" class="btn-line" type="button"><span>14일</span></button>
                                    <button OnClick="OnChangePeriod(3)" class="btn-line" type="button"><span>한달</span></button>
                                    <button OnClick="OnChangePeriod(4)" class="btn-line" type="button"><span>전체</span></button>
                                </div>
							</td>
						</TR>

						<tr>
							<th scope="row"><label for="keyword01">상품</label></th>
							<td>
                                <div>
                                    <select name="s_prod" class="select">
                                        <option value="pn" <?=$selected[s_prod]["pn"]?>>상품명</option>
                                        <option value="pc" <?=$selected[s_prod]["pc"]?>>상품코드</option>
                                        <option value="sc" <?=$selected[s_prod]["sc"]?>>진열코드</option>
                                    </select>
                                    <input type=text name=search_prod value="<?=$search_prod?>" class="w400">
                                </div>
                            </TD>
						</tr>


                        <TR>
							<th scope="row">주문상태1</th>
							<TD>
<? 
                            foreach ($oi_step1 as $k=>$v){ 
?>
	                            <input type="checkbox" name="oistep1[]" value="<?=$k?>" <?=( in_array($k, $oistep1_arr )?'checked':'')?>><label for="order-type01">&nbsp;<?=$v?></label></input>
<?
                            } 
?>
							</TD>
						</TR>
                        <TR>
							<th scope="row">주문상태2</th>
							<TD>
                                <input type="checkbox" name="oi_type[]" value="44" <?=(in_array(44,$oi_type_arr)?'checked':'')?>><label for="order-type01">&nbsp;입금전취소완료</label></input>
                                <input type="checkbox" name="oi_type[]" value="61" <?=(in_array(61,$oi_type_arr)?'checked':'')?>><label for="order-type01">&nbsp;교환접수</label></input>
                                <input type="checkbox" name="oi_type[]" value="62" <?=(in_array(62,$oi_type_arr)?'checked':'')?>><label for="order-type01">&nbsp;교환완료</label></input>
                                <input type="checkbox" name="oi_type[]" value="63" <?=(in_array(63,$oi_type_arr)?'checked':'')?>><label for="order-type01">&nbsp;반품접수</label></input>
                                <input type="checkbox" name="oi_type[]" value="64" <?=(in_array(64,$oi_type_arr)?'checked':'')?>><label for="order-type01">&nbsp;반품완료</label></input>
                                <input type="checkbox" name="oi_type[]" value="65" <?=(in_array(65,$oi_type_arr)?'checked':'')?>><label for="order-type01">&nbsp;환불접수</label></input>
                                <input type="checkbox" name="oi_type[]" value="66" <?=(in_array(66,$oi_type_arr)?'checked':'')?>><label for="order-type01">&nbsp;환불완료</label></input>
							</TD>
						</TR>

                        <TR>
							<th scope="row">결제상태</th>
							<TD class="td_con1">
                                <input type="radio" name="paystate" value="A" <?=$selected[paystate]["A"]?>><label for="order-type01">&nbsp;전체</label></input>
                                <input type="radio" name="paystate" value="N" <?=$selected[paystate]["N"]?>><label for="order-type01">&nbsp;입금전</label></input>
                                <input type="radio" name="paystate" value="Y" <?=$selected[paystate]["Y"]?>><label for="order-type01">&nbsp;입금완료(결제완료)</label></input>
                            </TD>
						</TR>

                        <TR>
							<th scope="row">결제타입</th>
							<TD class="td_con1">
<?php
							$arrPaymethod=array("B:무통장입금","CA:신용카드","VA:계좌이체","OA:가상계좌","MA:휴대폰");
							for($i=0;$i<count($arrPaymethod);$i++) {
								$tmpPaymethod=explode(":",$arrPaymethod[$i]);
								$selPaymethod='';
								if(in_array($tmpPaymethod[0],$paymethod_arr)>0)$selPaymethod="checked";
?>
								<input type="checkbox" name="paymethod[]" value="<?=$tmpPaymethod[0]?>" <?=$selPaymethod?>><label for="order-type01">&nbsp;<?=$tmpPaymethod[1]?></label>
<?
							}
?>
							</TD>
						</TR>

<?
if($vendercnt > 0) {
?>
                        <TR>
                            <th scope="row">브랜드</th>
                            <td>
                                <select name=sel_vender class="select">
                                    <option value="">==== 전체 ====</option>
<?php
                            foreach($venderlist as $key => $val) {
                                echo "<option value=\"{$val->vender}\"";
                                if($sel_vender==$val->vender) echo " selected";
                                echo ">{$val->brandname}</option>\n";
                            }
?>
                                </select> 
                                <input type=text name=brandname value="<?=$brandname?>" style="width:197" class="input"></TD>
                            </td>
                        </TR>
<?
}
?>
					</tbody>
				</table>
				<!-- <div class="detail-find"><input type="image" src="static/img/btn/detail_search.gif" alt="상세검색"></div> -->
				<div class="btn-place">
					<a href="javascript:searchForm();" class="btn-dib on">검색</a>
					<!-- <a href="#" class="btn-dib">초기화</a> -->
				</div>
                </form>
				
				<table class="th-top">
					<caption>주문정보 리스트</caption>
					<colgroup>
						<col style="width:60px"><col style="width:120px"><col style="width:120px"><col style="width:auto"><col style="width:75px"><col style="width:75px"><col style="width:75px">
					</colgroup>
					<thead>
						<tr>
							<th scope="row">번호</th>
							<th scope="row">주문일</th>
							<th scope="row">주문번호</th>
							<th scope="row">상품명</th>
							<th scope="row">실결제금액</th>
							<th scope="row">결제수단</th>
							<th scope="row">처리단계</th>
						</tr>
					</thead>
					<tbody>
<?
                if($cnt_ord > 0) {
                    $cnt=0;
                    while($row_ord = pmysql_fetch_object($ret_order)) {

                        $number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);

                        $date = substr($row_ord->ordercode,0,4)."/".substr($row_ord->ordercode,4,2)."/".substr($row_ord->ordercode,6,2)." (".substr($row_ord->ordercode,8,2).":".substr($row_ord->ordercode,10,2).")";
                        if($row_ord->prod_cnt > 1) $productname = strcutMbDot($row_ord->productname, 50)." 외 ".($row_ord->prod_cnt-1)."건";
                        else $productname = strcutMbDot($row_ord->productname, 50);
?>
						<tr>
							<td><?=$number?></td>
							<td><?=$date?></td>
							<td><A HREF="javascript:OrderDetailView('<?=$row_ord->ordercode?>')"><?=$row_ord->ordercode?></A></td>
							<td class="ta-l"><?=$productname?></td>
							<td><?=number_format($row_ord->price-$row_ord->dc_price-$row_ord->reserve+$row_ord->deli_price)?></td>
							<td><?=$arpm[$row_ord->paymethod[0]]?></td>
							<td><?=$o_step[$row_ord->oi_step1][$row_ord->oi_step2]?></td>
						</tr>
<?
                        $cnt_ord--;
                        $cnt++;
                    }
?>
                        <tr>
                            <td colspan="7" class="ta-c">
                                <div id="page_navi01" style="height:'40px'">
                                    <div class="page_navi">
                                        <ul>
                                            <?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
<?
                } else {
?>
						<tr>
							<td colspan="7" class="ta-c">주문 내역이 없습니다.</td>
						</tr>
<?
                }
pmysql_free_result($ret_order);
?>

					</tbody>
				</table>

                <form name=idxform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
                <input type="hidden" name="id" value="<?=$mem_id?>">
                <input type="hidden" name="menu" value="order">
                <input type=hidden name=type>
                <input type=hidden name=ordercodes>
                <input type=hidden name=block value="<?=$block?>">
                <input type=hidden name=gotopage value="<?=$gotopage?>">
                <input type=hidden name=orderby value="<?=$orderby?>">
                <input type=hidden name=s_check value="<?=$s_check?>">
                <input type=hidden name=search value="<?=$search?>">
                <input type=hidden name=search_start value="<?=$search_start?>">
                <input type=hidden name=search_end value="<?=$search_end?>">
                <input type=hidden name=dvcode value="<?=$dvcode?>">
                <input type=hidden name=oistep1 value="<?=$oistep1?>">
                <input type=hidden name=oi_type value="<?=$oi_type?>">
                <input type=hidden name=paymethod value="<?=$paymethod?>">
                <input type=hidden name=paystate value="<?=$paystate?>">
                <input type=hidden name=s_date value="<?=$s_date?>">
                <input type=hidden name=sel_vender value="<?=$sel_vender?>">
                <input type=hidden name=brandname value="<?=$brandname?>">
                </form>


				<dl class="help-attention mt-50">
					<dt>도움말</dt>
					<!-- <dd>1. 비회원인 경우는 어쩌고 저쩌고</dd>
					<dd>2. 회원인 경우는 어쩌고 저쩌고</dd> -->
				</dl>


			</div><!-- //.contentsBody -->