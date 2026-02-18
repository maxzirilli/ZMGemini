<template>
   <!-- .aside -->
   <aside class="bg-dark lter aside-md hidden-print hidden-xs" id="nav" v-if="MenuLaterale">          
     <section class="vbox">
       <section class="w-f scrollable">
         <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333" :style="{ height: (Altezza - 100)+ 'px'}">
           <nav class="nav-primary hidden-xs">
             <ul class="nav">
               <template v-for="MenuItem in SideMenu.Children" :key="MenuItem.Data.IdMenu">
                  <VUESideMenuItem :MenuItem="MenuItem" :SideMenu="SideMenu"></VUESideMenuItem>
               </template>
             </ul>
           </nav>
         </div>
       </section>
       
       <footer class="footer lt hidden-xs b-t b-dark">
         <a class="pull-right btn btn-sm btn-dark btn-icon" @click="CambioMenuLaterale">
           <i class="fa fa-angle-left text"></i>
           <i class="fa fa-angle-right text-active"></i>
         </a>
         <div class="btn-group hidden-nav-xs">
            <button @click="$router.push('/appMainWindow/VersioneGemini')" type="button" title="Info Versione" class="btn btn-icon btn-sm btn-dark">
              <i class="fa fa-info"></i>
            </button>
          </div>
       </footer>
     </section>
   </aside>

   <!-- /.aside -->
   <aside class="bg-dark lter aside-md hidden-print hidden-xs nav-xs" id="nav" v-else-if="!MenuLaterale" style="width:65px">          
          <section class="vbox">
            <section class="w-f scrollable">
              <div class="slim-scroll" data-height="auto" data-disable-fade-out="true" data-distance="0" data-size="5px" data-color="#333333" :style="{ height: (Altezza - 100)+ 'px'}">
                
                <!-- nav -->
                <nav class="nav-primary hidden-xs" style="width:65px; background-color: #9db1c5;">
                  <ul class="nav">
                    <template v-for="MenuItem in SideMenu.Children" :key="MenuItem.Data.IdMenu">
                     <VUESideMenuItem :MenuItem="MenuItem" :SideMenu="SideMenu"></VUESideMenuItem>
                    </template>
                    
                  </ul>
                </nav>
              </div>
            </section>
            <footer class="footer lt hidden-xs b-t b-dark" style="width:65px">
              <a data-toggle="class:nav-xs" class="pull-right btn btn-sm btn-dark btn-icon" @click="CambioMenuLaterale" style="width:37px;">
                <i class="fa fa-angle-left text-active"></i>
                <i class="fa fa-angle-right text"></i>
              </a>
            </footer>
          </section>
        </aside>


</template>

<script>
import VUESideMenuItem from './VUESideMenuItem.vue';
import { TZTree } from '../../../../../../../..\\Librerie\\VUE\\ZTreeObjects.js';
import { TIdSideMenu } from './VUESideMenuItem.vue';
import { SystemInformation, RUOLI } from '@/SystemInformation';

export default 
{
    name: "VUEAppSideMenu",
    props : ['Altezza'],

    data()
    {
      
       var SideMenu = new TZTree();
       SideMenu.HideExpandedNodeOnExpanding = true;
       SideMenu.SelectableLevels.push(1);
       return {
                  SideMenu                            : SideMenu,
                  MenuLaterale                        : false,
                  VisibilitaGestoreMailSolleciti      : SystemInformation.AccessRights.VisibilitaGestoreMailSolleciti(),
                  VisibilitaCategorieClienti          : SystemInformation.AccessRights.VisibilitaCategorieClienti(),
                  VisibilitaLogAzioniUtenti           : SystemInformation.AccessRights.VisibilitaLogAzioniUtenti(),
                  VisualizzaEsitoSorveglianza         : SystemInformation.AccessRights.VisualizzaEsitoSorveglianza(),
              }
    },

    components :
    {
       VUESideMenuItem,
    },

    methods: 
    {

       CambioMenuLaterale()
       {
        if(this.MenuLaterale == true)
        {
          this.MenuLaterale = false
          this.CreaMenu()
        }
        else 
        {
          this.MenuLaterale = true
          this.CreaMenu()
        }
       },


       CreaMenu()
       {
          this.SideMenu.Clear();
          let AMenu = null;
          let AMenu2 = null;

          this.SideMenu.AddChild(this.MenuLaterale? "Dashboard" : "Dash.",
                                { 
                                  IdMenu      : TIdSideMenu.DASHBOARD, 
                                  IconCfg     : "fa-dashboard", 
                                  ColorType   : "bg-info" 
                                });

//--------------------------------- Nascondere tranne che ad Amministratore e Superutente _BEGIN_ ------------------------------------------------------------------------------------
          if (SystemInformation.UserInformation.Ruolo == RUOLI.Amministratore || SystemInformation.UserInformation.Ruolo == RUOLI.SuperUtente)
          {
            AMenu = this.SideMenu.AddChild(this.MenuLaterale? "Amministrazione" : "Amm.",
                                          { 
                                            IdMenu      : TIdSideMenu.AMMINISTRAZIONE, 
                                            IconCfg     : "fa-folder-open", 
                                            ColorType   : "bg-danger" 
                                          });
            
            // AMenu2 = AMenu.AddChild("Importa xml fattura",
            //                         { 
            //                           IdMenu          : TIdSideMenu.AMM_IMPORTA_XML_FATT, 
            //                           IconCfg         : "fa-angle-right", 
            //                           IconCfgExpanded : "fa-angle-up", 
            //                           IconCfgShrinked : "fa-angle-down"
            //                         });   
            AMenu2 = AMenu.AddChild("Fatture elettroniche",
                                    {
                                      IdMenu           : TIdSideMenu.AMM_FATTURE_ELETTRONICHE, 
                                      IconCfg          : "fa-angle-right", 
                                      IconCfgExpanded  : "fa-angle-up", 
                                      IconCfgShrinked  : "fa-angle-down"
                                    });
            
                    AMenu2.AddChild("Esporta verso SdI",
                                    { 
                                      IdMenu          : TIdSideMenu.AMM_ESPORTA_SDI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    }); 


                    AMenu2.AddChild("Portale fatt.elettroniche",
                                    { 
                                      IdMenu          : TIdSideMenu.AMM_PORTALE_FATT_ELETTRONICHE, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });

                    AMenu2.AddChild("Importa fatt. passive",
                                    { 
                                      IdMenu          : TIdSideMenu.AMM_IMPORTAZIONE_FATT_PASS, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });

                    AMenu2.AddChild("Scarica file ZIP con fatt. e note di credito",
                                    { 
                                      IdMenu          : TIdSideMenu.AMM_SCARICO_FILE_ZIP_CON_XML, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
                                    
              AMenu2 = AMenu.AddChild("Gestione fatture passive",
                              { 
                                IdMenu          : TIdSideMenu.GESTORE_FATTURE_PASSIVE, 
                                IconCfg         : "fa-angle-right", 
                                IconCfgExpanded : "fa-angle-up", 
                                IconCfgShrinked : "fa-angle-down"
                              });
                              
              AMenu2 = AMenu.AddChild("Solleciti",
                                      { 
                                        IdMenu          : TIdSideMenu.SOLLECITI, 
                                        IconCfg         : "fa-angle-right", 
                                        IconCfgExpanded : "fa-angle-up", 
                                        IconCfgShrinked : "fa-angle-down"
                                      });
                                              
            AMenu2 = AMenu.AddChild("Log email inviate",
                                    { 
                                      IdMenu          : TIdSideMenu.LOG_EMAIL_INVIATE, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });

          }
          
//--------------------------------- Nascondere tranne che ad Amministratore e Superutente _BEGIN_ ------------------------------------------------------------------------------------
          
          if (SystemInformation.UserInformation.Ruolo == RUOLI.Amministratore || SystemInformation.UserInformation.Ruolo == RUOLI.SuperUtente)
          {
            AMenu = this.SideMenu.AddChild(this.MenuLaterale? "Configurazioni" : "Config.",
                                          { 
                                            IdMenu      : TIdSideMenu.CONFIGURAZIONI, 
                                            IconCfg     : "fa-pencil", 
                                            ColorType   : "bg-success" 
                                          }); 
            AMenu2 = AMenu.AddChild("Generale",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_GENERALE, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            AMenu2.AddChild("Dati Fiscali",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_DATI_FISCALI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            AMenu2.AddChild("Unità di Misura",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_UNITA_DI_MISURA, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            AMenu2.AddChild("Voci Preventivi Predefinite",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_VOCI_PREVENTIVI_PREDEFINITE, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            AMenu2.AddChild("Titoli",
                                    {
                                      IdMenu          : TIdSideMenu.CFG_TITOLI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            AMenu2.AddChild("Testi",
                                    {
                                      IdMenu          : TIdSideMenu.CFG_TESTI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            AMenu2.AddChild("Zone",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_ZONE, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            AMenu2.AddChild("Fattura elettronica",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_FATTURA_ELETTRONICA, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    }); 
            if(this.VisibilitaGestoreMailSolleciti)
            {
              AMenu2.AddChild("Mail segnalazioni",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_MAIL_SOLLECITI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
            }
            
            if (SystemInformation.UserInformation.Ruolo == RUOLI.Amministratore || SystemInformation.UserInformation.Ruolo == RUOLI.SuperUtente)
            {
              AMenu2.AddChild("Config. pannello amm.",
                                { 
                                  IdMenu          : TIdSideMenu.CFG_PANNELLO_AMM, 
                                  IconCfg         : "fa-angle-right", 
                                  IconCfgExpanded : "fa-angle-up", 
                                  IconCfgShrinked : "fa-angle-down"
                                });
            }
              AMenu2 = AMenu.AddChild("Pagamenti",
                                    { 
                                      IdMenu      : TIdSideMenu.CFG_PAGAMENTI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    }); 
              AMenu2.AddChild("Cond. pagamento",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_COND_PAGAMENTO, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
              AMenu2.AddChild("Mod. pagamento",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_MOD_PAGAMENTO, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
              AMenu2.AddChild("Tip. movimenti",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_CAT_MOVIMENTI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });
              AMenu2.AddChild("Causali",
                                    { 
                                      IdMenu          : TIdSideMenu.CFG_CAUSALI, 
                                      IconCfg         : "fa-angle-right", 
                                      IconCfgExpanded : "fa-angle-up", 
                                      IconCfgShrinked : "fa-angle-down"
                                    });

              AMenu2 = AMenu.AddChild("Prodotti",
                              { 
                                IdMenu          : TIdSideMenu.CFG_PRODOTTI, 
                                IconCfg         : "fa-angle-right", 
                                IconCfgExpanded : "fa-angle-up", 
                                IconCfgShrinked : "fa-angle-down"
                              });
              AMenu2.AddChild("Settori",
                              { 
                                IdMenu          : TIdSideMenu.CFG_SETTORI, 
                                IconCfg         : "fa-angle-right", 
                                IconCfgExpanded : "fa-angle-up", 
                                IconCfgShrinked : "fa-angle-down"
                              });
                AMenu2.AddChild("Magazzini",
                                { 
                                  IdMenu          : TIdSideMenu.CFG_MAGAZZINI, 
                                  IconCfg         : "fa-angle-right", 
                                  IconCfgExpanded : "fa-angle-up", 
                                  IconCfgShrinked : "fa-angle-down"
                                });
          }

//--------------------------------- Nascondere tranne che ad Amministratore e Superutente __END__ ------------------------------------------------------------------------------------


          if (SystemInformation.UserInformation.Ruolo == RUOLI.Amministratore || SystemInformation.UserInformation.Ruolo == RUOLI.SuperUtente)
          {
            AMenu = this.SideMenu.AddChild(this.MenuLaterale? "Gestione" : "Gest.",
                                          { 
                                            IdMenu      : TIdSideMenu.AMM_GESTIONE, 
                                            IconCfg     : "fa-user", 
                                            ColorType   : "bg-warning" 
                                          });
            AMenu.AddChild("Gestione utenti",
                            { 
                              IdMenu          : TIdSideMenu.AMM_GESTIONE_UTENTI, 
                              IconCfg         : "fa-angle-right", 
                              IconCfgExpanded : "fa-angle-up", 
                              IconCfgShrinked : "fa-angle-down"
                            });
            }
            if (SystemInformation.UserInformation.Ruolo == RUOLI.Amministratore)
            {
              AMenu.AddChild("Gestione messaggi",
                              {
                                IdMenu          : TIdSideMenu.GESTIONE_PROGRAMMAZIONE, 
                                IconCfg         : "fa-angle-right", 
                                IconCfgExpanded : "fa-angle-up", 
                                IconCfgShrinked : "fa-angle-down"
                              });
            }
          if (SystemInformation.UserInformation.Ruolo == RUOLI.Amministratore)
          {
            AMenu.AddChild("Azioni amministratore",
                            {
                              IdMenu          : TIdSideMenu.AMM_AZIONI, 
                              IconCfg         : "fa-angle-right", 
                              IconCfgExpanded : "fa-angle-up", 
                              IconCfgShrinked : "fa-angle-down"
                            });
          }
       }
    },

    beforeMount()
    {
      this.CreaMenu()
    }

}
</script>
