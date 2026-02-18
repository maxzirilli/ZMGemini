<template>
  <VUEConfirm :Popup="SchedaCliente.PopupConfermaDoppioCodiceFiscale" 
              :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Richiesta="'Attenzione: il codice fiscale è già presente.\n\nSei sicuro di voler inserire il nuovo cliente?'" 
              @onClickConfermaPopup="SchedaCliente.PopupConfermaDoppioCodiceOnSuccess" 
              @onClickChiudiPopup="AnnullaPopUpCF">
  </VUEConfirm>
  
  <VUEConfirm :Popup="SchedaCliente.PopupAggiornaAvvisoFattura.Visibile" 
              :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Altezza="'130px'"
              :Richiesta="SchedaCliente.PopupAggiornaAvvisoFattura.MessaggioPopup" 
              @onClickConfermaPopup="OnChangeDatiAvviso()" 
              @onClickAnnullaPopup="SchedaCliente.PopupAggiornaAvvisoFattura.Visibile"
              @onClickChiudiPopup="AnnullaPopUpAvvisi()">
  </VUEConfirm>
  
  <VUEConfirm :Popup="PopupDisattivaCliente" 
              :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"    
              :Altezza="'130px'"
              :Richiesta="'Stai disattivando questo cliente.'"  
              @onClickConfermaPopup="OnClickConfermaPopupDisattivaCliente" 
              @onClickAnnullaPopup="PopupDisattivaCliente = false"
              @onClickChiudiPopup="PopupDisattivaCliente = false">
  </VUEConfirm>

  <VUEModal v-if="DataContoCliente" :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma" :Titolo="'Inserire data per conto cliente'" :Altezza="'100px'" :Larghezza="'520px'"
            @onClickChiudiModal="DataContoCliente = false">
      <template v-slot:Body>
        <div class="form-row">
        <div class="col-md-12">
          <div style="float:left;width:50%">
            <label style="font-weight: bold;">Dal</label>
            <input type="date" class="form-control" style="width:100%" v-model="ContoClienteDal">
          </div>
          <div style="float:left;width:1%">&nbsp;</div>
          <div style="float:left;width:49%">
            <label style="font-weight: bold;">al </label>
            <input type="date" class="form-control" style="width:100%" v-model="ContoClienteAl">
          </div>
        </div>
        </div> 
      </template>
      <template v-slot:Footer>
        <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="DataContoCliente = false" data-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:20%" @click="ConfermaDataContoCliente" data-dismiss="modal">Conferma</button>
      </template>
  </VUEModal>

  <VUEModal v-if="PopupConfermaCaricamento" :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma" :Titolo="'Importa Filiali'" :Altezza="'100px'" :Larghezza="'450px'"
            @onClickChiudiModal="ClosePopup">
    <template v-slot:Body>
      <div class="form-row">
        <p style="font-size: 18px; text-align: center; margin-top: 5%">Le filiali sono state caricate con successo!</p>
      </div> 
    </template>
    <template v-slot:Footer>
      <button @click="ClosePopup" type="button" class="btn btn-info" style="float:right;margin-left:10px;font-weight:bold;width:20%">Chiudi</button>
    </template>
  </VUEModal>

  <VUEModal v-if="PopupVisualizzaFattura" :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma" :Titolo="'Visualizzazione fattura'" :Altezza="'650px'" :Larghezza="'1500px'" 
            @onClickChiudiModal="ChiudiPopupVisualizzaFattura">
            <template v-slot:Body>
              <div class="col-md-12"> 
                <div style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;">
                  <VUESchedaFattura :SchedaFattura="FatturaSelezionata" />
                </div>
              </div>
            </template>
            <template v-slot:Footer>
                
              <button v-if="!FatturaSelezionata.DatiModificati()"
                      type="button" 
                      class="btn btn-info" 
                      style="width:15%"
                      @click="ChiudiPopupVisualizzaFattura">
                      Chiudi
              </button>

              <button
                v-if="FatturaSelezionata.DatiModificati() && FatturaSelezionata.CanRecord()"
                type="button"
                style="width:15%"
                class="btn btn-success"
                @click="ConfermaPopupVisualizzaFattura">
                Conferma
              </button>

              <button v-if="FatturaSelezionata.DatiModificati()"
                      type="button" 
                      class="btn btn-danger" 
                      style="width:15%"
                      @click="ChiudiPopupVisualizzaFattura">
                      Annulla
              </button>
            </template>
  </VUEModal>

  <VUEModal v-if="PopupDocumento" :PathLogo="require('../../assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma" :Titolo="'Visualizzazione documento'" :Altezza="'650px'" :Larghezza="'1500px'" 
            @onClickChiudiModal="ChiudiPopupDocumento">
            <template v-slot:Body>
              <div class="col-md-12"> 
                <div style="display: flex; align-items: center;" >
                  <span style="font-size: large;font-weight: bold;">{{ TestoPopupDocumento }}</span>
                </div>
                <div v-if="TipoDocumento == 'F' || TipoDocumento == 'R'" style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;">
                  <VUESchedaFattura :SchedaFattura="DocumentoSelezionato" />
                </div>
                <div v-else-if="TipoDocumento == 'M'" style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;">
                  <VUESchedaMovimento :SchedaMovimento="DocumentoSelezionato" />
                </div>
                <div v-else-if="TipoDocumento == 'N'" style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;">
                  <VUESchedaNotaDiCredito :SchedaNotaDiCredito="DocumentoSelezionato" />
                </div>
              </div>
            </template>
            <template v-slot:Footer>
                
              <button v-if="!DocumentoSelezionato.DatiModificati()"
                      type="button" 
                      class="btn btn-info" 
                      style="width:15%"
                      @click="ChiudiPopupDocumento">
                      Chiudi
              </button>

              <button
                v-if="DocumentoSelezionato.DatiModificati() && DocumentoSelezionato.CanRecord()"
                type="button"
                style="width:15%"
                class="btn btn-success"
                @click="ConfermaPopupDocumento"
              >
                Conferma
              </button>
              <button v-if="DocumentoSelezionato.DatiModificati()"
                      type="button" 
                      class="btn btn-danger" 
                      style="width:15%"
                      @click="ChiudiPopupDocumento">
                      Annulla
              </button>
              <!-- <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="ChiudiPopupDocumento" data-dismiss="modal">Annulla</button>
              <button v-if="DocumentoSelezionato.CanRecord()" type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="ConfermaPopupDocumento" data-dismiss="modal">Conferma</button> -->
            </template>
  </VUEModal>

  <div>
    <div class="ZMCorpoSchedeDati">
      <header class="panel-heading bg-light" >
          <ul class="nav nav-tabs nav-justified">
            <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
                :class="{ active : ATab.Id == Tabs.ActiveTab }">
              <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
                <img v-if="ATab.Id == 'DatiFatturazione'" src="@/assets/images/IconeAlbero/Cliente2.png" style="width:40px;height:40px;float:left;margin-top:-9px">  
              </a>
            </li>
          </ul>
      </header>
      <div style="height:5px;"></div>
      <div  v-if="Tabs.ActiveTab == 'DatiFatturazione'">
          <div class="ZMNuovaRigaScheda" style="padding-top:15px">
            <div class="col-md-10">
              <div style="float:left;margin-right:5px">
                <input style="float:left;margin-top:2px" type="checkbox" v-model="SchedaCliente.Dati.ATTIVO" @change="OnChangeClienteAttivo"/>
              </div>
              <div style="float:left;">
                <label :style="{ color : SchedaCliente.Dati.ATTIVO ? undefined : 'red' }" style="font-weight: bold;">Cliente attivo&nbsp;</label>
              </div>
              <div style="float:left;width:3%">&nbsp;</div>

              <div style="float:left;margin-right:5px">
                <input style="float:left;margin-top:2px" type="checkbox" v-model="SchedaCliente.Dati.IS_FORNITORE"/>
              </div>
              <div style="float:left;">
                <label style="font-weight: bold;">Anche fornitore&nbsp;</label>
              </div>
              <div style="float:left;width:3%">&nbsp;</div>
              <div style="float:left;margin-right:5px">
                <input type="checkbox" style="margin-top:2px;" v-model="SchedaCliente.Dati.ENTE_PUBBLICO" @change="OnChangeEntePubblico"/>
              </div>
              <div style="float:left;">
                <label style="font-weight: bold;">Ente pubblico&nbsp;</label>
              </div>
              <div style="float:left;width:3%">&nbsp;</div>
              <div style="float:left;margin-right:5px">
                <input type="checkbox" style="margin-top:2px;" v-model="SchedaCliente.Dati.PASSATA_AD_AVVOCATO"/>
              </div>
              <div style="float:left;">
                <label style="font-weight: bold;" :class="{'text-highlighted': SchedaCliente.Dati.PASSATA_AD_AVVOCATO}">Passato ad avvocato&nbsp;</label>
              </div>
            </div>
            <div class="col-md-2" v-if="!SchedaCliente.DatiModificati() && !SchedaCliente.IsNuovo()">
              <div style="float:right;">
                <div class="btn-group open" >
                  <button type="button" 
                          class="btn btn-default" 
                          :class="{'data-toggle' : this.MenuNuovo.length != 0}" 
                          aria-expanded="true" 
                          style="width:120px;z-index: 9999"
                          @click="OnClickNuovo()">
                      Nuovo&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                      <i class="fa" 
                        :class="{ 'fa-sort-up' : MenuNuovo.length != 0, 'fa-sort-down' : MenuNuovo.length == 0 }" 
                        style="box-sizing: border-box;"></i>
                  </button>
                  <ul v-if="MenuNuovo.length != 0" class="dropdown-menu pull-right" style="z-index: 9999;">
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
              <div style="float:right">
                <div class="btn-group open" style="float:right;margin-right:5px">
                  <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,false,false,true)">
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
              </div>
              <div>
                <div style="float:right; margin-right: 20px;">
                  <input type="file" @change="handleFileUpload" id="file-upload" style="display: none;" />
                  <label for="file-upload" style="cursor: pointer; background-color: #4cc0c1; color: white; padding: 10px 20px; border-radius: 5px;">
                    Carica filiali
                  </label>
                </div>
              </div>

            </div>
          </div>
          <div class="ZMNuovaRigaScheda">
              <div v-if="this.ShowCodiceCliente || SchedaCliente.IsNuovo()" style="float:left;">
                    <label style="margin-right:15px;"><span style="font-weight:bold;">Ultimo codice cliente:</span> {{ SchedaCliente.CodiceUltimoCliente }}</label>
              </div>
          </div>
          <div style="clear:both;padding-top: 6px;">
            <div style="float:left;width:15%">
              <label style="font-weight: bold;">Codice cliente </label>
              <input type="text" class="form-control" @keypress="OnKeyPressCodiceCliente()" v-model="SchedaCliente.Dati.CODICE" placeholder="Codice cliente" maxlength="10"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;" :style="{ width: VisibilitaCategorieClienti ? '63%' : '84%' }">
              <label style="font-weight: bold;">Ragione sociale </label>
              <input type="text" class="form-control" v-model="SchedaCliente.Dati.RAGIONE_SOCIALE" placeholder="Ragione sociale"/>
              <label v-if="SchedaCliente.Dati.RAGIONE_SOCIALE.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
            </div>
            <div v-if="VisibilitaCategorieClienti" style="float:left;width:1%;">&nbsp;</div>
            <div v-if="VisibilitaCategorieClienti" style="float:left;width:20%">
              <label style="font-weight: bold;">Categoria</label>
              <select class="form-control" v-model="SchedaCliente.Dati.ID_CAT_CLIENTE">
                <option :value="-1">-</option>
                <option v-for="SelectOption in LsCategorieClienti" 
                        :Key="SelectOption.CHIAVE"
                        :value="SelectOption.CHIAVE"
                        :selected="SelectOption.CHIAVE == SchedaCliente.Dati.ID_CAT_CLIENTE">
                        {{SelectOption.DESCRIZIONE}} 
                </option>
              </select>
            </div>
          </div>
          <div style="clear:both;height:1%">&nbsp;</div>

          <div class="ZMSeparatoreScheda">Dati fatturazione</div>
          <div style="clear:both">
              <div style="float:left;width:28%">
                  <label style="font-weight: bold;">Indirizzo</label>
                  <input type="text" class="form-control" v-model="SchedaCliente.Dati.INDIRIZZO_FATTURAZIONE" placeholder="Indirizzo"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:10%">
                  <label style="font-weight: bold;">Civico</label>
                  <input maxlength="7" type="text" class="form-control" v-model="SchedaCliente.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:6%">
                  <label style="font-weight: bold;">CAP</label>
                  <VUEInputCAP v-model="SchedaCliente.Dati.CAP_FATTURAZIONE"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:25%">
                  <label style="font-weight: bold;">Comune</label>
                  <input type="text" class="form-control" v-model="SchedaCliente.Dati.COMUNE_FATTURAZIONE" placeholder="Comune"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:19%">
                  <label style="font-weight: bold;">Provincia</label>
                  <VUEInputProvince v-model="SchedaCliente.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:7%">
                  <label style="font-weight: bold;">Zona</label>
                  <VUEInputZone v-model="SchedaCliente.Dati.ZONA_FATTURAZIONE"/>
              </div> 
          </div>
          <div class="ZMNuovaRigaScheda">
          <div style="float:left;width:16%">
                  <label style="font-weight: bold;">Codice fiscale</label>
                  <VUEInputCodiceFiscale v-model="SchedaCliente.Dati.CODICE_FISCALE"/>
                  <label v-if="(SchedaCliente.Dati.PARTITA_IVA.trim() == '' && SchedaCliente.Dati.CODICE_FISCALE.trim() == '') && !DeveloperMode" 
                        class="ZMFormLabelError">Campo obbligatorio</label>
                  <label v-if="ErroreCodiceFiscale != '' && !DeveloperMode && SchedaCliente.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:11%">
                  <label style="font-weight: bold;">Partita IVA</label>
                  <VUEInputPartitaIVA v-model="SchedaCliente.Dati.PARTITA_IVA" />
                  <label v-if="(SchedaCliente.Dati.PARTITA_IVA.trim() == '' && SchedaCliente.Dati.CODICE_FISCALE.trim() == '') && !DeveloperMode" 
                        class="ZMFormLabelError">Campo obbligatorio</label>
                  <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaCliente.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
              </div> 
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:10%">
                  <label style="font-weight: bold;">Codice SDI</label>
                  <input type="text" class="form-control" v-model="SchedaCliente.Dati.COD_ENTE_SDI" placeholder="Cod. SDI" maxlength="7"/>
                  <label v-if="SchedaCliente.Dati.COD_ENTE_SDI.trim().length != 7" class="ZMFormLabelError">
                  {{(SchedaCliente.Dati.COD_ENTE_SDI.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
                  </label>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:9%">
                  <label style="font-weight: bold;">Cod. uff. dest.</label>
                  <input type="text" class="form-control" 
                        v-model="SchedaCliente.Dati.COD_UFFICIO_DEST"
                        placeholder="Codice ufficio destinazione"
                        maxlength="6"/>
                  <label v-if="SchedaCliente.Dati.COD_UFFICIO_DEST.length != 6 && SchedaCliente.Dati.COD_UFFICIO_DEST != ''" class="ZMFormLabelError">Campo non valido
                  </label>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:19%">
                <label style="font-weight: bold;">Naz. emit.</label>
                <VUEInputNazioni v-model="SchedaCliente.Dati.NAZIONE_EM_PIVA" emptyElement="false"/>
                <label v-if="SchedaCliente.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:8%">
              <label style="font-weight: bold;">Ritenuta [%]</label>
              <input placeholder="Ritenuta" min="0" step="0.1" type="number" class="form-control" v-model="SchedaCliente.Dati.RITENUTA"/>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>      
              <div style="float:left;width:6%"> 
                <label style="font-weight: bold;">IVA [%]</label>
                <input placeholder="IVA" min="0" type="number" pattern="\d+" class="form-control" v-model="SchedaCliente.Dati.IVA_SUGGERITA_CLIENTE" @change="OnChangeIva()"/>
              </div> 
              <div style="float:left;width:1%">&nbsp;</div>      
              <div style="float:left;width:14%">  
                <label style="font-weight: bold;">Natura pagamento</label>
                <VUEInputNaturaPagamenti  v-model="SchedaCliente.Dati.NATURA_PAGAMENTO"/>
                <label v-if="SchedaCliente.Dati.NATURA_PAGAMENTO == -1 && SchedaCliente.Dati.IVA_SUGGERITA_CLIENTE == 0" class="ZMFormLabelError">Campo obbligatorio</label>
              </div>
          </div>
          <div class="ZMNuovaRigaScheda">
              <div style="float:left;width:39%">
                <label style="font-weight: bold;">PEC</label>
                <input type="email" class="form-control" v-model="SchedaCliente.Dati.PEC" placeholder="PEC"/>
              </div> 
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:16%">
                <label style="font-weight: bold;">Bollo</label>
                  <select class="form-control" v-model="SchedaCliente.Dati.BOLLO">
                    <option v-for="SelectOption in LsPagamentoBollo" 
                            :Key="SelectOption.Id"
                            :value="SelectOption.Id"
                            :selected="SelectOption.Id == SchedaCliente.Dati.BOLLO">
                            {{SelectOption.Descrizione}} 
                    </option>
                  </select>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:15%">
                <label style="font-weight: bold;">Esigibilità IVA</label>
                <VUEInputEsigibilitaIVA v-model="SchedaCliente.Dati.ESIGIBILITA_IVA"/>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:19%">
                <label style="font-weight: bold;">Descrizione spese</label>
                <input placeholder="Descrizione spese" type="text" class="form-control" v-model="SchedaCliente.Dati.SPESE_INCASSO_DESCRIZIONE"/>
              </div> 
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:7%">
                <label style="font-weight: bold;">Spese [€]</label>
                <input type="number" step="0.01" class="form-control" v-model="SchedaCliente.Dati.SPESE_INCASSO_SUGGERITE"/>
              </div> 
          </div>
          <div class="ZMNuovaRigaScheda">
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:10%">
              <div style="float:left;margin-right:5px">
                <input type="checkbox" style="margin-top:2px;" v-model="SchedaCliente.Dati.SOGLIA_PRESENTE" @change="OnChangeSogliaPresente"/>
              </div>
              <div style="float:left;">
                <label style="font-weight: bold;">Soglia esenz. IVA&nbsp;</label>
              </div>
            </div>
            <div v-if="SchedaCliente.Dati.SOGLIA_PRESENTE">
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:10%">
                  <label style="font-weight: bold;">Soglia [€]</label>
                  <input type="number" step="0.01" class="form-control" v-model="SchedaCliente.Dati.SOGLIA_X_NO_IVA" placeholder="Soglia"/>
                  <label v-if="SchedaCliente.Dati.SOGLIA_PRESENTE && SchedaCliente.Dati.SOGLIA_X_NO_IVA == 0" class="ZMFormLabelError">Campo obbligatorio</label>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>      
              <div style="float:left;width:16%">  
                <label style="font-weight: bold;">Natura pagamento soglia</label>
                <VUEInputNaturaPagamenti v-model="SchedaCliente.Dati.SOGLIA_NAT_PAG"/>
                <label v-if="SchedaCliente.Dati.SOGLIA_PRESENTE && SchedaCliente.Dati.SOGLIA_NAT_PAG == -1" class="ZMFormLabelError">Campo obbligatorio</label>
              </div>
              <div style="clear:both;height:5px">&nbsp;</div>
            </div>
          </div>

          <div class="ZMSeparatoreScheda">Indirizzo di destinazione</div>
          <div style="clear:both">
            <div style="float:left;width:52%">
            <label style="font-weight: bold;">Presso</label>
            <input type="text" class="form-control" v-model="SchedaCliente.Dati.PRESSO" placeholder="Presso"/>
            </div>
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:36%">
                  <label style="font-weight: bold;">Indirizzo</label>
                  <input type="text" class="form-control" v-model="SchedaCliente.Dati.INDIRIZZO_SPEDIZIONE" placeholder="Indirizzo"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:10%">
                  <label style="font-weight: bold;">Civico</label>
                  <input maxlength="7" type="text" class="form-control" v-model="SchedaCliente.Dati.NR_CIVICO_SPEDIZIONE" placeholder="Nr. civico"/>
              </div> 
          </div>
          <div style="clear:both">
              <div style="float:left;width:6%">
                  <label style="font-weight:bold;">CAP</label>
                  <VUEInputCAP v-model="SchedaCliente.Dati.CAP_SPEDIZIONE"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:25%">
                  <label style="font-weight: bold;">Comune</label>
                  <input type="text" class="form-control" v-model="SchedaCliente.Dati.COMUNE_SPEDIZIONE" placeholder="Comune"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:19%">
                  <label style="font-weight: bold;">Provincia</label>
                  <VUEInputProvince v-model="SchedaCliente.Dati.PROVINCIA_SPEDIZIONE" emptyElement="true"/>
              </div>           
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:12%">
                  <label style="font-weight: bold;">Zona</label>
                  <VUEInputZone v-model="SchedaCliente.Dati.ZONA_SPEDIZIONE"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:23%">
                  <label style="font-weight: bold;">Nazione</label>
                  <VUEInputNazioni v-model="SchedaCliente.Dati.NAZIONE_SPEDIZIONE" emptyElement="true"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:10%">
                <label style="font-weight: bold;">&nbsp;</label>
                <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickCopiaDaDatiFatturazione">Copia dati fatt.</button>
              </div> 
              <div style="clear:both;height:1%">&nbsp;</div>
          </div>

          <div class="ZMSeparatoreScheda">Conto corrente</div>
          <div class="ZMNuovaRigaScheda">
            <div style="float:left;width:32%">
              <label style="font-weight: bold;">Condizioni pagamento</label>
              <VUEInputCondPagamenti v-model="SchedaCliente.Dati.COND_PAGAMENTO"/>
              <label v-if="SchedaCliente.Dati.COND_PAGAMENTO == -1" class="ZMFormLabelError">Campo obbligatorio</label>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>      
            <div style="float:left;width:67%">  
              <label style="font-weight: bold;">Note in fattura</label>
              <input type="text" class="form-control" v-model="SchedaCliente.Dati.NOTE_IN_FATTURA" placeholder="Note in fattura"/>
            </div>
          </div>
          <VUEInputContoRibaCorrente :ContoCorrente="SchedaCliente.Dati.ContoCorrente"/>

          <div style="clear:both;height:1%">&nbsp;</div>
          
          <div class="ZMSeparatoreScheda">Note</div>
          <div style="clear:both;height:10px">&nbsp;</div>
          <textarea style="resize:none;height:104px" class="form-control" v-model="SchedaCliente.Dati.NOTE" />
      </div>
      <div  v-if="Tabs.ActiveTab == 'Ritenute'">
        <div v-if="SchedaCliente.LsRitenute.length > 0">
          <section class="panel panel-default" >
          <div class="table-responsive" >
            <table class="table table-striped b-t b-light" style="background-color:#d0e9ff">
              <thead>
                <tr>
                  <th style="width:15%">Anno</th>
                  <th style="width:16%">Tot. ritenute RDA incassate</th>
                  <th style="width:16%">RDA banca</th>
                  <th style="width:16%">Certificazioni senza incasso</th>
                  <th style="width:16%">Tot. ritenute certificate</th>
                  <th style="width:16%">Totale diff. da certificare</th>
                  <th style="width:5%">Info</th>
                </tr>
              </thead>
              <tbody style="">
                <template v-for="Ritenuta in SchedaCliente.LsRitenute" :key="Ritenuta.Chiave">
                      <tr v-if="Ritenuta.VisualizzaIdentificativoFiscale">
                        <td colspan="6" style="background-color:#ebf6ff;">
                          <label style="font-weight: bold">{{ Ritenuta.IdentificativoFiscale }}</label>
                        </td>
                      </tr>
                      <tr>
                        <td style="pointer-events:none;padding:5px;border-bottom:0; background-color:#eee">
                          <input readonly class="form-control" v-model="Ritenuta.Anno" />
                        </td>
                        <td style="pointer-events:none; padding:5px;border-bottom:0; background-color:#eee; padding-top: 14px;text-align: right;">
                          <label v-if="((Ritenuta.Ritenuta + Ritenuta.SommaRitenutaDiFattureRDA - 0.01) > (Ritenuta.RitenutaPagata) && (Ritenuta.Ritenuta - Ritenuta.SommaRitenutaDiFattureNonPagateMaCertificate != 0))" class="ZMLabel" style="color:red"> {{ Number((Ritenuta.Ritenuta - Ritenuta.SommaRitenutaDiFattureNonPagateMaCertificate).toFixed(2)) }}</label>
                          <label v-else class="ZMLabel">{{ Number((Ritenuta.Ritenuta - Ritenuta.SommaRitenutaDiFattureNonPagateMaCertificate).toFixed(2)) }}</label>
                        </td>
                        <td style="pointer-events:none; padding:5px;border-bottom:0; background-color:#eee; padding-top: 14px;text-align: right">
                          <label v-if="((Ritenuta.Ritenuta + Ritenuta.SommaRitenutaDiFattureRDA - 0.01) > (Ritenuta.RitenutaPagata) && (Ritenuta.SommaRitenutaDiFattureRDA != 0))" class="ZMLabel" style="color:red">{{ Number((Ritenuta.SommaRitenutaDiFattureRDA).toFixed(2)) }}</label>
                          <label v-else class="ZMLabel">{{ Number((Ritenuta.SommaRitenutaDiFattureRDA).toFixed(2)) }}</label>
                        </td>
                        <td style="pointer-events:none; padding:5px;border-bottom:0; background-color:#eee; padding-top: 14px;text-align: right">
                          <label class="ZMLabel">{{ Number((Ritenuta.RitenutaCertificata).toFixed(2)) }}</label>
                        </td>
                        <td style="padding:5px;border:1px solid #eee; border-bottom:0; background-color:#eee">
                          <input type="number" min="0" step="0.01" v-model="Ritenuta.RitenutaPagata" @input="OnRitenutaChange(Ritenuta)" class="form-control"/>
                        </td>
                        <td style="pointer-events:none; padding:5px;border-bottom:0; background-color:#eee; padding-top: 14px;text-align: right">
                          <label v-if="Ritenuta.Ritenuta + Ritenuta.SommaRitenutaDiFattureRDA - Ritenuta.RitenutaPagata - Ritenuta.RitenutaCertificata - 0.01 > 0" class="ZMLabel" >{{ (Ritenuta.Ritenuta + Ritenuta.SommaRitenutaDiFattureRDA - Ritenuta.RitenutaPagata -  Ritenuta.RitenutaCertificata).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}</label>
                          <label v-else class="ZMLabel">Pagata</label>
                        </td>
                        <td style="background-color:#ebf6ff" >
                          <i v-if="!Ritenuta.FattureVisualizzate" class="ZMIconButton fa fa-arrow-circle-down text-info" @click="OnClickGetDettagliRitenuta(Ritenuta.Anno,Ritenuta)"></i>
                          <i v-else class="ZMIconButton fa fa-arrow-circle-up text-info text" @click="Ritenuta.FattureVisualizzate = false"></i>
                        </td>
                      </tr>
                      <tr style="background-color:#d0e9ff" >
                        <td style="background-color:#d0e9ff" colspan="8" v-if="Ritenuta.FattureVisualizzate == true && Ritenuta.LsFatture.length != 0">
                        <table class="table table-striped b-t b-light">
                            <thead>
                              <tr>
                                <th style="width:7%;background:#b3dbff">Numero fattura</th>
                                <th style="width:3%;background:#b3dbff">Data</th>
                                <th style="width:7%;background:#b3dbff">Imponibile</th>
                                <th style="width:7%;background:#b3dbff">Rate pagate</th>
                                <th style="width:7%;background:#b3dbff">RDA fattura</th> 
                                <!-- <th style="width:13%;background:#b3dbff">Frazione importo</th> -->
                                <th style="width:7%;background:#b3dbff">Ritenuta rilevata</th> 
                                <th style="width:7%;background:#b3dbff">RDA banca</th> 
                                <th style="width:7%;background:#b3dbff">Cert. senza incasso</th> 
                                <th style="width:7%;background:#b3dbff">Diff. da rilevare</th> 
                              </tr>
                            </thead>
                            <tbody v-for="Fattura in Ritenuta.LsFatture" :key="Fattura.IdFattura">
                              <tr>
                              <td>
                              {{ Fattura.Numero }} / {{ Fattura.Anno }}
                              </td>
                              <td>
                              {{ Fattura.DataFatturaFormatted }}
                              </td>
                              <td>
                              {{ Fattura.SommaImponibile.toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}
                              </td>
                              <td>
                                <span v-if="Fattura.TotalePagatoXAnno[Ritenuta.Anno] != null">{{ Fattura.TotalePagatoXAnno[Ritenuta.Anno].toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}</span>
                                <span v-else>/</span>
                              </td>
                              <td>
                              {{ !Fattura.IsRitenutaDiAcconto ? Fattura.SommaRitenuta.toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) : (0).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}
                              </td>
                              <td>
                              {{ (!Fattura.IsRitenutaDiAcconto && !Fattura.IsRitenutaCertificata) ? Fattura.SommaRitenutaXAnno[Ritenuta.Anno].toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) : (0).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}
                              </td>
                              <td>
                              {{ Fattura.IsRitenutaDiAcconto ? (Fattura.TotaleRitenutaDaBanca[Ritenuta.Anno] != undefined ? Fattura.TotaleRitenutaDaBanca[Ritenuta.Anno] : 0).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) : (0).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}
                              </td>
                              <td>
                              {{ (!Fattura.IsRitenutaDiAcconto && Fattura.IsRitenutaCertificata) ? (Fattura.SommaRitenutaXAnno[Ritenuta.Anno]).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) : (0).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}
                              </td>
                              <td style="color:red" v-if="(Fattura.SommaRitenutaXAnno[Ritenuta.Anno] - (Fattura.SommaRitenutaXAnno[Ritenuta.Anno] * (Fattura.PercPagato * 100) / 100)) != 0 && !Fattura.IsRitenutaDiAcconto">
                              {{ (Fattura.SommaRitenuta - (!Fattura.IsRitenutaDiAcconto ? Fattura.SommaRitenutaXAnno[Ritenuta.Anno] : Fattura.TotaleRitenutaDaBanca[Ritenuta.Anno])).toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }}
                              </td>
                              <td v-else>
                              Pagata
                              </td>
                            </tr>
                            </tbody>
                        </table>
                        </td>
                        <td colspan="6" v-if="Ritenuta.FattureVisualizzate == true && Ritenuta.LsFatture.length == 0">
                          Non ci sono fatture del cliente con ritenuta questo anno
                        </td>
                      </tr>
                </template>

              </tbody>
            </table>
          </div>
          </section>

        </div>
        <div v-else style="padding-bottom:2%; font-size:16px; font:bold; margin-top: 2%;margin-left: 1%;">NON SONO PRESENTI FATTURE CON RITENUTA DI ACCONTO</div>

        <VUEDataTable :DataTable="SchedaCliente.DataTableRitenuteCliente" 
                      :NomeProgramma="'Gemini'" 
                      :PathLogo="require('../../assets/images/LogoGemini2.png')">
        </VUEDataTable>

        <div>
          <div class="ZMNuovaRigaScheda" style="background-color:#d2e8ff;margin-top:20px">
            <div class="col-md-6" style="float:left" v-if="GetTotaleRitenute().length != 0">
             <div class="panel panel-default">
              <div class="table-responsive">
                  <table class="table table-striped b-t b-light">
                      <thead>
                        <tr>
                          <th style="width: 13%; text-align: left;">Anno</th>
                          <th style="width: 77%; text-align: left;">Totale</th>
                          <th style="width: 10%; text-align: left;"></th>
                        </tr>
                      </thead>
                      <tbody>
                          <tr v-for="(Oggetto,index) in GetTotaleRitenute()" :key="index">
                            <td style="text-align: left;">{{ Oggetto.Anno }}</td>
                            <td style="text-align: left;">{{ Oggetto.Totale.toLocaleString('it-IT', {currency: 'EUR', style: 'currency'}) }} </td>
                            <td style="text-align: left;"></td>
                          </tr>
                      </tbody>
                  </table>
              </div>
             </div>
            </div>
            <div v-else class="col-md-6" style="float:left">&nbsp;</div>

            <div class="col-md-6" style="float:left" v-if="SchedaCliente.ListaFattureCliente.length != 0">
             <div class="panel panel-default">
              <div class="table-responsive">
                  <table class="table table-striped b-t b-light">
                      <thead>
                        <tr>
                          <th style="width: 40%; text-align: left;">Data</th>
                          <th style="width: 25%; text-align: left;">Numero</th>
                          <th style="width: 25%; text-align: left;">Totale</th>
                          <th style="width: 10%; text-align: left;">Info</th>
                        </tr>
                      </thead>
                      <tbody>
                          <tr v-for="Fattura in SchedaCliente.ListaFattureCliente" :key="Fattura.CHIAVE">
                            <td style="text-align: left;">{{ Fattura.DATA }}</td>
                            <td style="text-align: left;">{{ Fattura.NUMERO }}</td>
                            <td style="text-align: left;">{{ Fattura.TOTALE_FATTURA }} €</td>
                            <td><i class="ZMIconButton fa fa-folder-open text-info" style="cursor: pointer" title="Apri fattura" @click="OnClickApriFattura(Fattura)"></i></td>
                          </tr>
                      </tbody>
                  </table>
              </div>
             </div>
            </div>
            <div v-else class="col-md-6" style="float:left">&nbsp;</div>
          </div>
        </div>

      </div>
      <div v-if="Tabs.ActiveTab == 'Filiali'">

        <div style="margin-top: 1%;">
          <div class="ZMNuovaRigaScheda" style="float: left;font-size:14px; padding-right: 10px;padding-top: 5px;">
            <label style="margin-bottom: 0px;">Stato filiali</label>
          </div>
          <div style="float:left;width:11%;">
            <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="FiltroFiliali">
              <option value="Tutte">Tutte</option>
              <option value="Attive">Attive</option>           
              <option value="Disattive">Disattive</option>           
            </select>
          </div>
        </div>

        <div class="ZMNuovaRigaScheda">
          
          <VUEDataTable :DataTable="DataTableFiliali" :NomeProgramma="'Gemini'" :PathLogo="require('../../assets/images/LogoGemini2.png')">
            <template v-slot:RowAlternativa="{ Riga, DataTable }">
              <div></div>
              <div v-if="FiltroFiliali == 'Tutte' || 
                         (FiltroFiliali == 'Attive' && !Riga.Dati.FILIALE_DISATTIVATA.Valore) ||
                         (FiltroFiliali == 'Disattive' && Riga.Dati.FILIALE_DISATTIVATA.Valore)" >
                <VUEDataRowFilialiClienti :Riga="Riga" :DataTable="DataTable" :SchedaCliente="SchedaCliente"/>
              </div>
            </template>
          </VUEDataTable>
        </div>
      </div>
      <div  v-if="Tabs.ActiveTab == 'Recapiti'">
        <VUERecapiti :SchedaRecapiti="SchedaCliente.SchedaRecapiti" @onChange="OnRecapitiChange"></VUERecapiti>
      </div>
      <div  v-if="Tabs.ActiveTab == 'AgendaEventi'">
        <VUEDataTable :DataTable="SchedaCliente.DataTableAgenda" 
                      :NomeProgramma="'Gemini'" 
                      :PathLogo="require('../../assets/images/LogoGemini2.png')">
        </VUEDataTable>
      </div>

      <div v-if="Tabs.ActiveTab == 'Sconti'">
          <div class="row wrapper">  
              <div>
                <div style="width:1%;float:left">&nbsp;</div>
                <input type="text" style="width:17%;float:left" class="input-sm form-control" placeholder="Cerca per codice" v-model="Filtro.xCodice">
                <div style="width:1%;float:left">&nbsp;</div>
                <input type="text" style="width:25%;float:left" class="input-sm form-control" placeholder="Cerca per descrizione" v-model="Filtro.xNome">
                <div style="width:32.5%;float:left">&nbsp;</div>
                <div style="display: flex; align-items: center; width: 20%;">
                    <label style="font-weight: bold;">Sconto generale</label>
                    <input v-model="SchedaCliente.Dati.SCONTO_GENERALE" type="text" style="width:40%;" class="input-sm form-control">
                    <span class="input-group-text" style="font-weight: bold; margin-left: 1%; font-size: normal;">%</span>
                </div>
              </div>
          </div>
          
          <div class="panel panel-default">
              <div class="table-responsive">
                  <table class="table table-striped b-t b-light">
                      <thead>
                        <tr>
                          <th style="width: 15%; text-align: left;">Codice</th>
                          <th style="width: 50%; text-align: left;">Prodotti</th>
                          <th style="width: 10%; text-align: left;">Opzioni</th>
                          <th style="width: 10%; text-align: left;"></th>
                        </tr>
                      </thead>
                      <tbody>
                          <tr v-for="Prodotto in ProdottiFiltratiEPaginati" :key="Prodotto.CODICE">
                            <td style="text-align: left;">{{ Prodotto.CODICE }}</td>
                            <td style="text-align: left;">{{ Prodotto.DESCRIZIONE }}</td>

                            <td style="text-align: left;">
                              <select class="form-control" v-model="Prodotto.TIPO_SCONTO" @change="OnChangeSconto(Prodotto)">
                                <option :value="TipiSconti.Listino">Listino</option>
                                <option :value="TipiSconti.Sconto">Sconto</option>
                                <option :value="TipiSconti.Prezzo">Prezzo</option>
                              </select>
                            </td>
                            <td style="text-align: left;">
                              <div v-if="Prodotto.TIPO_SCONTO == TipiSconti.Listino" style="display: flex; justify-content: center; align-items: center; width: 100%;">
                                <span class="input-group-text" style="font-weight: bold; margin-left: 10%; font-size: normal;"> {{ Prodotto.PREZZO_IMPONIBILE }} €</span>
                              </div>

                              <div v-if="Prodotto.TIPO_SCONTO == TipiSconti.Sconto" style="display: flex; justify-content: center; align-items: center; width: 100%;">
                                <input v-model="Prodotto.VALORE" class="form-control" style="text-align: center; width: 65%;" type="text" aria-label="Sconto" @input="OnChangeSconto(Prodotto)">
                                <span class="input-group-text" style="font-weight: bold; margin-left: 9%; font-size: normal;">%</span>
                              </div>

                              <div v-if="Prodotto.TIPO_SCONTO == TipiSconti.Prezzo" style="display: flex; justify-content: center; align-items: center; width: 100%;">
                                <input v-model="Prodotto.VALORE" class="form-control" style="text-align: center; width: 65%;" type="text" aria-label="Prezzo" @input="OnChangeSconto(Prodotto)">
                                <span class="input-group-text" style="font-weight: bold; margin-left: 10%; font-size: normal;">€</span>
                              </div>
                            </td>
                          </tr>
                      </tbody>
                  </table>
              </div>
              <footer class="panel-footer">
                <div class="row">
                  <div class="col-sm-4 hidden-xs">
                  </div>
                  <div class="col-sm-4 text-center">
                    <small class="text-muted inline m-t-sm m-b-sm">
                      {{TestoPaginazione}}
                    </small>
                  </div>
                  <div class="col-sm-4 text-right text-center-xs">                
                    <ul class="pagination pagination-sm m-t-none m-b-none">
                      <li><a @click="OnClickFirstPage()"><i class="fa fa-backward"></i></a></li>
                      <li><a @click="PortaIndietroGruppoPagine()" ><i :class="{ ZMDisabled : !BackGruppoPagineVisibile }" class="fa fa-chevron-left"></i></a></li>
                      <li :class="{ active : IsActivePage(0)}"><a @click="OnClickChangePage(0)">{{Paginazione.StartGruppoPagine}}</a></li>
                      <li v-if="TastoPaginaVisibile(1)" :class="{ active : IsActivePage(1)}"><a @click="OnClickChangePage(1)">{{Paginazione.StartGruppoPagine + 1}}</a></li>
                      <li v-if="TastoPaginaVisibile(2)" :class="{ active : IsActivePage(2)}"><a @click="OnClickChangePage(2)">{{Paginazione.StartGruppoPagine + 2}}</a></li>
                      <li v-if="TastoPaginaVisibile(3)" :class="{ active : IsActivePage(3)}"><a @click="OnClickChangePage(3)">{{Paginazione.StartGruppoPagine + 3}}</a></li>
                      <li v-if="TastoPaginaVisibile(4)" :class="{ active : IsActivePage(4)}"><a @click="OnClickChangePage(4)">{{Paginazione.StartGruppoPagine + 4}}</a></li>
                      <li><a @click="PortaAvantiGruppoPagine()"><i :class="{ ZMDisabled : !ForwardGruppoPagineVisibile }" class="fa fa-chevron-right"></i></a></li>
                      <li><a @click="OnClickLastPage()"><i class="fa fa-forward"></i></a></li>
                    </ul>
                  </div>
                </div>
              </footer>
          </div>
      </div>

      
      <div v-if="Tabs.ActiveTab == 'SituazioneContabile'">
          <div class="panel panel-default">
              <div class="table-responsive" style="background-color: #d0e9ff;">
                  <div style="display: flex; align-items: center; width: 50%; background-color: #d0e9ff;; margin-top: 1%;">
                    <div style="display: flex; align-items: center; width: 30%;">
                      <label style="font-weight: bold;">Dal</label>
                      <input type="date" class="form-control" v-model="SchedaCliente.SchedaStatoContabile.DallaData">
                    </div>
                    <div style="width: 5%">&nbsp;</div>
                    <div style="display: flex; align-items: center; width: 30%;">
                      <label style="font-weight: bold;">Al</label>
                      <input type="date" class="form-control" v-model="SchedaCliente.SchedaStatoContabile.AllaData" >
                    </div>
                    <div style="width: 5%">&nbsp;</div>
                    <div>
                      <button class="btn btn-info" style="width:150%" @click="OnClickCerca">Cerca</button>
                    </div>
                  </div>
                  <table style="margin-top: 1%;" class="table table-striped b-t b-light">
                      <thead>
                        <tr>
                          <th style="width: 5%; text-align: left;">Data</th>
                          <th style="width: 5%; text-align: left;">Numero</th>
                          <th style="width: 20%; text-align: left;">Operazione</th>
                          <!-- <th style="width: 5%; text-align: left;">SDI</th> -->
                          <th style="width: 10%; text-align: left;">Dare</th>
                          <th style="width: 10%; text-align: left;">Avere</th>
                          <th style="width: 10%; text-align: left;">Saldo</th>
                          <th style="width: 5%; text-align: left;">Info</th>
                        </tr>
                      </thead>
                      <tbody>
                          <tr v-for="Documento in SchedaCliente.SchedaStatoContabile.ListaDocumenti" :key="Documento.CHIAVE">
                            <td style="text-align: left;">{{ Documento.LB_DATA_DOCUMENTO }}</td>
                            <td style="text-align: left;">{{ Documento.LB_NUMERO_DOCUMENTO }}</td>
                            <td style="text-align: left;">{{ Documento.MM_DESCRIZIONE_DOCUMENTO }}</td>
                            <!-- <td style=" align-items: center; justify-content: center;" v-if="Documento.INVIATA_ALLO_SDI == 'T'">
                              <i class="ZMIconButton fa fa-check-square text-info"></i>
                            </td> -->
                            <td style="text-align: left;font-weight: bold;">{{ Documento.LB_DARE }}</td>
                            <td style="text-align: left;font-weight: bold;">{{ Documento.LB_AVERE }}</td>
                            <td style="text-align: left;font-weight: bold;">{{ Documento.LB_SALDO }}</td>
                            <td>
                              <i class="ZMIconButton fa fa-folder-open text-info" @click="VisualizzaDocumento(Documento)"
                              :style="{ visibility: (Documento.Tipologia != 'A' && Documento.Tipologia != 'P') ? 'visible' : 'hidden' }"></i>
                            </td>
                          </tr>
                          <template v-if="SchedaCliente.SchedaStatoContabile.TotaliDocumenti.length > 0">
                            <td></td>
                            <tr v-if="SchedaCliente.SchedaStatoContabile.SaldoPeriodo">
                            <td colspan="3" style="text-align: right; font-weight: bold;">
                              Saldo periodo:
                            </td>
                            <td style="font-weight: bold;">
                              {{ SchedaCliente.SchedaStatoContabile.SaldoPeriodo.LB_DARE }}
                            </td>
                            <td style="font-weight: bold;">
                              {{ SchedaCliente.SchedaStatoContabile.SaldoPeriodo.LB_AVERE }}
                            </td>
                            <td style="font-weight: bold;">
                              {{ SchedaCliente.SchedaStatoContabile.SaldoPeriodo.LB_SALDO }}
                            </td>
                            <td></td>
                            </tr>
                            <tr v-for="(Totali, index) in SchedaCliente.SchedaStatoContabile.TotaliDocumenti" :key="index"> 
                              <td colspan="3" style="text-align: right; font-weight: bold;">{{ Totali.QRLabel10 }} &nbsp;</td>
                              <td style="font-weight: bold;">{{ Totali.LB_TOTALE_DARE }} </td>
                              <td style="font-weight: bold;">{{ Totali.LB_TOTALE_AVERE }}</td>
                              <td style="font-weight: bold;">{{ Totali.LB_TOTALE_SALDO }}</td>
                              <td></td>
                            </tr>
                          </template>
                      </tbody>
                  </table>
              </div>
          </div>
      </div>

       <div  v-if="Tabs.ActiveTab == 'Allegati'">
      <VUEAllegati :SchedaAllegati="SchedaCliente.SchedaAllegati" 
                  NomeCampoModello="Clienti"
                  @onChange="OnAllegatiChange" />
   </div>
      <footer class="ZMNuovaRigaScheda" style="height: 2px;">&nbsp;</footer>
    </div>
  </div>
</template>

<script>
 import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
 import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable2.js'
 import VUEDataTable from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2.vue'
 import { SystemInformation, DASHBOARD_FILTER_TYPES, PAGAMENTO_BOLLO, RUOLI, TIPO_SCONTO, TIPOLOGIA_RIGHE_CONTO_CLIENTE, NOME_PROGRAMMA } from '@/SystemInformation.js' 
 import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
 import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
 import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
 import VUEInputNaturaPagamenti from '@/components/InputComponents/VUEInputNaturaPagamenti.vue';
 import VUEInputCondPagamenti from '@/components/InputComponents/VUEInputCondPagamenti.vue';
 import VUEInputContoRibaCorrente from '@/components/InputComponents/VUEInputContoRibaCorrente.vue';
 import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
 import VUEInputZone from '@/components/InputComponents/VUEInputZone.vue';
 import VUEInputPartitaIVA from '../../components/InputComponents/VUEInputPartitaIVA.vue';
 import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
 import VUERecapiti, {TSchedaRecapiti} from '@/views/SchedeDatabase/ComponentMultiScheda/VUERecapiti.vue';
 import { TSchedaIndiceMovCliente, TSchedaIndiceNotaDiCredito, TSchedaIndiceFatture, TSchedaIndiceConferme, TSchedaIndicePreventivoMultiparametrico } from '@/NodiVuoti'
 import { TSchedaIndiceDDT, TSchedaIndiceDDTEntrante } from '@/NodiVuoti'
 import { TZFattElettronicaEsigibilita } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';
 import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
 import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
 import { TZFatturaElettronica } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js'
 import { TZGenericFunct } from '../../../../../../../../Librerie/VUE/ZGenericFunct';
 import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
 import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
 import VUEDataRowFilialiClienti from '@/components/DataRows/VUEDataRowFilialiClienti.vue';
 import * as XLSX from 'xlsx';
//  import { TZStringFunct } from '../../../../../../../../Librerie/VUE/ZStringFunct.js';
import VUESchedaFattura, { TSchedaFattura } from './VUESchedaFattura.vue';
import VUESchedaMovimento, {TSchedaMovimento} from './VUESchedaMovimento.vue';
import VUESchedaNotaDiCredito, {TSchedaNotaDiCredito} from './VUESchedaNotaDiCredito.vue';
import VUEAllegati, { TSchedaAllegati } from '../../components/VUEAllegati.vue';

  class TRitenuta 
  {
      constructor(Anno, Ritenuta, IdentificativoFiscale, SommaRitenutaDiFattureNonPagateMaCertificate, SommaRitenutaDiFattureRDA, RitenutaCertificata)
      {
        this.Anno                               = Anno;
        this.IdentificativoFiscale              = IdentificativoFiscale
        this.Ritenuta                           = Ritenuta;
        this.RitenutaPagataInizio               = 0;
        this.RitenutaPagata                     = 0
        this.SommaRitenutaDiFattureNonPagateMaCertificate = SommaRitenutaDiFattureNonPagateMaCertificate
        this.RitenutaCertificata                = RitenutaCertificata
        this.SommaRitenutaDiFattureRDA          = SommaRitenutaDiFattureRDA
        this.FattureVisualizzate                = false;
        this.VisualizzaIdentificativoFiscale    = true;
        this.LsFatture                          = [];
      }
  }

  export const TModEspansioneCliente = {
      modClContabilita : 0,
      modClAntincendio : 1
  }

  class TSchedaFilialeCliente
  {
    constructor()
    {
        this.DataTableFiliali = new TZDataTable('CHIAVE');
        this.DataTableFiliali.ClearColumns();
        var Colonna = this.DataTableFiliali.AddColumn('Sede',
                                                      TZDTableColumnType.typBoolean,
                                                      'SEDE',
                                                      "0%");
        Colonna = this.DataTableFiliali.AddColumn('Nome',
                                                TZDTableColumnType.typString,
                                                'NOME',
                                                "0%");
        Colonna.Necessario  = true;
        this.DataTableFiliali.AddColumn('Indirizzo',
                                        TZDTableColumnType.typString,
                                        'INDIRIZZO',
                                        "0%");
        this.DataTableFiliali.AddColumn('Comune',
                                        TZDTableColumnType.typString,
                                        'COMUNE',
                                        "0%");
        this.DataTableFiliali.AddColumn('Provincia',
                                        TZDTableColumnType.typSelect,
                                        'PROVINCIA',
                                        "0%");
        this.DataTableFiliali.AddColumn('Nr.civico',
                                        TZDTableColumnType.typString,
                                        'NR_CIVICO',
                                        "0%");
        this.DataTableFiliali.AddColumn('CAP',
                                        TZDTableColumnType.typString,
                                        'CAP',
                                        "0%");
        Colonna = this.DataTableFiliali.AddColumn('Zona',
                                        TZDTableColumnType.typSelect,
                                        'ZONA',
                                        "0%");
        Colonna.CampoSelectNonValido = -1
        Colonna.DefaultValue = -1
        Colonna = this.DataTableFiliali.AddColumn('Nazione',
                                                  TZDTableColumnType.typSelect,
                                                  'NAZIONE',
                                                  "0%");
        Colonna.DefaultValue = SystemInformation.Configurazioni.StatoItaliano
        Colonna = this.DataTableFiliali.AddColumn('Attiv. presenti',
                                                  TZDTableColumnType.typInteger,
                                                  'ATTIVAZIONI_PRESENTI',
                                                  "0%");
        /* this.DataTableFiliali.AddColumn('Chiusura',
                                        TZDTableColumnType.typString,
                                        'CHIUSURA',
                                        "0%"); */
        this.DataTableFiliali.AddColumn('Note',
                                        TZDTableColumnType.typString,
                                        'NOTE',
                                        "0%");
        this.DataTableFiliali.AddColumn('Filiale disattivata',
                                        TZDTableColumnType.typBoolean,
                                        'FILIALE_DISATTIVATA',
                                        "0%");
        this.DataTableFiliali.AddColumn('Posizione registro',
                                        TZDTableColumnType.typString,
                                        'POSIZIONE_REGISTRO',
                                        "0%");
        this.DataTableFiliali.AddColumn('posizione delle chiavi',
                                        TZDTableColumnType.typString,
                                        'POSIZIONE_CHIAVI',
                                        "0%");
        this.DataTableFiliali.AddColumn('Alert chiavi',
                                        TZDTableColumnType.typBoolean,
                                        'ALERT_CHIAVI',
                                        "0%");
        this.DataTableFiliali.AddColumn('Lat.indirizzo',
                                        TZDTableColumnType.typMilionesimi,
                                        'LAT_IND',
                                        "0%");
        this.DataTableFiliali.AddColumn('Long.indirizzo',
                                        TZDTableColumnType.typMilionesimi,
                                        'LONG_IND',
                                        "0%");


        var Self = this
        this.DataTableFiliali.AfterAddRow = function(ANewRow)
        {
            Self.SchedaRecapitiFiliali  = new TSchedaRecapiti(true, false, true, true)
            Self.SchedaRecapitiFiliali.ClearSchedaRecapiti()

            var DataTableOrari = new TZDataTable('CHIAVE');
            DataTableOrari.ClearColumns();
            var Colonna = DataTableOrari.AddColumn('Descrizione',
                                            TZDTableColumnType.typString,
                                            'DESCRIZIONE',
                                            "28%");
            Colonna.Necessario  = true;

            DataTableOrari.AddColumn('Dalle',
                                      TZDTableColumnType.typTime,
                                      'ORARIO_DAL1',
                                      "13%");
            DataTableOrari.AddColumn('Alle',
                                      TZDTableColumnType.typTime,
                                      'ORARIO_AL1',
                                      "13%");
            DataTableOrari.AddColumn('Dalle',
                                      TZDTableColumnType.typTime,
                                      'ORARIO_DAL2',
                                      "13%")
            DataTableOrari.AddColumn('Alle',
                                      TZDTableColumnType.typTime,
                                      'ORARIO_AL2',
                                      "13%");
            DataTableOrari.AddColumn('Lun.',
                                      TZDTableColumnType.typBoolean,
                                      'APERTO_LUNEDI',
                                      "13%");
            DataTableOrari.AddColumn('Mar.',
                                      TZDTableColumnType.typBoolean,
                                      'APERTO_MARTEDI',
                                      "13%");
            DataTableOrari.AddColumn('Mer.',
                                      TZDTableColumnType.typBoolean,
                                      'APERTO_MERCOLEDI',
                                      "13%");
            DataTableOrari.AddColumn('Gio.',
                                      TZDTableColumnType.typBoolean,
                                      'APERTO_GIOVEDI',
                                      "13%");
            DataTableOrari.AddColumn('Ven.',
                                      TZDTableColumnType.typBoolean,
                                      'APERTO_VENERDI',
                                      "13%");
            DataTableOrari.AddColumn('Sab.',
                                      TZDTableColumnType.typBoolean,
                                      'APERTO_SABATO',
                                      "13%");
            DataTableOrari.AddColumn('Dom.',
                                      TZDTableColumnType.typBoolean,
                                      'APERTO_DOMENICA',
                                      "13%");
                                      
            ANewRow.DataTableOrari = DataTableOrari

            ANewRow.DataTableOrari.Configurazione.Colonne[5].DefaultValue = true
            ANewRow.DataTableOrari.Configurazione.Colonne[6].DefaultValue = true
            ANewRow.DataTableOrari.Configurazione.Colonne[7].DefaultValue = true
            ANewRow.DataTableOrari.Configurazione.Colonne[8].DefaultValue = true
            ANewRow.DataTableOrari.Configurazione.Colonne[9].DefaultValue = true

            ANewRow.SchedaRecapitiFiliali = Self.SchedaRecapitiFiliali
        }
    }

    AssignDati(ArrayFiliali, ArrayOrari, ArrayTelefono)
    {
      this.DataTableFiliali.AssignDati(ArrayFiliali)

      var Self = this
      if(ArrayOrari.length != 0)
      {
        ArrayOrari.forEach(function(Orari)
        {
          for(let i = 0; i < Self.DataTableFiliali.Righe.length; i++)
            if(Orari.ID_FILIALE == Self.DataTableFiliali.Righe[i].Dati['CHIAVE'])
            {
              let Vettore = []
              Vettore.push(Orari)
              Self.DataTableFiliali.Righe[i].DataTableOrari.AssignDati(Vettore, true)
            }
        })
      }
      if(ArrayTelefono.length != 0)
      {
        ArrayTelefono.forEach(function(Telefono)
        {
          for(let i = 0; i < Self.DataTableFiliali.Righe.length; i++)
            if(Telefono.ID_FILIALE == Self.DataTableFiliali.Righe[i].Dati['CHIAVE'])
            {
              let Vettore = []
              Vettore.push(Telefono)
              Self.DataTableFiliali.Righe[i].SchedaRecapitiFiliali.AssignDati(Vettore, true)
            }
        })
      }
    }

    PrepareQueryParameters(Operazioni,ChiaveCliente)
    {
        var Self = this
        this.DataTableFiliali.Righe.forEach(function(Riga)
        {
            let Parametri = Self.DataTableFiliali.PrepareQueryParameters(Riga);
            
            if(Riga.Nuovo)
            {
              if(ChiaveCliente != -1)
                  Parametri.CHIAVE_CLIENTE = ChiaveCliente;
              Operazioni.push({
                                Query     : "InsertFiliale",
                                Parametri : Parametri,
                                ResetKeys : [2]
                              })
            }
            else
            {
              if(Riga.Eliminato)
              {
                if(Riga.DataTableOrari.Righe.length != 0)
                  Self.PrepareQueryParametersOrari(Operazioni, Riga, Riga.Dati['CHIAVE'])
              
                Riga.SchedaRecapitiFiliali.PrepareQueryParametersTelefono(Operazioni, Riga.Dati['CHIAVE'], 'ID_FILIALE', true)


                Operazioni.push({
                                  Query     : "DeleteFiliale",
                                  Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                })
              }
              else 
              {
                if(Riga.Modificato())
                  Operazioni.push({
                                    Query     : "UpdateFiliale",
                                    Parametri : Parametri
                                  })
              }
            }

            if(!Riga.Eliminato)
            {
              if(Riga.DataTableOrari.Righe.length != 0)
                Self.PrepareQueryParametersOrari(Operazioni, Riga, Riga.Dati['CHIAVE'])
              
              Riga.SchedaRecapitiFiliali.PrepareQueryParametersTelefono(Operazioni, Riga.Dati['CHIAVE'], 'ID_FILIALE', true)
            }
        });
        
    }

    PrepareQueryParametersOrari(Operazioni, Riga, ChiaveFiliale)
    {
        Riga.DataTableOrari.Righe.forEach(function(RigaOrario)
        {
            let Parametri = Riga.DataTableOrari.PrepareQueryParameters(RigaOrario);
            
            if(ChiaveFiliale != -1)
                  Parametri.CHIAVE_FILIALE = ChiaveFiliale;
            
            if(RigaOrario.Nuovo)
            {
              Operazioni.push({
                                Query     : "InsertOrario",
                                Parametri : Parametri,
                                ResetKeys : [3]
                              })
            }
            else
            {
              if(RigaOrario.Eliminato)
                Operazioni.push({
                                  Query     : "DeleteOrario",
                                  Parametri : { CHIAVE : RigaOrario.Dati['CHIAVE'] }
                                })
              else 
              {
                if(RigaOrario.Modificato())
                {
                    Operazioni.push({
                                      Query     : "UpdateOrario",
                                      Parametri : Parametri
                                    })
                }
              }
            }
        });   
    }

    ClearSchedaFiliali()
    {
        this.DataTableFiliali.AssignDati([]);
    }
    
    CreazioneNuovoClienteSedePrincipaleAutomatica()
    {
        var SedePrincipale = [{SEDE : 'T', NOME : 'Sede principale'}]
        this.DataTableFiliali.AssignDati(SedePrincipale)
    }
  }

  class TSchedaScontiProdottiCliente
  {
    constructor()
    {
      this.LsProdotti         = []
    }

    AssignDati(ArraySconti)
    {
      if(ArraySconti.length != 0)
      {
        this.LsProdotti = ArraySconti

        this.LsProdotti.forEach(function(Prodotto)
        {
          if(Prodotto.TIPO_SCONTO == null)
            Prodotto.TIPO_SCONTO = TIPO_SCONTO.Listino

          Prodotto.Nuovo      = false
          Prodotto.Modificato = false
          Prodotto.Eliminato  = false
        })
      }
    }

    PrepareQueryParameters(Operazioni,ChiaveCliente) 
    {
      var Self = this

      if(Self.LsProdotti.length != 0)
      {
        for(let i=0; i < Self.LsProdotti.length; i++)
        {
          if(Self.LsProdotti[i].TIPO_SCONTO == TIPO_SCONTO.Sconto)
          {
            if(Self.LsProdotti[i].Nuovo == true)
            {
              Operazioni.push({
                                        Query     : "InsertSconto",
                                        Parametri :  {
                                                        ID_CLIENTE       : ChiaveCliente, 
                                                        ID_PRODOTTO      : TSchedaGenerica.PrepareForRecordInteger(Self.LsProdotti[i].CHIAVE),
                                                        TIPO_SCONTO      : TIPO_SCONTO.Sconto,
                                                        VALORE           : TSchedaGenerica.PrepareForRecordInteger((Self.LsProdotti[i].VALORE * 100)),
                                                      },
                                        ResetKeys : [1]
                                    })
            }
            else 
            { 
              if(Self.LsProdotti[i].Eliminato == true) 
              {
                Operazioni.push({
                                            Query     : "DeleteSconto",
                                            Parametri :  {
                                                            ID_CLIENTE       : ChiaveCliente, 
                                                            ID_PRODOTTO      : Self.LsProdotti[i].CHIAVE,
                                                          },
                                          })
              }
              else if(Self.LsProdotti[i].Modificato == true)
              {
                Operazioni.push({
                                            Query     : "UpdateSconto",
                                            Parametri :  {
                                                            ID_CLIENTE       : ChiaveCliente, 
                                                            ID_PRODOTTO      : TSchedaGenerica.PrepareForRecordInteger(Self.LsProdotti[i].CHIAVE),
                                                            TIPO_SCONTO      : TIPO_SCONTO.Sconto,
                                                            VALORE           : TSchedaGenerica.PrepareForRecordInteger((Self.LsProdotti[i].VALORE * 100)),
                                                          },
                                        })
              }
            }
          }

          if(Self.LsProdotti[i].TIPO_SCONTO == TIPO_SCONTO.Prezzo)
          {
            if(Self.LsProdotti[i].Nuovo == true)
            {
              Operazioni.push({
                                        Query     : "InsertPrezzo",
                                        Parametri :  {
                                                        ID_CLIENTE       : ChiaveCliente, 
                                                        ID_PRODOTTO      : TSchedaGenerica.PrepareForRecordInteger(Self.LsProdotti[i].CHIAVE),
                                                        TIPO_SCONTO      : TIPO_SCONTO.Prezzo,
                                                        VALORE           : TSchedaGenerica.PrepareForRecordInteger((Self.LsProdotti[i].VALORE * 100)),
                                                      },
                                        ResetKeys : [1]
                                    })
            }
            else 
            { 
              if(Self.LsProdotti[i].Eliminato == true) 
              {
                Operazioni.push({
                                            Query     : "DeletePrezzo",
                                            Parametri :  {
                                                            ID_CLIENTE       : ChiaveCliente, 
                                                            ID_PRODOTTO      : Self.LsProdotti[i].CHIAVE,
                                                          },
                                          })
              }
              else if(Self.LsProdotti[i].Modificato == true)
              {
                Operazioni.push({
                                            Query     : "UpdatePrezzo",
                                            Parametri :  {
                                                            ID_CLIENTE       : ChiaveCliente, 
                                                            ID_PRODOTTO      : TSchedaGenerica.PrepareForRecordInteger(Self.LsProdotti[i].CHIAVE),
                                                            TIPO_SCONTO      : TIPO_SCONTO.Prezzo,
                                                            VALORE           : TSchedaGenerica.PrepareForRecordInteger((Self.LsProdotti[i].VALORE * 100)),
                                                          },
                                        })
              }
            }
          }

          if(Self.LsProdotti[i].TIPO_SCONTO == TIPO_SCONTO.Listino)
          {
            if(Self.LsProdotti[i].Eliminato == true) 
            {
              Operazioni.push({
                                Query     : "DeleteSconto",
                                Parametri :  {
                                                ID_CLIENTE       : ChiaveCliente, 
                                                ID_PRODOTTO      : Self.LsProdotti[i].CHIAVE,
                                              },
                              })

              Operazioni.push({
                                Query     : "DeletePrezzo",
                                Parametri :  {
                                                ID_CLIENTE       : ChiaveCliente, 
                                                ID_PRODOTTO      : Self.LsProdotti[i].CHIAVE,
                                              },
                              })
            }
          }
        }
      }
    }
  }

  class TSchedaSituzioneContabile
  {
    constructor()
    {
      this.ListaDocumenti = []
      this.TotaliDocumenti = []

      this.DallaData = TZDateFunct.FormatDateTime('yyyy-mm-dd', new Date(new Date().getFullYear(), 0, 1))
      this.AllaData = TZDateFunct.FormatDateTime('yyyy-mm-dd', new Date())
    }

    AssignDati(ArraySituazioneContabile)
    {
      this.ListaDocumenti  = []
      this.TotaliDocumenti = []
      this.SaldoPeriodo    = null

      ArraySituazioneContabile.LsConti.BAND_SUMMARY.forEach(item => {
                                                                    this.ListaDocumenti.push(item)
                                                                  })

      ArraySituazioneContabile.LsConti.BAND_FOOTER.forEach(item => {
                                                                    this.TotaliDocumenti.push(item)
                                                                  })
      if(ArraySituazioneContabile.SaldoPeriodo)
         this.SaldoPeriodo = ArraySituazioneContabile.SaldoPeriodo
    }
  }

  export class TSchedaCliente extends TSchedaGenerica
  {
    constructor(AdvQuery,ModEspansioneCliente)
    {
      super(AdvQuery)
      this.PopupConfermaDoppioCodiceFiscale           = false;
      this.PopupConfermaDoppioCodiceOnSuccess         = null;
      this.ListaFattureCliente                        = []

      if(ModEspansioneCliente == undefined)
        this.__ModEspansioneCliente = TModEspansioneCliente.modClContabilita
      else this.__ModEspansioneCliente = ModEspansioneCliente
      
      this.PopupAggiornaAvvisoFattura = {
                                          Visibile : false
                                        }
    }

    OnInitialization()
    {
      this.SchedaRecapiti  = new TSchedaRecapiti(false, true, false, false, true)
      this.SchedaRecapiti.ClearSchedaRecapiti()
      this.LsRitenute      = []
      this.SchedaFiliali   = new TSchedaFilialeCliente(); 
      this.SchedaFiliali.ClearSchedaFiliali()
      this.SchedaAllegati  = new TSchedaAllegati();
      this.SchedaAllegati.ClearAllegati()
      this.SchedaAllegati.PassAdvQuery(SystemInformation.AdvQuery)

      this.SchedaSconti = new TSchedaScontiProdottiCliente()
      this.ProdottiScontati   = []
      this.SchedaStatoContabile = new TSchedaSituzioneContabile()
      this.ListaDocumenti = []
      this.TotaliDocumenti = []

      this.DataTableRitenuteCliente = new TZDataTable('CHIAVE');
      this.DataTableRitenuteCliente.ClearColumns();
      var ColonnaRitenuteCliente = this.DataTableRitenuteCliente.AddColumn('Data',
                                      TZDTableColumnType.typDate,
                                      'DATA',
                                      "15%")
      ColonnaRitenuteCliente.DefaultValue = new Date()
      ColonnaRitenuteCliente = this.DataTableRitenuteCliente.AddColumn('Ritenute',
                                      TZDTableColumnType.typCentesimi,
                                      'RITENUTE',
                                      "10%");
      ColonnaRitenuteCliente.Necessario = true
      ColonnaRitenuteCliente = this.DataTableRitenuteCliente.AddColumn('Certificata',
                                      TZDTableColumnType.typBoolean,
                                      'CERTIFICATA',
                                      "5%");
      ColonnaRitenuteCliente = this.DataTableRitenuteCliente.AddColumn('Note',
                                      TZDTableColumnType.typMemo,
                                      'NOTE',
                                      "60%");

      // table agenda
      this.DataTableAgenda = new TZDataTable('CHIAVE');
      this.DataTableAgenda.ClearColumns();
      var Colonna = this.DataTableAgenda.AddColumn('Data',
                                      TZDTableColumnType.typDate,
                                      'DATA',
                                      "8%")
      Colonna.DefaultValue = new Date()
      Colonna = this.DataTableAgenda.AddColumn('Evento',
                                      TZDTableColumnType.typMemo,
                                      'EVENTO',
                                      "50%");
      Colonna.Necessario = true

    }

    GetClassName()
    {
      return 'TSchedaCliente';
    }

    CanRecord()
    { 
      return this.Dati.RAGIONE_SOCIALE.trim() != '' && 
            this.Dati.NAZIONE_EM_PIVA != -1 &&
            ((this.Dati.CODICE_FISCALE.trim() != '' || this.Dati.PARTITA_IVA.trim() != '') || SystemInformation.DeveloperMode) &&
            this.Dati.COND_PAGAMENTO != -1 &&
            this.Dati.COD_ENTE_SDI.trim().length == 7 &&
            (this.Dati.COD_UFFICIO_DEST.length == 6 || this.Dati.COD_UFFICIO_DEST == '') &&
            (this.Dati.NATURA_PAGAMENTO != -1 || this.Dati.IVA_SUGGERITA_CLIENTE != 0) &&
            (!this.Dati.SOGLIA_PRESENTE || (this.Dati.SOGLIA_X_NO_IVA != 0 && this.Dati.SOGLIA_NAT_PAG != -1)) &&
            (this.Dati.CODICE_FISCALE == '' || 
              TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk ||
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            (this.Dati.PARTITA_IVA == '' || 
              TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || 
              SystemInformation.DeveloperMode || 
            this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            this.SchedaAllegati.AllDataOk() &&
            this.SchedaFiliali.DataTableFiliali.AllDataOk() && 
            // (this.ControlloPresenzaDiAlmenoUnaFiliale() || this.SchedaFiliali.DataTableFiliali.Righe.length == 0) &&
            // (this.ControlloInserimentoFilialePrincipale() || this.SchedaFiliali.DataTableFiliali.Righe.length == 0) &&
            this.DataTableAgenda.AllDataOk() && 
            this.DataTableRitenuteCliente.AllDataOk()
    }
    
    ControlloPresenzaDiAlmenoUnaFiliale()
    {
      if(this.SchedaFiliali.DataTableFiliali.Righe.length != 0)
      {
        var Contatore = 0;
        for(let i = 0; i < this.SchedaFiliali.DataTableFiliali.Righe.length; i++)
        {
          if(this.SchedaFiliali.DataTableFiliali.Righe[i].Eliminato)
            Contatore++
        }
        if(Contatore != 0 && Contatore == this.SchedaFiliali.DataTableFiliali.Righe.length)
          return false
        return true
      }
      return false;
    }

    ControlloInserimentoFilialePrincipale()
    {  
      for(let i = 0; i < this.SchedaFiliali.DataTableFiliali.Righe.length; i++)
      {
        if(!this.SchedaFiliali.DataTableFiliali.Righe[i].Eliminato)
          if(this.SchedaFiliali.DataTableFiliali.Righe[i].Dati['SEDE'].Valore)
            return true
      }
      return false
    }
    
    GetDescrizione()
    {
      return this.Dati.RAGIONE_SOCIALE;
    }

    GetImageIndex()
    {
      if(this.Dati.PASSATA_AD_AVVOCATO)
          return 'ClienteAV.png'

      else return this.Dati.ATTIVO ? 'Cliente.png' : 'ClienteNonAttivo.png'
    }

    CheckDoppiaPartitaIva(PartitaIva,CodiceFiscale,CodiceCliente,OnSuccess,OnError)
    {
      var Self = this
      let Parametri = {}
      if(PartitaIva != undefined && PartitaIva != '')
        Parametri.PartitaIva = PartitaIva
      if(CodiceFiscale != undefined && CodiceFiscale != '' && CodiceFiscale != Self.CodiceFiscaleIniziale)
        Parametri.CodiceFiscale = CodiceFiscale
      if(CodiceCliente != undefined && CodiceCliente != '')
        Parametri.CodiceCliente = CodiceCliente
      SystemInformation.AdvQuery.GetSQL('Clienti',Parametri,
                                        function(Results)
                                        {
                                          let ArrayInfoPartitaIva    = SystemInformation.AdvQuery.FindResults(Results,"ControlloDoppiaPartitaIva");
                                          let ArrayInfoCodiceFiscale = SystemInformation.AdvQuery.FindResults(Results,"ControlloDoppioCodiceFiscale");
                                          let ArrayInfoCodiceCliente = SystemInformation.AdvQuery.FindResults(Results,"ControlloDoppioCodiceCliente");
                                          var Trovato = false
                                          if(ArrayInfoPartitaIva.length != 0)
                                          {
                                            if((ArrayInfoPartitaIva[0].CHIAVE != Self.Chiave) || (ArrayInfoPartitaIva.length > 1))
                                            {
                                              OnError('PARTITA IVA GIA PRESENTE, APPARTENENTE A ' + ArrayInfoPartitaIva[0].RAGIONE_SOCIALE,'','')
                                              Trovato = true                                           
                                            }
                                          }
                                          if(ArrayInfoCodiceFiscale.length != 0)
                                          {
                                            if((ArrayInfoCodiceFiscale[0].CHIAVE != Self.Chiave) || (ArrayInfoCodiceFiscale.length > 1))
                                              {
                                                Trovato = true    
                                                Self.PopupConfermaDoppioCodiceFiscale   = true
                                                Self.PopupConfermaDoppioCodiceOnSuccess = OnSuccess
                                              }
                                          }
                                          if(ArrayInfoCodiceCliente.length  != 0)
                                          {
                                            if(ArrayInfoCodiceCliente[0].CHIAVE != Self.Chiave)
                                            {
                                              OnError('CODICE CLIENTE GIA PRESENTE, APPARTENENTE A ' + ArrayInfoCodiceCliente[0].RAGIONE_SOCIALE,'','')
                                              Trovato = true                                           
                                            }
                                          }
                                          
                                          if(!Trovato)
                                            OnSuccess()
                                        },
                                        OnError,
                                        'ControlloDoppiaPartitaIva');
    }

    CreazioneNuoviDocumenti()
    {  
      return true
    }

    Nuovo()
    {  
      super.Nuovo() 
      SystemInformation.CaricaUltimoCodiceCliente((CodiceUltimoCliente) =>
                                                  {
                                                    this.CodiceUltimoCliente = CodiceUltimoCliente
                                                  }, true);
    }

    DoRegistra(OnSuccess,OnError)
    {  
      var Self = this
      this.CheckDoppiaPartitaIva(this.Dati.PARTITA_IVA,this.Dati.CODICE_FISCALE, this.Dati.CODICE,
                                function()
                                {
                                  Self.PopupConfermaDoppioCodiceFiscale   = false
                                  var ObjQuery = { Operazioni : [] };
                                   ObjQuery.Operazioni.push({
                                                            Query     : Self.IsNuovo() ? "Insert" : "Update",
                                                            Parametri : {
                                                                            CHIAVE_CLIENTE              : Self.Chiave, 
                                                                            RAGIONE_SOCIALE             : TSchedaGenerica.PrepareForRecordString(Self.Dati.RAGIONE_SOCIALE),
                                                                            ID_CAT_CLIENTE              : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ID_CAT_CLIENTE),
                                                                            CODICE              : TSchedaGenerica.PrepareForRecordString(Self.Dati.CODICE),
                                                                            ID_AMMINISTRATORE           : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ID_AMMINISTRATORE),
                                                                            ENTE_PUBBLICO               : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.ENTE_PUBBLICO),
                                                                            COD_ENTE_SDI                : TSchedaGenerica.PrepareForRecordString(Self.Dati.COD_ENTE_SDI),
                                                                            COD_UFFICIO_DEST            : TSchedaGenerica.PrepareForRecordString(Self.Dati.COD_UFFICIO_DEST),
                                                                            PARTITA_IVA                 : TSchedaGenerica.PrepareForRecordString(Self.Dati.PARTITA_IVA),
                                                                            NAZIONE_EM_PIVA             : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NAZIONE_EM_PIVA),
                                                                            CODICE_FISCALE              : TSchedaGenerica.PrepareForRecordString(Self.Dati.CODICE_FISCALE),
                                                                            INDIRIZZO_FATTURAZIONE      : TSchedaGenerica.PrepareForRecordString(Self.Dati.INDIRIZZO_FATTURAZIONE),
                                                                            NR_CIVICO_FATTURAZIONE      : TSchedaGenerica.PrepareForRecordString(Self.Dati.NR_CIVICO_FATTURAZIONE),
                                                                            PROVINCIA_FATTURAZIONE      : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PROVINCIA_FATTURAZIONE),
                                                                            COMUNE_FATTURAZIONE         : TSchedaGenerica.PrepareForRecordString(Self.Dati.COMUNE_FATTURAZIONE),
                                                                            CAP_FATTURAZIONE            : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAP_FATTURAZIONE),
                                                                            ZONA_FATTURAZIONE           : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ZONA_FATTURAZIONE),
                                                                            PRESSO                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.PRESSO),
                                                                            INDIRIZZO_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordString(Self.Dati.INDIRIZZO_SPEDIZIONE),
                                                                            NR_CIVICO_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordString(Self.Dati.NR_CIVICO_SPEDIZIONE),
                                                                            PROVINCIA_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PROVINCIA_SPEDIZIONE),
                                                                            NAZIONE_SPEDIZIONE          : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NAZIONE_SPEDIZIONE),
                                                                            COMUNE_SPEDIZIONE           : TSchedaGenerica.PrepareForRecordString(Self.Dati.COMUNE_SPEDIZIONE),
                                                                            CAP_SPEDIZIONE              : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAP_SPEDIZIONE),
                                                                            ZONA_SPEDIZIONE             : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ZONA_SPEDIZIONE),
                                                                            ESIGIBILITA_IVA             : TSchedaGenerica.PrepareForRecordString(Self.Dati.ESIGIBILITA_IVA),
                                                                            NATURA_PAGAMENTO            : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NATURA_PAGAMENTO),
                                                                            COND_PAGAMENTO              : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.COND_PAGAMENTO),
                                                                            NOTE_IN_FATTURA             : TSchedaGenerica.PrepareForRecordString(Self.Dati.NOTE_IN_FATTURA),
                                                                            NOTE                        : TSchedaGenerica.PrepareForRecordString(Self.Dati.NOTE),
                                                                            IBAN                        : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.IBAN)  : null,
                                                                            BIC                         : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.BIC)   : null,
                                                                            SWIFT                       : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.SWIFT) : null,
                                                                            BANCA_APPOGGIO              : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.BANCA) : null,
                                                                            ID_CONTO_CORRENTE           : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ContoCorrente.ID_CONTO_CORRENTE),
                                                                            PEC                         : TSchedaGenerica.PrepareForRecordString(Self.Dati.PEC),
                                                                            SOGLIA_X_NO_IVA             : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.SOGLIA_X_NO_IVA * 100),
                                                                            SOGLIA_PRESENTE             : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.SOGLIA_PRESENTE),
                                                                            SOGLIA_NAT_PAG              : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.SOGLIA_NAT_PAG),
                                                                            SPESE_INCASSO_DESCRIZIONE   : TSchedaGenerica.PrepareForRecordString(Self.Dati.SPESE_INCASSO_DESCRIZIONE),
                                                                            SPESE_INCASSO_SUGGERITE     : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.SPESE_INCASSO_SUGGERITE * 100),
                                                                            IVA_SUGGERITA_CLIENTE       : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.IVA_SUGGERITA_CLIENTE * 10),
                                                                            BOLLO                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.BOLLO),
                                                                            ATTIVO                      : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.ATTIVO),
                                                                            TIPOLOGIA_SCISSIONE         : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.TIPOLOGIA_SCISSIONE),
                                                                            RITENUTA                    : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.RITENUTA * 10),
                                                                            CONDOMINIO_CON_FATTURAZIONE : Self.Dati.ID_AMMINISTRATORE != -1? TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.CONDOMINIO_CON_FATTURAZIONE) : 'F',
                                                                            IS_A_CONDOMINIO             : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.IS_A_CONDOMINIO),
                                                                            PASSATA_AD_AVVOCATO         : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.PASSATA_AD_AVVOCATO),
                                                                            SCONTO_GENERALE             : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.SCONTO_GENERALE * 100),
                                                                            IS_CLIENTE                  : true,
                                                                            IS_FORNITORE                : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.IS_FORNITORE),
                                                                        }
                                                          });

                                  Self.SchedaFiliali.PrepareQueryParameters(ObjQuery.Operazioni,Self.Chiave)
                                  Self.SchedaRecapiti.PrepareQueryParametersTelefono(ObjQuery.Operazioni,Self.Chiave,'ID_CLIENTE')
                                  Self.SchedaAllegati.PrepareQueryParameters(ObjQuery.Operazioni,'ID_CLIENTE') 

                                  Self.SchedaSconti.PrepareQueryParameters(ObjQuery.Operazioni,Self.Chiave)

                                  if(!Self.IsNuovo() && !Self.Dati.ModificaTabelle)
                                    Self.RicercaEdElaboraDatiModificati(Self);

                                  Self.LsRitenute.forEach(function(Ritenuta)
                                  { 
                                    if(Ritenuta.RitenutaPagata != Ritenuta.RitenutaPagataInizio) // CONTROLLA VALORE INIZIALE CON VALORE ATTUALE
                                    {
                                      ObjQuery.Operazioni.push({
                                                                Query     : 'Ritenuta',
                                                                Parametri : {
                                                                              CHIAVE_CLIENTE        : Self.Chiave,
                                                                              Anno                  : TSchedaGenerica.PrepareForRecordInteger(Ritenuta.Anno),
                                                                              Ritenuta              : TSchedaGenerica.PrepareForRecordInteger(Ritenuta.RitenutaPagata * 100),
                                                                              IdentificativoFiscale : TSchedaGenerica.PrepareForRecordString(Ritenuta.IdentificativoFiscale)
                                                                            }
                                                              });
                                    } 
                                    
                                  })

                                  if(!Self.PassataAdAvvocatoDatoServer && Self.Dati.PASSATA_AD_AVVOCATO)
                                  {
                                    Self.DataTableAgenda.AddRowsOnTop(1)
                                    Self.DataTableAgenda.Righe[0].Dati['EVENTO'].Valore = 'Segnalazione di una o più fatture passate all\'avvocato'
                                    Self.DataTableAgenda.Righe[0].Dati['DATA'].Valore   = TSchedaGenerica.PrepareForRecordDate(new Date())
                                  }

                                  Self.DataTableAgenda.Righe.forEach(function(Riga)
                                  {
                                      if(Riga.Nuovo)
                                      {
                                          let Parametri = Self.DataTableAgenda.PrepareQueryParameters(Riga);
                                          if(!Self.IsNuovo())
                                            Parametri.CHIAVE_CLIENTE = Self.Chiave;
                                          ObjQuery.Operazioni.push({
                                                                    Query     : "InsertEvento",
                                                                    Parametri : Parametri,
                                                                    ResetKeys : [2]
                                                                  })
                                      }
                                      else
                                      {
                                        if(Riga.Eliminato)
                                            ObjQuery.Operazioni.push({
                                                                      Query     : "DeleteEvento",
                                                                      Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                                                    })
                                        else 
                                        {
                                          if(Riga.Modificato())
                                              ObjQuery.Operazioni.push({
                                                                        Query     : "UpdateEvento",
                                                                        Parametri : Self.DataTableAgenda.PrepareQueryParameters(Riga)
                                                                      })
                                        }
                                      }
                                  });

                                  Self.DataTableRitenuteCliente.Righe.forEach(function(Riga)
                                  {
                                      if(Riga.Nuovo)
                                      {
                                          let Parametri = Self.DataTableRitenuteCliente.PrepareQueryParameters(Riga);
                                          if(!Self.IsNuovo())
                                            Parametri.CHIAVE_CLIENTE = Self.Chiave;
                                          Parametri.RITENUTA = TSchedaGenerica.PrepareForRecordInteger(Riga.RITENUTA * 100);
                                          ObjQuery.Operazioni.push({
                                                                    Query     : "InsertRitenuteCliente",
                                                                    Parametri :  Parametri,
                                                                    ResetKeys : [2]
                                                                  })
                                      }
                                      else
                                      {
                                        if(Riga.Eliminato)
                                            ObjQuery.Operazioni.push({
                                                                      Query     : "DeleteRitenuteCliente",
                                                                      Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                                                    })
                                        else 
                                        {
                                          if(Riga.Modificato())
                                              ObjQuery.Operazioni.push({
                                                                        Query     : "UpdateRitenuteCliente",
                                                                        Parametri : Self.DataTableRitenuteCliente.PrepareQueryParameters(Riga)
                                                                      })
                                        }
                                      }
                                  });
                                  
                                  if(Self.IsNuovo() && Self.Dati.CODICE.trim() != '')
                                  {
                                      ObjQuery.Operazioni.push({
                                                                  Query     : "InsertUltimoCodiceCliente",
                                                                  Parametri : { ULTIMO_CODICE_CLIENTE : Self.Dati.CODICE},
                                                                })
                                  }
                                  
                                  if(Self.CambiaAvvisiDocumentiAttivi)
                                  {
                                    ObjQuery.Operazioni.push({
                                                                Query     : "AggiornaAvvisiDiFattura",
                                                                Parametri : { 
                                                                              ChiaveCliente          : Self.Chiave,
                                                                              COD_ENTE_SDI           : Self.Dati.COD_ENTE_SDI,
                                                                              PEC                    : Self.Dati.PEC,
                                                                              INDIRIZZO_FATTURAZIONE : Self.Dati.INDIRIZZO_FATTURAZIONE,
                                                                              NR_CIVICO_FATTURAZIONE : Self.Dati.NR_CIVICO_FATTURAZIONE,
                                                                              CAP_FATTURAZIONE       : Self.Dati.CAP_FATTURAZIONE,
                                                                              COMUNE_FATTURAZIONE    : Self.Dati.COMUNE_FATTURAZIONE,
                                                                              PROVINCIA_FATTURAZIONE : Self.Dati.PROVINCIA_FATTURAZIONE,
                                                                              CODICE_FISCALE         : Self.Dati.CODICE_FISCALE,
                                                                              NAZIONE_EM_PIVA        : Self.Dati.NAZIONE_EM_PIVA,
                                                                              PARTITA_IVA            : Self.Dati.PARTITA_IVA
                                                                            },
                                                              })
                                    ObjQuery.Operazioni.push({
                                                                Query     : "AggiornaDTTInCorso",
                                                                Parametri : { 
                                                                              ChiaveCliente          : Self.Chiave,
                                                                              INDIRIZZO_FATTURAZIONE : Self.Dati.INDIRIZZO_FATTURAZIONE,
                                                                              NR_CIVICO_FATTURAZIONE : Self.Dati.NR_CIVICO_FATTURAZIONE,
                                                                              CAP_FATTURAZIONE       : Self.Dati.CAP_FATTURAZIONE,
                                                                              COMUNE_FATTURAZIONE    : Self.Dati.COMUNE_FATTURAZIONE,
                                                                              PROVINCIA_FATTURAZIONE : Self.Dati.PROVINCIA_FATTURAZIONE,
                                                                              CODICE_FISCALE         : Self.Dati.CODICE_FISCALE,
                                                                              NAZIONE_EM_PIVA        : Self.Dati.NAZIONE_EM_PIVA,
                                                                              PARTITA_IVA            : Self.Dati.PARTITA_IVA
                                                                            },
                                                              })
                                    ObjQuery.Operazioni.push({
                                                                Query     : "AggiornaPreventiviSospesi",
                                                                Parametri : { 
                                                                              ChiaveCliente          : Self.Chiave,
                                                                              COD_ENTE_SDI           : Self.Dati.COD_ENTE_SDI,
                                                                              PEC                    : Self.Dati.PEC,
                                                                              INDIRIZZO_FATTURAZIONE : Self.Dati.INDIRIZZO_FATTURAZIONE,
                                                                              NR_CIVICO_FATTURAZIONE : Self.Dati.NR_CIVICO_FATTURAZIONE,
                                                                              CAP_FATTURAZIONE       : Self.Dati.CAP_FATTURAZIONE,
                                                                              COMUNE_FATTURAZIONE    : Self.Dati.COMUNE_FATTURAZIONE,
                                                                              PROVINCIA_FATTURAZIONE : Self.Dati.PROVINCIA_FATTURAZIONE,
                                                                              CODICE_FISCALE         : Self.Dati.CODICE_FISCALE,
                                                                              NAZIONE_EM_PIVA        : Self.Dati.NAZIONE_EM_PIVA,
                                                                              PARTITA_IVA            : Self.Dati.PARTITA_IVA
                                                                            },
                                                              })
                                  }
                                  // if(Self.SchedaSconti.ProdottiScontati.length != 0)
                                  // {
                                  //   for(let i=0; i< Self.SchedaSconti.ProdottiScontati.length; i++)
                                  //   {
                                  //     if(Self.SchedaSconti.ProdottiScontati[i].Listino == true)
                                  //     {
                                  //       ObjQuery.Operazioni.push({
                                  //                                   Query     : "InsertListino",
                                  //                                   Parametri :  {
                                  //                                                   ID_CLIENTE       : Self.Chiave, 
                                  //                                                   ID_PRODOTTO      : TSchedaGenerica.PrepareForRecordInteger(Self.SchedaSconti.ProdottiScontati[i].CHIAVE),
                                  //                                                   TIPO_SCONTO      : 'L',
                                  //                                                   VALORE           : TSchedaGenerica.PrepareForRecordInteger(Self.SchedaSconti.ProdottiScontati[i].PREZZO_IMPONIBILE),
                                  //                                                 },
                                  //                                   ResetKeys : [1]
                                  //                               })
                                  //     }
                                  //   }
                                  // }

                                  Self.AdvQuery.PostSQL('Clienti',ObjQuery,function(Response)
                                  { 
                                    SystemInformation.GetConfigurazioni(function()
                                    {
                                      ObjQuery = {};
                                      if(Self.Chiave == -1)
                                          Self.Chiave = Response.NewKey1;
                                      if(Self.SchedaAllegati.LsFileDaEliminare.length != 0)
                                          Self.SchedaAllegati.DeleteFileDaEliminare()
                                      Self.Dati.ModificaTabellaFiliali      = false;
                                      Self.Dati.ModificaTabellaOrariFiliali = false;
                                      Self.Dati.ModificaTabelle             = false;
                                      Self.Dati.ModificaTabellaAllegati     = false;
                                      Self.CreateSnapshot();
                                      OnSuccess();
                                    })
                                  },
                                  function(HTTPError,SubHTTPError,Response)
                                  {
                                    OnError(HTTPError,SubHTTPError,Response);
                                  });
                                },
                                function(HTTPError,SubHTTPError,Response)
                                {
                                  OnError(HTTPError,SubHTTPError,Response);
                                })
    }

    RicercaEdElaboraDatiModificati(Self)
    {
    var Cambiamenti = Self.ComparaOggetti(Self);
    Self.ElaboraDatiModificati(Self, Cambiamenti);
    }

    ComparaOggetti(Self)
    {
      var Cambiamenti = {};
      var Oggetto1 = Self.Dati;
      var Oggetto2 = Self.CopiaDeiDati;
      const SetChiavi = new Set([...Object.keys(Oggetto1), ...Object.keys(Oggetto2)]);

      SetChiavi.forEach(function(Chiave)
      {
        if(Self.ControllaSeSalvareInCambiamenti(Chiave, Oggetto1, Oggetto2))
          Cambiamenti[Chiave] = {
                                  ValoreNuovo  : Oggetto1[Chiave],
                                  ValoreVecchio: Oggetto2[Chiave]
                                };
      });

      return Cambiamenti;
    }

    ControllaSeSalvareInCambiamenti(Chiave, Oggetto1, Oggetto2)
    {
      return (Oggetto1[Chiave] !== Oggetto2[Chiave] && typeof Oggetto1[Chiave] != 'object') ||
            (typeof Oggetto1[Chiave] == 'object' && Oggetto1[Chiave] != null && (Oggetto1[Chiave]?.IBAN != Oggetto2[Chiave]?.IBAN) ||
                                                                                (Oggetto1[Chiave]?.BIC != Oggetto2[Chiave]?.BIC) ||
                                                                                (Oggetto1[Chiave]?.SWIFT != Oggetto2[Chiave]?.SWIFT) ||
                                                                                (Oggetto1[Chiave]?.BANCA_APPOGGIO != Oggetto2[Chiave]?.BANCA_APPOGGIO) ||
                                                                                (Oggetto1[Chiave]?.CONTO_RIBA != Oggetto2[Chiave]?.CONTO_RIBA));
    }

    ElaboraDatiModificati(Self, Cambiamenti)
    {
      var ArrayCambiamenti = [];
      var Chiave;
      var MessaggioEvento = '';

      for(Chiave in Cambiamenti)
      {
        ArrayCambiamenti.push({
                                Chiave: Chiave,
                                ValoreVecchio: Cambiamenti[Chiave].ValoreVecchio,
                                ValoreNuovo: Cambiamenti[Chiave].ValoreNuovo
                              });
      }

      MessaggioEvento = Self.ComponiMessaggioEvento(MessaggioEvento, ArrayCambiamenti, Self);

      Self.InserisciEventoInArrayAgenda(Self, MessaggioEvento);
    }

    ComponiMessaggioEvento(MessaggioEvento, ArrayCambiamenti, Self)
    {
      for(var Elemento of ArrayCambiamenti)
      {
        if(Elemento.Chiave == 'ContoCorrente')
          MessaggioEvento += `\nNel conto corrente sono stati cambiati i seguenti dati: \n${Self.ComponiMessaggioCambiamentiContoCorrente(Elemento)}`;
        else if(typeof Elemento.ValoreNuovo == 'boolean')
          if(Elemento.ValoreNuovo)
            MessaggioEvento += `Messo la spunta a ${Self.SistemaNomeChiave(Elemento.Chiave)},\n`;
          else
            MessaggioEvento += `Tolto la spunta a ${Self.SistemaNomeChiave(Elemento.Chiave)}\n`;
        else if(Elemento.ValoreVecchio == '' && Elemento.Chiave != 'RITENUTA' && Elemento.Chiave != 'IVA_SUGGERITA_CLIENTE')
          MessaggioEvento += `Aggiunto [${Self.SistemaNomeChiave(Elemento.Chiave)}],\n`;
        else if(Elemento.ValoreNuovo == '' && Elemento.Chiave != 'RITENUTA' && Elemento.Chiave != 'IVA_SUGGERITA_CLIENTE')
          MessaggioEvento += `Eliminato [${Self.SistemaNomeChiave(Elemento.Chiave)}],\n`;
        else
          MessaggioEvento += `Modificato ${Self.SistemaNomeChiave(Elemento.Chiave)} da [${Self.SistemaNomeValore(Self, Elemento.ValoreVecchio, Elemento.Chiave)}] a [${Self.SistemaNomeValore(Self, Elemento.ValoreNuovo, Elemento.Chiave)}],\n`;
      }

        MessaggioEvento = MessaggioEvento.replace(/_/g, ' ').replace(/,\s*$/, '');

        MessaggioEvento += ` da ${SystemInformation.UserInformation.RagioneSociale}`;

        return MessaggioEvento;
    }

    SistemaNomeChiave(Chiave)
    {
      switch(Chiave)
      {
        case 'COD_ENTE_SDI'                : return 'Codice SDI';
        case 'COD_UFFICIO_DEST'            : return 'Codice ufficio destinatario';
        case 'NAZIONE_EM_PIVA'             : return 'Nazione partita IVA';
        case 'COND_PAGAMENTO'              : return 'Condizione di pagamento';
        case 'IVA_SUGGERITA_CLIENTE'       : return 'IVA';
        case 'SOGLIA_X_NO_IVA'             : return 'Soglia';
        case 'SOGLIA_PRESENTE'             : return 'Soglia esenzione IVA';
        case 'SOGLIA_NAT_PAG'              : return 'Natura pagamento soglia';
        case 'SPESE INCASSO SUGGERITE'     : return 'Spese';
        case 'ESIGIBILITA_IVA'             : return 'Esigibilità IVA';
        case 'ModificaRitenute'            : return 'Modifica ritenute';
        case 'ModificaTabellaFiliali'      : return 'Modifica tabella filiali';
        case 'ModificaTabellaOrariFiliali' : return 'Modifica tabella orari filiali';
      }

      return Chiave;
    }

    SistemaNomeValore(Self, Valore, Chiave)
    {
      switch (Valore)
      {
        case '-1'                                               : 
        case -1                                                 : return '-';

        case TZFattElettronicaEsigibilita.esgImmediata          :
        case TZFattElettronicaEsigibilita.esgDifferita          :
        case TZFattElettronicaEsigibilita.esgSplitPayment       : return Self.ConvertiNomeEsigibilitaIva(Valore);

        case PAGAMENTO_BOLLO.NessunBollo                        :
        case PAGAMENTO_BOLLO.PagatoDaNoi                        :
        case PAGAMENTO_BOLLO.PagatoDalCliente                   : return Self.ConvertiNomeBollo(Valore);
      }

      switch (Chiave)
      {
        case 'NATURA_PAGAMENTO'                                 :
        case 'SOGLIA_NAT_PAG'                                   : return Self.ConvertiNaturaPagamento(Valore);

        case 'COND_PAGAMENTO'                                   : return Self.ConvertiCondizioniPagamento(Valore);

        case 'PROVINCIA_SPEDIZIONE'                             : 
        case 'PROVINCIA_FATTURAZIONE'                           : return Self.ConvertiProvincia(Valore);

        case 'NAZIONE_EM_PIVA'                                  : 
        case 'NAZIONE_SPEDIZIONE'                               : return Self.ConvertiNazione(Valore);

        case 'ZONA_FATTURAZIONE'                                :
        case 'ZONA_SPEDIZIONE'                                  : return Self.ConvertiZona(Valore);
      }

      return Valore;
    }

    ConvertiNomeEsigibilitaIva(Chiave)
    {
      if(Chiave == TZFattElettronicaEsigibilita.esgImmediata)
        return 'Immediata';
      else if(Chiave == TZFattElettronicaEsigibilita.esgDifferita)
        return 'Differita';
      
      return 'Scissione dei pagamenti';
    }
    
    ConvertiNomeBollo(Chiave)
    {
      if(Chiave == PAGAMENTO_BOLLO.NessunBollo)
        return 'Nessun bollo';
      else if(Chiave == PAGAMENTO_BOLLO.PagatoDaNoi)
        return 'Pagato da noi';
      
      return 'Pagato dal cliente';
    }

    ConvertiNaturaPagamento(Valore)
    {
      var LsNaturaPagamenti = TZFatturaElettronica.GetLsNaturaPagamenti();

      for(var Chiave in LsNaturaPagamenti)
        if(LsNaturaPagamenti[Chiave].Id == Valore)
          return LsNaturaPagamenti[Chiave].Descrizione;
    }

    ConvertiCondizioniPagamento(Valore)
    {
      var LsCondizioniPagamento = SystemInformation.Configurazioni.CondPagamenti;

      for(var ChiaveCiclo in LsCondizioniPagamento)
        if(LsCondizioniPagamento[ChiaveCiclo].CHIAVE == Valore)
          return LsCondizioniPagamento[ChiaveCiclo].DESCRIZIONE;
    }

    ConvertiProvincia(Valore)
    {
      var LsProvince = SystemInformation.Configurazioni.Province;

      for(var ChiaveCiclo in LsProvince)
        if(LsProvince[ChiaveCiclo].CHIAVE == Valore)
          return LsProvince[ChiaveCiclo].NOME;
    }

    ConvertiNazione(Valore)
    {
      var LsNazioni = SystemInformation.Configurazioni.Nazioni;

      for(var ChiaveCiclo in LsNazioni)
        if(LsNazioni[ChiaveCiclo].CHIAVE == Valore)
          return LsNazioni[ChiaveCiclo].NOME;
    }

    ConvertiZona(Valore)
    {
      var LsZone = SystemInformation.Configurazioni.Zone;

      for(var ChiaveCiclo in LsZone)
        if(LsZone[ChiaveCiclo].CHIAVE == Valore)
          return LsZone[ChiaveCiclo].DESCRIZIONE;
    }

    ComponiMessaggioCambiamentiContoCorrente(OggettoContoCorrente)
    {
      var MessaggioCambiamentiContoCorrente = '';
      
      for(var Chiave in OggettoContoCorrente.ValoreVecchio)
        if(OggettoContoCorrente.ValoreVecchio[Chiave] != OggettoContoCorrente.ValoreNuovo[Chiave])
          MessaggioCambiamentiContoCorrente += `[${Chiave}] da [${OggettoContoCorrente.ValoreVecchio[Chiave]}] a [${OggettoContoCorrente.ValoreNuovo[Chiave]}],\n`

      return MessaggioCambiamentiContoCorrente;
    }

    InserisciEventoInArrayAgenda(Self, MessaggioEvento)
    {
      if(typeof MessaggioEvento === 'string')
      {
        Self.DataTableAgenda.AddRowsOnTop(1);
        Self.DataTableAgenda.Righe[0].Dati['EVENTO'].Valore              = MessaggioEvento;
        Self.DataTableAgenda.Righe[0].Dati['DATA'].Valore                = TSchedaGenerica.PrepareForRecordDate(new Date());
      }
      else
        alert('Il messaggio che si sta cercando di inserire non è di tipo string');
    }

    Registra(OnSuccess,OnError)
    {
        this.CambiaAvvisiDocumentiAttivi = false
        this.OnSuccessDoRegistra = OnSuccess
        this.OnErrorDoRegistra = OnError
        if(this.CanRecord())
        { 
          var Self = this
          if((this.Confronti.VecchiaPEC           != this.Dati.PEC || 
              this.Confronti.VecchioCodiceSDI     != this.Dati.COD_ENTE_SDI ||
              this.Confronti.VecchiaFatturazione  != this.Dati.INDIRIZZO_FATTURAZIONE ||
              this.Confronti.VecchioNumCivico     != this.Dati.NR_CIVICO_FATTURAZIONE ||
              this.Confronti.VecchioCAP           != this.Dati.CAP_FATTURAZIONE ||
              this.Confronti.VecchioComune        != this.Dati.COMUNE_FATTURAZIONE ||
              this.Confronti.VecchiaProvincia     != this.Dati.PROVINCIA_FATTURAZIONE ||
              this.Confronti.VecchioCodiceFiscale != this.Dati.CODICE_FISCALE ||
              this.Confronti.VecchiaNazionePIVA   != this.Dati.NAZIONE_EM_PIVA ||
              this.Confronti.VecchiaPIVA          != this.Dati.PARTITA_IVA) &&
              !this.IsNuovo())
            SystemInformation.AdvQuery.GetSQL('Fatture',{ ChiaveCliente : this.Chiave },
                                              function(Results)
                                              {
                                                  let ArrayInfo     = SystemInformation.AdvQuery.FindResults(Results,"QryFindAvvisiFattura");
                                                  let ArrayInfoPrev = SystemInformation.AdvQuery.FindResults(Results,"QryFindPreventiviNonConfermati");
                                                  let ArrayInfoDDT  = SystemInformation.AdvQuery.FindResults(Results,"QryFindDDTAperti");
                                                  let ArrayInfoNote = SystemInformation.AdvQuery.FindResults(Results,"QryAvvisiNoteCredito");
                                                  
                                                  if(ArrayInfo     != undefined && 
                                                    ArrayInfoPrev != undefined &&
                                                    ArrayInfoDDT  != undefined &&
                                                    ArrayInfoNote != undefined)
                                                  {
                                                    if(ArrayInfo[0].NUMERO_AVVISI != 0)
                                                    {
                                                      Self.PopupAggiornaAvvisoFattura.MessaggioPopup = 'Sono stati modificati i dati di fatturazione.\n\nVuoi aggiornare fatture aperte, DDT in corso, preventivi sospesi?'
                                                      Self.PopupAggiornaAvvisoFattura.Visibile       = true
                                                    }
                                                    else Self.DoRegistra(OnSuccess,OnError)
                                                  }
                                              },
                                              function(HTTPError,SubHTTPError,Response)
                                              {
                                                  SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                              },
                                              'FindAvvisiFatture')
          else this.DoRegistra(OnSuccess,OnError)
        }
    }

    Disponi(OnSuccess)
    {  
      var Self = this;
      this.LsRitenute = []
      if(this.InEliminazione) return;
      this.AdvQuery.ExecuteExternalScript('SelectDatiCliente',{ CHIAVE        : Self.Chiave, 
                                                                ChiaveCliente : Self.Chiave,
                                                                DataDal       : Self.SchedaStatoContabile.DallaData,
                                                                DataAl        : Self.SchedaStatoContabile.AllaData,
                                                                StatoConti    : true
                                                              },
                                          function(Results)
                                          {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo                = Results.Dettaglio
                                            let ArrayInfoFiliali         = Results.DatiClienteFiliali
                                            let ArrayInfoAgenda          = Results.DatiClienteAgenda
                                            let ArrayInfoTelefono        = Results.DatiClienteTelefono
                                            let ArrayInfoRitenuteCliente = Results.DatiRitenuteCliente
                                            let ArrayInfoRitenute        = Results.DatiClienteRitenute
                                            let ArrayInfoRitenutePagate  = Results.DatiClienteRitenutePagate
                                            let ArrayOrariFiliali        = Results.DatiClienteFilialiOrari
                                            let ArrayTelefonoFiliali     = Results.DatiClienteFilialiTelefoni
                                            let ArrayInfoScontiProdotti  = Results.DatiClienteScontiProdotti
                                            let ArrayInfoAllegati        = Results.DatiClienteAllegati
                                            let ArrayListaFattureCliente = Results.ListaFattureCliente
                                            
                                            if (Results.DatiSituazioneContabile)
                                            {
                                              let ArraySituazioneContabile = Results.DatiSituazioneContabile;
                                              Self.SchedaStatoContabile.AssignDati(ArraySituazioneContabile);
                                            } 

                                            if(ArrayInfo != undefined && 
                                               ArrayInfoFiliali != undefined && 
                                               ArrayInfoRitenute != undefined && 
                                               ArrayInfoRitenutePagate != undefined && 
                                               ArrayInfoAllegati != undefined &&
                                               ArrayListaFattureCliente != undefined)
                                            {
                                              if(ArrayListaFattureCliente.length != 0)
                                              {
                                                Self.ListaFattureCliente = ArrayListaFattureCliente
                                                Self.ListaFattureCliente.forEach(function(Fattura)
                                                {
                                                  Fattura.DATA = TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Fattura.DATA)) 
                                                })
                                              }

                                              if(ArrayInfoRitenute.length != 0)
                                              {
                                                  ArrayInfoRitenute[0].Ritenute.forEach(function(ARitenuta)
                                                  {
                                                    Self.LsRitenute.push(new TRitenuta(ARitenuta.Anno, 
                                                                                       parseFloat(ARitenuta.SommaRitenuta.toFixed(2)), 
                                                                                       ARitenuta.IdentificativoFiscale, 
                                                                                       ARitenuta.SommaRitenutaDiFattureNonPagateMaCertificate, 
                                                                                       ARitenuta.SommaRitenutaDiFattureRDA, 
                                                                                       ARitenuta.RitenutaCertificata))  
                                                  });
                                                  Self.LsRitenute.sort(function(a,b)
                                                                {
                                                                  if(a.IdentificativoFiscale > b.IdentificativoFiscale)
                                                                      return 1
                                                                  if(a.IdentificativoFiscale < b.IdentificativoFiscale)
                                                                      return -1
                                                                  if(a.Anno < b.Anno)
                                                                      return 1
                                                                  if(a.Anno > b.Anno)
                                                                      return -1
                                                                    return 0
                                                                })
                                                  var ContenitoreIdentificativoFiscale = ''
                                                  Self.LsRitenute.forEach(function(OggettoRitenuta)
                                                  { 
                                                      if(ContenitoreIdentificativoFiscale == OggettoRitenuta.IdentificativoFiscale)
                                                        OggettoRitenuta.VisualizzaIdentificativoFiscale = false
                                                      ContenitoreIdentificativoFiscale = OggettoRitenuta.IdentificativoFiscale
                                                  });
                                              }

                                              for(let i = 0; i < Self.LsRitenute.length; i++)
                                                for(let j = 0; j < ArrayInfoRitenutePagate.length; j++)
                                                  if(Self.LsRitenute[i].Anno == ArrayInfoRitenutePagate[j].ANNO && (Self.LsRitenute[i].IdentificativoFiscale == ArrayInfoRitenutePagate[j].IDENTIFICATIVO_FISCALE))
                                                    {
                                                      Self.LsRitenute[i].RitenutaPagata         = ArrayInfoRitenutePagate[j].RITENUTA / 100;
                                                      Self.LsRitenute[i].RitenutaPagataInizio   = ArrayInfoRitenutePagate[j].RITENUTA / 100;
                                                    }

                                              Self.SchedaFiliali.AssignDati(ArrayInfoFiliali, ArrayOrariFiliali, ArrayTelefonoFiliali);
                                              Self.DataTableAgenda.AssignDati(ArrayInfoAgenda);
                                              Self.DataTableRitenuteCliente.AssignDati(ArrayInfoRitenuteCliente);
                                              Self.SchedaRecapiti.AssignDati(ArrayInfoTelefono)
                                               if(ArrayInfoAllegati.length != 0)
                                                Self.SchedaAllegati.AssignDati(ArrayInfoAllegati,'CLIENTE')

                                              Self.SchedaSconti.AssignDati(ArrayInfoScontiProdotti)

                                              if(ArrayInfo.length != 0)
                                              {
                                                Self.CodiceFiscaleIniziale = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE)

                                                Self.PassataAdAvvocatoDatoServer = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].PASSATA_AD_AVVOCATO)
                                                
                                                Self.Dati = { 
                                                              RAGIONE_SOCIALE             : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                                                              ID_CAT_CLIENTE              : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CAT_CLIENTE),
                                                              CODICE                      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE),
                                                              ENTE_PUBBLICO               : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].ENTE_PUBBLICO),
                                                              COD_ENTE_SDI                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_ENTE_SDI),
                                                              COD_UFFICIO_DEST            : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COD_UFFICIO_DEST),
                                                              PARTITA_IVA                 : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA),
                                                              NAZIONE_EM_PIVA             : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA),
                                                              CODICE_FISCALE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE),
                                                              INDIRIZZO_FATTURAZIONE      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE),
                                                              NR_CIVICO_FATTURAZIONE      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE),
                                                              PROVINCIA_FATTURAZIONE      : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE),
                                                              COMUNE_FATTURAZIONE         : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE),
                                                              CAP_FATTURAZIONE            : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE),
                                                              ZONA_FATTURAZIONE           : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ZONA_FATTURAZIONE),
                                                              PRESSO                      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PRESSO),
                                                              INDIRIZZO_SPEDIZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_SPEDIZIONE),
                                                              NR_CIVICO_SPEDIZIONE        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_SPEDIZIONE),
                                                              PROVINCIA_SPEDIZIONE        : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SPEDIZIONE),
                                                              NAZIONE_SPEDIZIONE          : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SPEDIZIONE),
                                                              COMUNE_SPEDIZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_SPEDIZIONE),
                                                              CAP_SPEDIZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_SPEDIZIONE),
                                                              ZONA_SPEDIZIONE             : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ZONA_SPEDIZIONE),
                                                              ESIGIBILITA_IVA             : TSchedaGenerica.DisponiFromString(ArrayInfo[0].ESIGIBILITA_IVA),
                                                              NATURA_PAGAMENTO            : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NATURA_PAGAMENTO),
                                                              COND_PAGAMENTO              : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].COND_PAGAMENTO),
                                                              NOTE_IN_FATTURA             : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE_IN_FATTURA),
                                                              NOTE                        : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NOTE),
                                                              PEC                         : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC),
                                                              SOGLIA_X_NO_IVA             : parseFloat((TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].SOGLIA_X_NO_IVA) / 100).toFixed(2)),
                                                              SOGLIA_PRESENTE             : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].SOGLIA_PRESENTE),
                                                              SOGLIA_NAT_PAG              : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].SOGLIA_NAT_PAG),
                                                              SPESE_INCASSO_DESCRIZIONE   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SPESE_INCASSO_DESCRIZIONE),
                                                              SPESE_INCASSO_SUGGERITE     : parseFloat((TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].SPESE_INCASSO_SUGGERITE) / 100).toFixed(2)),
                                                              IVA_SUGGERITA_CLIENTE       : parseFloat((TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA_SUGGERITA_CLIENTE) / 10).toFixed(2)),
                                                              ATTIVO                      : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].ATTIVO),
                                                              BOLLO                       : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BOLLO),
                                                              TIPOLOGIA_SCISSIONE         : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].TIPOLOGIA_SCISSIONE),
                                                              RITENUTA                    : parseFloat((TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RITENUTA) / 10).toFixed(2)),
                                                              SCONTO_GENERALE             : parseFloat((TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].SCONTO_GENERALE) / 100).toFixed(2)),
                                                              IS_FORNITORE                : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].IS_FORNITORE),
                                                              ContoCorrente               : {
                                                                                              IBAN              : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN),
                                                                                              ID_CONTO_CORRENTE : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CONTO_CORRENTE),
                                                                                              BANCA             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA_APPOGGIO),
                                                                                              CONTO_RIBA        : ArrayInfo[0].ID_CONTO_CORRENTE != null? false : true,
                                                                                              NR_CONTO          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CONTO),
                                                                                              SWIFT             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT),
                                                                                              BIC               : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC),
                                                                                            },
                                                              PASSATA_AD_AVVOCATO          : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].PASSATA_AD_AVVOCATO),
                                                              // Indicazione stato tabelle dati
                                                              ModificaTabellaFiliali       : false,
                                                              ModificaTabellaOrariFiliali  : false,
                                                              ModificaTabelle              : false,
                                                              ModificaRitenute             : false,
                                                              ModificaTabellaAllegati      : false,
                                                            }
                                                Self.Confronti.VecchiaPEC           = Self.Dati.PEC;
                                                Self.Confronti.VecchioCodiceSDI     = Self.Dati.COD_ENTE_SDI;
                                                Self.Confronti.VecchiaFatturazione  = Self.Dati.INDIRIZZO_FATTURAZIONE;
                                                Self.Confronti.VecchioNumCivico     = Self.Dati.NR_CIVICO_FATTURAZIONE;
                                                Self.Confronti.VecchioCAP           = Self.Dati.CAP_FATTURAZIONE;
                                                Self.Confronti.VecchioComune        = Self.Dati.COMUNE_FATTURAZIONE;
                                                Self.Confronti.VecchiaProvincia     = Self.Dati.PROVINCIA_FATTURAZIONE;
                                                Self.Confronti.VecchioCodiceFiscale = Self.Dati.CODICE_FISCALE;
                                                Self.Confronti.VecchiaNazionePIVA   = Self.Dati.NAZIONE_EM_PIVA;
                                                Self.Confronti.VecchiaPIVA          = Self.Dati.PARTITA_IVA;

                                                Self.CopiaDeiDati = TZGenericFunct.GetCopyOfObject(Self.Dati, Self.CopiaDeiDati);
                                                
                                                Self.CreateSnapshot();
                                                
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Cliente eliminato')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio del cliente');
                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          })
    
      SystemInformation.CaricaUltimoCodiceCliente(function(CodiceUltimoCliente)
                                                  {
                                                    Self.CodiceUltimoCliente = CodiceUltimoCliente
                                                  }, false);
    }

    CaricaRiassunto(Riassunto)
    {
        this.Chiave                         = Riassunto.CHIAVE;
        this.Dati.RAGIONE_SOCIALE           = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE);
        this.Eliminabile                    = Riassunto.CLIENTE_CANCELLABILE == 1? false : true
        this.Dati.ATTIVO                    = TSchedaGenerica.DisponiFromBoolean(Riassunto.ATTIVO);
        this.Dati.PASSATA_AD_AVVOCATO       = TSchedaGenerica.DisponiFromBoolean(Riassunto.PASSATA_AD_AVVOCATO);
        this.SchedaAllegati.SetIdDocumento(this.Chiave)
        this.CreateSnapshot();
    }

    Clear()
    {
      this.SchedaFiliali.ClearSchedaFiliali();
      // this.SchedaFiliali.CreazioneNuovoClienteSedePrincipaleAutomatica()
      this.DataTableAgenda.AssignDati([]);
      this.DataTableRitenuteCliente.AssignDati([]);
      this.SchedaRecapiti.ClearSchedaRecapiti()
      this.LsRitenute = [];
      this.SchedaSconti.AssignDati([])
      this.SchedaAllegati.AssignDati([])
      this.SchedaAllegati.SetIdDocumento(-1)
      this.CodiceFiscaleIniziale = ''
      this.Dati = { 
                    RAGIONE_SOCIALE             : '',
                    ID_CAT_CLIENTE              : -1,
                    CODICE              : '',
                    ENTE_PUBBLICO               : false,
                    COD_ENTE_SDI                : '0000000',
                    COD_UFFICIO_DEST            : '',
                    PARTITA_IVA                 : '',
                    NAZIONE_EM_PIVA             : SystemInformation.Configurazioni.StatoItaliano,
                    CODICE_FISCALE              : '',
                    INDIRIZZO_FATTURAZIONE      : '',
                    NR_CIVICO_FATTURAZIONE      : '',
                    PROVINCIA_FATTURAZIONE      : -1,
                    COMUNE_FATTURAZIONE         : '',
                    CAP_FATTURAZIONE            : '',
                    ZONA_FATTURAZIONE           : -1,
                    PRESSO                      : '', 
                    INDIRIZZO_SPEDIZIONE        : '',
                    NR_CIVICO_SPEDIZIONE        : '',
                    PROVINCIA_SPEDIZIONE        : -1,
                    NAZIONE_SPEDIZIONE          : SystemInformation.Configurazioni.StatoItaliano,
                    COMUNE_SPEDIZIONE           : '',
                    CAP_SPEDIZIONE              : '',
                    ZONA_SPEDIZIONE             : -1,
                    ESIGIBILITA_IVA             : TZFattElettronicaEsigibilita.esgImmediata,
                    NATURA_PAGAMENTO            : -1,
                    COND_PAGAMENTO              : -1,
                    NOTE_IN_FATTURA             : '',
                    NOTE                        : '',
                    PEC                         : '',
                    SOGLIA_X_NO_IVA             : 0,
                    SOGLIA_PRESENTE             : false,
                    SOGLIA_NAT_PAG              : -1,
                    TIPOLOGIA_SCISSIONE         : -1,
                    SPESE_INCASSO_DESCRIZIONE   : '',
                    SPESE_INCASSO_SUGGERITE     : 0,
                    IVA_SUGGERITA_CLIENTE       : 22,
                    SCONTO_GENERALE             : 0,
                    ATTIVO                      : true,
                    BOLLO                       : PAGAMENTO_BOLLO.NessunBollo,
                    RITENUTA                    : 0,
                    IS_FORNITORE                : false,
                    ContoCorrente               : { 
                                                      IBAN              : '',
                                                      BIC               : '',
                                                      SWIFT             : '',
                                                      BANCA_APPOGGIO    : '',
                                                      ID_CONTO_CORRENTE : -1,
                                                      NR_CONTO          : '',
                                                      CONTO_RIBA        : false
                                                    },
                    PASSATA_AD_AVVOCATO           : false,
                    // Indicazioni stato tabelle dati
                    ModificaTabellaFiliali        : false,
                    ModificaTabellaOrariFiliali   : false,
                    ModificaTabelle               : false,
                    ModificaRitenute              : false,
                    ModificaTabellaAllegati       : false,
      }  
      this.Confronti = {
                        VecchiaPEC           : "",
                        VecchioCodiceSDI     : "",
                        VecchiaFatturazione  : "",
                        VecchioNumCivico     : "",
                        VecchioCAP           : "",
                        VecchioComune        : "",
                        VecchiaProvincia     : "",
                        VecchioCodiceFiscale : "",
                        VecchiaNazionePIVA   : "",
                        VecchiaPIVA          : ""
                      }
      super.Clear();
    }

    BeforeExpand(AnItem,OnSuccess)
    {
        if(this.Chiave != undefined)
        {
          var SchedaIndiceFatture = new TSchedaIndiceFatture('',this.Chiave);
          var NodeFattura = AnItem.AddChild(SchedaIndiceFatture.GetDescrizione(),SchedaIndiceFatture)
          NodeFattura.HasChildren = true;

          var SchedaIndiceNotaDiCredito = new TSchedaIndiceNotaDiCredito('',this.Chiave);
          var NodeNota = AnItem.AddChild(SchedaIndiceNotaDiCredito.GetDescrizione(),SchedaIndiceNotaDiCredito)
          NodeNota.HasChildren = true;

          var SchedaIndiceMovimenti = new TSchedaIndiceMovCliente(null,this.Chiave);
          var NodoMovimenti = AnItem.AddChild(SchedaIndiceMovimenti.GetDescrizione(),SchedaIndiceMovimenti)
          NodoMovimenti.HasChildren = true;

          var SchedaIndiceDDT = new TSchedaIndiceDDT('',this.Chiave);
          var NodeDDT = AnItem.AddChild(SchedaIndiceDDT.GetDescrizione(),SchedaIndiceDDT)
          NodeDDT.HasChildren = true;
          
          
          var SchedaIndiceDDTEntrante = new TSchedaIndiceDDTEntrante('',this.Chiave);
          var NodeDDTEntrante = AnItem.AddChild(SchedaIndiceDDTEntrante.GetDescrizione(),SchedaIndiceDDTEntrante)
          NodeDDTEntrante.HasChildren = true;
          
                
          var SchedaIndiceConferma = new TSchedaIndiceConferme('',this.Chiave);
          var NodeConferma = AnItem.AddChild(SchedaIndiceConferma.GetDescrizione(),SchedaIndiceConferma)
          NodeConferma.HasChildren = true; 

          var SchedaIndicePreventivoMulti = new TSchedaIndicePreventivoMultiparametrico('',this.Chiave);
          var NodePreventivoMulti = AnItem.AddChild(SchedaIndicePreventivoMulti.GetDescrizione(),SchedaIndicePreventivoMulti)
          NodePreventivoMulti.HasChildren = true; 

          OnSuccess()
        }   
    }

    DatiEliminabili()
    {
        if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
          return false;

        if(SystemInformation.UserInformation.Ruolo == RUOLI.Amministratore || SystemInformation.UserInformation.Ruolo == RUOLI.SuperUtente)
          return this.Eliminabile
        else return false
    }
    
    Elimina(OnSuccess,OnError) 
    {
      this.InEliminazione = true;
      var Self = this
      var ObjQuery = {Operazioni : []}
      for(let i = 0; i < this.SchedaFiliali.DataTableFiliali.Righe.length; i++)
      {  
        ObjQuery.Operazioni.push ({ 
                                    Query : "DeleteTelefonoFilialiTramiteCliente",
                                    Parametri : { CHIAVE_FILIALE : Self.SchedaFiliali.DataTableFiliali.Righe[i].Dati['CHIAVE']}
                                  });  
        for(let j = 0; j < this.SchedaFiliali.DataTableFiliali.Righe[i].DataTableOrari.Righe.length; j++)
        {
            ObjQuery.Operazioni.push ({ 
                                          Query : "DeleteOrariFilialiTramiteCliente",
                                          Parametri : { CHIAVE : Self.SchedaFiliali.DataTableFiliali.Righe[i].DataTableOrari.Righe[j].Dati.CHIAVE}
                                      });       
        }
      }
      ObjQuery.Operazioni.push ({
                                    Query: "DeleteFilialiTramiteCliente",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                })
      ObjQuery.Operazioni.push ({
                                    Query: "DeleteTelefoniTramiteCliente",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                })
      ObjQuery.Operazioni.push ({
                                    Query: "DeleteRitenuteAccontoTramiteCliente",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                })
      ObjQuery.Operazioni.push ({
                                    Query: "DeleteListinoScontiTramiteCliente",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                })
      ObjQuery.Operazioni.push ({
                                    Query: "DeleteSaldiAnnualiTramiteCliente",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                })
      ObjQuery.Operazioni.push ({
                                    Query: "DeleteAgendaTramiteCliente",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                })
      ObjQuery.Operazioni.push ({
                                    Query: "DeleteRitenutaTramiteCliente",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                })
      ObjQuery.Operazioni.push ({
                                    Query: "Delete",
                                    Parametri: { CHIAVE_CLIENTE : Self.Chiave }
                                }),
      this.AdvQuery.PostSQL('Clienti', ObjQuery, function () 
      {
        OnSuccess();
      },
      function (HTTPError, SubHTTPError, Response) 
      {
        OnError(HTTPError, SubHTTPError, Response);
      });
    } 

    GetFiltriAbilitati(OnSuccess)
    {
        OnSuccess([{
                    Name : DASHBOARD_FILTER_TYPES.Clienti,
                    Positions : [
                                    this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                    this.Chiave
                                ]
                }])
    }
  }

  export default 
  {
    components : 
    {
        VUEInputCAP,
        VUEDataTable,
        VUEInputProvince,
        VUEInputNazioni,
        VUEInputNaturaPagamenti,
        VUEInputCondPagamenti,
        VUEInputEsigibilitaIVA,
        VUEInputPartitaIVA,
        VUEInputCodiceFiscale,
        VUERecapiti,
        VUEModal,
        VUEInputContoRibaCorrente,
        VUEInputZone,
        VUEConfirm,
        VUESchedaFattura,
        VUESchedaMovimento,
        VUESchedaNotaDiCredito,
        VUEAllegati,
        VUEDataRowFilialiClienti
        //VUEInputScissioneDeiPagamenti
    },
    data()
    {
      return { 
                Tabs                                  : null,
                LsContiCorrenti                       : [],
                MenuNuovo                             : [],
                MenuStampa                            : [],
                DeveloperMode                         : SystemInformation.DeveloperMode,
                LsPagamentoBollo                      : SystemInformation.GetLsPagamentoBollo(),
                LsCategorieClienti                    : SystemInformation.Configurazioni.CatClienti,
                StatoItaliano                         : SystemInformation.Configurazioni.StatoItaliano,
                Collapsed                             : true,
                TipiFatturazione                      : SystemInformation.GetLsTipiFatturazione(),
                ShowCodiceCliente                     : false,
                DataContoCliente                      : false,
                ContoClienteDal                       : '',
                ContoClienteAl                        : '',
                SchedaSelezionataPopup                : new TSchedaGenerica(SystemInformation.AdvQuery),
                ChiaveComponenteInput                 : 0,
                headers                               : [],
                data                                  : [],
                Filiali                               : [],
                PopupConfermaCaricamento              : false,
                PopupDisattivaCliente                 : false,
                TipiSconti                            : TIPO_SCONTO,
                VisibilitaCategorieClienti            : SystemInformation.AccessRights.VisibilitaCategorieClienti(),
                NomeProgramma                         : NOME_PROGRAMMA,
                PopupVisualizzaFattura   : false,
                PopupDocumento           : false,
                TipoDocumento            : '',
                TestoPopupDocumento      : '',
                DocumentoSelezionato     : null,
                FiltroFiliali            : 'Tutte',              
                FatturaSelezionata       : null,
                Filtro                                : {
                                                          xNome : '',
                                                          xCodice : ''
                                                        },
                Paginazione                           : {
                                                          NrRighePerPagina  : 10,
                                                          NrPagina          : 1,
                                                          StartGruppoPagine : 1,
                                                        },
            };
    },
    
    emits : [ 'onClickNuovaFattura',
              'onClickNuovoPreventivo',
              'onClickNuovoMovimento',
              'onClickNuovaNotaDiCredito', 
              'onClickNuovaFatturaDaBanco', 
              'onClickNuovoDDT',
              'onClickNuovoDDTEntrante'],

    props : ['SchedaCliente'],
    
    watch : 
    {
      SchedaCliente ()
      {
        this.ShowCodiceCliente = false;
      },

      'SchedaCliente.DataTableRitenuteCliente' :
      { 
         handler(NewValue,OldValue)
         {
            if(NewValue != OldValue && NewValue != undefined)
            {
                this.SchedaCliente.DataTableRitenuteCliente.AssignOnRowChange(() =>
                {
                  this.SchedaCliente.Dati.ModificaTabelle = true
                })

                this.SchedaCliente.DataTableRitenuteCliente.AssignOnRowDelete(() =>
                {
                  this.SchedaCliente.Dati.ModificaTabelle = true
                })
            } 
         },
         immediate : true
      },
      
      'SchedaCliente.DataTableAgenda' :
      { 
         handler(NewValue,OldValue)
         {
            if(NewValue != OldValue && NewValue != undefined)
            {
                this.SchedaCliente.DataTableAgenda.AssignOnRowChange(() =>
                {
                  this.SchedaCliente.Dati.ModificaTabelle = true
                })

                this.SchedaCliente.DataTableAgenda.AssignOnRowDelete(() =>
                {
                  this.SchedaCliente.Dati.ModificaTabelle = true
                })
            } 
         },
         immediate : true
      },
      
      'SchedaCliente.SchedaFiliali.DataTableFiliali' :
      { 
         handler(NewValue)
         {          
            if(NewValue != undefined)
            {              
              this.SchedaCliente.SchedaFiliali.DataTableFiliali.AssignOnRowChange(() =>
              {
                this.SchedaCliente.Dati.ModificaTabellaFiliali = true

                for(let i = 0; i < this.DataTableFiliali.Righe.length; i++)
                {
                  if(this.DataTableFiliali.Righe[i].DataTableOrari.Modificato())
                  {
                    this.SchedaCliente.Dati.ModificaTabellaOrariFiliali = true
                    break;
                  }
                }

                for(let i = 0; i < this.DataTableFiliali.Righe.length; i++)
                {
                  if(this.DataTableFiliali.Righe[i].SchedaRecapitiFiliali.DataTableTelefono.Modificato())
                  {
                    this.SchedaCliente.Dati.ModificaTabellaTelefonoFiliali = true
                    break;
                  }
                }
              })

              this.SchedaCliente.SchedaFiliali.DataTableFiliali.AssignOnRowDelete(() =>
              {
                this.SchedaCliente.Dati.ModificaTabellaFiliali = true

                for(let i = 0; i < this.DataTableFiliali.Righe.length; i++)
                {
                  if(this.DataTableFiliali.Righe[i].DataTableOrari.Modificato())
                  {
                    this.SchedaCliente.Dati.ModificaTabellaOrariFiliali = true
                    break;
                  }
                }

                for(let i = 0; i < this.DataTableFiliali.Righe.length; i++)
                {
                  if(this.DataTableFiliali.Righe[i].SchedaRecapitiFiliali.DataTableTelefono.Modificato())
                  {
                    this.SchedaCliente.Dati.ModificaTabellaTelefonoFiliali = true
                    break;
                  }
                }
              })
            } 
         },
         immediate : true
      }
    },

    computed :
    {
      DataTableFiliali :
      {
        get()
        {
          return this.SchedaCliente.SchedaFiliali.DataTableFiliali
        }
      },

      DataTableOrari :
      {
        get()
        {
          return this.SchedaCliente.SchedaFiliali.DataTableOrari
        }
      },

      TipoEsigibilita()
      {
        return TZFattElettronicaEsigibilita
      },

      ErrorePartitaIVA :
      {
        get()
        {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaCliente.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
              return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
        }
      },

      ErroreCodiceFiscale :
      {
        get()
        {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaCliente.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
              return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
        }
      },

      FiltroCerca()
      {
        var Self = this
        var FiltroDescrizione = this.Filtro.xNome.toUpperCase().trim();
        var FiltroCodice      = this.Filtro.xCodice.toUpperCase().trim();
        var ListaParoleDescr  = FiltroDescrizione.split(' ')

        var ListaRighe = []

        if(FiltroDescrizione == '' && FiltroCodice == '')
        {
          return ListaRighe.concat(Self.SchedaCliente.SchedaSconti.LsProdotti)
        }
        else 
        {
          ListaRighe = Self.SchedaCliente.SchedaSconti.LsProdotti.filter(function(Prodotto)
            {
              if(FiltroCodice != '' && FiltroDescrizione != '')
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

              if(FiltroDescrizione != '')
              {
                return Self.FiltraPerDescrizione(Prodotto, ListaParoleDescr)
              }
              return false;
            })
            // ListaRighe = ListaRighe.slice(0, this.NumeroMassimoProdotti) 
            return ListaRighe
        }
      },

      ProdottiFiltratiEPaginati()
      {
        let ProdottiFiltrati = this.FiltroCerca
        let Start = (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighePerPagina
        let End = Start + this.Paginazione.NrRighePerPagina
        return ProdottiFiltrati.slice(Start, End)
      },

      TestoPaginazione :
      {
        get()
        {
          let Totale = this.FiltroCerca.length
          let Start = Totale === 0 ? 0 : (this.Paginazione.NrPagina - 1) * this.Paginazione.NrRighePerPagina + 1
          let End = Math.min(this.Paginazione.NrPagina * this.Paginazione.NrRighePerPagina, Totale)
          return "Visualizzati " + Start + ' - ' + End + ' di ' + Totale + ' risultati'
        }
      },

      ForwardGruppoPagineVisibile : 
      {
        get()
        {
          return this.FiltroCerca.length > this.Paginazione.NrPagina * this.Paginazione.NrRighePerPagina
        }
      },

      BackGruppoPagineVisibile :
      {
        get()
        {
             return this.Paginazione.NrPagina > 1
        }
      },

    },

    methods: 
    {
      FiltraPerCodice(FiltroCodice, Prodotto)
      {
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

      handleFileUpload(event)
      {
        const File     = event.target.files[0]
        const Reader   = new FileReader()

        Reader.onload = (ExcelFiliale) => 
        {
          const BinaryStr     = ExcelFiliale.target.result
          const Workbook      = XLSX.read(BinaryStr, { type: 'binary' })
          const SheetName     = Workbook.SheetNames[0]
          const Worksheet     = Workbook.Sheets[SheetName]
          const JsonData      = XLSX.utils.sheet_to_json(Worksheet, { header: 1 })

          this.Headers        = JsonData[0]
          this.Data           = JsonData.slice(1)
          this.Filiali        = []

          let GruppiReferente = []

          for (let i = 0; i < this.Headers.length; i++)
          {
            let Header = (this.Headers[i] || "").toString().toLowerCase()
            if (Header.startsWith("referente"))
            {
              let Numero = Header.replace("referente", "")
              let TelefonoIndex = this.Headers.findIndex(h => h.toString().toLowerCase() === "telefono" + Numero)
              let EmailIndex = this.Headers.findIndex(h => h.toString().toLowerCase() === "email" + Numero)

              GruppiReferente.push({
                                      ReferenteIndex : i,
                                      TelefonoIndex  : TelefonoIndex,
                                      EmailIndex     : EmailIndex
                                    })
            }
          }

          for (let i = 0; i < this.Data.length; i++)
          {
            let Riga = this.Data[i];
            if (!Riga || Riga.length === 0 || Riga[0] === undefined) continue

            let SedeRiscossioneStr = (Riga[1] || "").toString().trim().toLowerCase()
            let SedeRiscossione    = false

            if (SedeRiscossioneStr == "si")
              SedeRiscossione = true
            else
              SedeRiscossione = false

            let NomeNazione    = (Riga[8] || "").toString().trim().toLowerCase()
            let   ChiaveNazione    = null

            for (let n = 0; n < SystemInformation.Configurazioni.Nazioni.length; n++)
            {
              let Nazione = SystemInformation.Configurazioni.Nazioni[n]

              if ((Nazione.NOME || "").toString().trim().toLowerCase() == NomeNazione)
              {
                ChiaveNazione = Nazione.CHIAVE
                break
              }
            }

            let NomeZona   = (Riga[7] || "").toString().trim().toLowerCase()
            let   ChiaveZona = ""

            for (let z = 0; z < SystemInformation.Configurazioni.Zone.length; z++)
            {
              let Zona = SystemInformation.Configurazioni.Zone[z]

              if ((Zona.DESCRIZIONE || "").toString().trim().toLowerCase() === NomeZona)
              {
                ChiaveZona = Zona.CHIAVE != null ? Zona.CHIAVE.toString() : ""
                break
              }
            }

            const Filiale = 
            {
              RagioneSociale    : Riga[0]  || "",
              SedeRiscossione   : SedeRiscossione,
              ViaFiliale        : Riga[2]  || "",
              CivicoFiliale     : (Riga[3] != null) ? Riga[3].toString() : "",
              CAPFiliale        : Riga[4]  || "",
              ComuneFiliale     : Riga[5]  || "",
              ProvinciaFiliale  : Riga[6]  || "",
              ZonaFiliale       : ChiaveZona,
              NazioneFiliale    : ChiaveNazione,
              NoteFiliale       : Riga[9]  || ""
            }

            for (let k = 0; k < GruppiReferente.length; k++)
            {
              let Numero = k + 1
              Filiale[`Referente${Numero}`] = Riga[GruppiReferente[k].ReferenteIndex] || ""
              Filiale[`Telefono${Numero}`]  = GruppiReferente[k].TelefonoIndex !== -1 ? (Riga[GruppiReferente[k].TelefonoIndex] || "") : ""
              Filiale[`Email${Numero}`]     = GruppiReferente[k].EmailIndex !== -1 ? (Riga[GruppiReferente[k].EmailIndex] || "") : ""
            }

            this.Filiali.push(Filiale)
          }

          this.InserisciFilialiXLS(this.Filiali)
        }

        Reader.readAsArrayBuffer(File)
      },

      TastoPaginaVisibile(Offset)
      {
        return (this.Paginazione.StartGruppoPagine + Offset - 1) * this.Paginazione.NrRighePerPagina < this.FiltroCerca.length
      },

      OnClickOrdinamento(Colonna)
      {
        var OrdinamentoDiscendente = false;
        if(Colonna.Campo == this.DataTable.OrdinatoPer.Campo)
           OrdinamentoDiscendente = !this.DataTable.OrdinatoPer.Discendente
        this.DataTable.OrdinaPer(Colonna.Campo,OrdinamentoDiscendente)
      },

      IsActivePage(Offset)
      {
        return this.Paginazione.NrPagina == this.Paginazione.StartGruppoPagine + Offset
      },

      OnClickChangePage(Offset)
      {
        this.Paginazione.NrPagina = this.Paginazione.StartGruppoPagine + Offset
      },

      PortaIndietroGruppoPagine()
      {
          if(this.BackGruppoPagineVisibile)
             if(--this.Paginazione.NrPagina < this.Paginazione.StartGruppoPagine)
                this.Paginazione.StartGruppoPagine--
      },

      PortaAvantiGruppoPagine()
      {
         if(this.ForwardGruppoPagineVisibile)
            if(++this.Paginazione.NrPagina > this.Paginazione.StartGruppoPagine + 4)
                this.Paginazione.StartGruppoPagine++
      },

      OnClickFirstPage()
      {
        this.Paginazione.NrPagina = 1
        this.Paginazione.StartGruppoPagine = 1
      },

      OnClickLastPage()
      { 
        this.Paginazione.NrPagina = Math.ceil(this.FiltroCerca.length / this.Paginazione.NrRighePerPagina)
        if (this.Paginazione.NrPagina >= 5)
          this.Paginazione.StartGruppoPagine = this.Paginazione.NrPagina - 4
        else
          this.Paginazione.StartGruppoPagine = 1
      },

      ClosePopup()
      {
        this.PopupConfermaCaricamento = false
      },

      InserisciFilialiXLS()
      {
        var ChiaveCliente = this.SchedaCliente.Chiave
        var ObjQuery = { Operazioni : [] }
        var Self = this
        this.Filiali.forEach( 
        function(Filiale)
        {
          ObjQuery.Operazioni.push({
                                    Query: "InserisciFilialiTramiteXLS",
                                    Parametri:  {
                                                  CHIAVE      : undefined,
                                                  SEDE        : TSchedaGenerica.PrepareForRecordBoolean(Filiale.SedeRiscossione),
                                                  CLIENTE     : TSchedaGenerica.PrepareForRecordInteger(ChiaveCliente),
                                                  NR_CIVICO   : TSchedaGenerica.PrepareForRecordString(Filiale.CivicoFiliale),
                                                  NOME        : TSchedaGenerica.PrepareForRecordString(Filiale.RagioneSociale),
                                                  INDIRIZZO   : TSchedaGenerica.PrepareForRecordString(Filiale.ViaFiliale),
                                                  PROVINCIA   : TSchedaGenerica.PrepareForRecordInteger(SystemInformation.GetChiaveProvincia(Filiale.ProvinciaFiliale)),
                                                  COMUNE      : TSchedaGenerica.PrepareForRecordString(Filiale.ComuneFiliale),
                                                  CAP         : TSchedaGenerica.PrepareForRecordString(Filiale.CAPFiliale),
                                                  NAZIONE     : TSchedaGenerica.PrepareForRecordInteger(Filiale.NazioneFiliale),
                                                  ZONA        : TSchedaGenerica.PrepareForRecordInteger(Filiale.ZonaFiliale),
                                                  NOTE        : TSchedaGenerica.PrepareForRecordString(Filiale.NoteFiliale),
                                                },
                                    ResetKeys : [1]
                                  })
        })
        SystemInformation.AdvQuery.PostSQL('Clienti',ObjQuery,
        function(Results)
        {
          var NewKeys = Results.NewKeys || []

          NewKeys.forEach(function(Key, id)
          {
            var ChiaveFiliale = Key.Value
            var Filiale       = Self.Filiali[id]
            Self.InserisciRecapitiFiliale(ChiaveFiliale, Filiale)
          })

          Self.SchedaCliente.Disponi()
          Self.PopupConfermaCaricamento = true
        },
        function(HTTPError,SubHTTPError,Response)
        {
          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
        })

      },

      InserisciRecapitiFiliale(ChiaveFiliale, Filiale)
      {
        var Recapiti            = []
        var ObjQueryRecapiti    = { Operazioni : [] }

        Object.keys(Filiale).forEach(function(ChiaveCampo)
        {
          var MatchReferente    = ChiaveCampo.match(/^Referente(\d+)$/)

          if(MatchReferente)
          {
            var Indice          = MatchReferente[1]
            var NomeReferente   = Filiale['Referente' + Indice] || ""
            var Telefono        = Filiale['Telefono'  + Indice] || ""
            var Email           = Filiale['Email'     + Indice] || ""

            if(NomeReferente || Telefono || Email)
            {
              Recapiti.push({
                              REFERENTE : TSchedaGenerica.PrepareForRecordString(NomeReferente),
                              TELEFONO  : TSchedaGenerica.PrepareForRecordString(Telefono),
                              EMAIL     : TSchedaGenerica.PrepareForRecordString(Email)
                            })
            }
          }
        })

        Recapiti.forEach(function(Recapito)
        {
          ObjQueryRecapiti.Operazioni.push({
                                              Query     : "InsertTelefonoFiliale",
                                              Parametri : {
                                                            CHIAVE                 : undefined,
                                                            ID_FILIALE             : ChiaveFiliale,
                                                            DESCRIZIONE            : Recapito.REFERENTE,
                                                            TELEFONO               : Recapito.TELEFONO,
                                                            EMAIL_PER_AVVISO       : "F",
                                                            TELEFONO_PER_AVVISO    : "F",
                                                            EMAIL                  : Recapito.EMAIL
                                                          },
                                              ResetKeys : [3]
                                            })
        })

        if(ObjQueryRecapiti.Operazioni.length > 0)
        {
          SystemInformation.AdvQuery.PostSQL('Clienti',
                                              ObjQueryRecapiti,
                                              function() {},
                                              function(HTTPError,SubHTTPError,Response)
                                              {
                                                SystemInformation.HandleError(HTTPError,SubHTTPError,Response)
                                              })
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

      OnClickStampa()
      {
          var Self = this;
          if (this.MenuStampa.length == 0)
          { 
              var Parametri = {ChiaveCliente : this.SchedaCliente.Chiave}
              this.MenuNuovo = []
              this.MenuStampa = [
                  {
                    Caption: "Estratto conto",
                    OnClick: function () 
                    {
                        SystemInformation.AdvQuery.ExecuteExternalScript('StampaEstrattoConto', Parametri,
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
                    Caption: "Conto cliente",
                    OnClick: function () 
                    {
                        if(!Self.DataContoCliente)
                        {
                          let date = new Date() 
                          let y = date.getFullYear()
                          let m = date.getMonth()

                          if(SystemInformation.UltimaDataDalContoClienteInserita != '')
                            Self.ContoClienteDal = SystemInformation.UltimaDataDalContoClienteInserita
                          else
                          {
                            if(Self.ContoClienteDal == null || Self.ContoClienteDal == '')
                              Self.ContoClienteDal = TZDateFunct.DateInHTMLInputFormat(new Date(y, m, 1));
                          }
                          if(SystemInformation.UltimaDataAlContoClienteInserita != '')
                            Self.ContoClienteAl  = SystemInformation.UltimaDataAlContoClienteInserita
                          else
                          {
                            if(Self.ContoClienteAl == null || Self.ContoClienteAl == '')
                              Self.ContoClienteAl = TZDateFunct.DateInHTMLInputFormat(new Date(y, m + 1, 0));
                          }
                          Self.DataContoCliente = true
                        }
                        else Self.DataContoCliente = false 
                    }
                  }
              ]
          }
          else this.MenuStampa = [];
      },

      OnNextMenuNuovo(AMenu) 
      {
        if (AMenu.SubMenu != undefined)
            this.MenuNuovo = AMenu.SubMenu;
        else {
            AMenu.OnClick();
            this.MenuNuovo = [];
        }
      },

      OnClickConfermaSchedaSelezionataPopup()
      {
        // var Self = this
        this.SchedaSelezionataPopup.Registra(function()
        {
          // Self.VisualizzaAmministratore = false
        })
        
      },

      ConfermaDataContoCliente()
      {
        var Self = this
        var Parametri = {
                          ChiaveCliente : this.SchedaCliente.Chiave,
                          DataDal       : this.ContoClienteDal,
                          DataAl        : this.ContoClienteAl
                        }

        SystemInformation.UltimaDataDalContoClienteInserita = this.ContoClienteDal
        SystemInformation.UltimaDataAlContoClienteInserita  = this.ContoClienteAl

        if(this.DataContoCliente)
        {
          this.DataContoCliente = false
          SystemInformation.AdvQuery.ExecuteExternalScript('StampaConto', Parametri,
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

      OnClickNuovo() 
      {
          var Self = this;
          if (this.MenuNuovo.length == 0)
          { 
              this.MenuStampa = []
              this.MenuNuovo = [
                                  {
                                    Caption: "Fatture",
                                    SubMenu: [
                                        {
                                            Caption: "Fattura",
                                            OnClick: function () 
                                            {
                                              Self.$emit('onClickNuovaFattura', Self.SchedaCliente)
                                            }
                                        
                                        },
                                        {
                                            Caption: "Fattura da banco",
                                            OnClick: function () 
                                            {
                                              Self.$emit('onClickNuovaFatturaDaBanco', Self.SchedaCliente)
                                            }
                                        
                                        }]
                                  },
                                  {
                                    Caption: "Nuovo preventivo",
                                    OnClick: function () 
                                    {
                                      Self.$emit('onClickNuovoPreventivo', Self.SchedaCliente) 
                                    }
                                  },
                                  {
                                    Caption: "Nuovo movimento",
                                    OnClick: function () 
                                    {
                                      Self.$emit('onClickNuovoMovimento', Self.SchedaCliente)
                                    }
                                  },
                                  {
                                    Caption: "Nuova nota di credito",
                                    OnClick: function () 
                                    {
                                      Self.$emit('onClickNuovaNotaDiCredito', Self.SchedaCliente) 
                                    }
                                  },
                                  {
                                    Caption: "Nuovo DDT",
                                    OnClick: function () 
                                    {
                                      Self.$emit('onClickNuovoDDT', Self.SchedaCliente) 
                                    }
                                  },
                                  {
                                    Caption: "Nuovo DDT Entrante",
                                    OnClick: function () 
                                    {
                                      Self.$emit('onClickNuovoDDTEntrante', Self.SchedaCliente) 
                                    }
                                  }
                                ]
          }
          else this.MenuNuovo = [];
      },
      
      OnRecapitiChange()
      {
        this.SchedaCliente.Dati.ModificaTabelle = true
      },

      OnRitenutaChange(Ritenuta)
      {
        if(Ritenuta.RitenutaPagata != Ritenuta.RitenutaPagataInizio)
        {
          this.SchedaCliente.Dati.ModificaRitenute = true
        }
        else if (Ritenuta.RitenutaPagata == Ritenuta.RitenutaPagataInizio)
          this.SchedaCliente.Dati.ModificaRitenute = false
      },

      OnClickCopiaDaDatiFatturazione()
      {
        this.SchedaCliente.Dati.PRESSO               = this.SchedaCliente.Dati.RAGIONE_SOCIALE
        this.SchedaCliente.Dati.COMUNE_SPEDIZIONE    = this.SchedaCliente.Dati.COMUNE_FATTURAZIONE
        this.SchedaCliente.Dati.NR_CIVICO_SPEDIZIONE = this.SchedaCliente.Dati.NR_CIVICO_FATTURAZIONE
        this.SchedaCliente.Dati.NAZIONE_SPEDIZIONE   = this.SchedaCliente.Dati.NAZIONE_EM_PIVA
        this.SchedaCliente.Dati.INDIRIZZO_SPEDIZIONE = this.SchedaCliente.Dati.INDIRIZZO_FATTURAZIONE
        this.SchedaCliente.Dati.PROVINCIA_SPEDIZIONE = this.SchedaCliente.Dati.PROVINCIA_FATTURAZIONE
        this.SchedaCliente.Dati.CAP_SPEDIZIONE       = this.SchedaCliente.Dati.CAP_FATTURAZIONE
        this.SchedaCliente.Dati.ZONA_SPEDIZIONE      = this.SchedaCliente.Dati.ZONA_FATTURAZIONE
      },

      OnChangeIva()
      {
        if(this.SchedaCliente.Dati.IVA_SUGGERITA_CLIENTE != 0)
          this.SchedaCliente.Dati.NATURA_PAGAMENTO = -1
      },

      CaricaContoCorrente()
      {
        var Self = this;
        var Parametri = { ContiCorrenti : -1 }
        SystemInformation.AdvQuery.GetSQL("MovimentiConti",Parametri,
                                        function(Results)
                                        {
                                          let ArrayInfo  = SystemInformation.AdvQuery.FindResults(Results,"SelectContiCasse");
                                          if(ArrayInfo != undefined)
                                            Self.LsContiCorrenti = ArrayInfo
                                          if(Self.SchedaCliente.Dati.ID_CONTO_CORRENTE != -1)
                                          {
                                            for(let i = 0; i < Self.LsContiCorrenti.length; i++)
                                            {
                                              if(Self.LsContiCorrenti[i].CHIAVE == Self.SchedaCliente.Dati.ID_CONTO_CORRENTE)
                                              {
                                                Self.SchedaCliente.NUMERO_CONTO = Self.LsContiCorrenti[i].NR_CONTO
                                              }
                                            }
                                          }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        });
      },

      OnChangeContoCorrente()
      {
        if(this.SchedaCliente.Dati.ID_CONTO_CORRENTE != -1)
        {
          for(let i = 0; i < this.LsContiCorrenti.length; i++)
          {
            if(this.LsContiCorrenti[i].CHIAVE == this.SchedaCliente.Dati.ID_CONTO_CORRENTE)
            {
              this.SchedaCliente.Dati.BANCA_APPOGGIO = this.LsContiCorrenti[i].BANCA
              this.SchedaCliente.NUMERO_CONTO        = this.LsContiCorrenti[i].NR_CONTO
              this.SchedaCliente.Dati.IBAN           = this.LsContiCorrenti[i].IBAN
              this.SchedaCliente.Dati.BIC            = this.LsContiCorrenti[i].BIC
              this.SchedaCliente.Dati.SWIFT          = this.LsContiCorrenti[i].SWIFT
            }
          }
        }
        else
        {
          this.SchedaCliente.Dati.BANCA_APPOGGIO = ''
          this.SchedaCliente.NUMERO_CONTO        = ''
          this.SchedaCliente.Dati.IBAN           = ''
          this.SchedaCliente.Dati.BIC            = ''
          this.SchedaCliente.Dati.SWIFT          = ''
        }
      },

      OnKeyPressCodiceCliente()
      {
        this.ShowCodiceCliente = true
      },

      OnChangeSogliaPresente()
      {
        if(!this.SchedaCliente.Dati.SOGLIA_PRESENTE)
        {
          this.SchedaCliente.Dati.SOGLIA_NAT_PAG  = -1
          this.SchedaCliente.Dati.SOGLIA_X_NO_IVA = 0
        }
      },

      OnChangeEntePubblico()
      {
        if(this.SchedaCliente.Dati.ENTE_PUBBLICO)
        {
          this.SchedaCliente.Dati.IMPIANTO_REVERSE_CHARGE = false
          this.SchedaCliente.Dati.ESIGIBILITA_IVA         = TZFattElettronicaEsigibilita.esgSplitPayment
        }
        else this.SchedaCliente.Dati.ESIGIBILITA_IVA = TZFattElettronicaEsigibilita.esgImmediata
      },

      OnChangeImpiantoReverseChange()
      {
        if(this.SchedaCliente.Dati.IMPIANTO_REVERSE_CHARGE)
        {
          this.SchedaCliente.Dati.ENTE_PUBBLICO   = false
          this.SchedaCliente.Dati.ESIGIBILITA_IVA = TZFattElettronicaEsigibilita.esgImmediata
        }
      },

      OnChangeDatiAvviso()
      {
        this.SchedaCliente.CambiaAvvisiDocumentiAttivi         = true
        this.SchedaCliente.PopupAggiornaAvvisoFattura.Visibile = false
      },

      AnnullaPopUpAvvisi()
      {
        this.SchedaCliente.CambiaAvvisiDocumentiAttivi         = false
        this.SchedaCliente.PopupAggiornaAvvisoFattura.Visibile = false
      },

      AnnullaPopUpCF()
      {
        this.SchedaCliente.PopupConfermaDoppioCodiceOnSuccess  = null
        this.SchedaCliente.PopupConfermaDoppioCodiceFiscale    = false
      },

      OnClickConfermaPopupDisattivaCliente()
      {
        this.PopupDisattivaCliente = false
        this.SchedaCliente.ClienteDisattivato = true
      },

      OnChangeClienteAttivo()
      {
        if(!this.SchedaCliente.Dati.ATTIVO)
        {
          this.PopupDisattivaCliente = true
        }
        else
        {
          this.SchedaCliente.ClienteDisattivato = false
        }
      },

      OnClickGetDettagliRitenuta(Anno,Ritenuta)
      {
        this.Collapsed = false
        var Self       = this
        SystemInformation.AdvQuery.ExecuteExternalScript('GetDettagliRitenuta', { ChiaveCliente : this.SchedaCliente.Chiave, Anno : Anno },
                                                        function(Result)
                                                        {  
                                                          //var ListaFatture = []
                                                          //ListaFatture = Result.ListaTotaliFatture[0]
                                                          var ListaTotaliFiltrati = []
                                                          
                                                          for(let i = 0; i < Result.ListaTotaliFatture[0].length; i++)
                                                          {
                                                            if(Result.ListaTotaliFatture[0][i].IdentificativoFiscale == Ritenuta.IdentificativoFiscale)
                                                            {
                                                              if(Result.ListaTotaliFatture[0][i].DataRitenutaCertificata != null)
                                                                Result.ListaTotaliFatture[0][i].PercPagato = 1
                                                              else Result.ListaTotaliFatture[0][i].PercPagato = Result.ListaTotaliFatture[0][i].TotalePagatoXAnno[Anno] / 
                                                                                                          (Result.ListaTotaliFatture[0][i].TotaliDaIncassare + Result.ListaTotaliFatture[0][i].TotalePagato)
                                                              Result.ListaTotaliFatture[0][i].DataFatturaFormatted = TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(Result.ListaTotaliFatture[0][i].DataFattura)) 
                                                              
                                                              ListaTotaliFiltrati.push(Result.ListaTotaliFatture[0][i])
                                                            }
                                                          }

                                                          for(let i = 0; i < Self.SchedaCliente.LsRitenute.length; i++)
                                                          {
                                                            if(Self.SchedaCliente.LsRitenute[i].IdentificativoFiscale == Ritenuta.IdentificativoFiscale)
                                                            {
                                                              if(Self.SchedaCliente.LsRitenute[i].Anno == Anno)
                                                              {
                                                                Self.SchedaCliente.LsRitenute[i].LsFatture = ListaTotaliFiltrati
                                                                Self.SchedaCliente.LsRitenute[i].LsFatture.sort(function(a,b)
                                                                {
                                                                  if(a.DataFattura < b.DataFattura)
                                                                    return 1;
                                                                  if(a.DataFattura > b.DataFattura)
                                                                    return -1;
                                                                  return 0
                                                                })
                                                              break;
                                                              }
                                                            }
                                                          }
                                                          Ritenuta.FattureVisualizzate = true
                                                        },
                                                        function(HTTPError,SubHTTPError,Response)
                                                        {
                                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                        })   
      },

      OnChangeSconto(Prodotto)
      {
        if (Prodotto.TIPO_SCONTO == TIPO_SCONTO.Listino)
        {
          if(Prodotto.VALORE != null)
          {
            Prodotto.Eliminato = true
          }
        } 
        else if (Prodotto.TIPO_SCONTO == TIPO_SCONTO.Sconto || Prodotto.TIPO_SCONTO == TIPO_SCONTO.Prezzo)
        {
          if(Prodotto.VALORE != null)
            Prodotto.Modificato = true
          else Prodotto.Nuovo = true
        } 

        this.SchedaCliente.Dati.ModificaTabelle = true;
      },

      OnAllegatiChange()
      {
        this.SchedaCliente.Dati.ModificaTabellaAllegati = this.SchedaCliente.SchedaAllegati.Modificato();
      },

  
      GetListaProdotti()
      {
        var Self = this
        var Parametri = {
                          Limite : this.Paginazione.NrRighePerPagina,
                          Offset : (this.Paginazione.NrPagina -1) * this.Paginazione.NrRighePerPagina
                        }
        SystemInformation.AdvQuery.GetSQL('Clienti', Parametri,
                                            function(Results)
                                            {  
                                              let ArrayInfoProdotti = SystemInformation.AdvQuery.FindResults(Results,"ListaProdotti")

                                              if(ArrayInfoProdotti != undefined)
                                              {
                                                ArrayInfoProdotti.forEach(function(prodotto)
                                                                          {
                                                                            prodotto.Listino = false;
                                                                            prodotto.Prezzo = false;
                                                                            prodotto.Sconto = false;
                                                                          })
                                                Self.LsProdotti = ArrayInfoProdotti
                                              }
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                            }, 'SelectSQL')
      },

      OnClickCerca()
      {
        let Self = this
        if(Self.SchedaCliente.SchedaStatoContabile.DallaData && Self.SchedaCliente.SchedaStatoContabile.AllaData)
          Self.SchedaCliente.Disponi()
      },

      VisualizzaDocumento(Documento)
      {
        var Self = this

        if(Documento.Tipologia == TIPOLOGIA_RIGHE_CONTO_CLIENTE.Fatture)
        {
          Self.TipoDocumento                = Documento.Tipologia
          Self.TestoPopupDocumento          = Documento.MM_DESCRIZIONE_DOCUMENTO

          Self.DocumentoSelezionato         = new TSchedaFattura(SystemInformation.AdvQuery)
          Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
          Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
        }
        else if(Documento.Tipologia == TIPOLOGIA_RIGHE_CONTO_CLIENTE.RataFattura)
        {
          Self.TipoDocumento                = Documento.Tipologia
          Self.TestoPopupDocumento          = Documento.MM_DESCRIZIONE_DOCUMENTO

          Self.DocumentoSelezionato         = new TSchedaFattura(SystemInformation.AdvQuery)
          Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
          Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
        }
        else if(Documento.Tipologia == TIPOLOGIA_RIGHE_CONTO_CLIENTE.Movimenti)
        {
          Self.TipoDocumento                = Documento.Tipologia
          Self.TestoPopupDocumento          = 'Movimento di Conto'

          Self.DocumentoSelezionato         = new TSchedaMovimento(SystemInformation.AdvQuery)
          Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
          Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
        }
        else if(Documento.Tipologia == TIPOLOGIA_RIGHE_CONTO_CLIENTE.NotaDiCredito)
        {
          Self.TipoDocumento                = Documento.Tipologia
          Self.TestoPopupDocumento          = Documento.MM_DESCRIZIONE_DOCUMENTO

          Self.DocumentoSelezionato         = new TSchedaNotaDiCredito(SystemInformation.AdvQuery)
          Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
          Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
        }
      },

      OnClickApriFattura(Fattura)
      {
        var Self = this
        this.FatturaSelezionata        = new TSchedaFattura(SystemInformation.AdvQuery)
        this.FatturaSelezionata.Chiave = Fattura.CHIAVE
        this.FatturaSelezionata.Dati.ID_CLIENTE = this.SchedaCliente.Chiave
        this.FatturaSelezionata.PulsanteAlberoPremuto = true
        this.FatturaSelezionata.Disponi(function()
                                        {
                                          Self.PopupVisualizzaFattura = true
                                        })
      },

      ChiudiPopupVisualizzaFattura()
      {
        this.PopupVisualizzaFattura = false
      },

      ConfermaPopupVisualizzaFattura()
      {
        var Self = this
        Self.FatturaSelezionata.Registra(function()
                                        {
                                          Self.PopupVisualizzaFattura = false
                                        })
      },

      ChiudiPopupDocumento()
      {
        this.PopupDocumento = false
      },

      ConfermaPopupDocumento()
      {
        var Self = this
        Self.DocumentoSelezionato.Registra(function()
                                            {
                                              Self.PopupDocumento = false
                                            })
      },

      GetTabs()
      {
        let Result = null
        Result = {
                    ActiveTab : 'DatiFatturazione',
                    Tabs      : [
                                  {
                                    Caption : 'Dati fatturazione',
                                    Id      : 'DatiFatturazione'
                                  },
                                  {
                                    Caption : 'Recapiti',
                                    Id      : 'Recapiti' 
                                  },
                                  {
                                  Caption  : 'Filiali',
                                  Id       : 'Filiali'
                                  },
                                  {
                                  Caption  : 'Agenda eventi',
                                  Id       : 'AgendaEventi'
                                  },
                                  {
                                    Caption : 'Ritenute',
                                    Id      : 'Ritenute' 
                                  },
                                ]
                  }

        if(SystemInformation.AccessRights.VisibilitaListinoPrezziCliente())
        {
          Result.Tabs.push({
                            Caption : 'Listino',
                            Id      : 'Sconti' 
                          })
        }
        if(SystemInformation.AccessRights.VisibilitaSituazioneContabileCliente())
        {
          Result.Tabs.push({
                            Caption : 'Situazione contabile',
                            Id      : 'SituazioneContabile' 
                          })
        }
        if(SystemInformation.AccessRights.VisibilitaAllegatiCliente())
        {
          Result.Tabs.push({
                            Caption : 'Allegati',
                            Id      : 'Allegati' 
                          })
        }
        return Result

      },

      GetTotaleRitenute()
      {
        let Result = []
        for(let i = 0; i < this.SchedaCliente.DataTableRitenuteCliente.Righe.length; i++)
        {
          let Riga    = this.SchedaCliente.DataTableRitenuteCliente.Righe[i];
          if(Riga.Dati['CERTIFICATA'].Valore)
          {
            let Trovato = false
            let DataStr = Riga.Dati['DATA'].Valore;
            let Anno    = new Date(DataStr).getFullYear();
            let Ritenuta = parseFloat(Riga.Dati['RITENUTE'].Valore) || 0;

            for(let j = 0; j < Result.length; j++)
            {
              if (Result[j].Anno == Anno) 
              {
                Result[j].Totale += Ritenuta;
                Trovato = true;
                break;
              }
            }

            if(!Trovato)
            {
              Result.push({ 
                            Anno   : Anno,
                            Totale : Ritenuta,
                          })
            }
          }
        }

        for(let i = 0; i < this.SchedaCliente.LsRitenute.length; i++)
        {
          let RigaRitenuta = this.SchedaCliente.LsRitenute[i]

          let Trovato = false
          for(let j = 0; j < Result.length; j++)
          {
            if (Result[j].Anno == RigaRitenuta.Anno) 
            {
              Result[j].Totale += RigaRitenuta.RitenutaPagata;
              Result[j].Totale += RigaRitenuta.SommaRitenutaDiFattureNonPagateMaCertificate;
              Trovato = true;
              break;
            }
          }

          if(!Trovato)
          {
            Result.push({ 
                          Anno   : RigaRitenuta.Anno,
                          Totale : RigaRitenuta.RitenutaPagata,
                        })
          }
        }

        if(Result.length != 0)
          Result.sort((a, b) => b.Anno - a.Anno);

        return Result
      }
    },

    beforeMount() 
    {
      this.ActiveTab = 'DatiFatturazione'
      this.CaricaContoCorrente()
      this.Tabs = this.GetTabs()
      // this.GetListaProdotti()
    }
  }
</script>

<style scoped>
label {
  margin-bottom: 0px;
}

.text-highlighted {
  color: red;
}
</style>