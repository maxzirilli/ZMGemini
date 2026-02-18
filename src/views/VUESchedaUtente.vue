<template>
    <div>
      <VUEConfirm :Popup="PopupAnnulla" 
                  :PathLogo="require('@/assets/images/LogoGemini2.png')"
                  :Programma="NomeProgramma"
                  :Richiesta="'Annullare le modifiche effettuate ed uscire dalla pagina?'" @onClickConfermaPopup="ConfermaAnnulla" @onClickChiudiPopup="AnnullaAnnulla">
      </VUEConfirm>      
       <section>
         <VUEAppHeader></VUEAppHeader>         
         <section>
           <section class="hbox stretch">
             <VUEAppSideMenu :Altezza="Altezza"></VUEAppSideMenu>  
             <section id="content">
               <section class="vbox"  style="display:block;">          
                <section class="padder" style="overflow-y: scroll;" :style="{height: (Altezza - 50) + 'px'}">
                  <div v-if="TitoloPagina != ''" class="breadcrumb no-border no-radius b-b b-light pull-in">
                    <span class="ZMTitoloContenuti">{{TitoloPagina}}</span>
                  </div>
                  <VUEAppContentSchedaUtente @OnClickApriPopup="OnClickChildAnnulla" @OnClickConfermaModifiche="OnClickChildConfermaModifiche"></VUEAppContentSchedaUtente>
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
import VUEAppHeader from '@/components/FrameComponentsMultiPurpose/VUEAppHeader.vue';
import VUEAppSideMenu from '../components/FrameComponentsMultiPurpose/VUEAppSideMenu.vue';
import VUEAppContentSchedaUtente from './ContentPages/VUEAppContentSchedaUtente.vue';
import VUEConfirm from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEConfirm.vue';
import { NOME_PROGRAMMA } from '@/SystemInformation';
  
  export default 
  {
      name: "VUESchedaUtente",
      data() 
      {
       return {
                Altezza       : document.documentElement.clientHeight,
                TitoloPagina  : 'prova',
                PopupAnnulla  : false,
                LasciaPagina  : false,
                Destinazione  : '',  
                NomeProgramma : NOME_PROGRAMMA         
              };
      },
      components: 
      {
         VUEAppHeader,
         VUEAppSideMenu,
         VUEAppContentSchedaUtente,
         VUEConfirm        
      },
      computed:
      {
      },
      methods: 
      {
        OnClickChildAnnulla()
        {
          this.PopupAnnulla = true
        },
        OnClickChildConfermaModifiche()
        {
          this.LasciaPagina = true
        },
        getDimensions()
        {
            this.Altezza = document.documentElement.clientHeight;
        },
        AggiornaTipoPagina()
        {
          if (this.$route.params.pagina == 'nuovo')
            this.TitoloPagina = 'Nuovo Profilo'
          else this.TitoloPagina = 'Modifica Profilo'
        },
        ConfermaAnnulla()
        {
          this.LasciaPagina = true
          if (this.Destinazione == '')
            this.$router.go(-1)
          else
            this.$router.push(this.Destinazione)
        },
        AnnullaAnnulla()
        {
          this.PopupAnnulla = false
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
      beforeRouteLeave(to, from, next)
      {
        if (!this.LasciaPagina)
        {
            this.PopupAnnulla = true
            this.Destinazione = to.fullPath
            next(false)
        }
        else
        {          
          next()
        }
      },
      unmounted() 
      {
        window.removeEventListener('resize', this.getDimensions);
      },
  };
  </script>