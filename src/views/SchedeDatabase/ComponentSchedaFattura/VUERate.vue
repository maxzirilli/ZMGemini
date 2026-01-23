<template>
 <div>
  <div class="row wrapper">
   <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Rate</text>
        <hr style="margin-top:5px;margin-bottom:0px">
   </div>
  <section class="panel panel-default" style="background-color:white">
  <div class="table-responsive" style="clear:both;max-height:350px;">
    <table class="table table-striped b-t b-light" style=""  v-if="(LsRateVisibili.length > 0)">
      <thead>
        <tr>
          <th style="width:7%">Data</th>
          <th style="width:11%">Importo</th>
          <th style="width:7%">Data Pagamento</th>
          <th style="width:20%">Conto Corrente/Cassa</th>
          <th style="width:33%">Note</th>
          <th style="width:2%">No PN</th>
          <th style="width:2%">RDA</th>
          <th style="width:2%">Non scalata</th>
          <th style="width:15%">Movim. correlato</th>
          <th style="width:3%"></th>
        </tr>
      </thead>
      <tbody>
        <tr style="clear: both; width: 100%"
                    v-for="Rata in LsRateVisibili" :key="Rata.Chiave">
          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
            <input :readonly="Rata.Dati.IdMovimento != null" class="form-control" type="date" v-model="Rata.Dati.Data">
          </td>
          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
            <input :readonly="Rata.Dati.IdMovimento != null" class="form-control" type="number" step="0.01" min="0" v-model="Rata.Dati.Importo"/>
          </td>
          <!-- <td v-if="Rata.Dati.DataPagamento == '' || Rata.Dati.DataPagamento == null" style=" background-color:white;padding-left:30px; padding-top:20px; color:red">
            <b>Rata da Pagare</b>
          </td> -->
          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
            <input :readonly="Rata.Dati.IdMovimento != null" class="form-control" type="date" v-model="Rata.Dati.DataPagamento" @input="OnInputDataPagamento(Rata)"/>
            <label v-if="((Rata.Dati.IdContoCasse != '' && Rata.Dati.IdContoCasse != undefined && Rata.Dati.IdContoCasse != null && Rata.Dati.IdContoCasse != -1) || Rata.Dati.IsRitenutaDiAcconto) && 
                          (Rata.Dati.DataPagamento == null || Rata.Dati.DataPagamento == undefined || Rata.Dati.DataPagamento == '')" class="ZMFormLabelError">Inserire un data valida</label>
          </td>
          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
            <VUEInputContoCorrente :disabled="(Rata.Dati.IdMovimento != null || Rata.Dati.NoPrimaNota || Rata.Dati.IsRitenutaDiAcconto) && !Rata.Dati.IsNonScalata" v-model="Rata.Dati.IdContoCasse"/>
            <label v-if="(Rata.Dati.DataPagamento != '' && Rata.Dati.DataPagamento != undefined && Rata.Dati.DataPagamento != null) && (Rata.Dati.IdContoCasse == -1 || Rata.Dati.IdContoCasse == null) && !Rata.Dati.NoPrimaNota && (!Rata.Dati.IsRitenutaDiAcconto || Rata.Dati.IsNonScalata)" class="ZMFormLabelError">Inserire il conto corrente/cassa</label>
          </td>
          <td style=" border:1px solid #ddd;border-bottom:0; background-color:white">
            <textarea @input="OnInputNoteRata(Rata)" :style="{height : Rata.AltezzaTextArea? Rata.AltezzaTextArea : '34px'}" class="form-control" wrap="off" type="text" v-model="Rata.Dati.Note" style="resize:none; width:100%; scrollbar-width: thin;"/>
          </td>
          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
            <input :disabled="Rata.Dati.IdMovimento != null" class="form-control" type="checkbox" v-model="Rata.Dati.NoPrimaNota" @change="OnChangeNoPrimaNota(Rata)" style="transform: translateY(0.7%)"/>
          </td>
          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white;">
            <input :disabled="Rata.Dati.IdMovimento != null" class="form-control" type="checkbox" v-model="Rata.Dati.IsRitenutaDiAcconto" @change="OnChangeRitenutaDiAcconto(Rata)" style="transform: translateY(0.7%); width: 70%; margin-left: 15%;"/>
          </td>
          
          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white;">
            <input :disabled="Rata.Dati.IdMovimento != null || !Rata.Dati.IsRitenutaDiAcconto" class="form-control" type="checkbox" v-model="Rata.Dati.IsNonScalata" @change="OnChangeNonScalata(Rata)" style="transform: translateY(0.7%); width: 41%; margin-left:29.5%;"/>
          </td>

          <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
            <label v-if="Rata.Dati.IdMovimento != null" style="font-size:15px; font-weight:bold;">Rata associata al movimento del {{ Rata.DataMovimento }}</label>
          </td>
          <td style="padding:2px;border:1px solid #ddd;border-bottom:0;padding-top:1.3%; background-color:white">
            <a v-if="Rata.Dati.IdMovimento == null" @click="OnClickEliminaRata(Rata)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px;padding-top:10px" title="Elimina Voce"><i class="fa fa-trash-o"></i></a>
          </td>
        </tr>
      </tbody>
    </table>
    <table v-else>
     <tbody>
      <tr><th>Inserire rate</th></tr>
     </tbody>
    </table>
  </div>
  
  </section>
  <button type="button" 
          class="btn btn-info" 
          style="width:150px; float:right; margin-right:10px"
          @click="OnClickNuovaRata()">
          Nuova rata
  </button>
  </div>
 </div>
</template>

<script>
import { SystemInformation } from '@/SystemInformation.js'
import { TSchedaGenerica } from '../../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { FATT_ELE_ESIGIBILITA_IVA } from '@/SystemInformation.js'
import { TZDateFunct } from '../../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEInputContoCorrente from '@/components/InputComponents/VUEInputContoCorrente.vue'

export class TSingolaRata
{
   constructor(Chiave, Data, DataPagamento, Importo, Note, IdDocumento, IdContoCasse, IdMovimento, NoPrimaNota, IsRitenutaDiAcconto, IsNonScalata)
   {
      this.Dati = {}
      this.Dati.Chiave              = Chiave;
      this.Dati.Data                = Data;
      this.Dati.DataPagamento       = DataPagamento
      this.Dati.Note                = Note
      this.Dati.Importo             = Importo
      this.Dati.IdDocumento         = IdDocumento
      
      this.Dati.IdContoCasse        = IdContoCasse
      this.Dati.IdMovimento         = IdMovimento
      this.Dati.DaEliminare         = false
      this.Dati.IsRitenutaDiAcconto = IsRitenutaDiAcconto
      this.Dati.IsNonScalata        = IsNonScalata
      this.Dati.NoPrimaNota         = NoPrimaNota

      this.Snapshot                = JSON.stringify(this.Dati)   
      if(Chiave == -1)
        this.Dati.DaRegistrare = true 
   }

   AllDataOk()
   {
     var Result = true
     if(!this.Dati.DaEliminare)
     {
        Result = this.Dati.Descrizione != ''

        if(!Result)
          return Result
        
        if(this.Dati.DataPagamento != '' && this.Dati.DataPagamento != undefined && this.Dati.DataPagamento != null)
        {
          if((this.Dati.IdContoCasse == -1 || this.Dati.IdContoCasse == null || this.Dati.IdContoCasse == null || this.Dati.IdContoCasse == '') && 
              !this.Dati.NoPrimaNota && 
              !this.Dati.IsRitenutaDiAcconto)
            Result = false;
          if(this.Dati.IsNonScalata && 
             this.Dati.IsRitenutaDiAcconto &&
             (this.Dati.IdContoCasse == -1 || this.Dati.IdContoCasse == null || this.Dati.IdContoCasse == null || this.Dati.IdContoCasse == ''))
              Result = false;
        }
        else
        {
          if(this.Dati.IsRitenutaDiAcconto || 
             (this.Dati.IdContoCasse != null && this.Dati.IdContoCasse != -1 && this.Dati.IdContoCasse != undefined && this.Dati.IdContoCasse != ''))
            Result = false
        }
     }
     return Result
   }

   AllRatePagate()
   {
      if(!this.Dati.DaEliminare)
      {
        if(this.Dati.DataPagamento == '')
          return false
        if(this.Dati.Importo == 0)
          return false
        if(this.Dati.IdContoCasse == -1 || this.Dati.IdContoCasse == null)
          return false
      }
      return true
   }
  
   PrepareQueryParameters(Operazioni)
   {
     if(this.Dati.Chiave == -1 && !this.Dati.DaEliminare )
     {
        Operazioni.push({
                              Query     : 'InserisciRata',
                              Parametri : { 
                                            CHIAVE              : undefined,
                                            DATA                : TSchedaGenerica.PrepareForRecordDate(this.Dati.Data),
                                            DATA_PAGAMENTO      : this.Dati.IdMovimento  != null ? null : TSchedaGenerica.PrepareForRecordDate(this.Dati.DataPagamento),
                                            NOTE                : TSchedaGenerica.PrepareForRecordString(this.Dati.Note),
                                            IMPORTO             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Importo * 100),
                                            ID_FATTURA          : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdDocumento),
                                            ID_CONTO_CASSE      : this.Dati.IdMovimento  != null ? null : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdContoCasse),
                                            NO_PRIMA_NOTA       : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.NoPrimaNota),
                                            IS_RITENUTA_ACCONTO : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.IsRitenutaDiAcconto),
                                            IS_NON_SCARICATA    : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.IsNonScalata),
                                          },
                              ResetKeys   : [2]
        })
       
     }
     else
     {
        if(this.Dati.Chiave != -1)
        {
            if(this.Dati.DaEliminare)
            {
              Operazioni.push({
                                        Query     : 'EliminaRata',
                                        Parametri : { CHIAVE      : this.Dati.Chiave }
              })
            }
            else
            {
              if(this.Modificato() )
              {
                Operazioni.push({
                                          Query     : 'ModificaRata',
                                          Parametri : { 
                                                        CHIAVE              : this.Dati.Chiave,
                                                        DATA                : TSchedaGenerica.PrepareForRecordDate(this.Dati.Data),
                                                        DATA_PAGAMENTO      : this.Dati.IdMovimento  != null ? null : TSchedaGenerica.PrepareForRecordDate(this.Dati.DataPagamento),
                                                        NOTE                : TSchedaGenerica.PrepareForRecordString(this.Dati.Note),
                                                        IMPORTO             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Importo * 100),
                                                        ID_FATTURA          : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdDocumento),
                                                        ID_CONTO_CASSE      : this.Dati.IdMovimento  != null ? null : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdContoCasse),
                                                        NO_PRIMA_NOTA       : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.NoPrimaNota),
                                                        IS_RITENUTA_ACCONTO : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.IsRitenutaDiAcconto),
                                                        IS_NON_SCARICATA    : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.IsNonScalata),
                                                      }
                })
              }
            }
        }
     }
     return Operazioni
   }

   Modificato()
   {
     return this.Snapshot != JSON.stringify(this.Dati)
   }

   DaRegistrare()
   {
     return this.Snapshot != JSON.stringify(this.Dati)
   }
}

export class TSchedaRate
{
   constructor()
   {
      this.IdDocumento = -1
      this.LsRate = []
   }

   Modificato()
   {
      for(var i = 0; i < this.LsRate.length; i++)
         if(this.LsRate[i].Modificato())
            return true
      return false;
   }

   PrepareQueryParameters(Operazioni)
   {
      for(var i = 0; i < this.LsRate.length; i++)
        if(this.LsRate[i].Dati.DaEliminare  || this.LsRate[i].Dati.Chiave == -1 || this.LsRate[i].Modificato())
          this.LsRate[i].PrepareQueryParameters(Operazioni)
      return Operazioni
   }

   AllDataOk()
   {
    for(var i = 0; i < this.LsRate.length; i++)
        if(!this.LsRate[i].AllDataOk())
          return false
    return true 
   }

   CheckBeforeRegistrazione(TotaleFattura, SommaIva, RateNoteDiCreditoCorrelate)
   {
      var ControlloTotale = 0
      
      for(let i = 0; i < this.LsRate.length; i++)
        if(!this.LsRate[i].Dati.DaEliminare)
          ControlloTotale = ControlloTotale + this.LsRate[i].Dati.Importo

      if(RateNoteDiCreditoCorrelate != null && RateNoteDiCreditoCorrelate.length != 0)
      {
        RateNoteDiCreditoCorrelate.forEach(function(Rata)
        {
          ControlloTotale += Rata.IMPORTO / 100;
          ControlloTotale = parseFloat(ControlloTotale).toFixed(2);
        })
      }
      
      if(this.EsigibilitaIva == FATT_ELE_ESIGIBILITA_IVA.Scissione)
        TotaleFattura = TotaleFattura - SommaIva

      let DataScadenza = TZDateFunct.DateInHTMLInputFormat(new Date())
      ControlloTotale  = parseFloat((parseFloat(ControlloTotale)).toFixed(2))
      TotaleFattura    = parseFloat((parseFloat(TotaleFattura)).toFixed(2))
      
      if(ControlloTotale < TotaleFattura)
      {
        let SingolaRata = new TSingolaRata(-1, DataScadenza, '', parseFloat((TotaleFattura - ControlloTotale).toFixed(2)),'', this.IdDocumento, -1, null, false, false, false)
        this.LsRate.push(SingolaRata)
        this.Snapshot   = JSON.stringify(this.LsRate)   
      }
   }

   Ricalcola(IdCondizionePagamento, TotaleFattura, SommaIva, OnSuccess, APartireDallaData, RateNoteDiCreditoCorrelate)
   {
    if(IdCondizionePagamento == undefined || IdCondizionePagamento == -1)
      return
    for(let i = 0; i < this.LsRate.length; i++)
      if(!this.LsRate[i].Dati.DaEliminare)
        if((this.LsRate[i].Dati.DataPagamento != undefined && this.LsRate[i].Dati.DataPagamento != '') || 
           this.LsRate[i].Dati.IdMovimento != null || 
           (RateNoteDiCreditoCorrelate != null && RateNoteDiCreditoCorrelate.length != 0))
        {
          if(!this.ControlloTotaleRate(TotaleFattura, SommaIva, RateNoteDiCreditoCorrelate))
          {
            this.CheckBeforeRegistrazione(TotaleFattura, SommaIva, RateNoteDiCreditoCorrelate)
          }
          if(OnSuccess != undefined)
            OnSuccess()
          return
        }

    if(RateNoteDiCreditoCorrelate != null && RateNoteDiCreditoCorrelate.length != 0)
    {
      if(!this.ControlloTotaleRate(TotaleFattura, SommaIva, RateNoteDiCreditoCorrelate))
      {
        this.CheckBeforeRegistrazione(TotaleFattura, SommaIva, RateNoteDiCreditoCorrelate)
      }
      if(OnSuccess != undefined)
        OnSuccess()
      return
    }
  
    for(let i = 0; i < this.LsRate.length; i++)
        this.LsRate[i].Dati.DaEliminare = true
    var Self = this
    SystemInformation.AdvQuery.GetSQL('Fatture', { IdCond : IdCondizionePagamento }, 
                                function (Results) 
                                {
                                  let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "RateXCondPagamento");
                                  if (ArrayInfo != undefined)
                                  {
                                    ControlloRate(ArrayInfo[0], TotaleFattura, SommaIva, APartireDallaData)
                                    if(OnSuccess != undefined)
                                      OnSuccess()
                                  }
                                  },function(HTTPError,SubHTTPError,Response)
                                  {
                                    SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                  },
                                  'RateXCondPagamento')

    var ControlloRate = function(DatiCondPagamento, TotaleFattura, SommaIva, APartireDallaData)
    {
      //var StartDate = ''
      var NdxRate    = 1
      var DataRate   = []
      var ValoreRata = []

      if(DatiCondPagamento.P30GG == 'T')
      {
        var Data30gg = new Date();
        if(APartireDallaData != undefined)
          Data30gg = new Date(APartireDallaData);
        DataRate[NdxRate] = TZDateFunct.DateInHTMLInputFormat(new Date(Data30gg.setDate(Data30gg.getDate() + 30)))
        if(DatiCondPagamento.FINE_MESE == 'T')
        {
          DataRate[NdxRate] = TZDateFunct.GetLastDateMonth(DataRate[NdxRate])
        }
        NdxRate = NdxRate + 1
      }

      if(DatiCondPagamento.P60GG == 'T')
      {
        var Data60gg = new Date();
        if(APartireDallaData != undefined)
          Data60gg = new Date(APartireDallaData);
        DataRate[NdxRate] = TZDateFunct.DateInHTMLInputFormat(new Date(Data60gg.setDate(Data60gg.getDate() + 60)))
        if(DatiCondPagamento.FINE_MESE == 'T')
        {
          DataRate[NdxRate] = TZDateFunct.GetLastDateMonth(DataRate[NdxRate])
        }
        NdxRate = NdxRate + 1
      }

      if(DatiCondPagamento.P90GG == 'T')
      {
        var Data90gg = new Date();
        if(APartireDallaData != undefined)
          Data90gg = new Date(APartireDallaData);
        DataRate[NdxRate] = TZDateFunct.DateInHTMLInputFormat(new Date(Data90gg.setDate(Data90gg.getDate() + 90)))
        if(DatiCondPagamento.FINE_MESE == 'T')
        {
          DataRate[NdxRate] = TZDateFunct.GetLastDateMonth(DataRate[NdxRate])
        }
        NdxRate = NdxRate + 1
      }

      if(DatiCondPagamento.P120GG == 'T')
      {
        var Data120gg = new Date();
        if(APartireDallaData != undefined)
          Data120gg = new Date(APartireDallaData);
        DataRate[NdxRate] = TZDateFunct.DateInHTMLInputFormat(new Date(Data120gg.setDate(Data120gg.getDate() + 120)))
        if(DatiCondPagamento.FINE_MESE == 'T')
        {
          DataRate[NdxRate] = TZDateFunct.GetLastDateMonth(DataRate[NdxRate])
        }
        NdxRate = NdxRate + 1
      }

      var TotaleRate = 0
      if(Self.EsigibilitaIva == FATT_ELE_ESIGIBILITA_IVA.Scissione)
      {
        TotaleRate = TotaleFattura - SommaIva
        TotaleRate = parseFloat((TotaleRate).toFixed(2))
      }
      else TotaleRate = TotaleFattura

      var NumeroRate = NdxRate - 1
      switch (NumeroRate) 
      {
        case 0 : ValoreRata[0] = TotaleRate;
                 break;
        case 1 : ValoreRata[0] = TotaleRate;
                 break;
        case 2 : ValoreRata[0] = parseFloat((TotaleRate / 2).toFixed(2)); ValoreRata[1] = ValoreRata[0];
                 break;
        case 3 : ValoreRata[0] = parseFloat((TotaleRate / 3).toFixed(2)); ValoreRata[1] = ValoreRata[0]; ValoreRata[2] = ValoreRata[0];
                 break;
        case 4 : ValoreRata[0] = parseFloat((TotaleRate / 4).toFixed(2)); ValoreRata[1] = ValoreRata[0]; ValoreRata[2] = ValoreRata[0];  ValoreRata[4] = ValoreRata[0];
                 break;
      }

      if(NumeroRate > 1)
      {
        let SommaPrimeRate = 0 
        for(let j = 0; j < ValoreRata.length - 1; j++)
            SommaPrimeRate = SommaPrimeRate + ValoreRata[j]

        ValoreRata[ValoreRata.length - 1] = parseFloat((TotaleRate - SommaPrimeRate).toFixed(2));
      }

      if(NumeroRate == 0)
      {
        let SingolaRata = new TSingolaRata(-1, APartireDallaData != undefined? TZDateFunct.DateInHTMLInputFormat(new Date(APartireDallaData)) : TZDateFunct.DateInHTMLInputFormat(new Date()), '', ValoreRata[0], '', Self.IdDocumento, -1, null, false, false, false)
        Self.LsRate.push(SingolaRata)
      }
      else
      {
        for(let i = 0; i < NumeroRate; i++)
        {
          let SingolaRata = new TSingolaRata(-1, DataRate[i+1], '', ValoreRata[i], '', Self.IdDocumento, -1, null, false, false, false)
          Self.LsRate.push(SingolaRata)
        }
      }
    }   
   }

   ControlloTotaleRate(TotaleFattura, SommaIva, RateNoteDiCreditoCorrelate)
   {
    var TotalePagato = 0

    for(var i = 0; i < this.LsRate.length; i++)
      if((this.LsRate[i].DATA_PAGAMENTO != null && this.LsRate[i].DATA_PAGAMENTO != ''))
        TotalePagato = TotalePagato + this.LsRate[i].IMPORTO


    if(RateNoteDiCreditoCorrelate != null && RateNoteDiCreditoCorrelate.length > 0)
    {
      RateNoteDiCreditoCorrelate.forEach(function(Rata)
      {
        TotalePagato += Rata.IMPORTO / 100
        TotalePagato  = parseFloat(TotalePagato.toFixed(2))
      })
    }
      
    
    if(this.EsigibilitaIva == FATT_ELE_ESIGIBILITA_IVA.Scissione)
    {
      TotaleFattura = TotaleFattura - SommaIva
      TotaleFattura    = parseFloat(TotaleFattura.toFixed(2))
    }

    TotalePagato    = parseFloat(TotalePagato.toFixed(2))

    if(TotalePagato < TotaleFattura)
      return false
    if(TotalePagato >= TotaleFattura)
      return true
   }

   ControlloPresenzaRate()
   {
      let Result = false;
      if(this.LsRate.length == 0)
        return Result
      for(var i = 0; i < this.LsRate.length; i++)
        if(!this.LsRate[i].Dati.DaEliminare)
          Result = true
      return Result;
   }

   ControlloRatePagate()
   {
      if(!this.ControlloPresenzaRate())
        return false
      for(var i = 0; i < this.LsRate.length; i++)
        if(!this.LsRate[i].AllRatePagate())
          return false
      return true 
   }

   SetIdDocumento(IdDocumento)
   {
     this.IdDocumento = IdDocumento
   }

   AssignDati(LsRate,EsigibilitaIva)
   {
      this.LsRate         = []
      this.EsigibilitaIva = EsigibilitaIva
      for(var i = 0; i < LsRate.length; i++)
      {
          var SingolaRata = new TSingolaRata(LsRate[i].CHIAVE, 
                                             LsRate[i].DATA, 
                                             LsRate[i].DATA_PAGAMENTO, 
                                             parseFloat((LsRate[i].IMPORTO / 100).toFixed(2)),
                                             LsRate[i].NOTE, 
                                             LsRate[i].ID_FATTURA, 
                                             LsRate[i].ID_CONTO_CASSE,
                                             LsRate[i].ID_MOVIMENTO,
                                             TSchedaGenerica.DisponiFromBoolean(LsRate[i].NO_PRIMA_NOTA),
                                             TSchedaGenerica.DisponiFromBoolean(LsRate[i].IS_RITENUTA_ACCONTO),
                                             TSchedaGenerica.DisponiFromBoolean(LsRate[i].IS_NON_SCARICATA))

          if(LsRate[i].ID_MOVIMENTO != null)
            SingolaRata.DataMovimento = TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(LsRate[i].DATA_MOVIMENTO))
          this.SetAltezzaTextarea(SingolaRata)
          this.LsRate.push(SingolaRata)
      }
      this.Snapshot = JSON.stringify(this.LsRate);
   }


   ClearListaRate()
   {
      this.LsRate = []
   }

   SetAltezzaTextarea(Rata)
   { 
      if(Rata.Dati.Note != undefined)
      {
        let NrRighe = Rata.Dati.Note.split("\n").length
        Rata.AltezzaTextArea = NrRighe  <= 1 ? '34px' : NrRighe * 22  + 'px'
      }
   }

   GestioneSaldiChiusuraAnnuali(Operazioni, IdCliente)
   {
      for(var i = 0; i < this.LsRate.length; i++)
      {
        if(this.LsRate[i].Dati.Data != '' && this.LsRate[i].Dati.Data != null)
          Operazioni.push({
                            Query     : "EliminaRecordSaldiChiusureAnnuali",
                            Parametri : {
                                          CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(IdCliente),
                                          ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.LsRate[i].Dati.Data)))
                                        }
                        });

        if(this.LsRate[i].Dati.DataPagamento != '' && this.LsRate[i].Dati.DataPagamento != null)
          Operazioni.push({
                              Query     : "EliminaRecordSaldiChiusureAnnuali",
                              Parametri : {
                                            CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(IdCliente),
                                            ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.LsRate[i].Dati.DataPagamento)))
                                          }
                          });
      }
   }
}

export default {

 components : 
 {
  VUEInputContoCorrente
 },

 emits: ['onChange'],
 data()
 {
   return {
             InserimentoNuovaRata : {},
             NumeroRate           : 1,
   }
 },

 props : ['SchedaRate','CondPagamento'],

 watch:
  {
    SchedaRate:
    {
      handler()
      {
        this.$emit('onChange') 
      },
      deep: true
    }
  },

 computed :
 {
    LsRateVisibili :
    {
        get()
        {
          var Result = []
          this.SchedaRate.LsRate.forEach(function(ARate)
          {
              if(!ARate.Dati.DaEliminare)
                Result.push(ARate)
          })
          return Result;
        }
    },
 },

 methods: 
 {
  OnClickEliminaRata(Rata)
  {
      Rata.Dati.DaEliminare = true
  },

  OnClickNuovaRata()
  {
    this.InserimentoNuovaRata = {}
    this.InserimentoNuovaRata = new TSingolaRata(-1, '','', 0, '',this.SchedaRate.IdDocumento, -1, null, false, false, false)
    this.SchedaRate.LsRate.push(this.InserimentoNuovaRata)
    this.$emit('onChange')
  },
  
  OnInputNoteRata(Rata)
  {
    this.SchedaRate.SetAltezzaTextarea(Rata)
  },

  OnInputDataPagamento(Rata)
  {
    if(Rata.Dati.DataPagamento > TZDateFunct.DateInHTMLInputFormat(new Date()))
      Rata.Dati.DataPagamento = TZDateFunct.DateInHTMLInputFormat(new Date())
    if(Rata.Dati.DataPagamento == '')  
      if(Rata.Dati.IdContoCasse != -1 && Rata.Dati.IdContoCasse != null)
        Rata.Dati.IdContoCasse = -1
  },

  OnChangeNoPrimaNota(Rata)
  {
    if(Rata.Dati.NoPrimaNota)
      Rata.Dati.IdContoCasse = -1
  },

  OnChangeRitenutaDiAcconto(Rata)
  {
    if(Rata.Dati.IsRitenutaDiAcconto)
      Rata.Dati.IdContoCasse = -1
    else Rata.Dati.IsNonScalata = false
  },

  OnChangeNonScalata(Rata)
  {
    if(Rata.Dati.IsRitenutaDiAcconto)
      Rata.Dati.IdContoCasse = -1
  },

 }

}
</script>