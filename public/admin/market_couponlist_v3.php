<?php // hspark
$mode	= $_REQUEST['mode'];
if(!ord($mode)) {
	$type					= $_POST["type"];
	if(ord($type)) { 		
		$coupon_code	= $_POST["coupon_code"];
		$detail_auto		= $_POST["detail_auto"];

		$imagepath=$Dir.DataDir."shopimages/etc/";

		if($type=="detail_auto" && ord($coupon_code)) {	//제품 상세페이지에 쿠폰을 노출

			$sql = "UPDATE tblcouponinfo SET detail_auto='{$detail_auto}' WHERE coupon_code = '{$coupon_code}' ";
			pmysql_query($sql,get_db_conn());
			
			if(!pmysql_errno()) {	
				echo "<script>alert('제품 상세페이지에 쿠폰을 노출 ";
				if ($detail_auto == 'N') echo "안";
				echo "하였습니다.'); parent.location.reload();</script>";
				exit;
			} else {		
				echo "<script>alert('제품 상세페이지에 쿠폰 노출 설정중 오류가 발생하였습니다.');</script>";
				exit;
			}

		} else if($type=="start" && ord($coupon_code)) {	//쿠폰 발급 시작

			$sql = "UPDATE tblcouponinfo SET issue_status='Y' WHERE coupon_code = '{$coupon_code}' ";
			pmysql_query($sql,get_db_conn());

			if(!pmysql_errno()) {	
				echo "<script>alert('해당 쿠폰이 발급시작 되었습니다.'); parent.location.reload();</script>";
				exit;
			} else {		
				echo "<script>alert('발급시작중 오류가 발생하였습니다.');</script>";
				exit;
			}

		} else if($type=="stop" && ord($coupon_code)) {	//쿠폰 발급 중지

			$sql = "UPDATE tblcouponinfo SET issue_status='N' WHERE coupon_code = '{$coupon_code}' ";
			pmysql_query($sql,get_db_conn());

			if(!pmysql_errno()) {	
				echo "<script>alert('해당 쿠폰이 발급중지 되었습니다.\\n\\n기존 발급된 쿠폰만 사용가능합니다.'); parent.location.reload();</script>";
				exit;
			} else {		
				echo "<script>alert('발급중지중 오류가 발생하였습니다.');</script>";
				exit;
			}

		} else if($type=="delete" && ord($coupon_code)) {	//쿠폰 삭제 (완전삭제)
			
			pmysql_query("DELETE FROM tblcouponproduct WHERE coupon_code = '{$coupon_code}'", get_db_conn());
			pmysql_query("DELETE FROM tblcouponcategory WHERE coupon_code = '{$coupon_code}'", get_db_conn());
			pmysql_query("DELETE FROM tblcouponissue_standby WHERE coupon_code = '{$coupon_code}' ", get_db_conn());

			if(file_exists($imagepath."COUPON_{$coupon_code}.gif")) {
				unlink($imagepath."COUPON_{$coupon_code}.gif");
			}

			pmysql_query("DELETE FROM tblcouponpaper WHERE coupon_code = '{$coupon_code}' ", get_db_conn());
			pmysql_query("DELETE FROM tblcouponinfo WHERE coupon_code = '{$coupon_code}' ", get_db_conn());

			if(!pmysql_errno()) {	
				echo "<script>alert('해당 쿠폰이 삭제되었습니다.'); parent.location.reload();</script>";
				exit;
			} else {		
				echo "<script>alert('삭제중 오류가 발생하였습니다.');</script>";
				exit;
			}
		}
	}

	$CurrentTime = time();
	$period[0] = date("Y-m-d",$CurrentTime);
	$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
	$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));
	$period[3] = date("Y-m-d",strtotime('-1 month'));
	$period[4] = substr($_shopdata->regdate,0,4)."-".substr($_shopdata->regdate,4,2)."-".substr($_shopdata->regdate,6,2);

	$orderby    = $_GET["orderby"];
	if(ord($orderby)==0) $orderby = "DESC";

	$s_coupon_type	= $_GET["s_coupon_type"];
	$search			= trim($_GET["search"]);
	$s_date			= $_GET["s_date"];
	$e_date			= $_GET["e_date"];
	$search_start	= $_GET["search_start"];
	$search_end		= $_GET["search_end"];
	$s_sale_type	= $_GET["s_sale_type"]?$_GET["s_sale_type"]:"0";
	$s_use_type	    = $_GET["s_use_type"]?$_GET["s_use_type"]:"0";
	$s_status	    = $_GET["s_status"]?$_GET["s_status"]:"0";

	$selected[s_coupon_type][$s_coupon_type]    = 'selected';
	$selected[s_sale_type][$s_sale_type]  = 'checked';
	$selected[s_use_type][$s_use_type]  = 'checked';
	$selected[s_status][$s_status]  = 'checked';

	$s_date	= $s_date?$s_date:$period[3];
	$e_date	= $e_date?$e_date:date("Y-m-d",$CurrentTime);
	//$search_start	= $search_start?$search_start:$period[0];
	//$search_end	= $search_end?$search_end:date("Y-m-d",$CurrentTime);
	$s_d				= $s_date?str_replace("-","",$s_date."000000"):"";
	$e_d				= $e_date?str_replace("-","",$e_date."235959"):"";
	$search_s		= $search_start?str_replace("-","",$search_start."00"):"";
	$search_e		= $search_end?str_replace("-","",$search_end."23"):"";	

	// 기본 검색 조건
	$qry_from = "tblcouponinfo a ";
	$qry.= "WHERE issue_code IN ('".implode("','", $coupon_issue_code)."') ";

	//발급 구분
	if(ord($s_coupon_type)) {
		$qry.= "AND coupon_type = '{$s_coupon_type}' ";
	}

	// 쿠폰 명
	if(ord($search)) {
// 		$qry.= "AND coupon_name like '%{$search}%' ";
		$search = trim($search);
		$temp_search = explode("\r\n", $search);
		$cnt = count($temp_search);
		
		$search_arr = array();
		for($i = 0 ; $i < $cnt ; $i++){
			array_push($search_arr, "'%".$temp_search[$i]."%'");
		}
		
		$qry.= "AND coupon_name LIKE any ( array[".implode(",", $search_arr)."] ) ";
	}

	//생성 일자
	if ($s_d != "" || $e_d != "") { 
		if(substr($s_d,0,8)==substr($e_d,0,8)) {
			$qry .= "AND date LIKE '".substr($s_d,0,8)."%' ";
		} else {
			$qry .= "AND date>='{$s_d}' AND date <='{$e_d}' ";
		}
	}

	// 유효 기간
	if ($search_s != "" || $search_e != "") { 
		if(substr($search_s,0,8)==substr($search_e,0,8)) {
			$qry.= "AND (date_start LIKE '".substr($search_s,0,8)."%' OR date_end LIKE '".substr($search_e,0,8)."%' ) ";
		} else {
			$qry.= "AND ((date_start > '0' AND ((date_start >= '{$search_s}' AND date_start <='{$search_e}') OR (date_end >= '{$search_s}' AND date_end <='{$search_e}'))) OR (date_start <= '0' AND date_end >= '{$search_s}' AND date_end <='{$search_e}'))";
		}
	}

	// 혜택 구분
	if(ord($s_sale_type))	{
		if($s_sale_type != "0") $qry.= " AND sale_type = '{$s_sale_type}' ";
	}

	// 사용 방법
	if(ord($s_sale_type))	{
		if($s_use_type != "0") $qry.= " AND coupon_use_type = '{$s_use_type}' ";
	}

	// 발급상태
	if(ord($s_status))	{
        // 발급중
		if($s_status == "1") {
            $qry.= " AND ( a.date_end >= '".date("YmdH")."' and a.issue_status = 'Y' 
	                        and (case when (a.coupon_type in ('1', '9') and a.sel_gubun in ('M', 'E')) then (select count(*) from tblcouponissue_standby WHERE coupon_code = a.coupon_code AND used = 'N') else 99999 end ) > 0
	                        and (case when a.coupon_type in ('7') then (select count(*) from tblcouponpaper WHERE coupon_code = a.coupon_code AND used = 'N') else 99999 end ) > 0 
                        )";
        }

        // 발급중지
		if($s_status == "2") {
            $qry.= " AND ( a.date_end >= '".date("YmdH")."' and a.issue_status = 'N' 
	                        and (case when (a.coupon_type in ('1', '9') and a.sel_gubun in ('M', 'E')) then (select count(*) from tblcouponissue_standby WHERE coupon_code = a.coupon_code AND used = 'N') else 99999 end ) > 0
	                        and (case when a.coupon_type in ('7') then (select count(*) from tblcouponpaper WHERE coupon_code = a.coupon_code AND used = 'N') else 99999 end ) > 0 
                        )";
        }

        // 기간종료
		if($s_status == "3") {
            $qry.= " AND ( a.date_end < '".date("YmdH")."' and a.issue_status != 'R' )";
        }
	}

	include("header.php"); 

	$sql = "SELECT COUNT(*) as t_count FROM {$qry_from} {$qry} ";
	//echo "Sql = > ".$sql;
	$result = pmysql_query($sql,get_db_conn());
	$paging = new newPaging($sql,10,10,'GoPage');
	$t_count = $paging->t_count;	
	$gotopage = $paging->gotopage;	
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CouponView(code) {
	window.open("about:blank","couponview","width=650,height=650,scrollbars=no");
	document.cform.coupon_code.value=code;
	document.cform.submit();
}

function CouponModify(code) {
	document.mform.mode.value='modify';
	document.mform.coupon_code.value=code;
	document.mform.submit();
}

function CouponDetailAuto(code, detail_auto) {
	if(detail_auto == 'N') {
		var alert_text	= "제품 상세페이지에 쿠폰을 노출 안하시겠습니까?";
	} else if(detail_auto == 'Y') {
		var alert_text	= "제품 상세페이지에 쿠폰을 노출 하시겠습니까?";
	}
	if(confirm(alert_text)) {
		document.exeform.type.value='detail_auto';
		document.exeform.detail_auto.value=detail_auto;
		document.exeform.coupon_code.value=code;
		document.exeform.target="hiddenframe";
		document.exeform.submit();
	}
}

function CouponIssueStatus(code, type) {
	if(type == 'stop') {
		var alert_text	= "기존 회원에게 발급된 쿠폰은 사용이 가능합니다.\n\n해당 쿠폰 발급을 중지하시겠습니까?";
	} else if(type == 'start') {
		var alert_text	= "해당 쿠폰 발급을 시작하시겠습니까?";
	}
	if(confirm(alert_text)) {
		document.exeform.type.value=type;
		document.exeform.coupon_code.value=code;
		document.exeform.target="hiddenframe";
		document.exeform.submit();
	}
}

function CouponCopy(code) {
	document.mform.mode.value='copy_insert';
	document.mform.coupon_code.value=code;
	document.mform.submit();
}
function CouponRoute(url) {
	var IE=(document.all)?true:false;
	if (IE) {
		if(confirm("경로를 복사하시겠습니까?"))
			window.clipboardData.setData("Text", url);
	} else {
		temp = prompt("쿠폰발급 경로입니다. Ctrl+C를 눌러 클립보드로 복사하세요", url);
	}
}

function CouponIssue(code){
	window.open("about:blank","couponissuelist","width=650,height=650,scrollbars=no");
	document.iform.coupon_code.value=code;
	document.iform.submit();
}
function CouponDelete(code) {
	if(confirm("해당 쿠폰을 삭제하시겠습니까?")) {
		document.exeform.type.value="delete";
		document.exeform.coupon_code.value=code;
		document.exeform.target="hiddenframe";
		document.exeform.submit();
	}
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	

	pForm.s_date.value = period[val];
	pForm.e_date.value = period[0];
}

function OnChangePeriod2(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	period[3] = "<?=$period[3]?>";
	period[4] = "<?=$period[4]?>";
	

	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function searchForm() {
	document.form1.block.value = '';
	document.form1.gotopage.value = '';
	document.form1.submit();
}

function GoPage(block,gotopage) {
	document.form1.block.value = block;
	document.form1.gotopage.value = gotopage;
	document.form1.submit();
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 마케팅지원 &gt; 쿠폰발행 서비스 설정 &gt;<span><?=$menu_title_name?> 관리</span></p></div></div>
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
			<?php include("menu_market.php"); ?>
			</td>
			<td></td>
			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=GET>
			<input type=hidden name=block value="<?=$block?>">
			<input type=hidden name=gotopage value="<?=$gotopage?>">
			<table cellpadding="0" cellspacing="0" width="100%" style="table-layout:fixed">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3"><?=$menu_title_name?> 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>현재 발행한 <?=$menu_title_name?> 내역및 발급정보등을 관리할 수 있는 메뉴 입니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><?=$menu_title_name?> 현황 조회</div>
				</td>
			</tr>
			<tr>
				<td>				
					<table cellpadding="0" cellspacing="0" width="100%" bgcolor="white">
					<tr>
						<td width="100%">
						<div class="table_style01">
						<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
						<tr>
							<th><span>발급 구분</span></th>
							<TD class="td_con1">
								<select name=s_coupon_type class="select">
									<option value=''>전체</option>
								<?if ($coupon_type_check == 'auto') {?>
									<option value=2 <?=$selected[s_coupon_type]['2']?>>회원가입</option>
									<option value=3 <?=$selected[s_coupon_type]['3']?>>기념일</option>
									<option value=10 <?=$selected[s_coupon_type]['10']?>>생일</option>
									<option value=4 <?=$selected[s_coupon_type]['4']?>>첫구매</option>
									<option value=11 <?=$selected[s_coupon_type]['11']?>>상품구매 후기</option>
									<option value=12 <?=$selected[s_coupon_type]['12']?>>구매 수량 충족</option>
									<option value=13 <?=$selected[s_coupon_type]['13']?>>구매 금액 충족</option>
									<option value=14 <?=$selected[s_coupon_type]['14']?>>주말 출석</option>
									<option value=15 <?=$selected[s_coupon_type]['15']?>>회원 등급별</option>
								<?} else if ($coupon_type_check == 'normal') {?>
									<option value=16 <?=$selected[s_coupon_type]['16']?>>일반발급</option>
									<option value=6 <?=$selected[s_coupon_type]['6']?>>다운로드</option>
									<option value=1 <?=$selected[s_coupon_type]['1']?>>즉시발급</option>
									<option value=7 <?=$selected[s_coupon_type]['7']?>>페이퍼</option>
									<option value=9 <?=$selected[s_coupon_type]['9']?>>무료배송</option>
								<?}?>
								</select>
                            </TD>
						</tr>
						<tr>
							<th><span>쿠폰 명</span></th>
							<TD class="td_con1">
								<!--  
							    <input type=text name=search value="<?=$search?>" style="width:407" class="input">
								-->
								<textarea rows="2" cols="10" class="w200" name="search" id="search" style="resize:none;vertical-align:middle;"><?=$search?></textarea>
                            </TD>
						</tr>
						<tr>
							<th><span>생성 일자</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="s_date" OnClick="Calendar(event)" value="<?=$s_date?>"/> ~ <input class="input_bd_st01" type="text" name="e_date" OnClick="Calendar(event)" value="<?=$e_date?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(3)">
								<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(4)">
							</td>
						</tr>
						<tr>
							<th><span>유효 기간</span></th>
							<td>
                                <input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
								<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod2(0)">
								<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod2(1)">
								<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod2(2)">
								<img src=images/btn_day30.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod2(3)">
								<img src=images/btn_day_total.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod2(4)">
							</td>
						</tr>
                        <tr>
							<th><span>혜택 구분</span></th>
							<td class="td_con1">
                                <input type="radio" name="s_sale_type" value="0" <?=$selected[s_sale_type]["0"]?>>전체</input>
                                <input type="radio" name="s_sale_type" value="4" <?=$selected[s_sale_type]["4"]?>>금액 쿠폰</input>
                                <input type="radio" name="s_sale_type" value="2" <?=$selected[s_sale_type]["2"]?>>할인율 쿠폰</input>
                            </td>
						</tr>
                        <!-- <tr>
							<th><span>사용 방법</span></th>
							<td class="td_con1">
                                <input type="radio" name="s_use_type" value="0" <?=$selected[s_use_type]["0"]?>>전체</input>
                                <input type="radio" name="s_use_type" value="2" <?=$selected[s_use_type]["2"]?>>상품별 쿠폰</input>
                                <input type="radio" name="s_use_type" value="1" <?=$selected[s_use_type]["1"]?>>장바구니 쿠폰</input>
                            </td>
						</tr> -->
                        <tr>
							<th><span>발급상태</span></th>
							<td class="td_con1">
                                <input type="radio" name="s_status" value="0" <?=$selected[s_status]["0"]?>>전체</input>
                                <input type="radio" name="s_status" value="1" <?=$selected[s_status]["1"]?>>발급중</input>
                                <input type="radio" name="s_status" value="2" <?=$selected[s_status]["2"]?>>발급중지</input>
                                <input type="radio" name="s_status" value="3" <?=$selected[s_status]["3"]?>>기간종료</input>
                            </td>
						</tr>
						</table>
						</div>
						</td>
					</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td style="padding-top:4pt;" align="center"><a href="javascript:searchForm();"><img src="images/botteon_search.gif" border="0"></a></td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td style="padding-bottom:3pt;">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td width="" align="right"><img src="images/icon_8a.gif" border="0">총 : <B><?=number_format($t_count)?></B>건, &nbsp;&nbsp;<img src="images/icon_8a.gif" border="0">현재 <b><?=$gotopage?>/<?=ceil($t_count/$setup['list_num'])?></b> 페이지</td>
				</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<col width=75></col>
				<col width=100></col>
				<col width=75></col>
				<col width=></col>
				<col width=75></col>
				<col width=130></col>
				<col width=150></col>
				<col width=70></col>
				<?if($coupon_type_check == 'normal') {?>
				<col width=60></col>
				<?}?>
				<col width=60></col>
				<col width=76></col>
				<?if($coupon_type_check == 'normal') {?>
				<col width=45></col>
				<col width=45></col>
				<col width=45></col>
				<?}?>
				<col width=45></col>
				<col width=45></col>
				<TR align=center>
					<th>생성일자</th>
					<th>발급구분</th>
					<th>쿠폰코드</th>
					<th>쿠폰명</th>
					<th>쿠폰종류</th>
					<th>혜택</th>
					<th>유효기간</th>
					<th>발급수</th>
					<?if($coupon_type_check == 'normal') {?>
					<th>상세노출</th>
					<?}?>
					<th>상태</th>
					<th>발급</th>
					<?if($coupon_type_check == 'normal') {?>
					<th>다운</th>
					<th>복사</th>
					<th>경로</th>
					<?}?>
					<th>수정</th>
					<th>삭제</th>
				</TR>
<?php
				$colspan	= 16;
				if($coupon_type_check == 'auto') $colspan	= 12;

				$sql = "SELECT * FROM {$qry_from} {$qry} ORDER BY date {$orderby}";
				$sql = $paging->getSql($sql);
                //exdebug($sql);
				$result = pmysql_query($sql,get_db_conn());
				$cnt=0;
				while($row=pmysql_fetch_object($result)) {
					$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
					$cnt++;
					
					$regdate = substr($row->date,0,4).".".substr($row->date,4,2).".".substr($row->date,6,2);

					if ($row->coupon_type == '2') $coupon_type	= '회원가입';
					if ($row->coupon_type == '3') $coupon_type	= '기념일';
					if ($row->coupon_type == '10') $coupon_type	= '생일';
					if ($row->coupon_type == '4') $coupon_type	= '첫구매';
					if ($row->coupon_type == '11') $coupon_type	= '상품구매 후기';
					if ($row->coupon_type == '12') $coupon_type	= '구매 수량 충족';
					if ($row->coupon_type == '13') $coupon_type	= '구매 금액 충족';
					if ($row->coupon_type == '14') $coupon_type	= '주말 출석';
					if ($row->coupon_type == '15') $coupon_type	= '회원 등급별';
					if ($row->coupon_type == '16') $coupon_type	= '일반발급';
					if ($row->coupon_type == '6') $coupon_type	= '다운로드';
					if ($row->coupon_type == '1') $coupon_type	= '즉시발급';
					if ($row->coupon_type == '7') $coupon_type	= '페이퍼';
					if ($row->coupon_type == '9') $coupon_type	= '무료배송';

					if($coupon_code==$row->coupon_code) {
						$coupon_name=$row->coupon_name;
					}

                    if($row->coupon_use_type == "1") $coupon_use_type = "장바구니";
                    else $coupon_use_type = "상품쿠폰";

					if($row->sale_type == '1' || $row->sale_type == '2') $sale2_text = "할인율 쿠폰&nbsp;&nbsp;&nbsp;";
					if($row->sale_type == '3' || $row->sale_type == '4') $sale2_text = "금액 쿠폰&nbsp;&nbsp;&nbsp;";

					if($row->sale_type<=2) $dan="%&nbsp;&nbsp;&nbsp;";
					else $dan="원&nbsp;&nbsp;&nbsp;";

					$maxPrice = $row->sale_max_money?"(최대 ".number_format($row->sale_max_money)."원)&nbsp;&nbsp;&nbsp;":'';
					
					$sale_text	= $sale2_text."<br><span class=\"font_orange\">".number_format($row->sale_money).$dan."<br>".$maxPrice."</span>";
					if ($row->coupon_type == '9') $sale_text	= "<span class=\"font_orange\">배송료 무료&nbsp;&nbsp;&nbsp;<br>(개별배송료 제외)&nbsp;&nbsp;&nbsp;</span>";
					
					if($row->date_start>0) {
						$date = substr($row->date_start,0,4).".".substr($row->date_start,4,2).".".substr($row->date_start,6,2)."<br>~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
					} else {
						$date = "발급일 부터 ".abs($row->date_start)."일동안,<br>~ ".substr($row->date_end,0,4).".".substr($row->date_end,4,2).".".substr($row->date_end,6,2);
					}
					
					if ($row->date_end < date("YmdH") && $row->issue_status != 'R') {
						$issue_status_text	= "<span class=\"font_orange5\">기간종료</span>";
					} else {
						$issue_status_chk	= "Y";
						if ($row->coupon_type == '1' || $row->coupon_type == '7' || $row->coupon_type == '9') {
							if (($row->coupon_type == '1' || $row->coupon_type == '9')&&($row->sel_gubun == 'M' || $row->sel_gubun == 'E')) { // 즉시 발급, 무료배송일 경우 회원선택 또는 회원업로드일 경우
								//발급 안받은 회원이 있는지 체크한다.
								list($coupon_issue_cnt)=pmysql_fetch_array(pmysql_query("select count(*) from tblcouponissue_standby WHERE coupon_code = '".$row->coupon_code."' AND used = 'N' "));
								if($coupon_issue_cnt == 0) $issue_status_chk	= "N";
							}
							if ($row->coupon_type == '7') {// 페이퍼 쿠폰일 경우
								//발급 안받은 회원이 있는지 체크한다.
								list($coupon_issue_cnt)=pmysql_fetch_array(pmysql_query("select count(*) from tblcouponpaper WHERE coupon_code = '".$row->coupon_code."' AND used = 'N' "));
								if($coupon_issue_cnt == 0) $issue_status_chk	= "N";
							}						
						}

						if ($issue_status_chk == 'Y') {
							if ($row->issue_status == 'R') $issue_status_text	= "발급대기";
							if ($row->issue_status == 'Y') $issue_status_text	= "<span class=\"font_blue\">발급중</span>";
							if ($row->issue_status == 'N') $issue_status_text	= "<span class=\"font_green\">발급중지</span>";
						} else {
							$issue_status_text	= "<span class=\"font_orange5\">발급완료</span>";
						}
					}

					echo "<TR align=center>\n";
					echo "	<TD height=65>{$regdate}</TD>\n";
					echo "	<TD>{$coupon_type}</TD>\n";
					echo "	<TD><A HREF=\"javascript:CouponView('{$row->coupon_code}');\" style='text-decoration:underline;'><B><span class=\"font_blue\">{$row->coupon_code}</span></B></A></TD>\n";
					echo "	<TD style='text-align:left;'><A HREF=\"javascript:CouponView('{$row->coupon_code}');\" style='text-decoration:underline;'><b>{$row->coupon_name}</b></A></TD>\n";
					echo "	<TD>{$coupon_use_type}</TD>\n";
					echo "	<TD style='text-align:right;'>".$sale_text."</TD>\n";
					echo "	<TD>{$date}</TD>\n";
					echo "	<TD><a href=\"javascript:CouponIssue('{$row->coupon_code}');\" style='text-decoration:underline;'>".number_format($row->issue_no)."</a></TD>\n";		
					if($coupon_type_check == 'normal') {
						echo "	<TD>";
						if ($row->date_end < date("YmdH") && $row->issue_status != 'R') {
							echo "-";
						} else {
							if ($row->coupon_type == '6') {
								if ($row->detail_auto == 'N') echo "<a href=\"javascript:CouponDetailAuto('{$row->coupon_code}','Y');\"><img src=\"images/icon_off.gif\" border=\"0\"></a>";
								else if ($row->detail_auto == 'Y') echo "<a href=\"javascript:CouponDetailAuto('{$row->coupon_code}','N');\"><img src=\"images/icon_on.gif\" border=\"0\"></a>";
							} else {
								echo "-";
							}
						}
						echo "</TD>\n";	
					}
					echo "	<TD><b>$issue_status_text</b></TD>\n";						
					echo "	<TD>";
					if ($row->date_end < date("YmdH") && $row->issue_status != 'R') {
						echo "-";
					} else {
						if ($issue_status_chk == 'Y') {
							if ($row->issue_status == 'R')  echo "<a href=\"javascript:CouponIssueStatus('{$row->coupon_code}','start');\"><img src=\"images/btn_start.gif\" border=\"0\"></a>";
							else if ($row->issue_status == 'Y') echo "<a href=\"javascript:CouponIssueStatus('{$row->coupon_code}','stop');\"><img src=\"images/btn_stop.gif\" border=\"0\"></a>";
							else if ($row->issue_status == 'N') echo "<a href=\"javascript:CouponIssueStatus('{$row->coupon_code}','start');\"><img src=\"images/btn_start.gif\" border=\"0\"></a>";
						} else {
							echo "-";
						}
					}
					echo "</TD>\n";	
					if($coupon_type_check == 'normal') {
						echo "	<TD>";
						echo $row->coupon_type == '7'?"<a href=\"coupon_view_excel_paper.php?coupon_code={$row->coupon_code}\"><img src=\"images/btn_down2.gif\" border=\"0\"></a>":"-";
						echo "</TD>\n";						
						echo "	<TD>";
						echo $row->date >= '20160603000000'?"<a href=\"javascript:CouponCopy('{$row->coupon_code}');\"><img src=\"images/btn_cate_copy.gif\" border=\"0\"></a>":'-';
						echo "</TD>\n";	
						echo "	<TD>";
						if ($row->date_end < date("YmdH") && $row->issue_status != 'R') {
							echo "-";
						} else {
							echo ($row->coupon_type == '6' || $row->coupon_type == '16')?"<a href=\"javascript:CouponRoute('http://".$_ShopInfo->getShopurl()."front/couponissue.php?coupon=".encrypt_md5("COUPON|".$row->coupon_type."|".$row->coupon_code."|END","*ghkddnjsrl*")."');\"><img src=\"images/btn_cate_copy.gif\" border=\"0\"></a>":"-";
						}
						echo "</TD>\n";
					}
					echo "	<TD>";
					echo $row->issue_status == 'R'?"<a href=\"javascript:CouponModify('{$row->coupon_code}');\"><img src=\"img/btn/btn_cate_modify.gif\" border=\"0\"></a>":"-";
					echo "</TD>\n";		
					echo "	<TD>";
					echo $row->issue_status == 'R'?"<a href=\"javascript:CouponDelete('{$row->coupon_code}');\"><img src=\"images/btn_cate_del.gif\" border=\"0\"></a>":"-";
					echo "</TD>\n";		
					echo "</TR>\n";
				}
				pmysql_free_result($result);
				if ($cnt==0) {
					echo "<tr><td colspan={$colspan} align=center>발행한 ".$menu_title_name."내역이 없습니다.</td></tr>";
				}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<div id="page_navi01" style="height:'40px'">
					<div class="page_navi">
						<ul>
							<?=$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page?>
						</ul>
					</div>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 매뉴얼 -->
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>						
						<dl>
							<dt><span><?=$menu_title_name?> 관리</span></dt>
							<dd>
								- 
							</dd>	
						</dl>						
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>

			<form name=cform action="market_couponview_v3.php" method=post target=couponview>
				<input type=hidden name=coupon_code>
			</form>
			<form name=mform action="<?=$_SERVER['PHP_SELF']?>" method=GET>
				<input type=hidden name=mode>
				<input type=hidden name=coupon_code>
				<input type=hidden name=block value="<?=$_GET['block']?>">
				<input type=hidden name=gotopage value="<?=$_GET['gotopage']?>">
				<input type=hidden name=s_coupon_type value="<?=$_GET['s_coupon_type']?>">
				<input type=hidden name=search value="<?=$_GET['search']?>">
				<input type=hidden name=s_date value="<?=$_GET['s_date']?>">
				<input type=hidden name=e_date value="<?=$_GET['e_date']?>">
				<input type=hidden name=search_start value="<?=$_GET['search_start']?>">
				<input type=hidden name=search_end value="<?=$_GET['search_end']?>">
				<input type=hidden name=s_sale_type value="<?=$_GET['s_sale_type']?>">
				<input type=hidden name=s_use_type value="<?=$_GET['s_use_type']?>">
			</form>
			<form name=exeform action="<?=$_SERVER['PHP_SELF']?>" method=post>
				<input type=hidden name=type>
				<input type=hidden name=coupon_code>
				<input type=hidden name=detail_auto>
			</form>
			<form name=iform action="market_couponissuelist_v3.php" method=post target=couponissuelist>
				<input type=hidden name=coupon_code>
			</form>
            <IFRAME name="hiddenframe" src="<?=$Dir?>blank.php" width=0 height=0 frameborder=0 align=TOP scrolling="no" marginheight="0" marginwidth="0"></IFRAME>
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
} else {	
	include_once("market_couponform_v3.php");
}
?>
