<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      const RITENUTA_PAGATA          = 1;
      const RITENUTA_NON_PAGATA      = 2;
      const RITENUTA_NON_PRESENTE    = 3;
      const MAX_CLIENTI_VISUALIZZATI = 5;

      class TObjectRow
      {
            public $FieldName            = null;
            public $Ritenute             = null;
            public $Iniziale             = null;
            public $CHIAVE               = null;
            public $RAGIONE_SOCIALE      = null;
            public $ATTIVO               = null;
            public $CLIENTE_CANCELLABILE = null;
            public $PASSATA_AD_AVVOCATO  = null;
      }

      class TObjectRitenuta
      {
            public $RitenutaCertificata   = null;
            public $Anno                  = null;
            public $SommaRitenuta         = null;
            public $IdentificativoFiscale = null;  
            public $SommaRitenutaDiFattureNonPagateMaCertificate = null;
      } 

      class TAdvQuerySelectClienteFiltro extends TAdvQuery
      {     
          private $Parametri       = null;
          private $GetListaClienti = false;

          public function __construct($Parametri, $GetListaClienti = false, $ScriptEsterno = false)
          {
            parent::__construct($ScriptEsterno);

            if(isset($Parametri))
              $this->Parametri    = $Parametri;
            else $this->Parametri = json_decode($_POST['Params']);

            $this->GetListaClienti = $GetListaClienti;
          } 

          protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
          {
              $TempListaClienti = array();
              
              $CercaIniziali = isset($this->Parametri->CercaRitenutePagate) || 
                               isset($this->Parametri->LunghezzaSottostringa) || 
                               isset($this->Parametri->CodiceCliente) ||
                               $this->GetListaClienti;
                               


              $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                  'Clienti',
                                                  $CercaIniziali ? 'SelectClientiXFiltro' : 'SelectClientiXFiltroIniziale',
                                                  $CercaIniziali ? 'SelectClientiXFiltro' : 'SelectClientiXFiltroIniziale',
                                                  get_object_vars($this->Parametri));


              if($Query = $PDODBase->query($SQLBody))
                  while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                  { 
                        $ObjRow = new TObjectRow();
                        foreach($Row as $FieldName => $FieldValue)
                        {
                           if(!is_numeric($FieldName) && !is_null($FieldValue))
                              $ObjRow->$FieldName = $FieldValue;
                           $ObjRow->Ritenute = array();
                        }
                        array_push($TempListaClienti,$ObjRow);
                  }


              if(isset($this->Parametri->CercaRitenutePagate))
              {
                  $ListaRitenutePagateClienti = array();
                  $ListaRitenuteClienti = array();

                  $QueryPart = explode('FROM anagrafiche',
                                       $this->FGetQueryCompiled($PDODBase,
                                                                'Clienti', 
                                                                'SelectClientiXFiltro',
                                                                'SelectClientiXFiltro', 
                                                                get_object_vars($this->Parametri)));

                  $QueryRitenute = 'SELECT * 
                                      FROM ritenute_cliente 
                                     WHERE ritenute_cliente.ANNO = ' . $this->Parametri->AnnoRitenuta .'
                                       AND ritenute_cliente.ID_CLIENTE IN ( SELECT anagrafiche.CHIAVE 
												      FROM anagrafiche ' . $QueryPart[count($QueryPart) - 1] . ')
                                     ORDER BY ID_CLIENTE';



                  $ObjectCliente = null;
                  $LastCliente   = -1;
                  if($Query = $PDODBase->query($QueryRitenute))
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    {
                        if($LastCliente != $Row['ID_CLIENTE'])
                        {
                              $ObjectCliente = $this->FGetOggettoClienteDaLista($TempListaClienti, $Row['ID_CLIENTE']);
                              $LastCliente = $Row['ID_CLIENTE'];
                        }
                        $OggettoRitenuta = new TObjectRitenuta();
                        $OggettoRitenuta->Anno = $Row['ANNO'];
                        $OggettoRitenuta->RitenutaCertificata = $Row['RITENUTA'] / 100;
                        $OggettoRitenuta->IdentificativoFiscale = $Row['IDENTIFICATIVO_FISCALE'];
                        array_push($ObjectCliente->Ritenute, $OggettoRitenuta);
                    }

                  $QueryClienti = 'SELECT anagrafiche.CHIAVE FROM anagrafiche' . $QueryPart[count($QueryPart) - 1];                  
                  
                  $ListaRitenuteClienti = TSystemInformation::GetRitenutaClienteXAnno($PDODBase,$QueryClienti,$this->Parametri->AnnoRitenuta);
                  

                  $ObjectCliente = null;
                  for($i = 0; $i < count($ListaRitenuteClienti); $i++)
                  {
                        $ObjectCliente = $this->FGetOggettoClienteDaLista($TempListaClienti, $ListaRitenuteClienti[$i]->IdCliente);

                        for($j = 0; $j < count($ListaRitenuteClienti[$i]->Ritenute); $j++ )
                        {
                              $Trovato = false;
                              for($k = 0; $k < count($ObjectCliente->Ritenute); $k++ )
                              {
                                    if($ObjectCliente->Ritenute[$k]->IdentificativoFiscale == $ListaRitenuteClienti[$i]->Ritenute[$j]->IdentificativoFiscale)
                                    {
                                          if($ObjectCliente->Ritenute[$k]->Anno == $ListaRitenuteClienti[$i]->Ritenute[$j]->Anno)
                                          {
                                                $Trovato = true;
                                                $ObjectCliente->Ritenute[$k]->SommaRitenuta = $ListaRitenuteClienti[$i]->Ritenute[$j]->SommaRitenuta;
                                                $ObjectCliente->Ritenute[$k]->SommaRitenutaDiFattureNonPagateMaCertificate = $ListaRitenuteClienti[$i]->Ritenute[$j]->SommaRitenutaDiFattureNonPagateMaCertificate;
                                          }
                                    }
                              }
                              if(!$Trovato)
                              {
                                    $AggiungiRitenuta = new TObjectRitenuta();
                                    $AggiungiRitenuta->RitenutaCertificata = 0;
                                    $AggiungiRitenuta->Anno = $ListaRitenuteClienti[$i]->Ritenute[$j]->Anno;
                                    $AggiungiRitenuta->SommaRitenuta = $ListaRitenuteClienti[$i]->Ritenute[$j]->SommaRitenuta;
                                    $AggiungiRitenuta->SommaRitenutaDiFattureNonPagateMaCertificate = $ListaRitenuteClienti[$i]->Ritenute[$j]->SommaRitenutaDiFattureNonPagateMaCertificate;
                                    $AggiungiRitenuta->IdentificativoFiscale = $ListaRitenuteClienti[$i]->Ritenute[$j]->IdentificativoFiscale;                                    
                                    if(!isset($ObjectCliente->Ritenute))
                                       $ObjectCliente->Ritenute = array();
                                    array_push($ObjectCliente->Ritenute, $AggiungiRitenuta);
                              }
                        }
                  }


                  $Risultato = array();
                  for ($i = 0; $i < count($TempListaClienti); $i++)
                  {
                        if(!$this->Parametri->CercaRitenutePagate)
                        {
                              if($this->FClienteDaEliminare($TempListaClienti[$i], $this->Parametri,$this->Parametri->AnnoRitenuta, $PDODBase) == RITENUTA_NON_PAGATA)
                                    array_push($Risultato,$TempListaClienti[$i]);
                        }
                        else 
                        {
                              if($this->FClienteDaEliminare($TempListaClienti[$i], $this->Parametri,$this->Parametri->AnnoRitenuta, $PDODBase) == RITENUTA_PAGATA)
                                    array_push($Risultato,$TempListaClienti[$i]);
                        }

                  }

                  $TempListaClienti = $Risultato;
              }

              if(!$CercaIniziali)
                  $JSONAnswer->PrimaLetteraCliente = true;
            
              $JSONAnswer->ListaClienti = $TempListaClienti;
            }



            private function FClienteDaEliminare($Cliente, $Parametri, $AnnoRitenuta, $PDODBase)
            {
                  $Pagata = RITENUTA_PAGATA;

                  if(!empty($Cliente->Ritenute))
                  {
                     for($i = 0; $i < count($Cliente->Ritenute); $i++)
                        if($Cliente->Ritenute[$i]->Anno == $AnnoRitenuta)
                           if($Cliente->Ritenute[$i]->RitenutaCertificata < $Cliente->Ritenute[$i]->SommaRitenuta - 0.01)
                           {
                              $Pagata = RITENUTA_NON_PAGATA;
                              break;
                           }
                  }
                  else $Pagata = RITENUTA_NON_PRESENTE;

                  if($Pagata == RITENUTA_PAGATA || $Pagata == RITENUTA_NON_PRESENTE)
                  {
                        $ControlloSecondaTabella = $this->FControlloRitenuteAggiuntive($PDODBase, $Cliente, $AnnoRitenuta);
                        if($Pagata == RITENUTA_NON_PRESENTE)
                              $Pagata = $ControlloSecondaTabella;
                        else
                        {
                              if($Pagata == RITENUTA_PAGATA && $ControlloSecondaTabella != RITENUTA_NON_PRESENTE)
                                    $Pagata = $ControlloSecondaTabella;
                        }
                  }

                  return $Pagata;                  
            }

            private function FControlloRitenuteAggiuntive($PDODBase, $Cliente, $AnnoRitenuta)
            {
                  $QueryRitenute = 'SELECT * 
                                      FROM ritenute_acconto_clienti 
                                     WHERE YEAR(ritenute_acconto_clienti.DATA) = ' . $AnnoRitenuta .'
                                       AND ritenute_acconto_clienti.ID_CLIENTE = '.  $Cliente->CHIAVE; 

                  $AlmenoUnaRitenutaNonCertificata = false;
                  $RitenutaPresente = false;

                  if($Query = $PDODBase->query($QueryRitenute))
                    while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                    {
                        $RitenutaPresente = true;
                        if($Row['CERTIFICATA'] <> 'T')
                              $AlmenoUnaRitenutaNonCertificata = true;
                    }  

                  if($RitenutaPresente)
                  {
                        if($AlmenoUnaRitenutaNonCertificata)
                              return RITENUTA_NON_PAGATA;
                        return RITENUTA_PAGATA;
                  }
                  return RITENUTA_NON_PRESENTE;
            }

            private function FGetOggettoClienteDaLista($ListaClienti,$ChiaveCliente)
            {     
                  for($i = 0; $i < count($ListaClienti); $i++)
                        if($ListaClienti[$i]->CHIAVE == $ChiaveCliente)
                              return $ListaClienti[$i];
            }

      }

      // $Connection = new TExtraStampaRitenuta();
      // $Connection->ServerSideScript();
?>