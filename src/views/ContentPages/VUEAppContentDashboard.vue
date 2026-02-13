<template>
  <VUEModalCaricamentoDati v-if="PopupAttesaCalcolo" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>

  <VUEModal v-if="PopupProdottiExcel" :Titolo="'Stampa excel prodotti'" :Altezza="'170px'" :Larghezza="'520px'"
            @onClickChiudiModal="PopupProdottiExcel = false">
      <template v-slot:Body>
        <div class="form-row">
          <div class="col-md-12" style="margin-bottom:10px;">
            <!-- SCELTA RADIO: TUTTI -->
            <div style="margin-bottom:8px;">
              <label style="font-weight:bold; font-size:14px;">
                <input
                  type="radio"
                  v-model="PopupFiltroMagazzinoTipo"
                  value="Tutti"
                  style="transform: scale(1.3); margin-right:6px;"
                  @click="OnClickPopupProdottiExcelTutti"
                >
                Tutti i prodotti
              </label>
            </div>

            <!-- SCELTA RADIO: SINGOLO -->
            <div>
              <label style="font-weight:bold; font-size:14px;">
                <input
                  type="radio"
                  v-model="PopupFiltroMagazzinoTipo"
                  value="Singolo"
                  style="transform: scale(1.3); margin-right:6px;"
                >
                Singolo magazzino
              </label>
            </div>
          </div>

          <!-- SELECT VISIBILE SOLO SE "Singolo" -->
          <div class="col-md-12">
            <div style="margin-top:10px; width:60%;">
              <label style="font-weight:bold;">Seleziona magazzino</label>
              <select
                v-model="PopupMagazzinoSelezionato"
                class="form-control"
                style="width:100%;"
                :disabled="PopupFiltroMagazzinoTipo != 'Singolo'"
              >
                <option :value="-1">-</option>
                <option
                  v-for="Magazzino in LsMagazzini"
                  :key="Magazzino.CHIAVE"
                  :value="Magazzino.CHIAVE"
                >
                  {{ Magazzino.DESCRIZIONE }}
                </option>
              </select>
            </div>
          </div>
        </div>


      </template>
      <template v-slot:Footer>
        <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="PopupProdottiExcel = false" data-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:20%" @click="EsportaProdottiExcel" data-dismiss="modal">Conferma</button>
      </template>
  </VUEModal>

  <VUEModal v-if="PopupConfermaStampaRibaFatture" :Titolo="'Opzioni stampa ri.ba.'" :Altezza="'100px'" :Larghezza="'400px'"
          @onClickChiudiModal="PopupConfermaStampaRibaFatture = false">
    <template v-slot:Body>
      <div class="form-row">
      <div class="col-md-12">
        <div style="float:left;width:100%">
          <div style="clear:both">&nbsp;</div>
          <input type="checkbox" class="form-control" style="margin-left:10px;text-align:center;width:5%;float:left" v-model="MostraSoloRateNonPagate">
          <a style="margin-left:5%;width:80%;float:left;margin-top:7px;font-size:16px">Mostra solo rate non pagate</a>
        </div>
      </div>
      </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:30%" @click="PopupConfermaStampaRibaFatture = false" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="OnClickStampaRibaFatture" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal>

  <VUEModal v-if="PopupScaricoFileZip"
    Titolo="Scarica file ZIP con fatt., fatt. pass. e note di credito"
    @onClickConfermaModal="OnClickScaricoFileZip()"
    @onClickChiudiModal="OnClickAnnullaScaricoFileZip()">
    <template v-slot:Body>
      <label>Scegli un intervallo</label>
      <div style="display: flex; justify-content: center; gap: 10px">
        
        <div style="display: flex; align-items: center; gap: 5px">
          <label style="margin-bottom: 0">Dal</label>
          <input type="date" class="form-control" v-model="ScaricoFileZipDal"/>
        </div>

        <div style="display: flex; align-items: center; gap: 5px">
          <label style="margin-bottom: 0">Al</label>
          <input type="date" class="form-control" v-model="ScaricoFileZipAl"/>
        </div>
      </div>
    </template>
  </VUEModal>

  <VUEConfirm
    :Popup="PopupAnnullamento"
    :Richiesta="'Annullare le modifiche effettuate?'"
    @onClickConfermaPopup="ConfermaAnnullamento()"
    @onClickChiudiPopup="AnnullaAnnullamento()"
  />
  
  <VUEConfirm :Popup="PopupAnnullamentoAvvisoFatture" :Richiesta="'Sei sicuro di voler eliminare l\'avviso?'" @onClickConfermaPopup="ConfermaEliminazioneAvvisoFatturePassive" @onClickChiudiPopup="AnnullaAnnullamento">
  </VUEConfirm>


  <VUEModal v-if="PopupCreazioneDDTEntrante.Visualizza" :Titolo=" 'Creazione DDT Entrante' " :Altezza="'550px'" :Larghezza="'1250px'"
            @onClickChiudiModal="PopupCreazioneDDTEntrante.Visualizza ">
            <!-- = false -->
    <template v-slot:Body>
      <div class="col-md-12" style="padding-left:15px;padding-right:15px;padding-top:1px;padding-bottom:1px"> 
        <VUESchedaDocumentoDiTrasportoEntrante v-if="PopupCreazioneDDTEntrante.Visualizza" :SchedaDocumentoDiTrasportoEntrante="DDTEntrantePopupInCreazione"/>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:15%" @click="PopupCreazioneDDTEntrante.Visualizza = false" data-dismiss="modal">Annulla</button>
      <a v-if="DDTEntrantePopupInCreazione.CanRecord()" class="btn btn-s-md btn-success" style="margin-right:10px" @click="OnClickConfermaDDTPopup()">Conferma</a>
    </template>
  </VUEModal>

  <VUEModal v-if="PopupVisualizzaScheda" :Titolo="'Visualizzazione scheda'" :Altezza="'550px'" :Larghezza="'1250px'"
            @onClickChiudiModal="PopupVisualizzaScheda = false">
    <template v-slot:Body>
      <div class="form-row">
        <div class="col-md-12">

          <VUESchedaFornitore v-if="SchedaSelezionataPopup.TipoComunicazione == TipiComunicazioni.FornitoriSenzaCodice" 
                              :SchedaFornitore="SchedaSelezionataPopup"/>
        </div>
      </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:15%" @click="OnClickAnnullaSchedaSelezionataPopup" data-dismiss="modal">Annulla</button>
      <button v-if="SchedaSelezionataPopup.CanRecord()" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:15%" @click="OnClickConfermaSchedaSelezionataPopup" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal>

  <VUEModal v-if="PopupCreazioneFattura.Visualizza" :Titolo=" 'Creazione Fattura' " :Altezza="'550px'" :Larghezza="'1250px'"
            @onClickChiudiModal="PopupCreazioneFattura.Visualizza ">
            <!-- = false -->
    <template v-slot:Body>
      <div class="col-md-12" style="padding-left:15px;padding-right:15px;padding-top:1px;padding-bottom:1px"> 
          <button style="float:left;margin-top:10px;font-weight: bold;font-size:20px;width:30%" @click="PopupCreazioneFattura.VisualizzaFattura = !PopupCreazioneFattura.VisualizzaFattura">
              {{ PopupCreazioneFattura.VisualizzaFattura ? "" : "MOSTRA FATTURA"}}</button>
          <VUESchedaFattura v-if="PopupCreazioneFattura.VisualizzaFattura" :SchedaFattura="FatturaPopupInCreazione"/>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:15%" @click="PopupCreazioneFattura.Visualizza = false" data-dismiss="modal">Annulla</button>
      <a v-if="FatturaPopupInCreazione.CanRecord()" class="btn btn-s-md btn-success" style="margin-right:10px" @click="OnClickConfermaFatturaPopup()">Conferma</a>
      <img src="@/assets/images/Stampa.png" v-if="!PopupCreazioneFattura.VisualizzaFattura" style="float:left;margin-top:-8px;cursor:pointer">
    </template>
  </VUEModal>

  <VUEModal v-if="PopupComunicazioni" :Titolo="'Comunicazioni importanti'" :Altezza="'500px'" :Larghezza="'1200px'"
            @onClickChiudiModal="PopupComunicazioni = false ; ControlloPresenzaComunicazioni()">
    <template v-slot:Body>
      <VUEModal v-if="PopupInformazioniAggiuntive" :Titolo="'Info aggiuntive'" :Altezza="'300px'" :Larghezza="'750px'"
                @onClickChiudiModal="OnClickChiudiPopupInformazioniAggiuntive">
        <template v-slot:Body>
        <div class="form-row">
          <br>
          <div class="col-md-12">                
            <div style="font-size:16px;white-space: pre-line;word-wrap: break-word;">{{ MessaggioPopupInformazioniAggiuntive }}</div>
          </div>
        </div> 
        </template>
        <template v-slot:Footer>
          <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="OnClickChiudiPopupInformazioniAggiuntive" data-dismiss="modal">Chiudi</button>
        </template>
      </VUEModal>

      <div class="form-row">
        <div class="col-md-12">
          <div class="row wrapper">
            <section class="panel panel-default" style="background-color:white;">
              <div class="table-responsive" style="max-height: 100%;width:100%;">
                <table class="table table-striped b-t b-light">
                  <thead>
                    <tr>
                      <th style="width:5%">&nbsp;</th>
                      <th style="width:75%">Descrizione</th>
                      <th style="width:25%">Azioni</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(Comunicazione, index) in ListaComunicazioni" :key="index">
                      
                      <!-- IMMAGINE -->
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:18px;text-align:center;vertical-align: middle;font-weight:bold">
                        <img v-if="Comunicazione.Tipo == TipiComunicazioni.AvvisoGenerico" src="@/assets/images/AlertGenerico2.png" style=""> 
                        <img v-else-if="Comunicazione.Tipo == TipiComunicazioni.FornitoriSenzaCodice" src="@/assets/images/IconeAlbero/Fornitore.png" style=""> 
                        <img v-else src="@/assets/images/AlertGenerico2.png" style=""> 
                      </td>

                      <!-- DESCRIZIONE -->
                      <td style="padding:10px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:18px;text-align:left;vertical-align: middle;">
                        {{ Comunicazione.Descrizione }}

                        <i v-if="Comunicazione.Informazioni != null && 
                                  Comunicazione.Informazioni.PopupInformazioniAggiuntive != null && 
                                  Comunicazione.Informazioni.PopupInformazioniAggiuntive != ''" 
                            title="Info" 
                            style="cursor:pointer"
                            class="fa fa-info-circle" 
                            @click="OnClickVisualizzaInformazioniAggiuntive(Comunicazione.Informazioni.PopupInformazioniAggiuntive)"></i>
                      </td>

                      <!-- AZIONI -->
                      <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:18px;text-align:center;vertical-align: middle;">
                        <button v-if="Comunicazione.Tipo == TipiComunicazioni.AvvisoGenerico" @click="EliminaAvvisoFatturePassive()" style="width:70%;float:left; margin-left: 16px;" class="btn btn-sm btn-info">Chiudi Avviso</button>

                        <!-- VISUALIZZA FORNITORE SENZA CODICE -->
                        <button v-if="Comunicazione.Tipo == TipiComunicazioni.FornitoriSenzaCodice" @click="OnClickVisualizzaScheda(Comunicazione.Informazioni, Comunicazione.Tipo)" style="width:80%" class="btn btn-sm btn-info">Controlla </button>

                        <!-- VISUALIZZA NOTE DA GESTIRE -->
                        <button v-if="Comunicazione.Tipo == TipiComunicazioni.NoteGestite" @click="OnClickVisualizzaScheda(Comunicazione.Informazioni, Comunicazione.Tipo)" style="width:80%" class="btn btn-sm btn-info">Controlla </button>
                        
                        <!-- QUANTITÁ PRODOTTO MAGAZZINO MINORE SOGLIA ALLARME -->
                        <button v-if="Comunicazione.Tipo == TipiComunicazioni.AllarmeProdotto" @click="GestioneSogliaDiAllarme(Comunicazione)"  style="width:80%" class="btn btn-sm btn-info">Cambia soglia di allarme</button>

                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>
          </div>
        </div>
      </div> 
    </template>
    <template v-slot:Footer>
      <div></div>
    </template>
  </VUEModal>
  
  <VUEModal v-if="PopupFornitori" :Titolo="'Cambia soglia del prodotto'" :Altezza="'100px'" :Larghezza="'400px'"
            @onClickChiudiModal="PopupFornitori = false">
    <template v-slot:Body>
      <!-- <select style="float:left;width:100%;" class="form-control" v-model="IdFornitoreXDDT">
        <option selected value="-1">Nessun fornitore</option>
        <option v-for="Fornitore in ListaFornitoriXProdotto" 
                  :Key="Fornitore.ChiaveFornitore"
                  :value="Fornitore.ChiaveFornitore">
                  {{ Fornitore.RagioneSocialeFornitore }} 
        </option>
      </select> -->
      <div style="width:15%;float:left">&nbsp;</div>
      <a style="font-weight: bold;width:30%;float:left;margin-top:5px">Soglia di allarme</a>
      <input type="number" step="0.1" class="form-control" style="margin-left:10px;text-align:center;width:25%;float:left" v-model="ProdottoSelezionato.Soglia">
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:30%" @click="PopupFornitori = false" data-dismiss="modal">Annulla</button>
      <a v-if="ProdottoSelezionato.Soglia != ''" class="btn btn-s-md btn-success" style="margin-right:10px;width:30%" @click="OnClickConfermaNuovaSogliaProdotto">Conferma</a>
    </template>
  </VUEModal>

    <VUEModal v-if="AnnoStampaRitenutaClienti" :Titolo="'Inserire anno per stampa ritenuta'" :Altezza="'100px'" :Larghezza="'400px'"
            @onClickChiudiModal="AnnoStampaRitenutaClienti = false">
      <template v-slot:Body>
        <div class="form-row">
        <div class="col-md-12">
        <div style="float:left;width:100%">
          <div style="float:left;width:60%;">
            <label style="font-weight: bold;">Ritenute</label>
            <select style="float:left;" class="form-control" v-model="FiltroStampaRitenute">
              <option value="Qualsiasi">Qualsiasi</option>
              <option value="Pagate">Pagate</option>           
              <option value="NonPagate">Non certificate</option>           
            </select>
          </div>
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:39%">
            <label style="font-weight: bold;">Anno</label>
            <input type="number" min="1900" max="2100" step="1" class="form-control" style="text-align:center" v-model="AnnoStampaRitenuta">
          </div> 
        </div>
        </div>
        </div> 
      </template>
      <template v-slot:Footer>
        <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:30%" @click="AnnoStampaRitenutaClienti = false" data-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="ConfermaAnnoStampaRitenuteClienti" data-dismiss="modal">Conferma</button>
      </template>
    </VUEModal>

    <VUEModal v-if="DataSaldoClienti" :Titolo="'Inserire data per saldo clienti'" :Altezza="'150px'" :Larghezza="'520px'"
              @onClickChiudiModal="DataSaldoClienti = false">
        <template v-slot:Body>
          <div class="form-row">
            <div class="col-md-12">
              <div style="float:left;width:49%">
                <label style="font-weight: bold;">Al </label>
                <input type="date" class="form-control" style="width:100%" v-model="SaldoClientiAl">
              </div>
            </div>
            <div style="clear:both">&nbsp;</div>
            <div class="col-md-12">
              <div style="float:left;">
                <input  type ="radio" v-model="OrdinamentoSaldoClienti" value="C" style="transform: scale(1.5)">
              </div>
                <div style="float:left; margin-left: 7px; width:40%">
                  <text style="font-weight: bold; font-size:14px">Ordina per codice</text>
              </div>
              <div style="float:left;">
                <input  type ="radio" v-model="OrdinamentoSaldoClienti" value="A" style="transform: scale(1.5)">
              </div>
              <div style="float:left; margin-left: 7px; width:50%">
                  <text style="font-weight: bold; font-size:14px">Ordina per ragione sociale</text>
              </div>
            </div>
          </div> 
        </template>
        <template v-slot:Footer>
          <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="DataSaldoClienti = false" data-dismiss="modal">Annulla</button>
          <button v-if="SaldoClientiAl != ''" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:20%" @click="ConfermaDataSaldoClienti" data-dismiss="modal">Conferma</button>
        </template>
    </VUEModal>

    <VUEModal v-if="DataSaldoFornitori" :Titolo="'Inserire data per saldo fornitori'" :Altezza="'150px'" :Larghezza="'520px'"
              @onClickChiudiModal="DataSaldoFornitori = false">
        <template v-slot:Body>
          <div class="form-row">
            <div class="col-md-12">
              <div style="float:left;width:49%">
                <label style="font-weight: bold;">Al </label>
                <input type="date" class="form-control" style="width:100%" v-model="SaldoFornitoriAl">
              </div>
            </div>
            <div style="clear:both">&nbsp;</div>
            <div class="col-md-12">
              <div style="float:left;">
                <input  type ="radio" v-model="OrdinamentoSaldoFornitori" value="C" style="transform: scale(1.5)">
              </div>
                <div style="float:left; margin-left: 7px; width:40%">
                  <text style="font-weight: bold; font-size:14px">Ordina per codice</text>
              </div>
              <div style="float:left;">
                <input  type ="radio" v-model="OrdinamentoSaldoFornitori" value="A" style="transform: scale(1.5)">
              </div>
              <div style="float:left; margin-left: 7px; width:50%">
                  <text style="font-weight: bold; font-size:14px">Ordina per ragione sociale</text>
              </div>
            </div>
          </div> 
        </template>
        <template v-slot:Footer>
          <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="DataSaldoFornitori = false" data-dismiss="modal">Annulla</button>
          <button v-if="SaldoFornitoriAl != ''" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:20%" @click="ConfermaDataSaldoFornitori" data-dismiss="modal">Conferma</button>
        </template>
    </VUEModal>
    
  <div @keydown="KeyDownF2($event)">
    <div class="breadcrumb no-border no-radius b-b b-light pull-in" 
        style="height:65px;"
        v-if="!DatiInModifica">
      <div style="float:left;width:5%;padding-top: 6px;display: flex; justify-content: center">  
        <a class="pull-left btn btn-sm btn-dark btn-icon" style="margin-right:5px; width:33.6px; height:33.6px; display:flex; align-items:center; justify-content:center" v-if="!AlberoNascosto" @click="AlberoNascosto = true"><i class="fa fa-chevron-left"></i></a>
        <a class="pull-left btn btn-sm btn-dark btn-icon" style="margin-right:5px; width:33.6px; height:33.6px; display:flex; align-items:center; justify-content:center" v-else @click="AlberoNascosto = false"><i class="fa fa-chevron-right" ></i></a>
      </div>
      <div style="float:left;width:53%;">        
        <div class="col-md-12" style="padding-left:2px;padding-top: 6px;">
          <div style="float:left;padding-right: 5px;">
                <div class="btn-group open" >
                  <button type="button" 
                          class="btn btn-default" 
                          :class="{'data-toggle' : !MenuFilterVisible}" 
                          aria-expanded="true" 
                          style="width:200px;float:left;text-align: left;font-size:14px;z-index:9999"
                          @click="OnClickMainFilter()">
                      {{CurrentFilter.GetDescrizione()}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <i class="fa" 
                        :class="{ 'fa-sort-up' : MenuFilterVisible, 'fa-sort-down' : !MenuFilterVisible }" 
                        style="box-sizing: border-box;float:right"></i>
                  </button>
                  <ul v-if="MenuFilterVisible" class="dropdown-menu" style="width:100%">
                    <li v-for="(AMenu,index) in MenuFilter" :Key="index">
                      <a v-if="AMenu.Caption != ''" @click="OnNextMenuFilter(AMenu,MenuFilter)">
                          {{AMenu.Caption}}
                          <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                      </a>
                      <div v-else class="ZMSeparatoreMenu"></div>
                    </li>
                    <li>
                      <div class="ZMSeparatoreMenu"></div>
                    </li>
                    <li>
                      <a @click="MenuFilterVisible = false">Chiudi</a>
                    </li>
                  </ul>
                </div>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>

          <div style="float:left;padding-right: 7px;">
                <div class="btn-group open" >
                  <button type="button" 
                          class="btn btn-default" 
                          :class="{'data-toggle' : !MenuFilterRicercheVisible}" 
                          aria-expanded="true" 
                          style="width:200px;float:left;text-align: left;font-size:14px;z-index:9999"
                          @click="OnClickRicercheFilter()">
                      Ricerche &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <i class="fa" 
                        :class="{ 'fa-sort-up' : MenuFilterRicercheVisible, 'fa-sort-down' : !MenuFilterRicercheVisible }" 
                        style="box-sizing: border-box;float:right"></i>
                  </button>
                  <ul v-if="MenuFilterRicercheVisible" class="dropdown-menu" style="width:100%">
                    <li v-for="(AMenu,index) in MenuFilterRicerche" :Key="index">
                      <a v-if="AMenu.Caption != ''" @click="OnNextMenuFilterRicerche(AMenu)">
                          {{AMenu.Caption}}
                          <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                      </a>
                      <div v-else class="ZMSeparatoreMenu"></div>
                    </li>
                    <li>
                      <div class="ZMSeparatoreMenu"></div>
                    </li>
                    <li>
                      <a @click="MenuFilterRicercheVisible = false">Chiudi</a>
                    </li>
                  </ul>
                </div>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>

          <div style="float:left;padding-right: 7px;">
            <div class="btn-group open" style="float:left">
              <button type="button" 
                      class="btn btn-s-md btn-default" 
                      :class="{'data-toggle' : this.MenuNuovo.length != 0}" 
                      aria-expanded="true" 
                      style="width:100px;z-index: 9999"
                      @click="OnClickNuovo()">
                Nuovo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <i class="fa" 
                    :class="{ 'fa-sort-up' : MenuNuovo.length != 0, 'fa-sort-down' : MenuNuovo.length == 0 }">
                    </i>
              </button>
              <ul v-if="MenuNuovo.length != 0" class="dropdown-menu" style="z-index: 9999;">
                <li v-for="(AMenu,index) in MenuNuovo" :Key="index">
                  <a v-if="AMenu.Caption != ''" @click="OnNextMenuNuovo(AMenu)">
                    {{AMenu.Caption}}
                    <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                  </a>
                  <div v-else class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <div class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <a @click="MenuNuovo = []">Chiudi</a>
                </li>
              </ul>
            </div>
          </div>
          <div v-if="PresenzaComunicazioni" style="float:left;width:7%;display: flex; justify-content: center">  
            <a class=" btn-icon" 
                style="cursor:pointer;color:red;font-size:25px;margin-right:5px; width:33.6px; height:33.6px; display:flex; align-items:center; justify-content:center"
                @click="OnClickApriPopupComunicazioni">
              <i class="fa fa-bell-o"></i>
            </a>
          </div>
          <div v-if="DeveloperMode && !PresenzaComunicazioni" style="float:left;width:15%;display: flex; justify-content: center">  
            <button style="float:right;margin-right:2px;width:15%;min-width: 125px" @click="ControlloPresenzaComunicazioni()" class="btn btn-info">Down. comunic.</button>
          </div>

        </div>
      </div>
    
      <div style="width: 40%; display: flex; justify-content: flex-end; align-items: center; gap: 15px; float: right">
        
        <div style="max-width: 500px; display: flex; justify-content: flex-end; flex-wrap: wrap">
          <!-- <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoClienteRapido">Cliente</button> -->
          <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoFornitoreRapido">Fornitore</button>
          <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoProdottoRapido">Prodotto</button>
          <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoDDTEntranteRapido">DDT entrante</button>
          <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoDDTRapido">DDT</button>
          <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoDocScaricoProdCompostiRapido">Doc prodotti</button>
          <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoMovimentoMagazzinoRapido">Mov magazzino</button>
          <!-- <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoPreventivoMultiRapido">Preventivo</button> -->
          <!-- <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovoMovimentoRapido">Movimento</button> -->
          <!-- <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovaFatturaRapido">Fattura</button> -->
          <!-- <button class="btn btn-s-md btn-default btn-rounded btn-xs" style="width: 10%" @click="OnClickNuovaFatturaDaBancoRapido">Fatt. da banco</button> -->
        </div>


      </div>
    </div>
  </div>

     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Clienti'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
       <div class="col-md-10" style="padding:0px">
         <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
          <label>Cod. cliente</label>
         </div>
         <div style="float:left;width:11%">
          <input type="text" class="form-control" v-model="FilterCliente.CodiceCliente" />
         </div> 
          <!-- Filtro Ragione sociale-->
         <div style="float:left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
          <label>Cliente</label>
         </div>
         <div style="float:left;width:11%">
          <input type="text" class="form-control" v-model="FilterCliente.Cliente" />
         </div> 
         <!-- Filtro Partita IVA-->
         <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
          <label>Partita IVA</label>
         </div>
         <div style="float:left;width:11%;">
          <input type="text" class="form-control" v-model="FilterCliente.PartitaIva" />
         </div> 
         <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
          <label>Cod. fiscale</label>
         </div>
         <div style="float:left;width:11%">
          <input type="text" class="form-control" v-model="FilterCliente.CodiceFiscale" />
         </div> 
         <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
          <label style="margin-bottom: 0px;">Indirizzo</label>
         </div>
         <div style="float:left;width:11%;">
          <input type="text" class="form-control" v-model="FilterCliente.IndirizzoFatturazione" />
         </div>
       </div>

       <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

          <div class="btn-group open" style="margin-right:5px">
            <img v-if="ExcelRiassuntoClienti" src="@/assets/images/MenuExcel.png" style="margin-top:-8px;cursor:pointer;" @click="OnClickStampaXML()">
              <ul v-if="DropdownMenuXML.length != 0" class="dropdown-menu" style="width:200%">
                <li v-for="(AMenu,index) in DropdownMenuXML" :Key="index">
                  <a v-if="AMenu.Caption != ''" @click="OnNextMenuXML(AMenu)">
                      {{AMenu.Caption}}
                      <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                  </a>
                  <div v-else class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <div class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <a @click="DropdownMenuXML = []">Chiudi</a>
                </li>
              </ul>
          </div>
         
         <div class="btn-group open" style="margin-right:5px">
           <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,false,false,false,true,false,false)">
             <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="width:200%">
               <li v-for="(AMenu,index) in MenuStampa" :Key="index">
                 <a v-if="AMenu.Caption != ''" @click="OnNextMenuStampa(AMenu)">
                     {{AMenu.Caption}}
                     <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                 </a>
                 <div v-else class="ZMSeparatoreMenu"></div>
               </li>
               <li>
                 <div class="ZMSeparatoreMenu"></div>
               </li>
               <li>
                 <a @click="MenuStampa = []">Chiudi</a>
               </li>
             </ul>
         </div>
 
         <button style="margin-right:5px;width:5%;min-width: 100px" @click="FiltraDati" class="btn btn-info">[F2] Cerca</button>
 
        </div>

       <div class="col-md-10" style="padding:0px">  
         <div style="float:left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
          <label>Stato fatture</label>
         </div>
         <div style="float:left;width:11%;">
          <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="FilterCliente.StatoFatture">
            <option value="Qualsiasi">Qualsiasi</option>
            <option value="InRitardo">In ritardo</option>           
          </select>
         </div>
 
         <!-- Filtro ritenute cliente-->
         <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
           <label style="margin-bottom: 0px">Ritenute</label>
         </div>
         <div style="float:left;width:11%;">
          <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="FilterCliente.Ritenute">
            <option value="Qualsiasi">Qualsiasi</option>
            <option value="Pagate">Pagate</option>           
            <option value="NonPagate">Non certificate</option>           
          </select>
         </div>
         <div style="float:left;width:5%;margin-left:1px" v-if="FilterCliente.Ritenute != 'Qualsiasi'">
            <input type="number" min="1900" max="2099" step="1" class="form-control" v-model="FilterCliente.AnnoRitenuta"/>
         </div> 

         <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px;">Stato cliente</label>
         </div>
         <div style="float:left;width:11%;">
          <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="FilterCliente.StatoAttivita">
            <option value="Tutti">Tutti</option>
            <option value="Attivi">Attivi</option>           
            <option value="NonAttivi">Non attivi</option>           
          </select>
         </div> 
     

         <div style="float: left;font-size:14px;width:8%;padding-top: 5px;text-align: right; padding-right: 10px;">
          <label style="margin-bottom: 0px;">Ind. filiale</label>
         </div>
         <div style="float:left;width:11%;">
          <input type="text" class="form-control" v-model="FilterCliente.IndirizzoFiliale" />
         </div>
      </div>


       <div class="ZMSeparatoreFiltri" >&nbsp;</div>
       <div class="col-md-10" style="padding:0px;">
         <div style="float:left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px; ">
            <label style="float: left;margin-bottom: 0px">Regione</label>
          </div>
          <div style="float:left;width:11%;">
            <VUEInputRegioni v-model="FilterCliente.Regione" emptyElement="true"/>
          </div>
       
          <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px">Provincia</label>
          </div>
          <div style="float:left;width:11%;">
            <VUEInputProvince v-model="FilterCliente.Provincia" emptyElement="true"/>
          </div>

         <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px">Zona filiale</label>
          </div>
          <div style="float:left;width:11%;">
            <VUEInputZone v-model="FilterCliente.ZonaFiliale" emptyElement="true"/>
          </div>
       </div>

       <div class="ZMSeparatoreFiltri">&nbsp;</div>

     </div>

     <VUESchedaFiliali v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Filiali'"/>

     <VUESchedaMagazzini v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Magazzini'"/>

     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Fatture'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
       <!-- {{EditingDatiNuovi}} -->
      <div class="col-md-10" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:5%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px;">Tipo fatt.</label>
        </div>
        <div style="float:left;width:10.3%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterFattura.TipologiaFattura">
            <option value="Tutte">Tutte</option>                       
            <option value="Fatture">Fatture</option>   
            <option value="FattureDaBanco">Fatture da banco</option>
          </select>
        </div>
        
        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Dal nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterFattura.DalNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:1%;text-align: right; padding-right: 6x;">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number" min="0" class="form-control" v-model="FilterFattura.Anno1"/>
        </div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:3%;text-align: right; padding-right: 6px;">
          <label>Al nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterFattura.AlNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;;width:1%">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number" min="0" class="form-control" v-model="FilterFattura.Anno2" />
        </div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Dal</label>
        </div>
        <div style="float:left;width: 9%;">
          <input type="date" class="form-control" v-model="FilterFattura.DallaData"/>
        </div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width: 9%;">
          <input type="date"  class="form-control" v-model="FilterFattura.AllaData"/>
        </div> 

        <div style="float:left;font-size:14px;margin: 5px 5px 0 15px; text-align: right;">
          <label>Conformità</label>
        </div>
        <div style="float:left;width:8%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterFattura.Conformita">
            <option value="Tutte">-</option>           
            <option value="NonConforme">Non conforme</option>
            <option value="Conforme">Conforme</option>           
          </select>
        </div>
      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">
        
        <button style="margin-right: 5px; min-width: 80px" v-if="!EspandiRicercheFattura" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheFattura">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-down text" style="margin-right: 5px"></i>
            <span class="text">Espandi</span>
          </div>
        </button>
    
        <button style="margin-right:5px; min-width: 80px" v-if="EspandiRicercheFattura" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheFattura">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-up text" style="margin-right: 5px" />
            <span class="text">Riduci</span>
          </div>
        </button>

        <div class="btn-group open" style="margin-right:10px">
          <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(true,false,false,false,false,false,false)">
          <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="width:200%">
              <li v-for="(AMenu,index) in MenuStampa" :Key="index">
                <a v-if="AMenu.Caption != ''" @click="OnNextMenuStampa(AMenu)">
                    {{AMenu.Caption}}
                    <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                </a>
                <div v-else class="ZMSeparatoreMenu"></div>
              </li>
              <li>
                <div class="ZMSeparatoreMenu"></div>
              </li>
              <li>
                <a @click="MenuStampa = []">Chiudi</a>
              </li>
            </ul>
        </div>
        
        <button class="btn btn-s-md btn-info" 
                style="margin-right:5px;width:5%;min-width: 100px"
                @click="FiltraDati">[F2] Cerca
        </button>

      </div>

      <!-- <div v-if="EspandiRicercheFattura" class="ZMSeparatoreFiltri">&nbsp;</div> -->

      <div class="col-md-12" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:4%;text-align: right; padding-right: 15px;">
          <label>Cliente</label>
        </div>
        <div style="float:left;width:27%;">
          <VUEInputClienti v-model="FilterFattura.Cliente" 
                          :ShowImage="true" 
                          @onUpdate="newValue => FilterFattura.Cliente = newValue"/>
        </div>
        
        <div style="float:left;font-size:14px;padding-top: 5px;text-align: right; padding-right:6px;padding-left:34px">
            <label style="margin-bottom: 0px;">Inv. SDI</label>
        </div>
        <div style="float:left;width:11%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterFattura.InviateSDI">
            <option value="Tutte">Tutte</option>           
            <option value="NonInviate">Non inviate</option>
            <option value="Inviate">Inviate</option>           
          </select>
        </div>

        <div style="float:left;font-size:14px;padding-top: 5px;text-align: right; padding-right: 10px;padding-left:83px;">
            <label style="margin-bottom: 0px;">Stato</label>
        </div>
        <div style="float:left;width:7%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterFattura.Stato" @change="OnChangeStatoFilterFatture">
            <option value="Qualsiasi">Qualsiasi</option>                      
            <option value="Incassata">Incassata</option>
            <option value="NonIncassata">Non incassata</option>           
          </select>
        </div>
        
        <div style="float:left;width:1%;" v-if="FilterFattura.Stato != 'Qualsiasi'">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;text-align: right; padding-right: 15px;padding-left:5px;" v-if="FilterFattura.Stato != 'Qualsiasi'">
          <label>Dal</label>
        </div>
        <div style="float:left;width:7%;" v-if="FilterFattura.Stato != 'Qualsiasi'">
          <input type="date" class="form-control" v-model="FilterFattura.IncassataDal"/>
        </div>

        <div style="float:left;width:1%;" v-if="FilterFattura.Stato != 'Qualsiasi'">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;text-align: right; padding-right: 8px;padding-left:3%;" v-if="FilterFattura.Stato != 'Qualsiasi'">
          <label>Al</label>
        </div>
        <div style="float:left;width:8%;" v-if="FilterFattura.Stato != 'Qualsiasi'">
          <input type="date"  class="form-control" v-model="FilterFattura.IncassataAl"/>
        </div>


      <!-- Filtro fatture -->
         <div class="ZMSchedeMancanti" style="float: right"> 
            <label> N° Fatture: {{ TreeView.Children.length }}</label>
         </div> 

      </div>

      
      <div v-if="EspandiRicercheFattura" class="ZMSeparatoreFiltri">&nbsp;</div>
      
      <div class="col-md-12" v-if="EspandiRicercheFattura" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:4%;text-align: right; padding-right: 15px;">
          <label style="white-space: nowrap;">Cod. fisc.</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterFattura.CodiceFiscale"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top:5px;text-align: right; padding-right: 15px;padding-left:1%;">
          <label>Part. IVA</label>
        </div>
        <div style="float:left;width:9%;">
          <input type="text" class="form-control" v-model="FilterFattura.PartitaIva"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:6%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px;">Intestatario</label>
        </div>
        <div style="float:left;width:11%;">
          <input type="text" class="form-control" v-model="FilterFattura.Intestatario"/>
        </div>
        
        <div style="float:left;width:1%"> &nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:6%;text-align: right; padding-right: 10px;">
            <label style="margin-bottom: 0px;">Numeraz.</label>
        </div>
        <div style="float:left;width:7%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterFattura.Numerazione" @change="AnnullaFatturaDaAssegnare">
            <option value="Qualsiasi">Qualsiasi</option>           
            <option value="NumerazioneDaAssegnare">Da assegnare</option>
            <option value="NumerazioneAssegnata">Assegnata</option>           
          </select>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top:5px;text-align: right; padding-right: 5px;padding-left:1%">
          <label>Mese gen.</label>
        </div>
        <div style="float:left;width:7%;">
          <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="FilterFattura.MeseGenerazione">
            <option selected value="-1">-</option>
            <option v-for="(Mese,index) in LsMesi" 
                          :Key="index"
                          :value="index"
                          :Id="index">
            {{Mese}}
          </option>
          </select>
        </div>
        <div style="float:left;font-size:14px;margin-top:5px;margin-left:5px">
          <label>&nbsp;/ &nbsp;</label>
        </div>
        <div style="float:left;width:5%;">
          <input type="number" min="1900" max="2099" step="1" class="form-control" v-model="FilterFattura.AnnoGenerazione"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:1%;text-align: right; padding-right: 5px;">
            <label style="margin-bottom: 0px;">&nbsp;</label>
        </div>
        <div style="float:left;width:8%;">
          <div style="float: left;margin-left:10px;padding-top: 5px;margin-left:3%">
            <input type="checkbox" v-model="FilterFattura.AvvisiDaFatturare" @click="FatturaDaAssegnare"/>
          </div>
          <div style="float: left;;padding-top: 5px;margin-left:6px;margin-right: 10px">
            <label style="margin-bottom: 0px;margin-top: 0px;font-size:14px;">Da fatturare</label>
          </div>
        </div>
        

      </div>

      <div class="col-md-12" v-if="EspandiRicercheFattura" style="padding:0px">
        <div style="float:left;font-size:14px;margin-top:5px;width:4%;text-align: right; padding-right: 15px;">
          <label style="white-space: nowrap;">Avv. Fatt.</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterFattura.Chiave"/>
        </div>

        <div style="float:left;width:12%;">
          <div style="float: right;;padding-top: 5px;margin-left:6px;margin-right: 15px">
            <label style="margin-bottom: 0px;margin-top: 0px;font-size:14px;">Fatture con riba</label>
          </div>
          <div style="float: right;padding-top: 5px;margin-right:10px">
            <input type="checkbox" v-model="FilterFattura.FattureConRiba">
          </div>
        </div>
        <div style="float:left;width:12%;">
          <div style="float: right;;padding-top: 5px;margin-left:6px;margin-right: 15px">
            <label style="margin-bottom: 0px;margin-top: 0px;font-size:14px;">Fatture di acconto</label>
          </div>
          <div style="float: right;padding-top: 5px;margin-right:10px">
            <input type="checkbox" v-model="FilterFattura.FattureDiAcconto">
          </div>
        </div>
      </div>  


      <div class="ZMSeparatoreFiltri">&nbsp;</div>

     </div>

     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Autofatture'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
       <!-- {{EditingDatiNuovi}} -->
      <div class="col-md-10" style="padding:0px">
        <div style="float:left;font-size:14px;padding-top: 5px;width:6%;text-align: right; padding-right: 10px;">
            <label style="margin-bottom: 0px;">Fornitore</label>
        </div>
        <div style="float:left;width:15%;">
          <VUEInputFornitore v-model="FilterAutofattura.Fornitore" 
                            @onUpdate="newValue => FilterAutofattura.Fornitore = newValue"/>
        </div>
        <div style="float:left;width:2.3%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Dal nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterAutofattura.DalNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:1%;text-align: right; padding-right: 6x;">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number" min="0" class="form-control" v-model="FilterAutofattura.Anno1"/>
        </div>

        <div style="float:left;width:5.6%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:3%;text-align: right; padding-right: 6px;">
          <label>Al nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterAutofattura.AlNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;;width:1%">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number" min="0" class="form-control" v-model="FilterAutofattura.Anno2" />
        </div>

        <div style="float:left;width:5.6%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Dal</label>
        </div>
        <div style="float:left;width: 9%;">
          <input type="date" class="form-control" v-model="FilterAutofattura.DallaData"/>
        </div>

        <div style="float:left;width:5.6%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width: 9%;">
          <input type="date"  class="form-control" v-model="FilterAutofattura.AllaData"/>
        </div>
      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

        <button class="btn btn-s-md btn-info" 
                style="margin-right:5px;width:5%;min-width: 100px"
                @click="FiltraDati">[F2] Cerca
        </button>

      </div>

      <!-- <div v-if="EspandiRicercheFattura" class="ZMSeparatoreFiltri">&nbsp;</div> -->

      <div class="col-md-12" style="padding:0px">
        <div style="float:left;font-size:14px;margin-top:5px;width:5%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px;">Inv. SDI</label>
        </div>
        <div style="float:left;width:12.6%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterAutofattura.InviateSDI">
            <option value="Tutte">Tutte</option>           
            <option value="NonInviate">Non inviate</option>
            <option value="Inviate">Inviate</option>           
          </select>
        </div>
        
        <div style="float:left;width:2%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top:5px;width:4%;text-align: right; padding-right: 15px;">
          <label style="white-space: nowrap;">Cod. fisc.</label>
        </div>
        <div style="float:left;width:12.7%;">
          <input type="text" class="form-control" v-model="FilterAutofattura.CodiceFiscale"/>
        </div>

        <div style="float:left;width:2%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top:5px;text-align: right; padding-right: 15px;padding-left:1%;">
          <label>Part. IVA</label>
        </div>
        <div style="float:left;width:12.7%;">
          <input type="text" class="form-control" v-model="FilterAutofattura.PartitaIva"/>
        </div>

        <div style="float:left;width:2%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:5%;text-align: right; padding-right: 10px;">
            <label style="margin-bottom: 0px;">Intestatario</label>
        </div>
        <div style="float:left;width:12.6%;">
          <input type="text" class="form-control" v-model="FilterAutofattura.Intestatario"/>
        </div>

      <!-- Filtro autofatture -->
         <div class="ZMSchedeMancanti" style="float: right"> 
            <label> N° Autofatture: {{ TreeView.Children.length }}</label>
         </div> 
      </div>
      <!-- <div v-if="EspandiRicercheFattura" class="ZMSeparatoreFiltri">&nbsp;</div> -->
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
     </div>

    <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Note di credito'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
      <div class="col-md-10" style="padding:0px">

        <div style="float:left;font-size:14px;padding-top:5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Cliente</label>
        </div>
        <div style="float:left;width:10%;">
          <VUEInputClienti @onUpdate="newValue => FilterNota.Cliente = newValue" v-model="FilterNota.Cliente" :ShowImage="true"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:7%;text-align: right; padding-right: 15px;">
          <label>Dal nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterNota.DalNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:1%;text-align: right; padding-right: 6x;">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:5%;">
          <input type="number" min="0" class="form-control" v-model="FilterNota.Anno1"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:7%;text-align: right; padding-right: 6px;">
          <label>Al nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterNota.AlNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;;width:1%">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:5%;">
          <input type="number" min="0" class="form-control" v-model="FilterNota.Anno2" />
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterNota.DallaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterNota.AllaData"/>
        </div>

      </div>
      
       <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

          <button style="margin-right: 5px; min-width: 80px" v-if="!EspandiRicercheNota" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheNota">
            <div style="display: flex; justify-content: flex-start">
              <i class="fa fa-sort-down text" style="margin-right: 5px"></i>
              <span class="text">Espandi</span>
            </div>
          </button>
    
          <button style="margin-right: 5px; min-width: 80px" v-if="EspandiRicercheNota" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheNota">
            <div style="display: flex; justify-content: flex-start">
              <i class="fa fa-sort-up text" style="margin-right: 5px" />
              <span class="text">Riduci</span>
            </div>
          </button>

          <div class="btn-group open" style="margin-right:5px">
            <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,false,false, true,false,false, false)">
            <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="width:200%">
                <li v-for="(AMenu,index) in MenuStampa" :Key="index">
                  <a v-if="AMenu.Caption != ''" @click="OnNextMenuStampa(AMenu)">
                      {{AMenu.Caption}}
                      <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                  </a>
                  <div v-else class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <div class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <a @click="MenuStampa = []">Chiudi</a>
                </li>
              </ul>
          </div>

          <button class="btn btn-s-md btn-info" 
                  style="margin-right:5px;width:5%;min-width: 100px"
                  @click="FiltraDati">[F2] Cerca
          </button>

        </div>

      <div v-if="EspandiRicercheNota" class="ZMSeparatoreFiltri">&nbsp;</div>
      
        <!-- Filtro note di credito -->
         <div class="ZMSchedeMancanti" style="float: right">
            <label> N° Note di Credito: {{ TreeView.Children.length }}</label>
         </div> 

      <div class="col-md-10" v-if="EspandiRicercheNota" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:5%;text-align: right; padding-right: 6px;">
          <label>Part. IVA</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterNota.PartitaIva"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top:5px;width:7%;text-align: right; padding-right: 6px;">
          <label>Intestatario</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterNota.Intestatario"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:7%;text-align: right; padding-right: 6px;">
            <label style="margin-bottom: 0px;">Numerazione </label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterNota.Numerazione">
            <option value="NumerazioneDaAssegnare">Da assegnare</option>
            <option value="NumerazioneAssegnata">Assegnata</option>           
            <option value="Qualsiasi">Qualsiasi</option>           
          </select>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:5%;text-align: right; padding-right: 6px;">
            <label style="margin-bottom: 0px;">Pag. nota</label>
        </div>
        
        <div style="float:left;width:10%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterNota.Pagamento">
            <option value="Pagata">Pagata</option>
            <option value="DaPagare">Da pagare</option>           
            <option value="Qualsiasi">Qualsiasi</option>           
          </select>
        </div>
        <!-- <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float: left;margin-left:10px;padding-top: 5px;margin-left:10%" v-if="EspandiRicercheNota">
          <input type="checkbox" name="" value="RicevutaBancaria" v-model="FilterNota.RicevutaBancaria">
        </div>
        <div style="float: left;;padding-top: 5px;margin-left:6px;margin-right: 10px" v-if="EspandiRicercheNota">
          <label style="margin-bottom: 0px;margin-top: 0px;font-size:14px;">Pagamento per ricevuta bancaria</label>
        </div>
        <div style="float: left;margin-left:10px;padding-top: 5px;margin-left:10%" v-if="EspandiRicercheNota">
          <input :disabled="!FilterNota.RicevutaBancaria" type="checkbox" name="" value="RicevutaNonPresentata" v-model="FilterNota.RicevutaNonPresentata">
        </div>
        <div style="float: left;;padding-top: 5px;margin-left:6px;margin-right: 10px" v-if="EspandiRicercheNota">
          <label style="margin-bottom: 0px;margin-top: 0px;font-size:14px;">Ricevuta non presentata</label>
        </div> -->
      </div>
        
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
   </div>

   <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Conferme'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">

      <div class="col-md-10" style="padding:0px">

        <div style="float:left;font-size:14px;padding-top:5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Cliente</label>
        </div>
        <div style="float:left;width:10%;">
          <VUEInputClienti v-model="FilterConferma.Cliente" @onUpdate="newValue => FilterConferma.Cliente = newValue" :ShowImage="true"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:7%;text-align: right; padding-right: 15px;">
          <label>Dal nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterConferma.DalNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:1%;text-align: right; padding-right: 6x;">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:5%;">
          <input type="number" min="0" class="form-control" v-model="FilterConferma.Anno1"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:7%;text-align: right; padding-right: 6px;">
          <label>Al nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterConferma.AlNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;;width:1%">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:5%;">
          <input type="number" min="0" class="form-control" v-model="FilterConferma.Anno2" />
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterConferma.DallaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterConferma.AllaData"/>
        </div>
        
      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

          <button style="margin-right: 5px; min-width: 80px" v-if="!EspandiRicercheConferme" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheConferme">
            <div style="display: flex; justify-content: flex-start">
              <i class="fa fa-sort-down text" style="margin-right: 5px"></i>
              <span class="text">Espandi</span>
            </div>
          </button>
    
          <button style="margin-right: 5px; min-width: 80px" v-if="EspandiRicercheConferme" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheConferme">
            <div style="display: flex; justify-content: flex-start">
              <i class="fa fa-sort-up text" style="margin-right: 5px" />
              <span class="text">Riduci</span>
            </div>
          </button>

          <div class="btn-group open" style="margin-right:5px">
            <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,true,false, false,false,false, false)">
            <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="width:200%">
              <li v-for="(AMenu,index) in MenuStampa" :Key="index">
                <a v-if="AMenu.Caption != ''" @click="OnNextMenuStampa(AMenu)">
                    {{AMenu.Caption}}
                    <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                </a>
                <div v-else class="ZMSeparatoreMenu"></div>
              </li>
              <li>
                <div class="ZMSeparatoreMenu"></div>
              </li>
              <li>
                <a @click="MenuStampa = []">Chiudi</a>
              </li>
            </ul>
          </div>

          <button class="btn btn-s-md btn-info" 
                  style="margin-right:5px;width:5%;min-width: 100px"
                  @click="FiltraDati">[F2] Cerca
          </button>

        </div>

      <div v-if="EspandiRicercheConferme" class="ZMSeparatoreFiltri">&nbsp;</div>

         <!-- Filtro preventivi -->
         <div class="ZMSchedeMancanti" style="float: right">
            <label> N° Conferme: {{ TreeView.Children.length }}</label>
         </div> 
      
      <div class="col-md-10" v-if="EspandiRicercheConferme" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:5%;text-align: right; padding-right: 6px;">
          <label>Part. IVA</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterConferma.PartitaIva"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top:5px;width:7%;text-align: right; padding-right: 6px;">
          <label>Intestatario</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterConferma.Intestatario"/>
        </div>


        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:7%;text-align: right; padding-right: 6px;">
            <label style="margin-bottom: 0px;">Conf. d'ord.</label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterConferma.StatoConfermaDOrdine">
            <option value="Tutte">-</option>
            <option value="Gestite">Gestite</option>
            <option value="NonGestite">Non gestite</option>
          </select>
        </div>

      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
   </div>

   <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Preventivi'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">

      <div class="col-md-10" style="padding:0px">

        <div style="float:left;font-size:14px;padding-top:5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Cliente</label>
        </div>
        <div style="float:left;width:10%;">
          <VUEInputClienti v-model="FilterPreventivo.Cliente" @onUpdate="newValue => FilterPreventivo.Cliente = newValue" :ShowImage="true"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:7%;text-align: right; padding-right: 15px;">
          <label>Dal nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterPreventivo.DalNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:1%;text-align: right; padding-right: 6x;">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:5%;">
          <input type="number" min="0" class="form-control" v-model="FilterPreventivo.Anno1"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:7%;text-align: right; padding-right: 6px;">
          <label>Al nr.</label>
        </div>
        <div style="float:left;width:4%;">
          <input type="number"  min="0" class="form-control" v-model="FilterPreventivo.AlNr"/>
        </div>
        <div style="float:left;font-size:14px;padding-top: 5px;;width:1%">
          <label>&nbsp;/&nbsp;&nbsp;</label>
        </div>
        <div style="float:left;width:5%;">
          <input type="number" min="0" class="form-control" v-model="FilterPreventivo.Anno2" />
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterPreventivo.DallaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterPreventivo.AllaData"/>
        </div>

      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

        <button style="margin-right: 5px; min-width: 80px" v-if="!EspandiRicerchePreventivo" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicerchePreventivo">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-down text" style="margin-right: 5px"></i>
            <span class="text">Espandi</span>
          </div>
        </button>
  
        <button style="margin-right: 5px; min-width: 80px" v-if="EspandiRicerchePreventivo" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicerchePreventivo">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-up text" style="margin-right: 5px" />
            <span class="text">Riduci</span>
          </div>
        </button>

        <div class="btn-group open" style="margin-right:5px">
          <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,false,true, false,false,false, false)">
          <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="width:200%">
              <li v-for="(AMenu,index) in MenuStampa" :Key="index">
                <a v-if="AMenu.Caption != ''" @click="OnNextMenuStampa(AMenu)">
                    {{AMenu.Caption}}
                    <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                </a>
                <div v-else class="ZMSeparatoreMenu"></div>
              </li>
              <li>
                <div class="ZMSeparatoreMenu"></div>
              </li>
              <li>
                <a @click="MenuStampa = []">Chiudi</a>
              </li>
            </ul>
        </div>

        <button class="btn btn-s-md btn-info" 
                style="margin-right:5px;width:5%;min-width: 100px"
                @click="FiltraDati">[F2] Cerca
        </button>

      </div>

      <div v-if="EspandiRicerchePreventivo" class="ZMSeparatoreFiltri">&nbsp;</div>

         <!-- Filtro preventivi -->
         <div class="ZMSchedeMancanti" style="float: right">
            <label> N° Preventivi: {{ TreeView.Children.length }}</label>
         </div> 
      
      <div class="col-md-10" v-if="EspandiRicerchePreventivo" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:5%;text-align: right; padding-right: 6px;">
          <label>Part. IVA</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterPreventivo.PartitaIva"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top:5px;width:7%;text-align: right; padding-right: 6px;">
          <label>Intestatario</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterPreventivo.Intestatario"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;padding-top: 5px;width:7%;text-align: right; padding-right: 6px;">
            <label style="margin-bottom: 0px;">Stato prev.</label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterPreventivo.StatoPreventivo">
            <option value="-1">-</option>
            <option value="DaGestire">Da gestire</option>
            <option value="Confermati">Confermati</option>
            <option value="Rifiutati">Rifiutati</option>
          </select>
        </div>

      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
   </div>


     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Fatture passive'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
      <div class="col-md-10" style="padding:0px">

        <div style="float:left;font-size:14px;padding-top: 5px;width:5%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px;">Fornitore</label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:right;width:100%" class="form-control" v-model="FilterFatturaPassiva.Fornitore">
            <option selected value="-1">-</option>
            <option v-for="Fornitore in LsFornitori" 
                          :Key="Fornitore.CHIAVE"
                          :value="Fornitore.CHIAVE">
              {{Fornitore.RAGIONE_SOCIALE}}
            </option>
          </select>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterFatturaPassiva.DallaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterFatturaPassiva.AllaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Scad. dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterFatturaPassiva.ScadenzaDal"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterFatturaPassiva.ScadenzaAl"/>
        </div>

      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

        <div style="margin-right:5px">
          <img title="Carica un Xml" src="@/assets/images/UploadXml.png" style="margin-top:-8px; cursor:pointer" @click="onClickCaricaXml"/>
          <input ref="SelezioneFile" type="file" accept=".xml" style="display: none;" @change="onChangeCaricaXml"/>
        </div>

        <button style="margin-right:5px; min-width: 80px" v-if="!EspandiRicercheFatturaPassiva" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheFatturaPassiva">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-down text" style="margin-right: 5px"></i>
            <span class="text">Espandi</span>
          </div>
        </button>
  
        <button style="margin-right:5px; min-width: 80px" v-if="EspandiRicercheFatturaPassiva" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheFatturaPassiva">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-up text" style="margin-right: 5px" />
            <span class="text">Riduci</span>
          </div>
        </button>

        <button class="btn btn-s-md btn-info" 
                style="margin-right:5px;width:5%;min-width: 100px"
                @click="FiltraDati">[F2] Cerca
        </button>

      </div>

      <div v-if="EspandiRicercheFatturaPassiva" class="ZMSeparatoreFiltri">&nbsp;</div>
      
    <!-- Filtro fatture passive -->

         <div class="ZMSchedeMancanti" style="float: right" v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Fatture passive'">
            <label> N° Fatture Passive: {{ TreeView.Children.length }}</label>
         </div> 

      <div class="col-md-10" v-if="EspandiRicercheFatturaPassiva" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:5%;text-align: right; padding-right: 6px;">
          <label>Importo</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterFatturaPassiva.Importo"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Pagata dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterFatturaPassiva.PagataDal"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterFatturaPassiva.PagataAl"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:5%;text-align: right; padding-right: 6px;">
            <label style="margin-bottom: 0px;">Stato pag.</label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterFatturaPassiva.Pagate">
            <option value="Tutte">Tutte</option>
            <option value="Pagate">Pagate</option>           
            <option value="NonPagate">Non pagate</option>           
          </select>
        </div>

        <!-- <div style="float: left;margin-left:10px;padding-top: 5px;margin-left:10%">
          <input type="checkbox" v-model="FilterFatturaPassiva.RaggruppaPerFornitore">
        </div>
        <div style="float: left;;padding-top: 5px;margin-left:6px;margin-right: 10px">
          <label style="margin-bottom: 0px;margin-top: 0px;font-size:14px;">Raggruppa per fornitore</label>
        </div> -->

      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
     </div> 
     
     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Note di credito passive'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
      <div class="col-md-10" style="padding:0px">

        <div style="float:left;font-size:14px;padding-top: 5px;width:5%;text-align: right; padding-right: 15px;">
            <label style="margin-bottom: 0px;">Fornitore</label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:right;width:100%" class="form-control" v-model="FilterNotaDiCreditoPassiva.Fornitore">
            <option selected value="-1">-</option>
            <option v-for="Fornitore in LsFornitori" 
                          :Key="Fornitore.CHIAVE"
                          :value="Fornitore.CHIAVE">
              {{Fornitore.RAGIONE_SOCIALE}}
            </option>
          </select>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterNotaDiCreditoPassiva.DallaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterNotaDiCreditoPassiva.AllaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Scad. dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterNotaDiCreditoPassiva.ScadenzaDal"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterNotaDiCreditoPassiva.ScadenzaAl"/>
        </div>

      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

        <button style="margin-right: 5px; min-width: 80px" v-if="!EspandiRicercheNotaDiCreditoPassiva" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheNotaDiCreditoPassiva">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-down text" style="margin-right: 5px"></i>
            <span class="text">Espandi</span>
          </div>
        </button>
  
        <button style="margin-right: 5px; min-width: 80px" v-if="EspandiRicercheNotaDiCreditoPassiva" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheNotaDiCreditoPassiva">
          <div style="display: flex; justify-content: flex-start">
            <i class="fa fa-sort-up text" style="margin-right: 5px" />
            <span class="text">Riduci</span>
          </div>
        </button>

        <button class="btn btn-s-md btn-info" 
                style="margin-right: 5px; width:5%; min-width: 100px"
                @click="FiltraDati">[F2] Cerca
        </button>

      </div>
      
      <div v-if="EspandiRicercheNotaDiCreditoPassiva" class="ZMSeparatoreFiltri">&nbsp;</div>
         <div class="ZMSchedeMancanti" style="float: right" v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Note di credito passive'">
            <label> N° Note di Credito Passive: {{ TreeView.Children.length }}</label>
         </div> 

      <div class="col-md-10" v-if="EspandiRicercheNotaDiCreditoPassiva" style="padding:0px">

        <div style="float:left;font-size:14px;margin-top:5px;width:5%;text-align: right; padding-right: 6px;">
          <label>Importo</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text" class="form-control" v-model="FilterNotaDiCreditoPassiva.Importo"/>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Pagata dal</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterNotaDiCreditoPassiva.PagataDal"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:3%;text-align: right; padding-right: 15px;">
          <label>Al</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterNotaDiCreditoPassiva.PagataAl"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;font-size:14px;padding-top: 5px;width:5%;text-align: right; padding-right: 6px;">
            <label style="margin-bottom: 0px;">Stato pag.</label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:left;width:100%;" class="form-control" v-model="FilterNotaDiCreditoPassiva.Pagate">
            <option value="Tutte">Tutte</option>
            <option value="Pagate">Pagate</option>           
            <option value="NonPagate">Non pagate</option>           
          </select>
        </div>
      </div>
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
     </div>
     
     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Fornitori'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="height:50px;">
       <div class="col-md-10">
          <!-- Filtro Codice fornitore-->
          <div style="float:left;font-size:14px;padding-top: 5px;">
            <label>Codice&nbsp;&nbsp;&nbsp;</label>
          </div>
          <div style="float:left;width:15%">
            <input type="text" class="form-control" v-model="FilterFornitore.CodiceFornitore" />
          </div> 
          <!-- Filtro Ragione sociale-->
          <div style="float: left;font-size:14px;padding-top: 5px;margin-left:35px;">
            <label>Ragione sociale&nbsp;&nbsp;&nbsp;</label>
          </div>
          <div style="float:left;width:15%">
            <input type="text" class="form-control" v-model="FilterFornitore.RagioneSociale" />
          </div> 
          <!-- Filtro indirizzo-->
          <div style="float: left;font-size:14px;padding-top: 5px;margin-left:35px;">
            <label>Indirizzo&nbsp;&nbsp;&nbsp;</label>
          </div>
          <div style="float:left;width:15%">
            <input type="text" class="form-control" v-model="FilterFornitore.Indirizzo" />
          </div> 
       </div>

       <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">
       <!-- <div class="btn-group open" style="float:right;margin-right:5px">
          <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,false,false, false,false,false, true)">
          <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="width:200%">
            <li v-for="(AMenu,index) in MenuStampa" :Key="index">
              <a v-if="AMenu.Caption != ''" @click="OnNextMenuStampa(AMenu)">
                  {{AMenu.Caption}}
                  <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
              </a>
              <div v-else class="ZMSeparatoreMenu"></div>
            </li>
            <li>
              <div class="ZMSeparatoreMenu"></div>
            </li>
            <li>
              <a @click="MenuStampa = []">Chiudi</a>
            </li>
          </ul>
       </div> -->

       <button style="float:right; width:45%" @click="FiltraDati" class="btn btn-info">[F2] Cerca</button>

        <!-- <button style="float:right;width:45%" v-if="!EspandiRicercheCliente" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheCliente">
          <i class="fa fa-sort-down text"></i>
          <span class="text">Espandi</span>
        </button>
    
        <button style="float:right;width:45%" v-else class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheCliente">
          <i class="fa fa-sort-up text"/>
          <span class="text">Riduci</span>
        </button> -->
       </div>
       <div class="ZMSeparatoreFiltri">&nbsp;</div>
     </div>

     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Movimenti'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
      
      <div class="col-md-10" style="padding:0px">

        <div style="float:left;font-size:14px;padding-top: 5px;width:5%;text-align: right; padding-right: 15px;">
          <label>Tipologia</label>
        </div>
        <div style="float:left;width:10%;">
          <select style="float:right;width:100%" class="form-control" v-model="FilterMovimento.Tipologia">
            <option selected value="-1">-</option>
            <option v-for="Tipologia in LsTipologiaMovimenti" 
                            :Key="Tipologia.CHIAVE"
                            :value="Tipologia.CHIAVE">
              {{Tipologia.DESCRIZIONE}}
            </option>
          </select>
        </div>
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Dalla data </label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterMovimento.DallaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Alla data</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterMovimento.AllaData"/>
        </div>

        <!-- <button style="float:right;margin-right:5px;width:5%" v-if="!EspandiRicercheFatturaPassiva" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheFatturaPassiva">
          <i class="fa fa-sort-down text"></i>
          <span class="text">Espandi</span>
        </button>
  
        <button style="float:right;margin-right:5px;width:5%" v-if="EspandiRicercheFatturaPassiva" class="btn btn-sm btn-default" data-toggle="class:show" @click="OnClickEspandiRicercheFatturaPassiva">
          <i class="fa fa-sort-up text"/>
          <span class="text">Riduci</span>
        </button> -->
      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

        <button class="btn btn-s-md btn-info" 
                style="margin-right: 5px; width:5%; min-width: 100px"
                @click="FiltraDati">[F2] Cerca
        </button>

      </div>

      <!-- Filtro movimenti -->
      <div class="ZMSchedeMancanti">  
        <label style="margin-top: 9px"> N° Movimenti: {{ TreeView.Children.length }}</label>
      </div>
      
      <div class="ZMSeparatoreFiltri">&nbsp;</div>

     </div>

     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Movimenti Magazzini'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:0px">
      
      <div class="col-md-10" style="padding:0px">
        
        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Dalla data </label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date" class="form-control" v-model="FilterMovimentiMagazzini.DallaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:6%;text-align: right; padding-right: 15px;">
          <label>Alla data</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="date"  class="form-control" v-model="FilterMovimentiMagazzini.AllaData"/>
        </div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;width:1%;">&nbsp;</div>

        <div style="float:left;font-size:14px;margin-top: 5px;width:11%;text-align: right; padding-right: 15px;">
          <label>Descrizione prodotto</label>
        </div>
        <div style="float:left;width:10%;">
          <input type="text"  class="form-control" v-model="FilterMovimentiMagazzini.DescrizioneProdotto"/>
        </div>

      </div>

      <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

        <button class="btn btn-s-md btn-info" 
                style="margin-right: 5px; width:5%; min-width: 100px"
                @click="FiltraDati">[F2] Cerca
        </button>
      </div>

      <!-- Filtro movimenti -->
      <div class="ZMSchedeMancanti">  
        <label style="margin-top: 9px"> N° Movimenti: {{ TreeView.Children.length }}</label>
      </div>
      
      <div class="ZMSeparatoreFiltri">&nbsp;</div>
     </div>

<!-- Filtro documenti di trasporto -->
     <div v-if="!DatiInModifica && (CurrentFilter.GetFilterId() == 'Documenti di trasporto' || CurrentFilter.GetFilterId() == 'Entranti')" class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:2px">
        <div class="col-md-10" style="padding:0px">

          <div style="float:left;font-size:14px;padding-top:5px;text-align: right; padding-right: 15px;">
            <label>Part.IVA</label>
          </div>
          <div style="float:left;;width:13%;">
            <input type="text" class="form-control" v-model="FilterDDT.PartitaIva"/>
          </div>

          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;font-size:14px;padding-top:5px;text-align: right; padding-right: 15px;">
            <label>Intestatario</label>
          </div>
          <div style="float:left;width:13%;">
            <input type="text" class="form-control" v-model="FilterDDT.Intestatario"/>
          </div>
          
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;font-size:14px;padding-top:5px;text-align: right; padding-right: 15px;">
            <label>Stato</label>
          </div>
          <div style="float:left;width:13%; ">
            <select style="float:left;width:100%;" class="form-control" v-model="FilterDDT.StatoDDT">
              <option value="-1">Qualsiasi</option>
              <option value="Concluso">Concluso</option> 
              <option value="Annullato">Annullato</option> 
              <option value="InCorso">In Corso</option>           
            </select>
          </div>
        </div>

        <div class="col-md-2" style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 10px">

          <button class="btn btn-s-md btn-info" 
                  style="margin-right: 5px; width:5%; min-width: 100px"
                  @click="FiltraDati">[F2] Cerca
          </button>

        </div>

        <div class="col-md-10" style="padding-top:15px">

          <div style="float:left;font-size:14px;text-align: right; padding-right: 30px;padding-top: 5px;">
            <label>Dal</label>
          </div>
          <div style="float:left;width:13%;margin-right: 30px;">
            <input type="date" class="form-control" v-model="FilterDDT.DallaData"/>
          </div>

          <div style="float:left;">&nbsp;</div>
          <div style="float:left;font-size:14px;text-align: right; padding-right: 66px;padding-top: 5px;">
            <label>Al</label>
          </div>
          <div style="float:left;;width:13%;">
            <input type="date"  class="form-control" v-model="FilterDDT.AllaData"/>
          </div>
        
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;font-size:14px;text-align: right; padding-right: 8px;padding-top: 5px;">
            <label>Dal nr.</label>
          </div>
          <div style="float:left;width:8%;">
            <input type="number"  min="0" class="form-control" v-model="FilterDDT.DalNr"/>
          </div>
          <div style="float:left;font-size:14px;text-align: right; padding-right: 6x;">
            <label>&nbsp;/&nbsp;&nbsp;</label>
          </div>
          <div style="float:left;width:7%;">
            <input type="number" min="0" class="form-control" v-model="FilterDDT.Anno1"/>
          </div>

          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;font-size:14px;text-align: right; padding-right: 6px;padding-top: 5px;">
            <label>Al nr.</label>
          </div>
          <div style="float:left;width:8%;">
            <input type="number"  min="0" class="form-control" v-model="FilterDDT.AlNr"/>
          </div>
          <div style="float:left;font-size:14px;text-align: right; padding-right: 6x;">
            <label>&nbsp;/&nbsp;&nbsp;</label>
          </div>
          <div style="float:left;width:7%;">
            <input type="number" min="0" class="form-control" v-model="FilterDDT.Anno2" />
          </div>
          </div>

          <!-- Filtro documenti di trasporto -->
          <div class="ZMSchedeMancanti" style="float: right">
              <label> N° Documenti trasporto: {{ TreeView.Children.length }}</label>
          </div>    

        <div class="ZMSeparatoreFiltri">&nbsp;</div>
     </div>

    <!-- Filtro doc prodotti -->
    <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Doc scarico prod composti'" 
         class="breadcrumb no-border no-radius b-b b-light pull-in" style="padding-bottom:2px; overflow:auto;">
    
      <!-- Filtri a sinistra -->
      <div style="float:left; display:flex; align-items:center; font-size:14px;">
        <div style="margin-right:10px; text-align:right; padding-top:5px;">
          <label>Dal</label>
        </div>
        <div style="margin-right:30px;">
          <input type="date" class="form-control" v-model="FilterDocScaricoProdComposti.DallaData"/>
        </div>
    
        <div style="margin-right:10px; text-align:right; padding-top:5px;">
          <label>Al</label>
        </div>
        <div>
          <input type="date" class="form-control" v-model="FilterDocScaricoProdComposti.AllaData"/>
        </div>
      </div>
    
      <!-- Pulsante a destra -->
      <div style="float:right;">
        <button class="btn btn-s-md btn-info" 
                style="width:100px; min-width:100px;"
                @click="FiltraDati">
          [F2] Cerca
        </button>
      </div>
    
    </div>

     
     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Movimenti/Conti'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="height:50px;">
        <!-- {{EditingDatiNuovi}} -->
        <div style="display: flex; justify-content: flex-end; align-items: flex-start; gap: 5px; padding: 0 15px">
        </div>
     </div>


     <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Prodotti'" class="breadcrumb no-border no-radius b-b b-light pull-in" style="height:50px;">
        <div class="col-md-10" style="padding:0px">
        
          <div style="float: left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
            <label>Descrizione</label>
          </div>
          <div style="float:left;width:11%">
            <!-- <input type="text" class="form-control" v-model="FilterProdotto.Descrizione" />
              -->
            <VUEInputProdotti v-model="FilterProdotto.Descrizione"></VUEInputProdotti>
          </div> 
          <div>
            <!-- Filtro Settore-->
            <div style="float: left;font-size:14px;padding-top: 5px;width:6%;text-align: right; padding-right: 15px;">
              <label>Settore</label>
            </div>
            <div style="float:left;width:11%">
              <select class="form-control" v-model="FilterProdotto.Settore">
                  <option selected :value="-1">-</option>
                  <option v-for="(Settore) in LsSettori" :key="Settore.CHIAVE" :value="Settore.CHIAVE">
                      {{ Settore.DESCRIZIONE }}
                  </option>
              </select>
            </div> 
            <div style="float: left;font-size:14px;padding-top: 5px;width:6%;text-align: right; padding-right: 15px;">
              <label>Magazzino</label>
            </div>
            <div style="float:left;width:11%">
              <select class="form-control" v-model="FilterProdotto.IdMagazzino">
                  <option selected :value="-1">-</option>
                  <option v-for="(Magazzino) in LsMagazzini" :key="Magazzino.CHIAVE" :value="Magazzino.CHIAVE">
                      {{ Magazzino.DESCRIZIONE }}
                  </option>
              </select>
            </div> 
            <!-- Filtro Prodotti Sottosoglia-->
            <div style="float: left;font-size:14px;padding-top: 5px;width:14%;text-align: right; padding-right: 15px;">
              <label>Solo prodotti sottosoglia</label>
            </div>
            <div style="float:left;width:3%;padding-top: 1px">
              <input type="checkbox" style="height:20px" class="form-control" v-model="FilterProdotto.SoloSottosoglia" />
            </div> 
          </div>
        </div>

        <div class="col-md-2" style="padding:0px">

          <button style="float:right;margin-right:15px;width:5%;min-width: 100px" @click="FiltraDati" class="btn btn-info">[F2] Cerca</button>
          <div class="btn-group open" style="float:right;margin-right:5px">

            <!-- <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,false,false, false,false,true, false)">
              <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="width:200%">
                <li v-for="(AMenu,index) in MenuStampa" :Key="index">
                  <a v-if="AMenu.Caption != ''" @click="OnNextMenuStampa(AMenu)">
                      {{AMenu.Caption}}
                      <i v-if="AMenu.SubMenu != undefined" style="margin-left:15px;" class="fa fa-angle-right text-success"></i>
                  </a>
                  <div v-else class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <div class="ZMSeparatoreMenu"></div>
                </li>
                <li>
                  <a @click="MenuStampa = []">Chiudi</a>
                </li>
              </ul> -->
          </div>
        </div>
     </div>

     <div>
        <div v-if="!DatiInModifica && CurrentFilter.GetFilterId() == 'Prima nota'">
            <VUESchedaPrimaNota />
        </div>

        <div v-if="CurrentFilter.GetFilterId() != 'Prima nota' && CurrentFilter.GetFilterId() != 'Riscossioni tecnici' && 
                   CurrentFilter.GetFilterId() != 'Filiali' && CurrentFilter.GetFilterId() != 'Magazzini'">

        <div v-if="!DatiInModifica && 
                    CurrentFilter.GetFilterId() != 'Prima nota' && 
                    CurrentFilter.GetFilterId() != 'Riscossioni tecnici' && 
                    CurrentFilter.GetFilterId() != 'Filiali' &&
                    CurrentFilter.GetFilterId() != 'Magazzini' &&
                    !AlberoNascosto" 
              style="float:left;padding-left:5px;padding-right:5px;width:24%;overflow-y:auto"
              :style="{ height : (Altezza - AltezzaFiltri - AltezzaHeader- 50) + 'px'}"  >
            <template v-if="FirstSearch">
              <div v-if="TreeView.Children.length != 0" class="dd">
                <VUETreeViewLevel :LevelElements="TreeView.Children" 
                                  :TreeView="TreeView" 
                                  @onClick="OnClickAlbero"
                                  />
              </div>
            <div v-else style="border: 1px solid red;color: red; text-align: center;font-weight: bold;">
                Nessun dato presente
              </div>
            </template> 
        </div>
        
        <div v-if="TipoSchedaSelezionata != 'TSchedaGenerica'" 
              style="float:left;padding-left:5px;padding-right:5px;overflow-y:auto;"
              :style="{ width    : (DatiInModifica || AlberoNascosto ? '100%' : '76%'), 
                        height   : (SchedaSelezionata.DatiModificati() || SchedaSelezionata.IsNuovo())? ('100%') : ((Altezza - AltezzaFiltri - AltezzaHeader - 50) + 'px'),
                        overflow : (SchedaSelezionata.DatiModificati() || SchedaSelezionata.IsNuovo())? 'hidden' : 'none'}">
            <div v-if="SchedaSelezionata.DatiModificati() || SchedaSelezionata.IsNuovo()" style="text-align:right;padding-top:2px;padding-bottom:2px">
                <a v-if="SchedaSelezionata.CanRecord()" class="btn btn-s-md btn-success" style="margin-right:10px" @click="OnClickConferma()">Conferma</a>
                <a class="btn btn-s-md btn-danger" @click="OnClickAnnulla();">Annulla</a>
            </div>
            <VUESchedaCliente v-if="TipoSchedaSelezionata == 'TSchedaCliente'" 
                              :SchedaCliente="SchedaSelezionata"
                              @onClickNuovaFattura="OnClickNuovaFatturaDaSchedaCliente"
                              @onClickNuovoPreventivo="OnClickNuovoPreventivoDaSchedaCliente"
                              @onClickNuovoMovimento="OnClickNuovoMovimentoDaSchedaCliente"
                              @onClickNuovaNotaDiCredito="OnClickNuovaNotaDiCreditoDaSchedaCliente"
                              @onClickNuovaFatturaDaBanco="OnClickNuovaFatturaDaBancoDaSchedaCliente"
                              @onClickNuovoDDT="OnClickNuovoDDTDaSchedaCliente"
                              @onClickNuovoDDTEntrante="OnClickNuovoDDTEntranteDaSchedaCliente"/>
            <VUESchedaFattura v-if="TipoSchedaSelezionata == 'TSchedaFattura'" 
                              :SchedaFattura="SchedaSelezionata" 
                              @onChangeNodiAlbero="OnChangeNodiAlbero"
                              @onClickGeneraNotaDiCredito="OnClickGeneraNotaDiCredito"/>
            <VUESchedaFornitore v-if="TipoSchedaSelezionata == 'TSchedaFornitore'" 
                              :SchedaFornitore="SchedaSelezionata" />  
            <VUESchedaNotaDiCredito v-if="TipoSchedaSelezionata == 'TSchedaNotaDiCredito'" 
                              :SchedaNotaDiCredito="SchedaSelezionata"
                              @onChangeNodiAlbero="OnChangeNodiAlbero"/>
            <VUESchedaPreventivo v-if="TipoSchedaSelezionata == 'TSchedaPreventivo'" 
                              :SchedaPreventivo="SchedaSelezionata"
                              @onPreventivoConfermato="CreazioneDocumentiTramitePreventivo"
                              @onClickNuovaFattura="CreazioneFatturaTramitePreventivo"
                              @onClickNuovaDDT="CreazioneDDTTramitePreventivo"
                              @onClickNuovaDDTEntrante="CreazioneDDTEntranteTramitePreventivo"/>    
            <VUESchedaPreventivoMultiparametrico v-if="TipoSchedaSelezionata == 'TSchedaPreventivoMultiparametrico'" 
                                                :SchedaPreventivo="SchedaSelezionata"
                                                @onPreventivoConfermato="CreazioneConfermaTramitePreventivo"/>
            <VUESchedaDocumentoDiTrasporto v-if="TipoSchedaSelezionata == 'TSchedaDocumentoDiTrasporto'" 
                              :SchedaDocumentoDiTrasporto="SchedaSelezionata"
                              @onClickNuovaFattura="CreazioneFatturaTramiteDDT"/>
              <VUESchedaDocumentoDiTrasportoEntrante v-if="TipoSchedaSelezionata == 'TSchedaDocumentoDiTrasportoEntrante'" 
                              :SchedaDocumentoDiTrasportoEntrante="SchedaSelezionata"
                              @onClickNuovaFattura="CreazioneFatturaTramiteDDTEntrante"/> 
              <VUESchedaDocScaricoProdottiComposti v-if="TipoSchedaSelezionata == 'TSchedaDocScaricoProdottiComposti'" 
                              :SchedaDocScaricoProdottiComposti ="SchedaSelezionata"/>
              <VUESchedaContoCorrente v-if="TipoSchedaSelezionata == 'TSchedaContoCorrente'" 
                              :SchedaContoCorrente="SchedaSelezionata" /> 
              <VUESchedaMovimento v-if="TipoSchedaSelezionata == 'TSchedaMovimento'" 
                              :SchedaMovimento="SchedaSelezionata" />
              <VUESchedaFatturaPassiva v-if="TipoSchedaSelezionata == 'TSchedaFatturaPassiva'" 
                              :SchedaFatturaPassiva="SchedaSelezionata" />
              <VUESchedaMovimentiMagazzini v-if="TipoSchedaSelezionata == 'TSchedaMovimentiMagazzini'" 
                              :SchedaMovimentiMagazzini="SchedaSelezionata" />
              <VUESchedaProdotto v-if="TipoSchedaSelezionata == 'TSchedaProdotto'" 
                              :SchedaProdotto="SchedaSelezionata" /> 
              <VUESchedaAutoFattura v-if="TipoSchedaSelezionata == 'TSchedaAutoFattura'" 
                              :SchedaAutoFattura="SchedaSelezionata" /> 
              <VUESchedaFattureInsolutePregresse v-if="TipoSchedaSelezionata == 'TSchedaFattureInsolutePregresse'" 
                              :SchedaFattureInsolutePregresse="SchedaSelezionata"
                              @onClickNuovoMovimentoFromFattureInsolute="OnClickNuovoMovimentoFromFattureInsolute"/>     
              <VUESchedaFattureInsolutePregresseFornitori v-if="TipoSchedaSelezionata == 'TSchedaFattureInsolutePregresseFornitori'" 
                              :SchedaFattureInsolutePregresseFornitori="SchedaSelezionata"/>  
        </div>
       </div>
     </div>
</template>

<script>
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputFornitore from '@/components/InputComponents/VUEInputFornitore.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputRegioni from '@/components/InputComponents/VUEInputRegioni.vue';
import VUEInputZone from '@/components/InputComponents/VUEInputZone.vue';
import VUEInputProdotti from '@/components/InputComponents/VUEInputProdotti.vue';
import VUEModal from '@/components/Slots/VUEModal.vue';
import VUEConfirm from '@/components/VUEConfirm.vue';
import VUETreeViewLevel from '@/components/VUETreeViewLevel.vue';
import {
          TFilterAutofattura,
          TFilterCliente,
          TFilterConferme,
          TFilterDDT,
          TFilterDDTEntranti,
          TFilterDocScaricoProdComposti,
          TFilterFattura,
          TFilterFatturaPassiva,
          TFilterFiliale,
          TFilterMagazzini,
          TFilterFornitore,
          TFilterMovimento,
          TFilterMovimentiMagazzini,
          TFilterMovimentoConti,
          TFilterNota,
          TFilterNotaDiCreditoPassiva,
          TFilterPreventivo,
          TFilterPrimaNota,
          TFilterProdotto,
        } from '@/ListaFiltri.js';
import {
          DASHBOARD_FILTER_TYPES,
          RUOLI,
          SystemInformation,
          TIPO_COMUNICAZIONI,
        } from '@/SystemInformation';
import VUESchedaCliente, { TSchedaCliente } from '@/views/SchedeDatabase/VUESchedaCliente.vue';
import VUESchedaDocumentoDiTrasporto, { TSchedaDocumentoDiTrasporto } from '@/views/SchedeDatabase/VUESchedaDocumentoDiTrasporto.vue';
import VUESchedaDocumentoDiTrasportoEntrante, { TSchedaDocumentoDiTrasportoEntrante } from '@/views/SchedeDatabase/VUESchedaDocumentoDiTrasportoEntrante.vue';
import VUESchedaDocScaricoProdottiComposti, { TSchedaDocScaricoProdottiComposti } from '@/views/SchedeDatabase/VUESchedaDocScaricoProdottiComposti.vue';
import VUESchedaFattura, { TSchedaFattura } from '@/views/SchedeDatabase/VUESchedaFattura.vue';
import VUESchedaFornitore, { TSchedaFornitore } from '@/views/SchedeDatabase/VUESchedaFornitore.vue';
import VUESchedaNotaDiCredito, { TSchedaNotaDiCredito } from '@/views/SchedeDatabase/VUESchedaNotaDiCredito.vue';
import VUESchedaPreventivo, { TSchedaPreventivo } from '@/views/SchedeDatabase/VUESchedaPreventivo.vue';
import { saveAs } from 'file-saver';
import XLSX from 'xlsx-js-style/dist/xlsx.min.js';
import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js';
import { TZStringConvFunct } from '../../../../../../../../Librerie/VUE/ZStringConvFunct.js';
import { TZTree } from '../../../../../../../../Librerie/VUE/ZTreeObjects';
import VUESchedaAutoFattura,{ TSchedaAutoFattura } from '../SchedeDatabase/VUESchedaAutoFattura.vue';
import VUESchedaContoCorrente, {TSchedaContoCorrente} from '../SchedeDatabase/VUESchedaContoCorrente.vue';
import VUESchedaFatturaPassiva, { TSchedaFatturaPassiva } from '../SchedeDatabase/VUESchedaFatturaPassiva.vue';
import VUESchedaMovimentiMagazzini, { TSchedaMovimentiMagazzini } from '../SchedeDatabase/VUESchedaMovimentiMagazzini.vue';
import VUESchedaFattureInsolutePregresse from '../SchedeDatabase/VUESchedaFattureInsolutePregresse.vue';
import VUESchedaFattureInsolutePregresseFornitori from '../SchedeDatabase/VUESchedaFattureInsolutePregresseFornitori.vue';
import VUESchedaFiliali from '../SchedeDatabase/VUESchedaFiliali.vue';
import VUESchedaMagazzini from '../SchedeDatabase/VUESchedaMagazzini.vue';
import VUESchedaMovimento, { TSchedaMovimento } from '../SchedeDatabase/VUESchedaMovimento.vue';
import VUESchedaPreventivoMultiparametrico, { TSchedaPreventivoMultiparametrico } from '../SchedeDatabase/VUESchedaPreventivoMultiparametrico.vue';
import VUESchedaPrimaNota from '../SchedeDatabase/VUESchedaPrimaNota.vue';
import VUESchedaProdotto, { TSchedaProdotto } from '../SchedeDatabase/VUESchedaProdotto.vue';

export default
{
    props : ['Altezza'],
    name  : "VUEAppContentDashboard",
    components: { 
                  VUESchedaCliente, 
                  VUETreeViewLevel, 
                  VUEInputRegioni,
                  VUESchedaFattura, 
                  VUESchedaFornitore, 
                  VUESchedaFiliali,
                  VUESchedaMagazzini,
                  VUESchedaNotaDiCredito,
                  VUESchedaPreventivo,
                  VUESchedaDocumentoDiTrasporto,
                  VUESchedaDocumentoDiTrasportoEntrante,
                  VUESchedaDocScaricoProdottiComposti,
                  VUESchedaContoCorrente,
                  VUESchedaMovimento,
                  VUESchedaMovimentiMagazzini,
                  VUESchedaFatturaPassiva,
                  VUEInputProvince,
                  VUEInputZone,
                  VUESchedaProdotto,
                  VUESchedaPrimaNota,
                  VUEConfirm,
                  VUESchedaAutoFattura,
                  VUEInputClienti,
                  VUEInputFornitore,
                  VUEInputProdotti,
                  VUEModal,
                  VUESchedaFattureInsolutePregresse,
                  VUESchedaFattureInsolutePregresseFornitori,
                  VUEModalCaricamentoDati,
                  VUESchedaPreventivoMultiparametrico,
                },
    data() 
    {
        var Self = this;
        var TreeView = new TZTree();

        TreeView.BeforeDelete = function()
        {
           Self.SchedaSelezionata = new TSchedaGenerica(SystemInformation.AdvQuery);
        }

        TreeView.OnSelect = function(ANode,OnSuccess)
        {
          if(ANode != undefined) 
          {
            if(!Self.DisponiInCorso)
            {
              Self.DisponiInCorso = true
              Self.SchedaSelezionata = ANode.Data;

              if(Self.SchedaSelezionata.Disponi != undefined)
              {
                Self.Timer = setInterval(Self.UpdateTimer, SystemInformation.AttesaPopupCaricamentoAlbero);

                Self.SchedaSelezionata.Disponi(function()
                {
                  Self.DisponiInCorso = false
                  Self.BloccaTimer()
                  if(OnSuccess != undefined)
                    OnSuccess()
                });
              }
              else Self.DisponiInCorso = false
            }
          }
          else 
          {
            Self.SchedaSelezionata = new TSchedaGenerica(SystemInformation.AdvQuery);
            if(OnSuccess != undefined) OnSuccess(OnSuccess)
          }
        }

        var PagIniziale = new TFilterCliente()
        var Modello = {
                        Ricercaincorso                                            : false,
                        DisponiInCorso                                            : false,
                        BloccaWatchFiltraDati                                     : false,
                        Secondi                                                   : 0,
                        Timer                                                     : null,
                        FilterCliente                                             : PagIniziale,
                        OrdinamentoSaldoClienti                                   : 'C',
                        OrdinamentoSaldoFornitori                                 : 'C',
                        FilterFattura                                             : new TFilterFattura(),
                        FilterAutofattura                                         : new TFilterAutofattura(),
                        FilterNota                                                : new TFilterNota(),
                        FilterConferma                                            : new TFilterConferme(),
                        FilterPreventivo                                          : new TFilterPreventivo(),
                        FilterFatturaPassiva                                      : new TFilterFatturaPassiva(),
                        FilterNotaDiCreditoPassiva                                : new TFilterNotaDiCreditoPassiva(),
                        FilterProdotto                                            : new TFilterProdotto(),
                        FilterMovimento                                           : new TFilterMovimento(),
                        FilterDDT                                                 : new TFilterDDT(),
                        FilterFornitore                                           : new TFilterFornitore(),
                        FilterPrimaNota                                           : new TFilterPrimaNota(),
                        FilterFiliale                                             : new TFilterFiliale(),
                        FilterMagazzini                                           : new TFilterMagazzini(),
                        FilterMovimentoConto                                      : new TFilterMovimentoConti(),
                        FilterMovimentiMagazzini                                  : new TFilterMovimentiMagazzini(),
                        FilterDDTEntranti                                         : new TFilterDDTEntranti(),
                        FilterDocScaricoProdComposti                              : new TFilterDocScaricoProdComposti(),

                        AlberoNascosto                                            : false,
                        DeveloperMode                                             : SystemInformation.DeveloperMode,
                        Ruoli                                                     : RUOLI,
                        UserInformation                                           : SystemInformation.UserInformation,
                        VisibilitaCampanellaLog                                   : SystemInformation.AccessRights.VisibilitaCampanellaLog(),
                        PopupAnnullamento                                         : false,
                        PopupAnnullamentoAvvisoFatture                            : false,
                        PopupConfermaGestitaDiscrepanzaForniturePopupAvvisi       : false,
                        PopupAnnullamentoComunicazioniImportanti                  : false,
                        PopupFatturaPerConferma                                   : false,
                        MostraSoloRateNonPagate                                   : true,
                        PopupConfermaStampaRibaFatture                            : false,
                        InformazioniComunicazionePresaInConsiderazione            : null,
                        StatoPreventivoIniziale                                   : '',

                        PopupVisualizzaScheda                                     : false,
                        PresenzaComunicazioni                                     : false,
                        PopupAttesaCalcolo                                        : false,
                        // ListaFilialiVisibile                                      : SystemInformation.AccessRights.ListaFilialiVisibile(),
                        ExcelRiassuntoClienti                                     : SystemInformation.AccessRights.ExcelRiassuntoClientiVisibile(),
                        TipiPriorita                                              : SystemInformation.GetLsPrioritaUrgenze(),
                        TipiComunicazioni                                         : TIPO_COMUNICAZIONI,
                        ListaComunicazioni                                        : [],
                        ListaComunicazioniTemporaneo                              : [],
                        IndexRichiestaComunicazioni                               : 0,                   
                        AnnoStampaRitenutaClienti                                 : false,
                        AnnoStampaRitenuta                                        : new Date().getFullYear(),
                        FiltroStampaRitenute                                      : -1,
                        TreeView                                                  : TreeView, 
                        CurrentFilter                                             : PagIniziale,
                        AltezzaHeader                                             : 50 + 65,
                        PopupCreazioneFattura                                     : {
                                                                                      VisualizzaFattura : false,
                                                                                      Visualizza        : false
                                                                                    },
                        PopupFornitureDiverse                                     : {
                                                                                      ListaChiavi          : [], 
                                                                                      Visualizza           : false,
                                                                                      ElementoSelezionato  : '',
                                                                                      VisualizzaFattura    : false,
                                                                                      FatturaCaricata      : '',
                                                                                      NuovaFattura         : false,
                                                                                      Messaggi             :{
                                                                                                              Fattura    : 'Avviso Fattura N.',
                                                                                                              Creazione  : 'Fattura in creazione',
                                                                                                            }
                                                                                    },
                        PopupCreazioneDDTEntrante                                 : {
                                                                                      Visualizza   : false
                                                                                    },
                        FirstSearch                                               : false,
                        MenuNuovo                                                 : [],
                        MenuFilterPrincipale                                      : [],
                        MenuFilterPrincipaleRicerche                              : [],
                        MenuFilter                                                : [],
                        MenuFilterVisible                                         : false,
                        MenuFilterRicerche                                        : [],
                        MenuFilterRicercheVisible                                 : false,
                        MenuStampa                                                : [],
                        DropdownMenuXML                                           : [],
                        TipologiaFiltroPerXML                                     : 'Tutte',
                        MeseFiltroPerXML                                          : [],
                        SchedaSelezionata                                         : new TSchedaGenerica(SystemInformation.AdvQuery),
                        SchedaSelezionataPopup                                    : new TSchedaGenerica(SystemInformation.AdvQuery),
                        PreventivoPerConferma                                     : null,
                        FatturaPopupInCreazione                                   : null,
                        CurrentYear                                               : (new Date()).getFullYear(),
                        LsTipologiaMovimenti                                      : SystemInformation.Configurazioni.CatMovimenti,
                        LsFornitori                                               : SystemInformation.Configurazioni.Fornitori,
                        LsMesi                                                    : TZDateFunct.GetMesiInItaliano(),
                        LsZone                                                    : SystemInformation.Configurazioni.Zone,
                        LsSettori                                                 : SystemInformation.Configurazioni.Settori,
                        LsMagazzini                                               : SystemInformation.Configurazioni.Magazzini,
                        LsTecnici                                                 : SystemInformation.Configurazioni.Tecnici,
                        VisualizzaPrimaNota                                       : false,
                        EspandiRicercheCliente                                    : false,
                        EspandiRicercheFattura                                    : false,
                        EspandiRicercheNota                                       : false,
                        EspandiRicerchePreventivo                                 : false,
                        EspandiRicercheFatturaPassiva                             : false,
                        EspandiRicercheNotaDiCreditoPassiva                       : false,
                        EspandiRicercheDDT                                        : false,
                        EspandiRicercheConferme                                   : false,
                        CostanteDashboardFilter                                   : DASHBOARD_FILTER_TYPES,
                        PopupComunicazioni                                        : false,
                        PopupLogApp                                               : false,
                        PopupLogAccessi                                           : false,
                        PopupLogRichiestaModificaCliente                          : false,
                        PopupLogRichiestaModificaAnagrafica                       : false,
                        PopupFornitori                                            : false,
                        PopupInserimentoClienteGuidato                            : false,
                        MessaggioPopupInformazioniAggiuntive                      : '',
                        PopupInformazioniAggiuntive                               : false,
                        DataSaldoClienti                                          : false,
                        SaldoClientiAl                                            : '',
                        DataSaldoFornitori                                        : false,
                        SaldoFornitoriAl                                          : '',
                        PopupModificaAnagraficaCliente                            : false,
                        PopupModificaAnagraficaAmm                                : false,
                        PopupModificaAnagraficaFiliale                            : false,
                        IndexAnagrafiche                                          : 0,
                        OggettoVisualizzatoNelPopup                               : null,
                        ListaAnagraficheDaModificare                              : [],
                        IdFornitoreXDDT                                           : -1,
                        ProdottoSelezionato                                       : { 
                                                                                      ChiaveProdotto : null, 
                                                                                      Soglia : 0, 
                                                                                      ChiaveMagazzino : null 
                                                                                    },
                        MenuTrigger                                               : SystemInformation.MenuTrigger,
                        InserimentoClienteGuidatoTrigger                          : SystemInformation.InserimentoClienteGuidatoTrigger,
                        PopupScaricoFileZip                                       : false,
                        ScaricoFileZipDal                                         : TZDateFunct.FormatDateTime('yyyy-mm-dd', TZDateFunct.SumMonth(new Date(), -3)),
                        ScaricoFileZipAl                                          : TZDateFunct.FormatDateTime('yyyy-mm-dd', new Date()),
                        OperazioneLanciata                                        : false,
                        ConfermaLanciata                                          : false,
                        PopupAccesso                                              : false,
                        PopupDisdetta                                             : false,
                        RichiestaAccesso                                          : '',
                        RichiestaDisdetta                                         : '',
                        AzioneAccesso                                             : null,
                        LogSelezionato                                            : null,
                        // LsLogAppClienti                                           : [],
                        LsLogAccessiClienti                                       : [],
                        LsLogModificheDatiClienti                                 : [],
                        LsDatiClienteOriginali                                    : [],
                        NuoviDatiCliente                                          : {},
                        PopupProdottiExcel                                        : false,
                        PopupFiltroMagazzinoTipo                                  : "Tutti",
                        PopupMagazzinoSelezionato                                 : -1,
                      };

          
          Modello.MenuFilter  = [
            {
              Caption     : Modello.FilterCliente.GetDescrizione(),
              Filter      : Modello.FilterCliente,
            },
            {
              Caption : ""
            },
            {
              Caption     : Modello.FilterFornitore.GetDescrizione(),
              Filter      : Modello.FilterFornitore,
            },
            {
              Caption     : Modello.FilterMovimentoConto.GetDescrizione(),
              Filter      : Modello.FilterMovimentoConto,
            },
            {
              Caption     : Modello.FilterPrimaNota.GetDescrizione(),
              Filter      : Modello.FilterPrimaNota,
            },
            {
              Caption : ""
            },
            {
              Caption     : Modello.FilterProdotto.GetDescrizione(),
              Filter      : Modello.FilterProdotto,
            },
            {
              Caption     : Modello.FilterMagazzini.GetDescrizione(),
              Filter      : Modello.FilterMagazzini,
            },
            
            {
              Caption : ""
            },            
            {
              Caption: "Ricerche",
              SubMenu: [
                    {
                        Caption     : Modello.FilterFattura.GetDescrizione(),
                        Filter      : Modello.FilterFattura,
                    },
                    {
                        Caption     : Modello.FilterNota.GetDescrizione(),
                        Filter      : Modello.FilterNota,
                    },
                    {
                        Caption     : Modello.FilterConferma.GetDescrizione(),
                        Filter      : Modello.FilterConferma,
                    },
                    {
                        Caption     : Modello.FilterPreventivo.GetDescrizione(),
                        Filter      : Modello.FilterPreventivo,
                    },
                    {
                        Caption     : Modello.FilterFatturaPassiva.GetDescrizione(),
                        Filter      : Modello.FilterFatturaPassiva,
                    },
                    {
                        Caption     : Modello.FilterNotaDiCreditoPassiva.GetDescrizione(),
                        Filter      : Modello.FilterNotaDiCreditoPassiva,
                    },
                    {
                        Caption     : Modello.FilterAutofattura.GetDescrizione(),
                        Filter      : Modello.FilterAutofattura,
                    },
                    {
                        Caption     : "",
                    },
                    {
                        Caption     : "",
                    },
                    {
                        Caption     : Modello.FilterMovimento.GetDescrizione(),
                        Filter      : Modello.FilterMovimento,
                    },
                    {
                       Caption     : Modello.FilterMovimentiMagazzini.GetDescrizione(),
                       Filter      : Modello.FilterMovimentiMagazzini,
                    }
              ]
            }
          ]
          
          Modello.MenuFilterPrincipale = Modello.MenuFilter
          // if(Modello.ListaFilialiVisibile)
              Modello.MenuFilter.push( {
                                          Caption     : Modello.FilterFiliale.GetDescrizione(),
                                          Filter      : Modello.FilterFiliale,
                                        });
          
          Modello.MenuFilter.push ( {
                                        Caption     : 'Documenti di trasporto',
                                        SubMenu: [
                                                  {
                                                    Caption     : Modello.FilterDDT.GetDescrizione(),
                                                    Filter      : Modello.FilterDDT,
                                                  },
                                                  {
                                                    Caption     : Modello.FilterDDTEntranti.GetDescrizione(),
                                                    Filter      : Modello.FilterDDTEntranti,
                                                  },
                                                  {
                                                    Caption     : Modello.FilterDocScaricoProdComposti.GetDescrizione(),
                                                    Filter      : Modello.FilterDocScaricoProdComposti,
                                                  },
                                        ]
                                      });
           
          Modello.MenuFilterRicerche = [
            {
                Caption     : Modello.FilterFattura.GetDescrizione(),
                Filter      : Modello.FilterFattura,
            },
            {
                Caption     : Modello.FilterNota.GetDescrizione(),
                Filter      : Modello.FilterNota,
            },
            {
                Caption     : Modello.FilterConferma.GetDescrizione(),
                Filter      : Modello.FilterConferma,
            },
            {
                Caption     : Modello.FilterPreventivo.GetDescrizione(),
                Filter      : Modello.FilterPreventivo,
            },
            {
                Caption     : Modello.FilterFatturaPassiva.GetDescrizione(),
                Filter      : Modello.FilterFatturaPassiva,
            },
            {
                Caption     : Modello.FilterNotaDiCreditoPassiva.GetDescrizione(),
                Filter      : Modello.FilterNotaDiCreditoPassiva,
            },
            {
                Caption     : Modello.FilterAutofattura.GetDescrizione(),
                Filter      : Modello.FilterAutofattura,
            },
            {
                Caption     : "",
            },
            {
                Caption     : Modello.FilterMovimento.GetDescrizione(),
                Filter      : Modello.FilterMovimento,
            }
          ]
          
            Modello.MenuFilterRicerche.push({
                                                Caption     : Modello.FilterMovimentiMagazzini.GetDescrizione(),
                                                Filter      : Modello.FilterMovimentiMagazzini,
                                            });

          Modello.MenuFilterPrincipaleRicerche = Modello.MenuFilterRicerche
        
          Modello.MenuFilterRicerche.push({
                                           Caption     : "Documenti di trasporto",
                                           SubMenu: [
                                                      {
                                                        Caption     : Modello.FilterDDT.GetDescrizione(),
                                                        Filter      : Modello.FilterDDT,
                                                      },
                                                      {
                                                        Caption     : Modello.FilterDDTEntranti.GetDescrizione(),
                                                        Filter      : Modello.FilterDDTEntranti,
                                                      },
                                                      {
                                                      Caption     : Modello.FilterDocScaricoProdComposti.GetDescrizione(),
                                                      Filter      : Modello.FilterDocScaricoProdComposti,
                                                      },
                                                    ]
                                          })
         return Modello;
    },

    methods: 
    {
      UpdateTimer() 
      {
        if(this.DisponiInCorso)
        {
          this.Secondi = this.Secondi + SystemInformation.AttesaPopupCaricamentoAlbero;
          if(this.Secondi == SystemInformation.AttesaPopupCaricamentoAlbero)
          {
            this.PopupAttesaCalcolo = true
          }
          if(this.Secondi >= 10 * SystemInformation.AttesaPopupCaricamentoAlbero)
          {
            this.BloccaTimer()
          } 
        }
      },

      OnFileExcelClientiSelezionato(event)
      {
        let FileExcelSelezionato = event.target.files[0]

        if(!FileExcelSelezionato) 
        {
           alert("Seleziona prima un file Excel.")
           return
        }

        let reader = new FileReader()
      
        reader.onload = (e) => 
        {      
          let binaryStr = e.target.result;
          let workbook  = XLSX.read(binaryStr, { type: 'binary' })
      
          let firstSheetName = workbook.SheetNames[0]
          let worksheet      = workbook.Sheets[firstSheetName]
      
          let jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 })
          let DatiCliente = []   
          
          for(var i = 1; i < jsonData.length; i++)
          {
            let Riga = jsonData[i]
            if (!Riga || Riga.length === 0 || Riga[2] === undefined || Riga[4] === undefined || Riga[2] === '' || Riga[4] === '') continue

            let NomePagamento   = (Riga[19] || "").toString().trim().toLowerCase()
            let ChiavePagamento = ""

            for (let j = 0; j < SystemInformation.Configurazioni.CondPagamenti.length; j++)
            {
              let CondPagamento = SystemInformation.Configurazioni.CondPagamenti[j]

              if ((CondPagamento.DESCRIZIONE || "").toString().trim().toLowerCase() === NomePagamento)
              {
                ChiavePagamento = CondPagamento.CHIAVE != null ? CondPagamento.CHIAVE.toString() : ""
                break
              }
            }

            const Cliente = 
                  {
                    CODICE         : Riga[0] != null ? Riga[0].toString() : "",
                    // VsRif                  : Riga[1] != null ? Riga[1].toString() : "",
                    RAGIONE_SOCIALE        : Riga[2] || "",
                    // Nome                   : Riga[3]  || "",
                    COMUNE_FATTURAZIONE    : Riga[4] || "",
                    TELEFONO               : Riga[5] != null ? Riga[5].toString() : "",
                    // Fax                    : Riga[6] != null ? Riga[6].toString() : "",
                    INDIRIZZO_FATTURAZIONE : Riga[7] || "",
                    NR_CIVICO_FATTURAZIONE : Riga[8] != null ? Riga[8].toString() : "",
                    PROVINCIA_FATTURAZIONE : Riga[9] || "",
                    CAP_FATTURAZIONE       : Riga[10] != null ? Riga[10].toString() : "",
                    CELLULARE              : Riga[11] != null ? Riga[11].toString() : "",
                    // Zona                      : Riga[12]  || "",
                    PARTITA_IVA            : Riga[13] || "",
                    CODICE_FISCALE         : Riga[14] || "",
                    // Disattiva              : (Riga[15] || "").toString().trim().toLowerCase() == 'True' ? true : false,
                    EMAIL                  : Riga[16]  || "",
                    // DesAltro               : Riga[17]  || "",
                    // IDTipoPagam            : Riga[18]  || "",
                    COND_PAGAMENTO         : ChiavePagamento,
                    // DataIns                : Riga[20]  || "",
                    // Agente                 : Riga[21]  || "",
                    // EscluddalleListe       : (Riga[22] || "").toString().trim().toLowerCase() == 'True' ? true : false,
                    // Listino                : Riga[23]  || ""
                  }

            DatiCliente.push(Cliente)
          }

          var ObjQuery = { Operazioni : [] }

          DatiCliente.forEach(function(Cliente)
                              {
                                ObjQuery.Operazioni.push({
                                                            Query     : "InserisciClientiTramiteXLS",
                                                            Parametri:  {
                                                                          CHIAVE                 : undefined,
                                                                          CODICE         : TSchedaGenerica.PrepareForRecordString(Cliente.CODICE),
                                                                          RAGIONE_SOCIALE        : TSchedaGenerica.PrepareForRecordString(Cliente.RAGIONE_SOCIALE),
                                                                          COMUNE_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(Cliente.COMUNE_FATTURAZIONE),
                                                                          INDIRIZZO_FATTURAZIONE : TSchedaGenerica.PrepareForRecordString(Cliente.INDIRIZZO_FATTURAZIONE),
                                                                          NR_CIVICO_FATTURAZIONE : TSchedaGenerica.PrepareForRecordString(Cliente.NR_CIVICO_FATTURAZIONE),
                                                                          PROVINCIA_FATTURAZIONE : TSchedaGenerica.PrepareForRecordInteger(SystemInformation.GetChiaveProvincia(Cliente.PROVINCIA_FATTURAZIONE)),
                                                                          CAP_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(Cliente.CAP_FATTURAZIONE),
                                                                          ZONA_FATTURAZIONE      : TSchedaGenerica.PrepareForRecordInteger(Cliente.ZONA_FATTURAZIONE),
                                                                          PARTITA_IVA            : TSchedaGenerica.PrepareForRecordString(Cliente.PARTITA_IVA),
                                                                          CODICE_FISCALE         : TSchedaGenerica.PrepareForRecordString(Cliente.CODICE_FISCALE),
                                                                          COND_PAGAMENTO         : TSchedaGenerica.PrepareForRecordInteger(Cliente.COND_PAGAMENTO),
                                                                          NAZIONE_EM_PIVA        : 110,
                                                                          IVA_SUGGERITA_CLIENTE  : 220,
                                                                        },
                                                            ResetKeys : [1]
                                                          })

                                ObjQuery.Operazioni.push({
                                                            Query     : "InserisciRecapitiTramiteXLS",
                                                            Parametri:  {
                                                                          TELEFONO : TSchedaGenerica.PrepareForRecordString(Cliente.TELEFONO),
                                                                          EMAIL    : TSchedaGenerica.PrepareForRecordString(Cliente.EMAIL),
                                                                        },
                                                            ResetKeys : [2]
                                                          })
                                
                                if(Cliente.CELLULARE != '')
                                {
                                  ObjQuery.Operazioni.push({
                                                            Query     : "InserisciRecapitiTramiteXLS",
                                                            Parametri:  {
                                                                          TELEFONO : TSchedaGenerica.PrepareForRecordString(Cliente.CELLULARE),
                                                                        },
                                                            ResetKeys : [2]
                                                          })
                                }
                              })

          SystemInformation.AdvQuery.PostSQL('Clienti',ObjQuery,function()
                                            {
                                              alert('Dati caricati correttamente')
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                            })


        }
      
        reader.readAsArrayBuffer(FileExcelSelezionato)
      },

      BloccaTimer()
      {
        if(this.Timer != null)
        {
          this.PopupAttesaCalcolo = false
          this.Secondi = 0
          clearInterval(this.Timer); // Ferma il timer
        }
      },

      FatturaDaAssegnare()
      {
        this.FilterFattura.Numerazione = "NumerazioneDaAssegnare"
      },

      AnnullaFatturaDaAssegnare()
      {
        if(this.FilterFattura.Numerazione != "NumerazioneDaAssegnare")
          this.FilterFattura.AvvisiDaFatturare = false
      },

      OnClickSelezionaFileExcelClienti()
      {
        this.$refs.FileUploadExcelClienti.click();
      },

      ConfermaEliminazioneAvvisoFatturePassive()
      {
        this.PopupAnnullamentoAvvisoFatture = false
        this.PopupComunicazioni = false
        var Self = this
        var ObjQuery = { Operazioni : [] };
        ObjQuery.Operazioni.push({
                                  Query     : "ModificaAvvisoFatturePassive",
                                  Parametri : {  }
                                  })

        SystemInformation.AdvQuery.PostSQL('Impostazioni',ObjQuery,function()
        {
          Self.ControlloPresenzaComunicazioni()
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
        });
        
      },

      EliminaAvvisoFatturePassive()
      {
        this.PopupAnnullamento = false
        this.PopupAnnullamentoAvvisoFatture = true
        this.PopupComunicazioni = false
      },

      OnNextMenuNuovo(AMenu) 
      {
          if (AMenu.SubMenu != undefined)
              this.MenuNuovo = AMenu.SubMenu;
          else 
          {
              AMenu.OnClick();
              this.MenuNuovo = [];
          }
      },

      ConfermaAnnoStampaRitenuteClienti()
      {
        let Parametri = this.FilterCliente.GetParametriXCliente()
        Parametri.AnnoRitenuta = this.AnnoStampaRitenuta == ''? -1 : this.AnnoStampaRitenuta

        if(this.FiltroStampaRitenute != 'Qualsiasi')
        {
          if(this.FiltroStampaRitenute == 'Pagate')
            Parametri.CercaRitenutePagate = true;
          if(this.FiltroStampaRitenute == 'NonPagate')
            Parametri.CercaRitenutePagate = false;
        }

        var ModelloStampaPHP = ''

        if(SystemInformation.AccessRights.VisibilitaDettaglioRitenute())
        {
          ModelloStampaPHP = 'StampaResocontoRitenutaClientiDettaglio'
        }
        else
        {
          ModelloStampaPHP = 'StampaResocontoRitenutaClienti'
        }

        var Self = this
        this.AnnoStampaRitenutaClienti = false
        this.PopupAttesaCalcolo = true

        SystemInformation.AdvQuery.ExecuteExternalScript(ModelloStampaPHP, Parametri,
                                                function(Result)
                                                {  
                                                  Self.PopupAttesaCalcolo = false
                                                  
                                                  if(Result.PDF != undefined)
                                                  {
                                                    var routeData = Self.$router.resolve({ name   : "IFrameStampe" });
                                                    var AWindow = window.open(routeData.href, "_blank");
                                                    AWindow.BodyPDF = ( 'data:application/pdf;base64,' + Result.PDF);
                                                  }
                                                  else SystemInformation.HandleError('Documento non presente','','');
                                                },
                                                function(HTTPError,SubHTTPError,Response)
                                                {
                                                  Self.PopupAttesaCalcolo = false
                                                  SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                })  
       
      },

      // OnClickNuovoClienteRapido()
      // {
      //   this.SchedaSelezionata = new TSchedaCliente(SystemInformation.AdvQuery);
      //   this.SchedaSelezionata.Nuovo();
      // },

      OnClickNuovoFornitoreRapido()
      {
        this.SchedaSelezionata = new TSchedaFornitore(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
      },

      OnClickNuovoProdottoRapido()
      {
        this.SchedaSelezionata = new TSchedaProdotto(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
      },

      OnClickNuovoDDTEntranteRapido()
      {
        this.SchedaSelezionata = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
      },

      OnClickNuovoDDTRapido()
      {
        this.SchedaSelezionata = new TSchedaDocumentoDiTrasporto(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
      },

      OnClickNuovoDocScaricoProdCompostiRapido()
      {
        this.SchedaSelezionata = new TSchedaDocScaricoProdottiComposti(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
      },

      OnClickNuovoMovimentoMagazzinoRapido()
      {
        this.SchedaSelezionata = new TSchedaMovimentiMagazzini(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
      },  

      // OnClickNuovoMovimentoFromFattureInsolute(Dati)
      // {
      //   this.SchedaSelezionata = new TSchedaMovimento(SystemInformation.AdvQuery);
      //   this.SchedaSelezionata.Nuovo();
      //   this.SchedaSelezionata.AssignDati(Dati)
      // },


      // OnClickNuovoPreventivoMultiRapido()
      // {
      //   this.SchedaSelezionata = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
      //   this.SchedaSelezionata.Nuovo();
      // },

      // OnClickNuovoPreventivoRapido()
      // {
      //   this.SchedaSelezionata = new TSchedaPreventivo(SystemInformation.AdvQuery);
      //   this.SchedaSelezionata.Nuovo();
      // },

      // OnClickNuovaFatturaDaBancoRapido()
      // {
      //   this.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
      //   this.SchedaSelezionata.Nuovo();
      //   this.SchedaSelezionata.FatturaDaBanco()
      // },

      // OnClickNuovoMovimentoRapido()
      // {
      //   this.SchedaSelezionata = new TSchedaMovimento(SystemInformation.AdvQuery);
      //   this.SchedaSelezionata.Nuovo();
      // },

      // OnClickNuovaFatturaRapido()
      // {
      //   this.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
      //   this.SchedaSelezionata.Nuovo();
      // },

      KeyDownF2(event)
      {
        if(!this.SchedaSelezionata.DatiModificati())
          if(event.which == 113) 
            this.FiltraDati();
      },

      OnClickRadioFiltroRitenuta()
      {
        this.FilterCliente.AnnoRitenuta = (new Date()).getFullYear()
      },

      OnClickGeneraNotaDiCredito(SchedaFattura)
      {
        this.SchedaSelezionata = new TSchedaNotaDiCredito(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaFattura(SchedaFattura);
      },

      CreazioneDocumentiTramitePreventivo(SchedaPreventivo)
      {
        this.PreventivoPerConferma = SchedaPreventivo;
      },

      OnChangeNodiAlbero()
      {
        let NodeSelected     = this.TreeView.GetSelected();
        NodeSelected.Caption = this.SchedaSelezionata.GetDescrizione();
      },

      OnClickEspandiRicercheDDT()
      {
        this.EspandiRicercheDDT = this.EspandiRicercheDDT? false : true
      },

      OnClickEspandiRicercheFattura()
      {
        this.EspandiRicercheFattura = this.EspandiRicercheFattura? false : true
      },

      OnClickEspandiRicercheCliente()
      {
        this.EspandiRicercheCliente = !this.EspandiRicercheCliente
      },

      OnClickEspandiRicerchePreventivo()
      {
        this.EspandiRicerchePreventivo = this.EspandiRicerchePreventivo? false : true
      },

      OnClickEspandiRicercheConferme()
      {
        this.EspandiRicercheConferme = !this.EspandiRicercheConferme
      },

      OnClickEspandiRicercheNota()
      {
        this.EspandiRicercheNota = this.EspandiRicercheNota? false : true
      },

      OnClickEspandiRicercheFatturaPassiva()
      {
        this.EspandiRicercheFatturaPassiva = this.EspandiRicercheFatturaPassiva? false : true
      },

      OnClickEspandiRicercheNotaDiCreditoPassiva()
      {
        this.EspandiRicercheNotaDiCreditoPassiva = this.EspandiRicercheNotaDiCreditoPassiva? false : true
      },

      OnChangeStatoFilterFatture()
      {
        if(this.FilterFattura.Stato == 'Qualsiasi')
        {
          this.FilterFattura.IncassataDal = ''
          this.FilterFattura.IncassataAl  = ''
        }
      },

      OnAnnullaFatturaDaConferma()
      {
        this.PopupFatturaPerConferma = false
      },

      OnClickPopupProdottiExcel()
      {
        this.PopupProdottiExcel        = true
        this.PopupFiltroMagazzinoTipo  = "Tutti"
        this.PopupMagazzinoSelezionato = -1
      },

      OnClickPopupProdottiExcelTutti()
      {
        this.PopupMagazzinoSelezionato = -1
      },

      EsportaProdottiExcel()
      {
        this.PopupProdottiExcel = false
        this.PopupAttesaCalcolo = true
        var Self = this
        let Parametri = this.FilterProdotto.GetParametriXProdotto()
        if(this.PopupMagazzinoSelezionato != -1)
          Parametri.IdMagazzino = this.PopupMagazzinoSelezionato

        SystemInformation.AdvQuery.GetSQL('Magazzino', Parametri,
                                                          function(Results)
                                                          {
                                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ExcelListaProdotti")
                                                            if(ArrayInfo != undefined)
                                                            {

                                                              var TotalePrezzi = 0

                                                              const wb = XLSX.utils.book_new();
                                                              let ArrayContenitore = []
                                                              let Row = [
                                                                          { v: "Codice",                t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                                                                          { v: "Descrizione",           t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                                                                          { v: "Settore",               t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "Magazzino",             t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "Qnt. magazzino",        t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "Soglia di allarme",     t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "Prezzo Ultimo Acquisto",t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                        ]

                                                              ArrayContenitore.push(Row)

                                                              ArrayInfo.forEach(function(ARecord)
                                                              {
                                                                let RowProdotto = 
                                                                [
                                                                  { v: ARecord.CODICE,               t: "s", s: {font: {bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                  { v: ARecord.DESCRIZIONE,          t: "s", s: {font: {bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                  { v: ARecord.SETTORE,              t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                  { v: ARecord.DESCRIZIONE_MAGAZZINO || "", t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                  { v: (TSchedaGenerica.DisponiFromInteger(ARecord.QUANTITA_MAGAZZINO) / 100),  t: "n", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                  { v: (ARecord.SOGLIA_ALLARME / 100),     t: "n", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                  { v: (TSchedaGenerica.DisponiFromInteger(ARecord.PREZZO_ULTIMO_ACQUISTO) / 100), t: "n", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                ]

                                                                ArrayContenitore.push(RowProdotto)

                                                                // 🔹 Calcolo corretto del totale
                                                                TotalePrezzi += (TSchedaGenerica.DisponiFromInteger(ARecord.PREZZO_ULTIMO_ACQUISTO) * (TSchedaGenerica.DisponiFromInteger(ARecord.QUANTITA_MAGAZZINO))) / 100
                                                              })


                                                              let RigheVuote = [
                                                                          { v: "", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                                                                          { v: "", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                                                                          { v: "", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          { v: "", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                        ]
                                                              
                                                              let Spazio = 2

                                                              for(let Index = 0; Index < Spazio; Index++)
                                                              {
                                                                ArrayContenitore.push(RigheVuote)
                                                              }

                                                              let Totale = [
                                                                              { v: "",        t: "s", s: { font:{ bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                              { v: "",        t: "s", s: {font: {bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                              { v: "",        t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                              { v: "",        t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                              { v: "",        t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                                                                              { v: "TOTALE",  t: "s", s: {font: {bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                              { v: '€ ' + ((TotalePrezzi / 100).toLocaleString('it-IT')), t: "s", s: {font: {bold: true, name: 'Liberation Sans', sz: 10  }}},
                                                                          ]
                                                              
                                                              ArrayContenitore.push(Totale)

                                                              // STEP 3: Create worksheet with rows; Add worksheet to workbook
                                                              const ws = XLSX.utils.aoa_to_sheet(ArrayContenitore);
                                                              XLSX.utils.book_append_sheet(wb, ws, "readme demo");

                                                              var wscols = [
                                                                              {wch:25},   // Codice
                                                                              {wch:80},   // Descrizione
                                                                              {wch:20},   // Settore
                                                                              {wch:30},   // Magazzino
                                                                              {wch:15},   // Qnt
                                                                              {wch:15},   // Soglia
                                                                              {wch:20},   // Prezzo
                                                                          ];
                                                                            
                                                              ws['!cols'] = wscols;

                                                              // STEP 4: Write Excel file to browser
                                                              XLSX.writeFile(wb, "EsportazioneProdotti.xlsx");

                                                              Self.PopupAttesaCalcolo = false
                                                            }
                                                            else 
                                                            {
                                                              SystemInformation.HandleError('Impossibile ottenere la lista dei prodotti da esportare');
                                                              Self.PopupAttesaCalcolo = false
                                                            }
                                                          },
                                                          function(HTTPError,SubHTTPError,Response)
                                                          {
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                            Self.PopupAttesaCalcolo = false
                                                          },
                                                          'ExcelListaProdotti')
      },


      ControlloPresenza(Valore)
      {
        let Result = "Non Presente";

        if(Valore != undefined && Valore != '')
          Result = Valore;

        return Result
      },

      ControlloFrequenza(Valore)
      {
        let Result = "Non Presente";

        if(Valore != undefined && Valore != '' && Valore != 0)
        {
          switch(Valore)
          {
            case 1  :
            case 7  : Result = 'Gen - Lug'; break;
            case 2  :
            case 8  : Result = 'Feb - Ago'; break;
            case 3  :
            case 9  : Result = 'Mar - Sett'; break;
            case 4  :
            case 10 : Result = 'Apr - Ott'; break;
            case 5  :
            case 11 : Result = 'Mag - Nov'; break;
            case 6  :
            case 12 : Result = 'Giu - Dic'; break;
          }
        }
        return Result
      },

      FiltraNodoFatturaDaMostrare(OnSuccess)
      {
        let Self = this
        this.BloccaWatchFiltraDati = true
        this.SchedaSelezionata.GetFiltriAbilitati(function(FiltriAbilitati)
        {
          var FiltroSelezionato = FiltriAbilitati.find(function(Filtro)
          {
            return Self.CurrentFilter.GetFilterId() == Filtro.Name
          }) 

          if(FiltroSelezionato == undefined)
          {
            console.log('sss')
            let SubMenuTrovato
            let MenuFilterTrovato = Self.MenuFilter.find(function(AMenu)
                                    {
                                      if(AMenu.Caption != '')
                                      {
                                        if(AMenu.SubMenu != null)
                                        {
                                          let Trovato = AMenu.SubMenu.find(function(ASubMenu)
                                          {
                                            if(ASubMenu.Caption != '')
                                              return ASubMenu.Filter.GetFilterId() == FiltriAbilitati[0].Name                    
                                          })
                                          if(Trovato != undefined)
                                          {
                                            SubMenuTrovato = Trovato
                                            return true
                                          }
                                        }
                                        else return AMenu.Filter.GetFilterId() == FiltriAbilitati[0].Name
                                      }
                                    })

            if(SubMenuTrovato != undefined)
              Self.CurrentFilter = SubMenuTrovato.Filter
            else Self.CurrentFilter = MenuFilterTrovato.Filter

            FiltroSelezionato  = FiltriAbilitati[0]
          }

          if(!Self.SchedaSelezionata.DisableClearFilter)
            Self.ClearFiltriPerNome(FiltroSelezionato.Name)

          if(Self.SchedaSelezionata.Dati.StatoPreventivoIniziale != undefined)
            Self.StatoPreventivoIniziale = Self.SchedaSelezionata.Dati.StatoPreventivoIniziale
          else 
            Self.StatoPreventivoIniziale = 'NuovoPreventivo'

          if(!Self.DeveloperMode)
            Self.ControlloPresenzaComunicazioni()

          setTimeout(function() 
          {
            Self.FiltraDati(FiltroSelezionato.Positions,function()
            {
              setTimeout(function()
              {
                Self.BloccaWatchFiltraDati = false
                Self.VaiAlNodoSelezionato()
                
                if(Self.SchedaSelezionata.GetClassName() == 'TSchedaPreventivo' &&
                  !Self.SchedaSelezionata.Dati.PROGRAMMA_PROSSIMA_VISITA)
                    Self.SchedaSelezionata.ApriPopupGeneraFattura()

                if(OnSuccess != null)
                  OnSuccess()
              }, 1000)
            })
          }, 50)
        })
      },

      onClickCaricaXml()
      {
        this.$refs.SelezioneFile.click()      
      },

      onChangeCaricaXml(event)
      {
        let Parametri = {Body:'', File:''}
        let FileSelezionato = event.target.files[0];
        if (FileSelezionato)
        {
            let Reader = new FileReader()
            Reader.onload = (e) => {
            Parametri.Body = e.target.result
            Parametri.File = FileSelezionato.name
            let Self = this
            SystemInformation.AdvQuery.ExecuteExternalScript('UploadFatturePassive',Parametri, function(Result)
                                      {
                                        if(Result != undefined)
                                        SystemInformation.GetConfigurazioni(function()
                                        {
                                          alert('Operazione effettuata')
                                          Self.SchedaSelezionata = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                          Self.SchedaSelezionata.Chiave = Result.ChiaveFattura
                                          Self.SchedaSelezionata.Disponi(function()
                                          {
                                            Self.FiltraNodoFatturaDaMostrare()
                                          })
                                        })
                                      },
                                      function(HTTPError,SubHTTPError,Response)
                                      {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                      })
            }

            Reader.readAsText(FileSelezionato)
        }
      },

      OnClickStampaRibaFatture()
      {
        let Self = this
        this.PopupConfermaStampaRibaFatture = false
        let Parametri = Self.FilterFattura.GetParametriXFattura()
        Parametri.MostraSoloRateNonPagate = this.MostraSoloRateNonPagate
        
        SystemInformation.AdvQuery.ExecuteExternalScript('StampaResocontoRiBa', Parametri,
                                                          function(Result)
                                                          {  
                                                            if(Result.PDF != undefined)
                                                            {
                                                              var routeData = Self.$router.resolve({
                                                                            name   : "IFrameStampe"
                                                              });
                                                              var AWindow = window.open(routeData.href, "_blank");
                                                              AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                            }
                                                            else SystemInformation.HandleError('Documento non presente','','');
                                                          },
                                                          function(HTTPError,SubHTTPError,Response)
                                                          {
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                          }) 
      },

      OnClickStampaXML()
      {
        if(this.DropdownMenuXML.length == 0)
        {
          let Self = this
          this.MenuFilterVisible = false
          this.MenuFilterRicercheVisible = false
          this.MeseSelezionatoPerFilialeAttrezzature = -1
          this.MeseFiltroPerXML = []
          this.MenuNuovo  = []
          this.MenuStampa = []
          this.DropdownMenuXML = [
                              {
                                Caption: "Esportazione cliente",
                                OnClick: function () 
                                {
                                  Self.OnClickEsportaClientiExcel() 
                                }
                              }
                            ]
        }
        else this.DropdownMenuXML = [];
      },

      OnClickStampa(IsFatture, IsConferma, IsPreventivi, IsNote, IsCliente, IsProdotti, IsFornitore)
      {

          var Self = this;
          if (this.MenuStampa.length == 0)
          { 
              this.DropdownMenuXML   = []
              this.MenuFilterVisible = false
              this.MenuFilterRicercheVisible = false
              this.MenuNuovo = []
              if(IsFatture)
              {
                this.MenuStampa = [
                    {
                      Caption: "Totali",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaResocontoFatture', Self.FilterFattura.GetParametriXFattura(),
                                                    function(Result)
                                                    {
                                                      if(Result.PDF != undefined)
                                                      {
                                                        var routeData = Self.$router.resolve({
                                                                      name   : "IFrameStampe"
                                                        });
                                                        var AWindow = window.open(routeData.href, "_blank");
                                                        AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                      }
                                                      else SystemInformation.HandleError('Documento non presente','','');
                                                    },
                                                    function(HTTPError,SubHTTPError,Response)
                                                    {
                                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                    })  
                      }
                    },
                    {
                      Caption: "Ritenute",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaResocontoRitenutaFatture', Self.FilterFattura.GetParametriXFattura(),
                                                    function(Result)
                                                    {
                                                      if(Result.PDF != undefined)
                                                      {
                                                        var routeData = Self.$router.resolve({
                                                                      name   : "IFrameStampe"
                                                        });
                                                        var AWindow = window.open(routeData.href, "_blank");
                                                        AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                      }
                                                      else SystemInformation.HandleError('Documento non presente','','');
                                                    },
                                                    function(HTTPError,SubHTTPError,Response)
                                                    {
                                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                    })  
                      }
                    },
                    {
                      Caption: "Fatture",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaFattura', Self.FilterFattura.GetParametriXFattura(),
                                                                            function(Result)
                                                                            {  
                                                                              if(Result.PDF != undefined)
                                                                              {
                                                                                var routeData = Self.$router.resolve({
                                                                                              name   : "IFrameStampe"
                                                                                });
                                                                                var AWindow = window.open(routeData.href, "_blank");
                                                                                AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                                              }
                                                                              else SystemInformation.HandleError('Documento non presente','','');
                                                                            },
                                                                            function(HTTPError,SubHTTPError,Response)
                                                                            {
                                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                            })  
                      }
                    },
                    {
                      Caption: "Registro cassa",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaRegistroCassaFatture', Self.FilterFattura.GetParametriXFattura(),
                                                                            function(Result)
                                                                            {  
                                                                              if(Result.PDF != undefined)
                                                                              {
                                                                                var routeData = Self.$router.resolve({
                                                                                              name   : "IFrameStampe"
                                                                                });
                                                                                var AWindow = window.open(routeData.href, "_blank");
                                                                                AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                                              }
                                                                              else SystemInformation.HandleError('Documento non presente','','');
                                                                            },
                                                                            function(HTTPError,SubHTTPError,Response)
                                                                            {
                                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                            })  
                      }
                    },
                    {
                      Caption: "Ri.Ba.",
                      OnClick: function () 
                      {
                        Self.PopupConfermaStampaRibaFatture = true
                        Self.MostraSoloRateNonPagate = true
                      }
                    }
                  ]
              }
              if(IsConferma)
              {
                this.MenuStampa = [
                    {
                      Caption: "Resoconto",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaResocontoConferme', Self.FilterConferma.GetParametriXConferme(),
                                                                            function(Result)
                                                                            {  
                                                                              if(Result.PDF != undefined)
                                                                              {
                                                                                var routeData = Self.$router.resolve({
                                                                                              name   : "IFrameStampe"
                                                                                });
                                                                                var AWindow = window.open(routeData.href, "_blank");
                                                                                AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                                              }
                                                                              else SystemInformation.HandleError('Documento non presente','','');
                                                                            },
                                                                            function(HTTPError,SubHTTPError,Response)
                                                                            {
                                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                            }) 
                      }                          
                      
                    },
                    {
                      Caption: "Conferme",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaConferma', Self.FilterConferma.GetParametriXConferme(),
                                                                            function(Result)
                                                                            {  
                                                                              if(Result.PDF != undefined)
                                                                              {
                                                                                var routeData = Self.$router.resolve({
                                                                                              name   : "IFrameStampe"
                                                                                });
                                                                                var AWindow = window.open(routeData.href, "_blank");
                                                                                AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                                              }
                                                                              else SystemInformation.HandleError('Documento non presente','','');
                                                                            },
                                                                            function(HTTPError,SubHTTPError,Response)
                                                                            {
                                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                            }) 
                      }
                    }
                  ]
              }
              if(IsPreventivi)
              {
                this.MenuStampa = 
                [
                    {
                      Caption: "Preventivi",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaPreventivo', Self.FilterPreventivo.GetParametriXPreventivo(),
                                                                            function(Result)
                                                                            {  
                                                                              if(Result.PDF != undefined)
                                                                              {
                                                                                var routeData = Self.$router.resolve({
                                                                                              name   : "IFrameStampe"
                                                                                });
                                                                                var AWindow = window.open(routeData.href, "_blank");
                                                                                AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                                              }
                                                                              else SystemInformation.HandleError('Documento non presente','','');
                                                                            },
                                                                            function(HTTPError,SubHTTPError,Response)
                                                                            {
                                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                            }) 
                      }
                    }
                  ]
              }
              if(IsNote)
              {
                this.MenuStampa = [
                    {
                      Caption: "Resoconto",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaResocontoNote', Self.FilterNota.GetParametriXNote(),
                                                    function(Result)
                                                    {  
                                                      if(Result.PDF != undefined)
                                                      {
                                                        var routeData = Self.$router.resolve({
                                                                      name   : "IFrameStampe"
                                                        });
                                                        var AWindow = window.open(routeData.href, "_blank");
                                                        AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                      }
                                                      else SystemInformation.HandleError('Documento non presente','','');
                                                    },
                                                    function(HTTPError,SubHTTPError,Response)
                                                    {
                                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                    }) 
                      }                          
                      
                    },
                    {
                      Caption: "Note di credito",
                      OnClick: function () 
                      {
                          SystemInformation.AdvQuery.ExecuteExternalScript('StampaNotaDiCredito', Self.FilterNota.GetParametriXNote(),
                                                                            function(Result)
                                                                            {  
                                                                              if(Result.PDF != undefined)
                                                                              {
                                                                                var routeData = Self.$router.resolve({
                                                                                              name   : "IFrameStampe"
                                                                                });
                                                                                var AWindow = window.open(routeData.href, "_blank");
                                                                                AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                                              }
                                                                              else SystemInformation.HandleError('Documento non presente','','');
                                                                            },
                                                                            function(HTTPError,SubHTTPError,Response)
                                                                            {
                                                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                            })
                      }
                    }
                  ]
              }
              if(IsCliente)
              { 
                this.MenuStampa = 
                [
                    {
                      Caption: "Ritenute",
                      OnClick: function () 
                      {
                        Self.AnnoStampaRitenutaClienti = true
                        Self.FiltroStampaRitenute      = Self.FilterCliente.Ritenute
                        Self.AnnoStampaRitenuta        = Self.FilterCliente.AnnoRitenuta
                      }
                    },
                    {
                      Caption: "Saldi conti",
                      OnClick: function () 
                      {  
                          if(!Self.DataSaldoClienti)
                          {
                            let date = new Date() 
                            let y = date.getFullYear()
                            let m = date.getMonth()
                            Self.SaldoClientiAl = TZDateFunct.DateInHTMLInputFormat(new Date(y, m + 1, 0));
                            Self.DataSaldoClienti = true
                          }
                          else Self.DataSaldoClienti = false 
                      } 
                      }
                  ]
              }
              if(IsFornitore)
              { 
                this.MenuStampa = 
                [
                    {
                      Caption: "Saldi conti",
                      OnClick: function () 
                      {  
                          if(!Self.DataSaldoFornitori)
                          {
                            let date = new Date() 
                            let y = date.getFullYear()
                            let m = date.getMonth()
                            Self.SaldoFornitoriAl = TZDateFunct.DateInHTMLInputFormat(new Date(y, m + 1, 0));
                            Self.DataSaldoFornitori = true
                          }
                          else Self.DataSaldoFornitori = false 
                      } 
                      }
                  ]
              }
              if(IsProdotti)
              {
                this.MenuStampa = [
                    {
                      Caption: "Prodotti",
                      OnClick: function()
                      {
                        SystemInformation.AdvQuery.ExecuteExternalScript('StampaProdotti', Self.FilterProdotto.GetParametriXProdotto(),
                                                                          function(Result)
                                                                          {
                                                                            if(Result.PDF != undefined)
                                                                            {
                                                                              var routeData = Self.$router.resolve({
                                                                                              name   : "IFrameStampe"
                                                                              });
                                                                              var AWindow = window.open(routeData.href, "_blank");
                                                                              AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);
                                                                            }
                                                                            else SystemInformation.HandleError('Documento non presente','','');
                                                                          },
                                                                          function(HTTPError,SubHTTPError,Response)
                                                                          {
                                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                          })                      
                      }
                    }
                ]
              }
          }
          else this.MenuStampa = [];

      },

       OnClickEsportaClientiExcel()
      {
        this.PopupAttesaCalcolo = true
        var Self = this
        SystemInformation.AdvQuery.ExecuteExternalScript('EsportazioneClientiExcel',
        this.FilterCliente.GetParametriXCliente(),
        function(Results)
        {
          let ArrayInfo = Results.ListaClienti
          if(ArrayInfo != undefined)
          {
            const wb = XLSX.utils.book_new();
            let ArrayContenitore = []
            let Row = 
              [
                { v: "Regione", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                { v: "Provincia", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10, Width: 10 }}},
                { v: "Comune", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Indirizzo", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Nr. civico", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "CAP", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Clienti", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                { v: "Categoria", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
              ];

            ArrayContenitore.push(Row)

            let RowDivisore = 
            [
              { v: '', t: "s", s: {font: {name: 'Liberation Sans', sz: 10 }}},
            ];

            let LastRegione   = -1
            ArrayInfo.forEach(function(ARecord)
            {
                if(LastRegione != -1 && LastRegione != ARecord.REGIONE)
                {
                  ArrayContenitore.push(RowDivisore)
                  ArrayContenitore.push(RowDivisore)
                }
                let RowRegione = 
                [
                  { v: ARecord.REGIONE, t: "s", s: {font: {bold: true,name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.PROVINCIA, t: "s", s: {font: {bold: true,name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.COMUNE, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.INDIRIZZO, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.NR_CIVICO, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.CAP, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.RAGIONE_SOCIALE, t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                  { v: ARecord.CATEGORIA != null? ARecord.CATEGORIA : "PRIVATO", t: "s", s: {font: {name: 'Liberation Sans', sz: 10  }}},
                ];

                ArrayContenitore.push(RowRegione)
                LastRegione   = ARecord.REGIONE

            });
            ArrayContenitore.push([
                                    { v: '', t: "s", s: {font: {name: 'Liberation Sans', sz: 10 }}},
                                 ])


            let ArrayTotali = [
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10 }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: '', t: "s", s: {name: 'Liberation Sans', sz: 10  }},
                                    { v: "Totale:", t: "s", s: { font: { bold: true, name: 'Liberation Sans', sz: 10  }}},
                               ]
            
            ArrayContenitore.push(ArrayTotali)

            // STEP 3: Create worksheet with rows; Add worksheet to workbook
            const ws = XLSX.utils.aoa_to_sheet(ArrayContenitore);
            XLSX.utils.book_append_sheet(wb, ws, "readme demo");

            var wscols = [
                            {wch:12},
                            {wch:12},
                            {wch:25},
                            {wch:30},
                            {wch:12},
                            {wch:12},
                            {wch:35},
                            {wch:20}
                          ];
                          
            ws['!cols'] = wscols;

            // STEP 4: Write Excel file to browser
            XLSX.writeFile(wb, "EsportazioneClienti.xlsx");
            Self.PopupAttesaCalcolo = false

          }
          else SystemInformation.HandleError('Impossibile ottenere la lista dei clienti da esportare');
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
          Self.PopupAttesaCalcolo = false
        },
        'EsportazioneFileExcel')

        // const wb = XLSX.utils.book_new();

        // // STEP 2: Create data rows and styles
        // let row = 
        // [
        //   { v: "Courier: 24", t: "s", s: { font: { name: "Courier", sz: 24 } } },
        //   { v: "bold & color", t: "s", s: { font: { bold: true, color: { rgb: "FF0000" } } } },
        //   { v: "fill: color", t: "s", s: { fill: { fgColor: { rgb: "E9E9E9" } } } },
        //   { v: "line\nbreak", t: "s", s: { alignment: { wrapText: true } } },
        // ];

        // // STEP 3: Create worksheet with rows; Add worksheet to workbook
        // const ws = XLSX.utils.aoa_to_sheet([row,['a']]);
        // XLSX.utils.book_append_sheet(wb, ws, "readme demo");

        // // STEP 4: Write Excel file to browser
        // XLSX.writeFile(wb, "xlsx-js-style-demo.xlsx");
      },

      OnClickNuovaFatturaDaSchedaCliente(SchedaCliente)
      {
        this.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaCliente(SchedaCliente);
      },
      OnClickNuovaFatturaDaBancoDaSchedaCliente(SchedaCliente)
      {
        this.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.FatturaDaBanco();
        this.SchedaSelezionata.AssignSchedaCliente(SchedaCliente);
      },
      OnClickNuovoPreventivoDaSchedaCliente(SchedaCliente)
      {
        this.SchedaSelezionata = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaCliente(SchedaCliente);
      },
      OnClickNuovoMovimentoDaSchedaCliente(SchedaCliente)
      { 
        this.SchedaSelezionata = new TSchedaMovimento(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaCliente(SchedaCliente);
      },
      OnClickNuovaNotaDiCreditoDaSchedaCliente(SchedaCliente)
      {
        this.SchedaSelezionata = new TSchedaNotaDiCredito(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaCliente(SchedaCliente);
      },

      OnClickNuovoDDTDaSchedaCliente(SchedaCliente)
      {
        this.SchedaSelezionata = new TSchedaDocumentoDiTrasporto(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaCliente(SchedaCliente);
      },

      OnClickNuovoDDTEntranteDaSchedaCliente(SchedaCliente)
      {
          this.SchedaSelezionata = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
          this.SchedaSelezionata.Nuovo();
          this.SchedaSelezionata.AssignSchedaCliente(SchedaCliente);
      },

      OnClickGoToPositionAlbero(Filtro)
      {

        var Self = this
        this.ClearFiltriPerNome(Filtro[0].Name)
        var FiltroSelezionato = Filtro.find(function(Filtro)
        {
          return Self.CurrentFilter.GetFilterId() == Filtro.Name
          
        }) 
        if(FiltroSelezionato == undefined)
        {
        Self.CurrentFilter = Self.MenuFilter.find(function(AMenu)
         {
           if(AMenu.Caption != '')
             return AMenu.Filter.GetFilterId() == Filtro[0].Name
         }).Filter
        FiltroSelezionato  = Filtro[0]
        }
        setTimeout(function() {
                                Self.FiltraDati(FiltroSelezionato.Positions,
                                function()
                                {
                                  setTimeout(function()
                                  {
                                    var TuttiDiv = document.querySelectorAll(".TreeNodeSelected");
                                    if(TuttiDiv.length != 0)
                                      TuttiDiv[0].scrollIntoView({ behavior: 'instant', block: 'center' })
                                  }, 1000)
                                })
                              },
                              50)
      },

   
      CreazioneFatturaTramitePreventivo(SchedaPreventivo, FatturaDaBanco)
      {
          this.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
          this.SchedaSelezionata.Nuovo();
          this.SchedaSelezionata.AssignSchedaFromPreventivo(SchedaPreventivo, FatturaDaBanco);
      },

      CreazioneConfermaTramitePreventivo(SchedaMultiPreventivo)
      {
        this.SchedaSelezionata = new TSchedaPreventivo(SystemInformation.AdvQuery)
        this.SchedaSelezionata.AssignPreventivoMultiparametrico(SchedaMultiPreventivo)
        this.ConfermaLanciata  = false
      },

      CreazioneDDTTramitePreventivo(SchedaPreventivo)
      {
        this.SchedaSelezionata = new TSchedaDocumentoDiTrasporto(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaFromPreventivo(SchedaPreventivo);
      },

      CreazioneDDTEntranteTramitePreventivo(SchedaPreventivo)
      {
          this.SchedaSelezionata = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
          this.SchedaSelezionata.Nuovo();
          this.SchedaSelezionata.AssignSchedaFromPreventivo(SchedaPreventivo);
      },

      CreazioneFatturaTramiteDDT(SchedaDDT)
      {
        this.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
        this.SchedaSelezionata.Nuovo();
        this.SchedaSelezionata.AssignSchedaFromDDT(SchedaDDT);
      },

      CreazioneFatturaTramiteDDTEntrante(SchedaDDT)
      {
          this.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
          this.SchedaSelezionata.Nuovo();
          this.SchedaSelezionata.AssignSchedaFromDDT(SchedaDDT);  
      },

      OnClickAlbero()
      {
        this.MenuNuovo          = []
        this.MenuFilterVisible  = false
        this.MenuFilterRicercheVisible = false
        this.MenuStampa         = []
        this.DropdownMenuXML    = []
      },

      // OnClickFunzNodo1(Risultato)
      // {
      //   this.SchedaSelezionata = Risultato
      //   if(this.SchedaSelezionata.GetClassName() == 'TSchedaContratto')
      //     if(this.SchedaSelezionata.CreatoDaNodiVuoti)
      //     {
      //       this.SchedaSelezionata.SaltaIniziale = this.FilterCliente.Cliente.trim().length > NUMERO_LETTERE_PER_RICERCA || 
      //                                               this.FilterCliente.CodiceCliente.trim() != ''
      //     }
      // },
      
      OnNextMenuFilter(AMenu) 
      {
          if (AMenu.SubMenu != undefined)
          { 
              this.MenuFilter = AMenu.SubMenu;
          }
          else 
          {
              this.CurrentFilter = AMenu.Filter;
              this.MenuFilterVisible = false;
              this.MenuFilterRicercheVisible = false;
          }
      },

      OnNextMenuFilterRicerche(AMenu)
      {
        if (AMenu.SubMenu != undefined)
        {
            this.MenuFilterRicerche = AMenu.SubMenu;
        }
        else 
        {
            this.CurrentFilter = AMenu.Filter;
            this.MenuFilterRicercheVisible = false;
        }
      },

      OnNextMenuStampa(AMenu) 
      {
        if (AMenu.SubMenu != undefined)
        {
            this.MenuStampa = AMenu.SubMenu;
        }
        else 
        {
            AMenu.OnClick();
            this.MenuStampa = [];
        }
      },

      OnNextMenuXML(AMenu) 
      {
        if (AMenu.SubMenu != undefined)
        {
            this.DropDownMenuXML = AMenu.SubMenu;
        }
        else 
        {
            AMenu.OnClick();
            this.DropDownMenuXML = [];
        }
      },

      ClearFiltriPerNome(NomeFiltro)
      {
        switch(NomeFiltro)
        {
          case DASHBOARD_FILTER_TYPES.Clienti        : this.FilterCliente.ClearFilterCliente()
                                                       break;
          case DASHBOARD_FILTER_TYPES.Fornitore      : this.FilterFornitore.ClearFilterFornitore()
                                                       break;
          default                                    : break;
        }
      },

      OnClickConferma()
      {
        let Self    = this;

        if(!this.ConfermaLanciata)
        {
          this.ConfermaLanciata = true
          let IsNuovo = this.SchedaSelezionata.IsNuovo()
          
          this.SchedaSelezionata.Registra(function(OnSuccess)
          {
            if(IsNuovo)
            {
              Self.ConfermaLanciata = false
              Self.FiltraNodoFatturaDaMostrare(OnSuccess)
            }
            else 
            {
              let NodeSelected          = Self.TreeView.GetSelected();
              NodeSelected.Caption      = Self.SchedaSelezionata.GetDescrizione();
              
              let TempSchedaSelezionata = Self.SchedaSelezionata;
              Self.SchedaSelezionata    = new TSchedaGenerica();
              TempSchedaSelezionata.Disponi();
              
              setTimeout(() =>
              {
                Self.ConfermaLanciata  = false
                Self.SchedaSelezionata = TempSchedaSelezionata
                Self.VaiAlNodoSelezionato()
                
                if(OnSuccess != undefined)
                  OnSuccess()
              }, 50)
            }
          },
          function(HTTPError,SubHTTPError,Response)
          {
            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
            Self.ConfermaLanciata = false
          })
        }
        this.PopupCreazioneFattura.Visualizza = false
        this.PopupCreazioneDDTEntrante.Visualizza  = false
      },

      VaiAlNodoSelezionato()
      {
        var TuttiDiv = document.querySelectorAll(".TreeNodeSelected");
        
        if(TuttiDiv.length != 0)
          TuttiDiv[0].scrollIntoView({ behavior: 'instant', block: 'center' })
      },

      OnClickAnnulla()
      {
        this.PopupAnnullamento = true   
      },

      ConfermaDataSaldoFornitori()
      {
        var Self = this
        var Parametri = {
                          DataAl        : this.SaldoFornitoriAl,
                          Ordinamento   : this.OrdinamentoSaldoFornitori == null? 'A' : this.OrdinamentoSaldoFornitori
                        }
        
        this.DataSaldoFornitori = false
        this.PopupAttesaCalcolo = true

        SystemInformation.AdvQuery.ExecuteExternalScript('StampaSaldiFornitori', Parametri,
                                                function(Result)
                                                {  
                                                    if(Result.PDF != undefined)
                                                  {
                                                    var routeData = Self.$router.resolve({
                                                                  name   : "IFrameStampe"
                                                    });
                                                    var AWindow = window.open(routeData.href, "_blank");
                                                    AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);

                                                    Self.SaldoFornitoriAl   = ''
                                                    Self.PopupAttesaCalcolo = false
                                                  }
                                                  else SystemInformation.HandleError('Documento non presente','','');
                                                },
                                                function(HTTPError,SubHTTPError,Response)
                                                {
                                                  SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                  Self.PopupAttesaCalcolo = false
                                                })
      },

      ConfermaDataSaldoClienti()
      {
        var Self      = this
        var Parametri = {
                          DataAl        : this.SaldoClientiAl,
                          Ordinamento   : this.OrdinamentoSaldoClienti == null? 'A' : this.OrdinamentoSaldoClienti
                        }
        
        this.DataSaldoClienti   = false
        this.PopupAttesaCalcolo = true

        SystemInformation.AdvQuery.ExecuteExternalScript('StampaSaldiClienti', Parametri,
                                                          function(Result)
                                                          {  
                                                            if(Result.PDF != undefined)
                                                            {
                                                              var routeData = Self.$router.resolve({
                                                                            name   : "IFrameStampe"
                                                              });
                                                              var AWindow = window.open(routeData.href, "_blank");
                                                              AWindow.BodyPDF = ('data:application/pdf;base64,' + Result.PDF);

                                                              Self.SaldoClientiAl   = ''
                                                              Self.PopupAttesaCalcolo = false
                                                            }
                                                            else SystemInformation.HandleError('Documento non presente','','');
                                                          },
                                                          function(HTTPError,SubHTTPError,Response)
                                                          {
                                                            Self.PopupAttesaCalcolo = false
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                          })
      },
      
      FiltraDati(GoToPosition,AfterEndFilter)
      { 

        var Self = this;

        if(Self.FilterFattura.Conformita != 'Tutte')
           Self.PopupAttesaCalcolo = true

        if(!Self.Ricercaincorso)
        {
          Self.Ricercaincorso = true
          Self.PopupAttesaCalcolo = false
          
          var AnnullaSelezioneAlbero = function()
          {
            Self.SchedaSelezionata = new TSchedaGenerica(SystemInformation.AdvQuery);
          }
          var OnEndFilter = function()
          {
              Self.FirstSearch = true;
              if(GoToPosition == undefined || GoToPosition.length == 0)
              {
                if(Self.TreeView.Children.length != 0)
                  Self.TreeView.Select(Self.TreeView.Children[0], AfterEndFilter);
              }
              else Self.TreeView.SearchNode(GoToPosition,AnnullaSelezioneAlbero, AfterEndFilter);

              Self.Ricercaincorso     = false;
              Self.PopupAttesaCalcolo = false;
          }
          var OnErrorEndFilter = function()
          {
              Self.Ricercaincorso = false
              Self.PopupAttesaCalcolo = false;
          }
          if(this.CurrentFilter != null)
          {
            if(this.CurrentFilter.GetFilterId() == DASHBOARD_FILTER_TYPES.PrimaNota || 
                this.CurrentFilter.GetFilterId() == DASHBOARD_FILTER_TYPES.Filiali ||
                this.CurrentFilter.GetFilterId() == DASHBOARD_FILTER_TYPES.Magazzini)
            {
                this.Ricercaincorso = false
            }
            else
              this.CurrentFilter.Apply(this, OnEndFilter, OnErrorEndFilter)
          }          
          else
          { 
            Self.TreeView.Clear()
            Self.Ricercaincorso = false 
            Self.PopupAttesaCalcolo = false;
          }
        }
      },

      ConfermaAnnullamento()
      {
        var NodoSelezionato = this.TreeView.GetSelected();
        
        if(this.SchedaSelezionata.IsNuovo())
        {
          if(NodoSelezionato != undefined)
            this.SchedaSelezionata = NodoSelezionato.Data;
          else
            this.SchedaSelezionata = new TSchedaGenerica();
        }
        else
        {
          NodoSelezionato.Caption   = this.SchedaSelezionata.GetDescrizione();

          let TempSchedaSelezionata = this.SchedaSelezionata;
          this.SchedaSelezionata    = new TSchedaGenerica();
          TempSchedaSelezionata.Disponi();
          
          setTimeout(() =>
          {
            this.SchedaSelezionata = TempSchedaSelezionata
            this.VaiAlNodoSelezionato()
          }, 50)
        }
          
        this.AnnullaAnnullamento();
        
      },

      AnnullaAnnullamento()
      {
        this.PopupAnnullamento                                   = false
        this.PopupAnnullamentoAvvisoFatture                      = false
        this.PopupAnnullamentoComunicazioniImportanti            = false
      },

      OnClickScaricoFileZip()
      {
        let Self = this;

        if (!Self.OperazioneLanciata)
        {
          Self.OperazioneLanciata = true;
          Self.OnClickAnnullaScaricoFileZip();
          Self.PopupAttesaCalcolo = true;

          SystemInformation.AdvQuery.ExecuteExternalScript('GetZipFattureAttiveENote',
                                                          {
                                                            DATA_INIZIO : Self.ScaricoFileZipDal,
                                                            DATA_FINE   : Self.ScaricoFileZipAl,
                                                          },
                                                          function(JSONAnswer)
                                                          {
                                                            saveAs(TZStringConvFunct.Base64AsBlob(JSONAnswer.FileZipInBase64), "FattureFatturePassiveENote.zip");
                                                            Self.PopupAttesaCalcolo = false;
                                                            Self.OperazioneLanciata = false;
                                                          },
                                                          function(HTTPError, SubHTTPError, Response)
                                                          {
                                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                            Self.PopupAttesaCalcolo = false;
                                                            Self.OperazioneLanciata = false;
                                                          })
        }
      },

      OnClickAnnullaScaricoFileZip()
      {
        this.PopupScaricoFileZip = false;
      },

      OnClickNuovo() 
      {
          var Self = this;
          if (this.MenuNuovo.length == 0)
          { 
              this.MenuFilterVisible = false
              this.MenuFilterRicercheVisible = false
              this.MenuStampa = []
              this.DropdownMenuXML   = []
              this.MenuNuovo = [
                  {
                      Caption: "Anagrafiche",
                      SubMenu: [
                          {
                              Caption: this.FilterCliente.GetDescrizione(),
                              OnClick: function () 
                              {
                                Self.SchedaSelezionata = new TSchedaCliente(SystemInformation.AdvQuery);
                                Self.SchedaSelezionata.Nuovo();
                              }
                          },
                          {
                              Caption: this.FilterFornitore.GetDescrizione(),
                              OnClick: function () 
                              {
                                      Self.SchedaSelezionata = new TSchedaFornitore(SystemInformation.AdvQuery);
                                      Self.SchedaSelezionata.Nuovo();
                              }
                          },
                      ]
                  },
                    {
                      Caption: "Contabilità",
                      SubMenu: [
                          
                          {
                            Caption: "Fatture",
                            SubMenu: [
                                {
                                    Caption: this.FilterFattura.GetDescrizione(),
                                    OnClick: function () 
                                    {
                                      Self.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
                                      Self.SchedaSelezionata.Nuovo();
                                    }
                                
                                },
                                {
                                    Caption: "Fattura da banco",
                                    OnClick: function () 
                                    {
                                      Self.SchedaSelezionata = new TSchedaFattura(SystemInformation.AdvQuery);
                                      Self.SchedaSelezionata.Nuovo();
                                      Self.SchedaSelezionata.FatturaDaBanco()
                                    }
                                
                                },
                            ]       
                          },
                          {
                              Caption: this.FilterFatturaPassiva.GetDescrizione(),                                
                              OnClick: function () 
                              {
                                Self.SchedaSelezionata = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                Self.SchedaSelezionata.Nuovo();
                              }
                          },
                          {
                              Caption: this.FilterNotaDiCreditoPassiva.GetDescrizione(),                                
                              OnClick: function () 
                              {
                                Self.SchedaSelezionata = new TSchedaFatturaPassiva(SystemInformation.AdvQuery);
                                Self.SchedaSelezionata.Nuovo();
                              }
                          },
                          {
                              Caption: this.FilterNota.GetDescrizione(),
                              OnClick: function ()
                              {
                                Self.SchedaSelezionata = new TSchedaNotaDiCredito(SystemInformation.AdvQuery);
                                Self.SchedaSelezionata.Nuovo(); 
                              }
                          },
                          {
                              Caption: this.FilterConferma.GetDescrizione(),
                              OnClick: function () 
                              {
                                Self.SchedaSelezionata = new TSchedaPreventivo(SystemInformation.AdvQuery);
                                Self.SchedaSelezionata.Nuovo();
                              }
                          },
                          {
                              Caption: "Preventivi",
                              OnClick: function () 
                              {
                                Self.SchedaSelezionata = new TSchedaPreventivoMultiparametrico(SystemInformation.AdvQuery);
                                Self.SchedaSelezionata.Nuovo();
                              }
                          },
                          {
                            Caption : ""
                          },
                          {
                              Caption: "Autofatture",
                              OnClick: function () 
                              {
                                Self.SchedaSelezionata = new TSchedaAutoFattura(SystemInformation.AdvQuery);
                                Self.SchedaSelezionata.Nuovo();
                              }
                          },
                      ]
                  },
                  {
                      Caption: "Movimenti/Conti",
                      SubMenu: [
                          {
                              Caption: this.FilterMovimento.GetDescrizione(),
                              OnClick: function () 
                              {
                                  Self.SchedaSelezionata = new TSchedaMovimento(SystemInformation.AdvQuery);
                                  Self.SchedaSelezionata.Nuovo();
                              }
                          },
                          {
                              Caption: 'Conti Correnti/Casse',
                              OnClick: function () 
                              {
                                  Self.SchedaSelezionata = new TSchedaContoCorrente(SystemInformation.AdvQuery);
                                  Self.SchedaSelezionata.Nuovo();
                              }
                          },
                          
                        ]
                  },
                  {
                      Caption: this.FilterProdotto.GetDescrizione(),
                      OnClick: function () 
                      {
                        Self.SchedaSelezionata = new TSchedaProdotto(SystemInformation.AdvQuery);
                        Self.SchedaSelezionata.Nuovo(); 
                      }
                  },  
              ]
                this.MenuNuovo.push({
                                      Caption: this.FilterMovimentiMagazzini.GetDescrizione(),
                                      OnClick: function () 
                                      {
                                        Self.SchedaSelezionata = new TSchedaMovimentiMagazzini(SystemInformation.AdvQuery);
                                        Self.SchedaSelezionata.Nuovo(); 
                                      }
                                    })

              this.MenuNuovo.push({
                                      Caption     : "Documenti di trasporto",
                                      SubMenu: [
                                                {
                                                  Caption     : this.FilterDDT.GetDescrizione(),
                                                  OnClick: function () 
                                                    {
                                                        Self.SchedaSelezionata = new TSchedaDocumentoDiTrasporto(SystemInformation.AdvQuery);
                                                        Self.SchedaSelezionata.Nuovo();
                                                    }
                                                },
                                                {
                                                  Caption     : this.FilterDDTEntranti.GetDescrizione(),
                                                  OnClick: function () 
                                                    {
                                                        Self.SchedaSelezionata = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
                                                        Self.SchedaSelezionata.Nuovo();
                                                    }
                                                },
                                                {
                                                  Caption     : this.FilterDocScaricoProdComposti.GetDescrizione(),
                                                  OnClick: function () 
                                                    {
                                                        Self.SchedaSelezionata = new TSchedaDocScaricoProdottiComposti(SystemInformation.AdvQuery);
                                                        Self.SchedaSelezionata.Nuovo();
                                                    }
                                                },
                                               ]
                                   })
          }
          else this.MenuNuovo = [];
      },

      OnClickRicercheFilter()
      {
            this.MenuNuovo          = []
            this.MenuStampa         = []
            this.DropdownMenuXML    = []
            this.MenuFilterVisible  = false
            this.MenuFilterRicercheVisible = !this.MenuFilterRicercheVisible
            this.MenuFilterRicerche        = this.MenuFilterPrincipaleRicerche
      },

      OnClickMainFilter() 
      { 
          this.AlberoNascosto             = false
          this.MenuNuovo                  = []
          this.MenuStampa                 = []
          this.DropdownMenuXML            = []
          this.MenuFilterRicercheVisible  = false
          this.MenuFilterVisible          = !this.MenuFilterVisible
          this.MenuFilter                 = this.MenuFilterPrincipale
      },

      OnClickApriPopupComunicazioni()
      {
        this.PopupComunicazioni = true
      },

      OnClickApriPopupLogApp()
      {
        this.PopupLogApp = true
      },

      ControlloPresenzaComunicazioni(OnSuccess)
      {
        this.PresenzaComunicazioni        = false
        this.ListaComunicazioniTemporaneo = []
        this.ListaComunicazioni           = []
        this.DownloadComunicazioni(OnSuccess)
      },

      DownloadComunicazioni(OnSuccess)
      {
        var Self = this
        SystemInformation.AdvQuery.ExecuteExternalScript('PopupComunicazioni', { IndexComunicazioni : this.IndexRichiestaComunicazioni },
                                                          function(Response)
                                                          {  
                                                            if(Response != undefined)
                                                            {
                                                              if(Response.ListaComunicazioni.length != 0)
                                                                Self.ListaComunicazioniTemporaneo = Self.ListaComunicazioniTemporaneo.concat(Response.ListaComunicazioni)
                                                              
                                                              if(Response.TerminatoDownload)
                                                              {
                                                                Self.ListaComunicazioni          = Self.ListaComunicazioniTemporaneo
                                                                if(Self.ListaComunicazioni.length != 0)
                                                                  Self.PresenzaComunicazioni       = true
                                                                Self.IndexRichiestaComunicazioni = 0

                                                                // let index = Self.ListaComunicazioni.findIndex(Comunicazione => Comunicazione.Tipo == Self.TipiComunicazioni.PresenzaRapportiNonConformita);

                                                                // if (index > -1) 
                                                                // {
                                                                //   let [oggetto] = Self.ListaComunicazioni.splice(index, 1);
                                                                //   Self.ListaComunicazioni.unshift(oggetto);
                                                                // }

                                                                if(OnSuccess != undefined)
                                                                  OnSuccess()
                                                              }
                                                              else 
                                                              {
                                                                Self.IndexRichiestaComunicazioni++
                                                                setTimeout(function() 
                                                                {
                                                                  Self.DownloadComunicazioni(OnSuccess)
                                                                }, 50)
                                                              }
                                                            }
                                                            else SystemInformation.HandleError('Impossibile ottenere le comunicazioni','','');
                                                          },
                                                          SystemInformation.HandleError)
      },


      OnClickVisualizzaScheda(Informazioni, TipoComunicazione)
      {
        if(!this.PopupVisualizzaScheda)
        {
          var Self = this
          switch(TipoComunicazione)
          {
            case TIPO_COMUNICAZIONI.FornitoriSenzaCodice :  this.SchedaSelezionataPopup        = new TSchedaFornitore(SystemInformation.AdvQuery);
                                                            this.SchedaSelezionataPopup.Chiave = Informazioni.ChiaveFornitore
                                                            this.SchedaSelezionataPopup.TipoComunicazione = TIPO_COMUNICAZIONI.FornitoriSenzaCodice
                                                            this.SchedaSelezionataPopup.Disponi(function()
                                                            {
                                                              Self.PopupComunicazioni    = false
                                                              Self.PopupVisualizzaScheda = true
                                                            })
            break;
          }
        }
      },

      OnClickConfermaFattura(SchedaFattura)
      {
        SchedaFattura.Registra(function()
        {
          SchedaFattura.Disponi()
        })
      },

      OnClickCaricaFattura(Fattura)
      {
        if(!Fattura.VisualizzaDettagliFattura)
        {
          Fattura.SchedaFattura = new TSchedaFattura(SystemInformation.AdvQuery);
          Fattura.SchedaFattura.Chiave = Fattura.CHIAVE
          Fattura.SchedaFattura.Disponi(function()
          {
            Fattura.VisualizzaDettagliFattura = true
          })
        }
        else
        {
          Fattura.VisualizzaDettagliFattura = false
        }
      },

      GestioneSogliaDiAllarme(Comunicazione)
      {
        this.PopupComunicazioni = false
        this.ProdottoSelezionato.ChiaveProdotto  = Comunicazione.ChiaveProdotto
        this.ProdottoSelezionato.Soglia          = Comunicazione.SogliaProdotto
        this.ProdottoSelezionato.ChiaveMagazzino = Comunicazione.ChiaveMagazzino
        this.PopupFornitori     = true 
      },

      CreaDDTEntranteDaProdotto(IdFornitoreXDDT)
      {
        this.PopupFornitori = false
        this.DDTEntrantePopupInCreazione = new TSchedaDocumentoDiTrasportoEntrante(SystemInformation.AdvQuery);
        this.DDTEntrantePopupInCreazione.Nuovo();
        this.DDTEntrantePopupInCreazione.Selezionato = 'F';
        this.DDTEntrantePopupInCreazione.Dati.ID_FORNITORE = IdFornitoreXDDT
        this.PopupCreazioneDDTEntrante.Visualizza = true
      },

      OnClickConfermaNuovaSogliaProdotto()
      {
        this.PopupFornitori = false

        if(this.ProdottoSelezionato.Soglia != '' && this.ProdottoSelezionato.ChiaveProdotto != null && this.ProdottoSelezionato.ChiaveMagazzino != null)
        {
          var ObjQuery  = { Operazioni : [] }
          let Self      = this

          ObjQuery.Operazioni.push({
                                      Query     : 'UpdateSogliaProdottoDaComunicazioni',
                                      Parametri : { 
                                                    ID_PRODOTTO  : this.ProdottoSelezionato.ChiaveProdotto, 
                                                    ID_MAGAZZINO : this.ProdottoSelezionato.ChiaveMagazzino, 
                                                    SOGLIA       : TSchedaGenerica.PrepareForRecordInteger(this.ProdottoSelezionato.Soglia * 100)
                                                  }
                                    })

          SystemInformation.AdvQuery.PostSQL('Magazzino', ObjQuery, function () 
          {
            Self.ControlloPresenzaComunicazioni()
          },
          SystemInformation.HandleError);
        }
      },

      GestisciModificaCliente(Chiave, Dati)
      {
        this.RecordDaEliminareDopoIlControlloAnagrafica = {}
        this.RecordDaEliminareDopoIlControlloAnagrafica.CHIAVE = Chiave

        if(Dati.ModificataAnagrafica)
        {
          let OggettoPopup = {}
          OggettoPopup.VecchiaAnagrafica = Dati.VecchiaAnagrafica
          OggettoPopup.NuovaAnagrafica   = Dati.NuovaAnagrafica
          OggettoPopup.IsAnagrafica      = true
          this.ListaAnagraficheDaModificare.push(OggettoPopup)
        }
        
        if(Dati.ModificatiRecapiti)
        {
          let Self = this
          Dati.NuoviRecapiti.forEach(function(Recapito)
          {
            if(Recapito.Chiave < 0)
            {
              let OggettoPopup = {}
              OggettoPopup.IsNuovoRecapito      = true
              OggettoPopup.ChiaveCliente        = Dati.VecchiaAnagrafica.CHIAVE
              OggettoPopup.NuovoRecapito        = Recapito
              OggettoPopup.AppartenenzaRecapito = Dati.VecchiaAnagrafica.RAGIONE_SOCIALE
              Self.ListaAnagraficheDaModificare.push(OggettoPopup)
            }
            else if(Recapito.Eliminato)
            {
              let OggettoPopup = {}
              OggettoPopup.IsRecapitoEliminato  = true
              OggettoPopup.NuovoRecapito        = Recapito
              OggettoPopup.AppartenenzaRecapito = Dati.VecchiaAnagrafica.RAGIONE_SOCIALE
              Self.ListaAnagraficheDaModificare.push(OggettoPopup)    
            }
            else if(Recapito.RecapitoModificato)
            {
              let OggettoPopup = {}
              OggettoPopup.IsRecapitoModificato = true
              OggettoPopup.ChiaveCliente        = Dati.VecchiaAnagrafica.CHIAVE
              OggettoPopup.VecchioRecapito      = Self.GetVecchioRecapito(Recapito.Chiave, Dati.VecchiRecapiti)
              OggettoPopup.NuovoRecapito        = Recapito
              OggettoPopup.AppartenenzaRecapito = Dati.VecchiaAnagrafica.RAGIONE_SOCIALE
              Self.ListaAnagraficheDaModificare.push(OggettoPopup)
            }
          })
        }

        this.HandleAnagraficheCliente()
      },

      GestisciModificaFiliale(Chiave, RagioneSociale, Dati)
      {
        this.RecordDaEliminareDopoIlControlloAnagrafica = {}
        this.RecordDaEliminareDopoIlControlloAnagrafica.CHIAVE = Chiave

        if(Dati.ModificataAnagrafica)
        {
          let OggettoPopup = {}
          OggettoPopup.VecchiaAnagrafica = Dati.VecchiaAnagrafica
          OggettoPopup.NuovaAnagrafica   = Dati.NuovaAnagrafica
          OggettoPopup.IsAnagrafica      = true
          this.ListaAnagraficheDaModificare.push(OggettoPopup)
        }
        
        if(Dati.ModificatiRecapiti)
        {
          let Self = this
          Dati.NuoviRecapiti.forEach(function(Recapito)
          {
            if(Recapito.Chiave < 0)
            {
              let OggettoPopup = {}
              OggettoPopup.IsNuovoRecapito      = true
              OggettoPopup.ChiaveFiliale        = Dati.VecchiaAnagrafica.CHIAVE
              OggettoPopup.NuovoRecapito        = Recapito
              OggettoPopup.AppartenenzaRecapito = RagioneSociale
              Self.ListaAnagraficheDaModificare.push(OggettoPopup)
            }
            else if(Recapito.Eliminato)
            {
              let OggettoPopup = {}
              OggettoPopup.IsRecapitoEliminato  = true
              OggettoPopup.NuovoRecapito        = Recapito
              OggettoPopup.AppartenenzaRecapito = RagioneSociale
              Self.ListaAnagraficheDaModificare.push(OggettoPopup)    
            }
            else if(Recapito.RecapitoModificato)
            {
              let OggettoPopup = {}
              OggettoPopup.IsRecapitoModificato = true
              OggettoPopup.ChiaveFiliale        = Dati.VecchiaAnagrafica.CHIAVE
              OggettoPopup.VecchioRecapito      = Self.GetVecchioRecapito(Recapito.Chiave, Dati.VecchiRecapiti)
              OggettoPopup.NuovoRecapito        = Recapito
              OggettoPopup.AppartenenzaRecapito = RagioneSociale
              Self.ListaAnagraficheDaModificare.push(OggettoPopup)
            }
          })
        }

        this.HandleAnagraficheFiliale()
      },

      GetVecchioRecapito(ChiaveRecapito, ListaVecchi)
      {
        for(let i = 0; i < ListaVecchi.length; i++)
          if(ListaVecchi[i].Chiave == ChiaveRecapito)
            return ListaVecchi[i]
      },

      HandleAnagraficheCliente()
      {
        this.PopupComunicazioni = false;
        if( this.IndexAnagrafiche < this.ListaAnagraficheDaModificare.length)
          this.VisualizzaPopupCliente(this.ListaAnagraficheDaModificare[this.IndexAnagrafiche])
        else this.TutteLeModificheAnagraficheGestite()
      },

      HandleAnagraficheAmm()
      {
        this.PopupComunicazioni = false;
        if( this.IndexAnagrafiche < this.ListaAnagraficheDaModificare.length)
          this.VisualizzaPopupAmm(this.ListaAnagraficheDaModificare[this.IndexAnagrafiche])
        else this.TutteLeModificheAnagraficheGestite()
      },

      HandleAnagraficheFiliale()
      {
        this.PopupComunicazioni = false;
        if( this.IndexAnagrafiche < this.ListaAnagraficheDaModificare.length)
          this.VisualizzaPopupFiliale(this.ListaAnagraficheDaModificare[this.IndexAnagrafiche])
        else this.TutteLeModificheAnagraficheGestite()
      },

      TutteLeModificheAnagraficheGestite()
      {
        let Self = this
        SystemInformation.GetConfigurazioni(function()
        {
          Self.ListaAnagraficheDaModificare   = []
          Self.IndexAnagrafiche               = 0
          Self.PopupModificaAnagraficaCliente = false
          Self.PopupModificaAnagraficaAmm     = false
          Self.PopupModificaAnagraficaFiliale = false
          Self.EliminaRecordAnagrafica()
        })
      },

      VisualizzaPopupCliente(OggettoModificaAnagrafica)
      {
        this.OggettoVisualizzatoNelPopup    = OggettoModificaAnagrafica
        this.PopupModificaAnagraficaCliente = true
      },

      VisualizzaPopupAmm(OggettoModificaAnagrafica)
      {
        this.OggettoVisualizzatoNelPopup    = OggettoModificaAnagrafica
        this.PopupModificaAnagraficaAmm     = true
      },

      VisualizzaPopupFiliale(OggettoModificaAnagrafica)
      {
        this.OggettoVisualizzatoNelPopup    = OggettoModificaAnagrafica
        this.PopupModificaAnagraficaFiliale = true
      },
  
      RifiutaModificaPopupCliente()
      {
        this.PopupModificaAnagraficaCliente = false
        this.IndexAnagrafiche++
        this.HandleAnagraficheCliente()
      },

      AnnullaModificaPopupCliente()
      {
        this.PopupModificaAnagraficaCliente = false
        this.PopupComunicazioni             = true
        this.ListaAnagraficheDaModificare   = []
      },

      OnClickChiudiModalModificaPopupCliente()
      {
        if(this.IndexAnagrafiche == 0)
          this.AnnullaModificaPopupCliente()
        else this.RifiutaModificaPopupCliente()
      },

      RifiutaModificaPopupAmm()
      {
        this.PopupModificaAnagraficaAmm = false
        this.IndexAnagrafiche++
        this.HandleAnagraficheAmm()
      },

      OnClickChiudiModalModificaPopupAmm()
      {
        if(this.IndexAnagrafiche == 0)
          this.AnnullaModificaPopupAmm()
        else this.RifiutaModificaPopupAmm()
      },

      AnnullaModificaPopupAmm()
      {
        this.PopupModificaAnagraficaAmm   = false
        this.PopupComunicazioni           = true
        this.ListaAnagraficheDaModificare = []
      },

      RifiutaModificaPopupFiliale()
      {
        this.PopupModificaAnagraficaFiliale = false
        this.IndexAnagrafiche++
        this.HandleAnagraficheFiliale()
      },

      AnnullaModificaPopupFiliale()
      {
        this.PopupModificaAnagraficaFiliale = false
        this.PopupComunicazioni             = true
        this.ListaAnagraficheDaModificare   = []
      },

      OnClickChiudiModalModificaPopupFiliale()
      {
        if(this.IndexAnagrafiche == 0)
          this.AnnullaModificaPopupFiliale()
        else this.RifiutaModificaPopupFiliale()
      },

      OnClickConfermaSchedaSelezionataPopup()
      {
        var Self = this
        this.SchedaSelezionataPopup.Registra(function()
        {
          Self.PopupVisualizzaScheda = false
          Self.ControlloPresenzaComunicazioni(function()
          {
            Self.PopupComunicazioni    = true
          })
        })
      },

      OnClickAnnullaSchedaSelezionataPopup()
      {
        this.PopupVisualizzaScheda = false
        this.PopupComunicazioni    = true
      },

      OnClickVisualizzaInformazioniAggiuntive(Info)
      {
        if(Info != null && Info != '')
        {
          this.MessaggioPopupInformazioniAggiuntive = Info
          this.PopupInformazioniAggiuntive          = true
        }
      },

      OnClickChiudiPopupInformazioniAggiuntive()
      {
        this.MessaggioPopupInformazioniAggiuntive = ''
        this.PopupInformazioniAggiuntive          = false
      },

      GetDescrizioneProvincia(ChiaveProvincia)
      {
        return SystemInformation.GetDescrizioneProvincia(ChiaveProvincia)
      },

      GetDescrizioneZona(ChiaveZona)
      {
        return SystemInformation.GetDescrizioneZona(ChiaveZona)
      },

      GetOrariSenzaSecondi(Time)
      {
        if(Time != null && Time != '')
          return Time.substring(0, Time.length - 3)
        return ''
      },

      ApriPopupAccesso(Azione, Log)
      {
        // this.AggiornaListaLog()

        this.AzioneAccesso    = Azione
        this.LogSelezionato   = Log
        this.RichiestaAccesso = Azione == 'Conferma' ? 'Sei sicuro di voler confermare l\'accesso?'
                                                        : 'Sei sicuro di voler negare l\'accesso?'
        this.PopupLogApp      = false                                                                                                                
        this.PopupDisdetta    = true
      },
      ChiudiPopupDisdetta()
      {
        this.PopupDisdetta    = false;
        this.AzioneDisdetta   = null;
        this.LogSelezionato   = null;
      },
 
      ConfermaAccesso(Log, OnSuccess)
      {
        var Self = this
        var ObjQuery = { Operazioni : [] };

        if (Log.ID_CLIENTE != null)
        {
          Log.ID_CLIENTE = null;
        }

        var Parametri = {
                          Username          : Log.USERNAME,
                          RagioneSociale    : Log.NOME_CLIENTE,
                          Email             : Log.USERNAME,
                          IdCliente         : Log.ID_CLIENTE,
                          Password          : Log.Password
                        }

        ObjQuery.Operazioni.push({
                                  Query     : "InserisciUtente",
                                  Parametri : Parametri
                                  })
        
        var Stato     = 'I'

        ObjQuery.Operazioni.push({
                                    Query     : "UpdateLogAppClienti",
                                    Parametri : {
                                                  ChiaveLog : Log.CHIAVE_LOG,
                                                  Stato     : Stato
                                                }
                                  })

        SystemInformation.AdvQuery.PostSQL('AppClienti',ObjQuery,
                                            function()
                                            {
                                              Self.AggiornaListaLog()
                                              OnSuccess()
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                              OnSuccess()
                                            });
      },

      OnClickApriDettagliLog(Log)
      {
        this.PopupLogApp = false

        if(Log.Tipologia == 'AccessoDaConfermare')
        {
          this.LsLogAccessiClienti = Log.LogClienti
          this.PopupLogAccessi =true
        }
          
      },

      OnClickChiudiPopupLogAccessi()
      {
        this.PopupLogAccessi = false
        this.PopupLogApp = true
      },

      OnClickChiudiModalRichiestaModificaCliente()
      {
        this.PopupLogRichiestaModificaCliente = false
        this.PopupLogApp  = true
      },

      OnClickChiudiModalModificheAnagrafiche()
      {
        this.PopupLogRichiestaModificaAnagrafica = false
        this.PopupLogApp = true
      },

      AnnullaRichiestaModificaCliente()
      {
        this.PopupLogRichiestaModificaCliente = false
        this.PopupLogRichiestaModificaAnagrafica = true
        this.NuoviDatiCliente   = []
      },

      // ConfermaRichiestaModificaDatiCliente()
      // {
      //   var ObjQuery  = { Operazioni : [] }
      //   var Self      = this
      //   var ChiaveLog = Self.LsDatiClienteOriginali.ChiaveLog

      //   ObjQuery.Operazioni.push({
      //                               Query     : "ModificaAnagraficaClienteDaApp",
      //                               Parametri : { 
      //                                             CHIAVE                 : this.LsDatiClienteOriginali.CHIAVE,
      //                                             PARTITA_IVA            : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.PARTITA_IVA),
      //                                             CODICE_FISCALE         : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.CODICE_FISCALE),
      //                                             INDIRIZZO_FATTURAZIONE : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.INDIRIZZO_FATTURAZIONE),
      //                                             COMUNE_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.COMUNE_FATTURAZIONE),
      //                                             CAP_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.CAP_FATTURAZIONE),
      //                                             NR_CIVICO_FATTURAZIONE : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.NR_CIVICO_FATTURAZIONE),
      //                                             PROVINCIA_FATTURAZIONE : TSchedaGenerica.PrepareForRecordListIndex(this.NuoviDatiCliente.PROVINCIA_FATTURAZIONE != null 
      //                                                                         ? this.NuoviDatiCliente.PROVINCIA_FATTURAZIONE : -1),
      //                                             ZONA_FATTURAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(this.NuoviDatiCliente.ZONA_FATTURAZIONE != null 
      //                                                                         ? this.NuoviDatiCliente.ZONA_FATTURAZIONE : -1),
      //                                             INDIRIZZO_SPEDIZIONE   : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.INDIRIZZO_SPEDIZIONE),
      //                                             COMUNE_SPEDIZIONE      : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.COMUNE_SPEDIZIONE),
      //                                             CAP_SPEDIZIONE         : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.CAP_SPEDIZIONE),
      //                                             NR_CIVICO_SPEDIZIONE   : TSchedaGenerica.PrepareForRecordString(this.NuoviDatiCliente.NR_CIVICO_SPEDIZIONE),
      //                                             PROVINCIA_SPEDIZIONE   : TSchedaGenerica.PrepareForRecordListIndex(this.NuoviDatiCliente.PROVINCIA_SPEDIZIONE != null 
      //                                                                         ? this.NuoviDatiCliente.PROVINCIA_SPEDIZIONE : -1),
      //                                             ZONA_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordListIndex(this.NuoviDatiCliente.ZONA_SPEDIZIONE != null 
      //                                                                         ? this.NuoviDatiCliente.ZONA_SPEDIZIONE : -1),
      //                                           }
      //                             })


      //   ObjQuery.Operazioni.push({
      //                               Query     : "EliminaLogRichiestaModificaDatiClienteDaApp",
      //                               Parametri : {
      //                                             ChiaveLog : ChiaveLog
      //                                           }
      //                           })


      //   SystemInformation.AdvQuery.PostSQL('AppClienti', ObjQuery, 
      //                                       function () 
      //                                       {
      //                                         Self.GetLogAppClienti()
      //                                         Self.PopupLogRichiestaModificaCliente = false
      //                                         Self.PopupLogApp = true
      //                                       },
      //                                       SystemInformation.HandleError);
      // },

      // RifiutaRichiestaModificaDatiCliente()
      // {
      //   var ObjQuery  = { Operazioni : [] }
      //   var Self      = this
      //   var ChiaveLog = Self.LsDatiClienteOriginali.ChiaveLog

      //   ObjQuery.Operazioni.push({
      //                               Query     : "EliminaLogRichiestaModificaDatiClienteDaApp",
      //                               Parametri : {
      //                                             ChiaveLog : ChiaveLog
      //                                           }
      //                           })


      //   SystemInformation.AdvQuery.PostSQL('AppClienti', ObjQuery, 
      //                                       function () 
      //                                       {
      //                                         Self.GetLogAppClienti()
      //                                         Self.PopupLogRichiestaModificaCliente = false
      //                                         Self.PopupLogApp = true
      //                                       },
      //                                       SystemInformation.HandleError);
      // },

      OnClickAttivaModalInsermentoClienteGuidato()
      {
        this.PopupInserimentoClienteGuidato = false;
        this.InserimentoClienteGuidatoTrigger.TriggerInserimentoGuidato = false
      }
    },
    
    watch :
    {
        CurrentFilter : 
        {
           handler()
           {
              if(!this.BloccaWatchFiltraDati)
                this.FiltraDati();
           },
           immediate : true
        },

        TriggerPopupScaricoFileZip :
        {
          handler()
          {
            if(this.MenuTrigger.TriggerPopupScaricoFileZip != 0)
              this.PopupScaricoFileZip = true;
          },
          deep: true
        },
        
        WatchInserimentoClienteGuidato :
        {
          handler(newValue)
          {
            if(newValue)
              this.PopupInserimentoClienteGuidato = newValue
          },
          deep: true
        }
    },
    computed :
    {
      DatiInModifica :
      {
         get()
         {
           if(this.SchedaSelezionata.GetClassName() == 'TSchedaGenerica')
             return false;
           else return (this.SchedaSelezionata.IsNuovo() || this.SchedaSelezionata.DatiModificati())
         }
      },
      TipoSchedaSelezionata : 
      {
         get()
         {
          return this.SchedaSelezionata.GetClassName();
         }
      },
      AltezzaFiltri :
      {
        get()
        {
          let Result = 0
          switch(this.CurrentFilter)
          {
            case 'Clienti'                  : this.EspandiRicercheCliente?              Result = 86 : Result = 76
                                              break;
            case 'Fornitori'                : Result = 50
                                              break;
            case 'Filiali'                  : Result = 50
                                              break;   
            case 'Magazzini'                : Result = 50
                                              break;                                                                    
            case 'Movimenti/Conti'          : Result = 50
                                              break;
            case 'Prodotti'                 : Result = 50
                                              break;
            case 'Fatture'                  : this.EspandiRicercheFattura?              Result = 115 : Result = 53
                                              break; 
            case 'Preventivi'               : this.EspandiRicerchePreventivo?           Result = 86  : Result = 53
                                              break;
            case 'Conferme'                 : this.EspandiRicercheConferme?             Result = 86  : Result = 53
                                              break;
            case 'Note di credito'          : this.EspandiRicercheNota?                 Result = 86  : Result = 70
                                              break;
            case 'Fatture passive'          : this.EspandiRicercheFatturaPassiva?       Result = 80  : Result = 46
                                              break;   
            case 'Note di credito passive'  : this.EspandiRicercheNotaDiCreditoPassiva? Result = 80  : Result = 46
                                              break; 
            case 'Movimenti Magazzini'      : Result = 46
                                              break;   
            case 'Documenti di trasporto'   : Result = 47
                                              break;
            case 'Uscenti'                  : Result = 47
                                              break;
            case 'Entranti'                 : Result = 47
                                              break;        
            case 'Movimenti'                : Result = 46
                                              break;
            case 'Doc scarico prod composti'                : Result = 50
                                              break;
            default                         : Result = 120
                                              break;
          }

          return Result
        }
      },

      TriggerPopupScaricoFileZip:
      {
        get()
        {
          return this.MenuTrigger.TriggerPopupScaricoFileZip;
        }
      },

      MostraCampanella()
      {
        return this.VisibilitaCampanellaLog
      },

      WatchInserimentoClienteGuidato:
      {
        get()
        {
          return this.InserimentoClienteGuidatoTrigger.TriggerInserimentoGuidato;
        }
      }

    },

    mounted()
    {
      let Self = this
      window.addEventListener('keydown', function(event)
                                        {
                                          Self.KeyDownF2(event)
                                        })
    },

    beforeMount() 
    {
      if(!this.DeveloperMode)
        this.ControlloPresenzaComunicazioni()
    },

}
</script>
