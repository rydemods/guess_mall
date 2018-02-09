<?php
/**
 * 
 * @author kblee
 *
 */
class IoAdaptorTransport {
	
	/**
	 * 
	 * @var $socket
	 */
	private $socket;
	
	/**
	 * 
	 */
	public function IoAdaptorTransport(){
		
	}
	
	/**
	 * 
	 * @param unknown_type $socket
	 */
	public function setSocket($socket){
		$this->socket = $socket;
	}
	
	/**
	 * 
	 * @param unknown_type $msg
	 */
	public function doTrx($msg) {
		//set_time_limit(CONNECT_TIMEOUT);
		
		if(LogMode::isAppLogable()){
			$logJournal = NicePayLogJournal::getInstance();
		}

		try{
			$address = gethostbyname(NICEPAY_DOMAIN_NAME);
		}catch (Exception $e){
			if(LogMode::isAppLogable()) $logJournal->writeAppLog("PG SERVER NOT FOUND" );
			throw new ServiceException("X001","서버 도메인명이 잘못 설정되었습니다. : "+$e->getMessage());
		}
		$socket = null;
		try{
			$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
			socket_connect($socket, $address, NICEPAY_ADAPTOR_LISTEN_PORT);
		}catch(Exception $e){
			if(LogMode::isAppLogable()) $logJournal->writeAppLog("SERVER CONNECT ERROR" );
			throw new ServiceException("X002","서버로 소켓 연결 중 오류가 발생하였습니다. : "+$e->getMessage());
		}
		
		if(LogMode::isAppLogable()) $logJournal->writeAppLog("SERVER CONNECT OK" );
		

		socket_write($socket,$msg);
		
		$recvMessage = $this->readData($socket);
		
		
		
		socket_close($socket);
		
		return $recvMessage;
	}
	
	/**
	 * 
	 * @param unknown_type $socket
	 */
	private function readData($socket){
		$buffer = array();
		try{
			$data = socket_read($socket,256,PHP_BINARY_READ);
			
			$dataLength = strlen($data);
			
			if($dataLength >= LENGTH_END_POS){
				
				
				$readLengthStr = substr($data,LENGTH_START_POS,LENGTH_MSG_SIZE);
				
				$readLengthStr = $readLengthStr==null?"0":$readLengthStr;
				
				
				$mustReadLength = (int)$readLengthStr;
				
				$buffer = array_merge($buffer,str_split($data));
				
				
				$repeatReadCnt = 0;
				$readCnt = strlen($data);
				$readData = null;
				
				
				
				
				while(($readData = socket_read($socket,1024,PHP_BINARY_READ))!==false){
					$buffer = array_merge($buffer,str_split($readData));
					$repeatReadCount = strlen($readData);
					$readCnt+=$repeatReadCount;
					if($readCnt>=$mustReadLength){
						break;
					}
				}
				
				return implode($buffer);
			}else{
				throw new ServiceException("T002","비정상적인 수신 전문입니다.");
			}
			
		}catch(ServiceException $e){
			throw $e;
		}catch (Exception $e){
			throw new ServiceException("T002","비정상적인 수신 전문입니다.");
		}
		
	}
	
}

?>
