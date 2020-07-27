<?php
	require("../global.php");
	require("../cnx.php");
	$result=posturl(array("a"=>"all_prod"),$XXurl_erp1);
	$rrr=json_decode($result, true);
	$imp_prod=array();
	$cprod=count($rrr["prod"]);
	for($i=0;$i<$cprod;$i++)
	{
		$imp_prod[$i]="('";
		$imp_prod[$i].=implode("','",$rrr["prod"][$i]);
		$imp_prod[$i].="')";
	}
	$prods=implode(",",$imp_prod);
	mysql_query("delete from productos");
	$rst=mysql_query("insert into productos values $prods");
	$cant=mysql_affected_rows();
	
	
	$imp_kit=array();
	$ckit=count($rrr["kit"]);
	for($i=0;$i<$ckit;$i++)
	{
		$imp_kit[$i]="('";
		$imp_kit[$i].=implode("','",$rrr["kit"][$i]);
		$imp_kit[$i].="')";
	}
	$kits=implode(",",$imp_kit);
	mysql_query("delete from kitparts");
	mysql_query("insert into kitparts values $kits");
	mysql_query("update productos set categoria=4 where idprod in (22,56,67,98,118,296,301,302,303,304,305,331,332,333,334,718,922,923,924)");
	
	function posturl($data,$url)
	{$postdata = http_build_query($data);
		$opts = array('http' => array('method'  => 'POST','header'  => 'Content-type: application/x-www-form-urlencoded','content' => $postdata));
		$context = stream_context_create($opts);
		$result  = file_get_contents($url,false,$context);
	return $result;}
?>