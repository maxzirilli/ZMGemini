<template>
    <VUEConfirm :Popup="PopupAnnulla" :Richiesta="'Annullare le modifiche effettuate ed uscire dalla pagina?'" @onClickConfermaPopup="ConfermaAnnulla" @onClickChiudiPopup="AnnullaAnnulla">
    </VUEConfirm> 
    <div>           
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
                  <VUEAppContentAggiungiMessaggio @OnClickApriPopup="OnClickChildAnnulla" @OnClickConfermaModifiche="OnClickChildConfermaModifiche"></VUEAppContentAggiungiMessaggio>
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
import VUEAppContentAggiungiMessaggio from './ContentPages/VUEAppContentAggiungiMessaggio.vue';
import VUEConfirm from '@/components/VUEConfirm.vue'
export default 
{
      name: "VUEMessaggio",
      data() 
      {
       return {
                Altezza : document.documentElement.clientHeight,
                TitoloPagina : 'Messaggio',
                PopupAnnulla : false,
                LasciaPagina : false,
                Destinazione : ''
              };
      },
      components: 
      {
         VUEAppHeader,
         VUEAppSideMenu,
         VUEAppContentAggiungiMessaggio,
         VUEConfirm
      },
      methods: 
      {
        OnClickChildAnnulla()
        {
          this.PopupAnnulla = true;
        },
        OnClickChildConfermaModifiche()
        {
          this.LasciaPagina = true
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
          this.Dstinazione = ''
        },
        getDimensions()
        {
            this.Altezza = document.documentElement.clientHeight;
        },
        AggiornaTipoPagina()
        {
          if (this.$route.params.pagina == 'nuovo')
            this.TitoloPagina = 'Nuovo Messaggio'
          else this.TitoloPagina = 'Aggiorna Messaggio'
        }
      },
      beforeMount()
      {
        this.AggiornaTipoPagina();
      },
      beforeRouteLeave(to, from, next)
      {
        if (!this.LasciaPagina)
        {
            this.PopupAnnulla = true
            this.Destinazione = to
            next(false)
        }
        else
        {
          next()
        }
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
  };
  </script>