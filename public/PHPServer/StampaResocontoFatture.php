<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
 	    include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
      include_once PATH_LIBRERIE . 'ZEconomicFunct.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TRecapito
      {
        public $Telefono = null;
        public $Referente = null;

        function __construct($Referente, $Telefono)
        {
          $this->Telefono  = $Telefono;
          $this->Referente = $Referente;
        }
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 		
        public $QR_LOGO = null;
        public $QR_MEMO_IBAN = null;
      }

      class TDatiFatture
      {
        public $LB_NUMERO = null;	
        public $LB_DATA = null;        
        public $LB_INTESTATARIO = null;
        public $LB_COND_PAGAMENTO = null;
        public $LB_TOT_FATTURA = null;
        public $LB_TOTALE_INCASSATO = null;
        public $LB_TOTALE_DA_INCASSARE = null;
        public $LB_TELEFONO = null;
        public $QRMEMO_EMAIL = null;
      }  

      class TRigaGroupFooter
      {
        public $LB_TOTALE_RESOCONTO = null;
        public $LB_TOTALE_INCASSO_RESOCONTO = null;
        public $LB_NS_AVERE = null;
      }
         
      class TStampaResocontoFatture
      {
        public $BAND_INTESTAZIONE = null;
        public $BAND_PRIMA_RATA = null;
        public $QRChildBand1 = null;
        public $BAND_PAGE_FOOTER = null;
        public $BAND_GROUP_FOOTER = null;
      }

      class TEmail
      {
        public $Email = null;
        public $Referente = null;

        function __construct($Referente, $Email)
        {
          $this->Email     = $Email;
          $this->Referente = $Referente;
        }
      }
  
      class TResponsabile
      {
        public $IdResponsabile = null;
        public $Fatture = null;
        public $Recapiti = null;
        public $Email = null;  

        function __construct($IdResponsabile)
        {
           $this->IdResponsabile = $IdResponsabile;
           $this->Fatture  = array();
           $this->Recapiti = array();
           $this->Email    = array();
        }

        public function AddFattura($IdFattura)
        {
           array_push($this->Fatture,$IdFattura);
        }

        public function AddRecapito($Referente, $Telefono)
        {
           array_push($this->Recapiti,new TRecapito($Referente, $Telefono));
        }

        public function AddEmail($Referente, $Email)
        {
           array_push($this->Email,new TEmail($Referente, $Email));
        }

      }

      class TReportResocontoFatture extends TZReport
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

      class TExtraStampaResocontoFatture extends TAdvQuery
      {  
        public $RecapitiClienti   = array();

        function __construct()
        {
          parent::__construct();
          $this->RecapitiClienti = array();
        }

        private function FAddDocResponsabile($IdResponsabile,$IdFattura,&$ArrayResponsabili)
        {
          $CurrentResponsabile = null;
          foreach($ArrayResponsabili as $Responsabile)       
              if($Responsabile->IdResponsabile == $IdResponsabile)
              {
                 $CurrentResponsabile = $Responsabile;
                 break;
              }
          if($CurrentResponsabile == null)
          {
             $CurrentResponsabile = new TResponsabile($IdResponsabile);
             array_push($ArrayResponsabili,$CurrentResponsabile);
          }
          $CurrentResponsabile->AddFattura($IdFattura);
          
        }


        private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
            $Result = new TStampaResocontoFatture();
            $Result->BAND_INTESTAZIONE = array();
				    $DatiIntestazione = new TDatiIntestazione();
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

            $DatiIntestazione->QR_MEMO_IBAN = array();

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
                                          (SELECT DESCRIZIONE 
                                             FROM cond_pagamento
                                            WHERE cond_pagamento.CHIAVE = fatture.COND_PAGAMENTO) AS COND_PAGAMENTO
                                     FROM fatture ' . $QueryPart[count($QueryPart) - 1];
            $QueryDatiResoconto = explode('LIMIT', $QueryDatiResoconto)[0];

            $Result->BAND_PRIMA_RATA = array();
            $TutteLeRighe = array();
            if($Query = $PDODBase->query($QueryDatiResoconto))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    array_push($TutteLeRighe,$Row);
            

              foreach($TutteLeRighe as $Row)
              {
                $this->FAddDocResponsabile($Row['ID_CLIENTE'],$Row['CHIAVE'],$this->RecapitiClienti);
              }

              $TuttiIClienti = '';
              for($i = 0; $i < count($this->RecapitiClienti); $i++)
                  $TuttiIClienti .= $this->RecapitiClienti[$i]->IdResponsabile . ',';

              if($TuttiIClienti != '')
              {
                    $TuttiIClienti = substr($TuttiIClienti,0,strlen($TuttiIClienti) - 1);
                    $QueryTelefonoClienti = 'SELECT TELEFONO, ID_CLIENTE
                                               FROM clienti_telefono
                                              WHERE ID_CLIENTE IN ('. $TuttiIClienti . ')';

                    if($Query = $PDODBase->query($QueryTelefonoClienti))
                    {
                      while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                      { 
                          for($i = 0; $i < count($this->RecapitiClienti); $i++)
                            {
                              $this->RecapitiClienti[$i]->AddRecapito($Row['ID_CLIENTE'], $Row['TELEFONO']);
                            }
                      }
                    }

                    $QueryEmailClienti = 'SELECT EMAIL, 
                                                 ID_CLIENTE
                                            FROM clienti_telefono
                                           WHERE ID_CLIENTE IN ('. $TuttiIClienti . ')';

                    if($Query = $PDODBase->query($QueryEmailClienti))
                    {
                      while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                      { 
                          for($i = 0; $i < count($this->RecapitiClienti); $i++)
                            {
                              $this->RecapitiClienti[$i]->AddEmail($Row['ID_CLIENTE'], $Row['EMAIL']);
                            }
                      }
                    }
              }

              $TotaleResoconto        = 0;
              $TotaleResocontoIncasso = 0;
              $TotaleResocontoDaAvere = 0;

              foreach($TutteLeRighe as $Row)
              { 
				        $DatiFattura = new TDatiFatture();
                if(!is_null($Row['NUMERO']))
                  $DatiFattura->LB_NUMERO = $Row['NUMERO'];
                else $DatiFattura->LB_NUMERO = 'A.' . $Row['CHIAVE'];
                $DatiFattura->LB_DATA = date("d/m/Y", strtotime($Row['DATA']));
                $DatiFattura->LB_INTESTATARIO = $Row['RAGIONE_SOCIALE'];
                $DatiFattura->LB_COND_PAGAMENTO = $Row['COND_PAGAMENTO'];

                for($i = 0; $i < count($ArrayTotaliFatture); $i++)
                {
                  if($ArrayTotaliFatture[$i]->IdFattura == $Row['CHIAVE'])
                  {
                    $DatiFattura->LB_TOT_FATTURA         = EuropeanCurrencyFormat($ArrayTotaliFatture[$i]->NettoAPagare);
                    $DatiFattura->LB_TOTALE_INCASSATO    = EuropeanCurrencyFormat($ArrayTotaliFatture[$i]->TotalePagato);
                    $DatiFattura->LB_TOTALE_DA_INCASSARE = EuropeanCurrencyFormat($ArrayTotaliFatture[$i]->TotaliDaIncassare);    
                    $TotaleResoconto        += $ArrayTotaliFatture[$i]->NettoAPagare;
                    $TotaleResocontoIncasso += $ArrayTotaliFatture[$i]->TotalePagato;
                    $TotaleResocontoDaAvere += $ArrayTotaliFatture[$i]->TotaliDaIncassare; 
                  }
                }

              
                $DatiFattura->LB_TELEFONO = array();
                  for($j = 0; $j < count($this->RecapitiClienti); $j++)
                    if($this->RecapitiClienti[$j]->IdResponsabile == $Row['ID_CLIENTE'])
                    {
                      for($k = 0; $k < count($this->RecapitiClienti[$j]->Recapiti); $k++)
                      {
                        if($this->RecapitiClienti[$j]->Recapiti[$k]->Referente == $Row['ID_CLIENTE'])
                        {
                          if(isset($this->RecapitiClienti[$j]->Recapiti[$k]->Telefono))
                            array_push($DatiFattura->LB_TELEFONO, $this->RecapitiClienti[$j]->Recapiti[$k]->Telefono);
                        }
                      }
                    }

                
                $DatiFattura->QRMEMO_EMAIL = array();
                  for($j = 0; $j < count($this->RecapitiClienti); $j++)
                    if($this->RecapitiClienti[$j]->IdResponsabile == $Row['ID_CLIENTE'])
                    {
                      for($k = 0; $k < count($this->RecapitiClienti[$j]->Email); $k++)
                      {
                        if($this->RecapitiClienti[$j]->Email[$k]->Referente == $Row['ID_CLIENTE'])
                        {
                          if(isset($this->RecapitiClienti[$j]->Email[$k]->Email))
                            array_push($DatiFattura->QRMEMO_EMAIL, $this->RecapitiClienti[$j]->Email[$k]->Email);                        
                        }
                      }
                    }
                
                array_push($Result->BAND_PRIMA_RATA,$DatiFattura);
              }
            }    
            
            $Result->QRChildBand1 = array();
				    $DatiBanda = new stdClass();
            array_push($Result->QRChildBand1,$DatiBanda);

            $Result->BAND_PAGE_FOOTER = array();
            $RigaFooter = new stdClass();  
            array_push($Result->BAND_PAGE_FOOTER,$RigaFooter);

         
            $Result->BAND_GROUP_FOOTER = array();
            $RigaGroupFooter = new TRigaGroupFooter();  
            $RigaGroupFooter->LB_TOTALE_RESOCONTO         = EuropeanCurrencyFormat($TotaleResoconto);       
            $RigaGroupFooter->LB_TOTALE_INCASSO_RESOCONTO = EuropeanCurrencyFormat($TotaleResocontoIncasso);
            $RigaGroupFooter->LB_NS_AVERE                 = EuropeanCurrencyFormat($TotaleResocontoDaAvere);
            array_push($Result->BAND_GROUP_FOOTER,$RigaGroupFooter); 
            
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


            $Report = new TReportResocontoFatture();
            $Report->LoadFromFile('ModelliStampe/QrStampaResocontoFatture.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
              unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      

      $AConnection = new TExtraStampaResocontoFatture();
      $AConnection->ServerSideScript();      
