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
        $quizzes = Quiz::query()->where('type', '=', 'real', 'and')->withCount('questions')->latest()->get();
        return view('admin.real_quizzes.index', compact('quizzes'));
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
        $quiz->update($data);

        return redirect()->route('admin.real-materi.index')
            ->with('success', 'Real Materi evaluasi berhasil diperbarui!');
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
