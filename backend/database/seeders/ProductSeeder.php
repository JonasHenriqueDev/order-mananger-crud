<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Notebook Dell Inspiron 15',
                'slug' => 'notebook-dell-inspiron-15',
                'sku' => 'DELL-INSP-15-001',
                'description' => 'Notebook Dell Inspiron 15 com processador Intel Core i7, 16GB RAM, SSD 512GB',
                'price' => 4599.90,
                'stock' => 25,
                'status' => 'active',
                'is_featured' => true,
                'metadata' => json_encode([
                    'brand' => 'Dell',
                    'category' => 'Eletrônicos',
                    'warranty' => '12 meses',
                ]),
            ],
            [
                'name' => 'Mouse Logitech MX Master 3',
                'slug' => 'mouse-logitech-mx-master-3',
                'sku' => 'LOG-MX-M3-001',
                'description' => 'Mouse wireless ergonômico de alta precisão',
                'price' => 549.90,
                'stock' => 50,
                'status' => 'active',
                'is_featured' => true,
                'metadata' => json_encode([
                    'brand' => 'Logitech',
                    'category' => 'Periféricos',
                    'warranty' => '12 meses',
                ]),
            ],
            [
                'name' => 'Teclado Mecânico HyperX Alloy',
                'slug' => 'teclado-mecanico-hyperx-alloy',
                'sku' => 'HX-ALLOY-001',
                'description' => 'Teclado mecânico RGB com switches Cherry MX Red',
                'price' => 699.90,
                'stock' => 30,
                'status' => 'active',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'HyperX',
                    'category' => 'Periféricos',
                    'warranty' => '24 meses',
                ]),
            ],
            [
                'name' => 'Monitor LG UltraWide 29"',
                'slug' => 'monitor-lg-ultrawide-29',
                'sku' => 'LG-UW-29-001',
                'description' => 'Monitor LG UltraWide 29 polegadas Full HD IPS',
                'price' => 1299.90,
                'stock' => 15,
                'status' => 'active',
                'is_featured' => true,
                'metadata' => json_encode([
                    'brand' => 'LG',
                    'category' => 'Monitores',
                    'warranty' => '12 meses',
                ]),
            ],
            [
                'name' => 'Webcam Logitech C920 HD Pro',
                'slug' => 'webcam-logitech-c920-hd-pro',
                'sku' => 'LOG-C920-001',
                'description' => 'Webcam Full HD 1080p com microfone stereo',
                'price' => 449.90,
                'stock' => 40,
                'status' => 'active',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'Logitech',
                    'category' => 'Periféricos',
                    'warranty' => '12 meses',
                ]),
            ],
            [
                'name' => 'Headset HyperX Cloud II',
                'slug' => 'headset-hyperx-cloud-ii',
                'sku' => 'HX-CLOUD-II-001',
                'description' => 'Headset gamer com som surround 7.1 e microfone removível',
                'price' => 599.90,
                'stock' => 35,
                'status' => 'active',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'HyperX',
                    'category' => 'Áudio',
                    'warranty' => '24 meses',
                ]),
            ],
            [
                'name' => 'SSD Kingston A400 480GB',
                'slug' => 'ssd-kingston-a400-480gb',
                'sku' => 'KING-A400-480',
                'description' => 'SSD SATA 2.5" de 480GB para upgrade de desempenho',
                'price' => 299.90,
                'stock' => 60,
                'status' => 'active',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'Kingston',
                    'category' => 'Armazenamento',
                    'warranty' => '36 meses',
                ]),
            ],
            [
                'name' => 'Memória RAM Corsair Vengeance 16GB',
                'slug' => 'memoria-ram-corsair-vengeance-16gb',
                'sku' => 'CORS-VENG-16GB',
                'description' => 'Memória DDR4 3200MHz 16GB (2x8GB)',
                'price' => 399.90,
                'stock' => 45,
                'status' => 'active',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'Corsair',
                    'category' => 'Hardware',
                    'warranty' => '60 meses',
                ]),
            ],
            [
                'name' => 'Cadeira Gamer DT3Sports Elise',
                'slug' => 'cadeira-gamer-dt3sports-elise',
                'sku' => 'DT3-ELISE-001',
                'description' => 'Cadeira gamer ergonômica com ajuste de altura e apoio lombar',
                'price' => 1199.90,
                'stock' => 20,
                'status' => 'active',
                'is_featured' => true,
                'metadata' => json_encode([
                    'brand' => 'DT3Sports',
                    'category' => 'Mobiliário',
                    'warranty' => '24 meses',
                ]),
            ],
            [
                'name' => 'Hub USB-C 7 em 1',
                'slug' => 'hub-usb-c-7-em-1',
                'sku' => 'HUB-USBC-7IN1',
                'description' => 'Hub USB-C com HDMI, USB 3.0, SD/TF, e carregamento PD',
                'price' => 199.90,
                'stock' => 55,
                'status' => 'active',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'Generic',
                    'category' => 'Acessórios',
                    'warranty' => '12 meses',
                ]),
            ],
            [
                'name' => 'Smartphone Samsung Galaxy A54',
                'slug' => 'smartphone-samsung-galaxy-a54',
                'sku' => 'SAMS-A54-001',
                'description' => 'Smartphone Samsung Galaxy A54 128GB 5G',
                'price' => 2199.90,
                'stock' => 0,
                'status' => 'active',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'Samsung',
                    'category' => 'Smartphones',
                    'warranty' => '12 meses',
                ]),
            ],
            [
                'name' => 'Tablet iPad Air 10.9"',
                'slug' => 'tablet-ipad-air-109',
                'sku' => 'APPLE-IPAD-AIR',
                'description' => 'iPad Air com tela Liquid Retina de 10.9 polegadas 64GB',
                'price' => 4999.90,
                'stock' => 10,
                'status' => 'inactive',
                'is_featured' => false,
                'metadata' => json_encode([
                    'brand' => 'Apple',
                    'category' => 'Tablets',
                    'warranty' => '12 meses',
                ]),
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        Product::factory()->count(20)->create();

        $this->command->info('Products created successfully!');
    }
}

