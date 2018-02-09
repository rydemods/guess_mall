<?php // hspark
$Dir="../";
include_once($Dir."lib/init.php");
include_once($Dir."lib/lib.php");
include("access.php");

$mode=$_POST["mode"];
$code=$_POST["code"];
$prcode=$_POST["prcode"];

include("header.php"); 
?>
<style>td {line-height:18pt;}</style>
<script type="text/javascript" src="lib.js.php"></script>
<script>var LH = new LH_create();</script>
<script for=window event=onload>LH.exec();</script>
<script>LH.add("parent_resizeIframe('ListFrame')");</script>
<TABLE cellSpacing=0 cellPadding=0 width="100%" border=0 height="100%">
<form name=form1 action="<?=$_SERVER['PHP_SELF']?>" method=post>
<TR>
	<TD width="100%" height="100%"><select name=prcode size=28 style="width:100%;height:100%" onchange="parent.prcode=this.options[this.selectedIndex].value" class="select">
<?php
		$count=0;
		if (strlen($code)==12) {
			$likecode=substr($code,0,3);
			if(substr($code,3,3)!="000") {
				$likecode.=substr($code,3,3);
				if(substr($code,6,3)!="000") {
					$likecode.=substr($code,6,3);
					if(substr($code,9,3)!="000") {
						$likecode.=substr($code,9,3);
					}
				}
			}

			$link_qry="select c_productcode from tblproductlink where c_category like '{$likecode}%' group by c_productcode";
			$link_result=pmysql_query($link_qry);
			while($link_data=pmysql_fetch_object($link_result)){
				$linkcode[]=$link_data->c_productcode;
			}

						
			$sql = "SELECT productcode,productname FROM tblproduct a ";
			$sql.= "WHERE a.productcode in ('".implode("','",$linkcode)."') ORDER BY date DESC";
			$result = pmysql_query($sql,get_db_conn());
			while ($row = pmysql_fetch_object($result)) {
				$count++;
				$sale="";
				//if($row->quantity<=0 && $row->quantity<>NULL) $sale=" (품절)";
				if ($prcode == $row->productcode) {
					echo "<option selected value=\"{$row->productcode}\">{$count}. ".$row->productname.$sale;
					$productname=$row->productname;
				} else {
				  echo "<option value=\"{$row->productcode}\">{$count}. ".$row->productname.$sale;
				}
			}
			echo "</option>\n";
		}
		pmysql_free_result($result);
?>
	</select></TD>
</TR>
</form>
</TABLE>
</body>
</html>
