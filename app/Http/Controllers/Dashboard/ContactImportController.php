<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ContactImportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'contact_type' => 'required|in:customers,suppliers',
        ]);

        $file = $request->file('csv_file');
        $type = $request->input('contact_type');
        $content = file_get_contents($file->getRealPath());
        $lines = array_filter(array_map('trim', explode("\n", $content)));

        if (count($lines) < 2) {
            return redirect()->back()->with('error', 'CSV file is empty or has no data rows.');
        }

        $headers = array_map(fn($h) => strtolower(trim($h, '"')), str_getcsv($lines[0]));
        $success = 0;
        $failed = 0;
        $errors = [];
        $imported = [];
        $businessId = $this->currentBusinessId();

        for ($i = 1; $i < count($lines); $i++) {
            $row = str_getcsv($lines[$i]);
            $data = [];
            foreach ($headers as $idx => $header) {
                $data[$header] = isset($row[$idx]) ? trim($row[$idx], '"') : '';
            }

            if (empty($data['name'] ?? '')) {
                $failed++;
                $errors[] = ['row' => $i + 1, 'name' => $data['name'] ?? '', 'reason' => 'Name is required'];
                continue;
            }

            $recordData = [
                'name' => $data['name'] ?? '',
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'country' => $data['country'] ?? null,
                'status' => 'active',
                'created_by' => $businessId,
            ];

            try {
                if ($type === 'customers') {
                    if (!empty($data['credit_limit'])) {
                        $recordData['credit_limit'] = (float) $data['credit_limit'];
                    }
                    $recordData['notes'] = $data['notes'] ?? null;
                    $record = Customer::create($recordData);
                } else {
                    $recordData['company'] = $data['company'] ?? null;
                    $recordData['tax_number'] = $data['tax_number'] ?? null;
                    $recordData['pay_term'] = $data['pay_term'] ?? null;
                    $recordData['notes'] = $data['notes'] ?? null;
                    if (!empty($data['credit_limit'])) {
                        $recordData['credit_limit'] = (float) $data['credit_limit'];
                    }
                    $record = Supplier::create($recordData);
                }

                $success++;
                $imported[] = ['name' => $record->name, 'email' => $record->email, 'phone' => $record->phone];
            } catch (\Exception $e) {
                $failed++;
                $errors[] = ['row' => $i + 1, 'name' => $data['name'] ?? '', 'reason' => $e->getMessage()];
            }
        }

        Session::put('import_summary', [
            'type' => $type,
            'total' => count($lines) - 1,
            'success' => $success,
            'failed' => $failed,
            'imported' => $imported,
            'errors' => $errors,
        ]);

        return redirect()->route('dashboard.contacts.import-results');
    }

    public function results()
    {
        $summary = Session::get('import_summary');
        if (!$summary) {
            return redirect()->route('dashboard.contacts.import-contacts');
        }

        return view('dashboard.contacts.import-results', compact('summary'));
    }
}
