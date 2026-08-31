<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class RealQuizController extends Controller
{
    /**
     * Display listing of Real Materi quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::query()->where('type', '=', 'real')->withCount('questions')->latest()->get();
        
        $totalRealCount = $quizzes->count();
        $activeRealCount = $quizzes->where('is_active', true)->count();
        $isAllClosed = ($activeRealCount === 0 && $totalRealCount > 0);
        $isAllOpen = ($activeRealCount === $totalRealCount && $totalRealCount > 0);

        return view('admin.real_quizzes.index', compact('quizzes', 'totalRealCount', 'activeRealCount', 'isAllClosed', 'isAllOpen'));
    }

    /**
     * Show form to create Real Materi quiz.
     */
    public function create()
    {
        return view('admin.real_quizzes.create');
    }

    /**
     * Store new Real Materi quiz.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'title' => ['required', 'string', 'max:255', 'unique:quizzes,title'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'pillar.required' => 'Kategori / Pilar wajib dipilih.',
            'pillar.in' => 'Kategori / Pilar tidak valid.',
            'title.required' => 'Judul kuis wajib diisi.',
            'title.unique' => 'Judul kuis sudah digunakan.',
            'duration_minutes.required' => 'Durasi kuis wajib diisi.',
            'duration_minutes.integer' => 'Durasi kuis harus berupa angka.',
            'duration_minutes.min' => 'Durasi kuis minimal 1 menit.',
        ]);

        $data = $request->all();
        $data['type'] = 'real';
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        Quiz::create($data);

        return redirect()->route('admin.real-materi.index')
            ->with('success', 'Real Materi evaluasi berhasil dibuat!');
    }

    /**
     * Display details of a Real Materi quiz including its questions.
     */
    public function show(Quiz $real_materi)
    {
        $quiz = $real_materi;
        $quiz->load('questions');
        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Show form to edit Real Materi quiz.
     */
    public function edit(Quiz $real_materi)
    {
        $quiz = $real_materi;
        if ($quiz->type !== 'real') {
            return redirect()->route('admin.quizzes.edit', $quiz);
        }
        return view('admin.real_quizzes.edit', compact('quiz'));
    }

    /**
     * Update Real Materi quiz.
     */
    public function update(Request $request, Quiz $real_materi)
    {
        $quiz = $real_materi;
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'title' => ['required', 'string', 'max:255', 'unique:quizzes,title,' . $quiz->id],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'pillar.required' => 'Kategori / Pilar wajib dipilih.',
            'pillar.in' => 'Kategori / Pilar tidak valid.',
            'title.required' => 'Judul kuis wajib diisi.',
            'title.unique' => 'Judul kuis sudah digunakan.',
            'duration_minutes.required' => 'Durasi kuis wajib diisi.',
            'duration_minutes.integer' => 'Durasi kuis harus berupa angka.',
            'duration_minutes.min' => 'Durasi kuis minimal 1 menit.',
        ]);

        $data = $request->all();
        $data['type'] = 'real';
        $data['is_active'] = $request->boolean('is_active');
        $quiz->update($data);

        return redirect()->route('admin.real-materi.index')
            ->with('success', 'Real Materi evaluasi berhasil diperbarui!');
    }

    /**
     * Global Master Switch: Open or Close ALL Real Materi simultaneously.
     */
    public function toggleAll(Request $request)
    {
        $targetStatus = $request->boolean('status'); // true = Buka Semua, false = Tutup Semua

        Quiz::query()->where('type', 'real')->update(['is_active' => $targetStatus]);

        $statusMsg = $targetStatus 
            ? 'Seluruh paket evaluasi Real Materi BERHASIL DIBUKA untuk seluruh siswa!' 
            : 'Seluruh evaluasi Real Materi BERHASIL DITUTUP serentak! Para siswa tidak dapat mengakses atau mengerjakan Real Materi.';

        return redirect()->route('admin.real-materi.index')
            ->with('success', $statusMsg);
    }

    /**
     * Toggle active/closed status of an individual Real Materi quiz.
     */
    public function toggleStatus(Quiz $real_materi)
    {
        $real_materi->is_active = !$real_materi->is_active;
        $real_materi->save();

        $statusText = $real_materi->is_active ? 'dibuka kembali (siswa dapat mengerjakan)' : 'berhasil ditutup (siswa tidak dapat mengerjakan)';

        return back()->with('success', "Status Real Materi '{$real_materi->title}' {$statusText}!");
    }

    /**
     * Delete Real Materi quiz.
     */
    public function destroy(Quiz $real_materi)
    {
        Quiz::destroy($real_materi->id);

        return redirect()->route('admin.real-materi.index')
            ->with('success', 'Real Materi evaluasi berhasil dihapus!');
    }
}
