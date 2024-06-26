<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>PT KPM | Print Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">

    <!-- External CSS libraries -->
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/bootstrap.min.css') }}">

    <!-- Google fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Custom Stylesheet -->
    <link type="text/css" rel="stylesheet" href="{{ asset('assets/invoice/css/style.css') }}">
    <style>
        table {
            font-size: 15px;
            border-collapse: collapse;
        }

        table td {
            padding-left: 5px;
            border: 1px solid white !important;
        }

        hr {
            display: block;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
            margin-left: auto;
            margin-right: auto;
            border-style: inset;
            border-width: 1px;
        }
    </style>
</head>

<body>
    <iframe src="{{ route('order.invoiceDownload', $order->id) }}"
        style="width: 100%; height: 100%; position:absolute; top:0; z-index:-1"></iframe>
    <div class="invoice-16 invoice-content">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div style='font-family:tahoma; font-size:8pt;'>
                        <div class="d-flex flex-column justify-content-center align-items-center p-5">
                            <table
                                style='width:350px; font-size:14pt; text-wrap: nowrap; font-family:calibri; border-collapse: collapse;'
                                border = '0'>
                                <td width='70%' align='CENTER' style='vertical-align:top'><span style='color:black;'>
                                        <b>PT. KARYAMEGA PUTRA MANDIRI</b></br>{{ strtoupper(auth()->user()->branch->address) }}</span></br>
                                    <span style='font-size:12pt'>No :
                                        {{ ltrim(last(explode('-', $order->invoice_no)), '0') }},
                                        {{ \Carbon\Carbon::now()->locale('id-ID')->translatedFormat('d F Y') }}
                                        {{ date('H:i:s') }} (seller: {{ auth()->user()->name }})</span></br>
                                </td>
                            </table>
                            <table cellspacing='0' cellpadding='0'
                                style='width:400px; font-size:12pt; font-family:calibri;  border-collapse: collapse;'
                                border='0'>

                                <tr align='center'>
                                    <td width='40%' style="text-align: center">Barang</td>
                                    <td width='20%' style="text-align: center">Harga</td>
                                    <td width='10%' style="text-align: center">Jum</td>
                                    <td width='30%' style="text-align: center">Total</td>
                                <tr>
                                    <td colspan='4'>
                                        <hr>
                                    </td>
                                </tr>
                                </tr>
                                @foreach ($orderDetails as $item)
                                    <tr>
                                        <td style='vertical-align:top'>{{ $item->product->product_name }}</td>
                                        <td style='vertical-align:top; text-align:right; padding-right:10px'>Rp
                                            {{ number_format($item->unitcost, 0, ',', '.') }}</td>
                                        <td style='vertical-align:top; text-align:right; padding-right:10px'>
                                            {{ $item->quantity }}</td>
                                        <td style='text-align:right; vertical-align:top'>Rp
                                            {{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td colspan='4'>
                                        <hr>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan = '3'>
                                        <div style='text-align:right; color:black'>Total : </div>
                                    </td>
                                    <td style='text-align:right; font-size:14pt; text-wrap: nowrap; color:black'>Rp
                                        {{ number_format($order->total, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan = '3'>
                                        <div style='text-align:right; color:black'>Paid : </div>
                                    </td>
                                    <td style='text-align:right; font-size:16pt; color:black'> Rp
                                        {{ number_format($order->pay, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan = '3'>
                                        <div style='text-align:right; color:black'>Change : </div>
                                    </td>
                                    <td style='text-align:right; font-size:16pt; color:black'>Rp
                                        {{ number_format(max($order->pay - $order->total, 0), 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td colspan = '3'>
                                        <div style='text-align:right; color:black'>Sisa : </div>
                                    </td>
                                    <td style='text-align:right; font-size:16pt; color:black'>Rp
                                        {{ number_format(max($order->total - $order->pay, 0), 0, ',', '.') }}</td>
                                </tr>
                            </table>
                            <table style='width:350; font-size:12pt;' cellspacing='2'>
                                <tr></br>
                                    <td align='center'>****** TERIMAKASIH ******</br></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="invoice-btn-section clearfix d-print-none">
                        <a href="javascript:window.print()" class="btn btn-lg btn-print">
                            Cetak Struk
                        </a>
                        <a id="invoice_download_btn" class="btn btn-lg btn-download">
                            Unduh Invoice
                        </a>
                        <a href="{{ auth()->user()->isSalesRole() ? route('pos.salesPos') : route('order.completeOrders') }}"
                            class="btn btn-primary btn-lg">
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/invoice/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/invoice/js/jspdf.min.js') }}"></script>
    <script src="{{ asset('assets/invoice/js/html2canvas.js') }}"></script>
    <script src="{{ asset('assets/invoice/js/app.js') }}"></script>
</body>

</html>
