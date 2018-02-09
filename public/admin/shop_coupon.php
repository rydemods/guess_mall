<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

####################### 페이지 접근권한 check ###############
$PageCode = "sh-3";
$MenuCode = "shop";
if (!$_usersession->isAllowedTask($PageCode)) {
	include("AccessDeny.inc.php");
	exit;
}
#########################################################

$type=$_POST["type"];
$up_coupon_ok=$_POST["up_coupon_ok"];
$up_amount_floor			= $_POST["up_amount_floor"];				// 금액절삭
$brand_coupon=$_POST["brand_coupon"];

if ($type=="up") {
	$sql = "UPDATE tblshopinfo SET ";
	$sql.= "coupon_ok		= '{$up_coupon_ok}' ";
	pmysql_query($sql,get_db_conn());


	//쿠폰 기본설정시 쿠폰 사용 여부를 제외한 나머지를 저장한다.
	list($cp_num)=pmysql_fetch_array(pmysql_query("select num from tblcoupon "));
	if (!$cp_num) { 
		$sql = "INSERT INTO tblcoupon DEFAULT VALUES RETURNING num";
		$row2 = pmysql_fetch_array(pmysql_query($sql,get_db_conn()));
		$cp_num = $row2[0];	
	} 

	$sql = "UPDATE tblcoupon SET ";
	$sql.= "amount_floor	= '{$up_amount_floor}' ";
	$sql.= "WHERE num='{$cp_num}' ";
	pmysql_query($sql,get_db_conn());	
	
	//쿠폰정보의 금액 절삭을 모두 업데이트한다.
	$sql = "UPDATE tblcouponinfo SET amount_floor	= '{$up_amount_floor}' ";
	pmysql_query($sql,get_db_conn());	

	//브랜드별 쿠폰 사용 가능 퍼센트 셋팅
	foreach($brand_coupon as $bc=>$bcv){
		pmysql_query("update tblproductbrand set coupon_useper='".$bcv."' where bridx='".$bc."'");
	}

	DeleteCache("tblshopinfo.cache");
	$onload="<script>window.onload=function(){alert('쿠폰 관련 설정이 완료되었습니다.');location.href='shop_coupon.php'; }</script>\n";

	$log_content = "## 쿠폰설정 ## - 사용여부:$up_coupon_ok, 금액절삭 : $up_amount_floor ";
	ShopManagerLog($_ShopInfo->getId(),$connect_ip,$log_content);

	
}

list($coupon_ok)=pmysql_fetch("SELECT coupon_ok FROM tblshopinfo");
${"check_coupon_ok".$coupon_ok} = "checked";

list($amount_floor)=pmysql_fetch("SELECT amount_floor FROM tblcoupon");


$brand_sql = "SELECT a.*, b.brandname, b.productcode_a, b.bridx, b.staff_rate, b.coupon_useper FROM tblvenderinfo a JOIN tblproductbrand b ON a.vender = b.vender ";
$brand_sql.= "ORDER BY a.disabled ASC, a.vender DESC, lower(b.brandname) DESC ";
$brand_result=pmysql_query($brand_sql);
?>

<?php 
include("header.php");
?>

<script type="text/javascript" src="lib.js.php"></script>
<script language="JavaScript">
function CheckForm() {
	var form = document.form1;
	form.type.value="up";
	if (confirm("적용하시겠습니끼?")) {
		form.submit();
	}
}

</script>
<div class="admin_linemap"><div class="line"><p>현재위치 : 환경설정 &gt; 운영설정 &gt;<span>쿠폰 정책설정</span></p></div></div>
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
			<?php include("menu_shop.php"); ?>
			</td>

			<td></td>

			<td valign="top">
			<table cellpadding="0" cellspacing="0" width="100%">
			<tr><td height="8"></td></tr>
			<tr>
				<td>
					<!-- 페이지 타이틀 -->
					<div class="title_depth3">쿠폰 정책설정</div>
				</td>
			</tr>
			<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
			<input type=hidden name=type>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">쿠폰 정책설정</div>
				</td>
			</tr>
			<tr>
                <td style="padding-top:3pt; padding-bottom:3pt;">
                    
                    <!-- 도움말 -->
                    <div class="help_info01_wrap" style='min-height:30px;width:auto;'>
                        <ul style='margin:15px 0px 15px 50px;'>
                            <li><b style='font-size:14px;'>기본설정</b></li>
                            <li style='margin-top:8px'>1) <a href="javascript:parent.topframe.GoMenu(7,'market_couponnew.php');"><span class="font_blue">마케팅지원 > 쿠폰발행 서비스 설정</span></a> 에서 쿠폰 생성, 발급대상, 발급조회를 할 수 있습니다.</li>
                            <li>2) 쿠폰을 발행했더라도 쿠폰사용안함인 경우 회원들이 사용할 수 없습니다.</li>
                        </ul>
                    </div>                           
                </td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
					<table cellpadding="0" cellspacing="0" border="0" width="100%">
						<tr>
							<th><span>쿠폰 사용 여부</span></th>
							<td>
								<input type=radio id="idx_coupon_ok1" name=up_coupon_ok value="Y" <?=$check_coupon_okY?>><label style='CURSOR: hand; TEXT-DECORATION: none' onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_ok1>사용함</label>  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type=radio id="idx_coupon_ok2" name=up_coupon_ok value="N" <?=$check_coupon_okN?> onclick="javascript:if (!confirm('새쿠폰 발행 및 기존 쿠폰 발급이 모두 중지 됩니다. 선택하시겠습니까?')) {document.form1.up_coupon_ok.value='Y';}"><label style="CURSOR: hand; TEXT-DECORATION: none" onmouseover="style.textDecoration='underline'" onmouseout="style.textDecoration='none'" for=idx_coupon_ok2>사용안함</label>
							</td>
						</tr>
						
						<tr>								
							<th><span>금액절삭</span></th>
							<td>
							<SELECT name=up_amount_floor class="select">
		<?php
							$arfloor = array(1=>"일원단위, 예)12344 → 12340","십원단위, 예)12344 → 12300","백원단위, 예)12344 → 12000","천원단위, 예)12344 → 10000");
							$arcnt = count($arfloor);
							for($i=1;$i<$arcnt;$i++){
								echo "<option value=\"{$i}\"";
								if($amount_floor==$i) echo " selected";
								echo ">{$arfloor[$i]}</option>";
							}
		?>
							</SELECT>
							</td>
						</tr>
						
					</table>
				</div>
				</td>
			</tr>
			<tr>
				<td>
					<!-- 소제목 -->
					<div class="title_depth3_sub">신원 브랜드별 할인율 설정</div>
				</td>
			</tr>
			<tr>
				<td>
				<div class="table_style01">
                <TABLE cellSpacing=0 cellPadding=0 width="100%" border=0>
				<?while($brand_data=pmysql_fetch_object($brand_result)){?>
				<TR>
					<th><span><?=$brand_data->brandname?></span></th>
					<TD class="td_con1">
						상품 할인율 <input type=text name="brand_coupon[<?=$brand_data->bridx?>]" value="<?=$brand_data->coupon_useper?>" size=10 class="input">% 까지 쿠폰사용가능
					</TD>
				</TR>
				<?}?>
				</table>
				</div>
				</td>
			</tr>
			<tr>
				<td height=10></td>
			</tr>
			<tr>
				<td align="center"><a href="javascript:CheckForm();"><span class="btn-point">적용하기</span></a></td>
			</tr>
			</form>
			<tr>
				<td height=20></td>
			</tr>
			<tr>
				<td>
					<div class="sub_manual_wrap">
						<div class="title"><p>매뉴얼</p></div>
						<ul class="help_list">
							<li>쿠폰을 발행했더라도 쿠폰정책에서 [사용안함]인 경우 회원들이 사용할 수 없습니다.</li>
							<li>취소/반품/환불 시 쿠폰복원은 전체취소/전체반품/전체환불의 경우 복원되며, 부분취소/부분반품/부분환불의 경우 쿠폰복원을 제공하지 않습니다.</li>
							<li>신원/아울렛의 브랜드별 할인율에 따라 쿠폰사용이 가능합니다.</li>
						</ul>
						<!-- <dl>
							<dt><span>적립금 설정 안내</span></dt>
							<dd>- <b>적립금이 없는 쇼핑몰로 운영할 경우</b> : 현금결제 추가적립 공란+상품의 개별 적립금을 공란으로 설정<br>
							<b>&nbsp;&nbsp;</b>배송비는 적립금 계산에서 제외됩니다.<br>
							<b>&nbsp;&nbsp;</b>적립금은 배송완료 후 적립됩니다.(주문 취소시 적립금도 자동삭제, 비회원은 적립되지 않습니다.)<Br><br>
							- <b>사용한 적립금을 제외한 구매금액 대비 적립<span class="font_orange">(구매금액-사용적립금)</span>에 대한 안내</b><br>
							<b>&nbsp;&nbsp;</b><span class="font_blue"><b>적립금 미사용</b></span> : 상품가격(10,000원)&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;= 결제액(&nbsp;<span class="font_blue">10,000원</span> )에 대한 <span class="font_blue"><b>300원 적립(일반적립금)</b></span><br>
							<b>&nbsp;&nbsp;</b><span class="font_orange"><b>적립금</b>&nbsp;&nbsp;<b>&nbsp;&nbsp;사용</b></span> : 상품가격(10,000원) - 
							<span class="font_orange">사용적립금(2,000원)</span> = 결제액(<b>&nbsp;&nbsp;</b><span class="font_orange">8,000원</span> )에 대한 <span class="font_orange"><b>240원 적립</b></span>
							</dd>
						</dl> -->
					</div>
				</td>
			</tr>
			<tr><td height="50"></td></tr>
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
