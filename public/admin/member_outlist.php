<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");
include("calendar.php");

####################### 페이지 접근권한 check ###############
$PageCode = "me-1";
$MenuCode = "member";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$outid=$_POST["outid"];

//sendmail($to, $subject, $body, $header)
if ($type=="delete" && ord($outid)) {
	$sql = "SELECT * FROM tblmember WHERE id='{$outid}'";
	$result = pmysql_query($sql,get_db_conn());
	if ($row = pmysql_fetch_object($result)) {
		$email=$row->email;

		//로그 저장 텍스트를 만든다.
		$savetemp = "====================".date("Y-m-d H:i:s")."====================\n";
		if ($row=pmysql_fetch_object($result)) {
			foreach($row as $key=>$val){
				$savetemp.= $key." : ".$val."\n";
			}
		}
		$savetemp.= "\n";

	}
	pmysql_free_result($result);

	$sql = "SELECT COUNT(*) as cnt FROM tblorderinfo WHERE id='{$outid}'";
	$result= pmysql_query($sql,get_db_conn());
	$row = pmysql_fetch_object($result);
	pmysql_free_result($result);
	if ($row->cnt==0) {
		$sql = "DELETE FROM tblmember WHERE id = '{$outid}'";
		$state="Y";
	}else {
		$sql = "UPDATE tblmember SET ";
		$sql.= "passwd			= '', ";
		$sql.= "resno			= '', ";
		$sql.= "email			= '', ";
		$sql.= "news_yn			= 'N', ";
		$sql.= "age				= '', ";
		$sql.= "gender			= '', ";
		$sql.= "job				= '', ";
		$sql.= "birth			= '', ";
		$sql.= "home_post		= '', ";
		$sql.= "home_addr		= '', ";
		$sql.= "home_tel		= '', ";
		$sql.= "mobile			= '', ";
		$sql.= "office_post		= '', ";
		$sql.= "office_addr		= '', ";
		$sql.= "office_tel		= '', ";
		$sql.= "memo			= '', ";
		$sql.= "reserve			= 0, ";
		$sql.= "joinip			= '', ";
		$sql.= "ip				= '', ";
		$sql.= "authidkey		= '', ";
		$sql.= "group_code		= '', ";
		$sql.= "member_out		= 'Y', ";
		$sql.= "etcdata			= '', ";
		$sql.= "mb_department			= '', ";
		$sql.= "mb_facebook_oauthtoken			= '', ";
		$sql.= "mb_facebook_email			= '', ";
		$sql.= "mb_facebook_id			= '' ";
		$sql.= "WHERE id = '{$outid}'";
		$state="V";
	}

	//탈퇴회원정보를 파일로 저장한다.
	$file = "../data/backup/tblmember_out_".date("Y")."_".date("m")."_".date("d").".txt";
	if(!is_file($file)){
		$f = fopen($file,"a+");
		fclose($f);
		chmod($file,0777);
	}
	file_put_contents($file,$savetemp,FILE_APPEND);

	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblreserve WHERE id='{$outid}'";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblcouponissue WHERE id='{$outid}'";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblmemo WHERE id='{$outid}'";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblrecommendmanager WHERE rec_id='{$outid}'";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblrecomendlist WHERE id='{$outid}'";
	pmysql_query($sql,get_db_conn());
	$sql = "DELETE FROM tblpersonal WHERE id='{$outid}'";
	pmysql_query($sql,get_db_conn());
	$sql = "UPDATE tblmemberout SET state='{$state}' WHERE id='{$outid}'";
	pmysql_query($sql,get_db_conn());

	$maildata = "[{$_shopdata->shopname}]에서 회원 탈퇴 처리를 해드렸습니다.<br>";
	$maildata.= "그동안 저희 쇼핑몰을 이용해 주셔서 감사합니다.<br>";
	$maildata.= $_shopdata->shopname." 쇼핑몰 운영자 올림";
	sendmail($email,"[{$_shopdata->shopname}]회원탈퇴 처리가 완료되었습니다.",$maildata,"From: {$_shopdata->info_email}\r\nContent-Type: text/html; charset=euc-kr\r\n");

	$log_content = "## 회원삭제 : ID:$outid ##";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	$onload="<script>window.onload=function(){ alert('{$outid}님의 탈퇴처리가 완료되었습니다.');}</script>\n";
} elseif ($type=="cancel" && ord($outid)) {
	pmysql_query("DELETE FROM tblmemberout WHERE id = '{$outid}'",get_db_conn());
	$onload="<script>window.onload=function(){ alert('{$outid} 회원님의 탈퇴요청을 취소처리 하였습니다.');}</script>\n";
}

$CurrentTime = time();
$period[0] = date("Y-m-d",$CurrentTime);
$period[1] = date("Y-m-d",$CurrentTime-(60*60*24*7));
$period[2] = date("Y-m-d",$CurrentTime-(60*60*24*14));


$search_start=$_POST["search_start"];
$search_end=$_POST["search_end"];
$vperiod=(int)$_POST["vperiod"];
$search=$_POST["search"];

$search_start=$search_start?$search_start:$period[0];
$search_end=$search_end?$search_end:date("Y-m-d",$CurrentTime);
$search_s=$search_start?str_replace("-","",$search_start."000000"):str_replace("-","",$period[0]."000000");
$search_e=$search_end?str_replace("-","",$search_end."235959"):date("Ymd",$CurrentTime)."235959";
$s_check=$_POST["s_check"];
if(!$s_check) $s_check="id";

${"check_s_check".$s_check} = "checked";
${"check_vperiod".$vperiod} = "checked";

$termday = (strtotime($search_end)-strtotime($search_start))/86400;
if ($termday>367) {
	alert_go('검색기간은 1년을 초과할 수 없습니다.');
}
include("header.php"); 
?>
<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {

}

function CheckSearch() {
	document.form1.submit();
}

function OnChangePeriod(val) {
	var pForm = document.form1;
	var period = new Array(7);
	period[0] = "<?=$period[0]?>";
	period[1] = "<?=$period[1]?>";
	period[2] = "<?=$period[2]?>";
	
	pForm.search_start.value = period[val];
	pForm.search_end.value = period[0];
}

function OutResult(id){
	if(confirm('해당 회원을 탈퇴처리하시겠습니까?')){
		document.form2.type.value="delete";
		document.form2.outid.value=id;
		document.form2.submit();
	}
}

function OutCancel(id) {
	if(confirm('해당 회원의 탈퇴를 취소하시겠습니까?')){
		 document.form2.type.value="cancel";
		 document.form2.outid.value=id;
		 document.form2.submit();
	}
}

function GoPage(block,gotopage) {
	document.form2.block.value = block;
	document.form2.gotopage.value = gotopage;
	document.form2.submit();
}

function memberOutInfo(id) {
	window.open("about:blank","out_info","height=400,width=400,scrollbars=yes");
	document.form3.id.value=id;
	document.form3.submit();
}
</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 회원관리 &gt; 회원정보관리 &gt;<span>탈퇴요청 관리</span></p></div></div>
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
			<?php include("menu_member.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<input type=hidden name=outid>
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">탈퇴요청 관리</div>
					<!-- 소제목 -->
					<div class="title_depth3_sub"><span>쇼핑몰에서 탈퇴요청한 회원만 조회 및 탈퇴관리할 수 있습니다.</span></div>
                </td>
            </tr>
            <tr>
            	<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">탈퇴회원 조회</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR>
					<th><span>검색기간 선택</span></th>
								<td><input class="input_bd_st01" type="text" name="search_start" OnClick="Calendar(event)" value="<?=$search_start?>"/> ~ <input class="input_bd_st01" type="text" name="search_end" OnClick="Calendar(event)" value="<?=$search_end?>"/>
							<img src=images/btn_today01.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(0)">
							<img src=images/btn_day07.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(1)">
							<img src=images/btn_day14.gif border=0 align=absmiddle style="cursor:hand" onclick="OnChangePeriod(2)">
							</td>
				</TR>
				<TR>
					<th><span>검색어 입력</span></th>
					<TD><input name=search size=47 value="<?=$search?>" class="input"> <select size=1 name=s_check class="select">
						<option value="id" <?php if($s_check=="id") echo "selected"?>>회원 아이디
						<option value="name" <?php if($s_check=="name") echo "selected"?>>회원 이름
						</select> <a href="javascript:CheckSearch();"><img src="images/btn_search3.gif"  border="0" align=absmiddle></a></TD>
				</TR>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">탈퇴회원 검색 결과</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style02">
				<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<TR align=center>
					<th>No</th>
					<th>탈퇴일자/IP</th>
					<th>회원ID</th>
					<th>이름</th>
					<th>전화</th>
					<th>이메일</th>
					<!-- <th>탈퇴사유</th>
					<th>탈퇴상태</th> -->
				</TR>
<?php
		$colspan=7;
		$qry = "WHERE (date >= '{$search_s}' AND date <= '{$search_e}') ";
		if(ord($search)) $qry.= "AND {$s_check} LIKE '{$search}%' ";

		$sql = "SELECT COUNT(*) as t_count FROM tblmemberout ";
		$sql.= $qry;
		$paging = new Paging($sql,10,20);
		$t_count = $paging->t_count;
		$gotopage = $paging->gotopage;

		$sql = "SELECT * FROM tblmemberout ";
		$sql.= $qry." ";
		$sql.= "ORDER BY date DESC ";
		$sql = $paging->getSql($sql);
		$result = pmysql_query($sql,get_db_conn());
		$cnt=0;
		$message = array ("Y"=>"회원ID <font color=#00209E><b>포함</b></font> 삭제","V"=>"회원ID <font color=#FF5D00><b>미포함</b></font> 삭제");
		while($row=pmysql_fetch_object($result)) {
			$number = ($t_count-($setup['list_num'] * ($gotopage-1))-$cnt);
			$str_date = substr($row->date,0,4)."/".substr($row->date,4,2)."/".substr($row->date,6,2)."<br><span class=\"font_orange\">({$row->ip})</span>";
			echo "<tr>\n";
			echo "	<TD>{$number}</td>\n";
			echo "	<TD>{$str_date}</td>\n";
			echo "	<TD><b><span class=\"font_orange\">{$row->id}</span></b></TD>\n";
			echo "	<TD>{$row->name}</td>\n";
			echo "	<TD>&nbsp;{$row->tel}</td>\n";
			echo "	<TD>{$row->email}</td>\n";
			//echo "	<TD><a href=\"javascript:memberOutInfo('{$row->id}');\"><img src = '/admin/images/btn_detail.gif'></a></td>\n";
			//echo "	<TD>".($row->state=="N"?"<a href=\"javascript:OutResult('{$row->id}');\"><img src=\"images/icon_tal.gif\" border=\"0\"></a>&nbsp;<a href=\"javascript:OutCancel('{$row->id}');\"><img src=\"images/icon_canceltal.gif\" border=\"0\"></a>":$message[$row->state])."</td>\n";
			echo "</tr>\n";
			$cnt++;
		}
		pmysql_free_result($result);

		if ($cnt==0) {
			echo "<tr><td colspan={$colspan} align=center>회원 탈퇴요청 정보가 존재하지 않습니다.</td></tr>";
		}
?>
				</TABLE>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<table cellpadding="0" cellspacing="0" width="100%">
<?php				
		echo "<tr>\n";
		echo "	<td width=\"100%\" class=\"font_size\" colspan={$colspan} align=center>\n";
		echo "		".$a_div_prev_page.$paging->a_prev_page.$paging->print_page.$paging->a_next_page.$a_div_next_page;
		echo "	</td>\n";
		echo "</tr>\n";
?>
				</table>
				</td>
			</tr>
			<tr>
				<td height="20"></td>
			</tr>
			<tr>
				<td>
				<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<dl>
							<dt><span>회원탈퇴한 ID</span></dt>
							<dd>- 회원탈퇴처리 후 쇼핑몰에 등록된 모든 정보가 삭제되며 탈퇴회원에게 안내 메일이 발송됩니다.<br>
								<b>&nbsp;&nbsp;</b>재가입은 바로 가능합니다. 단, 탈퇴시 사용한 ID는 사용할 수 없습니다.<br>
							</dd>	
						</dl>
						<dl>
							<dt><span>회원탈퇴시 주문내역관리</span></dt>
							<dd>- 회원탈퇴처리시 <span class="font_orange"><b>[해당ID포함 삭제]</b></span>와 <span class="font_blue"><b>[해당ID미포함 삭제]</b></span>로 구분됩니다<br>					<b>&nbsp;&nbsp;</b><span class="font_orange"><b>[해당ID포함 삭제]</b></span>&nbsp;&nbsp;&nbsp;<b>&nbsp;</b>: <span style="letter-spacing:-0.5pt;">해당ID로 주문서가 <span class="font_orange">존재하지 않을 경우</span> 모든 정보가 삭제되어 <span class="font_orange">로그인이 불가능합니다</span>.</span><br>
								<b>&nbsp;&nbsp;</b><span class="font_blue"><b>[해당ID미포함 삭제]</b></span> : <span style="letter-spacing:-0.5pt;">해당ID로 주문서가 <span class="font_blue">존재한 경우</span> ID는 삭제되지 않습니다. <span class="font_blue">로그인 및 주문조회, 회원정보 확인이 가능합니다</span>.</span><br>
							</dd>
						</dl>
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
			</table>
			</form>
			</td>
		</tr>
		</table>
		</td>
	</tr>
	</table>
	</td>
</tr>
</table>
<form name=form2 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<input type=hidden name=type>
<input type=hidden name=outid>
<input type=hidden name=block value="<?=$block?>">
<input type=hidden name=gotopage value="<?=$gotopage?>">
<input type=hidden name=search_start value="<?=$search_start?>">
<input type=hidden name=search_end value="<?=$search_end?>">
<input type=hidden name=vperiod value="<?=$vperiod?>">
<input type=hidden name=search value="<?=$search?>">
<input type=hidden name=s_check value="<?=$s_check?>">
</form>
<form name=form3 action="member_outcontent.php" method=post target=out_info>
<input type=hidden name=id>
<input type=hidden name=type>
</form>
<?=$onload?>
<?php 
include("copyright.php");
