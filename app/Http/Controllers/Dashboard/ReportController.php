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
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ReportController extends Controller
{
    public function index()
    {
        return view('report.index');
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

        $cell = 0;
        $totalCells = [];

        foreach ($branches as $branch) {
            $manager = $branch->users()->where('role_id', 3)->first();

            $sisaStockArr = [];
            $penjualanArr = [];
            $jumlahArr = [];
            $pengirimanArr = [];
            $stockAwalArr = [];

            foreach ($branch->products as $product) {
                $sisaStock = $product->product_store;

                $penjualan = $product->orderDetails()->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->first()->quantity ?? 0;

                $jumlah = $sisaStock + $penjualan;

                $pengiriman = $product->restockDetails()->with(['restock'])->whereHas('restock', function ($query) use ($startDate, $endDate) {
                    $query->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
                })->first()->quantity ?? 0;

                $stockAwal = $jumlah - $pengiriman;

                array_push($sisaStockArr, $sisaStock * $product->selling_price);
                array_push($penjualanArr, $penjualan * $product->selling_price);
                array_push($jumlahArr, $jumlah * $product->selling_price);
                array_push($pengirimanArr, $pengiriman * $product->selling_price);
                array_push($stockAwalArr, $stockAwal * $product->selling_price);

                $columns[] = [
                    'DISTRIBUSI' => ($index > 0) ? '' : strtoupper($branch->region) . ' - ' . strtoupper($manager->name ?? 'Unknown Manager'),
                    'NAMA PRODUK' => $product->product_name,
                    'STOCK AWAL' => $stockAwal,
                    'PENGIRIMAN' => $pengiriman,
                    'JUMLAH' => $jumlah,
                    'PENJUALAN' => $penjualan,
                    'SISA STOCK' => $sisaStock
                ];

                $index++;
                $cell++;
            }

            if ($index === 0) {
                $columns[] = [
                    'DISTRIBUSI' => strtoupper($branch->region) . ' - ' . strtoupper($manager->name ?? 'Unknown Manager'),
                    'NAMA PRODUK' => '',
                    'STOCK AWAL' => '',
                    'PENGIRIMAN' => '',
                    'JUMLAH' => '',
                    'PENJUALAN' => '',
                    'SISA STOCK' => ''
                ];

                $index++;
                $cell++;
            }

            $columns[] = [
                'DISTRIBUSI' => '',
                'NAMA PRODUK' => '',
                'STOCK AWAL' => 'Rp ' . number_format(array_sum($stockAwalArr), 0, ',', '.'),
                'PENGIRIMAN' => 'Rp ' . number_format(array_sum($pengirimanArr), 0, ',', '.'),
                'JUMLAH' => 'Rp ' . number_format(array_sum($jumlahArr), 0, ',', '.'),
                'PENJUALAN' => 'Rp ' . number_format(array_sum($penjualanArr), 0, ',', '.'),
                'SISA STOCK' => 'Rp ' . number_format(array_sum($sisaStockArr), 0, ',', '.')
            ];

            array_push($totalCells, $cell);

            $index = 0;
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();

            $formatedStartDate = Carbon::parse($startDate)->locale('id-ID')->translatedFormat('d F Y');
            $formatedEndDate = Carbon::parse($endDate)->locale('id-ID')->translatedFormat('d F Y');

            $workSheet->setCellValue('A1', 'DISTRIBUSI');
            $workSheet->setCellValue('A2', 'PERIODE ' . $formatedStartDate . ' S/D ' . $formatedEndDate);

            $workSheet->getStyle('A1:A2')->getFont()->setBold(true);

            $workSheet->getDefaultColumnDimension()->setWidth(20);
            $workSheet->fromArray($columns, null, 'A4');

            $workSheet->getStyle('A4:G4')->getFont()->setBold(true);
            $workSheet->getStyle('A4:G4')->getAlignment()->setHorizontal('center');
            $workSheet->getStyle('A4:G4')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA07A');
            $workSheet->getStyle('A5:A' . (count($columns) + 2))->getAlignment()->setHorizontal('center');
            $workSheet->getStyle('A5:A' . (count($columns) + 2))->getFont()->setBold(true);
            $workSheet->getStyle('A4:G' . (count($columns) + 3))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

            foreach ($totalCells as $key => $cell) {
                $cell = $cell + 3;
                $workSheet->getStyle('B' . $cell . ':G' . $cell)->getFont()->setBold(true);

                $workSheet->getStyle('B' . $cell . ':D' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('f5bd28');

                $workSheet->getStyle('E' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('b4c9e3');

                $workSheet->getStyle('F' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('feff01');

                $workSheet->getStyle('G' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('91d350');
            }

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

    public function generate()
    {
        $startDate = '2024-05-24';
        $endDate = '2024-06-24';

        $this->createDistributionReport($startDate, $endDate);
    }
}
