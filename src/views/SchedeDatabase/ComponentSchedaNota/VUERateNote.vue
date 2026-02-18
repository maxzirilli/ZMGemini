<template>

  <VUEModal v-if="PopupLsFatture" :PathLogo="require('@/assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Lista Fatture '" :Altezza="'500px'" :Larghezza="'600px'"
            @onClickChiudiModal="OnClickAnnullaFattura">
    <template v-slot:Body>
        <!-- <input type="checkbox" style="width:15px;float:left;margin-left:15px" class="input-sm form-control" v-model="CercaPerSottostringaCodice">
        <label style="float:left;margin-left:10px; margin-top:7px;font-weight:bold;font-size:15px;width:30%">Cerca per sottostringa </label> -->
        <div style="clear:both;width:1%;height:3px">&nbsp;</div>
          <input type="text" style="width:40%;float:left" class="input-sm form-control" placeholder="Cerca per Numero Fattura" v-model="FiltroFatturaNumero">
          <div style="width:1%;float:left">&nbsp;</div>
          <!-- <input type="text" style="width:76%;float:left" class="input-sm form-control" placeholder="Cerca per descrizione" v-model="FiltroProdottiDescrizione"> -->
          <div style="clear:both;width:1%;height:10px">&nbsp;</div>
       
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:1%;position: sticky; top: 0;"></th>
                      <th style="width:8%;position: sticky; top: 0;">N. Avviso Fattura</th>
                      <th style="width:8%;position: sticky; top: 0;">Numero fattura</th>
                    </tr>
                  </thead>
                  <tbody v-if="ListaFatture.length > 0">
                    <tr v-for="Fattura in ListaFattureFiltrate" :key="Fattura.CHIAVE">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="radio" name="fattura" value='T' style="height: 20px;" class="form-control" v-model="Fattura.Selezionata" @change="ResetRadio(Fattura.CHIAVE)">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Fattura.CHIAVE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Fattura.NUMERO != undefined ? Fattura.NUMERO : 'Numero non presente'}}
                      </td>
                    </tr>
                    <tr v-if="ListaFatture.length == NumeroMassimoFatture">
                      <td colspan="7" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:right;vertical-align: middle;color:red">
                        Sono presenti più di 100 fatture...
                      </td>
                    </tr>
                  </tbody>
                </table>
                <label v-if="ListaFattureFiltrate.length == 0" style="text-align: center; padding: 10px;">Non sono presenti fatture</label>
              </div>
            </section>
          </div>
    </template>
    <template v-slot:Footer>
     <button class="btn btn-danger" @click="OnClickAnnullaFattura" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button v-if="FatturaSelezionata" class="btn btn-success" @click="OnClickConfermaFattura" style="float:right;margin-right:20px; width:20%">Conferma</button>    
    </template>
  </VUEModal>  

  <div>
    <div class="row wrapper">
      <div class="ZMSeparatoreScheda">       
        <text style="font-weight: bold;">Rate Nota</text>
        <hr style="margin-top:5px;margin-bottom:0px">
      </div>
    
      <section class="panel panel-default" style="background-color:white">
        <div class="table-responsive" style="clear:both;max-height:350px;">
          <table class="table table-striped b-t b-light" v-if="(LsRateNotaVisibili.length > 0)">
            <thead>
              <tr>
                <th style="width:7%">Data</th>
                <th style="width:11%">Importo</th>
                <th style="width:7%">Data Pagamento</th>
                <th style="width:20%">Conto Corrente/Cassa</th>
                <th style="width:33%">Note</th>
                <th style="width:2%">Scalata in fatt.</th>
                <th style="width:20%">Fatture Correlate</th>
                <th style="width:3%"></th>
              </tr>
            </thead>
            <tbody>
              <tr style="clear: both; width: 100%" v-for="Rata in LsRateNotaVisibili" :key="Rata.Chiave">
                
                <!-- Data -->
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <input class="form-control" type="date" v-model="Rata.Dati.Data">
                </td>
                
                <!-- Importo -->
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <input class="form-control" type="number" step="0.01" min="0" v-model="Rata.Dati.Importo"/>
                </td>
                
                <!-- Data Pagamento -->
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white" >
                  <input :disabled = "Rata.Dati.NumeroFattura != null || Rata.Dati.ScalataInFattura" class="form-control" type="date" v-model="Rata.Dati.DataPagamento" @input="OnInputDataPagamento(Rata)">
                </td>
                
                <!-- Conto Corrente/Cassa -->
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <VUEInputContoCorrente v-model="Rata.Dati.IdContoCasse" :disabled = "Rata.Dati.NumeroFattura != null || Rata.Dati.ScalataInFattura"/>
                  <label v-if="(Rata.Dati.DataPagamento != '' && Rata.Dati.DataPagamento != undefined && Rata.Dati.DataPagamento != null) && (Rata.Dati.IdContoCasse == -1 || Rata.Dati.IdContoCasse == null) && !Rata.Dati.ScalataInFattura" class="ZMFormLabelError">Inserire il conto corrente/cassa</label>
                </td>
                
                <!-- Note -->
                <td style=" border:1px solid #ddd;border-bottom:0; background-color:white">
                  <textarea  @input="OnInputNoteRata(Rata)" :style="{height : Rata.AltezzaTextArea? Rata.AltezzaTextArea : '34px'}" v-model="Rata.Dati.Note" class="form-control" wrap="off" type="text" style="resize:none; width:100%; scrollbar-width: thin;"/>
                </td>

                <!-- Scalata in fattura -->
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white">
                  <input class="form-control" type="checkbox" style="width: 23px;" v-model="Rata.Dati.ScalataInFattura" @change="OnChangeScalataInFattura(Rata)"/>
                </td>

                <!-- Fatture Correlate -->
                <td style=" border:1px solid #ddd; border-bottom:0; background-color:white; text-align: center;">
                  <button type="button" class="btn btn-info" :disabled="!Rata.Dati.ScalataInFattura"
                          v-if="Rata.Dati.IdFatturaCorrelata == null || Rata.Dati.IdFatturaCorrelata == undefined || Rata.Dati.IdFatturaCorrelata == -1"
                          @click="OnclickCorrelaFattura(Rata)"> 
                          
                          Correla Fattura
                        
                  </button>
                  <div v-if = "Rata.Dati.IdFatturaCorrelata != null && Rata.Dati.IdFatturaCorrelata != undefined && Rata.Dati.IdFatturaCorrelata != -1"
                        style="display: flex;">
                    
                    <label v-if="Rata.Dati.NumeroFattura != null">Scalata dalla fattura n. {{ Rata.Dati.NumeroFattura }} </label>
                    <label v-else> Scalata da avviso fattura n. {{ Rata.Dati.IdFatturaCorrelata }}</label>

                    <a @click="OnClickEliminaFatturaCorrelata(Rata)" 
                      style="font-size:13px; color:#fb6b5b; cursor:pointer;" 
                      title="Elimina Fattura Correlata">&#x274C;
                    </a>

                  </div>
                  
                  <label v-if=" (Rata.Dati.ScalataInFattura && Rata.Dati.IdFatturaCorrelata == -1)" class="ZMFormLabelError">Inserire una fattura correlata</label>
                </td>
                
                <!-- Elimina -->
                <td style="padding:2px;border:1px solid #ddd;border-bottom:0;padding-top:1.3%; background-color:white">
                  <a @click="OnClickEliminaRata(Rata)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px;padding-top:10px" title="Elimina Voce"><i class="fa fa-trash-o"></i></a>
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
          @click="OnClickNuovaRataNota()">
          Nuova rata
      </button>
    </div>
  </div>

</template>

<script>

import { SystemInformation , NOME_PROGRAMMA } from '@/SystemInformation.js'
import { TSchedaGenerica } from '../../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import VUEInputContoCorrente from '@/components/InputComponents/VUEInputContoCorrente.vue'
import { TZDateFunct } from '../../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEModal from '../../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue'

export class TSingolaRata
{
  constructor(Chiave, IdDocumento, Data, Importo, DataPagamento, IdContoCasse, Note, ScalataInFattura = false, IdFatturaCorrelata, NumeroFattura)
   {
      this.Dati = {}
      this.Dati.Chiave              = Chiave;
      this.Dati.Data                = Data;
      this.Dati.IdDocumento         = IdDocumento;
      this.Dati.Importo             = Importo;
      this.Dati.DataPagamento       = DataPagamento;
      this.Dati.IdContoCasse        = IdContoCasse;
      this.Dati.Note                = Note;
      this.Dati.ScalataInFattura    = ScalataInFattura;
      this.Dati.IdFatturaCorrelata  = IdFatturaCorrelata;
      this.Dati.NumeroFattura       = NumeroFattura;
      this.Dati.DaEliminare         = false;
      
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
      
      if(this.Dati.DataPagamento == '' && this.Dati.DataPagamento == undefined && this.Dati.DataPagamento == null)
      {
        Result = false;
      }

      if(this.Dati.DataPagamento != '' && this.Dati.DataPagamento != undefined && this.Dati.DataPagamento != null)
      {
        if((this.Dati.IdContoCasse == -1 || this.Dati.IdContoCasse == null || this.Dati.IdContoCasse == null || this.Dati.IdContoCasse == '') && 
            !this.Dati.ScalataInFattura)
          Result = false;
      }

      if(this.Dati.ScalataInFattura && this.Dati.IdFatturaCorrelata == -1)
      {
        Result = false;
      }
    }
    return Result
  }
  
  PrepareQueryParameters(Operazioni)
  {
    if(this.Dati.Chiave == -1 && !this.Dati.DaEliminare )
    {
      Operazioni.push({
                            Query     : 'InserisciRata',
                            Parametri : { 
                                          CHIAVE              : undefined,
                                          ID_NOTA             : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdDocumento),
                                          DATA                : TSchedaGenerica.PrepareForRecordDate(this.Dati.Data),
                                          DATA_PAGAMENTO      : TSchedaGenerica.PrepareForRecordDate(this.Dati.DataPagamento),
                                          NOTE                : TSchedaGenerica.PrepareForRecordString(this.Dati.Note),
                                          IMPORTO             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Importo * 100),
                                          ID_FATTURA          : this.Dati.IdFatturaCorrelata != -1 ? TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdFatturaCorrelata) : undefined,
                                          ID_CONTO_CASSE      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdContoCasse),
                                          SCALATA_IN_FATTURA  : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.ScalataInFattura)
                                        },
                            ResetKeys   : [3]
      })
      
    }
    else
    {
      if(this.Dati.Chiave != -1)
      {
          if(this.Dati.DaEliminare)
          {
            Operazioni.push({
                                      Query : 'EliminaFatturaCorrelata',
                                      Parametri : { CHIAVE      : this.Dati.Chiave }
                            })
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
                                                      ID_NOTA             : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdDocumento),
                                                      DATA                : TSchedaGenerica.PrepareForRecordDate(this.Dati.Data),
                                                      DATA_PAGAMENTO      : this.Dati.IdMovimento  != null ? null : TSchedaGenerica.PrepareForRecordDate(this.Dati.DataPagamento),
                                                      NOTE                : TSchedaGenerica.PrepareForRecordString(this.Dati.Note),
                                                      IMPORTO             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Importo * 100),
                                                      ID_FATTURA          : this.Dati.IdFatturaCorrelata != -1 ? TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdFatturaCorrelata) : undefined,
                                                      ID_CONTO_CASSE      : this.Dati.IdMovimento  != null ? null : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdContoCasse),
                                                      SCALATA_IN_FATTURA  : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.ScalataInFattura)
                                                      
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

export class TSchedaRateNota
{
  constructor()
  {
    this.IdDocumento = -1
    this.LsRateNota = []
    this.OldChiaviFatture = []
  }

  Modificato()
  {
    for(var i = 0; i < this.LsRateNota.length; i++)
        if(this.LsRateNota[i].Modificato())
          return true
    return false;
  }

  PrepareQueryParameters(Operazioni)
  {
    for(var i = 0; i < this.LsRateNota.length; i++)
      if(this.LsRateNota[i].Dati.DaEliminare  || this.LsRateNota[i].Dati.Chiave == -1 || this.LsRateNota[i].Modificato())
        this.LsRateNota[i].PrepareQueryParameters(Operazioni)
    return Operazioni
  }

  AllDataOk()
  {
    for(var i = 0; i < this.LsRateNota.length; i++)
        if(!this.LsRateNota[i].AllDataOk())
          return false
    return true 
  }

  CalcolaRate(TotaleNota)
  {
    let Data = TZDateFunct.DateInHTMLInputFormat(new Date());
    
    if(this.LsRateNota.length == 0)
    {
      TotaleNota = parseFloat((parseFloat(TotaleNota)).toFixed(2))

      let SingolaRata = new TSingolaRata(
                                          -1, 
                                          this.IdDocumento, 
                                          Data,
                                          TotaleNota, 
                                          '', 
                                          -1,
                                          '',
                                          false, 
                                          -1,
                                          null
                                        )
      this.LsRateNota.push(SingolaRata)
      this.Snapshot = JSON.stringify(this.LsRateNota)
    }
    else
    {
      if(this.LsRateNota.length > 0)
      {
        let ImportoTotaleRate = 0

        for(let i = 0; i < this.LsRateNota.length; i++)
        {
          if(!this.LsRateNota[i].Dati.DaEliminare)
            ImportoTotaleRate += this.LsRateNota[i].Dati.Importo
        }
        
        TotaleNota = parseFloat((parseFloat(TotaleNota)).toFixed(2))
        ImportoTotaleRate = parseFloat((parseFloat(ImportoTotaleRate)).toFixed(2))
        
        
        if(ImportoTotaleRate < TotaleNota)
        {
          if(ImportoTotaleRate > 0)
          {
            let SingolaRata = new TSingolaRata(
                                              -1, 
                                              this.IdDocumento, 
                                              Data,
                                              parseFloat((TotaleNota - ImportoTotaleRate).toFixed(2)), 
                                              '', 
                                              -1,
                                              '',
                                              false, 
                                              -1,
                                              null
                                            )
            this.LsRateNota.push(SingolaRata)
          }
          else if(ImportoTotaleRate <= 0)
          {
            // for(let i = 0; i < this.LsRateNota.length; i++)
            // {
            //   if(this.LsRateNota[i].Dati.Importo <= 0)
            //     this.LsRateNota[i].Dati.DaEliminare = true
            // }
            let SingolaRata = new TSingolaRata(
                                                -1, 
                                                this.IdDocumento, 
                                                Data,
                                                parseFloat((TotaleNota).toFixed(2)), 
                                                '', 
                                                -1,
                                                '',
                                                false, 
                                                -1,
                                                null
                                              )
            this.LsRateNota.push(SingolaRata)
          }
        }

        // Ha senso però per ora lo commentiamo
        // if(ImportoTotaleRate > TotaleNota)
        // {
        //   let Eccesso = (ImportoTotaleRate - TotaleNota);
          
        //   Eccesso = parseFloat((parseFloat(Eccesso)).toFixed(2))

        //   if(this.LsRateNota.length == 1)
        //     this.LsRateNota[0].Dati.Importo = parseFloat((this.LsRateNota[0].Dati.Importo - Eccesso).toFixed(2))

        //   if(this.LsRateNota.length > 1)
        //   {
        //     for(let i = (this.LsRateNota.length - 1); i > 0; i--)
        //     {
        //       if(this.LsRateNota[i].Dati.Importo > Eccesso)
        //       {
        //         this.LsRateNota[i].Dati.Importo = parseFloat((this.LsRateNota[i].Dati.Importo - Eccesso).toFixed(2))
        //         break;
        //       }

        //       if(this.LsRateNota[i].Dati.Importo <= Eccesso)
        //       {
        //         Eccesso = Eccesso - this.LsRateNota[i].Dati.Importo
        //         this.LsRateNota[i].Dati.DaEliminare = true;
        //       }
        //     }
        //   }
        // }
      }
    }
  }

  SetIdDocumento(IdDocumento)
  {
    this.IdDocumento = IdDocumento
  }

  AssignDati(LsRate)
  {
    this.LsRateNota = []
    this.OldChiaviFatture = []
    for(var i = 0; i < LsRate.length; i++)
    {
        var SingolaRata = new TSingolaRata( LsRate[i].CHIAVE,
                                            LsRate[i].ID_NOTA, 
                                            LsRate[i].DATA,
                                            parseFloat((LsRate[i].IMPORTO / 100).toFixed(2)),
                                            LsRate[i].DATA_PAGAMENTO, 
                                            LsRate[i].ID_CONTO_CASSE,
                                            LsRate[i].NOTE,
                                            TSchedaGenerica.DisponiFromBoolean(LsRate[i].SCALATA_IN_FATTURA), 
                                            TSchedaGenerica.DisponiFromListIndex(LsRate[i].ID_FATTURA),
                                            LsRate[i].NUMERO
                                          )

        
        this.SetAltezzaTextarea(SingolaRata)
        if(LsRate[i].ID_FATTURA != null)
          this.OldChiaviFatture.push(LsRate[i].ID_FATTURA)
        this.LsRateNota.push(SingolaRata)
    }
    this.Snapshot = JSON.stringify(this.LsRate);
  }

  CopyDataDaFattura(Scheda)
  {
    this.LsRateNota = []

    let TutteRatePagate = true
    Scheda.SchedaRate.LsRate.forEach(function(Rata)
    {
      if(Rata.Dati.IdMovimento == null && (Rata.Dati.DataPagamento == null || Rata.Dati.DataPagamento == ''))
        TutteRatePagate = false
    })

    let TotaleFattura = Scheda.TOTALE_FATTURA;
    
    if(Scheda.Chiave != null)
    {
      let SingolaRata = new TSingolaRata(
                                          -1,
                                          this.IdDocumento,
                                          TZDateFunct.DateInHTMLInputFormat(new Date()),
                                          TotaleFattura,
                                          "",
                                          -1,
                                          '',
                                          TutteRatePagate? false : true,
                                          TutteRatePagate? -1 : Scheda.Chiave,
                                          Scheda.Dati.NUMERO != null && Scheda.Dati.NUMERO != 0? Scheda.Dati.NUMERO : null
                                        )
      
      this.SetAltezzaTextarea(SingolaRata)
      this.LsRateNota.push(SingolaRata)
    }
  }

  ClearListaRate()
  {
    this.LsRateNota = []
  }

  SetAltezzaTextarea(Rata)
  { 
    if(Rata.Dati.Note != undefined)
    {
      let NrRighe = Rata.Dati.Note.split("\n").length
      Rata.AltezzaTextArea = NrRighe  <= 1 ? '34px' : NrRighe * 22  + 'px'
    }
  }

  Ricalcola(Id_Documento, TotaleNota, OnSuccess)
  {
    for(let i = 0; i < this.LsRateNota.length; i++)
      if(!this.LsRateNota[i].Dati.DaEliminare)
        if((this.LsRateNota[i].Dati.DataPagamento != undefined && this.LsRateNota[i].Dati.DataPagamento != '') 
          ||(this.LsRateNota[i].Dati.IdFatturaCorrelata != -1 && this.LsRateNota[i].Dati.IdFatturaCorrelata != null))
        {
          if(!this.ControlloTotaleRate(TotaleNota))
          {
            this.CalcolaRate(TotaleNota)
          }
          if(OnSuccess != undefined)
            OnSuccess()
          return
        }
  
    for(let i = 0; i < this.LsRateNota.length; i++)
        this.LsRateNota[i].Dati.DaEliminare = true
    
    let Data = TZDateFunct.DateInHTMLInputFormat(new Date());
    
    let SingolaRata = new TSingolaRata(
                                        -1, 
                                        Id_Documento, 
                                        Data, 
                                        TotaleNota, 
                                        "", 
                                        -1, 
                                        "", 
                                        false, 
                                        -1, 
                                        null
                                      )
    
    this.LsRateNota.push(SingolaRata)
    
  }  

  ControlloTotaleRate(TotaleNota)
  {
    var TotalePagato = 0

    for(var i = 0; i < this.LsRateNota.length; i++)
      if((this.LsRateNota[i].Dati.DataPagamento != null && this.LsRateNota[i].Dati.DataPagamento != ''))
        TotalePagato = TotalePagato + (this.LsRateNota[i].Dati.Importo)/100

    TotalePagato    = parseFloat(TotalePagato.toFixed(2))

    if(TotalePagato < TotaleNota)
      return false
    if(TotalePagato >= TotaleNota)
      return true
  }

  NotaPagata()
  {
    if(this.LsRateNota.length == 0)
    {
      return false
    }

    if(this.LsRateNota.length > 0)
    {
      for(let i = 0; i < this.LsRateNota.length; i++)
      {
        if((this.LsRateNota[i].Dati.DataPagamento == undefined && this.LsRateNota[i].Dati.DataPagamento == null && this.LsRateNota[i].Dati.DataPagamento == '')
          || (this.LsRateNota[i].Dati.IdFatturaCorrelata == -1 && this.LsRateNota[i].Dati.NumeroFattura == null))
        {
          return false
        }
      }
    }
    return true
  }
}

export default {

  components :
  {
    VUEInputContoCorrente,
    VUEModal

  },
  
  emits: ['onChange'],
  
  data()
  {
    return{
      InserimentoNuovaRata : {},
      NumeroRate           : 1,
      PopupLsFatture       : false,
      NumeroMassimoFatture : 100,
      ListaFatture         : [],
      RataSelezionata      : -1,
      FiltroFatturaNumero  : null,
      NomeProgramma        : NOME_PROGRAMMA
    }

  },

  props : ['SchedaRateNota', 'ID_CLIENTE'],

  watch:
  {
    SchedaRateNota:
    {
      handler()
      {
        this.$emit('onChange') 
      },
      deep: true
    }
  },

  computed:
  {
    LsRateNotaVisibili :
    {
        get()
        {
          var Result = []
          this.SchedaRateNota.LsRateNota.forEach(function(ARate)
          {
              if(!ARate.Dati.DaEliminare)
                Result.push(ARate)
          })
          return Result;
        }
    },

    FatturaSelezionata :
    {
      get()
      {
        for(var i = 0; i < this.ListaFatture.length; i++)
        {
          if(this.ListaFatture[i].Selezionata == 'T')
            return true
        }
        return false
      }
    },

    ListaFattureFiltrate :
    {
      get()
      {
        let Self = this

        var FiltroNumero = this.FiltroFatturaNumero

        var ListaRighe = []

        if(FiltroNumero == null)
        {
          ListaRighe = this.ListaFatture.slice(0, this.NumeroMassimoFatture)
          return ListaRighe
        }

        else
        {
          ListaRighe = this.ListaFatture.filter(function(Fattura)
                                                {
                                                  if(FiltroNumero != null)
                                                  {
                                                    return Self.FiltraPerNumero(FiltroNumero, Fattura)
                                                  }
                                                })
          ListaRighe = ListaRighe.slice(0, this.NumeroMassimoFatture) 
          return ListaRighe
        }

      }
    }
  },
  
  methods:
  {
    OnClickEliminaRata(Rata)
    {
      Rata.Dati.DaEliminare = true
    },

    OnClickNuovaRataNota()
    {
      this.InserimentoNuovaRata = {}
      this.InserimentoNuovaRata = new TSingolaRata(
                                                    -1, 
                                                    this.SchedaRateNota.IdDocumento, 
                                                    TZDateFunct.DateInHTMLInputFormat(new Date()),
                                                    0, 
                                                    '', 
                                                    -1,
                                                    '',
                                                    false, 
                                                    -1,
                                                    null
                                                  )
      this.SchedaRateNota.LsRateNota.push(this.InserimentoNuovaRata)
      this.$emit('onChange')
    },

    OnInputNoteRata(Rata)
    {
      this.SchedaRateNota.SetAltezzaTextarea(Rata)
    },

    OnInputDataPagamento(Rata)
    {
      if(Rata.Dati.DataPagamento > TZDateFunct.DateInHTMLInputFormat(new Date()))
        Rata.Dati.DataPagamento = TZDateFunct.DateInHTMLInputFormat(new Date())
      if(Rata.Dati.DataPagamento == '')  
        if(Rata.Dati.IdContoCasse != -1 && Rata.Dati.IdContoCasse != null)
          Rata.Dati.IdContoCasse = -1
    },

    OnChangeScalataInFattura(Rata)
    {
      if(Rata.Dati.ScalataInFattura)
      {
        Rata.Dati.DataPagamento = ''
        Rata.Dati.IdContoCasse = -1;
      }

      if(!Rata.Dati.ScalataInFattura)
      {
        Rata.Dati.IdFatturaCorrelata = -1;
        Rata.Dati.NumeroFattura = null;
      }
        
    },

    OnclickCorrelaFattura(Rata)
    {
      this.ListaFatture = [];
      var Self = this;
      
      this.RataSelezionata = null
      
      this.RataSelezionata = Rata

      Rata.Dati.DataPagamento = ''
      Rata.Dati.IdContoCasse = -1

      SystemInformation.AdvQuery.GetSQL('NoteDiCredito',
                                        {CHIAVE_CLIENTE : this.ID_CLIENTE},
                                        function(Results)
                                        {
                                          let ArrayFatture = SystemInformation.AdvQuery.FindResults(Results, "CorrelaFatturaARata");

                                          if(ArrayFatture != undefined)
                                          {
                                            Self.ListaFatture = ArrayFatture

                                            Self.PopupLsFatture = true
                                          }
                                          else SystemInformation.HandleError('Impossibile ottenere lista delle fatture');
                                        },
                                        function (HTTPError, SubHTTPError, Response) 
                                        {
                                          SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                        },
                                        'CorrelaFatturaARata')
      
    },

    OnClickAnnullaFattura()
    {
      for(let i = 0; i < this.ListaFatture.length; i++)
      { 
        if(this.ListaFatture[i].Selezionata == 'T')
          this.ListaFatture[i].Selezionata = ''
      }
      
      this.RataSelezionata = null
      this.PopupLsFatture = false
    },

    OnClickConfermaFattura()
    {
      for(let i = 0; i < this.ListaFatture.length; i++)
      {
        if(this.ListaFatture[i].Selezionata == 'T')
        {
          let ChiaveFattura = this.ListaFatture[i].CHIAVE
          let NumeroFattura = this.ListaFatture[i].NUMERO

          this.RataSelezionata.Dati.IdFatturaCorrelata = ChiaveFattura
          this.RataSelezionata.Dati.NumeroFattura = NumeroFattura

          this.PopupLsFatture = false
        }
      }
    },

    OnClickEliminaFatturaCorrelata(Rata)
    {
      let ChiaveFattura = Rata.Dati.IdFatturaCorrelata

      for(let i = 0; i < this.SchedaRateNota.LsRateNota.length; i++)
      {
        if(this.SchedaRateNota.LsRateNota[i].Dati.IdFatturaCorrelata == ChiaveFattura 
           && this.SchedaRateNota.LsRateNota[i].Dati.Chiave == Rata.Dati.Chiave)
        {
          this.SchedaRateNota.LsRateNota[i].Dati.IdFatturaCorrelata = -1
          this.SchedaRateNota.LsRateNota[i].Dati.NumeroFattura = null

          // if(this.SchedaRateNota.ListaChiaviFatture.includes(ChiaveFattura))
          // {
          //   let Indice = this.SchedaRateNota.ListaChiaviFatture.indexOf(ChiaveFattura);
          //   this.SchedaRateNota.ListaChiaviFatture.splice(Indice, 1)
          // }
        }
        
      }
    },

    FiltraPerNumero(FiltroNumero, Fattura)
    {
      if(Fattura.NUMERO.toString().includes(FiltroNumero))
        return true
      return false
    },

    ResetRadio(Chiave)
    {
      this.ListaFatture.forEach(Fattura => {
                                              Fattura.Selezionata = Fattura.CHIAVE === Chiave ? 'T' : ''
                                           })
    },

  }
}

</script>