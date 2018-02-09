<?
//0901 원재 ㅠㅠ 집에 가고 싶다
##############function###############
function get_smsinfo() //sms기본 정보 가져오기
{
	$sql = " select id,authkey,return_tel from tblsmsinfo limit 1 ";
	$result = pmysql_query($sql);
	$info = pmysql_fetch_object($result);
	return $info;
}

function in_couponlog($id,$couponcode,$msg,$type,$etc_msg) //쿠폰 로그 남기긔
{
	$now_date = date("Y-m-d h:i:s");
	$e_sql = " insert into tblsignage_couponlog (
				couponcode,
				id, 
				msg, 
				type, 
				date,
				etc_msg
			) ";
	$e_sql .= " values( 
		'{$couponcode}',
		'{$id}',
		'{$msg}',
		'{$type}',
		'{$now_date}',
		'{$etc_msg}'
	)";
	pmysql_query($e_sql);
}

function signage_coupon_mms($id)
{
	//정상적으로 가입된 아이디 인지 확인
	$mem_sql = " select id, name, mobile from tblmember ";
	$mem_sql .= " where id ='{$id}' ";
	$mem_result = pmysql_query($mem_sql);
	$mem_info = pmysql_fetch_object($mem_result);

	if($mem_info->id){//가입된 아이디가 맞을경우 쿠폰 조회 및 발급
		$cou_sql = "select min(couponcode) as coupon from tblsignage_couponlist ";
		$cou_sql .= " where id is null AND use_yn = 'N' "; //발급되지 않은 쿠폰중 가장 앞선 번호의 쿠폰번호를 가져옵니다
		$cou_result = pmysql_query($cou_sql);
		$couponcode = pmysql_fetch_object($cou_result)->coupon;

		$now_date = date("Y-m-d h:i:s");
		$chk_coupon = true;

		$cou_sql2 = " update tblsignage_couponlist set ";
		$cou_sql2 .= " id = '{$mem_info->id}' , ";
		$cou_sql2 .= " publish_date = '{$now_date}' ";
		$cou_sql2 .= " where couponcode = '{$couponcode}' AND id is null ";

		try {

			BeginTrans();

			pmysql_query($cou_sql2);

			if( pmysql_error() ){
				$chk_coupon = false;
				throw new Exception( "쿠폰발급 실패" );
				break;
			}
		
		} catch( Exception $e ) {
			RollbackTrans();
			$msg = $e->getMessage();
			in_couponlog($mem_info->id, $couponcode, $msg, $type="F"); //쿠폰 발급 실패시 로그를 남겨줍니다.
		}

		if( $chk_coupon ){ //쿠폰 정상 발급시 commit 및 sms 발송 합니다 ㅠㅠ

			CommitTrans();
			
			$sms_info = get_smsinfo();
			$sms_id = $sms_info->id;
			$sms_authkey = $sms_info->authkey;
			$sms_from = $sms_info->return_tel;
			//$sms_msg = " 반갑습니다 핫티입니다! 가입 10% 할인쿠폰[{$couponcode}] 매장 카운터 제시해주세요.";
			$sms_msg = " 반갑습니다. 고객님~ 핫티 회원이 되신 것을 진심으로 환영합니다.신규가입 회원님들께 10% 할인쿠폰을 선물로 드립니다.

			할인쿠폰번호 : [{$couponcode}]
			사용방법: 핫티 매장에 방문하시고 상품 구매 시 카운터에 제시해주세요.

			핫티  매장 안내
			명동점, 강남점, 홍대점, 여의도IFC몰, 일산웨스턴돔점,안양점, 인천구월점대구동성로점, 울산성남점,부산대학로점, 부산서면점, 대전은행점,광주충장로점
			
			수신동의를 하셔야 이벤트 당첨내역을 받아보실 수 있습니다.";
			$mobile = $mem_info->mobile;
			$return_msg = SendMMS($sms_id, $sms_authkey, $mobile, '', $sms_from, $sms_date, $sms_msg, $sms_etcmsg,$_FILES, $subject);
			$msg = " 쿠폰발급 성공";
			in_couponlog($mem_info->id, $couponcode, $msg, $type="S",$return_msg['msg']); //쿠폰 발급 성공시 로그 남겨줍니다.
		}
	}
}
##############//function###############
?>