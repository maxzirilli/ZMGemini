<template>    
  <div class="container" style="width:80%">
  <VUEConfirm :Popup="PopupGestioneDispositivi" :Richiesta="DescrPopup" @onClickConfermaPopup="ConfermaElimina(true)" @onClickChiudiPopup="AnnullaElimina"/>
    <div class="row"> 
      <div class="block">
        <div class="header" style="padding:0px"> 
          <div class="block block-transparent nm">
              <div class="content controls">
                  <div class="form-row">                        
                      <div class="col-md-12" style="padding-left:0px;padding-right:0px">
                          <div class="input-group">
                              <div class="input-group-addon"><span class="fa fa-search"></span></div>
                              <input type="text" class="form-control" placeholder="Cerca per tecnico" v-model="FiltroDispositivo"/>
                          </div>
                      </div>
                  </div>
              </div>
          </div>                                      
        </div>

        <div class="content">
            <table cellpadding="0" cellspacing="0" width="100%" class="table table-bordered table-striped sortable">
                <thead style="background-color:#68b6be;color:white">
                    <tr>
                      <th width="10%"></th>
                      <th width="20%">Dispositivo</th>
                      <th width="20%">Tecnico</th>
                      <th width="20%">Ragione sociale</th>
                      <th width="20%">Data ultimo accesso</th>
                      <th width="10%">Bloccato</th>
                    </tr>
                </thead>
                <tbody style="background-color:#b3dbff">
                    <tr v-for="Dispositivo in FiltroCerca" :Key="Dispositivo.CHIAVE">
                        <td style="text-align:center">
                          <img v-if="Dispositivo.IMMAGINE"
                                :src="Dispositivo.IMMAGINE"
                                style="width:40px; height:40px; margin-right:8px;border:1px solid rgba(255,255,255,0.3); border-radius:0;" 
                                class="img-circle img-thumbnail"/>
                          <img v-else src="@/assets/images/avatar_default.jpg"
                                style="width:40px; height:40px; margin-right:8px;background:white;border:1px solid rgba(255,255,255,0.3); border-radius:0;" 
                                class="img-circle img-thumbnail"/>
                        </td>
                        <td style="vertical-align:middle">{{Dispositivo.IDENTIFICATIVO}}</td>
                        <td style="vertical-align:middle">{{Dispositivo.USERNAME}}</td>
                        <td style="vertical-align:middle">{{Dispositivo.RAGIONE_SOCIALE}}</td>
                        <td style="vertical-align:middle">{{ Dispositivo.DATA_ULTIMO_LOG}}</td>                                     
                        <td style="vertical-align:middle;text-align:center;">
                            <input style="height:20px;width:20px" type="checkbox" v-model="Dispositivo.BLOCCATO" @change="OnClickBloccaDispositivo(Dispositivo)"/>
                        </td>                                    
                    </tr>
                </tbody>
            </table>                                        
        </div>
      </div>               
    </div>
  </div>           
</template>


<script>
import { SystemInformation, RUOLI } from '@/SystemInformation.js'
import VUEConfirm from '@/components/VUEConfirm.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'

export default 
{
    name:'app',
    components: 
    {
        VUEConfirm
    },

    data()
    {
       return {
                ListaDispositivi             : [],
                FiltroDispositivo            : '',
                Ruoli                        : RUOLI,
                Modello                      : '',
                PopupGestioneDispositivi     : false,
                DispositivoSelezionato       : '',
                DescrPopup                   : '',
              }
    },
    methods :
    {
      ConfermaElimina(Bloccato)
      {
        var ObjQuery = { Operazioni: [] };
        var Self = this;
        ObjQuery.Operazioni.push({
                                    Query: "ModificaBloccoDispositivo",
                                    Parametri: {
                                                  IDENTIFICATIVO : Self.DispositivoSelezionato.IDENTIFICATIVO,
                                                  BLOCCATO       : Bloccato? 'T' : 'F'
                                                }
                                });

        SystemInformation.AdvQuery.PostSQL(this.Modello, ObjQuery,
                                            function()
                                            { 
                                                Self.CaricaListaDispositivi()
                                            },
                                            SystemInformation.HandleError)
        
        this.PopupGestioneDispositivi = false
      },

      AnnullaElimina()
      {
        this.DispositivoSelezionato.BLOCCATO = false
        this.PopupGestioneDispositivi = false
      },

      OnClickBloccaDispositivo(Dispositivo)
      {
        if(Dispositivo.BLOCCATO)
        {
          this.DispositivoSelezionato   = Dispositivo
          this.PopupGestioneDispositivi = true
          this.DescrPopup               = 'Bloccare il dispositivo n. ' + Dispositivo.IDENTIFICATIVO + '?'
        }
        else
        {
          this.DispositivoSelezionato   = Dispositivo
          this.ConfermaElimina(false)
        }
      },

      CaricaListaDispositivi()
      {
        var Self = this;
        SystemInformation.AdvQuery.GetSQL(this.Modello,{},
                                            function(Results)
                                            {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Lista");
                                            if(ArrayInfo != undefined)
                                            {
                                                Self.ListaDispositivi = ArrayInfo;
                                                for(let i = 0; i < Self.ListaDispositivi.length; i++)
                                                {
                                                  Self.ListaDispositivi[i].DATA_ULTIMO_LOG = TZDateFunct.FormatDateTime('dd/mm/yyyy',new Date(Self.ListaDispositivi[i].DATA_ULTIMO_LOG))
                                                  Self.ListaDispositivi[i].BLOCCATO        = Self.ListaDispositivi[i].BLOCCATO == 'T'
                                                }
                                            }
                                            else SystemInformation.HandleError('Errore nel modello gestione dispositivi'); 
                                            },
                                            SystemInformation.HandleError,
                                            'ListaDispositivi')
      },
    },

    computed :
    {
        FiltroCerca()
        {
            var Self = this;
            return this.ListaDispositivi.filter(function(Dispositivo) 
            {
                if(Self.FiltroDispositivo.trim() != '')  
                   if(!Dispositivo.USERNAME.toUpperCase().match(Self.FiltroDispositivo.toUpperCase().trim()))
                      return false;
                return true;
            })
        }
    },

    beforeMount()
    {
      switch (SystemInformation.UserInformation.Ruolo)
      {
          case this.Ruoli.Amministratore : this.Modello = 'AdminESuper_GestioneDispositivi'
          break
          case this.Ruoli.SuperUtente    : this.Modello = 'AdminESuper_GestioneDispositivi'
          break
          default                        : this.Modello = ''
      }
      this.CaricaListaDispositivi()
    }
}
</script>
