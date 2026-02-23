<template>
  <VUEModalCaricamentoDati v-if="PopupAttesaCalcolo" :PathGif="require('@/assets/images/CaricamentoDatiGif.gif')"/>


  <VUEConfirm :Popup="SchedaClienti.PopupConfermaDoppioCodiceFiscale" 
              :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma"
              :Richiesta="'Attenzione: il codice fiscale è già presente.\n\nSei sicuro di voler inserire il nuovo cliente?'" 
              @onClickConfermaPopup="SchedaClienti.PopupConfermaDoppioCodiceOnSuccess" 
              @onClickChiudiPopup="AnnullaPopUpCF">
  </VUEConfirm>


  <VUEModal v-if="PopupConfermaInserimento" :PathLogo="require('../../assets/images/LogoGemini2.png')"
                 :Programma="NomeProgramma" :Titolo="'Conferma'" :Altezza="'100px'" :Larghezza="'400px'"
          @onClickChiudiModal="ChiusuraCreazioneClienteGuidato">
    <template v-slot:Body>
      <div class="form-row">
      <div class="col-md-12">
          <label style="margin-left:5%;width:80%;float:left;margin-top:7px;font-size:16px">Cliente registrato.</label>
      </div>
      </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="ChiusuraCreazioneClienteGuidato" data-dismiss="modal">Chiudi</button>
    </template>
  </VUEModal>


  <VUEModal v-if="ModalConferma" :PathLogo="require('../../assets/images/LogoGemini2.png')"
                 :Programma="NomeProgramma" :Titolo="'Conferma'" :Altezza="'200px'" :Larghezza="'600px'"
            @onClickChiudiModal="ChiusuraCreazioneClienteGuidato">
      <template v-slot:Body>
        <div style="float:center; font-weight:bold;">Desideri aggiungere un'altra filiale oppure procedere con l'inserimento del cliente?</div>
      </template>
      <template v-slot:Footer>
        <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;" @click="ChiusuraCreazioneClienteGuidato" data-dismiss="modal">Annulla</button>
        <button type="button" class="btn btn-info" style="float:right;margin-left:10px;font-weight:bold;" @click="OnClickAggiungiFiliale()" data-dismiss="modal">Aggiungi nuova filiale</button>
        <button type="button" class="btn btn-success" style="margin-left:10px; font-weight:bold;" @click="OnClickConferma()" data-dismiss="modal">Conferma inserimento cliente</button>
      </template>
  </VUEModal>





  <VUEModal v-if="PopupInserimentoCliente" :PathLogo="require('../../assets/images/LogoGemini2.png')"
              :Programma="NomeProgramma" :Altezza="'530px'" :Larghezza="'1350px'" :Titolo="'Inserimento cliente guidato'"
              @onClickChiudiModal="ChiusuraCreazioneClienteGuidato">
      <template v-slot:Body>

        <VUEConfirm :Popup="PopupDataRowCordinate"
                    :PathLogo="require('../../assets/images/LogoGemini2.png')"
                    :Programma="NomeProgramma" 
                    :Richiesta="'Vuoi aggiornare le coordinate?'" 
                    @onClickConfermaPopup="ConfermaAggiorna" 
                    @onClickChiudiPopup="AnnullaAggiorna">
        </VUEConfirm>

        <div v-if="SchedaAttuale == SCHEDA_CLIENTE">
          <div class="ZMSeparatoreScheda">Cliente</div>
          <div class="ZMNuovaRigaScheda">
            <div v-if="CodiceUltimoCliente != null" style="float:left;height: 15px">
              <label style="margin-right:15px;"><span style="font-weight:bold;">Ultimo codice cliente:</span> {{ CodiceUltimoCliente }}</label>
            </div>
          </div>
          <div style="clear:both;height:5px">&nbsp;</div>
          <div style="clear:both;padding-top: 6px;">
            <div style="float:left;width:15%">
              <label style="font-weight: bold;">Codice cliente </label>
              <input type="text" class="form-control" @keypress="OnKeyPressCodiceCliente()" v-model="SchedaClienti.Dati.CODICE_CLIENTE" placeholder="Codice cliente" maxlength="10"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:84%">
              <label style="font-weight: bold;">Ragione sociale </label>
              <input type="text" class="form-control" v-model="SchedaClienti.Dati.RAGIONE_SOCIALE" placeholder="Ragione sociale"/>
              <label v-if="SchedaClienti.Dati.RAGIONE_SOCIALE.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
            </div>
          </div>

          <div style="clear:both;height:5px">&nbsp;</div>

          <div class="ZMSeparatoreScheda">Dati fatturazione</div>
            <div style="clear:both">
              <div style="float:left;width:28%">
                <label style="font-weight: bold;">Indirizzo</label>
                <input type="text" class="form-control" v-model="SchedaClienti.Dati.INDIRIZZO_FATTURAZIONE" placeholder="Indirizzo"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:10%">
                <label style="font-weight: bold;">Civico</label>
                <input maxlength="7" type="text" class="form-control" v-model="SchedaClienti.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:6%">
                <label style="font-weight: bold;">CAP</label>
                <VUEInputCAP v-model="SchedaClienti.Dati.CAP_FATTURAZIONE"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:25%">
                <label style="font-weight: bold;">Comune</label>
                <input type="text" class="form-control" v-model="SchedaClienti.Dati.COMUNE_FATTURAZIONE" placeholder="Comune"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:19%">
                <label style="font-weight: bold;">Provincia</label>
                <VUEInputProvince v-model="SchedaClienti.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:7%">
                <label style="font-weight: bold;">Zona</label>
                <VUEInputZone v-model="SchedaClienti.Dati.ZONA_FATTURAZIONE"/>
              </div> 
            </div>
            <div class="ZMNuovaRigaScheda">
            <div style="float:left;width:16%">
              <label style="font-weight: bold;">Codice fiscale</label>
              <VUEInputCodiceFiscale v-model="SchedaClienti.Dati.CODICE_FISCALE"/>
              <label v-if="(SchedaClienti.Dati.PARTITA_IVA.trim() == '' && SchedaClienti.Dati.CODICE_FISCALE.trim() == '') && !SchedaClienti.Dati.IS_A_CONDOMINIO" 
                    class="ZMFormLabelError">Campo obbligatorio</label>
              <label v-if="ErroreCodiceFiscale != '' && !DeveloperMode && SchedaClienti.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:11%">
              <label style="font-weight: bold;">Partita IVA</label>
              <VUEInputPartitaIVA v-model="SchedaClienti.Dati.PARTITA_IVA" />
              <label v-if="(SchedaClienti.Dati.PARTITA_IVA.trim() == '' && SchedaClienti.Dati.CODICE_FISCALE.trim() == '' && !SchedaClienti.Dati.IS_A_CONDOMINIO)" 
                    class="ZMFormLabelError">Campo obbligatorio</label>
              <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaClienti.Dati.NAZIONE_EM_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:10%">
                <label style="font-weight: bold;">&nbsp;</label>
                <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickAcquisisciCliente">Acquisisci cliente</button>
              </div> 
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:10%">
              <label style="font-weight: bold;">Codice SDI</label>
              <input type="text" class="form-control" v-model="SchedaClienti.Dati.COD_ENTE_SDI" placeholder="Cod. SDI" maxlength="7"/>
              <label v-if="SchedaClienti.Dati.COD_ENTE_SDI.trim().length != 7" class="ZMFormLabelError">
                {{(SchedaClienti.Dati.COD_ENTE_SDI.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
              </label>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:10%">
              <label style="font-weight: bold;">Cod. uff. dest.</label>
              <input type="text" class="form-control" 
                    v-model="SchedaClienti.Dati.COD_UFFICIO_DEST"
                    placeholder="Codice ufficio destinazione"
                    maxlength="6"/>
              <label v-if="SchedaClienti.Dati.COD_UFFICIO_DEST.length != 6 && SchedaClienti.Dati.COD_UFFICIO_DEST != ''" class="ZMFormLabelError">Campo non valido
              </label>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:19%">
              <label style="font-weight: bold;">Naz. emit.</label>
              <VUEInputNazioni v-model="SchedaClienti.Dati.NAZIONE_EM_PIVA" emptyElement="false"/>
              <label v-if="SchedaClienti.Dati.NAZIONE_EM_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:8%">
              <label style="font-weight: bold;">Ritenuta [%]</label>
              <input placeholder="Ritenuta" min="0" step="0.1" type="number" class="form-control" v-model="SchedaClienti.Dati.RITENUTA"/>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>      
            <div style="float:left;width:9%"> 
              <label style="font-weight: bold;">IVA [%]</label>
              <input placeholder="IVA" min="0" type="number" pattern="\d+" class="form-control" v-model="SchedaClienti.Dati.IVA_SUGGERITA_CLIENTE" @change="OnChangeIva()"/>
            </div> 
            
          </div>

            <!-- <VUEAppImportazioneClientiGuidata @DatiCliente="OnDatiClienteAggiornati" ref="ImportazioneClienti"/> -->
          <div class="ZMNuovaRigaScheda">
            <div style="float:left;width:14%">  
              <label style="font-weight: bold;">Natura pagamento</label>
              <VUEInputNaturaPagamenti  v-model="SchedaClienti.Dati.NATURA_PAGAMENTO"/>
              <label v-if="SchedaClienti.Dati.NATURA_PAGAMENTO == -1 && SchedaClienti.Dati.IVA_SUGGERITA_CLIENTE == 0" class="ZMFormLabelError">Campo obbligatorio</label>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>      
            <div style="float:left;width:24%">
              <label style="font-weight: bold;">PEC</label>
              <input type="email" class="form-control" v-model="SchedaClienti.Dati.PEC" placeholder="PEC"/>
            </div> 
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:16%">
              <label style="font-weight: bold;">Bollo</label>
              <select class="form-control" v-model="SchedaClienti.Dati.BOLLO">
                <option v-for="SelectOption in LsPagamentoBollo" 
                        :key="SelectOption.Id"
                        :value="SelectOption.Id"
                        :selected="SelectOption.Id == SchedaClienti.Dati.BOLLO">
                  {{SelectOption.Descrizione}} 
                </option>
              </select>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:15%">
              <label style="font-weight: bold;">Esigibilità IVA</label>
              <VUEInputEsigibilitaIVA v-model="SchedaClienti.Dati.ESIGIBILITA_IVA"/>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:19%">
              <label style="font-weight: bold;">Descrizione spese</label>
              <input placeholder="Descrizione spese" type="text" class="form-control" v-model="SchedaClienti.Dati.SPESE_INCASSO_DESCRIZIONE"/>
            </div> 
            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:7%">
              <label style="font-weight: bold;">Spese [€]</label>
              <input type="number" step="0.01" class="form-control" v-model="SchedaClienti.Dati.SPESE_INCASSO_SUGGERITE"/>
            </div>
          </div>

          <div class="ZMNuovaRigaScheda">
            <div style="float:left;width:15%">
              <div style="float:left;margin-right:5px">
                <input type="checkbox" style="margin-top:2px;" v-model="SchedaClienti.Dati.SOGLIA_PRESENTE" @change="OnChangeSogliaPresente"/>
              </div>
              <div style="float:left;">
                <label style="font-weight: bold;">Soglia esenz. IVA&nbsp;</label>
              </div>
            </div>
            <div v-if="SchedaClienti.Dati.SOGLIA_PRESENTE">
              <div style="float:left;width:1%">&nbsp;</div>
              <div style="float:left;width:10%">
                  <label style="font-weight: bold;">Soglia [€]</label>
                  <input type="number" step="0.01" class="form-control" v-model="SchedaClienti.Dati.SOGLIA_X_NO_IVA" placeholder="Soglia"/>
                  <label v-if="SchedaClienti.Dati.SOGLIA_PRESENTE && SchedaClienti.Dati.SOGLIA_X_NO_IVA == 0" class="ZMFormLabelError">Campo obbligatorio</label>
              </div>
              <div style="float:left;width:1%">&nbsp;</div>      
              <div style="float:left;width:16%">  
                <label style="font-weight: bold;">Natura pagamento soglia</label>
                <VUEInputNaturaPagamenti v-model="SchedaClienti.Dati.SOGLIA_NAT_PAG"/>
                <label v-if="SchedaClienti.Dati.SOGLIA_PRESENTE && SchedaClienti.Dati.SOGLIA_NAT_PAG == -1" class="ZMFormLabelError">Campo obbligatorio</label>
              </div>
              <div style="clear:both;height:5px">&nbsp;</div>
            </div>
          </div>



          <div class="ZMSeparatoreScheda">Indirizzo di destinazione</div>
          <div style="clear:both">
            <div style="float:left;width:52%">
            <label style="font-weight: bold;">Presso</label>
            <input type="text" class="form-control" v-model="SchedaClienti.Dati.PRESSO" placeholder="Presso"/>
            </div>
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:36%">
                  <label style="font-weight: bold;">Indirizzo</label>
                  <input type="text" class="form-control" v-model="SchedaClienti.Dati.INDIRIZZO_SPEDIZIONE" placeholder="Indirizzo"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:10%">
                  <label style="font-weight: bold;">Civico</label>
                  <input maxlength="7" type="text" class="form-control" v-model="SchedaClienti.Dati.NR_CIVICO_SPEDIZIONE" placeholder="Nr. civico"/>
              </div> 
          </div>
          <div style="clear:both">
              <div style="float:left;width:6%">
                  <label style="font-weight:bold;">CAP</label>
                  <VUEInputCAP v-model="SchedaClienti.Dati.CAP_SPEDIZIONE"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:25%">
                  <label style="font-weight: bold;">Comune</label>
                  <input type="text" class="form-control" v-model="SchedaClienti.Dati.COMUNE_SPEDIZIONE" placeholder="Comune"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:19%">
                  <label style="font-weight: bold;">Provincia</label>
                  <VUEInputProvince v-model="SchedaClienti.Dati.PROVINCIA_SPEDIZIONE" emptyElement="true"/>
              </div>           
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:12%">
                  <label style="font-weight: bold;">Zona</label>
                  <VUEInputZone v-model="SchedaClienti.Dati.ZONA_SPEDIZIONE"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:23%">
                  <label style="font-weight: bold;">Nazione</label>
                  <VUEInputNazioni v-model="SchedaClienti.Dati.NAZIONE_SPEDIZIONE" emptyElement="true"/>
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
              <VUEInputCondPagamenti v-model="SchedaClienti.Dati.COND_PAGAMENTO"/>
              <label v-if="SchedaClienti.Dati.COND_PAGAMENTO == -1" class="ZMFormLabelError">Campo obbligatorio</label>
            </div>
            <div style="float:left;width:1%">&nbsp;</div>      
            <div style="float:left;width:67%">  
              <label style="font-weight: bold;">Note in fattura</label>
              <input type="text" class="form-control" v-model="SchedaClienti.Dati.NOTE_IN_FATTURA" placeholder="Note in fattura"/>
            </div>
          </div>
          <VUEInputContoRibaCorrente :ContoCorrente="SchedaClienti.Dati.ContoCorrente"/>


        </div>


        <div v-if="SchedaAttuale == SCHEDA_RECAPITI">
          <div class="ZMSeparatoreScheda">Recapiti</div>
          <div style="clear:both;height:1%">&nbsp;</div>
          <VUERecapiti :SchedaRecapiti="SchedaClienti.SchedaRecapiti"></VUERecapiti>
        </div>

        <div v-if="SchedaAttuale == SCHEDA_FILIALI">

          <div v-if="SchedaClienti.CurrentSchedaFiliale == 0" class="ZMSeparatoreScheda">Filiale Principale</div>
          <div v-if="SchedaClienti.CurrentSchedaFiliale != 0" class="ZMSeparatoreScheda">Filiale</div>
          <div style="float:left;width: 10%;">&nbsp;</div>
          <div style="float:left;width: 50%;">
            <label style="font-weight: bold;">Nome</label>
            <input type="text" class="form-control" v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].NOME" />
            <label v-if="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].NOME == ''" class="ZMFormLabelError">Campo obbligatorio</label>
          </div>
          <div style="float:left;width: 1%;">&nbsp;</div>
          <div style="float:left;width: 24%;">
            <label style="font-weight: bold;">Mese visita</label>
            <select class="form-control" v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].MeseVisitaFiliale">
              <option v-for="Mese in ListaMesi" :key="Mese.CHIAVE" :value="Mese.CHIAVE">{{ Mese.DESCRIZIONE }}</option>
            </select>
          </div>
          <div style="clear:both;height:10px">&nbsp;</div>

          <div class="ZMSeparatoreScheda">Indirizzo</div>

          <div style="clear:both">
              <div style="float:left;width:50%">
                  <label style="font-weight: bold;">Indirizzo</label>
                  <input type="text" class="form-control" v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].INDIRIZZO" placeholder="Indirizzo"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:7%">
                  <label style="font-weight: bold;">Civico</label>
                  <input maxlength="7" type="text" class="form-control" v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].NR_CIVICO" placeholder="Nr. civico"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:7%">
                  <label style="font-weight: bold;">CAP</label>
                  <VUEInputCAP v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].CAP"/>
              </div>
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:13%">
                  <label style="font-weight: bold;">Latitudine</label>
                  <input maxlength="10" type="text" class="form-control" v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].LAT_IND" placeholder="Latitudine"/>
              </div> 
              <div style="float:left;width:1%;">&nbsp;</div>
              <div style="float:left;width:13%">
                  <label style="font-weight: bold;">Longitudine</label>
                  <input maxlength="10" type="text" class="form-control" v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].LONG_IND" placeholder="Longitudine"/>
              </div>
              <div style="padding-top:15px;">
                      <img style="float:left;cursor:pointer; margin-left:20px"  
                        src="@/assets/images/coordinate.png" 
                        @click="OnClickPopupModificaCoordinate"/>
              </div>
          </div>
          <div style="clear:both">
            <div style="float:left;width:24%">
                  <label style="font-weight: bold;">Comune</label>
                  <input type="text" class="form-control" v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].COMUNE" placeholder="Comune"/>
            </div>
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:14%">
                <label style="font-weight: bold;">Provincia</label>
                <VUEInputProvince v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].PROVINCIA" emptyElement="true"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:10%">
                <label style="font-weight: bold;">Zona</label>
                <VUEInputZone v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].ZONA"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:15%">
                <label style="font-weight: bold;">Nazione</label>
                <VUEInputNazioni v-model="SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].NAZIONE" emptyElement="true"/>
            </div>
            
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:13%">
                <label style="font-weight: bold;">&nbsp;</label>
                <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickCopiaDatiFatturazione">Copia dati fatt.</button>
            </div>
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:13%">
                <label style="font-weight: bold;">&nbsp;</label>
                <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickCopiaDatiDestinazione">Copia dati dest.</button>
            </div>
          </div>

          <div style="clear:both;height:10px">&nbsp;</div>

        </div>

      </template>
      <template v-slot:Footer>
        <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:10%" @click="ChiusuraCreazioneClienteGuidato" data-dismiss="modal">Annulla</button>
        
        <button type="button" v-if="SchedaAttuale == SCHEDA_CLIENTE && SchedaClienti.CanRecord()" class="btn btn-info" style="float:right;margin-left:10px;font-weight:bold;width:10%" @click="GetProssimaScheda()" data-dismiss="modal">Avanti</button>
        <button type="button" v-else-if="SchedaAttuale != SCHEDA_CLIENTE" class="btn btn-info" style="float:right;margin-left:10px;font-weight:bold;width:10%" @click="GetProssimaScheda()" data-dismiss="modal">Avanti</button>
        
        <button v-if="SchedaAttuale != SCHEDA_CLIENTE ||
                      SchedaAttuale == SCHEDA_FILIALI &&
                      SchedaClienti.CurrentSchedaFiliale > 0" type="button" class="btn btn-info" style="float:right;margin-left:10px;font-weight:bold;width:10%" @click="GetSchedaPrecedente()" data-dismiss="modal">Indietro</button>
        <button v-if="type=SchedaAttuale == SCHEDA_FILIALI &&
                      SchedaClienti.CurrentSchedaFiliale > 0" class="btn btn-info" style="margin-left:10px; float:right; font-weight:bold; width:10%" @click="OnClickEliminaFiliale()" data-dismiss="modal">Elimina filiale</button>
      </template>
    </VUEModal>
  

</template>

<script>
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputNaturaPagamenti from '@/components/InputComponents/VUEInputNaturaPagamenti.vue';
import VUEInputPartitaIVA from '../../components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputEsigibilitaIVA from '@/components/InputComponents/VUEInputEsigibilitaIVA.vue';
import VUEInputZone from '@/components/InputComponents/VUEInputZone.vue';
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import VUERecapiti, {TSchedaRecapiti} from '@/views/SchedeDatabase/ComponentMultiScheda/VUERecapiti.vue';
import { SystemInformation, PAGAMENTO_BOLLO, NOME_PROGRAMMA} from '@/SystemInformation.js' 
import { TZFattElettronicaEsigibilita } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import VUEInputCondPagamenti from '@/components/InputComponents/VUEInputCondPagamenti.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js';
import VUEModalCaricamentoDati from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModalCaricamentoDati.vue';
import VUEConfirm from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import { TZOpenStreetMap } from '../../../../../../../../Librerie/VUE/ZOpenStreetMap.js';
import VUEInputContoRibaCorrente from '@/components/InputComponents/VUEInputContoRibaCorrente.vue';
// import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable.js'

export class TSchedaFiliale
{
  NOME                = ''   
  INDIRIZZO           = ''
  NR_CIVICO           = ''
  CAP                 = ''
  COMUNE              = ''
  PROVINCIA           = ''
  ZONA                = ''
  NAZIONE             = ''
  FILIALE_DISATTIVATA = 'F'
  LONG_IND            = null
  LAT_IND             = null
  MeseVisitaFiliale   = 1

}

export class TSchedaCliente
{
    SchedaRecapiti        = ''
    SchedeFiliali         = []
    CodiceFiscaleIniziale = ''
    CurrentSchedaFiliale  = 0
    PopupConfermaDoppioCodiceFiscale   = false;
    PopupConfermaDoppioCodiceOnSuccess = null;
    ChiaveCliente = null

    Dati = { 
                  RAGIONE_SOCIALE             : '',
                  CODICE_CLIENTE              : '',
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
                  ESIGIBILITA_IVA             : TZFattElettronicaEsigibilita.esgImmediata,
                  NATURA_PAGAMENTO            : -1,
                  COND_PAGAMENTO              : -1,
                  PEC                         : '',
                  SOGLIA_X_NO_IVA             : 0,
                  SOGLIA_PRESENTE             : false,
                  SOGLIA_NAT_PAG              : -1,
                  SPESE_INCASSO_DESCRIZIONE   : '',
                  SPESE_INCASSO_SUGGERITE     : 0,
                  IVA_SUGGERITA_CLIENTE       : 22,
                  BOLLO                       : PAGAMENTO_BOLLO.NessunBollo,
                  RITENUTA                    : 0,
                  IS_A_CONDOMINIO             : false,
                  ATTIVO                      : 'T',

                  PRESSO                      : '',
                  INDIRIZZO_SPEDIZIONE        : '',
                  NR_CIVICO_SPEDIZIONE        : '',
                  CAP_SPEDIZIONE              : '',
                  COMUNE_SPEDIZIONE           : '',
                  PROVINCIA_SPEDIZIONE        : -1,
                  ZONA_SPEDIZIONE             : -1,
                  NAZIONE_SPEDIZIONE          : -1,

                  NOTE_IN_FATTURA             : '',
                  ContoCorrente               : { 
                                                  IBAN              : '',
                                                  BIC               : '',
                                                  SWIFT             : '',
                                                  BANCA_APPOGGIO    : '',
                                                  ID_CONTO_CORRENTE : -1,
                                                  NR_CONTO          : '',
                                                  CONTO_RIBA        : false
                                                },
    }  
    constructor()
    {
      this.SchedaRecapiti  = new TSchedaRecapiti(false, true, false, false, true)
      this.SchedeFiliali.push(new TSchedaFiliale());
      
    }

    CanRecord()
    {
      return this.Dati.RAGIONE_SOCIALE.trim() != '' && 
              this.Dati.NAZIONE_EM_PIVA != -1 &&
              // (this.Dati.IS_A_CONDOMINIO || (this.Dati.CODICE_FISCALE.trim() != '' || this.Dati.PARTITA_IVA.trim() != '') || SystemInformation.DeveloperMode) &&
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
              this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano)
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
                                            if((ArrayInfoPartitaIva[0].CHIAVE != Self.ChiaveCliente) || (ArrayInfoPartitaIva.length > 1))
                                            {
                                              OnError('PARTITA IVA GIA PRESENTE, APPARTENENTE A ' + ArrayInfoPartitaIva[0].RAGIONE_SOCIALE,'','')
                                              Trovato = true                                           
                                            }
                                          }
                                          if(ArrayInfoCodiceFiscale.length != 0)
                                          {
                                            if((ArrayInfoCodiceFiscale[0].CHIAVE != Self.ChiaveCliente) || (ArrayInfoCodiceFiscale.length > 1))
                                              {
                                                Trovato = true    
                                                Self.PopupConfermaDoppioCodiceFiscale   = true
                                                Self.PopupConfermaDoppioCodiceOnSuccess = OnSuccess
                                              }
                                          }
                                          if(ArrayInfoCodiceCliente.length  != 0)
                                          {
                                            if(ArrayInfoCodiceCliente[0].CHIAVE != Self.ChiaveCliente)
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

    Registra(OnSuccess, OnError)
    {  
      var Self = this
      this.CheckDoppiaPartitaIva(this.Dati.PARTITA_IVA,this.Dati.CODICE_FISCALE, this.Dati.CODICE_CLIENTE,
                                function()
                                {
                                  Self.PopupConfermaDoppioCodiceFiscale   = false
                                  var ObjQuery = { Operazioni : [] };
                                  ObjQuery.Operazioni.push({
                                                            Query     : Self.ChiaveCliente != null? "UpdateCliente" : "InsertCliente",
                                                            Parametri : {
                                                                          CHIAVE_CLIENTE              : Self.ChiaveCliente,
                                                                          RAGIONE_SOCIALE             : TSchedaGenerica.PrepareForRecordString(Self.Dati.RAGIONE_SOCIALE),
                                                                          CODICE                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.CODICE),
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
                                                                          ESIGIBILITA_IVA             : TSchedaGenerica.PrepareForRecordString(Self.Dati.ESIGIBILITA_IVA),
                                                                          NATURA_PAGAMENTO            : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NATURA_PAGAMENTO),
                                                                          PEC                         : TSchedaGenerica.PrepareForRecordString(Self.Dati.PEC),
                                                                          SOGLIA_X_NO_IVA             : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.SOGLIA_X_NO_IVA * 100),
                                                                          SOGLIA_PRESENTE             : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.SOGLIA_PRESENTE),
                                                                          SOGLIA_NAT_PAG              : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.SOGLIA_NAT_PAG),
                                                                          SPESE_INCASSO_DESCRIZIONE   : TSchedaGenerica.PrepareForRecordString(Self.Dati.SPESE_INCASSO_DESCRIZIONE),
                                                                          SPESE_INCASSO_SUGGERITE     : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.SPESE_INCASSO_SUGGERITE * 100),
                                                                          IVA_SUGGERITA_CLIENTE       : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.IVA_SUGGERITA_CLIENTE * 10),
                                                                          BOLLO                       : TSchedaGenerica.PrepareForRecordString(Self.Dati.BOLLO),
                                                                          RITENUTA                    : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.RITENUTA * 10),
                                                                          ATTIVO                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.ATTIVO),
                                                                          PRESSO                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.PRESSO),
                                                                          INDIRIZZO_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordString(Self.Dati.INDIRIZZO_SPEDIZIONE),
                                                                          NR_CIVICO_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordString(Self.Dati.NR_CIVICO_SPEDIZIONE),
                                                                          CAP_SPEDIZIONE              : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAP_SPEDIZIONE),
                                                                          COMUNE_SPEDIZIONE           : TSchedaGenerica.PrepareForRecordString(Self.Dati.COMUNE_SPEDIZIONE),
                                                                          PROVINCIA_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PROVINCIA_SPEDIZIONE),
                                                                          ZONA_SPEDIZIONE             : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ZONA_SPEDIZIONE),
                                                                          NAZIONE_SPEDIZIONE          : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NAZIONE_SPEDIZIONE),
                                                                          COND_PAGAMENTO              : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.COND_PAGAMENTO),
                                                                          NOTE_IN_FATTURA             : TSchedaGenerica.PrepareForRecordString(Self.Dati.NOTE_IN_FATTURA),
                                                                          IBAN                        : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.IBAN)  : null,
                                                                          BIC                         : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.BIC)   : null,
                                                                          SWIFT                       : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.SWIFT) : null,
                                                                          BANCA_APPOGGIO              : Self.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(Self.Dati.ContoCorrente.BANCA) : null,
                                                                          ID_CONTO_CORRENTE           : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ContoCorrente.ID_CONTO_CORRENTE),
                                                                          IS_CLIENTE                  : true
                                                                        }
                                                          });
                                  
                                  for(let IndiceFiliale = 0; IndiceFiliale < Self.SchedeFiliali.length; IndiceFiliale++)
                                  {
                                    ObjQuery.Operazioni.push({
                                                            Query     : "InsertFiliale",
                                                            Parametri : {
                                                                          CHIAVE_CLIENTE      : Self.ChiaveCliente, 
                                                                          SEDE                : TSchedaGenerica.PrepareForRecordString(IndiceFiliale == 0 ? 'T' : 'F'),
                                                                          NOME                : TSchedaGenerica.PrepareForRecordString(Self.SchedeFiliali[IndiceFiliale].NOME),
                                                                          INDIRIZZO           : TSchedaGenerica.PrepareForRecordString(Self.SchedeFiliali[IndiceFiliale].INDIRIZZO),
                                                                          NR_CIVICO           : TSchedaGenerica.PrepareForRecordString(Self.SchedeFiliali[IndiceFiliale].NR_CIVICO),
                                                                          CAP                 : TSchedaGenerica.PrepareForRecordString(Self.SchedeFiliali[IndiceFiliale].CAP),
                                                                          COMUNE              : TSchedaGenerica.PrepareForRecordString(Self.SchedeFiliali[IndiceFiliale].COMUNE),
                                                                          PROVINCIA           : TSchedaGenerica.PrepareForRecordListIndex(Self.SchedeFiliali[IndiceFiliale].PROVINCIA),
                                                                          ZONA                : TSchedaGenerica.PrepareForRecordListIndex(Self.SchedeFiliali[IndiceFiliale].ZONA),
                                                                          NAZIONE             : TSchedaGenerica.PrepareForRecordListIndex(Self.SchedeFiliali[IndiceFiliale].NAZIONE),
                                                                          FILIALE_DISATTIVATA : TSchedaGenerica.PrepareForRecordString(Self.SchedeFiliali[IndiceFiliale].FILIALE_DISATTIVATA),
                                                                          LONG_IND            : TSchedaGenerica.PrepareForRecordInteger(Self.SchedeFiliali[IndiceFiliale].LONG_IND * 1000000) ,
                                                                          LAT_IND             : TSchedaGenerica.PrepareForRecordInteger(Self.SchedeFiliali[IndiceFiliale].LAT_IND * 1000000),
                                                                        },
                                                            ResetKeys : [2]
                                                          });
                                    Self.SchedaRecapiti.DataTableTelefono.Righe.forEach(function(Riga)
                                    {
                                        let Parametri = Self.SchedaRecapiti.DataTableTelefono.PrepareQueryParameters(Riga);
                                        if(Self.ChiaveCliente != null)
                                          Parametri.CHIAVE_CLIENTE = Self.ChiaveCliente;
                                        ObjQuery.Operazioni.push({
                                                                  Query     : "InsertTelefono",
                                                                  Parametri : Parametri,
                                                                  ResetKeys : [2]
                                                                })
                                    });
                                  }

                                  SystemInformation.AdvQuery.PostSQL('InserimentoClienteGuidata',ObjQuery,function()
                                  { 
                                    let ObjQuery2 = { Operazioni : [] };


                                    SystemInformation.AdvQuery.PostSQL('InserimentoClienteGuidata', ObjQuery2, function()
                                    { 
                                      SystemInformation.GetConfigurazioni(function()
                                      {
                                        OnSuccess()
                                      })
                                    },
                                    function(HTTPError,SubHTTPError,Response)
                                    {
                                      OnError()
                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                    });
                                  },
                                  function(HTTPError,SubHTTPError,Response)
                                  {
                                    OnError()
                                    SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                  });
                                },
                                function(HTTPError,SubHTTPError,Response)
                                {
                                  OnError()
                                  SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                })
    }
}

export default
{
  components : 
  {
    VUEInputCAP,
    VUEInputProvince,
    VUEInputNazioni,
    VUEInputNaturaPagamenti,
    VUEInputPartitaIVA,
    VUEInputCodiceFiscale,
    VUEInputEsigibilitaIVA,
    VUEInputZone,
    VUEModal,
    VUERecapiti,
    VUEInputCondPagamenti,
    VUEModalCaricamentoDati,
    VUEConfirm,
    VUEInputContoRibaCorrente
  },
    
  name: "VUEImportazioneClientiGuidata",
  data() 
  {
    const QUANTITA_TIPOLOGIA     = 'quantita e tipologia'
    const QUANTITA               = 'quantita'
    return {
      PopupInserimentoCliente  : true,
      PopupConfermaInserimento : false,
      PopupAttesaCalcolo       : false,
      SCHEDA_CLIENTE         : 'Cliente',
      SCHEDA_RECAPITI        : 'Recapiti',
      SCHEDA_FILIALI         : 'Filiali',
      SchedaAttuale          : 'Cliente',
      QUANTITA_TIPOLOGIA     : QUANTITA_TIPOLOGIA,
      QUANTITA               : QUANTITA,
      ShowCodiceCliente      : false,
      ChiaveComponenteInput  : 0,
      SchedaClienti          : new TSchedaCliente(),
      DeveloperMode          : SystemInformation.DeveloperMode,
      StatoItaliano          : SystemInformation.Configurazioni.StatoItaliano,
      LsPagamentoBollo       : SystemInformation.GetLsPagamentoBollo(),
      SystemInformationConfigurazioni      : SystemInformation.Configurazioni,
      ModalConferma          : false,
      CodiceUltimoCliente    : null,
      PopupDataRowCordinate  : false,
      ListaMesi              : TZDateFunct.GetListaMensilitaPerDataTable(),
      NomeProgramma          : NOME_PROGRAMMA

    };
  },

  methods:
  {
    OnClickEliminaFiliale()
    {
      this.SchedaClienti.SchedeFiliali.splice(this.SchedaClienti.CurrentSchedaFiliale, 1)
      this.SchedaClienti.CurrentSchedaFiliale--
    },

    OnClickConferma()
    {
      this.PopupInserimentoCliente  = false
      this.PopupAttesaCalcolo       = true
      this.ModalConferma            = false
      this.SchedaClienti.Registra(() => this.ConfermaInserimento(), () => this.OnErrorInserimento())
    },

    OnClickAggiungiFiliale()
    {
      this.ModalConferma = false
      this.PopupInserimentoCliente = true
      this.SchedaAttuale = this.SCHEDA_FILIALI
      this.SchedaClienti.SchedeFiliali.push(new TSchedaFiliale())
      this.SchedaClienti.CurrentSchedaFiliale = this.SchedaClienti.SchedeFiliali.length-1
    },
    
    OnClickCopiaDatiFatturazione()
    {
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].INDIRIZZO = this.SchedaClienti.Dati.INDIRIZZO_FATTURAZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].NR_CIVICO = this.SchedaClienti.Dati.NR_CIVICO_FATTURAZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].COMUNE    = this.SchedaClienti.Dati.COMUNE_FATTURAZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].PROVINCIA = this.SchedaClienti.Dati.PROVINCIA_FATTURAZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].ZONA      = this.SchedaClienti.Dati.ZONA_FATTURAZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].NAZIONE   = this.SchedaClienti.Dati.NAZIONE_EM_PIVA
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].CAP       = this.SchedaClienti.Dati.CAP_FATTURAZIONE
    },

    OnClickCopiaDatiDestinazione()
    {
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].INDIRIZZO = this.SchedaClienti.Dati.INDIRIZZO_SPEDIZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].NR_CIVICO = this.SchedaClienti.Dati.NR_CIVICO_SPEDIZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].COMUNE    = this.SchedaClienti.Dati.COMUNE_SPEDIZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].PROVINCIA = this.SchedaClienti.Dati.PROVINCIA_SPEDIZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].ZONA      = this.SchedaClienti.Dati.ZONA_SPEDIZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].NAZIONE   = this.SchedaClienti.Dati.NAZIONE_SPEDIZIONE
      this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].CAP       = this.SchedaClienti.Dati.CAP_SPEDIZIONE
    },

    OnClickCopiaDaDatiFatturazione()
    {
      this.SchedaClienti.Dati.INDIRIZZO_SPEDIZIONE = this.SchedaClienti.Dati.INDIRIZZO_FATTURAZIONE
      this.SchedaClienti.Dati.NR_CIVICO_SPEDIZIONE = this.SchedaClienti.Dati.NR_CIVICO_FATTURAZIONE
      this.SchedaClienti.Dati.COMUNE_SPEDIZIONE    = this.SchedaClienti.Dati.COMUNE_FATTURAZIONE
      this.SchedaClienti.Dati.PROVINCIA_SPEDIZIONE = this.SchedaClienti.Dati.PROVINCIA_FATTURAZIONE
      this.SchedaClienti.Dati.ZONA_SPEDIZIONE      = this.SchedaClienti.Dati.ZONA_FATTURAZIONE
      this.SchedaClienti.Dati.NAZIONE_SPEDIZIONE   = this.SchedaClienti.Dati.NAZIONE_EM_PIVA
      this.SchedaClienti.Dati.CAP_SPEDIZIONE       = this.SchedaClienti.Dati.CAP_FATTURAZIONE
    },

    OnKeyPressCodiceCliente()
    {
      this.ShowCodiceCliente = true
    },

    // SchedaClienti.SchedeFiliali[SchedaClienti.CurrentSchedaFiliale].NAZIONE
    GetProssimaScheda()
    {
      if(this.ControllaCampiObbligatoriCliente && this.ControllaCampiObbligatoriFiliale && this.SchedaAttuale == this.SCHEDA_FILIALI && this.SchedaClienti.CurrentSchedaFiliale == this.SchedaClienti.SchedeFiliali.length-1)
      {
        this.PopupInserimentoCliente = false
        this.ModalConferma = true
      }
      else
      {
        if(this.SchedaAttuale == this.SCHEDA_CLIENTE)
          this.SchedaAttuale = this.SCHEDA_RECAPITI
        else if(this.SchedaAttuale == this.SCHEDA_RECAPITI)
          this.SchedaAttuale = this.SCHEDA_FILIALI
        else if(this.SchedaAttuale == this.SCHEDA_FILIALI &&
                this.SchedaClienti.CurrentSchedaFiliale < this.SchedaClienti.SchedeFiliali.length - 1)
          this.SchedaClienti.CurrentSchedaFiliale++
      }
    },

    GetSchedaPrecedente()
    {
      if(this.SchedaAttuale == this.SCHEDA_RECAPITI)
        this.SchedaAttuale = this.SCHEDA_CLIENTE
      else 
      {
        if(this.SchedaAttuale == this.SCHEDA_FILIALI)
        {
          if(this.SchedaClienti.CurrentSchedaFiliale > 0)
            this.SchedaClienti.CurrentSchedaFiliale--
          else this.SchedaAttuale = this.SCHEDA_RECAPITI
        }
      }
    },

    GetClassName()
    {
      return 'TSchedaCliente';
    },

    ChiudiModal()
    {
      this.PopupInserimentoCliente  = false
      this.PopupAttesaCalcolo       = false
    },

    ConfermaInserimento()
    {
      this.PopupInserimentoCliente  = false
      this.PopupAttesaCalcolo       = false
      this.PopupConfermaInserimento = true
    },

    OnErrorInserimento()
    {
      this.PopupInserimentoCliente  = true
      this.PopupAttesaCalcolo       = false
    },

    ChiusuraCreazioneClienteGuidato()
    {
      this.PopupInserimentoCliente  = false
      this.PopupAttesaCalcolo       = false
      this.PopupConfermaInserimento = false
      this.$emit('onClickAttivaModalInsermentoClienteGuidato')
    },

    AnnullaPopUpCF()
    {
      this.SchedaClienti.PopupConfermaDoppioCodiceOnSuccess  = null
      this.SchedaClienti.PopupConfermaDoppioCodiceFiscale    = false
      this.OnErrorInserimento()
    },

    ConfermaAggiorna()
    {
      this.OnClickTrovaCoordinateFiliale()
      this.PopupDataRowCordinate = false 
    },

    AnnullaAggiorna()
    {
      this.PopupDataRowCordinate = false 
    },

    OnClickPopupModificaCoordinate()
    {
      this.PopupDataRowCordinate = true
    },

    OnClickTrovaCoordinateFiliale()
    { 
      let Self = this
      var Provincia = SystemInformation.Configurazioni.Province.find(function(Elemento)
                      {
                        return Elemento.CHIAVE == Self.SchedaClienti.SchedeFiliali[Self.SchedaClienti.CurrentSchedaFiliale].PROVINCIA
                      });
      
      Provincia = Provincia != undefined ? Provincia.NOME : '-'

      TZOpenStreetMap.GetCoordinate(this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].NR_CIVICO, 
                                    this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].INDIRIZZO, 
                                    Provincia,
                                    function(Latitudine, Longitudine)
                                    {
                                      Self.SchedaClienti.SchedeFiliali[Self.SchedaClienti.CurrentSchedaFiliale].LAT_IND  = Latitudine
                                      Self.SchedaClienti.SchedeFiliali[Self.SchedaClienti.CurrentSchedaFiliale].LONG_IND = Longitudine
                                      Self.PopupDataRowCordinate = false
                                    },
                                    function(Message)
                                    {
                                      Self.SchedaClienti.SchedeFiliali[Self.SchedaClienti.CurrentSchedaFiliale].LAT_IND  = null
                                      Self.SchedaClienti.SchedeFiliali[Self.SchedaClienti.CurrentSchedaFiliale].LONG_IND = null
                                      alert(Message)
                                      Self.PopupDataRowCordinate = false
                                    });
    },

    OnClickAcquisisciCliente()
    {
      if(this.SchedaClienti.Dati.PARTITA_IVA != '' || this.SchedaClienti.Dati.CODICE_FISCALE != '')
      {
        let Parametri = {}
        Parametri.PARTITA_IVA    = this.SchedaClienti.Dati.PARTITA_IVA
        Parametri.CODICE_FISCALE = this.SchedaClienti.Dati.CODICE_FISCALE

        if(this.SchedaClienti.Dati.PARTITA_IVA != '' && this.SchedaClienti.Dati.CODICE_FISCALE != '')
          Parametri.Entrambi = -1
        else if(this.SchedaClienti.Dati.PARTITA_IVA != '')
          Parametri.SoloPartitaIva = -1
        else if(this.SchedaClienti.Dati.CODICE_FISCALE != '')
          Parametri.SoloCodiceFiscale = -1

        let Self      = this

        SystemInformation.AdvQuery.GetSQL('InserimentoClienteGuidata', 
                                          Parametri,
                                          function(Results)
                                          {  
                                            let ArrayInfoClienti = SystemInformation.AdvQuery.FindResults(Results,"SelectCliente")

                                            if(ArrayInfoClienti != undefined)
                                            {
                                              if(ArrayInfoClienti.length != 0)
                                              {
                                                Self.SchedaClienti.ChiaveCliente = TSchedaGenerica.DisponiFromInteger(ArrayInfoClienti[0].CHIAVE)
                                                Self.SchedaClienti.Dati.RAGIONE_SOCIALE           = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].RAGIONE_SOCIALE),
                                                Self.SchedaClienti.Dati.CODICE_CLIENTE            = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].CODICE_CLIENTE),
                                                Self.SchedaClienti.Dati.COD_ENTE_SDI              = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].COD_ENTE_SDI),
                                                Self.SchedaClienti.Dati.COD_UFFICIO_DEST          = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].COD_UFFICIO_DEST),
                                                Self.SchedaClienti.Dati.PARTITA_IVA               = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].PARTITA_IVA),
                                                Self.SchedaClienti.Dati.NAZIONE_EM_PIVA           = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].NAZIONE_EM_PIVA),
                                                Self.SchedaClienti.Dati.CODICE_FISCALE            = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].CODICE_FISCALE),
                                                Self.SchedaClienti.Dati.INDIRIZZO_FATTURAZIONE    = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].INDIRIZZO_FATTURAZIONE),
                                                Self.SchedaClienti.Dati.NR_CIVICO_FATTURAZIONE    = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].NR_CIVICO_FATTURAZIONE),
                                                Self.SchedaClienti.Dati.PROVINCIA_FATTURAZIONE    = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].PROVINCIA_FATTURAZIONE),
                                                Self.SchedaClienti.Dati.COMUNE_FATTURAZIONE       = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].COMUNE_FATTURAZIONE),
                                                Self.SchedaClienti.Dati.CAP_FATTURAZIONE          = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].CAP_FATTURAZIONE),
                                                Self.SchedaClienti.Dati.ZONA_FATTURAZIONE         = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].ZONA_FATTURAZIONE),
                                                Self.SchedaClienti.Dati.ESIGIBILITA_IVA           = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].ESIGIBILITA_IVA),
                                                Self.SchedaClienti.Dati.NATURA_PAGAMENTO          = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].NATURA_PAGAMENTO),
                                                Self.SchedaClienti.Dati.PEC                       = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].PEC),
                                                Self.SchedaClienti.Dati.SOGLIA_X_NO_IVA           = TSchedaGenerica.DisponiFromInteger(ArrayInfoClienti[0].SOGLIA_X_NO_IVA) / 100,
                                                Self.SchedaClienti.Dati.SOGLIA_PRESENTE           = TSchedaGenerica.DisponiFromBoolean(ArrayInfoClienti[0].SOGLIA_PRESENTE),
                                                Self.SchedaClienti.Dati.SOGLIA_NAT_PAG            = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].SOGLIA_NAT_PAG),
                                                Self.SchedaClienti.Dati.SPESE_INCASSO_DESCRIZIONE = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].SPESE_INCASSO_DESCRIZIONE),
                                                Self.SchedaClienti.Dati.SPESE_INCASSO_SUGGERITE   = TSchedaGenerica.DisponiFromInteger(ArrayInfoClienti[0].SPESE_INCASSO_SUGGERITE) / 100,
                                                Self.SchedaClienti.Dati.IVA_SUGGERITA_CLIENTE     = TSchedaGenerica.DisponiFromInteger(ArrayInfoClienti[0].IVA_SUGGERITA_CLIENTE) / 10,
                                                Self.SchedaClienti.Dati.BOLLO                     = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].BOLLO),
                                                Self.SchedaClienti.Dati.RITENUTA                  = TSchedaGenerica.DisponiFromInteger(ArrayInfoClienti[0].RITENUTA) / 10,
                                                Self.SchedaClienti.Dati.IS_A_CONDOMINIO           = TSchedaGenerica.DisponiFromBoolean(ArrayInfoClienti[0].IS_A_CONDOMINIO),
                                                Self.SchedaClienti.Dati.ATTIVO                    = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].ATTIVO),
                                                Self.SchedaClienti.Dati.PRESSO                    = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].PRESSO),
                                                Self.SchedaClienti.Dati.INDIRIZZO_SPEDIZIONE      = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].INDIRIZZO_SPEDIZIONE),
                                                Self.SchedaClienti.Dati.NR_CIVICO_SPEDIZIONE      = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].NR_CIVICO_SPEDIZIONE),
                                                Self.SchedaClienti.Dati.CAP_SPEDIZIONE            = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].CAP_SPEDIZIONE),
                                                Self.SchedaClienti.Dati.COMUNE_SPEDIZIONE         = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].COMUNE_SPEDIZIONE),
                                                Self.SchedaClienti.Dati.PROVINCIA_SPEDIZIONE      = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].PROVINCIA_SPEDIZIONE),
                                                Self.SchedaClienti.Dati.ZONA_SPEDIZIONE           = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].ZONA_SPEDIZIONE),
                                                Self.SchedaClienti.Dati.NAZIONE_SPEDIZIONE        = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].NAZIONE_SPEDIZIONE),
                                                Self.SchedaClienti.Dati.COND_PAGAMENTO            = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].COND_PAGAMENTO),
                                                Self.SchedaClienti.Dati.NOTE_IN_FATTURA           = TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].NOTE_IN_FATTURA),
                                                Self.SchedaClienti.Dati.ID_CONTO_CORRENTE         = TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].ID_CONTO_CORRENTE),
                                                Self.SchedaClienti.Dati.ContoCorrente             = {
                                                                                                      IBAN              : ArrayInfoClienti[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].IBAN_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].IBAN),
                                                                                                      BIC               : ArrayInfoClienti[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].BIC_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].BIC),
                                                                                                      SWIFT             : ArrayInfoClienti[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].SWIFT_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].SWIFT),
                                                                                                      BANCA             : ArrayInfoClienti[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].BANCA_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].BANCA_APPOGGIO),
                                                                                                      ID_CONTO_CORRENTE : TSchedaGenerica.DisponiFromListIndex(ArrayInfoClienti[0].ID_CONTO_CORRENTE),
                                                                                                      NR_CONTO          : TSchedaGenerica.DisponiFromString(ArrayInfoClienti[0].NR_CONTO),
                                                                                                      CONTO_RIBA        : ArrayInfoClienti[0].ID_CONTO_CORRENTE != null? false : true,
                                                                                                    }
                                              }
                                              else SystemInformation.HandleError('Nessuna corrispondenza trovata.'); 
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il cliente.');                                             

                                          },
                                          function(HTTPError,SubHTTPError,Response)
                                          {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                          }, 
                                          'SelectCliente')
      }
    }
  },
  
  emits: [ 'onClickAttivaModalInsermentoClienteGuidato' ],

  computed :
  {
      DataTableFiliali :
      {
        get()
        {
          return this.SchedaClienti.SchedeFiliali.DataTableFiliali
        }
      },

      DataTableOrari :
      {
        get()
        {
          return this.SchedaClienti.SchedeFiliali.DataTableOrari
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
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaClienti.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
              return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
        }
      },

      ErroreCodiceFiscale :
      {
        get()
        {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaClienti.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
              return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
        }
      },

      ControllaCampiObbligatoriCliente :
      {
        get()
        {
          if (this.SchedaClienti.Dati.RAGIONE_SOCIALE.trim() != '' && 
              this.SchedaClienti.Dati.NAZIONE_EM_PIVA != -1 &&
              (this.SchedaClienti.Dati.IS_A_CONDOMINIO || (this.SchedaClienti.Dati.CODICE_FISCALE.trim() != '' || this.SchedaClienti.Dati.PARTITA_IVA.trim() != '')) &&
              this.SchedaClienti.Dati.COND_PAGAMENTO != -1 &&
              this.SchedaClienti.Dati.COD_ENTE_SDI.trim().length == 7 &&
              (this.SchedaClienti.Dati.COD_UFFICIO_DEST.length == 6 || this.SchedaClienti.Dati.COD_UFFICIO_DEST == '') &&
              (this.SchedaClienti.Dati.NATURA_PAGAMENTO != -1 || this.SchedaClienti.Dati.IVA_SUGGERITA_CLIENTE != 0) &&
              (!this.SchedaClienti.Dati.SOGLIA_PRESENTE || (this.SchedaClienti.Dati.SOGLIA_X_NO_IVA != 0 && this.SchedaClienti.Dati.SOGLIA_NAT_PAG != -1)) &&
              (this.SchedaClienti.Dati.CODICE_FISCALE == '' || 
                TZEconomicFunct.VerificaCodiceFiscale(this.SchedaClienti.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk ||
                SystemInformation.DeveloperMode || 
              this.SchedaClienti.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
              (this.SchedaClienti.Dati.PARTITA_IVA == '' || 
                TZEconomicFunct.VerificaPartitaIVA(this.SchedaClienti.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || 
                SystemInformation.DeveloperMode || 
              this.SchedaClienti.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano))
              return true
          return false
        }
      },

      ControllaCampiObbligatoriFiliale :
      {
        get()
        {
          if(this.SchedaClienti.SchedeFiliali[this.SchedaClienti.CurrentSchedaFiliale].NOME == '')
            return false
          return true 
        }
      },

  },

  beforeMount()
  {
    SystemInformation.CaricaUltimoCodiceCliente((CodiceUltimoCliente) =>
                                                {
                                                  this.CodiceUltimoCliente = CodiceUltimoCliente
                                                }, true);
  }
};
</script>