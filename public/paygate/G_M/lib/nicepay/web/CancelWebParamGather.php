<?php

/**
 * 
 * @author kblee
 *
 */
class CancelWebParamGather implements WebParamGather {
	
	/**
	 * 
	 */
	public function CancelWebParamGather(){
		
	}
	
	
	/**
	 * 
	 * @param $request
	 */
	public function gather($request){
		
		$webParam = new WebMessageDTO();
		
		$tid = $request["TID"];
		
		$svcCd = "";
		
		if(strlen($tid)>=30){
			$svcCd = substr($tid,10, 2);
		}
		$payMethod = "";
		if(SVC_CD_CARD == $svcCd){
			$payMethod = CARD_PAY_METHOD;
		}else if(SVC_CD_BANK == $svcCd){
			$payMethod = BANK_PAY_METHOD;
		}else if(SVC_CD_CELLPHONE == $svcCd){
			$payMethod = CELLPHONE_PAY_METHOD;
		}else if(SVC_CD_RECEIPT == $svcCd){
			$payMethod = CASHRCPT_PAY_METHOD;
		}else if(SVC_CD_VBANK == $svcCd){
			$payMethod = VBANK_PAY_METHOD;
		}

		$webParam->setParameter(PAY_METHOD, $payMethod);
		
		$cancelAmt = $request["CancelAmt"];
		$webParam->setParameter(CANCEL_AMT, $cancelAmt);
		
		$cancelPwd = $request["CancelPwd"];
		$webParam->setParameter(CANCEL_PWD, $cancelPwd);
		
		$cancelMsg = $request["CancelMsg"];
		$webParam->setParameter(CANCEL_MSG, $cancelMsg);
		
		$cancelIP = $request["CancelIP"];
		$webParam->setParameter(CANCEL_IP, $cancelIP);
		
		$partialCancelCode = $request["PartialCancelCode"];
		$webParam->setParameter(PARTIAL_CANCEL_CODE,$partialCancelCode);

		$ServiceAmt = $request["ServiceAmt"] == null ? "0" : $request["ServiceAmt"];
		$webParam->setParameter("ServiceAmt",$ServiceAmt);
		
		$GoodsVat = $request["GoodsVat"] == null ? "0" : $request["GoodsVat"];
		$webParam->setParameter("GoodsVat",$GoodsVat);
		
		$SupplyAmt = $request["SupplyAmt"] == null ? "0" : $request["SupplyAmt"];
		$webParam->setParameter("SupplyAmt",$SupplyAmt);
		
		$TaxFreeAmt = $request["TaxFreeAmt"] == null ? "0" : $request["TaxFreeAmt"];
		$webParam->setParameter("TaxFreeAmt",$TaxFreeAmt);
		
		return $webParam;
	}
	
}
?>
