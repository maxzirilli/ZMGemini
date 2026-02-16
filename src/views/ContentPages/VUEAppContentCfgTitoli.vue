<template>
  <VUEConfirm :Popup="PopupContentCfgTitoli" :Richiesta="'Annullare le modifiche effettuate?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
  </VUEConfirm>
  <div v-if="ModificheDaApplicare" style="text-align:right;padding-top:2px;padding-bottom:2px">
      <a class="btn btn-s-md btn-success" style="margin-right:20px" v-if="DataTable.AllDataOk()" @click="Registra()">Conferma</a>
      <a class="btn btn-s-md btn-danger" @click="OnClickAnnulla();">Annulla</a>
  </div>

  <VUEDataTable :DataTable="DataTable" :NomeProgramma="'Gemini'" :PathLogo="require('../../assets/images/LogoGemini2.png')"></VUEDataTable>
</template>

<script>
import { SystemInformation } from '@/SystemInformation';
import VUEDataTable from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2.vue';
import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable2.js'
import VUEConfirm from '@/components/VUEConfirm.vue';

export default 
{
  props : ['Altezza','NomeModello'],
  data()
  {
    var DataTable = new TZDataTable('CHIAVE');
    DataTable.ClearColumns();
    var Colonna = DataTable.AddColumn('Titolo',
                                      TZDTableColumnType.typString,
                                      'DESCRIZIONE',
                                      "90%");
    Colonna.Ordinamento = true;
    Colonna.Necessario  = true;
    return {
        DataTable             : DataTable,
        ModificheDaApplicare  : false,
        PopupContentCfgTitoli : false
    }
  },
  components : 
  {
    VUEDataTable,
    VUEConfirm,
  },
  methods:
  {
    Registra()
    {
      var Self = this;
      var ObjQuery = { Operazioni : [] };
      this.DataTable.Righe.forEach(function(Riga)
      {
          if(Riga.Nuovo)
             ObjQuery.Operazioni.push({
                                        Query     : "InserisciTitolo",
                                        Parametri : Self.DataTable.PrepareQueryParameters(Riga),
                                        ResetKeys : [1]
                                      })
          else
          {
            if(Riga.Eliminato)
               ObjQuery.Operazioni.push({
                                          Query     : "EliminaTitolo",
                                          Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                        })
            else 
            {
              if(Riga.Modificato())
                 ObjQuery.Operazioni.push({
                                            Query     : "ModificaTitolo",
                                            Parametri : Self.DataTable.PrepareQueryParameters(Riga)
                                          })
            }
          }
      });

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
    ConfermaElimina()
    {
       this.Annulla()
       this.AnnullaElimina()
    },
    AnnullaElimina()
    {
       this.PopupContentCfgTitoli = false
    },
    OnClickAnnulla()
    {
       this.PopupContentCfgTitoli = true
    },
    Annulla()
    {
       var Self = this;
       SystemInformation.AdvQuery.GetSQL(this.NomeModello,{},
                                         function(Results)
                                         {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"TuttiTitoli");
                                           Self.DataTable.AssignDati(ArrayInfo);
                                           Self.ModificheDaApplicare = false
                                         },
                                         function(HTTPError,SubHTTPError,Response)
                                         {
                                           SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                         });
    }
  },
  beforeMount() 
  {
    this.Annulla()
  },

  mounted()
  {
    this.DataTable.AssignOnRowChange(() =>
    {
      this.ModificheDaApplicare = true
    })
    this.DataTable.AssignOnRowDelete(() =>
    {
      this.ModificheDaApplicare = true
    })
    this.Annulla();
  },
   
}
</script>
