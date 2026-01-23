<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'ClassXGenerateFatture.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZFatturaElettronica.php';
      include_once PATH_LIBRERIE . 'ZStringFunct.php';
      include_once PATH_LIBRERIE . 'ZAdvQueryClient.php';
      include_once PATH_LIBRERIE . 'ZDBaseFunct.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:GET, OPTIONS"); 

      class TLogEmail
      {
        public $Chiave   = null;
        public $Allegato = null;

        public function __construct($Chiave, $Allegato) 
        {
          $this->Chiave    = $Chiave;
          $this->Allegato  = $Allegato;
        }
      }
      
      class TAdvQueryFixGenerazioneInserimentoVoceCorrelataRapporto extends TAdvQuery
      {
        protected function FExtraScriptIgnoreLogin()
        {
            return true;
        }

        public function Esegui()
        { 
          $PDODBase    = $this->FGetNewConnection();
          $ListaAllegatiDaSistemare = array();
          $Costante    = $_GET['Costante'];
          $Dal         = $_GET['Dal'];
          $Al          = $_GET['Al'];
          $Conteggio   = 0;

          $SQLBody = "SELECT COUNT(*) AS CONTEGGIO
                        FROM log_email
                       WHERE log_email.ALLEGATO <> ''
                         AND log_email.ALLEGATO IS NOT NULL";

          if($Query = $PDODBase->query($SQLBody))
            while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              $Conteggio = $Row['CONTEGGIO'];

          // $Query = null;
          unset($Query);


          $Effettuati = $Dal;
          while ($Effettuati < $Conteggio && $Effettuati < $Al)
          {
            $SQLBody = "SELECT CHIAVE,
                               ALLEGATO
                          FROM log_email
                         WHERE log_email.ALLEGATO <> ''
                           AND log_email.ALLEGATO IS NOT NULL
                         LIMIT $Costante OFFSET " . $Effettuati;

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                $Allegato = new TLogEmail($Row['CHIAVE'], $Row['ALLEGATO']);
                array_push($ListaAllegatiDaSistemare, $Allegato);
                unset($Allegato);
              }
            }
            
            $Effettuati = $Effettuati + $Costante;
            // $Query = null;
            unset($Query);
          }

          for($i = 0; $i < count($ListaAllegatiDaSistemare); $i++)
            $this->InsertAllegatoEmail($ListaAllegatiDaSistemare[$i]->Chiave, $ListaAllegatiDaSistemare[$i]->Allegato, $PDODBase);

        }

        protected function InsertAllegatoEmail($ChiaveLog, $Allegato, $PDODBase)
        {
            $Parametri = array();

            $OggettoBlob = new TAdvQueryOggettoBlob(OPERAZIONE_BLOB_INSERIMENTO, 
                                                    PrepareForRecordString($Allegato),
                                                    'data:application/pdf;',
                                                    '',
                                                    'ALLEGATO_LOG_EMAIL');

            $Parametri['CHIAVE']       = $this->FGetNewKey($PDODBase, GEN_CHIAVI);
            $Parametri['ID_LOG_EMAIL'] = $ChiaveLog;
            $Parametri['ALLEGATO']     = $OggettoBlob;
            $Parametri['DESCRIZIONE']  = 'ALLEGATO.pdf';

            $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                'LogEmail', 
                                                'EditSQL',
                                                'InserisciAllegatoEmail', 
                                                $Parametri,
                                                [1]);
            
            try
            {
              $PDODBase->query($SQLBody);
            }
            catch(Exception $e)
            {
              error_log($SQLBody);
              throw $e;
            }
            unset($Parametri);
        }
      }

      $AConnection = new TAdvQueryFixGenerazioneInserimentoVoceCorrelataRapporto();
      $AConnection->Esegui();