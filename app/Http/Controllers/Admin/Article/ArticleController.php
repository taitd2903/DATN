<?php

namespace App\Http\Controllers\Admin\Article;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticleController extends Controller {
    public function index()
    {
        $articles = Article::latest()->paginate(10);
        return view('Admin.articles.index', compact('articles'));
    }

    public function create()
    {
        return view('Admin.articles.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        if (!$data['slug']) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        Article::create($data);

        return redirect()->route('admin.articles.index')->with('success', 'Bài viết đã được tạo!');
    }

    public function show(Article $article)
    {
        return view('Admin.articles.show', compact('article'));
    }
   
    public function edit(Article $article)
    {
        return view('Admin.articles.edit', compact('article'));
    }

    public function update(Request $request, Article $article)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'image' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:255',
            'seo_keywords' => 'nullable|string|max:255',
        ]);

        if (!$data['slug']) {
            $data['slug'] = Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('articles', 'public');
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Bài viết đã bị xóa!');
    }



    public function showUser(Article $article)
    {
        $article->increment('views');
        return view('Users.articles.show', compact('article'));
    }
    public function indexUser()
{
    $articles = Article::where('is_active', true)
        ->orderBy('created_at', 'desc')
        ->paginate(10); 

    return view('Users.articles.index', compact('articles'));
}




}