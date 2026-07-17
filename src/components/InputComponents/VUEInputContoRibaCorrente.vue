<template>
    <div class="ZMNuovaRigaScheda">
      <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;" :style="{width: CurrentContoCorrente.TIPO_COORDINATE == 'IBAN' ? '25%' : '11%'}">
        <label style="font-weight: bold;">Banca</label>
        <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control" v-model="CurrentContoCorrente.BANCA"/>
      </div>
      <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:1%" >&nbsp;</div>
      <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:13%">
          <label style="font-weight:bold;">Coordinate bancarie</label>
          <div style="display:flex;justify-content:space-between;align-items:center">
            <label style="display:flex;align-items:center;margin:0;">
              <input type="radio" style="margin:0px" value="IBAN" v-model="CurrentContoCorrente.TIPO_COORDINATE"
                                  :disabled="FatturaInviataAlloSdi || Disabilitato" @change="CurrentContoCorrente.ABI = null; 
                                                                                             CurrentContoCorrente.CAB = null; 
                                                                                             CurrentContoCorrente.NUMERO_CONTO_CORR = null">
              <span style="margin-left:4px; font-size: 15px;">IBAN</span>
            </label>

            <label style="display:flex;align-items:center;margin:0;">
              <input type="radio" style="margin:0px" value="ABICAB" v-model="CurrentContoCorrente.TIPO_COORDINATE"
                     :disabled="FatturaInviataAlloSdi || Disabilitato" @change="CurrentContoCorrente.IBAN = null">
              <span style="margin-left:4px; font-size: 15px;">ABI/CAB</span>
            </label>
          </div>
      </div>
        <!-- IBAN -->
        <div v-if="CurrentContoCorrente.TIPO_COORDINATE == 'IBAN'" style="float:left;width:20%">
          <label style="font-weight:bold;">IBAN</label>
          <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control"
                 v-model="CurrentContoCorrente.IBAN"/>
        </div>
        <!-- ABI/CAB -->
        <div v-if="CurrentContoCorrente.TIPO_COORDINATE == 'ABICAB'">
          <div style="float:left;width:10%">
            <label style="font-weight:bold;">ABI</label>
            <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control"
                   v-model="CurrentContoCorrente.ABI"/>
          </div>
          <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:1%" >&nbsp;</div>
          <div style="float:left;width:9%">
            <label style="font-weight:bold;">CAB</label>
            <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control"
                   v-model="CurrentContoCorrente.CAB"/>
          </div>
          <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:1%" >&nbsp;</div>
          <div style="float:left;width:13%">
            <label style="font-weight:bold;">NR. CONTO</label>
            <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control"
                   v-model="CurrentContoCorrente.NUMERO_CONTO_CORR"/>
          </div>
        </div>
      
      <!-- <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:23%">
        <label style="font-weight: bold;">IBAN</label>
        <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control" v-model="CurrentContoCorrente.IBAN"/>
      </div> -->
      <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:1%" >&nbsp;</div>
      <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:9%">
        <label style="font-weight: bold;">BIC</label>
        <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control" v-model="CurrentContoCorrente.BIC"/>
      </div>
      <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:1%" >&nbsp;</div>
      <div v-if="CurrentContoCorrente.CONTO_RIBA" style="float:left;width:9%">
        <label style="font-weight: bold;">SWIFT</label>
        <input :readonly="FatturaInviataAlloSdi || Disabilitato" type="text" class="form-control" v-model="CurrentContoCorrente.SWIFT"/>
      </div>

      <div v-if="!CurrentContoCorrente.CONTO_RIBA" style="float:left;width:23%">
        <label style="font-weight: bold;">Conto corrente</label>
         <select :disabled="FatturaInviataAlloSdi || Disabilitato" class="form-control" v-model="CurrentContoCorrente.ID_CONTO_CORRENTE" @change="OnChangeContoCorrente" style="cursor:default">
          <option :selected="CurrentContoCorrente.ID_CONTO_CORRENTE == -1" value="-1">-</option>
          <option v-for="SelectOption in ListaContiCorrenti" 
                  :Key="SelectOption.CHIAVE"
                  :value="SelectOption.CHIAVE"
                  :selected="SelectOption.CHIAVE == CurrentContoCorrente.ID_CONTO_CORRENTE">
                  {{SelectOption.DESCRIZIONE}}
          </option>
         </select>
      </div>
      <div v-if="!CurrentContoCorrente.CONTO_RIBA" style="float:left;width:1%">&nbsp;</div>
      <div v-if="!CurrentContoCorrente.CONTO_RIBA" style="float:left;width:15%">
        <label style="font-weight: bold;">Banca</label>
        <label class="ZMLabelInput">{{ CurrentContoCorrente.BANCA }}</label>
      </div>

      <div v-if="!CurrentContoCorrente.CONTO_RIBA ">
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:19%">
          <label style="font-weight: bold;">IBAN</label>
          <label class="ZMLabelInput">{{ CurrentContoCorrente.IBAN }}</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:9%">
          <label style="font-weight: bold;">BIC</label>
          <label class="ZMLabelInput">{{ CurrentContoCorrente.BIC }}</label>
        </div>
        <div style="float:left;width:1%">&nbsp;</div>
        <div style="float:left;width:9%">
          <label style="font-weight: bold;">SWIFT</label>
          <label class="ZMLabelInput">{{ CurrentContoCorrente.SWIFT }}</label>
        </div>
      </div>

      <div style="float:left;width:1%">&nbsp;</div>
      <div style="float:left;width:20%;margin-top:15px;">
        <div v-if="!SchedaFornitore || SchedaFornitore == undefined">
          <div class="btn-group m-b-sm" style="width:100%;float:right;margin-bottom:0px" v-if="CurrentContoCorrente.CONTO_RIBA ">
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" @click="OnClickContoRiba" type="button" class="btn btn-default" style="width:50%;float:right;">Conto corrente</button>
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" type="button" class="btn btn-info" style="width:50%;float:right">Conto Riba</button>
          </div>
          <div class="btn-group m-b-sm" style="width:100%;float:left;float:right;margin-bottom:0px" v-if="!CurrentContoCorrente.CONTO_RIBA ">
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" type="button" class="btn btn-info" style="width:50%;float:right">Conto corrente</button>
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" @click="OnClickContoRiba" type="button" class="btn btn-default" style="width:50%;float:right">Conto Riba</button>
          </div>  
        </div>
        <div v-if="SchedaFornitore">
          <div class="btn-group m-b-sm" style="width:85%;float:right;" v-if="!CurrentContoCorrente.CONTO_RIBA ">
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" @click="OnClickContoRibaFornitore" type="button" class="btn btn-default" style="width:50%;float:right;">Conto corrente</button>
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" type="button" class="btn btn-info" style="width:50%;float:right">Conto Riba</button>
          </div>
          <div class="btn-group m-b-sm" style="width:85%;float:left;float:right;" v-if="CurrentContoCorrente.CONTO_RIBA ">
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" type="button" class="btn btn-info" style="width:50%;float:right">Conto corrente</button>
            <button :disabled="FatturaInviataAlloSdi || Disabilitato" @click="OnClickContoRibaFornitore" type="button" class="btn btn-default" style="width:50%;float:right">Conto Riba</button>
          </div> 
        </div> 
      </div>    
    </div>

    <div style="clear:both;height:3px">&nbsp;</div>
</template>

<script>
 import { SystemInformation } from '@/SystemInformation.js';

 export default {
   data()
   {
      return {
                ListaContiCorrenti : []
             }
   },
   props : ['ContoCorrente', 'InviataAlloSdi','Fornitore', 'Disabilitato'],

   computed : 
   {
    CurrentContoCorrente : 
    {
        get()
        {
          return this.ContoCorrente;
        }
    },
    FatturaInviataAlloSdi : 
    {
        get()
        {
          return this.InviataAlloSdi;
        }
    },
    SchedaFornitore : 
    {
        get()
        {
          return this.Fornitore;
        }
    },

   },

   methods : 
   {
    CaricaContoCorrente()
    {
    var Self = this;
    var Parametri = { ContiCorrenti : -1 }
    SystemInformation.AdvQuery.GetSQL("MovimentiConti",Parametri,
                                    function(Results)
                                    {
                                        let ArrayInfo  = SystemInformation.AdvQuery.FindResults(Results,"SelectContiCasse");
                                        if(ArrayInfo != undefined)
                                        {
                                            Self.ListaContiCorrenti = ArrayInfo
                                        }
                                    },
                                    function(HTTPError,SubHTTPError,Response)
                                    {
                                        SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                    });
    },

    OnChangeContoCorrente()
    {
        if(this.CurrentContoCorrente.ID_CONTO_CORRENTE != -1)
        {
            for(let i = 0; i < this.ListaContiCorrenti.length; i++)
            {
                if(this.ListaContiCorrenti[i].CHIAVE == this.CurrentContoCorrente.ID_CONTO_CORRENTE)
                {
                    this.CurrentContoCorrente.BANCA     = this.ListaContiCorrenti[i].BANCA
                    this.CurrentContoCorrente.NR_CONTO  = this.ListaContiCorrenti[i].NR_CONTO
                    this.CurrentContoCorrente.IBAN      = this.ListaContiCorrenti[i].IBAN
                    this.CurrentContoCorrente.BIC       = this.ListaContiCorrenti[i].BIC
                    this.CurrentContoCorrente.SWIFT     = this.ListaContiCorrenti[i].SWIFT
                }
            }
        }
        else
        {
            this.CurrentContoCorrente.BANCA    = ''
            this.CurrentContoCorrente.NR_CONTO = ''
            this.CurrentContoCorrente.IBAN     = ''
            this.CurrentContoCorrente.SWIFT    = ''
            this.CurrentContoCorrente.BIC      = ''
        }
    },

    OnClickContoRiba()
    {
      if(this.CurrentContoCorrente.CONTO_RIBA )
      { 
          this.CurrentContoCorrente.CONTO_RIBA        = false
          this.CurrentContoCorrente.TIPO_COORDINATE   = ''
          this.CurrentContoCorrente.ABI               = ''
          this.CurrentContoCorrente.CAB               = ''
          this.CurrentContoCorrente.NUMERO_CONTO_CORR = ''
          this.CurrentContoCorrente.IBAN              = ''
          this.CurrentContoCorrente.NR_CONTO          = ''
          this.CurrentContoCorrente.ID_CONTO_CORRENTE = -1
          this.CurrentContoCorrente.BANCA             = ''
          this.CurrentContoCorrente.BIC               = ''
          this.CurrentContoCorrente.SWIFT             = ''
      }
      else
      {
          this.CurrentContoCorrente.CONTO_RIBA        = true
          this.CurrentContoCorrente.BANCA             = ''
          this.CurrentContoCorrente.IBAN              = ''
          this.CurrentContoCorrente.TIPO_COORDINATE   = 'IBAN'
          this.CurrentContoCorrente.ABI               = ''
          this.CurrentContoCorrente.NUMERO_CONTO_CORR = ''
          this.CurrentContoCorrente.CAB               = ''
          this.CurrentContoCorrente.NR_CONTO          = ''
          this.CurrentContoCorrente.BIC               = ''
          this.CurrentContoCorrente.SWIFT             = ''
          this.CurrentContoCorrente.ID_CONTO_CORRENTE = -1
      }
    },
    OnClickContoRibaFornitore()
    {
      if(!this.CurrentContoCorrente.CONTO_RIBA )
      {  
          this.CurrentContoCorrente.CONTO_RIBA        = true
          this.CurrentContoCorrente.TIPO_COORDINATE   = 'IBAN'
          this.CurrentContoCorrente.ABI               = ''
          this.CurrentContoCorrente.NUMERO_CONTO_CORR = ''
          this.CurrentContoCorrente.CAB               = ''
          this.CurrentContoCorrente.IBAN              = ''
          this.CurrentContoCorrente.SWIFT             = ''
          this.CurrentContoCorrente.BIC               = ''
          this.CurrentContoCorrente.NR_CONTO          = ''
          this.CurrentContoCorrente.ID_CONTO_CORRENTE = -1
          this.CurrentContoCorrente.BANCA             = ''
          
      }
      else
      {
          this.CurrentContoCorrente.CONTO_RIBA        = false
          this.CurrentContoCorrente.BANCA             = ''
          this.CurrentContoCorrente.TIPO_COORDINATE   = ''
          this.CurrentContoCorrente.ABI               = ''
          this.CurrentContoCorrente.NUMERO_CONTO_CORR = ''
          this.CurrentContoCorrente.CAB               = ''
          this.CurrentContoCorrente.BIC               = ''
          this.CurrentContoCorrente.SWIFT             = ''
          this.CurrentContoCorrente.IBAN              = ''
          this.CurrentContoCorrente.NR_CONTO          = ''
          this.CurrentContoCorrente.ID_CONTO_CORRENTE = -1
      }
    },
   },
 
   beforeMount()
   {
     this.CaricaContoCorrente()
   }
 }
</script>