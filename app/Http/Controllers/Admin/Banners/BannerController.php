<?php

namespace App\Http\Controllers\Admin\Banners;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller {
    public function index() {
        $banners = Banner::all();
        return view('Admin.banner.index', compact('banners'));
    }

    public function create() {
        return view('Admin.banner.create');
    }

    public function store(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $request->file('image')->store('banners', 'public');

        Banner::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'link' => $request->link,
            'is_active' => $request->is_active ?? 0, 
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully');
    }

    public function edit(Banner $banner) {
        return view('Admin.banner.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner) {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        $data = $request->only(['title', 'description', 'link']);
    
        // Cập nhật trạng thái theo nút được bấm
        if ($request->has('is_active')) {
            $data['is_active'] = $request->is_active;
        }
    
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('banners', 'public');
        }
    
        $banner->update($data);
    
        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully');
    }

    public function destroy(Banner $banner) {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully');
    }
}