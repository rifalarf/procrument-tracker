<?php

namespace App\Imports;

use App\Models\ProcurementItem;
use App\Models\ImportProgress;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AdvancedProcurementImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts
{
    protected $mapping;
    protected $strategy;
    protected ?int $progressId;

    public function __construct(array $mapping, string $strategy, ?int $progressId = null)
    {
        $this->mapping = $mapping;
        $this->strategy = $strategy;
        $this->progressId = $progressId;
    }

    public function collection(Collection $rows)
    {
        $successCount = 0;
        $failedCount = 0;
        $processedCount = 0;

        foreach ($rows as $row) {
            $processedCount++;
            
            // Map row data based on user mapping (DB Column Key => Excel Header Slug)
            $data = [];

            foreach ($this->mapping as $dbKey => $excelHeader) {
                if ($excelHeader) {
                    $value = $row[$excelHeader] ?? null;
                    
                    // Handle Dates
                    if (str_starts_with($dbKey, 'tanggal_') && $value) {
                         try {
                            if (is_numeric($value)) {
                                $value = Date::excelToDateTimeObject($value);
                            } else {
                                $value = \Carbon\Carbon::parse($value);
                            }
                         } catch (\Exception $e) {
                            $value = null; 
                         }
                    }
                    
                    $data[$dbKey] = $value;
                }
            }

            // Clean Nilai specifically
            if (isset($data['nilai'])) {
                $data['nilai'] = $this->cleanNilai($data['nilai']);
            }

            // Enum Conversion for Status
            if (isset($data['status'])) {
                $statusInput = trim($data['status']); 
                
                // 1. Try exact value match
                $statusEnum = \App\Enums\ProcurementStatusEnum::tryFrom($statusInput);
                
                // 2. Try UPPERCASE value match (e.g. "po" -> "PO")
                if (!$statusEnum) {
                     $statusEnum = \App\Enums\ProcurementStatusEnum::tryFrom(strtoupper($statusInput));
                }

                // 3. Try Normalized (spaces -> underscores) e.g. "Proses PO" -> "PROSES_PO"
                if (!$statusEnum) {
                    $normalized = strtoupper(str_replace(' ', '_', $statusInput));
                    $statusEnum = \App\Enums\ProcurementStatusEnum::tryFrom($normalized);
                    
                    if (!$statusEnum) {
                         if ($normalized === 'PROSES_PO') $statusEnum = \App\Enums\ProcurementStatusEnum::PO;
                    }
                }

                if ($statusEnum) {
                    $data['status'] = $statusEnum->value;
                } else {
                    $found = false;
                    foreach (\App\Enums\ProcurementStatusEnum::cases() as $case) {
                        if (strtolower(trim($case->label())) === strtolower($statusInput)) {
                            $data['status'] = $case->value;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) $data['status'] = null;
                }
            }
            
            // Enum Conversion for Buyer
            if (isset($data['buyer'])) {
                $buyerInput = trim($data['buyer']);
                
                $buyerEnum = \App\Enums\BuyerEnum::tryFrom($buyerInput);
                if (!$buyerEnum) {
                     $buyerEnum = \App\Enums\BuyerEnum::tryFrom(strtoupper($buyerInput));
                }
                
                if (!$buyerEnum) {
                     $normalizedBuyer = strtoupper(str_replace(' ', '_', $buyerInput));
                     $buyerEnum = \App\Enums\BuyerEnum::tryFrom($normalizedBuyer);
                }

                if ($buyerEnum) {
                    $data['buyer'] = $buyerEnum->value;
                } else {
                    $found = false;
                    foreach (\App\Enums\BuyerEnum::cases() as $case) {
                        if (strtolower(trim($case->label())) === strtolower($buyerInput)) {
                            $data['buyer'] = $case->value;
                            $found = true;
                            break;
                        }
                    }
                     if (!$found) $data['buyer'] = null;
                }
            }

            // Enum Conversion for Bagian
            if (isset($data['bagian'])) {
                $bagianInput = trim($data['bagian']);
                
                $bagianEnum = \App\Enums\BagianEnum::tryFrom($bagianInput);
                 if (!$bagianEnum) {
                     $bagianEnum = \App\Enums\BagianEnum::tryFrom(strtoupper($bagianInput));
                }
                
                if (!$bagianEnum) {
                     $normalizedBagian = strtoupper(str_replace(' ', '', $bagianInput));
                     $bagianEnum = \App\Enums\BagianEnum::tryFrom($normalizedBagian);
                }

                if ($bagianEnum) {
                    $data['bagian'] = $bagianEnum->value;
                } else {
                    $found = false;
                    foreach (\App\Enums\BagianEnum::cases() as $case) {
                        if (strtolower(trim($case->label())) === strtolower($bagianInput)) {
                            $data['bagian'] = $case->value;
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) $data['bagian'] = null;
                }
            }
            
            // Check for conflict (ID Dokumen/Procurement)
            $externalId = $data['no_pr'] ?? $data['id_procurement'] ?? null;
            
            if ($externalId) {
                $existing = ProcurementItem::where('no_pr', $externalId)->first();
                if ($existing) {
                    if ($this->strategy === 'update') {
                         $existing->update(array_merge($data, [
                             'last_updated_by' => auth()->user()->email ?? 'Importer',
                             'last_updated_at' => now(),
                        ]));
                        $successCount++;
                    }
                    continue; 
                }
            }

            // Skip empty row
            if (empty($data['mat_code']) && empty($data['nama_barang']) && empty($data['no_pr'])) {
                continue;
            }

            // Create new
            try {
                ProcurementItem::create(array_merge($data, [
                    'last_updated_by' => auth()->user()->email ?? 'Importer',
                    'last_updated_at' => now(),
                ]));
                $successCount++;
            } catch (\Exception $e) {
                $failedCount++;
                \Log::warning('Import failed for row: ' . $e->getMessage());
            }
        }

        // Update progress after each chunk
        $this->updateProgress($processedCount, $successCount, $failedCount);
    }

    /**
     * Update the import progress record
     */
    private function updateProgress(int $processed, int $success, int $failed): void
    {
        if (!$this->progressId) {
            return;
        }

        $progress = ImportProgress::find($this->progressId);
        if ($progress) {
            $progress->increment('processed_rows', $processed);
            $progress->increment('success_count', $success);
            $progress->increment('failed_count', $failed);
            $progress->save();
        }
    }

    private function cleanNilai($value)
    {
        if (is_null($value)) return 0;
        
        if (is_numeric($value)) {
            return $value;
        }

        if (is_string($value)) {
             $cleaned = str_replace('.', '', $value);
             $cleaned = str_replace(',', '.', $cleaned);
             
             if (is_numeric($cleaned)) {
                 return $cleaned;
             }
        }
        return 0;
    }

    public function chunkSize(): int
    {
        return 500;
    }

    public function batchSize(): int
    {
        return 100;
    }
}
