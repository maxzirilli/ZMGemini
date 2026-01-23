<template>    
    <div class="container" style="width:100%">
        <VUEConfirm :Popup="PopupContentGestioneUtenti" :Richiesta="DescrPopup" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
        </VUEConfirm>
      
        <VUEConfirm :Popup="PopupResettaPassword" 
                    :Richiesta="'Sei sicuro di voler resettare la password a ' + ObjResettaPassword.RAGIONE_SOCIALE + '?'" 
                    @onClickConfermaPopup="ConfermaResetPassword" 
                    @onClickChiudiPopup="AnnullaResetPassword">
        </VUEConfirm>

        <VUEModal v-if="PopupConfermaReset" :Titolo="'Comunicazione'" :Altezza="'100px'" :Larghezza="'500px'" 
                @onClickChiudiModal="PopupConfermaReset = false">
            <template v-slot:Body>
                <p style="white-space: pre-line">Passord resettata</p>
            </template>
            <template v-slot:Footer>
                <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:20%" @click="PopupConfermaReset = false" data-dismiss="modal">Ok</button>
            </template>
        </VUEModal>

        <div class="row"> 
                <div class="block">
                    <div class="header" style="padding:0px"> 
                      <div class="block block-transparent nm">
                          <div class="content controls">
                              <div class="form-row">                        
                                  <div class="col-md-5" style="padding-left:0px">
                                      <div class="input-group">
                                          <div class="input-group-addon"><span class="fa fa-search"></span></div>
                                          <input type="text" class="form-control" placeholder="Cerca Utente" v-model="FiltroUtente"/>
                                      </div>
                                  </div>
                                  <div class="col-md-2" style="float:right;padding-right:0px;">
                                      <button type="button" class="btn btn-success" @click="OnClickAggiungiProfilo" style="width:100%">Aggiungi</button>
                                  </div>                            
                              </div>
                          </div>
                      </div>                                      
                    </div>
                    <div class="content">

                        <table cellpadding="0" cellspacing="0" width="100%" class="table table-bordered table-striped sortable">
                            <thead style="background-color:#68b6be;color:white">
                                <tr>
                                    <th width="4%"></th>
                                    <th width="15%">Username</th>
                                    <th width="18%">Ragione Sociale</th>
                                    <th width="13%">Ruolo</th>
                                    <th width="24%">E-mail</th>                                    
                                    <th width="5%">Password</th>
                                    <th width="5%">Azioni</th>

                                </tr>
                            </thead>
                            <tbody style="background-color:#b3dbff">
                                <tr v-for="Utente in FiltroCerca" :Key="Utente.CHIAVE">
                                    <td>
                                      <img v-if="Utente.IMMAGINE"
                                       :src="Utente.IMMAGINE"
                                       style="width:40px; height:40px; margin-right:8px;border:1px solid rgba(255,255,255,0.3); border-radius:0" 
                                       class="img-circle img-thumbnail"/>
                                      <img v-else src="@/assets/images/avatar_default.jpg"
                                       style="width:40px; height:40px; margin-right:8px;background:white;border:1px solid rgba(255,255,255,0.3); border-radius:0" 
                                       class="img-circle img-thumbnail"/>
                                    </td>
                                    <td style="vertical-align:middle">{{Utente.USERNAME}}</td>
                                    <td style="vertical-align:middle">{{Utente.RAGIONE_SOCIALE}}</td>
                                    <td style="vertical-align:middle" v-if="Utente.ROLE == Ruoli.Amministratore">Amministratore</td>
                                    <td style="vertical-align:middle" v-if="Utente.ROLE == Ruoli.SuperUtente">Super Utente</td>
                                    <td style="vertical-align:middle" v-if="Utente.ROLE == Ruoli.Dipendente">Dipendente</td>
                                    <td style="vertical-align:middle">{{Utente.EMAIL}}</td>                                     
                                    <td style="vertical-align:middle">
                                        <button type="button" class="btn btn-info" @click="OnClickResettaPasswordUtente(Utente)" style="width:100%">Reset password</button>
                                    </td>                                     
                                    <td style="vertical-align:middle;text-align:center;">
                                        <div v-if="Utente.USERNAME != SelfUsername">
                                            <i class="fa fa-pencil text-warning ZMIconButton" @click="OnClickModificaProfilo(Utente)" style="margin-right:20px"></i>
                                            <i class="fa fa-trash-o text-danger ZMIconButton" @click="OnClickEliminaProfilo(Utente)"></i>
                                        </div>
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
import VUEModal from '@/components/Slots/VUEModal.vue';

export default {
    name:'app',
    components: {
                   VUEConfirm,
                   VUEModal,
    },
    data()
    {
       return {
                ListaUtenti                  : [],
                FiltroUtente                 : '',
                Ruoli                        : RUOLI,
                Modello                      : '',
                PopupContentGestioneUtenti   : false,
                ProfiloModifica              : {},
                DescrPopup                   : '',
                SelfUsername                 : SystemInformation.UserInformation.UserName,
                PopupResettaPassword         : false,
                ObjResettaPassword           : {},
                PopupConfermaReset           : false,
                SelfRole                     : SystemInformation.UserInformation.Ruolo
              }
    },
    methods :
    {
      ConfermaElimina()
      {
         var ObjQuery = { Operazioni: [] };
         var Self = this;
         ObjQuery.Operazioni.push({
                                     Query: "Delete",
                                     Parametri: {
                                                 CHIAVE              : Self.ProfiloModifica.CHIAVE
                                             }
                                 });
         SystemInformation.AdvQuery.PostSQL(this.Modello,ObjQuery,
                                             function()
                                             { 
                                                 Self.$router.go()
                                             },
                                             SystemInformation.HandleError)
         this.AnnullaElimina()
      },

      AnnullaElimina()
      {
         this.PopupContentGestioneUtenti = false
      },

      OnClickAggiungiProfilo()
      {
        this.$router.push('/SchedaUtente/nuovo')
      },
      OnClickModificaProfilo(Profilo)
      {
        this.$router.push('/SchedaUtente/' + Profilo.CHIAVE)
      },
      OnClickEliminaProfilo(Profilo)
      {
         this.ProfiloModifica = Profilo
         this.PopupContentGestioneUtenti = true
         this.DescrPopup = 'Eliminare ' + Profilo.USERNAME + '?'
      },

      OnClickResettaPasswordUtente(Utente)
      {
        this.ObjResettaPassword   = Utente
        this.PopupResettaPassword = true
      },

      ConfermaResetPassword()
      {
        let Self = this
        SystemInformation.ResettaPasswordUtente(this.ObjResettaPassword.CHIAVE, function()
        {
            Self.ObjResettaPassword   = {}
            Self.PopupResettaPassword = false
            setTimeout(() => Self.PopupConfermaReset   = true, 500)
        })
      },

      AnnullaResetPassword()
      {
        this.ObjResettaPassword   = {}
        this.PopupResettaPassword = false
      },

      CaricaListaProfili()
      {
        var Self = this;
        SystemInformation.AdvQuery.GetSQL(this.Modello,{},
                                            function(Results)
                                            {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Lista");
                                            if(ArrayInfo != undefined)
                                                Self.ListaUtenti = ArrayInfo;
                                            else SystemInformation.HandleError('Errore nel modello utenti'); 
                                            },
                                            SystemInformation.HandleError)
      },
    },
    computed :
    {
        FiltroCerca()
        {
            var Self = this;
            return this.ListaUtenti.filter(function(Utente) 
            {
                if(Self.FiltroUtente.trim() != '')  
                   if(!Utente.USERNAME.toUpperCase().match(Self.FiltroUtente.toUpperCase().trim()))
                      return false;
                return true;
            })
        }
    },
    beforeMount()
    {
       switch (SystemInformation.UserInformation.Ruolo)
       {
            case this.Ruoli.Amministratore : this.Modello = 'Admin_GestioneUtenti'
            break
            case this.Ruoli.SuperUtente    : this.Modello = 'SuperUtente_GestioneUtenti'
            break
            default                        : this.Modello = ''
       }
        this.CaricaListaProfili()
          
    }
}
</script>
