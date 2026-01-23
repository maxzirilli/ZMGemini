<template>
  <div>
    <VUEConfirm  :Popup="PopupCambiaPagina" :Richiesta="'Annullare le modifiche effettuate ed uscire dalla pagina?'" @onClickConfermaPopup="ConfermaCambiaPagina" @onClickChiudiPopup="AnnullaCambiaPagina">
    </VUEConfirm>
     <section>
       <VUEAppHeader></VUEAppHeader>
       <section>
         <section class ="hbox stretch">
           <VUEAppSideMenu :Altezza="Altezza"></VUEAppSideMenu>
           <section id="content">
             <section class="vbox"  style="display:block;">          
              <section class="padder" style="overflow-y: scroll;" :style="{height: (Altezza - 50) + 'px'}">
                <div v-if="TitoloPagina != ''" class="breadcrumb no-border no-radius b-b b-light pull-in">
                  <span class ="ZMTitoloContenuti">{{TitoloPagina}}</span>
                </div>
                <VUEAppContentDashboard                            v-if="Pages.Dashboard" :Altezza="Altezza"></VUEAppContentDashboard>
                <VUEAppContentOpzioni                              v-if="Pages.Opzioni"></VUEAppContentOpzioni>
                <VUEAppContentCondPagamento                        v-if="Pages.CondizioniPagamento" :Altezza="Altezza"></VUEAppContentCondPagamento>
                <!-- <VUEAppContentCfgDescrizione v-if="Pages.ScissionePagamenti" NomeModello="ScissionePagamenti" :Altezza="Altezza"/> -->
                <VUEAppContentCfgDescrizione                       v-if="Pages.ModalitaPagamento" NomeModello="ModalitaPagamento" :Altezza="Altezza" />
                <VUEAppContentCfgUnitaDiMisura                     v-if="Pages.UnitaDiMisura" NomeModello="UnitaDiMisura" :Altezza="Altezza"></VUEAppContentCfgUnitaDiMisura>
                <VUEAppContentCfgCatClienti                        v-if="Pages.CatClienti" NomeModello="CategorieClienti" :Altezza="Altezza"></VUEAppContentCfgCatClienti>
                <VUEAppContentCfgVociPreventiviPredefinite         v-if="Pages.VociPreventiviPredefinite" NomeModello="VociPreventiviPredefinite" :Altezza="Altezza"></VUEAppContentCfgVociPreventiviPredefinite>
                <VUEAppContentCfgTitoli                            v-if="Pages.Titoli" NomeModello="Titoli" :Altezza="Altezza"></VUEAppContentCfgTitoli>
                <VUEAppContentCfgTesti                             v-if="Pages.Testi" NomeModello="Testi" :Altezza="Altezza"></VUEAppContentCfgTesti>
                <VUEAppContentCfgDescrizione                       v-if="Pages.CategorieMovimenti" NomeModello="CategorieMovimenti" :Altezza="Altezza"></VUEAppContentCfgDescrizione>
                <VUEAppContentAmmEsportaSdI                        v-if="Pages.AmministrazioneEsporta && !InvioManualeCheck" :Altezza="Altezza"></VUEAppContentAmmEsportaSdI>
                <VUEAppContentAmmEsportaManualmente                v-if="Pages.AmministrazioneEsporta && InvioManualeCheck" :Altezza="Altezza"></VUEAppContentAmmEsportaManualmente>
                <VUEAppContentSolleciti                            v-if="Pages.Solleciti && VisualizzaSolleciti" :Altezza="Altezza"></VUEAppContentSolleciti>
                <VUEAppContentGestioneFatturePassive               v-if="Pages.GestioneFatturePassive"></VUEAppContentGestioneFatturePassive>
                <VUEAppContentCfgFatturaElettronica                v-if="Pages.CfgFatturaElettronica" :Altezza="Altezza"></VUEAppContentCfgFatturaElettronica>
                <VUEAppContentCfgEmailSegnalazioni                 v-if="Pages.CfgEmailSegnalazioni" NomeModello="EmailSegnalazioni" :Altezza="Altezza"></VUEAppContentCfgEmailSegnalazioni>
                <VUEAppContentCfgPannelloAmministratore            v-if="Pages.CfgPannelloAmministratore" NomeModello="Testi" :Altezza="Altezza"></VUEAppContentCfgPannelloAmministratore>
                <VUEAppContentImportazioneFatturePassive           v-if="Pages.ImportazioneFatturePassive" :Altezza="Altezza"></VUEAppContentImportazioneFatturePassive>

                <VUEVersioneGemini                                  v-if="Pages.VersioneGemini" :Altezza="Altezza"></VUEVersioneGemini>

                <VUEAppContentCausali                 v-if="Pages.Causali" NomeModello="Causali" :Altezza="Altezza" />
                <VUEAppContentCfgSettori              v-if="Pages.Settori" NomeModello="Settori" :Altezza="Altezza" />
                <VUEAppContentCfgMagazzini            v-if="Pages.Magazzini" NomeModello="Magazzini" :Altezza="Altezza" />
                <VUEAppContentCfgZone                 v-if="Pages.Zone" NomeModello="Zone" :Altezza="Altezza" />
                <VUEAppContentGestioneUtenti          v-if="Pages.GestioneUtenti" :Altezza="Altezza"></VUEAppContentGestioneUtenti>
                <VUEAppContentGestioneDispositivi     v-if="Pages.GestioneDispositivi" :Altezza="Altezza"></VUEAppContentGestioneDispositivi>
                <VUEAppContentGestioneMessaggi        v-if="Pages.GestioneMessaggi" :Altezza="Altezza"></VUEAppContentGestioneMessaggi>
                <VUEAppContentAggiungiMessaggio       v-if="Pages.AggiungiMessaggio" :Altezza="Altezza"></VUEAppContentAggiungiMessaggio>
                <VUEAppContentSchedaMessaggi          v-if="Pages.SchedaMessaggi"></VUEAppContentSchedaMessaggi>
                <VUEAppContentLogEmailInviate         v-if="Pages.LogEmailInviate"></VUEAppContentLogEmailInviate>
                <VUEAppContentFattureInsolute         v-if="Pages.FattureInsolute && VisualizzaSolleciti"></VUEAppContentFattureInsolute>
                <VUEAppAzioniAmministratore           v-if="Pages.AzioniAmministratore" :Altezza="Altezza"></VUEAppAzioniAmministratore>
             </section>
             </section>
             <a href="#" class="hide nav-off-screen-block" data-toggle="class:nav-off-screen, open" data-target="#nav,html"></a>
           </section>
           <aside class="bg-light lter b-l aside-md hide" id="notes">
             <div class="wrapper">Notification</div>
           </aside>
         </section>
       </section>
     </section>
  </div>
</template>

<script>
import { SystemInformation, RUOLI } from '@/SystemInformation';
import VUEConfirm from  '@/components/VUEConfirm.vue'
import VUEAppSideMenu from '@/components/FrameComponentsMultiPurpose/VUEAppSideMenu.vue';
import VUEAppHeader from '@/components/FrameComponentsMultiPurpose/VUEAppHeader.vue';
import VUEAppContentCondPagamento from '@/views/ContentPages/VUEAppContentCondPagamento.vue';
import VUEAppContentDashboard from '@/views/ContentPages/VUEAppContentDashboard.vue';
import VUEAppContentOpzioni from './ContentPages/VUEAppContentOpzioni.vue';
import VUEAppContentCfgDescrizione from './ContentPages/VUEAppContentCfgDescrizione.vue';
import VUEAppContentCfgUnitaDiMisura from './ContentPages/VUEAppContentCfgUnitaDiMisura.vue';
import VUEAppContentCfgCatClienti from './ContentPages/VUEAppContentCfgCatClienti.vue';
import VUEAppContentCfgVociPreventiviPredefinite from './ContentPages/VUEAppContentCfgVociPreventiviPredefinite.vue';
import VUEAppContentCfgTitoli from './ContentPages/VUEAppContentCfgTitoli.vue';
import VUEAppContentCfgTesti from './ContentPages/VUEAppContentCfgTesti.vue';
import VUEAppContentSolleciti from './ContentPages/VUEAppContentSolleciti.vue';
import VUEAppContentGestioneFatturePassive from './ContentPages/VUEAppContentGestioneFatturePassive.vue';
import VUEAppContentCfgFatturaElettronica from './ContentPages/VUEAppContentCfgFatturaElettronica.vue';
import VUEAppContentCfgEmailSegnalazioni from './ContentPages/VUEAppContentCfgEmailSegnalazioni.vue';
import VUEAppContentCfgPannelloAmministratore from './ContentPages/VUEAppContentCfgPannelloAmministratore.vue';
import VUEAppContentAmmEsportaSdI from './ContentPages/VUEAppContentAmmEsportaSdI.vue';
import VUEAppContentAmmEsportaManualmente from './ContentPages/VUEAppContentAmmEsportaManualmente.vue';
import VUEAppContentCfgZone from '@/views/ContentPages/VUEAppContentCfgZone.vue';
import VUEAppContentCfgSettori from '@/views/ContentPages/VUEAppContentCfgSettori.vue';
import VUEAppContentCfgMagazzini from '@/views/ContentPages/VUEAppContentCfgMagazzini.vue';
import { TSchedaGenerica } from '../../../../../../../Librerie/VUE/ZSchedaGenerica.js'

import VUEAppContentImportazioneFatturePassive from './ContentPages/VUEAppContentImportazioneFatturePassive.vue';
import VUEAppAzioniAmministratore from './ContentPages/VUEAppAzioniAmministratore.vue'
import VUEVersioneGemini from './Versione/VUEVersioneGemini.vue';
import VUEAppContentCausali from './ContentPages/VUEAppContentCausali.vue';
import VUEAppContentGestioneUtenti from './ContentPages/VUEAppContentGestioneUtenti.vue';
import VUEAppContentGestioneDispositivi from './ContentPages/VUEAppContentGestioneDispositivi.vue';
import VUEAppContentGestioneMessaggi from './ContentPages/VUEAppContentGestioneMessaggi.vue';
import VUEAppContentAggiungiMessaggio  from './ContentPages/VUEAppContentAggiungiMessaggio.vue';
import VUEAppContentSchedaMessaggi     from './ContentPages/VUEAppContentSchedaMessaggi.vue';
import VUEAppContentLogEmailInviate    from './ContentPages/VUEAppContentLogEmailInviate.vue';
import VUEAppContentFattureInsolute    from './ContentPages/VUEAppContentFattureInsolute.vue';

export default 
{
    name: "VUEGestionale",
    data() 
    {
     return { 
              UserInformation        : SystemInformation.UserInformation,
              Ruoli                  : RUOLI,
              Altezza                : document.documentElement.clientHeight,
              TitoloPagina           : '',
              NuovaDestinazione      : '',
              PopupCambiaPagina      : false,
              LasciaPagina           : false,
              PaginaInEditing        : false,
              VisualizzaSolleciti    : SystemInformation.AccessRights.VisibilitaSolleciti(),
              InvioManualeCheck      : TSchedaGenerica.DisponiFromBoolean(SystemInformation.Configurazioni.Impostazioni.ESPORTAZIONE_MANUALE_FATTURE),
              Pages                  : {
                                          Dashboard                 : false,
                                          Opzioni                   : false,
                                          CondizioniPagamento       : false,
                                          Causali                   : false,
                                          ScissionePagamenti        : false,
                                          ModalitaPagamento         : false,
                                          UnitaDiMisura             : false,
                                          CatClienti                : false,
                                          VociPreventiviPredefinite : false,
                                          Titoli                    : false,
                                          Testi                     : false, 
                                          CategorieMovimenti        : false,
                                          AmministrazioneEsporta    : false,
                                          Solleciti                 : false,
                                          GestioneFatturePassive    : false,
                                          LogEmailInviate           : false,
                                          FattureInsolute           : false,
                                          CfgFatturaElettronica     : false,
                                          CfgEmailSegnalazioni      : false,
                                          CfgPannelloAmministratore : false,

                                          Settori                   : false, 
                                          Magazzini                 : false, 
                                          TecniciCorsi              : false, 
                                          Zone                      : false, 
                                          VersioneGemini            : false, 
                                          GestioneUtenti            : false,
                                          GestioneDispositivi       : false,
                                          GestioneMessaggi          : false,
                                          AggiungiMessaggio         : false,
                                          SchedaMessaggi            : false,
                                          MostraSideMenu            : false,
                                        }
              
            };
    },

    components: 
    {
      VUEConfirm,
      VUEAppSideMenu,
      VUEAppHeader,
      VUEAppContentCondPagamento,
      VUEAppContentOpzioni,
      VUEAppContentDashboard,
      VUEAppContentCfgDescrizione,
      VUEAppContentCfgUnitaDiMisura,
      VUEAppContentCfgCatClienti,
      VUEAppContentCfgVociPreventiviPredefinite,
      VUEAppContentCfgTitoli,
      VUEAppContentCfgTesti,

      VUEAppContentImportazioneFatturePassive,
      VUEAppAzioniAmministratore,
      VUEVersioneGemini,
      VUEAppContentCfgZone,
      VUEAppContentCfgSettori,
      VUEAppContentCfgMagazzini,
      VUEAppContentAmmEsportaSdI,
      VUEAppContentAmmEsportaManualmente,
      VUEAppContentCfgFatturaElettronica,
      VUEAppContentCfgEmailSegnalazioni,
      VUEAppContentCfgPannelloAmministratore,
      VUEAppContentGestioneUtenti,
      VUEAppContentGestioneDispositivi,
      VUEAppContentGestioneMessaggi,
      VUEAppContentAggiungiMessaggio,
      VUEAppContentSchedaMessaggi,
      VUEAppContentSolleciti,
      VUEAppContentLogEmailInviate,
      VUEAppContentFattureInsolute,
      VUEAppContentCausali,
      VUEAppContentGestioneFatturePassive,
    },

    methods: 
    {
      ConfermaCambiaPagina()
      {
        this.LasciaPagina = true        
        this.$router.push(this.Destinazione)
      },
      AnnullaCambiaPagina()
      {
        this.PopupCambiaPagina = false
      },
      InizioEditingChild()
      {
        this.PaginaInEditing = true
      },
      FineEditingChild()
      {
        this.PaginaInEditing = false
      },
      getDimensions()
      {
        this.Altezza = document.documentElement.clientHeight;
      },
      AggiornaTipoPagina()
      {
        this.TitoloPagina = '';
        this.Pages.Dashboard =  this.$route.params.pagina == 'Dashboard';
        this.Pages.Opzioni =  this.$route.params.pagina == 'Opzioni';

        if(this.Pages.Opzioni) this.TitoloPagina = 'Impostazioni generali'; 

        this.Pages.AmministrazioneEsporta =  this.$route.params.pagina == 'AmmEsportaSdI';      
        if(this.Pages.AmministrazioneEsporta) this.TitoloPagina = 'Esporta fatture verso SdI';

        this.Pages.Solleciti = this.$route.params.pagina == 'Solleciti';
        if(this.Pages.Solleciti) this.TitoloPagina = 'Solleciti';
        
        this.Pages.GestioneFatturePassive = this.$route.params.pagina == 'GestioneFatturePassive';
        if(this.Pages.GestioneFatturePassive) this.TitoloPagina = 'Gestione fatture passive';
        
        this.Pages.LogEmailInviate = this.$route.params.pagina == 'LogEmailInviate';      
        if(this.Pages.LogEmailInviate) this.TitoloPagina = 'Log email inviate';

        this.Pages.CfgFatturaElettronica = this.$route.params.pagina == 'CfgFatturaElettronica';
        if(this.Pages.CfgFatturaElettronica) this.TitoloPagina = 'Configurazione fattura elettronica';
        
        this.Pages.CfgEmailSegnalazioni = this.$route.params.pagina == 'CfgEmailSegnalazioni';
        if(this.Pages.CfgEmailSegnalazioni) this.TitoloPagina = 'Configurazione email segnalazioni';
        
        this.Pages.CfgPannelloAmministratore = this.$route.params.pagina == 'ConfigurazionePannelloAmministratore';
        if(this.Pages.CfgPannelloAmministratore) this.TitoloPagina = 'Pannello configurazione amministratore'; 

        this.Pages.CondizioniPagamento = this.$route.params.pagina == 'CondPagamento';
        if(this.Pages.CondizioniPagamento) this.TitoloPagina = 'Condizioni pagamento';

        this.Pages.Causali = this.$route.params.pagina == 'Causali';
        if(this.Pages.Causali) this.TitoloPagina = 'Causali';

        // this.Pages.ScissionePagamenti =  this.$route.params.pagina == 'ScissionePagamenti';
        // if(this.Pages.ScissionePagamenti) this.TitoloPagina = 'Scissione dei pagamenti';

        this.Pages.ModalitaPagamento =  this.$route.params.pagina == 'ModPagamento';
        if(this.Pages.ModalitaPagamento) this.TitoloPagina = 'Modalità pagamento';

        this.Pages.CategorieMovimenti =  this.$route.params.pagina == 'CatMovimenti';
        if(this.Pages.CategorieMovimenti) this.TitoloPagina = 'Tipologia movimenti';

        this.Pages.UnitaDiMisura = this.$route.params.pagina == 'UnitaDiMisura';
        if(this.Pages.UnitaDiMisura) this.TitoloPagina = 'Unita di misura';

        this.Pages.VociPreventiviPredefinite = this.$route.params.pagina == 'VociPreventiviPredefinite';
        if(this.Pages.VociPreventiviPredefinite) this.TitoloPagina = 'Voci preventivi predefinite';

        this.Pages.Titoli = this.$route.params.pagina == 'Titoli';
        if(this.Pages.Titoli) this.TitoloPagina = 'Titoli';

        this.Pages.Testi = this.$route.params.pagina == 'Testi';
        if(this.Pages.Testi) this.TitoloPagina = 'Testi';

        this.Pages.ImportazioneFatturePassive = this.$route.params.pagina == 'ImportazioneFatturePassive';
        if(this.Pages.ImportazioneFatturePassive) this.TitoloPagina = 'Importazione fatture passive';

        this.Pages.AzioniAmministratore = this.$route.params.pagina == 'AzioniAmministratore';
        if(this.Pages.AzioniAmministratore) this.TitoloPagina = 'Azioni amministratore';

        this.Pages.VersioneGemini =  this.$route.params.pagina == 'VersioneGemini';
        if(this.Pages.VersioneGemini) this.TitoloPagina = 'Versione Gemini';
        this.Pages.Settori = this.$route.params.pagina == 'Settori';
        if(this.Pages.Settori) this.TitoloPagina = 'Settori merceologici';
        this.Pages.Magazzini = this.$route.params.pagina == 'Magazzini';
        if(this.Pages.Magazzini) this.TitoloPagina = 'Magazzini';
        this.Pages.Zone = this.$route.params.pagina == 'ConfigurazioneZone';
        if(this.Pages.Zone) this.TitoloPagina = 'Zone';
        this.Pages.GestioneUtenti = this.$route.params.pagina == 'GestioneUtenti';
        if(this.Pages.GestioneUtenti) this.TitoloPagina = 'Gestione utenti';
        this.Pages.GestioneDispositivi = this.$route.params.pagina == 'GestioneDispositivi';
        if(this.Pages.GestioneDispositivi) this.TitoloPagina = 'Gestione dispositivi';
        this.Pages.GestioneMessaggi = this.$route.params.pagina == 'GestioneMessaggi';
        if(this.Pages.GestioneMessaggi) this.TitoloPagina = 'Gestione messaggi';
        this.Pages.AggiungiMessaggio = this.$route.params.pagina == 'AggiungiMessaggio';
        if(this.Pages.AggiungiMessaggio) this.TitoloPagina = 'Aggiungi messaggio';
        this.Pages.SchedaMessaggi = this.$route.params.pagina == 'SchedaMessaggi';
        if(this.Pages.SchedaMessaggi) this.TitoloPagina = 'Scheda messaggi';

      }
    },
    beforeMount()
    {
      this.AggiornaTipoPagina();
    },
    beforeUpdate()
    {
      this.AggiornaTipoPagina();
    },
    mounted() 
    {
      window.addEventListener('resize', this.getDimensions);
    },
    unmounted() 
    {
      window.removeEventListener('resize', this.getDimensions);
    },
    beforeRouteUpdate(to, from, next)
    {
      if (this.PaginaInEditing)
      {
        if (!this.LasciaPagina)
        {
            this.PopupCambiaPagina = true
            this.NuovaDestinazione = to.fullPath
            next(false)
        }
        else
        {
          this.PaginaInEditing = false         
          next()
        }
      }
      else
        next()
    },
};
</script>

<style scoped>
  .dropdown-menu {
    display: block;
  }
</style>