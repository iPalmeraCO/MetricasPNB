<!DOCTYPE html>
<html>
  <head>
    <title>Generador de Metricas</title>
  </head>
  <p>Ingrese el mes y numero de visitaras del Mes anterior</p>
  {{ Form::open(array('url' => '/exportarmetrica', 'method' => 'POST', 'class' => 'form-captura')) }}
  <!-- <form action="exportarmetrica.php" method="POST"> -->
   Seleccione el tipo de Reporte:<br>
   <select name="tipo">
      <option value="1">Ingresos</option>
      <option value="2">Descarga de Documentos</option>
  </select>

   Seleccione el mes:<br>
   <input type="month" name="mes"><br>

   <input type="submit" value="enviar">

  </form>
  <?php 
 
$reporte = new Reportes();

$datos= $reporte->estadisticasingresos(1);
$resultado = array_merge($datos,  array());
$resultado = array_merge($resultado,  array('GESTOR MÓVIL'));
$resultado = array_merge($resultado,  $reporte->estadisticasingresos(2));

print_r($resultado);

   ?>
</html>