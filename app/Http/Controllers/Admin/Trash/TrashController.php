<?php

namespace App\Http\Controllers\Admin\Trash;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class TrashController extends Controller
{
    public function index()
    {
        $products = Product::where('is_delete', true)->get();
        $categories = Category::whereNull('parent_id')->with('children')->get();
        return view('Admin.trash.index', compact('products', 'categories'));
    }



    public function restore($id)
    {
        $product = Product::where('id', $id)->where('is_delete', true)->first();
        if ($product) {
          
            $product->is_delete = false;
            $product->save();
            return redirect()->route('admin.trash.index')->with('success', 'Sản phẩm đã được phục hồi!');
        }
        return redirect()->route('admin.trash.index')->with('error', 'Sản phẩm này không thể phục hồi vì không tồn tại hoặc không bị xóa!');
    }
    
    
    


    public function destroy($id)
    {

        $product = Product::findOrFail($id);
        $product->delete(); 

        return redirect()->route('Admin.trash.index')->with('success', 'Sản phẩm đã bị xóa vĩnh viễn!');
    }
}
