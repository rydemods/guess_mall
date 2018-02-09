<?
	$Dir="../";
	include_once($Dir."lib/init.php");
	include_once($Dir."lib/lib.php");
	include("access.php");
	$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

	switch ($mode){
		case "materialReg":
			$query = "
								INSERT INTO 
								tblmaterials 
									(
										name, ename, lauric, myristic, 
										palmitic, stearic, ricinoleic, oleic, 
										linoleic, linolenic, hardness, cleansing, 
										conditions, bubbly, creamy, naoh, 
										koh
									)
								VALUES
									(
										'$_POST[name]', '$_POST[ename]', '".($_POST[lauric]+0)."', '".($_POST[myristic]+0)."',
										'".($_POST[palmitic]+0)."', '".($_POST[stearic]+0)."', '".($_POST[ricinoleic]+0)."', '".($_POST[oleic]+0)."',
										'".($_POST[linoleic]+0)."', '".($_POST[linolenic]+0)."', '".($_POST[hardness]+0)."', '".($_POST[cleansing]+0)."',
										'".($_POST[conditions]+0)."', '".($_POST[bubbly]+0)."', '".($_POST[creamy]+0)."', '".($_POST[naoh]+0)."', 
										'".($_POST[koh]+0)."'
									)
			";
			pmysql_query($query);
			break;
		case "materialMod":
			$query = "
								UPDATE 
									tblmaterials 
								SET
									name		= '".$_POST[name]."',
									ename		= '".$_POST[ename]."',
									lauric		= '".($_POST[lauric]+0)."',
									myristic		= '".($_POST[myristic]+0)."',
									palmitic		= '".($_POST[palmitic]+0)."',
									stearic		= '".($_POST[stearic]+0)."',
									ricinoleic		= '".($_POST[ricinoleic]+0)."',
									oleic		= '".($_POST[oleic]+0)."',
									linoleic		= '".($_POST[linoleic]+0)."',
									linolenic		= '".($_POST[linolenic]+0)."',
									hardness		= '".($_POST[hardness]+0)."',
									cleansing		= '".($_POST[cleansing]+0)."',
									conditions		= '".($_POST[conditions]+0)."',
									bubbly		= '".($_POST[bubbly]+0)."',
									creamy		= '".($_POST[creamy]+0)."',
									naoh		= '".($_POST[naoh]+0)."',
									koh		= '".($_POST[koh]+0)."'
								WHERE 
									mno = '$_POST[mno]'
			";
			pmysql_query($query);
			break;
		case "materialDel":
			$query = "DELETE FROM tblmaterials WHERE mno = '".$_GET[mno]."'";
			pmysql_query($query);
			$_POST[returnUrl] = $_GET[returnUrl];
			break;
	}
	if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
	go($_POST[returnUrl]);
?>