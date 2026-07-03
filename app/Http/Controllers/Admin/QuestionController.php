<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
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
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['required', 'string', 'max:255'],
            'option_d' => ['required', 'string', 'max:255'],
            'option_e' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'string', 'in:a,b,c,d,e'],
            'explanation' => ['nullable', 'string'],
        ], [
            'question_text.required' => 'Pertanyaan wajib diisi.',
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
            'question_text' => ['required', 'string'],
            'option_a' => ['required', 'string', 'max:255'],
            'option_b' => ['required', 'string', 'max:255'],
            'option_c' => ['required', 'string', 'max:255'],
            'option_d' => ['required', 'string', 'max:255'],
            'option_e' => ['required', 'string', 'max:255'],
            'correct_option' => ['required', 'string', 'in:a,b,c,d,e'],
            'explanation' => ['nullable', 'string'],
        ], [
            'question_text.required' => 'Pertanyaan wajib diisi.',
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
