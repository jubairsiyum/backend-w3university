<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    /**
     * Display a listing of all exercises for admin.
     */
    public function index(Request $request)
    {
        $query = Exercise::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_bn', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('problem_statement', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        // Filter by language
        if ($request->has('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $exercises = $query->paginate($request->get('per_page', 15));

        return response()->json($exercises);
    }

    /**
     * Display the specified exercise.
     */
    public function show($id)
    {
        $exercise = Exercise::findOrFail($id);
        return response()->json($exercise);
    }

    /**
     * Store a newly created exercise.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'instructions' => 'nullable|string',
            'instructions_bn' => 'nullable|string',
            'problem_statement' => 'nullable|string',
            'problem_statement_bn' => 'nullable|string',
            'input_description' => 'nullable|string',
            'input_description_bn' => 'nullable|string',
            'output_description' => 'nullable|string',
            'output_description_bn' => 'nullable|string',
            'sample_input' => 'nullable|string',
            'sample_input_bn' => 'nullable|string',
            'sample_output' => 'nullable|string',
            'sample_output_bn' => 'nullable|string',
            'difficulty' => 'required|string|max:255',
            'difficulty_bn' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'duration_bn' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:255',
            'category_bn' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags_bn' => 'nullable|array',
            'starter_code' => 'nullable|string',
            'solution_code' => 'nullable|string',
            'programming_language' => 'nullable|string|max:100',
            'language_id' => 'nullable|string|max:100',
            'language_name' => 'nullable|string|max:255',
            'language_name_bn' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'slug' => 'nullable|string|unique:exercises,slug',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => 'nullable|date',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Exercise::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        // Set published_at if status is published and not provided
        if ($validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $exercise = Exercise::create($validated);

        return response()->json([
            'message' => 'Exercise created successfully',
            'exercise' => $exercise,
        ], 201);
    }

    /**
     * Update the specified exercise.
     */
    public function update(Request $request, $id)
    {
        $exercise = Exercise::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'title_bn' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_bn' => 'nullable|string',
            'instructions' => 'nullable|string',
            'instructions_bn' => 'nullable|string',
            'problem_statement' => 'nullable|string',
            'problem_statement_bn' => 'nullable|string',
            'input_description' => 'nullable|string',
            'input_description_bn' => 'nullable|string',
            'output_description' => 'nullable|string',
            'output_description_bn' => 'nullable|string',
            'sample_input' => 'nullable|string',
            'sample_input_bn' => 'nullable|string',
            'sample_output' => 'nullable|string',
            'sample_output_bn' => 'nullable|string',
            'difficulty' => 'sometimes|required|string|max:255',
            'difficulty_bn' => 'nullable|string|max:255',
            'duration' => 'nullable|integer',
            'duration_bn' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:255',
            'category_bn' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags_bn' => 'nullable|array',
            'starter_code' => 'nullable|string',
            'solution_code' => 'nullable|string',
            'programming_language' => 'nullable|string|max:100',
            'language_id' => 'nullable|string|max:100',
            'language_name' => 'nullable|string|max:255',
            'language_name_bn' => 'nullable|string|max:255',
            'image_url' => 'nullable|url',
            'slug' => ['nullable', 'string', Rule::unique('exercises', 'slug')->ignore($exercise->id)],
            'status' => ['sometimes', 'required', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => 'nullable|date',
        ]);

        // Set published_at if status changes to published and not already set
        if (isset($validated['status']) && $validated['status'] === 'published' && !$exercise->published_at) {
            $validated['published_at'] = now();
        }

        $exercise->update($validated);

        return response()->json([
            'message' => 'Exercise updated successfully',
            'exercise' => $exercise->fresh(),
        ]);
    }

    /**
     * Remove the specified exercise.
     */
    public function destroy($id)
    {
        $exercise = Exercise::findOrFail($id);
        $exercise->delete();

        return response()->json([
            'message' => 'Exercise deleted successfully',
        ]);
    }

    /**
     * Get exercise statistics.
     */
    public function stats()
    {
        $stats = [
            'total' => Exercise::count(),
            'published' => Exercise::where('status', 'published')->count(),
            'drafts' => Exercise::where('status', 'draft')->count(),
            'archived' => Exercise::where('status', 'archived')->count(),
            'total_views' => Exercise::sum('views'),
            'total_completions' => Exercise::sum('completions'),
            'by_difficulty' => [
                'beginner' => Exercise::where('difficulty', 'Beginner')->count(),
                'intermediate' => Exercise::where('difficulty', 'Intermediate')->count(),
                'advanced' => Exercise::where('difficulty', 'Advanced')->count(),
            ],
            'by_language' => Exercise::select('language_name', \DB::raw('count(*) as count'))
                ->whereNotNull('language_name')
                ->groupBy('language_name')
                ->get(),
            'recent_exercises' => Exercise::orderBy('created_at', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'difficulty', 'views', 'created_at', 'status']),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk delete exercises.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:exercises,id',
        ]);

        Exercise::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'message' => 'Exercises deleted successfully',
        ]);
    }

    /**
     * Bulk update exercise status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:exercises,id',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        $updates = ['status' => $validated['status']];
        
        // Set published_at for published exercises
        if ($validated['status'] === 'published') {
            $updates['published_at'] = now();
        }

        Exercise::whereIn('id', $validated['ids'])->update($updates);

        return response()->json([
            'message' => 'Exercise status updated successfully',
        ]);
    }
}
