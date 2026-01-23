<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
 	    include_once PATH_LIBRERIE . 'ZGenericFunct.php';
 	    include_once PATH_LIBRERIE . 'ZStringFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
      include_once PATH_LIBRERIE . 'ZEconomicFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaRegistroCassaFatture
      {
        public $BAND_INTESTAZIONE = array();
        public $BAND_PRIMA_RATA   = array();
        public $QRChildBand1      = array();
        public $BAND_PAGE_FOOTER  = array();
      }

      class TDatiFattura
      {
        public $LB_NUMERO         = null;
        public $LB_DATA           = null;
        public $MEMO_INTESTATARIO = array();
        public $LB_CODICE_CLIENTE = null;
        public $LB_AMMINISTRATORE = null;
        public $LB_NOTE           = null;
        public $MEMO_NOTE         = array();
        public $LB_IVA            = null;
        public $LB_TOT_FATTURA    = null;
        public $LB_IMPONIBILE     = null;
        public $DataDocumento     = null;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 
        public $QR_LOGO = null;
      }

      class TReportRegistroCassa extends TZReport
      {
         private $FCambioColore  = false;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'BAND_PRIMA_RATA')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
           }
         }
      }

      class TExtraStampaRegistroCassaFatture extends TAdvQuery
      {  
        private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
            $Result = new TStampaRegistroCassaFatture();

				    $DatiIntestazione = new TDatiIntestazione();
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
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
                                          fatture.DATA, 
                                          fatture.CHIAVE,
                                          fatture.RAGIONE_SOCIALE, 
                                          fatture.CHIAVE,
                                          fatture.ID_CLIENTE,
                                          fatture.DA_BANCO,
                                          clienti.CODICE_CLIENTE,
                                          amministratori.RAGIONE_SOCIALE AS RAGIONE_SOCIALE_AMM,
                                          rate_fattura.NOTE,
                                          rate_fattura.DATA_PAGAMENTO AS DATA_RATA
                                     FROM fatture 
                                                     JOIN clienti        ON clienti.CHIAVE = fatture.ID_CLIENTE
                                          LEFT OUTER JOIN rate_fattura   ON rate_fattura.ID_FATTURA = fatture.CHIAVE
                                          LEFT OUTER JOIN amministratori ON amministratori.CHIAVE = clienti.ID_AMMINISTRATORE' . $QueryPart[count($QueryPart) - 1];
            $QueryDatiResoconto = explode('LIMIT', $QueryDatiResoconto)[0];
            
            $LastChiaveFattura = -1;
            $StringaNote       = '';

            if($Query = $PDODBase->query($QueryDatiResoconto))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                if($LastChiaveFattura != $Row['CHIAVE'])
                {
                  $LastChiaveFattura = $Row['CHIAVE'];
                  for($i = 0; $i < count($ArrayTotaliFatture); $i++)
                  {
                    if($ArrayTotaliFatture[$i]->IdFattura == $Row['CHIAVE'])
                    {
                      for($j = 0; $j < count($ArrayTotaliFatture[$i]->LsIva); $j++)
                      {
                        $DatiFattura = new TDatiFattura();

                        if(!is_null($Row['NUMERO']))
                        {
                          if(($Row['DA_BANCO'] == 'T'))
                            $DatiFattura->LB_NUMERO = 'B' . $Row['NUMERO'];
                          else $DatiFattura->LB_NUMERO = $Row['NUMERO'];
                        }
                        else $DatiFattura->LB_NUMERO = $Row['CHIAVE'];

                        $DatiFattura->LB_DATA = date("d/m/Y", strtotime($Row['DATA']));
                        $DatiFattura->DataDocumento = $Row['DATA'];
                        array_push($DatiFattura->MEMO_INTESTATARIO, $Row['RAGIONE_SOCIALE']);
                        $DatiFattura->LB_CODICE_CLIENTE = isset($Row['CODICE_CLIENTE'])? $Row['CODICE_CLIENTE'] : '';
                        $DatiFattura->LB_AMMINISTRATORE = isset($Row['RAGIONE_SOCIALE_AMM'])? substr($Row['RAGIONE_SOCIALE_AMM'], 0, 30) : '';
                        $DatiFattura->LB_IVA  = isset($Row['CODICE_CLIENTE'])? $Row['CODICE_CLIENTE'] : '';

                        
                        
                        $DatiFattura->LB_TOT_FATTURA = EuropeanCurrencyFormat($ArrayTotaliFatture[$i]->Totale);
                        $DatiFattura->LB_IMPONIBILE  = EuropeanCurrencyFormat($ArrayTotaliFatture[$i]->SommaImponibile);
                            
                        $DatiFattura->LB_IVA = $ArrayTotaliFatture[$i]->LsIva[$j]->IVA . '%';
                        
                        array_push($Result->BAND_PRIMA_RATA, $DatiFattura);
                      }
                    }
                  }
                }
                if(isset($Row['NOTE']) && $Row['NOTE'] != '')
                {
                  if(isset($Row['DATA_RATA']) && $Row['DATA_RATA'] != '')
                    $StringaNote = date("d/m/Y", strtotime($Row['DATA_RATA'])) . ' ' . $Row['NOTE'];
                  else $StringaNote = $Row['NOTE'];
                  // $StringaNote = $Row['NOTE'];
                  // if($StringaNote != null)
                    // $StringaNote = TruncateString($StringaNote, 25);
                  
                  array_push($DatiFattura->MEMO_NOTE, $StringaNote);
                  // $StringaNote = '';
                }
                // $DatiFattura->LB_NOTE = ? substr($Row['NOTE'], 0, 25) : '';
              }
            }    

            usort($Result->BAND_PRIMA_RATA, 
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
                      else
                      {
                        $PrimoNumero   = filter_var($a->LB_IVA, FILTER_SANITIZE_NUMBER_INT);
                        $SecondoNumero = filter_var($b->LB_IVA, FILTER_SANITIZE_NUMBER_INT);
                        if($PrimoNumero < $SecondoNumero)
                          return 1;
                        if($PrimoNumero > $SecondoNumero)
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


            $Report = new TReportRegistroCassa();
            $Report->LoadFromFile('ModelliStampe/QrStampaRegistroCassaFatture.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
              unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      

      $AConnection = new TExtraStampaRegistroCassaFatture();
      $AConnection->ServerSideScript();      
