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
                                    filiali_clienti.COMUNE,
                                    filiali_clienti.CAP,
                                    province.NOME AS PROVINCIA,
                                    province.TARGA AS TARGA_PROVINCIA,
                                    nazioni.NOME AS NAZIONE
                               FROM filiali_clienti
                               LEFT JOIN province ON province.CHIAVE = filiali_clienti.PROVINCIA
                               LEFT JOIN nazioni ON nazioni.CHIAVE = filiali_clienti.NAZIONE
                              WHERE (filiali_clienti.LAT_IND  IS NULL OR filiali_clienti.LAT_IND  = 0)
                                AND (filiali_clienti.LONG_IND IS NULL OR filiali_clienti.LONG_IND = 0)
                           ORDER BY filiali_clienti.CHIAVE";
            
            $CurlInit = curl_init();
            curl_setopt($CurlInit, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($CurlInit, CURLOPT_USERAGENT, $UserAgent);
            curl_setopt($CurlInit, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($CurlInit, CURLOPT_TIMEOUT, 20);

            try
            {
                $QueryTabelle = $PDODBase->query($QuerySelect);
                while($Tabelle = $QueryTabelle->fetch(PDO::FETCH_ASSOC))
                {
                    $Url = $this->FCreaUrlNominatim($Tabelle);
                    if($Url == '')
                       continue;

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

                        $RisultatoValido = $this->FTrovaRisultatoValido($Response, $Tabelle);
                        if($RisultatoValido !== null)
                        {
                            $LatInd  = intval((float)$RisultatoValido->lat * 1000000);
                            $LongInd = intval((float)$RisultatoValido->lon * 1000000);
                        }
                        else
                        {
                            error_log('Nessun risultato Nominatim coerente per la filiale ' . $Chiave);
                        }

                        if($LatInd != 0 && $LongInd != 0)
                        {
                            $QueryUpdate = "UPDATE filiali_clienti SET LAT_IND = $LatInd, LONG_IND = $LongInd WHERE CHIAVE = $Chiave";
                            array_push($JSONAnswer->Risultati, $QueryUpdate);
                            error_log($QueryUpdate);
                            $PDODBase->query($QueryUpdate);
                        }
                    }

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


        private function FCreaUrlNominatim($Tabelle)
        {
            $Indirizzo = trim(($Tabelle['NR_CIVICO'] ?? '') . ' ' . ($Tabelle['INDIRIZZO'] ?? ''));
            if($Indirizzo == '')
               return '';

            $Parametri = array(
                'format'         => 'jsonv2',
                'street'         => $Indirizzo,
                'city'           => trim($Tabelle['COMUNE'] ?? ''),
                'county'         => trim($Tabelle['PROVINCIA'] ?? ''),
                'postalcode'     => trim($Tabelle['CAP'] ?? ''),
                'country'        => trim($Tabelle['NAZIONE'] ?? ''),
                'addressdetails' => 1,
                'limit'          => 10
            );

            if($this->FNormalizzaTesto($Tabelle['NAZIONE'] ?? '') == 'italia')
               $Parametri['countrycodes'] = 'it';

            $Parametri = array_filter($Parametri, function($Valore)
            {
                return $Valore !== '' && $Valore !== null;
            });

            return 'https://nominatim.openstreetmap.org/search?' . http_build_query($Parametri);
        }

        private function FTrovaRisultatoValido($Response, $Tabelle)
        {
            if(!is_array($Response) || count($Response) == 0)
               return null;

            $CAPRichiesto = trim((string)($Tabelle['CAP'] ?? ''));
            $ComuneRichiesto = $this->FNormalizzaTesto($Tabelle['COMUNE'] ?? '');
            $CivicoRichiesto = $this->FNormalizzaTesto($Tabelle['NR_CIVICO'] ?? '');

            $RisultatiValidi = array();

            foreach($Response as $Risultato)
            {
                if(!isset($Risultato->address))
                   continue;

                $Indirizzo = $Risultato->address;
                $CAPRisultato = trim((string)($Indirizzo->postcode ?? ''));
                $CivicoRisultato = $this->FNormalizzaTesto($Indirizzo->house_number ?? '');
                $ComuneRisultato = $this->FNormalizzaTesto(
                    $Indirizzo->city
                    ?? $Indirizzo->town
                    ?? $Indirizzo->village
                    ?? $Indirizzo->municipality
                    ?? ''
                );

                if($CAPRichiesto != '' && $CAPRisultato !== $CAPRichiesto)
                   continue;

                if($ComuneRichiesto != '' && $ComuneRisultato !== $ComuneRichiesto)
                   continue;

                if($CivicoRichiesto != '' && $CivicoRisultato !== $CivicoRichiesto)
                   continue;

                array_push($RisultatiValidi, $Risultato);
            }

            if(count($RisultatiValidi) == 0)
               return null;

            foreach($RisultatiValidi as $Risultato)
            {
                if(($Risultato->category ?? '') === 'place' &&
                   ($Risultato->type ?? '') === 'house')
                   return $Risultato;
            }

            return $RisultatiValidi[0];
        }

        private function FNormalizzaTesto($Valore)
        {
            $Valore = trim((string)$Valore);
            if($Valore == '')
               return '';

            $Valore = mb_strtolower($Valore, 'UTF-8');
            $Valore = str_replace(array('.', ',', "'", 'â€™'), ' ', $Valore);
            $Valore = preg_replace('/\s+/', ' ', $Valore);

            return trim($Valore);
        }
      }

      $AConnection = new TUpdateLatitudineLongitudine();
      $AConnection->ServerSideScript();
