unit QrStampaResocontoPreventivi;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls, FramePreventivi,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery,
     ZFileFunct,ZFormConfAnnulla,ZStringConvFunct;

type
  TQR_STAMPA_RESOCONTO_PREVENTIVI = class(TQuickRep)
    BAND_INTESTAZIONE: TQRBand;
    BAND_PAGE_FOOTER: TQRBand;
    QryGeneric: TpFIBQuery;
    LB_TITOLO: TQRLabel;
    QRLabel38: TQRLabel;
    QRSysData1: TQRSysData;
    LB_INTESTATARIO: TQRLabel;
    LB_DATA: TQRLabel;
    LB_NUMERO: TQRLabel;
    BAND_GROUP_FOOTER: TQRBand;
    QRChildBand1: TQRChildBand;
    LB_TOTALE: TQRLabel;
    LB_TOT_PREVENTIVI: TQRLabel;
    LB_TELEFONO: TQRMemo;
    DENOMINAZIONE_SOCIETA: TQRLabel;
    INTESTAZIONE_SOCIETA: TQRMemo;
    QR_LOGO: TQRImage;
    QRShape1: TQRShape;
    QRShape8: TQRShape;
    QRShape7: TQRShape;
    QRShape6: TQRShape;
    QRLabel13: TQRLabel;
    QRLabel19: TQRLabel;
    QRLabel1: TQRLabel;
    QRLabel15: TQRLabel;
    QRLabel5: TQRLabel;
    QRLabel2: TQRLabel;
    QRShape2: TQRShape;
    QRShape3: TQRShape;
    QRShape4: TQRShape;
    QRShape5: TQRShape;
    procedure QuickRepPreview(Sender: TObject);
    procedure DetailResocontoPreventiviNeedData(Sender: TObject;
      var MoreData: Boolean);
    procedure QuickRepBeforePrint(Sender: TCustomQuickRep;
      var PrintReport: Boolean);
    procedure DetailResocontoPreventiviBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure LB_TOT_PREVENTIVIPrint(sender: TObject; var Value: string);
    procedure QRLabel4Print(sender: TObject; var Value: string);
  private
    FListaPreventivi  : TStringList;
    FIndexPreventivo  : Integer;
    FTotale           : Double;
    FTotFatturato     : Double;
    FORM_PREVIEW      : TFORM_PREVIEW;
  public
   Procedure Init(Connection : TpFIBQuery; ListaPreventivi : TStringList;
                  Suggerimento : TStringList; AddTitolo : String);
   Procedure EsportaSuFile(FileName : String);
  end;

implementation

{$R *.DFM}
Procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.EsportaSuFile(FileName : String);
var BodyFile  : TStringList;
    PrintBand : Boolean;
    i         : Integer;
begin
 BodyFile := TStringList.Create;
 try
  BodyFile.Add(LB_TITOLO.Caption);
  BodyFile.Add(SUGGERIMENTO_RESOCONTO.Caption);
  BodyFile.Add('Numero' + #9 + 'Data' + #9 + 'Intestatario' + #9 + 'Totale' + #9 + 'Fatturato' +#9 + 'Cond. pagamento' + #9 + 'Telefono');
  for i := 0 to FListaPreventivi.Count - 1 do
      begin
        FIndexPreventivo := i;
        DetailResocontoPreventiviBeforePrint(Nil,PrintBand);
        if PrintBand then
           BodyFile.Add(LB_NUMERO.Caption + #9 + ' ' + LB_DATA.Caption + #9 +
                        LB_INTESTATARIO.Caption + #9 + LB_TOTALE.Caption + #9 +
                        LB_FATTURATO.Caption + #9 +
                        LB_COND_PAGAMENTO.Caption + #9 + LB_TELEFONO.Caption);
      end;
  BodyFile.SaveToFile(FileName);
  if Conferma(CAPTION_MESSAGE,'Vuoi vedere il file generato?') then
     ExecuteFile(FileName,'','',WS_MAXIMIZE);
 finally
  BodyFile.Free;
 end;
end;


procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.DetailResocontoPreventiviBeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
var Preventivo   : TNodoPreventivo;
    FramePrevent : TFRAME_PREVENTIVI;
begin
  PrintBand := True;
  Preventivo := TNodoPreventivo(FListaPreventivi.Objects[FIndexPreventivo]);
  Preventivo.JumpAllegati := True;
  try
    Preventivo.Disponi;
  finally
   Preventivo.JumpAllegati := False;
  end;
  FramePrevent := TFRAME_PREVENTIVI(Preventivo.Frame);
  LB_NUMERO.Caption          := FramePrevent.NUMERO.Text +
                                FramePrevent.ANNO.Text;
  LB_DATA.Caption            := FramePrevent.DATA.Text;
  LB_INTESTATARIO.Caption    := FramePrevent.TITOLO_INTESTATARIO.Text + ' ' +
                                FramePrevent.GENERALITA_INTESTATARIO.Text;
  FTotale                    := FTotale + FramePrevent.TotalePreventivo;
  LB_TOTALE.Caption          := FramePrevent.LB_TOTALE.Caption;
  if FramePrevent.FatturaCorrelata then
     begin
      FTotFatturato          := FTotFatturato + FramePrevent.TotalePreventivo;
      LB_FATTURATO.Caption   := FramePrevent.LB_TOTALE.Caption;
     end
  else LB_FATTURATO.Caption  := '0';

  if FramePrevent.COND_PAGAMENTO.ItemIndex = -1 then LB_COND_PAGAMENTO.Caption := '-'
  else LB_COND_PAGAMENTO.Caption   := FramePrevent.COND_PAGAMENTO.Items[FramePrevent.COND_PAGAMENTO.ItemIndex];

  if Preventivo.IdContratto <> -1 then
     LB_TELEFONO.Caption := GetDescrizioneRecord(QryGeneric,TABLEContratti,'TELEFONO1','CHIAVE',IntToStr(Preventivo.IdContratto),'<???>');
  if Preventivo.IdAmministratore <> -1 then
     LB_TELEFONO.Caption := GetDescrizioneRecord(QryGeneric,TABLEAmministratori,'TELEFONO1','CHIAVE',IntToStr(Preventivo.IdAmministratore),'<???>');


end;

procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.DetailResocontoPreventiviNeedData(
  Sender: TObject; var MoreData: Boolean);
begin
  Inc(FIndexPreventivo);
  MoreData := FIndexPreventivo < FListaPreventivi.Count;
end;

Procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.Init(Connection : TpFIBQuery; ListaPreventivi : TStringList;
                                               Suggerimento : TStringList; AddTitolo : String);
begin
  FListaPreventivi := ListaPreventivi;
  SUGGERIMENTO_RESOCONTO.Lines.Assign(Suggerimento);
  FIBPlusCopyConnectionParams(Connection,QryGeneric);
  if IsValidName(AddTitolo) then
     LB_TITOLO.Caption := LB_TITOLO.Caption + ' ' + AddTitolo;
end;

procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.QRLabel4Print(sender: TObject;
  var Value: string);
begin
  Value := FormatFloat('0.00',FTotFatturato);
end;

procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.LB_TOT_PREVENTIVIPrint(sender: TObject; var Value: string);
begin
  Value := FormatFloat('0.00',FTotale);
end;

procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.QuickRepBeforePrint(
  Sender: TCustomQuickRep; var PrintReport: Boolean);
begin
  FIndexPreventivo := -1;
  FTotale          := 0;
  FTotFatturato    := 0;
end;

procedure TQR_STAMPA_RESOCONTO_PREVENTIVI.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

end.
