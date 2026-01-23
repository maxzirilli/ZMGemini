<template>
<VUEConfirm :Popup="PopupFatturaElettronica" :Richiesta="'Annullare le modifiche effettuate?'" @onClickConfermaPopup="ConfermaElimina" @onClickChiudiPopup="AnnullaElimina">
</VUEConfirm>
<div v-if="ModificheDaApplicare" style="text-align:right;padding-top:2px;padding-bottom:2px">
    <a v-if="CanRecord()" class="btn btn-s-md btn-success" style="margin-right:20px" @click="Registra()">Conferma</a>
    <a class="btn btn-s-md btn-danger" @click="PopupFatturaElettronica = true">Annulla</a>
</div>
 <div class="ZMCorpoSchedeDati">
  <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}</a>
         </li>
       </ul>
   </header>
   <div style="height:5px;"></div>
   <div  v-if="Tabs.ActiveTab == 'DatiTrasmissione'" style="height:300px">
    <div style="clear:both;height:50px"></div>
    <div style="clear:both">
      <div style="float:left;width:50%">
        <label style="font-weight: bold;">Partita IVA del trasmittente</label>
        <input type="text" class="form-control" v-model="Dati.PartitaIvaTrasmittente" placeholder="Partita IVA del trasmittente"/>
        <label v-if="Dati.PartitaIvaTrasmittente == ''" class="ZMFormLabelError">Campo obbligatorio</label>                
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:49%">
        <label style="font-weight: bold;">emessa da</label>
        <VUEInputNazioni v-model="Dati.PaeseTrasmittente"/>
        <label v-if="Dati.PaeseTrasmittente == -1" class="ZMFormLabelError">Campo obbligatorio</label>                
      </div>
    </div>
    <div style="clear:both"></div>
   </div>


   <div  v-if="Tabs.ActiveTab == 'Anagrafica'">
    <div style="clear:both">
      <div style="float:left;width:20%">
        <label style="font-weight: bold;">Nazione</label>
        <VUEInputNazioni v-model="Dati.NazionePIvaPrestatore"/>
        <label v-if="Dati.NazionePIvaPrestatore == -1" class="ZMFormLabelError">Campo obbligatorio</label>                
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:19%">
        <label style="font-weight: bold;">Partita IVA</label>
        <input type="text" class="form-control" v-model="Dati.PartitaIvaPrestatore" placeholder="Partita IVA"/>
        <label v-if="Dati.PartitaIvaPrestatore == ''" class="ZMFormLabelError">Campo obbligatorio</label>                
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:19%">
        <label style="font-weight: bold;">Ns. codice fiscale</label>
        <VUEInputCodiceFiscale v-model="Dati.CodiceFiscalePrestatore"/>
        <label v-if="Dati.CodiceFiscalePrestatore == ''" class="ZMFormLabelError">Campo obbligatorio</label>                
        <label v-if="ErroreCodiceFiscale != ''" class="ZMFormLabelError">{{ ErroreCodiceFiscale }}</label>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:19%">
        <label style="font-weight: bold;">Codice SdI</label>
        <input maxlength="7" type="text" class="form-control" v-model="Dati.CodiceSdiPrestatore" placeholder="Codice SdI"/>
        <label v-if="Dati.CodiceSdiPrestatore.length != 7" class="ZMFormLabelError">
          {{(Dati.CodiceSdiPrestatore.length == 0 ? 'Campo obbligatorio' : 'Campo non valido')}}
        </label>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:19%">
        <label style="font-weight: bold;">PEC</label>
        <input type="text" class="form-control" v-model="Dati.PecPrestatore" placeholder="PEC"/>
      </div>
    </div>
    <div class="ZMNuovaRigaScheda" style="width:40%">
            <div style="float:left;">
              <input type="checkbox" v-model="Dati.PersonaFisicaPrestatore"/>
            </div>   
            <div style="float:left;padding-top: 1px;width:28%">
               <label style="font-weight: bold;">&nbsp;Persona fisica</label>
            </div>
    </div>
    <div style="clear:both">
      <div style="float:left;width:50%;margin-bottom:1%">
        <label style="font-weight: bold;">Denominazione</label>
        <input type="text" class="form-control" v-model="Dati.DenominazionePrestatore" placeholder="Denominazione"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:49%">
        <label style="font-weight: bold;">Regime fiscale</label>
        <select v-model="Dati.RegimeFiscalePrestatore" class="form-control">
            <option v-for="SelectOption in LsRegimiFiscali" 
                          :Key="SelectOption.Id"
                          :value="SelectOption.Id"
                          :selected="SelectOption.Id == Dati.RegimeFiscalePrestatore">
                          {{SelectOption.Descrizione}}
            </option>       
        </select>    
      </div>
    </div>

    <div class="ZMSeparatoreScheda" style="margin-top:1%">Sede</div>
    <div style="clear:both">
      <div style="float:left;width:75%">
        <label style="font-weight: bold;">Indirizzo</label>
        <input type="text" class="form-control" v-model="Dati.IndirizzoSedePrestatore" placeholder="Indirizzo"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Numero civico</label>
          <input maxlength="7" type="text" class="form-control" v-model="Dati.CivicoSedePrestatore" placeholder="Numero civico"/>
        </div>
    </div>
    <div style="clear:both">
      <div style="float:left;width:50%">
        <label style="font-weight: bold;">Comune</label>
        <input type="text" class="form-control" v-model="Dati.ComuneSedePrestatore" placeholder="Comune"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:24%">
        <label style="font-weight: bold;">Provincia</label>
        <VUEInputProvince v-model="Dati.ProvinciaSedePrestatore"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:24%">
        <label style="font-weight: bold;">CAP</label>
        <input type="text" class="form-control" v-model="Dati.CAPSedePrestatore" placeholder="CAP"/>
      </div>
    </div>
    <div class="ZMNuovaRigaScheda">
        <label style="font-weight: bold;">Nazione</label>
        <VUEInputNazioni v-model="Dati.NazioneSedePrestatore"/>
    </div>

   <div class="ZMSeparatoreScheda" style="margin-top:1%">Stabilimento organizzativo</div>
    <div style="clear:both">
      <div style="float:left;width:75%">
        <label style="font-weight: bold;">Indirizzo</label>
        <input type="text" class="form-control" v-model="Dati.IndirizzoSuccursale" placeholder="Indirizzo"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Numero civico</label>
          <input maxlength="7" type="text" class="form-control" v-model="Dati.CivicoSuccursale" placeholder="Numero civico"/>
        </div>
    </div>
    <div style="clear:both">
      <div style="float:left;width:50%">
        <label style="font-weight: bold;">Comune</label>
        <input type="text" class="form-control" v-model="Dati.ComuneSuccursale" placeholder="Comune"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:24%">
        <label style="font-weight: bold;">Provincia</label>
        <VUEInputProvince v-model="Dati.ProvinciaSuccursale"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:24%">
        <label style="font-weight: bold;">CAP</label>
        <input type="text" class="form-control" v-model="Dati.CAPSuccursale" placeholder="CAP"/>
      </div>
    </div>
    <div class="ZMNuovaRigaScheda">
        <label style="font-weight: bold;">Nazione</label>
        <VUEInputNazioni v-model="Dati.NazioneSuccursale"/>
    </div>

   <div class="ZMSeparatoreScheda" style="margin-top:1%">Recapiti</div>  
    <div style="clear:both">
      <div style="float:left;width:50%;margin-bottom:1%">
        <label style="font-weight: bold;">Telefono</label>
        <input type="text" class="form-control" v-model="Dati.TelefonoPrestatore" placeholder="Telefono"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:49%">
        <label style="font-weight: bold;">Email</label>
        <input type="text" class="form-control" v-model="Dati.EmailPrestatore" placeholder="Email"/>
      </div>
    </div>
   <div class="ZMSeparatoreScheda" style="margin-top:1%">Ritenuta</div>  
   <div style="clear:both">
      <div style="float:left;width:50%;margin-bottom:1%">
        <label style="font-weight: bold;">Tipo ritenuta</label>
        <select v-model="Dati.TipoRitenuta" class="form-control">
          <option v-for="SelectOption in LsTipoRitenutaPrestatore" 
                        :Key="SelectOption.CHIAVE"
                        :value="SelectOption.CHIAVE"
                        :selected="SelectOption.CHIAVE == Dati.TipoRitenuta">
                        {{SelectOption.DESCRIZIONE}}
          </option>      
        </select>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
      <div style="float:left;width:49%">
        <label style="font-weight: bold;">Causale ritenuta</label>
        <select v-model="Dati.CasualeRitenuta" class="form-control">
          <option v-for="SelectOption in LsCausaleRitenuta" 
                        :Key="SelectOption.Id"
                        :value="SelectOption.Id"
                        :selected="SelectOption.Id == Dati.CasualeRitenuta">
                        {{SelectOption.Descrizione}}
          </option>      
        </select> 
      </div>
    </div>
    <div style="clear:both"></div>
   </div>


   <div  v-if="Tabs.ActiveTab == 'IscrizioneREA'" style="height:300px">
    <div class="ZMNuovaRigaScheda" style="width:100%">
      <div style="float:left;width:30%">&nbsp;</div>
      <div style="float:left;">
        <input type="checkbox" v-model="Dati.IscrittoREA" @click="OnClickIscrittoREA"/>
      </div>   
      <div style="float:left;padding-top: 1px;width:28%">
          <label style="font-weight: bold;">&nbsp;Iscritto al REA</label>
      </div>
    </div>
    <div style="clear:both" v-if="Dati.IscrittoREA">
      <div style="float:left;width:75%">
        <label style="font-weight: bold;">Codice REA</label>
        <input type="text" class="form-control" v-model="Dati.CodiceREA" placeholder="Codice REA"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:24%">
          <label style="font-weight: bold;">Provincia ufficio REA</label>
          <VUEInputProvince v-model="Dati.ProvinciaUfficioREA"/>
        </div>
    </div>
    <div class="ZMNuovaRigaScheda" v-if="Dati.IscrittoREA">
        <label style="font-weight: bold;">Capitale sociale</label>
        <input type="number" class="form-control" v-model="Dati.CapitaleSociale" placeholder="Capitale sociale"/>
    </div>
    <div style="clear: both" v-if="Dati.IscrittoREA">
      <div style="clear: both">&nbsp;</div>
      <label style="font-weight: bold;">Stato liquidazione</label>
      <div style="float:left;">
        <input type="radio" :value="false" v-model="Dati.StatoLiquidazione"/>
      </div>   
      <div style="float:left;padding-top: 1px;">
          <label style="font-weight: bold;">&nbsp;Non in liquidazione</label>
      </div> 
      <div style="float:left;margin-left:15px">
        <input type="radio" :value="true" v-model="Dati.StatoLiquidazione"/>
      </div>   
      <div style="float:left;padding-top: 1px;">
          <label style="font-weight: bold;">&nbsp;In liquidazione</label>
      </div> 
    </div>
    <div style="clear: both">&nbsp;</div>
    <div style="clear: both;" v-if="Dati.IscrittoREA" >
      <label style="font-weight: bold;">Socio unico</label>
      <div style="float:left;">
        <input type="radio" :value="true" v-model="Dati.SocioUnico"/>
      </div>   
      <div style="float:left;padding-top: 1px;">
          <label style="font-weight: bold;">&nbsp;Socio unico</label>
      </div> 
      <div style="float:left;margin-left:15px">
        <input type="radio" :value="false" v-model="Dati.SocioUnico"/>
      </div>   
      <div style="float:left;padding-top: 1px;">
          <label style="font-weight: bold;">&nbsp;Più soci</label>
      </div> 
    </div>
    <div style="clear:both"></div>
   </div>


   <div  v-if="Tabs.ActiveTab == 'RappresentanteFiscale'" style="height:300px">
    <div class="ZMNuovaRigaScheda" style="width:100%">
            <div style="float:left;width:30%">&nbsp;</div>
            <div style="float:left;">
              <input type="checkbox" v-model="Dati.UsufruisceRapprFisc" @click="OnClickRappresentanteFiscale"/>
            </div>   
            <div style="float:left;padding-top: 1px;">
               <label style="font-weight: bold;">&nbsp;Usufruisce di un rappresentante fiscale nei termini del DPR 633 del 1972</label>
            </div>
    </div>
    <div style="clear:both" v-if="Dati.UsufruisceRapprFisc">
      <div style="float:left;width:50%">
        <label style="font-weight: bold;">Nazione</label>
        <VUEInputNazioni v-model="Dati.NazionePIvaRapprFiscale"/>
      </div>
      <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:left;width:49%">
          <label style="font-weight: bold;">Partita IVA</label>
          <input type="text" class="form-control" v-model="Dati.PartitaIvaRapprFiscale" placeholder="Partita IVA"/>
        </div>
    </div>
    <div class="ZMNuovaRigaScheda" style="width:40%" v-if="Dati.UsufruisceRapprFisc">
            <div style="float:left;">
              <input type="checkbox" v-model="Dati.RapprFiscalePersFisica"/>
            </div>   
            <div style="float:left;padding-top: 1px;width:28%">
               <label style="font-weight: bold;">&nbsp;Persona fisica</label>
            </div>
    </div>
    <div style="clear:both" v-if="Dati.UsufruisceRapprFisc">
      <div style="float:left;width:50%;margin-bottom:1%">
        <label style="font-weight: bold;">Denominazione</label>
        <input type="text" class="form-control" v-model="Dati.DenomRapprFiscFiscale" placeholder="Denominazione"/>
      </div> 
    </div> 
    <div style="clear:both"></div>
   </div>

 </div>
</template>

<script>
import { SystemInformation } from '@/SystemInformation';
import VUEInputNazioni from '@/components/InputComponents/VUEInputNazioni.vue';
import VUEInputCodiceFiscale from '@/components/InputComponents/VUEInputCodiceFiscale.vue';
import VUEInputProvince from '@/components/InputComponents/VUEInputProvince.vue';
import VUEConfirm from '@/components/VUEConfirm.vue';
import { TZFatturaElettronica,
         TZFatturaTipoRitenuta, 
         TZFattElettronicaCausaleRitenuta, 
         TZFattElettronicaRegimeFiscale } from '../../../../../../../../Librerie/VUE/ZFatturaElettronica.js';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { TZEconomicFunct, TZCheckDatiFiscali } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';

export default 
{
   props : ['Altezza'],
   components : 
   {
      VUEInputNazioni, 
      VUEInputProvince,
      VUEConfirm,
      VUEInputCodiceFiscale,
   },
   data()
   {
     return { 
              Tabs            : {
                                    ActiveTab : 'DatiTrasmissione',
                                    Tabs      : [
                                                  {
                                                    Caption : 'Dati trasmissione',
                                                    Id      : 'DatiTrasmissione'
                                                  },
                                                  {
                                                    Caption : 'Anagrafica cedente/prestatore',
                                                    Id      : 'Anagrafica'
                                                  },
                                                  {
                                                    Caption : 'Iscrizione REA',
                                                    Id      : 'IscrizioneREA' 
                                                  },
                                                  {
                                                   Caption: 'Rappresentante fiscale',
                                                   Id     : 'RappresentanteFiscale'
                                                  },
                                                ]
                                 },
              Dati    : {
                            PartitaIvaTrasmittente  : '',
                            PaeseTrasmittente       : -1,
                            NazionePIvaPrestatore   : -1,
                            PartitaIvaPrestatore    : '',
                            CodiceFiscalePrestatore : '',
                            CodiceSdiPrestatore     : '',
                            PecPrestatore           : '',
                            PersonaFisicaPrestatore : false,
                            DenominazionePrestatore : '',
                            RegimeFiscalePrestatore : TZFattElettronicaRegimeFiscale.ID_FATT_REGIME_FISCALE_RF01,
                            IndirizzoSedePrestatore : '',
                            ComuneSedePrestatore    : '',
                            ProvinciaSedePrestatore : -1,
                            CivicoSedePrestatore    : '',
                            NazioneSedePrestatore   : -1,
                            CAPSedePrestatore       : '',
                            IndirizzoSuccursale     : '',
                            ComuneSuccursale        : '',
                            ProvinciaSuccursale     : -1,
                            CivicoSuccursale        : '',
                            NazioneSuccursale       : -1,
                            CAPSuccursale           : '',
                            TelefonoPrestatore      : '',
                            EmailPrestatore         : '',
                            TipoRitenuta            : TZFatturaTipoRitenuta.ritPersonaGiuridica,
                            CasualeRitenuta         : TZFattElettronicaCausaleRitenuta.ID_FATT_CAUSALE_RITENUTA_RITA,
                            IscrittoREA             : false,
                            CodiceREA               : '',
                            ProvinciaUfficioREA     : '',
                            CapitaleSociale         : 0,
                            StatoLiquidazione       : false,
                            SocioUnico              : false,
                            UsufruisceRapprFisc     : false,
                            RapprFiscalePersFisica  : false,
                            NazionePIvaRapprFiscale : '',
                            PartitaIvaRapprFiscale  : '',
                            NomeRapprFiscale        : '',
                            CognomeRapprFiscale     : '',
                            DenomRapprFiscFiscale   : '',
                        },

              Snapshot                 : '',
              IsNuovo                  : false,
              LsTipoRitenutaPrestatore : TZFatturaElettronica.GetLsTipoRitenuta(),
              LsCausaleRitenuta        : TZFatturaElettronica.GetLsCausaliRitenuta(),
              LsRegimiFiscali          : TZFatturaElettronica.GetLsRegimiFiscali(),
              PopupFatturaElettronica  : false
            }
   },

   computed :
   {
     ModificheDaApplicare : 
     {
        get()
        {
          if(this.Snapshot != '')
            return this.Snapshot != JSON.stringify(this.Dati)
          return false
        }
     },

    ErroreCodiceFiscale :
    {
      get()
      {
          let Result = TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CodiceFiscalePrestatore);
          if(Result != TZCheckDatiFiscali.datiFiscaliOk  && Result != TZCheckDatiFiscali.datiFiscaliNonCompilati)
            return TZEconomicFunct.MessageResultCheckDatiFiscali(Result,true);
          return ''
      }
    },

   },

   methods:
   {
        Registra()
        {
          var Self = this;
          if(this.CanRecord())
          {
            var ObjQuery = { 
                                Operazioni : [
                                                {
                                                    Query     : Self.IsNuovo? "Inserisci" : "Update",
                                                    Parametri : {
                                                                    PAESE_TRASMITTENTE              : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.PaeseTrasmittente),
                                                                    PIVA_TRASMITTENTE               : TSchedaGenerica.PrepareForRecordString(Self.Dati.PartitaIvaTrasmittente),
                                                                    NAZIONE_EM_PIVA_PRESTATORE      : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NazionePIvaPrestatore),
                                                                    PARTITA_IVA_PRESTATORE          : TSchedaGenerica.PrepareForRecordString(Self.Dati.PartitaIvaPrestatore),
                                                                    CODICE_FISCALE_PRESTATORE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.CodiceFiscalePrestatore),
                                                                    CODICE_SDI_PRESTATORE           : TSchedaGenerica.PrepareForRecordString(Self.Dati.CodiceSdiPrestatore),
                                                                    PEC_PRESTATORE                  : TSchedaGenerica.PrepareForRecordString(Self.Dati.PecPrestatore),
                                                                    DENOMINAZIONE_PRESTATORE        : TSchedaGenerica.PrepareForRecordString(Self.Dati.DenominazionePrestatore),
                                                                    //COGNOME_PRESTATORE         : Self.Dati.cognome,
                                                                    REGIME_FISCALE_PRESTATORE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.RegimeFiscalePrestatore),
                                                                    INDIRIZZO_SEDE_PRESTATORE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.IndirizzoSedePrestatore),
                                                                    NR_CIVICO_SEDE_PRESTATORE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.CivicoSedePrestatore),
                                                                    CAP_SEDE_PRESTATORE             : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAPSedePrestatore),
                                                                    COMUNE_SEDE_PRESTATORE          : TSchedaGenerica.PrepareForRecordString(Self.Dati.ComuneSedePrestatore),
                                                                    NAZIONE_SEDE_PRESTATORE         : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NazioneSedePrestatore),
                                                                    NAZIONE_SUCCURSALE_PRESTATORE   : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NazioneSuccursale),
                                                                    INDIRIZZO_SUCCURSALE_PRESTATORE : TSchedaGenerica.PrepareForRecordString(Self.Dati.IndirizzoSuccursale),
                                                                    COMUNE_SUCCURSALE_PRESTATORE    : TSchedaGenerica.PrepareForRecordString(Self.Dati.ComuneSuccursale),
                                                                    NR_CIVICO_SUCCURSALE_PRESTATORE : TSchedaGenerica.PrepareForRecordString(Self.Dati.CivicoSuccursale),
                                                                    CAP_SUCCURSALE_PRESTATORE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.CAPSuccursale),
                                                                    PRESTATORE_PERSONA_FISICA       : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.PersonaFisicaPrestatore),
                                                                    ISCRITTO_REA                    : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.IscrittoREA),
                                                                    NUMERO_REA                      : TSchedaGenerica.PrepareForRecordString(Self.Dati.CodiceREA),
                                                                    ID_PROVINCIA_REA                : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ProvinciaUfficioREA),
                                                                    CAPITALE_SOCIALE_REA            : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.CapitaleSociale * 100),
                                                                    SOCIO_UNICO_REA                 : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.SocioUnico),  
                                                                    IN_LIQUIDAZIONE_REA             : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.StatoLiquidazione),
                                                                    RAPPRESENTANTE_FISCALE          : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.UsufruisceRapprFisc),
                                                                    PARTITA_IVA_RAPPR_FISCALE       : TSchedaGenerica.PrepareForRecordString(Self.Dati.PartitaIvaRapprFiscale),
                                                                    NAZIONE_EM_PIVA_RAPPR_FISCALE   : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.NazionePIvaRapprFiscale),
                                                                    DENOMINAZIONE_RAPPR_FISCALE     : TSchedaGenerica.PrepareForRecordString(Self.Dati.DenomRapprFiscFiscale),
                                                                    COGNOME_RAPPR_FISCALE           : TSchedaGenerica.PrepareForRecordString(Self.Dati.CognomeRapprFiscale),
                                                                    RAPPR_FISCALE_PERSONA_FISICA    : TSchedaGenerica.PrepareForRecordBoolean(Self.Dati.RapprFiscalePersFisica),
                                                                    PROVINCIA_SEDE_PRESTATORE       : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ProvinciaSedePrestatore),
                                                                    PROVINCIA_SUCCURSALE_PRESTATORE : TSchedaGenerica.PrepareForRecordListIndex(Self.Dati.ProvinciaSuccursale),
                                                                    TELEFONO_PRESTATORE             : TSchedaGenerica.PrepareForRecordString(Self.Dati.TelefonoPrestatore),
                                                                    EMAIL_PRESTATORE                : TSchedaGenerica.PrepareForRecordString(Self.Dati.EmailPrestatore),
                                                                    TIPO_RITENUTA                   : TSchedaGenerica.PrepareForRecordInteger(Self.Dati.TipoRitenuta),
                                                                    CAUSALE_RITENUTA                : TSchedaGenerica.PrepareForRecordString(Self.Dati.CasualeRitenuta)
                                                            }
                                                }
                                            ] 
                            };
            SystemInformation.AdvQuery.PostSQL('ImpostazioniFatturaElettronica',ObjQuery,function()
            {
                SystemInformation.GetConfigurazioni(function()
                {
                  ObjQuery = {};
                  Self.Annulla();
                })
            },
            function(HTTPError,SubHTTPError,Response)
            {
                SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
            });
          }
        },

        ConfermaElimina()
        {
            this.Annulla()
            this.PopupFatturaElettronica = false
        },

        CanRecord()
        {
          return ( this.Dati.PaeseTrasmittente          != '' &&
                   this.Dati.PartitaIvaTrasmittente     != '' && 
                   this.Dati.NazionePIvaPrestatore      != -1 && 
                   this.Dati.PartitaIvaPrestatore       != -1 &&
                   this.Dati.CodiceFiscalePrestatore    != '' &&
                   this.Dati.CodiceSdiPrestatore.length == 7  &&
                   TZEconomicFunct.VerificaCodiceFiscale(this.Dati.CodiceFiscalePrestatore) == TZCheckDatiFiscali.datiFiscaliOk
                  )
        },

        OnClickIscrittoREA()
        {
            this.Dati.CodiceREA           = ''
            this.Dati.ProvinciaUfficioREA = ''
            this.Dati.CapitaleSociale     = 0
            this.Dati.StatoLiquidazione   = false
            this.Dati.SocioUnico          = true 
        },

        OnClickRappresentanteFiscale()
        {
            this.Dati.RapprFiscalePersFisica  = false
            this.Dati.PartitaIvaRapprFiscale  = ''
            this.Dati.DenomRapprFiscFiscale   = ''
            this.Dati.NazionePIvaRapprFiscale = -1
        },  

        ReturnUpperCase()
        {
           this.Dati.ProvinciaUfficioREA = this.Dati.ProvinciaUfficioREA.toUpperCase()
        },

        AnnullaElimina()
        {
          this.Annulla()
        },

        Annulla()
        {
        var Self = this;
        SystemInformation.AdvQuery.GetSQL("ImpostazioniFatturaElettronica",{},
                                            function(Results)
                                            {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"ImpostazioniFatturaElettronica");
                                            
                                            if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                            {
                                              Self.Dati.PaeseTrasmittente       = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PAESE_TRASMITTENTE)
                                              Self.Dati.PartitaIvaTrasmittente  = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PIVA_TRASMITTENTE)                               
                                              Self.Dati.NazionePIvaPrestatore   = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA_PRESTATORE)                      
                                              Self.Dati.PartitaIvaPrestatore    = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA_PRESTATORE)                        
                                              Self.Dati.CodiceFiscalePrestatore = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_FISCALE_PRESTATORE)                        
                                              Self.Dati.CodiceSdiPrestatore     = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CODICE_SDI_PRESTATORE)                        
                                              Self.Dati.PecPrestatore           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PEC_PRESTATORE)                        
                                              Self.Dati.DenominazionePrestatore = TSchedaGenerica.DisponiFromString(ArrayInfo[0].DENOMINAZIONE_PRESTATORE)                        
                                              //Self.Dati.cognome =                //COGNOME_PRESTATORE           
                                              Self.Dati.RegimeFiscalePrestatore = TSchedaGenerica.DisponiFromString(ArrayInfo[0].REGIME_FISCALE_PRESTATORE)                     
                                              Self.Dati.IndirizzoSedePrestatore = TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_SEDE_PRESTATORE)                                    
                                              Self.Dati.CivicoSedePrestatore    = TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_SEDE_PRESTATORE)                  
                                              Self.Dati.CAPSedePrestatore       = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_SEDE_PRESTATORE)                     
                                              Self.Dati.ComuneSedePrestatore    = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_SEDE_PRESTATORE)                    
                                              Self.Dati.NazioneSedePrestatore   = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SEDE_PRESTATORE)                     
                                              Self.Dati.NazioneSuccursale       = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_SUCCURSALE_PRESTATORE)
                                              Self.Dati.IndirizzoSuccursale     = TSchedaGenerica.DisponiFromString(ArrayInfo[0].INDIRIZZO_SUCCURSALE_PRESTATORE)           
                                              Self.Dati.ComuneSuccursale        = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COMUNE_SUCCURSALE_PRESTATORE)           
                                              Self.Dati.CivicoSuccursale        = TSchedaGenerica.DisponiFromString(ArrayInfo[0].NR_CIVICO_SUCCURSALE_PRESTATORE)        
                                              Self.Dati.CAPSuccursale           = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAP_SUCCURSALE_PRESTATORE)                                         
                                              Self.Dati.PersonaFisicaPrestatore = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].PRESTATORE_PERSONA_FISICA)
                                              Self.Dati.CodiceREA               = TSchedaGenerica.DisponiFromString(ArrayInfo[0].NUMERO_REA)
                                              Self.Dati.IscrittoREA             = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].ISCRITTO_REA)
                                              Self.Dati.ProvinciaUfficioREA     = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_PROVINCIA_REA)                             
                                              Self.Dati.CapitaleSociale         = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].CAPITALE_SOCIALE_REA) / 100                  
                                              Self.Dati.SocioUnico              = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].SOCIO_UNICO_REA) 
                                              Self.Dati.StatoLiquidazione       = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].IN_LIQUIDAZIONE_REA) 
                                              Self.Dati.UsufruisceRapprFisc     = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].RAPPRESENTANTE_FISCALE)          
                                              Self.Dati.PartitaIvaRapprFiscale  = TSchedaGenerica.DisponiFromString(ArrayInfo[0].PARTITA_IVA_RAPPR_FISCALE)                    
                                              Self.Dati.NazionePIvaRapprFiscale = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].NAZIONE_EM_PIVA_RAPPR_FISCALE)                 
                                              Self.Dati.DenomRapprFiscFiscale   = TSchedaGenerica.DisponiFromString(ArrayInfo[0].DENOMINAZIONE_RAPPR_FISCALE)                 
                                              Self.Dati.CognomeRapprFiscale     = TSchedaGenerica.DisponiFromString(ArrayInfo[0].COGNOME_RAPPR_FISCALE)                     
                                              Self.Dati.RapprFiscalePersFisica  = TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].RAPPR_FISCALE_PERSONA_FISICA)    
                                              Self.Dati.ProvinciaSedePrestatore = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SEDE_PRESTATORE)                     
                                              Self.Dati.ProvinciaSuccursale     = TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].PROVINCIA_SUCCURSALE_PRESTATORE)                          
                                              Self.Dati.TelefonoPrestatore      = TSchedaGenerica.DisponiFromString(ArrayInfo[0].TELEFONO_PRESTATORE)                      
                                              Self.Dati.EmailPrestatore         = TSchedaGenerica.DisponiFromString(ArrayInfo[0].EMAIL_PRESTATORE)         
                                              Self.Dati.TipoRitenuta            = TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].TIPO_RITENUTA)                    
                                              Self.Dati.CasualeRitenuta         = TSchedaGenerica.DisponiFromString(ArrayInfo[0].CAUSALE_RITENUTA)       
                                            }
                                            if( ArrayInfo.length == 0)
                                              Self.IsNuovo = true
                                            Self.Snapshot = JSON.stringify(Self.Dati);
                                            },
                                            function(HTTPError,SubHTTPError,Response)
                                            {
                                            SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                            });
        },
   },

   beforeMount() 
   {
    this.Annulla()
   },
}
</script>