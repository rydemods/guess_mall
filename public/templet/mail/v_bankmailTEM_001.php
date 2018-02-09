<!DOCTYPE html PUBtdC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=8;IE=EDGE">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>결제 완료</title>
</head>
<body>
<style type="text/css">
body {padding:0px; margin:0px;}
a , a:link , a:visited , a:active , a:hover , img {text-decoration:none; outline:0;border:none; color:#5e5e5e;}
</style>
<div style="width:684px; margin:0 auto; font-size:12px; color:#5e5e5e; font-family:dotum; text-align:left; border:1px solid #000">
<table width="684" cellpadding="0" cellspacing="0" border="0" align="center" >
	<tr>
		<td align="center">
<!-- 상단 -->
<table width="664" cellpadding="0" cellspacing="0" border="0" align="center" style="font-size:12px; color:#5e5e5e; font-family:dotum;">
	<tr><td colspan="2" height="15"></td></tr>
	<tr>
		<td align="left"><a href="http://<?=$shopurl?>" target="_blank"><img src="http://[URL]static/img/automail/logo.png" alt="핫티 로고" /></a></td>
		<td align="right" valign="bottom" style="font-family:tahoma; font-size:11px; color:#505050"><b><?=date("Y.m.d")?></b></td>
	</tr>
	<tr><td colspan="2" height="15"></td></tr>
	<tr><td colspan="2" height="2" bgcolor="#505050"></td></tr>
	<tr height="260">
		<td colspan="2" align="center"><img src="http://[URL]static/img/automail/ment_deposit_ok.jpg" alt="입금 확인 되었습니다. 상품 배송이 준비됩니다." /></td>
	</tr>
</table><!-- //상단 -->

<!-- 내용 -->
<table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
	<tr>
		<td align="left" style="font-size:12px; color:#5e5e5e; font-family:dotum;">
			<div><b><span style="color:#000">[V_NAME]</span> 고객님, 안녕하세요!</b></div>
			<div style="padding-top:5px;line-height:1.2;">
			결제가 정상적으로 완료되었습니다.<br />
			물품확인 후 빠른시일내에 배송해 드리겠습니다.
			</div>
		</td>
	</tr>
	<tr><td height="20"></td></tr>
	<tr>
		<td>

				<table  width="600" cellpadding="0" cellspacing="0" border="0" style="font-size:12px; color:#5e5e5e; font-family:dotum;">
					<caption style="font-size:14px;font-weight:bold; text-align:left; background-color:#fff">주문상세내역</caption>
					<colgroup>
						<col style="width:80px" /><col style="width:280px" /><col style="width:70px" /><col style="width:70px" /><col style="width:100px" />
					</colgroup>
					<tr><td colspan="5" height="2" bgcolor="#505050"></td></tr>
					<tr height="30" style="background-color:#fafafa;">
						<th colspan="2">상품명</th>
						<th>판매가</th>
						<th>수량</th>
						<th>주문금액</th>
					</tr>
					<tr><td colspan="5" height="1" bgcolor="#d5d5d5"></td></tr>
<?
$total_deli_price = 0;
$total_sum_price = 0;
$total_sum_use_reserve = 0;
$total_sum_dc_price = 0;
$product_num=1;
foreach( $orderproduct as $vender => $venderObj ){
	
	$venderCnt = 0; // 벤더별 상품 단위
	# 상품별 배송료, 총합계금액 조회
	$deli_data=pmysql_fetch_object(pmysql_query("select sum(deli_price) as deli_price, sum((price+option_price)*option_quantity) as sum_price, sum(use_point) as sum_use_reserve, sum(coupon_price) as sum_dc_price from tblorderproduct where ordercode='".$venderObj[0]->ordercode."' and vender='".$venderObj[0]->vender."'"));
	
	$total_deli_price      += $deli_data->deli_price;
	$total_sum_price       += $deli_data->sum_price;
	$total_sum_use_reserve += $deli_data->sum_use_reserve;
	$total_sum_dc_price    += $deli_data->sum_dc_price;
	
	foreach( $venderObj as $opKey => $opVal ) {
		$product_img="";
        $product_link = "";
		$venderCss="";
		
		$deli_price=$deli_data->deli_price?$deli_data->deli_price:"무료";
		
		$tmp1 = explode( '@#', $opVal->opt1_name );
		$tmp2 = explode( chr(30), $opVal->opt2_name );
		$tmp_text_subject = explode( '@#', $opVal->text_opt_subject );
		$tmp_text_content = explode( '@#', $opVal->text_opt_content );
		$option_name="";

		#옵션 구하기
		foreach( $tmp1 as $tmpKey=>$tmpVal ){
			if($tmpVal)	$option_name[]=$tmpVal.' / '.$tmp2[$tmpKey];
		}

		#이니셜 구하기
		foreach( $tmp_text_subject as $tmp_subKey=>$tmp_subVal ){
			if($tmp_subVal)	$option_name[]=$tmp_subVal.' / '.$tmp_text_content[$tmp_subKey];
		}

        ## 옵션 추가 금액 구하기
        if($opVal->option_price) {
            $option_name[] = " 추가금액 : ".$opVal->option_price;
        }

		# 이미지 경로 체크
		$img_check=stripos($opVal->tinyimage, "ttp:");

		if(!empty($img_check)){
			$product_img=$opVal->tinyimage;
		}else{
			$product_img="http://".$shopurl."/data/shopimages/product/".$opVal->tinyimage;
		}
    
        $product_link = "http://".$shopurl."/front/productdetail.php?productcode=".$opVal->productcode;

		if(count( $venderObj ) > 1 && count( $venderObj ) > ($venderCnt+1)) $venderCss="border-bottom:1px solid #cccccc";
		
?>

					<tr>
						<td><a href="<?=$product_link?>"><img src="<?=$product_img?>" alt="가로 세로 60px 사이즈 이미지" width="94" height="52" style="width:94px; height:52px; margin:5px; "/></a></td>
						<td align="left">
							<a href="<?=$product_link?>" style="color:#5e5e5e;text-decoration:none;">
							<?=$opVal->productname?> <br />
							<span style="font-size:11px; color:#a0a0a0"><?=implode(" / ", $option_name)?></span>
							</a>
						</td>
						<td align="center"><?=number_format($opVal->price+$opVal->option_price)?>원</td>
						<td align="center"><?=$opVal->quantity?></td>
						<td align="center"><b style="color:#ca3030"><?=number_format($deli_data->sum_price)?></b>원</td>
					</tr>
<?
	    $venderCnt++;
    } // $venderObj foreach ( 장바구니 단위 상품단위 묶음 )

	if(count( $orderproduct ) > $product_num){
?>
					<!-- 벤더라인 -->
					<tr><td colspan="5" height="1" bgcolor="#d5d5d5"></td></tr>
					<!-- 벤더라인 -->
<?		
    }
    $product_num++;
} // $orderproduct foreach ( 벤더  단위 )
?>

					<tr><td colspan="5" height="1" bgcolor="#d5d5d5"></td></tr>
				</table>
		</td>
	</tr>


	<tr><td height="20"></td></tr>
	<tr>
		<td>

				<table  width="600" cellpadding="0" cellspacing="0" border="0" style="font-size:12px; color:#5e5e5e; font-family:dotum;">
					<colgroup>
						<col style="width:auto"><col style="width:50px">
						<col style="width:auto"><col style="width:50px">
						<col style="width:auto"><col style="width:50px">
						<col style="width:auto">
					</colgroup>
					<tr height="55">
						<td align="center">
							<div style="font-size:12px; color:#838383;font-family:dotum; text-align:center;">상품금액</div>
							<strong style="display:block; font-size:12px; color:#838383"><?=number_format($total_sum_price)?></strong>
						</td>
						<td style="text-align:center"><img src="http://[URL]static/img/automail/icon_plus.gif" alt="더하기"></td>
						<td align="center">
							<div style="font-size:12px; color:#838383;font-family:dotum; text-align:center;">배송비</div>
							<strong style="display:block; font-size:12px; color:#838383"><?=number_format($total_deli_price)?></strong>
						</td>
						<td style="text-align:center"><img src="http://[URL]static/img/automail/icon_minus.gif" alt="빼기"></td>
						<td align="center">
							<div style="font-size:12px; color:#838383;font-family:dotum; text-align:center;">쿠폰할인</div>
							<strong style="display:block; font-size:12px; color:#838383"><?=number_format($total_sum_dc_price)?></strong>
						</td>
						<td style="text-align:center"><img src="http://[URL]static/img/automail/icon_equl.gif" alt="합계"></td>
						<td align="center">
							<div style="font-size:12px; color:#4b4b4b;font-family:dotum; text-align:center;">결제 금액</div>
							<strong style="display:block; font-size:12px; color:#4b4b4b">
								<?=number_format($total_sum_price+$total_deli_price-$total_sum_dc_price)?>
							</strong>
						</td>
					</tr>
					<tr><td colspan="9" height="1" bgcolor="#d5d5d5"></td></tr>
				</table>
		</td>
	</tr>


	<tr><td height="20"></td></tr>
	<tr>
		<td>

				<table  width="600" cellpadding="0" cellspacing="0" border="0" style="font-size:12px; color:#5e5e5e; font-family:dotum;">
					<caption style="font-size:14px;font-weight:bold; text-align:left; background-color:#fff">주문자 정보</caption>
					<colgroup>
						<col style="width:130px" /><col style="width:auto" />
					</colgroup>
					<tr><td colspan="2" height="2" bgcolor="#505050"></td></tr>
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>주문번호</b></td>
						<td style="text-indent:10px; color:#ca3030; font-weight:bold">[ORDERCODE]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>주문일자</b></td>
						<td style="text-indent:10px">[ORDERDATE]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>이름</b></td>
						<td style="text-indent:10px">[NAME]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<!-- <tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>전화번호</b></td>
						<td style="text-indent:10px">031-141-3214</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr> -->
					<!-- <tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>이메일</b></td>
						<td style="text-indent:10px">hong123@naver.com</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr> -->
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>결제방법</b></td>
						<td style="text-indent:10px">[PAYTYPE]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<!-- <tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>잔여 적립금</b></td>
						<td style="text-indent:10px">2,300</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr> -->
				</table>

		</td>
	</tr>
	<tr><td height="20"></td></tr>
	<tr>
		<td>

				<table  width="600" cellpadding="0" cellspacing="0" border="0" style="font-size:12px; color:#5e5e5e; font-family:dotum;">
					<caption style="font-size:14px;font-weight:bold; text-align:left; background-color:#fff">배송정보</caption>
					<colgroup>
						<col style="width:130px" /><col style="width:auto" />
					</colgroup>
					<tr><td colspan="2" height="2" bgcolor="#505050"></td></tr>
					<!-- <tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>주문일자</b></td>
						<td style="text-indent:10px">2014.07.20</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr> -->
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>이름</b></td>
						<td style="text-indent:10px">[RECEIVERNAME]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>전화번호</b></td>
						<td style="text-indent:10px">[RECEIVERTELL2] / [RECEIVERTELL1]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>주소</b></td>
						<td style="text-indent:10px">[RECEIVERADDR]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<tr height="30"  align="left">
						<td bgcolor="#fafafa" style="text-indent:10px"><b>주문메세지</b></td>
						<td style="text-indent:10px">[ORDMSG]</td>
					</tr>
					<tr><td colspan="2" height="1" bgcolor="#d5d5d5"></td></tr>
					<tr height="130">
						<td colspan="2" align="center">
							<a href="http://<?=$shopurl?>" target="_blank"><img src="http://[URL]static/img/automail/btn_thehook_home.gif" alt="핫티 바로가기" /></a>
						</td>
					</tr>
				</table>

		</td>
	</tr>
</table><!-- //내용 -->

<!-- 푸터 -->
<table width="664" cellpadding="0" cellspacing="0" border="0" bgcolor="#f7f7f7" align="center" style="margin-bottom:10px;border:1px solid #e3e3e3;font-size:12px;color:#5e5e5e;font-family:dotum;">
	<tr>
		<td align="center">
			<table width="627" cellpadding="0" cellspacing="0" border="0" align="center" style="color:#777;font-family:dotum;font-size:11px;line-height:16px;">
				<tr>
					<td colspan="2" style="padding:15px 18px;color:#777;font-family:dotum;font-size:11px;line-height:16px;">본 메일은 회원님께서 수신 가능 메일주소로 설정하신 e-mail 주소로 발송된 것으로 발신전용입니다.<br>관련 문의는 <span style="color:#000;font-weight:700;text-decoration:underline;">핫티 고객센터(1544-9556)</span>를 이용해주시기 바랍니다.</td>
				</tr>
				<tr><td colspan="2" height="1" bgcolor="#d7d7d7"></td></tr>
				<tr>
					<td align="center" style="padding:35px 18px;vertical-align:top;"><img src="http://[URL]static/img/automail/logo_btm.png" alt="the hook"></td>
					<td style="padding:20px 18px;color:#777;font-family:dotum;font-size:11px;line-height:16px;">HOT-T /사업자등록번호 : 105-86-14706 / 통신판매업신고 : 제2009-서울강남-00623호<br>
					개인정보관리 및 청소년보호책임자 : 안명환 / 대표전화 : 1544-9556<br>
					주소 : 서울특별시 강남구 테헤란로 306 (역삼동) 카이트타워 7층 <br>
					<p style="margin-top:10px;text-transform:uppercase;">COPYRIGHT 2016 HOT-T. ALL RIGHTS RESERVED.</p></td>
				</tr>
			</table>
		</td>
	</tr>
</table><!-- //푸터 -->
		</td>
	</tr>
</table>
</div>


<map name="Map" id="Map">
  <area shape="rect" coords="53,87,112,160" href="#" target="_blank" />
</map>
</body>
</html>