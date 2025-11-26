<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs.
     */
    public function index(Request $request)
    {
        $query = Blog::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        } else {
            // Default to published blogs for public API
            $query->published();
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
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
        $blogs = $query->paginate($perPage);

        return response()->json($blogs);
    }

    /**
     * Store a newly created blog.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_bn' => 'required|string|max:255',
            'excerpt' => 'required|string',
            'excerpt_bn' => 'required|string',
            'content' => 'required|string',
            'content_bn' => 'required|string',
            'author' => 'required|string|max:255',
            'author_bn' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'category_bn' => 'required|string|max:255',
            'tags' => 'nullable|array',
            'tags_bn' => 'nullable|array',
            'read_time' => 'nullable|string|max:50',
            'read_time_bn' => 'nullable|string|max:50',
            'image_url' => 'nullable|url',
            'slug' => 'nullable|string|unique:blogs,slug',
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => 'nullable|date',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Blog::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        // Set published_at if status is published and not provided
        if (!empty($validated['status']) && $validated['status'] === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $blog = Blog::create($validated);

        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => $blog,
        ], 201);
    }

    /**
     * Display the specified blog.
     */
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        // Increment views
        $blog->incrementViews();

        return response()->json($blog);
    }

    /**
     * Update the specified blog.
     */
    public function update(Request $request, $slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'title_bn' => 'sometimes|required|string|max:255',
            'excerpt' => 'sometimes|required|string',
            'excerpt_bn' => 'sometimes|required|string',
            'content' => 'sometimes|required|string',
            'content_bn' => 'sometimes|required|string',
            'author' => 'sometimes|required|string|max:255',
            'author_bn' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|string|max:255',
            'category_bn' => 'sometimes|required|string|max:255',
            'tags' => 'nullable|array',
            'tags_bn' => 'nullable|array',
            'read_time' => 'nullable|string|max:50',
            'read_time_bn' => 'nullable|string|max:50',
            'image_url' => 'nullable|url',
            'slug' => ['nullable', 'string', Rule::unique('blogs', 'slug')->ignore($blog->id)],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'published_at' => 'nullable|date',
        ]);

        // Set published_at if status changes to published
        if (isset($validated['status']) && $validated['status'] === 'published' && !$blog->published_at) {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return response()->json([
            'message' => 'Blog updated successfully',
            'blog' => $blog->fresh(),
        ]);
    }

    /**
     * Remove the specified blog.
     */
    public function destroy($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();
        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully',
        ]);
    }

    /**
     * Get all unique categories.
     */
    public function categories()
    {
        $categories = Blog::published()
            ->select('category', 'category_bn')
            ->distinct()
            ->get();

        return response()->json($categories);
    }

    /**
     * Get popular blogs (by views).
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 5);

        $blogs = Blog::published()
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($blogs);
    }

    /**
     * Get recent blogs.
     */
    public function recent(Request $request)
    {
        $limit = $request->get('limit', 5);

        $blogs = Blog::published()
            ->orderBy('published_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json($blogs);
    }
}
