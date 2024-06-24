<?php

namespace App\Http\Controllers\Dashboard;

use Exception;
use App\Models\Order;
use App\Models\Branch;
use App\Models\Restock;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ReportController extends Controller
{
    public function index()
    {
        return view('report.index');
    }

    private function createReport($title, $data)
    {
        try {
            $spreadSheet = new Spreadsheet();
            $workSheet = $spreadSheet->getActiveSheet();

            // Set title
            $workSheet->setTitle($title);

            $workSheet->getDefaultColumnDimension()->setWidth(20);
            $workSheet->fromArray($data);

            $excelWritter = new Xls($spreadSheet);
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="Products_ExportedData.xls"');
            header('Cache-Control: max-age=0');
            ob_end_clean();
            $excelWritter->save('php://output');
            exit();
        } catch (Exception $e) {
            return;
        }
    }

    private function createDistributionReport($startDate, $endDate)
    {
        $columns[] = [
            'DISTRIBUSI',
            'NAMA PRODUK',
            'STOCK AWAL',
            'PENGIRIMAN',
            'JUMLAH',
            'PENJUALAN 1 HARI',
            'SISA STOCK'
        ];

        $branches = Branch::all();

        foreach ($branches as $branch) {
            $manager = $branch->users()->where('role_id', 3)->first();

            $data[] = [];

            foreach ($branch->products as $product) {
                $sisaStock = $product->product_store;
                $penjualan1Hari = Order::where('branch_id', $branch->id)
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->orderDetails()->where('product_id', $product->id)->quantity;
                $jumlah = $sisaStock + $penjualan1Hari;
                $pengiriman = Restock::where('branch_id', $branch->id)
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
                    ->restockDetails()->where('product_id', $product->id)->quantity;
                $stockAwal = $jumlah - $pengiriman;

                array_push($data['NAMA PRODUK'], $product->product_name);
                array_push($data['STOCK AWAL'], $stockAwal);
                array_push($data['PENGIRIMAN'], $pengiriman);
                array_push($data['JUMLAH'], $jumlah);
                array_push($data['PENJUALAN 1 HARI'], $penjualan1Hari);
                array_push($data['SISA STOCK'], $sisaStock);
            }

            array_push($data['NAMA PRODUK'], '');
            array_push($data['SISA STOCK'], array_sum($data['SISA STOCK']));

            $columns[] = [
                'DISTRIBUSI' => strtoupper($branch->region) . '\n' . strtoupper($manager->name),
            ];
        }
    }
}
