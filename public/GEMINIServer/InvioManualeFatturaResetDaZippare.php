<?php
     include_once 'Configurations.php';
     include_once 'Definitions.php';
     include_once PATH_LIBRERIE . 'ZAdvQuery.php';

     header("Content-Type: application/json;charset=UTF-8");
     header(ACCESS_CONTROLL_SHARED);
     header("Access-Control-Allow-Methods:POST, OPTIONS"); 

     class InvioManualeFatturaResetDaZippare extends TAdvQuery
     {
      protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
      {
       $Parametri = JSON_decode($_POST['Params']); 

       $ChiaviFatture     = $Parametri->ChiaviFatture;
       $ChiaviNote        = $Parametri->ChiaviNote;
       $ChiaviAutofatture = $Parametri->ChiaviAutofatture;

       if (!empty($ChiaviFatture)) 
       {
         $this->FCambioToStatoDaZippare($PDODBase, $ChiaviFatture, 'UpdateFattureResetStatoDaZippare');
       }
       if (!empty($ChiaviNote)) 
       {
         $this->FCambioToStatoDaZippare($PDODBase, $ChiaviNote, 'UpdateNoteDiCreditoResetStatoDaZippare');
       }
       if (!empty($ChiaviAutofatture)) 
       {
         $this->FCambioToStatoDaZippare($PDODBase, $ChiaviAutofatture, 'UpdateAutofattureResetStatoDaZippare');
       }
      }

      protected function FCambioToStatoDaZippare($PDODBase, $ListaChiavi, $NomeQuery)
      {
       $Parametri = new stdClass();
       $Parametri->ListaChiavi = $ListaChiavi;
       $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                           'EsportaFattureManualmente', 
                                           'EditSQL',
                                           $NomeQuery, 
                                           (array) $Parametri
                                          );

       try
       {           
         if(!$PDODBase->query($SQLBody))
         {
             error_log($SQLBody);
             throw new Exception('Impossibile impostare come da zippare il documento');
         }
       }
       catch(Exception $e)
       {
         error_log($SQLBody);
         throw new Exception($e->getMessage());         
       }
      }
     }

     $Connection = new InvioManualeFatturaResetDaZippare();
     $Connection->ServerSideScript();