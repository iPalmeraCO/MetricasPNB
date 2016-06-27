<?php

class Reportes {

	function Reportes(){

	}
	/**
	Retorna el porcentaje de ingresos del mes 

	*/
	public function porcentajedatos($totalingresos, $totalfuncionarios){
		$porcentaje = ($totalingresos * 100) / $totalfuncionarios;
		return $porcentaje;
	}

	public function datosgestormovil(){
		
	}

	public function estadisticasingresos($tipo){
		$sql = "SELECT nombre,totaling,totalvisitas,totalnuevos,totalrecurrentes FROM metricasingresos WHERE tipo=$tipo";
		$datos = $this->ejecutarconsulta($sql);
		$arrayresultante= array();
		$sum1=0;$sum2=0;$sum3=0;$sum4=0;
		  foreach ($datos as $dato ) {    
			    $datos = array();
			    $datos = array($dato->nombre, $dato->totaling, $dato->totalvisitas, $dato->totalnuevos, $dato->totalrecurrentes);
			    $sum1+=$dato->totaling; $sum2+=$dato->totalvisitas; $sum3+=$dato->totalnuevos; $sum4+=$dato->totalrecurrentes; 
			    array_push($arrayresultante, $datos);
		    }
		 $datos= array('Totales', $sum1, $sum2, $sum3, $sum4);
		 array_push($arrayresultante, $datos);
		 return $arrayresultante;
	}

	public function ejecutarconsultaarray($sql){
		DB::setFetchMode(PDO::FETCH_ASSOC);
    	$consulta = DB::Select($sql);
     	DB::setFetchMode(PDO::FETCH_CLASS);     	
     	return $consulta;	
	}

	public function ingresosporfuncionario(){
		$sql="SELECT codigo_base, COUNT(codigo_base) as conteo   FROM `metricamensual` GROUP BY codigo_base ORDER BY COUNT(codigo_base) desc";
		return $this->ejecutarconsultaarray($sql);
	}
	public function ingresosporcargo(){
		$sql="SELECT cargo, COUNT(codigo_base) as conteo   FROM `metricamensual` GROUP BY cargo ORDER BY COUNT(codigo_base) desc";
		return $this->ejecutarconsultaarray($sql);
	}
	public function ingresosporzona(){
		$sql="SELECT zona, COUNT(codigo_base) as conteo   FROM `metricamensual` GROUP BY zona ORDER BY COUNT(codigo_base) desc";
		return $this->ejecutarconsultaarray($sql);
	}
	public function ingresosporterritorial(){
		$sql="SELECT territorial, COUNT(codigo_base) as conteo   FROM `metricamensual` GROUP BY territorial ORDER BY COUNT(codigo_base) desc";
		return $this->ejecutarconsultaarray($sql);
	}
	public function ingresosporciudad(){
		$sql="SELECT ciudad, COUNT(codigo_base) as conteo   FROM `metricamensual` GROUP BY ciudad ORDER BY COUNT(codigo_base) desc";
		return $this->ejecutarconsultaarray($sql);
	}
	
	public function ejecutarconsulta($sql){		
    	$consulta = DB::Select($sql);     	
     	return $consulta;	
	}

	public function consultarmetrica($mes){
		$sql = "SELECT * FROM metricasingresos WHERE fecha='$mes'";
		$data = $this->ejecutarconsulta($sql);
		if ($data != null){
			return true;
		} else {
			return false;
		}		
	}

	public function totalbase(){
		$sql = "SELECT count(codigo) as total FROM metricabasefuncionarios";
		return $this->ejecutarconsulta($sql);
	}
	public function totalvisitasmes($ano, $mes, $tipo){
		$t="";
		if ($tipo == 2){
			$t="and device = 'Ipad'";
		}
		$sql = "SELECT COUNT(login_pk) as total FROM `log_login` WHERE YEAR(login_fecha) = '$ano' and MONTH(login_fecha)='$mes' ".$t;
		return $this->ejecutarconsulta($sql);
	}

	public function totalingresosfuncionarios($ano, $mes, $tipo){
		$t="";
		if ($tipo == 2){
			$t="and device = 'Ipad'";
		}
		$sql="SELECT COUNT(DISTINCT(login_usr)) as total FROM log_login, metricabasefuncionarios WHERE YEAR(login_fecha) = '$ano' and MONTH(login_fecha)='$mes' and login_usr=codigo ".$t;
		return $this->ejecutarconsulta($sql);
	}
	public function totalingresosmesanterior($tipo){
		$sql ="SELECT totaling as total  FROM metricasingresos WHERE tipo=$tipo ORDER BY fecha desc LIMIT 1";
		return $this->ejecutarconsulta($sql);
	}
	public function getnombrefecha($mes){		
		$meses = array(
		 '01'=>'Enero',
		 '02'=>'Febrero',
		 '03'=>'Marzo',
		 '04'=>'Abril',
		 '05'=>'Mayo',
		 '06'=>'Junio',
		 '07'=>'Julio',
		 '08'=>'Agosto',
		 '09'=>'Septiembre',
		 '10'=>'Octubre',
		 '11'=>'Noviembre',
		 '12'=>'Diciembre'
		);
		return $meses[$mes];
	}

	public function ultimoreporte(){
		$sql ="SELECT * FROM metricasingresos WHERE tipo=1 ORDER BY fecha desc LIMIT 1";
		return $this->ejecutarconsulta($sql);
	}

	/*REPORTES GENERACIÓN DE DOCUMENTOS*/
	public function consultarmetricadocumentos($mes){
		$sql = "SELECT * FROM metricasdocumentos WHERE fecha='$mes'";
		$data = $this->ejecutarconsulta($sql);
		if ($data != null){
			return true;
		} else {
			return false;
		}		
	}


	public function totaldescarga($ano, $mes, $tipo){
		
		$sql="SELECT COUNT(id) as total FROM  log_creacion_pdfs  WHERE YEAR(`time`) = '$ano' and MONTH(`time`)='$mes' and tipo=$tipo";
		return $this->ejecutarconsulta($sql);
	}

	public function listarmetricasdescargas(){
		$sql="SELECT nombre, propuestacomercial, ofertacomercial, cartadenegocios  FROM  metricasdocumentos";
		$datos = $this->ejecutarconsulta($sql);
		$arrayresultante= array();
		$sum1=0;$sum2=0;$sum3=0;
		  foreach ($datos as $dato ) {    
			    $datos = array();
			    $datos = array($dato->nombre, $dato->propuestacomercial, $dato->ofertacomercial, $dato->cartadenegocios);
			    $sum1+=$dato->propuestacomercial; $sum2+=$dato->ofertacomercial; $sum3+=$dato->cartadenegocios;  
			    array_push($arrayresultante, $datos);
		    }
		 $datos= array('Totales', $sum1, $sum2, $sum3);
		 array_push($arrayresultante, $datos);
		 return $arrayresultante;
	}
	public function descargas($ano, $mes, $tipo){
		$sql="SELECT * FROM  log_creacion_pdfs  WHERE YEAR(`time`) = '$ano' and MONTH(`time`)='$mes' and tipo=$tipo";
		return $this->ejecutarconsultaarray($sql);
	}

	public function ultimoreportedescarga(){
		$sql ="SELECT * FROM metricasdocumentos ORDER BY fecha desc LIMIT 1";
		return $this->ejecutarconsulta($sql);
	}

}
