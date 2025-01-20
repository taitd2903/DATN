<?php

namespace App\Http\Controllers\Admin\Coupons;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CouponController extends Controller
{

    public function index()
    {
        $coupons = Coupon::all();
        return view('Admin.Coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('Admin.Coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons|max:255',
            'discount' => 'required|numeric',
            'expires_at' => 'nullable|date',
        ]);
        Coupon::create($request->all());

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon đã tạo thành công!');
    }

    public function show(Coupon $coupon)
    {
        return view('Admin.Coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        return view('Admin.Coupons.edit', compact('coupon'));
        //Đạt đang cấn đoạn này:
        // if ($coupon->expires_at) {
        //     $coupon->expires_at = $coupon->expires_at->format('Y-m-d');
        // }
        //Hết đoạn Đạt cấn
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|max:255|unique:coupons,code,' . $coupon->id,
            'discount' => 'required|numeric',
            'expires_at' => 'nullable|date',
        ]);
        //Đạt đang cấn đoạn này:
    // if ($request->has('expires_at')) {
    //     $request->merge([
    //         'expires_at' => date('Y-m-d', strtotime($request->expires_at))
    //     ]);
    // }
        //Hết đoạn Đạt cấn
        $coupon->update($request->all());

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon cập nhật thành công!.');
    }

   
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon đã xoá thành công!');
    }
}
