<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['key' => 'manage_categories', 'label' => 'Danh mục'],
            ['key' => 'manage_products', 'label' => 'Sản phẩm'],
            ['key' => 'manage_discounts', 'label' => 'Mã giảm giá'],
            ['key' => 'manage_banners', 'label' => 'Quản lý Banner'],
            ['key' => 'manage_messages', 'label' => 'Tin nhắn'],
            ['key' => 'manage_reviews', 'label' => 'Quản lý đánh giá'],
            ['key' => 'manage_orders', 'label' => 'Quản lý đơn hàng'],
            ['key' => 'manage_posts', 'label' => 'Quản lý bài viết'],
            ['key' => 'manage_trash', 'label' => 'Thùng rác'],
        ];
        
        foreach ($permissions as $perm) {
            Permission::updateOrCreate(['key' => $perm['key']], ['label' => $perm['label']]);
        }
    }
}
