<template>

  <div class="container" style="width:80%">
    <div class="row" style="padding:10px;border-top:1px;border: 1 px solid">
      <div class="col-md-12">
        
        <button v-if="!DownloadInCorso" style="float:right;width:20%" class="btn btn-s-md btn-info" @click="OnClickImportaFatturePassive">Importa</button>
        <p v-else style="float:left;width:60%;color:red;font-size:22px">Operazione in svolgimento. Attendere.</p>
        <!-- <div style="float:right;width:1%">&nbsp;</div>
        <button style="float:right;width:20%" class="btn btn-s-md btn-info" @click="OnClickPopup">Popup</button> -->
      </div>
    </div>
    <div v-if="ListaFatturePassiveScaricate.length != 0" class="content"> <!--  && ImportazioneNonPremuto -->
      <div class="ZMSeparatoreScheda" 
         style="font-weight: bold;clear:both;padding-bottom: 2px;margin-bottom: 5px;background-color:#68b6be">
         Fatture passive importate
      </div>
      <div v-for="Fattura in ListaFatturePassiveScaricate" :key="Fattura.File">
        <div class="ZMCorpoSchedeDati" style="float:left; padding-bottom:0px">
          <div class="panel-group m-b" style="font-size:14px">
            <div class="panel panel-default" style="background-color:#d0e9ff">
              <div class="panel-heading">
                <div style="text-align: center">
                  Fornitore: <b>{{ Fattura.Fornitore }}</b>, Numero fattura: <b>{{ Fattura.NumFattura }}</b> <br /> 
                  <i>{{ Fattura.IsNuovoFornitore ? "Inserita nuova scheda fornitore" : "Aggiornata scheda fornitore" }}</i>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-if="ListaFatturePassiveScaricate.length == 0 && !DownloadInCorso && ImportaPremuto">
      <text style="float:left; margin-left:17px; margin-top:1px;margin-bottom:5px;font-size:14px; font-style:italic; width:30%">Nessuna nuova fattura passiva trovata</text>
    </div>
  </div>
</template>

<script>
import { SystemInformation } from '@/SystemInformation.js'
// import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
// import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
// import { TAdvQueryOggettoBlob, OPERAZIONE_BLOB } from '../../../../../../../../Librerie/VUE/ZAdvQuery.js'
// import { TZGenericFunct } from '../../../../../../../../Librerie/VUE/ZGenericFunct';

export default 
{
  props      : [ 'Altezza'], 
  data()
  {
    return { 
              ListaFatturePassiveScaricate    : [],
              ImportaPremuto                  : false,
              DownloadInCorso                 : false,
           }
  },

  methods :
  {
    OnClickImportaFatturePassive()
    {
      if(!this.DownloadInCorso)
      {
        this.ImportaPremuto               = true;
        this.DownloadInCorso              = true;
        this.ListaFatturePassiveScaricate = [];

        var Self = this                            
        SystemInformation.AdvQuery.ExecuteExternalScript('DownloadFatturePassive', {},
                                                          function(Result)
                                                          {  
                                                            Self.ListaFatturePassiveScaricate = Result.LsFatturePassive;
                                                            
                                                            Self.DownloadInCorso         = false;
                                                          },
                                                          function(HTTPError,SubHTTPError,Response)
                                                          {
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                          }
                                                        );
      }
    }
  },
}
</script>
