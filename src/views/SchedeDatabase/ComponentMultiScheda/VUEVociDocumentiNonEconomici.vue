<template>
<div>
  <div class="row wrapper">
    <div class="col-sm-12 m-b-xs">
      <div v-if="!CurrentReadOnly">
        <button class="btn btn-sm btn-success" style="float: left;" @click="OnClickNuovaRigaVoce">Aggiungi riga</button>
        <div style="float: left;">
          <input type="number" min="1" style="width: 60px; height: 30px;margin-left:5px;margin-right:10px"  v-model="NrRigheDaAggiungere">
        </div>
        <button class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickListaProdotti">Aggiungi prodotto</button>
        <button v-if="!IsSchedaScaricoProdotti && (IsSchedaDDT || ID_CLIENTE != -1 || IsSchedaDDTentrante)" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickListaConferme">Carica conferme</button>
      </div>
    </div>
    <div class="col-sm-4 m-b-xs"></div>
    <div class="col-sm-6">
    </div>
  </div>



  <div class="row wrapper">
  <section class="panel panel-default" style="background-color:white;">
    <div v-if="IsSchedaDDT" :ref="'Tabella'" class="table-responsive" style="max-height:350px;">
      <table class="table table-striped b-t b-light" style="min-width:500px;">
        <thead>
          <tr>
            <th style="width:35%;position: sticky; top: 0">Descrizione</th>
            <th style="width:8%;position: sticky; top: 0">Udm</th>
            <th style="width:7%;position: sticky; top: 0">Qnt.</th>
            <th style="width:6%;position: sticky; top: 0">Prezzo</th>
            <th style="width:7%;position: sticky; top: 0">IVA</th>
            <th style="width:8%;position: sticky; top: 0;">Sc. [%]</th>

            <th style="width:1%;position: sticky; top: 0"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="Voce in LsVociVisibili" :key="Voce.Chiave">
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <textarea :readonly="CurrentReadOnly" type="text" wrap="off" v-model="Voce.Dati.Descrizione" @input="OnInputDescrizioneVoce(Voce)" class="form-control" :style="{height : Voce.AltezzaTextArea? Voce.AltezzaTextArea : '34px'}" style="resize:none;overflow-y:hidden"></textarea>
              <label v-if="Voce.Dati.Descrizione.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <VUEInputUdm :disabled="CurrentReadOnly" v-model="Voce.Dati.Unita_di_Misura" class="form-control" />
              <label v-if="Voce.Dati.Unita_di_Misura == -1" class="ZMFormLabelError">Campo obbligatorio</label>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Quantita" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Prezzo" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Iva" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Sconto" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
              <a v-if="!CurrentReadOnly" @click="OnClickEliminaVoce(Voce)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px" title="Elimina Voce"><i class="fa fa-trash-o"></i></a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="IsSchedaDDTentrante" :ref="'Tabella'" class="table-responsive" style="max-height:350px;">
      <table class="table table-striped b-t b-light" style="min-width:500px;">
        <thead>
          <tr>
            <th style="width:35%;position: sticky; top: 0">Descrizione</th>
            <th style="width:8%;position: sticky; top: 0">Udm</th>
            <th style="width:7%;position: sticky; top: 0">Qnt.</th>
            <th style="width:6%;position: sticky; top: 0">Prezzo</th>
            <th style="width:7%;position: sticky; top: 0">IVA</th>
            <th style="width:8%;position: sticky; top: 0;">Sc. [%]</th>
            <th style="width:1%;position: sticky; top: 0"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="Voce in LsVociVisibili" :key="Voce.Chiave">
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <textarea :readonly="CurrentReadOnly" type="text" wrap="off" v-model="Voce.Dati.Descrizione" @input="OnInputDescrizioneVoce(Voce)" class="form-control" :style="{height : Voce.AltezzaTextArea? Voce.AltezzaTextArea : '34px'}" style="resize:none;overflow-y:hidden"></textarea>
              <label v-if="Voce.Dati.Descrizione.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <VUEInputUdm :disabled="CurrentReadOnly" v-model="Voce.Dati.Unita_di_Misura" class="form-control" />
              <label v-if="Voce.Dati.Unita_di_Misura == -1" class="ZMFormLabelError">Campo obbligatorio</label>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Quantita" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Prezzo" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Iva" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Sconto" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
              <a v-if="!CurrentReadOnly" @click="OnClickEliminaVoce(Voce)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px" title="Elimina Voce"><i class="fa fa-trash-o"></i></a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
        <div v-if="IsSchedaScaricoProdotti" :ref="'Tabella'" class="table-responsive" style="max-height:350px;">
      <table class="table table-striped b-t b-light" style="min-width:500px;">
        <thead>
          <tr>
            <th style="width:35%;position: sticky; top: 0">Descrizione</th>
            <th style="width:8%;position: sticky; top: 0">Udm</th>
            <th style="width:7%;position: sticky; top: 0">Qnt.</th>
            <th style="width:6%;position: sticky; top: 0">Prezzo</th>
            <th style="width:7%;position: sticky; top: 0">IVA</th>
            <th style="width:8%;position: sticky; top: 0;">Sc. [%]</th>

            <th style="width:1%;position: sticky; top: 0"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="Voce in LsVociVisibili" :key="Voce.Chiave">
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <textarea :readonly="CurrentReadOnly" type="text" wrap="off" v-model="Voce.Dati.Descrizione" @input="OnInputDescrizioneVoce(Voce)" class="form-control" :style="{height : Voce.AltezzaTextArea? Voce.AltezzaTextArea : '34px'}" style="resize:none;overflow-y:hidden"></textarea>
              <label v-if="Voce.Dati.Descrizione.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <VUEInputUdm :disabled="CurrentReadOnly" v-model="Voce.Dati.Unita_di_Misura" class="form-control" />
              <label v-if="Voce.Dati.Unita_di_Misura == -1" class="ZMFormLabelError">Campo obbligatorio</label>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Quantita" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Prezzo" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Iva" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
              <input :readonly="CurrentReadOnly" type="number" min="0" v-model="Voce.Dati.Sconto" class="form-control" step="0.01"/>
            </td>
            <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
              <a v-if="!CurrentReadOnly" @click="OnClickEliminaVoce(Voce)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px" title="Elimina Voce"><i class="fa fa-trash-o"></i></a>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
      <div>  
        <div class="ZMNuovaRigaScheda">
          <br>
          <div class="col-md-4">
            
          </div> 
        </div> 
      </div>
  </div>
  
   <VUEModal v-if="PopupLsProdotti" :Titolo="'Lista prodotti'" :Altezza="'400px'" :Larghezza="'1000px'"
            @onClickChiudiModal="PopupLsProdotti = false">
      <template v-slot:Body>
      <div style="width:1%;float:left">&nbsp;</div>
      <input type="text" style="width:73%;float:left" class="input-sm form-control" placeholder="Cerca per descrizione" v-model="FiltroProdottiDescrizione">
     <div style="clear:both;width:1%;height:10px">&nbsp;</div>
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:7%;position: sticky; top: 0;"></th>
                      <th style="width:43%;position: sticky; top: 0;">Descrizione</th>
                      <th style="width:15%;position: sticky; top: 0;">Settore</th>
                      <th style="width:10%;position: sticky; top: 0;">Prezzo [€]</th>
                      <th style="width:10%;position: sticky; top: 0;">IVA [%]</th>
                      <th v-if="VisibilitaListinoPrezziCliente" style="width:10%;position: sticky; top: 0;">Sconto [€]</th>
                      <th v-if="VisibilitaListinoPrezziCliente" style="width:10%;position: sticky; top: 0;">Sconto [%]</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Prodotto in ProdottiFiltrati" :key="Prodotto.CHIAVE">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" style="height: 20px;" class="form-control" v-model="Prodotto.Presente">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.DESCRIZIONE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.SETTORE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.PREZZO_IMPONIBILE / 100}}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.IVA / 10}}
                      </td>
                      <td v-if="VisibilitaListinoPrezziCliente" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.PREZZO_SCONTATO != null ? Prodotto.PREZZO_SCONTATO : '-' }}
                      </td>
                      <td v-if="VisibilitaListinoPrezziCliente" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.PREZZO_SCONTO_PERCENTUALE != null ? Prodotto.PREZZO_SCONTO_PERCENTUALE : '-' }}
                      </td>
                    </tr>
                    <tr v-if="ProdottiFiltrati.length == NumeroMassimoProdotti">
                      <td colspan="7" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:right;vertical-align: middle;color:red">
                        Sono presenti più di 100 prodotti...
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
        </template>
        <template v-slot:Footer>
          <button class="btn btn-danger" @click="OnClickAnnullaProdotti" style="float:right;margin-right:20px; width:20%">Annulla</button>
          <button class="btn btn-success" @click="OnClickConfermaProdotti" style="float:right;margin-right:20px; width:20%">Conferma</button>
        </template>
   </VUEModal>


   <VUEModal v-if="PopupLsPreventivi" :Titolo="'Lista conferme d\'ordine'" :Altezza="'400px'" :Larghezza="'1000px'"
            @onClickChiudiModal="PopupLsPreventivi = false">
      <template v-slot:Body>
            <div style="width:45%;float:left">
              <label>Dal </label>
              <input type="date" class="input-sm form-control" v-model="DallaData">
            </div>
            <div style="width:1%;float:left">&nbsp;</div>
            <div style="width:44%;float:left">
              <label>al </label>
              <input type="date" class="input-sm form-control" v-model="AllaData">
            </div>
            <div style="width:1%;float:left">&nbsp;</div>
            <div style="width:9%;float:left">
              <label>&nbsp;</label>
              <button style="width:90%" class="btn btn-sm btn-info" @click="RicercaConferme">Cerca</button>
            </div>
            <div style="clear:both"></div>
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:7%"></th>
                      <th style="width:20%">Numero</th>
                      <th style="width:25%">Data</th>
                      <th style="width:15%">Codice cliente</th>
                      <th style="width:25%">Cliente</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Preventivo in ListaPreventivi" :key="Preventivo.CHIAVE">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" style="height: 20px;" class="form-control" v-model="Preventivo.Presente">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        Conferma d'ordine nr. {{ Preventivo.NUMERO }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Preventivo.DATA }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Preventivo.CODICE_CLIENTE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Preventivo.RAGIONE_SOCIALE_CLIENTE }}
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>     
        </template>
        <template v-slot:Footer>
          <button class="btn btn-danger" @click="OnClickAnnullaConferme" style="float:right;margin-right:20px; width:20%">Annulla</button>
          <button v-if="ConfermeSelezionate" class="btn btn-success" @click="OnClickConfermaConfermaDOrdine" style="float:right;margin-right:20px; width:20%">Conferma</button>    
        </template>
   </VUEModal>
</div>
</template>

<script>
import VUEInputUdm from '@/components/InputComponents/VUEInputUdm.vue'
import { SystemInformation, TIPO_SCONTO } from '@/SystemInformation.js'
import { TZDateFunct } from '../../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEModal from '@/components/Slots/VUEModal.vue';
import { TSchedaGenerica } from '../../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'


export class TSingoloVociDocumentiNonEconomici
{
   constructor(Chiave, Descrizione, Quantita, IdDocumento, Unita_di_Misura, Prezzo, Iva, Sconto, IdProdotto)
   {
     this.Dati = {}
     this.Dati.Chiave          = Chiave;
     this.Dati.Descrizione     = Descrizione;
     this.Dati.Quantita        = Quantita
     this.Dati.IdDocumento     = IdDocumento
     this.Dati.Unita_di_Misura = Unita_di_Misura
     this.Dati.Prezzo          = Prezzo
     this.Dati.Iva             = Iva
     this.Dati.Sconto          = Sconto
     if(IdProdotto != undefined)
        this.Dati.IdProdotto = IdProdotto
     else this.Dati.IdProdotto  = -1

     this.DaEliminare          = false
     this.Snapshot             = JSON.stringify(this.Dati)  
     if(Chiave == -1)
      this.Dati.DaRegistrare = true 
   }

   AllDataOk()
   {
     var Result = this.Dati.Descrizione.trim() != ''
     if(Result)
     {
        if(!this.DaEliminare)
          Result = this.Dati.Descrizione.trim() != '';
     }
     return Result
   }

   PrepareQueryParameters(Operazioni,NomeCampoDocumento,Ordinamento)
   {
     var Parametri = { 
                        CHIAVE               : this.Dati.Chiave,
                        [NomeCampoDocumento] : this.Dati.IdDocumento == -1? undefined : this.Dati.IdDocumento,
                        DESCRIZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.Descrizione),
                        UNITA_DI_MISURA      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.Unita_di_Misura),
                        QUANTITA             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Quantita * 100),
                        SCONTO               : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Sconto * 100),
                        IMPORTO              : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Prezzo * 100),
                        IVA                  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Iva * 10),
                        ID_PRODOTTO          : this.Dati.IdProdotto == -1 ? null : TSchedaGenerica.PrepareForRecordInteger(this.Dati.IdProdotto),
                        ORDINAMENTO          : Ordinamento
                      }

     if(this.Dati.Chiave == -1 && !this.DaEliminare)
     {
        Operazioni.push({
                                  Query     : 'InserisciVoce',
                                  Parametri : Parametri,
                                  ResetKeys   : [2]
        })
     }
     else
     {
        if(this.Dati.Chiave != -1)
        {
            if(this.DaEliminare)
            {
              Operazioni.push({
                                        Query     : 'EliminaVoce',
                                        Parametri : { CHIAVE      : this.Dati.Chiave }
              })
            }
            else
            {
              if(this.DaRegistrare() )
              {
                Operazioni.push({
                                          Query     : 'ModificaVoce',
                                          Parametri : Parametri,
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
export class TSchedaVociDocumentiNonEconomici
{
   constructor()
   {
      this.LsVociDocumentiNonEconomici = []
      this.EsenteIva                   = false; 
      this.CreateSnapshot();
   }

   SetDatiCliente(EsenteIva)
   {
     this.EsenteIva       = EsenteIva;
   }

   RitornaEsenteIva()
   {
    return this.EsenteIva
   }

   Modificato()
   {
      for(var i = 0; i < this.LsVociDocumentiNonEconomici.length; i++)
         if(this.LsVociDocumentiNonEconomici[i].Modificato())
            return true
      return false;
   }

   PrepareQueryParameters(Operazioni,NomeCampoDocumento)
   {
     for(var i = 0; i < this.LsVociDocumentiNonEconomici.length; i++)
        if(this.LsVociDocumentiNonEconomici[i].DaEliminare || this.LsVociDocumentiNonEconomici[i].Dati.Chiave == -1 || this.LsVociDocumentiNonEconomici[i].DaRegistrare)
            this.LsVociDocumentiNonEconomici[i].PrepareQueryParameters(Operazioni,NomeCampoDocumento,i + 1)
     return Operazioni
   }

   AllDataOk()
   {
    for(var i = 0; i < this.LsVociDocumentiNonEconomici.length; i++)
        if(!this.LsVociDocumentiNonEconomici[i].AllDataOk())
          return false
    return true 
   }

   SetIdDocumento(IdDocumento)
   {
      this.IdDocumento = IdDocumento
   }

   AssignDati(LsVociDocumentiNonEconomici,EsenteIva,NomeCampoDocumento)
   {
      this.EsenteIva                   = EsenteIva;
      this.LsVociDocumentiNonEconomici = []
      this.CreateSnapshot();
      for(var i = 0; i < LsVociDocumentiNonEconomici.length; i++)
      {
          var SingoloVociDocumentiNonEconomici = new TSingoloVociDocumentiNonEconomici(LsVociDocumentiNonEconomici[i].CHIAVE, 
                                                                                       TSchedaGenerica.DisponiFromString(LsVociDocumentiNonEconomici[i].DESCRIZIONE),
                                                                                       TSchedaGenerica.DisponiFromInteger(LsVociDocumentiNonEconomici[i].QUANTITA) / 100,
                                                                                       LsVociDocumentiNonEconomici[i][NomeCampoDocumento], 
                                                                                       TSchedaGenerica.DisponiFromListIndex(LsVociDocumentiNonEconomici[i].UNITA_DI_MISURA),
                                                                                       TSchedaGenerica.DisponiFromInteger(LsVociDocumentiNonEconomici[i].IMPORTO) / 100,
                                                                                       TSchedaGenerica.DisponiFromInteger(LsVociDocumentiNonEconomici[i].IVA) / 10,
                                                                                       TSchedaGenerica.DisponiFromInteger(LsVociDocumentiNonEconomici[i].SCONTO) / 100,
                                                                                       TSchedaGenerica.DisponiFromInteger(LsVociDocumentiNonEconomici[i].ID_PRODOTTO))
          this.LsVociDocumentiNonEconomici.push(SingoloVociDocumentiNonEconomici)
          this.SetAltezzaTextarea(SingoloVociDocumentiNonEconomici)
      }
      this.Snapshot = JSON.stringify(this.LsVociDocumentiNonEconomici);
   }

   CreateSnapshot()
   {
     this.Snapshot = JSON.stringify(this.Dati)
   }

   ClearVociDocumentiNonEconomici()
   {
    this.LsVociDocumentiNonEconomici= []
   }

    EsenteIVA()
    {
      for( let i = 0; i < this.LsVociDocumentiNonEconomici.length; i++)
      {
        this.LsVociDocumentiNonEconomici[i].Dati.Iva = 0
      }
    }

   CopyData(DocumentiEconomiciSorgente)
   {
      this.LsVociDocumentiNonEconomici = []
      var LsVociSorgente = DocumentiEconomiciSorgente.LsVociDocumentiEconomici

      for(let i = 0; i < LsVociSorgente.length; i++)
      {
          let SingoloVociDocumentiNonEconomici = new TSingoloVociDocumentiNonEconomici(-1, 
                                                                                      LsVociSorgente[i].Dati.Descrizione,
                                                                                      LsVociSorgente[i].Dati.Quantita, 
                                                                                      -1, 
                                                                                      LsVociSorgente[i].Dati.UnitaDiMisura,
                                                                                      LsVociSorgente[i].Dati.Imponibile, //qua imponibile perchè arriva dai preventivi
                                                                                      LsVociSorgente[i].Dati.Iva,
                                                                                      LsVociSorgente[i].Dati.Sconto,
                                                                                      LsVociSorgente[i].Dati.IdProdotto)
          this.SetAltezzaTextarea(SingoloVociDocumentiNonEconomici)
          this.LsVociDocumentiNonEconomici.push(SingoloVociDocumentiNonEconomici)
      }
      this.CreateSnapshot()
   }

   SetAltezzaTextarea(Voce)
   { 
      let NrRighe = Voce.Dati.Descrizione.split("\n").length
      Voce.AltezzaTextArea = NrRighe  <= 1 ? '34px' : NrRighe * 22 + 10  + 'px'
   }

}


export default {

  components : 
   {
    VUEInputUdm,
    VUEModal
   },

  emits: ['onChange'],
  data()
  {
    return {
              InserimentoNuovaRiga        : {},
              NrRigheDaAggiungere         : 1,
              PopupLsProdotti             : false,
              PopupLsPreventivi           : false,
              ListaProdotti               : [],
              ListaProdottiVariazPrezzo   : [],
              ListaPreventivi             : [],
              FiltroProdottiDescrizione   : '',
              NumeroMassimoProdotti       : 100,
              VisibilitaListinoPrezziCliente : SystemInformation.AccessRights.VisibilitaListinoPrezziCliente(),
              DallaData                   : TZDateFunct.DateInHTMLInputFormat(new Date('January 01,' + (new Date().getFullYear()))),
              AllaData                    : TZDateFunct.DateInHTMLInputFormat(new Date()),                       
    }
  },

  props : ['SchedaVociDocumentiNonEconomici','NomeCampoDocumento', 'IsSchedaDDT', 'IsSchedaDDTentrante','IsSchedaScaricoProdotti', 'IdCliente', 'ReadOnly'],
  
  computed :
  {
    CurrentReadOnly :
    {
      get()
      {
        return this.ReadOnly 
      }
    },

    LsVociVisibili :
    {
        get()
        {
          var Result = []
          this.SchedaVociDocumentiNonEconomici.LsVociDocumentiNonEconomici.forEach(function(AVociDocumentiNonEconomici)
          {
              if(!AVociDocumentiNonEconomici.DaEliminare)
                Result.push(AVociDocumentiNonEconomici)
          })
          return Result;
        }
    },
      
    ProdottiFiltrati :
    {
      get()
      {
        var FiltroDescr  = this.FiltroProdottiDescrizione.toUpperCase().trim();

        var ListaRighe   = []

        if(FiltroDescr == '')
        {
          ListaRighe = this.ListaProdotti.slice(0, this.NumeroMassimoProdotti)
          return ListaRighe
        }
        else 
        {
          ListaRighe = this.ListaProdotti.filter(function(Prodotto)
          {
            if(FiltroDescr != '')
            {
              if((Prodotto.DESCRIZIONE).includes(FiltroDescr))
                  return true;
              return false
            }
            
            if(FiltroDescr != '')
            {
              if(Prodotto.DESCRIZIONE.includes(FiltroDescr))
                return true
              return false
            }
            return false;
          })
          ListaRighe = ListaRighe.slice(0, this.NumeroMassimoProdotti) 
          return ListaRighe
        }
      }
    },

    ConfermeSelezionate :
    {
      get()
      {
        for(var i = 0; i < this.ListaPreventivi.length; i++)
        if(this.ListaPreventivi[i].Presente)
          return true
        return false
      }
    },

    ID_CLIENTE: 
    {
      get()
      {
        return this.IdCliente;
      }
    },          

  },

  watch:
  {
    SchedaVociDocumentiNonEconomici:
    {
      handler()
      {
        this.$emit('onChange') 
      },
      deep: true
    }
  },

  methods:
  {

    OnClickNuovaRigaVoce()
    {
      for(let i = 0; i < this.NrRigheDaAggiungere; i++)
      {
        this.InserimentoNuovaRiga = new TSingoloVociDocumentiNonEconomici(-1,'',1,this.SchedaVociDocumentiNonEconomici.IdDocumento,SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA, this.SchedaVociDocumentiNonEconomici.IdProdotto)
        this.SchedaVociDocumentiNonEconomici.LsVociDocumentiNonEconomici.push(this.InserimentoNuovaRiga)
        setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50) 
      }
      this.$emit('onChange')       
    },

    OnClickEliminaVoce(Voce)
    {
      Voce.DaEliminare = true
      Voce.Dati.Descrizione = 'a'
      this.$emit('onChange')
    },

    OnClickAnnullaProdotti()
    {
      for(let i = 0; i < this.ListaProdotti.length; i++)
      { 
        if(this.ListaProdotti[i].Presente)
          this.ListaProdotti[i].Presente = false
      }
      this.PopupLsProdotti = false
    },

    OnClickListaProdotti()
    {
      var Self = this;

      if(this.IsSchedaDDTentrante)
      {  
        this.ListaProdotti = SystemInformation.GetProdottiSemplici()
        Self.PopupLsProdotti = true
      }
      if(this.IsSchedaDDT)
      {
        this.ListaProdotti   = null
        
        SystemInformation.AdvQuery.GetSQL('Magazzino',{CHIAVE : this.ID_CLIENTE}, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaProdottiVariazionePrezzo");
                                            if (ArrayInfo != undefined) 
                                            {
                                              Self.ListaProdotti = ArrayInfo
                                              
                                              for(let i = 0; i < Self.ListaProdotti.length; i++)
                                              {
                                                if(Self.ListaProdotti[i].TIPO_SCONTO == TIPO_SCONTO.Prezzo)
                                                {
                                                  Self.ListaProdotti[i].PREZZO_SCONTATO = Self.ListaProdotti[i].VALORE
                                                }  
                                                else if (Self.ListaProdotti[i].TIPO_SCONTO == TIPO_SCONTO.Sconto)
                                                {
                                                  Self.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE = Self.ListaProdotti[i].VALORE
                                                }
                                                else
                                                {
                                                  if(Self.ListaProdotti[i].SCONTO_GENERALE != null && Self.ListaProdotti[i].SCONTO_GENERALE != 0)
                                                    Self.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE = Self.ListaProdotti[i].SCONTO_GENERALE
                                                }
                                                
                                                if(Self.ListaProdotti[i].PREZZO_SCONTATO != null)
                                                  Self.ListaProdotti[i].PREZZO_SCONTATO = parseFloat((Self.ListaProdotti[i].PREZZO_SCONTATO / 100).toFixed(2))
                                                if(Self.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE != null)
                                                  Self.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE = parseFloat((Self.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE / 100).toFixed(2))
                                              }
                                              Self.PopupLsProdotti = true
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista dei prodotti con prezzo variato');
                                          },
                                          function (HTTPError, SubHTTPError, Response) 
                                          {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaProdottiVariazionePrezzo')
      }
      if(this.IsSchedaScaricoProdotti)
      {
         this.ListaProdotti = SystemInformation.GetProdottiComposti()
         Self.PopupLsProdotti = true
      }
    },

    OnClickConfermaProdotti()
    {
      for(let i = 0; i < this.ListaProdotti.length; i++)
      {
        if(this.ListaProdotti[i].Presente)
        {
          let InserimentoNuovaRiga = new TSingoloVociDocumentiNonEconomici(-1,
                                                                          
                                                                           TSchedaGenerica.DisponiFromString(this.ListaProdotti[i].DESCRIZIONE),
                                                                          
                                                                           TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].QUANTITA_SUGGERITA),
                                                                          
                                                                           this.SchedaVociDocumentiNonEconomici.IdDocumento,
                                                                          
                                                                           this.ListaProdotti[i].UNITA_DI_MISURA == null? SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA : this.ListaProdotti[i].UNITA_DI_MISURA,
                                                                          
                                                                           this.ListaProdotti[i].PREZZO_SCONTATO == null? this.ListaProdotti[i].PREZZO_IMPONIBILE / 100 : this.ListaProdotti[i].PREZZO_SCONTATO,
                                                                          
                                                                           TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].IVA) / 10,
                                                                           
                                                                           this.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE == null? 0.00 : this.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE,
                                                                           
                                                                           TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].CHIAVE))

          this.SchedaVociDocumentiNonEconomici.LsVociDocumentiNonEconomici.push(InserimentoNuovaRiga)
          this.ListaProdotti[i].Presente = false
        }
      }
      setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50) 
      this.PopupLsProdotti = false
    },

    OnInputDescrizioneVoce(Voce)
    {
       this.SchedaVociDocumentiNonEconomici.SetAltezzaTextarea(Voce)
    },
    

    RicercaConferme()
    {   
      var Self = this;
      SystemInformation.AdvQuery.GetSQL('Preventivi',{DallaData: this.DallaData, AllaData: this.AllaData, CHIAVE : this.ID_CLIENTE}, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaPreventiviConfermati");
                                            if (ArrayInfo != undefined) 
                                            {
                                              Self.ListaPreventivi = ArrayInfo
                                              Self.ListaPreventivi.forEach(function(Preventivo)
                                              {
                                                  Preventivo.DATA = new Date(Preventivo.DATA)
                                                  Preventivo.DATA = TZDateFunct.FormatDateTime('dd/mm/yyyy',Preventivo.DATA)
                                              })
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista dei preventivi');
                                          },
                                          function (HTTPError, SubHTTPError, Response) {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaPreventiviConfermati')    
    },


    OnClickListaConferme()
    {
        this.DallaData = TZDateFunct.DateInHTMLInputFormat(new Date('January 01,' + (new Date().getFullYear())))
        this.AllaData  = TZDateFunct.DateInHTMLInputFormat(new Date())
        this.PopupLsPreventivi = true
        this.RicercaConferme()
    },    

    OnClickAnnullaConferme()
    {
        this.PopupLsPreventivi = false      
    },


    OnClickConfermaConfermaDOrdine()
    {
      this.PopupLsPreventivi = false    
      var ListaCheckati = []
      for(let i = 0; i < this.ListaPreventivi.length; i++)
      {
        if(this.ListaPreventivi[i].Presente)
        {
          ListaCheckati.push(this.ListaPreventivi[i].CHIAVE)
        }
      }
      var Self = this;
      SystemInformation.AdvQuery.GetSQL('Preventivi',{ ListaCheckati: ListaCheckati }, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaVociPreventiviSelezionati");
                                            if (ArrayInfo != undefined) 
                                            {
                                              var NumeroConfermaDOrdine = 0
                                              for(let i = 0; i < ArrayInfo.length; i++)
                                              {
                                                if(NumeroConfermaDOrdine != ArrayInfo[i].NUMERO)
                                                {
                                                  NumeroConfermaDOrdine = ArrayInfo[i].NUMERO
                                                  let InserimentoNuovaSeparatore = new TSingoloVociDocumentiNonEconomici(-1,
                                                                                                                        'Riferimento conferma d\'ordine nr. ' + ArrayInfo[i].NUMERO,
                                                                                                                        0,
                                                                                                                        Self.SchedaVociDocumentiNonEconomici.IdDocumento,
                                                                                                                        SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
                                                
                                                  Self.SchedaVociDocumentiNonEconomici.SetAltezzaTextarea(InserimentoNuovaSeparatore)                                                
                                                  Self.SchedaVociDocumentiNonEconomici.LsVociDocumentiNonEconomici.push(InserimentoNuovaSeparatore)
                                                }
                                                let InserimentoNuovaRiga = new TSingoloVociDocumentiNonEconomici(-1,
                                                                                                                ArrayInfo[i].DESCRIZIONE,
                                                                                                                ArrayInfo[i].QUANTITA / 100,
                                                                                                                Self.SchedaVociDocumentiNonEconomici.IdDocumento,
                                                                                                                ArrayInfo[i].UNITA_DI_MISURA == null? SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA : ArrayInfo[i].UNITA_DI_MISURA,
                                                                                                                ArrayInfo[i].IMPORTO / 100,
                                                                                                                ArrayInfo[i].IVA / 100,
                                                                                                                ArrayInfo[i].SCONTO / 100,
                                                                                                                ArrayInfo[i].ID_PRODOTTO)

                                                Self.SchedaVociDocumentiNonEconomici.SetAltezzaTextarea(InserimentoNuovaRiga)                                                
                                                Self.SchedaVociDocumentiNonEconomici.LsVociDocumentiNonEconomici.push(InserimentoNuovaRiga)
                                              }
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista dei preventivi');
                                          },
                                          function (HTTPError, SubHTTPError, Response) {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaVociPreventiviSelezionati')  
    },    

  },

  beforeMount() 
  {
  },

}
</script>