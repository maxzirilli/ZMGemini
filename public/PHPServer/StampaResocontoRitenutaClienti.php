<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
 	    include_once PATH_LIBRERIE . 'ZReport.php';
 	    include_once PATH_LIBRERIE . 'ZGenericFunct.php';
      include_once PATH_LIBRERIE . 'ZFileFunct.php';
 	    include_once PATH_LIBRERIE . 'ZEconomicFunct.php';
      include_once 'ClassSelectClienteFiltro.php';

      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TStampaResocontoRitenuteClienti
      {
        public $BAND_INTESTAZIONE       = null;
        public $BAND_GROUP_FOOTER       = null;
        public $BAND_RESOCONTO_RITENUTE = null;
      }

      class TDatiRitenute
      {
        public $IdCliente = null;            
        public $LB_NOME_CLIENTE = null;  
        public $LB_ANNO = null;
        public $LB_TOTALE_RITENUTE_INCASSATE = null;
        public $LB_RITENUTE_CERTIFICATE = null;
        public $LB_TOTALE_DIFF_DA_CERTIF = null;
        public $IdentificativoFiscale = null;
      }

      class TDatiIntestazione
      {
        public $DENOMINAZIONE_SOCIETA = null;
        public $INTESTAZIONE_SOCIETA = null; 		
        public $LB_TITOLO = null;
        public $QR_LOGO = null;
      }

      class TRigaFooter
      {
        public $LB_TOT_RITENUTE = null;
        public $LB_TOT_CERTIF = null;
        public $LB_TOTALE_DA_CERTIF = null;
      }

      class TReportResocontoRitenute extends TZReport
      {
         private $FReportEnded        = true;
         private $FLastIdentificativo = '';
         private $FLastCliente        = -1;

         public function BeforeBandRepetitionPrint($ABand,$ASingleRecord,&$PrintBand, $NextRecord)
         {  
            TSystemInformation::GestioneNomeAziendaBandHeader($ABand, $ASingleRecord);
           
            if($ABand->Name == 'BAND_RESOCONTO_RITENUTE')
            {
                $PrintBand = $this->FLastCliente != $ASingleRecord->IdCliente;
                if($PrintBand)
                {
                  $this->FLastCliente  = $ASingleRecord->IdCliente;
                  $this->FReportEnded  = $PrintBand;

                  $ABand->Color = '#ECF5FF';

                }
                else
                {
                  $PrintBand = $this->FLastIdentificativo != $ASingleRecord->IdentificativoFiscale;
                  if($PrintBand)
                  {
                    $this->FLastIdentificativo = $ASingleRecord->IdentificativoFiscale;
                  }
                  $this->FReportEnded = $PrintBand;
                }
            }
            if($ABand->Name == 'QRChildBand1')
              $ABand->Color = '#F9FCFF';
         }
      }

      class TExtraStampaResocontoRitenutaClienti extends TAdvQuery
      {  
        public $TotaleResocontoFatture = 0;
        public $TotaleResocontoFattureRitenute = 0;
        
        function __construct()
        {
          parent::__construct();
          $this->TotaleResocontoFatture         = 0;
          $this->TotaleResocontoFattureRitenute = 0;
        }

        private function FGetDatiStampa($Parametri, $PDODBase, &$JSONAnswer, $NomeLogo)
        {
            $Result = new TStampaResocontoRitenuteClienti();

            $Result->BAND_GROUP_FOOTER = array();
            $RigaFooter = new TRigaFooter(); 
            $RigaFooter->LB_TOT_RITENUTE     = 0;
            $RigaFooter->LB_TOT_CERTIF       = 0;
            $RigaFooter->LB_TOTALE_DA_CERTIF = 0;

            $AConnection         = new TAdvQuerySelectClienteFiltro($Parametri, true, true);
            $Risposta            = $AConnection->ServerSideScript(false);
            $ListaClienti        = $Risposta->ListaClienti; 
  
            $ChiaviClienti = array();
  
            foreach($ListaClienti as $Cliente) 
            {
             if(isset($Cliente->CHIAVE))
               $ChiaviClienti[] = $Cliente->CHIAVE;
            }
            
            $Chiavi = implode(',', $ChiaviClienti);

            $DatiClienteRitenute = array();
            $DatiClienteRitenute = TSystemInformation::GetRitenutaClienteXAnno($PDODBase, $Chiavi, $Parametri->AnnoRitenuta);


            $QueryRitenuteCertificate = "SELECT ritenute_cliente.RITENUTA AS RITENUTA_CERTIFICATA,
                                                anagrafiche.RAGIONE_SOCIALE,
                                                anagrafiche.CODICE,
                                                ritenute_cliente.ID_CLIENTE,
                                                ritenute_cliente.ANNO AS ANNO_RITENUTA_CERTIF,
                                                ritenute_cliente.IDENTIFICATIVO_FISCALE 
                                           FROM ritenute_cliente
                                                    JOIN anagrafiche ON anagrafiche.CHIAVE = ritenute_cliente.ID_CLIENTE
                                          WHERE ritenute_cliente.ID_CLIENTE IN ($Chiavi)";

            $DatiClienteRitenuteCertif = array();

            if($Query = $PDODBase->query($QueryRitenuteCertificate))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                  array_push($DatiClienteRitenuteCertif, $Row);
              }
            }

            $QueryRitenuteAcconto = "SELECT ritenute_acconto_clienti.RITENUTE AS RITENUTA_ACCONTO,
                                            anagrafiche.RAGIONE_SOCIALE,
                                            anagrafiche.CODICE,
                                            ritenute_acconto_clienti.ID_CLIENTE,
                                            ritenute_acconto_clienti.CERTIFICATA AS RITENUTA_ACCONTO_CERTIFICATA,
                                            ritenute_acconto_clienti.DATA AS ANNO_RITENUTA_ACCONTO
                                       FROM ritenute_acconto_clienti
                                            JOIN anagrafiche ON anagrafiche.CHIAVE = ritenute_acconto_clienti.ID_CLIENTE
                                      WHERE ritenute_acconto_clienti.ID_CLIENTE IN ($Chiavi)";
                                      
            $DatiClienteRitenuteAcconto = array();

            if($Query = $PDODBase->query($QueryRitenuteAcconto))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                  array_push($DatiClienteRitenuteAcconto, $Row);
              }
            }

           $IndiceClienti = [];
           for($i = 0; $i < count($DatiClienteRitenute); $i++)
           {
               $IndiceClienti[$DatiClienteRitenute[$i]->IdCliente] = $i;
           }
           
           $AnnoRichiesto = (int)$Parametri->AnnoRitenuta;
           for($h = 0; $h < count($DatiClienteRitenuteAcconto); $h++) 
           {
               $RigaAcconto  = $DatiClienteRitenuteAcconto[$h];
               $IdCliente   = (int)$RigaAcconto['ID_CLIENTE'];
               $AnnoAcconto = (int)date('Y', strtotime($RigaAcconto['ANNO_RITENUTA_ACCONTO']));
           
               if($AnnoAcconto != $AnnoRichiesto)
                   continue;
           
               if(!isset($IndiceClienti[$IdCliente])) 
               {
                   $NuovoCliente                  = new stdClass();
                   $NuovoCliente->IdCliente       = $IdCliente;
                   $NuovoCliente->RagioneSociale  = $RigaAcconto['RAGIONE_SOCIALE'];
                   $NuovoCliente->CodiceCliente   = $RigaAcconto['CODICE'];
                   $NuovoCliente->Ritenute        = [];
                   $DatiClienteRitenute[]         = $NuovoCliente;
                   end($DatiClienteRitenute);
                   $IndiceClienti[$IdCliente]     = key($DatiClienteRitenute);
               }
           
               $Cliente = $DatiClienteRitenute[$IndiceClienti[$IdCliente]];
               $Trovato = false;
               foreach ($Cliente->Ritenute as $Ritenuta)
               {
                   if($Ritenuta->Anno == $AnnoRichiesto) 
                   {
                       $Trovato = true;
                       break;
                   }
               }
               if(!$Trovato) 
               {
                   $NuovaRitenuta            = new stdClass();
                   $NuovaRitenuta->Anno      = $AnnoRichiesto;
                   $NuovaRitenuta->IdentificativoFiscale = '';
                   $NuovaRitenuta->SommaRitenuta         = 0.0;
                   $Cliente->Ritenute[]                  = $NuovaRitenuta;
               }
           }


            for($i = 0; $i < count($DatiClienteRitenute); $i++) 
            {
             for($j = 0; $j < count($DatiClienteRitenuteCertif); $j++) 
             {
               if($DatiClienteRitenute[$i]->IdCliente == $DatiClienteRitenuteCertif[$j]['ID_CLIENTE'])
               {
                 $DatiClienteRitenute[$i]->RagioneSociale = $DatiClienteRitenuteCertif[$j]['RAGIONE_SOCIALE'];
                 $DatiClienteRitenute[$i]->CodiceCliente = $DatiClienteRitenuteCertif[$j]['CODICE'];

                for($k = 0; $k < count($DatiClienteRitenute[$i]->Ritenute); $k++) 
                {
                  if($DatiClienteRitenute[$i]->Ritenute[$k]->Anno == $DatiClienteRitenuteCertif[$j]['ANNO_RITENUTA_CERTIF'] && 
                     $DatiClienteRitenute[$i]->Ritenute[$k]->IdentificativoFiscale == $DatiClienteRitenuteCertif[$j]['IDENTIFICATIVO_FISCALE'])
                  {
                    $DatiClienteRitenute[$i]->Ritenute[$k]->RitenutaCertificata += intval($DatiClienteRitenuteCertif[$j]['RITENUTA_CERTIFICATA']) / 100;
                  }
                }
               }
             }
               for($h = 0; $h < count($DatiClienteRitenuteAcconto); $h++) 
               {
                 if($DatiClienteRitenute[$i]->IdCliente == $DatiClienteRitenuteAcconto[$h]['ID_CLIENTE'] && 
                    $DatiClienteRitenuteAcconto[$h]['RITENUTA_ACCONTO_CERTIFICATA'] == 'T') 
                 {
                   for($k = 0; $k < count($DatiClienteRitenute[$i]->Ritenute); $k++)
                   {
                     $AnnoAcconto = date('Y', strtotime($DatiClienteRitenuteAcconto[$h]['ANNO_RITENUTA_ACCONTO']));

                     if($DatiClienteRitenute[$i]->Ritenute[$k]->Anno == $AnnoAcconto)
                     {
                      if(!isset($DatiClienteRitenute[$i]->Ritenute[$k]->RitenutaCertificata)) 
                         $DatiClienteRitenute[$i]->Ritenute[$k]->RitenutaCertificata = 0;
                      $RitenutaAccontoCertificata = floatval($DatiClienteRitenuteAcconto[$h]['RITENUTA_ACCONTO']) / 100;
                      $DatiClienteRitenute[$i]->Ritenute[$k]->RitenutaCertificata += $RitenutaAccontoCertificata;
                     }
                   }
                 }
               }
            }

          $QueryDatiClienti = "SELECT anagrafiche.CHIAVE,
                                        anagrafiche.RAGIONE_SOCIALE,
                                        anagrafiche.CODICE
                                   FROM anagrafiche
                                   WHERE anagrafiche.CHIAVE IN ($Chiavi)";

            $DatiClienti = array();
            
            if($Query = $PDODBase->query($QueryDatiClienti))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                  array_push($DatiClienti, $Row);
              }
            }

            for($i = 0; $i < count($DatiClienteRitenute); $i++)
              for($j = 0; $j < count($DatiClienti); $j++)
              {
                if($DatiClienteRitenute[$i]->IdCliente == $DatiClienti[$j]['CHIAVE'])
                {
                  $DatiClienteRitenute[$i]->RagioneSociale = $DatiClienti[$j]['RAGIONE_SOCIALE'];
                  $DatiClienteRitenute[$i]->CodiceCliente  = $DatiClienti[$j]['CODICE'];
                }
              }

            for($i = 0; $i < count($DatiClienteRitenute); $i++)
            {
              usort($DatiClienteRitenute[$i]->Ritenute, function($a, $b) {return strcmp($b->IdentificativoFiscale, $a->IdentificativoFiscale);});
            }

            $Result->BAND_INTESTAZIONE = array();
				    $DatiIntestazione = new TDatiIntestazione();
            TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
            $DatiIntestazione->LB_TITOLO = 'Resoconto ritenute clienti - Anno ' . $Parametri->AnnoRitenuta;
            array_push($Result->BAND_INTESTAZIONE, $DatiIntestazione);

            $Result->BAND_RESOCONTO_RITENUTE = array();

            $this->FOrdinamentoEGestioneDatiClienti($DatiClienteRitenute);

            for($i = 0; $i < count($DatiClienteRitenute); $i++)
            {
              for($j = 0; $j < count($DatiClienteRitenute[$i]->Ritenute); $j++)
              {
                $DatiRitenute = new TDatiRitenute();
                $DatiRitenute->IdCliente = $DatiClienteRitenute[$i]->IdCliente;
                $DatiRitenute->IdentificativoFiscale = $DatiClienteRitenute[$i]->Ritenute[$j]->IdentificativoFiscale;
  
                $DatiRitenute->LB_NOME_CLIENTE = $DatiClienteRitenute[$i]->CodiceCliente? 
                                                 $DatiClienteRitenute[$i]->CodiceCliente . ' - ' . $DatiClienteRitenute[$i]->RagioneSociale . ' - ' . $DatiClienteRitenute[$i]->Ritenute[$j]->IdentificativoFiscale : 
                                                 $DatiClienteRitenute[$i]->RagioneSociale . ' - ' . $DatiClienteRitenute[$i]->Ritenute[$j]->IdentificativoFiscale;
                
                $DatiRitenute->LB_ANNO = 'Anno: ' . $DatiClienteRitenute[$i]->Ritenute[$j]->Anno;

                $DatiRitenute->LB_TOTALE_RITENUTE_INCASSATE = $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenuta;
                $RigaFooter->LB_TOT_RITENUTE += $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenuta;

                if(isset($DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata) || isset($DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata))
                {
                  $SommaRitenutaCertificata = 0;
                  if(isset($DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata))
                  {
                    $SommaRitenutaCertificata  += $DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata;
                    $RigaFooter->LB_TOT_CERTIF += $DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata;
                  }
                  // if(isset($DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenutaDiFattureNonPagateMaCertificate))
                  // {
                  //   $SommaRitenutaCertificata  += $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenutaDiFattureNonPagateMaCertificate;
                  //   $RigaFooter->LB_TOT_CERTIF += $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenutaDiFattureNonPagateMaCertificate;
                  // }
                  
                  $DatiRitenute->LB_RITENUTE_CERTIFICATE = EuropeanCurrencyFormat($SommaRitenutaCertificata);
                }
                else $DatiRitenute->LB_RITENUTE_CERTIFICATE = EuropeanCurrencyFormat(0);

                if(isset($DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata))
                {
                  $DifferenzaRitenute = $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenuta - $DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata;
                  if($DifferenzaRitenute > 0)
                    $RigaFooter->LB_TOTALE_DA_CERTIF += number_format($DifferenzaRitenute, 2); //_dg_ NON sostituire con EuropeanCurrencyFormat(.) poiché col += fa casino
                }
                else 
                {
                  $DifferenzaRitenute = $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenuta;
                  $RigaFooter->LB_TOTALE_DA_CERTIF += number_format($DifferenzaRitenute, 2); //_dg_ NON sostituire con EuropeanCurrencyFormat(.) poiché col += fa casino
                }

                for($h = 0; $h < count($DatiClienteRitenuteAcconto); $h++) 
                {
                    if($DatiClienteRitenute[$i]->IdCliente == $DatiClienteRitenuteAcconto[$h]['ID_CLIENTE'] && 
                       $DatiClienteRitenuteAcconto[$h]['RITENUTA_ACCONTO_CERTIFICATA'] == 'F') 
                    {
                        for($k = 0; $k < count($DatiClienteRitenute[$i]->Ritenute); $k++)
                        {
                            $AnnoAcconto = date('Y', strtotime($DatiClienteRitenuteAcconto[$h]['ANNO_RITENUTA_ACCONTO']));
                            if($DatiClienteRitenute[$i]->Ritenute[$k]->Anno == $AnnoAcconto)
                            {
                                $RitenutaAcconto = floatval($DatiClienteRitenuteAcconto[$h]['RITENUTA_ACCONTO']) / 100;
                                $DifferenzaRitenute += $RitenutaAcconto;
                                $RigaFooter->LB_TOTALE_DA_CERTIF += number_format($RitenutaAcconto, 2); 
                                $DatiRitenute->LB_TOTALE_RITENUTE_INCASSATE += $RitenutaAcconto;
                                $RigaFooter->LB_TOT_RITENUTE += $RitenutaAcconto;
                            }
                        }
                    }  

                    if($DatiClienteRitenute[$i]->IdCliente == $DatiClienteRitenuteAcconto[$h]['ID_CLIENTE'] && 
                        $DatiClienteRitenuteAcconto[$h]['RITENUTA_ACCONTO_CERTIFICATA'] == 'T') 
                    {
                     for($k = 0; $k < count($DatiClienteRitenute[$i]->Ritenute); $k++)
                     {
                         $AnnoAcconto = date('Y', strtotime($DatiClienteRitenuteAcconto[$h]['ANNO_RITENUTA_ACCONTO']));
                         if($DatiClienteRitenute[$i]->Ritenute[$k]->Anno == $AnnoAcconto)
                         {
                             $RitenutaAccontoCertificata = floatval($DatiClienteRitenuteAcconto[$h]['RITENUTA_ACCONTO']) / 100;
                             $DatiRitenute->LB_TOTALE_RITENUTE_INCASSATE += $RitenutaAccontoCertificata;
                             $RigaFooter->LB_TOT_RITENUTE += $RitenutaAccontoCertificata;
                         }
                     }
                    }  
                }
                
                $DatiRitenute->LB_TOTALE_RITENUTE_INCASSATE = EuropeanCurrencyFormat($DatiRitenute->LB_TOTALE_RITENUTE_INCASSATE);
                
                if($DifferenzaRitenute < 0.01) 
                   $DatiRitenute->LB_TOTALE_DIFF_DA_CERTIF = 'Pagata';
                else
                  $DatiRitenute->LB_TOTALE_DIFF_DA_CERTIF = EuropeanCurrencyFormat($DifferenzaRitenute);
                
                array_push($Result->BAND_RESOCONTO_RITENUTE, $DatiRitenute);
              } 
            }
           
            $RigaFooter->LB_TOT_RITENUTE     = EuropeanCurrencyFormat($RigaFooter->LB_TOT_RITENUTE);
            $RigaFooter->LB_TOT_CERTIF       = EuropeanCurrencyFormat($RigaFooter->LB_TOT_CERTIF);
            $RigaFooter->LB_TOTALE_DA_CERTIF = EuropeanCurrencyFormat($RigaFooter->LB_TOTALE_DA_CERTIF);
            array_push($Result->BAND_GROUP_FOOTER, $RigaFooter);

            if($NomeLogo != '')
              $DatiIntestazione->QR_LOGO = NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg'; 

            return $Result;
        }

        private function FOrdinamentoEGestioneDatiClienti(&$DatiClienteRitenute)
        {
          // ELIMINO I CLIENTI SENZA RITENUTA
          for($i = 0; $i < count($DatiClienteRitenute); $i++)
          {
            $TotaleRitenuteCliente = 0;

            for($j = 0; $j < count($DatiClienteRitenute[$i]->Ritenute); $j++)
            {
              $TotaleRitenuteCliente += $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenuta;
            }
            
            if($TotaleRitenuteCliente == 0)
            {
              array_splice($DatiClienteRitenute, $i, 1);
              $i--;
            }
          }

          // ORDINO PER CODICE CLIENTE
          usort($DatiClienteRitenute, function($a, $b) 
          {
            if($a->CodiceCliente > $b->CodiceCliente)
              return 1;
            if($a->CodiceCliente < $b->CodiceCliente)
              return -1;
            return 0;
          });
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


            $Report = new TReportResocontoRitenute();
            $Report->LoadFromFile('ModelliStampe/QrStampaResocontoRitenutaClienti.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      

      $AConnection = new TExtraStampaResocontoRitenutaClienti();
      $AConnection->ServerSideScript();      
