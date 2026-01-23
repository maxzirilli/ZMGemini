<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:GET,POST,OPTIONS");
      header("Access-Control-Allow-Headers: ParSessionId, Content-Type");

      if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
      }

      $AConnection = new TAdvQuery();
      switch($_SERVER['REQUEST_METHOD'])
      {
         case 'POST'   : $AConnection->EditSQL();
                         break;
         case 'GET'    : $AConnection->Select();
                         break;
         default       : break;
      }
?>
