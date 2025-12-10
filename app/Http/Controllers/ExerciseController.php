<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExerciseController extends Controller
{
    /**
     * Display a listing of exercises.
     */
    public function index(Request $request)
    {
        $query = Exercise::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to published exercises for public API
            $query->published();
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->byDifficulty($request->difficulty);
        }

        // Search by title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_bn', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'published_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginate
        $perPage = $request->get('per_page', 10);
        $exercises = $query->paginate($perPage);

        return response()->json($exercises);
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
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => 'nullable|date',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Exercise::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Set default status if not provided
        if (empty($validated['status'])) {
            $validated['status'] = 'draft';
        }

        // Set published_at if status is published and not provided
        if (!empty($validated['status']) && $validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $exercise = Exercise::create($validated);

        return response()->json([
            'message' => 'Exercise created successfully',
            'exercise' => $exercise,
        ], 201);
    }

    /**
     * Display the specified exercise.
     */
    public function show($slug)
    {
        $exercise = Exercise::where('slug', $slug)->firstOrFail();

        // Increment views
        $exercise->incrementViews();

        return response()->json($exercise);
    }

    /**
     * Update the specified exercise.
     */
    public function update(Request $request, $slug)
    {
        $exercise = Exercise::where('slug', $slug)->firstOrFail();

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
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => 'nullable|date',
        ]);

        // Set published_at if status changes to published
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
    public function destroy($slug)
    {
        $exercise = Exercise::where('slug', $slug)->firstOrFail();
        $exercise->delete();

        return response()->json([
            'message' => 'Exercise deleted successfully',
        ]);
    }

    /**
     * Mark exercise as completed.
     */
    public function complete($slug)
    {
        $exercise = Exercise::where('slug', $slug)->firstOrFail();
        $exercise->incrementCompletions();

        return response()->json([
            'message' => 'Exercise marked as completed',
            'completions' => $exercise->completions,
        ]);
    }

    /**
     * Get all unique categories.
     */
    public function categories()
    {
        $categories = Exercise::published()
            ->select('category', 'category_bn')
            ->distinct()
            ->get();

        return response()->json($categories);
    }

    /**
     * Get popular exercises (by views).
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 5);

        $exercises = Exercise::published()
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($exercises);
    }

    /**
     * Get recent exercises.
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 5);

        $exercises = Exercise::published()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($exercises);
    }

    /**
     * Get exercises by difficulty.
     */
    public function byDifficulty(Request $request, $difficulty)
    {
        $perPage = $request->get('per_page', 10);

        $exercises = Exercise::published()
            ->byDifficulty($difficulty)
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return response()->json($exercises);
    }
}
