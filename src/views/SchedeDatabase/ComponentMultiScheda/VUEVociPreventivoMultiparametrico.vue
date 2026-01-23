

<template>
<div>
  <div class="row wrapper" style="padding-right:3px;padding-left:3px;padding-bottom:0px;padding-top:0px">
    <div class="col-sm-12 m-b-xs">
      <button :disabled="Disabilitato" v-if="!IsDiventatoConfermaDOrdine" @click="OnClickAggiungiSezionePreventivo" class="btn btn-sm btn-success" style="float: left;margin-right:10px">
            Aggiungi sezione
      </button>
      <div style="float: left;">
        <input v-if="!IsDiventatoConfermaDOrdine" type="number" min="1" style="width: 60px; height: 30px;margin-left:5px;margin-right:10px" v-model="NrSezioniDaAggiungere">
      </div>

      <label class="ZMLabel" style="float:right;height:20px;font-size:15px;width:7%;margin-top:5px">{{ CurrentSchedaVociPreventivoMultiparametrico.Dati.IvaSuggerita }}</label>
      <text style="font-weight:bold;float:right;margin-top:5px;margin-right:8px">IVA suggerita </text>
      <div style="float: right;width:4%">&nbsp;</div>

      <input type="number" 
             class="form-control" 
             style="width:7%;float:right"
             v-model="CurrentSchedaVociPreventivoMultiparametrico.Dati.RitenutaAcconto"
             :readonly="IsDiventatoConfermaDOrdine || Disabilitato"
             @input="CurrentSchedaVociPreventivoMultiparametrico.CalcoloTotaliSeEsisteUnaSolaSezione(), OnEmitVociFattura()">
      <text style="font-weight: bold;float:right;margin-top:5px;margin-right:8px">Ritenuta d'acconto [%]</text>

      <slot name="UpSection">
      </slot>
      <!-- <button v-if="!IsDiventatoConfermaDOrdine && !NascondiPulsantiVoci" class="btn btn-sm btn-info" @click="OnClickAssegnaSconto" style="float:right;" :disabled="Disabilitato">Ass. sconto</button> -->
    </div>
  </div>


  <VUEModal v-if="PopupLsProdotti" :Titolo="'Lista Prodotti '" :Altezza="'500px'" :Larghezza="'1200px'"
            @onClickChiudiModal="OnClickAnnullaProdotti">
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


  <VUEModal v-if="PopupNaturaPagamento.Visibile" :Titolo="'Natura Pagamento'" :Altezza="'500px'" :Larghezza="'1200px'"
            @onClickChiudiModal="PopupNaturaPagamento.Visibile ">
            <!-- = false -->
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
                  <th style="width:7%;position: sticky; top: 0;"></th>
                  <th style="width:93%;position: sticky; top: 0;">Descrizione</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="Voce in ListaVociPreventiviPredefinite" :key="Voce.CHIAVE">
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    <input type="checkbox" style="height: 20px;" class="form-control" v-model="Voce.Presente">
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Voce.DESCRIZIONE }}
                  </td>
                </tr>
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

  <div v-if="CurrentSchedaVociPreventivoMultiparametrico.LsSezioniPreventivoMultiparametrico.length != 0" style="padding:3px">
    <div v-for="(Sezione, index) in GetSezioniSenzaEliminati(CurrentSchedaVociPreventivoMultiparametrico.LsSezioniPreventivoMultiparametrico)" :key="Sezione.IndexKeyComponent">

      <div class="ZMSeparatoreScheda" style="text-align:left;padding:13px">       
        <text style="font-weight: bold;margin-bottom:-15px;margin-left: 20px;font-size:15px">Sezione {{index + 1}}</text>
        <button v-if="!IsDiventatoConfermaDOrdine" :disabled="Disabilitato" @click="OnClickEliminaSezione(Sezione)" class="btn btn-sm btn-danger" style="height:28px;float:right;margin-right:8px" :title="'Elimina sezione ' + (index + 1)">
          <i class="fa fa-trash text"></i>
        </button>
        <button v-if="!IsDiventatoConfermaDOrdine" :disabled="Disabilitato" @click="OnClickAggiungiSoluzione(Sezione.Dati.LsSoluzioni, Sezione.Dati.Chiave)" class="btn btn-sm btn-success" style="height:28px;float:right;margin-right:8px">
          <i class="fa fa-plus text"></i>
        </button>
        
      </div>
      <div v-for="(Soluzione,index) in GetSoluzioniSenzaEliminati(Sezione.Dati.LsSoluzioni)" :key="Soluzione.IndexKeyComponent" style="border-right: #68b6be 4px solid;border-left: #68b6be 4px solid;">
        <aside class="bg-light lter b-l" id="email-list">
          <section class="vbox">
            <header class="bg-light dk header clearfix">
             
              <div class="btn-group" style="margin-top:16px;float:left">
                <span class="label label-sm bg-success text-uc" style="font-size:13px">Soluzione {{index + 1}}</span>
              </div>
              <div style="float: left;width:1%">&nbsp;</div>

              <button v-if="!IsDiventatoConfermaDOrdine" :disabled="Disabilitato" @click="OnClickEliminaSoluzione(Soluzione)" class="btn btn-sm btn-danger" style="height:28px;float:right;margin-right:8px" :title="'Elimina soluzione ' + (index + 1)">
                <i class="fa fa-trash text"></i>
              </button>

              <div style="float: left;width:1%">&nbsp;</div>

              <button v-if="!IsDiventatoConfermaDOrdine && !NascondiPulsantiVoci" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickListaProdotti(Soluzione)" :disabled="Disabilitato">Aggiungi prodotto</button>
              <button v-if="!IsDiventatoConfermaDOrdine && !NascondiPulsantiVoci" class="btn btn-sm btn-info" style="float: left;margin-right:10px" @click="OnClickAggiungiVoci(Soluzione)" :disabled="Disabilitato">Aggiungi voci</button>

              <div style="float: right;margin-top:10px">
                <input v-if="!IsDiventatoConfermaDOrdine" type="number" min="1" style="width: 60px; height: 30px;margin-left:5px;margin-right:10px" v-model="Soluzione.NrVociDaAggiungere">
              </div>
              <button v-if="!IsDiventatoConfermaDOrdine" class="btn btn-sm btn-success" style="float: right;" @click="OnClickAggiungiVoceSoluzione(Soluzione)" :disabled="Disabilitato">Aggiungi voce</button>
            </header>            
            <div class="row wrapper" style="padding-top:0px;padding-bottom:1%">
              <section class="panel panel-default" style="background-color:#f1f1f1;margin-bottom:0px; width: 98%; margin-left: 1%;">
                <div :ref="'Tabella'" class="table-responsive" style="max-height:100%;width:100%;">
                  <table class="table table-striped b-t b-light">
                    <thead>
                      <tr>
                        <th style="width:7%;position: sticky; top: 0;">Codice</th>
                        <th style="width:25%;position: sticky; top: 0;">Descrizione</th>
                        <th style="width:3%;position: sticky; top: 0;">Udm</th>
                        <th style="width:2%;position: sticky; top: 0;">Qnt.</th>
                        <th style="width:6%;position: sticky; top: 0;">Importo</th>
                        <th style="width:3%;position: sticky; top: 0;">IVA</th>
                        <th style="width:3%;position: sticky; top: 0;">Sc. [%]</th>
                        <th style="width:4%;position: sticky; top: 0;">Tot</th>
                        <th style="width:1%;position: sticky; top: 0;"></th>
                        <th style="width:1%;position: sticky; top: 0;"></th>
                        <th style="width:1%;position: sticky; top: 0;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="(Voce, index) in GetVociSenzaEliminati(Soluzione.Dati.LsVociSoluzioni)" :key="Voce.IndexKeyComponent">
                        <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                          <input :readonly="IsDiventatoConfermaDOrdine || Disabilitato" v-model="Voce.Dati.CodiceProdotto" type="text" class="form-control" @input="OnEmitVociFattura(Voce)"/>
                        </td>
                        <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                          <textarea :readonly="IsDiventatoConfermaDOrdine || Disabilitato" type="text" wrap="off" v-model="Voce.Dati.Descrizione" class="form-control" @input="OnInputDescrizioneVoce(Voce)" :style="{height : Voce.AltezzaTextArea? Voce.AltezzaTextArea : '34px'}" style="resize:none;overflow-y:hidden;"></textarea>
                          <label v-if="Voce.Dati.Descrizione.trim() == '' && Voce.Dati.Quantita != 0" class="ZMFormLabelError">Campo obbligatorio</label>
                        </td>
                        <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                          <VUEInputUdm :disabled="IsDiventatoConfermaDOrdine || Disabilitato" v-model="Voce.Dati.UnitaDiMisura" @input="OnEmitVociFattura(Voce)" class="form-control"  style="cursor:default" />
                          <label v-if="Voce.Dati.UnitaDiMisura == -1" class="ZMFormLabelError">Campo obbligatorio</label>
                        </td>
                        <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white">
                          <input :readonly="IsDiventatoConfermaDOrdine || Disabilitato" type="number" min="0" v-model="Voce.Dati.Quantita" class="form-control" step="0.01" @input="OnInputValori(Voce), OnEmitVociFattura(Voce)"/>
                          <label label v-if="Voce.Dati.Imponibile != 0 && Voce.Dati.Quantita == 0" class="ZMFormLabelError">Campo obbligatorio</label> 
                        </td>
                        <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                          <input :readonly="IsDiventatoConfermaDOrdine || Disabilitato" type="number" step="0.01" min="0" @input="OnInputImponibile(Voce), OnEmitVociFattura(Voce)" class="form-control" v-model="Voce.Dati.Imponibile" />
                        </td>
                        <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                          <input :readonly="IsDiventatoConfermaDOrdine || Disabilitato" type="number" step="0.01" min="0" @input="OnInputValori(Voce), OnEmitVociFattura(Voce)" class="form-control" v-model="Voce.Dati.Iva" />
                        </td>
                        <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                          <input :readonly="IsDiventatoConfermaDOrdine || Disabilitato" type="number" @input="OnInputValori(Voce), OnEmitVociFattura(Voce)" class="form-control" v-model="Voce.Dati.Sconto" />
                        </td>
                        <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                          <input readonly type="number" class="form-control" v-model="Voce.DatiTotale.Totale" @input="OnEmitVociFattura(Voce)" />
                        </td>
                        <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                          <a v-if="!IsDiventatoConfermaDOrdine && index != (GetVociSenzaEliminati(Soluzione.Dati.LsVociSoluzioni).length - 1) && !Disabilitato" @click="OnClickSpostaVoce(Soluzione, index, index + 1)" data-toggle="class" style="font-size:12px; cursor:pointer;margin-top:5px;margin-left:8px" title="Sposta verso il basso"><i class="fa fa-arrow-down"></i></a>
                          <a v-if="!IsDiventatoConfermaDOrdine && index != 0 && !Disabilitato" @click="OnClickSpostaVoce(Soluzione, index, index - 1)" data-toggle="class" style="font-size:12px; cursor:pointer;margin-top:5px;margin-left:8px" title="Sposta verso l'alto"><i class="fa fa-arrow-up"></i></a>
                        </td>
                        <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                          <a v-if="!IsDiventatoConfermaDOrdine && !Disabilitato" @click="OnClickEliminaVoce(Voce, Soluzione)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px" title="Elimina Voce"><i class="fa fa-trash-o"></i></a>
                        </td>
                        <td style="padding:2px;border:1px solid #ddd;border-bottom:0; background-color:white">
                          <a v-if="Voce.Dati.Iva == 0 && Voce.Dati.Quantita != 0" @click="OnClickNaturaPagamento(Voce)" data-toggle="class" style="font-size:17px;color:#fb6b5b; cursor:pointer;margin-top:5px;margin-left:8px" 
                          :title="GetDescrizioneNaturaPagamento(Voce.Dati.NaturaPagamento)"><i class="fa fa-align-justify"></i></a>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </section>
              <div>  
            </div>
           </div>
           
            <!-- <footer class="footer b-t bg-white-only">
              <form class="m-t-sm">
                <div class="input-group">
                  <input type="text" class="input-sm form-control input-s-sm" placeholder="Search">
                  <div class="input-group-btn">
                    <button class="btn btn-sm btn-default"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </form>
            </footer> -->
          </section>
        </aside>
      <div v-if="index != Sezione.Dati.LsSoluzioni.length - 1" class="ZMSeparatoreFiltri" style="height:15px"></div>
      </div>
      <div class="ZMSeparatoreFiltri" style="height:15px;background-color:#42586F">
      </div>
      <div v-if="VisibilitaSezione()" class="ZMNuovaRigaScheda">
          <br>
          <div class="col-md-4" style="float:right; width:38%; margin-right:20px">
            <div class="ZMNuovaRigaScheda">
              <a style="float:left">Imponibile </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociPreventivoMultiparametrico.SommaImponibile)  }}</label>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a style="float:left">Totale IVA </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociPreventivoMultiparametrico.SommaIva)}}</label>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a  style="float:left">Totale Ivato </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;"> {{ FormattaImporto(CurrentSchedaVociPreventivoMultiparametrico.TotaleIvato)}}</label>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a  style="float:left">Ritenuta</a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;"> {{ FormattaImporto(CurrentSchedaVociPreventivoMultiparametrico.TotaleRitenuta)}}</label>
            </div>
            <div class="ZMNuovaRigaScheda">
              <a v-if="!CurrentSchedaVociPreventivoMultiparametrico.SplitPayment" style="float:left">Netto a pagare </a>
              <a v-else style="float:left">Totale </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{FormattaImporto(CurrentSchedaVociPreventivoMultiparametrico.TotaleFattura)  }}</label>
              <label v-if="CurrentSchedaVociPreventivoMultiparametrico.TotaleFattura < 0" class="ZMFormLabelError" style="float:right; text-align: right; font-size: 12px;">Il totale dev'essere positivo</label>
            </div>
            <div class="ZMNuovaRigaScheda" v-if="CurrentSchedaVociPreventivoMultiparametrico.SplitPayment">
              <a style="float:left">Netto a pagare </a>
              <label class="ZMLabel" style="width:70%; float:right; text-align: right;">{{ FormattaImporto(CurrentSchedaVociPreventivoMultiparametrico.TotaleSplitPayment) }}</label>
            </div>
        </div>
      </div>
    <div class="ZMSeparatoreFiltri" style="height:40px"></div>
   </div>
  </div>

  


</div>
</template>

<script>
import { SystemInformation, PAGAMENTO_BOLLO, TIPO_SCONTO } from '@/SystemInformation.js'
import VUEInputUdm from '@/components/InputComponents/VUEInputUdm.vue'
import { TZEconomicFunct } from '../../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { TZDateFunct } from '../../../../../../../../../Librerie/VUE/ZDateFunct.js'
import { TSchedaGenerica } from '../../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import VUEModal from '@/components/Slots/VUEModal.vue';
import { TZFatturaElettronica, TZFattElettronicaNaturaPagamenti } from '../../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';

const DEFAULT_NATURA_PAGAMENTO = TZFattElettronicaNaturaPagamenti.natInversioneContabileSubappaltoEdile

export class TVoceSoluzionePreventivoMultiparametrico
{   
    constructor(Chiave, IdSoluzione, IdPreventivo, Descrizione, Importo, Quantita, Iva, 
                Sconto, UnitaDiMisura, CodiceProdotto = '', NaturaPagamento = DEFAULT_NATURA_PAGAMENTO, IdProdotto)
    {
        this.Dati = {}
        this.Dati.Chiave                = Chiave;
        this.Dati.Descrizione           = Descrizione;
        this.Dati.Imponibile            = Importo
        this.Dati.Quantita              = Quantita
        this.Dati.Iva                   = Iva
        this.Dati.Sconto                = Sconto
        this.Dati.IdSoluzione           = IdSoluzione
        this.Dati.IdPreventivo          = IdPreventivo
        this.Dati.UnitaDiMisura         = UnitaDiMisura
        this.Dati.CodiceProdotto        = CodiceProdotto
        this.Dati.NaturaPagamento       = NaturaPagamento;
        this.Dati.IdProdotto            = IdProdotto;

        this.DatiTotale                 = {}
        this.DatiTotale.Totale          = 0
        this.Dati.DaEliminare           = false
        this.IndexKeyComponent          = Chiave
        this.Snapshot                   = JSON.stringify(this.Dati)  
        this.CopiaDati                  = JSON.parse(this.Snapshot)


      if(Importo)
      {
         this.Dati.Imponibile  = Importo
         this.Dati.Ivato       = TZEconomicFunct.GetIvatoFromImponibile(Importo,Iva)
         this.Dati.Ivato       = parseFloat((this.Dati.Ivato).toFixed(2))
      }
      else 
      {
        this.Dati.Ivato      = 0
        this.Dati.Imponibile = 0
      }

      this.DatiTotale = {}
      this.DatiTotale.Totale       = 0

      this.CalcoloTotale()
      this.Dati.DaEliminare   = false
      this.Snapshot           = JSON.stringify(this.Dati)  
      this.CopiaDati          = JSON.parse(this.Snapshot)
      
   }

   CalcoloTotale()
   {
      this.DatiTotale.Totale       = 0

      this.DatiTotale.Totale       = this.Dati.Quantita * (this.Dati.Imponibile - (this.Dati.Imponibile * this.Dati.Sconto / 100))

      this.DatiTotale.Totale       = parseFloat((this.DatiTotale.Totale).toFixed(2))
    }


    PrepareQueryParameters(Operazioni, Ordinamento, ForzaOrdine)
    {
      var Parametri = { 
                          CHIAVE               : this.Dati.Chiave,
                          ID_SOLUZIONE         : this.Dati.IdSoluzione == -1? undefined : this.Dati.IdSoluzione,
                          ID_PREVENTIVO_MULTI  : this.Dati.IdPreventivo == -1? undefined : this.Dati.IdPreventivo,
                          DESCRIZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.Descrizione),
                          ORDINAMENTO          : Ordinamento,
                          UNITA_DI_MISURA      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.UnitaDiMisura),
                          IMPORTO              : this.Dati.Importo_Ivato == 'T' ? (TSchedaGenerica.PrepareForRecordInteger(this.Dati.Ivato * 100)) : (TSchedaGenerica.PrepareForRecordInteger(this.Dati.Imponibile * 100)),
                          QUANTITA             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Quantita * 100),
                          IVA                  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Iva * 100),
                          SCONTO               : TSchedaGenerica.PrepareForRecordInteger(this.Dati.Sconto * 100),
                          CODICE_PRODOTTO      : TSchedaGenerica.PrepareForRecordString(this.Dati.CodiceProdotto),
                          NATURA_PAGAMENTO     : TSchedaGenerica.PrepareForRecordInteger(this.Dati.NaturaPagamento),
                          ID_PRODOTTO          : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.IdProdotto),
                        }
      if(this.Dati.Chiave == -1 && !this.Dati.DaEliminare)
      {
        Operazioni.push({
                                  Query     : 'InserisciVoceSoluzione',
                                  Parametri : Parametri,
                                  ResetKeys : [4]
        })
      }
      else
      {
          if(this.Dati.Chiave != -1)
          {
            if(this.Dati.DaEliminare)
            {
              Operazioni.push({
                                        Query     : 'EliminaVoceSoluzione',
                                        Parametri : { CHIAVE : this.Dati.Chiave }
              })
            }
            else
            {
              if(this.Modificato() || ForzaOrdine)
              {
                Operazioni.push({
                                  Query     : 'ModificaVoceSoluzione',
                                  Parametri : Parametri
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

    AllDataOk()
    {
      return (this.Dati.Descrizione.trim() != '' || this.Dati.Quantita == 0) && 
              this.Dati.UnitaDiMisura != -1 && 
             ((this.Dati.Imponibile != 0 && this.Dati.Quantita !=0) || (this.Dati.Imponibile == 0 && this.Dati.Quantita == 0) || (this.Dati.Imponibile == 0 && this.Dati.Quantita != 0))
    }
}

export class TSoluzionePreventivoMultiparametrico
{   
    constructor(Chiave, IdSezione, IdPreventivo)
    {
      this.Dati                 = {}
      this.Dati.Chiave          = Chiave;
      this.Dati.IdSezione       = IdSezione;
      this.Dati.IdPreventivo    = IdPreventivo;
      this.Dati.DaEliminare     = false
      this.NrVociDaAggiungere   = 1
      this.IndexKeyComponent    = Chiave
      this.Dati.LsVociSoluzioni = []
      this.ForzaOrdineVoci      = false
    }

    SistemaVociDaEliminareInFondoLaLista()
    {
      this.Dati.LsVociSoluzioni.sort(function(a,b)
                                    {
                                      if(!a.Dati.DaEliminare && b.Dati.DaEliminare)
                                        return -1
                                      if(a.Dati.DaEliminare && !b.Dati.DaEliminare)
                                        return 1
                                      return 0
                                    })
    }

    Modificato()
    {
      return this.Snapshot != JSON.stringify(this.Dati)
    }

    AllDataOk()
    {
      if(this.Dati.LsVociSoluzioni.length == 0)
        return false
      else
      {
        for(var i = 0; i < this.Dati.LsVociSoluzioni.length; i++)
          if(!this.Dati.LsVociSoluzioni[i].Dati.DaEliminare)
            if(!this.Dati.LsVociSoluzioni[i].AllDataOk())
              return false
        return true
      }
    }

    PrepareQueryParameters(Operazioni)
    {
      var Parametri = { 
                        CHIAVE              : this.Dati.Chiave,
                        ID_SEZIONE          : this.Dati.IdSezione == -1? undefined : this.Dati.IdSezione,
                        ID_PREVENTIVO_MULTI : this.Dati.IdPreventivo == -1? undefined : this.Dati.IdPreventivo,
                      }

      if(this.Dati.Chiave == -1 && !this.Dati.DaEliminare)
      {
          Operazioni.push({
                              Query     : 'InserisciSoluzione',
                              Parametri : Parametri,
                              ResetKeys : [3]
          })
      }
      else
      {
        if(this.Dati.Chiave != -1)
        {
          if(this.Dati.DaEliminare)
          {
            if(this.Dati.LsVociSoluzioni.length != 0)
            {
              for(let i = 0; i < this.Dati.LsVociSoluzioni.length; i++)
              {
                Operazioni.push({
                                  Query     : 'EliminaVoceSoluzione',
                                  Parametri : { CHIAVE : this.Dati.LsVociSoluzioni[i].Dati.Chiave }
                                })
              }
            }
            Operazioni.push({
                              Query     : 'EliminaSoluzione',
                              Parametri : { CHIAVE : this.Dati.Chiave }
            })
          }
          else
          {
            if(this.Modificato() )
            {
              // Operazioni.push({
              //                   Query     : 'ModificaVoce',
              //                   Parametri : Parametri
              // })
            }
          }
        }
      }

      for(var i = 0; i < this.Dati.LsVociSoluzioni.length; i++)
        if(this.Dati.LsVociSoluzioni[i].Dati.DaEliminare || 
           this.Dati.LsVociSoluzioni[i].Dati.Chiave == -1 || 
           this.Dati.LsVociSoluzioni[i].Modificato() ||
           this.ForzaOrdineVoci == true)
          this.Dati.LsVociSoluzioni[i].PrepareQueryParameters(Operazioni, i + 1, this.ForzaOrdineVoci)

      return Operazioni
    }

    EliminaTutteLeVoci()
    {
      for(var i = 0; i < this.Dati.LsVociSoluzioni.length; i++)
        this.Dati.LsVociSoluzioni[i].Dati.DaEliminare = true
    }
}

export class TSezionePreventivoMultiparametrico
{   
    constructor(Chiave, IdDocumento)
    {
      this.Dati              = {}
      this.Dati.Chiave       = Chiave;
      this.Dati.IdDocumento  = IdDocumento
      this.Dati.DaEliminare  = false
      this.IndexKeyComponent = Chiave
      this.Dati.LsSoluzioni  = []
    }


    Modificato()
    {
      return this.Snapshot != JSON.stringify(this.Dati)
    }

    AllDataOk()
    {
      if(this.Dati.LsSoluzioni.length == 0)
        return false
      else
      {
        for(var i = 0; i < this.Dati.LsSoluzioni.length; i++)
          if(!this.Dati.LsSoluzioni[i].Dati.DaEliminare)
            if(!this.Dati.LsSoluzioni[i].AllDataOk())
              return false
        return true
      }
    }

    EliminaTutteLeSoluzioni()
    {
      for(var i = 0; i < this.Dati.LsSoluzioni.length; i++)
      {
        this.Dati.LsSoluzioni[i].Dati.DaEliminare = true
        this.Dati.LsSoluzioni[i].EliminaTutteLeVoci()
      }
    }

    PrepareQueryParameters(Operazioni)
    {
      var Parametri = { 
                        CHIAVE               : this.Dati.Chiave,
                        ID_PREVENTIVO_MULTI  : this.Dati.IdDocumento == -1? undefined : this.Dati.IdDocumento,
                      }

      if(this.Dati.Chiave == -1 && !this.Dati.DaEliminare)
      {
          Operazioni.push({
                              Query     : 'InserisciSezione',
                              Parametri : Parametri,
                              ResetKeys : [2]
          })
      }
      else
      {
        if(this.Dati.Chiave != -1)
        {
          if(this.Dati.DaEliminare)
          {
            if(this.Dati.LsSoluzioni.length != 0)
            {
              for(let i = 0; i < this.Dati.LsSoluzioni.length; i++)
              {
                if(this.Dati.LsSoluzioni[i].Dati.LsVociSoluzioni.length != 0)
                {
                  for(let j = 0; j < this.Dati.LsSoluzioni[i].Dati.LsVociSoluzioni.length; j++)
                  {
                    Operazioni.push({
                                      Query     : 'EliminaVoceSoluzione',
                                      Parametri : { CHIAVE : this.Dati.LsSoluzioni[i].Dati.LsVociSoluzioni[j].Dati.Chiave }
                                    })
                  }
                }
                Operazioni.push({
                                  Query     : 'EliminaSoluzione',
                                  Parametri : { CHIAVE : this.Dati.LsSoluzioni[i].Dati.Chiave }
                })
              }
            }

            Operazioni.push({
                              Query     : 'EliminaSezione',
                              Parametri : { CHIAVE      : this.Dati.Chiave }
            })
          }
          else
          {
            if(this.Modificato() )
            {
              // Operazioni.push({
              //                   Query     : 'ModificaVoce',
              //                   Parametri : Parametri
              // })
            }
          }
        }
      }

      for(var i = 0; i < this.Dati.LsSoluzioni.length; i++)
        if(this.Dati.LsSoluzioni[i].Dati.DaEliminare || this.Dati.LsSoluzioni[i].Dati.Chiave == -1 || this.Dati.LsSoluzioni[i].Modificato())
          this.Dati.LsSoluzioni[i].PrepareQueryParameters(Operazioni)

      return Operazioni
    }
}


export class TSchedaVociPreventivoMultiparametrico
{
   constructor()
   {
      this.LsSezioniPreventivoMultiparametrico = []
      this.LsVociDaEliminare          = []
      this.PreventiviCorrelati        = []
      this.TotaleFattura              = 0
      this.TotaleSplitPayment         = 0
      this.TotaleIvato                = 0
      this.Importo_Ivato              = 0
      this.SommaIva                   = 0
      this.SommaImponibile            = 0
      this.TotaleRitenuta             = 0
      this.Dati                       = {
                                          RitenutaAcconto  : SystemInformation.Configurazioni.Impostazioni.RITENUTA_ACCONTO,
                                          IvaSuggerita     : SystemInformation.Configurazioni.Impostazioni.IVA_SUGGERITA / 10,
                                          CostoBollo       : SystemInformation.Configurazioni.Impostazioni.COSTO_BOLLO_SUGGERITO / 100,
                                          PagamentoBollo   : PAGAMENTO_BOLLO.NessunBollo,
                                          ReverseCharge    : false,
                                        }
      this.SplitPayment             = false
      this.CreateSnapshot();
      this.NaturaPagamentoSuggerita = DEFAULT_NATURA_PAGAMENTO
   }
   CalcoloTotaliSeEsisteUnaSolaSezione()
  { 
   for( var i= 0; i < this.LsSezioniPreventivoMultiparametrico.length; i++)
    if(this.LsSezioniPreventivoMultiparametrico.length == 1)
     for(var j = 0; j < this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni.length; j++)
      if(this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni.length == 1)
        this.CalcoloTotaleVociSoluzione(this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni[j].Dati.LsVociSoluzioni)
  }

   CalcoloTotaleVociSoluzione(ListaVociSoluzione)
   {
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

      ListaVociSoluzione.forEach(function(AVoce)
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
      
      if(this.Dati.RitenutaAcconto != 0 && this.Dati.RitenutaAcconto != '')
        this.TotaleRitenuta = this.SommaImponibile * this.Dati.RitenutaAcconto / 100

      this.TotaleIvato     = this.SommaImponibile + this.SommaIva

      // let OldTotaleFattura = this.TotaleFattura;
      this.TotaleFattura   = this.TotaleIvato - this.TotaleRitenuta

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


   }

   InserisciUnaNuovaSezione()
   {
    let InserimentoNuovaSezione = new TSezionePreventivoMultiparametrico(-1, -1)
    this.LsSezioniPreventivoMultiparametrico.push(InserimentoNuovaSezione)

    let InserimentoNuovaSoluzione = new TSoluzionePreventivoMultiparametrico(-1, -1, -1)
    InserimentoNuovaSezione.Dati.LsSoluzioni.push(InserimentoNuovaSoluzione)
   }


   CheckPresenzaSezioneSoluzione(Descrizione, Prezzo, Quantita, Iva, Sconto)
   {
    var Self=this
      // 1. Se non esiste una sezione aggiungo una sezione
      // 2. se nella prima sezione non c'è una soluzione aggiungo una soluzione in quella sezione
      if(this.LsSezioniPreventivoMultiparametrico.length == 0)
      { 
        let InserimentoNuovaSezione = new TSezionePreventivoMultiparametrico(-1, Self.IdDocumento)
        Self.LsSezioniPreventivoMultiparametrico.push(InserimentoNuovaSezione)
      
        if(InserimentoNuovaSezione.Dati.LsSoluzioni.length == 0)
        {
          let InserimentoNuovaSoluzione = new TSoluzionePreventivoMultiparametrico(-1, Self.IdSezione, Self.IdDocumento)
          InserimentoNuovaSezione.Dati.LsSoluzioni.push(InserimentoNuovaSoluzione)
          InserimentoNuovaSoluzione.Dati.LsVociSoluzioni.push(this.AggiungiVocePreventivo(Descrizione, Prezzo, Quantita, Iva))  //poi si aggiungono le voci
        }
      }
      else
      {
          if(this.LsSezioniPreventivoMultiparametrico[0] != undefined)
          {
            if(this.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni[0] != undefined)
            {
              this.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni[0].Dati.LsVociSoluzioni.push(this.AggiungiVocePreventivo(Descrizione, Prezzo, Quantita, Iva))
            }
            else
            {
              let InserimentoNuovaSoluzione = new TSoluzionePreventivoMultiparametrico(-1, Self.IdSezione, Self.IdDocumento)
              this.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni.push(InserimentoNuovaSoluzione)
              InserimentoNuovaSoluzione.Dati.LsVociSoluzioni.push(this.AggiungiVocePreventivo(Descrizione, Prezzo, Quantita, Iva, Sconto))  
            }
            
          }
      }
   }
  

   CreateSnapshot()
   {
     this.Snapshot = JSON.stringify(this.Dati)
   }

   SetDatiCliente(IvaSuggerita, RitenutaAcconto, NaturaPagamento)
   {
      this.Dati.IvaSuggerita        = IvaSuggerita;
      this.Dati.RitenutaAcconto     = RitenutaAcconto;

      if (NaturaPagamento != undefined)
        this.NaturaPagamentoSuggerita = NaturaPagamento;
      else
        this.NaturaPagamentoSuggerita = DEFAULT_NATURA_PAGAMENTO;
   }

   SetIdDocumento(IdDocumento)
   {
     this.IdDocumento = IdDocumento
   }

   SetNaturaPagamento(NaturaPagamento)
   {
      this.NaturaPagamentoSuggerita = NaturaPagamento
   }

   SetSplitPayment(SplitPayment)
   {
     this.SplitPayment = SplitPayment
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

   Modificato()
   {
    
      for(var i = 0; i < this.LsSezioniPreventivoMultiparametrico.length; i++)
      {
        if(this.LsSezioniPreventivoMultiparametrico[i].Modificato() || this.LsSezioniPreventivoMultiparametrico[i].Dati.Chiave == -1)
          return true
        
        for(var j = 0; j < this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni.length; j++)
        {
          if(this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni[j].Modificato() || this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni[j].Dati.Chiave == -1)
            return true
          
          for(var k = 0; k < this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni[j].Dati.LsVociSoluzioni.length; k++)
            if(this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni[j].Dati.LsVociSoluzioni[k].Modificato() || this.LsSezioniPreventivoMultiparametrico[i].Dati.LsSoluzioni[j].Dati.LsVociSoluzioni[k].Dati.Chiave == -1)
              return true
        }
      }
      
      // if(this.Snapshot != JSON.stringify(this.Dati)) 
        // return true

      return false;
   }

   PrepareQueryParameters(Operazioni)
   {
      for(var j = 0; j < this.LsSezioniPreventivoMultiparametrico.length; j++)
        if(this.LsSezioniPreventivoMultiparametrico[j].Dati.DaEliminare || this.LsSezioniPreventivoMultiparametrico[j].Dati.Chiave == -1 || this.LsSezioniPreventivoMultiparametrico[j].Modificato())
          this.LsSezioniPreventivoMultiparametrico[j].PrepareQueryParameters(Operazioni, j + 1)
      
      return Operazioni
   }

   AllDataOk()
   {
     if(this.LsSezioniPreventivoMultiparametrico.length == 0)
       return false
     else
     {
       for(var i = 0; i < this.LsSezioniPreventivoMultiparametrico.length; i++)
         if(!this.LsSezioniPreventivoMultiparametrico[i].Dati.DaEliminare)
           if(!this.LsSezioniPreventivoMultiparametrico[i].AllDataOk())
             return false
       return true
     }
   }

   SetAltezzaTextarea(Voce)
   { 
      let NrRighe = 1 
      if(Voce.Dati.Descrizione != undefined)
         NrRighe = Voce.Dati.Descrizione.split("\n").length
      Voce.AltezzaTextArea = NrRighe  <= 1 ? '34px' : NrRighe * 22 + 10  + 'px'
   }

   AssignDati(LsSezioniPreventivoMultiparametrico,IvaSuggerita,RitenutaAcconto,NaturaPagamento)
   {
      if(NaturaPagamento == -1)
        this.NaturaPagamentoSuggerita    = DEFAULT_NATURA_PAGAMENTO 
      else this.NaturaPagamentoSuggerita = NaturaPagamento
   
      this.Dati.RitenutaAcconto     = RitenutaAcconto / 10;
      this.Dati.IvaSuggerita        = IvaSuggerita;
      this.CreateSnapshot();

      this.AssignDatiSezioni(LsSezioniPreventivoMultiparametrico, this.Dati.IvaSuggerita, this.Dati.RitenutaAcconto )
   }

   AggiungiVocePreventivo(Descrizione, Prezzo, Quantita, Iva, Sconto)
   {
    let InserimentoNuovaVoceSoluzione = new TVoceSoluzionePreventivoMultiparametrico(-1,
                                                                                    this.CHIAVE_SOLUZIONE, 
                                                                                    this.ID_PREVENTIVO_MULTI, 
                                                                                    Descrizione, 
                                                                                    TSchedaGenerica.PrepareForRecordInteger(Prezzo),
                                                                                    TSchedaGenerica.PrepareForRecordInteger(Quantita), 
                                                                                    TSchedaGenerica.PrepareForRecordInteger(Iva), 
                                                                                    TSchedaGenerica.PrepareForRecordInteger(Sconto), 
                                                                                    SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA, 
                                                                                    TSchedaGenerica.PrepareForRecordString(this.CODICE_PRODOTTO),
                                                                                    DEFAULT_NATURA_PAGAMENTO)
      return InserimentoNuovaVoceSoluzione;
   }

   AssignDatiSezioni(LsSezioni, IvaSuggerita, RitenutaAcconto)
   {
      this.LsSezioniPreventivoMultiparametrico = []
      this.Dati.IvaSuggerita    = IvaSuggerita
      this.Dati.RitenutaAcconto = RitenutaAcconto

      let ChiaveSezione       = null
      let ChiaveSoluzione     = null
      let ChiaveVoce          = null

      for(let i = 0; i < LsSezioni.length; i++)
      {
        if(ChiaveSezione != LsSezioni[i].CHIAVE_SEZIONE)
        {
          let InserimentoNuovaSezione = new TSezionePreventivoMultiparametrico(LsSezioni[i].CHIAVE_SEZIONE, 
                                                                               LsSezioni[i].ID_PREVENTIVO_MULTI)

          this.LsSezioniPreventivoMultiparametrico.push(InserimentoNuovaSezione)

          let InserimentoNuovaSoluzione = new TSoluzionePreventivoMultiparametrico(LsSezioni[i].CHIAVE_SOLUZIONE, 
                                                                                   LsSezioni[i].CHIAVE_SEZIONE,
                                                                                   LsSezioni[i].ID_PREVENTIVO_MULTI)

          InserimentoNuovaSezione.Dati.LsSoluzioni.push(InserimentoNuovaSoluzione)

          let InserimentoNuovaVoceSoluzione = new TVoceSoluzionePreventivoMultiparametrico(LsSezioni[i].CHIAVE_VOCE,
                                                                                           LsSezioni[i].CHIAVE_SOLUZIONE, 
                                                                                           LsSezioni[i].ID_PREVENTIVO_MULTI, 
                                                                                           TSchedaGenerica.DisponiFromString(LsSezioni[i].DESCRIZIONE), 
                                                                                           TSchedaGenerica.DisponiFromInteger(LsSezioni[i].IMPORTO) / 100,
                                                                                           TSchedaGenerica.DisponiFromInteger(LsSezioni[i].QUANTITA) / 100, 
                                                                                           TSchedaGenerica.DisponiFromInteger(LsSezioni[i].IVA) / 100,  
                                                                                           TSchedaGenerica.DisponiFromInteger(LsSezioni[i].SCONTO) / 100, 
                                                                                           TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].UNITA_DI_MISURA), 
                                                                                           TSchedaGenerica.DisponiFromString(LsSezioni[i].CODICE_PRODOTTO),
                                                                                           LsSezioni[i].NATURA_PAGAMENTO == -1? 
                                                                                            DEFAULT_NATURA_PAGAMENTO : 
                                                                                            TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].NATURA_PAGAMENTO),
                                                                                           TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].ID_PRODOTTO))

          InserimentoNuovaSoluzione.Dati.LsVociSoluzioni.push(InserimentoNuovaVoceSoluzione)
          InserimentoNuovaVoceSoluzione.CalcoloTotale()
          ChiaveSezione   = LsSezioni[i].CHIAVE_SEZIONE
          ChiaveSoluzione = LsSezioni[i].CHIAVE_SOLUZIONE
          ChiaveVoce      = LsSezioni[i].CHIAVE_VOCE
        }

        if(ChiaveSoluzione != LsSezioni[i].CHIAVE_SOLUZIONE)
        {
          for(let j = 0; j < this.LsSezioniPreventivoMultiparametrico.length; j++)
          {
            if(this.LsSezioniPreventivoMultiparametrico[j].Dati.Chiave == LsSezioni[i].CHIAVE_SEZIONE)
            {
              let InserimentoNuovaSoluzioneDaSoluzione = new TSoluzionePreventivoMultiparametrico(LsSezioni[i].CHIAVE_SOLUZIONE, 
                                                                                                  LsSezioni[i].CHIAVE_SEZIONE,
                                                                                                  LsSezioni[i].ID_PREVENTIVO_MULTI)

              this.LsSezioniPreventivoMultiparametrico[j].Dati.LsSoluzioni.push(InserimentoNuovaSoluzioneDaSoluzione)

              let InserimentoUnaNuovaVoceSoluzioneDaSoluzione = new TVoceSoluzionePreventivoMultiparametrico(LsSezioni[i].CHIAVE_VOCE,
                                                                                                            LsSezioni[i].CHIAVE_SOLUZIONE, 
                                                                                                            LsSezioni[i].ID_PREVENTIVO_MULTI, 
                                                                                                            TSchedaGenerica.DisponiFromString(LsSezioni[i].DESCRIZIONE), 
                                                                                                            TSchedaGenerica.DisponiFromInteger(LsSezioni[i].IMPORTO) / 100,
                                                                                                            TSchedaGenerica.DisponiFromInteger(LsSezioni[i].QUANTITA) / 100, 
                                                                                                            TSchedaGenerica.DisponiFromInteger(LsSezioni[i].IVA) / 100,  
                                                                                                            TSchedaGenerica.DisponiFromInteger(LsSezioni[i].SCONTO) / 100, 
                                                                                                            TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].UNITA_DI_MISURA), 
                                                                                                            TSchedaGenerica.DisponiFromString(LsSezioni[i].CODICE_PRODOTTO),
                                                                                                            LsSezioni[i].NATURA_PAGAMENTO == -1? 
                                                                                                              DEFAULT_NATURA_PAGAMENTO : 
                                                                                                              TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].NATURA_PAGAMENTO),
                                                                                                            TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].ID_PRODOTTO))

              InserimentoNuovaSoluzioneDaSoluzione.Dati.LsVociSoluzioni.push(InserimentoUnaNuovaVoceSoluzioneDaSoluzione)
              InserimentoUnaNuovaVoceSoluzioneDaSoluzione.CalcoloTotale()
              ChiaveSoluzione = LsSezioni[i].CHIAVE_SOLUZIONE 
              ChiaveVoce      = LsSezioni[i].CHIAVE_VOCE
              break;
            }
          }
        }

        if(ChiaveVoce != LsSezioni[i].CHIAVE_VOCE)
        {
          for(let j = 0; j < this.LsSezioniPreventivoMultiparametrico.length; j++)
          {
            if(this.LsSezioniPreventivoMultiparametrico[j].Dati.Chiave == LsSezioni[i].CHIAVE_SEZIONE)
            {
              for(let k = 0; k < this.LsSezioniPreventivoMultiparametrico[j].Dati.LsSoluzioni.length; k++)
              {
                if(this.LsSezioniPreventivoMultiparametrico[j].Dati.LsSoluzioni[k].Dati.Chiave == LsSezioni[i].CHIAVE_SOLUZIONE)
                {
                  let InserimentoUnaNuovaVoceSoluzione = new TVoceSoluzionePreventivoMultiparametrico(LsSezioni[i].CHIAVE_VOCE,
                                                                                                      LsSezioni[i].CHIAVE_SOLUZIONE, 
                                                                                                      LsSezioni[i].ID_PREVENTIVO_MULTI, 
                                                                                                      TSchedaGenerica.DisponiFromString(LsSezioni[i].DESCRIZIONE), 
                                                                                                      TSchedaGenerica.DisponiFromInteger(LsSezioni[i].IMPORTO) / 100,
                                                                                                      TSchedaGenerica.DisponiFromInteger(LsSezioni[i].QUANTITA) / 100, 
                                                                                                      TSchedaGenerica.DisponiFromInteger(LsSezioni[i].IVA) / 100,  
                                                                                                      TSchedaGenerica.DisponiFromInteger(LsSezioni[i].SCONTO) / 100, 
                                                                                                      TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].UNITA_DI_MISURA), 
                                                                                                      TSchedaGenerica.DisponiFromString(LsSezioni[i].CODICE_PRODOTTO),
                                                                                                      LsSezioni[i].NATURA_PAGAMENTO == -1? 
                                                                                                        DEFAULT_NATURA_PAGAMENTO : 
                                                                                                        TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].NATURA_PAGAMENTO),
                                                                                                      TSchedaGenerica.DisponiFromListIndex(LsSezioni[i].ID_PRODOTTO))

                  this.LsSezioniPreventivoMultiparametrico[j].Dati.LsSoluzioni[k].Dati.LsVociSoluzioni.push(InserimentoUnaNuovaVoceSoluzione)
                  InserimentoUnaNuovaVoceSoluzione.CalcoloTotale()
                  ChiaveVoce      = LsSezioni[i].CHIAVE_VOCE
                  break;
                }
              }
              break;
            }
          }
        }
      }
      this.CalcoloTotaliSeEsisteUnaSolaSezione();
     
   }

   AggiungiVociPredefinite(VettoreVociPredefinite)
   {
      if(this.LsSezioniPreventivoMultiparametrico[0] != null && this.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni[0] != null)
        for(let i = 0; i < VettoreVociPredefinite.length; i++)
        {
          let InserimentoNuovaVoceSoluzione = new TVoceSoluzionePreventivoMultiparametrico(-1,
                                                                                            -1, 
                                                                                            this.IdDocumento, 
                                                                                            VettoreVociPredefinite[i], 
                                                                                            0,
                                                                                            0, 
                                                                                            this.Dati.IvaSuggerita, 
                                                                                            0, 
                                                                                            SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA, 
                                                                                            '')

          this.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni[0].Dati.LsVociSoluzioni.push(InserimentoNuovaVoceSoluzione)
          InserimentoNuovaVoceSoluzione.CalcoloTotale();
        }
      this.CalcoloTotaliSeEsisteUnaSolaSezione()
   }

   ClearVociDocumentiEconomici()
   {
      this.LsSezioniPreventivoMultiparametrico = []
   }
}


export default {

  components : 
   {
      VUEInputUdm,
      VUEModal,
   },

  emits: ['onChange'],
  data()
  {
    return {
              NrSezioniDaAggiungere             : 1,
              AssegnaSconto                     : 0,
              SoluzioneAppoggioPerPopup         : null,
              PopupLsProdotti                   : false,
              PopupLsVociPreventiviPredefinite  : false,
              PopupLsPreventivi                 : false,
              PopupLsDDT                        : false,
              PopupVociDocumentiEconomici       : false,
              PopupLsFatture                    : false,
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
              VisibilitaListinoPrezziCliente           : SystemInformation.AccessRights.VisibilitaListinoPrezziCliente(),
              positions                         : {
                                                    clientX: undefined,
                                                    clientY: undefined,
                                                    movementX: 0,
                                                    movementY: 0
                                                  }
    }
  },

  props : ['SchedaVociPreventivoMultiparametrico', 'SchedaPreventivo', 'DiventatoConfermaDOrdine',
           'IdCliente', 'NascondiIvaSuggerita', 
           'NascondiPulsanti', 'ReverseCharge', 'Disabilitato'],
  
  computed :
  {
    CurrentSchedaVociPreventivoMultiparametrico: 
    {
      get()
      {
        var TmpScheda = this.SchedaVociPreventivoMultiparametrico;
        var Self = this;
        TmpScheda.OnChangeTotale = function()
        {
          Self.$emit('onChange')
        }
        return TmpScheda;
      }
    },

    IsDiventatoConfermaDOrdine: 
    {
      get()
      {
        if(this.DiventatoConfermaDOrdine == undefined)
          return false;
        return this.DiventatoConfermaDOrdine;
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
    },
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
        if(Voce.Presente)
          Voce.Presente = false;

      this.PopupLsVociPreventiviPredefinite = false;
      this.SoluzioneAppoggioPerPopup        = null
    },

    OnClickAnnullaProdotti()
    {
      for(let i = 0; i < this.ListaProdotti.length; i++)
        if(this.ListaProdotti[i].Presente)
          this.ListaProdotti[i].Presente = false

      this.PopupLsProdotti           = false
      this.SoluzioneAppoggioPerPopup = null
    },

    OnClickListaProdotti(Soluzione)
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

                                              Self.PopupLsProdotti           = true
                                              Self.SoluzioneAppoggioPerPopup = null
                                              Self.SoluzioneAppoggioPerPopup = Soluzione
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista dei prodotti con prezzo variato');
                                          },
                                          function (HTTPError, SubHTTPError, Response) 
                                          {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaProdottiVariazionePrezzo')
    },

    OnClickConfermaProdotti()
    {
      let IVA     = this.CurrentSchedaVociPreventivoMultiparametrico.Dati.IvaSuggerita
      for(let i = 0; i < this.ListaProdotti.length; i++)
      {
        if(this.ListaProdotti[i].Presente)
        {
          let IVARiga =   IVA < TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].IVA) / 10? 
                          IVA : TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].IVA) / 10

          let InserimentoNuovaVoceSoluzione = new TVoceSoluzionePreventivoMultiparametrico(-1,
                                                                                          this.SoluzioneAppoggioPerPopup.Dati.Chiave, 
                                                                                          this.SoluzioneAppoggioPerPopup.Dati.IdPreventivo, 
                                                                                          this.ListaProdotti[i].DESCRIZIONE, 
                                                                                          this.ListaProdotti[i].PREZZO_SCONTATO == null? 
                                                                                            this.ListaProdotti[i].PREZZO_IMPONIBILE / 100 
                                                                                            : this.ListaProdotti[i].PREZZO_SCONTATO,
                                                                                          TSchedaGenerica.DisponiFromInteger(this.ListaProdotti[i].QUANTITA_SUGGERITA), 
                                                                                          IVARiga,

                                                                                          this.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE == null? 
                                                                                          0.00 
                                                                                          : this.ListaProdotti[i].PREZZO_SCONTO_PERCENTUALE,

                                                                                          this.ListaProdotti[i].UNITA_DI_MISURA == null? 
                                                                                            SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA : 
                                                                                            this.ListaProdotti[i].UNITA_DI_MISURA, 
                                                                                          this.ListaProdotti[i].CODICE,
                                                                                          -1,
                                                                                         this.ListaProdotti[i].CHIAVE)

          this.SoluzioneAppoggioPerPopup.Dati.LsVociSoluzioni.push(InserimentoNuovaVoceSoluzione)
          InserimentoNuovaVoceSoluzione.CalcoloTotale()                                                             
          this.ListaProdotti[i].Presente = false
          this.$emit('onChange')
        }
      }
      
      setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50)
   
      this.CurrentSchedaVociPreventivoMultiparametrico.CalcoloTotaliSeEsisteUnaSolaSezione();
      this.$emit('onChange')
      this.PopupLsProdotti           = false
      this.SoluzioneAppoggioPerPopup = null
    },

    OnClickConfermaVociPreventiviPredefinite()
    {
      for(let i = 0; i < this.ListaVociPreventiviPredefinite.length; i++)
      {
        if(this.ListaVociPreventiviPredefinite[i].Presente)
        {
          let InserimentoNuovaVoceSoluzione = new TVoceSoluzionePreventivoMultiparametrico(-1,
                                                                                          this.SoluzioneAppoggioPerPopup.Dati.Chiave, 
                                                                                          this.SoluzioneAppoggioPerPopup.Dati.IdPreventivo, 
                                                                                          this.ListaVociPreventiviPredefinite[i].DESCRIZIONE, 
                                                                                          0,
                                                                                          TSchedaGenerica.DisponiFromInteger(1), 
                                                                                          0, 
                                                                                          0.00,
                                                                                          SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA, 
                                                                                          '')

          this.SoluzioneAppoggioPerPopup.Dati.LsVociSoluzioni.push(InserimentoNuovaVoceSoluzione)
          InserimentoNuovaVoceSoluzione.CalcoloTotale()
          this.ListaVociPreventiviPredefinite[i].Presente = false
        }
      }
      setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50) 

      this.CurrentSchedaVociPreventivoMultiparametrico.CalcoloTotaliSeEsisteUnaSolaSezione();
      this.$emit('onChange')
      this.PopupLsVociPreventiviPredefinite = false;
      this.SoluzioneAppoggioPerPopup        = null;
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
        if(this.LsNaturaPagamento[i].Id == NaturaPagamento)
          return this.LsNaturaPagamento[i].Descrizione
      return "???"
    },

    OnClickAggiungiVoci(Soluzione)
    {
      this.ListaVociPreventiviPredefinite   = SystemInformation.Configurazioni.VociPreventiviPredefinite;
      this.PopupLsVociPreventiviPredefinite = true;
      this.$emit('onChange')
      this.SoluzioneAppoggioPerPopup        = null
      this.SoluzioneAppoggioPerPopup        = Soluzione
      this.$emit('onChange')
    },

    OnClickConfermaNaturaPagamento()
    {
      this.PopupNaturaPagamento.VoceInModifica.Dati.NaturaPagamento = this.PopupNaturaPagamento.NaturaSelezionata
      this.PopupNaturaPagamento.Visibile = false
      if(this.PopupNaturaPagamento.NaturaSelezionata != this.VecchiaNaturaPagamento)
        this.$emit('onChange')
    }, 

    OnClickAnnullaNaturaPagamento()
    {
      this.PopupNaturaPagamento.Visibile = false
    },

    OnClickSpostaVoce(Soluzione, Index, NewIndex)
    {
      Soluzione.ForzaOrdineVoci = true
      
      let LsVociSoluzioni = Soluzione.Dati.LsVociSoluzioni
      let Tmp = LsVociSoluzioni[Index];

      LsVociSoluzioni[Index]    = LsVociSoluzioni[NewIndex];
      LsVociSoluzioni[NewIndex] = Tmp;
      
      LsVociSoluzioni[Index].IndexKeyComponent++
      LsVociSoluzioni[NewIndex].IndexKeyComponent++
      
      this.$emit('onChange')
    },

    OnInputValori(Voce)
    { 
      this.OnInputImponibile(Voce)
    },

    OnInputImponibile(Voce)
    {
      Voce.Dati.Importo_Ivato = 'F'
      Voce.Dati.Ivato         = TZEconomicFunct.GetIvatoFromImponibile(Voce.Dati.Imponibile, Voce.Dati.Iva)
      Voce.Dati.Ivato         = parseFloat((Voce.Dati.Ivato).toFixed(2))
      Voce.CalcoloTotale()
      this.CurrentSchedaVociPreventivoMultiparametrico.CalcoloTotaliSeEsisteUnaSolaSezione()
    },

    OnInputIvaSuggerita()
    {
      if(this.CurrentSchedaVociPreventivoMultiparametrico.Dati.IvaSuggerita != 0)
      {
        this.CurrentSchedaVociPreventivoMultiparametrico.Dati.NaturaPagamento = -1
      }
      else
      {
        this.CurrentSchedaVociPreventivoMultiparametrico.CaricaIvaSuggerita()
        this.CurrentSchedaVociPreventivoMultiparametrico.Dati.NaturaPagamento = -1
      }
    },

    OnChangeNaturaPagamento()
    { 
      this.$emit('onChange')
    },

    OnInputDescrizioneVoce(Voce)
    {
       this.CurrentSchedaVociPreventivoMultiparametrico.SetAltezzaTextarea(Voce)
       this.$emit('onChange')
    },

    OnEmitVociFattura()
    {
       this.$emit('onChange')
    },

    OnClickAggiungiSezionePreventivo()
    {
      for(let i = 0; i < this.NrSezioniDaAggiungere; i++)
      {
        let InserimentoNuovaSezione = new TSezionePreventivoMultiparametrico(-1, this.CurrentSchedaVociPreventivoMultiparametrico.IdDocumento)

        this.CurrentSchedaVociPreventivoMultiparametrico.LsSezioniPreventivoMultiparametrico.push(InserimentoNuovaSezione)
      }
      this.$emit('onChange')
    },

    OnClickAggiungiSoluzione(Vettore, IdSezione)
    {
      let InserimentoNuovaSoluzione = new TSoluzionePreventivoMultiparametrico(-1, IdSezione, this.CurrentSchedaVociPreventivoMultiparametrico.IdDocumento)

      Vettore.push(InserimentoNuovaSoluzione)
      
      this.$emit('onChange')
    },

    OnClickAggiungiVoceSoluzione(Soluzione)
    {
      for(let i = 0; i < Soluzione.NrVociDaAggiungere; i++)
      {
        let InserimentoNuovaVoceSoluzione = new TVoceSoluzionePreventivoMultiparametrico(-1,
                                                                                          Soluzione.Dati.Chiave, 
                                                                                          this.CurrentSchedaVociPreventivoMultiparametrico.IdDocumento, 
                                                                                          '', 
                                                                                          0,
                                                                                          0, 
                                                                                          this.CurrentSchedaVociPreventivoMultiparametrico.Dati.IvaSuggerita, 
                                                                                          0, 
                                                                                          SystemInformation.Configurazioni.Impostazioni.UNITA_DI_MISURA_SUGGERITA, 
                                                                                          '')

        Soluzione.Dati.LsVociSoluzioni.push(InserimentoNuovaVoceSoluzione)

        setTimeout(() => {this.$refs.Tabella.scrollTop += 99999999}, 50)        
      }
      this.CurrentSchedaVociPreventivoMultiparametrico.CalcoloTotaliSeEsisteUnaSolaSezione()
      Soluzione.SistemaVociDaEliminareInFondoLaLista()
      this.$emit('onChange')
    },

    OnClickEliminaSezione(Sezione)
    {
      Sezione.Dati.DaEliminare = true
      Sezione.EliminaTutteLeSoluzioni()
      Sezione.IndexKeyComponent++
      this.$emit('onChange')
    },

    OnClickEliminaSoluzione(Soluzione)
    {
      Soluzione.Dati.DaEliminare = true
      Soluzione.IndexKeyComponent++
      Soluzione.EliminaTutteLeVoci()
      Soluzione.SistemaVociDaEliminareInFondoLaLista()
      this.$emit('onChange')
    },

    OnClickEliminaVoce(Voce, Soluzione)
    {
      Voce.Dati.DaEliminare = true
      Voce.IndexKeyComponent++
      this.$emit('onChange')
      Soluzione.SistemaVociDaEliminareInFondoLaLista()
      this.CurrentSchedaVociPreventivoMultiparametrico.CalcoloTotaliSeEsisteUnaSolaSezione()  
    },

    GetVociSenzaEliminati(LsVociSoluzioni)
    {
      let Result = []

      for(let i = 0; i < LsVociSoluzioni.length; i++)
        if(!LsVociSoluzioni[i].Dati.DaEliminare)  
          Result.push(LsVociSoluzioni[i])
      
      return Result
    },

    GetSoluzioniSenzaEliminati(LsSoluzioni)
    {
      let Result = []

      for(let i = 0; i < LsSoluzioni.length; i++)
        if(!LsSoluzioni[i].Dati.DaEliminare)  
          Result.push(LsSoluzioni[i])
      
      return Result
    },

    GetSezioniSenzaEliminati(LsSezioni)
    {
      let Result = []

      for(let i = 0; i < LsSezioni.length; i++)
        if(!LsSezioni[i].Dati.DaEliminare)  
          Result.push(LsSezioni[i])
      
      return Result
    },

    VisibilitaSezione()
    {
      if(this.CurrentSchedaVociPreventivoMultiparametrico.LsSezioniPreventivoMultiparametrico.length <= 1 && this.CurrentSchedaVociPreventivoMultiparametrico.LsSezioniPreventivoMultiparametrico[0].Dati.LsSoluzioni.length <=1 )
       return true
      else return false

    },

  },

  beforeMount() 
  {
  },

}
</script>