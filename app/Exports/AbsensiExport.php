<?php

namespace App\Exports;

use App\Models\Absensi;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class AbsensiExport implements FromView, WithTitle, WithEvents, ShouldAutoSize
{
    protected $absensi;
    protected $filters;

    public function __construct($absensi, array $filters = [])
    {
        $this->absensi = $absensi;
        $this->filters = $filters;
    }

    public function view(): View
    {
        return view('admin.absensi.export-excel', [
            'absensi' => $this->absensi,
            'filters' => $this->filters,
        ]);
    }

    public function title(): string
    {
        return 'Data Absensi';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet      = $event->sheet->getDelegate();
                $totalRows  = $this->absensi->count();
                $lastRow    = $totalRows + 6; // offset: 5 baris header + 1 header tabel

                // ── Header perusahaan (baris 1–3) ──────────────────────
                $sheet->mergeCells('A1:J1');
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A3:J3');

                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['size' => 11, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['rgb' => '94A3B8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ── Garis pemisah baris 4 ──────────────────────────────
                $sheet->mergeCells('A4:J4');

                // ── Info filter baris 5 ────────────────────────────────
                $sheet->mergeCells('A5:J5');
                $sheet->getStyle('A5')->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // ── Header tabel (baris 6) ─────────────────────────────
                $headerRow = 6;
                $sheet->getStyle("A{$headerRow}:J{$headerRow}")->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 11,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '3B82F6'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '2563EB'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(28);

                // ── Data rows styling ──────────────────────────────────
                for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
                    $isEven = ($row - $headerRow) % 2 === 0;

                    $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $isEven ? 'F8FAFC' : 'FFFFFF'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'E2E8F0'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                    $sheet->getRowDimension($row)->setRowHeight(22);
                }

                // ── Status kolom warna (kolom J = Status) ─────────────
                for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
                    $status = strtolower($sheet->getCell("J{$row}")->getValue());
                    $color  = match($status) {
                        'hadir'  => ['bg' => 'D1FAE5', 'fg' => '065F46'],
                        'izin'   => ['bg' => 'FEF3C7', 'fg' => '92400E'],
                        'sakit'  => ['bg' => 'FED7AA', 'fg' => '9A3412'],
                        'cuti'   => ['bg' => 'DBEAFE', 'fg' => '1E40AF'],
                        'alpha'  => ['bg' => 'FEE2E2', 'fg' => '991B1B'],
                        default  => ['bg' => 'F1F5F9', 'fg' => '475569'],
                    };

                    $sheet->getStyle("J{$row}")->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $color['bg']],
                        ],
                        'font'      => ['color' => ['rgb' => $color['fg']], 'bold' => true],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }

                // ── Alignment kolom angka ──────────────────────────────
                // A=No, C=Tanggal, D=Jam Masuk, E=Jam Pulang, H=Terlambat, I=Wajah
                foreach (['A', 'C', 'D', 'E', 'H', 'I'] as $col) {
                    $sheet->getStyle("{$col}" . ($headerRow + 1) . ":{$col}{$lastRow}")
                          ->getAlignment()
                          ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── Freeze header ──────────────────────────────────────
                $sheet->freezePane('A' . ($headerRow + 1));

                // ── Baris footer ───────────────────────────────────────
                $footerRow = $lastRow + 2;
                $sheet->mergeCells("A{$footerRow}:J{$footerRow}");
                $sheet->setCellValue("A{$footerRow}", 'Dicetak pada: ' . now()->format('d/m/Y H:i'));
                $sheet->getStyle("A{$footerRow}")->applyFromArray([
                    'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '94A3B8']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            },
        ];
    }
}