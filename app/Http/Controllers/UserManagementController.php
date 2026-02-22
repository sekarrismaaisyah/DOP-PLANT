<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UserManagementController extends Controller
{
    /**
     * Daftar user (master manajemen).
     */
    public function index()
    {
        $users = User::orderBy('id')->get();
        return view('user-management.index', compact('users'));
    }

    /**
     * Form tambah user.
     */
    public function create()
    {
        return view('user-management.create');
    }

    /**
     * Simpan user baru.
     * Email = text (boleh tanpa @).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|unique:users,email',
            'role' => 'nullable|string|max:50|in:user,admin,administrator',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email (kode) wajib diisi.',
            'email.unique' => 'Email (kode) sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role ?? 'user',
            'password' => Hash::make($request->password),
        ]);

        return redirect()
            ->route('user-management.index')
            ->with('success', 'User berhasil dibuat.');
    }

    /**
     * Form edit user.
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('user-management.edit', compact('user'));
    }

    /**
     * Update user.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => 'nullable|string|max:50|in:user,admin,administrator',
        ];
        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $request->validate($rules, [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email (kode) wajib diisi.',
            'email.unique' => 'Email (kode) sudah digunakan.',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role ?? 'user',
        ];
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()
            ->route('user-management.index')
            ->with('success', 'User berhasil diupdate.');
    }

    /**
     * Hapus user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()
            ->route('user-management.index')
            ->with('success', 'User berhasil dihapus.');
    }

    /**
     * Form upload Excel.
     */
    public function importForm()
    {
        return view('user-management.import');
    }

    /**
     * Download template Excel (name, email, role).
     */
    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('User');

        $headers = ['name', 'email', 'role'];
        $sheet->setCellValue('A1', 'name');
        $sheet->setCellValue('B1', 'email');
        $sheet->setCellValue('C1', 'role');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

        $examples = [
            ['SRI KASTORO', 'XUJG3', 'user'],
            ['MASRUR ABDUL AZIS', 'H5UBW', 'user'],
        ];
        $row = 2;
        foreach ($examples as $ex) {
            $sheet->setCellValue('A' . $row, $ex[0]);
            $sheet->setCellValue('B' . $row, $ex[1]);
            $sheet->setCellValue('C' . $row, $ex[2]);
            $row++;
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_user_import.xlsx';
        $path = storage_path('app/public/' . $filename);
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import user dari Excel.
     * Kolom: name, email (text), role. Password default untuk semua.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes' => 'File harus berformat xlsx, xls, atau csv.',
        ]);

        $file = $request->file('file');
        $defaultPassword = $request->input('default_password', 'password123');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();
        } catch (\Throwable $e) {
            return redirect()
                ->route('user-management.import-form')
                ->with('error', 'File tidak valid atau rusak: ' . $e->getMessage());
        }

        if (empty($rows)) {
            return redirect()
                ->route('user-management.import-form')
                ->with('error', 'File kosong atau tidak memiliki data.');
        }

        $header = array_map('trim', array_map('strtolower', (array) $rows[0]));
        $nameIdx = $this->findColumnIndex($header, ['name', 'nama']);
        $emailIdx = $this->findColumnIndex($header, ['email']);
        $roleIdx = $this->findColumnIndex($header, ['role']);

        if ($nameIdx === null || $emailIdx === null) {
            return redirect()
                ->route('user-management.import-form')
                ->with('error', 'Kolom wajib: name dan email. Pastikan header baris pertama berisi: name, email, role.');
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            $name = isset($row[$nameIdx]) ? trim((string) $row[$nameIdx]) : '';
            $email = isset($row[$emailIdx]) ? trim((string) $row[$emailIdx]) : '';
            $role = ($roleIdx !== null && isset($row[$roleIdx])) ? trim((string) $row[$roleIdx]) : 'user';
            if (!in_array($role, ['user', 'admin', 'administrator'])) {
                $role = 'user';
            }

            if ($name === '' || $email === '') {
                $errors[] = "Baris " . ($i + 1) . ": name dan email wajib diisi.";
                $skipped++;
                continue;
            }

            if (User::where('email', $email)->exists()) {
                $errors[] = "Baris " . ($i + 1) . ": email \"{$email}\" sudah ada.";
                $skipped++;
                continue;
            }

            try {
                User::create([
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'password' => Hash::make($defaultPassword),
                ]);
                $created++;
            } catch (\Throwable $e) {
                $errors[] = "Baris " . ($i + 1) . ": " . $e->getMessage();
                $skipped++;
            }
        }

        $message = "Import selesai: {$created} user dibuat.";
        if ($skipped > 0) {
            $message .= " {$skipped} baris dilewati.";
        }
        if (!empty($errors)) {
            $request->session()->flash('import_errors', $errors);
        }

        return redirect()
            ->route('user-management.index')
            ->with('success', $message);
    }

    private function findColumnIndex(array $header, array $possibleNames): ?int
    {
        foreach ($possibleNames as $name) {
            $idx = array_search($name, $header, true);
            if ($idx !== false) {
                return (int) $idx;
            }
        }
        return null;
    }
}
