<template>
  <VUEConfirm :Popup="PopupStampa" 
              :Richiesta="TestoPopupStampa" 
              :PathLogo="require('@/assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              @onClickConfermaPopup="ConfermaStampa"
              @onClickChiudiPopup="AnnullaStampa">
  </VUEConfirm>

  <VUEConfirm :Popup="PopupElimina" 
              :Richiesta="TestoPopupElimina" 
              :PathLogo="require('@/assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              @onClickConfermaPopup="ConfermaElimina" 
              @onClickChiudiPopup="AnnullaElimina">
  </VUEConfirm>

  <VUEConfirm :Popup="PopupFunzNodo1" 
              :Richiesta="TestoPopupFunzNodo1" 
              :PathLogo="require('@/assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              @onClickConfermaPopup="ConfermaFunzNodo1()" 
              @onClickChiudiPopup="AnnullaFunzNodo1">
  </VUEConfirm>

  <VUEModal v-if="PopupRevisione" :PathLogo="require('@/assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma" :Titolo="'Conferma'" :Altezza="'700px'" :Larghezza="'1500px'" 
            @onClickChiudiModal="AnnullaStampa" @onClickConfermaModal="ConfermaStampa">
            <template v-slot:Body>
              <div class="col-md-12">                
                <div style="display: flex; align-items: center;" >
                  <span style="font-size: large;font-weight: bold;">{{ TestoPopupRevisione }}</span>
                  <select class="form-control" v-model="RevisioneScelta" style="cursor:default; margin-left: 3%; width: 6%;">
                    <option v-for="Revisione in ListaRevisioni" 
                            :Key="Revisione.Chiave"
                            :value="Revisione.Chiave">{{Revisione.NumeroRevisione}}
                    </option>
                  </select>
                </div>
                <div style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;"> 
                  <VUESchedaPreventivoMultiparametrico :SchedaPreventivo="RevisioneSelezionata" :IsReadOnly="true" /> 
                </div>
              </div>
            </template>
            <template v-slot:Footer>
              <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="AnnullaStampa" data-dismiss="modal">Annulla</button>
              <button type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="ConfermaStampa" data-dismiss="modal">Conferma</button>
            </template>
  </VUEModal>

  <VUEModalInvioEmail v-if="Nodo != undefined"
                      :Titolo ="TitoloMail"
                      :PathLogo="require('@/assets/images/LogoGemini2.png')"
                      :Programma="NomeProgramma"
                      :AttivazionePopup="PopupInviaEmail" 
                      :OggettoEmail="OggettoEmail"
                      :ListaEmailCliente="Nodo.Data.ListaEmailCliente"
                      @onClickChiudiModal="AnnullaInvia"
                      @onClickConfermaModal="ConfermaInvia">
  </VUEModalInvioEmail>
  
  <ol class="dd-list">
    <li v-for="ANode in LevelElements" :Key="ANode.Id" class="dd-item">
        <button v-if="ANode.Expanded && ANode.Children.length != 0" 
               :class="{ ButtonSelected : ANode.Selected,
                         Button         : !ANode.Selected }"
               data-action="collapse" 
               type="button" 
               @click="TreeView.ExpandToggle(ANode)">
               Collapse
       </button>
       <button v-if="!ANode.Expanded && (ANode.HasChildren || ANode.Children.length != 0)" 
               :class="{ ButtonSelected : ANode.Selected,
                         Button         : !ANode.Selected }"
               data-action="expand" 
               type="button"
               @click="TreeView.ExpandToggle(ANode)">
               Expand
       </button>
       <div v-if="ANode.Caption != CaptionFilters">
        <div class="dd-handle" 
              :class="{ TreeNodeSelected : ANode.Selected,
                        TreeNode         : !ANode.Selected }" 
              style="cursor: pointer;"
              @click="OnClick(ANode)" @dblclick="TreeView.ExpandToggle(ANode)">
              <div style="float:left; width:28px;">
                <template v-if="ANode.Data.GetImageIndex() != ''" >
                  <img :src="require('@/assets/images/IconeAlbero/' + ANode.Data.GetImageIndex())" />
                </template>
              </div>
              <div style="float:left;width: calc(100% - 100px);padding-top:3px;white-space: pre-line;" v-if="DeveloperMode">
                {{ANode.Caption}} - {{ ANode.Data.Chiave}}
              </div>
              <div style="float:left;width: calc(100% - 100px);padding-top:3px;white-space: pre-line;" v-else>
                {{ANode.Caption}}
              </div>  
              <div style="float:right; text-align: right;" :style="{'width' : CalcoloIconeAbilitate(ANode) + 'px'}">
                

              <i v-if="ANode.Data.FunzNodo1Abilitata()" 
                  style="text-align:right;"
                :style="{'margin-right' : (ANode.Data.DatiEliminabili() || ANode.Data.DatiStampabili() || ANode.Data.DatiInviabili() || ANode.Data.FunzNodo2Abilitata())? '7px' : '0px'}" 
                :class="ANode.Data.FunzNodo1Icona()" 
                @click="OnClickFunzNodo1(ANode)"/>
                
              <i v-if="ANode.Data.FunzNodo2Abilitata()" 
                  style="text-align:right;"
                :style="{'margin-right' : (ANode.Data.DatiEliminabili() || ANode.Data.DatiStampabili() || ANode.Data.DatiInviabili())? '7px' : '0px'}" 
                :class="ANode.Data.FunzNodo2Icona()" 
                @click="ConfermaStampaDiretta(ANode)"/>

              <i v-if="ANode.Data.DatiInviabili()" 
                  style="text-align:right; color:cadetblue" 
                  class="fa fa-envelope-o ZMIconButton" 
                :style="{'margin-right' : (ANode.Data.DatiEliminabili() || ANode.Data.DatiStampabili())? '7px' : '0px'}"
                @click="OnClickInvia(ANode)"/>

              <i v-if="ANode.Data.DatiStampabili() && ANode.Data.FunzNodo2Abilitata() == false" 
                  style="text-align:right;" 
                  class="fa fa-print text-info ZMIconButton" 
                :style="{'margin-right' : ANode.Data.DatiEliminabili()? '7px' : '0px'}"
                @click="OnClickStampa(ANode)"/>

              <i v-if="ANode.Data.DatiEliminabili()" 
                  style="text-align:right;" 
                  class="fa fa-trash-o text-danger ZMIconButton" 
                @click="OnClickElimina(ANode)"/>
              </div>
              <div style="clear:both;height:0px"></div>
        </div>
      </div>
      <div v-else>
        <div class="btn btn-info" style="float:center; text-align:center; cursor: pointer; width: 100%;" 
            @click="OnClickCaricaAltreRighe(ANode)">
            carica altre {{NRigheCaricate}} righe
        </div>
      </div>
       
       <VUETreeViewLevel v-if="ANode.Expanded" 
                        :LevelElements="ANode.Children"
                        :TreeView="TreeView" 
                        @onClick="$emit('onClick')"
                        @onClickFunzNodo1="OnEventFunzNodo1"/>
    </li>
</ol>
</template>

<script>
import { SystemInformation, CAPTION_FILTERS, LIMITA_RIGHE_CARICATE, NOME_PROGRAMMA} from '@/SystemInformation'
import VUEConfirm from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import VUEModal from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import VUESchedaPreventivoMultiparametrico, { TSchedaPreventivoMultiparametrico } from '@/views/SchedeDatabase/VUESchedaPreventivoMultiparametrico.vue';
import VUEModalInvioEmail from '@/views/SchedeDatabase/ComponentMultiScheda/VUEModalInvioEmail.vue';
import { TFilterFattura } from '@/ListaFiltri.js'

 export default 
 {
    data() 
    {
     return { 
                Nodo                      : null,
                PopupElimina              : false,
                PopupStampa               : false,
                PopupRevisione            : false,
                PopupInviaEmail           : false,
                PopupFunzNodo1            : false,
                TestoPopupElimina         : '',
                TestoPopupStampa          : '',
                TestoPopupRevisione       : '',
                TestoPopupFunzNodo1       : '',
                OggettoEmail              : {
                                              CorpoEmail   : '',
                                              Cc           : '',
                                              Ccn          : '',
                                              Destinatario : '',
                                              Oggetto      : ''
                                            },
                ListaRevisioni            : [],
                RevisioneScelta           : null,
                RevisioneSelezionata      : null,
                TitoloMail                : 'Invio fattura tramite email',
                DeveloperMode             : SystemInformation.DeveloperMode,
                FilterFattura             : new TFilterFattura(),
                CaptionFilters            : CAPTION_FILTERS,
                NRigheCaricate            : LIMITA_RIGHE_CARICATE,
                NomeProgramma             : NOME_PROGRAMMA
     }
    },
    components: { VUEConfirm, VUEModalInvioEmail, VUEModal, VUESchedaPreventivoMultiparametrico },
    props : ['LevelElements', 'TreeView'],
    watch:
    {
      RevisioneScelta :
      {
         handler()
         {
           this.OnChangeRevisione();
         },
      }
    },
    emits : [ 'onClick', 'onClickFunzNodo1' ],
    methods :
    {
       CalcoloIconeAbilitate(ANode)
       {
          let CalcoloPixel = 0
          if(ANode.Data.DatiStampabili())
            CalcoloPixel += 18
          if(ANode.Data.DatiEliminabili())
            CalcoloPixel += 18
          if(ANode.Data.FunzNodo1Abilitata())
            CalcoloPixel += 18
          if(ANode.Data.FunzNodo2Abilitata())
            CalcoloPixel += 18
          if(ANode.Data.DatiInviabili())
            CalcoloPixel += 18
          return CalcoloPixel
       },

       ConfermaStampaDiretta(ANode) 
       {
        this.Nodo = ANode;
        this.ConfermaStampa();
       },

       OnClick(ANode)
       {
         this.TreeView.Select(ANode)
         this.$emit('onClick');
       },
       
       OnClickElimina(ANode)
       {
         this.Nodo = ANode
         this.PopupElimina = true
         this.TestoPopupElimina = 'Eliminare ' + this.Nodo.Data.GetDescrizione() + '?'
       },
       
       CaricaRevisioni(NumeroPreventivo, AnnoPreventivo)
       {
        let Self = this
        SystemInformation.AdvQuery.GetSQL('PreventiviMultiparametrici',{ NUMERO : NumeroPreventivo , ANNO : AnnoPreventivo},
                                       function(Results)
                                       {
                                          let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"SelectRevisione");
                                          if(ArrayInfo != undefined)
                                          {  
                                            if(ArrayInfo.length > 1)
                                            {
                                              Self.TestoPopupRevisione = 'Scegli la revisione che desideri stampare'
                                              Self.ListaRevisioni = []
                                              for (let i = 0; i < ArrayInfo.length; i++) 
                                              {
                                                let Preventivo = ArrayInfo[i]
                                                Self.ListaRevisioni.push({
                                                                            Chiave          : Preventivo.CHIAVE,
                                                                            NumeroRevisione : Preventivo.NUMERO_REVISIONE ? Preventivo.NUMERO_REVISIONE : -1,
                                                                        })
                                              }
                                              Self.PopupRevisione = true
                                            }
                                            else 
                                            {
                                              Self.ConfermaStampa()
                                              // Self.PopupStampa = true
                                            }
                                          }
                                          else console.error('Impossibile ottenere la lista delle revisioni');
                                       },
                                       function(HTTPError,SubHTTPError,Response)
                                       {
                                         SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                       }, 
                                       'SelectRevisione')
       },

       OnChangeRevisione(OnSuccess)
       {
          this.RevisioneSelezionata.Nuovo();
          this.RevisioneSelezionata.Chiave = this.RevisioneScelta
          this.RevisioneSelezionata.Disponi(OnSuccess);
       },

       OnClickStampa(ANode)
       {
          let Self = this
          this.Nodo = ANode

          if(ANode.Data.GetClassName() == 'TSchedaPreventivoMultiparametrico')
          { 
              let ObjDatiNodo = JSON.parse(ANode.Data.Snapshot)
              this.RevisioneScelta = ANode.Data.Chiave
              this.RevisioneSelezionata = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
              this.OnChangeRevisione(function()
              {
                Self.CaricaRevisioni(ObjDatiNodo.NUMERO, ObjDatiNodo.ANNO)
              })
          }
          else 
          {
            this.ConfermaStampa()
            // this.PopupStampa = true
          }
       },

       OnClickInvia(ANode)
       {
         this.Nodo = ANode
         var Self  = this
         this.OggettoEmail.Destinatario = ''
         this.OggettoEmail.Oggetto      = ''
         this.OggettoEmail.Cc           = ''
         this.OggettoEmail.Ccn          = ''
         this.OggettoEmail.CorpoEmail   = ""

         if(this.Nodo.Data.IsRapportoIntervento)
            this.TitoloMail                = 'Invio rapporto di intervento tramite mail'
         else
         {
            if(this.Nodo.Data.IsFattura)
            {
              this.TitoloMail                = 'Invio fattura tramite mail'
              this.OggettoEmail.Oggetto      = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_FATTURE
              this.OggettoEmail.CorpoEmail   = SystemInformation.Configurazioni.Impostazioni.CORPO_EMAIL_FATTURE
              
            }
            else
            {
              if(this.Nodo.Data.IsPreventivo)
              {
                this.TitoloMail               = 'Invio preventivo tramite mail'
                this.OggettoEmail.Oggetto     = SystemInformation.Configurazioni.Impostazioni.OGGETTO_EMAIL_PREVENTIVI
                this.OggettoEmail.CorpoEmail  = SystemInformation.Configurazioni.Impostazioni.CORPO_EMAIL_PREVENTIVI    

              }
              else
              {
                if(this.Nodo.Data.IsNotaDiCredito)
                {
                  this.TitoloMail                = 'Invio nota di credito tramite mail'
                }
              }
            }
          }

         this.Nodo.Data.RecuperaEmailCliente(function()
         {
            Self.PopupInviaEmail = true
            if(Self.Nodo.Data.ListaEmailCliente != '')
              Self.OggettoEmail.Destinatario = Self.Nodo.Data.ListaEmailCliente
            if(Self.Nodo.Data.ListaEmailAmm != '' && Self.OggettoEmail.Destinatario == '')
              Self.OggettoEmail.Destinatario = Self.Nodo.Data.ListaEmailAmm
         })
       },

       OnClickFunzNodo1(ANode)
       {
         this.Nodo = ANode
         this.TestoPopupFunzNodo1 = this.Nodo.Data.FunzNodo1Messaggio()
         if(this.TestoPopupFunzNodo1 != '')
            this.PopupFunzNodo1 = true
         else this.ConfermaFunzNodo1()
       },

       OnClickCaricaAltreRighe(ANode)
       {
        ANode.Parent.Children.pop() 
        ANode.Data.Apply(ANode.Data.LastComponent, function() {}, ANode.OnError, this.LevelElements.length)
       },

       OnEventFunzNodo1(Parametri)
       {
         this.$emit('onClickFunzNodo1', Parametri)
       },

       ConfermaFunzNodo1()
       {
          var RisultatoFunzNodo1 = this.Nodo.Data.FunzNodo1(this.Nodo)
          this.PopupFunzNodo1 = false
          if(RisultatoFunzNodo1 != undefined)
            this.$emit('onClickFunzNodo1', RisultatoFunzNodo1);
       },

       ConfermaElimina()
       {
         var Self = this; 
         this.Nodo.Data.Elimina(function()
                                {
                                  Self.TreeView.DeleteNode(Self.Nodo)
                                  Self.AnnullaElimina()  
                                },
                                function(HTTPError,SubHTTPError,Response)
                                {
                                  SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                })
       },

       ConfermaStampa()
       {
         var Self = this;
         if(this.Nodo.Data.constructor.name == 'TSchedaPreventivoMultiparametrico')
         {
          this.Nodo.Data.Stampa(function(BodyPDF)
                                {
                                  if(BodyPDF != undefined)
                                  {
                                    var routeData = Self.$router.resolve({ name   : "IFrameStampe" });
                                    var AWindow = window.open(routeData.href, "_blank");
                                    AWindow.BodyPDF = BodyPDF;
                                  }
                                }, Self.RevisioneScelta)
         }
         else
         {
           this.Nodo.Data.Stampa(function(BodyPDF)
                                {
                                  if(BodyPDF != undefined)
                                  {
                                    var routeData = Self.$router.resolve({ name   : "IFrameStampe" });
                                    var AWindow = window.open(routeData.href, "_blank");
                                    AWindow.BodyPDF = BodyPDF;
                                  }
                                })
         }
         Self.AnnullaStampa()
        },

       ConfermaInvia()
       {
         this.AnnullaInvia()
         var Self = this
         let OnSuccess = function(Answer)
                          {
                            if(Answer.Risposta == 'MAIL_INVIATA')
                            {
                              if(Self.Nodo.Data.IsPreventivoMultiparametrico)
                                Self.Nodo.Data.ConservaFlagMailInviata()
                              alert('Invio mail effettuato con successo')
                            }
                            else 
                            {
                              if(Answer.ErroreMail != undefined)
                                  alert('Errore invio mail')
                              // if (Answer.MessaggioUtente)
                              //     alert("L'account selezionato non esiste o non ha alcuna mail collegata")                   
                            }
                            Self.OggettoEmail.CorpoEmail   = ''
                            Self.OggettoEmail.Cc           = ''
                            Self.OggettoEmail.Ccn          = ''
                            Self.OggettoEmail.Destinatario = ''
                            Self.OggettoEmail.Oggetto      = ''
                          }

         if(this.Nodo.Data.IsPreventivoMultiparametrico)
         {
            this.Nodo.Data.GestionePopupAllegati(this.OggettoEmail, OnSuccess)
         }
         else
         {
            this.Nodo.Data.Invia(this.OggettoEmail, OnSuccess)
         }
       },

       AnnullaElimina()
       {
         this.PopupElimina    = false
       },
       AnnullaStampa()
       {
         this.PopupStampa     = false
         this.PopupRevisione = false
       },
       AnnullaFunzNodo1()
       {
         this.PopupFunzNodo1  = false
       },
       AnnullaInvia()
       {
         this.PopupInviaEmail = false
       },
    },
 }
</script>

<style scoped>
.TreeNodeSelected {
  background-color: #68b6be;
  color : white;
  font-weight: bold;
}
.TreeNodeSelected:hover {
  background-color: #68b6be;
  color : white;
  font-weight: bold;
}
.TreeNode {
  background-color: white;
}
.TreeNode:hover {
  background-color: #f7f7f7;
}
</style>