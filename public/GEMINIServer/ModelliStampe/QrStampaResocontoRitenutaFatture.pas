unit QrStampaResocontoRitenutaFatture;

interface

uses Windows, SysUtils, Messages, Classes, Graphics, Controls,FrameFatture,
     StdCtrls, ExtCtrls, Forms, QuickRpt, QRCtrls, pFIBQuery,ZTStringsFunct,
     QrPrnTr, ZFormPreview, DB,  Globale, AllTexts, FIBDataSet,ContNrs,
     ZStringFunct,ZMathFunct,ZDbaseFunct,pFIBDataSet,ZHandleImage, FIBQuery,
     ZFileFunct,ZFormConfAnnulla,ZStringConvFunct,ComCtrls;

type
  TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE = class(TQuickRep)
    BAND_INTESTAZIONE: TQRBand;
    BAND_PAGE_FOOTER: TQRBand;
    QryGeneric: TpFIBQuery;
    LB_TITOLO: TQRLabel;
    QRLabel38: TQRLabel;
    QRSysData1: TQRSysData;
    LB_INTESTATARIO: TQRLabel;
    LB_RITENUTA: TQRLabel;
    LB_DATA: TQRLabel;
    LB_NUMERO: TQRLabel;
    BAND_GROUP_FOOTER: TQRBand;
    QRChildBand1: TQRChildBand;
    QRLabel13: TQRLabel;
    QRLabel19: TQRLabel;
    QRLabel1: TQRLabel;
    QRLabel3: TQRLabel;
    LB_TOTALE: TQRLabel;
    QRLabel5: TQRLabel;
    LB_TOT_FATTURE: TQRLabel;
    LB_TOT_RITENUTE: TQRLabel;
    QRLabel4: TQRLabel;
    QRShape7: TQRShape;
    QRShape1: TQRShape;
    QRShape3: TQRShape;
    QRShape4: TQRShape;
    QRShape6: TQRShape;
    QRShape8: TQRShape;
    QRShape10: TQRShape;
    QRShape11: TQRShape;
    DENOMINAZIONE_SOCIETA: TQRLabel;
    INTESTAZIONE_SOCIETA: TQRMemo;
    QR_LOGO: TQRImage;
    procedure QuickRepPreview(Sender: TObject);
    procedure DetailResocontoFattureNeedData(Sender: TObject;
      var MoreData: Boolean);
    procedure QuickRepBeforePrint(Sender: TCustomQuickRep;
      var PrintReport: Boolean);
    procedure DetailResocontoFattureBeforePrint(Sender: TQRCustomBand;
      var PrintBand: Boolean);
    procedure LB_TERZA_RATAPrint(sender: TObject; var Value: string);
    procedure BAND_RATABeforePrint(Sender: TQRCustomBand; var PrintBand: Boolean);
    procedure LB_TOT_FATTUREPrint(sender: TObject; var Value: string);
    procedure LB_TOT_RITENUTEPrint(sender: TObject; var Value: string);
  private
    FListaFatture  : TStringList;
    FIndexFattura  : Integer;
    FIndexRata     : Integer;
    FTotTotali     : Double;
    FTotRitenute   : Double;
    FORM_PREVIEW   : TFORM_PREVIEW;
    FProgressBar   : TProgressBar;
  public
   Procedure EsportaSuFile(FileName : String);
   Procedure Init(Connection : TpFIBQuery; ListaFatture : TStringList;
                  Suggerimento : TStringList; AddTitolo : String;
                  ProgressBar : TProgressBar);
  end;

implementation

{$R *.DFM}

Procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.EsportaSuFile(FileName : String);
var BodyFile  : TStringList;
    PrintBand : Boolean;
    i         : Integer;
begin
 BodyFile := TStringList.Create;
 try
  BodyFile.Add(LB_TITOLO.Caption);
  BodyFile.Add(SUGGERIMENTO_RESOCONTO.Lines.Text);
  BodyFile.Add('Numero' + #9 + 'Data' + #9 + 'Intestatario' + #9 + 'Totale' + #9 + 'Ritenuta');
  for i := 0 to FListaFatture.Count - 1 do
      begin
        FIndexFattura := i;
        DetailResocontoFattureBeforePrint(Nil,PrintBand);
        if PrintBand then
           BodyFile.Add(' ' + LB_NUMERO.Caption + #9 + ' ' + LB_DATA.Caption + #9 +
                        LB_INTESTATARIO.Caption + #9 + LB_TOTALE.Caption + #9 +
                        LB_RITENUTA.Caption);
      end;
  BodyFile.SaveToFile(FileName);
  if Conferma(CAPTION_MESSAGE,'Vuoi vedere il file generato?') then
     ExecuteFile(FileName,'','',WS_MAXIMIZE);
 finally
  BodyFile.Free;
 end;
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.BAND_RATABeforePrint(Sender: TQRCustomBand; var PrintBand: Boolean);
var Fattura      : TNodoFattura;
    FrameFattura : TFRAME_FATTURE;
    TmpString    : String;
begin
  PrintBand := FIndexRata <> -1;
  if PrintBand then
     begin
      Fattura := TNodoFattura(FListaFatture.Objects[FIndexFattura]);
      FrameFattura := TFRAME_FATTURE(Fattura.Frame);
      With FrameFattura.GRID_RATE do
           begin
            LB_RATA_DATA.Caption    := Cells[COL_FATT_ATTIVA_RATE_DATA,FIndexRata].Text;
            LB_RATA_IMPORTO.Caption := Cells[COL_FATT_ATTIVA_RATE_IMPORTO,FIndexRata].Text;
            if Cells[COL_FATT_ATTIVA_RATE_IS_NOTA,FIndexRata].Tag = ROW_IS_NOTA then
               begin
                 LB_RATA_DESCRIZIONE.Caption    := Cells[COL_FATT_ATTIVA_RATE_DESCRIZIONE,FIndexRata].Text;
                 LB_RATA_DESCRIZIONE.Font.Color := clBlue;
               end
            else
               begin
                if Cells[COL_FATT_ATTIVA_RATE_DATA_PAGAMENTO,FIndexRata].Date = 0.0 then
                   begin
                    LB_RATA_DESCRIZIONE.Caption    := 'Rata NON pagata';
                    LB_RATA_DESCRIZIONE.Font.Color := clRed;
                   end
                else
                   begin
                    LB_RATA_DESCRIZIONE.Font.Color := clBlack;
                    TmpString := 'Rata pagata in data ' + Cells[COL_FATT_ATTIVA_RATE_DATA_PAGAMENTO,FIndexRata].Text;
                    if Cells[COL_FATT_ATTIVA_RATE_CASSA_CONTANTI,FIndexRata].BoolValue then
                       TmpString := TmpString + ' in contanti'
                    else
                      begin
                       if Cells[COL_FATT_ATTIVA_RATE_CASSA_ASSEGNI,FIndexRata].BoolValue then
                          TmpString := TmpString + ' con assegno'
                       else TmpString := TmpString + ' su conto ' + Cells[COL_FATT_ATTIVA_RATE_CASSA_ASSEGNI,FIndexRata].Text;
                      end;
                    LB_RATA_DESCRIZIONE.Caption := TmpString;
                   end;
               end;
           end;
     end;
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.DetailResocontoFattureBeforePrint(
  Sender: TQRCustomBand; var PrintBand: Boolean);
var Fattura      : TNodoFattura;
    FrameFattura : TFRAME_FATTURE;
begin
  Fattura := TNodoFattura(FListaFatture.Objects[FIndexFattura]);
  FrameFattura := TFRAME_FATTURE(Fattura.Frame);
  PrintBand := FIndexRata <= FrameFattura.GRID_RATE.FixedRows;
  if Sender = Nil then
     begin
      Fattura.LoadImage := False;
      try
         Fattura.Disponi;
      finally
        Fattura.LoadImage := True;
      end;
     end;

  if PrintBand then
     begin
      if FrameFattura.ANNO.Value >= 2019 then
         LB_NUMERO.Caption := FrameFattura.PROGRESSIVO_INVIO.Caption
      else LB_NUMERO.Caption          := FrameFattura.NUMERO.Text +
                                    SostBoolean(Fattura.FatturaElettronica,'E/','/') +
                                    FrameFattura.ANNO.Text;
      LB_DATA.Caption            := FrameFattura.DATA.Text;
      LB_INTESTATARIO.Caption    := FrameFattura.TITOLO_INTESTATARIO.Text + ' ' +
                                    FrameFattura.GENERALITA_INTESTATARIO.Text;
      LB_TOTALE.Caption          := FrameFattura.LB_TOTALE.Caption;
      FTotTotali                 := FTotTotali + FrameFattura.TotaleFattura;
      LB_RITENUTA.Caption        := FrameFattura.LB_TOT_RITENUTA.Caption;
      FTotRitenute               := FTotRitenute + FrameFattura.TotRitenuta;
     end;
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.DetailResocontoFattureNeedData(
  Sender: TObject; var MoreData: Boolean);
var FrameFattura : TFRAME_FATTURE;
    Fattura      : TNodoFattura;
    OldIndexFat  : Integer;
begin
  OldIndexFat := FIndexFattura;
  if FIndexFattura > -1 then
     begin
      if FIndexRata = -1 then
         Inc(FIndexFattura)
      else
         begin
          Fattura := TNodoFattura(FListaFatture.Objects[FIndexFattura]);
          FrameFattura := TFRAME_FATTURE(Fattura.Frame);
          Inc(FIndexRata);
          if FIndexRata >= FrameFattura.GRID_RATE.LastRow then
             begin
              FIndexRata := -1;
              Inc(FIndexFattura);
             end;
         end;
     end
  else FIndexFattura := 0;
  MoreData := FIndexFattura < FListaFatture.Count;
  if FListaFatture.Count <> 0 then
     begin
      FProgressBar.Position := Trunc(FIndexFattura / FListaFatture.Count * 100);
      Application.ProcessMessages;
     end;
  if MoreData and (OldIndexFat <> FIndexFattura) then
     begin
      Fattura := TNodoFattura(FListaFatture.Objects[FIndexFattura]);
      FrameFattura := TFRAME_FATTURE(Fattura.Frame);
      TNodoFattura(FListaFatture.Objects[FIndexFattura]).Disponi;
      if FrameFattura.GRID_RATE.LastRow = FrameFattura.GRID_RATE.FixedRows + 1 then
         FIndexRata := -1
      else FIndexRata := FrameFattura.GRID_RATE.FixedRows;
     end;
end;

Procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.Init(Connection : TpFIBQuery; ListaFatture : TStringList;
                                            Suggerimento : TStringList; AddTitolo : String;
                                            ProgressBar : TProgressBar);
begin
  FListaFatture := ListaFatture;
  FProgressBar  := ProgressBar;
  SUGGERIMENTO_RESOCONTO.Lines.Assign(Suggerimento);
  FIBPlusCopyConnectionParams(Connection,QryGeneric);
  if IsValidName(AddTitolo) then
     LB_TITOLO.Caption := LB_TITOLO.Caption + ' ' + AddTitolo;
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.LB_TERZA_RATAPrint(sender: TObject; var Value: string);
begin
  Value := ' ';
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.LB_TOT_FATTUREPrint(
  sender: TObject; var Value: string);
begin
  Value  := FormatFloat('0.00',FTotTotali);
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.LB_TOT_RITENUTEPrint(
  sender: TObject; var Value: string);
begin
  Value := FormatFloat('0.00',FTotRitenute);
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.QuickRepBeforePrint(
  Sender: TCustomQuickRep; var PrintReport: Boolean);
begin
  FIndexFattura := -1;
  FIndexRata    := -1;
  FTotTotali    := 0;
  FTotRitenute  := 0;
end;

procedure TQR_STAMPA_RESOCONTO_RITENUTA_FATTURE.QuickRepPreview(Sender: TObject);
begin
 FORM_PREVIEW := TFORM_PREVIEW.Create(Self);
 FORM_PREVIEW.THE_PREVIEW.QRPrinter := TQrPrinter(Sender);
 FORM_PREVIEW.Show;
end;

end.
