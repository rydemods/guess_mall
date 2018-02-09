<?		
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include_once($Dir."lib/shopdata.php");

	//exdebug("hello");
	$page = $_GET['part'];
	if($page){
		$data = file_get_contents('http://www.saveheels.com/shop/member/tran_member.php?part='.$page);
	}else{
		$data = file_get_contents('http://www.saveheels.com/shop/member/tran_member.php');
	}

	$arrData = explode("<br>", $data);
	$cnt = 0;
	foreach($arrData as $k => $v){
		$param = array();
		$arrMemberData = explode("//", $v);
		list($chkId) = pmysql_fetch("SELECT id FROM tblmember WHERE id = '".$arrMemberData[0]."'");
		if($chkId){
			##### 기존회원
			$type = "UPDATE ";
			
			$qry = "UPDATE tblmember SET ";
			$qry.= "reserve = '".$arrMemberData[20]."', ";
			$qry.= "sumprice = '".$arrMemberData[26]."' ";
			$qry.= "WHERE id='".$arrMemberData[0]."'";
			
			$result=pmysql_query($qry);
			
			//debug("존재 회원 : ".$arrMemberData[0]);
		}else{
			##### 신규회원
			$type = "INSERT ";
			
			
			//비밀번호
			$checknum = explode("-" , $arrMemberData[3]);
			$mobilenum = $checknum[2];
			$passwd = $arrMemberData[0].$mobilenum;
			$passwd = md5($passwd);
			
			//메일링& sms
			if($arrMemberData[4]=="y" && $arrMemberData[5]=="y") {
				$news_yn="Y";
			} else if($arrMemberData[4]=="y") {
				$news_yn="M";
			} else if($arrMemberData[5]=="y") {
				$news_yn="S";
			} else {
				$news_yn="N";
			}

			//남성 or 여성
			if($arrMemberData[6] == 'm'){
				$gender = '1';
			}else{
				$gender = '2';
			}
			
			//주소
			$home_post = str_replace("-","",$arrMemberData[7]);
			$home_addr = $arrMemberData[8]."=".$arrMemberData[9];
			
			//마지막 접속시간
			if($arrMemberData[10]=="0000-00-00 00:00:00"){
				$logindate = "19700101000000";
			}else{
				$logindatea = explode(" " , $arrMemberData[10]);
				$logindateb = explode("-",$logindatea[0]);
				$logindatec = explode(":",$logindatea[1]);
				$logindate = $logindateb[0].$logindateb[2].$logindateb[1].$logindatec[0].$logindatec[1]."00";
			}

			//등록 날짜
			if($arrMemberData[11]=="0000-00-00 00:00:00"){
				$date = "19700101000000";
			}else{
				$datea = explode(" " , $arrMemberData[11]);
				$dateb = explode("-",$datea[0]);
				$datec = explode(":",$datea[1]);
				$date = $dateb[0].$dateb[1].$dateb[2].$datec[0].$datec[1]."00";
			}

			//생년 월일
			if($arrMemberData[12] && $arrMemberData[13]){
				if(strlen($arrMemberData[13]) == 2){
					$birth = $arrMemberData[12]."-0".substr($arrMemberData[13],0,1)."-0".substr($arrMemberData[13],1,1);
				}elseif(strlen($arrMemberData[13]) == 3){
					$birth = $arrMemberData[12]."-0".substr($arrMemberData[13],0,1)."-".substr($arrMemberData[13],1,2);
				}elseif(strlen($arrMemberData[13]) == 4){
					$birth = $arrMemberData[12]."-".substr($arrMemberData[13],0,2)."-".substr($arrMemberData[13],2,2);
				}else{
					$birth = "1970-01-01";
				}
			}
			
			//음력 양력
			$lunar = $arrMemberData[18];
			if($lunar=="s"){
				$lunar = "1";
			}else{
				$lunar = "0";
			}

			$param["id"] = $arrMemberData[0];
			$param["passwd"] = $passwd;
			$param["name"] = $arrMemberData[14];
			$param["resno"] = '';
			$param["email"] = $arrMemberData[15];
			$param["mobile"] = $arrMemberData[3];
			$param["news_yn"] = $news_yn;
			$param["gender"] = $gender;
			$param["married_yn"] = $arrMemberData[16];
			$param["job"] = $arrMemberData[17];
			$param["birth"] = $birth;
			$param["lunar"] = $lunar;
			$param["home_post"] = $home_post;
			$param["home_addr"] = $home_addr;
			$param["home_tel"] = $arrMemberData[19];
			$param["office_post"] = '';
			$param["office_addr"] = '';
			$param["office_tel"] = '';
			$param["memo"] = '';
			$param["reserve"] = $arrMemberData[20];
			$param["joinip"] = $arrMemberData[21];
			$param["ip"] = $arrMemberData[21];
			$param["logindate"] = $logindate;
			$param["logincnt"] = $arrMemberData[22];
			$param["date"] = $date;
			$param["confirm_yn"] = 'Y';
			$param["rec_id"] = $arrMemberData[23];
			$param["group_code"] = '003';
			$param["member_out"] = 'N';
			$param["nickname"] = $arrMemberData[24];
			$param["mem_type"] = 0;
			$param["m_no"] = $arrMemberData[25];
			$param["interest"] = 0;
			$param["mem_wholesale"] = '0';
			$param["sumprice"] = $arrMemberData[26];
			$param["random_price"] = 0;

			
			$keys = array();
			$values = array();
			foreach($param as $kk=>$vv){
				$keys[] = $kk;
				
				$v_str = str_replace("'","''",$vv);
				$values[] = $v_str;
			}
			
			$key_str = implode(",",$keys);
			//exdebug($key_str);
			



			$qry="insert into tblmember("
			.$key_str."
			)values(
			'".$values[0]."',
			'".$values[1]."',
			'".$values[2]."',
			'".$values[3]."',
			'".$values[4]."',
			'".$values[5]."',
			'".$values[6]."',
			'".$values[7]."',
			'".$values[8]."',
			'".$values[9]."',
			'".$values[10]."',
			'".$values[11]."',
			'".$values[12]."',
			'".$values[13]."',
			'".$values[14]."',
			'".$values[15]."',
			'".$values[16]."',
			'".$values[17]."',
			'".$values[18]."',
			'".$values[19]."',
			'".$values[20]."',
			'".$values[21]."',
			'".$values[22]."',
			'".$values[23]."',
			'".$values[24]."',
			'".$values[25]."',
			'".$values[26]."',
			'".$values[27]."',
			'".$values[28]."',
			'".$values[29]."',
			'".$values[30]."',
			'".$values[31]."',
			'".$values[32]."',
			'".$values[33]."',
			'".$values[34]."',
			'".$values[35]."')";

			$result=pmysql_query($qry);

		}
		$cnt++;
		exdebug($type." : ".$arrMemberData[0]." : ".$arrMemberData[3]);
		pmysql_free_result($result);
	}
	exdebug($cnt);

/*
	$result=pmysql_query($sql,get_db_conn());
	*/
?>