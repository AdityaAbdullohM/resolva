<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Kelas; // Import Kelas model
use App\Models\MataPelajaran; // Import MataPelajaran model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;

class AdminUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $roleFilter = $request->input('role');

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('nip', 'like', '%' . $search . '%') // Search by NIP
                  ->orWhere('nis', 'like', '%' . $search . '%'); // Search by NIS
            });
        }

        if ($request->filled('role')) {
            $roleFilter = $request->input('role');
            $query->where('role', $roleFilter);
        }

        $users = $query->paginate(10);

        return view('admin.users.index', compact('users', 'search', 'roleFilter'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        $mataPelajarans = MataPelajaran::all();
        return view('admin.users.create', compact('kelas', 'mataPelajarans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
        ];

        if ($request->role === 'siswa') {
            $rules['nis'] = 'required|string|max:255|unique:users';
            // If the creator is admin the kelas field may not be present in the form,
            // so allow it to be nullable in that case. Otherwise require it.
            if (auth()->check() && auth()->user()->role === 'admin') {
                $rules['kelas_id'] = 'nullable|exists:kelas,id';
            } else {
                $rules['kelas_id'] = 'required|exists:kelas,id';
            }
        } elseif ($request->role === 'guru') {
            $rules['nip'] = 'required|string|max:255|unique:users';
            if (auth()->check() && auth()->user()->role === 'admin') {
                $rules['guru_kelas_id'] = 'nullable|exists:kelas,id';
            } else {
                $rules['guru_kelas_id'] = 'required|exists:kelas,id';
            }
            // The form no longer includes Mata Kuliah input in create view,
            // make this optional so validation won't always fail.
            $rules['guru_mata_pelajaran_id'] = 'nullable|exists:mata_pelajarans,id';
        }

        $validatedData = $request->validate($rules);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'nis' => $validatedData['nis'] ?? null,
            'nip' => $validatedData['nip'] ?? null,
            'kelas_id' => $validatedData['kelas_id'] ?? null,
        ]);

        if ($user->role === 'guru') {
            if (isset($validatedData['guru_kelas_id']) && isset($validatedData['guru_mata_pelajaran_id'])) {
                $user->kelasYangDiajar()->attach($validatedData['guru_kelas_id'], ['mata_pelajaran_id' => $validatedData['guru_mata_pelajaran_id']]);
            }
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['kelas', 'kelasYangDiajar.mataPelajaran'])->select('*', 'nip');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $kelas = Kelas::all();
        $mataPelajarans = MataPelajaran::all();
        return view('admin.users.edit', compact('user', 'kelas', 'mataPelajarans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'role' => ['required', Rule::in(['admin', 'guru', 'siswa'])],
        ];

        if ($request->role === 'siswa') {
            $rules['nis'] = ['required', 'string', 'max:255', Rule::unique('users', 'nis')->ignore($user->id)];
            // make kelas optional on update; do not force admin to re-select kelas
            $rules['kelas_id'] = 'nullable|exists:kelas,id';
        } elseif ($request->role === 'guru') {
            $rules['nip'] = ['required', 'string', 'max:255', Rule::unique('users', 'nip')->ignore($user->id)];
            // These are not required because the admin might just be editing the name/email
            $rules['guru_kelas_id'] = 'nullable|exists:kelas,id';
            // Allow mata pelajaran to be nullable so admin can add a kelas without
            // selecting a mata pelajaran on this form. The pivot record will
            // store a null mata_pelajaran_id if none is provided.
            $rules['guru_mata_pelajaran_id'] = 'nullable|exists:mata_pelajarans,id';
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:8|confirmed';
        }

        $validatedData = $request->validate($rules);

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'role' => $validatedData['role'],
        ];

        if (!empty($validatedData['password'])) {
            $userData['password'] = Hash::make($validatedData['password']);
        }

        if ($request->role === 'siswa') {
            // only update kelas_id if provided in request; otherwise keep existing
            if (array_key_exists('kelas_id', $validatedData)) {
                $userData['kelas_id'] = $validatedData['kelas_id'];
            }
            $userData['nis'] = $validatedData['nis'];
            $userData['nip'] = null;
        } elseif ($request->role === 'guru') {
            $userData['kelas_id'] = null;
            $userData['nis'] = null;
            $userData['nip'] = $validatedData['nip'];
        } else {
            $userData['kelas_id'] = null;
            $userData['nis'] = null;
            $userData['nip'] = null;
        }

        $user->update($userData);
        
        // If the user is a guru and a new class is provided, attach it.
        // Mata pelajaran is optional on this form; allow null in the pivot.
        if ($user->role === 'guru' && !empty($validatedData['guru_kelas_id'])) {
            $user->kelasYangDiajar()->syncWithoutDetaching([
                $validatedData['guru_kelas_id'] => ['mata_pelajaran_id' => $validatedData['guru_mata_pelajaran_id'] ?? null]
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Import users from uploaded Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv',
        ]);

        try {
            $path = $request->file('file')->getRealPath();

            if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                throw new \Exception('PhpSpreadsheet library not available.');
            }

            $inputFileType = IOFactory::identify($path);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) > 1) {
                // First row assumed to be headers
                $headers = [];
                $first = reset($rows);
                foreach ($first as $col => $value) {
                    $header = strtolower(trim((string) $value));
                    $header = preg_replace('/[\s\-]+/', '_', $header);
                    $header = preg_replace('/[^a-z0-9_]/', '', $header);
                    $headers[$col] = $header;
                }

                $rowIndex = 0;
                foreach ($rows as $row) {
                    $rowIndex++;
                    if ($rowIndex === 1) continue; // skip header

                    $data = [];
                    foreach ($headers as $col => $key) {
                        $data[$key] = isset($row[$col]) ? trim($row[$col]) : null;
                    }

                    $email = $data['email'] ?? null;
                    $name = $data['name'] ?? null;
                    if (empty($email) || empty($name)) {
                        continue;
                    }
                    if (User::where('email', $email)->exists()) {
                        continue;
                    }

                    $password = $data['password'] ?? 'password';
                    $nim = $data['nim'] ?? $data['nis'] ?? null;

                    $kelasId = null;
                    $kelasValue = $data['kelas'] ?? $data['kelas_id'] ?? $data['kelas_name'] ?? null;
                    if (!empty($kelasValue)) {
                        $kelasValue = trim((string) $kelasValue);
                        if (ctype_digit($kelasValue)) {
                            $kelas = Kelas::find((int) $kelasValue);
                        } else {
                            $kelas = Kelas::whereRaw('LOWER(nama) = ?', [mb_strtolower($kelasValue)])->first();
                            if (!$kelas) {
                                $kelas = Kelas::where('nama', 'like', $kelasValue)->first();
                            }
                        }
                        $kelasId = $kelas->id ?? null;
                    }

                    // Normalize role names: dosen -> guru, mahasiswa -> siswa
                    $role = $data['role'] ?? 'siswa';
                    $role = strtolower($role);
                    if ($role === 'dosen') {
                        $role = 'guru';
                    } elseif ($role === 'mahasiswa') {
                        $role = 'siswa';
                    }

                    User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make($password),
                        'role' => $role,
                        'nis' => $nim,
                        'nip' => $data['nip'] ?? null,
                        'kelas_id' => $kelasId,
                    ]);
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.users.index')->with('success', 'Import gagal: ' . $e->getMessage());
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diimpor.');
    }
}
