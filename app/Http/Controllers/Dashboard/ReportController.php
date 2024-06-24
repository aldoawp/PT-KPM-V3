<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\OrderDetails;
use Exception;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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

        $cell = 1;
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
                'STOCK AWAL' => array_sum($stockAwalArr),
                'PENGIRIMAN' => array_sum($pengirimanArr),
                'JUMLAH' => array_sum($jumlahArr),
                'PENJUALAN' => array_sum($penjualanArr),
                'SISA STOCK' => array_sum($sisaStockArr)
            ];

            array_push($totalCells, $cell);

            $index = 0;
            $cell++;
        }

        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '4000M');

        try {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();

            $formatedStartDate = Carbon::parse($startDate)->locale('id-ID')->translatedFormat('d F Y');
            $formatedEndDate = Carbon::parse($endDate)->locale('id-ID')->translatedFormat('d F Y');

            $workSheet->setCellValue('A1', 'DISTRIBUSI');
            $workSheet->setCellValue('A2', 'PERIODE ' . $formatedStartDate . ' s/d ' . $formatedEndDate);

            $workSheet->getStyle('A1:A2')->getFont()->setBold(true);

            $workSheet->getDefaultColumnDimension()->setWidth(20);
            $workSheet->fromArray($columns, null, 'A4', true);

            $workSheet->getStyle('A4:G4')->getFont()->setBold(true);

            $workSheet->getStyle('A4:G4')->getAlignment()->setHorizontal('center');

            $workSheet->getStyle('A4:G4')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA07A');

            $workSheet->getStyle('A5:A' . (count($columns) + 2))->getAlignment()->setHorizontal('center');

            $workSheet->getStyle('A5:A' . (count($columns) + 2))->getFont()->setBold(true);

            $workSheet->getStyle('A4:G' . (count($columns) + 3))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

            for ($i = 0; $i < count($totalCells); $i++) {
                $cell = $totalCells[$i] + 4;

                $workSheet->mergeCells('A' . ($totalCells[$i - 1] ?? 0) + 5 . ':A' . $cell);

                $workSheet->getStyle('A' . ($totalCells[$i - 1] ?? 0) + 5 . ':A' . $cell)->getAlignment()->setVertical('center');

                $workSheet->getStyle('A' . ($totalCells[$i - 1] ?? 0) + 5 . ':A' . $cell)->getAlignment()->setWrapText(true);

                $workSheet->getStyle('B' . $cell . ':G' . $cell)->getFont()->setBold(true);

                $workSheet->getStyle('B' . $cell . ':G' . $cell)->getNumberFormat()->setFormatCode('"Rp "#,##0;-"Rp "#,##0');

                $workSheet->getStyle('B' . $cell . ':D' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('f5bd28');

                $workSheet->getStyle('E' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('b4c9e3');

                $workSheet->getStyle('F' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('feff01');

                $workSheet->getStyle('G' . $cell)->getFill()->setFillType('solid')->getStartColor()->setARGB('91d350');
            }

            $excelWritter = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="LAPORAN_DISTRIBUSI_' . date('d_m_Y') . '.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $excelWritter->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    private function createSalesReport($branch_id, $startDate, $endDate)
    {
        $baseColumns[] = [
            'NO',
            'TANGGAL',
            'NAMA PELANGGAN',
            'NO. INVOICE',
            'ALAMAT'
        ];

        // Zero means all branch
        if ($branch_id === 0) {
            $branches = Branch::all();

            try {
                $spreadSheet = new Spreadsheet();

                $iter = 1;

                foreach ($branches as $branch) {
                    $spreadSheet->createSheet();
                    $spreadSheet->setActiveSheetIndex($iter++);
                    $workSheet = $spreadSheet->getActiveSheet();

                    $workSheet->setTitle($branch->region);

                    $columns = $baseColumns;

                    $products = $branch->products;

                    foreach ($products as $product) {
                        array_push($columns[0], strtoupper($product->product_name) . ' (' . 'Rp ' . number_format($product->selling_price, 0, ',', '.') . ')');
                    }

                    array_push($columns[0], 'TOTAL PENJUALAN');
                    array_push($columns[0], 'SALES');

                    $productStarts = 5;
                    $productsEnds = count($columns[0]) - 3;

                    $orders = Order::where('branch_id', $branch->id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();

                    $index = 1;

                    for ($i = 0; $i < count($orders); $i++) {
                        $order = $orders[$i];

                        $columns[$index] = [
                            'NO' => $i + 1,
                            'TANGGAL' => Carbon::parse($order->created_at)->format('d/m/Y'),
                            'NAMA PELANGGAN' => $order->customer->name,
                            'NO. INVOICE' => ltrim(last(explode('-', $order->invoice_no)), '0'),
                            'ALAMAT' => $order->customer->address
                        ];

                        $totalPenjualan = 0;

                        for ($j = $productStarts; $j <= $productsEnds; $j++) {
                            $product = $products[$j - $productStarts];

                            $orderDetail = $order->orderDetails()->where('product_id', $product->id)->first();

                            $columns[$index][$columns[0][$j]] = $orderDetail->quantity ?? 0;

                            $totalPenjualan += $orderDetail->quantity ?? 0 * $product->selling_price;
                        }

                        $columns[$index]['TOTAL PENJUALAN'] = $totalPenjualan;
                        $columns[$index]['SALES'] = $order->user->name;

                        $index++;
                    }

                    $columns[$index] = [
                        'NO' => 'GRAND TOTAL',
                        'TANGGAL' => '',
                        'NAMA PELANGGAN' => '',
                        'NO. INVOICE' => '',
                        'ALAMAT' => ''
                    ];

                    $totalPenjualan = 0;

                    for ($j = $productStarts; $j <= $productsEnds; $j++) {
                        $quantitySum = OrderDetails::where('product_id', $products[$j - $productStarts]->id)->whereHas('order', function ($query) use ($branch, $startDate, $endDate) {
                            $query->where('branch_id', $branch->id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
                        })->sum('quantity');

                        $columns[$index][$columns[0][$j]] = $quantitySum;

                        $totalPenjualan += $quantitySum * $product->selling_price;
                    }

                    $columns[$index]['TOTAL PENJUALAN'] = $totalPenjualan;
                    $columns[$index]['SALES'] = '';

                    $formatedStartDate = Carbon::parse($startDate)->locale('id-ID')->translatedFormat('d F Y');
                    $formatedEndDate = Carbon::parse($endDate)->locale('id-ID')->translatedFormat('d F Y');

                    $workSheet->setCellValue('A1', 'LAPORAN PENJUALAN GROSIR TUNAI AREA ' . strtoupper($branch->region));
                    $workSheet->setCellValue('A2', 'PERIODE ' . $formatedStartDate . ' s/d ' . $formatedEndDate);

                    $workSheet->getStyle('A1:A2')->getFont()->setBold(true);

                    $workSheet->getDefaultColumnDimension()->setWidth(20);
                    $workSheet->fromArray($columns, null, 'A4', true);

                    $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . '4')->getFont()->setBold(true);

                    $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . '4')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA07A');

                    $workSheet->getStyle('A5:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setHorizontal('center');

                    $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setVertical('center');

                    $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setWrapText(true);

                    $workSheet->getColumnDimension('A')->setWidth(5);

                    $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

                    $workSheet->getStyle('B4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setHorizontal('center');

                    foreach (range('B', chr(ord('A') + count($columns[0]) - 1)) as $column) {
                        $workSheet->getColumnDimension($column)->setAutoSize(true);
                    }

                    $workSheet->getStyle('A' . (count($columns) + 3) . ':' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA07A');

                    $workSheet->getStyle('A' . (count($columns) + 3) . ':' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getFont()->setBold(true);

                    $workSheet->mergeCells('A' . (count($columns) + 3) . ':' . 'E' . (count($columns) + 3));

                    $workSheet->getStyle('F' . (count($columns) + 3) . ':' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getNumberFormat()->setFormatCode('"Rp "#,##0;-"Rp "#,##0');
                }

                $spreadSheet->removeSheetByIndex(0);
                $spreadSheet->setActiveSheetIndex(0);

                $excelWritter = new Xls($spreadSheet);
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="LAPORAN_PENJUALAN_ALL_REGION_' . date('d_m_Y') . '.xls"');
                header('Cache-Control: max-age=0');
                ob_end_clean();
                $excelWritter->save('php://output');
                exit();
            } catch (Exception $e) {
                return;
            }
        }

        $columns = $baseColumns;

        $products = Branch::find($branch_id)->products;

        foreach ($products as $product) {
            array_push($columns[0], strtoupper($product->product_name) . ' (' . 'Rp ' . number_format($product->selling_price, 0, ',', '.') . ')');
        }

        array_push($columns[0], 'TOTAL PENJUALAN');
        array_push($columns[0], 'SALES');

        $productStarts = 5;
        $productsEnds = count($columns[0]) - 3;

        $orders = Order::where('branch_id', $branch_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate)->get();

        $index = 1;

        for ($i = 0; $i < count($orders); $i++) {
            $order = $orders[$i];

            $columns[$index] = [
                'NO' => $i + 1,
                'TANGGAL' => Carbon::parse($order->created_at)->format('d/m/Y'),
                'NAMA PELANGGAN' => $order->customer->name,
                'NO. INVOICE' => ltrim(last(explode('-', $order->invoice_no)), '0'),
                'ALAMAT' => $order->customer->address
            ];

            $totalPenjualan = 0;

            for ($j = $productStarts; $j <= $productsEnds; $j++) {
                $product = $products[$j - $productStarts];

                $orderDetail = $order->orderDetails()->where('product_id', $product->id)->first();

                $columns[$index][$columns[0][$j]] = $orderDetail->quantity ?? 0;

                $totalPenjualan += $orderDetail->quantity ?? 0 * $product->selling_price;
            }

            $columns[$index]['TOTAL PENJUALAN'] = $totalPenjualan;
            $columns[$index]['SALES'] = $order->user->name;

            $index++;
        }

        $columns[$index] = [
            'NO' => 'GRAND TOTAL',
            'TANGGAL' => '',
            'NAMA PELANGGAN' => '',
            'NO. INVOICE' => '',
            'ALAMAT' => ''
        ];

        $totalPenjualan = 0;

        for ($j = $productStarts; $j <= $productsEnds; $j++) {
            $quantitySum = OrderDetails::where('product_id', $products[$j - $productStarts]->id)->whereHas('order', function ($query) use ($branch_id, $startDate, $endDate) {
                $query->where('branch_id', $branch_id)->whereDate('created_at', '>=', $startDate)->whereDate('created_at', '<=', $endDate);
            })->sum('quantity');

            $columns[$index][$columns[0][$j]] = $quantitySum;

            $totalPenjualan += $quantitySum * $product->selling_price;
        }

        $columns[$index]['TOTAL PENJUALAN'] = $totalPenjualan;
        $columns[$index]['SALES'] = '';

        $branch = Branch::find($branch_id);

        try {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();

            $formatedStartDate = Carbon::parse($startDate)->locale('id-ID')->translatedFormat('d F Y');
            $formatedEndDate = Carbon::parse($endDate)->locale('id-ID')->translatedFormat('d F Y');

            $workSheet->setCellValue('A1', 'LAPORAN PENJUALAN GROSIR TUNAI AREA ' . strtoupper($branch->region));

            $workSheet->setCellValue('A2', 'PERIODE ' . $formatedStartDate . ' s/d ' . $formatedEndDate);

            $workSheet->getStyle('A1:A2')->getFont()->setBold(true);

            $workSheet->getDefaultColumnDimension()->setWidth(20);
            $workSheet->fromArray($columns, null, 'A4', true);

            $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . '4')->getFont()->setBold(true);

            $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . '4')->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA07A');

            $workSheet->getStyle('A5:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setHorizontal('center');

            $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setVertical('center');

            $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setWrapText(true);

            $workSheet->getColumnDimension('A')->setWidth(5);

            $workSheet->getStyle('A4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->setColor(new Color('000000'));

            $workSheet->getStyle('B4:' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getAlignment()->setHorizontal('center');

            foreach (range('B', chr(ord('A') + count($columns[0]) - 1)) as $column) {
                $workSheet->getColumnDimension($column)->setAutoSize(true);
            }

            $workSheet->getStyle('A' . (count($columns) + 3) . ':' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getFill()->setFillType('solid')->getStartColor()->setARGB('FFA07A');

            $workSheet->getStyle('A' . (count($columns) + 3) . ':' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getFont()->setBold(true);

            $workSheet->mergeCells('A' . (count($columns) + 3) . ':' . 'E' . (count($columns) + 3));

            $workSheet->getStyle('F' . (count($columns) + 3) . ':' . chr(ord('A') + count($columns[0]) - 1) . (count($columns) + 3))->getNumberFormat()->setFormatCode('"Rp "#,##0;-"Rp "#,##0');

            $excelWritter = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');

            header('Content-Disposition: attachment;filename="LAPORAN_PENJUALAN_' . $branch->region . '_' . date('d_m_Y') . '.xls"');

            header('Cache-Control: max-age=0');
            ob_end_clean();
            $excelWritter->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    public function generateDistributionReport(Request $request)
    {
        $rules = [
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ];

        $validatedData = $request->validate($rules);

        $this->createDistributionReport($validatedData['start_date'], $validatedData['end_date']);

        return redirect()->back();
    }

    public function generateSalesReport(Request $request)
    {
        $rules = [
            'branch_id' => 'required|numeric',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ];

        $validatedData = $request->validate($rules);

        $this->createSalesReport(intval($validatedData['branch_id']), $validatedData['start_date'], $validatedData['end_date']);

        return redirect()->back();
    }
}
