<?php
require_once dirname(__FILE__).'/../util/KeyUtils.php';
require_once dirname(__FILE__).'/Constants.php';
/**
 * 
 * @author kblee
 *
 */
class HeaderValueSetter {
	
	/**
	 * 
	 */
	function HeaderValueSetter(){
		
	}
	
	/**
	 * 
	 * @param  $paramSet
	 */
	public function fillValue($paramSet){
		
		
		// 전문생성일시
		$paramSet->setParameter(EDIT_DATE, date("YmdHis"));
		
		// 전문길이
		$paramSet->setParameter(LENGTH, "0");
		
		// 거래ID (결제서비스일 경우만 설정, 취소서비스일 경우 JSP에서 설정)
		if(PAY_SERVICE_CODE == $paramSet->getParameter(SERVICE_MODE)){
			$payMethod = $paramSet->getParameter(PAY_METHOD);
			if($payMethod !== BANK_PAY_METHOD &&  $payMethod !== CELLPHONE_PAY_METHOD){
				$paramSet->setParameter(TID,$this->generateNewTid($paramSet));
			}
		}

		if(LogMode::isAppLogable()){
			$logJournal = NicePayLogJournal::getInstance();
			$logJournal->writeAppLog("TID : ".$paramSet->getParameter(TID));
		}

		// 에러시스템명
		$paramSet->setParameter(ERROR_SYSTEM, "MALL");
		
		// 에러코드
		$paramSet->setParameter(ERROR_CODE, "00000");
		
		// 에러메시지
		$paramSet->setParameter(ERROR_MSG, "");
	
		return $paramSet;
	}
	
	
	/**
	 * 
	 * @param  $paramSet
	 */
	private function generateNewTid($paramSet){
		$mid = $paramSet->getParameter(MID);
		$payMethod = $paramSet->getParameter(PAY_METHOD);
		$svcCd = "";

		if(CARD_PAY_METHOD == $payMethod){
			$svcCd = SVC_CD_CARD;
		}else if(BANK_PAY_METHOD == $payMethod){
			$svcCd = SVC_CD_BANK;
		}else if(VBANK_PAY_METHOD == $payMethod){
			$svcCd = SVC_CD_VBANK;
		}else if(CELLPHONE_PAY_METHOD == $payMethod){
			$svcCd = SVC_CD_CELLPHONE;
		}else if(CPBILL_PAY_METHOD == $payMethod){
			$svcCd = SVC_CD_CPBILL;
		}else if(VBANK_BULK_PAY_METHOD == $payMethod){
			$svcCd = SVC_CD_VBANK;
		}else if(CASHRCPT_PAY_METHOD == $payMethod){
			$svcCd = SVC_CD_RECEIPT;
		}else{
			throw new ServiceException("V005","지원하지 않는 지불수단입니다.");
		}
		
		
		return KeyUtils::genTID($mid, $svcCd, SVC_PRDT_CD_ONLINE);
	}
	
}
?>
