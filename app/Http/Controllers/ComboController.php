<?php
namespace App\Http\Controllers;

use App\Models\Combo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComboController extends Controller
{
    
    public function index()
{
    $combos = Combo::all();
    return view('Admin.combos.index', compact('combos'));
}


    public function create()
    {
        $products = DB::table('products')
        ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
        ->select(
            'products.id',
            'products.name',
            'product_variants.id as variant_id',
            'product_variants.size',
            'product_variants.color',
            'product_variants.price',
            'product_variants.image'
        )
        ->get();
        return view('admin.combos.create', compact('products'));
    }

    public function store(Request $request)
{
    $productIds = $request->input('products', []);

    // Kiểm tra xem có sản phẩm nào được chọn không
    if (empty($productIds)) {
        return back()->with('error', 'Bạn phải chọn ít nhất một sản phẩm!');
    }

    // Lấy danh sách sản phẩm đã chọn
    $products = DB::table('products')
        ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
        ->whereIn('products.id', $request->products)
        ->select('products.name', 'product_variants.price')
        ->get();



       
    // Tính tổng giá ban đầu

    $totalPrice = $products->sum('price');

    // Tính giảm giá theo số lượng sản phẩm
    $count = $request->has('products') ? count($request->products) : 0;

    $discount = 0;
    if ($count == 2) {
        $discount = 0.03; // 3% cho 2 sản phẩm
    } elseif ($count >= 3) {
        $discount = 0.05; // 5% cho 3 sản phẩm trở lên
    }

    // Tính giá cuối cùng
    $finalPrice = $totalPrice * (1 - $discount);

    // Lưu vào cơ sở dữ liệu
    Combo::create([
        'name' => $request->combo_name,
        'products' => json_encode($request->products),
        'discount_price' => $finalPrice,
    ]);

    return redirect()->route('combos.index')->with('success', 'Combo đã tạo thành công!');
}



    public function edit(Combo $combo)
    {
        $products = Product::all();
        return view('admin.combos.edit', compact('combo', 'products'));
    }

    public function update(Request $request, Combo $combo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_price' => 'required|numeric',
            'products' => 'required|array',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        $imagePath = $combo->image;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('combos', 'public');
        }
    
        $combo->update([
            'name' => $request->name,
            'discount_price' => $request->discount_price,
            'image' => $imagePath,
            'is_flash_sale' => $request->has('is_flash_sale'),
        ]);
    
        $combo->products()->sync($request->products);
    
        return redirect()->route('admin.combos.index')->with('success', 'Combo đã được cập nhật!');
    }
    public function destroy(Combo $combo)
    {
        $combo->products()->detach();
        $combo->delete();

        return redirect()->route('combos.index')->with('success', 'Combo đã được xoá!');
    }
}
