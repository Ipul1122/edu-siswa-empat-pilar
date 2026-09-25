<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Province;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    /**
     * Display listing of practice quizzes.
     */
    public function index()
    {
        $quizzes = Quiz::query()->where('type', '=', 'practice', 'and')->with(['province'])->withCount('questions')->latest()->get();
        return view('admin.quizzes.index', compact('quizzes'));
    }

    /**
     * Show form to create practice quiz.
     */
    public function create()
    {
        $provinces = Province::orderBy('name')->get();
        return view('admin.quizzes.create', compact('provinces'));
    }

    /**
     * Store new practice quiz.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'title' => ['required', 'string', 'max:255', 'unique:quizzes,title'],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'package_code' => ['nullable', 'string', 'max:50'],
            'province_id' => ['nullable', 'exists:provinces,id'],
        ], [
            'pillar.required' => 'Kategori / Pilar wajib dipilih.',
            'pillar.in' => 'Kategori / Pilar tidak valid.',
            'title.required' => 'Judul kuis wajib diisi.',
            'title.unique' => 'Judul kuis sudah digunakan.',
            'duration_minutes.required' => 'Durasi kuis wajib diisi.',
            'duration_minutes.integer' => 'Durasi kuis harus berupa angka.',
            'duration_minutes.min' => 'Durasi kuis minimal 1 menit.',
            'province_id.exists' => 'Provinsi yang dipilih tidak terdaftar.',
        ]);

        $data = $request->all();
        $data['type'] = 'practice';
        $data['package_code'] = $request->input('package_code') ?: 'Paket Utama';
        $data['province_id'] = $request->input('province_id') ?: null;
        $data['randomize_questions'] = $request->has('randomize_questions');
        $data['randomize_options'] = $request->has('randomize_options');
        $data['is_active'] = true;

        Quiz::create($data);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Latihan kuis berhasil dibuat!');
    }

    /**
     * Display details of a quiz including its questions.
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['questions', 'province']);
        return view('admin.quizzes.show', compact('quiz'));
    }

    /**
     * Show form to edit practice quiz.
     */
    public function edit(Quiz $quiz)
    {
        if ($quiz->type !== 'practice') {
            return redirect()->route('admin.real-materi.edit', $quiz);
        }
        $provinces = Province::orderBy('name')->get();
        return view('admin.quizzes.edit', compact('quiz', 'provinces'));
    }

    /**
     * Update practice quiz.
     */
    public function update(Request $request, Quiz $quiz)
    {
        $request->validate([
            'pillar' => ['required', 'string', 'in:pancasila,uud_1945,nkri,bhinneka_tunggal_ika,twk_kedinasan'],
            'title' => ['required', 'string', 'max:255', 'unique:quizzes,title,' . $quiz->id],
            'description' => ['nullable', 'string'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'package_code' => ['nullable', 'string', 'max:50'],
            'province_id' => ['nullable', 'exists:provinces,id'],
        ], [
            'pillar.required' => 'Kategori / Pilar wajib dipilih.',
            'pillar.in' => 'Kategori / Pilar tidak valid.',
            'title.required' => 'Judul kuis wajib diisi.',
            'title.unique' => 'Judul kuis sudah digunakan.',
            'duration_minutes.required' => 'Durasi kuis wajib diisi.',
            'duration_minutes.integer' => 'Durasi kuis harus berupa angka.',
            'duration_minutes.min' => 'Durasi kuis minimal 1 menit.',
            'province_id.exists' => 'Provinsi yang dipilih tidak terdaftar.',
        ]);

        $data = $request->all();
        $data['type'] = 'practice';
        $data['package_code'] = $request->input('package_code') ?: 'Paket Utama';
        $data['province_id'] = $request->input('province_id') ?: null;
        $data['randomize_questions'] = $request->has('randomize_questions');
        $data['randomize_options'] = $request->has('randomize_options');

        $quiz->update($data);

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Latihan kuis berhasil diperbarui!');
    }

    /**
     * Delete quiz.
     */
    public function destroy(Quiz $quiz)
    {
        $type = $quiz->type;
        Quiz::destroy($quiz->id);

        if ($type === 'real') {
            return redirect()->route('admin.real-materi.index')
                ->with('success', 'Real Materi berhasil dihapus!');
        }

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Latihan kuis berhasil dihapus!');
    }
}
