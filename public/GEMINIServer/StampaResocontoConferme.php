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

      class TStampaResocontoPreventivi
      {
        public $DetailResocontoPreventivi = null;  
        public $BAND_INTESTAZIONE = null;     
        public $BAND_GROUP_FOOTER = null; 
        public $BAND_PAGE_FOOTER = null;
        public $QRChildBand1 = null;
      }

      class TDatiPreventivo
      {
        public $LB_NUMERO = null;	
        public $LB_DATA = null;        
        public $LB_INTESTATARIO = null;
        public $LB_COND_PAGAMENTO = null;
        public $LB_TOTALE = null;
        public $LB_TELEFONO = null;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 		
        public $QR_LOGO = null;
        public $SUGGERIMENTO_RESOCONTO = null;
      }

      class TDatiTotali
      {
        public $LB_TOT_PREVENTIVI = null;
      }

      class TRecapito
      {
        public $Telefono  = null;
        public $Referente = null;

        function __construct($Referente,$Telefono)
        {
          $this->Telefono = $Telefono;
          $this->Referente = $Referente;
        }
      }
  
      class TResponsabile
      {
        public $IdResponsabile = null;
        public $Preventivi     = array();
        public $Recapiti       = array();

        function __construct($IdResponsabile)
        {
           $this->IdResponsabile = $IdResponsabile;
        }

        public function AddPreventivo($IdPreventivo)
        {
           array_push($this->Preventivi,$IdPreventivo);
        }

        public function AddRecapito($Referente,$Telefono)
        {
           array_push($this->Recapiti,new TRecapito($Referente,$Telefono));
        }

      }

      class TReportResocontoPreventivi extends TZReport
      {
         private $FCambioColore  = false;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'DetailResocontoPreventivi')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
           }
         }
      }


      class TExtraStampaResocontoPreventivi extends TAdvQuery
      {  
        public $RecapitiClienti = array();
        public $TotaleResocontoPreventivi = 0;

        function __construct()
        {
          parent::__construct();
          $this->RecapitiClienti = array();
          $this->TotaleResocontoPreventivi = 0;
        }

        private function FAddDocResponsabile($IdResponsabile,$IdPreventivo,&$ArrayResponsabili)
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
          $CurrentResponsabile->AddPreventivo($IdPreventivo);
          
        }
        
        private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer , $NomeLogo)
        {

            $Result = new TStampaResocontoPreventivi();
            $QueryPart = explode('FROM',$this->FGetQueryCompiled($PDODBase,
                                                                 'Preventivi', 
                                                                 'FiltroPreventivo',
                                                                 'FiltroPreventivo', 
                                                                  get_object_vars($Parametri)));
            $QueryChiavi = 'SELECT preventivi.CHIAVE FROM' . $QueryPart[count($QueryPart) - 1];
            $QueryChiavi = explode('LIMIT', $QueryChiavi)[0];

            $ArrayTotaliPreventivi = TSystemInformation::GetTotaliPreventivo($PDODBase,$QueryChiavi);


            $QueryDatiResoconto = 'SELECT preventivi.NUMERO, 
                                          preventivi.DATA, 
                                          preventivi.STATO,
                                          preventivi.RAGIONE_SOCIALE, 
                                          preventivi.CHIAVE,
                                          preventivi.ID_CLIENTE,
                                          (SELECT DESCRIZIONE 
                                             FROM cond_pagamento
                                            WHERE cond_pagamento.CHIAVE = preventivi.COND_PAGAMENTO) AS COND_PAGAMENTO
                                     FROM ' . $QueryPart[count($QueryPart) - 1];
            $QueryDatiResoconto = explode('LIMIT', $QueryDatiResoconto)[0];

            $Result->DetailResocontoPreventivi = array();
            $TutteLeRighe = array();
            if($Query = $PDODBase->query($QueryDatiResoconto))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    array_push($TutteLeRighe,$Row);
            

              foreach($TutteLeRighe as $Row)
              {
                $this->FAddDocResponsabile($Row['ID_CLIENTE'], $Row['CHIAVE'], $this->RecapitiClienti);
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
                              $this->RecapitiClienti[$i]->AddRecapito(isset($Row['ID_CLIENTE'])? $Row['ID_CLIENTE'] : '', 
                                                                      isset($Row['TELEFONO'])? $Row['TELEFONO'] : '');
                            }
                      }
                    }
              }

              foreach($TutteLeRighe as $Row)
              { 
			
				        $DatiPreventivo = new TDatiPreventivo();
                if($Row['STATO'] == 'C')
                  $DatiPreventivo->LB_NUMERO = 'Conf. nr.' . $Row['NUMERO'];
                else $DatiPreventivo->LB_NUMERO = 'Prev. nr.' . $Row['NUMERO'];
                $DatiPreventivo->LB_DATA = date("d/m/Y", strtotime($Row['DATA']));
                $DatiPreventivo->LB_INTESTATARIO = $Row['RAGIONE_SOCIALE'];
                $DatiPreventivo->LB_COND_PAGAMENTO = $Row['COND_PAGAMENTO'];

                for($i = 0; $i < count($ArrayTotaliPreventivi); $i++)
                {
                  if($ArrayTotaliPreventivi[$i]->IdPreventivo == $Row['CHIAVE'])
                  {
                    $DatiPreventivo->LB_TOTALE = EuropeanCurrencyFormat($ArrayTotaliPreventivi[$i]->Totale);
                    $this->TotaleResocontoPreventivi += $ArrayTotaliPreventivi[$i]->Totale;
                    break;
                  }
                }

                $DatiPreventivo->LB_TELEFONO = array();
                  for($j = 0; $j < count($this->RecapitiClienti); $j++)
                    if($this->RecapitiClienti[$j]->IdResponsabile == $Row['ID_CLIENTE'])
                    {
                      for($k = 0; $k < count($this->RecapitiClienti[$j]->Recapiti); $k++)
                      {
                        if($this->RecapitiClienti[$j]->Recapiti[$k]->Referente == $Row['ID_CLIENTE'])
                         array_push($DatiPreventivo->LB_TELEFONO, $this->RecapitiClienti[$j]->Recapiti[$k]->Telefono);
                      }
                    }
                array_push($Result->DetailResocontoPreventivi,$DatiPreventivo);
              }
            }    
                                    
            $Result->BAND_GROUP_FOOTER = array();
				    $DatiTotali = new TDatiTotali();
            $DatiTotali->LB_TOT_PREVENTIVI = EuropeanCurrencyFormat($this->TotaleResocontoPreventivi);
            array_push($Result->BAND_GROUP_FOOTER,$DatiTotali);
            

            $Result->BAND_INTESTAZIONE = array();
				    $DatiIntestazione = new TDatiIntestazione();
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
            $DatiIntestazione->SUGGERIMENTO_RESOCONTO = '';
            array_push($Result->BAND_INTESTAZIONE,$DatiIntestazione);

          
            $Result->QRChildBand1 = array();
				    $DatiBanda = new stdClass();
            array_push($Result->QRChildBand1,$DatiBanda);

            $Result->BAND_PAGE_FOOTER = array();
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

            $Report = new TReportResocontoPreventivi();
            $Report->LoadFromFile('ModelliStampe/QrStampaResocontoPreventivi.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      

      $AConnection = new TExtraStampaResocontoPreventivi();
      $AConnection->ServerSideScript();      
