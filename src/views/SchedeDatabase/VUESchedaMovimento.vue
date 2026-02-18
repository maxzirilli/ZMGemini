<template>
    <VUEModal v-if="PopupGestisciImporti" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
            :Titolo="'Gestisci importo'" 
            :Altezza="'500px'" 
            :Larghezza="'900px'"
            @onClickChiudiModal="PopupGestisciImporti = false">
    <template v-slot:Body>
      <div style="float:left;width:2%;">&nbsp;</div>
        <div class="row wrapper">
          <section class="panel panel-default" style="background-color:white;">
            <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
              <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                <thead>
                  <tr>
                    <th style="width:10%;position: sticky; top: 0;">Numero fatt.</th>
                    <th style="width:16%;position: sticky; top: 0;">Scadenza rata</th>
                    <th style="width:15%;position: sticky; top: 0;">Importo</th>
                    <th style="width:44%;position: sticky; top: 0;">Cliente</th>
                    <th style="width:15%;position: sticky; top: 0;">Pagato</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="Elemento in VisualizzaSoloSelezionati" :key="Elemento.CHIAVE">
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Elemento.NUMERO_FATTURA }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ CambioDataScadenza(Elemento.DATA) }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ (Elemento.IMPORTO / 100).toFixed(2) }} €
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Elemento.RAGIONE_SOCIALE }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      <input type="number" style="font-size: 16px" class="input-sm form-control" v-model="Elemento.IMPORTO_PAGATO_TEMPORANEO">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
      </div>
      <label style="font-weight: bold;font-size:18px;float:right;width:13%">{{ GetSommaTotalePagatoFatture(VisualizzaSoloSelezionati).toFixed(2)}} €</label>
      <label style="font-size:18px;float:right;width:44%;text-align:end">Totale pagato:&nbsp;&nbsp;&nbsp; </label>
     
      <label style="font-weight: bold;font-size:18px;float:right;width:13%">{{ GetSommaTotaleFatture(VisualizzaSoloSelezionati).toFixed(2)}} €</label>
      <label style="font-size:18px;float:right;width:15%;text-align:end">Totale rate:&nbsp;&nbsp;&nbsp; </label>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="PopupGestisciImporti = false" data-dismiss="modal">Annulla</button>
      <button v-if="CanConfirmGestisciImporti()" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickConfermaImportiFatture" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal> 

  <VUEModal v-if="PopupGestisciImportiFornitori" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
            :Titolo="'Gestisci importo'" 
            :Altezza="'500px'" 
            :Larghezza="'900px'"
            @onClickChiudiModal="PopupGestisciImportiFornitori = false">
    <template v-slot:Body>
      <div style="float:left;width:2%;">&nbsp;</div>
        <div class="row wrapper">
          <section class="panel panel-default" style="background-color:white;">
            <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
              <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                <thead>
                  <tr>
                    <th style="width:10%;position: sticky; top: 0;">Numero fatt.</th>
                    <th style="width:16%;position: sticky; top: 0;">Scadenza rata</th>
                    <th style="width:15%;position: sticky; top: 0;">Importo</th>
                    <th style="width:44%;position: sticky; top: 0;">Cliente</th>
                    <th style="width:15%;position: sticky; top: 0;">Pagato</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="Elemento in VisualizzaSoloSelezionati" :key="Elemento.CHIAVE">
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Elemento.NUMERO_FATTURA }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ CambioDataScadenza(Elemento.DATA) }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ (Elemento.IMPORTO / 1000).toFixed(2) }} €
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Elemento.RAGIONE_SOCIALE }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      <input type="number" style="font-size: 16px" class="input-sm form-control" v-model="Elemento.IMPORTO_PAGATO_TEMPORANEO">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
      </div>
      <label style="font-weight: bold;font-size:18px;float:right;width:13%">{{ GetSommaTotalePagatoFatture(VisualizzaSoloSelezionati).toFixed(2)}} €</label>
      <label style="font-size:18px;float:right;width:44%;text-align:end">Totale pagato:&nbsp;&nbsp;&nbsp; </label>
     
      <label style="font-weight: bold;font-size:18px;float:right;width:13%">{{ GetSommaTotaleFattureFornitori(VisualizzaSoloSelezionati).toFixed(2)}} €</label>
      <label style="font-size:18px;float:right;width:15%;text-align:end">Totale rate:&nbsp;&nbsp;&nbsp; </label>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="PopupGestisciImportiFornitori = false" data-dismiss="modal">Annulla</button>
      <button v-if="CanConfirmGestisciImporti()" type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickConfermaImportiFattureFornitori" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal>

  <VUEModal v-if="PopupMovimentoCorrelatoARateFattureFornitori" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
            :Titolo="'Movimento correlato'" 
            :Altezza="'500px'" 
            :Larghezza="'900px'"
            @onClickChiudiModal="OnClickChiudiModalFattureFornitori">
    <template v-slot:Body>
      <div style="float:left;width:2%;">&nbsp;</div>
        <input type="text" class="input-sm form-control" placeholder="Cerca..." v-model="FiltroRagioneSocialeRateFattureFornitori">
        <div class="row wrapper">
          <section class="panel panel-default" style="background-color:white;">
            <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
              <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                <thead>
                  <tr>
                    <th style="width:7%;position: sticky; top: 0;"></th>
                    <th style="width:10%;position: sticky; top: 0;">Numero fatt.</th>
                    <th style="width:16%;position: sticky; top: 0;">Scadenza rata</th>
                    <th style="width:15%;position: sticky; top: 0;">Importo</th>
                    <th style="width:52%;position: sticky; top: 0;">Fornitore</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="Rata in RateFattureFornitoriFiltrate" :key="Rata.CHIAVE">
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      <input type="checkbox" style="height: 20px;" class="form-control" v-model="Rata.Selezionata">
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Rata.NUMERO_FATTURA }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ CambioDataScadenza(Rata.DATA) }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ (Rata.IMPORTO / 1000).toFixed(2) }} €
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Rata.RAGIONE_SOCIALE }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
            <div style="float:right;width:40%">
             <label style="font-weight: bold;font-size: large;">Totale selezionato: {{ TotaleRateFattureFornitoriFiltrate }} €</label>
            </div>
        </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="OnClickChiudiModalFattureFornitori" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickGestisciRateFatturaFornitoriMovimento" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal> 

  <VUEModal v-if="PopupMovimentoCorrelato" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
            :Titolo="'Movimento correlato'" 
            :Altezza="'500px'" 
            :Larghezza="'900px'"
            @onClickChiudiModal="OnClickChiudiModalRate">
    <template v-slot:Body>
      <div style="float:left;width:2%;">&nbsp;</div>
        <input type="text" class="input-sm form-control" placeholder="Cerca..." v-model="FiltroRagioneSociale">
        <div class="row wrapper">
          <section class="panel panel-default" style="background-color:white;">
            <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
              <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                <thead>
                  <tr>
                    <th style="width:7%;position: sticky; top: 0;"></th>
                    <th style="width:10%;position: sticky; top: 0;">Numero fatt.</th>
                    <th style="width:16%;position: sticky; top: 0;">Scadenza rata</th>
                    <th style="width:15%;position: sticky; top: 0;">Importo</th>
                    <th style="width:52%;position: sticky; top: 0;">Cliente</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="Rata in RateFiltrate" :key="Rata.CHIAVE">
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      <input type="checkbox" style="height: 20px;" class="form-control" v-model="Rata.Selezionata">
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Rata.NUMERO_FATTURA }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ CambioDataScadenza(Rata.DATA) }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ (Rata.IMPORTO / 100).toFixed(2) }} €
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Rata.RAGIONE_SOCIALE }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
            <div style="float:right;width:40%">
             <label style="font-weight: bold;font-size: large;">Totale selezionato: {{ TotaleRateFiltrate }} €</label>
            </div>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="OnClickChiudiModalRate" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickGestisciRateMovimento" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal> 

    <VUEModal v-if="PopupMovimentoCorrelatoAFatturePregresseFornitori"  :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
            :Titolo="'Movimento correlato'" 
            :Altezza="'500px'" 
            :Larghezza="'900px'"
            @onClickChiudiModal="OnClickChiudiModalFatturePregresseFornitori">
    <template v-slot:Body>
      <div style="float:left;width:2%;">&nbsp;</div>
        <input type="text" class="input-sm form-control" placeholder="Cerca..." v-model="FiltroRagioneSocialeFatturePregresseFornitori">
        <div class="row wrapper">
          <section class="panel panel-default" style="background-color:white;">
            <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
              <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                <thead>
                  <tr>
                    <th style="width:7%;position: sticky; top: 0;"></th>
                    <th style="width:10%;position: sticky; top: 0;">Numero</th>
                    <th style="width:16%;position: sticky; top: 0;">Data</th>
                    <th style="width:15%;position: sticky; top: 0;">Importo</th>
                    <th style="width:52%;position: sticky; top: 0;">Fornitore</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="FatturaPregressa in FatturePregresseFornitoriFiltrate" :key="FatturaPregressa.CHIAVE">
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      <input type="checkbox" style="height: 20px;" class="form-control" v-model="FatturaPregressa.Selezionata">
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ FatturaPregressa.NUMERO_FATTURA }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ CambioDataScadenza(FatturaPregressa.DATA) }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ (FatturaPregressa.IMPORTO / 1000).toFixed(2) }} €
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ FatturaPregressa.RAGIONE_SOCIALE }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
            <div style="float:right;width:40%">
             <label style="font-weight: bold;font-size: large;">Totale selezionato: {{ TotaleFatturePregresseFornitoriFiltrate }} €</label>
            </div>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="OnClickChiudiModalFatturePregresseFornitori" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickGestisciFatturePregresseFornitori" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal> 

  <VUEModal v-if="PopupMovimentoCorrelatoAFatturePregresse" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma"
            :Titolo="'Movimento correlato'" 
            :Altezza="'500px'" 
            :Larghezza="'900px'"
            @onClickChiudiModal="OnClickChiudiModalFatturePregresse">
    <template v-slot:Body>
      <div style="float:left;width:2%;">&nbsp;</div>
        <input type="text" class="input-sm form-control" placeholder="Cerca..." v-model="FiltroRagioneSocialeFatturePregresse">
        <div class="row wrapper">
          <section class="panel panel-default" style="background-color:white;">
            <div class="table-responsive" style="max-height:350px;width:100%;height: 60%;">
              <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                <thead>
                  <tr>
                    <th style="width:7%;position: sticky; top: 0;"></th>
                    <th style="width:10%;position: sticky; top: 0;">Numero</th>
                    <th style="width:16%;position: sticky; top: 0;">Data</th>
                    <th style="width:15%;position: sticky; top: 0;">Importo</th>
                    <th style="width:52%;position: sticky; top: 0;">Cliente</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="FatturaPregressa in FatturePregresseFiltrate" :key="FatturaPregressa.CHIAVE">
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      <input type="checkbox" style="height: 20px;" class="form-control" v-model="FatturaPregressa.Selezionata">
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ FatturaPregressa.NUMERO_FATTURA }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ CambioDataScadenza(FatturaPregressa.DATA) }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ (FatturaPregressa.IMPORTO / 100).toFixed(2) }} €
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ FatturaPregressa.RAGIONE_SOCIALE }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
            <div style="float:right;width:40%">
             <label style="font-weight: bold;font-size: large;">Totale selezionato: {{ TotaleFatturePregresseFiltrate }} €</label>
            </div>
      </div>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="OnClickChiudiModalFatturePregresse" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:25%" @click="OnClickGestisciFatturePregresse" data-dismiss="modal">Conferma</button>
    </template>
  </VUEModal> 


  <VUEModal v-if="PopupAssociazione" :Titolo="'Associazione'" :PathLogo="require('../../assets/images/LogoGemini2.png')"
             :Programma="NomeProgramma" :Altezza="'305px'" :Larghezza="'700px'"
         @onClickChiudiModal="OnClickChiudiPopupAssociazione">
    <template v-slot:Body>
       <div> {{ TipoSelezione }}</div>
       <div style="margin-bottom:50px">
          <div style="float: left;margin-left:10px;margin-top:5px;">
          <input type="radio" 
                  value="NessunaAssociazione"
                  v-model="TipoSelezione">
          </div>
          <div style="float: left;font-size:18px;margin-left:12px;margin-top:3px">
          <label style="margin-bottom: 10px;" >Nessuna associazione</label>
          </div>
       </div>

       <div style="height:2px;clear:both;">&nbsp;</div>
       <div style="float: left;margin-left:10px;margin-top:3px;">
            <input type="radio" 
                    value="Fornitore"
                    v-model="TipoSelezione">
       </div>
       <div style="float: left;font-size:18px;margin-left:12px;margin-top:3px">
            <label style="margin-bottom: 10px;">Al fornitore</label>
       </div>
       <VUEInputFornitore v-model="this.IDFornitoreTemporaneo" placeholder="Fornitore"
                          v-if="TipoSelezione == 'Fornitore'"/>
       <div style="height:2px;clear:both;">&nbsp;</div>

       <div style="float: left;margin-left:10px;margin-top:5px;">
            <input type="radio" 
                    value="Cliente"
                    v-model="TipoSelezione">
       </div>
       <div style="float: left;font-size:18px;margin-left:12px;margin-top:3px">
          <label style="margin-bottom: 10px;">Al cliente</label>
       </div>
       <VUEInputClienti v-model="this.IDClienteTemporaneo"
                        v-if="TipoSelezione == 'Cliente'"
                        @onUpdate="newValue => this.IDClienteTemporaneo = newValue"/>
    </template>
    <template v-slot:Footer>
      <button type="button" class="btn btn-danger" style="float:right;margin-left:10px;font-weight:bold;width:25%" @click="OnClickChiudiPopupAssociazione" data-dismiss="modal">Annulla</button>
      <button type="button" class="btn btn-info" style="float:right;font-weight:bold;width:30%" @click="OnClickConfermaPopupAssociazione" data-dismiss="modal">Conferma associazione</button>
    </template>
    </VUEModal>
  
 <div class="ZMCorpoSchedeDati">
   <header class="panel-heading bg-light" >
       <ul class="nav nav-tabs nav-justified">
         <li v-for="ATab in Tabs.Tabs" :Key="ATab.Id" 
             :class="{ active : ATab.Id == Tabs.ActiveTab }">
             <a @click="Tabs.ActiveTab = ATab.Id">{{ATab.Caption}}
              <img v-if="ATab.Id == 'Generale'" src="@/assets/images/IconeAlbero/Trasferimento2.png" style="width:40px;height:40px;float:left;margin-top:-8px;">  
             </a>
         </li>
       </ul>
   </header>
   <div style="height:5px;"></div>
   <div  v-if="Tabs.ActiveTab == 'Generale'">
    <div class="ZMNuovaRigaScheda"> 
     <div style="float:left;margin-left:3px">
      <text style="font-weight: bold;">Data</text>
      <input style="" type="date" id="input-data" class="form-control" v-model="CurrentSchedaMovimento.Dati.DATA"/>
      <label v-if="CurrentSchedaMovimento.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
     </div> 

     <div v-if="CurrentSchedaMovimento.Dati.ID_ANAGRAFICA != -1" style="float:left;margin-left:3px">
      <text style="font-weight: bold;">Data chiusura</text>
      <input style="" type="date" id="input-data" class="form-control" v-model="CurrentSchedaMovimento.Dati.DATA_CHIUSURA"/>
      <label v-if="CurrentSchedaMovimento.Dati.DATA == ''" class="ZMFormLabelError">Campo obbligatorio</label>
     </div>  
      <div style="float:left;margin-right:5px">
        <input style="float:left;margin-top:25px; margin-left:10px" type="checkbox" v-model="CurrentSchedaMovimento.Dati.NO_PRIMA_NOTA"/>
      </div>
      <div style="float:left;">
        <label style="font-weight: bold;margin-top:23px">NO PN&nbsp;</label>
      </div>

    <button class="btn btn-sm btn-info" style="float:right;margin-top:15px;font-weight:bold;width:10%" @click="OnClickApriPopupAssociazione" data-dismiss="modal">Associa</button>
     <div style= "float:right;margin-top:22px;margin-right:1x;padding-right:20px;font-weight:bold;width:auto">
    <label v-if="CurrentSchedaMovimento.Dati.ID_ANAGRAFICA != -1"><b>Associato a</b> : {{ this.RagioneSocialeAssociato }}</label>
    </div>

     <div style="clear:both; height:10px"></div>
     <div style="float:left;margin-left:10px">
      <label v-if="CurrentSchedaMovimento.Dati.CONTO_CORRENTE_PRELIEVO == SchedaMovimento.Dati.CONTO_CORRENTE_VERSAMENTO && CurrentSchedaMovimento.Dati.CONTO_CORRENTE_PRELIEVO != -1"
             style="float:left;font-size: small;"
             class="ZMFormLabelError">
             I due Conti Correnti/Casse non possono essere gli stessi
      </label>
      <label v-if="CurrentSchedaMovimento.Dati.CkVersamentoEsterno && CurrentSchedaMovimento.Dati.CkPrelievoEsterno"
             style="float:left;font-size: small;"
             class="ZMFormLabelError">
             Non selezionare due conti esterni
      </label>
     </div>

    </div>

    <div class="ZMSeparatoreScheda">Prelievo DA </div>
     <div class="ZMNuovaRigaScheda">
     <!-- esterno -->
      <div style="float: left;margin-left:10px;margin-top:5px;">
       <input v-model="CurrentSchedaMovimento.Dati.CkPrelievoEsterno" 
              :checked="CurrentSchedaMovimento.Dati.CkPrelievoEsterno" 
              :value="true" 
              name="Prelievo" 
              type="radio" 
              style="cursor: auto" 
              :disabled="CurrentSchedaMovimento.BloccoPrelievoDa"
              @change="EsternoSelezionato">
      </div>
      <div style="float: left;font-size:14px;margin-left:12px;margin-top:7px">
       <label style="margin-bottom: 0px;">Esterno</label>
      </div>
     </div>
     <!-- conto corrente -->
     <div class="ZMNuovaRigaScheda" style="margin-bottom:50px">
      <div style="float: left;margin-left:10px;margin-top:5px;">
       <input type="radio" 
              v-model="CurrentSchedaMovimento.Dati.CkPrelievoDaConto" 
             :checked="CurrentSchedaMovimento.Dati.CkPrelievoDaConto" 
             :value="true" 
              name="Prelievo" 
             :disabled="CurrentSchedaMovimento.BloccoPrelievoDa"
             @change="ChangeRadioPrelievo">
      </div>
      <div style="float: left;font-size:14px;margin-left:12px;margin-top:7px">
       <label style="margin-bottom: 0px;">Conto corrente/Cassa</label>
      </div>

      <div>
       <select v-if="CurrentSchedaMovimento.Dati.CkPrelievoDaConto" 
               v-model="CurrentSchedaMovimento.Dati.CONTO_CORRENTE_PRELIEVO" 
               class="form-control" style="float: left; width: 40%; margin-left:20px;cursor: auto">
        <option selected value="-1">-</option>
        <option v-for="ContoCassa in CurrentSchedaMovimento.LsContiCorrentiCasse" 
                :Key="ContoCassa.CHIAVE"
                :value="ContoCassa.CHIAVE">
                {{getNomeConto(ContoCassa,ContoCassa.BANCA)}}
         </option>
       </select>
      </div>
     <label style="margin-left:5px;float:left" v-if="SchedaMovimento.Dati.CONTO_CORRENTE_PRELIEVO == -1 && SchedaMovimento.Dati.CkPrelievoDaConto" class="ZMFormLabelError">Selezionare un conto corrente o cassa</label>
     </div>
     

     <div class="ZMSeparatoreScheda">Versamento IN </div>
     <div class="ZMNuovaRigaScheda">
     <!-- Esterno -->
      <div style="float: left;margin-left:10px;margin-top:5px">
       <input v-model="CurrentSchedaMovimento.Dati.CkVersamentoEsterno" 
              :checked="CurrentSchedaMovimento.Dati.CkVersamentoEsterno" 
              :value="true" name="Versamento" type="radio" style="cursor: auto" 
              :disabled="CurrentSchedaMovimento.BloccoVersamentoIn"
              @change="EsternoSelezionato">
      </div>
      <div style="float: left;font-size:14px;margin-left:12px;margin-top:5px" >
       <label style="margin-bottom: 0px;">Esterno</label>
      </div>
     </div>
      <!-- Conto corrente -->
     <div class="ZMNuovaRigaScheda" style="margin-bottom:50px">
      <div style="float: left;margin-left:10px; margin-top:7px">
       <input v-model="CurrentSchedaMovimento.Dati.CkVersamentoDaConto" 
              :value="true" name="Versamento" type="radio" 
              :disabled="CurrentSchedaMovimento.BloccoVersamentoIn"
              @change="ChangeRadioVersamento">
      </div>
      <div style="float: left;font-size:14px;margin-left:12px;margin-top:7px">
       <label style="margin-bottom: 0px;">Conto corrente/Cassa</label>
      </div>
      <div>
       <select v-if="CurrentSchedaMovimento.Dati.CkVersamentoDaConto" 
               v-model="CurrentSchedaMovimento.Dati.CONTO_CORRENTE_VERSAMENTO" 
               class="form-control" style="float: left; width: 40%; margin-left:20px;cursor: auto">
        <option selected value="-1">-</option>
        <option v-for="ContoCassa in CurrentSchedaMovimento.LsContiCorrentiCasse" 
                :Key="ContoCassa.CHIAVE"
                :value="ContoCassa.CHIAVE">
                {{getNomeConto(ContoCassa,ContoCassa.BANCA)}}
         </option>
       </select>
      </div>
      <label style="margin-left:5px;float:left" v-if="SchedaMovimento.Dati.CONTO_CORRENTE_VERSAMENTO == -1 && SchedaMovimento.Dati.CkVersamentoDaConto" class="ZMFormLabelError">Selezionare un conto corrente o cassa</label>
     </div>
    <div class="ZMSeparatoreScheda">Descrizione</div>
    <div class="ZMNuovaRigaScheda">
      <textarea type="text" style="resize: none; height: 100px;" class="form-control" v-model="CurrentSchedaMovimento.Dati.DESCRIZIONE" placeholder="Descrizione"/>
      <label style="margin-left:5px" v-if="CurrentSchedaMovimento.Dati.DESCRIZIONE.trim() == '' && CurrentSchedaMovimento.Dati.ID_CATEGORIA_MOVIMENTO == -1" class="ZMFormLabelError">Campo obbligatorio</label>
    </div>
    <br>
    <div class="ZMSeparatoreScheda">Tipologia e Importo</div>
    <div class="ZMNuovaRigaScheda" style="margin-right:20px">
      <div>
        <div style="float:left;">&nbsp;</div>
        <div style="float:left;width:40%">
            <label style="font-weight: bold;">Tipologia</label>
            <select class="form-control" v-model="CurrentSchedaMovimento.Dati.ID_CATEGORIA_MOVIMENTO">
              <option value="-1">-</option>
              <option v-for="SelectOption in CatMovimenti"
                            :Key="SelectOption.CHIAVE"
                            :value="SelectOption.CHIAVE">
                            {{SelectOption.DESCRIZIONE}}
              </option>
            </select>
        </div> 
        <div style="float:left;width:1%;">&nbsp;</div>
        <div style="float:right;width:41%">
            <label style="font-weight: bold;">Importo</label>
            <input :disabled="VisualizzaSoloSelezionati.length != 0" class="form-control" type="number" style="" step="0.01" min="0" v-model="CurrentSchedaMovimento.Dati.IMPORTO" >
            <label style="margin-left:5px" v-if="CurrentSchedaMovimento.Dati.IMPORTO == 0" class="ZMFormLabelError">Campo obbligatorio</label>
        </div>
        <div style="clear:both">&nbsp;</div>
    </div>
  </div>
  <div class="ZMNuovaRigaScheda" style="margin-right:20px" v-if="PresenzaFattureAttive">
    <div style="float:right; width:13%">
      <button class="btn btn-info" style="width:100%;margin-bottom:10px" @click="OnClickCorrelaAFatturePregresse">Fatt. pregr.</button>
    </div>
    <div style="float:right; width:1%">&nbsp;</div>
    <div style="float:right; width:13%">
      <button class="btn btn-info" style="width:100%;margin-bottom:10px" @click="OnClickCorrelaAFatture">Fatture</button>
    </div>
    <div class="clear:both;height:1px" >&nbsp;</div>
  </div>
  <div class="ZMNuovaRigaScheda" style="margin-right:20px" v-if="PresenzaFatturePassive">
    <div style="float:right; width:13%">
      <button class="btn btn-info" style="width:100%;margin-bottom:10px" @click="OnClickCorrelaAFatturePregresseFornitori">Fatt. pregr. forn.</button>
    </div>
    <div style="float:right; width:1%">&nbsp;</div>
    <div style="float:right; width:13%">
      <button class="btn btn-info" style="width:100%;margin-bottom:10px" @click="OnClickCorrelaARateFatturePregresseFornitori">Fatt. fornitori</button>
    </div>
    <div class="clear:both;height:1px">&nbsp;</div>
  </div>
      <div v-if="VisualizzaSoloSelezionati.length != 0">
        <div class="ZMSeparatoreScheda" style="margin-top:10px">Rate correlate</div>
        <div class="row wrapper">
          <section class="panel panel-default" style="background-color:white; width: 98%; margin-left: 1%;">
            <div class="table-responsive" style="max-height:350px;width:100%;height: 60%; ">
              <table class="table table-striped b-t b-light" style="width:100%;height: 60%;">
                <thead>
                  <tr>
                      <th style="width:10%">Numero fatt.</th>
                      <th style="width:16%">Data</th>
                      <th style="width:15%">Importo</th>
                      <th style="width:52%">Intestato a</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="Rata in VisualizzaSoloSelezionati" :key="Rata.CHIAVE">
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Rata.NUMERO_FATTURA }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ CambioDataScadenza(Rata.DATA) }}
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ GetImportoRata(Rata) }} €
                    </td>
                    <td style="padding:2px;border:1px solid #ddd; border-bottom:0; background-color:white;font-size:16px;text-align:center;vertical-align: middle;">
                      {{ Rata.RAGIONE_SOCIALE }}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>
        <div class="ZMNuovaRigaScheda" style="margin-right:20px" >
          <!-- v-if="PresenzaFattureAttive" -->
          <div v-if="PresenzaFatturePassive" style="float:right; width:13%">
            <button class="btn btn-info" style="width:100%;margin-bottom:10px" @click="OnClickModificaImportiSelezionateFornitori">Modifica importi</button>
          </div>
          <div v-else style="float:right; width:13%">
            <button class="btn btn-info" style="width:100%;margin-bottom:10px" @click="OnClickModificaImportiSelezionate">Modifica importi</button>
          </div>
        </div>
      </div>

    </div>
    <div class="ZMSeparatoreFiltri"></div>
 </div>
</template>


<script>
import { TSchedaGenerica } from '../../../../../../../../Librerie/VUE/ZSchedaGenerica.js'
import { SystemInformation, DASHBOARD_FILTER_TYPES, RUOLI , NOME_PROGRAMMA} from '@/SystemInformation.js'
import { TZDateFunct } from '../../../../../../../../Librerie/VUE/ZDateFunct.js'
import VUEModal from '../../../../../../../../Librerie/VUE/TemplateGestionale/VUEModal.vue';
import { TZStringFunct } from '../../../../../../../../Librerie/VUE/ZStringFunct.js';
import { TZGenericFunct } from '../../../../../../../../Librerie/VUE/ZGenericFunct.js';
import VUEInputFornitore from '@/components/InputComponents/VUEInputFornitore.vue';
import VUEInputClienti from '@/components/InputComponents/VUEInputClienti.vue';


export const TIPO_RATA_FATT_PASSIVA = 'D'

export class TSchedaMovimento extends TSchedaGenerica
{
  AssignDati(Dati)
  {
    for(let i = 0; i < Dati.length; i++)
    {
      this.Dati.IMPORTO += Dati[i].Dati['IMPORTO'].Valore
      this.Dati.DESCRIZIONE += 'Fattura nr ' + Dati[i].Dati['NUMERO'].Valore + ' del ' + TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Dati[i].Dati['DATA'].Valore)) + ' \n' 
    }

    this.Dati.IMPORTO = this.Dati.IMPORTO.toFixed(2)
    this.Dati.DESCRIZIONE = this.Dati.DESCRIZIONE.substring(0, this.Dati.DESCRIZIONE.length - 2)
  }
  
  OnInitialization()
  {

  }

  GetClassName()
  {
    return 'TSchedaMovimento';
  }

  CanRecord()
  {
    return (this.Dati.DESCRIZIONE.trim() != '' || this.Dati.ID_CATEGORIA_MOVIMENTO != -1) && 
            this.Dati.CONTO_CORRENTE_PRELIEVO != this.Dati.CONTO_CORRENTE_VERSAMENTO &&
            this.Dati.DATA != '' &&
            this.Dati.IMPORTO != 0
  }
  
  GetDescrizione()
  {
    let Data = TZDateFunct.FormatDateTime('dd/mm/yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA))
    if(this.Dati.CONTO_CORRENTE_PRELIEVO == undefined || this.Dati.CONTO_CORRENTE_PRELIEVO == -1)
      return ('Entrata - ' + Data)
    else if(this.Dati.CONTO_CORRENTE_VERSAMENTO == undefined || this.Dati.CONTO_CORRENTE_VERSAMENTO == -1)
      return ('Uscita - ' + Data)
    else return ('Trasferimento - ' + Data)
  }

  DatiEliminabili()
  {
    if (SystemInformation.UserInformation.Ruolo == RUOLI.Dipendente && !SystemInformation.AccessRights.DipendentePuoCancellareDoc())
        return false;

    return true;
  }

   Elimina(OnSuccess,OnError)
   {
      this.InEliminazione = true;
      var ObjQuery = { Operazioni : [ 
                                      {
                                        Query     : "DeleteCorrelazioneMovimento",
                                        Parametri : { CHIAVE_MOVIMENTO : this.Chiave }
                                      },
                                      {
                                        Query     : "DeleteCorrelazioneMovimentoFatturaPregressaFornitori",
                                        Parametri : { CHIAVE_MOVIMENTO : this.Chiave }
                                      },
                                      {
                                        Query     : "DeleteCorrelazioneMovimentoFatturaPregressa",
                                        Parametri : { CHIAVE_MOVIMENTO : this.Chiave }
                                      },
                                      {
                                        Query     : "DeleteCorrelazioneMovimentoFatturaFornitori",
                                        Parametri : { CHIAVE_MOVIMENTO : this.Chiave }
                                      },
                                      {
                                        Query     : "DeleteMovimento",
                                        Parametri : { CHIAVE_MOVIMENTO : this.Chiave }
                                      },
                                    ]};

     ObjQuery = this.ControlloSaldiChiusureAnnuali(ObjQuery)

     this.AdvQuery.PostSQL('MovimentiConti',ObjQuery,function()
     {
        OnSuccess();
     },
     function(HTTPError,SubHTTPError,Response)
     {
        OnError(HTTPError,SubHTTPError,Response);
     });
   }

   ControlloSaldiChiusureAnnuali(ObjQuery)
   {
      if(this.Dati.ID_ANAGRAFICA != -1)
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_ANAGRAFICA),
                                                  ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });
      }

      // if(this.Dati.ID_FORNITORE != -1)
      // {
      //   ObjQuery.Operazioni.push({
      //                               Query     : "EliminaRecordSaldiChiusureAnnualiFornitore",
      //                               Parametri : {
      //                                             CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_FORNITORE),
      //                                             ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
      //                                           }
      //                           });
      // }

      for(let i = 0; i < this.Dati.LsRateCorrelate.length; i++ )
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });
      }

      for(let i = 0; i < this.Dati.LsFattureCorrelate.length; i++ )
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFattureCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });
      }

      for(let i = 0; i < this.Dati.LsFatturePregresseFornitoriCorrelate.length; i++ )
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnualiFornitore",
                                    Parametri : {
                                                  CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFatturePregresseFornitoriCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });
      }
      for(let i = 0; i < this.Dati.LsRateFattureFornitoriCorrelate.length; i++ )
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnualiFornitore",
                                    Parametri : {
                                                  CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateFattureFornitoriCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });
      }

      return ObjQuery
   }

   GetImageIndex()
   {
      if(this.Dati.CONTO_CORRENTE_PRELIEVO == undefined || this.Dati.CONTO_CORRENTE_PRELIEVO == -1)
      return 'Entrata.png'
      else if(this.Dati.CONTO_CORRENTE_VERSAMENTO == undefined || this.Dati.CONTO_CORRENTE_VERSAMENTO == -1)
      return 'Uscita.png'
      else return 'Trasferimento.png'
   }

   CaricaRiassunto(Riassunto)
   {
      this.Chiave                             = Riassunto.CHIAVE;
      this.Dati.DATA                          = Riassunto.DATA
      if(Riassunto.CONTO_CORRENTE_PRELIEVO   != undefined)
        this.Dati.CONTO_CORRENTE_PRELIEVO     = Riassunto.CONTO_CORRENTE_PRELIEVO
      if(Riassunto.CONTO_CORRENTE_VERSAMENTO != undefined)
        this.Dati.CONTO_CORRENTE_VERSAMENTO   = Riassunto.CONTO_CORRENTE_VERSAMENTO
      this.CreateSnapshot();
   }

   Clear()
   {
      this.LsContiCorrentiCasse = []
      this.BloccoPrelievoDa     = false
      this.BloccoVersamentoIn   = false
      this.Dati                 = { 
                                     DATA                        : TZDateFunct.DateInHTMLInputFormat(new Date()),
                                     IMPORTO                     : '',
                                     SOSPESO                     : '',
                                     DESCRIZIONE                 : '',
                                     CONTO_CORRENTE_PRELIEVO     : -1,
                                     DATA_CHIUSURA               : '',
                                     CONTO_CORRENTE_VERSAMENTO   : -1,
                                     ID_CATEGORIA_MOVIMENTO      : -1,
                                     CkPrelievoEsterno           : false,
                                     CkPrelievoDaConto           : false,
                                     CkVersamentoEsterno         : false,
                                     CkVersamentoDaConto         : false,
                                     AltezzaTextArea             : 0,
                                     LsRateCorrelate             : [],
                                     LsFattureCorrelate          : [],
                                     LsRateFattureFornitoriCorrelate : [],
                                     LsFatturePregresseFornitoriCorrelate : [],
                                     ID_ANAGRAFICA                  : -1,
                                     NO_PRIMA_NOTA               : ''
                                  }                                 
      super.Clear();
   }

   Registra(OnSuccess,OnError)
   {
      if(this.CanRecord())
      {
        var ObjQuery = { Operazioni : [] };
        ObjQuery.Operazioni.push({
                                   Query     : this.IsNuovo() ? "InsertMovimento" : "UpdateMovimento",
                                   Parametri : {
                                                  CHIAVE                     : this.Chiave, 
                                                  DATA                       : TSchedaGenerica.PrepareForRecordString(this.Dati.DATA),
                                                  IMPORTO                    : TSchedaGenerica.PrepareForRecordInteger(this.Dati.IMPORTO * 100) ,
                                                  SOSPESO                    : this.Dati.SOSPESO == ''? null:TSchedaGenerica.PrepareForRecordString(this.Dati.SOSPESO),
                                                  DESCRIZIONE                : TSchedaGenerica.PrepareForRecordString(this.Dati.DESCRIZIONE),
                                                  DATA_CHIUSURA              : TSchedaGenerica.PrepareForRecordDate(this.Dati.DATA_CHIUSURA),
                                                  ID_CATEGORIA_MOVIMENTO     : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.ID_CATEGORIA_MOVIMENTO),
                                                  CONTO_CORRENTE_PRELIEVO    : this.Dati.CkPrelievoEsterno? null : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.CONTO_CORRENTE_PRELIEVO),
                                                  CONTO_CORRENTE_VERSAMENTO  : this.Dati.CkVersamentoEsterno? null : TSchedaGenerica.PrepareForRecordListIndex(this.Dati.CONTO_CORRENTE_VERSAMENTO),
                                                  ID_ANAGRAFICA              : this.Dati.ID_ANAGRAFICA == -1 ? null : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_ANAGRAFICA), 
                                                  NO_PRIMA_NOTA              : TSchedaGenerica.PrepareForRecordBoolean(this.Dati.NO_PRIMA_NOTA),                                            
                                               }
                                 });
        var Self = this

        if(this.Dati.ID_ANAGRAFICA != -1)
        {
          ObjQuery.Operazioni.push({
                                      Query     : "EliminaRecordSaldiChiusureAnnuali",
                                      Parametri : {
                                                    CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_ANAGRAFICA),
                                                    ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                  }
                                  });
        }

        // if(this.Dati.ID_FORNITORE != -1)
        // {
        //   ObjQuery.Operazioni.push({
        //                               Query     : "EliminaRecordSaldiChiusureAnnualiFornitore",
        //                               Parametri : {
        //                                             CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.ID_FORNITORE),
        //                                             ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
        //                                           }
        //                           });
        // }

        this.AdvQuery.PostSQL('MovimentiConti',ObjQuery,function(Response)
        {
          ObjQuery = {};
          if(Self.Chiave == -1)
          {
            Self.Chiave = Response.NewKey1; 
            Self.RegistraCorrelazioneRate(Response.NewKey1, OnError)
          }
          else Self.RegistraCorrelazioneRate(Self.Chiave, OnError)

          Self.CreateSnapshot();
          OnSuccess();
        },
        function(HTTPError,SubHTTPError,Response)
        {
          OnError(HTTPError,SubHTTPError,Response);
        });

      }
   }

   RegistraCorrelazioneRate(ChiaveMovimento, OnError)
   {
      var ObjQuery = { Operazioni : [] };

      for(let i = 0; i < this.Dati.LsRateCorrelate.length; i++ ) 
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });

        if(this.Dati.LsRateCorrelate[i].Selezionata == false) 
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CancellaMovimentoCorrelato",
                                      Parametri : {
                                                    CHIAVE_RATA      : this.Dati.LsRateCorrelate[i].CHIAVE,
                                                  }
                                  });
        }
        else
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CorrelaMovimentoAllaRata",
                                      Parametri : {
                                                    CHIAVE_MOVIMENTO : ChiaveMovimento, 
                                                    CHIAVE_RATA      : this.Dati.LsRateCorrelate[i].CHIAVE,
                                                  }
                                  });
          if(Math.round(this.Dati.LsRateCorrelate[i].IMPORTO_PAGATO * 100) < this.Dati.LsRateCorrelate[i].IMPORTO)
          {
            ObjQuery.Operazioni.push({
                                      Query     : "AggiungiRataDifferenzaMovimento",
                                      Parametri : {
                                                    CHIAVE      : undefined,
                                                    IMPORTO     : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateCorrelate[i].IMPORTO - Math.round(this.Dati.LsRateCorrelate[i].IMPORTO_PAGATO * 100)),
                                                    ID_FATTURA  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateCorrelate[i].ID_FATTURA),
                                                    DATA        : TSchedaGenerica.PrepareForRecordDate(this.Dati.LsRateCorrelate[i].DATA),
                                                    NOTE        : TSchedaGenerica.PrepareForRecordString('Generata dalla correlazione con il movimento'),
                                                  },
                                      ResetKeys : [1]
                                    });
            ObjQuery.Operazioni.push({
                                      Query     : "ModificaImportoRataPerMovimentoConImportoMinore",
                                      Parametri : {
                                                    IMPORTO          : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateCorrelate[i].IMPORTO_PAGATO * 100), 
                                                    CHIAVE_RATA      : this.Dati.LsRateCorrelate[i].CHIAVE,
                                                  }
                                    });
          }
        }
      }

      for(let i = 0; i < this.Dati.LsFattureCorrelate.length; i++ ) 
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnuali",
                                    Parametri : {
                                                  CHIAVE_CLIENTE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFattureCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO           : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });

        if(this.Dati.LsFattureCorrelate[i].Selezionata == false)
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CancellaMovimentoCorrelatoAllaFattura",
                                      Parametri : {
                                                    CHIAVE : this.Dati.LsFattureCorrelate[i].CHIAVE,
                                                  }
                                  });
        }
        else
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CorrelaMovimentoAllaFatturaPregressa",
                                      Parametri : {
                                                    CHIAVE_MOVIMENTO : ChiaveMovimento, 
                                                    CHIAVE           : this.Dati.LsFattureCorrelate[i].CHIAVE,
                                                  }
                                  });

          if(Math.round(this.Dati.LsFattureCorrelate[i].IMPORTO_PAGATO * 100) < this.Dati.LsFattureCorrelate[i].IMPORTO)
          {
            ObjQuery.Operazioni.push({
                                      Query     : "AggiungiFatturaPregressaDifferenzaMovimento",
                                      Parametri : {
                                                    CHIAVE      : undefined,
                                                    IMPORTO     : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFattureCorrelate[i].IMPORTO - Math.round(this.Dati.LsFattureCorrelate[i].IMPORTO_PAGATO * 100)),
                                                    NUMERO      : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFattureCorrelate[i].NUMERO_FATTURA),
                                                    ID_CLIENTE  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFattureCorrelate[i].ID_ANAGRAFICA),
                                                    DATA        : TSchedaGenerica.PrepareForRecordDate(this.Dati.LsFattureCorrelate[i].DATA),
                                                    NOTE        : TSchedaGenerica.PrepareForRecordString('Generata dalla correlazione con il movimento'),
                                                  },
                                      ResetKeys : [1]
                                    });
            ObjQuery.Operazioni.push({
                                      Query     : "ModificaImportoFatturaPregressaPerMovimentoConImportoMinore",
                                      Parametri : {
                                                    IMPORTO          : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFattureCorrelate[i].IMPORTO_PAGATO * 100), 
                                                    CHIAVE_FATTURA   : this.Dati.LsFattureCorrelate[i].CHIAVE,
                                                  }
                                    });
          }
        }
      }

      for(let i = 0; i < this.Dati.LsFatturePregresseFornitoriCorrelate.length; i++ )
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnualiFornitore",
                                    Parametri : {
                                                  CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFatturePregresseFornitoriCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });

        if(this.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata == false)
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CancellaMovimentoCorrelatoAllaFatturaPregressaFornitore",
                                      Parametri : {
                                                    CHIAVE_FATTURA : this.Dati.LsFatturePregresseFornitoriCorrelate[i].CHIAVE,
                                                  }
                                  });
        }
        else
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CorrelaMovimentoAllaFatturaPregressaFornitore",
                                      Parametri : {
                                                    CHIAVE_MOVIMENTO : ChiaveMovimento, 
                                                    CHIAVE           : this.Dati.LsFatturePregresseFornitoriCorrelate[i].CHIAVE,
                                                  }
                                  });
          
          if(Math.round(this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO_PAGATO * 1000) < this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO)
          {
            ObjQuery.Operazioni.push({
                                      Query     : "AggiungiFatturaPregressaDifferenzaMovimentoFornitore",
                                      Parametri : {
                                                    CHIAVE        : undefined,
                                                    IMPORTO       : TSchedaGenerica.PrepareForRecordInteger((this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO - Math.round(this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO_PAGATO * 1000)) / 10),
                                                    NUMERO        : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFatturePregresseFornitoriCorrelate[i].NUMERO_FATTURA),
                                                    ID_FORNITORE  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFatturePregresseFornitoriCorrelate[i].ID_ANAGRAFICA),
                                                    DATA          : TSchedaGenerica.PrepareForRecordDate(this.Dati.LsFatturePregresseFornitoriCorrelate[i].DATA),
                                                    NOTE          : TSchedaGenerica.PrepareForRecordString('Generata dalla correlazione con il movimento'),
                                                  },
                                      ResetKeys : [1]
                                    });
            ObjQuery.Operazioni.push({
                                      Query     : "ModificaImportoFatturaPregressaPerMovimentoConImportoMinoreFornitore",
                                      Parametri : {
                                                    IMPORTO          : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO_PAGATO * 100), 
                                                    CHIAVE_FATTURA   : this.Dati.LsFatturePregresseFornitoriCorrelate[i].CHIAVE,
                                                  }
                                    });
          }
        }
      }

      for(let i = 0; i < this.Dati.LsRateFattureFornitoriCorrelate.length; i++ )
      {
        ObjQuery.Operazioni.push({
                                    Query     : "EliminaRecordSaldiChiusureAnnualiFornitore",
                                    Parametri : {
                                                  CHIAVE_FORNITORE : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateFattureFornitoriCorrelate[i].ID_ANAGRAFICA),
                                                  ANNO             : TSchedaGenerica.PrepareForRecordString(TZDateFunct.FormatDateTime('yyyy',TZDateFunct.DateFromHTMLInput(this.Dati.DATA)))
                                                }
                                });

        if(this.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata == false)
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CancellaMovimentoCorrelatoAllaFatturaFornitore",
                                      Parametri : {
                                                    CHIAVE_RATA : this.Dati.LsRateFattureFornitoriCorrelate[i].CHIAVE,
                                                  }
                                  });
        }
        else
        {
          ObjQuery.Operazioni.push({
                                      Query     : "CorrelaMovimentoAllaFatturaFornitore",
                                      Parametri : {
                                                    CHIAVE_MOVIMENTO : ChiaveMovimento, 
                                                    CHIAVE_RATA      : this.Dati.LsRateFattureFornitoriCorrelate[i].CHIAVE,
                                                  }
                                  });
          
          if(Math.round(this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO_PAGATO * 1000) < this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO)
          {
            ObjQuery.Operazioni.push({
                                      Query     : "AggiungiRataDifferenzaMovimentoFornitore",
                                      Parametri : {
                                                    CHIAVE              : undefined,
                                                    IMPORTO             : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO - Math.round(this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO_PAGATO * 1000)),
                                                    ID_FATTURA_PASSIVA  : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateFattureFornitoriCorrelate[i].ID_FATTURA_PASSIVA),
                                                    DATA_SCADENZA       : TSchedaGenerica.PrepareForRecordDate(this.Dati.LsRateFattureFornitoriCorrelate[i].DATA),
                                                    NOTE                : TSchedaGenerica.PrepareForRecordString('Generata dalla correlazione con il movimento'),
                                                  },
                                      ResetKeys : [1]
                                    });
            ObjQuery.Operazioni.push({
                                      Query     : "ModificaImportoRataPerMovimentoConImportoMinoreFornitore",
                                      Parametri : {
                                                    IMPORTO          : TSchedaGenerica.PrepareForRecordInteger(this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO_PAGATO * 1000), 
                                                    CHIAVE_RATA      : this.Dati.LsRateFattureFornitoriCorrelate[i].CHIAVE,
                                                  }
                                    });
          }
        }
      }

      if(ObjQuery.Operazioni.length != 0)
      {
        this.AdvQuery.PostSQL('MovimentiConti',ObjQuery,function()
        {
        },
        function(HTTPError,SubHTTPError,Response)
        {
          OnError(HTTPError,SubHTTPError,Response);
        });
      }
   }

   AssignSchedaCliente(Scheda)
   {  
      this.Dati.ID_ANAGRAFICA  = Scheda.Chiave
   }

   GetTotaleDocumentiCorrelati()
   {
    this.Dati.IMPORTO = 0

    if(this.Dati.LsFattureCorrelate.length != 0)
      for( let i = 0; i < this.Dati.LsFattureCorrelate.length; i++)
        if(this.Dati.LsFattureCorrelate[i].Selezionata)
        {
          if(this.Dati.LsFattureCorrelate[i].IMPORTO_PAGATO != undefined)
            this.Dati.IMPORTO += this.Dati.LsFattureCorrelate[i].IMPORTO_PAGATO * 100  
          else this.Dati.IMPORTO += this.Dati.LsFattureCorrelate[i].IMPORTO
        }
    
    if(this.Dati.LsRateCorrelate.length != 0)
      for( let i = 0; i < this.Dati.LsRateCorrelate.length; i++)
        if(this.Dati.LsRateCorrelate[i].Selezionata)
        {
          if(this.Dati.LsRateCorrelate[i].IMPORTO_PAGATO != undefined)
            this.Dati.IMPORTO += this.Dati.LsRateCorrelate[i].IMPORTO_PAGATO * 100  
          else this.Dati.IMPORTO += this.Dati.LsRateCorrelate[i].IMPORTO
        }


    if(this.Dati.LsFatturePregresseFornitoriCorrelate.length != 0)
      for( let i = 0; i < this.Dati.LsFatturePregresseFornitoriCorrelate.length; i++)
        if(this.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata)
        {
          if(this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO_PAGATO != undefined)
            this.Dati.IMPORTO += this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO_PAGATO * 1000  
          else this.Dati.IMPORTO += this.Dati.LsFatturePregresseFornitoriCorrelate[i].IMPORTO
        }


    if(this.Dati.LsRateFattureFornitoriCorrelate.length != 0)
      for( let i = 0; i < this.Dati.LsRateFattureFornitoriCorrelate.length; i++)
        if(this.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata)
        {
          if(this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO_PAGATO != undefined)
            this.Dati.IMPORTO += this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO_PAGATO * 1000  
          else this.Dati.IMPORTO += this.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO
        }

    if(this.Dati.LsRateFattureFornitoriCorrelate.length != 0)
    {
      if(this.Dati.IMPORTO != 0)
        this.Dati.IMPORTO = Math.round(this.Dati.IMPORTO) / 1000
    }
    else if(this.Dati.LsFatturePregresseFornitoriCorrelate.length != 0)
    {
      if(this.Dati.IMPORTO != 0)
        this.Dati.IMPORTO = Math.round(this.Dati.IMPORTO) / 1000
    }
    else if(this.Dati.IMPORTO != 0)
      this.Dati.IMPORTO = Math.round(this.Dati.IMPORTO) / 100

   }

   Nuovo()
   {
      this.GetContiCorrentiCasse()
      super.Nuovo()
   }

   Disponi(OnSuccess)
   {
      var Self = this;
      this.Dati.LsRateCorrelate             = []
      this.Dati.LsFattureCorrelate          = []
      this.Dati.LsRateFattureFornitoriCorrelate = []
      this.Dati.LsFatturePregresseFornitoriCorrelate = []
      this.GetContiCorrentiCasse(function()
      {
        if(Self.InEliminazione) return;
        Self.AdvQuery.GetSQL('MovimentiConti',{ CHIAVE : Self.Chiave },
                                        function(Results)
                                        {
                                            if(Self.InEliminazione) return;
                                            let ArrayInfo                              = Self.AdvQuery.FindResults(Results,"SelectMovimento");
                                            let ArrayInfoRateCorrelate                 = Self.AdvQuery.FindResults(Results,"RateCorrelate");
                                            let ArrayInfoFattureCorrelate              = Self.AdvQuery.FindResults(Results,"FatturePregresseCorrelate");
                                            let ArrayInfoFattureCorrelateFornitori     = Self.AdvQuery.FindResults(Results,"FatturePregresseCorrelateFornitori");
                                            let ArrayInfoRateFattureCorrelateFornitori = Self.AdvQuery.FindResults(Results,"RateFatturePregresseCorrelateFornitori");

                                            if(ArrayInfo != undefined && 
                                               ArrayInfoRateCorrelate != undefined && 
                                               ArrayInfoFattureCorrelate != undefined && 
                                               ArrayInfoFattureCorrelateFornitori != undefined &&
                                               ArrayInfoRateFattureCorrelateFornitori != undefined)
                                            {
                                              if(ArrayInfo.length != 0)
                                              {
                                                Self.Dati = 
                                                { 
                                                    CHIAVE                    : Self.Chiave, 
                                                    DATA                      : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DATA),
                                                    IMPORTO                   : parseFloat((TSchedaGenerica.DisponiFromInteger(ArrayInfo[0].IMPORTO) / 100).toFixed(2)),
                                                    SOSPESO                   : TSchedaGenerica.DisponiFromString(ArrayInfo[0].SOSPESO),
                                                    DESCRIZIONE               : TSchedaGenerica.DisponiFromString(ArrayInfo[0].DESCRIZIONE),
                                                    DATA_CHIUSURA             : TSchedaGenerica.DisponiFromDate(ArrayInfo[0].DATA_CHIUSURA),
                                                    ID_CATEGORIA_MOVIMENTO    : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_CATEGORIA_MOVIMENTO),
                                                    CONTO_CORRENTE_PRELIEVO   : ArrayInfo[0].CONTO_CORRENTE_PRELIEVO != undefined? TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].CONTO_CORRENTE_PRELIEVO) : -1,
                                                    CONTO_CORRENTE_VERSAMENTO : ArrayInfo[0].CONTO_CORRENTE_VERSAMENTO != undefined? TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].CONTO_CORRENTE_VERSAMENTO) : -1,
                                                    ID_ANAGRAFICA             : Self.Dati.ID_ANAGRAFICA == null ? -1 : TSchedaGenerica.DisponiFromListIndex(ArrayInfo[0].ID_ANAGRAFICA),
                                                    NO_PRIMA_NOTA             : TSchedaGenerica.DisponiFromBoolean(ArrayInfo[0].NO_PRIMA_NOTA)
                                                }

                                                Self.Dati.LsRateCorrelate = []
                                                if(ArrayInfoRateCorrelate.length != 0)
                                                {
                                                  Self.Dati.LsRateCorrelate = Self.Dati.LsRateCorrelate.concat(ArrayInfoRateCorrelate)
                                                  Self.BloccoPrelievoDa = true
                                                  Self.Dati.LsRateCorrelate.forEach(function(Rata)
                                                  {
                                                    Rata.Selezionata = true
                                                  })
                                                }

                                                Self.Dati.LsFattureCorrelate = []
                                                if(ArrayInfoFattureCorrelate.length != 0)
                                                {
                                                  Self.Dati.LsFattureCorrelate = Self.Dati.LsFattureCorrelate.concat(ArrayInfoFattureCorrelate)
                                                  Self.BloccoPrelievoDa = true
                                                  Self.Dati.LsFattureCorrelate.forEach(function(FatturaPregressa)
                                                  {
                                                    FatturaPregressa.Selezionata = true
                                                  })
                                                }

                                                Self.Dati.LsFatturePregresseFornitoriCorrelate = []
                                                if(ArrayInfoFattureCorrelateFornitori.length != 0)
                                                {
                                                  Self.Dati.LsFatturePregresseFornitoriCorrelate = Self.Dati.LsFatturePregresseFornitoriCorrelate.concat(ArrayInfoFattureCorrelateFornitori)
                                                  Self.BloccoVersamentoIn = true
                                                  Self.Dati.LsFatturePregresseFornitoriCorrelate.forEach(function(FatturaPregressa)
                                                  {
                                                    FatturaPregressa.Selezionata = true
                                                  })
                                                }

                                                Self.Dati.LsRateFattureFornitoriCorrelate = []
                                                if(ArrayInfoRateFattureCorrelateFornitori.length != 0)
                                                {
                                                  Self.Dati.LsRateFattureFornitoriCorrelate = Self.Dati.LsRateFattureFornitoriCorrelate.concat(ArrayInfoRateFattureCorrelateFornitori)
                                                  Self.BloccoVersamentoIn = true
                                                  Self.Dati.LsRateFattureFornitoriCorrelate.forEach(function(Rata)
                                                  {
                                                    Rata.Selezionata = true
                                                  })
                                                }

                                                if(Self.Dati.CONTO_CORRENTE_PRELIEVO == -1)
                                                   Self.Dati.CkPrelievoEsterno = true
                                                else Self.Dati.CkPrelievoDaConto = true
                                                if(Self.Dati.CONTO_CORRENTE_VERSAMENTO == -1)
                                                   Self.Dati.CkVersamentoEsterno = true
                                                else Self.Dati.CkVersamentoDaConto = true
                                                Self.CreateSnapshot();
                                              }
                                              else SystemInformation.HandleError('Movimento/Conto eliminato')
                                            }
                                            else SystemInformation.HandleError('Impossibile ottenere il dettaglio del movimento/conto');

                                            if(OnSuccess != undefined)
                                              OnSuccess()
                                        },
                                        function(HTTPError,SubHTTPError,Response)
                                        {
                                          SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                                        },
                                        'SelectDettaglioMovimento')
      })
   }

   GetContiCorrentiCasse(OnSuccess)
   {
      var Self = this
      this.AdvQuery.GetSQL('MovimentiConti',{},
                            function(Results)
                            {
                              if(Self.InEliminazione) return;
                              let ArrayInfo = Self.AdvQuery.FindResults(Results,"SelectContiCasse");
                              if(ArrayInfo != undefined)
                              {
                                  Self.LsContiCorrentiCasse = ArrayInfo
                                  if(OnSuccess != undefined)
                                    OnSuccess()
                              }
                              else SystemInformation.HandleError('Impossibile ottenere conti/casse');
                            },
                            function(HTTPError,SubHTTPError,Response)
                            {
                              SystemInformation.HandleError(HTTPError,SubHTTPError,Response);
                            })
   }

  GetFiltriAbilitati(OnSuccess)
  {
    var Anno = new Date(this.Dati.DATA)
    var Mese = Anno.getMonth()
    let MesiInIta = TZDateFunct.GetMesiInItaliano()
    Mese = MesiInIta[Mese]

    OnSuccess([{
                Name : DASHBOARD_FILTER_TYPES.MovimentiConti,
                Positions : [
                                this.Dati.CONTO_CORRENTE_PRELIEVO != -1? this.Dati.CONTO_CORRENTE_PRELIEVO : this.Dati.CONTO_CORRENTE_VERSAMENTO,
                                Anno.getFullYear(),
                                Mese,
                                this.Chiave
                            ]
            }])      


  }

}

export default 
{
  components : 
  {
    VUEModal,
    VUEInputFornitore,
    VUEInputClienti,
  },
  data()
  {
      return { 
                    Tabs                                              : {
                                                                          ActiveTab : 'Generale',
                                                                          Tabs      : [
                                                                                        {
                                                                                          Caption : 'Generale',
                                                                                          Id      : 'Generale'
                                                                                        },
                                                                                      ]
                                                                        },
                    CatMovimenti                                      : SystemInformation.Configurazioni.CatMovimenti,
                    ListaRate                                         : [],
                    ListaFatturePregresse                             : [],
                    ListaFatturePregresseFornitori                    : [],
                    ListaRateFattureFornitori                         : [],
                    FiltroRagioneSociale                              : '',
                    FiltroRagioneSocialeFatturePregresse              : '',
                    FiltroRagioneSocialeRateFattureFornitori          : '',
                    FiltroRagioneSocialeFatturePregresseFornitori     : '',
                    PopupMovimentoCorrelato                           : false,
                    PopupMovimentoCorrelatoAFatturePregresse          : false,
                    PopupMovimentoCorrelatoARateFattureFornitori      : false,
                    PopupMovimentoCorrelatoAFatturePregresseFornitori : false,
                    VettoreCopiaPerAnnulla                            : [],
                    PresenzaFattureAttive                             : true,
                    PresenzaFatturePassive                            : true,
                    TipoRataFattPassiva                               : 'D', // nei modelli
                    TipoRataFatturaAttiva                             : 'R', // nei modelli
                    TipoFatturaPregressa                              : 'F',
                    TipoFatturaPregressaFornitori                     : 'P',
                    PopupAssociazione                                 : false,
                    TipoSelezione                                     : '' ,
                    IDClienteTemporaneo                               : -1,
                    IDFornitoreTemporaneo                             : -1,
                    PopupGestisciImporti                              : false,
                    PopupGestisciImportiFornitori                     : false,
                    NomeProgramma                                     : NOME_PROGRAMMA
             };
  },
  props : ['SchedaMovimento'],
  computed :
  {
    // RagioneSocialeAssociato :
    // {
    //   get()
    //   {
    //     return SystemInformation.GetRagioneSocialeFornitore(this.CurrentSchedaMovimento.Dati.ID_FORNITORE)
    //   }
    // }, 

    TotaleRateFattureFornitoriFiltrate : 
      {
        get()
        {
          var Totale = 0
          for(let i = 0; i < this.ListaRateFattureFornitori.length; i++)
          {
            if (this.ListaRateFattureFornitori[i].Selezionata)
              Totale += this.ListaRateFattureFornitori[i].IMPORTO
          }
          return (Totale / 1000)
        }
      },

      TotaleRateFiltrate : 
      {
        get()
        {
          var Totale = 0
          for(let i = 0; i < this.ListaRate.length; i++)
          {
            if (this.ListaRate[i].Selezionata)
              Totale += this.ListaRate[i].IMPORTO
          }
          return (Totale / 100)
        }
      },

      TotaleFatturePregresseFornitoriFiltrate : 
      {
        get()
        {
          var Totale = 0
          for(let i = 0; i < this.ListaFatturePregresseFornitori.length; i++)
          {
            if (this.ListaFatturePregresseFornitori[i].Selezionata)
              Totale += this.ListaFatturePregresseFornitori[i].IMPORTO
          }
          return (Totale / 1000)
        }
      },

      TotaleFatturePregresseFiltrate : 
      {
        get()
        {
          var Totale = 0
          for(let i = 0; i < this.ListaFatturePregresse.length; i++)
          {
            if (this.ListaFatturePregresse[i].Selezionata)
              Totale += this.ListaFatturePregresse[i].IMPORTO
          }
          return (Totale / 100)
        }
      },

      RagioneSocialeAssociato :
      {
        get()
        {
          return SystemInformation.GetRagioneSociale(this.CurrentSchedaMovimento.Dati.ID_ANAGRAFICA)
        }
      },

      CurrentSchedaMovimento : 
      {
         get()
         {
           return this.SchedaMovimento;
         }
      },

      RateFiltrate :
      {
        get()
        {
          var Filtro = this.FiltroRagioneSociale.toUpperCase().trim();
          var ListaRighe = []

          if(Filtro == '')
          {
            return ListaRighe.concat(this.ListaRate)
          }
          else 
          {
            ListaRighe = this.ListaRate.filter(function(Rata)
            {
              if(TZStringFunct.RicercaParoleInStringa(Rata.RAGIONE_SOCIALE,Filtro))
                return true;

              if(TZStringFunct.RicercaParoleInStringa(Rata.CODICE,Filtro))
                return true;

              return false;
            })
            return ListaRighe
          }
        }
      },

      RateFattureFornitoriFiltrate :
      {
        get()
        {
          var Filtro = this.FiltroRagioneSocialeRateFattureFornitori.toUpperCase().trim();
          var ListaRighe = []
          if(Filtro == '')
          {
            return ListaRighe.concat(this.ListaRateFattureFornitori)
          }
          else 
          {
            ListaRighe = this.ListaRateFattureFornitori.filter(function(Rata)
            {
              if(TZStringFunct.RicercaParoleInStringa(Rata.RAGIONE_SOCIALE,Filtro))
                return true;
                            
              if(TZStringFunct.RicercaParoleInStringa(Rata.CODICE,Filtro))
                return true;

              return false;
            })
            return ListaRighe
          }
        }
      },

      FatturePregresseFiltrate :
      {
        get()
        {
          var Filtro = this.FiltroRagioneSocialeFatturePregresse.toUpperCase().trim();
          var ListaRighe = []

          if(Filtro == '')
            return ListaRighe.concat(this.ListaFatturePregresse)
          else 
          {
            ListaRighe = this.ListaFatturePregresse.filter(function(FatturaPregressa)
            {
              if(TZStringFunct.RicercaParoleInStringa(FatturaPregressa.RAGIONE_SOCIALE,Filtro))
                return true;
                          
              if(TZStringFunct.RicercaParoleInStringa(FatturaPregressa.CODICE,Filtro))
                return true;

              return false;
            })
            return ListaRighe
          }
        }
      },

      FatturePregresseFornitoriFiltrate :
      {
        get()
        {
          var Filtro = this.FiltroRagioneSocialeFatturePregresseFornitori.toUpperCase().trim();
          var ListaRighe = []

          if(Filtro == '')
            return ListaRighe.concat(this.ListaFatturePregresseFornitori)
          else 
          {
            ListaRighe = this.ListaFatturePregresseFornitori.filter(function(FatturaPregressaFornitore)
            {
              if(TZStringFunct.RicercaParoleInStringa(FatturaPregressaFornitore.RAGIONE_SOCIALE,Filtro))
                return true;
                                          
              if(TZStringFunct.RicercaParoleInStringa(FatturaPregressaFornitore.CODICE,Filtro))
                return true;
              
              return false;
            })
            return ListaRighe
          }
        }
      },

      VisualizzaSoloSelezionati :
      {
        get()
        {
          var ListaRighe                 = []
          var ListaRigheFatture          = []
          var ListaRigheFattureFornitori = []
          var ListaRigheFatturePregresseFornitori = []
          var Self = this

          if(this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length != 0)
            ListaRighe = this.CurrentSchedaMovimento.Dati.LsRateCorrelate.filter(function(Rata)
            {
              if(Rata.Selezionata == false)
                return false;
              Self.PresenzaFatturePassive = false
              return true;
            })

          if(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length != 0)
            ListaRigheFatture = this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.filter(function(FatturaPregressa)
            {
              if(FatturaPregressa.Selezionata == false)
                return false;
              Self.PresenzaFatturePassive = false
              return true;
            })
          
          if(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length != 0)
            ListaRigheFatturePregresseFornitori = this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.filter(function(FatturaPregressaFornitore)
            {
              if(FatturaPregressaFornitore.Selezionata == false)
                return false;
              Self.PresenzaFattureAttive = false
              return true;
            })

          if(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length != 0)
            ListaRigheFattureFornitori = this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.filter(function(FatturaFornitore)
            {
              if(FatturaFornitore.Selezionata == false)
                return false;
              Self.PresenzaFattureAttive = false
              return true;
            })

          
          var Result = ListaRighe.concat(ListaRigheFatture, ListaRigheFatturePregresseFornitori, ListaRigheFattureFornitori) 
          if(Result.length == 0)
          {
            Self.PresenzaFattureAttive  = true
            Self.PresenzaFatturePassive = true
          }
          
          return Result
        }
      },

      RateSelezionate :
      {
        get()
        {
          var ListaRighe = []
          ListaRighe = this.ListaRate.filter(function(Rata)
          {
            if(Rata.Selezionata)
              return true;
            return false;
          })
          return ListaRighe
        }
      },

      FattureSelezionate :
      {
        get()
        {
          var ListaRighe = []
          ListaRighe = this.ListaFatturePregresse.filter(function(FatturaPregressa)
          {
            if(FatturaPregressa.Selezionata)
              return true;
            return false;
          })
          return ListaRighe
        }
      },

      FatturePregresseFornitoriSelezionate :
      {
        get()
        {
          var ListaRighe = []
          ListaRighe = this.ListaFatturePregresseFornitori.filter(function(FatturaPregressaFornitore)
          {
            if(FatturaPregressaFornitore.Selezionata)
              return true;
            return false;
          })
          return ListaRighe
        }
      },

      RateFattureFornitoriSelezionate :
      {
        get()
        {
          var ListaRighe = []
          ListaRighe = this.ListaRateFattureFornitori.filter(function(FatturaFornitore)
          {
            if(FatturaFornitore.Selezionata)
              return true;
            return false;
          })
          return ListaRighe
        }
      },

    },

    methods: 
    {
      getNomeConto(ContoCassa, value) 
      {
        if (value == '') 
        {
          return ('Cassa - ' + ContoCassa.DESCRIZIONE);
        }
        else 
        {
          return ('Conto corrente - ' + ContoCassa.BANCA + ' - ' + ContoCassa.NR_CONTO);
        }
      },

      CambioDataScadenza(Data)
      {
        return TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Data))
      },

      ChangeRadioPrelievo()
      {
        if(this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno)
          this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno = !this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno
      },

      ChangeRadioVersamento()
      {
        if(this.CurrentSchedaMovimento.Dati.CkVersamentoEsterno)
          this.CurrentSchedaMovimento.Dati.CkVersamentoEsterno = !this.CurrentSchedaMovimento.Dati.CkVersamentoEsterno
      },

      EsternoSelezionato()
      {
        if(this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno)
        {
          this.CurrentSchedaMovimento.Dati.CONTO_CORRENTE_PRELIEVO = -1
          this.CurrentSchedaMovimento.Dati.CkPrelievoDaConto = false
        }
        if(this.CurrentSchedaMovimento.Dati.CkVersamentoEsterno)
        {
          this.CurrentSchedaMovimento.Dati.CONTO_CORRENTE_VERSAMENTO = -1
          this.CurrentSchedaMovimento.Dati.CkVersamentoDaConto = false
        }
      },

      OnClickChiudiModalRate()
      {
        this.PopupMovimentoCorrelato  = false
        this.FiltroRagioneSociale     = ''

        if(this.VettoreCopiaPerAnnulla.length != 0)
        {
          this.CurrentSchedaMovimento.Dati.LsRateCorrelate = []
          for(let i = 0; i < this.VettoreCopiaPerAnnulla.length; i++)
            this.CurrentSchedaMovimento.Dati.LsRateCorrelate.push(this.VettoreCopiaPerAnnulla[i]) 
        }
      },

      OnClickChiudiModalFatturePregresse()
      {
        this.PopupMovimentoCorrelatoAFatturePregresse = false
        this.FiltroRagioneSocialeFatturePregresse     = ''

        if(this.VettoreCopiaPerAnnulla.length != 0)
        {
          this.CurrentSchedaMovimento.Dati.LsFattureCorrelate = []
          for(let i = 0; i < this.VettoreCopiaPerAnnulla.length; i++)
            this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.push(this.VettoreCopiaPerAnnulla[i]) 
        }
      },

      OnClickChiudiModalFatturePregresseFornitori()
      {
        this.PopupMovimentoCorrelatoAFatturePregresseFornitori = false
        this.FiltroRagioneSocialeFatturePregresseFornitori     = ''

        if(this.VettoreCopiaPerAnnulla.length != 0)
        {
          this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate = []
          for(let i = 0; i < this.VettoreCopiaPerAnnulla.length; i++)
            this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.push(this.VettoreCopiaPerAnnulla[i]) 
        }
      },

      OnClickChiudiModalFattureFornitori()
      {
        this.PopupMovimentoCorrelatoARateFattureFornitori = false
        this.FiltroRagioneSocialeRateFattureFornitori     = ''

        if(this.VettoreCopiaPerAnnulla.length != 0)
        {
          this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate = []
          for(let i = 0; i < this.VettoreCopiaPerAnnulla.length; i++)
            this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.push(this.VettoreCopiaPerAnnulla[i]) 
        }
      },

      OnClickApriPopupAssociazione()
      {
        this.PopupAssociazione = true
         
        this.IDClienteTemporaneo = this.CurrentSchedaMovimento.Dati.ID_ANAGRAFICA
        this.IDFornitoreTemporaneo = this.CurrentSchedaMovimento.Dati.ID_ANAGRAFICA
      
        if(this.IDClienteTemporaneo == -1 && this.IDFornitoreTemporaneo == -1)
        {
          this.TipoSelezione = 'NessunaAssociazione'
        }
        else 
        {
          if(this.IDClienteTemporaneo != -1)
          {
            this.TipoSelezione = 'Cliente'
          }
          else this.TipoSelezione = 'Fornitore'      
        }
      },

      OnClickCorrelaAFatture()
      {
        let Self = this
        this.CaricaRate(function()
        {
          Self.PopupMovimentoCorrelato = true
        })
      },

      OnClickCorrelaAFatturePregresse()
      {
        let Self = this
        this.CaricaFatturePregresse(function()
        {
          Self.PopupMovimentoCorrelatoAFatturePregresse = true
        })
      },

      OnClickCorrelaAFatturePregresseFornitori()
      {
        let Self = this
        this.CaricaFatturePregresseFornitori(function()
        {
          Self.PopupMovimentoCorrelatoAFatturePregresseFornitori = true
        })
      },

      OnClickCorrelaARateFatturePregresseFornitori()
      {
        let Self = this
        this.CaricaRateFattureFornitori(function()
        {
          Self.PopupMovimentoCorrelatoARateFattureFornitori = true
        })
      },

      CaricaFatturePregresseFornitori(OnSuccess)
      {
        var Self = this;
        this.VettoreCopiaPerAnnulla = []
        if(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length != 0)
          for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length; i++)
            this.VettoreCopiaPerAnnulla.push(TZGenericFunct.GetCopyOfObject(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i], null))
        SystemInformation.AdvQuery.GetSQL('MovimentiConti', {}, 
                                            function (Results) 
                                            {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "Lista");
                                              if(ArrayInfo != undefined) 
                                              {
                                                Self.ListaFatturePregresseFornitori = ArrayInfo
                                                if(Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length != 0)
                                                {
                                                  for(let i = 0; i < Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length; i++)
                                                  {
                                                    let Trovato = false;
                                                    for(let j = 0; j < Self.ListaFatturePregresseFornitori.length; j++)
                                                    {
                                                      if(Self.ListaFatturePregresseFornitori[j].CHIAVE == Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].CHIAVE)
                                                      {
                                                        Trovato = true
                                                        if(Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata != undefined)
                                                          Self.ListaFatturePregresseFornitori[j].Selezionata = Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata
                                                        else Self.ListaFatturePregresseFornitori[j].Selezionata = true
                                                      }
                                                    } 
                                                    if(!Trovato)
                                                    {
                                                      if(Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata == undefined)
                                                        Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata = true
                                                      Self.ListaFatturePregresseFornitori.unshift(Self.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i])
                                                    }
                                                  }
                                                }
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere lista delle fatture pregresse dei fornitori');
                                            },
                                            function (HTTPError, SubHTTPError, Response) 
                                            {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                            },
                                            'ListaFatturePregresseFornitoriDaCorrelare')  
      },

      CaricaRateFattureFornitori(OnSuccess)
      {
        var Self = this;
        this.VettoreCopiaPerAnnulla = []
        if(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length != 0)
          for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length; i++)
            this.VettoreCopiaPerAnnulla.push(TZGenericFunct.GetCopyOfObject(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i], null))
        SystemInformation.AdvQuery.GetSQL('MovimentiConti', {}, 
                                            function (Results) 
                                            {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "FiltroRateNonPagateFattureFornitoriNumerate");
                                              if(ArrayInfo != undefined) 
                                              {
                                                Self.ListaRateFattureFornitori = ArrayInfo
                                                if(Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length != 0)
                                                {
                                                  for(let i = 0; i < Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length; i++)
                                                  {
                                                    let Trovato = false;
                                                    for(let j = 0; j < Self.ListaRateFattureFornitori.length; j++)
                                                    {
                                                      if(Self.ListaRateFattureFornitori[j].CHIAVE == Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].CHIAVE)
                                                      {
                                                        Trovato = true
                                                        if(Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata != undefined)
                                                          Self.ListaRateFattureFornitori[j].Selezionata = Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata
                                                        else Self.ListaRateFattureFornitori[j].Selezionata = true
                                                        if(Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO_PAGATO != undefined)
                                                          Self.ListaRateFattureFornitori[j].IMPORTO_PAGATO = Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].IMPORTO_PAGATO
                                                      }
                                                    } 
                                                    if(!Trovato)
                                                    {
                                                      if(Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata == undefined)
                                                        Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata = true
                                                      Self.ListaRateFattureFornitori.unshift(Self.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i])
                                                    }
                                                  }
                                                }
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere lista delle rate');
                                            },
                                            function (HTTPError, SubHTTPError, Response) 
                                            {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                            },
                                            'FiltroRateNonPagateFattureFornitoriNumerate')  
      },

      CaricaRate(OnSuccess)
      {
        var Self = this;
        this.VettoreCopiaPerAnnulla = []
        if(this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length != 0)
          for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length; i++)
            this.VettoreCopiaPerAnnulla.push(TZGenericFunct.GetCopyOfObject(this.CurrentSchedaMovimento.Dati.LsRateCorrelate[i], null))
        SystemInformation.AdvQuery.GetSQL('MovimentiConti', {}, 
                                            function (Results) 
                                            {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "FiltroRateNonPagateFattureNumerate");
                                              if(ArrayInfo != undefined) 
                                              {
                                                Self.ListaRate = ArrayInfo
                                                if(Self.CurrentSchedaMovimento.Dati.LsRateCorrelate.length != 0)
                                                {
                                                  for(let i = 0; i < Self.CurrentSchedaMovimento.Dati.LsRateCorrelate.length; i++)
                                                  {
                                                    let Trovato = false;
                                                    for(let j = 0; j < Self.ListaRate.length; j++)
                                                    {
                                                      if(Self.ListaRate[j].CHIAVE == Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].CHIAVE)
                                                      {
                                                        Trovato = true
                                                        if(Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].Selezionata != undefined)
                                                          Self.ListaRate[j].Selezionata = Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].Selezionata
                                                        else Self.ListaRate[j].Selezionata = true
                                                        if(Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].IMPORTO_PAGATO != undefined)
                                                          Self.ListaRate[j].IMPORTO_PAGATO = Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].IMPORTO_PAGATO
                                                      }
                                                    } 
                                                    if(!Trovato)
                                                    {
                                                      if(Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].Selezionata == undefined)
                                                        Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].Selezionata = true
                                                      Self.ListaRate.unshift(Self.CurrentSchedaMovimento.Dati.LsRateCorrelate[i])
                                                    }
                                                  }
                                                }
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere lista delle rate');
                                            },
                                            function (HTTPError, SubHTTPError, Response) 
                                            {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                            },
                                            'FiltroRateNonPagateFattureNumerate')  
      },

      CaricaFatturePregresse(OnSuccess)
      {
        var Self = this;
        this.VettoreCopiaPerAnnulla = []
        if(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length != 0)
          for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length; i++)
            this.VettoreCopiaPerAnnulla.push(TZGenericFunct.GetCopyOfObject(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i], null))
        SystemInformation.AdvQuery.GetSQL('MovimentiConti', {}, 
                                            function (Results) 
                                            {
                                              let ArrayInfo = SystemInformation.AdvQuery.FindResults(Results, "Lista");
                                              if(ArrayInfo != undefined) 
                                              {
                                                Self.ListaFatturePregresse = ArrayInfo
                                                if(Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length != 0)
                                                {
                                                  for(let i = 0; i < Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length; i++)
                                                  {
                                                    let Trovato = false;
                                                    for(let j = 0; j < Self.ListaFatturePregresse.length; j++)
                                                    {
                                                      if(Self.ListaFatturePregresse[j].CHIAVE == Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].CHIAVE)
                                                      {
                                                        Trovato = true
                                                        if(Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].Selezionata != undefined)
                                                          Self.ListaFatturePregresse[j].Selezionata = Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].Selezionata
                                                        else Self.ListaFatturePregresse[j].Selezionata = true
                                                      }
                                                    } 
                                                    if(!Trovato)
                                                    {
                                                      if(Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].Selezionata == undefined)
                                                        Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].Selezionata = true
                                                      Self.ListaFatturePregresse.unshift(Self.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i])
                                                    }
                                                  }
                                                }
                                                if(OnSuccess != undefined)
                                                  OnSuccess()
                                              }
                                              else SystemInformation.HandleError('Impossibile ottenere lista delle fatture pregresse');
                                            },
                                            function (HTTPError, SubHTTPError, Response) 
                                            {
                                              SystemInformation.HandleError(HTTPError, SubHTTPError, Response);
                                            },
                                            'ListaFatturePregresseDaCorrelare')  
      },

      GetImportoRata(Rata)
      {
        switch(Rata.TIPO_RATE)
        {
          case this.TipoRataFattPassiva   : return Rata.IMPORTO_PAGATO != null? (Rata.IMPORTO_PAGATO).toFixed(2) : (Rata.IMPORTO / 1000).toFixed(2)
          case this.TipoRataFatturaAttiva : return Rata.IMPORTO_PAGATO != null? (Rata.IMPORTO_PAGATO).toFixed(2) : (Rata.IMPORTO / 100).toFixed(2)
          case this.TipoFatturaPregressa  : return Rata.IMPORTO_PAGATO != null? (Rata.IMPORTO_PAGATO).toFixed(2) : (Rata.IMPORTO / 100).toFixed(2)
          case this.TipoFatturaPregressaFornitori  : return Rata.IMPORTO_PAGATO != null? (Rata.IMPORTO_PAGATO).toFixed(2) : (Rata.IMPORTO / 1000).toFixed(2)
          default                         : return (Rata.IMPORTO / 100).toFixed(2)
        }
      },

      GetSommaTotaleFatture(ListaRateFatture)
      {
        let Result = 0
        
        ListaRateFatture.forEach(function(Rata)
        {
            Result += Rata.IMPORTO
        })
        
        return Result / 100
      },

      GetSommaTotaleFattureFornitori(ListaRateFatture)
      {
        let Result = 0
        
        ListaRateFatture.forEach(function(Rata)
        {
            Result += Rata.IMPORTO
        })
        
        return Result / 1000
      },

      GetSommaTotalePagatoFatture(ListaRateFatture)
      {
        let Result = 0
        
        ListaRateFatture.forEach(function(Rata)
        {
          if(Rata.IMPORTO_PAGATO != undefined)
            Result += Rata.IMPORTO_PAGATO_TEMPORANEO
          else Result += Rata.IMPORTO
        })
        
        return Result
      },

      OnClickConfermaImportiFatture()
      {
        this.VisualizzaSoloSelezionati.forEach(function(Elemento)
        {
          Elemento.IMPORTO_PAGATO = Elemento.IMPORTO_PAGATO_TEMPORANEO
        })

        this.PopupGestisciImporti = false
        this.CurrentSchedaMovimento.GetTotaleDocumentiCorrelati()
      },

      OnClickConfermaImportiFattureFornitori()
      {
        this.VisualizzaSoloSelezionati.forEach(function(Elemento)
        {
          Elemento.IMPORTO_PAGATO = Elemento.IMPORTO_PAGATO_TEMPORANEO
        })

        this.PopupGestisciImportiFornitori = false
        this.CurrentSchedaMovimento.GetTotaleDocumentiCorrelati()
      },
      
      OnClickModificaImportiSelezionate()
      {
        if(this.VisualizzaSoloSelezionati.length != 0)
        {
          this.PopupGestisciImporti = true
          this.VisualizzaSoloSelezionati.forEach(function(Elemento)
          {
            if(Elemento.IMPORTO_PAGATO == null)
              Elemento.IMPORTO_PAGATO = Elemento.IMPORTO / 100

            Elemento.IMPORTO_PAGATO_TEMPORANEO = Elemento.IMPORTO_PAGATO
          })
        }
      },

      OnClickModificaImportiSelezionateFornitori()
      {
        if(this.VisualizzaSoloSelezionati.length != 0)
        {
          this.PopupGestisciImportiFornitori = true
          this.VisualizzaSoloSelezionati.forEach(function(Elemento)
          {
            if(Elemento.IMPORTO_PAGATO == null)
              Elemento.IMPORTO_PAGATO = Elemento.IMPORTO / 1000

            Elemento.IMPORTO_PAGATO_TEMPORANEO = Elemento.IMPORTO_PAGATO
          })
        }
      },

      CanConfirmGestisciImporti()
      {
        if(this.VisualizzaSoloSelezionati.length != 0)
        {
          for(let i = 0; i < this.VisualizzaSoloSelezionati.length; i++)
            if(this.VisualizzaSoloSelezionati[i].IMPORTO_PAGATO != null)
              if(this.VisualizzaSoloSelezionati[i].IMPORTO_PAGATO <= 0)
                return false
          return true
        }
        return false
      },

      OnClickGestisciRateMovimento()
      {
        var Self = this
        if(this.RateSelezionate.length != 0)
        {
          if(this.VisualizzaSoloSelezionati.length == 0)
          {
            this.CurrentSchedaMovimento.Dati.LsRateCorrelate = this.CurrentSchedaMovimento.Dati.LsRateCorrelate.concat(this.RateSelezionate)
            this.CurrentSchedaMovimento.Dati.LsRateCorrelate.forEach(function(Rata)
            {
              Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                               'Fattura n° ' + 
                                                               Rata.NUMERO_FATTURA + 
                                                               ' del ' + 
                                                               TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Rata.DATA_FATTURA)) +
                                                               ' del cliente ' +
                                                               Rata.RAGIONE_SOCIALE +
                                                               ' Cod. ' + Rata.CODICE
            });
          }
          else
          {
            if(this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length != 0)
            {
              for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length; i++)
              {
                for(let j = 0; j < this.ListaRate.length; j++)
                {
                  if(this.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].CHIAVE == this.ListaRate[j].CHIAVE)
                    this.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].Selezionata = this.ListaRate[j].Selezionata

                  if(this.ListaRate[j].Selezionata)
                  {
                    let TrovatoSelezionato = false
                    for(let k = 0; k < this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length; k++)
                      if(this.CurrentSchedaMovimento.Dati.LsRateCorrelate[k].CHIAVE == this.ListaRate[j].CHIAVE)
                        TrovatoSelezionato = true
                    
                    if(!TrovatoSelezionato)
                    {
                      this.CurrentSchedaMovimento.Dati.LsRateCorrelate.push(this.ListaRate[j])
                      this.CurrentSchedaMovimento.Dati.DESCRIZIONE += (this.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                       'Fattura n° ' + 
                                                                       this.ListaRate[j].NUMERO_FATTURA + 
                                                                       ' del ' + 
                                                                       TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(this.ListaRate[j].DATA_FATTURA)) +
                                                                       ' del cliente ' +
                                                                       this.ListaRate[j].RAGIONE_SOCIALE 
                                                                       + ' Cod. ' + this.ListaRate[j].CODICE
                    }
                  }
                }
              }
            }
            else
            {
              this.CurrentSchedaMovimento.Dati.LsRateCorrelate = this.CurrentSchedaMovimento.Dati.LsRateCorrelate.concat(this.RateSelezionate)
              this.CurrentSchedaMovimento.Dati.LsRateCorrelate.forEach(function(Rata)
              {
                  Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                  'Fattura n° ' + 
                                                                  Rata.NUMERO_FATTURA + 
                                                                  ' del ' + 
                                                                  TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Rata.DATA_FATTURA)) +
                                                                  ' del cliente ' +
                                                                  Rata.RAGIONE_SOCIALE +
                                                                  ' Cod. ' + Rata.CODICE
              });
            }
          }
        }
        else
        {
          if(this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length != 0)
          {
            for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length; i++)
              this.CurrentSchedaMovimento.Dati.LsRateCorrelate[i].Selezionata = false
            
            if(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length == 0)
            {
              this.CurrentSchedaMovimento.Dati.IMPORTO     = 0
              this.CurrentSchedaMovimento.Dati.DESCRIZIONE = ''
            }
          }
        }

        if(this.VisualizzaSoloSelezionati.length != 0)
        {
          this.CurrentSchedaMovimento.BloccoPrelievoDa             = true
          this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno       = true
          this.CurrentSchedaMovimento.Dati.CONTO_CORRENTE_PRELIEVO = -1
          this.CurrentSchedaMovimento.Dati.CkPrelievoDaConto       = false
        }
        else this.CurrentSchedaMovimento.BloccoPrelievoDa = false
        
        this.CurrentSchedaMovimento.GetTotaleDocumentiCorrelati()
        this.PopupMovimentoCorrelato  = false
        this.FiltroRagioneSociale     = ''
      },

      OnClickGestisciFatturePregresseFornitori()
      {
        var Self = this
        if(this.FatturePregresseFornitoriSelezionate.length != 0)
        {
          if(this.VisualizzaSoloSelezionati.length == 0)
          {
            this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate = this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.concat(this.FatturePregresseFornitoriSelezionate)
            this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.forEach(function(FatturaPregressa)
            {
              Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                              'Fattura pregressa n° ' +
                                                                FatturaPregressa.NUMERO_FATTURA + 
                                                              ' del ' + 
                                                                TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(FatturaPregressa.DATA)) +
                                                              ' del fornitore ' +
                                                                FatturaPregressa.RAGIONE_SOCIALE +
                                                              ' Cod. ' +
                                                                FatturaPregressa.CODICE
            });
          }
          else
          {
            if(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length != 0)
            {
              for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length; i++)
              {
                for(let j = 0; j < this.ListaFatturePregresseFornitori.length; j++)
                {
                  if(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].CHIAVE == this.ListaFatturePregresseFornitori[j].CHIAVE)
                    this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata = this.ListaFatturePregresseFornitori[j].Selezionata

                  if(this.ListaFatturePregresseFornitori[j].Selezionata)
                  {
                    let TrovatoSelezionato = false
                    for(let k = 0; k < this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length; k++)
                      if(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[k].CHIAVE == this.ListaFatturePregresseFornitori[j].CHIAVE)
                        TrovatoSelezionato = true
                    
                    if(!TrovatoSelezionato)
                    {
                      this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.push(this.ListaFatturePregresseFornitori[j])
                      this.CurrentSchedaMovimento.Dati.DESCRIZIONE += (this.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                      'Fattura pregressa n° ' +
                                                                        this.ListaFatturePregresseFornitori[j].NUMERO_FATTURA + 
                                                                      ' del ' + 
                                                                        TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(this.ListaFatturePregresseFornitori[j].DATA)) +
                                                                      ' del cliente ' +
                                                                        this.ListaFatturePregresseFornitori[j].RAGIONE_SOCIALE +
                                                                      ' Cod. ' +
                                                                        this.ListaFatturePregresseFornitori[j].CODICE
                
                      }
                  }
                }
              }
            }
            else
            {
              this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate = this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.concat(this.FatturePregresseFornitoriSelezionate)
              this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.forEach(function(FatturaPregressa)
              {
                  Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                  'Fattura pregressa n° ' +
                                                                    FatturaPregressa.NUMERO_FATTURA + 
                                                                  ' del ' + 
                                                                    TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(FatturaPregressa.DATA)) +
                                                                  ' del fornitore ' +
                                                                    FatturaPregressa.RAGIONE_SOCIALE +
                                                                  ' Cod. ' +
                                                                    FatturaPregressa.CODICE
                  });
            }
          }
        }
        else
        {
          if(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length != 0)
          {
            for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length; i++)
              this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate[i].Selezionata = false
            
            if(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length == 0)
            {
              this.CurrentSchedaMovimento.Dati.IMPORTO     = 0
              this.CurrentSchedaMovimento.Dati.DESCRIZIONE = ''
            }
          }
        }

        if(this.VisualizzaSoloSelezionati.length != 0)
        {
          this.CurrentSchedaMovimento.BloccoVersamentoIn             = true
          this.CurrentSchedaMovimento.Dati.CkVersamentoEsterno       = true
          this.CurrentSchedaMovimento.Dati.CONTO_CORRENTE_VERSAMENTO = -1
          this.CurrentSchedaMovimento.Dati.CkPrelievoDaConto         = true
          this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno         = false
        }
        else this.CurrentSchedaMovimento.BloccoVersamentoIn = false
        
        this.CurrentSchedaMovimento.GetTotaleDocumentiCorrelati()
        this.PopupMovimentoCorrelatoAFatturePregresseFornitori = false
        this.FiltroRagioneSocialeFatturePregresseFornitori     = ''
      },

      OnClickGestisciRateFatturaFornitoriMovimento()
      {
        var Self = this
        if(this.RateFattureFornitoriSelezionate.length != 0)
        {
          if(this.VisualizzaSoloSelezionati.length == 0)
          {
            this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate = this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.concat(this.RateFattureFornitoriSelezionate)
            this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.forEach(function(Rata)
            {
              Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                               'Fattura n° ' + 
                                                               Rata.NUMERO_FATTURA + 
                                                               ' del ' + 
                                                               TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Rata.DATA_FATTURA)) +
                                                               ' del fornitore ' +
                                                               Rata.RAGIONE_SOCIALE +
                                                               ' Cod. ' + Rata.CODICE
            });
          }
          else
          {
            if(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length != 0)
            {
              for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length; i++)
              {
                for(let j = 0; j < this.ListaRateFattureFornitori.length; j++)
                {
                  if(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].CHIAVE == this.ListaRateFattureFornitori[j].CHIAVE)
                    this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata = this.ListaRateFattureFornitori[j].Selezionata

                  if(this.ListaRateFattureFornitori[j].Selezionata)
                  {
                    let TrovatoSelezionato = false
                    for(let k = 0; k < this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length; k++)
                      if(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[k].CHIAVE == this.ListaRateFattureFornitori[j].CHIAVE)
                        TrovatoSelezionato = true
                    
                    if(!TrovatoSelezionato)
                    {
                      this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.push(this.ListaRateFattureFornitori[j])
                      this.CurrentSchedaMovimento.Dati.DESCRIZIONE += (this.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                       'Fattura n° ' + 
                                                                       this.ListaRateFattureFornitori[j].NUMERO_FATTURA + 
                                                                       ' del ' + 
                                                                       TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(this.ListaRateFattureFornitori[j].DATA_FATTURA)) +
                                                                       ' del fornitore ' +
                                                                       this.ListaRateFattureFornitori[j].RAGIONE_SOCIALE +
                                                                       ' Cod. ' +
                                                                       this.ListaRateFattureFornitori[j].CODICE
                    }
                  }
                }
              }
            }
            else
            {
              this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate = this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.concat(this.RateFattureFornitoriSelezionate)
              this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.forEach(function(Rata)
              {
                  Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                  'Fattura n° ' + 
                                                                  Rata.NUMERO_FATTURA + 
                                                                  ' del ' + 
                                                                  TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(Rata.DATA_FATTURA)) +
                                                                  ' del fornitore ' +
                                                                  Rata.RAGIONE_SOCIALE +
                                                                  ' Cod. ' + Rata.CODICE
              });
            }
          }
        }
        else
        {
          if(this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length != 0)
          {
            for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate.length; i++)
              this.CurrentSchedaMovimento.Dati.LsRateFattureFornitoriCorrelate[i].Selezionata = false
            
            if(this.CurrentSchedaMovimento.Dati.LsFatturePregresseFornitoriCorrelate.length == 0)
            {
              this.CurrentSchedaMovimento.Dati.IMPORTO     = 0
              this.CurrentSchedaMovimento.Dati.DESCRIZIONE = ''
            }
          }
        }

        if(this.VisualizzaSoloSelezionati.length != 0)
        {
          this.CurrentSchedaMovimento.BloccoVersamentoIn             = true
          this.CurrentSchedaMovimento.Dati.CkVersamentoEsterno       = true
          this.CurrentSchedaMovimento.Dati.CONTO_CORRENTE_VERSAMENTO = -1
          this.CurrentSchedaMovimento.Dati.CkPrelievoDaConto         = true
          this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno         = false
        }
        else this.CurrentSchedaMovimento.BloccoVersamentoIn = false
        
        this.CurrentSchedaMovimento.GetTotaleDocumentiCorrelati()
        this.PopupMovimentoCorrelatoARateFattureFornitori = false
        this.FiltroRagioneSocialeRateFattureFornitori     = ''
      },

      OnClickGestisciFatturePregresse()
      {
        var Self = this
        if(this.FattureSelezionate.length != 0)
        {
          if(this.VisualizzaSoloSelezionati.length == 0)
          {
            this.CurrentSchedaMovimento.Dati.LsFattureCorrelate = this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.concat(this.FattureSelezionate)
            this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.forEach(function(FatturaPregressa)
            {
              Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                              'Fattura pregressa n° ' +
                                                                FatturaPregressa.NUMERO_FATTURA + 
                                                              ' del ' + 
                                                                TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(FatturaPregressa.DATA)) +
                                                              ' del cliente ' +
                                                                FatturaPregressa.RAGIONE_SOCIALE +
                                                              ' Cod. ' + FatturaPregressa.CODICE
            });
          }
          else
          {
            if(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length != 0)
            {
              for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length; i++)
              {
                for(let j = 0; j < this.ListaFatturePregresse.length; j++)
                {
                  if(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].CHIAVE == this.ListaFatturePregresse[j].CHIAVE)
                    this.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].Selezionata = this.ListaFatturePregresse[j].Selezionata

                  if(this.ListaFatturePregresse[j].Selezionata)
                  {
                    let TrovatoSelezionato = false
                    for(let k = 0; k < this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length; k++)
                      if(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate[k].CHIAVE == this.ListaFatturePregresse[j].CHIAVE)
                        TrovatoSelezionato = true
                    
                    if(!TrovatoSelezionato)
                    {
                      this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.push(this.ListaFatturePregresse[j])
                      this.CurrentSchedaMovimento.Dati.DESCRIZIONE += (this.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                      'Fattura pregressa n° ' +
                                                                        this.ListaFatturePregresse[j].NUMERO_FATTURA + 
                                                                      ' del ' + 
                                                                        TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(this.ListaFatturePregresse[j].DATA)) +
                                                                      ' del cliente ' +
                                                                        this.ListaFatturePregresse[j].RAGIONE_SOCIALE 
                                                                        + ' Cod. ' + this.ListaFatturePregresse[j].CODICE
                
                      }
                  }
                }
              }
            }
            else
            {
              this.CurrentSchedaMovimento.Dati.LsFattureCorrelate = this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.concat(this.FattureSelezionate)
              this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.forEach(function(FatturaPregressa)
              {
                  Self.CurrentSchedaMovimento.Dati.DESCRIZIONE += (Self.CurrentSchedaMovimento.Dati.DESCRIZIONE != ''? '\n' : '') +
                                                                  'Fattura pregressa n° ' +
                                                                    FatturaPregressa.NUMERO_FATTURA + 
                                                                  ' del ' + 
                                                                    TZDateFunct.FormatDateTime('dd/mm/yyyy', new Date(FatturaPregressa.DATA)) +
                                                                  ' del cliente ' +
                                                                    FatturaPregressa.RAGIONE_SOCIALE +
                                                                  ' Cod. ' + FatturaPregressa.CODICE
              });
            }
          }
        }
        else
        {
          if(this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length != 0)
          {
            for(let i = 0; i < this.CurrentSchedaMovimento.Dati.LsFattureCorrelate.length; i++)
              this.CurrentSchedaMovimento.Dati.LsFattureCorrelate[i].Selezionata = false
            
            if(this.CurrentSchedaMovimento.Dati.LsRateCorrelate.length == 0)
            {
              this.CurrentSchedaMovimento.Dati.IMPORTO     = 0
              this.CurrentSchedaMovimento.Dati.DESCRIZIONE = ''
            }
          }
        }

        if(this.VisualizzaSoloSelezionati.length != 0)
        {
          this.CurrentSchedaMovimento.BloccoPrelievoDa             = true
          this.CurrentSchedaMovimento.Dati.CkPrelievoEsterno       = true
          this.CurrentSchedaMovimento.Dati.CONTO_CORRENTE_PRELIEVO = -1
          this.CurrentSchedaMovimento.Dati.CkPrelievoDaConto       = false
        }
        else this.CurrentSchedaMovimento.BloccoPrelievoDa = false
        
        this.CurrentSchedaMovimento.GetTotaleDocumentiCorrelati()
        this.PopupMovimentoCorrelatoAFatturePregresse = false
        this.FiltroRagioneSocialeFatturePregresse     = ''
      },

      OnClickChiudiPopupAssociazione()
      {
        this.PopupAssociazione = false
      },

     OnClickConfermaPopupAssociazione()
     {  
        this.PopupAssociazione = false

        if(this.TipoSelezione == 'NessunaAssociazione')
        {
          this.CurrentSchedaMovimento.Dati.ID_ANAGRAFICA = -1
        }
        else
        {
          if(this.TipoSelezione == 'Cliente')
          {
            this.CurrentSchedaMovimento.Dati.ID_ANAGRAFICA = this.IDClienteTemporaneo
            
            
          }
          else
          {
            this.CurrentSchedaMovimento.Dati.ID_ANAGRAFICA = this.IDFornitoreTemporaneo
            
          }
        }
     }
    },

    beforeMount() 
    {
      this.ActiveTab = 'Generale'
    },
 }
</script>

<style scoped>
label {
  margin-bottom: 0px;
}
</style>
