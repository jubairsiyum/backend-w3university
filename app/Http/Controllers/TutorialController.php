<?php

namespace App\Http\Controllers;

use App\Models\Tutorial;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TutorialController extends Controller
{
    /**
     * Display a listing of tutorials.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Tutorial::query();

        // Filter by language_id
        if ($request->has('language_id')) {
            $query->forLanguage($request->language_id);
        }

        // Filter by published status (default to published for public API)
        if ($request->has('is_published')) {
            $query->where('is_published', $request->boolean('is_published'));
        } else {
            $query->published();
        }

        // Search by title
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        // Order by custom order field or fallback to id
        $query->ordered();

        // Paginate or get all
        if ($request->has('per_page')) {
            $perPage = $request->get('per_page', 10);
            $tutorials = $query->paginate($perPage);
        } else {
            $tutorials = $query->get();
        }

        return response()->json($tutorials);
    }

    /**
     * Store a newly created tutorial.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'language_id' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'code_example' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $tutorial = Tutorial::create($validated);

        return response()->json([
            'message' => 'Tutorial created successfully',
            'tutorial' => $tutorial,
        ], 201);
    }

    /**
     * Display the specified tutorial.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $tutorial = Tutorial::findOrFail($id);

        return response()->json($tutorial);
    }

    /**
     * Update the specified tutorial.
     * 
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $tutorial = Tutorial::findOrFail($id);

        $validated = $request->validate([
            'language_id' => 'sometimes|required|string|max:255',
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'code_example' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        $tutorial->update($validated);

        return response()->json([
            'message' => 'Tutorial updated successfully',
            'tutorial' => $tutorial->fresh(),
        ]);
    }

    /**
     * Remove the specified tutorial.
     * 
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $tutorial = Tutorial::findOrFail($id);
        $tutorial->delete();

        return response()->json([
            'message' => 'Tutorial deleted successfully',
        ]);
    }

    /**
     * Get all unique language IDs.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function languages()
    {
        $languages = Tutorial::published()
            ->select('language_id')
            ->distinct()
            ->pluck('language_id');

        return response()->json($languages);
    }

    /**
     * Bulk delete tutorials.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:tutorials,id',
        ]);

        Tutorial::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'message' => 'Tutorials deleted successfully',
        ]);
    }

    /**
     * Bulk update published status.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:tutorials,id',
            'is_published' => 'required|boolean',
        ]);

        Tutorial::whereIn('id', $validated['ids'])
            ->update(['is_published' => $validated['is_published']]);

        return response()->json([
            'message' => 'Tutorial status updated successfully',
        ]);
    }
}
