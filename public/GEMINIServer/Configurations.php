<?php

  error_reporting(E_ALL);
  ini_set("display_errors", 0);
  ini_set("log_errors", 1);
  ini_set("error_log", 'ZCipher.log');

  if(file_exists('ConfigDefinitions.php'))
    include_once('ConfigDefinitions.php');
  else
  {
    // Decifrazione della chiave simmetrica utilizzando la chiave asimmetrica pubblica 
    $ChiaveSimmetricaCifrata = file_get_contents('OtherInfos.cyp');
    $ChiavePubblica = file_get_contents('Utilties.bin');
    openssl_public_decrypt($ChiaveSimmetricaCifrata, $ChiaveSimmetricaDecifrata, $ChiavePubblica);

    // Decifrare JSON con la chiave simmetrica
    $ConfigurazioniCrittate = file_get_contents('ConfigDefinitions.enc');
    
    $ConfigurazioniDecrittate = openssl_decrypt($ConfigurazioniCrittate, 'aes-256-cbc', $ChiaveSimmetricaDecifrata, OPENSSL_RAW_DATA, 'KenIlGuerriero75');
    
    // Utilizzo del JSON
    $ArrayConfigurazioni = json_decode($ConfigurazioniDecrittate, true);
    if($ArrayConfigurazioni != null)
    {
      foreach($ArrayConfigurazioni as $Chiave => $Valore) 
         define($Chiave, $Valore);
    }
  } 

  ini_set("error_log", PATH_LOG);
?>