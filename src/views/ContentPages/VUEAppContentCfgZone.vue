<template>
  <VUEConfirm :Popup="PopupContentCfgZone" :Richiesta="'Annullare le modifiche effettuate?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
  </VUEConfirm>

  <div v-if="ModificheDaApplicare" style="text-align:right;padding-top:2px;padding-bottom:2px">
    <a class="btn btn-s-md btn-success" style="margin-right:20px" v-if="DataTable.AllDataOk()" @click="Registra()">Conferma</a>
    <a class="btn btn-s-md btn-danger" @click="OnClickAnnulla();">Annulla</a>
  </div>

  <VUEDataTable :DataTable="DataTable" @onChange="OnDataChanged"></VUEDataTable>
</template>

<script>
import { SystemInformation } from '@/SystemInformation';
import VUEDataTable from '@/components/VUEDataTable.vue'
import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable.js'
import VUEConfirm from '@/components/VUEConfirm.vue';

export default 
{
  props : ['Altezza','NomeModello'],
  
  data()
  {
    var DataTable = new TZDataTable('CHIAVE');
    DataTable.ClearColumns();
    var Colonna = DataTable.AddColumn('Descrizione',
                                      TZDTableColumnType.typString,
                                      'DESCRIZIONE',
                                      "80%");
    Colonna.Ordinamento = true;
    Colonna.Necessario  = true;
    Colonna = DataTable.AddColumn('ZTL',
                                   TZDTableColumnType.typBoolean,
                                  'IS_ZTL',
                                  "10%");
    Colonna.Necessario   = true;

    return {
        DataTable                   : DataTable,
        ModificheDaApplicare        : false,
        PopupContentCfgZone         : false
    }
  },
  components : 
  {
    VUEDataTable, VUEConfirm,
  },
  methods:
  {
    OnDataChanged()
    {
     this.ModificheDaApplicare = this.DataTable.Modificato();
    },

    Registra()
    {
      var Self = this;
      var ObjQuery = { Operazioni : [] };
      this.DataTable.Righe.forEach(function(Riga)
      {
          if(Riga.Nuovo)
             ObjQuery.Operazioni.push({
                                        Query     : "Insert",
                                        Parametri : Self.DataTable.PrepareQueryParameters(Riga),
                                        ResetKeys : [1]
                                      })
          else
          {
            if(Riga.Eliminato)
               ObjQuery.Operazioni.push({
                                          Query     : "Delete",
                                          Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                        })
            else 
            {
              if(Riga.Modificato())
                 ObjQuery.Operazioni.push({
                                            Query     : "Update",
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
       this.PopupContentCfgZone = false
    },
    OnClickAnnulla()
    {
       this.PopupContentCfgZone = true
    },
    Annulla()
    {
       var Self = this;
       SystemInformation.AdvQuery.GetSQL(this.NomeModello,{},
                                         function(Results)
                                         {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Tutto");
                                           Self.DataTable.AssignDati(ArrayInfo);
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
   
}
</script>
