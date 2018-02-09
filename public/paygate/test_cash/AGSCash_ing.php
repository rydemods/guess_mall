<?php

/****************************************************************************
*
* $IsDebug : 1:수신,전송 메세지 Print 0:사용안함
* $LOCALADDR : PG서버와 통신을 담당하는 암호화Process가 위치해 있는 IP (220.85.12.74)
* $LOCALPORT : 포트
* $ENCRYPT : "C" 현금영수증
* $CONN_TIMEOUT : 암호화 데몬과 접속 Connect타임아웃 시간(초)
* $READ_TIMEOUT : 데이터 수신 타임아웃 시간(초)
* 
****************************************************************************/

$IsDebug = 0;
$LOCALADDR = "220.85.12.74";
$LOCALPORT = "29760";
$ENCTYPE = 0;
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;

/****************************************************************************
*
* AGSCash.html 로 부터 넘겨받을 데이타
*
****************************************************************************/

$Retailer_id = trim($_POST["Retailer_id"]);			//상점아이디

$Ord_No = trim($_POST["Order_no"]);					//주문번호

$Amtcash = trim($_POST["Amtcash"]);					//거래금액

$Cust_no = trim($_POST["Cust_no"]);					//회원아이디

$Cat_id = trim($_POST["Cat_id"]);					//단말기번호

$Amttex = trim($_POST["Amttex"]);				    //부가가치세

$Amtadd = trim($_POST["Amtadd"]);				    //봉사료

$prod_nm = trim($_POST["prod_nm"]);				    //상품명

$prod_set = trim($_POST["prod_set"]);				//상품갯수

$deal_won = trim($_POST["deal_won"]);	            //공급가액

$Gubun_cd = trim($_POST["Gubun_cd"]);				//거래자구분

$Confirm_no = trim($_POST["Confirm_no"]);			//신분확인번호

$Pay_kind = trim($_POST["Pay_kind"]);				//결제종류

$Pay_type = trim($_POST["Pay_type"]);	            //결제방식 1.무통장임급

$Org_adm_no = trim($_POST["Org_adm_no"]);	        //취소시 승인번호

$Email = trim($_POST["Email"]);						//이메일주소
			
$Corp_no = trim($_POST["Corp_no"]);					//사업자번호

$Corp_nm = trim($_POST["Corp_nm"]);					//상점명

$Url = trim($_POST["Url"]);							//URL

$Ceo_nm = trim($_POST["Ceo_nm"]);					//대표자명

$Addr = trim($_POST["Addr"]);						//주소

$Tel_no = trim($_POST["Tel_no"]);					//연락처


	 /*******************************************************************************************
     * 
     * Pay_kind = cash-appr" 현금영수증 승인요청시 
	 *
     ******************************************************************************************/
    	
		if( strcmp( $Pay_kind, "cash-appr" ) == 0 )   
		{

		  /**************************************************************
           * 승인요청시
           **************************************************************/

			$ENCTYPE = "C";

			/****************************************************************************
			* 
			* 전송 전문 Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				$Pay_kind."|".
				$Pay_type."|".
				$Retailer_id."|".
				$Cust_no."|".
				$Ord_No."|".
				$Cat_id."|".
				$Amtcash."|".
				$Amttex."|".
				$Amtadd."|".
				$Gubun_cd."|".
				$Confirm_no."|".
				$Email."|".
				$prod_nm."|";
		
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
	
			/****************************************************************************
			* 
			* 전송 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	    
			/****************************************************************************
			* 
			* 암호화Process와 연결을 하고 승인 데이터 송수신
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT ); // &$errno, &$errstr 오류발생
					
			if( !$fp )
			{
				/** 연결 실패로 인한 승인실패 메세지 전송 **/
				
				$Success = "n";
				$rResMsg = "연결 실패로 인한 실패";
			}
			else 
			{
				/** 연결에 성공하였으므로 데이터를 받는다. **/
				
				$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
								
				/** 승인 전문을 암호화Process로 전송 **/
				
				fputs( $fp, $sSendMsg );
				
				socket_set_timeout($fp, $READ_TIMEOUT);
				
				/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
				/****************************************************************************
				* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
				* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
				* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
				* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
		
				/** 소켓 close **/
				
				fclose( $fp );
			}
			
			/****************************************************************************
			* 
			* 수신 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** 수신 데이터(길이) 체크 정상 **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
						
				$rRetailer_id = $RecvValArray[0];
				$rDealno = $RecvValArray[1];
				$rAdm_no = $RecvValArray[2];
				$rSuccess = $RecvValArray[3];
				$rResMsg = $RecvValArray[4];
				$rAlert_msg1 = $RecvValArray[5];
				$rAlert_msg2 = $RecvValArray[6];
								
			}
			else
			{
				/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
				
				$Success = "n";
				$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
				
			}
		}
	
     /*******************************************************************************************
     * 
     * Pay_kind = "cash-cncl" 현금영수증 취소요청시 
	 *
     ******************************************************************************************/
		
		if( strcmp( $Pay_kind, "cash-cncl" ) == 0 )   
		{
		  /**************************************************************
          * 취소요청시
          **************************************************************/

			$ENCTYPE = "C";

			/****************************************************************************
			* 
			* 전송 전문 Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				$Pay_kind."|".
				$Pay_type."|".
				$Retailer_id."|".
				$Cust_no."|".
				$Ord_No."|".
				$Cat_id."|".
				$Amtcash."|".
				$Amttex."|".
				$Amtadd."|".
				$Gubun_cd."|".
				$Confirm_no."|".
				$Org_adm_no."|".
				$Email."|".
				$prod_nm."|";

				
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
	
			/****************************************************************************
			* 
			* 전송 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	    
			/****************************************************************************
			* 
			* 암호화Process와 연결을 하고 승인 데이터 송수신
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT ); // &$errno, &$errstr 오류발생
				
			if( !$fp )
			{
				/** 연결 실패로 인한 승인실패 메세지 전송 **/
				
				$Success = "n";
				$rResMsg = "연결 실패로 인한 실패";
			}
			else 
			{
				/** 연결에 성공하였으므로 데이터를 받는다. **/
				
				$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
								
				/** 승인 전문을 암호화Process로 전송 **/
				
				fputs( $fp, $sSendMsg );
				
				socket_set_timeout($fp, $READ_TIMEOUT);
				
				/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
				/****************************************************************************
				* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
				* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
				* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
				* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
		
				/** 소켓 close **/
				
				fclose( $fp );
			}
			
			/****************************************************************************
			* 
			* 수신 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** 수신 데이터(길이) 체크 정상 **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
						
				$rRetailer_id = $RecvValArray[0];
				$rDealno = $RecvValArray[1];
				$rAdm_no = $RecvValArray[2];
				$rSuccess = $RecvValArray[3];
				$rResMsg = $RecvValArray[4];
				$rAlert_msg1 = $RecvValArray[5];
				$rAlert_msg2 = $RecvValArray[6];
						
			}
			else
			{
				/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
				
				$Success = "n";
				$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
				
			}
		}

	 /*******************************************************************************************
     * 
     * Pay_kind = cash-appr-temp" 현금영수증 임시승인저장요청시 
	 *
     ******************************************************************************************/
    	
		if( strcmp( $Pay_kind, "cash-appr-temp" ) == 0 )   
		{

		  /**************************************************************
           * 승인요청시
           **************************************************************/

			$ENCTYPE = "C";

			/****************************************************************************
			* 
			* 전송 전문 Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				$Pay_kind."|".
				$Pay_type."|".
				$Retailer_id."|".
				$Cust_no."|".
				$Ord_No."|".
				$Cat_id."|".
				$Amtcash."|".
				$Amttex."|".
				$Amtadd."|".
				$Gubun_cd."|".
				$Confirm_no."|".
				$Email."|".
				$prod_nm."|";

		
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
	
			/****************************************************************************
			* 
			* 전송 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	    
			/****************************************************************************
			* 
			* 암호화Process와 연결을 하고 승인 데이터 송수신
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT ); // &$errno, &$errstr 오류발생
					
			if( !$fp )
			{
				/** 연결 실패로 인한 승인실패 메세지 전송 **/
				
				$Success = "n";
				$rResMsg = "연결 실패로 인한 실패";
			}
			else 
			{
				/** 연결에 성공하였으므로 데이터를 받는다. **/
				
				$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
								
				/** 승인 전문을 암호화Process로 전송 **/
				
				fputs( $fp, $sSendMsg );
				
				socket_set_timeout($fp, $READ_TIMEOUT);
				
				/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
				/****************************************************************************
				* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
				* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
				* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
				* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
		
				/** 소켓 close **/
				
				fclose( $fp );
			}
			
			/****************************************************************************
			* 
			* 수신 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** 수신 데이터(길이) 체크 정상 **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
						
				$rRetailer_id = $RecvValArray[0];
				$rDealno = $RecvValArray[1];
				$rSuccess = $RecvValArray[2];
				$rResMsg = $RecvValArray[3];
								
			}
			else
			{
				/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
				
				$Success = "n";
				$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
				
			}
		}
	
     /*******************************************************************************************
     * 
     * Pay_kind = "cash-cncl-temp" 현금영수증 취소요청시 
	 *
     ******************************************************************************************/
		
		if( strcmp( $Pay_kind, "cash-cncl-temp" ) == 0 )   
		{
		  /**************************************************************
          * 취소요청시
          **************************************************************/

			$ENCTYPE = "C";

			/****************************************************************************
			* 
			* 전송 전문 Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				$Pay_kind."|".
				$Pay_type."|".
				$Retailer_id."|".
				$Cust_no."|".
				$Ord_No."|".
				$Cat_id."|".
				$Amtcash."|".
				$Amttex."|".
				$Amtadd."|".
				$Gubun_cd."|".
				$Confirm_no."|".
				$Org_adm_no."|".
				$Email."|".
				$prod_nm."|";

				
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
	
			/****************************************************************************
			* 
			* 전송 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	    
			/****************************************************************************
			* 
			* 암호화Process와 연결을 하고 승인 데이터 송수신
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT ); // &$errno, &$errstr 오류발생
				
			if( !$fp )
			{
				/** 연결 실패로 인한 승인실패 메세지 전송 **/
				
				$Success = "n";
				$rResMsg = "연결 실패로 인한 실패";
			}
			else 
			{
				/** 연결에 성공하였으므로 데이터를 받는다. **/
				
				$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
								
				/** 승인 전문을 암호화Process로 전송 **/
				
				fputs( $fp, $sSendMsg );
				
				socket_set_timeout($fp, $READ_TIMEOUT);
				
				/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
				/****************************************************************************
				* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
				* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
				* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
				* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
		
				/** 소켓 close **/
				
				fclose( $fp );
			}
			
			/****************************************************************************
			* 
			* 수신 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** 수신 데이터(길이) 체크 정상 **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
						
				$rRetailer_id = $RecvValArray[0];
				$rDealno = $RecvValArray[1];
				$rSuccess = $RecvValArray[2];
				$rResMsg = $RecvValArray[3];
						
			}
			else
			{
				/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
				
				$Success = "n";
				$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
				
			}
		}


	 /*******************************************************************************************
     * 
     * Pay_kind = non-cash-appr" 미등록 상점 현금영수증 승인요청시 
	 *
     ******************************************************************************************/
    	
		if( strcmp( $Pay_kind, "non-cash-appr" ) == 0 )   
		{

		  /**************************************************************
           * 승인요청시
           **************************************************************/

			$ENCTYPE = "C";

			/****************************************************************************
			* 
			* 전송 전문 Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				$Pay_kind."|".
				$Pay_type."|".
				$Retailer_id."|".
				$Cust_no."|".
				$Ord_No."|".
				$Cat_id."|".
				$Amtcash."|".
				$Amttex."|".
				$Amtadd."|".
				$Gubun_cd."|".
				$Confirm_no."|".
				$Email."|".
				$prod_nm."|".
				$Corp_no."|".
				$Corp_nm."|".
				$Url."|".
				$Ceo_nm."|".
				$Addr."|".
				$Tel_no."|";
		
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
	
			/****************************************************************************
			* 
			* 전송 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	    
			/****************************************************************************
			* 
			* 암호화Process와 연결을 하고 승인 데이터 송수신
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT ); // &$errno, &$errstr 오류발생
					
			if( !$fp )
			{
				/** 연결 실패로 인한 승인실패 메세지 전송 **/
				
				$Success = "n";
				$rResMsg = "연결 실패로 인한 실패";
			}
			else 
			{
				/** 연결에 성공하였으므로 데이터를 받는다. **/
				
				$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
								
				/** 승인 전문을 암호화Process로 전송 **/
				
				fputs( $fp, $sSendMsg );
				
				socket_set_timeout($fp, $READ_TIMEOUT);
				
				/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
				/****************************************************************************
				* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
				* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
				* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
				* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
		
				/** 소켓 close **/
				
				fclose( $fp );
			}
			
			/****************************************************************************
			* 
			* 수신 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** 수신 데이터(길이) 체크 정상 **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
						
				$rRetailer_id = $RecvValArray[0];
				$rDealno = $RecvValArray[1];
				$rAdm_no = $RecvValArray[2];
				$rSuccess = $RecvValArray[3];
				$rResMsg = $RecvValArray[4];
				$rAlert_msg1 = $RecvValArray[5];
				$rAlert_msg2 = $RecvValArray[6];
								
			}
			else
			{
				/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
				
				$Success = "n";
				$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
				
			}
		}

     /*******************************************************************************************
     * 
     * Pay_kind = "cash-cncl" 현금영수증 취소요청시 
	 *
     ******************************************************************************************/
		
		if( strcmp( $Pay_kind, "non-cash-cncl" ) == 0 )   
		{
		  /**************************************************************
          * 취소요청시
          **************************************************************/

			$ENCTYPE = "C";

			/****************************************************************************
			* 
			* 전송 전문 Make
			* 
			****************************************************************************/
			
			$sDataMsg = $ENCTYPE.
				$Pay_kind."|".
				$Pay_type."|".
				$Retailer_id."|".
				$Cust_no."|".
				$Ord_No."|".
				$Cat_id."|".
				$Amtcash."|".
				$Amttex."|".
				$Amtadd."|".
				$Gubun_cd."|".
				$Confirm_no."|".
				$Org_adm_no."|".
				$Email."|".
				$prod_nm."|".
				$Corp_no."|".
				$Corp_nm."|".
				$Url."|".
				$Ceo_nm."|".
				$Addr."|".
				$Tel_no."|";
				
			$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
	
			/****************************************************************************
			* 
			* 전송 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )
			{
				print $sSendMsg."<br>";
			}
	    
			/****************************************************************************
			* 
			* 암호화Process와 연결을 하고 승인 데이터 송수신
			* 
			****************************************************************************/
			
			$fp = fsockopen( $LOCALADDR, $LOCALPORT , $errno, $errstr, $CONN_TIMEOUT ); // &$errno, &$errstr 오류발생
				
			if( !$fp )
			{
				/** 연결 실패로 인한 승인실패 메세지 전송 **/
				
				$Success = "n";
				$rResMsg = "연결 실패로 인한 실패";
			}
			else 
			{
				/** 연결에 성공하였으므로 데이터를 받는다. **/
				
				$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";
								
				/** 승인 전문을 암호화Process로 전송 **/
				
				fputs( $fp, $sSendMsg );
				
				socket_set_timeout($fp, $READ_TIMEOUT);
				
				/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
				
				$sRecvLen = fgets( $fp, 7 );
				$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
			
				/****************************************************************************
				* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
				* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
				* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
				* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
				* $sRecvLen = fgets( $fp, 6 );
				* $sRecvMsg = fgets( $fp, $sRecvLen );
				*
				****************************************************************************/
		
				/** 소켓 close **/
				
				fclose( $fp );
			}
			
			/****************************************************************************
			* 
			* 수신 메세지 프린트
			* 
			****************************************************************************/
			
			if( $IsDebug == 1 )	
			{
				print $sRecvMsg."<br>";
			}
			
			if( strlen( $sRecvMsg ) == $sRecvLen )
			{
				/** 수신 데이터(길이) 체크 정상 **/
				
				$RecvValArray = array();
				$RecvValArray = explode( "|", $sRecvMsg );
						
				$rRetailer_id = $RecvValArray[0];
				$rDealno = $RecvValArray[1];
				$rAdm_no = $RecvValArray[2];
				$rSuccess = $RecvValArray[3];
				$rResMsg = $RecvValArray[4];
				$rAlert_msg1 = $RecvValArray[5];
				$rAlert_msg2 = $RecvValArray[6];
						
			}
			else
			{
				/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
				
				$Success = "n";
				$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
				
			}
		}
?>
<html>
<head>
</head>
<body onload="javascript:cash_pay.submit();">
<form name=cash_pay method=post action=AGSCash_result.php>
<input type=hidden name=Retailer_id value="<?=$rRetailer_id?>">
<input type=hidden name=Ord_No value="<?=$Ord_No?>">
<input type=hidden name=Dealno value="<?=$rDealno?>">
<input type=hidden name=Cust_no value="<?=$Cust_no?>">
<input type=hidden name=Adm_no value="<?=$rAdm_no?>">
<input type=hidden name=Success value="<?=$rSuccess?>">
<input type=hidden name=Alert_msg1 value="<?=$rAlert_msg1?>">
<input type=hidden name=Alert_msg2 value="<?=$rAlert_msg2?>">
<input type=hidden name=deal_won value="<?=$deal_won?>">
<input type=hidden name=Amttex value="<?=$Amttex?>">
<input type=hidden name=rResMsg value="<?=$rResMsg?>">
<input type=hidden name=prod_nm value="<?=$prod_nm?>">
<input type=hidden name=prod_set value="<?=$prod_set?>">
<input type=hidden name=deal_won value="<?=$deal_won?>">
<input type=hidden name=Amtadd value="<?=$Amtadd?>">
<input type=hidden name=Amtcash value="<?=$Amtcash?>">
<input type=hidden name=Gubun_cd value="<?=$Gubun_cd?>">
<input type=hidden name=Pay_kind value="<?=$Pay_kind?>">
<input type=hidden name=Pay_type value="<?=$Pay_type?>">
<input type=hidden name=Confirm_no value="<?=$Confirm_no?>">
<input type=hidden name=Org_adm_no value="<?=$Org_adm_no?>">
<input type=hidden name=Email value="<?=$Email?>">
</body>
</html>
