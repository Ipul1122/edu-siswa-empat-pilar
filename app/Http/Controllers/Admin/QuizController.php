<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display listing of quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::withCount('questions')->latest()->get();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Show form to create quiz.
     */
    public function create()
    {
        return view('admin.quizzes.create');
    }

    /**
     * Store new quiz.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'pillar.required' => 'Pilar Kebangsaan wajib dipilih.',
            'pillar.in' => 'Pilar Kebangsaan tidak valid.',
            'title.required' => 'Judul kuis wajib diisi.',
            'duration_minutes.required' => 'Durasi kuis wajib diisi.',
            'duration_minutes.integer' => 'Durasi kuis harus berupa angka.',
            'duration_minutes.min' => 'Durasi kuis minimal 1 menit.',
        ]);

        Quiz::create($request->all());

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Kuis berhasil dibuat!');
    }

    /**
     * Display details of a quiz including its questions.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load('questions');
        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Show form to edit quiz.
     */
    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    /**
     * Update quiz.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
        ], [
            'pillar.required' => 'Pilar Kebangsaan wajib dipilih.',
            'pillar.in' => 'Pilar Kebangsaan tidak valid.',
            'title.required' => 'Judul kuis wajib diisi.',
            'duration_minutes.required' => 'Durasi kuis wajib diisi.',
            'duration_minutes.integer' => 'Durasi kuis harus berupa angka.',
            'duration_minutes.min' => 'Durasi kuis minimal 1 menit.',
        ]);

        $quiz->update($request->all());

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Kuis berhasil diperbarui!');
    }

    /**
     * Delete quiz.
     */
    public function destroy(Quiz $quiz)
    {
        Quiz::destroy($quiz->id);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Kuis berhasil dihapus!');
    }
}
