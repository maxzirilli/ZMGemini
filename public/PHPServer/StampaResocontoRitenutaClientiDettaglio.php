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

      const LB_PAGATA = "Pagata";

      class TStampaResocontoRitenuteClienti
      {
        public $BAND_INTESTAZIONE       = null;
        public $BAND_GROUP_FOOTER       = null;
        public $BAND_RESOCONTO_RITENUTE = null;
      }

      class TRigaStampa
      {
        public $IdCliente = null;            
        public $LB_DESCRIZIONE = null;
        public $LB_INCASSATE      = null;
        public $LB_CERTIFICATE    = '';
        public $LB_DA_CERTIFICARE = '';
        public $IdentificativoFiscale = null;
      }

      class TVoceRitenuta
      {
        public $LB_VOCI_RITENUTE = null;
        public $LB_TOT_SINGOLA_VOCE = null;
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

      class TRiga 
      {
        public $Chiave          = -1;
        public $Descrizione     = '';
        public $NumeroDocumento = '';
        public $Anno            = '';
        public $Importo         = 0;
        public $IsRitenuta      = false;
        public $Certificata     = false;
        public $ChiaveCliente   = -1;

        function __construct($Chiave, $Descrizione, $NumeroDocumento, $Anno, $Importo, $IsRitenuta, $Certificata, $ChiaveCliente)
        {
          $this->Chiave           = $Chiave; 
          $this->Descrizione      = $Descrizione;
          $this->NumeroDocumento  = $NumeroDocumento;
          $this->Anno             = $Anno;       
          $this->Importo          = (float) $Importo;
          $this->IsRitenuta       = $IsRitenuta;
          $this->Certificata        = $Certificata;
          $this->ChiaveCliente    = $ChiaveCliente;
        }
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
              if(isset($ASingleRecord->IdCliente))
              {
                $ABand->Color = '#ECF5FF';
              }
              else 
              {
                $ABand->Color = '#F9FCFF';
              }

              $TmpObject = $this->GetObjectFromBand($ABand,"LB_DA_CERTIFICARE");
              $Cambiato = false;
              if($TmpObject->Name == "LB_DA_CERTIFICARE")
                if($ASingleRecord->LB_DA_CERTIFICARE != '' && $ASingleRecord->LB_DA_CERTIFICARE != LB_PAGATA)
                {
                  $TmpObject->FontColor = 'red';
                  $Cambiato = true;
                }
              
              if(!$Cambiato)
                $TmpObject->FontColor = 'WindowText';
            }

            if($ABand->Name == 'BAND_GROUP_FOOTER')
              if($ASingleRecord->LB_TOTALE_DA_CERTIF != '' && $ASingleRecord->LB_TOTALE_DA_CERTIF != EuropeanCurrencyFormat(0))
              {
                $TmpObject = $this->GetObjectFromBand($ABand,"LB_TOTALE_DA_CERTIF");
                $TmpObject->FontColor = 'red';
              }
         }
      }

      class TExtraStampaResocontoRitenutaClienti extends TAdvQuery
      {  
        public $TotaleResocontoFatture = 0;
        public $TotaleResocontoFattureRitenute = 0;
        private $FRigheRitenuta  = array();
        private $FRigheRitenuteAggiuntive  = array();
        
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


          // FILTRO CLIENTI
          $AConnection         = new TAdvQuerySelectClienteFiltro($Parametri, true, true);
          $Risposta            = $AConnection->ServerSideScript(false);
          $ListaClienti        = $Risposta->ListaClienti; 

          $ChiaviClienti = array();

          if(count($ListaClienti) != 0)
          {
            foreach($ListaClienti as $Cliente) 
            {
            if(isset($Cliente->CHIAVE))
              $ChiaviClienti[] = $Cliente->CHIAVE;
            }
            
            $Chiavi = implode(',', $ChiaviClienti);
          }
          else $Chiavi = "";


          // OTTENGO LE RITENUTE PER ANNO DEI CLIENTI FILTRATI
          $DatiClienteRitenute = array();
          if($Chiavi != "")
            $DatiClienteRitenute = TSystemInformation::GetRitenutaClienteXAnno($PDODBase, $Chiavi, $Parametri->AnnoRitenuta);




          // OTTENGO LE RITENUTE CERTIFICATE
          $DatiClienteRitenuteCertif = array();
          if($Chiavi != "")
          {
            $QueryRitenuteCertificate = "SELECT ritenute_cliente.RITENUTA AS RITENUTA_CERTIFICATA,
                                                anagrafiche.RAGIONE_SOCIALE,
                                                anagrafiche.CODICE,
                                                ritenute_cliente.ID_CLIENTE,
                                                ritenute_cliente.ANNO AS ANNO_RITENUTA_CERTIF,
                                                ritenute_cliente.IDENTIFICATIVO_FISCALE 
                                           FROM ritenute_cliente
                                                JOIN anagrafiche ON anagrafiche.CHIAVE = ritenute_cliente.ID_CLIENTE
                                          WHERE ritenute_cliente.ID_CLIENTE IN ($Chiavi)";

            if($Query = $PDODBase->query($QueryRitenuteCertificate))
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                array_push($DatiClienteRitenuteCertif, $Row);

          }


          // OTTENGO LE RITENUTE DELLE FATTURE
          $DatiFattureClienteRitenute = array();
          if($Chiavi != "")
            $DatiFattureClienteRitenute = TSystemInformation::GetRitenutaClienteXAnno($PDODBase, $Chiavi, $Parametri->AnnoRitenuta, true);

          for($i = 0; $i < count($DatiFattureClienteRitenute); $i++)
          {

            $Anno = $Parametri->AnnoRitenuta;
            if( is_array($DatiFattureClienteRitenute[$i]->SommaRitenutaXAnno) &&
                isset($DatiFattureClienteRitenute[$i]->SommaRitenutaXAnno[$Anno]) &&
                $DatiFattureClienteRitenute[$i]->SommaRitenutaXAnno[$Anno] > 0)
            {

              $ImportoRitenuta = $DatiFattureClienteRitenute[$i]->SommaRitenutaXAnno[$Anno];

              $this->FAddNewRiga(
                                    $DatiFattureClienteRitenute[$i]->IdFattura,
                                    'Ritenuta della fattura n. ' . $DatiFattureClienteRitenute[$i]->Numero, 
                                    $DatiFattureClienteRitenute[$i]->Numero, 
                                    $DatiFattureClienteRitenute[$i]->Anno, 
                                    $ImportoRitenuta,
                                    true,
                                    false,
                                    $DatiFattureClienteRitenute[$i]->IdCliente
                                );
            }
          }




          // OTTENGO LE RITENUTE AGGIUNTIVE
          $DatiClienteRitenuteAcconto = array();
          if($Chiavi != "")
          {
            $QueryRitenuteAcconto = "SELECT ritenute_acconto_clienti.RITENUTE AS RITENUTA_ACCONTO,
                                            anagrafiche.RAGIONE_SOCIALE,
                                            anagrafiche.CODICE,
                                            ritenute_acconto_clienti.ID_CLIENTE,
                                            ritenute_acconto_clienti.CERTIFICATA AS RITENUTA_ACCONTO_CERTIFICATA,
                                            ritenute_acconto_clienti.DATA AS ANNO_RITENUTA_ACCONTO,
                                            ritenute_acconto_clienti.NOTE AS NOTA_RITENUTA
                                        FROM ritenute_acconto_clienti
                                            JOIN anagrafiche ON anagrafiche.CHIAVE = ritenute_acconto_clienti.ID_CLIENTE
                                      WHERE YEAR(ritenute_acconto_clienti.DATA) = $Parametri->AnnoRitenuta
                                            AND ritenute_acconto_clienti.ID_CLIENTE IN ($Chiavi)";


            if ($Query = $PDODBase->query($QueryRitenuteAcconto))
            {
              while ($Row = $Query->fetch(PDO::FETCH_ASSOC))
              {
                array_push($DatiClienteRitenuteAcconto, $Row);

                $Nota         = trim((string)$Row['NOTA_RITENUTA']);
                $Descrizione  = 'Rit. agg.';

                if (trim((string)$Row['NOTA_RITENUTA']) != '')
                  $Descrizione .= ' - ' . $Nota;

                $this->FAddNewRiga(
                                    -1,
                                    $Descrizione,
                                    '',
                                    '',
                                    $Row['RITENUTA_ACCONTO'],
                                    false,
                                    strtoupper(trim($Row['RITENUTA_ACCONTO_CERTIFICATA'])) == 'T',
                                    $Row['ID_CLIENTE']
                                  );
              }
            }
          }



          // INSERISCO UN CLIENTE SE NON HA FATTURE CON RITENUTE MA HA RITENUTE AGGIUNTIVE
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


          // INSERISCO NEL RELATIVO CLIENTE LA SUA RITENUTA CERTIFICATA
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


          // OTTENGO RAGIONE SOCIALE E CODICE CLIENTE
          if($Chiavi != "")
          {
            $QueryDatiClienti = "SELECT  anagrafiche.CHIAVE,
                                        anagrafiche.RAGIONE_SOCIALE,
                                        anagrafiche.CODICE
                                  FROM  anagrafiche
                                  WHERE  anagrafiche.CHIAVE IN ($Chiavi)";

            
            if($Query = $PDODBase->query($QueryDatiClienti))
            {
              while($Row = $Query->fetch(PDO::FETCH_ASSOC))
              { 
                for($i = 0; $i < count($DatiClienteRitenute); $i++)
                  if($DatiClienteRitenute[$i]->IdCliente == $Row['CHIAVE'])
                  {
                    $DatiClienteRitenute[$i]->RagioneSociale = $Row['RAGIONE_SOCIALE'];
                    $DatiClienteRitenute[$i]->CodiceCliente  = $Row['CODICE'];
                  }
              }
            }
          }

          

          // ORDINO LE RITENUTE PER IDENTIFICATIVO FISCALE
          for($i = 0; $i < count($DatiClienteRitenute); $i++)
            usort($DatiClienteRitenute[$i]->Ritenute, function($a, $b) {return strcmp($b->IdentificativoFiscale, $a->IdentificativoFiscale);});
          




          // INSERISCO I RISULTATI NELL'OGGETTO DELLA STAMPA
          $Result->BAND_INTESTAZIONE = array();
          $DatiIntestazione = new TDatiIntestazione();
          TSystemInformation::GetDatiIntestazione($PDODBase, $DatiIntestazione);
          $DatiIntestazione->LB_TITOLO = 'Resoconto ritenute clienti - Anno ' . $Parametri->AnnoRitenuta;
          
          if(isset($Parametri->CercaRitenutePagate) && !$Parametri->CercaRitenutePagate)
            $DatiIntestazione->LB_TITOLO .= " - Non certificate";
          if(isset($Parametri->CercaRitenutePagate) && $Parametri->CercaRitenutePagate)
            $DatiIntestazione->LB_TITOLO .= " - Pagate";
           
          array_push($Result->BAND_INTESTAZIONE, $DatiIntestazione);

          $Result->BAND_RESOCONTO_RITENUTE = array();




          //ORDINO E CANCELLO I CLIENTI SENZA RITENUTA E SENZA RIT CERTIFICATA
          $this->FOrdinamentoEGestioneDatiClienti($DatiClienteRitenute);





          for($i = 0; $i < count($DatiClienteRitenute); $i++)
          {
            for($j = 0; $j < count($DatiClienteRitenute[$i]->Ritenute); $j++)
            {
              if($DatiClienteRitenute[$i]->Ritenute[$j]->Anno == $Parametri->AnnoRitenuta)
              {
                $RigaCliente                          = new TRigaStampa();
                $RigaCliente->IdCliente              = $DatiClienteRitenute[$i]->IdCliente;
                $RigaCliente->IdentificativoFiscale  = $DatiClienteRitenute[$i]->Ritenute[$j]->IdentificativoFiscale;
               
               
                $RigaCliente->LB_DESCRIZIONE         = ($DatiClienteRitenute[$i]->CodiceCliente) ?
                                                        $DatiClienteRitenute[$i]->CodiceCliente . ' - ' . $DatiClienteRitenute[$i]->RagioneSociale :
                                                        $DatiClienteRitenute[$i]->RagioneSociale;
                if($DatiClienteRitenute[$i]->Ritenute[$j]->IdentificativoFiscale != '')
                  $RigaCliente->LB_DESCRIZIONE .= ' - ' . $DatiClienteRitenute[$i]->Ritenute[$j]->IdentificativoFiscale;

               
                $RigaCliente->LB_INCASSATE           = '';


                array_push($Result->BAND_RESOCONTO_RITENUTE, $RigaCliente);
                

                $Trovato = $this->FCheckCliente($DatiClienteRitenute[$i]->IdCliente);
                
                if($Trovato)
                {
                  foreach($this->FRigheRitenuta as $Riga)
                  {
                    
                    if($Riga->ChiaveCliente == $DatiClienteRitenute[$i]->IdCliente)
                    { 
                      $RigaVoce     = new TRigaStampa();

                      $RigaVoce->LB_DESCRIZIONE   = $Riga->Descrizione;

                      if($Riga->IsRitenuta == false)
                      {
                        $ImportoTmp = floatval($Riga->Importo) / 100;

                        if($Riga->Certificata == true)
                        {
                          $RigaVoce->LB_INCASSATE   = EuropeanCurrencyFormat($ImportoTmp);

                          $RigaVoce->LB_CERTIFICATE = EuropeanCurrencyFormat($ImportoTmp);
                        }
                        else
                        {
                          $RigaVoce->LB_INCASSATE     = EuropeanCurrencyFormat($ImportoTmp);
                        }
                      }
                      else $RigaVoce->LB_INCASSATE     = EuropeanCurrencyFormat($Riga->Importo);

                      array_push($Result->BAND_RESOCONTO_RITENUTE, $RigaVoce);
                    }
                  }
                }

                $DatiRitenute     = new TRigaStampa();

                $DatiRitenute->LB_DESCRIZIONE    = 'Anno: ' . $DatiClienteRitenute[$i]->Ritenute[$j]->Anno;

                $DatiRitenute->LB_INCASSATE = $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenuta;
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
                  
                  $DatiRitenute->LB_CERTIFICATE    = EuropeanCurrencyFormat($SommaRitenutaCertificata);
                }
                else $DatiRitenute->LB_CERTIFICATE = EuropeanCurrencyFormat(0);

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
                                $DatiRitenute->LB_INCASSATE += $RitenutaAcconto;
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
                            $DatiRitenute->LB_INCASSATE += $RitenutaAccontoCertificata;
                            $RigaFooter->LB_TOT_RITENUTE += $RitenutaAccontoCertificata;
                        }
                    }
                    }  
                }
                
                $DatiRitenute->LB_INCASSATE = EuropeanCurrencyFormat($DatiRitenute->LB_INCASSATE);
                
                if($DifferenzaRitenute < 0.01) 
                  $DatiRitenute->LB_DA_CERTIFICARE = LB_PAGATA;
                else
                  $DatiRitenute->LB_DA_CERTIFICARE = EuropeanCurrencyFormat($DifferenzaRitenute);
                  
                array_push($Result->BAND_RESOCONTO_RITENUTE, $DatiRitenute);
              }
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
            $TotaleRitenuteCertificataCliente = 0;

            for($j = 0; $j < count($DatiClienteRitenute[$i]->Ritenute); $j++)
            {
              $TotaleRitenuteCliente            += $DatiClienteRitenute[$i]->Ritenute[$j]->SommaRitenuta;
              $TotaleRitenuteCertificataCliente += $DatiClienteRitenute[$i]->Ritenute[$j]->RitenutaCertificata;
            }
            
            if($TotaleRitenuteCliente == 0 && $TotaleRitenuteCertificataCliente == 0)
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

        private function FAddNewRiga($Chiave, $Descrizione, $NumeroDocumento, $Anno, $Importo, $IsRitenuta = false, $Certificata = false, $ChiaveCliente)
        {
          for($i = 0; $i < count($this->FRigheRitenuta); $i++)
          {
              if($this->FRigheRitenuta[$i]->Chiave == $Chiave && $this->FRigheRitenuta[$i]->Descrizione == $Descrizione 
                  && $this->FRigheRitenuta[$i]->ChiaveCliente == $ChiaveCliente && $this->FRigheRitenuta[$i]->Importo == $Importo)
                return $this->FRigheRitenuta[$i];
          }

          $NuovaRiga = new TRiga($Chiave, $Descrizione, $NumeroDocumento, $Anno, $Importo, $IsRitenuta, $Certificata, $ChiaveCliente);

          array_push($this->FRigheRitenuta, $NuovaRiga);
          
          return $NuovaRiga;
        }

        private function FCheckCliente($Chiave)
        {
          for($i = 0; $i < count($this->FRigheRitenuta); $i++)
          {
            if($this->FRigheRitenuta[$i]->ChiaveCliente == $Chiave)
              return true;
          }
          return false;
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
            $Report->LoadFromFile('ModelliStampe/QrStampaDettaglioRitenutaClienti.json');
            $JSONAnswer->PDF = base64_encode($Report->GetPDF($this->FGetDatiStampa($Parametri, $PDODBase, $JSONAnswer, $NomeLogo))); 

            if($NomeLogo != '')
               unlink(NOME_CARTELLA_FILE_TEMP . '/' . $NomeLogo . '.jpg');  
        }
      }
      
      $AConnection = new TExtraStampaResocontoRitenutaClienti();
      $AConnection->ServerSideScript();      
