<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
 	    include_once PATH_LIBRERIE . 'ZGenericFunct.php';
 	    include_once PATH_LIBRERIE . 'ZStringFunct.php';
 	    include_once PATH_LIBRERIE . 'ZEconomicFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaResocontoRiBa
      {
        public $BAND_INTESTAZIONE = array();
        public $BAND_LOOPING_ROW  = array();
        public $QRChildBand1      = array();
        public $BAND_PAGE_FOOTER  = array();
        public $BAND_GROUP_FOOTER = array();
      }

      class TGroupFooterData
      {
        public $LB_TOTALE_RESOCONTO = 'Mazzol';
      }

      class TDatiLoopingRow
      {
        public $LB_DATA_SCADENZA  = '';
        public $LB_IMPORTO        = '';
        public $MM_INTESTATARIO   = array();
        public $LB_DATA_FATTURA   = '';
        public $DataDocumento     = null;
        public $LB_NUMERO         = '';
        public $MM_BANCA          = array();
        public $LB_ABI            = '';
        public $LB_CAB            = '';
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 
        public $QR_LOGO = null;
        public $LB_TITOLO = 'Resoconto fatture con Ri.Ba.';
      }

      class TReportResocontoRiBa extends TZReport
      {
         private $FCambioColore  = false;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'BAND_LOOPING_ROW')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
           }
         }
      }

      class TExtraStampaResocontoRiBa extends TAdvQuery
      {  
        private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
            $Result = new TStampaResocontoRiBa();

            $DatiIntestazione = new TDatiIntestazione();
            
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);

            $DatiIntestazione->LB_TITOLO = 'Resoconto fatture con Ri.Ba.';
            if(isset($Parametri->DallaData))
              $DatiIntestazione->LB_TITOLO .= ' dal ' . (new DateTime($Parametri->DallaData))->format('d/m/Y');
            if(isset($Parametri->AllaData))
              $DatiIntestazione->LB_TITOLO .= ' al ' . (new DateTime($Parametri->AllaData))->format('d/m/Y');  

            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

            $QueryPart = explode('FROM fatture',$this->FGetQueryCompiled($PDODBase,
                                                                 'Fatture', 
                                                                 'SelectSQL',
                                                                 'SelectFatture', 
                                                                  get_object_vars($Parametri)));
            $QueryChiavi = 'SELECT fatture.CHIAVE FROM fatture ' . $QueryPart[count($QueryPart) - 1];
            $QueryChiavi = explode('LIMIT', $QueryChiavi)[0];
            
            $ArrayTotaliFatture = TSystemInformation::GetTotaliFattura($PDODBase,$QueryChiavi,true);


            $QueryDatiResoconto = 'SELECT fatture.NUMERO, 
                                          fatture.DATA                    AS DATA_FATTURA, 
                                          fatture.CHIAVE,
                                          fatture.RAGIONE_SOCIALE, 
                                          fatture.CHIAVE,
                                          fatture.ID_CLIENTE,
                                          fatture.DA_BANCO,
                                          anagrafiche.CODICE,
                                          fatture.BANCA,
                                          fatture.IBAN                    AS IBAN_FATTURA,
                                          rate_fattura.NOTE,
                                          rate_fattura.IMPORTO            AS IMPORTO_RATA,
                                          rate_fattura.DATA               AS DATA_SCADENZA,
                                          rate_fattura.DATA_PAGAMENTO     AS DATA_RATA,
                                          cond_pagamento.RICEVUTA_BANCARIA
                                     FROM fatture 
                                                     JOIN anagrafiche        ON anagrafiche.CHIAVE = fatture.ID_CLIENTE
                                          LEFT OUTER JOIN rate_fattura   ON rate_fattura.ID_FATTURA = fatture.CHIAVE' . $QueryPart[count($QueryPart) - 1];
            $QueryDatiResoconto = explode('LIMIT', $QueryDatiResoconto)[0];
            
            // $LastChiaveFattura = -1;
            $StringaNote       = '';

            $TotaleImporti = 0;
            if($Query = $PDODBase->query($QueryDatiResoconto))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                if($Row['RICEVUTA_BANCARIA'] != 'T')
                  continue; // Se questa riga non è relativa a una fattura con Ri.Ba., non considero questa riga e passo direttamente alla successiva
                if(isset($Parametri->MostraSoloRateNonPagate) && $Parametri->MostraSoloRateNonPagate && isset($Row['DATA_RATA']))
                  continue; // Se la rata è pagata la elimino dalla stampa

                // if($LastChiaveFattura != $Row['CHIAVE'])
                // {
                  // $LastChiaveFattura = $Row['CHIAVE'];
                  for($i = 0; $i < count($ArrayTotaliFatture); $i++)
                  {
                    if($ArrayTotaliFatture[$i]->IdFattura == $Row['CHIAVE'])
                    {
                      // for($j = 0; $j < count($ArrayTotaliFatture[$i]->LsIva); $j++)
                      // {
                        $DatiLoopingRow = new TDatiLoopingRow();

                        if(!is_null($Row['NUMERO']))
                        {
                          if(($Row['DA_BANCO'] == 'T'))
                            $DatiLoopingRow->LB_NUMERO = 'B' . $Row['NUMERO'];
                          else $DatiLoopingRow->LB_NUMERO = $Row['NUMERO'];
                        }
                        else $DatiLoopingRow->LB_NUMERO = $Row['CHIAVE'];

                        $DatiLoopingRow->LB_DATA_FATTURA = date("d/m/Y", strtotime($Row['DATA_FATTURA']));
                        $DatiLoopingRow->DataDocumento = $Row['DATA_FATTURA'];
                        array_push($DatiLoopingRow->MM_INTESTATARIO, $Row['RAGIONE_SOCIALE']);
                        $DatiLoopingRow->LB_DATA_SCADENZA = date("d/m/Y", strtotime($Row['DATA_SCADENZA']));

                        $DatiLoopingRow->LB_IMPORTO = EuropeanCurrencyFormat($Row['IMPORTO_RATA'] / 100);
                        $TotaleImporti += $Row['IMPORTO_RATA'] / 100;

                        
                        $DatiLoopingRow->MM_BANCA = $Row['BANCA'] ?? '';

                        $CurrentIban = $Row['IBAN_FATTURA'];

                        if(   $CurrentIban != null
                           && $CurrentIban != ''
                           && strlen($CurrentIban) == 27
                           && strtoupper(substr($CurrentIban, 0, 2)) == 'IT')
                        {
                          $CurrentAbiCab = GetAbiCabFromIban($CurrentIban);

                          if($CurrentAbiCab->Valido)
                          {
                            $DatiLoopingRow->LB_ABI = $CurrentAbiCab->ABI;
                            $DatiLoopingRow->LB_CAB = $CurrentAbiCab->CAB;
                          }
                        }
                      // }
                      array_push($Result->BAND_LOOPING_ROW, $DatiLoopingRow);
                    }
                  }
                // }
              }
            }    

            $GroupFooterData = new TGroupFooterData();
            $GroupFooterData->LB_TOTALE_RESOCONTO = EuropeanCurrencyFormat($TotaleImporti);
            array_push($Result->BAND_GROUP_FOOTER,$GroupFooterData);

            usort($Result->BAND_LOOPING_ROW, 
                  function($a, $b) 
                  {
                    if(strcmp($a->DataDocumento, $b->DataDocumento) != 0)
                      return strcmp($a->DataDocumento, $b->DataDocumento);
                    else 
                    {
                      if(strcmp($a->LB_NUMERO, $b->LB_NUMERO) != 0)
                      {
                        $PrimoNumero   = filter_var($a->LB_NUMERO, FILTER_SANITIZE_NUMBER_INT);
                        $SecondoNumero = filter_var($b->LB_NUMERO, FILTER_SANITIZE_NUMBER_INT);
                        if($PrimoNumero > $SecondoNumero)
                          return 1;
                        if($PrimoNumero < $SecondoNumero)
                         return -1;
                      }
                    }
                  });

				    $DatiBanda = new stdClass();
            array_push($Result->QRChildBand1,$DatiBanda);

            $RigaFooter = new stdClass();  
            array_push($Result->BAND_PAGE_FOOTER,$RigaFooter);
            
            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 
            return $Result;
        }


        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {
            $Parametri = json_decode($_POST['Params']);

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


            $Report = new TReportResocontoRiBa();
            $Report->LoadFromFile('ModelliStampe/QrStampaResocontoRiBa.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      

      $AConnection = new TExtraStampaResocontoRiBa();
      $AConnection->ServerSideScript();      
