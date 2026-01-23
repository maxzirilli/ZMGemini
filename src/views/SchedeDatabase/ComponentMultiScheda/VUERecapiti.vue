<template>
    <VUEDataTableSecondaria :DataTableSecondaria="SchedaRecapiti.DataTableTelefono" @onChange="OnRecapitiChange"></VUEDataTableSecondaria>
</template>

<script>
import { TZDataTable,TZDTableColumnType } from '@/../../../../../../Librerie/VUE/ZDataTable.js'
import VUEDataTableSecondaria from '@/components/VUEDataTableSecondaria.vue'

export class TSchedaRecapiti
{
    constructor(NascondiCheckPrincipale = false, CambioNomeRecapitiCliente = false, RecapitiFilialiConAvvisi = false)
    {
      this.DataTableTelefono = new TZDataTable('CHIAVE');
      this.DataTableTelefono.ClearColumns();
      if(!NascondiCheckPrincipale)
        this.DataTableTelefono.AddColumn(CambioNomeRecapitiCliente? 'Avv. fatt.' : 'Princ.',
                                          TZDTableColumnType.typBoolean,
                                          'PRINCIPALE',
                                          "3%");
      if(RecapitiFilialiConAvvisi)
      {
        this.DataTableTelefono.AddColumn('Av.Em.',
                                          TZDTableColumnType.typBoolean,
                                          'EMAIL_PER_AVVISO',
                                          "2%");
        var AColumn = this.DataTableTelefono.AddColumn('Av.Tel.',
                                          TZDTableColumnType.typBoolean,
                                          'TELEFONO_PER_AVVISO',
                                          "2%");
        AColumn.BoolEsclusivo = true

      }

      this.DataTableTelefono.AddColumn('Referente',
                                      TZDTableColumnType.typString,
                                      'DESCRIZIONE',
                                      "30%");                                               
      this.DataTableTelefono.AddColumn('Telefono',
                                      TZDTableColumnType.typString,
                                      'TELEFONO',
                                      "30%");
      this.DataTableTelefono.AddColumn('Email',
                                       TZDTableColumnType.typString,
                                      'EMAIL',
                                      "27%");

    }

    CheckAlmenoUnCampoRiempito()
    {
      var Self = this
      this.DataTableTelefono.Righe.forEach(function(Riga)
      {
          if(Riga.Dati['DESCRIZIONE'] != '')
            Self.AlmenoUnCampo = true
          if(Riga.Dati['EMAIL'] != '')
            Self.AlmenoUnCampo = true
          if(Riga.Dati['TELEFONO'] != '')
            Self.AlmenoUnCampo = true
      })
    }
    
    ClearSchedaRecapiti()
    {
      this.DataTableTelefono.AssignDati([]);
    }

    AssignDati(ArrayTelefono, NonAzzerareVettore, InseritiDaWatch)
    {
      if(NonAzzerareVettore)
        this.DataTableTelefono.AssignDati(ArrayTelefono, NonAzzerareVettore)
      else this.DataTableTelefono.AssignDati(ArrayTelefono)
      if(InseritiDaWatch)
        this.DataTableTelefono.Righe.forEach(function(Riga)
        {
          Riga.Nuovo = true
        })
    }

    PrepareQueryParametersTelefono(Operazioni, ChiaveDocumento, CampoDocumento, IsFiliale)
    {
      var Self = this
      if(IsFiliale)
      {
        this.DataTableTelefono.Righe.forEach(function(Riga)
        {
            let Parametri = Self.DataTableTelefono.PrepareQueryParameters(Riga);
            if(Riga.Nuovo)
            {
              if(ChiaveDocumento != -1)
                  Parametri[CampoDocumento] = ChiaveDocumento;
              Operazioni.push({
                                Query     : "InsertTelefonoFiliale",
                                Parametri : Parametri,
                                ResetKeys : [3]
                              })
            }
            else
            {
              if(Riga.Eliminato)
                Operazioni.push({
                                  Query     : "DeleteTelefonoFiliale",
                                  Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                })
              else 
              {
                if(Riga.Modificato())
                  Operazioni.push({
                                    Query     : "UpdateTelefonoFiliale",
                                    Parametri : Parametri
                                  })
              }
            }
        });
      }
      else
      {
        this.DataTableTelefono.Righe.forEach(function(Riga)
        {
            let Parametri = Self.DataTableTelefono.PrepareQueryParameters(Riga);
            if(Riga.Nuovo)
            {
              if(ChiaveDocumento != -1)
                  Parametri[CampoDocumento] = ChiaveDocumento;
              Operazioni.push({
                                Query     : "InsertTelefono",
                                Parametri : Parametri,
                                ResetKeys : [2]
                              })
            }
            else
            {
              if(Riga.Eliminato)
                Operazioni.push({
                                  Query     : "DeleteTelefono",
                                  Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                })
              else 
              {
                if(Riga.Modificato())
                  Operazioni.push({
                                    Query     : "UpdateTelefono",
                                    Parametri : Parametri
                                  })
              }
            }
        });
      }
    }
}

export default 
{

  props: ['SchedaRecapiti'],
  emits: ['onChange'],
  components: 
  {
    VUEDataTableSecondaria
  },

  data()
  {    
     return {
              
     }
  },

  methods :
  {
    OnRecapitiChange()
    {
        if(this.SchedaRecapiti.DataTableTelefono.Modificato())
          this.$emit('onChange');
    }
  }
}     

</script>
