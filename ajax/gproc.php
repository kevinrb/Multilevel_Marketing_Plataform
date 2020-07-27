<?php
	require_once("../sec.php");
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	exec("rm -r -f /space2/sonidos.zip");
	exec("rm -r -f /var/www/html/vox/sonidos.zip");
	$ttt="";
	
	if(isset($_GET['file']) and isset($_GET['tipo'])){
		//exec("rm -r -f *.wav");
		
		$modulo1=$_GET['file']%1000;
		if($_GET['tipo']==1)
		{
			$fecha=mysql_fetch_row(qry("select date(hora) from  ((select * from nodosinh where idnodoin=".$_GET['file'].") union all (select * from nodosin where idnodoin=".$_GET['file'].")) a"));
			$fecha=$fecha[0];
			$modulo=$_GET['file']/10000;
			$modulo=floor($modulo);
			$en="in";
			$file3="in".$_GET['file'].".wav";
			$file3a="in".$_GET['file'].".gsm";
		}
		elseif($_GET['tipo']==2){
			$fecha=mysql_fetch_row(qry("select date(hora) from  ((select * from newcallsh where idcall=".$_GET['file'].") union all (select * from newcalls where idcall=".$_GET['file'].")) a"));
			$fecha=$fecha[0];	
			$modulo=$_GET['file']/100000;
			$modulo=floor($modulo);
			$en="new";
			$file3="new".$_GET['file'].".wav";
			$file3a="new".$_GET['file'].".gsm";
		}
		
		$file = "../vox/ajax/".$file3;
		//exec("scp /disco1/ALL/$en$modulo/".$file3." /var/www/html/vox/ajax/ ");
		//exec("scp /backup1/$en$modulo/".$file3." /var/www/html/vox/ajax/ ");
		//exec("scp /backup1/$modulo1/".$file3." /var/www/html/vox/ajax/ ");
		
		//exec("scp /disco1/ALL/$en$modulo/".$file3a." /var/www/html/vox/ajax/ ");
		//exec("scp /backup1/$modulo1/".$file3a." /var/www/html/vox/ajax/ ");
		if(file_exists("/mnt/disco1/space2/$fecha/".$file3a)){
			exec("scp /mnt/disco1/space2/$fecha/".$file3a." /var/www/html/vox/ajax/ ");
		}
		elseif(file_exists("/space2/$fecha/".$file3a)){
			exec("scp /space2/$fecha/".$file3a." /var/www/html/vox/ajax/ ");
		}
		//echo "scp /space2/$fecha/".$file3a." /var/www/html/vox/ajax/ ";
		
		if (!file_exists("/var/www/html/vox/ajax/$file3")){
			$file = "../vox/ajax/".$file3a;
			
		}
		
		header("Location: $file");
		/* header('Content-Description: File Transfer');
			header('Content-Type: audio/x-wav');
			header('Content-Disposition: attachment; filename='.$file.'');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			ob_clean();
			flush();
			$handle = fopen("$file", "r");
			/*	while (!feof($handle)) {
			$buffer = fgets($handle, 4096);
			echo $buffer;
			}
		fclose($handle);*/
		
	}
	
	//////////////////////////////////
	
	else if(isset($_GET['zip'])){
		
		$info=$_GET['zip'];
		
		
		exec("$info"); 
		exec("scp /disco1/sonidos.zip /var/www/html/vox/ ");
		
		
		
		header('Content-Type: application/zip');
		header("Content-Disposition: attachment; filename='sonidos.zip'");
		header('Content-Length: ' . filesize($zipname));
		header("Location: ../vox/sonidos.zip");
		
		
	}
	
	elseif(isset($pst_tar)){
		
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		if($pst_tar==1){
			
			
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		
		else if($pst_tar==3){
			//exec("rm -r -f *.wav");
			/*
				$modulo=$pst_id%1000;
				if($pst_tipo==1)
				{
				$file3="in".$pst_id.".wav";
				$file3a="in".$pst_id.".gsm";
				}
				elseif($pst_tipo==2){
				$file3="new".$pst_id.".wav";
				$file3a="new".$pst_id.".gsm";
				}
				
				////Al mismo nivel del PHP ejecutor
				echo exec("scp /space2/$modulo/".$file3." /var/www/html/vox/ajax ");
				//echo exec("scp /backup1/$modulo/".$file3." /var/www/html/vox/ajax ");
				echo exec("scp /space2/$modulo/".$file3a." /var/www/html/vox/ajax ");
				//echo exec("scp /backup1/$modulo/".$file3a." /var/www/html/vox/ajax ");
				echo "scp /space2/$modulo/".$file3." /var/www/html ";
				//opendir("/var/www/html/vox/ajax");
			*/
		}
	}
	elseif($pst_a=="fuser"){
		$s=$_POST["s"];
		$c=$_POST["c"];
		$l=8;
		$r=qry("select login,nombre,apellidos from usuarios where (apellidos like '%$s%') or (nombre like '%$s%') or (login like '%$s%') limit $l;");
		
		$a=array();	
		if(mysql_num_rows($r)>0){
		while($temp=mysql_fetch_row($r)){$a[]=$temp;}}
		echo json_encode($a);
		
		}elseif($pst_a=="fuser_act"){
		$s=$_POST["s"];
		$c=$_POST["c"];
		$l=8;
		$r=qry("select login,nombre,apellidos from usuarios where ((apellidos like '%$s%') or (nombre like '%$s%') or (login like '%$s%')) and activo=1 limit $l;");
		
		$a=array();	
		if(mysql_num_rows($r)>0){
		while($temp=mysql_fetch_row($r)){$a[]=$temp;}}
		echo json_encode($a);
		
	}
	
	
	//fono--->origen(callsin)
	
	
	
?>
