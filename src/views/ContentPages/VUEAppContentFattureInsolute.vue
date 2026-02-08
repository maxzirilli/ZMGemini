<template>
  
  <div @keydown="KeyDownF2($event)">
    <div class="ZMNuovaRigaScheda" style="float:left;width:100%">
      
      <div>  
        <div style="width:10%;float:left;font-size:16px;margin-top:5px"><label>Ragione sociale</label></div>
        <div style="float:left;width:15%;margin-top:5px">
          <VUEInputClienti @onUpdate="newValue => Filtri.ChiaveCliente = newValue" v-model ="Filtri.ChiaveCliente" :ShowImage="true"/>
        </div>
      </div>
      
      <div style="padding-left:600px">
        <div style="width:30%">
          
          <div style="width:60%;float:left;font-size:16px;margin-top:5px">
            <label>Mese di intervento</label>
          </div>
          
          <div style="float:left;width:40%;margin-top:5px">
            <VUEInputMese v-model="Filtri.MeseVisita" NessunaSelezione = "true"/>
          </div>
        
        </div>
      </div>

      <div style="float: right; width: 15%">
        <a @click="Cerca()" style="width:100%" class="btn btn-s-md btn-info">Cerca [F2]</a>
      </div>

    </div>
  </div>
  
  <div class="ZMCorpoSchedeDati" style="float:left;margin-top:5px">
    <div class="ZMSeparatoreFiltri">&nbsp;</div>
    <div class="ZMSeparatoreScheda">
      <text style="font-weight: bold;">Clienti con almeno una fattura insoluta</text>
    </div>

    <div class="ZMSchedeMancanti">
      <label> N° clienti con fatture insolute: {{ VettoreRidottoClienti.length }}</label>
    </div>

    <div class="panel panel-default" v-for="Cliente in VettoreRidottoClienti" :key="Cliente.CHIAVE">
      
      <div class="panel-heading" style="min-height:50px; display: flex; align-items: center; cursor:pointer;" @click="OnClickEspandiPagina(Cliente)">
        <div style="width:3%;margin-top:5px">
          <a>
            <img src="../../assets/images/IconeAlbero/Cliente2.png" style="width:25px;height:25px;float:left;margin-top:-3px;margin-right:2px">
          </a>
        </div>
        <div style="width: 32%; margin-top: -2px;">
          <div style="width: 60%; height: 100%; cursor: pointer; font-weight: initial; font-size: 12px; float:left"></div>
          <b>{{Cliente.CodiceCliente + ' - ' + Cliente.RagioneSociale }}<br/></b>
          <text>{{ 'Privato ' }}</text>
        </div>
        <div style="height:5px;clear:both;">&nbsp;</div>
      </div>

      <div v-if="Cliente.Grafica.ClienteEspanso" style="background-color:rgb(230 230 230);font-weight:bold;margin-top:3px" class="panel-heading">
        <div class="panel-collapse collapse in">
          <div class="panel-body text-sm" style="background-color:white">
            <table class="table table-striped b-t b-light">
              <thead>
                <tr>
                  <th colspan="6" style="text-align: center;">Fatture insolute</th>
                </tr>
                <tr>
                  <th style="background:#b3dbff; width: 1%;">Tipo fattura</th>
                  <th style="background:#b3dbff; width: 20%;">Nº fattura</th>
                  <th style="background:#b3dbff; width: 20%;">Data di scadenza</th>
                  <th style="background:#b3dbff; width: 20%;">Data di inizio segnalazione</th>
                  <th style="background:#b3dbff; width: 20%;">Totale</th>
                  <th style="background:#b3dbff; width: 20%;">Da pagare</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="Fattura in Cliente.LsFattureInsolute" :key="Fattura.Numero">
                  <td style="display: flex; align-items: center; justify-content: center;">
                    <img v-if="Fattura.IsAFatturaPregressa" src="../../assets/images/IconeAlbero/FatturaPregressa.png" style="width:25px;height:25px;margin:auto">
                    <img v-else-if="Fattura.DaBanco" src="../../assets/images/IconeAlbero/FatturaDaBanco2.png" style="width:25px;height:25px;margin:auto">
                    <img v-else src="../../assets/images/IconeAlbero/Fattura2.png" style="width:25px;height:25px;margin:auto">
                  </td>
                  <td><text style="font-weight: initial">{{ Fattura.Numero == 0 ? 'Avviso di fattura ' + Fattura.Chiave : Fattura.Numero }}</text></td>
                  <td><text style="font-weight: initial">{{ Fattura.Data ? FormattaData(Fattura.Data) : '' }}</text></td>
                  <td><text style="font-weight: initial">{{ Fattura.Data ? FormattaData(CalcolaDataScadenzaFattura(Cliente, Fattura)) : '' }}</text></td>
                  <td><text style="font-weight: initial">{{ FormattaImporto(Fattura.Totale) }}</text></td>
                  <td><text style="font-weight: initial">{{ FormattaImporto(Fattura.TotDaPagare) }}</text></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';
import VUEInputMese from '@/components/InputComponents/VUEInputMese.vue';
import { SystemInformation } from '@/SystemInformation.js';
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js';
import { TZEconomicFunct } from '../../../../../../../../Librerie/VUE/ZEconomicFunct.js';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js';

export default
{
  props : ['Altezza'],
  components: { 
                VUEInputClienti,
                VUEInputMese,
              },

  data()
  {
    return {
             Filtri                  : {
                                         ChiaveCliente       : -1, 
                                         MeseVisita          : -1,
                                       },
             LsClienti               : [],
             MotivazioneSospensione  : '',
           }
  },

  computed :
  {
    ButtonDisabilitato:
    {
      get()
      {
        return this.MotivazioneSospensione.length == 0;
      }
    },
  
    // VettoreRidottoClienti : 
    // {
    //   get()
    //   {
    //       let Result = [];
    //       for(let i = 0; i < this.LsClienti.length; i++)
    //       {   
    //         if(this.LsClienti[i].LsContratti.length != 0)
    //             Result.push(this.LsClienti[i]);
    //       }
    //       return Result;
    //   }  
    // }
  },
  methods: 
  {
    FormattaImporto(Valore)
    {
      return TZEconomicFunct.EuropeanCurrencyFormat(Valore)
    },

    FormattaData(Data)
    {
      return TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Date.parse(Data)));
    },

    CalcolaDataScadenzaFattura(Fattura)
    {
        return TZDateFunct.SumMonth(new Date(Date.parse(Fattura.Data)), SystemInformation.Configurazioni.Impostazioni.NUMERO_MESI_PER_FATTURE_INSOLUTE_PER_PRIVATI);
    },

    KeyDownF2(event)
    {
      if(event.which == 113)
        this.Cerca()
    },

    Cerca()
    {
      var Self = this;
      var OggettoFiltro = {}; 
      this.LsClienti = [];

      if(Self.Filtri.ChiaveCliente != -1)
        OggettoFiltro.CHIAVE_CLIENTE = Self.Filtri.ChiaveCliente;
      else
        OggettoFiltro.CHIAVE_CLIENTE = -1;

      if(Self.Filtri.MeseVisita != -1)
      {
        OggettoFiltro.MeseVisita = Self.Filtri.MeseVisita;
        let VisitaSemestrale = parseInt(Self.Filtri.MeseVisita) + 6
        OggettoFiltro.MeseVisitaSemestrale = VisitaSemestrale > 12 ? (VisitaSemestrale - 12).toString() : VisitaSemestrale.toString();

      }

      SystemInformation.AdvQuery.GetSQL('Clienti', OggettoFiltro,
                                    function(Results)
                                    {
                                      var PrecedenteChiaveCliente = -1;
                                      var ObjectCliente = null;
                                      
                                      let ArrayLsClienti        = SystemInformation.AdvQuery.FindResults(Results,"LsClientiInsoluti");

                                      if(ArrayLsClienti.length != 0)
                                      {
                                        ArrayLsClienti.forEach(function(Cliente)
                                        {
                                          if(PrecedenteChiaveCliente != Cliente.CHIAVE)
                                          {
                                            ObjectCliente = {
                                                              Chiave            : TSchedaGenerica.DisponiFromInteger(Cliente.CHIAVE),
                                                              CodiceCliente     : TSchedaGenerica.DisponiFromInteger(Cliente.CODICE),
                                                              RagioneSociale    : TSchedaGenerica.DisponiFromString(Cliente.RAGIONE_SOCIALE),
                                                              Grafica           : {
                                                                                    ClienteEspanso  : false,
                                                                                    ClienteVisibile : true,
                                                                                  },
                                                              LsFattureInsolute : [],
                                                            }
                                          
                                            PrecedenteChiaveCliente = Cliente.CHIAVE;
                                            Self.LsClienti.push(ObjectCliente);
                                          }

                                          // Cliente.DATA = new Date(Cliente.DATA);
                                          
                                          // ObjectCliente.LsFattureInsolute.push({
                                          //                                       Numero              : TSchedaGenerica.DisponiFromListIndex(Cliente.NUMERO),
                                          //                                       Chiave              : TSchedaGenerica.DisponiFromDate(Cliente.CHIAVE_FATTURA),
                                          //                                       Data                : TSchedaGenerica.DisponiFromDate(Cliente.DATA),
                                          //                                       DaBanco             : TSchedaGenerica.DisponiFromBoolean(Cliente.DA_BANCO),
                                          //                                       IsAFatturaPregressa : TSchedaGenerica.DisponiFromBoolean(Cliente.IS_A_FATTURA_PREGRESSA),
                                          //                                       Totale              : TSchedaGenerica.DisponiFromInteger(Cliente.TOT_FATTURA) / 100,
                                          //                                       TotDaPagare         : TSchedaGenerica.DisponiFromInteger(Cliente.TOT_DA_PAGARE) / 100,
                                          //                                     })
                                        })

                                      }
                                    },
                                    function(HTTPError,SubHTTPError,Response)
                                    {
                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                    },
                                    'Insoluti');
    },

    OnClickEspandiPagina(Cliente) 
    {
      var Indice = this.LsClienti.indexOf(Cliente);
      // var FatturePregresseControllate = false
      let Self = this
      Cliente.LsFattureInsolute = []
      SystemInformation.AdvQuery.GetSQL('Clienti', {CHIAVE : Cliente.Chiave},
                                        function(Results)
                                        {
                                          let ArrayFattureInsolute = SystemInformation.AdvQuery.FindResults(Results,"LsFattureInsolute");
                                          if(ArrayFattureInsolute.length != 0)
                                          {
                                            ArrayFattureInsolute.forEach(function(FattureInsolute)
                                            {
                                              FattureInsolute.DATA = new Date(FattureInsolute.DATA);
                                              Cliente.LsFattureInsolute.push({
                                                                                Numero              : TSchedaGenerica.DisponiFromListIndex(FattureInsolute.NUMERO),
                                                                                Chiave              : TSchedaGenerica.DisponiFromDate(FattureInsolute.CHIAVE_FATTURA),
                                                                                Data                : TSchedaGenerica.DisponiFromDate(FattureInsolute.DATA),
                                                                                DaBanco             : TSchedaGenerica.DisponiFromBoolean(FattureInsolute.DA_BANCO),
                                                                                IsAFatturaPregressa : TSchedaGenerica.DisponiFromBoolean(FattureInsolute.IS_A_FATTURA_PREGRESSA),
                                                                                Totale              : TSchedaGenerica.DisponiFromInteger(FattureInsolute.TOT_FATTURA) / 100,
                                                                                TotDaPagare         : TSchedaGenerica.DisponiFromInteger(FattureInsolute.TOT_DA_PAGARE) / 100,
                                                                              })

                                            })
                                          }
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'Insoluti');

      this.LsClienti.forEach(function(Cliente)
      {
        if(Self.LsClienti.indexOf(Cliente) != Indice)
          Cliente.Grafica.ClienteEspanso = false;
      })

      this.LsClienti[Indice].Grafica.ClienteEspanso = !this.LsClienti[Indice].Grafica.ClienteEspanso;

      if(this.LsClienti[Indice].Grafica.ClienteEspanso)
      {
        this.MotivazioneSospensione = '';

        // for(let i = 0; i < Cliente.LsFattureInsolute.length; i++)
        // {
        //   if(Cliente.LsFattureInsolute[i].Numero == -1)
        //   {
        //     FatturePregresseControllate = true
        //     break
        //   }
        // }

        // if(!FatturePregresseControllate)
        //   SystemInformation.AdvQuery.GetSQL('Clienti', { CHIAVE : Cliente.Chiave },
        //                                 function(Results)
        //                                 {
        //                                   let ArrayAvvisiFattura = SystemInformation.AdvQuery.FindResults(Results,"GetAvvisiFattura");
        //                                   if(ArrayAvvisiFattura.length != 0)
        //                                   {
        //                                     for(let i = 0; i < ArrayAvvisiFattura.length; i++)
        //                                     {
        //                                       if(ArrayAvvisiFattura[i].TOT_DA_PAGARE != undefined && ArrayAvvisiFattura[i].TOT_DA_PAGARE != 0)
        //                                       {
        //                                         let Trovato = false
        //                                         for(let j = 0; j < Cliente.LsFattureInsolute.length; j++)
        //                                           if(Cliente.LsFattureInsolute[j].Chiave == ArrayAvvisiFattura[i].CHIAVE)
        //                                           {
        //                                             Trovato = true
        //                                             break;
        //                                           }
                                                
        //                                         if(!Trovato)
        //                                           Cliente.LsFattureInsolute.push({
        //                                                                             Chiave              : TSchedaGenerica.DisponiFromInteger(ArrayAvvisiFattura[i].CHIAVE),
        //                                                                             Numero              : TSchedaGenerica.DisponiFromInteger(ArrayAvvisiFattura[i].NUMERO),
        //                                                                             Data                : TSchedaGenerica.DisponiFromString(ArrayAvvisiFattura[i].DATA),
        //                                                                             DaBanco             : TSchedaGenerica.DisponiFromBoolean(ArrayAvvisiFattura[i].DA_BANCO),
        //                                                                             IsAFatturaPregressa : false ,
        //                                                                             Totale              : TSchedaGenerica.DisponiFromInteger(ArrayAvvisiFattura[i].TOT_FATTURA) / 100,
        //                                                                             TotDaPagare         : TSchedaGenerica.DisponiFromInteger(ArrayAvvisiFattura[i].TOT_DA_PAGARE) / 100,
        //                                                                           })
        //                                       }
        //                                     }
        //                                   }
        //                                 },
        //                                 function(HTTPError,SubHTTPError,Response)
        //                                 {
        //                                   SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
        //                                 },'Insoluti');
        
      }
    },

    OnChangeMese()
    {
      this.VettoreDocumentiDaGenerare = {
                                          ArrayFatture   : []
                                        }

      this.VettorePreventivi             = []
      this.FattureDaCostruire            = []
    },
    
  },

  mounted()
  {
    this.Cerca()
  },

}

</script>
<style>
</style>