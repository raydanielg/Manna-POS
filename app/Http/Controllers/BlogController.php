<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogComment;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::where('is_published', true)
            ->orderByDesc('published_at')
            ->paginate(9);

        $latest = Blog::where('is_published', true)
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        return view('blog.index', compact('blogs', 'latest'));
    }

    public function show(Blog $blog)
    {
        abort_if(! $blog->is_published, 404);

        $blog->incrementViews();

        $comments = $blog->comments()->latest()->get();

        $related = Blog::where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->where('category', $blog->category)
            ->take(3)
            ->get();

        $latest = Blog::where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->orderByDesc('published_at')
            ->take(4)
            ->get();

        return view('blog.show', compact('blog', 'comments', 'related', 'latest'));
    }

    public function storeComment(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:80',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9\+\-\s\(\)]{7,20}$/'],
            'body'  => 'required|string|min:5|max:1000',
        ]);

        $blog->comments()->create($data);

        return back()->with('comment_success', 'Asante! Maoni yako yamewasilishwa.');
    }
}
