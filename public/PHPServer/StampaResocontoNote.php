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

        function __construct($Referente,$Telefono)
        {
          $this->Telefono = $Telefono;
          $this->Referente = $Referente;
        }
      }

      class TDatiNote
      {
        public $LB_NUMERO = null;	
        public $LB_DATA = null;        
        public $LB_INTESTATARIO = null;
        public $LB_COND_PAGAMENTO = null;
        public $LB_TOTALE = null;
        public $LB_TELEFONO = null;
      }      

      class TStampaResocontoNote
      {
        public $DetailResocontoNote = null;  
        public $BAND_INTESTAZIONE = null;     
        public $BAND_GROUP_FOOTER = null; 
        public $BAND_PAGE_FOOTER = null;
        public $QRChildBand1 = null;
      }

      class TDatiTotali
      {
        public $QRLabel4 = null;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 		
        public $QR_LOGO = null;
        public $SUGGERIMENTO_RESOCONTO = null;
      }
  
      class TResponsabile
      {
        public $IdResponsabile = null;
        public $Note           = array();
        public $Recapiti       = array();

        function __construct($IdResponsabile)
        {
           $this->IdResponsabile = $IdResponsabile;
        }

        public function AddNote($IdNote)
        {
           array_push($this->Note,$IdNote);
        }

        public function AddRecapito($Referente,$Telefono)
        {
           array_push($this->Recapiti,new TRecapito($Referente,$Telefono));
        }

      }

      class TReportResocontoNote extends TZReport
      {
         private $FCambioColore  = false;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
           TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
           if($ABand->Name == 'DetailResocontoNote')
           {
              $Colore = $this->FCambioColore ? '#F9FCFF' : '#ECF5FF';
              $ABand->Color = $Colore;
              $this->FCambioColore = !$this->FCambioColore;
           }
         }
      }

      class TExtraStampaResocontoNote extends TAdvQuery
      {  
        public $RecapitiClienti     = array();
        public $TotaleResocontoNote = 0;

        function __construct()
        {
           parent::__construct();
           $this->RecapitiClienti = array();
           $this->TotaleResocontoNote = 0;
        }

        private function FAddDocResponsabile($IdResponsabile,$IdNote,&$ArrayResponsabili)
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
          $CurrentResponsabile->AddNote($IdNote);
          
        }


        private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
            $Result = new TStampaResocontoNote();
            $QueryPart = explode('FROM',$this->FGetQueryCompiled($PDODBase,
                                                                 'NoteDiCredito', 
                                                                 'FiltroNote',
                                                                 'FiltroNote', 
                                                                  get_object_vars($Parametri)));
            $QueryChiavi = 'SELECT note_di_credito.CHIAVE FROM' . $QueryPart[count($QueryPart) - 1];
            $QueryChiavi = explode('LIMIT', $QueryChiavi)[0];

            $ArrayTotaliNote = TSystemInformation::GetTotaliNota($PDODBase,$QueryChiavi,true);

            $QueryDatiResoconto = 'SELECT note_di_credito.NUMERO, 
                                          note_di_credito.DATA, 
                                          note_di_credito.RAGIONE_SOCIALE, 
                                          note_di_credito.CHIAVE,
                                          note_di_credito.ID_CLIENTE,
                                          (SELECT DESCRIZIONE 
                                             FROM cond_pagamento
                                            WHERE cond_pagamento.CHIAVE = note_di_credito.COND_PAGAMENTO) AS COND_PAGAMENTO
                                     FROM ' . $QueryPart[count($QueryPart) - 1];
            $QueryDatiResoconto = explode('LIMIT', $QueryDatiResoconto)[0];

            $Result->DetailResocontoNote = array();
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
              }

              foreach($TutteLeRighe as $Row)
              { 
				        $DatiNote = new TDatiNote();
                if(!is_null($Row['NUMERO']))
                  $DatiNote->LB_NUMERO = 'Nota nr.' . $Row['NUMERO'];
                else $DatiNote->LB_NUMERO = 'Avviso nota';
                $DatiNote->LB_DATA = date("d/m/Y", strtotime($Row['DATA']));
                $DatiNote->LB_INTESTATARIO = $Row['RAGIONE_SOCIALE'];
                $DatiNote->LB_COND_PAGAMENTO = $Row['COND_PAGAMENTO'];
      
                for($i = 0; $i < count($ArrayTotaliNote); $i++)
                {
                  if($ArrayTotaliNote[$i]->IdNota == $Row['CHIAVE'])
                  {
                    $DatiNote->LB_TOTALE = EuropeanCurrencyFormat($ArrayTotaliNote[$i]->Totale);
                    $this->TotaleResocontoNote += $ArrayTotaliNote[$i]->Totale;
                    break;                    
                  }
                }

                $DatiNote->LB_TELEFONO = array();
                  for($j = 0; $j < count($this->RecapitiClienti); $j++)
                    if($this->RecapitiClienti[$j]->IdResponsabile == $Row['ID_CLIENTE'])
                    {
                      for($k = 0; $k < count($this->RecapitiClienti[$j]->Recapiti); $k++)
                      {
                        if($this->RecapitiClienti[$j]->Recapiti[$k]->Referente == $Row['ID_CLIENTE'])
                          if($this->RecapitiClienti[$j]->Recapiti[$k]->Telefono != null)
                            array_push($DatiNote->LB_TELEFONO, $this->RecapitiClienti[$j]->Recapiti[$k]->Telefono);
                      }
                    }
                array_push($Result->DetailResocontoNote,$DatiNote);
              }
            }    
            
            $Result->BAND_GROUP_FOOTER = array();
				    $DatiTotali = new TDatiTotali();
            $DatiTotali->QRLabel4 = EuropeanCurrencyFormat($this->TotaleResocontoNote);
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


            $Report = new TReportResocontoNote();
            $Report->LoadFromFile('ModelliStampe/QrStampaResocontoNote.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo)));

            if($NomeLogo != '')
              unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      

      $AConnection = new TExtraStampaResocontoNote();
      $AConnection->ServerSideScript();      
