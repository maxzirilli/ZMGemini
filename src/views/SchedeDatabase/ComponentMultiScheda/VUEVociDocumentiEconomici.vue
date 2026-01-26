

<template>
  <VUEConfirm :Popup="PopupVociDocumentiEconomici" 
              :Richiesta="'Vuoi inserire le spese di trasporto tra le voci della fattura?'" 
              @onClickConfermaPopup="OnClickConfermaPopup" 
              @onClickChiudiPopup="PopupVociDocumentiEconomici = false">
  </VUEConfirm>

  <VUEConfirm :Popup="PopupCondPagamentoNonOmogeneeNeiPreventiviCaricati" 
              :Richiesta="'Stai caricando delle conferme che hanno condizioni di pagamento non omogenee, vuoi continuare o annulli e crei più fatture?'" 
              @onClickConfermaPopup="OnClickPopupCondPagamentoNonOmogeneeNeiPreventiviCaricati" 
              @onClickChiudiPopup="PopupCondPagamentoNonOmogeneeNeiPreventiviCaricati = false">
  </VUEConfirm>

  <VUEConfirm :Popup="PopupGeneraNotaDiCredito" 
              :Richiesta="RichiestaPopupNota" 
              @onClickConfermaPopup="ConfermaGenerazioneNota" 
              @onClickChiudiPopup="PopupGeneraNotaDiCredito = false">
  </VUEConfirm>

<div>
  <label v-if="!CurrentSchedaVociDocumentiEconomici.VociPresenti()" class="ZMFormLabelError">Inserire almeno una voce</label>
  <div class="row wrapper" style="padding-right:3px;padding-left:3px;padding-bottom:0px;padding-top:0px">
    <div class="col-sm-12 m-b-xs">
      <button v-if="!FatturaInviataAlloSdi" class="btn btn-sm btn-success" style="float: left;" @click="OnClickNuovaRigaVoce" :disabled="Disabilitato">Aggiungi riga</button>
      <div style="float: left;">
        <input v-if="!FatturaInviataAlloSdi" type="number" min="1" style="width: 60px; height: 30px;margin-left:5px;margin-right:10px" v-model="NrRigheDaAggiungere">
      </div>
      <!-- <button v-if="!FatturaInviataAlloSdi && !NascondiPulsantiVoci" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickAggiungiSpeseDiTrasporto">Spese di trasporto</button> -->
      <button v-if="!FatturaInviataAlloSdi && !NascondiPulsantiVoci" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickListaProdotti" :disabled="Disabilitato">Aggiungi prodotto</button>
      <button v-if="IsSchedaPreventivo" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickAggiungiVoci" :disabled="Disabilitato">Aggiungi voci</button>
      
      <button v-if="SoloPerLeFatture && !FatturaInviataAlloSdi && !NascondiPulsantiVoci" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickListaFatture" :disabled="Disabilitato">Carica fatture</button>
      <button v-if="SoloPerLeFatture && !FatturaInviataAlloSdi && !NascondiPulsantiVoci" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickListaConferme" :disabled="Disabilitato">Carica conferme</button>
      <button v-if="SoloPerLeFatture && !FatturaInviataAlloSdi && !NascondiPulsantiVoci" class="btn btn-sm btn-info" style="float: left;margin-right:10px;width: 110px;" @click="OnClickListaDDT" :disabled="Disabilitato">Carica DDT</button>


      <button v-if="!FatturaInviataAlloSdi && !NascondiPulsantiVoci" class="btn btn-sm btn-info" @click="OnClickAssegnaSconto" style="float:right;" :disabled="Disabilitato">Ass. sconto</button>
      <button v-if="TastoCreaNotaVisibile && !NascondiPulsantiVoci" 
              class="btn btn-sm btn-info" 
              style="float:right;margin-right:10px"
              @click="OnClickGeneraNotaDiCredito">Crea nota
      </button>
    </div>
  </div>
  <VUEModal v-if="AssociaSconto" :Titolo="'Inserire sconto'" :Altezza="'100px'" :Larghezza="'400px'"
            @onClickChiudiModal="OnClickAssegnaSconto">
    <template v-slot:Body>
      <div class="form-row">
      <div class="col-md-12">
        <div style="float:left;width:100%">
          <label style="font-weight: bold;margin-left:30%;width:30%">Inserisci sconto</label>
          <input type="number" min="0" step="0,01" class="form-control" style="margin-left:30%;;width:35%" v-model="AssegnaSconto">
        </div>
      </div>
      </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:30%" @click="OnClickAssegnaSconto" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="OnClickConfermaSconto" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal>

  <div class="row wrapper" style="padding-top:5px;padding-bottom:5px">
    <section class="panel panel-default" style="background-color:#f1f1f1;margin-bottom:0px; margin-left: 0.75%; width: 98.5%;">
      <div :ref="'Tabella'" class="table-responsive" style="max-height:350px; overflow-y:auto">
        <table class="table table-striped b-t b-light" style="min-width:1000px;">
          <thead>
            <tr>
              <th style="width:7%;position: sticky; top: 0;">Codice</th>
              <th style="width:25%;position: sticky; top: 0;">Descrizione</th>
              <th style="width:2%;position: sticky; top: 0;">Udm</th>
              <th style="width:2%;position: sticky; top: 0;">Qnt.</th>
              <th style="width:6%;position: sticky; top: 0;">Importo</th>
              <th style="width:3%;position: sticky; top: 0;">IVA</th>
              <!-- <th style="width:6%;position: sticky; top: 0;">Ivato</th> -->
              <th style="width:3%;position: sticky; top: 0;">Sc. [%]</th>
              <th style="width:4%;position: sticky; top: 0;">Tot</th>
              <!-- <th style="width:4%;position: sticky; top: 0;">Tot ivato</th> -->
              <th style="width:1%;position: sticky; top: 0;"></th>
              <th style="width:1%;position: sticky; top: 0;"></th>
              <th style="width:1%;position: sticky; top: 0;"></th>
              <th style="width:1%;position: sticky; top: 0;"></th>
              <th style="width:1%;position: sticky; top: 0;"></th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(Voce, index) in LsVociVisibili" :key="Voce.Chiave">
              <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                <input :readonly="FatturaInviataAlloSdi || Disabilitato" v-model="Voce.Dati.CodiceProdotto" type="text" class="form-control" @input="OnEmitVociFattura(Voce)"/>
              </td>
              <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                <textarea :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" wrap="off" v-model="Voce.Dati.Descrizione" class="form-control" @input="OnInputDescrizioneVoce(Voce)" :style="{height : Voce.AltezzaTextArea? Voce.AltezzaTextArea : '34px'}" style="resize:none;overflow-y:hidden;"></textarea>
                <label v-if="Voce.Dati.Descrizione.trim() == '' && Voce.Dati.Quantita != 0" class="ZMFormLabelError">Campo obbligatorio</label>
              </td>
              <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                <VUEInputUdm :disabled="FatturaInviataAlloSdi || Disabilitato" v-model="Voce.Dati.UnitaDiMisura" @input="OnEmitVociFattura(Voce)" class="form-control"  style="cursor:default" />
                <label v-if="Voce.Dati.UnitaDiMisura == -1" class="ZMFormLabelError">Campo obbligatorio</label>
              </td>
              <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="number" min="0" v-model="Voce.Dati.Quantita" class="form-control" step="0.01" @input="OnInputValori(Voce)"/>
                <label v-if="Voce.Dati.Imponibile != 0 && Voce.Dati.Quantita == 0" class="ZMFormLabelError">Campo obbligatorio</label>  
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="number" step="0.01" min="0" @input="OnInputImponibile(Voce), OnEmitVociFattura(Voce)" class="form-control" v-model="Voce.Dati.Imponibile" />
                <label v-if="Voce.Dati.Anticipo != null && Voce.Dati.Imponibile >= 0" class="ZMFormLabelError">Il valore della fattura d'anticipo dev'essere negativo</label>
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="number" step="0.01" min="0" @input="OnInputValori(Voce), OnEmitVociFattura(Voce)" class="form-control" v-model="Voce.Dati.Iva" />
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="number" @input="OnInputValori(Voce), OnEmitVociFattura(Voce)" class="form-control" v-model="Voce.Dati.Sconto" />
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <input readonly type="number" class="form-control" v-model="Voce.DatiTotale.Totale" @input="OnEmitVociFattura(Voce)" />
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <a v-if="!FatturaInviataAlloSdi && index != (LsVociVisibili.length - 1) && !Disabilitato" @click="OnClickSpostaVoce(index, index + 1)" data-toggle="class" style="font-size:12px; cursor:pointer;margin-top:5px;margin-left:8px" title="Sposta verso il basso"><i class="fa fa-arrow-down"></i></a>
                <a v-if="!FatturaInviataAlloSdi && index != 0 && !Disabilitato" @click="OnClickSpostaVoce(index, index - 1)" data-toggle="class" style="font-size:12px; cursor:pointer;margin-top:5px;margin-left:8px" title="Sposta verso l'alto"><i class="fa fa-arrow-up"></i></a>
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <a v-if="!FatturaInviataAlloSdi && !Disabilitato" @click="OnClickEliminaVoce(Voce)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px" title="Elimina Voce"><i class="fa fa-trash-o"></i></a>
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <a v-if="Voce.Dati.Anticipo != null" :title="`${Voce.DescrizioneFatturaAnticipo != undefined ? Voce.DescrizioneFatturaAnticipo : 'Avviso di fattura'}`" style="font-size:17px;color:green; cursor:pointer;margin-top:5px;margin-left:8px"><i class="fa fa-chain"></i></a>
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <a v-if="Voce.Dati.Iva == 0 && Voce.Dati.Quantita != 0" @click="OnClickNaturaPagamento(Voce)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px" 
                :title="GetDescrizioneNaturaPagamento(Voce.Dati.NaturaPagamento)"><i class="fa fa-align-justify"></i></a>
              </td>
              <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                <a v-if="Voce.Dati.IdProdotto" @click="OnClickApriPopupDettaglioProdotto(Voce.Dati.IdProdotto)" data-toggle="class" style="font-size:17px;color:#008f39;cursor:pointer;margin-top:5px;margin-left:8px" title="Info prodotto"><i class="fa fa-info-circle"></i></a>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="PopupDettaglioProdotto" style="border:1px">
        <div class="modal-dialog" style="z-index:99999;position:fixed;top:10%;bottom:0;right:0;left:0">
          <div class="modal-content" style="background:#d0e9ff">
            <div style="background-color:#68b6be" class="modal-header">
              <h4 class="modal-title" style="color:#d0e9ff;" id="myModalLabel">
              <img src="../../../assets/images/LogoGemini2.png" style="height:20px;float:left" class="m-r-sm">
              Gemini - Info Prodotto 
              <button style="float:right;margin-top:-5px" class="btn btn-sm btn-icon btn-info" @click="PopupDettaglioProdotto = !PopupDettaglioProdotto">
                <i class="fa fa-times"></i>
              </button>
              </h4>
            </div>
            <div class="modal-body" style="height:150px">
              <div class="ZMNuovaRigaScheda" style="width:100%; float:left">
                <label style="font-size:16px;font-weight: bold;float:left;width:30%">Codice:</label>
                <label style="width:50%;font-size:16px;:left">{{ this.PopupProdottoVisualizzato.Codice }}</label>
              </div>
              <div class="ZMNuovaRigaScheda" style="width:100%; float:left">
                <label style="font-size:16px;font-weight: bold;float:left;width:30%">Descrizione:</label>
                <label style="width:50%;font-size:16px;:left">{{ this.PopupProdottoVisualizzato.Descrizione }}</label>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
      <div>  
        <div class="ZMNuovaRigaScheda">
          <br>
          <div class="col-md-4">
            
          </div> 
          <div class="col-md-3">
            <div class="ZMNuovaRigaScheda" v-if="!NascondiInputIvaSuggerita" style="margin-top:-13px">
              <text style="font-weight:bold;float:left">IVA suggerita</text>
              <label class="ZMLabel" style="float:right;height:20px;font-size:15px;width:50%;text-align:center">{{ CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita }}</label>
            </div>
            
            <div class="ZMNuovaRigaScheda">
              <text style="font-weight: bold">Ritenuta d'acconto [%]</text>
              <input type="number" class="form-control" 
                      v-model="CurrentSchedaVociDocumentiEconomici.Dati.RitenutaAcconto"
                      @input="CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()"
                      :readonly="FatturaInviataAlloSdi || Disabilitato">
            </div>
            <div class="ZMNuovaRigaScheda" v-if="SoloPerLeFatture">
              <label style="font-weight: bold;margin-bottom:0px">Bollo</label>
              <select @change="CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()" style="cursor:default" :disabled="FatturaInviataAlloSdi" class="form-control" v-model="CurrentSchedaVociDocumentiEconomici.Dati.PagamentoBollo">
                <option v-for="SelectOption in LsPagamentoBollo" 
                        :Key="SelectOption.Id"
                        :value="SelectOption.Id"
                        :selected="SelectOption.Id == CurrentSchedaVociDocumentiEconomici.Dati.PagamentoBollo">
                        {{SelectOption.Descrizione}} 
                </option>
              </select>
            </div>
          </div> 
          <div class="col-md-4" style="float:right; width:38%; margin-right:20px">
            <div class="ZMNuovaRigaScheda">
              <a style="float:left">Imponibile </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociDocumentiEconomici.SommaImponibile) }}</label>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a style="float:left">Totale IVA </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociDocumentiEconomici.SommaIva) }}</label>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a  style="float:left">Totale Ivato </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;"> {{ FormattaImporto(CurrentSchedaVociDocumentiEconomici.TotaleIvato) }}</label>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a style="float:left">Ritenuta </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociDocumentiEconomici.TotaleRitenuta) }}</label>
            </div>
            <div class="ZMNuovaRigaScheda" v-if="SoloPerLeFatture && (SchedaVociDocumentiEconomici.Dati.PagamentoBollo == CostantePagamentoBollo.PagatoDaNoi || SchedaVociDocumentiEconomici.Dati.PagamentoBollo == CostantePagamentoBollo.PagatoDalCliente)">
              <a style="float:left;margin-top:5px">Costo bollo </a>
                <input type="number" style="width:70%; float:right; text-align: right;" class="form-control" 
                       v-model="CurrentSchedaVociDocumentiEconomici.Dati.CostoBollo"
                       :readonly="FatturaInviataAlloSdi"
                       step="0.01"
                       @input="CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()">
            </div>
            <div class="ZMNuovaRigaScheda" v-if="SoloPerLeFatture && VisibilitaNumeroAnticipo">
              <a style="float:left;margin-top:3px">Anticipo [€]</a>
                <input type="number" style="width:70%; float:right; text-align: right;" class="form-control" 
                      v-model="CurrentSchedaVociDocumentiEconomici.Dati.NumeroAnticipo"
                      @input="CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()"
                      :readonly="FatturaInviataAlloSdi || Disabilitato">
            </div>
            <div class="ZMNuovaRigaScheda">
              <a v-if="!CurrentSchedaVociDocumentiEconomici.SplitPayment" style="float:left">Netto a pagare </a>
              <a v-else style="float:left">Totale </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociDocumentiEconomici.TotaleFattura) }}</label>
              <label v-if="CurrentSchedaVociDocumentiEconomici.TotaleFattura < 0" class="ZMFormLabelError" style="float:right; text-align: right; font-size: 12px;">Il totale dev'essere positivo</label>
            </div>
            <div class="ZMNuovaRigaScheda" v-if="CurrentSchedaVociDocumentiEconomici.SplitPayment">
              <a style="float:left">Netto a pagare </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociDocumentiEconomici.TotaleSplitPayment) }}</label>
            </div>
          </div>


        </div>
      </div>
  </div>
  <VUEModal v-if="PopupLsProdotti" :Titolo="'Lista Prodotti '" :Altezza="'500px'" :Larghezza="'1200px'"
            @onClickChiudiModal="PopupLsProdotti=false">
    <template v-slot:Body>
        <input type="checkbox" style="width:15px;float:left;margin-left:15px" class="input-sm form-control" v-model="CercaPerSottostringaCodice">
        <label style="float:left;margin-left:10px; margin-top:7px;font-weight:bold;font-size:15px;width:30%">Cerca per sottostringa </label>
        <div style="clear:both;width:1%;height:3px">&nbsp;</div>
          <input type="text" style="width:23%;float:left" class="input-sm form-control" placeholder="Cerca per codice" v-model="FiltroProdottiCodice">
          <div style="width:1%;float:left">&nbsp;</div>
          <input type="text" style="width:76%;float:left" class="input-sm form-control" placeholder="Cerca per descrizione" v-model="FiltroProdottiDescrizione">
     <div style="clear:both;width:1%;height:10px">&nbsp;</div>
       
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:7%;position: sticky; top: 0;"></th>
                      <th style="width:15%;position: sticky; top: 0;">Codice</th>
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
                        {{ Prodotto.CODICE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.DESCRIZIONE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.SETTORE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.PREZZO_IMPONIBILE / 100 != 0? ((Prodotto.PREZZO_IMPONIBILE / 100).toLocaleString("it-IT", {minimumFractionDigits: 2})) : 0}}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Prodotto.IVA / 10 != 0? (Prodotto.IVA / 10).toLocaleString("it-IT", {minimumFractionDigits: 1}) : 0}}
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
     <button class="btn btn-danger" @click="OnClickAnnullaProdotti" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button v-if="ProdottiSelezionati" class="btn btn-success" @click="OnClickConfermaProdotti" style="float:right;margin-right:20px; width:20%">Conferma</button>    
    </template>
  </VUEModal>  

  <VUEModal v-if="PopupNaturaPagamento.Visibile" :Titolo="'Natura Pagamento'" :Altezza="'500px'" :Larghezza="'1200px'" @onClickChiudiModal="PopupNaturaPagamento.Visibile=false">
    <template v-slot:Body>
      <table class="table table-striped b-t b-light" style="width:100%;height: 60%">
        <thead>  
            <tr>
              <th style="position:sticky; width:7%; border:1px solid #ddd; top: 0; background-color:#3B9C9C"></th>
              <th style="position:sticky; width:7%; top: 0; border:1px solid #ddd; font-size:16px; color:white; background-color:#3B9C9C">Natura Pagamento</th> 
            </tr>
        </thead>
        <tbody>
                
          <template v-for="NaturaPagamento in LsNaturaPagamento" :key="NaturaPagamento.Id">
                    <tr>
                        <td style="width:2%; padding:2px; border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;"> 
                          <input 
                              type="radio" 
                              name="ChkNaturaPagamento"
                              v-model="PopupNaturaPagamento.NaturaSelezionata"
                              :value="NaturaPagamento.Id"
                              /> 
                        </td>
                        <td style="width:98%; padding:2px; border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;vertical-align: middle;"> 
                        {{ NaturaPagamento.Descrizione }}
                        </td>
                    </tr>
          </template>

        </tbody>
      </table>
    </template>
    <template v-slot:Footer>
     <button class="btn btn-danger" @click="OnClickAnnullaNaturaPagamento" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button class="btn btn-success" v-if="this.PopupNaturaPagamento.NaturaSelezionata" @click="OnClickConfermaNaturaPagamento" style="float:right;margin-right:20px; width:20%">Conferma</button> 
    </template>
            

  </VUEModal>

  <VUEModal v-if="PopupLsVociPreventiviPredefinite" :Titolo="'Voci preventivi predefinite'" :Altezza="'500px'" :Larghezza="'1200px'"
            @onClickChiudiModal="OnClickAnnullaVociPreventiviPredefinite">
    <template v-slot:Body>
      <div style="clear:both;width:1%;">&nbsp;</div>
      
       
      <div class="row wrapper">
        <section class="panel panel-default" style="background-color:white;">
          <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
            <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
              <thead>
                <tr>
                  <!-- <th>Descrizione</th> -->
                  <th style="width:7%;position: sticky; top: 0;"></th>
                  <!-- <th style="width:15%;position: sticky; top: 0;">Codice</th> -->
                  <th style="width:93%;position: sticky; top: 0;">Descrizione</th>
                  
                </tr>
              </thead>
              <tbody>
                <tr v-for="Voce in ListaVociPreventiviPredefinite" :key="Voce.CHIAVE">
                  <!-- <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Voce.DESCRIZIONE }}
                  </td> -->
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    <input type="checkbox" style="height: 20px;" class="form-control" v-model="Voce.Presente">
                  </td>
                  <!-- <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Prodotto.CODICE }}
                  </td> -->
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Voce.DESCRIZIONE }}
                  </td>
                  <!-- <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Prodotto.SETTORE }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Prodotto.PREZZO_IMPONIBILE / 100 != 0? ((Prodotto.PREZZO_IMPONIBILE / 100).toLocaleString("it-IT", {minimumFractionDigits: 2})) : 0}}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Prodotto.IVA / 10 != 0? (Prodotto.IVA / 10).toLocaleString("it-IT", {minimumFractionDigits: 1}) : 0}}
                  </td> -->
                </tr>
                <!-- <tr v-if="ProdottiFiltrati.length == NumeroMassimoProdotti">
                  <td colspan="6" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:right;vertical-align: middle;color:red">
                    Sono presenti più di 100 prodotti...
                  </td>
                </tr> -->
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </template>
    <template v-slot:Footer>
     <button class="btn btn-danger" @click="OnClickAnnullaVociPreventiviPredefinite" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button v-if="VociSelezionate" class="btn btn-success" @click="OnClickConfermaVociPreventiviPredefinite" style="float:right;margin-right:20px; width:20%">Conferma</button>    
    </template>
  </VUEModal>


    <VUEModal v-if="PopupLsPreventivi" :Titolo="'Lista conferme d\'ordine '" :Altezza="'400px'" :Larghezza="'1000px'"
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
          <div class="row wrapper" style="height:100%">
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
                        {{ FormattaData(Preventivo.DATA) }}
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
     <button class="btn btn-danger" @click="OnClickAnnullaConferme" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button v-if="ConfermeSelezionate" class="btn btn-success" @click="OnClickConfermaConfermaDOrdine" style="float:right;margin-right:20px;width:20%">Conferma</button>
    </template>
  </VUEModal>

    <VUEModal v-if="PopupLsDDT" :Titolo="'Lista DDT'" :Altezza="'400px'" :Larghezza="'1000px'"
            @onClickChiudiModal="PopupLsDDT = false">
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
              <button style="width:90%" class="btn btn-sm btn-info" @click="RicercaDDT">Cerca</button>
            </div>
            <div style="clear:both"></div>
          <div class="row wrapper" style="height:100%">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:7%"></th>
                      <th style="width:12%">Numero</th>
                      <th style="width:12%">Data</th>
                      <th style="width:25%">Cliente</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="DDT in ListaDDT" :key="DDT.CHIAVE">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" style="height: 20px;" class="form-control" v-model="DDT.Presente">
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        DDT nr. {{ DDT.NUMERO_DDT }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ DDT.DATA_DDT }}
                      </td>
                      <td v-if="DDT.RAGIONE_SOCIALE_CLIENTE && DDT.CODICE_CLIENTE" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ DDT.CODICE_CLIENTE }} - {{ DDT.RAGIONE_SOCIALE_CLIENTE }}
                      </td>
                      <td v-else-if="DDT.RAGIONE_SOCIALE_CLIENTE" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ DDT.RAGIONE_SOCIALE_CLIENTE }}
                      </td>
                      <td v-else style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>          
    </template>
    <template v-slot:Footer>
     <button class="btn btn-danger" @click="OnClickAnnullaDDT" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button v-if="DDTSelezionati" class="btn btn-success" @click="OnClickConfermaDDT" style="float:right;margin-right:20px;width:20%">Conferma</button>
    </template>
  </VUEModal>

  <VUEModal v-if="PopupLsFatture" :Titolo="'Lista fatture'" :Altezza="'400px'" :Larghezza="'1000px'"
            @onClickChiudiModal="PopupLsFatture = false">
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
              <button style="width:90%" class="btn btn-sm btn-info" @click="RicercaFatture">Cerca</button>
            </div>
            <div style="clear:both"></div>
          <div class="row wrapper" style="height:100%">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
                <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                  <thead>
                    <tr>
                      <th style="width:7%"></th>
                      <th style="width:12%">Numero</th>
                      <th style="width:12%">Data</th>
                      <th style="width:25%">Cliente</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="Fattura in ListaFatture" :key="Fattura.CHIAVE">
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        <input type="checkbox" style="height: 20px;" class="form-control" v-model="Fattura.Presente">
                      </td>
                      <td v-if="Fattura.NUMERO" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        Fattura nr. {{ Fattura.NUMERO }}
                      </td>
                      <td v-else style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        Avviso fattura nr. {{ Fattura.CHIAVE }}
                      </td>
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Fattura.DATA }}
                      </td>
                      <td v-if="Fattura.RAGIONE_SOCIALE_CLIENTE && Fattura.CODICE_CLIENTE" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Fattura.CODICE_CLIENTE }} - {{ Fattura.RAGIONE_SOCIALE_CLIENTE }}
                      </td>
                      <td v-else-if="Fattura.RAGIONE_SOCIALE_CLIENTE" style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                        {{ Fattura.RAGIONE_SOCIALE_CLIENTE }}
                      </td>
                      <td v-else style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>          
    </template>
    <template v-slot:Footer>
     <button class="btn btn-danger" @click="OnClickAnnullaFatture" style="float:right;margin-right:20px;width:20%">Annulla</button>
     <button v-if="FattureSelezionate" class="btn btn-success" @click="OnClickConfermaFatture" style="float:right;margin-right:20px;width:20%">Conferma</button>
    </template>
  </VUEModal>
</div>
</template>

<script>
import { SystemInformation, PAGAMENTO_BOLLO, TIPO_SCONTO } from '@/SystemInformation.js'
import VUEInputUdm from '@/components/InputComponents/VUEInputUdm.vue'
import { TZEconomicFunct } from '../../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { TZDateFunct } from '../../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { TSchedaGenerica } from '../../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import VUEModal from '@/components/Slots/VUEModal.vue';
import VUEConfirm from '@/components/VUEConfirm.vue';
import { TZFatturaElettronica, TZFattElettronicaNaturaPagamenti } from '../../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';

const DEFAULT_NATURA_PAGAMENTO = TZFattElettronicaNaturaPagamenti.natInversioneContabileSubappaltoEdile
const NATURA_PAGAMENTO_RIBA = TZFattElettronicaNaturaPagamenti.natEscluseExArt15

export class TSingoloVociDocumentiEconomici
{   
   constructor(Chiave, Descrizione, Importo, Quantita, Importo_Ivato, Iva, 
               Sconto, IdDocumento, UnitaDiMisura, 
               CodiceProdotto = '', 
               Anticipo = null, 
               DescrizioneFatturaAnticipo = '',
               NaturaPagamento = DEFAULT_NATURA_PAGAMENTO,
               IdProdotto,
               NonCambiareIva = false)
   {
      this.Dati = {}
      this.Dati.Chiave                = Chiave;
      this.Dati.Descrizione           = Descrizione;
      this.Dati.NaturaPagamento       = NaturaPagamento;
      this.Dati.Importo_Ivato         = Importo_Ivato
      this.Dati.Iva                   = Iva
      this.Dati.Sconto                = Sconto
      this.Dati.Quantita              = Quantita
      this.Dati.IdDocumento           = IdDocumento
      this.Dati.UnitaDiMisura         = UnitaDiMisura
      this.Dati.CodiceProdotto        = CodiceProdotto
      this.Dati.Anticipo              = Anticipo
      this.DescrizioneFatturaAnticipo = DescrizioneFatturaAnticipo
      this.Dati.IdProdotto            = IdProdotto
      this.NonCambiareIva             = NonCambiareIva

      if(Importo)
      {
        if(Importo_Ivato == 'T')
        {
          this.Dati.Ivato          = Importo
          this.Dati.Imponibile     = TZEconomicFunct.GetImponibileFromIvato(Importo,Iva)
          this.Dati.Imponibile     = parseFloat((this.Dati.Imponibile).toFixed(2))
        }
        else
        {
          this.Dati.Imponibile  = Importo
          this.Dati.Ivato       = TZEconomicFunct.GetIvatoFromImponibile(Importo,Iva)
          this.Dati.Ivato       = parseFloat((this.Dati.Ivato).toFixed(2))
        } 
      }
      else 
      {
        this.Dati.Ivato      = 0
        this.Dati.Imponibile = 0
      }

      this.DatiTotale = {}
      this.DatiTotale.Totale       = 0
      this.DatiTotale.Totale_Ivato = 0

      this.CalcoloTotale()
      this.Dati.DaEliminare   = false
      this.Snapshot           = JSON.stringify(this.Dati)  
      this.CopiaDati          = JSON.parse(this.Snapshot)
      
   }

   CalcoloTotale()
   {
      this.DatiTotale.Totale       = 0
      this.DatiTotale.Totale_Ivato = 0

      this.DatiTotale.Totale       = this.Dati.Quantita * (this.Dati.Imponibile - (this.Dati.Imponibile * this.Dati.Sconto / 100))
      this.DatiTotale.Totale_Ivato = this.Dati.Quantita * (this.Dati.Ivato - (this.Dati.Ivato * this.Dati.Sconto / 100))

      this.DatiTotale.Totale       = parseFloat((this.DatiTotale.Totale).toFixed(2))
      this.DatiTotale.Totale_Ivato = parseFloat((this.DatiTotale.Totale_Ivato).toFixed(2))
    }

   AllDataOk()
   {
    
      return (this.Dati.Descrizione.trim() != '' || this.Dati.Quantita == 0) && 
              this.Dati.UnitaDiMisura != -1 && 
             (this.Dati.Anticipo != null ? this.Dati.Imponibile < 0 : true) && 
             ((this.Dati.Imponibile != 0 && this.Dati.Quantita !=0) || (this.Dati.Imponibile == 0 && this.Dati.Quantita == 0) || (this.Dati.Imponibile == 0 && this.Dati.Quantita != 0))

   }

   PrepareQueryParameters(Operazioni,NomeCampoDocumento,Ordinamento, ForzaOrdine)
   {
     var Parametri = { 
                        CHIAVE               : this.Dati.Chiave,
                        [NomeCampoDocumento] : this.Dati.IdDocumento == -1? undefined : this.Dati.IdDocumento,
                        DESCRIZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.Descrizione),
                        ORDINAMENTO          : Ordinamento,
                        UNITA_DI_MISURA      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.UnitaDiMisura),
                        IMPORTO              : this.Dati.Importo_Ivato == 'T' ? (TSchedaGenerica.PrepareForRecordInteger(this.Dati.Ivato * 100)) : (TSchedaGenerica.PrepareForRecordInteger(this.Dati.Imponibile * 100)),
                        QUANTITA             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Quantita * 100),
                        IVA                  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Iva * 100),
                        IMPORTO_IVATO        : TSchedaGenerica.PrepareForRecordString(this.Dati.Importo_Ivato),
                        SCONTO               : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Sconto * 100),
                        CODICE_PRODOTTO      : TSchedaGenerica.PrepareForRecordString(this.Dati.CodiceProdotto),
                        ID_FATTURA_ANTICIPO  : (this.Dati.Anticipo == undefined || this.Dati.Anticipo == null) ? null : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Anticipo),
                        NATURA_PAGAMENTO     : TSchedaGenerica.PrepareForRecordInteger(this.Dati.NaturaPagamento),
                        ID_PRODOTTO          : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdProdotto),
                      }
     if(this.Dati.Chiave == -1 && !this.Dati.DaEliminare)
     {
       Operazioni.push({
                                 Query     : 'InserisciVoce',
                                 Parametri : Parametri,
                                 ResetKeys : [2]
       })
       Operazioni.push({
                                 Query     : 'InserisciLog',
                                 Parametri : { 
                                                CHIAVE               : undefined,
                                                [NomeCampoDocumento] : this.Dati.IdDocumento == -1 ? undefined : this.Dati.IdDocumento,
                                                DATA                 : TSchedaGenerica.PrepareForRecordDateTime(new Date()),
                                                ID_UTENTE            : undefined,
                                                NOTE                 : 'Aggiunta la voce ' + this.__GetDescrizione(this.Dati)
                                              } ,
                                 ResetKeys : [2]
       })
     }
     else
     {
        if(this.Dati.Chiave != -1)
        {
            if(this.Dati.DaEliminare)
            {
              Operazioni.push({
                                        Query     : 'EliminaVoce',
                                        Parametri : { CHIAVE      : this.Dati.Chiave }
              })
              Operazioni.push({
                                        Query     : 'InserisciLog',
                                        Parametri : { 
                                                        CHIAVE               : undefined,
                                                        [NomeCampoDocumento] : this.Dati.IdDocumento == -1 ? undefined : this.Dati.IdDocumento,
                                                        DATA                 : TSchedaGenerica.PrepareForRecordDateTime(new Date()),
                                                        ID_UTENTE            : undefined,
                                                        NOTE                 : 'Eliminata la voce ' + this.__GetDescrizione(this.Dati)
                                                      } ,
                                        ResetKeys : [2]
              })
            }
            else
            {
              if(this.Modificato() || ForzaOrdine )
              {
                Operazioni.push({
                                  Query     : 'ModificaVoce',
                                  Parametri : Parametri
                })
                Operazioni.push({
                                  Query     : 'InserisciLog',
                                  Parametri : { 
                                                  CHIAVE               : undefined,
                                                  [NomeCampoDocumento] : this.Dati.IdDocumento == -1 ? undefined : this.Dati.IdDocumento,
                                                  DATA                 : TSchedaGenerica.PrepareForRecordDateTime(new Date()),
                                                  ID_UTENTE            : undefined,
                                                  NOTE                 : 'Modificata la voce:  ' + this.__GetDescrizione(this.CopiaDati) +
                                                                         '\nin                                ' + this.__GetDescrizione(this.Dati) 
                                                } ,
                                 ResetKeys : [2]
                })
              }
            }
        }
     }
     return Operazioni
   }

   __GetDescrizione(OggettoDati)
   {
     var DescrizioneUnitaDiMisura = SystemInformation.Configurazioni.UnitaDiMisura.find(function(Element)
     {
       return Element.CHIAVE == OggettoDati.UnitaDiMisura
     });
     return OggettoDati.Quantita + ' ' + (DescrizioneUnitaDiMisura? DescrizioneUnitaDiMisura.DESCRIZIONE : '') + ' ' + OggettoDati.Descrizione +
            ' (Prezzo ' + OggettoDati.Imponibile + ') '
   }

   Modificato()
   {
     return this.Snapshot != JSON.stringify(this.Dati)
   }

}

export class TSchedaVociDocumentiEconomici
{
   constructor()
   {
      this.LsVociDocumentiEconomici   = []
      this.LsVociDaEliminare          = []
      this.PreventiviCorrelati        = []
      this.TotaleFattura              = 0
      this.TotaleSplitPayment         = 0
      this.TotaleIvato                = 0
      this.SommaIva                   = 0
      this.SommaImponibile            = 0
      this.TotaleRitenuta             = 0
      this.ForzaOrdineVoci            = false
      this.Dati                       = {
                                          RitenutaAcconto : SystemInformation.Configurazioni.Impostazioni.RITENUTA_ACCONTO,
                                          IvaSuggerita    : SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA,
                                          CostoBollo      : SystemInformation.Configurazioni.Impostazioni.COSTO_BOLLO_SUGGERITO / 100,
                                          PagamentoBollo  : PAGAMENTO_BOLLO.NessunBollo,
                                          NumeroAnticipo  : 0,
                                          ReverseCharge   : false
                                        }
      this.CreateSnapshot();
      this.SplitPayment             = false
      this.SogliaEsenzioneIva       = false
      this.Soglia                   = 0
      this.SogliaNatPag             = -1
      this.IvaOriginale             = 0
      this.LastSommaImponibile      = 0
      this.NaturaPagamentoSuggerita = DEFAULT_NATURA_PAGAMENTO
      this.CalcoloTotaliFattura()
   }

   CreateSnapshot()
   {
     this.Snapshot = JSON.stringify(this.Dati)
   }

   SetDatiCliente(IvaSuggerita,RitenutaAcconto,PagamentoBollo,NaturaPagamento)
   {
     this.Dati.IvaSuggerita        = IvaSuggerita;
     this.Dati.RitenutaAcconto     = RitenutaAcconto;
     this.NaturaPagamentoSuggerita = NaturaPagamento
     if(PagamentoBollo != undefined)
        this.Dati.PagamentoBollo = PagamentoBollo;
   }

   SetSplitPayment(SplitPayment)
   {
     this.SplitPayment = SplitPayment
   }

   SetIdDocumento(IdDocumento)
   {
     this.IdDocumento = IdDocumento
   }

   SetNaturaPagamento(NaturaPagamento)
   {
     this.NaturaPagamentoSuggerita = NaturaPagamento
   }

   SetNumeroAnticipo(NumeroAnticipo)
   {
     this.Dati.NumeroAnticipo = NumeroAnticipo
   }

   RitornaNaturaPagamentoSuggerita()
   {
    return this.NaturaPagamentoSuggerita
   }

   RitornaRitenutaAcconto()
   {
    return (this.Dati.RitenutaAcconto * 10)
   }

   RitornaIvaSuggerita()
   {
    return (this.Dati.IvaSuggerita * 10)
   }

   RitornaNumeroAnticipo()
   {
    return (this.Dati.NumeroAnticipo * 100)
   }

   RitornaCostoBollo()
   {
    return (this.Dati.CostoBollo *100)
   }

   RitornaPagamentoBollo()
   {
    return this.Dati.PagamentoBollo
   }

   Modificato()
   {
      for(var i = 0; i < this.LsVociDocumentiEconomici.length; i++)
         if(this.LsVociDocumentiEconomici[i].Modificato() || this.LsVociDocumentiEconomici[i].Dati.Chiave == -1)
            return true
      if(this.Snapshot != JSON.stringify(this.Dati)) 
        return true
      return false;
   }

   PrepareQueryParameters(Operazioni,NomeCampoDocumento)
   {
      for(var i = 0; i < this.LsVociDocumentiEconomici.length; i++)
        if(this.LsVociDocumentiEconomici[i].Dati.DaEliminare || 
           this.LsVociDocumentiEconomici[i].Dati.Chiave == -1 || 
           this.LsVociDocumentiEconomici[i].Modificato() ||
           this.ForzaOrdineVoci == true)
          this.LsVociDocumentiEconomici[i].PrepareQueryParameters(Operazioni, NomeCampoDocumento, i + 1, this.ForzaOrdineVoci)
      return Operazioni
   }

   AllDataOk()
   {
      for(var i = 0; i < this.LsVociDocumentiEconomici.length; i++)
        if(!this.LsVociDocumentiEconomici[i].Dati.DaEliminare)
           if(!this.LsVociDocumentiEconomici[i].AllDataOk())
             return false
      return true 
   }

   VociPresenti()
   {
    for(var i = 0; i < this.LsVociDocumentiEconomici.length; i++)
      if(!this.LsVociDocumentiEconomici[i].Dati.DaEliminare || this.LsVociDocumentiEconomici[i].Dati.DaEliminare == undefined)
        return true
    return false
   }

   AlmenoUnaVoceSenzaIva()
   {
      for(let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
        if(this.LsVociDocumentiEconomici[i].Dati.Iva == 0 && this.LsVociDocumentiEconomici[i].Dati.Quantita != 0)
          return true
      return false
   }

   SetAltezzaTextarea(Voce)
   { 
      let NrRighe = 1 
      if(Voce.Dati.Descrizione != undefined)
         NrRighe = Voce.Dati.Descrizione.split("\n").length
      Voce.AltezzaTextArea = NrRighe  <= 1 ? '34px' : NrRighe * 22 + 10  + 'px'
   }

   SetTutteLeVociConIvaSuggerita()
   {
      for(let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
        if(!this.LsVociDocumentiEconomici[i].NonCambiareIva)
          this.LsVociDocumentiEconomici[i].Dati.Iva = this.Dati.IvaSuggerita
   }

   CopyData(DocumentiEconomiciSorgente, IsDDT)
   {
      this.LsVociDocumentiEconomici = []
      var LsVociSorgente            = []
      if(IsDDT)
        LsVociSorgente = DocumentiEconomiciSorgente.LsVociDocumentiNonEconomici
      else LsVociSorgente = DocumentiEconomiciSorgente.LsVociDocumentiEconomici

      for(let i = 0; i < LsVociSorgente.length; i++)
      {   

          let SingoloVociDocumentiEconomici = new TSingoloVociDocumentiEconomici(-1, 
                                                                                 LsVociSorgente[i].Dati.Descrizione,
                                                                                 
                                                                                 IsDDT? 
                                                                                 LsVociSorgente[i].Dati.Prezzo : 
                                                                                  (LsVociSorgente[i].Dati.Importo_Ivato == 'T' ? 
                                                                                   LsVociSorgente[i].Dati.Ivato : 
                                                                                   LsVociSorgente[i].Dati.Imponibile), 

                                                                                 LsVociSorgente[i].Dati.Quantita, 
                                                                                 IsDDT? 'F' : LsVociSorgente[i].Dati.Importo_Ivato,
                                                                                 IsDDT? LsVociSorgente[i].Dati.Iva : LsVociSorgente[i].Dati.Iva, 
                                                                                 LsVociSorgente[i].Dati.Sconto,
                                                                                 -1, 
                                                                                 IsDDT? LsVociSorgente[i].Dati.Unita_di_Misura : LsVociSorgente[i].Dati.UnitaDiMisura,
                                                                                 IsDDT? LsVociSorgente[i].Dati.Codice : LsVociSorgente[i].Dati.CodiceProdotto,
                                                                                 null, 
                                                                                 '',
                                                                                 IsDDT? this.NaturaPagamentoSuggerita     : LsVociSorgente[i].Dati.NaturaPagamento,
                                                                                 IsDDT? LsVociSorgente[i].Dati.IdProdotto : LsVociSorgente[i].Dati.IdProdotto)

          this.SetAltezzaTextarea(SingoloVociDocumentiEconomici)
          this.LsVociDocumentiEconomici.push(SingoloVociDocumentiEconomici)
          
      }
      if(!IsDDT)
      {
        if(DocumentiEconomiciSorgente.Dati.PagamentoBollo != undefined)
          this.Dati.PagamentoBollo      = DocumentiEconomiciSorgente.Dati.PagamentoBollo
        if(DocumentiEconomiciSorgente.Dati.CostoBollo != undefined)
          this.Dati.CostoBollo          = DocumentiEconomiciSorgente.Dati.CostoBollo
        this.Dati.RitenutaAcconto     = DocumentiEconomiciSorgente.Dati.RitenutaAcconto
        this.Dati.IvaSuggerita        = DocumentiEconomiciSorgente.Dati.IvaSuggerita      
      }
      this.CreateSnapshot()
      this.CalcoloTotaliFattura()
   }

   AggiungiVoce(Chiave, Descrizione, Importo, Quantita, Importo_Ivato, Iva, Sconto, IdDocumento, UnitaDiMisura, CodiceProdotto, Anticipo, DescrizioneFatturaAnticipo, NaturaPagamento, IdProdotto)
   {
      let SingoloVociDocumentiEconomici = new TSingoloVociDocumentiEconomici(Chiave, 
                                                                            Descrizione, 
                                                                            Importo, 
                                                                            Quantita, 
                                                                            Importo_Ivato, 
                                                                            Iva, 
                                                                            Sconto, 
                                                                            IdDocumento, 
                                                                            UnitaDiMisura, 
                                                                            CodiceProdotto, 
                                                                            Anticipo, 
                                                                            DescrizioneFatturaAnticipo, 
                                                                            NaturaPagamento,
                                                                            IdProdotto)
      this.SetAltezzaTextarea(SingoloVociDocumentiEconomici)
      this.LsVociDocumentiEconomici.push(SingoloVociDocumentiEconomici)
   }

   SetSoluzioniApprovate(LsSoluzioniAccettate)
   {
      for(let i = 0; i < LsSoluzioniAccettate.length; i++)
      {
        let Soluzione = LsSoluzioniAccettate[i]
        if(LsSoluzioniAccettate.length > 1)
          this.AggiungiVoce(-1,
                            'Soluzione Nr. ' + (i + 1),
                            0, 
                            0, 
                            'F',
                            0, 
                            0,
                            -1, 
                            SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA,
                            '',
                            0,
                            '',
                            -1,
                            -1)

        for(let j = 0; j < Soluzione.Dati.LsVociSoluzioni.length; j++)
        {
          let Voce = Soluzione.Dati.LsVociSoluzioni[j]

          this.AggiungiVoce(-1,
                            Voce.Dati.Descrizione,
                            parseFloat((Voce.Dati.Imponibile).toFixed(2)), 
                            parseFloat((Voce.Dati.Quantita).toFixed(2)), 
                            'F',
                            parseFloat((Voce.Dati.Iva).toFixed(2)), 
                            parseFloat((Voce.Dati.Sconto).toFixed(2)),
                            -1, 
                            parseInt(Voce.Dati.UnitaDiMisura),
                            TSchedaGenerica.DisponiFromString(Voce.Dati.CodiceProdotto),
                            null, 
                            '',
                            Voce.Dati.NaturaPagamento,
                            TSchedaGenerica.DisponiFromListIndex(Voce.Dati.IdProdotto))
        }
      }
      this.CalcoloTotaliFattura()
   }

   AssignDati(LsVociDocumentiEconomici,IvaSuggerita,RitenutaAcconto,NomeCampoDocumento,NaturaPagamento,PagamentoBollo,CostoBollo,SplitPayment, NumeroAnticipo)
   {
    
      if(PagamentoBollo != undefined)
        this.Dati.PagamentoBollo = PagamentoBollo
      
      if(NumeroAnticipo != undefined)
        this.Dati.NumeroAnticipo = NumeroAnticipo
      else this.Dati.NumeroAnticipo = 0
      
      if(CostoBollo != undefined)
        this.Dati.CostoBollo = CostoBollo != undefined? (CostoBollo/100) : (SystemInformation.Configurazioni.Impostazioni.COSTO_BOLLO_SUGGERITO / 100)
      
      if(NaturaPagamento == -1)
        this.NaturaPagamentoSuggerita = DEFAULT_NATURA_PAGAMENTO 
      else this.NaturaPagamentoSuggerita     = NaturaPagamento

      this.Dati.RitenutaAcconto     = RitenutaAcconto / 10;
      
      this.Dati.IvaSuggerita        = IvaSuggerita;
      
      this.CreateSnapshot();
      this.NomeCampoDocumento       = NomeCampoDocumento;
      this.SplitPayment             = SplitPayment;
      this.LsVociDocumentiEconomici = []

      for(let i = 0; i < LsVociDocumentiEconomici.length; i++)
      {
        this.AggiungiVoce(LsVociDocumentiEconomici[i].CHIAVE,
                          LsVociDocumentiEconomici[i].DESCRIZIONE,
                          parseFloat((LsVociDocumentiEconomici[i].IMPORTO / 100).toFixed(2)), 
                          parseFloat((LsVociDocumentiEconomici[i].QUANTITA / 100).toFixed(2)), 
                          LsVociDocumentiEconomici[i].IMPORTO_IVATO,
                          parseFloat((LsVociDocumentiEconomici[i].IVA / 100).toFixed(2)), 
                          parseFloat((LsVociDocumentiEconomici[i].SCONTO / 100).toFixed(2)),
                          LsVociDocumentiEconomici[i][NomeCampoDocumento], 
                          parseInt(LsVociDocumentiEconomici[i].UNITA_DI_MISURA),
                          TSchedaGenerica.DisponiFromString(LsVociDocumentiEconomici[i].CODICE_PRODOTTO),
                          LsVociDocumentiEconomici[i].ID_FATTURA_ANTICIPO == undefined ? null : parseInt(LsVociDocumentiEconomici[i].ID_FATTURA_ANTICIPO),
                          LsVociDocumentiEconomici[i].DESCR_FATTURA_ANTICIPO,
                          LsVociDocumentiEconomici[i].NATURA_PAGAMENTO,
                          TSchedaGenerica.DisponiFromListIndex(LsVociDocumentiEconomici[i].ID_PRODOTTO))
      }
      this.CalcoloTotaliFattura()
   }

   ClearVociDocumentiEconomici()
   {
    this.LsVociDocumentiEconomici= []
   }

   SetSpesaIncasso(Descrizione, ImportoSuggerito, ContoRiba = false)
   {
      this.LsVociDocumentiEconomici = []
      let Importo = parseFloat((ImportoSuggerito /100).toFixed(2))
      let NaturaPagamento = ContoRiba ? NATURA_PAGAMENTO_RIBA : this.NaturaPagamentoSuggerita
      let SingoloVociDocumentiEconomici = new TSingoloVociDocumentiEconomici(-1,
                                                                             Descrizione,
                                                                             Importo, 
                                                                             1,
                                                                             'F',
                                                                             ContoRiba ? 0 : 22, 
                                                                             0, 
                                                                             this.IdDocumento,
                                                                             SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA,
                                                                             '',
                                                                             null,
                                                                             '',
                                                                             NaturaPagamento,
                                                                             true)
      this.SetAltezzaTextarea(SingoloVociDocumentiEconomici)
      this.LsVociDocumentiEconomici.push(SingoloVociDocumentiEconomici)
      
      this.CalcoloTotaliFattura()
      this.Snapshot = JSON.stringify(this.LsVociDocumentiEconomici);

   }

   CalcoloTotaliFattura()
   {
      //ATTENZIONE STESSO CODICE IN PHP, SE CAMBI QUA CAMBIARE ANCHE I CALCOLI IN PHP
      
      var Self = this
      this.TotaleFattura      = 0.00
      this.TotaleIvato        = 0.00
      this.SommaIva           = 0.00
      this.SommaImponibile    = 0.00
      this.TotaleRitenuta     = 0.00
      this.TotaleSplitPayment = 0.00

      var LsIva = []; 

      var GetIva = function(IVA)
      {
        for(var i = 0; i < LsIva.length; i++)
          if(LsIva[i].IVA == IVA)
              return LsIva[i];

        var Result = { IVA : IVA, SommaImponibile : 0}
        LsIva.push(Result);
        return Result;
      }

      this.LsVociDocumentiEconomici.forEach(function(AVoce)
      {
        if(!AVoce.Dati.DaEliminare)
           GetIva(AVoce.Dati.Iva).SommaImponibile += parseFloat((AVoce.Dati.Quantita * (AVoce.Dati.Imponibile - (AVoce.Dati.Imponibile * AVoce.Dati.Sconto / 100))).toFixed(2))
      })

      LsIva.forEach(function(Elemento)
      {
        Self.SommaImponibile = (Self.SommaImponibile + Elemento.SommaImponibile)
      })

      LsIva.forEach(function(Elemento)
      {
        Self.SommaIva = Self.SommaIva + (Elemento.SommaImponibile * Elemento.IVA / 100)
      })

      this.SommaImponibile = Math.round(this.SommaImponibile * 100) / 100;
      this.SommaIva        = Math.round(this.SommaIva * 100) / 100;
      
      if(this.Dati.RitenutaAcconto != 0 && this.Dati.RitenutaAcconto != '')
      {
        this.TotaleRitenuta = this.SommaImponibile * this.Dati.RitenutaAcconto / 100
        this.TotaleRitenuta = Math.round(this.TotaleRitenuta * 100) / 100;
      }

      this.TotaleIvato     = this.SommaImponibile + this.SommaIva

      let OldTotaleFattura = this.TotaleFattura;
      this.TotaleFattura   = this.TotaleIvato - this.TotaleRitenuta

      if(this.Dati.NumeroAnticipo != 0)
        this.TotaleFattura  -= this.Dati.NumeroAnticipo

      if(this.Dati.PagamentoBollo == PAGAMENTO_BOLLO.PagatoDalCliente)
        this.TotaleFattura   = this.TotaleFattura + parseFloat(this.Dati.CostoBollo)

      if(this.SplitPayment)
      {
        this.TotaleSplitPayment = this.TotaleFattura - this.SommaIva
        this.TotaleSplitPayment = this.TotaleSplitPayment.toFixed(2)
      }

      this.TotaleIvato     = this.TotaleIvato.toFixed(2)
      this.SommaIva        = this.SommaIva.toFixed(2)
      this.SommaImponibile = this.SommaImponibile.toFixed(2)
      this.TotaleRitenuta  = this.TotaleRitenuta.toFixed(2)
      this.TotaleFattura   = this.TotaleFattura.toFixed(2)

      if(OldTotaleFattura != this.TotaleFattura)
        if(this.OnChangeTotale != undefined)
          this.OnChangeTotale(); 

      if(this.SommaImponibile != this.LastSommaImponibile)
        if(this.SogliaEsenzioneIva)
          this.RaggiungimentoSogliaEsenzione()

      this.SistemaVociDaEliminareInFondoLaLista()
    }

    RaggiungimentoSogliaEsenzione()
    {
      this.LastSommaImponibile = this.SommaImponibile
      if(this.Dati.IvaSuggerita != 0)
        this.IvaOriginale = this.Dati.IvaSuggerita

      if(this.SommaImponibile - this.Soglia >= 0)
      {
        for( let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
        {
          this.LsVociDocumentiEconomici[i].Dati.Iva = 0
          this.LsVociDocumentiEconomici[i].Dati.NaturaPagamento = this.SogliaNatPag
        }
        
        this.Dati.IvaSuggerita    = 0
        for( let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
          this.LsVociDocumentiEconomici[i].CalcoloTotale()
        this.CalcoloTotaliFattura()
      }
      else
      {
        this.Dati.IvaSuggerita = this.IvaOriginale
        for( let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
          this.LsVociDocumentiEconomici[i].Dati.Iva = this.IvaOriginale
        for( let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
          this.LsVociDocumentiEconomici[i].CalcoloTotale()
        this.CalcoloTotaliFattura()
      }
    }

    CaricaIvaSuggerita()
    {
      for( let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
      {
        this.LsVociDocumentiEconomici[i].Dati.Iva = this.Dati.IvaSuggerita
        this.LsVociDocumentiEconomici[i].CalcoloTotale()
      }
      this.CalcoloTotaliFattura()
    }

    CaricaIvaDefault()
    {
      for( let i = 0; i < this.LsVociDocumentiEconomici.length; i++)
      {
        this.LsVociDocumentiEconomici[i].Dati.Iva = SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA /10
        this.LsVociDocumentiEconomici[i].CalcoloTotale()
      }
      this.CalcoloTotaliFattura()
    }

    SistemaVociDaEliminareInFondoLaLista()
    {
      this.LsVociDocumentiEconomici.sort(function(a,b)
                                        {
                                          if(!a.Dati.DaEliminare && b.Dati.DaEliminare)
                                            return -1
                                          if(a.Dati.DaEliminare && !b.Dati.DaEliminare)
                                            return 1
                                          return 0
                                        })
    }
}


export default {

  components : 
   {
      VUEInputUdm,
      VUEModal,
      VUEConfirm
   },

  emits: ['onChange', 'onClickGeneraNotaDiCredito', 'inviaChiaviDDTCaricate', 'onChangeCondizioniDiPagamentoCaricandoConferme'],
  data()
  {
    return {
              InserimentoNuovaRiga              : {},
              NrRigheDaAggiungere               : 1,
              AssociaSconto                     : false,
              AssegnaSconto                     : 0,
              PopupDettaglioProdotto            : false,
              PopupProdottoVisualizzato         : {},
              PopupLsProdotti                   : false,
              PopupLsVociPreventiviPredefinite  : false,
              PopupLsPreventivi                 : false,
              PopupLsDDT                        : false,
              PopupVociDocumentiEconomici       : false,
              PopupLsFatture                    : false,
              PopupGeneraNotaDiCredito          : false,
              PopupNaturaPagamento              : {
                                                     Visibile               : false,
                                                     NaturaSelezionata      : -1,
                                                     VoceInModifica         : null, 
                                                  },
              VecchiaNaturaPagamento            : -1, 
              PopupCondPagamentoNonOmogeneeNeiPreventiviCaricati : false,                                  
              ListaProdotti                     : [],
              ListaProdottiVariazPrezzo         : [],
              LsNaturaPagamento                 : TZFatturaElettronica.GetLsNaturaPagamenti(),
              ListaVociPreventiviPredefinite    : [],
              ListaPreventivi                   : [],
              ListaDDT                          : [],
              ListaFatture                      : [],
              FiltroProdottiCodice              : '',
              FiltroProdottiDescrizione         : '',
              CercaPerSottostringaCodice        : false,
              NumeroMassimoProdotti             : 100,
              DallaData                         : TZDateFunct.DateInHTMLInputFormat(new Date('January 01,' + (new Date().getFullYear()))),
              AllaData                          : TZDateFunct.DateInHTMLInputFormat(new Date()),
              CostantePagamentoBollo            : PAGAMENTO_BOLLO,
              LsPagamentoBollo                  : SystemInformation.GetLsPagamentoBollo(),
              VisibilitaListinoPrezziCliente    : SystemInformation.AccessRights.VisibilitaListinoPrezziCliente(),
              VisibilitaNumeroAnticipo          : SystemInformation.AccessRights.VisibilitaNumeroAnticipo(),
              positions                         : {
                                                    clientX: undefined,
                                                    clientY: undefined,
                                                    movementX: 0,
                                                    movementY: 0
                                                  }
    }
  },

  props : ['SchedaVociDocumentiEconomici','TastoCreaNotaVisibile', 'InviataAlloSdi', 
           'IsSchedaFattura', 'IsSchedaPreventivo', 'IdCliente', 'NascondiIvaSuggerita', 
           'NascondiPulsanti', 'ReverseCharge', 'Disabilitato', 'RichiestaPopupNotaDiCredito'],
  
  computed :
  {
    CurrentSchedaVociDocumentiEconomici: 
    {
      get()
      {
        var TmpScheda = this.SchedaVociDocumentiEconomici;
        var Self = this;
        TmpScheda.OnChangeTotale = function()
        {
          Self.$emit('onChange')
        }
        return TmpScheda;
      }
    },
    RichiestaPopupNota :
    {
      get()
      {
        if(this.RichiestaPopupNotaDiCredito != undefined)
          return this.RichiestaPopupNotaDiCredito
        return 'Vuoi generare la nota di credito?'
      }
    },
    FatturaInviataAlloSdi: 
    {
      get()
      {
        return this.InviataAlloSdi;
      }
    },
    SoloPerLeFatture: 
    {
      get()
      {
        return this.IsSchedaFattura;
      }
    },
    ID_CLIENTE: 
    {
      get()
      {
        return this.IdCliente;
      }
    },
    NascondiInputIvaSuggerita: 
    {
      get()
      {
        if(this.NascondiIvaSuggerita == undefined || this.NascondiIvaSuggerita == false)
          return false
        else return true;
      }
    },
    NascondiPulsantiVoci: 
    {
      get()
      {
        return this.NascondiPulsanti;
      }
    },
    AlmenoUnaVoceSenzaIva:
    {
      get()
      {
        for(let i = 0; i < this.SchedaVociDocumentiEconomici.LsVociDocumentiEconomici.length; i++)
          if(this.SchedaVociDocumentiEconomici.LsVociDocumentiEconomici[i].Dati.Quantita != 0)
            if(this.SchedaVociDocumentiEconomici.LsVociDocumentiEconomici[i].Dati.Iva == 0)
              return true
        return false
      }
    },
    LsVociVisibili :
    {
        get()
        {
          var Result = []
          this.SchedaVociDocumentiEconomici.LsVociDocumentiEconomici.forEach(function(AVociDocumentiEconomici)
          {
              if(!AVociDocumentiEconomici.Dati.DaEliminare)
                Result.push(AVociDocumentiEconomici)
          })
          return Result;
        }
    },

    ProdottiSelezionati :
    {
      get()
      {
        for(var i = 0; i < this.ListaProdotti.length; i++)
        if(this.ListaProdotti[i].Presente)
          return true
        return false
      }
    },

    VociSelezionate :
    {
      get()
      {
        for(var i = 0; i < this.ListaVociPreventiviPredefinite.length; i++)
        if(this.ListaVociPreventiviPredefinite[i].Presente)
          return true
        return false
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

    DDTSelezionati :
    {
      get()
      {
        for(var i = 0; i < this.ListaDDT.length; i++)
        if(this.ListaDDT[i].Presente)
          return true
        return false
      }
    },

    FattureSelezionate :
    {
      get()
      {
        for(var i = 0; i < this.ListaFatture.length; i++)
        if(this.ListaFatture[i].Presente)
          return true
        return false
      }
    },

    ProdottiFiltrati :
    {
      get()
      {
        var FiltroCodice            = this.FiltroProdottiCodice.toUpperCase().trim();

        var FiltroDescr             = this.FiltroProdottiDescrizione.toUpperCase().trim();
        var ListaParoleDescr        = FiltroDescr.split(' ')

        var ListaRighe       = []
        let Self = this

        if(FiltroCodice == '' && FiltroDescr == '')
        {
          ListaRighe = this.ListaProdotti.slice(0, this.NumeroMassimoProdotti)
          return ListaRighe
        }
        else 
        {
          ListaRighe = this.ListaProdotti.filter(function(Prodotto)
          {
            if(FiltroCodice != '' && FiltroDescr != '')
            {
              if(Self.FiltraPerCodice(FiltroCodice, Prodotto))
                if(Self.FiltraPerDescrizione(Prodotto, ListaParoleDescr))
                  return true;
              return false
            }
            
            if(FiltroCodice != '')
            {
              return Self.FiltraPerCodice(FiltroCodice, Prodotto)
            }

            if(FiltroDescr != '')
            {
              return Self.FiltraPerDescrizione(Prodotto, ListaParoleDescr)
            }
            return false;
          })
          ListaRighe = ListaRighe.slice(0, this.NumeroMassimoProdotti) 
          return ListaRighe
        }
      }
    }
  },

  methods:
  {
    FormattaData(Data)
    {
      return TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Date.parse(Data)));
    },

    FormattaImporto(Importo)
    {
      return SystemInformation.FormattaImporto(Importo)
    },

    FiltraPerCodice(FiltroCodice, Prodotto)
    {
      if(this.CercaPerSottostringaCodice)
      {
        if(Prodotto.CODICE == FiltroCodice)
          return true
        return false
      }

      if(Prodotto.CODICE.includes(FiltroCodice))
        return true
      return false
    },

    FiltraPerDescrizione(Prodotto, ListaParoleDescr)
    {
      let ListaParoleProdotto = Prodotto.DESCRIZIONE.split(' ')
      let Trovato             = false
      for(let i = 0; i < ListaParoleDescr.length; i++)
      {
        for(let j = 0; j < ListaParoleProdotto.length; j++)
        {
          if(ListaParoleProdotto[j].includes(ListaParoleDescr[i]))
          {
            Trovato = true
          }
        }
        if(!Trovato)
        {
          return false
        }
        Trovato = false
      }
      return true
    },

    OnMouseDownDatiTask: function (event) 
    {            
        this.positions.clientX = event.clientX
        this.positions.clientY = event.clientY
        document.onmousemove   = this.OnMouseMoveDatiTask
        document.onmouseup     = this.OnMouseUpDatiTask
    },
    OnMouseMoveDatiTask: function (event) 
    {
        this.positions.movementX = this.positions.clientX - event.clientX
        this.positions.movementY = this.positions.clientY - event.clientY
        this.positions.clientX   = event.clientX
        this.positions.clientY   = event.clientY
        // set the element's new position:
        this.$refs.ModalProdotti.style.top  = (this.$refs.ModalProdotti.offsetTop - this.positions.movementY) + 'px'
        this.$refs.ModalProdotti.style.left = (this.$refs.ModalProdotti.offsetLeft - this.positions.movementX) + 'px'
    },
    OnMouseUpDatiTask() 
    {
        document.onmouseup = null
        document.onmousemove = null
    },

    OnClickAnnullaVociPreventiviPredefinite()
    {
      for(let Voce of this.ListaVociPreventiviPredefinite)
      { 
        if(Voce.Presente)
          Voce.Presente = false;
      }

      this.PopupLsVociPreventiviPredefinite = false;
    },

    OnClickConfermaVociPreventiviPredefinite()
    {
      for(let i = 0; i < this.ListaVociPreventiviPredefinite.length; i++)
      {
        if(this.ListaVociPreventiviPredefinite[i].Presente)
        {
          let InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                        this.ListaVociPreventiviPredefinite[i].DESCRIZIONE,
                                                                        0,
                                                                        TSchedaGenerica.DisponiFromInteger(1),
                                                                        'F',
                                                                        0,
                                                                        0.00,
                                                                        this.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                        SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA,
                                                                        this.ListaVociPreventiviPredefinite[i].CHIAVE)
          InserimentoNuovaRiga.CalcoloTotale()
          this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoNuovaRiga)
          this.ListaVociPreventiviPredefinite[i].Presente = false
        }
      }
      setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50) 
      this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()



      this.PopupLsVociPreventiviPredefinite = false;
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

    OnClickGeneraNotaDiCredito()
    {
      this.PopupGeneraNotaDiCredito = true
    },

    ConfermaGenerazioneNota()
    {
      this.$emit('onClickGeneraNotaDiCredito') 
    },

    OnClickListaProdotti()
    {
      this.ListaProdotti   = null
      
      var Self = this;
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
    },

    OnClickApriPopupDettaglioProdotto(IdProdotto)
    {
      this.ListaProdotti = SystemInformation.Configurazioni.Prodotti
      this.PopupProdottoVisualizzato = {}
      for(let i = 0; i < this.ListaProdotti.length; i++)
      {
        if(this.ListaProdotti[i].CHIAVE == IdProdotto)
        {
          this.PopupProdottoVisualizzato.ChiaveProdotto = IdProdotto
          this.PopupProdottoVisualizzato.Codice = this.ListaProdotti[i].CODICE
          this.PopupProdottoVisualizzato.Descrizione = this.ListaProdotti[i].DESCRIZIONE
          this.PopupDettaglioProdotto = true
          return this.PopupProdottoVisualizzato
        }
      }
    },

    OnClickNaturaPagamento(Voce)
    {   
        this.PopupNaturaPagamento.Visibile = true
        this.PopupNaturaPagamento.VoceInModifica = Voce
        this.PopupNaturaPagamento.NaturaSelezionata = Voce.Dati.NaturaPagamento
    },

    GetDescrizioneNaturaPagamento(NaturaPagamento)
    {
      for(let i = 0; i < this.LsNaturaPagamento.length; i++)
      {
          if(this.LsNaturaPagamento[i].Id == NaturaPagamento)
            return this.LsNaturaPagamento[i].Descrizione
      }    
      return "???"
    },

    OnClickAggiungiVoci()
    {
      this.ListaVociPreventiviPredefinite   = SystemInformation.Configurazioni.VociPreventiviPredefinite;
      this.PopupLsVociPreventiviPredefinite = true;
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
                                              Self.ListaPreventivi = ArrayInfo;
                                              Self.ListaPreventivi.forEach(function(Preventivo)
                                              {
                                                Preventivo.DATA = new Date(Preventivo.DATA);
                                              })
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista dei preventivi');
                                          },
                                          function (HTTPError, SubHTTPError, Response) 
                                          {
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

    OnClickAnnullaFatture()
    {
        this.PopupLsFatture = false
    },

    RicercaFatture()
    {   
      var Self = this;
      SystemInformation.AdvQuery.GetSQL('Fatture',{DallaData: this.DallaData, AllaData: this.AllaData, CHIAVE : this.ID_CLIENTE}, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaFattureXVociFattura");
                                            if (ArrayInfo != undefined) 
                                            {
                                              Self.ListaFatture = ArrayInfo
                                              Self.ListaFatture.forEach(function(Fattura)
                                              {
                                                  Fattura.DATA = new Date(Fattura.DATA)
                                                  Fattura.DATA = TZDateFunct.FormatDateTime('dd/mm/yyyy',Fattura.DATA)
                                              })
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista delle fatture');
                                          },
                                          function (HTTPError, SubHTTPError, Response) {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaFattureXVociFattura')    
    },    

    OnClickListaFatture()
    {
        this.DallaData = TZDateFunct.DateInHTMLInputFormat(new Date('January 01,' + (new Date().getFullYear())))
        this.AllaData  = TZDateFunct.DateInHTMLInputFormat(new Date())
        this.PopupLsFatture = true
        this.RicercaFatture()
    },

    OnClickAnnullaDDT()
    {
        this.PopupLsDDT = false
    },

    OnClickListaDDT()
    {
        this.DallaData = TZDateFunct.DateInHTMLInputFormat(new Date('January 01,' + (new Date().getFullYear())))
        this.AllaData  = TZDateFunct.DateInHTMLInputFormat(new Date())
        this.PopupLsDDT = true
        this.RicercaDDT()
    },

    RicercaDDT()
    {
      var Self = this;
      SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasporto',{DallaData: this.DallaData, AllaData: this.AllaData, CHIAVE : this.ID_CLIENTE}, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaDDTVoci");
                                            if (ArrayInfo != undefined) 
                                            {
                                              Self.ListaDDT = ArrayInfo
                                              Self.ListaDDT.forEach(function(DDT)
                                              {
                                                  DDT.DATA_DDT = new Date(DDT.DATA_DDT)
                                                  DDT.DATA_DDT = TZDateFunct.FormatDateTime('dd/mm/yyyy',DDT.DATA_DDT)
                                              })
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista dei DDT');
                                          },
                                          function (HTTPError, SubHTTPError, Response) {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaDDTVoci')    
    },

    OnClickConfermaPopup()
    {
       this.InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,'Spese di trasporto',parseInt(SystemInformation.Configurazioni.Impostazioni.SPESE_DI_TRASPORTO_SUGGERITE/100),1,'F',22,0.00,this.CurrentSchedaVociDocumentiEconomici.IdDocumento,SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
       this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(this.InserimentoNuovaRiga)
       this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
       this.PopupVociDocumentiEconomici = false
    },

    OnClickAggiungiSpeseDiTrasporto()
    {
      this.PopupVociDocumentiEconomici = true
    },  

    OnClickConfermaProdotti()
    {
      let IVA     = this.CurrentSchedaVociDocumentiEconomici.Dati.ReverseCharge ? 
                    0 : this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita
      for(let i = 0; i < this.ListaProdotti.length; i++)
      {
        if(this.ListaProdotti[i].Presente)
        {
          let IVARiga = IVA < TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].IVA) / 10? 
                        IVA : TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].IVA) / 10

          let InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                        
                                                                        this.ListaProdotti[i].DESCRIZIONE,

                                                                        this.ListaProdotti[i].PREZZO_SCONTATO == null? 
                                                                          this.ListaProdotti[i].PREZZO_IMPONIBILE / 100 
                                                                          : this.ListaProdotti[i].PREZZO_SCONTATO,
                                                                        
                                                                        TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].QUANTITA_SUGGERITA),
                                                                        
                                                                        'F',

                                                                        IVARiga,
                                                                        
                                                                        this.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE == null? 
                                                                          0.00 
                                                                          : this.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE,

                                                                        this.CurrentSchedaVociDocumentiEconomici.IdDocumento,

                                                                        this.ListaProdotti[i].UNITA_DI_MISURA == null? 
                                                                        SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA : 
                                                                        this.ListaProdotti[i].UNITA_DI_MISURA,
                                                                        
                                                                        this.ListaProdotti[i].CODICE,
                                                                        
                                                                        null,
                                                                        
                                                                        false,
                                                                        
                                                                        this.CurrentSchedaVociDocumentiEconomici.NaturaPagamentoSuggerita,
                                                                      
                                                                        this.ListaProdotti[i].CHIAVE)
                                                                        
          InserimentoNuovaRiga.CalcoloTotale()
          this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoNuovaRiga)
          this.ListaProdotti[i].Presente = false
        }
      }
      setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50) 
      this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
      this.PopupLsProdotti = false
    },

    OnClickConfermaNaturaPagamento()
    {
      this.PopupNaturaPagamento.VoceInModifica.Dati.NaturaPagamento = this.PopupNaturaPagamento.NaturaSelezionata
      this.PopupNaturaPagamento.Visibile = false
      if(this.PopupNaturaPagamento.NaturaSelezionata != this.VecchiaNaturaPagamento)
      {
         this.$emit('onChange')
      }
    }, 

    OnClickAnnullaNaturaPagamento()
    {
      this.PopupNaturaPagamento.Visibile = false
    },

    
    OnClickConfermaConfermaDOrdine()
    {
      this.PopupLsPreventivi = false

      var CondPagamento   = null
      var CondPagOmogeneo = true

      for(let i = 0; i < this.ListaPreventivi.length; i++)
      {
        if(this.ListaPreventivi[i].Presente)
        {
          if(CondPagamento != null)
          {
            if(CondPagamento != this.ListaPreventivi[i].COND_PAGAMENTO)
              CondPagOmogeneo = false
          }
          else CondPagamento = this.ListaPreventivi[i].COND_PAGAMENTO
        }
      }

      if(this.CurrentSchedaVociDocumentiEconomici.PreventiviCorrelati.length != 0)
      {
        for(let i = 0; i < this.CurrentSchedaVociDocumentiEconomici.PreventiviCorrelati.length; i++)
        {
          if(this.CurrentSchedaVociDocumentiEconomici.PreventiviCorrelati[i].COND_PAGAMENTO != CondPagamento)
            CondPagOmogeneo = false
        }
      }
      

      if(CondPagOmogeneo)
      {
        this.CaricaVociDeiPreventiviSelezionati(CondPagamento)
      }
      else
      {
        this.PopupCondPagamentoNonOmogeneeNeiPreventiviCaricati = true
      }
    },

    OttieniListaCheckati()
    {
      var ListaCheckati   = []

      for(let i = 0; i < this.ListaPreventivi.length; i++)
      {
        if(this.ListaPreventivi[i].Presente && !this.isPreventivoContenuto(this.ListaPreventivi[i].CHIAVE))
        {
          ListaCheckati.push(this.ListaPreventivi[i].CHIAVE);

          var PreventivoCorrelato = {}

          PreventivoCorrelato = Object.assign(PreventivoCorrelato, this.ListaPreventivi[i]);
          this.CurrentSchedaVociDocumentiEconomici.PreventiviCorrelati.push(PreventivoCorrelato);
        }
      }

      return ListaCheckati
    },

    OnClickPopupCondPagamentoNonOmogeneeNeiPreventiviCaricati()
    {
      this.CaricaVociDeiPreventiviSelezionati()
    },

    CaricaVociDeiPreventiviSelezionati(CondPagamento)
    {
      var Self = this;
      let ListaCheckati = this.OttieniListaCheckati()
      if(ListaCheckati.length != 0)
      {
        SystemInformation.AdvQuery.GetSQL('Preventivi', { ListaCheckati: ListaCheckati }, 
                                            function (Results) 
                                            {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaVociPreventiviSelezionati");
                                              if (ArrayInfo != undefined) 
                                              {
                                                let NumeroConfermaDOrdine = 0
                                                for(let i = 0; i < ArrayInfo.length; i++)
                                                {
                                                  if(NumeroConfermaDOrdine != ArrayInfo[i].NUMERO)
                                                  {
                                                    NumeroConfermaDOrdine = ArrayInfo[i].NUMERO
                                                    let InserimentoSeparatore = new TSingoloVociDocumentiEconomici(-1,
                                                                                                                  'Riferimento conferma d\'ordine nr. ' + ArrayInfo[i].NUMERO + ' rev. ' + ArrayInfo[i].NUMERO_REVISIONE + ' del ' + TZDateFunct.FormatDateTime('dd/mm/yyyy',new Date(ArrayInfo[i].DATA_PREVENTIVO)),
                                                                                                                  0.00,
                                                                                                                  0.00,
                                                                                                                  'F',
                                                                                                                  0.00,
                                                                                                                  0.00,
                                                                                                                  Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                                  SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
                                                    
                                                    Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoSeparatore)                                                
                                                    Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoSeparatore)
                                                    
                                                    if(ArrayInfo[i].DESTINAZIONE && ArrayInfo[i].DESTINAZIONE != '')
                                                    {
                                                      let InserimentoSeparatoreDestinazione = new TSingoloVociDocumentiEconomici(-1,
                                                                                                                                'Destinazione: ' + ArrayInfo[i].DESTINAZIONE + ', ' + ArrayInfo[i].INDIRIZZO_DESTINAZIONE + ' ' + ArrayInfo[i].NR_CIVICO_DESTINAZIONE + ', ' + ArrayInfo[i].COMUNE_DESTINAZIONE,
                                                                                                                                  0.00,
                                                                                                                                  0.00,
                                                                                                                                  'F',
                                                                                                                                  0.00,
                                                                                                                                  0.00,
                                                                                                                                  Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                                                  SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
          
                                                      Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoSeparatoreDestinazione)                                                
                                                      Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoSeparatoreDestinazione)
                                                    }
                                                  }
                                                  let InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                                                                ArrayInfo[i].DESCRIZIONE,
                                                                                                                ArrayInfo[i].IMPORTO_IVATO == 'F'? ArrayInfo[i].IMPORTO / 100 : (ArrayInfo[i].IMPORTO * 100 / (100 + ArrayInfo[i].IVA)),
                                                                                                                ArrayInfo[i].QUANTITA / 100,
                                                                                                                ArrayInfo[i].IMPORTO_IVATO,
                                                                                                                Self.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita == 0? 0 : (ArrayInfo[i].IVA / 100),
                                                                                                                ArrayInfo[i].SCONTO / 100,
                                                                                                                Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                                ArrayInfo[i].UNITA_DI_MISURA == null? SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA : ArrayInfo[i].UNITA_DI_MISURA)
                                                  
                                                  Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoNuovaRiga)                                                
                                                  Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoNuovaRiga)
                                                }
                                                Self.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
                                                Self.PopupCondPagamentoNonOmogeneeNeiPreventiviCaricati = false

                                                if(CondPagamento != undefined)
                                                  Self.$emit('onChangeCondizioniDiPagamentoCaricandoConferme', CondPagamento)
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere lista dei preventivi');
                                            },
                                            function (HTTPError, SubHTTPError, Response) 
                                            {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                            },
                                            'ListaVociPreventiviSelezionati')
      }
      else
      {
        this.PopupCondPagamentoNonOmogeneeNeiPreventiviCaricati = false
      }
    },

    isPreventivoContenuto(ChiavePreventivo)
    {
      for(let i = 0; i < this.CurrentSchedaVociDocumentiEconomici.PreventiviCorrelati.length; i++)
      {
        if(this.CurrentSchedaVociDocumentiEconomici.PreventiviCorrelati[i].CHIAVE == ChiavePreventivo)
          return true
      }
      return false
    },

    OnClickConfermaDDT()
    {
      this.PopupLsDDT= false    
      var ListaCheckati = []
      for(let i = 0; i < this.ListaDDT.length; i++)
      {
        if(this.ListaDDT[i].Presente)
        {
          ListaCheckati.push(this.ListaDDT[i].CHIAVE)
        }
      }
      var Self = this;
      SystemInformation.AdvQuery.GetSQL('DocumentiDiTrasporto',{ ListaCheckati: ListaCheckati }, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaVociDDTSelezionati");
                                            if (ArrayInfo != undefined) 
                                            {
                                              var NumeroDDT = 0
                                              let ListaChiaviDDT = []
                                              for(let i = 0; i < ArrayInfo.length; i++)
                                              {
                                                if(NumeroDDT != ArrayInfo[i].NUMERO_DDT)
                                                {
                                                  NumeroDDT = ArrayInfo[i].NUMERO_DDT
                                                  ListaChiaviDDT.push(ArrayInfo[i].CHIAVE_DDT)
                                                  let InserimentoSeparatore = new TSingoloVociDocumentiEconomici(-1,
                                                                                                                'Riferimento DDT nr. ' + ArrayInfo[i].NUMERO_DDT + ' del ' + TZDateFunct.FormatDateTime('dd/mm/yyyy',new Date(ArrayInfo[i].DATA_DDT)),
                                                                                                                0.00,
                                                                                                                0.00,
                                                                                                                'F',
                                                                                                                0.00,
                                                                                                                0.00,
                                                                                                                Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                                SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
                                                  
                                                  Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoSeparatore)                                                
                                                  Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoSeparatore)

                                                  if(ArrayInfo[i].DESTINAZIONE && ArrayInfo[i].DESTINAZIONE != '')
                                                  {
                                                    let InserimentoSeparatoreDestinazione = new TSingoloVociDocumentiEconomici(-1,
                                                                                                                              'Destinazione: ' + ArrayInfo[i].DESTINAZIONE + ', ' + ArrayInfo[i].INDIRIZZO_DESTINAZIONE + ' ' + ArrayInfo[i].NR_CIVICO_DESTINAZIONE + ', ' + ArrayInfo[i].COMUNE_DESTINAZIONE,
                                                                                                                                0.00,
                                                                                                                                0.00,
                                                                                                                                'F',
                                                                                                                                0.00,
                                                                                                                                0.00,
                                                                                                                                Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                                                SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
        
                                                    Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoSeparatoreDestinazione)                                                
                                                    Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoSeparatoreDestinazione)
                                                  }
                                                }
                                                let InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                                                              ArrayInfo[i].DESCRIZIONE,
                                                                                                              TSchedaGenerica.DisponiFromInteger(ArrayInfo[i].IMPORTO) / 100,
                                                                                                              TSchedaGenerica.DisponiFromInteger(ArrayInfo[i].QUANTITA) / 100,
                                                                                                              'F',
                                                                                                              Self.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita == 0? 0 : (SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA /10),
                                                                                                              TSchedaGenerica.DisponiFromInteger(ArrayInfo[i].SCONTO) / 100,
                                                                                                              Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                              ArrayInfo[i].UNITA_DI_MISURA == null? SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA : ArrayInfo[i].UNITA_DI_MISURA,
                                                                                                              TSchedaGenerica.DisponiFromString(ArrayInfo[i].CODICE))
                                                
                                                Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoNuovaRiga)                                                                                                
                                                Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoNuovaRiga)
                                              }
                                              Self.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
                                              if(Self.IsSchedaFattura)
                                                Self.$emit('inviaChiaviDDTCaricate', ListaChiaviDDT) 
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista delle voci ddt');
                                          },
                                          function (HTTPError, SubHTTPError, Response) 
                                          {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaVociDDTSelezionati') 
    },

    OnClickConfermaFatture()
    {
      this.PopupLsFatture = false    
      var ListaCheckati = []
      for(let i = 0; i < this.ListaFatture.length; i++)
      {
        if(this.ListaFatture[i].Presente)
        {
          ListaCheckati.push(this.ListaFatture[i].CHIAVE)
        }
      }
      var Self = this;
      SystemInformation.AdvQuery.GetSQL('Fatture',{ ListaCheckati: ListaCheckati },
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "ListaVociFattureSelezionate");
                                            if (ArrayInfo != undefined) 
                                            {
                                              var NumeroFattura = 0
                                              for(let i = 0; i < ArrayInfo.length; i++)
                                              {
                                                if(NumeroFattura != ArrayInfo[i].NUMERO)
                                                {
                                                  NumeroFattura = ArrayInfo[i].NUMERO
                                                  let Stringa = ArrayInfo[i].NUMERO? 'Riferimento fattura nr. ' + ArrayInfo[i].NUMERO : 'Riferimento avviso fattura nr. ' + ArrayInfo[i].CHIAVE
                                                  let InserimentoSeparatore = new TSingoloVociDocumentiEconomici(-1,
                                                                                                                Stringa + ' del ' + TZDateFunct.FormatDateTime('dd/mm/yyyy',new Date(ArrayInfo[i].DATA)),
                                                                                                                0.00,
                                                                                                                0.00,
                                                                                                                'F',
                                                                                                                0.00,
                                                                                                                0.00,
                                                                                                                Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                                SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA)
                                                  
                                                  Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoSeparatore)                                                
                                                  Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoSeparatore)
                                                }
                                                let InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                                                              ArrayInfo[i].DESCRIZIONE,
                                                                                                              ArrayInfo[i].IMPORTO_IVATO == 'F'? ArrayInfo[i].IMPORTO / 100 : (ArrayInfo[i].IMPORTO * 100 / (100 + ArrayInfo[i].IVA)),
                                                                                                              ArrayInfo[i].QUANTITA / 100,
                                                                                                              ArrayInfo[i].IMPORTO_IVATO,
                                                                                                              Self.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita == 0? 0 : (ArrayInfo[i].IVA / 100),
                                                                                                              ArrayInfo[i].SCONTO / 100,
                                                                                                              Self.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                                                              ArrayInfo[i].UNITA_DI_MISURA == null? SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA : ArrayInfo[i].UNITA_DI_MISURA)
                                                
                                                Self.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(InserimentoNuovaRiga)                                                
                                                Self.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(InserimentoNuovaRiga)
                                              }
                                                Self.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()

                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista delle voci fatture');
                                          },
                                          function (HTTPError, SubHTTPError, Response) {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaVociFattureSelezionate') 
    },

    OnClickNuovaRigaVoce()
    {
      let IVA = this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita == 0? 
                0 : this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita

      IVA     = this.CurrentSchedaVociDocumentiEconomici.Dati.ReverseCharge ? 
                0 : IVA

      for(let i = 0; i < this.NrRigheDaAggiungere; i++)
      {
        this.InserimentoNuovaRiga = new TSingoloVociDocumentiEconomici(-1,
                                                                       '',
                                                                       0.00,
                                                                       0.00,
                                                                       'F',
                                                                       IVA,
                                                                       0.00,
                                                                       this.CurrentSchedaVociDocumentiEconomici.IdDocumento,
                                                                       SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA,
                                                                       '',null,'',this.CurrentSchedaVociDocumentiEconomici.NaturaPagamentoSuggerita)
        this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.push(this.InserimentoNuovaRiga)
        setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50)        
      }
      this.CurrentSchedaVociDocumentiEconomici.SistemaVociDaEliminareInFondoLaLista()
      this.$emit('onChange')
    },

    OnClickEliminaVoce(Voce)
    {
      Voce.Dati.DaEliminare = true
      this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
    },

    OnClickSpostaVoce(Index, NewIndex)
    {
      this.CurrentSchedaVociDocumentiEconomici.ForzaOrdineVoci = true
      let Tmp = this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[Index];

      this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[Index]    = this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[NewIndex];
      this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[NewIndex] = Tmp;
      
      //Forse non serve
      if(this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[Index].Dati.OrdinamentoModificato == undefined)
         this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[Index].Dati.OrdinamentoModificato = 0;
      this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[Index].Dati.OrdinamentoModificato++;
      if(this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[NewIndex].Dati.OrdinamentoModificato == undefined)
         this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[NewIndex].Dati.OrdinamentoModificato = 0;
      this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[NewIndex].Dati.OrdinamentoModificato++;
      //forse non serve

      this.$emit('onChange')
    },

    OnInputValori(Voce)
    { 
      if(!this.AlmenoUnaVoceSenzaIva)
        this.CurrentSchedaVociDocumentiEconomici.Dati.NaturaPagamento = -1
      if(Voce.Dati.Importo_Ivato == 'T')
        this.OnInputIvato(Voce)
      if(Voce.Dati.Importo_Ivato == 'F')
        this.OnInputImponibile(Voce)
    
    },

    OnInputImponibile(Voce)
    {
      Voce.Dati.Importo_Ivato = 'F'
      Voce.Dati.Ivato         = TZEconomicFunct.GetIvatoFromImponibile(Voce.Dati.Imponibile, Voce.Dati.Iva)
      Voce.Dati.Ivato         = parseFloat((Voce.Dati.Ivato).toFixed(2))
      Voce.CalcoloTotale()
      this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
    },

    OnInputIvato(Voce)
    {
      Voce.Dati.Importo_Ivato = 'T'
      Voce.Dati.Imponibile    = TZEconomicFunct.GetImponibileFromIvato(Voce.Dati.Ivato, Voce.Dati.Iva)
      Voce.Dati.Imponibile    = parseFloat((Voce.Dati.Imponibile).toFixed(2))
      Voce.CalcoloTotale()
      this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
    },

    OnChangeCostoBollo()
    {
      this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
    },

    OnInputIvaSuggerita()
    {
      if(this.CurrentSchedaVociDocumentiEconomici.Dati.IvaSuggerita != 0)
      {
        this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
        this.CurrentSchedaVociDocumentiEconomici.Dati.NaturaPagamento = -1
      }
      else
      {
        this.CurrentSchedaVociDocumentiEconomici.CaricaIvaSuggerita()
        this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
        this.CurrentSchedaVociDocumentiEconomici.Dati.NaturaPagamento = -1
      }
    },

    OnClickAssegnaSconto()
    {
      if(this.AssociaSconto)
      {
        this.AssegnaSconto = 0
        this.AssociaSconto = false
      }
      else this.AssociaSconto = true
    },

    OnChangeNaturaPagamento()
    { 
      this.$emit('onChange')
    },

    OnClickConfermaSconto()
    {
      if(this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.length > 0)
      {
        for(let i = 0; i < this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici.length; i++)
        {
          this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[i].Dati.Sconto = this.AssegnaSconto
          this.CurrentSchedaVociDocumentiEconomici.LsVociDocumentiEconomici[i].CalcoloTotale()
        }
        this.CurrentSchedaVociDocumentiEconomici.CalcoloTotaliFattura()
        this.AssegnaSconto = 0
      }
      this.AssociaSconto = false
    },
    
    OnInputDescrizioneVoce(Voce)
    {
       this.CurrentSchedaVociDocumentiEconomici.SetAltezzaTextarea(Voce)
       this.$emit('onChange')
    },

    OnEmitVociFattura()
    {
       this.$emit('onChange')
    },

  },

  beforeMount() 
  {
  },

}
</script>