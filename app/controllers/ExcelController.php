<?php 
 
class ExcelController extends BaseController {
 
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

		$mes       = $_POST['mes'];
		$m = explode("-", $mes);
		$messbd = $mes."-00";		
		$reporte = new Reportes();
		$tipo = $_POST['tipo'];

		if ($tipo == 1 ){			

		$d= $reporte->consultarmetrica($messbd);
		$nombre = "";
		$porcentaje= "";
		$totalfuncionarios = "";
		if ($d == false){
			$totalfuncionarios = $reporte->totalbase();			
			$totalfuncionarios = $totalfuncionarios[0]->total;			
			$nombre = $reporte->getnombrefecha($m[1]);			
			$totalvisitas     = $reporte->totalvisitasmes($m[0], $m[1],1);
			$totalvisitas     = $totalvisitas[0]->total;
			$totalingresos    = $reporte->totalingresosfuncionarios($m[0], $m[1],1);
			$totalingresos    = $totalingresos[0]->total;
			//echo $totalvisitas."<br>".$totalingresos;
			$porcentaje       = round ($reporte->porcentajedatos($totalingresos, $totalfuncionarios));
			$ingmesanterior   = $reporte->totalingresosmesanterior(1);
			$ingmesanterior   = $ingmesanterior[0]->total;
			$usuariosnuevos   = $totalingresos-$ingmesanterior;
			$usurecurrentes   = $usuariosnuevos + $totalingresos;
			
			/* GESTOR MÓVIL */
			$totalvisitasmovil     = $reporte->totalvisitasmes($m[0], $m[1],2);
			$totalvisitasmovil     = $totalvisitasmovil[0]->total;
			$totalingresosmovil    = $reporte->totalingresosfuncionarios($m[0], $m[1],2);
			$totalingresosmovil    = $totalingresosmovil[0]->total;
			$ingmesanteriormovil   = $reporte->totalingresosmesanterior(2);
			$ingmesanteriormovil   = $ingmesanteriormovil[0]->total;
			$usuariosnuevosmovil   = $totalingresosmovil-$ingmesanteriormovil;
			$usurecurrentesmovil   = $usuariosnuevosmovil + $totalingresosmovil;

			$datos = array ('nombre'=>$nombre,'totaling'=>$totalingresos, 'totalvisitas'=>$totalvisitas, 'totalnuevos'=>$usuariosnuevos, 'totalrecurrentes'=>$usurecurrentes, 'tipo'=>'1','fecha'=>$messbd,'totalbase'=>$totalfuncionarios,'porcentaje'=>$porcentaje); 
			$datosmovil = array ('nombre'=>$nombre,'totaling'=>$totalingresosmovil, 'totalvisitas'=>$totalvisitasmovil, 'totalnuevos'=>$usuariosnuevosmovil, 'totalrecurrentes'=>$usurecurrentesmovil, 'tipo'=>'2','fecha'=>$messbd , 'totalbase'=>$totalfuncionarios,'porcentaje'=>$porcentaje); 

			DB::table('metricasingresos')->insert($datos);
			DB::table('metricasingresos')->insert($datosmovil);

		} else {
			$ultimoreporte= $reporte->ultimoreporte();			
			$nombre = $ultimoreporte[0]->nombre;
			$totalfuncionarios = $ultimoreporte[0]->totalbase;
			$porcentaje = $ultimoreporte[0]->porcentaje;
			$mes= $ultimoreporte[0]->fecha;			
			$mes = str_replace("-00", "", $mes);
			
		}

		//print_r($data)


	Excel::create('Reporte de ingresos '.$nombre." ".$m[0], function($excel) use ($nombre, $totalfuncionarios, $porcentaje, $mes) {
 			
            $excel->sheet('Estadísticas', function($sheet) use ($nombre, $totalfuncionarios, $porcentaje, $mes)  {
 
               $array1 = array('id', 'Codigo_base', 'empresa', 'codigo_funcionario', 'cedula', 'nombres', 'Fecha de antiguedad', 'correo', 'cargo', 'area', 'Territorial', 'zona', 'Ubicacion', 'dispositivo', 'hora', 'Fecha' );

				
				$m = explode("-", $mes);
				$d = cal_days_in_month(CAL_GREGORIAN,$m[1],$m[0]);
				// defino el rango
				$inicio = $mes.'-01';
				$fin    = $mes.'-'.$d;
				$titulo= "Del 1 al ".$d." de ".$nombre." de ".$m[0];
			
$sheet->row(1, array(
     'Reporte Métricas'
));
$sheet->row(1, function($row) {

    // call cell manipulation methods
    //$row->setBackground('#538ED5');
    $row->setFontColor('#538ED5');

});

$sheet->row(2, array(
     'Portal de Negocios Bancarios BBVA'
));
$sheet->row(3, array(
     $titulo
));
$p=$porcentaje." %";
$sheet->row(4, array(
     '% Sobre Base de datos cargada en el mes.',$totalfuncionarios, $p
));

$sheet->row(6, array(
     'Mes', 'Total Usuarios que ingresaron','Total Visitas generadas','Total Usuarios Nuevos','Total Usuarios Recurrentes')
);

$reporte = new Reportes();

$datos= $reporte->estadisticasingresos(1);

$resultado = array_merge($datos,  $reporte->estadisticasingresos(2));
$sheet->fromArray($resultado, null, 'A7', true,false);
$cantidad= count($reporte->estadisticasingresos(1));
$filaactual=7+$cantidad; 
$sheet->prependRow($filaactual, array(
    
));
$sheet->prependRow($filaactual+1, array(
    'GESTOR MÓVIL'
));

});

  $excel->sheet('Gráfica', function($sheet){
            $objDrawing = new PHPExcel_Worksheet_Drawing;
            $objDrawing->setPath(public_path('img/graficames.png')); //your image path
            $objDrawing->setCoordinates('A2');
            $objDrawing->setWorksheet($sheet);
       });


  $excel->sheet('Ingresos x Funcionarios', function($sheet) {
  $reporte = new Reportes();
  $sheet->row(1, array(
     'Código funcionario','Número de Ingresos'
));
  $sheet->fromArray($reporte->ingresosporfuncionario(), null, 'A2', true,false);

  });
  $excel->sheet('Ingresos x Cargo', function($sheet) {
  $reporte = new Reportes();
  $sheet->row(1, array(
     'Cargo','Número de Ingresos'
));
  $sheet->fromArray($reporte->ingresosporcargo(), null, 'A2', true,false);

  });
    $excel->sheet('Ingresos x Zona', function($sheet) {
  $reporte = new Reportes();
  $sheet->row(1, array(
     'Zona','Número de Ingresos'
));
  $sheet->fromArray($reporte->ingresosporzona(), null, 'A2', true,false);

  });
      $excel->sheet('Ingresos x Territorial', function($sheet) {
  $reporte = new Reportes();
  $sheet->row(1, array(
     'Territorial','Número de Ingresos'
));
  $sheet->fromArray($reporte->ingresosporterritorial(), null, 'A2', true,false);

  });
        $excel->sheet('Ingresos x Ciudad', function($sheet) {
  $reporte = new Reportes();
  $sheet->row(1, array(
     'Ciudad','Número de Ingresos'
));
  $sheet->fromArray($reporte->ingresosporciudad(), null, 'A2', true,false);

  });

       


    })->export('xls');

} else {

		$d= $reporte->consultarmetricadocumentos($messbd);
		$nombre = "";
		$porcentaje= "";
		$totalfuncionarios = "";
		if ($d == false){
			$nombre = $reporte->getnombrefecha($m[1]);			
			
			$totaloferta        = $reporte->totaldescarga($m[0], $m[1],1);
			$totaloferta        = $totaloferta[0]->total;
			$totalcartanegocios = $reporte->totaldescarga($m[0], $m[1],2);
			$totalcartanegocios = $totalcartanegocios[0]->total;
			$totalpropuesta     = $reporte->totaldescarga($m[0], $m[1],3);
			$totalpropuesta     = $totalpropuesta[0]->total;
			
			$datos = array('nombre'=>$nombre,'propuestacomercial'=>$totalpropuesta, 'ofertacomercial'=>$totaloferta, 'cartadenegocios'=>$totalcartanegocios, 'fecha'=>$messbd);
			
			DB::table('metricasdocumentos')->insert($datos);
			

			//echo $totalvisitas."<br>".$totalingresos;
			
			


		} else{
			$ultimoreporte= $reporte->ultimoreportedescarga();			
			$nombre = $ultimoreporte[0]->nombre;						
			$mes= $ultimoreporte[0]->fecha;			
			$mes = str_replace("-00", "", $mes);

		}	

Excel::create('Reporte descarga de documentos '.$nombre." ".$m[0], function($excel) use ($nombre,$mes,$m)  {
 			
            $excel->sheet('Estadísticas', function($sheet) use ($nombre,$mes)  {
            	$m = explode("-", $mes);
				$d = cal_days_in_month(CAL_GREGORIAN,$m[1],$m[0]);

$titulo= "Del 1 al ".$d." de ".$nombre." de ".$m[0];
			
$sheet->row(1, array(
     'Reporte Métricas'
));
$sheet->row(1, function($row) {

    // call cell manipulation methods
    //$row->setBackground('#538ED5');
    $row->setFontColor('#538ED5');

});

$sheet->row(2, array(
     'Portal de Negocios Bancarios BBVA'
));
$sheet->row(3, array(
     $titulo
));
$sheet->row(5, array(
     'Mes', 'Propuesta Comercial','Oferta Comercial','Carta de Negocios')
);

$reporte = new Reportes();

$datos= $reporte->listarmetricasdescargas();
$sheet->fromArray($datos, null, 'A6', true,false);

            	});

                 $excel->sheet('Descargas Propuesta Comercial', function($sheet) use ($m) {
  $reporte = new Reportes();



  $sheet->fromArray($reporte->descargas($m[0], $m[1], 3), null, 'A1', true,true);

  });

                         $excel->sheet('Descargas Oferta Comercial', function($sheet) use ($m) {
  $reporte = new Reportes();



  $sheet->fromArray($reporte->descargas($m[0], $m[1], 1), null, 'A1', true,true);

  });

                                 $excel->sheet('Descargas Carta de Negocios', function($sheet) use ($m) {
  $reporte = new Reportes();



  $sheet->fromArray($reporte->descargas($m[0], $m[1], 2), null, 'A1', true,true);

  });

    })->export('xls');
 }
 
	}

}