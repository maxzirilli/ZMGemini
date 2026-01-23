<template>
<VUEModalCaricamentoDati v-if="PopupAttesaCalcolo" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>

<VUEConfirm :Popup="PopupConfermaAggiornamentoCoordinateFiliali" 
            :Richiesta="'Sei sicuro di voler recuperare le coordinate delle filiali che non le hanno?'" 
            @onClickConfermaPopup="ConfermaAggiornamentoCoordinateFiliali" 
            @onClickChiudiPopup="Annulla">
</VUEConfirm>

<VUEModal v-if="PopupMostraRisultati" :Titolo="'[ADMIN] Funzionalità per admin'" :Altezza="'500px'" :Larghezza="'850px'"
          @onClickChiudiModal="Annulla">
   <template v-slot:Body>
    <div class="form-row" style="max-height:320px; overflow-y:auto; padding:10px;">
      <div v-for="(item, index) in RisultatiCreazione" :key="index" class="card mb-2 shadow-sm" style="padding:10px; background-color:#f8f9fa;">
        <ul style="margin-bottom:0;">
          <li>{{ index }}. {{ item }}</li>
        </ul>
      </div>
    </div>
  </template>
  <template v-slot:Footer>
    <button type="button" class="btn btn-info" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="Annulla" data-dismiss="modal">Chiudi</button>
  </template>
</VUEModal>

<VUEModal v-if="PopupImportazioneLogRapportoAdmin" :Titolo="'[ADMIN] Funzionalità per admin'" :Altezza="'150px'" :Larghezza="'550px'"
          @onClickChiudiModal="PopupImportazioneLogRapportoAdmin = false">
  <template v-slot:Body>
    <div class="form-row">
      <div class="col-md-12">
        <label style="font-size:16px"> Inserire la chiave del log del rapporto da importare nuovamente: </label>
        <input class="form-control" style="width:100%;" type="text" v-model="ChiaveImportazioneLogRapportoAdmin">
      </div>
    </div> 
  </template>
  <template v-slot:Footer>
    <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="PopupImportazioneLogRapportoAdmin = false" data-dismiss="modal">Annulla</button>
    <button v-if="ChiaveImportazioneLogRapportoAdmin != ''" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickConfermaImportazioneLogRapportoAdmin" data-dismiss="modal">Importa</button>
  </template>
</VUEModal>

<div class="container mt-4">
  <table class="table table-hover table-bordered text-center align-middle shadow-sm" style="border-radius:10px; overflow:hidden;">
    <thead style="background: linear-gradient(90deg, #343a40, #495057); color: white;">
      <tr>
        <th style="width: 30%;">Azione</th>
        <th>Descrizione</th>
      </tr>
    </thead>
    <tbody>

      <!-- BOTTONE PER L'OTTENIMENTO DELLE COORDINATE DELLE FILIALI -->
      <tr style="background-color:#e3f2fd;">
        <td style="text-align:center; vertical-align:middle;">
          <button class="btn btn-dark btn-sm btn-rounded" style="font-size:16px" @click="OnClickConfermaAggiornamentoCoordinateFiliali">
            Inserisci coordinate filiali
          </button>
        </td>
        <td style="white-space: pre-line; font-size:17px">
          Questo pulsante permette di recuperare le coordinate di tutte le filiali dei clienti.<br><br>
          È necessario eseguire questo script più volte, perché può aggiornare circa 50 elementi per volta. 
          Questo limite è dovuto al tempo di attesa richiesto per il limite del server, 
          per la chiamata a OpenStreetMap e al fatto che devono essere elaborate tutte le filiali dei clienti.<br><br>
        </td>
      </tr>


    </tbody>
  </table>
</div>


</template>
  
<script>
  import { SystemInformation, RUOLI } from '@/SystemInformation';
  import VUEConfirm from '@/components/VUEConfirm.vue';
  import VUEModal from '@/components/Slots/VUEModal.vue';
  import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';
  import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct';
  
  export default
  {
    props : ['Altezza','NomeModello'],
    data()
    {
      return {
                PopupAttesaCalcolo                          : false,
                PopupImportazioneLogRapportoAdmin           : false,
                ChiaveImportazioneLogRapportoAdmin          : null,
                MesePrimaVisitaDaImpostare                  : 1,
                LsMesi                                      : TZDateFunct.GetMesiInItaliano(),
                RisultatiCreazione                          : null,
                PopupMostraRisultati                        : false,
                PopupConfermaAggiornamentoCoordinateFiliali : false,
      }
    },
    components : 
    {
      VUEConfirm,
      VUEModal,
      VUEModalCaricamentoDati
    },
    methods:
    {
      OnClickCaricaJsonAdmin()
      {
        this.PopupImportazioneLogRapportoAdmin = true
      },

      OnClickConfermaImportazioneLogRapportoAdmin()
      {
        this.InvioRapportoImportazione(this.ChiaveImportazioneLogRapportoAdmin)
        this.PopupImportazioneLogRapportoAdmin = false
      },


      OnClickConfermaAggiornamentoCoordinateFiliali()
      {
        this.PopupConfermaAggiornamentoCoordinateFiliali = true
      },

      ConfermaAggiornamentoCoordinateFiliali()
      {
        let Self = this
        this.PopupAttesaCalcolo = true
        this.PopupConfermaAggiornamentoCoordinateFiliali = false

        SystemInformation.AdvQuery.ExecuteExternalScript('UpdateLatitudineLongitudine', {},
                                                          function(Result)
                                                          {  
                                                            if(Result != undefined)
                                                              alert('Eseguito');
                                                            else 
                                                            {
                                                              SystemInformation.HandleError('Non eseguito','','');
                                                              Self.Annulla()
                                                            }
                                                            Self.Annulla()
                                                          },
                                                          function(HTTPError,SubHTTPError,Response)
                                                          {
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                            Self.Annulla()
                                                          });
      },

      Annulla()
      {
        this.PopupAttesaCalcolo                      = false
        this.MesePrimaVisitaDaImpostare              = 1
        this.PopupMostraRisultati                    = false
        this.RisultatiCreazione                      = null
        this.PopupConfermaAggiornamentoCoordinateFiliali = false
      }
  
      
    },

    beforeMount() 
    {
      if(SystemInformation.UserInformation.Ruolo != RUOLI.Amministratore)
        SystemInformation.Router.push('/appMainWindow/Dashboard')
    }
  }
</script>
