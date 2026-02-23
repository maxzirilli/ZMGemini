<template>

<VUEModal v-if="PopupDataContoFornitore" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Titolo="'Inserire data per conto Fornitore'" :Altezza="'100px'" :Larghezza="'520px'"
          @onClickChiudiModal="PopupDataContoFornitore = false">
    <template v-slot:Body>
      <div class="form-row">
       <div class="col-md-12">
        <div style="float:left;width:50%">
          <label style="font-weight: bold;">Dal</label>
          <input type="date" class="form-control" style="width:100%" v-model="ContoFornitoreDal">
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:49%">
          <label style="font-weight: bold;">al </label>
          <input type="date" class="form-control" style="width:100%" v-model="ContoFornitoreAl">
        </div>
       </div>
      </div> 
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="PopupDataContoFornitore = false" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:20%" @click="ConfermaPopupDataContoFornitore" data-dismiss="modal">Conferma</button>
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
        <div v-if="TipoDocumento == 'P' || TipoDocumento == 'R'" style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;">
          <VUESchedaFatturaPassiva :SchedaFatturaPassiva = "DocumentoSelezionato" />
        </div>
        <div v-else-if="TipoDocumento == 'M'" style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;">
          <VUESchedaMovimento :SchedaMovimento = "DocumentoSelezionato" />
        </div>
        <div v-else-if="TipoDocumento == 'N'" style="position: relative; border: grey 1px solid; margin-top:20px; padding:7px; background-color:white;">
          <VUESchedaFatturaPassiva :SchedaFatturaPassiva = "DocumentoSelezionato" />
        </div>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="ChiudiPopupDocumento" data-dismiss="modal">Annulla</button>
      <button v-if="DocumentoSelezionato.CanRecord()" type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="ConfermaPopupDocumento" data-dismiss="modal">Conferma</button>
    </template>
</VUEModal>

<div class="ZMCorpoSchedeDati">
    <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
              <img v-if="ATab.Id == 'DatiFatturazione'" src="@/assets/images/IconeAlbero/Fornitore2.png" style="width:35px;height:35px;float:left;margin-top:-7px">                        
             </a>
         </li>
       </ul>
    </header>

    <div  v-if="Tabs.ActiveTab == 'DatiFatturazione'">
        <div class="ZMNuovaRigaScheda">
            <div v-if="SchedaFornitore.Dati.CODICE == '' "  style="float:left;">
                  <label style="margin-right:15px;"><span style="font-weight:bold;">Ultimo codice fornitore inserito:</span> {{ SchedaFornitore.CodiceUltimoFornitore }}</label>
            </div> 
        </div>   
        <div style="float:left;margin-right:5px">
                <input style="float:left;margin-top:2px" type="checkbox" v-model="SchedaFornitore.Dati.IS_CLIENTE"/>
        </div>
        <div style="float:left;">
                <label style="font-weight: bold;">Anche cliente&nbsp;</label>
        </div>  
         <div style="clear:both">
            
          <div style="float:left;width:14%">
            <label style="font-weight: bold;">Cod. fornitore </label>
            <input type="text" class="form-control" v-model="SchedaFornitore.Dati.CODICE" placeholder="Codice fornitore" maxlength="10"/>
          </div> 
          <div style="float:left;width:1%;">&nbsp;</div>
          <div style="float:left;width:39%">
            <label style="font-weight: bold;">Ragione sociale </label>
            <input type="text" class="form-control" v-model="SchedaFornitore.Dati.RAGIONE_SOCIALE" placeholder="Ragione sociale"/>
            <label v-if="SchedaFornitore.Dati.RAGIONE_SOCIALE.trim() == ''" class="ZMFormLabelError">Campo obbligatorio</label>
          </div>
          <div style="float:left;width:1%">&nbsp;</div>
          <div style="float:left;width:24%">
            <label style="font-weight: bold;">Regime fiscale</label>
            <select v-model="SchedaFornitore.Dati.REGIME_FISCALE" class="form-control">
              <option v-for="SelectOption in LsRegimiFiscali" 
                            :Key="SelectOption.Id"
                            :value="SelectOption.Id"
                            :selected="SelectOption.Id == SchedaFornitore.Dati.REGIME_FISCALE">
                            {{SelectOption.Descrizione}}
              </option>       
            </select>
          </div>
          <div style="float:left;width:1%">&nbsp;</div>
          <div style="float:left;width:20%;">
            <label style="font-weight: bold;">Tipo ritenuta</label>
            <select v-model="SchedaFornitore.Dati.TIPO_RITENUTA" class="form-control">
              <option v-for="SelectOption in LsTipoRitenuta" 
                            :Key="SelectOption.CHIAVE"
                            :value="SelectOption.CHIAVE"
                            :selected="SelectOption.CHIAVE == SchedaFornitore.Dati.TIPO_RITENUTA">
                            {{SelectOption.DESCRIZIONE}}
              </option>      
            </select>
          </div>
        </div>
        <div style="clear: both;padding-top:5px; ">
          <label style="font-weight: bold;">Autofattura {{SchedaFornitore.Dati.TIPO_AUTOFATTURA}}</label>
          <select class="form-control" style="width: 14%;" v-model="SchedaFornitore.Dati.TIPO_AUTOFATTURA" :disabled="SchedaFornitore.Dati.INVIATA_ALLO_SDI">
              <option :value="-1">-</option>
              <option :value="TipoAutofattura.ExtraComunitarie">TD17 - Extra comunitarie</option>
              <option :value="TipoAutofattura.IntraComunitarie">TD18 - Intra comunitarie</option>
              <option :value="TipoAutofattura.ExArticolo17">TD19 - Acquisto Beni (Ex Articolo 17)</option>
              <option :value="TipoAutofattura.IntegrFatture">TD20 - Regol. & Integraz. Fatture</option>
              <option :value="TipoAutofattura.Splafonamento">TD21 - Splafonamento</option>
              <option :value="TipoAutofattura.SanMarino">TD28 - San Marino</option>
          </select>

          <!-- <div style="float:left;">
            <input type="radio" :value="TipoAutofattura.ExtraComunitarie" 
                                :checked="SchedaFornitore.Dati.TIPO_AUTOFATTURA == TipoAutofattura.ExtraComunitarie" 
                                  name="TipoAutofattura" 
                                  v-model="SchedaFornitore.Dati.TIPO_AUTOFATTURA"/>
          </div>   
          <div style="float:left;padding-top: 1px;">
              <label style="font-weight: bold;">&nbsp;Extra communitarie</label>
          </div> 
          <div style="float:left;margin-left:15px">
            <input type="radio" :value="TipoAutofattura.IntraComunitarie" 
                                :checked="SchedaFornitore.Dati.TIPO_AUTOFATTURA == TipoAutofattura.IntraComunitarie" 
                                  name="TipoAutofattura" 
                                  v-model="SchedaFornitore.Dati.TIPO_AUTOFATTURA"/>
          </div>   
          <div style="float:left;padding-top: 1px;">
              <label style="font-weight: bold;">&nbsp;Intra comunitarie</label>
          </div> 
          <div style="float:left;margin-left:15px">
            <input type="radio" :value="TipoAutofattura.SanMarino" 
                                :checked="SchedaFornitore.Dati.TIPO_AUTOFATTURA == TipoAutofattura.SanMarino" 
                                  name="TipoAutofattura" 
                                  v-model="SchedaFornitore.Dati.TIPO_AUTOFATTURA"/>
          </div>   
          <div style="float:left;padding-top: 1px;">
              <label style="font-weight: bold;">&nbsp;San Marino</label>
          </div>  -->

            <div class="btn-group open" style="float:right;margin-right:5px">
              <img src="@/assets/images/Stampa.png" style="margin-top:-8px;cursor:pointer" @click="OnClickStampa(false,false,false,true)">
              <ul v-if="MenuStampa.length != 0" class="dropdown-menu" style="left: -105px">
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
        <div class="ZMSeparatoreScheda" style="margin-top:10px">Indirizzo di Fatturazione</div>
        <div style="clear:both">
            <div style="float:left;width:30%">
                <label style="font-weight: bold;">Indirizzo</label>
                <input type="text" class="form-control" v-model="SchedaFornitore.Dati.INDIRIZZO_FATTURAZIONE" placeholder="Indirizzo"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:13%">
                <label style="font-weight: bold;">Civico</label>
                <input type="text" class="form-control" maxlength="7" v-model="SchedaFornitore.Dati.NR_CIVICO_FATTURAZIONE" placeholder="Nr. civico"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:9%">
                <label style="font-weight: bold;">CAP</label>
                <VUEInputCAP v-model="SchedaFornitore.Dati.CAP_FATTURAZIONE"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:24%">
                <label style="font-weight: bold;">Comune</label>
                <input type="text" class="form-control" v-model="SchedaFornitore.Dati.COMUNE_FATTURAZIONE" placeholder="Comune"/>
            </div>
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:20%">
                <label style="font-weight: bold;">Provincia</label>
                <VUEInputProvince v-model="SchedaFornitore.Dati.PROVINCIA_FATTURAZIONE" emptyElement="true"/>
            </div> 
            <div style="clear:both; height:8px">&nbsp;</div>
        </div>

        <div class="ZMSeparatoreScheda">Recapito di spedizione</div>
        <div style="clear:both">
            <div style="float:left;width:54%">
                <label style="font-weight: bold;">Destinazione</label>
                <input type="text" class="form-control" v-model="SchedaFornitore.Dati.PRESSO" placeholder="Destinazione"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:35%">
                <label style="font-weight: bold;">Indirizzo</label>
                <input type="text" class="form-control" v-model="SchedaFornitore.Dati.INDIRIZZO_SPEDIZIONE" placeholder="Indirizzo"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:9%">
                <label style="font-weight: bold;">Civico</label>
                <input type="text" class="form-control" maxlength="7" v-model="SchedaFornitore.Dati.NR_CIVICO_SPEDIZIONE" placeholder="Nr. civico"/>
            </div> 
        </div>
        <div style="clear:both">
            <div style="float:left;width:10%">
                <label style="font-weight: bold;">CAP</label>
                <VUEInputCAP v-model="SchedaFornitore.Dati.CAP_SPEDIZIONE"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:19%">
                <label style="font-weight: bold;">Comune</label>
                <input type="text" class="form-control" v-model="SchedaFornitore.Dati.COMUNE_SPEDIZIONE" placeholder="Comune"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:23%">
                <label style="font-weight: bold;">Provincia</label>
                <VUEInputProvince v-model="SchedaFornitore.Dati.PROVINCIA_SPEDIZIONE" emptyElement="true"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:35%">
                <label style="font-weight: bold;">Nazione</label>
                <VUEInputNazioni v-model="SchedaFornitore.Dati.NAZIONE_SPEDIZIONE" emptyElement="true"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:9%">
              <label style="font-weight: bold;">&nbsp;</label>
              <button class="btn btn-sm btn-info" style="width:100%" @click="OnClickCopiaDaDatiFatturazione">Copia dati fatt.</button>
            </div> 
            <div style="clear:both; height:8px">&nbsp;</div>
        </div>

        <div class="ZMSeparatoreScheda">Dati Fiscali</div>
        <div class="ZMNuovaRigaScheda">
            <div style="float:left;width:25%">
                <label style="font-weight: bold;">Partita IVA</label>
                <VUEInputPartitaIVA v-model="SchedaFornitore.Dati.PARTITA_IVA" />
                <label v-if="SchedaFornitore.Dati.PARTITA_IVA.trim() == '' && SchedaFornitore.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
                      class="ZMFormLabelError">Campo obbligatorio</label>
                <label v-if="ErrorePartitaIVA != '' && !DeveloperMode && SchedaFornitore.Dati.EMITTENTE_PIVA == StatoItaliano" class="ZMFormLabelError">{{ ErrorePartitaIVA }}</label>
            </div> 

            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:24%">
                <label style="font-weight: bold;">Codice fiscale</label>
                <VUEInputCodiceFiscale v-model="SchedaFornitore.Dati.CODICE_FISCALE"/>
                <label v-if="SchedaFornitore.Dati.PARTITA_IVA.trim() == '' && SchedaFornitore.Dati.CODICE_FISCALE.trim() == '' && !DeveloperMode" 
                      class="ZMFormLabelError">Campo obbligatorio</label>
                <label v-if="(ErroreCodiceFiscale != '' && !DeveloperMode && SchedaFornitore.Dati.EMITTENTE_PIVA == StatoItaliano)" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
            </div> 

            <div style="float:left;width:1%">&nbsp;</div>
            <div style="float:left;width:24%">
                <label style="font-weight: bold;">Nazione</label>
                <VUEInputNazioni v-model="SchedaFornitore.Dati.EMITTENTE_PIVA" emptyElement="true"/>
                <label v-if="SchedaFornitore.Dati.EMITTENTE_PIVA == -1" class="ZMFormLabelError">Campo obbligatorio</label>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:12%">
                <label style="font-weight: bold;">Ritenuta</label>
                <input type="number" min="0" step="0.1" class="form-control" v-model="SchedaFornitore.Dati.RITENUTA" placeholder="Ritenuta"/>
            </div> 
            <div style="float:left;width:1%;">&nbsp;</div>
            <div style="float:left;width:11%">
                <label style="font-weight: bold;">IVA</label>
                <input type="number" min="0" step="0.1" class="form-control" v-model="SchedaFornitore.Dati.IVA_FORNITORE" placeholder="IVA"/>
            </div> 
            <div style="clear:both; height:8px">&nbsp;</div>
        </div>

        <div class="ZMSeparatoreScheda">Info contabili</div>
        <div class="ZMNuovaRigaScheda">
            <VUEInputContoRibaCorrente :ContoCorrente="SchedaFornitore.Dati.ContoCorrente" :Fornitore="true"/>
        </div>
        
        <div class="ZMSeparatoreScheda">Recapiti</div>
        <VUERecapiti :SchedaRecapiti="SchedaFornitore.SchedaRecapiti" @onChange="OnRecapitiChange"></VUERecapiti>

    </div>
    <div  v-if="Tabs.ActiveTab == 'Prodotti'">
      <VUEDataTable :DataTable="SchedaFornitore.DataTableCodiciFornitore"
                  :NomeProgramma="'Gemini'" 
                  :PathLogo="require('../../assets/images/LogoGemini2.png')"/>
    </div>

    <div v-if="Tabs.ActiveTab == 'SituazioneContabile'">
        <div class="panel panel-default">
            <div class="table-responsive" style="background-color: #d0e9ff;">
                <div style="display: flex; align-items: center; width: 50%; background-color: #d0e9ff; margin-top: 1%;">
                  <div style="display: flex; align-items: center; width: 30%;">
                    <label style="font-weight: bold;">Dal</label>
                    <input type="date" class="form-control" v-model="SchedaFornitore.SchedaStatoContabile.DallaData">
                  </div>
                  <div style="width: 5%">&nbsp;</div>
                  <div style="display: flex; align-items: center; width: 30%;">
                    <label style="font-weight: bold;">Al</label>
                    <input type="date" class="form-control" v-model="SchedaFornitore.SchedaStatoContabile.AllaData">
                  </div>
                  <div style="width: 5%">&nbsp;</div>
                  <div>
                    <button class="btn btn-primary" @click="OnClickCerca">Cerca</button>
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
                          <tr v-for="Documento in SchedaFornitore.SchedaStatoContabile.ListaDocumenti" :key="Documento.CHIAVE">
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
                          <template v-if="SchedaFornitore.SchedaStatoContabile.TotaliDocumenti.length > 0">
                            <td></td>
                            <tr v-if="SchedaFornitore.SchedaStatoContabile.SaldoPeriodo">
                            <td colspan="3" style="text-align: right; font-weight: bold;">
                              Saldo periodo:
                            </td>
                            <td style="font-weight: bold;">
                              {{ SchedaFornitore.SchedaStatoContabile.SaldoPeriodo.LB_DARE }}
                            </td>
                            <td style="font-weight: bold;">
                              {{ SchedaFornitore.SchedaStatoContabile.SaldoPeriodo.LB_AVERE }}
                            </td>
                            <td style="font-weight: bold;">
                              {{ SchedaFornitore.SchedaStatoContabile.SaldoPeriodo.LB_SALDO }}
                            </td>
                            <td></td>
                            </tr>
                            <tr v-for="(Totali, index) in SchedaFornitore.SchedaStatoContabile.TotaliDocumenti" :key="index"> 
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
</div>
</template>


<script>
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEDataTable from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEDataTable2.vue';
import { TZDataTable,TZDTableColumnType } from '../../../../../../../../Librerie/VUE/ZDataTable2.js'
import { SystemInformation, DASHBOARD_FILTER_TYPES, TIPO_AUTOFATTURA, RUOLI,NOME_PROGRAMMA } from '@/SystemInformation.js'
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import VUEInputCAP from '@/components/InputComponents/VUEInputCAP.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputPartitaIVA from '../../components/InputComponents/VUEInputPartitaIVA.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputContoRibaCorrente from '@/components/InputComponents/VUEInputContoRibaCorrente.vue';
import VUERecapiti, {TSchedaRecapiti} from '@/views/SchedeDatabase/ComponentMultiScheda/VUERecapiti.vue';
import { TSchedaIndiceFatturePassive, 
         TSchedaIndiceNoteDiCreditoPassive, 
         TSchedaIndiceDDTFornitore,
         TSchedaIndiceMovFornitore,
         TSchedaIndiceDDTEntranteFornitore,
         TSchedaIndiceAutoFatture          } from '@/NodiVuoti'
import { TZFatturaElettronica, TZFattElettronicaRegimeFiscale, TZFatturaTipoRitenuta } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import VUESchedaFatturaPassiva, {TSchedaFatturaPassiva} from './VUESchedaFatturaPassiva.vue';
import VUESchedaMovimento, {TSchedaMovimento} from './VUESchedaMovimento.vue';
// import VUESchedaNotaDiCredito, {TSchedaNotaDiCredito} from './VUESchedaNotaDiCredito.vue';


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


export class TSchedaFornitore extends TSchedaGenerica
{
   OnInitialization()
   {
      this.SchedaRecapiti  = new TSchedaRecapiti()
      this.SchedaRecapiti.ClearSchedaRecapiti()

      this.SchedaStatoContabile = new TSchedaSituzioneContabile()
      this.ListaDocumenti = []
      this.TotaliDocumenti = []

      //table codici prodotti
      this.DataTableCodiciFornitore = new TZDataTable('CHIAVE');
      this.DataTableCodiciFornitore.ClearColumns();
      // var Colonna = this.DataTableCodiciFornitore.AddColumn('Codice articolo',
      //                                          TZDTableColumnType.typString,
      //                                          'CODICE_PRODOTTO',
      //                                          '50%')
      var Colonna = this.DataTableCodiciFornitore.AddColumn('Prodotto',
                                      TZDTableColumnType.typSelect,
                                      'ID_PRODOTTO',
                                      '60%');
      Colonna.Necessario       = true
      Colonna.Lista            = SystemInformation.Configurazioni.Prodotti
   }

   Clear()
   {      
      this.DataTableCodiciFornitore.AssignDati([])
      this.SchedaRecapiti.ClearSchedaRecapiti()
      this.Dati = { 
                    RAGIONE_SOCIALE                : '',
                    REGIME_FISCALE                 : TZFattElettronicaRegimeFiscale.ID_FATT_REGIME_FISCALE_RF01,
                    TIPO_RITENUTA                  : TZFatturaTipoRitenuta.ritPersonaGiuridica,
                    CODICE               : '',
                    CODICE_FISCALE                 : '',
                    PARTITA_IVA                    : '',
                    INDIRIZZO_FATTURAZIONE         : '',
                    COMUNE_FATTURAZIONE            : '',
                    PROVINCIA_FATTURAZIONE         : -1,
                    EMITTENTE_PIVA                 : SystemInformation.Configurazioni.StatoItaliano,
                    RITENUTA                       : 0,
                    IVA_FORNITORE                  : 0,
                    CAP_FATTURAZIONE               : '',
                    NR_CIVICO_FATTURAZIONE         : '',
                    PRESSO                         : '',
                    INDIRIZZO_SPEDIZIONE           : '',
                    NR_CIVICO_SPEDIZIONE           : '',
                    PROVINCIA_SPEDIZIONE           : -1,
                    NAZIONE_SPEDIZIONE             : SystemInformation.Configurazioni.StatoItaliano,
                    COMUNE_SPEDIZIONE              : '',
                    CAP_SPEDIZIONE                 : '',
                    TIPO_AUTOFATTURA               : TIPO_AUTOFATTURA.ExtraComunitarie,
                    IS_CLIENTE               : false,
                    ContoCorrente                  : { 
                                                        IBAN              : '',
                                                        BANCA             : '',
                                                        ID_CONTO_CORRENTE : -1,
                                                        NR_CONTO          : '',
                                                        CONTO_RIBA        : false,
                                                        BIC               : '',
                                                        SWIFT             : ''
                                                      },  
                    PEC                            : '',
                    ModificaTabelle                : false,
                    ModificaTabellaCodiciFornitore : false   
      }
      super.Clear();
   }

   Nuovo()
   {
     var Self = this;  
     super.Nuovo() 
     this.Clear()
     SystemInformation.CaricaUltimoCodiceFornitore(function(CodiceUltimoFornitore)
                       {
                         Self.CodiceUltimoFornitore = CodiceUltimoFornitore
                       });
   }

   Disponi(OnSuccess)
    {
      var Self = this;
      if(this.InEliminazione) return;
      SystemInformation.CaricaUltimoCodiceFornitore(function(CodiceUltimoFornitore)
                       {
                         Self.CodiceUltimoFornitore = CodiceUltimoFornitore
                       });

      this.AdvQuery.ExecuteExternalScript('SelectDatiFornitore',{ CHIAVE : Self.Chiave, 
                                                                  ChiaveFornitore : Self.Chiave,
                                                                  DataDal       : Self.SchedaStatoContabile.DallaData,
                                                                  DataAl        : Self.SchedaStatoContabile.AllaData,
                                                                  StatoConti    : true 
                                                                },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo          = Results.Dettaglio
                                            let ArrayInfoTelefono  = Results.DatiForitoreTelefono
                                            let ArrayCodiciFornitore = Results.DatiFornitoreCodici

                                            if(Results.DatiSituazioneContabileFornitore)
                                            {
                                              let ArraySituazioneContabile = Results.DatiSituazioneContabileFornitore;

                                              Self.SchedaStatoContabile.AssignDati(ArraySituazioneContabile);
                                            } 

                                            if(ArrayInfo != undefined && ArrayInfoTelefono != undefined && ArrayCodiciFornitore != undefined)
                                            {
                                              Self.SchedaRecapiti.AssignDati(ArrayInfoTelefono)
                                              Self.DataTableCodiciFornitore.AssignDati(ArrayCodiciFornitore)

                                              if(ArrayInfo.length != 0 )
                                              {
                                                Self.Dati = { 
                                                              RAGIONE_SOCIALE                : TSchedaGenerica.DisponiFromString(ArrayInfo[0].RAGIONE_SOCIALE),
                                                              REGIME_FISCALE                 : TSchedaGenerica.DisponiFromString(ArrayInfo[0].REGIME_FISCALE),
                                                              TIPO_RITENUTA                  : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].TIPO_RITENUTA),
                                                              CODICE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE),
                                                              CODICE_FISCALE                 : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE),
                                                              PARTITA_IVA                    : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA),
                                                              INDIRIZZO_FATTURAZIONE         : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_FATTURAZIONE),
                                                              COMUNE_FATTURAZIONE            : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_FATTURAZIONE),
                                                              PROVINCIA_FATTURAZIONE         : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_FATTURAZIONE),
                                                              EMITTENTE_PIVA                 : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].EMITTENTE_PIVA),
                                                              RITENUTA                       : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].RITENUTA) / 10,
                                                              IVA_FORNITORE                  : TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IVA_FORNITORE) / 10,
                                                              CAP_FATTURAZIONE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_FATTURAZIONE),
                                                              NR_CIVICO_FATTURAZIONE         : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_FATTURAZIONE),
                                                              PRESSO                         : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PRESSO),
                                                              INDIRIZZO_SPEDIZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_SPEDIZIONE),
                                                              NR_CIVICO_SPEDIZIONE           : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_SPEDIZIONE),
                                                              PROVINCIA_SPEDIZIONE           : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SPEDIZIONE),
                                                              NAZIONE_SPEDIZIONE             : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SPEDIZIONE),
                                                              COMUNE_SPEDIZIONE              : TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_SPEDIZIONE),
                                                              CAP_SPEDIZIONE                 : TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_SPEDIZIONE),
                                                              IS_CLIENTE               : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].IS_CLIENTE),
                                                              ContoCorrente                  : {
                                                                                                  IBAN              : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].IBAN),
                                                                                                  ID_CONTO_CORRENTE : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CONTO_CORRENTE),
                                                                                                  BANCA             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BANCA),
                                                                                                  CONTO_RIBA        : ArrayInfo[0].ID_CONTO_CORRENTE != null? false : true,
                                                                                                  NR_CONTO          : TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CONTO),
                                                                                                  SWIFT             : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SWIFT),
                                                                                                  BIC               : ArrayInfo[0].ID_CONTO_CORRENTE != null? TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC_CONTO) : TSchedaGenerica.DisponiFromString(ArrayInfo[0].BIC),
                                                                                               },
                                                              PEC                            : TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC),
                                                              TIPO_AUTOFATTURA               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].TIPO_AUTOFATTURA) != ''? TSchedaGenerica.DisponiFromString(ArrayInfo[0].TIPO_AUTOFATTURA) : -1, 
                                                              ModificaTabelle                : false,
                                                              ModificaTabellaCodiciFornitore : false
                                                            }
                                                            Self.CreateSnapshot();

                                              }
                                              else SystemInformation.HandleError('Fornitore eliminato')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio del fornitore');

                                            if(OnSuccess != undefined)
                                              OnSuccess()
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        })
    }

  CanRecord()
  {
    return this.Dati.RAGIONE_SOCIALE.trim() != '' && 
          this.Dati.EMITTENTE_PIVA != -1 &&
          (this.Dati.CODICE_FISCALE == '' || 
            TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk || 
            SystemInformation.DeveloperMode || 
            this.Dati.EMITTENTE_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
          (this.Dati.PARTITA_IVA == '' || 
            TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || 
            SystemInformation.DeveloperMode || 
            this.Dati.EMITTENTE_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
            this.DataTableCodiciFornitore.AllDataOk()


          // (this.Dati.CODICE_FISCALE == '' || 
          //   TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CODICE_FISCALE) == TZCheckDatiFiscali.datiFiscaliOk ||
          //   SystemInformation.DeveloperMode || 
          // this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&
          // (this.Dati.PARTITA_IVA == '' || 
          //  TZEconomicFunct.VerificaPartitaIVA(this.Dati.PARTITA_IVA) == TZCheckDatiFiscali.datiFiscaliOk || 
          //  SystemInformation.DeveloperMode || 
          // this.Dati.NAZIONE_EM_PIVA != SystemInformation.Configurazioni.StatoItaliano) &&           
  }

  Registra(OnSuccess,OnError)
  {
    if(this.CanRecord())
    {
      var Self = this
      var ObjQuery = { Operazioni : [] };
      ObjQuery.Operazioni.push({
                                  Query     : this.IsNuovo() ? "InserisciFornitore" : "ModificaFornitore",
                                  Parametri : {
                                                CHIAVE_FORNITORE          : this.Chiave, 
                                                RAGIONE_SOCIALE           : TSchedaGenerica.PrepareForRecordString(this.Dati.RAGIONE_SOCIALE),
                                                REGIME_FISCALE            : TSchedaGenerica.PrepareForRecordString(this.Dati.REGIME_FISCALE),
                                                TIPO_RITENUTA             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.TIPO_RITENUTA),
                                                CODICE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CODICE),
                                                PARTITA_IVA               : TSchedaGenerica.PrepareForRecordString(this.Dati.PARTITA_IVA),
                                                EMITTENTE_PIVA            : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.EMITTENTE_PIVA),
                                                RITENUTA                  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.RITENUTA * 10),
                                                IVA_FORNITORE             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.IVA_FORNITORE * 10),
                                                CODICE_FISCALE            : TSchedaGenerica.PrepareForRecordString(this.Dati.CODICE_FISCALE),
                                                INDIRIZZO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_FATTURAZIONE),
                                                NR_CIVICO_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_FATTURAZIONE),
                                                PROVINCIA_FATTURAZIONE    : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_FATTURAZIONE),
                                                COMUNE_FATTURAZIONE       : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_FATTURAZIONE),
                                                CAP_FATTURAZIONE          : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_FATTURAZIONE),
                                                PRESSO                    : TSchedaGenerica.PrepareForRecordString(this.Dati.PRESSO),
                                                INDIRIZZO_SPEDIZIONE      : TSchedaGenerica.PrepareForRecordString(this.Dati.INDIRIZZO_SPEDIZIONE),
                                                NR_CIVICO_SPEDIZIONE      : TSchedaGenerica.PrepareForRecordString(this.Dati.NR_CIVICO_SPEDIZIONE),
                                                PROVINCIA_SPEDIZIONE      : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.PROVINCIA_SPEDIZIONE),
                                                NAZIONE_SPEDIZIONE        : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.NAZIONE_SPEDIZIONE),
                                                COMUNE_SPEDIZIONE         : TSchedaGenerica.PrepareForRecordString(this.Dati.COMUNE_SPEDIZIONE),
                                                CAP_SPEDIZIONE            : TSchedaGenerica.PrepareForRecordString(this.Dati.CAP_SPEDIZIONE),
                                                IBAN                      : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.IBAN) : null,
                                                BANCA                     : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.BANCA) : null,
                                                BIC                       : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.BIC)   : null,
                                                SWIFT                     : this.Dati.ContoCorrente.ID_CONTO_CORRENTE == -1? TSchedaGenerica.PrepareForRecordString(this.Dati.ContoCorrente.SWIFT) : null,
                                                ID_CONTO_CORRENTE         : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ContoCorrente.ID_CONTO_CORRENTE),
                                                PEC                       : TSchedaGenerica.PrepareForRecordString(this.Dati.PEC),
                                                TIPO_AUTOFATTURA          : TSchedaGenerica.PrepareForRecordString(this.Dati.TIPO_AUTOFATTURA) != ''? TSchedaGenerica.PrepareForRecordString(this.Dati.TIPO_AUTOFATTURA) : null,
                                                IS_FORNITORE              : true,
                                                IS_CLIENTE                : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.IS_CLIENTE),
                                                ModificaTabellaCodiciFornitore : false
                                              }
                                });
        
        Self.SchedaRecapiti.PrepareQueryParametersTelefono(ObjQuery.Operazioni, Self.Chiave, 'ID_FORNITORE')

        if(Self.IsNuovo)
        {
          ObjQuery.Operazioni.push({
                                          Query     : "InsertUltimoCodiceFornitore",
                                          Parametri : { ULTIMO_CODICE_FORNITORE : Self.Dati.CODICE},
                                        })
        }

        this.DataTableCodiciFornitore.Righe.forEach(function(Riga)
        {
          let Parametri = Self.DataTableCodiciFornitore.PrepareQueryParameters(Riga)
          Parametri.ID_FORNITORE = Self.Chiave;

          
          if(Riga.Nuovo)
          {
            if(!Self.IsNuovo())
            ObjQuery.Operazioni.push({
                                        Query     : "InsertCodiciFornitore",
                                        Parametri : Parametri,
                                        ResetKeys : [2]
                                      })
          }
          else
          {
            if(Riga.Eliminato)
                ObjQuery.Operazioni.push({
                                          Query     : "DeleteCodiciFornitore",
                                          Parametri : { CHIAVE : Riga.Dati['CHIAVE'] }
                                        })
            else 
            {
              if(Riga.Modificato())
              ObjQuery.Operazioni.push({
                                        Query     : "UpdateCodiciFornitore",
                                        Parametri : Parametri
                                      }) 
            }
          }
        });

        this.AdvQuery.PostSQL('Fornitori',ObjQuery,function(Response)
        {
        SystemInformation.GetConfigurazioni(function()
        {
          ObjQuery = {};
          if(Self.Chiave == -1)
              Self.Chiave = Response.NewKey1;
          Self.Dati.ModificaTabelle = false;
          Self.CreateSnapshot();
          OnSuccess();
        })
        },
        function(HTTPError,SubHTTPError,Response)
        {
          OnError(HTTPError,SubHTTPError,Response);
        });

    }
  }

  GetDescrizione()
  {
    return this.Dati.RAGIONE_SOCIALE;
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
      return false;

    return this.Eliminabile;
  }

  Elimina(OnSuccess,OnError)
  {
    this.InEliminazione = true;
    var ObjQuery = { Operazioni : [ 
                                    {
                                      Query     : "EliminaTelefonoTramiteFornitore",
                                      Parametri : { CHIAVE_FORNITORE : this.Chiave }
                                    },
                                    {
                                      Query     : "DeleteSaldiAnnualiTramiteFornitore",
                                      Parametri : { CHIAVE_FORNITORE : this.Chiave }
                                    },
                                    {
                                      Query     : "EliminaFornitore",
                                      Parametri : { CHIAVE_FORNITORE : this.Chiave }
                                    }
                                  ]};
    this.DataTableCodiciFornitore.Righe.forEach(function(Riga)
    {
      ObjQuery.Operazioni.push({
                                Query     : "EliminaCodiciFornitore", 
                                Parametri : { CHIAVE : Riga.Dati['CHIAVE']}
                              })
    });

    this.AdvQuery.PostSQL('Fornitori',ObjQuery,function()
    {
      OnSuccess();
    },
    function(HTTPError,SubHTTPError,Response)
    {
      OnError(HTTPError,SubHTTPError,Response);
    });
  }

  GetClassName()
  {
    return 'TSchedaFornitore';
  }

  GetImageIndex()
  {
    return 'Fornitore.png'
  }


  CaricaRiassunto(Riassunto)
  {
    this.Chiave                         = Riassunto.CHIAVE;
    this.Eliminabile                    = Riassunto.FORNITORE_CANCELLABILE == 1? false : true
    this.Dati.RAGIONE_SOCIALE           = TSchedaGenerica.DisponiFromString(Riassunto.RAGIONE_SOCIALE);
    this.CreateSnapshot();
  }

  BeforeExpand(AnItem,OnSuccess)
  {
    if(this.Chiave != undefined)
    {
      var SchedaIndiceFatturePassive = new TSchedaIndiceFatturePassive('',this.Chiave);
      var NodeFatturePassive = AnItem.AddChild(SchedaIndiceFatturePassive.GetDescrizione(),SchedaIndiceFatturePassive)
      NodeFatturePassive.HasChildren = true

      var SchedaIndiceNoteDiCredito = new TSchedaIndiceNoteDiCreditoPassive('',this.Chiave);
      var NodeNoteDiCredito = AnItem.AddChild(SchedaIndiceNoteDiCredito.GetDescrizione(),SchedaIndiceNoteDiCredito)
      NodeNoteDiCredito.HasChildren = true;

      var SchedaIndiceMovimenti = new TSchedaIndiceMovFornitore(null,this.Chiave);
      var NodoMovimenti = AnItem.AddChild(SchedaIndiceMovimenti.GetDescrizione(),SchedaIndiceMovimenti)
      NodoMovimenti.HasChildren = true;

      var SchedaIndiceDDT = new TSchedaIndiceDDTFornitore('',this.Chiave);
      var NodeDDT = AnItem.AddChild(SchedaIndiceDDT.GetDescrizione(),SchedaIndiceDDT)
      NodeDDT.HasChildren = true;

      var SchedaIndiceDDTEntrante = new TSchedaIndiceDDTEntranteFornitore('',this.Chiave);
      var NodeDDTEntrante = AnItem.AddChild(SchedaIndiceDDTEntrante.GetDescrizione(),SchedaIndiceDDTEntrante)
      NodeDDTEntrante.HasChildren = true;

      var SchedaIndiceAutoFatture = new TSchedaIndiceAutoFatture('',this.Chiave);
      var NodeAutoFatture = AnItem.AddChild(SchedaIndiceAutoFatture.GetDescrizione(),SchedaIndiceAutoFatture)
      NodeAutoFatture.HasChildren = true;
      
      OnSuccess()
    }

  }

  GetFiltriAbilitati(OnSuccess)
  {
    OnSuccess([{
                    Name : DASHBOARD_FILTER_TYPES.Fornitore,
                    Positions : [
                                    this.Dati.RAGIONE_SOCIALE.substring(0,1).toUpperCase(),
                                    this.Chiave
                                ]
            }])
  }
}
export default
{
  components:
  {
        VUEInputCAP,
        VUEInputProvince,
        VUEInputNazioni,
        VUEInputPartitaIVA,
        VUEInputCodiceFiscale,
        VUEInputContoRibaCorrente,
        VUERecapiti,
        VUEModal,
        VUEDataTable,
        VUESchedaFatturaPassiva,
        VUESchedaMovimento,
        // VUESchedaNotaDiCredito
  },

  data()
  {
    return{ 
            PopupDataContoFornitore : false,
            MenuStampa              : [],
            Tabs                    : null,
            DeveloperMode           : SystemInformation.DeveloperMode,
            TipoAutofattura         : TIPO_AUTOFATTURA,            
            LsRegimiFiscali         : TZFatturaElettronica.GetLsRegimiFiscali(),
            LsTipoRitenuta          : TZFatturaElettronica.GetLsTipoRitenuta(),
            StatoItaliano           : SystemInformation.Configurazioni.StatoItaliano,
            DocumentoSelezionato     : new TSchedaGenerica(SystemInformation.AdvQuery),
            PopupDocumento           : false,
            TipoDocumento            : '',
            TestoPopupDocumento      : '',
            NomeProgramma            : NOME_PROGRAMMA
          };
  },
  props : ['SchedaFornitore'],
  
  watch: {
    SchedaFornitore :
    { 
        handler(NewValue,OldValue)
        {
          if(NewValue != OldValue && NewValue != undefined)
          {
              this.SchedaFornitore.DataTableCodiciFornitore.AssignOnRowChange(() =>
              {
                this.SchedaFornitore.Dati.ModificaTabellaCodiciFornitore = true
              })

              this.SchedaFornitore.DataTableCodiciFornitore.AssignOnRowDelete(() =>
              {
                this.SchedaFornitore.Dati.ModificaTabellaCodiciFornitore = true
              })
          } 
        },
        immediate : true
    }
  },

  computed :
  {    
    ErrorePartitaIVA :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaPartitaIVA(this.SchedaFornitore.Dati.PARTITA_IVA);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,false);
          return ''
      }
    },
    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.SchedaFornitore.Dati.CODICE_FISCALE);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
      }
    },

  },

  methods :
  {
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
        var Parametri = {ChiaveCliente : this.SchedaFornitore.Chiave}
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
            Caption: "Conto fornitore",
            OnClick: function () 
            {
              if(!Self.PopupDataContoFornitore)
              {
                let date = new Date() 
                let y = date.getFullYear()
                let m = date.getMonth()

                if(SystemInformation.UltimaDataDalContoFornitoreInserita != '')
                  Self.ContoFornitoreDal = SystemInformation.UltimaDataDalContoFornitoreInserita
                else
                {
                  if(Self.ContoFornitoreDal == null || Self.ContoFornitoreDal == '')
                    Self.ContoFornitoreDal = TZDateFunct.DateInHTMLInputFormat(new Date(y, m, 1));
                }
                if(SystemInformation.UltimaDataAlContoFornitoreInserita != '')
                  Self.ContoFornitoreAl  = SystemInformation.UltimaDataAlContoFornitoreInserita
                else
                {
                  if(Self.ContoFornitoreAl == null || Self.ContoFornitoreAl == '')
                    Self.ContoFornitoreAl = TZDateFunct.DateInHTMLInputFormat(new Date(y, m + 1, 0));
                }

                Self.PopupDataContoFornitore = true
              }
              else Self.PopupDataContoFornitore = false 
            }
          }
        ]
      }
      else this.MenuStampa = [];
    },

    ConfermaPopupDataContoFornitore()
    {
      var Self = this
      var Parametri = {
                        ChiaveCliente   : this.SchedaFornitore.Chiave,
                        DataDal         : this.ContoFornitoreDal,
                        DataAl          : this.ContoFornitoreAl
                      }

      SystemInformation.UltimaDataDalContoFornitoreInserita = this.ContoFornitoreDal
      SystemInformation.UltimaDataAlContoFornitoreInserita  = this.ContoFornitoreAl

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

      this.ContoFornitoreDal  = ''
      this.ContoFornitoreAl   = ''
      this.PopupDataContoFornitore = false
    },

    OnRecapitiChange()
    {
      this.SchedaFornitore.Dati.ModificaTabelle = true
    },

    OnClickCopiaDaDatiFatturazione()
    {
      this.SchedaFornitore.Dati.COMUNE_SPEDIZIONE    = this.SchedaFornitore.Dati.COMUNE_FATTURAZIONE
      this.SchedaFornitore.Dati.NR_CIVICO_SPEDIZIONE = this.SchedaFornitore.Dati.NR_CIVICO_FATTURAZIONE
      this.SchedaFornitore.Dati.NAZIONE_SPEDIZIONE   = this.SchedaFornitore.Dati.NAZIONE_EM_PIVA
      this.SchedaFornitore.Dati.INDIRIZZO_SPEDIZIONE = this.SchedaFornitore.Dati.INDIRIZZO_FATTURAZIONE
      this.SchedaFornitore.Dati.PROVINCIA_SPEDIZIONE = this.SchedaFornitore.Dati.PROVINCIA_FATTURAZIONE
      this.SchedaFornitore.Dati.CAP_SPEDIZIONE       = this.SchedaFornitore.Dati.CAP_FATTURAZIONE
    },
    
    OnClickCerca()
    {
      let Self = this
      if(Self.SchedaFornitore.SchedaStatoContabile.DallaData && Self.SchedaFornitore.SchedaStatoContabile.AllaData)
      {
        Self.AggiornaListaDocumenti(
                                      Self.SchedaFornitore.SchedaStatoContabile.DallaData,
                                      Self.SchedaFornitore.SchedaStatoContabile.AllaData
                                    )
      }
    },

    AggiornaListaDocumenti(Da, A)
    {
      let Self = this

      if (Da && A)
      {
        Self.SchedaFornitore.Disponi()
      }
    },

    VisualizzaDocumento(Documento)
    {
      var Self = this

      if(Documento.Tipologia == 'P')
      {
        Self.TipoDocumento                = Documento.Tipologia
        Self.TestoPopupDocumento          = Documento.LB_DESCRIZIONE_DOCUMENTO

        Self.PopupDocumento               = true
        Self.DocumentoSelezionato         = new TSchedaFatturaPassiva(SystemInformation.AdvQuery)
        Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
        Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
      }
      else if(Documento.Tipologia == 'R')
      {
        Self.TipoDocumento                = Documento.Tipologia
        Self.TestoPopupDocumento          = Documento.LB_DESCRIZIONE_DOCUMENTO
        Self.PopupDocumento               = true
        Self.DocumentoSelezionato         = new TSchedaFatturaPassiva(SystemInformation.AdvQuery)
        Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
        Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
      }
      else if(Documento.Tipologia == 'M')
      {
        Self.TipoDocumento                = Documento.Tipologia
        Self.TestoPopupDocumento          = 'Movimento di Conto'
        Self.PopupDocumento               = true
        Self.DocumentoSelezionato         = new TSchedaMovimento(SystemInformation.AdvQuery)
        Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
        Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
      }
      else if(Documento.Tipologia == 'N')
      {
        Self.TipoDocumento                = Documento.Tipologia
        Self.TestoPopupDocumento          = Documento.LB_DESCRIZIONE_DOCUMENTO

        Self.PopupDocumento               = true
        Self.DocumentoSelezionato         = new TSchedaFatturaPassiva(SystemInformation.AdvQuery)
        Self.DocumentoSelezionato.Chiave  = Documento.ChiaveDocumento
        Self.DocumentoSelezionato.Disponi(function()
                                            {
                                              Self.PopupDocumento = true
                                            })
      }
    },

    ChiudiPopupDocumento()
    {
      var Self = this
      Self.PopupDocumento = false
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
      Result =  {
                    ActiveTab : 'DatiFatturazione',
                    Tabs      : [
                                  {
                                    Caption : 'Dati fatturazione',
                                    Id      : 'DatiFatturazione'
                                  },
                                ]
                }

      
        Result.Tabs.push({
                            Caption : 'Prodotti',
                            Id      : 'Prodotti'
                        })

      Result.Tabs.push({
                          Caption : 'Situazione contabile',
                          Id      : 'SituazioneContabile' 
                      })
      
      return Result
    }
  },

  beforeMount() 
  {
    this.Tabs = this.GetTabs()
  },
}
</script>