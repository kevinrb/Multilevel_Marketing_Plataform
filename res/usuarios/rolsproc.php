<?php
	require_once("../../sec.php");
	
	
	extract ($_POST, EXTR_PREFIX_ALL, "pst");
	
	///////////////////////////////////////////////////GUARDAR ROL////////////////////////////////////////////////////////////////////////
	if($pst_tar==1){
		$nombre=$pst_nombre;
		$sql="INSERT INTO rols(nombre) VALUES ('$nombre')";
		$psql=qry($sql);
		$lid=mysql_insert_id();
		for($i=0;$i<$pst_cc;$i++){
			$idf=$pst_funcs[$i];
			$sql="INSERT INTO rolfunc(idrol,idfunc) VALUES ('$lid','$idf'); ";
			$psql=qry($sql);
		}
	}
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	else if($pst_tar==2){
		$sql="SELECT idrol,nombre,obs FROM rols WHERE estado!='0' ORDER BY idrol";
		$psql=qry($sql);
		
		$data="";
		while($tmp=mysql_fetch_row($psql)){
			if($tmp[0]==9999 or $tmp[0]==9998 or $tmp[0]==9997){
				$data.=
				'<div class="input-group" id="div'.$tmp[0].'">
				<input type="text" class="form-control more" id="rol'.$tmp[0].'" name="'.$tmp[0].'" value="'.$tmp[1].'" readonly>
				<span class="input-group-btn">
				<button type="button" class="btn btn-default aplicar" data-toggle="modal" data-target="#modaplicar" idrol="'.$tmp[0].'" title="Asignar"><span class="glyphicon glyphicon-pushpin"></span></button>
				</span>
				</div><!-- /input-group -->';
			}
			else{
				$data.=
				'<div class="input-group" id="div'.$tmp[0].'">
				<input type="text" class="form-control more" id="rol'.$tmp[0].'" name="'.$tmp[0].'" value="'.$tmp[1].'" readonly>
				<span class="input-group-btn">
				<button type="button" class="btn btn-default aplicar" data-toggle="modal" data-target="#modaplicar" idrol="'.$tmp[0].'" title="Asignar"><span class="glyphicon glyphicon-pushpin"></span></button>
				<button type="button" class="btn btn-default editar"  data-toggle="modal" data-target="#modeditar'.$tmp[0].'" tar="modeditar'.$tmp[0].'" idrol="'.$tmp[0].'" title="Editar"><span class="glyphicon glyphicon-edit"></span></button>
				<button type="button" class="btn btn-default quitar" act="#div'.$tmp[0].'" idrol="'.$tmp[0].'" title="Eliminar"><span class="glyphicon glyphicon-remove"></span></button>
				</span>
			</div><!-- /input-group -->';}
		}
		echo $data;	
	}
	////////////////////////////////////////////////////ASIGNAR ROL EN TABLAS()/////////////////////////////////////////////////////////////////
	else if($pst_tar==3){
		$hoy = date("Y-m-d"); 
		for($i=0;$i<$pst_cc;$i++){
			$sql="INSERT INTO userrol (idrol,login,hora)
			SELECT * FROM (SELECT '$pst_idrol','$pst_user[$i]','$hoy' ) AS tmp
			WHERE NOT EXISTS (SELECT login FROM userrol WHERE login='$pst_user[$i]' AND idrol='$pst_idrol')";
			$psql=qry($sql);
			
			
			$q1="SELECT idfunc FROM rolfunc WHERE idrol='$pst_idrol'";
			$pq1=qry($q1);
			while($t1=mysql_fetch_row($pq1)){
				$q2="INSERT INTO permisos(login,idfunc)
				SELECT * FROM (SELECT '$pst_user[$i]','$t1[0]' ) AS tmp
				WHERE NOT EXISTS (SELECT login FROM permisos WHERE login='$pst_user[$i]' AND idfunc='$t1[0]')";
				//$q2="INSERT INTO permisos(login,idfunc) VALUES ('$pst_user[$i]','$t1[0]')";
				$pq2=qry($q2);
				
			}
			echo $q2;
		}
	}
	
	///////////////////////////////////////ELIMINAR ROL//////////////////////////////////////////////
	else if($pst_tar==4){
		$q1="DELETE FROM permisos WHERE login IN (select login from userrol where idrol='$pst_idrol') AND idfunc IN (select A.idfunc from (select * from rolfunc where idrol='$pst_idrol' ) A left join ( select * from rolfunc where idrol in (select idrol from userrol WHERE login in (select login from userrol where idrol='$pst_idrol') AND idrol!='$pst_idrol') ) B on A.idfunc=B.idfunc where B.idfunc is null)";
		//$q1="select A.idfunc from (select * from rolfunc where idrol='$pst_idrol' ) A left join ( select * from rolfunc where idrol!='$pst_idrol' ) B on A.idfunc=B.idfunc where B.idfunc is null";
		$pq1=qry($q1);
		echo $q1;
		/*
			while($t1=mysql_fetch_row($pq1)){
			$q2="DELETE FROM permisos WHERE idfunc='$t1[0]' ";
			$pq2=qry($q2);
		}*/
		
		$sql="DELETE FROM userrol WHERE idrol='$pst_idrol'";
		$psql=qry($sql);
		$sql="DELETE FROM rols WHERE idrol='$pst_idrol'";
		$psql=qry($sql);
		$sql="DELETE FROM rolfunc WHERE idrol='$pst_idrol'";
		$psql=qry($sql);
		
	}
	//////////////////////////////////////////////////////////////////////////////////
	else if($pst_tar==5){
		$pp="";
		for($i=0;$i<$pst_cc;$i++){
			$pp.=$pst_idfunc[$i].",";
		}
		$idf=rtrim($pp,",");
		$sql="SELECT DISTINCT grupo FROM menu WHERE idfunc in($idf)";
		$psql=qry($sql);
		$s1="";
		$s2="";
		while($tmp=mysql_fetch_row($psql)){
			$s1.='<option value="'.$tmp[0].'">'.$tmp[0].'</option>';
		}
		$sql="SELECT nombre FROM menu WHERE idfunc in($idf)";
		$psql=qry($sql);
		while($tmp=mysql_fetch_row($psql)){
			$s2.='<option value="'.$tmp[0].'">'.$tmp[0].'</option>';
		}
		
		$sel1='<option value="allgrupo" id="allg">TODOS</option>'.$s1;
		$sel2=$s2;
		echo json_encode(array("s1"=>$sel1,"s2"=>$sel2));
	}
	//////////////////////////////////////////////////////////////////////////////////
	else if($pst_tar==6){
		$gg="";
		for($i=0;$i<$pst_cc2;$i++){
			$gg.="'".$pst_grupo[$i]."',";
		}
		$grupo=rtrim($gg,",");
		
		$pp="";
		for($i=0;$i<$pst_cc;$i++){
			$pp.=$pst_idfunc[$i].",";
		}
		$idf=rtrim($pp,",");
		
		$sql="SELECT nombre FROM menu WHERE grupo in($grupo) AND idfunc in($idf) ";
		$psql=qry($sql);
		while($tmp=mysql_fetch_row($psql)){
			$sel2.='<option value="'.$tmp[0].'">'.$tmp[0].'</option>';
		}
		echo $sel2;
	}
	//////////////////////////////////////////////////////////////////////////////////
	else if($pst_tar==7){
		
		$sql=qry("SELECT nombre FROM rols WHERE idrol='$pst_idrol'");
		$tmp=mysql_fetch_row($sql);
		$nombre=$tmp[0];
		
		$sql=qry("SELECT idfunc FROM rolfunc WHERE idrol='$pst_idrol'");
		$ddd="";
		$pp="";
		while($tmp=mysql_fetch_row($sql)){
			$ddd.='<div class="col-sm-12 col-xs-6"><div class="input-group">
			<span class="form-control input-sm input-group-addon idfuncs">'.$tmp[0].'</span>
			<span class="input-group-btn"><button class="btn btn-sm btn-default xidf" type="button"><span class="glyphicon glyphicon-remove"></span></button>
			</span></div></div>';
			$pp.=$tmp[0].",";
		}
		
		$sql="SELECT DISTINCT idfunc FROM menu order by idfunc";
		$psql=qry($sql);
		while($tmp=mysql_fetch_row($psql)){
			$func.='<option value="'.$tmp[0].'" idfunc="'.$tmp[0].'">'.$tmp[0].'</option>';
		}
		$selfunc='<select class="form-control"  name="funcs" id="sidfunc"><option value="" selected disabled>--☼--</option>'.$func.'</select>';
		
		$idf=rtrim($pp,",");
		$sql="SELECT DISTINCT grupo FROM menu WHERE idfunc in($idf)";
		$psql=qry($sql);
		$s1="";
		$s2="";
		while($tmp=mysql_fetch_row($psql)){
			$s1.='<option value="'.$tmp[0].'">'.$tmp[0].'</option>';
		}
		$sql="SELECT nombre FROM menu WHERE idfunc in($idf)";
		$psql=qry($sql);
		while($tmp=mysql_fetch_row($psql)){
			$s2.='<option value="'.$tmp[0].'">'.$tmp[0].'</option>';
		}
		
		$sel1='<option value="allgrupo" id="allg">TODOS</option>'.$s1;
		$sel2=$s2;
		
		/*$res='<div class="form-group">
			<label for="exampleInputFile">Nombre</label>
			<input class="form-control" type="text" id="nombre" value="'.$nombre.'">
			</div>
		    
			<div class="form-group">
			<label for="exampleInputFile">Funciones</label>
			<table class="table">
			<tr><td class="col-md-2 active">Idfunc</td><td class="col-md-4 active">Grupo</td><td class="col-md-6 active">Nombre</td></tr>
			<tr>
			<td class="col-md-2">
			<table class="table">
			<tr><td class="col-md-6">'.$selfunc.'</td></tr>
			<tr><td class="col-md-6"><div class="col-md-12" id="elegidos">'.$ddd.'</div></td></tr>
			</table>			
			</td>
			
			<td class="col-md-4"><select class="form-control" size="25" name="funcs" id="sgrupo" multiple>'.$sel1.'</select></td>
			<td class="col-md-6"><select class="form-control" size="25" name="funcs" id="snombre" multiple>'.$sel2.'</select></td>
			
			</tr>
			</table>			
		</div>';*/
		$res='<div class="row">
		<div class="col-md-12">
		<div class="form-group">
		<label for="exampleInputFile">Nombre</label>
		<input class="form-control" type="text" id="nombre" value="'.$nombre.'">
		</div>
		</div>
		
		<div class="col-md-12">
		
		<div class="col-md-4"><label>Funciones</label><table class="table">
		<tr><td class="">'.$selfunc.'</td></tr>
		<tr><td class=""><div class="col-md-12" id="elegidos">'.$ddd.'</div></td></tr>
		</table></div>
		<div class="col-md-4"><label>Grupo</label><select class="form-control" size="15" name="funcs" id="sgrupo" multiple>'.$sel1.'</select></div>
		<div class="col-md-4"><label>Nombre</label><select class="form-control" size="15" name="funcs" id="snombre" multiple>'.$sel2.'</select></div>
		</div></div>';
		echo $res;		   
	}
	//////////////////////////////////////////////////////////////////////////////////
	else if($pst_tar==8){
		$sql=qry("UPDATE rols set nombre='$pst_nombre' WHERE idrol='$pst_idrol'");
		
		
		
		
		if($pst_cc2!=0){
			
			$pp="";
			for($i=0;$i<$pst_cc2;$i++){
				$pp.=$pst_remove[$i].",";
			}
			$idfr=rtrim($pp,",");
			
			$q1="DELETE FROM permisos WHERE login in(select login from rolfunc where idrol='$pst_idrol') AND idfunc in(SELECT M.res from (select A.idfunc as res from (select * from rolfunc where idrol='$pst_idrol' ) A left join 
			( select * from rolfunc where idrol in (select idrol from userrol WHERE login in (select login from userrol where idrol='$pst_idrol') AND idrol!='$pst_idrol') ) B 
			on A.idfunc=B.idfunc WHERE B.idfunc is null
			UNION ALL
			select A.idfunc from (select * from rolfunc where idrol in (select idrol from userrol WHERE login in (select login from userrol where idrol='$pst_idrol') AND idrol!='$pst_idrol')  ) A left join 
			( select * from rolfunc where idrol='$pst_idrol' ) B 
			on A.idfunc=B.idfunc WHERE B.idfunc is null) M WHERE M.res in($idfr))";
			$pq1=qry($q1);
			
			qry("delete from permisos where login in (select login from userrol where idrol='$pst_idrol');");
			
			for($i=0;$i<$pst_cc2;$i++){
				$idf=$pst_remove[$i];
				$q2="DELETE FROM rolfunc WHERE idrol='$pst_idrol' AND idfunc='$idf'";
				$pq2=qry($q2);
			}
			
		}
		
		if($pst_cc3!=0){
			for($i=0;$i<$pst_cc3;$i++){
				$idf=$pst_add[$i];
				$q2="INSERT INTO rolfunc(idrol,idfunc)
				SELECT * FROM (SELECT '$pst_idrol','$idf' ) AS tmp
				WHERE NOT EXISTS (SELECT idrol FROM rolfunc WHERE idrol='$pst_idrol' AND idfunc='$idf')";
				$pq2=qry($q2);
			}
			
			$sql=qry("SELECT login FROM userrol WHERE idrol='$pst_idrol'");
			while($tmp=mysql_fetch_row($sql)){
				for($i=0;$i<$pst_cc3;$i++){
					$idf=$pst_add[$i];
					$q2="INSERT INTO permisos(login,idfunc)
					SELECT * FROM (SELECT '$tmp[0]','$idf' ) AS tmp
					WHERE NOT EXISTS (SELECT login FROM permisos WHERE login='$tmp[0]' AND idfunc='$idf')";
					$pq2=qry($q2);
				}
			}
			
		}
		
		/*$q1="SELECT M.res from (select A.idfunc as res from (select * from rolfunc where idrol='$pst_idrol' ) A left join 
			( select * from rolfunc where idrol in (select idrol from userrol WHERE login in (select login from userrol where idrol='$pst_idrol') AND idrol!='$pst_idrol') ) B 
			on A.idfunc=B.idfunc WHERE B.idfunc is null
			UNION ALL
			select A.idfunc from (select * from rolfunc where idrol in (select idrol from userrol WHERE login in (select login from userrol where idrol='$pst_idrol') AND idrol!='$pst_idrol')  ) A left join 
			( select * from rolfunc where idrol='$pst_idrol' ) B 
		on A.idfunc=B.idfunc WHERE B.idfunc is null) M WHERE M.res in($idfr);";*/
		
		qry("insert ignore into permisos select login,idfunc from userrol a, rolfunc b where a.idrol=b.idrol;");
		
		echo $q1;
		
	}
	/*
		columnas($resc[4])
		
		function columnas($tam)
		{
		$dato="";
		for($i=1;$i<$tam;$i++)
		{$dato .= "C$i".",";}
		$dato .= "C$tam";
		return $dato;
		}
	*/
	
	
?>