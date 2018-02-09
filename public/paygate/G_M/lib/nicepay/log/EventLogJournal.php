<?php
require_once dirname(__FILE__).'/../core/Constants.php';
/**
 * 
 * @author kblee
 *
 */
class EventLogJournal{
	
	/**
	 * 
	 * @var $instance
	 */
	private static $instance;
	
	/** The event log path. */
	private $eventLogPath;
	
	
	/** The event logger. */
	private $eventLogger;
	
	/**
	 * 
	 */
	private function EventLogJournal(){
		
	}
	
	/**
	 * 
	 */
	public static function getInstance(){
		if(!isset(EventLogJournal::$instance)){
			EventLogJournal::$instance = new EventLogJournal();
		}
		return EventLogJournal::$instance;
	}
	
	/**
	 * 
	 * @param  $eventLogPath
	 */
	public function setLogDirectoryPath($eventLogPath){
		$this->eventLogPath = $eventLogPath;
	}
	
	/**
	 * 
	 */
	public function configureEventLog4J(){
		if(!isset($this->eventLogger)){
			try {
				$currentPath = dirname(__FILE__);
				$currentPathXml = $currentPath.'/event_log4php.xml';
					
				$doc = new DOMDocument();
				
				if($doc->load($currentPathXml)){
					$xpath = new DOMXPath($doc);
					$nodeList = $xpath->query("/log4php:configuration/appender[@name='eventJournal']/param[@name='File']");
					$fileParamNode = $nodeList->item(0);

					$fileParamNode->setAttribute("value",$this->eventLogPath."/event_%s.log");

					//$doc->save($currentPathXml);


					Logger::configure($currentPathXml);
				
				$this->eventLogger = Logger::getLogger("EventJournal");
					
				}else{
					echo "Event Logger Configuration Load Fail..";
				}
				
			} catch (Exception $e) {
				echo "Exception  : Event Logger Configuration Loading";
			}
			
		}
		
	}
	
	/**
	 * Write journal.
	 * 
	 * @param dto the dto
	 */
	public function writeLog($dto){
		
		$serviceMode  = $dto->getParameter(SERVICE_MODE);
		$logString = "";
		
		//StringBuffer logBuffer = new StringBuffer(); 
		$resultCode = $dto->getParameter("ResultCode");
		$reqDate = date("Ymd");
		$reqTime = date("His");
		
		echo $serviceMode;		
		if(PAY_SERVICE_CODE == $serviceMode){ //결제승인
			/**
			 *  승인
	         *  P|결제성공여부|결제요청일|결제요청시간|결제수단|결제상품명|결제금액|USER_ID|응답코드|응답메시지
			 */
			$logString.="P|";
			if( ("3001" == $resultCode) || ("4000" == $resultCode)){ //결제성공
				$logString.="TE|";
			}else{ //결제실패
				$logString.="TF|";
			}
			$logString.=$reqDate."|";
			$logString.=$reqTime."|";
			$logString.= ($dto->getParameter(PAY_METHOD)==null?"":trim($dto->getParameter(PAY_METHOD)))."|";
			$logString.= ($dto->getParameter(GOODS_NAME)==null?"":trim($dto->getParameter(GOODS_NAME)))."|";
			$logString.= ($dto->getParameter(GOODS_AMT)==null?"0":trim($dto->getParameter(GOODS_AMT)))."|";
			$logString.= ($dto->getParameter(MALL_USER_ID)==null?"":trim($dto->getParameter(MALL_USER_ID)))."|";
			$logString.=trim($resultCode)."|";
			$logString.=($dto->getParameter("ResultMsg")==null?"":trim($dto->getParameter("ResultMsg")));
		}else if(CANCEL_SERVICE_CODE == $serviceMode){                                                          // 결제취소
			/**
			 *  취소
	         *  C|취소성공여부|취소요청일|취소요청시간|결제수단|취소금액|USER_ID|응답코드|응답메시지
			 */
			$logString.="C|";
			if(("2001" == $resultCode) || ("2005" == $resultCode)){ //취소성공
				$logString.="TE|";
			}else{ //결제실패
				$logString.="TF|";
			}
			$logString.=$reqDate."|";
			$logString.=$reqTime."|";
			
			$logString.= ($dto->getParameter(PAY_METHOD)==null?"":trim($dto->getParameter(PAY_METHOD)))."|";
			$logString.= ($dto->getParameter(GOODS_NAME)==null?"":trim($dto->getParameter(GOODS_NAME)))."|";
			$logString.= ($dto->getParameter(GOODS_AMT)==null?"0":trim($dto->getParameter(GOODS_AMT)))."|";
			$logString.= ($dto->getParameter(MALL_USER_ID)==null?"":trim($dto->getParameter(MALL_USER_ID)))."|";
			$logString.=trim($resultCode)."|";
			$logString.=($dto->getParameter("ResultMsg")==null?"":trim($dto->getParameter("ResultMsg")));
		}else if(ESCROW_SERVICE_CODE == $serviceMode){
			$logString.="E|";
			if("0000" == $resultCode){
				$logString.="TE|";
			}else{
				$logString.="TF|";
			}
			$logString.=$reqDate."|";
			$logString.=$reqTime."|";
			$logString.= ($dto->getParameter(PAY_METHOD)==null?"":trim($dto->getParameter(PAY_METHOD)))."|";
			$logString.="|";
			$logString.="|";
			$logString.="|";
			$logString.=$resultCode."|";
			$logString.=($dto->getParameter("ResultMsg")==null?"":trim($dto->getParameter("ResultMsg")));
		}
		
		if($logString!=null && $logString!=""){
			$this->eventLogger->debug($logString);
		}
	}
	
	
}
?>
