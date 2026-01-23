object QR_STAMPA_SALDO: TQR_STAMPA_SALDO
  Left = 0
  Top = 0
  Width = 1111
  Height = 1572
  Frame.Color = clBlack
  Frame.DrawTop = False
  Frame.DrawBottom = False
  Frame.DrawLeft = False
  Frame.DrawRight = False
  AfterPrint = QuickRepAfterPrint
  AfterPreview = QuickRepAfterPrint
  BeforePrint = QuickRepBeforePrint
  Font.Charset = ANSI_CHARSET
  Font.Color = clWindowText
  Font.Height = -13
  Font.Name = 'Times New Roman'
  Font.Style = []
  Functions.Strings = (
    'PAGENUMBER'
    'COLUMNNUMBER'
    'REPORTTITLE'
    'QRSTRINGSBAND1')
  Functions.DATA = (
    '0'
    '0'
    #39#39
    #39#39)
  OnPreview = QuickRepPreview
  Options = [FirstPageHeader, LastPageFooter]
  Page.Columns = 1
  Page.Orientation = poPortrait
  Page.PaperSize = A4
  Page.Continuous = False
  Page.Values = (
    100.000000000000000000
    2970.000000000000000000
    100.000000000000000000
    2100.000000000000000000
    100.000000000000000000
    100.000000000000000000
    0.000000000000000000)
  PrinterSettings.Copies = 1
  PrinterSettings.OutputBin = Auto
  PrinterSettings.Duplex = False
  PrinterSettings.FirstPage = 0
  PrinterSettings.LastPage = 0
  PrinterSettings.UseStandardprinter = False
  PrinterSettings.UseCustomBinCode = False
  PrinterSettings.CustomBinCode = 0
  PrinterSettings.ExtendedDuplex = 0
  PrinterSettings.UseCustomPaperCode = False
  PrinterSettings.CustomPaperCode = 0
  PrinterSettings.PrintMetaFile = False
  PrinterSettings.PrintQuality = 0
  PrinterSettings.Collate = 0
  PrinterSettings.ColorOption = 0
  PrintIfEmpty = True
  ShowProgress = False
  SnapToGrid = True
  Units = MM
  Zoom = 140
  PrevFormStyle = fsNormal
  PreviewInitialState = wsNormal
  PrevInitialZoom = qrZoomToFit
  PreviewDefaultSaveType = stQRP
  PreviewLeft = 0
  PreviewTop = 0
  object BAND_INTESTAZIONE: TQRBand
    Left = 53
    Top = 53
    Width = 1005
    Height = 316
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    BeforePrint = BAND_INTESTAZIONEBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      597.202380952380900000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbPageHeader
    object DENOMINAZIONE_SOCIETA: TQRLabel
      Left = 443
      Top = 20
      Width = 550
      Height = 32
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        60.476190476190480000
        837.217261904762000000
        37.797619047619050000
        1039.434523809524000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'DENOMINAZIONE_SOCIETA'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -19
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      OnPrint = DENOMINAZIONE_SOCIETAPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 14
    end
    object INTESTAZIONE_SOCIETA: TQRMemo
      Left = 443
      Top = 60
      Width = 550
      Height = 136
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        257.023809523809500000
        837.217261904762000000
        113.392857142857100000
        1039.434523809524000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = True
      Color = clWhite
      Lines.Strings = (
        '1'
        '2'
        '3'
        '4'
        '5'
        '6')
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 10
    end
    object QR_LOGO: TQRImage
      Left = 11
      Top = 17
      Width = 425
      Height = 185
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        349.627976190476200000
        20.788690476190480000
        32.127976190476190000
        803.199404761904800000)
      XLColumn = 0
      Stretch = True
      ImageStyle = imgStandard
      TableInfo.NumCols = 3
      TableInfo.NumRows = 10
      TableInfo.RowHeaderCount = 1
      TableInfo.ColHeaderCount = 0
      TableFontHeader.Charset = DEFAULT_CHARSET
      TableFontHeader.Color = clWindowText
      TableFontHeader.Height = -11
      TableFontHeader.Name = 'Tahoma'
      TableFontHeader.Style = []
      TableFontHeaderSize = 0
      TableFont.Charset = DEFAULT_CHARSET
      TableFont.Color = clWindowText
      TableFont.Height = -11
      TableFont.Name = 'Tahoma'
      TableFont.Style = []
      TableFontSize = 0
    end
    object QRLabel2: TQRLabel
      Left = 11
      Top = 235
      Width = 990
      Height = 31
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        58.586309523809540000
        20.788690476190480000
        444.122023809523800000
        1870.982142857143000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'SALDO FORNITORI'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -19
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 14
    end
    object LB_NOME_CAUSALE: TQRLabel
      Left = 11
      Top = 281
      Width = 990
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        20.788690476190480000
        531.056547619047700000
        1870.982142857143000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Data saldo'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
  end
  object BAND_SUMMARY: TQRSubDetail
    Left = 53
    Top = 369
    Width = 1005
    Height = 24
    Frame.Color = clBlack
    Frame.DrawTop = True
    Frame.DrawBottom = True
    Frame.DrawLeft = True
    Frame.DrawRight = True
    AlignToBottom = False
    BeforePrint = BAND_SUMMARYBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      45.357142857142850000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    Master = Owner
    FooterBand = BAND_FOOTER
    PrintBefore = False
    PrintIfEmpty = False
    object QRShape3: TQRShape
      Left = 786
      Top = 1
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1485.446428571429000000
        1.889880952380953000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape2: TQRShape
      Left = 166
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        313.720238095238100000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRLabel1: TQRLabel
      Left = 0
      Top = 2
      Width = 169
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        0.000000000000000000
        3.779761904761905000
        319.389880952381000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'CODICE'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel6: TQRLabel
      Left = 175
      Top = 0
      Width = 618
      Height = 24
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        45.357142857142850000
        330.729166666666700000
        0.000000000000000000
        1167.946428571428000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'DESCRIZIONE'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRLabel8: TQRLabel
      Left = 799
      Top = 1
      Width = 202
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1510.014880952381000000
        1.889880952380953000
        381.755952380952400000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'SALDO'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
  end
  object QRLabel4: TQRLabel
    Left = 19
    Top = 6
    Width = 63
    Height = 22
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    Size.Values = (
      41.577380952380950000
      35.907738095238090000
      11.339285714285710000
      119.062500000000000000)
    XLColumn = 0
    Alignment = taLeftJustify
    AlignToBand = False
    AutoSize = True
    AutoStretch = False
    Caption = 'Causale'
    Color = clWhite
    Font.Charset = ANSI_CHARSET
    Font.Color = clWindowText
    Font.Height = -13
    Font.Name = 'Times New Roman'
    Font.Style = [fsBold]
    ParentFont = False
    Transparent = False
    WordWrap = True
    ExportAs = exptText
    WrapStyle = BreakOnSpaces
    FontSize = 10
  end
  object BAND_FOOTER: TQRBand
    Left = 53
    Top = 417
    Width = 1005
    Height = 44
    Frame.Color = clBlack
    Frame.DrawTop = False
    Frame.DrawBottom = False
    Frame.DrawLeft = False
    Frame.DrawRight = False
    AlignToBottom = False
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      83.154761904761910000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbGroupFooter
    object LB_TOTALE_SALDO: TQRLabel
      Left = 799
      Top = 12
      Width = 201
      Height = 27
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        51.026785714285720000
        1510.014880952381000000
        22.678571428571430000
        379.866071428571500000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '99999.99'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -15
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 11
    end
  end
  object BAND_ELENCO_CLIENTI: TQRChildBand
    Left = 53
    Top = 393
    Width = 1005
    Height = 24
    Frame.Color = clBlack
    Frame.DrawTop = True
    Frame.DrawBottom = True
    Frame.DrawLeft = True
    Frame.DrawRight = True
    AlignToBottom = False
    BeforePrint = BAND_ELENCO_CLIENTIBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      45.357142857142850000
      1899.330357142857000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    ParentBand = BAND_SUMMARY
    PrintOrder = cboAfterParent
    object LB_CODICE_CLIENTE: TQRLabel
      Left = 0
      Top = 2
      Width = 169
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        0.000000000000000000
        3.779761904761905000
        319.389880952381000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Codice Cliente'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object LB_SALDO: TQRLabel
      Left = 799
      Top = 2
      Width = 202
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        1510.014880952381000000
        3.779761904761905000
        381.755952380952400000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Totale da pagare'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -13
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 10
    end
    object QRShape8: TQRShape
      Left = 166
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        313.720238095238100000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape11: TQRShape
      Left = 786
      Top = 0
      Width = 14
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        47.247023809523810000
        1485.446428571429000000
        0.000000000000000000
        26.458333333333340000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object LB_DESCRIZIONE_CLIENTE: TQRMemo
      Left = 175
      Top = 2
      Width = 618
      Height = 22
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.577380952380950000
        330.729166666666700000
        3.779761904761905000
        1167.946428571428000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Color = clWhite
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 10
    end
  end
end
