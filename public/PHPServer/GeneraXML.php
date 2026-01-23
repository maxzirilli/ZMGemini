<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'ClassXGenerateFatture.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';
      include_once PATH_LIBRERIE . 'ZStringFunct.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQueryGeneraXML extends TAdvQueryGeneraXMLInterface
      {
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        { 
          $Documento = JSON_decode($_POST['Params']); 
          $Tipo = TIPO_DOCUMENTO_FATTURA;
          if($Documento->IS_NOTA_DI_CREDITO == 'T')
            $Tipo = TIPO_DOCUMENTO_NOTA;
          else 
          {
            if($Documento->IS_AUTOFATTURA == 'T')
              $Tipo = TIPO_DOCUMENTO_AUTOFATTURA;
          }
          $JSONAnswer->XMLBody = $this->FGeneraXML($this->FPrepareParameterValue($Documento->CHIAVE,':'), $Tipo, $PDODBase);
        }

        protected function FGeneraXML($Chiave, $TipoDocumento, $PDODBase)
        {
          $Fattura = $this->FCreateFatturaElettronica($Chiave, $TipoDocumento, $PDODBase); 
          return $Fattura->XMLBody->saveXML();             
        }
      }

      $Connection = new TAdvQueryGeneraXML();
      $Connection->ServerSideScript();