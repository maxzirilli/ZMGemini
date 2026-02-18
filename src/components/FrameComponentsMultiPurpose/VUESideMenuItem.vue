<template>
 <li :class="ClassMenuItem(MenuItem)" style="z-index:99990">
   <a @click="OnClickMenu(MenuItem)">
     <i :class="MainIcon" class="fa icon">
       <b v-if="MenuItem.Level == 1" :class="MenuItem.Data.ColorType"></b>
     </i>
     <span v-if="MenuItem.Level == 1 && MenuItem.Children.length > 0 || MenuItem.HasChildren" class="pull-right">
       <i :class="MenuItem.Expanded ? 'fa-angle-up' : 'fa-angle-down'" class="fa text"></i>
     </span>
     <span>{{MenuItem.Caption}}</span>
   </a>
   <ul v-if="MenuItem.Expanded && MenuItem.Children.length > 0" class="nav" style="display: block;">
     <template v-for="SubMenuItem in MenuItem.Children" :key="SubMenuItem.Data.IdMenu">
        <VUESideMenuItem :MenuItem="SubMenuItem" :SideMenu="SideMenu"></VUESideMenuItem>
     </template> 
   </ul>
 </li>  
</template>

<script>

import { SystemInformation } from '@/SystemInformation';

export const TIdSideMenu = 
{
   DASHBOARD                       : 1,
   CONFIGURAZIONI                  : 2,
   CFG_COND_PAGAMENTO              : 3,
   CFG_MOD_PAGAMENTO               : 4,
   CFG_UNITA_DI_MISURA             : 5,
    
   ATTR_PREZZI_ORDINARI            : 7,
   ATTR_BATTERIE                   : 8, //eliminato
   ATTR_TECNICHE                   : 9,
       
   CFG_ALTRE_CONFIGURAZIONI        : 10,
   CFG_PAGAMENTI                   : 11,
   CFG_GENERALE                    : 12,
   CFG_DATI_FISCALI                : 13,
   CFG_CAT_MOVIMENTI               : 14,
   CFG_PRODOTTI                    : 15,
   CFG_SETTORI                     : 16,
       
   CFG_CAUSALI                     : 17,
   CFG_SCISSIONE_PAGAMENTI         : 18,
   CFG_CORSI_DEI_TECNICI           : 19,
   CFG_FATTURA_ELETTRONICA         : 20,
       
   AMM_ESPORTA_SDI                 : 21,
   AMMINISTRAZIONE                 : 22,
   AMM_GESTIONE                    : 23,
   AMM_GESTIONE_DISPOSITIVI        : 24,
   AMM_GESTIONE_MESSAGGI           : 25,
   AMM_MY_SOND                     : 26,

   CFG_TITOLI                      : 29,
   CFG_ZONE                        : 30,
   CFG_PANNELLO_AMM                : 31,
   LOG_EMAIL_INVIATE               : 32,
   IMPOSTAZIONI_TECNICI_CHECKLIST  : 33,
   CFG_MOTIVAZIONI_RITIRO          : 34,
   AMM_PORTALE_FATT_ELETTRONICHE   : 36,
   CFG_TESTI                       : 37,

   GESTORE_PREVENTIVI_ANOMALIE     : 39,

   AMM_FATTURE_ELETTRONICHE        : 40,

   CFG_VOCI_PREVENTIVI_PREDEFINITE : 42,

   AMM_IMPORTAZIONE_FATT_PASS      : 43,
   CFG_MOTIVAZIONI_RITIRO_IDR      : 44,
   SOLLECITI                       : 47,
   CFG_OPENSTREETMAP               : 49,

   GESTORE_FATTURE_PASSIVE         : 50,
   AMM_GESTIONE_CONTATTI           : 52,

   GESTIONE_PROGRAMMAZIONE         : 54,

   AMM_SCARICO_FILE_ZIP_CON_XML    : 55,

   CFG_MAIL_SOLLECITI              : 56,
   CFG_CAT_CLIENTI                 : 57,

   LOG_MODIFICHE_UTENTI            : 58,

   ATTR_ALTRI_PREZZI               : 60,

   CFG_ESITO_SORVEGLIANZA          : 61,
   CFG_MAGAZZINI                   : 62,
   AMM_AZIONI                      : 63,

}

export default 
{
    name: "VUESideMenuItem",
    props : ['MenuItem','SideMenu'],
    components: {},
    data() 
    {
        return {
          MenuTrigger: SystemInformation.MenuTrigger,
          InserimentoClienteGuidatoTrigger: SystemInformation.InserimentoClienteGuidatoTrigger
        };
    },
    methods:
    {
       OnClickMenu(AMenuItem)
       {
         this.SideMenu.Select(AMenuItem);
         this.SideMenu.ExpandToggle(AMenuItem);
         switch(AMenuItem.Data.IdMenu)
         {
            case TIdSideMenu.DASHBOARD                            : this.$router.push("/appMainWindow/Dashboard");
                                                                    break;
            case TIdSideMenu.CFG_DATI_FISCALI                     : this.$router.push("/appMainWindow/Opzioni");
                                                                    break;
            case TIdSideMenu.CFG_COND_PAGAMENTO                   : this.$router.push('/appMainWindow/CondPagamento');
                                                                    break;   
            case TIdSideMenu.CFG_MOD_PAGAMENTO                    : this.$router.push('/appMainWindow/ModPagamento');
                                                                    break;
            case TIdSideMenu.CFG_CAUSALI                          : this.$router.push('/appMainWindow/Causali');
                                                                    break; 
            // case TIdSideMenu.CFG_SCISSIONE_PAGAMENTI           : this.$router.push('/appMainWindow/ScissionePagamenti');
            //                                                      break;                                                     
            case TIdSideMenu.CFG_CAT_MOVIMENTI                    : this.$router.push('/appMainWindow/CatMovimenti');
                                                                    break;   
            case TIdSideMenu.CFG_UNITA_DI_MISURA                  : this.$router.push('/appMainWindow/UnitaDiMisura');
                                                                    break;
            case TIdSideMenu.CFG_CAT_CLIENTI                      : this.$router.push('/appMainWindow/CatClienti');
                                                                    break;
            case TIdSideMenu.CFG_VOCI_PREVENTIVI_PREDEFINITE      : this.$router.push('/appMainWindow/VociPreventiviPredefinite');
                                                                    break;
            case TIdSideMenu.CFG_TITOLI                           : this.$router.push('/appMainWindow/Titoli');
                                                                    break;
            case TIdSideMenu.CFG_TESTI                            : this.$router.push('/appMainWindow/Testi');
                                                                    break;
            case TIdSideMenu.CFG_CORSI_DEI_TECNICI                : this.$router.push('/appMainWindow/TecniciCorsi');
                                                                    break;
            case TIdSideMenu.CFG_ZONE                             : this.$router.push('/appMainWindow/ConfigurazioneZone');
                                                                    break;                                                       
            case TIdSideMenu.ATTR_TECNICHE                        : this.$router.push('/appMainWindow/AttrTecniche');
                                                                    break;   
            case TIdSideMenu.ATTR_PREZZI_ORDINARI                 : this.$router.push('/appMainWindow/PrezziOrdinariAttr');
                                                                    break;
            case TIdSideMenu.ATTR_ALTRI_PREZZI                    : this.$router.push('/appMainWindow/AltriPrezziariAttr');
                                                                    break;                                                                    
            case TIdSideMenu.CFG_SETTORI                          : this.$router.push('/appMainWindow/Settori');
                                                                    break;
            case TIdSideMenu.CFG_MAGAZZINI                        : this.$router.push('/appMainWindow/Magazzini');
                                                                    break;
            case TIdSideMenu.AMM_ESPORTA_SDI                      : this.$router.push('/appMainWindow/AmmEsportaSdI');
                                                                    break;
            case TIdSideMenu.SOLLECITI                            : this.$router.push('/appMainWindow/Solleciti');
                                                                    break;
            case TIdSideMenu.GESTORE_FATTURE_PASSIVE              : this.$router.push('/appMainWindow/GestioneFatturePassive');
                                                                    break;
            case TIdSideMenu.AMM_PORTALE_FATT_ELETTRONICHE : 
                                                       SystemInformation.AdvQuery.GetSQL("ConfigurazioniAmministratore",{},
                                                                                         (Results) =>
                                                                                         {
                                                                                           let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results,"Impostazioni");
                                                                                           if(ArrayInfo != undefined && ArrayInfo.length != 0)
                                                                                           {
                                                                                              let Path = "https://eportale.eu/angular/loginApp.html?codiceazienda=" + ArrayInfo[0].COD_AZIENDA_MY_SOND + 
                                                                                                         "&nomeutente="  + ArrayInfo[0].USERNAME_MY_SOND + 
                                                                                                         "&password=" + ArrayInfo[0].PASSWORD_MY_SOND;
                                                                                              window.open(Path, '_blank');
                                                                                           }
                                                                                         },
                                                                                         function(HTTPError,SubHTTPError,Response)
                                                                                         {
                                                                                           SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                                                                         },
                                                                                         'MySmond');
                                                                break;                                                        
            case TIdSideMenu.CFG_FATTURA_ELETTRONICA          : this.$router.push('/appMainWindow/CfgFatturaElettronica');
                                                                break; 
                                                      
            case TIdSideMenu.CFG_MAIL_SOLLECITI               : this.$router.push('/appMainWindow/CfgEmailSegnalazioni');
                                                                break; 

            case TIdSideMenu.AMM_GESTIONE_UTENTI              : this.$router.push('/appMainWindow/GestioneUtenti');
                                                                break;
            case TIdSideMenu.AMM_GESTIONE_MESSAGGI            : this.$router.push('/appMainWindow/GestioneMessaggi');
                                                                break;
            case TIdSideMenu.LOG_EMAIL_INVIATE                : this.$router.push('/appMainWindow/LogEmailInviate');
                                                                break; 
            case TIdSideMenu.CFG_PANNELLO_AMM                 : this.$router.push('/appMainWindow/ConfigurazionePannelloAmministratore');
                                                                break;
            case TIdSideMenu.AMM_IMPORTAZIONE_FATT_PASS       : this.$router.push('/appMainWindow/ImportazioneFatturePassive');
                                                                break;
            case TIdSideMenu.CFG_OPENSTREETMAP                : this.$router.push('/appMainWindow/ConfigurazioneOpenStreetMap');
                                                                break;
            case TIdSideMenu.AMM_SCARICO_FILE_ZIP_CON_XML     : this.MenuTrigger.TriggerPopupScaricoFileZip++;
                                                                break;
            case TIdSideMenu.CFG_ESITO_SORVEGLIANZA           : this.$router.push('/appMainWindow/ConfigurazioneEsitoSorveglianza');
                                                                break;
            case TIdSideMenu.AMM_AZIONI                       : this.$router.push('/appMainWindow/AzioniAmministratore');
                                                                break;
         }
       },
       ClassMenuItem(AMenuItem)
       {
         let Result = "";
         if(AMenuItem.Selected)
            Result += 'active ';
         Result += 'MenuColorLivello' + AMenuItem.Level;
         return Result.trim();
       }
    },
    computed :
    {
      MainIcon()
      {
        let Result = this.MenuItem.Data.IconCfg;
        if(this.MenuItem.Children.length > 0)
          if(this.MenuItem.Data.IconCfgExpanded != undefined)
             Result = this.MenuItem.Expanded ? this.MenuItem.Data.IconCfgExpanded : this.MenuItem.Data.IconCfgShrinked;
        return Result;
      }
    }
}

</script>
<style>
  .MenuColorLivello1 {
     background-color: #68b6be !important ;
  }
  
  .MenuColorLivello2 {
     background-color: #3e5468 !important ;
  }
  .MenuColorLivello3 {
     background-color: #4f5f70 !important ;
  }  
  </style>
