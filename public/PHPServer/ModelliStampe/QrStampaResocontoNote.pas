unit QrStampaResocontoNote;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls,FrameNoteDiCredito,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery,
     ZStringConvFunct;

type
  TQR_STAMPA_RESOCONTO_NOTE = class(TQuickRep)
    BAND_INTESTAZIONE: TQRBand;
    BAND_PAGE_FOOTER: TQRBand;
    QryGeneric: TpFIBQuery;
    LB_TITOLO: TQRLabel;
    QRLabel38: TQRLabel;
    QRSysData1: TQRSysData;
    LB_INTESTATARIO: TQRLabel;
    LB_TOTALE: TQRLabel;
    LB_DATA: TQRLabel;
    LB_NUMERO: TQRLabel;
    BAND_GROUP_FOOTER: TQRBand;
    QRLabel4: TQRLabel;
    QRChildBand1: TQRChildBand;
    LB_TELEFONO: TQRMemo;
    DENOMINAZIONE_SOCIETA: TQRLabel;
    INTESTAZIONE_SOCIETA: TQRMemo;
    QR_LOGO: TQRImage;
    QRLabel5: TQRLabel;
    QRLabel15: TQRLabel;
    QRLabel1: TQRLabel;
    QRLabel19: TQRLabel;
    QRLabel13: TQRLabel;
    QRShape6: TQRShape;
    QRShape7: TQRShape;
    QRShape12: TQRShape;
    QRShape1: TQRShape;
    QRShape2: TQRShape;
    QRShape3: TQRShape;
    QRShape4: TQRShape;
    QRShape5: TQRShape;
    QRLabel2: TQRLabel;
    procedure QuickRepPreview(Sender: TObject);
    procedure DetailResocontoNoteNeedData(Sender: TObject;
      var MoreData: Boolean);
    procedure QuickRepBeforePrint(Sender: TCustomQuickRep;
      var PrintReport: Boolean);
    procedure DetailResocontoNoteBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure QRLabel4Print(sender: TObject; var Value: string);
  private
    FListaNote     : TStringList;
    FIndexNota     : Integer;
    FTotale        : Double;
    FORM_PREVIEW   : TFORM_PREVIEW;
  public
   Procedure Init(Connection : TpFIBQuery; ListaNote : TStringList;
                  Suggerimento : TStringList; AddTitolo : String);
  end;

implementation

{$R *.DFM}
procedure TQR_STAMPA_RESOCONTO_NOTE.DetailResocontoNoteBeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
var NotaDiCredito : TNodoNotaDiCredito;
    FrameNota     : TFRAME_NOTE_DI_CREDITO;
begin
  NotaDiCredito              := TNodoNotaDiCredito(FListaNote.Objects[FIndexNota]);
  FrameNota                  := TFRAME_NOTE_DI_CREDITO(NotaDiCredito.Frame);
  LB_NUMERO.Caption          := FrameNota.NUMERO.Text + '/' +
                                SostBoolean(NotaDiCredito.NotaElettronica,'E/','/') +
                                FrameNota.ANNO.Text;
  LB_DATA.Caption            := FrameNota.DATA.Text;
  LB_INTESTATARIO.Caption    := FrameNota.TITOLO_INTESTATARIO.Text + ' ' +
                                FrameNota.GENERALITA_INTESTATARIO.Text;
  LB_TOTALE.Caption          := FrameNota.LB_TOTALE.Caption;
  FTotale                    := FTotale + FrameNota.TotaleNota;

  if FrameNota.COND_PAGAMENTO.ItemIndex = -1 then LB_COND_PAGAMENTO.Caption := '-'
  else LB_COND_PAGAMENTO.Caption   := FrameNota.COND_PAGAMENTO.Items[FrameNota.COND_PAGAMENTO.ItemIndex];

  if NotaDiCredito.IdContratto <> -1 then
     LB_TELEFONO.Caption := GetDescrizioneRecord(QryGeneric,TABLEContratti,'TELEFONO1','CHIAVE',IntToStr(NotaDiCredito.IdContratto),'<???>');
  if NotaDiCredito.IdAmministratore <> -1 then
     LB_TELEFONO.Caption := GetDescrizioneRecord(QryGeneric,TABLEAmministratori,'TELEFONO1','CHIAVE',IntToStr(NotaDiCredito.IdAmministratore),'<???>');
end;

procedure TQR_STAMPA_RESOCONTO_NOTE.DetailResocontoNoteNeedData(
  Sender: TObject; var MoreData: Boolean);
begin
  Inc(FIndexNota);
  MoreData := FIndexNota < FListaNote.Count;
  if MoreData then TNodoNotaDiCredito(FListaNote.Objects[FIndexNota]).Disponi;
end;

Procedure TQR_STAMPA_RESOCONTO_NOTE.Init(Connection : TpFIBQuery; ListaNote : TStringList;
                                         Suggerimento : TStringList; AddTitolo : String);
begin
  FListaNote := ListaNote;
  SUGGERIMENTO_RESOCONTO.Lines.Assign(Suggerimento);
  FIBPlusCopyConnectionParams(Connection,QryGeneric);
  if IsValidName(AddTitolo) then
     LB_TITOLO.Caption := LB_TITOLO.Caption + ' ' + AddTitolo;
end;

procedure TQR_STAMPA_RESOCONTO_NOTE.QRLabel4Print(sender: TObject;
  var Value: string);
begin
  Value := FormatFloat('0.00',FTotale);
end;

procedure TQR_STAMPA_RESOCONTO_NOTE.QuickRepBeforePrint(
  Sender: TCustomQuickRep; var PrintReport: Boolean);
begin
  FIndexNota := -1;
  FTotale       := 0;
end;

procedure TQR_STAMPA_RESOCONTO_NOTE.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

end.
