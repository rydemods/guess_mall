<?php

	require_once("../Excel/reader.php");
	require_once("../config/db_connect.php");
	require_once("../config/function.php");

//엑셀 파일 업로드 후 데이터 DB 포팅
function upload($file1,$fileTemp, $targetdir, $max_size = 1 , $allowext = array('xls'))   
{   
    $max_size = $max_size * 1024 * 1024;
    if($filearray['size'] > $max_size)
	{
		return false;   
    }else
	{
            $file_ext = end(explode('.', $file1));   
			
			         
            if(in_array($file_ext, $allowext))
            {   
                $file_name = rand(time(),1) . '.' . $file_ext;    
                   
                $path = $targetdir . '/' . $file_name;   
                   
                if(move_uploaded_file($fileTemp, $path))    
                {   
					
                    return $file_name;    
					
                }   
                else return false;   

            }   
            else return false;   
    }   
}






//브랜드 이름 DB에 저장
//$mallName = iconv('EUCKR', 'UTF-8', $_POST['mallName']);

    $uploaddir = $_SERVER['DOCUMENT_ROOT']."/WDERP/cargoexcel/exceltemp";
	$filename = upload($_FILES['excelfile']['name'], $_FILES['excelfile']['tmp_name'], $uploaddir , 1, array('xls'));  




$data = new Spreadsheet_Excel_Reader();

// Set output Encoding.
$data->setOutputEncoding('EUCKR'); 

$data->read($uploaddir.'/'.$filename);
//$data->read($uploaddir."/55562635.xls");

$ecnt=0;


for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) 
{
	//goods 추가컬럼
	$g_ins[]="goodsno_ord='".$data->sheets[0]['cells'][$i][1]."'";
	$g_ins[]="site_type='".$data->sheets[0]['cells'][$i][2]."'";
	$g_ins[]="publication_date='".$data->sheets[0]['cells'][$i][5]."'";
	$g_ins[]="introduce='".$data->sheets[0]['cells'][$i][10]."'";
	$g_ins[]="size='".$data->sheets[0]['cells'][$i][11]."'";
	$g_ins[]="page='".$data->sheets[0]['cells'][$i][12]."'";
	$g_ins[]="author='".$data->sheets[0]['cells'][$i][13]."'";
	$g_ins[]="form_type='".$data->sheets[0]['cells'][$i][14]."'";
	$o_ins[]="isbn='".$data->sheets[0]['cells'][$i][16]."'";

	//goods 기존 컬럼
	$g_ins[]="goodsnm='".$data->sheets[0]['cells'][$i][3]."'";
	$g_ins[]="longdesc='".$data->sheets[0]['cells'][$i][10]."'";
	$g_ins[]="regdt='".$data->sheets[0]['cells'][$i][17]."'";
	$g_ins[]="img_i='".$data->sheets[0]['cells'][$i][4]."'";
	$g_ins[]="img_s='".$data->sheets[0]['cells'][$i][18]."'";
	$g_ins[]="img_m='".$data->sheets[0]['cells'][$i][19]."'";
	$g_ins[]="img_l='".$data->sheets[0]['cells'][$i][20]."'";
	$g_ins[]="totstock='1'";

	$g_query="insert into gd_goods set ".implode(" , ", $g_ins);


	
	$s_query="select goodsno from gd_goods order by goodsno desc limit 1";
	$s_result=mysql_query($s_query);
	$s_row=mysql_fetch_array($s_result);

	//goods_option 추가컬럼
	$o_ins[]="discount='".$data->sheets[0]['cells'][$i][7]."'";
	$o_ins[]="discount_price='".$data->sheets[0]['cells'][$i][8]."'";
	$o_ins[]="barcode='".$data->sheets[0]['cells'][$i][15]."'";
	$o_ins[]="srs_no='".$data->sheets[0]['cells'][$i][22]."'";

	//goods_option 기존컬럼
	$o_ins[]="consumer='".$data->sheets[0]['cells'][$i][6]."'";
	$o_ins[]="price='".$data->sheets[0]['cells'][$i][9]."'";
	$o_ins[]="stock='1'";
	$o_ins[]="link='1'";
	$o_ins[]="goodsno='".$s_row[goodsno]."'";

	$o_query="insert into gd_goods_option set ".implode(" , ", $o_ins);
	
	
	$no_query="select optno, sno from gd_goods_option order by sno desc limit 1";
	$no_result=mysql_query($no_query);
	$no_row=mysql_fetch_array($no_result);

	$u_query="update gd_goods_option set optno='".$no_row[sno]."' where sno='".$no_row[sno]."'";

}//for

?>