<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once 'ClassStampaDocumentoGenerico.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      $AConnection = new TExtraStampaDocumentoGenerico(null);
      $AConnection->ServerSideScript();