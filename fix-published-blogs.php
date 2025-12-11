<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Blog;

echo "=== Fixing Published Blogs Without published_at ===\n\n";

$blogs = Blog::where('status', 'published')
    ->whereNull('published_at')
    ->get();

echo "Found {$blogs->count()} published blogs without published_at date.\n\n";

foreach ($blogs as $blog) {
    $blog->published_at = $blog->created_at ?? now();
    $blog->save();
    echo "✓ Fixed Blog ID {$blog->id}: '{$blog->title}' - Set published_at to {$blog->published_at}\n";
}

echo "\n=== All Done! ===\n";
echo "Now checking published blogs...\n\n";

$published = Blog::where('status', 'published')
    ->whereNotNull('published_at')
    ->where('published_at', '<=', now())
    ->get(['id', 'title', 'published_at']);

echo "Total published blogs: {$published->count()}\n";
foreach ($published as $blog) {
    echo "ID: {$blog->id} | Title: {$blog->title} | Published: {$blog->published_at}\n";
}
