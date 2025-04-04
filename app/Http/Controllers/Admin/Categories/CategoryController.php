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

    public function create() {
       
        $categories = Category::whereNull('parent_id')->get(); 
        return view('Admin.Categories.create', compact('categories'));
    }

   
    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được thêm thành công!');
    }

    
    public function destroy($id) {
        $category = Category::findOrFail($id);
    
   
        if ($category->products()->count() > 0) {
           
            $category->products()->update(['category_id' => null]);
        }
    
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa danh mục có danh mục con!');
        }
    

        $category->delete();
    
        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được xóa thành công!');
    }
    
    public function edit($id) {
        $category = Category::findOrFail($id);
    
        if ($category->children->count() > 0) {
            // Nếu là danh mục cha, không cho đổi danh mục cha
            $categories = []; 
        } else {
            // Nếu là danh mục con, chỉ cho chọn danh mục cha (cấp 1)
            $categories = Category::where('id', '!=', $id)
                ->whereNull('parent_id') // chỉ lấy danh mục không có cha
                ->get();
        }
    
        return view('Admin.Categories.edit', compact('category', 'categories'));
    }
    
    
    public function update(Request $request, $id) {
        $category = Category::findOrFail($id);
    
        $request->validate([
            'name' => 'required|string|max:255',
        ]);
    
        
        if ($category->children->count() > 0) {
            $category->update([
                'name' => $request->name,
            ]);
        } else {
            $request->validate([
                'parent_id' => 'nullable|exists:categories,id|not_in:' . $id,
            ]);
            $category->update($request->all());
        }
    
        return redirect()->route('admin.categories.index')->with('success', 'Danh mục đã được cập nhật!');
    }
    
    public function showCategories() {
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return view('Users.categories.index', compact('categories'));
    }
    
    
}
