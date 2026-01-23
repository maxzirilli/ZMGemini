<?php
     include_once "ClassXGenerateFatture.php";

     header("Content-Type: application/json;charset=UTF-8");
     header(ACCESS_CONTROLL_SHARED);
     header("Access-Control-Allow-Methods:POST, OPTIONS"); 

     class InvioManualeFattura extends TAdvQueryGeneraXMLInterface
     {
      protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
      { 
       $ListaFatture        = [];
       $ListaAutofatture    = [];
       $ListaNoteDiCredito  = [];

       $Zip = null;
       $TmpFile = null;

        try
        {
          $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                              'EsportaFattureManualmente', 
                                              'SelectSQL',
                                              'FattureDaInviare', 
                                              []);
          
          $Query = $PDODBase->query($SQLBody);
          while ($Doc = $Query->fetch(PDO::FETCH_ASSOC))
          {
            if($Doc['IS_NOTA_DI_CREDITO'] === 'T')
               $ListaNoteDiCredito[] = $Doc;
            elseif($Doc['IS_AUTOFATTURA'] === 'T') 
               $ListaAutofatture[] = $Doc;
            else
               $ListaFatture[] = $Doc;
          }
         }
         catch(Exception $e)
         {
           $JSONAnswer->Response = self::ERROR_EXTRA_SCRIPT;
           $JSONAnswer->Error    = $e->getMessage();
         }

         if (empty($ListaFatture) && empty($ListaNoteDiCredito) && empty($ListaAutofatture)) 
         {
           $JSONAnswer->Response = self::OPERATION_OK;
           $JSONAnswer->Message  = "Nessun documento da esportare";
           return;
         }

         $WorkOk = true;
         $Zip = new ZipArchive();
         $TmpFile = tempnam(sys_get_temp_dir(), 'zip');
         $Zip->open($TmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

         try
         {
           if(!empty($ListaNoteDiCredito))
           {
            foreach($ListaNoteDiCredito as $Doc)
            {
             $this->GeneraXMLEDisponiZip($Zip, 
                                         $this->FPrepareParameterValue($Doc['CHIAVE'],':'), 
                                         $Doc['ANNO'], 
                                         $Doc['NUMERO'], 
                                         $Doc['RAGIONE_SOCIALE'], 
                                         TIPO_DOCUMENTO_NOTA, 
                                         $PDODBase, 
                                         'F');
            }
           }
           
           if(!empty($ListaAutofatture))
           {
            foreach($ListaAutofatture as $Doc)
            {
             $this->GeneraXMLEDisponiZip($Zip, 
                                         $this->FPrepareParameterValue($Doc['CHIAVE'],':'), 
                                         $Doc['ANNO'], 
                                         $Doc['NUMERO'], 
                                         $Doc['RAGIONE_SOCIALE'],
                                         TIPO_DOCUMENTO_AUTOFATTURA,
                                         $PDODBase,
                                         'F'); 
            }
           }
             
           if(!empty($ListaFatture))
           {
            foreach($ListaFatture as $Doc)
            {
             $this->GeneraXMLEDisponiZip($Zip, 
                                         $this->FPrepareParameterValue($Doc['CHIAVE'],':'), 
                                         $Doc['ANNO'], 
                                         $Doc['NUMERO'], 
                                         $Doc['RAGIONE_SOCIALE'],
                                         TIPO_DOCUMENTO_FATTURA, 
                                         $PDODBase, 
                                         $Doc['DA_BANCO']);
            }
           } 
         }
         catch(Exception $e)
         {
           $JSONAnswer->Response = self::ERROR_EXTRA_SCRIPT;
           $JSONAnswer->Error    = $e->getMessage();
           $WorkOk               = false;
         }
         $Zip->close();

         if(!$WorkOk) return;

         try
         {
           $ZipData   = file_get_contents($TmpFile);
           $ZipBase64 = base64_encode($ZipData);

           $JSONAnswer->ZipBase64         = $ZipBase64;
           $JSONAnswer->ChiaviFatture     = array_column($ListaFatture, 'CHIAVE');
           $JSONAnswer->ChiaviAutofatture = array_column($ListaAutofatture, 'CHIAVE');
           $JSONAnswer->ChiaviNote        = array_column($ListaNoteDiCredito, 'CHIAVE');

           $ChiaviFatture     = array_column($ListaFatture, 'CHIAVE');
           $ChiaviNote        = array_column($ListaNoteDiCredito, 'CHIAVE');
           $ChiaviAutofatture = array_column($ListaAutofatture, 'CHIAVE');

           $PDODBase->beginTransaction();
           try
           {
            if(!empty($ChiaviFatture))
            {
               $this->FCambioToStatoInviato($PDODBase, $ChiaviFatture, 'UpdateFattureStatoInviato');
            }
            if(!empty($ChiaviNote)) 
            {
               $this->FCambioToStatoInviato($PDODBase, $ChiaviNote, 'UpdateNoteDiCreditoStatoInviato');
            }
            if(!empty($ChiaviAutofatture)) 
            {
               $this->FCambioToStatoInviato($PDODBase, $ChiaviAutofatture, 'UpdateAutofattureStatoInviato');
            }
            $PDODBase->commit();
           }
           catch(Exception $e)
           {
             $PDODBase->rollBack();
             throw new Exception($e->getMessage());         
           }
         }
         catch(Throwable $e)
         {
           $JSONAnswer->Response = self::ERROR_EXTRA_SCRIPT;
           $JSONAnswer->Error    = $e->getMessage();
         }
         if($TmpFile && file_exists($TmpFile)) 
            unlink($TmpFile);
      }

      protected function GeneraXMLEDisponiZip($Zip, $Chiave, $Anno, $Numero, $Cliente, $TipoDocumento, $PDODBase, $DaBanco)
      {
       $Fattura = $this->FCreateFatturaElettronica($Chiave, $TipoDocumento, $PDODBase);
       $BodyFattura = $Fattura->XMLBody->saveXML();

       $ClienteDepurato = preg_replace('/[^A-Za-z0-9]/', '', $Cliente);
       $DaBancoStr = ($DaBanco === 'T') ? 'DaBanco' : '';

       if($TipoDocumento === TIPO_DOCUMENTO_FATTURA)
       {
        $TipoStr = 'Fatture';
       } 
       elseif($TipoDocumento === TIPO_DOCUMENTO_AUTOFATTURA)
       {
        $TipoStr = 'Autofatture';
       }
       else
       {
        $TipoStr = 'NoteDiCredito';
       }

       $Zip->addFromString($Anno . '_' . $Numero . '_' . $ClienteDepurato . '_' . $TipoStr . $DaBancoStr . '.xml', $BodyFattura);
      }

      protected function FCambioToStatoInviato($PDODBase, $ListaChiavi, $NomeQuery)
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
             throw new Exception('Impossibile impostare come inviato il documento');
         }
       }
       catch(Exception $e)
       {
         error_log($SQLBody);
         throw $e;         
       }
      }
     }

     $Connection = new InvioManualeFattura();
     $Connection->ServerSideScript();