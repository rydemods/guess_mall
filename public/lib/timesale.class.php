<?
class TIMESALE
{
	function TIMESALE()
	{
		
		include_once(dirname(__FILE__)."/init.php");
		include_once(dirname(__FILE__)."/lib.php");
	
	}

	
	function getPdtData($productcode=''){
			
		$time_tday=date('Y-m-d H:i:s');

		if($productcode!=''){
			
			$qry="select * from tbl_timesale_list where productcode='".$productcode."'
			and view_type='1'
			and sdate<='".$time_tday."'
			and edate>='".$time_tday."'
			and ea>sale_cnt
			";
			//debug($qry);
			$res=pmysql_query($qry);
			$row=pmysql_fetch_array($res);
			pmysql_free_result($res);

			$data = $row;
			
			return $data;
		}
	}


}
?>