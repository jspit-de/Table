<?php
// dev
  ini_set('display_errors', 'On');
  error_reporting(-1);
  
  header('Content-Type: text/html; charset=UTF-8'); 

  require __DIR__."/../class/Table.php";  //Adapt path !
  
  $data = [  
  ['name' => 'A1', 'value' => 3],  
  ['name' => 'A12', 'value' => 6],  
  ['name' => 'A2','value' => 14],  
  ['name' => 'A14','value' => 7],  
]; 
$htmlTable = Table::create($data)
  ->attribute('class="mytab"') 
  ->title(['Name','Wert']) 
  ->format(['value' => '%03d'])
  ->getHtml();

?>
<!DOCTYPE html>
<html lang=de>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <title>HTML-Test</title>
    <style>
      .mytab { border: #5F5F5F 2px solid; border-collapse:collapse;width:90%;max-width:300px;} 
      .mytab th, td{ border: 1px solid black; padding: 4px; margin:0;} 
      .mytab th{background-color: #338; color: white;} 
      .mytab tr:nth-child(odd)  { background-color:inherit; } 
      .mytab tr:nth-child(even) { background-color:#DDD; }
      .mytab .Col_value { width: 80px;}
    </style>
  </head>
  <body>
    <h1>Simple Table Demo</h1>
    <?php echo $htmlTable ?>
  </body>
</html>
