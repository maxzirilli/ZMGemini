<template>
  <VUEConfirm :Popup="PopupContentCfgTesti" :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma" 
              :Richiesta="'Annullare le modifiche effettuate?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
  </VUEConfirm>
  
  <div v-if="ModificheDaApplicare" style="text-align:right;padding-top:2px;padding-bottom:2px">
    <a class="btn btn-s-md btn-success" style="margin-right: 10px" @click="Registra()">Conferma</a>
    <a class="btn btn-s-md btn-danger" @click="OnClickAnnulla();">Annulla</a>
  </div>

  <div class="col-md-12">
    <div style="float:left; margin-right: 5%;width:40%">
      <label style="font-size:14px;">Descrizione prezzo fisso
        <input type="text" class="form-control" v-model="Dati.DescrizionePrezzoFisso"/>
      </label>
    </div>
  </div>

  <div class="col-md-12">
    <div style="float:left; margin-right: 5%;width:40%">
      <label style="font-size:14px;">Descrizione canone annuale
        <input type="text" class="form-control" v-model="Dati.DescrizioneCanoneAnnuale"/>
      </label>
    </div>
  </div>
</template>

<script>
import { SystemInformation, NOME_PROGRAMMA } from '@/SystemInformation';
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'

export default 
{
  props : ['Altezza','NomeModello'],
  data()
  {
    return {
        Dati                 : {
                                  DescrizionePrezzoFisso            : '',
                                  DescrizioneCanoneAnnuale          : ''
                               },
        Iniziale             : '',
        PopupContentCfgTesti : false,
        NomeProgramma        : NOME_PROGRAMMA
    }
  },
  components : 
  {
    VUEConfirm,
  },
  methods:
  {
    Registra()
    {
      var Self = this;
      
      var ObjQuery = { Operazioni : [] };
        
      ObjQuery.Operazioni.push({
                                Query     : "ModificaTesti",
                                Parametri : {
                                              DESCRIZIONE_PREZZO_FISSO              : this.Dati.DescrizionePrezzoFisso,
                                              DESCRIZIONE_CANONE_ANNUALE            : this.Dati.DescrizioneCanoneAnnuale
                                            }
                              })

      SystemInformation.AdvQuery.PostSQL(this.NomeModello,ObjQuery,function()
      {
        SystemInformation.GetConfigurazioni(function()
        {
          ObjQuery = {};
          Self.Annulla();
        })
      },
      function(HTTPError,SubHTTPError,Response)
      {
        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
      });
    },

    OnClickAnnulla()
    {
       this.PopupContentCfgTesti = true
    },

    ConfermaElimina()
    {
       this.Annulla()
       this.AnnullaElimina()
    },

    AnnullaElimina()
    {
      this.PopupContentCfgTesti = false
    },
    
    Annulla()
    {
       var Self = this;
       SystemInformation.AdvQuery.GetSQL(this.NomeModello,{},
                                         function(Results)
                                         {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"TuttiTesti");
                                           
                                           Self.Dati.DescrizionePrezzoFisso           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE_PREZZO_FISSO)
                                           Self.Dati.DescrizioneCanoneAnnuale         = TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE_CANONE_ANNUALE)
                                           
                                           Self.Iniziale = JSON.stringify(Self.Dati)                                        
                                         },
                                         function(HTTPError,SubHTTPError,Response)
                                         {
                                           SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                         });
    }
  },
  computed:
  {
    ModificheDaApplicare:
    {
      get()
      {
        return this.Iniziale != JSON.stringify(this.Dati)
      }
    }
  },
  beforeMount() 
  {
    this.Annulla()
  },
   
}
</script>
