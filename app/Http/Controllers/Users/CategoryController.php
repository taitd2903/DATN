<?php
namespace App\Http\Controllers\Admin\Categories;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller {
    public function index() {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return view('Admin.Categories.index', compact('categories'));
    }

  
    public function showCategories() {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return view('Users.categories.index', compact('categories'));
    }
    
    
}
