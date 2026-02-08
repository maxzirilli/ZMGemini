<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once 'AccessRights.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      const TIPO_COMUNICAZIONE_AVVISO_GENERICO                    = 'A';
      const TIPO_COMUNICAZIONE_FORNITORI_SENZA_CODICE             = 'F';
      const TIPO_COMUNICAZIONE_NOTE_GESTITE                       = 'N';
      const TIPO_COMUNICAZIONE_ALLARME_PRODOTTO                   = 'D';
      const TIPO_COMUNICAZIONE_QUANTITA_FORNITURE_NON_CONTROLLATE = 'Q';

      class TOggettoComunicazione
      {
        public $Tipo                = null;
        public $Descrizione         = '';
        public $ChiaveProdotto      = null;
        public $SogliaProdotto      = null;
        public $ChiaveCliente       = null;
        public $Chiave              = null;
        public $ChiaveMagazzino     = null;
        public $Informazioni        = null;

        function __construct($Tipo, $Descrizione)
        {
          $this->Tipo        = $Tipo;
          $this->Descrizione = $Descrizione;
        }
      }

      class TInformazioni
      {
        public $ListaFornitori    = [];
        public $ChiaveFornitore   = null;
        public $ChiaveProdotto    = null;
        public $MeseGenerazione   = null;
        public $ChiaveCliente     = null;
        public $RagioneSociale    = null;
        public $DatiUtili         = null;
        public $TipologiaProposta = null;
        public $PopupInformazioniAggiuntive = null;
      }

      class TInformazioniFornitore
      {
        public $ChiaveFornitore         = null;
        public $RagioneSocialeFornitore = null;
      }


      class TAdvQueryPopupComunicazioni extends TAdvQuery
      {
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
              $Parametri          = json_decode($_POST['Params']);
              $IndexComunicazioni = $Parametri->IndexComunicazioni;
              $ListaComunicazioni = [];
              $TerminatoDownload  = false;

              if($IndexComunicazioni == 0)
              {

                $this->FControlloErroreImportazionePassiveInserito($PDODBase, $ListaComunicazioni);
              }

              if($IndexComunicazioni == 1)
              {
                $this->FGetFornitoriSenzaCodice($PDODBase, $ListaComunicazioni);

              }

              if($IndexComunicazioni == 2)
              {
              }

              if($IndexComunicazioni == 3)
              {
                $this->FGetAllarmeProdotto($PDODBase, $ListaComunicazioni);

                //ATTIVARE IL BOOLEANO SOLO QUANDO SI è TERMINATO IL DOWNLOAD
                $TerminatoDownload = true;
              }

              $JSONAnswer->ListaComunicazioni = $ListaComunicazioni;
              $JSONAnswer->TerminatoDownload  = $TerminatoDownload;
            }

            // NON CANCELLARE PER CONTROLLARE I TEMPI DI ESECUZIONE DELLE QUERY
            // protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            // {
            //   $ListaComunicazioni = [];


            //   $start = microtime(true);
            //   $this->FControlloErroreImportazionePassiveInserito($PDODBase, $ListaComunicazioni);
            //   $end = microtime(true);
            //   $durata_ms = ($end - $start) * 1000;
            //   error_log("Funzione FControlloErroreImportazionePassiveInserito eseguita in " . round($durata_ms, 2) . " ms\n");
              
            //   $start = microtime(true);
            //   $this->FGetFornitoriSenzaCodice($PDODBase, $ListaComunicazioni);
            //   $end = microtime(true);
            //   $durata_ms = ($end - $start) * 1000;
            //   error_log("Funzione FGetFornitoriSenzaCodice eseguita in " . round($durata_ms, 2) . " ms\n");
              
            //   $start = microtime(true);
            //   $this->FGetNoteGestite($PDODBase, $ListaComunicazioni);
            //   $end = microtime(true);
            //   $durata_ms = ($end - $start) * 1000;
            //   error_log("Funzione FGetNoteGestite eseguita in " . round($durata_ms, 2) . " ms\n");
                      
            //   $start = microtime(true);
            //   $this->FGetScadenzaNoleggioProgrammato($PDODBase,$ListaComunicazioni);
            //   $end = microtime(true);
            //   $durata_ms = ($end - $start) * 1000;
            //   error_log("Funzione FGetScadenzaNoleggioProgrammato eseguita in " . round($durata_ms, 2) . " ms\n");

            //   $start = microtime(true);
            //   $this->FGetProposteModificheAnagrafiche($PDODBase, $ListaComunicazioni);
            //   $end = microtime(true);
            //   $durata_ms = ($end - $start) * 1000;
            //   error_log("Funzione FGetProposteModificheAnagrafiche eseguita in " . round($durata_ms, 2) . " ms\n");
              
            //   $start = microtime(true);
            //   $this->FGetAllarmeProdotto($PDODBase, $ListaComunicazioni);
            //   $end = microtime(true);
            //   $durata_ms = ($end - $start) * 1000;
            //   error_log("Funzione FGetAllarmeProdotto eseguita in " . round($durata_ms, 2) . " ms\n");

            //   $JSONAnswer->ListaComunicazioni = $ListaComunicazioni;
            // }
            private function FControlloErroreImportazionePassiveInserito($PDODBase, &$ListaComunicazioni)
            {
                $SQLBody = "SELECT impostazioni.ERRORE_IMPORTAZIONE_PASSIVE
                              FROM impostazioni";

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {  
                    if(isset($Row['ERRORE_IMPORTAZIONE_PASSIVE']))
                    {
                      $OggettoComunicazione = new TOggettoComunicazione(TIPO_COMUNICAZIONE_AVVISO_GENERICO, 
                                                                        "Errore importazione fatt. pass.: " . $Row['ERRORE_IMPORTAZIONE_PASSIVE']);
                      array_push($ListaComunicazioni, $OggettoComunicazione);
                    }
                  }
                }
                else throw new Exception("Impossibile ottenere l'errore di importazione delle fatture passive");
            }

            private function FGetAllarmeProdotto($PDODBase, &$ListaComunicazioni)
            {
              $SQLBody = "SELECT  prodotti.CHIAVE,
                                  prodotti.DESCRIZIONE,
                                  qnt_x_magazzino.QUANTITA_MAGAZZINO,
                                  qnt_x_magazzino.SOGLIA_ALLARME,
                                  magazzini.CHIAVE AS CHIAVE_MAGAZZINO,
                                  magazzini.DESCRIZIONE AS DESCRIZIONE_MAGAZZINO,
                                  codici_fornitore.ID_FORNITORE,
                                  anagrafiche.RAGIONE_SOCIALE
                             FROM prodotti
                             JOIN settori ON settori.CHIAVE = prodotti.ID_SETTORE
                             LEFT JOIN qnt_x_magazzino ON qnt_x_magazzino.ID_PRODOTTO = prodotti.CHIAVE
                             LEFT JOIN magazzini ON magazzini.CHIAVE = qnt_x_magazzino.ID_MAGAZZINO
                             LEFT JOIN codici_fornitore ON codici_fornitore.ID_PRODOTTO = prodotti.CHIAVE
                             LEFT JOIN anagrafiche ON anagrafiche.CHIAVE = codici_fornitore.ID_FORNITORE
                            WHERE prodotti.PRODOTTO_COMPOSTO <> 'T'
                              AND (settori.IS_SERVIZI = 'F' OR settori.IS_SERVIZI IS NULL)
                              AND qnt_x_magazzino.QUANTITA_MAGAZZINO < qnt_x_magazzino.SOGLIA_ALLARME
                            ORDER BY prodotti.CHIAVE";

              $LastProdotto         = null;
              $OggettoComunicazione = null;
              $OggettoFornitore     = null;

              if($Query = $PDODBase->query($SQLBody))
              {
                while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                {
                  if($LastProdotto != $Row['CHIAVE'])
                  {
                    $LastProdotto                         = $Row['CHIAVE'];
                    $OggettoComunicazione                 = new TOggettoComunicazione(TIPO_COMUNICAZIONE_ALLARME_PRODOTTO, "La quantità (". round($Row['QUANTITA_MAGAZZINO'] / 100, 2) . ") di " . $Row['DESCRIZIONE'] . 
                                                                                      " è inferiore alla soglia di allarme (". round($Row['SOGLIA_ALLARME'] / 100, 2) . ") nel magazzino '" . $Row['DESCRIZIONE_MAGAZZINO'] . "'");
                    $OggettoComunicazione->Informazioni    = new TInformazioni();
                    $OggettoComunicazione->ChiaveProdotto  = $Row['CHIAVE'];
                    $OggettoComunicazione->SogliaProdotto  = round($Row['SOGLIA_ALLARME'] / 100, 2);
                    $OggettoComunicazione->ChiaveMagazzino = $Row['CHIAVE_MAGAZZINO'];
                    array_push($ListaComunicazioni, $OggettoComunicazione);
                  }
                  // if(isset($Row['ID_FORNITORE']) && isset($Row['RAGIONE_SOCIALE']))
                  // {
                  //   $OggettoFornitore                          = new TInformazioniFornitore();
                  //   $OggettoFornitore->ChiaveFornitore         = $Row['ID_FORNITORE'];
                  //   $OggettoFornitore->RagioneSocialeFornitore = $Row['RAGIONE_SOCIALE'];
                  //   array_push($OggettoComunicazione->Informazioni->ListaFornitori, $OggettoFornitore);
                  // }
                }
              }
              else throw new Exception('Impossibile ottenere i prodotti da gestire'); 
            }

            private function FGetFornitoriSenzaCodice($PDODBase, &$ListaComunicazioni)
            {
                $SQLBody = "SELECT anagrafiche.CHIAVE,
                                   anagrafiche.RAGIONE_SOCIALE
                              FROM anagrafiche
                             WHERE (anagrafiche.CODICE IS NULL OR anagrafiche.CODICE = '')";

                if($Query = $PDODBase->query($SQLBody))
                {
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  {  
                      $OggettoComunicazione               = new TOggettoComunicazione(TIPO_COMUNICAZIONE_FORNITORI_SENZA_CODICE, 
                                                                                     "Attenzione: il fornitore " . $Row['RAGIONE_SOCIALE'] . ' è senza codice');
                      $OggettoComunicazione->Informazioni = new TInformazioni();
                      $OggettoComunicazione->Informazioni->ChiaveFornitore = $Row['CHIAVE'];
                      array_push($ListaComunicazioni, $OggettoComunicazione);
                  }
                }
                else throw new Exception('Impossibile ottenere i fornitori senza codice');
            }

      }

      $Connection = new TAdvQueryPopupComunicazioni();
      $Connection->ServerSideScript();
?>