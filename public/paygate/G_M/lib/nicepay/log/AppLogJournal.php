<?php

require_once dirname(__FILE__).'/../../log4php/Logger.php';

/**
 * 
 * @author kblee
 *
 */
class AppLogJournal{
	
	/**
	 * 
	 * @var $instance
	 */
	private static $instance;
	
	/**
	 * 
	 * @var $appLogPath
	 */
	private $appLogPath;
	
	/**
	 * 
	 * @var $logger
	 */
	private $logger;
	
	/**
	 * 
	 */
	private function AppLogJournal(){
		
	}
	
	/**
	 * 
	 */
	public static function getInstance(){
		if(!isset(AppLogJournal::$instance)){
			AppLogJournal::$instance = new AppLogJournal();
		}
		return AppLogJournal::$instance;
	}
	
	/**
	 * 
	 */
	public function initialize(){
		if(!isset(AppLogJournal::$instance)){
			configure();
		}
	}
	
	/**
	 * 
	 */
	public function configure(){
		
		try{
			$currentPath = dirname(__FILE__);
			$currentPathXml = $currentPath.'/./app_log4php.xml';
			
			$doc = new DOMDocument();
			
			
			if($doc->load($currentPathXml)){
				$xpath = new DOMXPath($doc);
				$nodeList = $xpath->query("/log4php:configuration/appender[@name='NICEPAY_FILE']/param[@name='File']");
				$fileParamNode = $nodeList->item(0);
				
				$fileParamNode->setAttribute("value",$this->appLogPath."/application_%s.log");
				
				//$doc->save($currentPathXml);
				
				
				Logger::configure($currentPathXml);
				
				$this->logger = Logger::getLogger("AppJournal");
				
				
			}else{
				echo "Application Logger Configuration Load Fail..";
			}
			
			
			
		}catch(Exception $e){
			echo "Exception  : Application Logger Configuration Loading";
		}
		
	}

	/**
	 * 
	 * @param  $logPath
	 */
	public function setLogDirectoryPath($logPath) {
		$this->appLogPath = $logPath;
	}

	/**
	 * 
	 * @param  $string
	 */
	public function writeLog($string) {
		$this->logger->debug($string);
	}
	
	/**
	 * 
	 * @param  $string
	 */
	public function errorLog($string){
		$this->logger->error($string);
	}
	
	/**
	 * 
	 * @param  $string
	 */
	public function warnLog($string){
		$this->logger->warn($string);
	}
	
	
}
?>
