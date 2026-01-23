object QR_STAMPA_RESOCONTO_FATTURE: TQR_STAMPA_RESOCONTO_FATTURE
  AlignWithMargins = True
  Left = 0
  Top = 0
  Width = 1796
  Height = 1270
  Frame.Color = clBlack
  Frame.DrawTop = False
  Frame.DrawBottom = False
  Frame.DrawLeft = False
  Frame.DrawRight = False
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
  Page.Orientation = poLandscape
  Page.PaperSize = A4
  Page.Continuous = False
  Page.Values = (
    100.000000000000000000
    2100.000000000000000000
    100.000000000000000000
    2970.000000000000000000
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
  Zoom = 160
  PrevFormStyle = fsNormal
  PreviewInitialState = wsNormal
  PrevInitialZoom = qrZoomToFit
  PreviewDefaultSaveType = stQRP
  PreviewLeft = 0
  PreviewTop = 0
  object BAND_INTESTAZIONE: TQRBand
    Left = 60
    Top = 60
    Width = 1675
    Height = 273
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
      451.445312500000000000
      2769.856770833333000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbPageHeader
    object LB_TITOLO: TQRLabel
      Left = 1
      Top = 234
      Width = 1673
      Height = 37
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        60.476190476190480000
        1.889880952380953000
        387.425595238095200000
        2766.785714285715000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Resoconto fatture con Ri.Ba.'
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
    object DENOMINAZIONE_SOCIETA: TQRLabel
      Left = 512
      Top = 16
      Width = 629
      Height = 37
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        60.476190476190480000
        846.666666666666900000
        26.458333333333340000
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
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 14
    end
    object INTESTAZIONE_SOCIETA: TQRMemo
      Left = 512
      Top = 62
      Width = 629
      Height = 155
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        257.023809523809500000
        846.666666666666900000
        102.053571428571400000
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
      Left = 18
      Top = 16
      Width = 486
      Height = 211
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        349.627976190476200000
        30.238095238095240000
        26.458333333333340000
        803.199404761904600000)
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
  end
  object BAND_PAGE_FOOTER: TQRBand
    Left = 60
    Top = 462
    Width = 1675
    Height = 43
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
      71.106770833333340000
      2769.856770833333000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbPageFooter
    object QRLabel38: TQRLabel
      Left = 1530
      Top = 8
      Width = 62
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        2530.078125000000000000
        13.229166666666670000
        102.526041666666700000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      AutoStretch = False
      Caption = 'Pagina'
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
    object QRSysData1: TQRSysData
      Left = 1599
      Top = 8
      Width = 67
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        2644.179687500000000000
        13.229166666666670000
        110.794270833333300000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = True
      Color = clWhite
      Data = qrsPageNumber
      Transparent = False
      ExportAs = exptText
      FontSize = 10
    end
  end
  object BAND_LOOPING_ROW: TQRSubDetail
    Left = 60
    Top = 381
    Width = 1675
    Height = 33
    Frame.Color = clBlack
    Frame.DrawTop = True
    Frame.DrawBottom = True
    Frame.DrawLeft = True
    Frame.DrawRight = True
    AlignToBottom = False
    BeforePrint = BAND_LOOPING_ROWBeforePrint
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      54.570312499999990000
      2769.856770833333000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    Master = Owner
    FooterBand = BAND_GROUP_FOOTER
    OnNeedData = BAND_LOOPING_ROWNeedData
    PrintBefore = False
    PrintIfEmpty = False
    object LB_CAB: TQRLabel
      Left = 1560
      Top = 5
      Width = 106
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        2579.687500000000000000
        8.268229166666668000
        175.286458333333400000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '12345'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object LB_DATA_SCADENZA: TQRLabel
      Left = 2
      Top = 5
      Width = 98
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        3.307291666666666000
        8.268229166666668000
        162.057291666666700000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '25/12/2024'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object LB_IMPORTO: TQRLabel
      Left = 113
      Top = 5
      Width = 105
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        186.861979166666700000
        8.268229166666668000
        173.632812500000000000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '99999.99'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object LB_DATA_FATTURA: TQRLabel
      Left = 647
      Top = 5
      Width = 113
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        1069.908854166667000000
        8.268229166666668000
        186.861979166666700000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '25/12/2024'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object LB_NUMERO: TQRLabel
      Left = 763
      Top = 5
      Width = 100
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        1261.731770833333000000
        8.268229166666668000
        165.364583333333300000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '456'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object MM_BANCA: TQRMemo
      Left = 865
      Top = 5
      Width = 587
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        1430.403645833333000000
        8.268229166666668000
        970.690104166666600000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = True
      Color = clWhite
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 10
    end
    object LB_ABI: TQRLabel
      Left = 1458
      Top = 5
      Width = 106
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        2411.015625000000000000
        8.268229166666668000
        175.286458333333400000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '12345'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = []
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object MM_INTESTATARIO: TQRMemo
      Left = 228
      Top = 6
      Width = 414
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        377.031250000000000000
        9.921875000000000000
        684.609375000000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = True
      Color = clWhite
      Transparent = False
      WordWrap = True
      FullJustify = False
      MaxBreakChars = 0
      FontSize = 10
    end
    object QRShape2: TQRShape
      Left = 106
      Top = 1
      Width = 1
      Height = 25
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        41.341145833333340000
        175.286458333333400000
        1.653645833333333000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape3: TQRShape
      Left = 222
      Top = 0
      Width = 1
      Height = 30
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        49.609375000000000000
        367.109375000000000000
        0.000000000000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape4: TQRShape
      Left = 643
      Top = 2
      Width = 1
      Height = 26
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.994791666666670000
        1063.294270833333000000
        3.307291666666666000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape13: TQRShape
      Left = 760
      Top = 3
      Width = 1
      Height = 26
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.994791666666670000
        1256.770833333333000000
        4.960937500000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape14: TQRShape
      Left = 864
      Top = 3
      Width = 1
      Height = 26
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.994791666666670000
        1428.750000000000000000
        4.960937500000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape15: TQRShape
      Left = 1452
      Top = 3
      Width = 1
      Height = 26
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.994791666666670000
        2401.093750000000000000
        4.960937500000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
    object QRShape16: TQRShape
      Left = 1559
      Top = 2
      Width = 1
      Height = 26
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        42.994791666666670000
        2578.033854166667000000
        3.307291666666666000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsBandHeight
      VertAdjust = 0
    end
  end
  object BAND_GROUP_FOOTER: TQRBand
    Left = 60
    Top = 414
    Width = 1675
    Height = 48
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
      79.375000000000000000
      2769.856770833333000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    BandType = rbGroupFooter
    object LB_TOTALE_RESOCONTO: TQRLabel
      Left = 112
      Top = 13
      Width = 105
      Height = 31
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        51.263020833333340000
        185.208333333333300000
        21.497395833333330000
        173.632812500000000000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = '999999.99'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      OnPrint = LB_TOTALE_RESOCONTOPrint
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
    object QRLabel4: TQRLabel
      Left = 0
      Top = 13
      Width = 106
      Height = 31
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        51.263020833333340000
        0.000000000000000000
        21.497395833333330000
        175.286458333333400000)
      XLColumn = 0
      Alignment = taRightJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'TOTALE:'
      Color = clWhite
      Font.Charset = ANSI_CHARSET
      Font.Color = clWindowText
      Font.Height = -12
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 9
    end
  end
  object QRChildBand1: TQRChildBand
    Left = 60
    Top = 333
    Width = 1675
    Height = 48
    Frame.Color = clBlack
    Frame.DrawTop = True
    Frame.DrawBottom = True
    Frame.DrawLeft = True
    Frame.DrawRight = True
    AlignToBottom = False
    Color = clWhite
    TransparentBand = False
    ForceNewColumn = False
    ForceNewPage = False
    Size.Values = (
      79.375000000000000000
      2769.856770833333000000)
    PreCaluculateBandHeight = False
    KeepOnOnePage = False
    ParentBand = BAND_INTESTAZIONE
    PrintOrder = cboAfterParent
    object QRLabel1: TQRLabel
      Left = 222
      Top = 4
      Width = 419
      Height = 43
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        71.106770833333340000
        367.109375000000000000
        6.614583333333332000
        692.877604166666800000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Intestatario'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
    object QRLabelImporto: TQRLabel
      Left = 106
      Top = 3
      Width = 117
      Height = 42
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        69.453125000000000000
        175.286458333333400000
        4.960937500000000000
        193.476562500000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Importo'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
    object QRLabel8: TQRLabel
      Left = 865
      Top = 3
      Width = 587
      Height = 42
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        69.453125000000000000
        1430.403645833333000000
        4.960937500000000000
        970.690104166666600000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Banca'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
    object QRLabel15: TQRLabel
      Left = 1453
      Top = 3
      Width = 106
      Height = 42
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        69.453125000000000000
        2402.747395833333000000
        4.960937500000000000
        175.286458333333400000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'ABI'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
    object QRLabel10: TQRLabel
      Left = 1560
      Top = 3
      Width = 116
      Height = 43
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        71.106770833333340000
        2579.687500000000000000
        4.960937500000000000
        191.822916666666700000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'CAB'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
    object QRLabel5: TQRLabel
      Left = 643
      Top = 2
      Width = 117
      Height = 42
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        69.453125000000000000
        1063.294270833333000000
        3.307291666666666000
        193.476562500000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'DataFatt.'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
    object QRLabel3: TQRLabel
      Left = 761
      Top = 3
      Width = 102
      Height = 42
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        69.453125000000000000
        1258.424479166667000000
        4.960937500000000000
        168.671875000000000000)
      XLColumn = 0
      Alignment = taCenter
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Nr.'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
    object QRShape12: TQRShape
      Left = 1559
      Top = -2
      Width = 1
      Height = 49
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        81.028645833333320000
        2578.033854166667000000
        -3.307291666666666000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape10: TQRShape
      Left = 864
      Top = 0
      Width = 1
      Height = 47
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        77.721354166666680000
        1428.750000000000000000
        0.000000000000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape9: TQRShape
      Left = 760
      Top = 4
      Width = 1
      Height = 46
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        76.067708333333340000
        1256.770833333333000000
        6.614583333333332000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape8: TQRShape
      Left = 643
      Top = 0
      Width = 1
      Height = 47
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        77.721354166666680000
        1063.294270833333000000
        0.000000000000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape11: TQRShape
      Left = 1452
      Top = 0
      Width = 1
      Height = 47
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        77.721354166666680000
        2401.093750000000000000
        0.000000000000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape7: TQRShape
      Left = 222
      Top = 0
      Width = 1
      Height = 47
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        77.721354166666680000
        367.109375000000000000
        0.000000000000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRShape6: TQRShape
      Left = 106
      Top = 0
      Width = 1
      Height = 47
      Frame.Color = clBlack
      Frame.DrawTop = False
      Frame.DrawBottom = False
      Frame.DrawLeft = False
      Frame.DrawRight = False
      Size.Values = (
        77.721354166666680000
        175.286458333333400000
        0.000000000000000000
        1.653645833333333000)
      XLColumn = 0
      Shape = qrsVertLine
      VertAdjust = 0
    end
    object QRLabelDataScadenza: TQRLabel
      Left = 0
      Top = 2
      Width = 105
      Height = 43
      Frame.Color = clBlack
      Frame.DrawTop = True
      Frame.DrawBottom = True
      Frame.DrawLeft = True
      Frame.DrawRight = True
      Size.Values = (
        71.106770833333340000
        0.000000000000000000
        3.307291666666666000
        173.632812500000000000)
      XLColumn = 0
      Alignment = taLeftJustify
      AlignToBand = False
      AutoSize = False
      AutoStretch = False
      Caption = 'Data Sc.'
      Color = 16771536
      Font.Charset = ANSI_CHARSET
      Font.Color = clBlack
      Font.Height = -16
      Font.Name = 'Times New Roman'
      Font.Style = [fsBold]
      ParentFont = False
      Transparent = False
      WordWrap = True
      ExportAs = exptText
      WrapStyle = BreakOnSpaces
      FontSize = 12
    end
  end
  object QryGeneric: TpFIBQuery
    Left = 192
  end
end
