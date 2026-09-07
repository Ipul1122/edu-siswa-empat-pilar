<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionController extends Controller
{
    /**
     * Download CSV template for importing questions.
     */
    public function template(Quiz $quiz)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_soal_' . Str::slug($quiz->title) . '.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM for Microsoft Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($handle, [
                'pertanyaan',
                'pilihan_a',
                'pilihan_b',
                'pilihan_c',
                'pilihan_d',
                'pilihan_e',
                'jawaban_benar',
                'pembahasan'
            ]);

            // Sample rows
            fputcsv($handle, [
                'Apa lambang sila pertama Pancasila?',
                'Bintang Tunggal',
                'Rantai Baja',
                'Pohon Beringin',
                'Kepala Banteng',
                'Padi dan Kapas',
                'a',
                'Bintang emas perisai hitam merupakan simbol sila ke-1 Ketuhanan Yang Maha Esa.'
            ]);

            fputcsv($handle, [
                'UUD 1945 disahkan sebagai konstitusi RI pada tanggal?',
                '17 Agustus 1945',
                '18 Agustus 1945',
                '19 Agustus 1945',
                '20 Agustus 1945',
                '21 Agustus 1945',
                'b',
                'UUD 1945 disahkan pada sidang PPKI tanggal 18 Agustus 1945.'
            ]);

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Import questions from CSV file.
     */
    public function import(Request $request, Quiz $quiz)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'csv_file.required' => 'Pilih berkas CSV terlebih dahulu.',
            'csv_file.file' => 'Berkas yang diunggah tidak valid.',
            'csv_file.mimes' => 'Format berkas harus berekstensi .csv.',
            'csv_file.max' => 'Ukuran berkas maksimal 5MB.',
        ]);

        $file = $request->file('csv_file');
        $filePath = $file->getRealPath();

        $rows = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            // Detect delimiter by reading first line
            $firstLine = fgets($handle);
            rewind($handle);

            // Strip UTF-8 BOM if present
            $firstLineClean = ltrim($firstLine, "\xEF\xBB\xBF");
            $delimiter = (substr_count($firstLineClean, ';') > substr_count($firstLineClean, ',')) ? ';' : ',';

            $isHeader = true;
            $rowNumber = 0;
            $errors = [];

            while (($data = fgetcsv($handle, 10000, $delimiter)) !== false) {
                $rowNumber++;
                
                // If it's the header row, skip it
                if ($isHeader) {
                    $isHeader = false;
                    continue;
                }

                // Skip completely empty rows
                if (!array_filter($data, fn($value) => $value !== null && trim($value) !== '')) {
                    continue;
                }

                // Check minimum required columns
                if (count($data) < 7) {
                    $errors[] = "Baris {$rowNumber}: Kolom tidak lengkap (minimal 7 kolom wajib: pertanyaan, pilihan A-E, jawaban benar).";
                    continue;
                }

                $questionText = trim($data[0] ?? '');
                $optionA = trim($data[1] ?? '');
                $optionB = trim($data[2] ?? '');
                $optionC = trim($data[3] ?? '');
                $optionD = trim($data[4] ?? '');
                $optionE = trim($data[5] ?? '');
                $correctOption = strtolower(trim($data[6] ?? ''));
                $explanation = isset($data[7]) ? trim($data[7]) : null;

                // Validate fields
                if (empty($questionText)) {
                    $errors[] = "Baris {$rowNumber}: Teks pertanyaan kosong.";
                    continue;
                }
                if (empty($optionA) || empty($optionB) || empty($optionC) || empty($optionD) || empty($optionE)) {
                    $errors[] = "Baris {$rowNumber}: Semua pilihan A sampai E wajib diisi.";
                    continue;
                }
                if (!in_array($correctOption, ['a', 'b', 'c', 'd', 'e'])) {
                    $errors[] = "Baris {$rowNumber}: Jawaban benar '{$correctOption}' tidak valid (harus a, b, c, d, atau e).";
                    continue;
                }

                $rows[] = [
                    'quiz_id' => $quiz->id,
                    'question_text' => $questionText,
                    'option_a' => $optionA,
                    'option_b' => $optionB,
                    'option_c' => $optionC,
                    'option_d' => $optionD,
                    'option_e' => $optionE,
                    'correct_option' => $correctOption,
                    'explanation' => $explanation,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            fclose($handle);

            if (!empty($errors)) {
                $errorSummary = implode('<br>', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $errorSummary .= '<br>...dan ' . (count($errors) - 5) . ' kesalahan lainnya.';
                }
                return redirect()->route('admin.quizzes.show', $quiz)
                    ->with('error', 'Gagal memproses berkas CSV:<br>' . $errorSummary);
            }

            if (empty($rows)) {
                return redirect()->route('admin.quizzes.show', $quiz)
                    ->with('error', 'Tidak ada data soal valid yang ditemukan dalam berkas CSV.');
            }

            // Check duplicate question text within existing questions of this quiz
            $existingTexts = $quiz->questions()->pluck('question_text')->map(fn($t) => trim(mb_strtolower($t)))->toArray();
            $filteredRows = [];
            $duplicateCount = 0;

            foreach ($rows as $row) {
                $normalized = trim(mb_strtolower($row['question_text']));
                if (in_array($normalized, $existingTexts)) {
                    $duplicateCount++;
                    continue;
                }
                $existingTexts[] = $normalized;
                $filteredRows[] = $row;
            }

            if (empty($filteredRows)) {
                return redirect()->route('admin.quizzes.show', $quiz)
                    ->with('error', 'Semua soal dalam berkas CSV sudah ada di kuis ini (duplikat).');
            }

            DB::transaction(function () use ($filteredRows) {
                Question::insert($filteredRows);
            });

            $successMsg = 'Berhasil mengimpor ' . count($filteredRows) . ' soal ke dalam kuis!';
            if ($duplicateCount > 0) {
                $successMsg .= " ({$duplicateCount} soal dilewati karena sudah ada/duplikat).";
            }

            return redirect()->route('admin.quizzes.show', $quiz)->with('success', $successMsg);
        }

        return redirect()->route('admin.quizzes.show', $quiz)->with('error', 'Gagal membuka berkas CSV.');
    }

    /**
     * Show form to create question for a specific quiz.
     */
    public function create(Quiz $quiz)
    {
        return view('admin.questions.create', compact('quiz'));
    }

    /**
     * Store new question.
     */
    public function store(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_text' => [
                'required', 
                'string',
                \Illuminate\Validation\Rule::unique('questions', 'question_text')->where(function ($query) use ($quiz) {
                    return $query->where('quiz_id', $quiz->id);
                })
            ],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['required', 'string', 'max:255'],
            'option_d' => ['required', 'string', 'max:255'],
            'option_e' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'string', 'in:a,b,c,d,e'],
            'explanation' => ['nullable', 'string'],
        ], [
            'question_text.required' => 'Pertanyaan wajib diisi.',
            'question_text.unique' => 'Pertanyaan ini sudah ada di dalam kuis.',
            'option_a.required' => 'Pilihan A wajib diisi.',
            'option_b.required' => 'Pilihan B wajib diisi.',
            'option_c.required' => 'Pilihan C wajib diisi.',
            'option_d.required' => 'Pilihan D wajib diisi.',
            'option_e.required' => 'Pilihan E wajib diisi.',
            'correct_option.required' => 'Jawaban yang benar wajib dipilih.',
            'correct_option.in' => 'Pilihan jawaban benar tidak valid.',
        ]);

        $quiz->questions()->create($request->all());

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Soal kuis berhasil ditambahkan!');
    }

    /**
     * Show form to edit question.
     */
    public function edit(Question $question)
    {
        $question->load('quiz');
        return view('admin.questions.edit', compact('question'));
    }

    /**
     * Update question.
     */
    public function update(Request $request, Question $question)
    {
        $request->validate([
            'question_text' => [
                'required', 
                'string',
                \Illuminate\Validation\Rule::unique('questions', 'question_text')
                    ->where(function ($query) use ($question) {
                        return $query->where('quiz_id', $question->quiz_id);
                    })
                    ->ignore($question->id)
            ],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['required', 'string', 'max:255'],
            'option_d' => ['required', 'string', 'max:255'],
            'option_e' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'string', 'in:a,b,c,d,e'],
            'explanation' => ['nullable', 'string'],
        ], [
            'question_text.required' => 'Pertanyaan wajib diisi.',
            'question_text.unique' => 'Pertanyaan ini sudah ada di dalam kuis.',
            'option_a.required' => 'Pilihan A wajib diisi.',
            'option_b.required' => 'Pilihan B wajib diisi.',
            'option_c.required' => 'Pilihan C wajib diisi.',
            'option_d.required' => 'Pilihan D wajib diisi.',
            'option_e.required' => 'Pilihan E wajib diisi.',
            'correct_option.required' => 'Jawaban yang benar wajib dipilih.',
            'correct_option.in' => 'Pilihan jawaban benar tidak valid.',
        ]);

        $question->update($request->all());

        return redirect()->route('admin.quizzes.show', $question->quiz_id)
            ->with('success', 'Soal kuis berhasil diperbarui!');
    }

    /**
     * Delete question.
     */
    public function destroy(Question $question)
    {
        $quizId = $question->quiz_id;
        Question::destroy($question->id);

        return redirect()->route('admin.quizzes.show', $quizId)
            ->with('success', 'Soal kuis berhasil dihapus!');
    }
}
