<template>
    
    <div class="container" style="width:100%">
      <VUEConfirm :Popup="PopupContentGestioneMessaggi" :Richiesta="PopupDescr" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
    </VUEConfirm>

        <div class="row"> 
                                
                <div class="block">
                    <div class="header" style="padding:0px">
                      <div class="block block-transparent nm">
                          <div class="content controls">
                              <div class="form-row">
                                  <div class="col-md-2" style="float:right;padding-right:0px;">
                                      <button type="button" class="btn btn-success" @click="OnClickAggiungiMessaggio" style="width:100%">Aggiungi</button>
                                  </div>                            
                              </div>
                          </div>
                      </div>                                       
                    </div>
                    <div class="content">

                        <table cellpadding="0" cellspacing="0" width="100%" style="background-color:#b3dbff" class="table table-bordered table-striped sortable">
                            <thead style="background-color:#68b6be;color:white">
                                <tr>
                                    <th width="20%">Titolo</th>
                                    <th width="52%">Testo del messaggio</th>
                                    <th width="20%">Allegati</th>
                                    <th width="5%">Azioni</th>
                                </tr>
                            </thead>
                            <tbody style="background-color:#b3dbff">
                                <tr v-for="Messaggio, index in ListaMessaggi" :Key="Messaggio.Chiave">
                                    <td>
                                        <img v-if="Messaggio.Immagine"
                                        :src="Messaggio.Immagine"
                                         style="width:40px; height:40px; margin-right:8px;border:1px solid rgba(255,255,255,0.3); border-radius:0" 
                                         class="img-circle img-thumbnail"/>

                                         <img v-else src="@/assets/images/IconaPDF.png"
                                         style="width:40px; height:40px; margin-right:8px;background:white;border:1px solid rgba(255,255,255,0.3); border-radius:0" 
                                         class="img-circle img-thumbnail"/>
                                         
                                         {{Messaggio.Titolo}} 
                                    </td>
                                    <td style="vertical-align:middle">{{Messaggio.Testo}}</td>
                                    <td v-if="Messaggio.Allegati.length > 0" style="vertical-align:middle">
                                        <tr v-for="Allegato in ListaMessaggi[index].Allegati" :Key="Allegato.Chiave">{{ Allegato.NomeFile }}</tr>
                                    </td>
                                    <td v-else style="vertical-align:middle">Nessun allegato presente</td>
                                    <td style="vertical-align:middle;text-align:center;">
                                        <div>
                                            <i class="fa fa-pencil text-warning ZMIconButton" @click="OnClickModificaMessaggio(Messaggio)" style="margin-right:20px"></i>
                                            <i class="fa fa-trash-o text-danger ZMIconButton" @click="OnClickEliminaMessaggio(Messaggio)"></i>
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
import { SystemInformation} from '@/SystemInformation.js'
import VUEConfirm from '@/components/VUEConfirm.vue';

export default {
    name:'app',
    components: {
                   VUEConfirm
    },
    data()
    {
       return {
                ListaMessaggi                : [],
                PopupContentGestioneMessaggi : false,
                MessaggioPopup               : {},
                PopupDescr                   : ''
              }
    },
    methods :
    {
      OnClickAggiungiMessaggio()
      {
        this.$router.push('/Messaggio/nuovo')
      },

      CaricaListaMessaggi()
      {
        var Self = this;
        SystemInformation.AdvQuery.GetSQL('Admin_Messaggi',{},
                                            function(Results)
                                            {
                                                let ArrayInfoMessaggi = SystemInformation.AdvQuery.FindResults(Results,"ListaMessaggi")
                                                let ArrayInfoAllegati = SystemInformation.AdvQuery.FindResults(Results,"ListaAllegati")
                                                if (ArrayInfoMessaggi != undefined)
                                                {
                                                    for (let i = 0; i < ArrayInfoMessaggi.length; i++)
                                                    {
                                                        let ObjMessaggio = {Chiave   : ArrayInfoMessaggi[i].CHIAVE,
                                                                            Titolo   : ArrayInfoMessaggi[i].TITOLO,
                                                                            Immagine : ArrayInfoMessaggi[i].IMMAGINE,
                                                                            Testo    : ArrayInfoMessaggi[i].TESTO,
                                                                            Allegati : []}
                                                        if (ArrayInfoAllegati != undefined)
                                                        {
                                                            for (let j = 0; j < ArrayInfoAllegati.length; j++)
                                                            {
                                                                if (ArrayInfoAllegati[j].ID_MESSAGGIO == ObjMessaggio.Chiave)
                                                                    ObjMessaggio.Allegati.push({
                                                                                                  Chiave : ArrayInfoAllegati[j].CHIAVE,
                                                                                                  Allegato : ArrayInfoAllegati[j].ALLEGATO,
                                                                                                  Tipo : ArrayInfoAllegati[j].TIPO,
                                                                                                  NomeFile : ArrayInfoAllegati[j].NOME_FILE
                                                                    })
                                                            }
                                                        }
                                                        Self.ListaMessaggi.push(ObjMessaggio)
                                                    }
                                                }
                                                else SystemInformation.HandleError('Errore nel modello messaggi'); 
                                                
                                            },
                                            SystemInformation.HandleError)
      },

      ConfermaElimina()
      {
         let messaggio = this.MessaggioPopup
         var ObjQuery = { Operazioni: [] };
         var Self = this;
            ObjQuery.Operazioni.push({
                                            Query: "DeleteAllegati",
                                            Parametri: {
                                                        Chiave              : messaggio.Chiave
                                                    }
                                        });
            ObjQuery.Operazioni.push({
                                            Query: "DeleteLetti",
                                            Parametri: {
                                                        Chiave              : messaggio.Chiave
                                                    }
                                        });
            ObjQuery.Operazioni.push({
                                        Query: "DeleteMessaggio",
                                        Parametri: {
                                                    Chiave              : messaggio.Chiave
                                                }
                                    });
            SystemInformation.AdvQuery.PostSQL('Admin_Messaggi',ObjQuery,
                                                function()
                                                { 
                                                    Self.$router.go()
                                                },
                                                SystemInformation.HandleError)
         this.AnnullaElimina()
      },
      AnnullaElimina()
      {
         this.PopupContentGestioneMessaggi = false
      },

      OnClickModificaMessaggio(messaggio)
      {
        this.$router.push('/Messaggio/' + messaggio.Chiave)
      },

      OnClickEliminaMessaggio(messaggio)
      {
        this.PopupContentGestioneMessaggi = true
        this.MessaggioPopup = messaggio
        this.PopupDescr = 'Eliminare ' + messaggio.Titolo + '?'        
      }
    },
    computed :
    {
        
    },
    beforeMount()
    {
      this.CaricaListaMessaggi()
    }
}
</script>