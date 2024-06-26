@extends('dashboard.body.main')

@section('container')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card card-block">
                    <div class="card-header d-flex justify-content-between bg-primary">
                        <div class="iq-header-title">
                            <h4 class="card-title mb-0">Struk Pembayaran</h4>
                        </div>

                        <div class="invoice-btn d-flex">
                            <button type="button" class="btn btn-primary-dark mr-2" data-toggle="modal"
                                data-target=".bd-example-modal-lg">Konfirmasi</button>

                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-white">
                                            <h3 class="modal-title text-center mx-auto">Struk pembayaran dari
                                                {{ $customer->name }}<br />Total Tagihan: Rp
                                                {{ number_format($productItem->total(), 0, ',', '.') }}</h3>
                                        </div>
                                        <form action="{{ route('pos.sales.storeOrder') }}" method="post">
                                            @csrf
                                            <div class="modal-body">
                                                <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="payment_status">Jenis Pembayaran</label>
                                                        <select
                                                            class="form-control @error('payment_status') is-invalid @enderror"
                                                            name="payment_status">
                                                            <option selected="" disabled="">-- Pilih Pembayaran --
                                                            </option>
                                                            <option value="tunai">Tunai</option>
                                                            <option value="cek">Cek</option>
                                                            <option value="bon">Bon</option>
                                                        </select>
                                                        @error('payment_status')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="pay">Dibayarkan Sekarang</label>
                                                        <input type="number"
                                                            class="form-control @error('pay') is-invalid @enderror"
                                                            id="pay" name="pay" value="{{ old('pay') }}"
                                                            min="0" max="{{ $productItem->total() }}" placeholder="0" required>
                                                        @error('pay')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-dismiss="modal">Tutup</button>
                                                <button type="submit" class="btn btn-primary">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style='font-family:tahoma; font-size:8pt;'>
                        <div class="d-flex flex-column justify-content-center align-items-center py-5 px-3">
                            <table
                                style='width:auto; font-size:14pt; text-wrap: nowrap; font-family:calibri; border-collapse: collapse;'
                                border = '0'>
                                <td width='70%' align='CENTER' style='vertical-align:top'><span style='color:black;'>
                                        <b>PT. KARYAMEGA PUTRA MANDIRI</b></br>{{ strtoupper(auth()->user()->branch->address) }}</span></br>
                                    <span style='font-size:12pt'>
                                        {{ \Carbon\Carbon::now()->locale('id-ID')->translatedFormat('d F Y') }}
                                        {{ date('H:i:s') }} (seller: {{ auth()->user()->name }})</span></br>
                                </td>
                            </table>
                            <style>
                                table {
                                    font-size: 15px;
                                    border-collapse: collapse;
                                }

                                table td {
                                    padding-left: 5px;
                                    border: 0;
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
                            <table cellspacing='0' cellpadding='0'
                                style='width:auto; font-size:12pt; font-family:calibri;  border-collapse: collapse;'
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
                                @foreach ($productItem->content() as $item)
                                    <tr>
                                        <td style='vertical-align:top'>{{ $item->name }}</td>
                                        <td style='vertical-align:top; text-align:right; padding-right:10px'>Rp
                                            {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td style='vertical-align:top; text-align:right; padding-right:10px'>{{ $item->qty }}</td>
                                        <td style='text-align:right; vertical-align:top'>Rp
                                            {{ number_format($item->subtotal , 0, ',', '.') }}</td>
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
                                        {{ number_format($productItem->total(), 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
