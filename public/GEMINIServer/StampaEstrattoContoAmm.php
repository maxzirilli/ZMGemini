<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      include_once PATH_LIBRERIE . 'ZGenericFunct.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TEstrattoConto
      {
        public $BAND_INTESTAZIONE = array();
        public $BAND_SUMMARY      = array();
        public $BAND_FOOTER       = array();
        public $BAND_NOME_CONDOMINIO = array();
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA  = null;
        public $LB_NOME_CAUSALE       = '';
        public $QR_MEMO_IBAN = array();
        public $LB_NOME_AMMINISTRATORE = 'Nome Amministratore';
        public $DESTINATARIO = array();
        public $QR_LOGO      = null;
      }

      class TDatiFooter
      {
        public $LB_IMPORTO_DA_PAGARE_TOTALE = null;
      }

      class TDatiCondominio
      {
        public $QR_MEMO_FATTURE    = array();
        public $ChiaveCondominio   = null;
        public $LB_NOME_CONDOMINIO = null;
        public $LB_NUMERO_FATTURA  = null;
        public $LB_DATA_FATTURA    = null;

        public $LB_TOTALE_FATTURA   = null;
        public $LB_TOTALE_DA_PAGARE = null;
        public $TotaleFattura       = null;
        public $TotaliDaIncassare   = null;

        public $LB_TOTALE_CONDOMINIO = '0'; 
        public $LB_TOTALE_DA_PAGARE_CONDOMINIO = '0';
      }

      class TReportFattura extends TZReport
      {
         private $FLastCondominio           = -1;
         private $FTotaleCondominio         = 0;
         private $FTotaleCondominioDaPagare = 0;

         public function BeforeBandRepetitionPrint($ABand, $ASingleRecord, &$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'BAND_NOME_CONDOMINIO')
           {
              $PrintBand = $this->FLastCondominio != $ASingleRecord->ChiaveCondominio;
              if($PrintBand)
                 $this->FLastCondominio = $ASingleRecord->ChiaveCondominio;
              $this->FTotaleCondominio += $ASingleRecord->TotaleFattura;
              $this->FTotaleCondominioDaPagare += $ASingleRecord->TotaliDaIncassare;
           }
           if($ABand->Name == 'BAND_TOTALE_PARZIALE')
           {
              $PrintBand = $NextRecord == null || ($ASingleRecord->ChiaveCondominio != $NextRecord->ChiaveCondominio);
              if($PrintBand)
              { 
                $ASingleRecord->LB_TOTALE_CONDOMINIO = number_format($this->FTotaleCondominio, 2, ',', '.') . '€';
                $ASingleRecord->LB_TOTALE_DA_PAGARE_CONDOMINIO = number_format($this->FTotaleCondominioDaPagare, 2, ',', '.') . '€';
                $this->FTotaleCondominio = 0;
                $this->FTotaleCondominioDaPagare = 0;
              }
           }
         }
      }

      class TExtraStampaEstrattoContoAmminstratore extends TAdvQuery
      {            
          private function FGetDatiStampa($ChiaveAmministratore, $PDODBase, &$JSONAnswer, $NomeLogo)
          {
			   
            $Result           = new TEstrattoConto();
            $DatiIntestazione = new TDatiIntestazione();
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
            $DatiIntestazione ->LB_NOME_CAUSALE = 'ESTRATTO CONTO SOSPESI SCADUTI AL: ' . date('d/m/Y', time());

            $DatiFooter = new TDatiFooter();
            $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE = 0;

            $this->FPrepareParameterValue($ChiaveAmministratore, ':');

            $SQLBody = "SELECT amministratori.*,
                               zone.DESCRIZIONE AS DESCRIZIONE_ZONA
                          FROM amministratori
                               LEFT OUTER JOIN zone ON zone.CHIAVE = amministratori.ZONA_FATTURAZIONE  
                         WHERE amministratori.CHIAVE = $ChiaveAmministratore";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                $DatiIntestazione->LB_NOME_AMMINISTRATORE = $Row['RAGIONE_SOCIALE']; 
                $DatiIntestazione->DESTINATARIO = array( $Row['INDIRIZZO_FATTURAZIONE'] . ' ' . $Row['NR_CIVICO_FATTURAZIONE'] . ' - ' . $Row['CAP_FATTURAZIONE'] . ' ' . $Row['COMUNE_FATTURAZIONE'] . ' ' . $Row['DESCRIZIONE_ZONA']);
              }
            }

            $SQLBody = "SELECT amministratori_telefono.*
                          FROM amministratori_telefono
                         WHERE amministratori_telefono.ID_AMMINISTRATORE = $ChiaveAmministratore";

            $FirstNumberTel   = 0;
            $FirstNumberEmail = 0;
            $StringaTel       = '';
            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {  
                if(isset($Row['TELEFONO']) && $Row['TELEFONO'] != '')
                {
                  if($FirstNumberTel == 0)
                  {
                    $StringaTel = 'Telefono: ' . $Row['TELEFONO'];
                    $FirstNumberTel = 1;
                  }
                  else $StringaTel .= '; ' . $Row['TELEFONO'];
                }
                if(isset($Row['EMAIL']) && $Row['EMAIL'] != '')
                {
                  if($FirstNumberEmail == 0)
                  {
                    array_push($DatiIntestazione->DESTINATARIO, 'Email: ' . $Row['EMAIL']);
                    $FirstNumberEmail = 1;
                  }
                  else array_push($DatiIntestazione->DESTINATARIO, $Row['EMAIL']);
                }
              }
            }
            if($StringaTel != '')
              array_push($DatiIntestazione->DESTINATARIO, $StringaTel);


            $LastCliente = 0;
            $SQLBody = "SELECT * 
                          FROM
                       (SELECT fatture.ID_CLIENTE,
                               fatture.NUMERO,
                               fatture.CHIAVE,
                               fatture.DATA,
                               fatture.ID_CONTO_CORRENTE,
                               0 AS IMPORTO,
                               fatture.NOTE_IN_FATTURA AS NOTE,
                               clienti.CODICE_CLIENTE,
                               clienti.RAGIONE_SOCIALE AS RAGIONE_SOCIALE_CLIENTE,
                               conti_correnti_casse.IBAN AS IBAN_CONTO,
                               'F' AS TIPOLOGIA
                          FROM fatture
                                          JOIN clienti ON clienti.CHIAVE = fatture.ID_CLIENTE
                               LEFT OUTER JOIN conti_correnti_casse ON conti_correnti_casse.CHIAVE = fatture.ID_CONTO_CORRENTE
                         WHERE clienti.ID_AMMINISTRATORE = $ChiaveAmministratore
                           AND fatture.NUMERO IS NOT NULL
                           AND (SELECT COUNT(*)
                                  FROM rate_fattura
                                 WHERE DATA_PAGAMENTO IS NULL
                                   AND ID_MOVIMENTO IS NULL
                                   AND ID_FATTURA = fatture.CHIAVE) > 0

                        UNION

                        SELECT fatture_insolute_pregresse.ID_CLIENTE,
                               fatture_insolute_pregresse.NUMERO,
                               fatture_insolute_pregresse.CHIAVE,
                               fatture_insolute_pregresse.DATA,
                               fatture_insolute_pregresse.ID_CONTO_PAGAMENTO AS ID_CONTO_CORRENTE,
                               fatture_insolute_pregresse.IMPORTO,
                               fatture_insolute_pregresse.NOTE,
                               clienti.CODICE_CLIENTE,
                               clienti.RAGIONE_SOCIALE AS RAGIONE_SOCIALE_CLIENTE,
                               conti_correnti_casse.IBAN AS IBAN_CONTO,
                               'P' AS TIPOLOGIA
                          FROM fatture_insolute_pregresse
                                          JOIN clienti ON clienti.CHIAVE = fatture_insolute_pregresse.ID_CLIENTE
                               LEFT OUTER JOIN conti_correnti_casse ON conti_correnti_casse.CHIAVE = fatture_insolute_pregresse.ID_CONTO_PAGAMENTO
                         WHERE clienti.ID_AMMINISTRATORE = $ChiaveAmministratore
                         AND fatture_insolute_pregresse.NUMERO IS NOT NULL
                         AND fatture_insolute_pregresse.DATA_PAGAMENTO IS NULL
                         AND fatture_insolute_pregresse.ID_MOVIMENTO IS NULL
                         ) AS UNION_TABLE
                         ORDER BY ID_CLIENTE, DATA";

            if($Query = $PDODBase->query($SQLBody))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {       
                  $DatiCondominio = new TDatiCondominio();

                  $DatiCondominio->ChiaveCondominio = $Row['ID_CLIENTE'];

                  if(isset($Row['CODICE_CLIENTE']) && $Row['CODICE_CLIENTE'] != '')
                    $DatiCondominio->LB_NOME_CONDOMINIO = $Row['CODICE_CLIENTE'] . ' - ' . $Row['RAGIONE_SOCIALE_CLIENTE'];
                  else $DatiCondominio->LB_NOME_CONDOMINIO = $Row['RAGIONE_SOCIALE_CLIENTE'];
                  
                  if($Row['TIPOLOGIA'] == 'F')
                    $DatiCondominio->LB_NUMERO_FATTURA = isset($Row['NUMERO'])? 'Fatt.ra ' . $Row['NUMERO'] : 'Avviso fatt.ra ' . $Row['CHIAVE'];
                  if($Row['TIPOLOGIA'] == 'P')
                    $DatiCondominio->LB_NUMERO_FATTURA = 'F. P. ' . $Row['NUMERO'];
                  
                  if(isset($Row['NOTE']) && $Row['NOTE'] != '')
                    $DatiCondominio->LB_NUMERO_FATTURA .= ' - ' . $Row['NOTE'];

                  $DatiCondominio->LB_DATA_FATTURA   = date("d/m/Y", strtotime($Row['DATA']));

                  if($Row['TIPOLOGIA'] == 'F')
                  {
                    $TotaleFattura = TSystemInformation::GetTotaliFattura($PDODBase, $Row['CHIAVE'], true);

                    $DatiCondominio->LB_TOTALE_FATTURA   = number_format($TotaleFattura[0]->NettoAPagare, 2, ',', '.') . '€';
                    $DatiCondominio->LB_TOTALE_DA_PAGARE = number_format($TotaleFattura[0]->TotaliDaIncassare, 2, ',', '.') . '€';

                    $DatiCondominio->TotaleFattura            = $TotaleFattura[0]->NettoAPagare;
                    $DatiCondominio->TotaliDaIncassare        = $TotaleFattura[0]->TotaliDaIncassare;
                    $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE += $TotaleFattura[0]->TotaliDaIncassare;
                  }

                  if($Row['TIPOLOGIA'] == 'P')
                  {
                    $DatiCondominio->LB_TOTALE_FATTURA        = number_format($Row['IMPORTO'] / 100, 2, ',', '.') . '€';
                    $DatiCondominio->LB_TOTALE_DA_PAGARE      = number_format($Row['IMPORTO'] / 100, 2, ',', '.') . '€';

                    $DatiCondominio->TotaleFattura            = $Row['IMPORTO'] / 100;
                    $DatiCondominio->TotaliDaIncassare        = $Row['IMPORTO'] / 100;
                    $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE += $Row['IMPORTO'] / 100;
                  }

                  

                  $DatiCondominio->LB_TOTALE_CONDOMINIO = '0';
                  if(isset($Row['ID_CONTO_CORRENTE']))
                    if(isset($Row['IBAN_CONTO']))
                    {
                      $ContoTrovato = false;
                      for( $i = 0; $i < count($DatiIntestazione->QR_MEMO_IBAN); $i++ )
                      {
                        if($DatiIntestazione->QR_MEMO_IBAN[$i] == $Row['IBAN_CONTO'])
                        {
                          $ContoTrovato = true;
                          break;
                        }
                      }
                      if(!$ContoTrovato)
                      {
                        array_push($DatiIntestazione->QR_MEMO_IBAN, $Row['IBAN_CONTO']);
                        $ContoTrovato = false;
                      }
                    }
                  array_push($Result->BAND_NOME_CONDOMINIO, $DatiCondominio);
              }
            } 
            else 
            {
              error_log($SQLBody);
              throw new Exception('Impossibile trovare le fatture dell\'amministratore');
            }


            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);


            $DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE = number_format($DatiFooter->LB_IMPORTO_DA_PAGARE_TOTALE, 2, ',', '.') . '€';
            array_push($Result->BAND_FOOTER, $DatiFooter);

            return $Result;
          }
            
          protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
          { 
            $Parametri = json_decode($_POST['Params']);
            $ChiaveAmministratore = $Parametri->ChiaveAmministratore;
               
            $NomeLogo = '';
            $SQLBody = "SELECT impostazioni.LOGO_AZIENDA
                          FROM impostazioni";

            if($Query = $PDODBase->query($SQLBody))
               while($Row = $Query->fetch(PDO::FETCH_ASSOC))
               { 
                 if(isset($Row['LOGO_AZIENDA']))
                 {
                    $NomeLogo = date("Y-m-d_H_i_s_U");
                    ForceDirectory(NOME_CARTELLA_FILE_TEMP);
                    file_put_contents(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg',base64_decode(explode(',',$Row['LOGO_AZIENDA'])[1]));
                 }
               }

            $Report = new TReportFattura();
            $Report->LoadFromFile('ModelliStampe/QrStampaEstrattoContoAmministratore.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($ChiaveAmministratore, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');            
          }
      }
      $AConnection = new TExtraStampaEstrattoContoAmminstratore();
      $AConnection->ServerSideScript();