<?php 
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
            
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST, OPTIONS"); 
      
      class TAdvQuerySelectFatture extends TAdvQuery
      {
            protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
            {
              $Parametri = JSON_decode($_POST['Params']); 

              $this->FSelectDatiFattura($Parametri, $JSONAnswer, $PDODBase);
            }

            private function FSelectDatiFattura($Parametri, &$JSONAnswer, $PDODBase)
            {
                  $JSONAnswer->ListaFatture = array();
                  $ArrayChiaviFatture       = array();

                  // FUNZIONE CHE OTTIENE LA LISTA DELLE FATTURE
                  if(isset($Parametri->GruppoQuery) && isset($Parametri->Query))
                        $this->FSelectFatture($PDODBase, $Parametri, $JSONAnswer->ListaFatture, $ArrayChiaviFatture, $Parametri->GruppoQuery, $Parametri->Query);
                  else  $this->FSelectQueryFatture($PDODBase, $Parametri, $JSONAnswer->ListaFatture, $ArrayChiaviFatture);

                  // FUNZIONE CHE INSERISCE I TOTALI NELLE FATTURE
                  $this->FInserisciTotaliNelleFatture($PDODBase, $JSONAnswer, $ArrayChiaviFatture);
                  
                  // error_log(json_encode($JSONAnswer));
            }
            
            

            private function FSelectQueryFatture($PDODBase, $Parametri, &$Array, &$ArrayChiaviFatture)
            {
                  $FiltroDaFatturareAttivo = isset($Parametri->DaFatturare) && $Parametri->DaFatturare;
                  $FiltroConformitaAttivo  = isset($Parametri->Conformita)  && $Parametri->Conformita != 'Tutte';

                  if($FiltroConformitaAttivo)
                        $this->FGEstioneFiltroConformitaAttivo($PDODBase, $Parametri, $Array, $ArrayChiaviFatture);
                  else
                  {
                        $this->FSelectFatture($PDODBase, $Parametri, $Array, $ArrayChiaviFatture, 'SelectSQL', 'SelectFatture');
                  }
            }

            private function FSelectFatture($PDODBase, $Parametri, &$Array, &$ArrayChiaviFatture, $GruppoQuery, $Query)
            {
                  $SQLBody = $this->FGetQueryCompiled($PDODBase,
                                                      'Fatture', 
                                                      $GruppoQuery,
                                                      $Query, 
                                                      get_object_vars($Parametri));
            //      error_log($SQLBody);
                  try
                  {
                        if($Query = $PDODBase->query($SQLBody))
                           while($Row = $Query->fetch(PDO::FETCH_ASSOC))
                           { 
                              array_push($Array, $Row);

                              array_push($ArrayChiaviFatture, $Row['CHIAVE']);
                           }
                  }
                  catch(Exception $e)
                  {
                     error_log($SQLBody);
                     throw $e;
                  }
            }

            private function FInserisciTotaliNelleFatture($PDODBase, &$JSONAnswer, $ArrayChiaviFatture)
            {
                  $StringaChiavi = '';
                  foreach($ArrayChiaviFatture as $Chiave)
                        $StringaChiavi .= $Chiave . ',';
                  
                  $StringaChiavi = rtrim($StringaChiavi, ",");

                  if($StringaChiavi != '')
                  {
                        $ArrTotaliFattura = TSystemInformation::GetTotaliFattura($PDODBase, $StringaChiavi);

                        for($i = 0; $i < count($ArrTotaliFattura); $i++)
                        {
                              for($j = 0; $j < count($JSONAnswer->ListaFatture); $j++)
                              {
                                    if($ArrTotaliFattura[$i]->IdFattura == $JSONAnswer->ListaFatture[$j]['CHIAVE'])
                                    {
                                          $JSONAnswer->ListaFatture[$j]['TOTALE_FATTURA'] = $ArrTotaliFattura[$i]->NettoAPagare;
                                          break;
                                    }
                              }
                        }
                  }
            }
      }

      $Connection = new TAdvQuerySelectFatture();
      $Connection->ServerSideScript();
?>