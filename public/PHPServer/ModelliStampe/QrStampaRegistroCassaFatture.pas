unit QrStampaRegistroCassaFatture;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls,FrameFatture,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery,
     ZFileFunct,ZFormConfAnnulla,ZStringConvFunct,ComCtrls, qrFramelines;

type
  TQR_STAMPA_RESOCONTO_FATTURE = class(TQuickRep)
    BAND_INTESTAZIONE: TQRBand;
    BAND_PAGE_FOOTER: TQRBand;
    QryGeneric: TpFIBQuery;
    LB_TITOLO: TQRLabel;
    QRLabel38: TQRLabel;
    QRSysData1: TQRSysData;
    LB_DATA: TQRLabel;
    LB_NUMERO: TQRLabel;
    QRChildBand1: TQRChildBand;
    QRLabel13: TQRLabel;
    QRLabel19: TQRLabel;
    QRLabel1: TQRLabel;
    QRLabel3: TQRLabel;
    QRLabel5: TQRLabel;
    LB_TOT_FATTURA: TQRLabel;
    LB_IMPONIBILE: TQRLabel;
    QRLabel8: TQRLabel;
    LB_AMMINISTRATORE: TQRLabel;
    QRLabel10: TQRLabel;
    QRShape2: TQRShape;
    QRShape6: TQRShape;
    QRShape7: TQRShape;
    QRShape8: TQRShape;
    QRShape9: TQRShape;
    QRShape10: TQRShape;
    QRShape12: TQRShape;
    QRShape4: TQRShape;
    QRShape13: TQRShape;
    QRShape14: TQRShape;
    QRShape16: TQRShape;
    DENOMINAZIONE_SOCIETA: TQRLabel;
    INTESTAZIONE_SOCIETA: TQRMemo;
    QR_LOGO: TQRImage;
    QRShape11: TQRShape;
    QRShape19: TQRShape;
    QRLabel2: TQRLabel;
    LB_CODICE_CLIENTE: TQRLabel;
    QRShape3: TQRShape;
    QRShape5: TQRShape;
    QRLabel4: TQRLabel;
    LB_IVA: TQRLabel;
    QRShape15: TQRShape;
    MEMO_INTESTATARIO: TQRMemo;
    MEMO_NOTE: TQRMemo;
    procedure QuickRepPreview(Sender: TObject);
    procedure BAND_PRIMA_RATANeedData(Sender: TObject;
      var MoreData: Boolean);
    procedure QuickRepBeforePrint(Sender: TCustomQuickRep;
      var PrintReport: Boolean);
    procedure BAND_PRIMA_RATABeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure LB_TOTALE_INCASSO_RESOCONTOPrint(sender: TObject; var Value: string);
    procedure LB_NS_AVEREPrint(sender: TObject; var Value: string);
    procedure LB_TOTALE_RESOCONTOPrint(sender: TObject; var Value: string);
    procedure LB_TERZA_RATAPrint(sender: TObject; var Value: string);
    procedure BAND_SECONDA_RATABeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_TERZA_RATABeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure BAND_TOTALI_FATTURABeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
  private
    FListaFatture  : TStringList;
    FIndexFattura  : Integer;
    FTotale        : Double;
    FTotPagato     : Double;
    FProgressBar   : TProgressBar;
    FORM_PREVIEW   : TFORM_PREVIEW;
  public
   Procedure Init(Connection : TpFIBQuery; ListaFatture : TStringList;
                  Suggerimento : TStringList; AddTitolo : String;
                  ProgressBar : TProgressBar);
   Procedure EsportaSuFile(FileName : String);
  end;

implementation

{$R *.DFM}
Procedure TQR_STAMPA_RESOCONTO_FATTURE.EsportaSuFile(FileName : String);
var BodyFile  : TStringList;
    PrintBand : Boolean;
    i         : Integer;
begin
 BodyFile := TStringList.Create;
 try
  BodyFile.Add(LB_TITOLO.Caption);
  BodyFile.Add(SUGGERIMENTO_RESOCONTO.Lines.Text);
  BodyFile.Add('Numero' + #9 + 'Data' + #9 + 'Intestatario' + #9 + 'Totale' + #9 + 'Incassato' + #9 + 'Scadenza' + #9 + 'Cond. pagamento' + #9 + 'Telefono');
  for i := 0 to FListaFatture.Count - 1 do
      begin
        FIndexFattura := i;
        BAND_PRIMA_RATABeforePrint(Nil,PrintBand);
        if PrintBand then
           BodyFile.Add(LB_NUMERO.Caption + #9 + ' ' + LB_DATA.Caption + #9 +
                        LB_INTESTATARIO.Caption + #9 + LB_TOTALE.Caption + #9 +
                        LB_PAGATO.Caption + #9 + ' ' + LB_TOT_PRIMA_RATA.Caption + #9 +
                        LB_COND_PAGAMENTO.Caption + #9 + LB_TELEFONO.Caption);
      end;
  BodyFile.SaveToFile(FileName);
  if Conferma(CAPTION_MESSAGE,'Vuoi vedere il file generato?') then
     ExecuteFile(FileName,'','',WS_MAXIMIZE);
 finally
  BodyFile.Free;
 end;
end;


procedure TQR_STAMPA_RESOCONTO_FATTURE.BAND_PRIMA_RATABeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
type TLabelRata = record
                   LbTotale   : TQRLabel;
                   LbScadenza : TQRLabel;
                   LbIncasso  : TQRLabel;
                   Presente   : Boolean;
                  end;
var Fattura      : TNodoFattura;
    FrameFattura : TFRAME_FATTURE;
    i            : Integer;
    LbRate       : Array[1..3] of TLabelRata;
    Pagato       : Double;
    NdxRata      : Integer;
begin
  PrintBand := True;
  LbRate[1].LbScadenza := LB_SCADENZA_PRIMA_RATA;
  LbRate[2].LbScadenza := LB_SCADENZA_SECONDA_RATA;
  LbRate[3].LbScadenza := LB_SCADENZA_TERZA_RATA;
  LbRate[1].LbTotale   := LB_TOT_PRIMA_RATA;
  LbRate[2].LbTotale   := LB_TOT_SECONDA_RATA;
  LbRate[3].LbTotale   := LB_TOT_TERZA_RATA;
  LbRate[1].LbIncasso  := LB_INCASSO_PRIMA_RATA;
  LbRate[2].LbIncasso  := LB_INCASSO_SECONDA_RATA;
  LbRate[3].LbIncasso  := LB_INCASSO_TERZA_RATA;
  for i := Low(LbRate) to High(LbRate) do
      begin
       LbRate[i].Presente := False;
       LbRate[i].LbTotale.Caption   := '';
       LbRate[i].LbScadenza.Caption := '';
       LbRate[i].LbIncasso.Caption  := '';
      end;

  Fattura := TNodoFattura(FListaFatture.Objects[FIndexFattura]);
  Fattura.LoadImage := False;
  try
    Fattura.Disponi;
  finally
   Fattura.LoadImage := True;
  end;
  FrameFattura := TFRAME_FATTURE(Fattura.Frame);
  if FrameFattura.ANNO.Value >= 2019 then
     LB_NUMERO.Caption := FrameFattura.PROGRESSIVO_INVIO.Caption
  else LB_NUMERO.Caption := FrameFattura.NUMERO.Text +
                            SostBoolean(Fattura.FatturaElettronica,'E/','/') +
                            FrameFattura.ANNO.Text;
  LB_DATA.Caption            := FrameFattura.DATA.Text;
  LB_INTESTATARIO.Caption    := FrameFattura.TITOLO_INTESTATARIO.Text + ' ' +
                                FrameFattura.GENERALITA_INTESTATARIO.Text;
  FTotale                    := FTotale + FrameFattura.TotaleFattura;
  Pagato                     := FrameFattura.TotRatePagate;
  FTotPagato                 := FTotPagato + Pagato;
  LB_TOTALE.Caption          := FrameFattura.LB_TOTALE.Caption;
  LB_PAGATO.Caption          := FormatFloat('0.00',Pagato);
  if RealToCents(FrameFattura.TotaleFattura) <> RealToCents(Pagato) then
     LB_TOTALE.Font.Color := clRed
  else LB_TOTALE.Font.Color := clBlack;

  NdxRata := Low(LbRate);
  for i := FrameFattura.GRID_RATE.FixedRows to FrameFattura.GRID_RATE.LastRow do
        if (FrameFattura.GRID_RATE.Cells[COL_FATT_ATTIVA_RATE_DATA,i].Date <> 0.0) and
           (FrameFattura.GRID_RATE.Cells[COL_FATT_ATTIVA_RATE_IS_NOTA,i].Tag <> ROW_IS_NOTA) then
           begin
             LbRate[NdxRata].LbScadenza.Caption   := FrameFattura.GRID_RATE.Cells[COL_FATT_ATTIVA_RATE_DATA,i].Text;
             LbRate[NdxRata].LbTotale.Caption     := FrameFattura.GRID_RATE.Cells[COL_FATT_ATTIVA_RATE_IMPORTO,i].Text;
             LbRate[NdxRata].LbIncasso.Caption    := SostBoolean(FrameFattura.GRID_RATE.Cells[COL_FATT_ATTIVA_RATE_DATA_PAGAMENTO,i].Date <> 0.0,
                                                                 FrameFattura.GRID_RATE.Cells[COL_FATT_ATTIVA_RATE_IMPORTO,i].Text,'0.00');
             LbRate[NdxRata].LbTotale.Font.Color  := SostBooleanWithInt(FrameFattura.GRID_RATE.Cells[COL_FATT_ATTIVA_RATE_DATA_PAGAMENTO,i].Date <> 0.0,
                                                                        clBlack,clRed);
             Inc(NdxRata);
             if NdxRata > High(LbRate) then Break;

           end;
  if FrameFattura.COND_PAGAMENTO.ItemIndex = -1 then LB_COND_PAGAMENTO.Caption := '-'
  else LB_COND_PAGAMENTO.Caption   := FrameFattura.COND_PAGAMENTO.Items[FrameFattura.COND_PAGAMENTO.ItemIndex];

  if Fattura.IdContratto <> -1 then
     LB_TELEFONO.Caption := GetDescrizioneRecord(QryGeneric,TABLEContratti,'TELEFONO1','CHIAVE',IntToStr(Fattura.IdContratto),'<???>');
  if Fattura.IdAmministratore <> -1 then
     LB_TELEFONO.Caption := GetDescrizioneRecord(QryGeneric,TABLEAmministratori,'TELEFONO1','CHIAVE',IntToStr(Fattura.IdAmministratore),'<???>');


end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.BAND_PRIMA_RATANeedData(
  Sender: TObject; var MoreData: Boolean);
begin
  Inc(FIndexFattura);
  MoreData := FIndexFattura < FListaFatture.Count;
  if FListaFatture.Count <> 0 then
     begin
      FProgressBar.Position := Trunc(FIndexFattura / FListaFatture.Count * 100);
      Application.ProcessMessages;
     end;
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.BAND_SECONDA_RATABeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
begin
  PrintBand := LB_SCADENZA_SECONDA_RATA.Caption <> '';
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.BAND_TERZA_RATABeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
begin
  PrintBand := LB_SCADENZA_TERZA_RATA.Caption <> '';
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.BAND_TOTALI_FATTURABeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
begin
  PrintBand := LB_SCADENZA_SECONDA_RATA.Caption <> '';
end;

Procedure TQR_STAMPA_RESOCONTO_FATTURE.Init(Connection : TpFIBQuery; ListaFatture : TStringList;
                                            Suggerimento : TStringList; AddTitolo : String;
                                            ProgressBar : TProgressBar);
begin
  FListaFatture := ListaFatture;
  FProgressBar := ProgressBar;
  SUGGERIMENTO_RESOCONTO.Lines.Assign(Suggerimento);
  FIBPlusCopyConnectionParams(Connection,QryGeneric);
  if IsValidName(AddTitolo) then
     LB_TITOLO.Caption := LB_TITOLO.Caption + ' ' + AddTitolo;
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.LB_NS_AVEREPrint(sender: TObject;
  var Value: string);
begin
  Value := FormatFloat('0.00',FTotale - FTotPagato);
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.LB_TOTALE_INCASSO_RESOCONTOPrint(sender: TObject;
  var Value: string);
begin
  Value := FormatFloat('0.00',FTotPagato)
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.LB_TOTALE_RESOCONTOPrint(sender: TObject; var Value: string);
begin
  Value := FormatFloat('0.00',FTotale);
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.LB_TERZA_RATAPrint(sender: TObject; var Value: string);
begin
  Value := ' ';
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.QuickRepBeforePrint(
  Sender: TCustomQuickRep; var PrintReport: Boolean);
begin
  FIndexFattura := -1;
  FTotale       := 0;
  FTotPagato    := 0;
end;

procedure TQR_STAMPA_RESOCONTO_FATTURE.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

end.
