<template>
  <div @keydown="KeyDownF2($event)">
    <text style="float:left;margin-top:1px;margin-bottom:5px;font-weight: bold;font-size:20px;width:50%">GESTIONE FATTURE PASSIVE</text>
    
    <div class="ZMNuovaRigaScheda" style="float:left;width:100%">
      <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
        <label>Dal</label>
      </div>
      <div style="float:left;width:10%;">
        <input type="date" class="form-control" v-model="FilterFattura.DallaData"/>
      </div>

      <div style="float:left;width:1%;">&nbsp;</div>

      <div style="float:left;font-size:14px;margin-top: 5px;width:5%;text-align: right; padding-right: 15px;">
        <label>Al</label>
      </div>
      <div style="float:left;width:10%;">
        <input type="date"  class="form-control" v-model="FilterFattura.AllaData"/>
      </div> 

      <div style="float:left;width:1%;">&nbsp;</div>

      <div style="float:left;font-size:14px;padding-top: 5px;width:8%;text-align: right; padding-right: 15px;">
        <label>Fornitore</label>
      </div>
      <div style="float:left;width:18%">
        <VUEInputFornitore @onUpdate="newValue => FilterFattura.Fornitore = newValue" v-model="FilterFattura.Fornitore"/> 
      </div> 

      <div style="float:left;width:1%;">&nbsp;</div>

      <div style="float: left;font-size:14px;padding-top: 5px;width:10%;text-align: right; padding-right: 15px;">
        <label style="margin-bottom: 0px;">Tipo fatture</label>
      </div>

      <div style="float: left; width: 8%; margin-right: 20px;">
        <select style="float:left;width:100%;margin-right:5px" class="form-control" v-model="FilterFattura.Tipologia">
          <option value="Tutte">Tutte</option>
          <option value="Presenti">Gestite</option>           
          <option value="NonPresenti">Da gestire</option>           
        </select>
      </div>

      <div style="display: flex; max-width: 300px; margin-left: 10px;">
        <a @click="Carica()" style="width:100%" class="btn btn-s-md btn-info">Cerca [F2]</a>
      </div>

    </div>

    <div class="ZMSeparatoreFiltri">&nbsp;</div>
    <div class="ZMSeparatoreFiltri">&nbsp;</div>

    <div class="ZMCorpoSchedeDati" style="float:left;margin-top:5px">
      <div class="panel-default" v-for="OggettoFatturaPassiva in LSFatturePassive" :key="OggettoFatturaPassiva.Chiave">
        <div class="panel-group m-b" style="font-size:14px">
          <div class="panel panel-default" style="background-color:#d0e9ff">
          <div
            class="panel-heading"
            @click="OnClickEspandiFatturaPassiva(OggettoFatturaPassiva)"
            style="cursor:pointer;min-height:55px;"
          >
            <div style="width:5%;float:left;margin-top:5px">
              <span>
                <img
                  src="@/assets/images/IconeAlbero/FatturaPassiva2.png"
                  style="width:25px;height:25px;float:left;margin-top:-3px;margin-right:2px"
                />
              </span>
            </div>

            <div style="width:30%;float:left;margin-top:5px;">
              <span style="font-weight:bold;white-space: nowrap;">
                Fattura passiva n.&nbsp;
              </span>
              <span style="font-weight:normal;overflow-x:hidden">
                {{ OggettoFatturaPassiva.Numero }}
              </span>
            </div>

            <div style="width:25%;float:left;margin-top:5px;">
              <span style="font-weight:bold;white-space: nowrap;">
                Data:&nbsp;
              </span>
              <span style="font-weight:normal;overflow-x:hidden">
                {{ OggettoFatturaPassiva.Data }}
              </span>
            </div>

            <div style="width:25%;float:left;margin-top:5px;">
              <span style="font-weight:bold;white-space: nowrap;">
                Fornitore:&nbsp;
              </span>
              <span style="font-weight:normal;overflow-x:hidden">
                {{ OggettoFatturaPassiva.RagioneSocialeFornitore }}
              </span>
            </div>

            <div
              v-if="OggettoFatturaPassiva.DaGestire"
              style="width:10%;float:right;margin-top:5px;"
            >
              <span style="font-weight:bold;white-space: nowrap;">
                Fattura da gestire
                <img
                  src="@/assets/images/IconeAlbero/LuciEmergenza.png"
                  style="width:20px;height:25px;float:right;margin-top:-3px;margin-right:2px"
                />
              </span>
            </div>
          </div>

          <div
            v-if="OggettoFatturaPassiva.Espanso"
            style="background-color:rgb(230 230 230);font-weight:bold;margin-top:3px"
            class="panel-heading"
          >
            <div class="panel-collapse collapse in">
              <div class="panel-body text-sm" style="background-color:white">
                <table class="table table-striped b-t b-light">
                  <thead>
                    <tr>
                      <th style="width:10%;">CODICE</th>
                      <th style="width:45%;">DESCRIZIONE</th>
                      <th style="width:45%;">PRODOTTO</th>
                      <th style="width:5%;">GESTITO</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr
                      v-for="VoceFatturaPassiva in OggettoFatturaPassiva.ListaVociFatturaPassiva"
                      :key="VoceFatturaPassiva.CHIAVE_VOCE_FATTURA_PASSIVA"
                    >
                      <td>
                        <span style="font-weight: initial">
                          {{ VoceFatturaPassiva.CODICE_ARTICOLO }}
                        </span>
                      </td>

                      <td>
                        <span style="font-weight: initial">
                          {{ VoceFatturaPassiva.BENE_SERVIZIO }}
                        </span>
                      </td>

                      <td>
                        <VUEInputProdotti
                          v-model="VoceFatturaPassiva.ID_PRODOTTO"
                          @onUpdate="newValue => InserisciProdottoVoceFattura(OggettoFatturaPassiva, VoceFatturaPassiva, newValue)"
                          :disabled="!VoceFatturaPassiva.CODICE_ARTICOLO"
                        />
                      </td>

                      <td style="text-align: center; vertical-align: middle;">
                        <input
                          v-if="VoceFatturaPassiva.CODICE_ARTICOLO && (VoceFatturaPassiva.ID_PRODOTTO === -1 || VoceFatturaPassiva.ID_PRODOTTO === undefined)"
                          type="checkbox"
                          :checked="VoceFatturaPassiva.GESTITO == 'T'"
                          @change="event => GestisciVoceFattura(OggettoFatturaPassiva, VoceFatturaPassiva, event.target.checked)"
                        />
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</template>

<script>
import VUEInputProdotti from '@/components/InputComponents/VUEInputProdotti.vue';  
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js';
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js';
import { SystemInformation } from '@/SystemInformation';
import VUEInputFornitore from '@/components/InputComponents/VUEInputFornitore.vue';

export default
{
  components :
  {
    VUEInputProdotti,
    VUEInputFornitore,
  },
  data()
  {
    return {
            LSFatturePassive              : [],
            FilterFattura                 : {
                                              DallaData : '',
                                              AllaData  : '',
                                              Fornitore : -1,
                                              Tipologia : 'Tutte'
                                            }
            }
  },

  methods :
  {
    InserisciProdottoVoceFattura(OggettoFatturaPassiva, VoceFatturaPassiva, NewValue)
    {
      var ObjQuery = { Operazioni : [] };
      var Self = this

      let Parametri = {}
      Parametri.CHIAVE           = VoceFatturaPassiva.CHIAVE_VOCE_FATTURA_PASSIVA
      Parametri.ID_FORNITORE     = VoceFatturaPassiva.ID_FORNITORE
      Parametri.CODICE_PRODOTTO  = VoceFatturaPassiva.CODICE_ARTICOLO
      Parametri.ID_FATTURA_PASSIVA = VoceFatturaPassiva.ID_FATTURA_PASSIVA
      Parametri.ID_PRODOTTO      = NewValue != -1 ? NewValue : null

      if(Parametri.ID_PRODOTTO != null)
      {    
        ObjQuery.Operazioni.push({
                                    Query      : 'InserisciCodiciFornitore',
                                    Parametri  : Parametri
                                })
      }

      ObjQuery.Operazioni.push({
                                Query      : 'ModificaProdottoVoceFatturaPassiva',
                                Parametri  : Parametri
      })
      SystemInformation.AdvQuery.PostSQL('FatturePassive', ObjQuery,
                                  function()
                                  {
                                    Self.Carica()
                                  },
                                  function(HTTPError,SubHTTPError,Response)
                                  {
                                    SystemInformation.HandleError(HTTPError,SubHTTPError,Response)
                                  })  
    },

    GestisciVoceFattura(OggettoFatturaPassiva, VoceFatturaPassiva, NewValue)
    {
      var ObjQuery = { Operazioni : [] };
      let Parametri = {}
      Parametri.CHIAVE      = VoceFatturaPassiva.CHIAVE_VOCE_FATTURA_PASSIVA
      Parametri.GESTITO     = NewValue ? 'T' : 'F'

      var Self = this

      ObjQuery.Operazioni.push({
                                Query      : 'ModificaGestioneVoceFatturaPassiva',
                                Parametri  : Parametri
      })

      SystemInformation.AdvQuery.PostSQL('FatturePassive', ObjQuery,
                                  function()
                                  {
                                    VoceFatturaPassiva.GESTITO = NewValue ? 'T' : 'F'
                                    OggettoFatturaPassiva.DaGestire = OggettoFatturaPassiva.ListaVociFatturaPassiva.some(voce =>
                                                                                                                          voce.GESTITO !== 'T' && voce.CODICE_ARTICOLO !== ''
                                                                                                                        )
                                    Self.Carica()
                                  },
                                  function(HTTPError,SubHTTPError,Response)
                                  {
                                    SystemInformation.HandleError(HTTPError,SubHTTPError,Response)
                                  })  
    },

    OnClickEspandiFatturaPassiva(OggettoFatturaPassiva)
    {
      if(!OggettoFatturaPassiva.Espanso)
        OggettoFatturaPassiva.Espanso    = true
      else OggettoFatturaPassiva.Espanso = false
    },

    VerificaEspansione(ChiaveFattura)
    {
      for (let i = 0; i < this.LSFatturePassive.length; i++)
      {
        if (this.LSFatturePassive[i].Chiave == ChiaveFattura)
        {
          if (this.LSFatturePassive[i].Espanso == true)
            return true
          else
            return false
        }
      }
      return false
    },

    Carica()
    {
      var Self = this
      // this.LSFatturePassive = []

      var Parametri = {}
      if(this.FilterFattura.DallaData != '')
        Parametri.DallaData = this.FilterFattura.DallaData

      if(this.FilterFattura.AllaData != '')
        Parametri.AllaData = this.FilterFattura.AllaData

      if(this.FilterFattura.Fornitore != -1)
        Parametri.Fornitore = this.FilterFattura.Fornitore

      SystemInformation.AdvQuery.GetSQL('FatturePassive', Parametri,
                                    function(Results)
                                    {
                                      let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "SelectFatturePassiveDaGestire");

                                      if(ArrayInfo != undefined)
                                      {
                                        let LastChiave            = null
                                        let OggettoFatturaPassiva = null
                                        let TempFatture           = []
                                        
                                        ArrayInfo.forEach(function(VoceFatturaPassiva)
                                        {
                                          if(LastChiave != VoceFatturaPassiva.CHIAVE_FATTURA_PASSIVA)
                                          {
                                            LastChiave                                    = VoceFatturaPassiva.CHIAVE_FATTURA_PASSIVA
                                            OggettoFatturaPassiva = {}
                                            OggettoFatturaPassiva.Chiave                  = VoceFatturaPassiva.CHIAVE_FATTURA_PASSIVA
                                            OggettoFatturaPassiva.Numero                  = VoceFatturaPassiva.NUMERO
                                            OggettoFatturaPassiva.RagioneSocialeFornitore = VoceFatturaPassiva.RAGIONE_SOCIALE_FORNITORE
                                            OggettoFatturaPassiva.Data                    = TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(VoceFatturaPassiva.DATA))
                                            OggettoFatturaPassiva.Espanso                 = Self.VerificaEspansione(OggettoFatturaPassiva.Chiave) != true ? false : true
                                            OggettoFatturaPassiva.DaGestire               = false

                                            OggettoFatturaPassiva.ListaVociFatturaPassiva = []
                                            TempFatture.push(OggettoFatturaPassiva)
                                          }

                                          OggettoFatturaPassiva.ListaVociFatturaPassiva.push(VoceFatturaPassiva)
                                        })

                                        Self.ControlloSeDaGestire(TempFatture)

                                        Self.LSFatturePassive = TempFatture

                                        if(Self.FilterFattura.Tipologia != 'Tutte')
                                        {
                                          if(Self.FilterFattura.Tipologia == 'Presenti')
                                          {
                                            for(let i = 0; i < Self.LSFatturePassive.length; i++)
                                            {
                                              if(Self.LSFatturePassive[i].DaGestire)
                                              {
                                                Self.LSFatturePassive.splice(i, 1);
                                                i--
                                              }
                                            }
                                          }
                                          else if(Self.FilterFattura.Tipologia == 'NonPresenti')
                                          {
                                            for(let i = 0; i < Self.LSFatturePassive.length; i++)
                                            {
                                              if(!Self.LSFatturePassive[i].DaGestire)
                                              {
                                                Self.LSFatturePassive.splice(i, 1);
                                                i--
                                              }
                                            }
                                          }
                                        }

                                      }
                                      else SystemInformation.HandleError('Impossibile ottenere il dettaglio della fattura passiva')
                                    },
                                    function(HTTPError,SubHTTPError,Response)
                                    {
                                      SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                    },
                                    'GestioneFatturePassive')
    },

    ControlloSeDaGestire(VettoreFatture)
    {
      VettoreFatture.forEach(function(Fattura)
      {
        let FatturaDaGestire = false

        Fattura.ListaVociFatturaPassiva.forEach(function(VoceFatturaPassiva)
        {
          if(VoceFatturaPassiva.GESTITO != 'T' && 
             TSchedaGenerica.DisponiFromListIndex(VoceFatturaPassiva.ID_PRODOTTO) == -1 && 
             VoceFatturaPassiva.CODICE_ARTICOLO != '')
            FatturaDaGestire = true
        })

        Fattura.DaGestire = FatturaDaGestire
      })
    },

    KeyDownF2(event)
    {
      if(event.which == 113) 
        this.Carica();
    },
  },

  beforeMount()
  {
    this.Carica()
  },
}
</script>