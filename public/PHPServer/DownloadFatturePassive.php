<?php 
      include_once 'AcquisisciFattura.php';
      include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS"); 

      // class TMyFatturaPassiva
      // {
      //   public $Fornitore         = '';
      //   public $NumFattura        = '';
      //   public $IsNuovoFornitore  = false;

      //   function __construct($Fornitore, $NumFattura, $IsNuovoFornitore)
      //   {
      //     $this->Fornitore        = $Fornitore . '';      
      //     $this->NumFattura       = $NumFattura . '';
      //     $this->IsNuovoFornitore = $IsNuovoFornitore;  
      //   }       
      // }

      class TAdvQueryDownloadFatturePassive extends TAdvQueryAcquisisciFattura
      {
        private function FLoadConfiguration($PDODBase)
        {
            $SQLBody = "SELECT STILE_STRINGHE,ULTIMA_DATA_CARICATA FROM impostazioni";
            $Query = $PDODBase->query($SQLBody); 
            if ($Row = $Query->fetch(PDO::FETCH_ASSOC))
            {
              if(!is_null($Row["ULTIMA_DATA_CARICATA"])) 
                $this->FUltimaDataCaricata = $Row["ULTIMA_DATA_CARICATA"];
              $this->FStileStinghe = $Row['STILE_STRINGHE'];
            }
            $this->FUltimaDataCaricata = new DateTime();
            $this->FUltimaDataCaricata = $this->FUltimaDataCaricata->modify('-3 months');
            $this->FUltimaDataCaricata = $this->FUltimaDataCaricata->format('Y-m-d');

          //  echo $this->FUltimaDataCaricata . ' - ' . $this->FStileStinghe . "\n";
        }

        private function FFatturaGiaConsiderata($PDODBase, $NomeFile)
        {
          $SQLBody = "SELECT NOME_FILE 
                        FROM fatture_passive_importate 
                        WHERE NOME_FILE = '$NomeFile'";

          if($Query = $PDODBase->query($SQLBody))
          { 
            if($Row = $Query->fetch(PDO::FETCH_ASSOC))
                return true; 
            else return false;
          }
          else throw new Exception('Impossibile raccogliere dal database i nomi file delle fatture già importate');
        }

        protected function FExtraScriptServerSide($PDODBase, &$JSONAnswer)
        {
          $MySmond = TSystemInformation::CreazioneMySond($PDODBase);
          $LsFatturePassive = []; 
          $JSONAnswer->LsFatturePassive = [];
          $Error = '';
          
          $this->FLoadConfiguration($PDODBase);
          try
          {
            if($MySmond->GetListaFatturePassive($LsFatturePassive, $this->FUltimaDataCaricata, date('Y-m-d'), $Error))
            { 
              foreach($LsFatturePassive as $Fattura)
              {
                  if($this->FFatturaGiaConsiderata($PDODBase, $Fattura->File))
                    continue;
                  $MySmond->GetBodyFatturaPassiva($Fattura->File,$Fattura->Body);
                  $this->FAcquisisciFattura($PDODBase, $JSONAnswer, $Fattura->Body, $Fattura->File);
              }


              $PDODBase->query("UPDATE impostazioni SET ERRORE_IMPORTAZIONE_PASSIVE = NULL");
              $PDODBase->query("UPDATE impostazioni SET ULTIMA_DATA_CARICATA = " . $this->FPrepareParameterValue($this->FUltimaDataCaricata,'#'));
            }
            else throw new ErrorException('Impossibile caricare le fatture passive: ' . $Error);
          }
          catch(Exception $e)
          {
            error_log($e->getMessage());
            $PDODBase->query("UPDATE impostazioni SET ERRORE_IMPORTAZIONE_PASSIVE = " . $this->FPrepareParameterValue($e->getMessage(),'#'));
          }
        }
    }

      $Connection = new TAdvQueryDownloadFatturePassive();
      $Connection->ServerSideScript();
?>