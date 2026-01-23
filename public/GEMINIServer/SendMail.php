<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';

      require_once PATH_LIBRERIE . 'mailer_PHP8/src/PHPMailer.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/SMTP.php';
      require_once PATH_LIBRERIE . 'mailer_PHP8/src/Exception.php';
      
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 

      $AConnection = new TAdvQuery();
      $AConnection->SendMail(false);
?>