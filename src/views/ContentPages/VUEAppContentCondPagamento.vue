<template> 
<VUEConfirm :Popup="PopupContentCondPagamento" :Richiesta="'Annullare le modifiche effettuate?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
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
import { TZFatturaElettronica } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica';

export default 
{
  props : [ 'Altezza'],
  data()
  {
    var DataTable = new TZDataTable('CHIAVE');
    DataTable.ClearColumns();
    var Colonna = DataTable.AddColumn('Descrizione',
                                      TZDTableColumnType.typString,
                                      'DESCRIZIONE',
                                      "20%");
    Colonna.Ordinamento = true;
    Colonna.Necessario  = true;
    DataTable.AddColumn('Ricevuta bancaria',
                        TZDTableColumnType.typBoolean,
                        'RICEVUTA_BANCARIA',
                        "8%");
    DataTable.AddColumn('30gg',
                        TZDTableColumnType.typBoolean,
                        'P30GG',
                        "7%");
    DataTable.AddColumn('60gg',
                        TZDTableColumnType.typBoolean,
                        'P60GG',
                        "7%");
    DataTable.AddColumn('90gg',
                        TZDTableColumnType.typBoolean,
                        'P90GG',
                        "7%");
    DataTable.AddColumn('120gg',
                        TZDTableColumnType.typBoolean,
                        'P120GG',
                        "7%");
    DataTable.AddColumn('Fine mese',
                        TZDTableColumnType.typBoolean,
                        'FINE_MESE',
                        "9%");
    Colonna = DataTable.AddColumn('Descrizione SDI',
                                   TZDTableColumnType.typSelect,
                                   'COND_PAG_FATT_ELE',
                                   "10%");
    Colonna.Lista = TZFatturaElettronica.GetLsCondizioniPagamento()
    Colonna.Necessario = true                                   
    Colonna.SelCampoChiave = 'Id';
    Colonna.SelCampoCaption = 'Descrizione'
    Colonna = DataTable.AddColumn('Mod. pagamento',
                                  TZDTableColumnType.typSelect,
                                  'MOD_PAG_FATT_ELE',
                                  "10%");
    Colonna.Lista = TZFatturaElettronica.GetLsModalitaPagamento()
    Colonna.Necessario = true                                   
    Colonna.SelCampoChiave = 'Id';
    Colonna.SelCampoCaption = 'Descrizione'
    DataTable.AddColumn('Pagamento obblig.',
                        TZDTableColumnType.typBoolean,
                        'PAGAMENTO_OBBLIGATORIO',
                        "9%");
    DataTable.AddColumn('Forza invio fatt. su tablet',
                        TZDTableColumnType.typBoolean,
                        'FORZA_INVIO_FATTURE_SU_TABLET',
                        "9%");
   
    return {
        DataTable                  : DataTable,
        ModificheDaApplicare       : false,
        PopupContentCondPagamento  : false
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
                                         Query     : "InsertCondizione",
                                         Parametri : Self.DataTable.PrepareQueryParameters(Riga)
                                       })
           else
           {
             if(Riga.Eliminato)
                ObjQuery.Operazioni.push({
                                           Query     : "DeleteCondizione",
                                           Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                         })
             else 
             {
               if(Riga.Modificato())
                  ObjQuery.Operazioni.push({
                                             Query     : "UpdateCondizione",
                                             Parametri : Self.DataTable.PrepareQueryParameters(Riga)
                                           })
             }
          }
      });

      SystemInformation.AdvQuery.PostSQL('CondPagamento',ObjQuery,function()
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
       this.PopupContentCondPagamento = false
    },
    OnClickAnnulla()
    {
       this.PopupContentCondPagamento = true
    },
    
    Annulla()
    {
       var Self = this;
       SystemInformation.AdvQuery.GetSQL("CondPagamento",{},
                                         function(Results)
                                         {
                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"TutteLeCondizioni");
                                           Self.DataTable.AssignDati(ArrayInfo);
                                           Self.ModificheDaApplicare = false
                                         },
                                         function(HTTPError,SubHTTPError,Response)
                                         {
                                           SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                         });
     }
     
  },
  computed:
  {
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
