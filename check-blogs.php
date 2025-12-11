<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;

echo "=== Blog Database Check ===\n\n";
echo "Total blogs: " . Blog::count() . "\n";
echo "Published blogs: " . Blog::where('status', 'published')->count() . "\n";
echo "Draft blogs: " . Blog::where('status', 'draft')->count() . "\n\n";

echo "=== All Blogs ===\n";
$blogs = Blog::all(['id', 'title', 'status', 'published_at']);
foreach ($blogs as $blog) {
    echo "ID: {$blog->id} | Title: {$blog->title} | Status: {$blog->status} | Published: " . ($blog->published_at ?? 'NULL') . "\n";
}

echo "\n=== Published Blogs (with published_at check) ===\n";
$published = Blog::where('status', 'published')
    ->whereNotNull('published_at')
    ->where('published_at', '<=', now())
    ->get(['id', 'title', 'published_at']);

if ($published->isEmpty()) {
    echo "No published blogs found with valid published_at date.\n";
} else {
    foreach ($published as $blog) {
        echo "ID: {$blog->id} | Title: {$blog->title} | Published: {$blog->published_at}\n";
    }
}
