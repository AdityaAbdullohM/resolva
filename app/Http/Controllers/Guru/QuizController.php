<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuizController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (!Auth::user()->hasRole('guru')) {
            abort(403, 'Unauthorized action.');
        }
        $query = Quiz::with('kelas')->where('user_id', Auth::id());

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $quizzes = $query->latest()->paginate(10)->appends($request->only('search'));

        return view('guru.kuis.index', compact('quizzes'));
    }

    /**
     * Reset (delete) all attempts for a quiz.
     */
    public function resetAttempts(Quiz $kuis)
    {
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        // Delete all attempts (and their answers) for this quiz
        $deleted = 0;
        foreach ($kuis->attempts as $attempt) {
            // delete related answers first if any
            if (method_exists($attempt, 'answers')) {
                $attempt->answers()->delete();
            }
            $attempt->delete();
            $deleted++;
        }

        return redirect()->route('dosen.kuis.index')->with('success', "Semua attempt untuk kuis direset ({$deleted}).");
    }

    /**
     * Download an image from a URL and store it to public storage. Returns storage path or null.
     */
    private function downloadImageFromUrl(?string $url, string $folder = 'quiz_questions')
    {
        if (empty($url)) return null;
        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) return null;

        try {
            $context = stream_context_create(['http' => ['timeout' => 8]]);
            $contents = @file_get_contents($url, false, $context);
            if ($contents === false) return null;

            $imgInfo = @getimagesizefromstring($contents);
            if ($imgInfo === false) return null;

            $mime = $imgInfo['mime'] ?? null;
            $ext = null;
            if ($mime) {
                $map = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                $ext = $map[$mime] ?? null;
            }
            if (!$ext) {
                // try to guess from URL
                $p = pathinfo(parse_url($url, PHP_URL_PATH) ?? '');
                $ext = isset($p['extension']) ? strtolower($p['extension']) : 'jpg';
            }

            $filename = $folder . '/' . uniqid('img_') . '.' . $ext;
            Storage::disk('public')->put($filename, $contents);
            return $filename;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!Auth::user()->hasRole('guru')) {
            abort(403, 'Unauthorized action.');
        }
        $user = Auth::user();
        if ($user->hasRole('guru')) {
            $kelas = Kelas::all();
        } else {
            $kelas = $user->kelasYangDiajar;
        }
        return view('guru.kuis.create', compact('kelas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('guru')) {
            abort(403, 'Unauthorized action.');
        }
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'required|integer|min:1',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajarans,id',
        ]);

        $quiz = Quiz::create([
            'title' => $request->title,
            'description' => $request->description,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration' => $request->duration,
            'user_id' => Auth::id(),
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id ?? null,
        ]);

        return redirect()->route('dosen.kuis.index')->with('success', 'Kuis berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Quiz $kuis)
    {
        if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id())) {
            return view('guru.kuis.show', compact('kuis'));
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quiz $kuis)
    {
        if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id())) {
            $user = Auth::user();
            if ($user->hasRole('admin')) {
                $kelas = Kelas::all();
            } else {
                $kelas = $user->kelasYangDiajar;
            }
            return view('guru.kuis.edit', compact('kuis', 'kelas'));
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Quiz $kuis)
    {
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration' => 'required|integer|min:1',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajarans,id',
        ]);

        // Ensure we persist mata_pelajaran_id as well
        $kuis->update(array_merge($validatedData, ['mata_pelajaran_id' => $request->mata_pelajaran_id ?? null]));

        return redirect()->route('dosen.kuis.index')->with('success', 'Kuis berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quiz $kuis)
    {
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $kuis->delete();

        return redirect()->route('dosen.kuis.index')->with('success', 'Kuis berhasil dihapus.');
    }

    // --- Question Management ---

    /**
     * Show the form for creating a new question for a quiz.
     */
    public function createQuestion(Quiz $kuis)
    {
        if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id())) {
            return view('guru.kuis.questions.create', compact('kuis'));
        }
        abort(403, 'Unauthorized action.');
    }

    /**
     * Store a newly created question in storage.
     */
    public function storeQuestion(Request $request, Quiz $kuis)
    {
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'pertanyaan' => 'required|string',
            'pertanyaan_java' => 'nullable|string',
            'tipe' => 'required|string|in:pilihan_ganda,benar_salah,isian',
            'opsi' => 'required_if:tipe,pilihan_ganda|array',
            'opsi.*' => 'nullable|string',
            'opsi_gambar.*' => 'nullable|image|max:2048',
            'jawaban_benar' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'nilai' => 'nullable|integer|min:0',
        ]);

        $dataToCreate = [
            'pertanyaan' => $validatedData['pertanyaan'],
            'pertanyaan_java' => $validatedData['pertanyaan_java'] ?? null,
            'tipe' => $validatedData['tipe'],
            'nilai' => isset($validatedData['nilai']) ? $validatedData['nilai'] : null,
            'user_id' => Auth::id(),
        ];

        if ($validatedData['tipe'] === 'pilihan_ganda') {
            $inputOpsi = $request->input('opsi', []);
            $letters = ['a','b','c','d','e'];
            $options = [];
            foreach ($letters as $let) {
                $text = isset($inputOpsi[$let]) ? trim($inputOpsi[$let]) : null;
                $options[] = $text;
            }

            // collect per-option java flags (checkboxes named opsi_is_java[<key>])
            $inputOpsiIsJava = $request->input('opsi_is_java', []);
            $opsiIsJavaFlags = [];
            foreach ($letters as $idx => $let) {
                $opsiIsJavaFlags[$idx] = isset($inputOpsiIsJava[$let]) && $inputOpsiIsJava[$let] ? true : false;
            }

            // Determine correct answer index. 'jawaban_benar' may be letter (a-e) or numeric index or text
            $jawaban = $request->input('jawaban_benar');
            $correctAnswerIndex = null;
            if (in_array($jawaban, $letters, true)) {
                $correctAnswerIndex = array_search($jawaban, $letters, true);
            } elseif (is_numeric($jawaban)) {
                $n = (int)$jawaban;
                if (isset($options[$n])) {
                    $correctAnswerIndex = $n;
                } elseif ($n >= 1 && isset($options[$n - 1])) {
                    // accept 1-based numbering from Excel (1..5)
                    $correctAnswerIndex = $n - 1;
                }
            } else {
                // try match by text
                $searchIndex = array_search(trim($jawaban), $options, true);
                if ($searchIndex !== false) $correctAnswerIndex = $searchIndex;
            }

            if ($correctAnswerIndex === null) {
                return back()->withErrors(['jawaban_benar' => 'Jawaban benar tidak valid untuk opsi yang diberikan.'])->withInput();
            }

            $dataToCreate['opsi_jawaban'] = $options;
            $dataToCreate['opsi_is_java'] = $opsiIsJavaFlags ?: null;
            $dataToCreate['jawaban_benar'] = $correctAnswerIndex;

            // handle opsi_gambar uploads
            $opsiGambarPaths = [];
            if ($request->hasFile('opsi_gambar')) {
                $files = $request->file('opsi_gambar');
                foreach ($letters as $idx => $let) {
                    if (isset($files[$let]) && $files[$let]) {
                        $p = $files[$let]->store('quiz_options', 'public');
                        $opsiGambarPaths[$idx] = $p;
                    }
                }
            }
            $dataToCreate['opsi_gambar'] = $opsiGambarPaths ?: null;
            // opsi_is_java already handled above
        } else {
            $dataToCreate['opsi_jawaban'] = null;
            $dataToCreate['opsi_gambar'] = null;
            $dataToCreate['jawaban_benar'] = $validatedData['jawaban_benar'];
            $dataToCreate['opsi_is_java'] = null;
        }

        // Handle uploaded image if provided
        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('quiz_questions', 'public');
            $dataToCreate['gambar'] = $path;
        }

        $kuis->questions()->create($dataToCreate);

        return redirect()->route('dosen.kuis.show', $kuis)->with('success', 'Pertanyaan berhasil ditambahkan.');
    }

    /**
     * Import questions from Excel/CSV file.
     */
    public function importQuestions(Request $request, Quiz $kuis)
    {
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,csv',
            'images_zip' => 'nullable|file|mimes:zip',
        ]);

        $extractedImagesDir = null;
        // If an images ZIP was uploaded, extract to a temp folder
        if ($request->hasFile('images_zip')) {
            $zipFile = $request->file('images_zip')->getRealPath();
            $uniq = uniqid('import_imgs_');
            $extractedImagesDir = storage_path('app/tmp/' . $uniq);
            if (!is_dir($extractedImagesDir)) {
                mkdir($extractedImagesDir, 0755, true);
            }
            $zip = new \ZipArchive();
            if ($zip->open($zipFile) === true) {
                $zip->extractTo($extractedImagesDir);
                $zip->close();
            } else {
                // extraction failed; set to null
                $extractedImagesDir = null;
            }
        }

        try {
            $path = $request->file('file')->getRealPath();

            if (!class_exists(\PhpOffice\PhpSpreadsheet\IOFactory::class)) {
                throw new \Exception('PhpSpreadsheet library not available.');
            }

            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($path);
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

            // If CSV, attempt to detect delimiter (comma or semicolon) from first line
            if (strtolower($inputFileType) === 'csv' || stripos($inputFileType, 'csv') !== false) {
                $firstBytes = @file_get_contents($path, false, null, 0, 4096);
                $detectedDelimiter = ',';
                if ($firstBytes !== false) {
                    $firstLine = strtok($firstBytes, "\n");
                    $commaCount = substr_count($firstLine, ',');
                    $semiCount = substr_count($firstLine, ';');
                    if ($semiCount > $commaCount) {
                        $detectedDelimiter = ';';
                    }
                }
                if (method_exists($reader, 'setDelimiter')) {
                    $reader->setDelimiter($detectedDelimiter);
                }
                $reader->setReadDataOnly(true);
            } else {
                $reader->setReadDataOnly(false); // Make sure to read drawings for Excel
            }

            $spreadsheet = $reader->load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            // Extract drawings if any
            $drawings = [];
            if (method_exists($sheet, 'getDrawingCollection')) {
                foreach ($sheet->getDrawingCollection() as $drawing) {
                    if ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing) {
                        $coordinates = $drawing->getCoordinates();
                        $rowNum = preg_replace('/[^0-9]/', '', $coordinates);
                        $colLetter = preg_replace('/[^A-Z]/', '', $coordinates);
                        ob_start();
                        call_user_func(
                            $drawing->getRenderingFunction(),
                            $drawing->getImageResource()
                        );
                        $imageContents = ob_get_contents();
                        ob_end_clean();
                        switch ($drawing->getMimeType()) {
                            case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_PNG :
                                $ext = 'png'; break;
                            case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_GIF:
                                $ext = 'gif'; break;
                            case \PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing::MIMETYPE_JPEG :
                                $ext = 'jpg'; break;
                            default:
                                $ext = 'png';
                        }
                        $filename = 'quiz_questions/' . uniqid('img_') . '.' . $ext;
                        \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $imageContents);
                        $drawings[$rowNum][$colLetter] = $filename;
                    } elseif ($drawing instanceof \PhpOffice\PhpSpreadsheet\Worksheet\Drawing) {
                        $coordinates = $drawing->getCoordinates();
                        $rowNum = preg_replace('/[^0-9]/', '', $coordinates);
                        $colLetter = preg_replace('/[^A-Z]/', '', $coordinates);
                        $ext = $drawing->getExtension();
                        $filename = 'quiz_questions/' . uniqid('img_') . '.' . $ext;
                        $contents = @file_get_contents($drawing->getPath());
                        if ($contents !== false) {
                            \Illuminate\Support\Facades\Storage::disk('public')->put($filename, $contents);
                            $drawings[$rowNum][$colLetter] = $filename;
                        }
                    }
                }
            }

            // Fallback: if PhpSpreadsheet parsed each row as single column (delimiter wrong),
            // try manual CSV parsing per line using detected delimiter so we map per-column headers.
            $first = reset($rows);
            $singleColumn = is_array($first) && count(array_filter($first, function($v){ return $v !== null && $v !== ''; })) === 1;
            if ($singleColumn) {
                // read file lines and parse with detected delimiter
                $content = @file_get_contents($path);
                if ($content !== false) {
                    $lines = preg_split('/\r?\n/', trim($content));
                    // detect delimiter: prefer semicolon if present in header line
                    $detected = ',';
                    if (isset($lines[0]) && substr_count($lines[0], ';') > substr_count($lines[0], ',')) {
                        $detected = ';';
                    }
                    $manualRows = [];
                    foreach ($lines as $line) {
                        if (trim($line) === '') continue;
                        $manualRows[] = str_getcsv($line, $detected);
                    }

                    // convert to associative array similar to $sheet->toArray format (A=>value)
                    $converted = [];
                    foreach ($manualRows as $r) {
                        $rowAssoc = [];
                        foreach ($r as $i => $cell) {
                            // map to letters A..Z.. (PhpSpreadsheet uses numeric keys but we used letters earlier)
                            $col = chr(65 + $i); // A, B, C ... (works up to Z)
                            $rowAssoc[$col] = $cell;
                        }
                        $converted[] = $rowAssoc;
                    }
                    if (count($converted) > 0) {
                        $rows = $converted;
                    }
                }
            }

            if (count($rows) <= 1) {
                return back()->with('error', 'File kosong atau tidak berisi baris data.');
            }

            // Expect header row with column names like: pertanyaan, tipe, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban_benar
            $first = reset($rows);
            $headers = [];
            foreach ($first as $col => $value) {
                $headers[$col] = str_replace(' ', '_', strtolower(trim($value)));
            }

            if (!in_array('pertanyaan', $headers, true) || !in_array('tipe', $headers, true)) {
                return back()->with('error', 'Format file tidak sesuai. Pastikan file memiliki kolom header "pertanyaan" dan "tipe".');
            }

            $created = 0;
            $rowIndex = 0;
            foreach ($rows as $row) {
                $rowIndex++;
                if ($rowIndex === 1) {
                    continue; // skip header
                }

                $data = [];
                foreach ($headers as $col => $key) {
                    $data[$key] = isset($row[$col]) ? trim($row[$col]) : null;
                }

                // minimal required: pertanyaan and tipe
                if (empty($data['pertanyaan']) || empty($data['tipe'])) {
                    continue;
                }

                $tipe = $data['tipe'];
                $questionData = [
                    'pertanyaan' => $data['pertanyaan'],
                    'pertanyaan_java' => $data['kode_java'] ?? $data['pertanyaan_java'] ?? null,
                    'nilai' => $data['nilai'] ?? 10,
                    'tipe' => $tipe,
                    'user_id' => Auth::id(),
                ];

                if ($tipe === 'pilihan_ganda') {
                    $letters = ['a','b','c','d','e'];
                    $options = [];
                    foreach ($letters as $let) {
                        $colKey = 'opsi_' . $let;
                        $options[] = $data[$colKey] ?? null;
                    }

                    // Debug: log raw jawaban and options to help diagnose import issues
                    try {
                        \Illuminate\Support\Facades\Log::debug('ImportQuestions: parsing pilihan_ganda row', [
                            'row' => $rowIndex,
                            'headers' => $headers,
                            'jawaban_raw' => $data['jawaban_benar'] ?? null,
                            'options' => $options,
                            'drawings' => isset($drawings[$rowIndex]) ? array_keys($drawings[$rowIndex]) : [],
                        ]);
                    } catch (\Exception $e) {
                        // ignore logging failures
                    }

                    // determine correct answer
                    $jawab = $data['jawaban_benar'] ?? null;
                    $correctIndex = null;
                    if ($jawab !== null) {
                        $jawab = trim($jawab);
                        if (in_array(strtolower($jawab), $letters, true)) {
                            $correctIndex = array_search(strtolower($jawab), $letters, true);
                        } elseif (is_numeric($jawab)) {
                            $n = (int)$jawab;
                            // Accept numeric answers as either 0-based (0..4) or 1-based (1..5).
                            // Do not require option text to be present (image-only options possible).
                            if ($n >= 0 && $n <= 4) {
                                $correctIndex = $n;
                            } elseif ($n >= 1 && $n <= 5) {
                                $correctIndex = $n - 1;
                            }
                        } else {
                            $si = array_search($jawab, $options, true);
                            if ($si !== false) $correctIndex = $si;
                        }
                    }

                    // detect per-option is_java flags from columns if present
                    $opsiIsJavaFlags = [];
                    foreach ($letters as $idx => $let) {
                        $possibleKeys = [
                            "opsi_{$let}_is_java",
                            "opsi_{$let}_isjava",
                            "{$let}_is_java",
                            "{$let}_isjava",
                            "opsi_{$let}_isjav",
                            "{$let}_isjav",
                        ];
                        $foundKey = null;
                        foreach ($possibleKeys as $pk) {
                            if (array_key_exists($pk, $data)) { $foundKey = $pk; break; }
                        }
                        if ($foundKey !== null && $data[$foundKey] !== null && $data[$foundKey] !== '') {
                            $val = strtolower(trim($data[$foundKey]));
                            $opsiIsJavaFlags[$idx] = in_array($val, ['1','true','yes','y'], true);
                        } else {
                            $opsiIsJavaFlags[$idx] = false;
                        }
                    }

                    $questionData['opsi_jawaban'] = $options;
                    $questionData['jawaban_benar'] = $correctIndex;
                    $questionData['opsi_is_java'] = $opsiIsJavaFlags ?: null;
                    // try download opsi images from columns like opsi_a_gambar / opsi_a_image / opsi_a_url
                    $opsiGambarPaths = [];
                    foreach ($letters as $idx => $let) {
                        // also consider the plain opsi_{let} header (e.g. opsi_a) because exported images
                        // are placed in the opsi columns (F-J) as drawings
                        $possibleKeys = [
                            "opsi_{$let}_gambar",
                            "opsi_{$let}_image",
                            "opsi_{$let}_url",
                            "opsi_{$let}",
                            "{$let}_gambar",
                            "{$let}_image",
                            "{$let}_url",
                        ];
                        $foundVal = null;
                        
                        $colLetterOpsiGambar = false;
                        foreach ($possibleKeys as $pk) {
                            $colLetterOpsiGambar = array_search($pk, $headers);
                            if ($colLetterOpsiGambar !== false) break;
                        }

                        if ($colLetterOpsiGambar !== false && isset($drawings[$rowIndex][$colLetterOpsiGambar])) {
                            $opsiGambarPaths[$idx] = $drawings[$rowIndex][$colLetterOpsiGambar];
                        } else {
                            foreach ($possibleKeys as $k) {
                                if (!empty($data[$k])) { $foundVal = $data[$k]; break; }
                            }
                            if ($foundVal && $foundVal !== '') {
                                $foundVal = trim($foundVal);
                                $downloaded = null;
                                // if it's a URL, download
                                if (preg_match('#^https?://#i', $foundVal)) {
                                    $downloaded = $this->downloadImageFromUrl($foundVal, 'quiz_options');
                                } else {
                                    // try to find in extracted images dir if available
                                    if ($extractedImagesDir) {
                                        $candidate = $extractedImagesDir . DIRECTORY_SEPARATOR . $foundVal;
                                        if (!file_exists($candidate)) {
                                            // try basename match
                                            $candidate = $extractedImagesDir . DIRECTORY_SEPARATOR . basename($foundVal);
                                        }
                                        if (file_exists($candidate)) {
                                            $ext = pathinfo($candidate, PATHINFO_EXTENSION) ?: 'jpg';
                                            $destName = 'quiz_options/' . uniqid('img_') . '.' . $ext;
                                            $contents = @file_get_contents($candidate);
                                            if ($contents !== false) {
                                                Storage::disk('public')->put($destName, $contents);
                                                $downloaded = $destName;
                                            }
                                        }
                                    }
                                }
                                if ($downloaded) {
                                    $opsiGambarPaths[$idx] = $downloaded;
                                }
                            }
                        }
                    }
                    $questionData['opsi_gambar'] = $opsiGambarPaths ?: null;
                } else {
                    $questionData['opsi_jawaban'] = null;
                    $questionData['jawaban_benar'] = $data['jawaban_benar'] ?? null;
                }

                // question image URL handling: columns like 'gambar', 'image', 'gambar_url', 'image_url'
                $questionImageUrl = null;
                $colLetterGambar = false;
                foreach (['gambar','image','gambar_url','image_url'] as $k) {
                    $colLetterGambar = array_search($k, $headers);
                    if ($colLetterGambar !== false) break;
                }

                if ($colLetterGambar !== false && isset($drawings[$rowIndex][$colLetterGambar])) {
                    $questionData['gambar'] = $drawings[$rowIndex][$colLetterGambar];
                } else {
                    foreach (['gambar','image','gambar_url','image_url'] as $k) {
                        if (!empty($data[$k])) { $questionImageUrl = $data[$k]; break; }
                    }
                    if ($questionImageUrl) {
                        $questionImageUrl = trim($questionImageUrl);
                        $downloaded = null;
                        if (preg_match('#^https?://#i', $questionImageUrl)) {
                            $downloaded = $this->downloadImageFromUrl($questionImageUrl, 'quiz_questions');
                        } else {
                            if ($extractedImagesDir) {
                                $candidate = $extractedImagesDir . DIRECTORY_SEPARATOR . $questionImageUrl;
                                if (!file_exists($candidate)) {
                                    $candidate = $extractedImagesDir . DIRECTORY_SEPARATOR . basename($questionImageUrl);
                                }
                                if (file_exists($candidate)) {
                                    $ext = pathinfo($candidate, PATHINFO_EXTENSION) ?: 'jpg';
                                    $destName = 'quiz_questions/' . uniqid('img_') . '.' . $ext;
                                    $contents = @file_get_contents($candidate);
                                    if ($contents !== false) {
                                        Storage::disk('public')->put($destName, $contents);
                                        $downloaded = $destName;
                                    }
                                }
                            }
                        }
                        if ($downloaded) {
                            $questionData['gambar'] = $downloaded;
                        }
                    }
                }

                $kuis->questions()->create($questionData);
                $created++;
            }

            // cleanup extracted images dir
            if ($extractedImagesDir && is_dir($extractedImagesDir)) {
                // remove files and directory
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractedImagesDir, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $fileinfo) {
                    $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
                    @$todo($fileinfo->getRealPath());
                }
                @rmdir($extractedImagesDir);
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Import gagal: ' . $e->getMessage());
        }

        if ($created === 0) {
            return back()->with('error', 'Import selesai tetapi tidak ada pertanyaan yang ditambahkan. Periksa format file dan kolom header.');
        }

        return back()->with('success', "Selesai mengimpor: {$created} pertanyaan.");
    }

    /**
     * Show the form for editing the specified question.
     */
    public function editQuestion(QuizQuestion $question)
    {
        $kuis = $question->quiz;
        if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id())) {
            return view('guru.kuis.questions.edit', compact('question'));
        }
        abort(403, 'Unauthorized action.');
    }

    /**
     * Update the specified question in storage.
     */
    public function updateQuestion(Request $request, QuizQuestion $question)
    {
        $kuis = $question->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
              'pertanyaan' => 'required|string',
              'pertanyaan_java' => 'nullable|string',
            'tipe' => 'required|string|in:pilihan_ganda,benar_salah,isian',
            'opsi' => 'required_if:tipe,pilihan_ganda|array',
            'opsi.*' => 'nullable|string',
            'opsi_gambar.*' => 'nullable|image|max:2048',
            'jawaban_benar' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
                        'nilai' => 'nullable|integer|min:0',
        ]);

        $dataToUpdate = [
            'pertanyaan' => $validatedData['pertanyaan'],
            'pertanyaan_java' => $validatedData['pertanyaan_java'] ?? null,
            'tipe' => $validatedData['tipe'],
            'nilai' => isset($validatedData['nilai']) ? $validatedData['nilai'] : null,
        ];

        if ($validatedData['tipe'] === 'pilihan_ganda') {
            $inputOpsi = $request->input('opsi', []);
            $letters = ['a','b','c','d','e'];
            $options = [];
            foreach ($letters as $let) {
                $text = isset($inputOpsi[$let]) ? trim($inputOpsi[$let]) : null;
                $options[] = $text;
            }

            $jawaban = $request->input('jawaban_benar');
            $correctAnswerIndex = null;
            if (in_array($jawaban, $letters, true)) {
                $correctAnswerIndex = array_search($jawaban, $letters, true);
            } elseif (is_numeric($jawaban)) {
                $n = (int)$jawaban;
                if (isset($options[$n])) {
                    $correctAnswerIndex = $n;
                } elseif ($n >= 1 && isset($options[$n - 1])) {
                    $correctAnswerIndex = $n - 1;
                }
            } else {
                $searchIndex = array_search(trim($jawaban), $options, true);
                if ($searchIndex !== false) $correctAnswerIndex = $searchIndex;
            }

            if ($correctAnswerIndex === null) {
                return back()->withErrors(['jawaban_benar' => 'Jawaban benar tidak valid untuk opsi yang diberikan.'])->withInput();
            }

            $dataToUpdate['opsi_jawaban'] = $options;
            $inputOpsiIsJava = $request->input('opsi_is_java', []);
            $opsiIsJavaFlags = [];
            foreach ($letters as $idx => $let) {
                $opsiIsJavaFlags[$idx] = isset($inputOpsiIsJava[$let]) && $inputOpsiIsJava[$let] ? true : false;
            }
            $dataToUpdate['opsi_is_java'] = $opsiIsJavaFlags ?: null;
            $dataToUpdate['jawaban_benar'] = $correctAnswerIndex;

            // handle opsi_gambar uploads (replace existing per index if new file uploaded)
            $opsiGambarPaths = $question->opsi_gambar ?? [];
            if ($request->hasFile('opsi_gambar')) {
                $files = $request->file('opsi_gambar');
                foreach ($letters as $idx => $let) {
                    if (isset($files[$let]) && $files[$let]) {
                        // delete old if exists
                        if (isset($opsiGambarPaths[$idx]) && Storage::disk('public')->exists($opsiGambarPaths[$idx])) {
                            Storage::disk('public')->delete($opsiGambarPaths[$idx]);
                        }
                        $p = $files[$let]->store('quiz_options', 'public');
                        $opsiGambarPaths[$idx] = $p;
                    }
                }
            }
            $dataToUpdate['opsi_gambar'] = $opsiGambarPaths ?: null;
        } else {
            $dataToUpdate['opsi_jawaban'] = null;
            $dataToUpdate['opsi_gambar'] = null;
            $dataToUpdate['jawaban_benar'] = $validatedData['jawaban_benar'];
            $dataToUpdate['opsi_is_java'] = null;
        }

        // Handle uploaded image on update
        if ($request->hasFile('gambar')) {
            // delete old image if present
            if (!empty($question->gambar) && Storage::disk('public')->exists($question->gambar)) {
                Storage::disk('public')->delete($question->gambar);
            }
            $path = $request->file('gambar')->store('quiz_questions', 'public');
            $dataToUpdate['gambar'] = $path;
        }

        $question->update($dataToUpdate);

        return redirect()->route('dosen.kuis.show', $question->quiz)->with('success', 'Pertanyaan berhasil diperbarui.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroyQuestion(QuizQuestion $question)
    {
        $quiz = $question->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $quiz->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }
        $question->delete();

        return redirect()->route('dosen.kuis.show', $quiz)->with('success', 'Pertanyaan berhasil dihapus.');
    }

    /**
     * Show recap of quiz attempts for a quiz.
     */
    public function rekap(Quiz $kuis)
    {
        if (Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id())) {
            $attempts = $kuis->attempts()->with('user')->orderByDesc('score')->get();
            return view('guru.kuis.rekap', compact('kuis', 'attempts'));
        }

        abort(403, 'Unauthorized action.');
    }

    /**
     * Export rekap attempts as CSV download.
     */
    public function exportRekap(Quiz $kuis)
    {
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }
        $attempts = $kuis->attempts()->with('user')->orderByDesc('score')->get();

        $filename = 'rekap_kuis_' . $kuis->id . '_' . now()->format('Ymd_His') . '.xlsx';

        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $spread = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spread->getActiveSheet();

            $headers = ['No', 'User ID', 'Nama', 'Score', 'Status', 'Start Time', 'End Time'];
            $sheet->fromArray($headers, null, 'A1');

            $row = 2;
            foreach ($attempts as $i => $attempt) {
                $data = [
                    $i + 1,
                    $attempt->user_id,
                    $attempt->user->name ?? 'Unknown',
                    $attempt->score,
                    $attempt->status,
                    $attempt->start_time ? $attempt->start_time->format('Y-m-d H:i:s') : '',
                    $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : '',
                ];
                $sheet->fromArray($data, null, 'A' . $row);
                $row++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spread);
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'max-age=0, no-cache, must-revalidate, proxy-revalidate',
                'Pragma' => 'public',
                'Expires' => '0',
                'Content-Transfer-Encoding' => 'binary',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return response()->streamDownload(function () use ($writer) {
                while (ob_get_level() > 0) {
                    ob_end_clean();
                }
                $writer->save('php://output');
            }, $filename, $headers);
        }

        return back()->with('error', 'Ekspor Excel memerlukan paket PhpSpreadsheet. Jalankan: composer require phpoffice/phpspreadsheet lalu coba lagi.');
    }

    /**
     * Export quiz questions to Excel (XLSX) or fallback to CSV if PhpSpreadsheet not available.
     */
    public function exportQuestions(Quiz $kuis)
    {
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $questions = $kuis->questions()->get();
        $filename = 'kuis_' . $kuis->id . '_questions_' . now()->format('Ymd_His') . '.xlsx';

        if (class_exists(\PhpOffice\PhpSpreadsheet\Spreadsheet::class)) {
            $spread = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spread->getActiveSheet();
            $headers = ['No', 'Pertanyaan', 'Tipe', 'Nilai', 'Jawaban Benar', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 'Opsi A IsJava', 'Opsi B IsJava', 'Opsi C IsJava', 'Opsi D IsJava', 'Opsi E IsJava', 'Gambar', 'Kode Java'];
            $sheet->fromArray($headers, null, 'A1');
            $sheet->getColumnDimension('P')->setWidth(20);

            $row = 2;
            foreach ($questions as $i => $q) {
                $options = is_array($q->opsi_jawaban) ? $q->opsi_jawaban : [];
                $letters = ['a','b','c','d','e'];
                $jawaban = $q->jawaban_benar;
                // export jawaban as letter when possible to make import deterministic
                $jawabanDisplay = '';
                if (is_numeric($jawaban) && isset($options[(int)$jawaban])) {
                    $jawabanDisplay = $letters[(int)$jawaban] ?? (string)$jawaban;
                } elseif (is_string($jawaban) && in_array(strtolower($jawaban), $letters, true)) {
                    $jawabanDisplay = strtolower($jawaban);
                } else {
                    // try to find by matching option text
                    $found = array_search($jawaban, $options, true);
                    if ($found !== false && isset($letters[$found])) {
                        $jawabanDisplay = $letters[$found];
                    } else {
                        $jawabanDisplay = (string)$jawaban;
                    }
                }

                $data = [
                    $i + 1,
                    strip_tags($q->pertanyaan ?? ''),
                    $q->tipe,
                    $q->nilai,
                    $jawabanDisplay,
                    $options[0] ?? null,
                    $options[1] ?? null,
                    $options[2] ?? null,
                    $options[3] ?? null,
                    $options[4] ?? null,
                    // per-option is_java flags
                    (isset($q->opsi_is_java[0]) && $q->opsi_is_java[0]) ? '1' : '',
                    (isset($q->opsi_is_java[1]) && $q->opsi_is_java[1]) ? '1' : '',
                    (isset($q->opsi_is_java[2]) && $q->opsi_is_java[2]) ? '1' : '',
                    (isset($q->opsi_is_java[3]) && $q->opsi_is_java[3]) ? '1' : '',
                    (isset($q->opsi_is_java[4]) && $q->opsi_is_java[4]) ? '1' : '',
                    '', // Placeholder for Gambar
                    $q->pertanyaan_java ?? '',
                ];

                $sheet->fromArray($data, null, 'A' . $row);

                // Question main image
                if ($q->gambar) {
                    $imagePath = storage_path('app/public/' . $q->gambar);
                    if (file_exists($imagePath)) {
                        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                        $drawing->setName('Gambar');
                        $drawing->setDescription('Gambar Pertanyaan');
                        $drawing->setPath($imagePath);
                        $drawing->setHeight(80);
                        $drawing->setCoordinates('P' . $row);
                        $drawing->setOffsetX(5);
                        $drawing->setOffsetY(5);
                        $drawing->setWorksheet($sheet);
                        $sheet->getRowDimension($row)->setRowHeight(70);
                    } else {
                        $sheet->setCellValue('P' . $row, 'Image missing');
                    }
                }

                // Option images (opsi_gambar) into columns F-J
                $optionCols = ['F','G','H','I','J'];
                $opsiGambar = is_array($q->opsi_gambar) ? $q->opsi_gambar : [];
                foreach ($optionCols as $idx => $col) {
                    if (isset($opsiGambar[$idx]) && $opsiGambar[$idx]) {
                        $optPath = storage_path('app/public/' . $opsiGambar[$idx]);
                        if (file_exists($optPath)) {
                            try {
                                $d = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                                $d->setName('Opsi ' . ($idx+1));
                                $d->setDescription('Opsi Gambar');
                                $d->setPath($optPath);
                                $d->setHeight(60);
                                $d->setCoordinates($col . $row);
                                $d->setOffsetX(3);
                                $d->setOffsetY(3);
                                $d->setWorksheet($sheet);
                                // ensure row height is enough
                                $current = $sheet->getRowDimension($row)->getRowHeight();
                                if (!$current || $current < 70) {
                                    $sheet->getRowDimension($row)->setRowHeight(70);
                                }
                            } catch (\Exception $e) {
                                // fall back to text note
                                $sheet->setCellValue($col . $row, 'Image error');
                            }
                        } else {
                            $sheet->setCellValue($col . $row, 'Image missing');
                        }
                    }
                }

                $row++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spread);
            $tempFile = tempnam(sys_get_temp_dir(), 'xlsx');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
        }

        // If PhpSpreadsheet is not installed, instruct the user to install it instead of falling back to CSV
        return back()->with('error', 'Ekspor Excel memerlukan paket PhpSpreadsheet. Jalankan: composer require phpoffice/phpspreadsheet lalu coba lagi.');
    }

    /**
     * Show edit form for a quiz attempt (guru can update score/status).
     */
    public function editAttempt(\App\Models\QuizAttempt $attempt)
    {
        $kuis = $attempt->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        return view('guru.kuis.attempts.edit', compact('attempt'));
    }

    /**
     * Update quiz attempt (score/status).
     */
    public function updateAttempt(Request $request, \App\Models\QuizAttempt $attempt)
    {
        $kuis = $attempt->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $logContext = [
            'attempt_id' => $attempt->id,
            'quiz_id' => $kuis->id,
            'user_id' => Auth::id(),
            'timestamp' => now()->toIso8601String(),
        ];

        try {
            $input = $request->all();
            $originalInput = $input; // Keep original for error display
            
            \Illuminate\Support\Facades\Log::info('QuizController::updateAttempt - Raw input received', array_merge($logContext, [
                'raw_input' => $input,
            ]));
            
            if (isset($input['score'])) {
                $input['score'] = str_replace(',', '.', trim($input['score']));
            }

            // Preprocess datetime inputs from HTML5 datetime-local format (YYYY-MM-DDTHH:mm:ss)
            // Convert to standard date format that Laravel validator accepts
            if (!empty($input['start_time']) && is_string($input['start_time'])) {
                $input['start_time'] = str_replace('T', ' ', trim($input['start_time']));
            }
            if (!empty($input['end_time']) && is_string($input['end_time'])) {
                $input['end_time'] = str_replace('T', ' ', trim($input['end_time']));
            }

            \Illuminate\Support\Facades\Log::info('QuizController::updateAttempt - After preprocessing', array_merge($logContext, [
                'preprocessed_input' => $input,
            ]));

            $validator = \Illuminate\Support\Facades\Validator::make($input, [
                'score' => 'nullable|numeric|min:0|max:100',
                'status' => 'required|in:started,finished,graded',
                'start_time' => 'nullable|regex:/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}(:\d{2})?$/',
                'end_time' => 'nullable|regex:/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}(:\d{2})?$/',
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::warning('QuizController::updateAttempt - Validation failed', array_merge($logContext, [
                    'errors' => $validator->errors()->toArray(),
                    'input' => $input,
                ]));
                return back()->withErrors($validator->errors())->withInput($originalInput);
            }

            $validated = $validator->validate();

            \Illuminate\Support\Facades\Log::info('QuizController::updateAttempt - Validation passed', array_merge($logContext, [
                'validated_data' => $validated,
            ]));

            // Prepare update data
            $updateData = [
                'score' => $validated['score'] ?? null,
                'status' => $validated['status'],
            ];

            // Handle start_time: preserve as string to prevent MySQL timestamp auto-updates
            if (!empty($validated['start_time'])) {
                $updateData['start_time'] = $validated['start_time'];
            } elseif (isset($input['start_time']) && $input['start_time'] === '') {
                $updateData['start_time'] = null;
            }

            // Handle end_time: preserve as string to prevent MySQL timestamp auto-updates
            if (!empty($validated['end_time'])) {
                $updateData['end_time'] = $validated['end_time'];
            } elseif (isset($input['end_time']) && $input['end_time'] === '') {
                $updateData['end_time'] = null;
            }

            \Illuminate\Support\Facades\Log::info('QuizController::updateAttempt - Update data prepared', array_merge($logContext, [
                'update_data' => $updateData,
            ]));

            // Save via Eloquent so model casts, timestamps, and database connection handling are applied consistently.
            if (!empty($updateData['start_time'])) {
                $updateData['start_time'] = \Illuminate\Support\Carbon::parse($updateData['start_time'])->toDateTimeString();
            }
            if (!empty($updateData['end_time'])) {
                $updateData['end_time'] = \Illuminate\Support\Carbon::parse($updateData['end_time'])->toDateTimeString();
            }

            $attempt->fill($updateData);
            $attempt->save();

            \Illuminate\Support\Facades\Log::info('QuizController::updateAttempt - Eloquent update executed', array_merge($logContext, [
                'attempt_score' => $attempt->score,
                'attempt_status' => $attempt->status,
                'attempt_start_time' => $attempt->start_time ? $attempt->start_time->toDateTimeString() : null,
                'attempt_end_time' => $attempt->end_time ? $attempt->end_time->toDateTimeString() : null,
            ]));

            \Illuminate\Support\Facades\Log::info('QuizController::updateAttempt - Record refreshed from database', array_merge($logContext, [
                'refreshed_score' => $attempt->score,
                'refreshed_status' => $attempt->status,
                'refreshed_start_time' => $attempt->start_time ? $attempt->start_time->toIso8601String() : null,
                'refreshed_end_time' => $attempt->end_time ? $attempt->end_time->toIso8601String() : null,
            ]));

            // Write an audit line to a dedicated quiz attempt update log so debug lookups work even when info logs are filtered.
            try {
                $auditLog = [
                    'timestamp' => now()->toIso8601String(),
                    'attempt_id' => $attempt->id,
                    'quiz_id' => $kuis->id,
                    'user_id' => Auth::id(),
                    'score' => $attempt->score,
                    'status' => $attempt->status,
                    'start_time' => $attempt->start_time ? $attempt->start_time->toIso8601String() : null,
                    'end_time' => $attempt->end_time ? $attempt->end_time->toIso8601String() : null,
                ];
                file_put_contents(storage_path('logs/quiz_attempt_updates.log'), json_encode($auditLog) . PHP_EOL, FILE_APPEND | LOCK_EX);
            } catch (\Throwable $auditException) {
                \Illuminate\Support\Facades\Log::warning('QuizController::updateAttempt - Failed to write audit log', array_merge($logContext, [
                    'exception_message' => $auditException->getMessage(),
                    'exception_file' => $auditException->getFile(),
                    'exception_line' => $auditException->getLine(),
                ]));
            }

            return redirect()->route('dosen.kuis.rekap', ['kuis' => $kuis->id])->with('success', 'Attempt berhasil diperbarui.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('QuizController::updateAttempt - Exception occurred', array_merge($logContext, [
                'exception_message' => $e->getMessage(),
                'exception_code' => $e->getCode(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'exception_trace' => $e->getTraceAsString(),
            ]));
            
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput($originalInput ?? []);
        }
    }

    /**
     * Show details and answers for a quiz attempt.
     */
    public function showAttempt(\App\Models\QuizAttempt $attempt)
    {
        $kuis = $attempt->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $attempt->load(['user', 'answers.quizQuestion']);

        return view('guru.kuis.attempts.show', compact('attempt'));
    }

    /**
     * Remove a quiz attempt.
     */
    public function destroyAttempt(\App\Models\QuizAttempt $attempt)
    {
        $kuis = $attempt->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        if (method_exists($attempt, 'answers')) {
            $attempt->answers()->delete();
        }
        $attempt->delete();

        return redirect()->back()->with('success', 'Attempt berhasil dihapus.');
    }

    /**
     * Show edit form for a single answer within an attempt.
     */
    public function editAnswer(\App\Models\QuizAttempt $attempt, \App\Models\QuizAnswer $answer)
    {
        $kuis = $attempt->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        if (($answer->quiz_attempt_id ?? null) != $attempt->id) {
            abort(404);
        }

        $answer->load('quizQuestion');

        return view('guru.kuis.attempts.edit_answer', compact('attempt', 'answer'));
    }

    /**
     * Update a single answer and recalculate attempt total.
     */
    public function updateAnswer(Request $request, \App\Models\QuizAttempt $attempt, \App\Models\QuizAnswer $answer)
    {
        $kuis = $attempt->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        if (($answer->quiz_attempt_id ?? null) != $attempt->id) {
            abort(404);
        }

        $data = $request->validate([
            'answer' => 'nullable',
            'answer_text' => 'nullable|string',
            'is_correct' => 'nullable|boolean',
            'points_awarded' => 'nullable|numeric|min:0',
        ]);

        // Save answer index/value for pilihan_ganda/benar_salah
        if (array_key_exists('answer', $data)) {
            $answer->answer = $data['answer'];
        }

        // Save free-text answer for isian
        if (array_key_exists('answer_text', $data)) {
            $answer->answer_text = $data['answer_text'];
        }

        if (array_key_exists('is_correct', $data)) {
            $answer->is_correct = (bool)$data['is_correct'];
        }
        if (array_key_exists('points_awarded', $data)) $answer->points_awarded = (float) $data['points_awarded'];

        // If teacher didn't explicitly set is_correct and this is a pilihan_ganda, derive it from question
        $answer->load('quizQuestion');
        if (!array_key_exists('is_correct', $data) && $answer->quizQuestion && $answer->quizQuestion->tipe === 'pilihan_ganda') {
            $derived = null;
            $correct = $answer->quizQuestion->jawaban_benar;
            if ($correct !== null && isset($answer->answer)) {
                $derived = ((string)$answer->answer === (string)$correct);
            }
            if ($derived !== null) {
                $answer->is_correct = (bool)$derived;
            }
        }

        $answer->save();

        // Recalculate total score for the attempt
        $attempt->load('answers.quizQuestion');
        $total = 0.0;
        foreach ($attempt->answers as $a) {
            $q = $a->quizQuestion;
            if ($a->points_awarded !== null) {
                $total += (float) $a->points_awarded;
            } elseif ($q && $q->tipe === 'pilihan_ganda') {
                $total += ($a->is_correct ? (float) ($q->nilai ?? 0) : 0);
            }
        }

        $oldStart = $attempt->start_time ? $attempt->start_time->format('Y-m-d H:i:s') : null;
        $oldEnd = $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : null;

        \Illuminate\Support\Facades\DB::table('quiz_attempts')->where('id', $attempt->id)->update([
            'score' => $total,
            'status' => 'graded',
            'start_time' => $oldStart,
            'end_time' => $oldEnd,
        ]);

        $attempt->refresh();

        return redirect()->route('dosen.kuis.attempts.show', $attempt)->with('success', 'Jawaban berhasil diperbarui.');
    }

    /**
     * Save manual points for answers (used for isian questions).
     */
    public function gradeAnswers(Request $request, \App\Models\QuizAttempt $attempt)
    {
        $kuis = $attempt->quiz;
        if (!(Auth::user()->hasRole('admin') || (Auth::user()->hasRole('guru') && $kuis->user_id == Auth::id()))) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'points' => 'required|array',
            'points.*' => 'nullable|numeric|min:0',
        ]);

        $points = $data['points'] ?? [];

        // ensure related question data is loaded
        $attempt->load('answers.quizQuestion');

        // First, set points for pilihan_ganda answers (fixed by question->nilai)
        foreach ($attempt->answers as $answer) {
            $q = $answer->quizQuestion;
            if ($q && $q->tipe === 'pilihan_ganda') {
                if ($answer->is_correct) {
                    $answer->points_awarded = (float) ($q->nilai ?? 0);
                } else {
                    $answer->points_awarded = 0;
                }
                $answer->save();
            }
        }

        // Then apply manual points for isian where provided
        foreach ($attempt->answers as $answer) {
            if (isset($points[$answer->id])) {
                $val = $points[$answer->id];
                if ($val === null || $val === '') {
                    $answer->points_awarded = null;
                } else {
                    $answer->points_awarded = (float) $val;
                }
                $answer->save();
            }
        }

        // Reload answers from database to get fresh points_awarded values
        $attempt->load('answers.quizQuestion');

        // Recalculate total from the freshly loaded answers collection (sum points_awarded, null -> 0)
        $total = 0.0;
        foreach ($attempt->answers as $answer) {
            $total += ($answer->points_awarded !== null) ? (float) $answer->points_awarded : 0.0;
        }

        // Persist attempt score/status while preserving start_time/end_time
        $oldStart = $attempt->start_time ? $attempt->start_time->format('Y-m-d H:i:s') : null;
        $oldEnd = $attempt->end_time ? $attempt->end_time->format('Y-m-d H:i:s') : null;

        \Illuminate\Support\Facades\DB::table('quiz_attempts')->where('id', $attempt->id)->update([
            'score' => $total,
            'status' => 'graded',
            'start_time' => $oldStart,
            'end_time' => $oldEnd,
        ]);

        // refresh attempt model
        $attempt->refresh();

        $display = (int) round($total);

        return redirect()->route('dosen.kuis.attempts.show', $attempt)->with('success', 'Nilai jawaban berhasil disimpan. Total skor: ' . $display);
    }
}