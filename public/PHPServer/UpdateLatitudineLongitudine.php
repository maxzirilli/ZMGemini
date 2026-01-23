<?php       
      include_once 'Configurations.php';
      include_once 'Definitions.php';
      include_once 'SystemInformation.php';
      include_once PATH_LIBRERIE . 'ZAdvQuery.php';
      
      header("Content-Type: application/json;charset=UTF-8");
      header(ACCESS_CONTROLL_SHARED);
      header("Access-Control-Allow-Methods:POST,GET, OPTIONS");  

      class TUpdateLatitudineLongitudine extends TAdvQuery
      {           
        protected function FExtraScriptServerSide($PDODBase,&$JSONAnswer)
        {  
            $QueryUpdate = "";
            $UserAgent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:131.0) Gecko/20100101 Firefox/131.0";
            $JSONAnswer->Risultati = array();
            
            $QuerySelect = " SELECT filiali_clienti.CHIAVE,
                                    filiali_clienti.INDIRIZZO,
                                    filiali_clienti.NR_CIVICO,
                                    filiali_clienti.COMUNE
                               FROM filiali_clienti
                              WHERE filiali_clienti.INDIRIZZO   != ''
                                AND filiali_clienti.INDIRIZZO   IS NOT NULL
                                AND filiali_clienti.LAT_IND     IS NULL
                                AND filiali_clienti.LONG_IND    IS NULL
                           ORDER BY filiali_clienti.CHIAVE";
            
            $Url = "https://nominatim.openstreetmap.org/search.php?q=";
            $CurlInit = curl_init();
            curl_setopt($CurlInit, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($CurlInit, CURLOPT_USERAGENT, $UserAgent);

            try
            {
                $QueryTabelle = $PDODBase->query($QuerySelect);
                while($Tabelle = $QueryTabelle->fetch(PDO::FETCH_ASSOC))
                {
                    $String = $Tabelle['INDIRIZZO'] . " " . (isset($Tabelle['NR_CIVICO'])? $Tabelle['NR_CIVICO'] : '') . " " . (isset($Tabelle['COMUNE'])? $Tabelle['COMUNE'] : '');
                    $Url .= urlencode($String);
                    $Url .= "&format=jsonv2";
                    curl_setopt($CurlInit, CURLOPT_URL, $Url);

                    // Esegui la richiesta GET
                    $Response = curl_exec($CurlInit);
                    if (curl_errno($CurlInit)) 
                    {
                        echo 'Errore cURL: ' . curl_error($CurlInit);
                    }
                    else 
                    {
                        $Chiave  =  $Tabelle['CHIAVE'];
                        $Response = json_decode($Response);
                        $LatInd  = 0;
                        $LongInd = 0;
                        if(isset($Response) && count($Response) != 0)
                        {
                            $LatInd  = intval($Response[0]->lat * 1000000);
                            $LongInd = intval($Response[0]->lon * 1000000);
                        }

                        if($LatInd != 0 && $LongInd != 0)
                        {
                            $QueryUpdate = "UPDATE filiali_clienti SET LAT_IND = $LatInd, LONG_IND = $LongInd WHERE CHIAVE = $Chiave";
                            array_push($JSONAnswer->Risultati, $QueryUpdate);
                            error_log($QueryUpdate);
                            $PDODBase->query($QueryUpdate);
                        }
                    }

                    $Url = "https://nominatim.openstreetmap.org/search.php?q=";
                    sleep(1);
                }
                curl_close($CurlInit);    
            }
            catch(Exception $e)
            {
              error_log($QuerySelect);
              throw new Exception($e->getMessage());         
            } 
        }
      }

      $AConnection = new TUpdateLatitudineLongitudine();
      $AConnection->ServerSideScript();
