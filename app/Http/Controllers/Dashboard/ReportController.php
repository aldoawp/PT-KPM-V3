<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Restock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportController extends Controller
{
    public function index_distribusi()
    {
        return view('report.index-distribusi');
    }

    public function index_penjualan()
    {
        return view('report.index-penjualan', [
            'branches' => Branch::all()
        ]);
    }

    private function createDistributionReport($startDate, $endDate)
    {
        $columns[] = [
            'DISTRIBUSI',
            'NAMA PRODUK',
            'STOCK AWAL',
            'PENGIRIMAN',
            'JUMLAH',
            'PENJUALAN',
            'SISA STOCK'
        ];

        $branches = Branch::all();

        $index = 0;

        foreach ($branches as $branch) {
            $manager = $branch->users()->where('role_id', 3)->first();

            $sisaStockArr = [];
            $penjualan1HariArr = [];
            $jumlahArr = [];
            $pengirimanArr = [];
            $stockAwalArr = [];

            foreach ($branch->products as $product) {
                $sisaStock = $product->product_store;

                $penjualan1Hari = $product->orderDetails()->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->first()->quantity ?? 0;

                $jumlah = $sisaStock + $penjualan1Hari;

                $pengiriman = $product->restockDetails()->with(['restock'])->whereHas('restock', function ($query) use ($startDate, $endDate) {
                    $query->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
                })->first()->quantity ?? 0;

                $stockAwal = $jumlah - $pengiriman;

                array_push($sisaStockArr, $sisaStock * $product->selling_price);
                array_push($penjualan1HariArr, $penjualan1Hari * $product->selling_price);
                array_push($jumlahArr, $jumlah * $product->selling_price);
                array_push($pengirimanArr, $pengiriman * $product->selling_price);
                array_push($stockAwalArr, $stockAwal * $product->selling_price);

                $columns[] = [
                    'DISTRIBUSI' => ($index > 0) ? '' : strtoupper($branch->region) . ' - ' . strtoupper($manager->name ?? 'Samsul'),
                    'NAMA PRODUK' => $product->product_name,
                    'STOCK AWAL' => $stockAwal,
                    'PENGIRIMAN' => $pengiriman,
                    'JUMLAH' => $jumlah,
                    'PENJUALAN' => $penjualan1Hari,
                    'SISA STOCK' => $sisaStock
                ];

                $index++;
            }

            if ($index === 0) {
                $columns[] = [
                    'DISTRIBUSI' => strtoupper($branch->region) . ' - ' . strtoupper($manager->name ?? 'Samsul'),
                    'NAMA PRODUK' => '',
                    'STOCK AWAL' => '',
                    'PENGIRIMAN' => '',
                    'JUMLAH' => '',
                    'PENJUALAN' => '',
                    'SISA STOCK' => ''
                ];
            }

            $columns[] = [
                'DISTRIBUSI' => '',
                'NAMA PRODUK' => '',
                'STOCK AWAL' => 'Rp ' . number_format(array_sum($stockAwalArr), 0, ',', '.'),
                'PENGIRIMAN' => 'Rp ' . number_format(array_sum($pengirimanArr), 0, ',', '.'),
                'JUMLAH' => 'Rp ' . number_format(array_sum($jumlahArr), 0, ',', '.'),
                'PENJUALAN' => 'Rp ' . number_format(array_sum($penjualan1HariArr), 0, ',', '.'),
                'SISA STOCK' => 'Rp ' . number_format(array_sum($sisaStockArr), 0, ',', '.')
            ];

            $index = 0;
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();

            $formatedStartDate = Carbon::parse($startDate)->locale('id-ID')->translatedFormat('d F Y');
            $formatedEndDate = Carbon::parse($endDate)->locale('id-ID')->translatedFormat('d F Y');

            $spreadSheet->getActiveSheet()->setCellValue('A1', 'DISTRIBUSI');
            $spreadSheet->getActiveSheet()->setCellValue('A2', 'PERIODE ' . $formatedStartDate . ' S/D ' . $formatedEndDate);

            $spreadSheet->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);
            $spreadSheet->getActiveSheet()->fromArray($columns, null, 'A4');

            $excelWritter = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="LAPORAN_DISTRIBUSI.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $excelWritter->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function createSalesReport()
    {
        dd();
    }

    public function generate()
    {
        $startDate = '2024-05-24';
        $endDate = '2024-06-24';

        $this->createDistributionReport($startDate, $endDate);
    }
}
