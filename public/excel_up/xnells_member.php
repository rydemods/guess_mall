<?php
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
require_once($Dir."Excel/reader.php");

$uploaddir = $_SERVER['DOCUMENT_ROOT']."/excel_up/exceltemp";

$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/xnells_member_20141230.xls');

$ecnt=0;

$rows = $data->sheets[0]['numRows'];
//exdebug($rows);
/*for ($i = 1; $i <= $data->sheets[0]['numCols']; $i++) {
	echo $i."�� : ".$data->sheets[0]['cells'][3][$i]." | ";
}
*/
/*
1�� : -���̵� [id]| 2�� : ȸ����� [group_code]| 3�� : -���� [name]| 
4�� : -���� [gender]1��,2��| 5�� : -���� [age]| 6�� : -������ [reserve]| 
7�� : -���� [logincnt]| 8�� : -�ֱ������� [logindate]20141013173151| 9�� : -������ [date]20141223184338| 
10�� : ���Űź� ??| 11�� : -�ѱ��űݾ� [accrue]| 12�� : -���� [birth]2014-12-25| 
13�� : -�̸��� [email]| 14�� : -��ȭ��ȣ [home_tel]02-0000-0000| 15�� : -�޴��� [mobile]000-0000-0000| 
16�� : -�ּ� [home_post],[home_addr]��=�豸���� ���� |
*/
//insert excel
$groupArray = array("VIP�����ȸ��(����)"=>"0004","���Ͼ�ȸ��"=>"0007");
for($i=2;$i<=$rows;$i++){
//for($i=4;$i<5;$i++){
	//���� $data->sheets[0]['cells'][$i][5]
	// echo "���� ".$data->sheets[0]['cells'][$i][7];	echo "  |  ";	
	//echo "�ѱ��űݾ� ".$data->sheets[0]['cells'][$i][11];	echo "  |  "; �÷��߰�
	//echo "���� ".$data->sheets[0]['cells'][$i][12];	echo "  |  ";
	//echo "�̸��� ".$data->sheets[0]['cells'][$i][13];	echo "  |  ";
	//echo "��ȭ��ȣ ".$data->sheets[0]['cells'][$i][14];	echo "  |  ";
	//echo "�޴��� ".$data->sheets[0]['cells'][$i][15];	echo "  |  ";
	
	
	//������
	$date_ymd = str_replace("-","",$data->sheets[0]['cells'][$i][9]);
	$date = $date_ymd."000000";
	//��ȣ
	$passwd = md5($data->sheets[0]['cells'][$i][1]."1234");
	//�ּ�
	$homePost_start = strpos($data->sheets[0]['cells'][$i][16],"[");
	$homePost_end = strpos($data->sheets[0]['cells'][$i][16],"]");
	$home_post = str_replace("-","",substr($data->sheets[0]['cells'][$i][16],$homePost_start+1,7));

	$homeAdder_array = explode(" ",substr($data->sheets[0]['cells'][$i][16],$homePost_end+1));
	$home_adder = "";
	foreach($homeAdder_array as $k=>$v){
		if($k == 4){
			$home_adder.="=";
		}
			$home_adder.=$v." ";
	}
	//����
	if($data->sheets[0]['cells'][$i][4] == "��"){
		$gender = "1";
	}else if($data->sheets[0]['cells'][$i][4] == "��"){
		$gender = "2";
	}else{
		$gender = "1";
	}
	//������
	$priceArray = array("��",",");
	$reserve = str_replace($priceArray,"",$data->sheets[0]['cells'][$i][6]);
	//������
	$sumprice = str_replace($priceArray,"",$data->sheets[0]['cells'][$i][11]);
	//�ֱ�������
	$replaceArray = array(":","-"," ");
	$logindate = str_replace($replaceArray,"",$data->sheets[0]['cells'][$i][8]);
	
	$sql = "
		INSERT INTO tblmember 
		(
			id,
			passwd,
			name,
			gender,
			age,
			reserve,
			email,
			mobile,
			home_post,
			home_addr,
			home_tel,
			date,
			birth,
			logindate,
			logincnt,
			sumprice,
			group_code,
			confirm_yn,
			mem_type,
			nickname
		)
		VALUES
		(
			'".$data->sheets[0]['cells'][$i][1]."',
			'".$passwd."',
			'".$data->sheets[0]['cells'][$i][3]."',
			'".$gender."',
			'".$data->sheets[0]['cells'][$i][5]."',
			'".$reserve."',
			'".$data->sheets[0]['cells'][$i][13]."',
			'".$data->sheets[0]['cells'][$i][15]."',
			'".$home_post."',
			'".$home_adder."',
			'".$data->sheets[0]['cells'][$i][14]."',
			'".$date."',
			'".$data->sheets[0]['cells'][$i][12]."',
			'".$logindate."',
			'".$data->sheets[0]['cells'][$i][7]."',
			'".$sumprice."',
			'0007',
			'N',
			0,
			''
		)
		";
		
		pmysql_query($sql);
		if(pmysql_error()){
			exdebug($i."�� | ".$data->sheets[0]['cells'][$i][1]." ���̵� error");
		}else{
			exdebug($i."�� | ".$data->sheets[0]['cells'][$i][1]." ���̵� �Է�");
		}
		
		//exdebug($sql);
		//echo $i;
		echo "<br>";
}//for

?>