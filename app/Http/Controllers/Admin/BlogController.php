<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    /**
     * Display a listing of all blogs for admin.
     */
    public function index(Request $request)
    {
        $query = Blog::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title_en', 'like', "%{$search}%")
                  ->orWhere('title_bn', 'like', "%{$search}%")
                  ->orWhere('content_en', 'like', "%{$search}%")
                  ->orWhere('content_bn', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'published') {
                $query->published();
            } elseif ($request->status === 'draft') {
                $query->draft();
            }
        }

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        // Filter by author
        if ($request->has('author_id')) {
            $query->where('author_id', $request->author_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $blogs = $query->paginate($request->get('per_page', 15));

        return response()->json($blogs);
    }

    /**
     * Display the specified blog.
     */
    public function show($id)
    {
        $blog = Blog::findOrFail($id);
        return response()->json($blog);
    }

    /**
     * Store a newly created blog.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_en' => 'required|string|max:255',
            'title_bn' => 'required|string|max:255',
            'content_en' => 'required|string',
            'content_bn' => 'required|string',
            'excerpt_en' => 'nullable|string|max:500',
            'excerpt_bn' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'tags' => 'nullable|array',
            'featured_image' => 'nullable|url|max:500',
            'status' => 'required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        $validated['author_id'] = $request->user()->id;
        
        // Auto-generate slug if not provided
        if (!isset($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title_en']);
        }

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $count = 1;
        while (Blog::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        $blog = Blog::create($validated);

        return response()->json([
            'message' => 'Blog created successfully',
            'blog' => $blog
        ], 201);
    }

    /**
     * Update the specified blog.
     */
    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);

        $validated = $request->validate([
            'title_en' => 'sometimes|required|string|max:255',
            'title_bn' => 'sometimes|required|string|max:255',
            'content_en' => 'sometimes|required|string',
            'content_bn' => 'sometimes|required|string',
            'excerpt_en' => 'nullable|string|max:500',
            'excerpt_bn' => 'nullable|string|max:500',
            'category' => 'sometimes|required|string|max:100',
            'tags' => 'nullable|array',
            'featured_image' => 'nullable|url|max:500',
            'status' => 'sometimes|required|in:draft,published',
            'published_at' => 'nullable|date',
        ]);

        // Update slug if title changes
        if (isset($validated['title_en']) && $validated['title_en'] !== $blog->title_en) {
            $validated['slug'] = Str::slug($validated['title_en']);
            
            // Ensure unique slug
            $originalSlug = $validated['slug'];
            $count = 1;
            while (Blog::where('slug', $validated['slug'])->where('id', '!=', $blog->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        $blog->update($validated);

        return response()->json([
            'message' => 'Blog updated successfully',
            'blog' => $blog
        ]);
    }

    /**
     * Remove the specified blog.
     */
    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        $blog->delete();

        return response()->json([
            'message' => 'Blog deleted successfully'
        ]);
    }

    /**
     * Get dashboard statistics.
     */
    public function stats()
    {
        $stats = [
            'total_blogs' => Blog::count(),
            'published_blogs' => Blog::published()->count(),
            'draft_blogs' => Blog::draft()->count(),
            'total_views' => Blog::sum('views'),
            'categories' => Blog::select('category')
                ->groupBy('category')
                ->get()
                ->pluck('category'),
            'recent_blogs' => Blog::orderBy('created_at', 'desc')->take(5)->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Bulk delete blogs.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blogs,id'
        ]);

        $deleted = Blog::whereIn('id', $validated['ids'])->delete();

        return response()->json([
            'message' => "{$deleted} blogs deleted successfully",
            'deleted_count' => $deleted
        ]);
    }

    /**
     * Bulk update blog status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blogs,id',
            'status' => 'required|in:draft,published'
        ]);

        $updated = Blog::whereIn('id', $validated['ids'])
            ->update(['status' => $validated['status']]);

        return response()->json([
            'message' => "{$updated} blogs updated successfully",
            'updated_count' => $updated
        ]);
    }
}
