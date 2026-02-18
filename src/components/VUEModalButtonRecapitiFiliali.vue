<template>
  <VUEModal v-if="PopupRecapitiFiliali" 
            :PathLogo="require('@/assets/images/LogoGemini2.png')"
            :Programma="NomeProgramma"
            :Titolo="'Recapiti Filiali '" 
            :Altezza="'500px'" 
            :Larghezza="'1200px'"
            @onClickChiudiModal="OnClickAnnullaRecapitiFiliali"
            @onClickConfermaModal="OnClickConfermaRecapitiFiliali">
    <template v-slot:Body>
      
        <div style="display: flex; justify-content: center;">
        <input
          type="text"
          class="input-sm form-control"
          placeholder="CERCA PER NOME O INDIRIZZO"
          v-model="FiltroFiliali"
          style="width: 50%;"
        >
      </div>

        <div style="clear:both;width:1%;height:10px">&nbsp;</div>
        
        <div class="row wrapper">
        <section class="panel panel-default" style="background-color:white;">
          <div class="table-responsive" style="max-height:370px;width:100%;height: 60%;">
            <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
              <thead>
                <tr>
                  <th style="width:7%;position: sticky; top: 0;"></th>
                  <th style="width:22%;position: sticky; top: 0;">Nome</th>
                  <th style="width:43%;position: sticky; top: 0;">Indirizzo</th>
                  <th style="width:15%;position: sticky; top: 0;">Civ.</th>
                  <th style="width:10%;position: sticky; top: 0;">Comune</th>
                  <th style="width:10%;position: sticky; top: 0;">Prov.</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="Filiale in FilialiFiltrate" :key="Filiale.CHIAVE">
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    <input type="radio" :value="Filiale"
                                        :checked="Filiale.CHIAVE == FilialeSelezionata.CHIAVE && FiltroFiliale == ''"
                                        name="filiale_selezionata"
                                        v-model="FilialeSelezionata"
                                        style="height: 20px;" class="form-control">
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ GestisciStringa(Filiale.NOME) }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ GestisciStringa(Filiale.INDIRIZZO) }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ Filiale.NR_CIVICO }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ GestisciStringa(Filiale.COMUNE) }}
                  </td>
                  <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                    {{ SelectProvincia(Filiale.PROVINCIA)?.TARGA }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:20%" @click="OnClickAnnullaRecapitiFiliali" data-dismiss="modal">Annulla</button>
      <button v-if="CanConfirm()" type="button" class="btn btn-success" style="float:right;font-weight:bold;width:20%" @click="OnClickConfermaRecapitiFiliali" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal>

  <div>
    <button class="btn btn-sm btn-info" :disabled="IsDisabled" :style="styleForButton" @click="OnClickCopiaRecapitoFiliale">Copia Recapito Filiale</button>
  </div>        
</template>
  
<script>
  import VUEModal from '../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
  import { SystemInformation, NOME_PROGRAMMA }  from '@/SystemInformation';
  import { TSchedaGenerica }    from './../../../../../../../Librerie/VUE/ZSchedaGenerica.js';

  export default
  {
    components: 
    {
      VUEModal
    },

    data()
    {
      return  {
                PopupRecapitiFiliali    : false,
                Filiali                 : [],
                Province                : SystemInformation.Configurazioni.Province,
                FilialeSelezionata      : -1,
                FiltroFiliali           : '',
                NomeProgramma           : NOME_PROGRAMMA

              }
    },
    props : ['IdCliente', 'styleForButton', 'IsDisabled'],
    emits:  ['filiale-selected'],
    computed :
    {
      FilialiFiltrate :
      {
        get()
        {
          var FiltroFiliale = this.FiltroFiliali.toUpperCase().trim();

          var FilialiFiltrate = [];

          if(FiltroFiliale != '')
          {
            FilialiFiltrate = this.Filiali.filter(
                                                    function(Filiale)
                                                    {                                     
                                                      if(Filiale.NOME.toUpperCase().includes(FiltroFiliale) || Filiale.INDIRIZZO.toUpperCase().includes(FiltroFiliale))
                                                        if(!TSchedaGenerica.DisponiFromBoolean(Filiale.FILIALE_DISATTIVATA))
                                                          return true
                                                      return false
                                                    }
                                                 )
          }
          else
          {
            for(var Filiale of this.Filiali)
              if(!TSchedaGenerica.DisponiFromBoolean(Filiale.FILIALE_DISATTIVATA) )
                FilialiFiltrate.push(Filiale);
          }
          
          return FilialiFiltrate;
        }
      },

      // CurrentOggettoModalCaricamentoDati :
      // {
      //   get()
      //   {
      //     if(this.OggettoModalCaricamentoDati.DatoPassato)
      //       return this.OggettoModalCaricamentoDati.Width
      //     return false
      //   }
      // }
    },
  
    methods :
    {
      GestisciStringa(Valore)   
      {
        return TSchedaGenerica.DisponiFromString(Valore);
      },

      CanConfirm()
      {
        if(this.FilialeSelezionata != -1)
          return true
        return false
      },

      CaricaFiliali()
      {
        const Self = this;
        SystemInformation.AdvQuery.GetSQL('Clienti', { CHIAVE_CLIENTE : this.IdCliente }, 
                                          function (Results) 
                                          {
                                            let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "Lista");
                                            if (ArrayInfo != undefined) 
                                            {
                                              Self.Filiali = ArrayInfo;

                                              // if(OnSuccess != undefined)
                                              //   OnSuccess(); 
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere lista delle filiali del cliente');
                                          },
                                          function (HTTPError, SubHTTPError, Response) 
                                          {
                                            SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                          },
                                          'ListaFiliali');

      },

      SelectProvincia(ChiaveProvincia)
      {
        for(const Provincia of this.Province)
          if(Provincia.CHIAVE == ChiaveProvincia)
            return Provincia;
      },

      OnClickCopiaRecapitoFiliale()
      {
        // this.CaricaFiliali();
        this.FilialeSelezionata = -1;
        this.PopupRecapitiFiliali = true;
      },
      OnClickAnnullaRecapitiFiliali()
      {
        this.PopupRecapitiFiliali = false;
      },
      OnClickConfermaRecapitiFiliali()
      {
        this.$emit('filiale-selected', this.FilialeSelezionata);           

        this.PopupRecapitiFiliali = false;
      }
    },

    watch:
    {
      FiltroFiliali()
      {
        this.FilialeSelezionata = -1
      }
    },

    // Lifecycle Hooks
    mounted()
    {
      this.CaricaFiliali();
    },

    updated()
    {
      this.CaricaFiliali();
    },

  }
</script>
  