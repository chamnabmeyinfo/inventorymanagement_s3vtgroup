<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTruckScale();
        $this->seedWeighbridge();
        $this->seedDigitalScale();
        $this->seedStorageRacking();
        $this->seedLifting();
        $this->seedForkliftMaterialHandling();
        $this->seedParkingSystem();
        $this->seedStoragePallets();
        $this->seedStorageBaskets();
        $this->seedDockMaterialLift();
    }

    private function createProduct(string $categorySlug, array $data): void
    {
        $category = Category::where('slug', $categorySlug)->first();
        if (! $category) {
            return;
        }

        $slug = Str::slug($data['name']);
        $product = Product::updateOrCreate(
            ['sku' => $data['sku']],
            [
                'name' => $data['name'],
                'slug' => $slug,
                'category_id' => $category->id,
                'description' => $data['description'] ?? null,
                'specifications' => $data['specifications'] ?? null,
                'price_display_type' => $data['price_display_type'] ?? 'on_request',
                'price_amount' => $data['price_amount'] ?? null,
                'related_product_ids' => $data['related_product_ids'] ?? null,
            ]
        );

        Stock::updateOrCreate(
            ['product_id' => $product->id],
            ['quantity' => 0, 'status' => 'on_order']
        );
    }

    private function seedTruckScale(): void
    {
        $products = [
            [
                'sku' => 'TS-001',
                'name' => 'Truck Scale',
                'description' => 'Heavy-duty vehicle weighing platform for commercial trucks. Full-length truck scales range 60–120 feet with capacity ratings up to 280,000 lbs (130 tons). Uses precision load cells and digital instrumentation for accurate weight measurement. Pit-type or surface-mounted configurations available. NIST Handbook 44 Class III/III-L compliant. Applications: regulatory weigh stations, logistics, freight terminals.',
                'specifications' => [
                    'capacity' => '20–200 tons',
                    'platform_length' => '60–120 feet',
                    'platform_width' => '10–12 feet',
                    'accuracy_class' => 'Class III / III-L',
                    'division_value' => '20–50 kg',
                    'deck_type' => 'Steel or concrete',
                ],
            ],
            [
                'sku' => 'TS-002',
                'name' => 'Concrete Truck Scale',
                'description' => 'Permanent truck scale with reinforced concrete deck. 10–14 inch thick concrete surface on steel framework. Superior longevity (20–30 years), excellent traction wet or dry, vibration damping. Ideal for high-volume installations: batching plants, waste management, mining, agriculture. Digital load cell technology for active compensation.',
                'specifications' => [
                    'capacity' => '120,000–200,000 lbs',
                    'deck_thickness' => '10–14 inches',
                    'scale_width' => '9\'10" – 12\'',
                    'scale_length' => '10–140 feet',
                    'division_value' => '20 lbs minimum',
                    'material' => 'Reinforced concrete + steel',
                ],
            ],
            [
                'sku' => 'TS-003',
                'name' => 'Vehicle Weighing Scale',
                'description' => 'Versatile vehicle scale for light to medium commercial vehicles. Suitable for axle weighing, gross weight determination, and fleet management. Compact configurations (6m+) for single-axle or short trucks. Portable and surface-mount options available.',
                'specifications' => [
                    'capacity' => '20–80 tons',
                    'platform_length' => '6–24 meters',
                    'platform_width' => '3–3.2 meters',
                    'installation' => 'Surface or pit-mounted',
                    'applications' => 'Fleet, logistics, agriculture',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('truck-scale', $p);
        }
    }

    private function seedWeighbridge(): void
    {
        $products = [
            [
                'sku' => 'WB-001',
                'name' => 'Weighbridge',
                'description' => 'Industrial weighbridge for heavy vehicle weighing. Fixed installation at quarry, mining, logistics, waste, or agricultural sites. Modular design with steel or concrete deck. Multiple load cells for accurate gross weight. Legal-for-trade certification available.',
                'specifications' => [
                    'capacity' => '60–200 tons',
                    'platform_length' => '12–24 meters',
                    'platform_width' => '3 meters',
                    'deck_type' => 'Steel or concrete',
                    'accuracy' => '±0.5% at 20%+ load',
                ],
            ],
            [
                'sku' => 'WB-002',
                'name' => 'Mobile Weighbridge',
                'description' => 'Portable weighbridge for temporary sites. Bolt-together modules enable customization from 10-foot base to 100+ feet. Quick installation (2–3 hours, 3 personnel, forklift). No excavation required. Ideal for construction, seasonal agricultural, mining exploration.',
                'specifications' => [
                    'capacity' => '20–60 tons',
                    'installation_time' => '2–3 hours',
                    'construction' => 'Bolt-together modular',
                    'portability' => 'Relocatable',
                    'foundation' => 'Minimal - surface mount',
                ],
            ],
            [
                'sku' => 'WB-003',
                'name' => 'Industrial Weighing System',
                'description' => 'Complete industrial weighing solution: weighbridge plus indicator, ticket printer, and software. Integrated with ERP, batch control, or logistics systems. Automated ticket processing, fraud prevention, remote monitoring. Suitable for batching plants, logistics terminals, commodity trading.',
                'specifications' => [
                    'components' => 'Scale, indicator, software',
                    'integration' => 'ERP, batch control, API',
                    'automation' => 'Ticket printing, data capture',
                    'certification' => 'Legal-for-trade (NTEP)',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('weighbridge', $p);
        }
    }

    private function seedDigitalScale(): void
    {
        $products = [
            [
                'sku' => 'DS-001',
                'name' => 'Digital Scale',
                'description' => 'General-purpose digital weighing scale for industrial and commercial use. LCD display, tare, unit switching. Suitable for retail, laboratory, and light industrial applications.',
                'specifications' => [
                    'capacity' => '5–100 kg (varies by model)',
                    'readability' => '0.1g–10g',
                    'display' => 'LCD',
                    'power' => 'AC/battery',
                ],
            ],
            [
                'sku' => 'DS-002',
                'name' => 'Counting Scale',
                'description' => 'Industrial parts counting scale. Count by weight using known sample. Backlit display, numeric keypad, tare, RS-232/USB. Parts counting and check counting. Capacities 75–300 kg. 5-year warranty typical.',
                'specifications' => [
                    'capacity' => '75–300 kg (165–660 lb)',
                    'readability' => '0.005–0.02 kg',
                    'features' => 'Parts counting, check counting',
                    'connectivity' => 'RS-232, USB',
                    'display' => 'Backlit',
                ],
            ],
            [
                'sku' => 'DS-003',
                'name' => 'Waterproof Scale',
                'description' => 'IP67/IP68-rated waterproof scale for washdown environments. Stainless steel construction. Food production, processing, breweries. Sealed keypads, dual LED displays, checkweighing LEDs.',
                'specifications' => [
                    'protection' => 'IP67 or IP68',
                    'capacity' => '4–32 kg',
                    'readability' => '0.5–5g',
                    'construction' => 'Stainless steel',
                    'temperature' => '0°C to 40°C',
                ],
            ],
            [
                'sku' => 'DS-004',
                'name' => 'Floor Scale',
                'description' => 'Heavy-duty floor scale for pallet and palletized loads. Low profile, easy wheeled access. Capacities from 500 kg to 3+ tons. Industrial warehousing, shipping, receiving.',
                'specifications' => [
                    'capacity' => '500 kg – 3,000 kg',
                    'platform_size' => '1000x1000mm – 1200x1500mm',
                    'height' => 'Low profile (50–80mm)',
                    'applications' => 'Warehouse, shipping, receiving',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('digital-scale', $p);
        }
    }

    private function seedStorageRacking(): void
    {
        $products = [
            [
                'sku' => 'SR-001',
                'name' => 'Medium Rack',
                'description' => 'Medium-duty shelving for hand-loaded storage. Load capacity 200–800 kg per shelf. Modular design, 30mm height increments. Retail backrooms, workshops, small warehouses. Galvanized steel, bolt-less assembly.',
                'specifications' => [
                    'capacity_per_shelf' => '200–800 kg',
                    'frame_dimensions' => '1500x600x1800 mm',
                    'upright_material' => '1.8mm galvanized steel',
                    'height_increment' => '30 mm',
                    'application' => 'Hand-loaded',
                ],
            ],
            [
                'sku' => 'SR-002',
                'name' => 'Selective Rack',
                'description' => 'Selective pallet racking with direct access to every pallet. Industry standard for high-SKU warehouses. Capacity 2,000–5,000 lbs per level. Teardrop beam connections, adjustable shelf heights.',
                'specifications' => [
                    'capacity_per_level' => '2,000–5,000 lbs',
                    'shelf_width' => '36–96 inches',
                    'upright_depth' => '42 or 48 inches',
                    'upright_height' => '4–20 feet',
                    'beam_type' => 'Step or box beam',
                ],
            ],
            [
                'sku' => 'SR-003',
                'name' => 'Pallet Rack',
                'description' => 'Heavy-duty pallet racking for warehouse storage. Roll-formed or structural steel uprights. RMI/ANSI MH16.1 compliant. Floor-anchored. Wire decking, pallet supports available.',
                'specifications' => [
                    'capacity_per_level' => 'Up to 10,000+ lbs',
                    'beam_length' => '8 ft (2 pallets) or 12 ft (3 pallets)',
                    'upright_width' => '3 or 4 inches',
                    'anchoring' => 'Required per RMI',
                    'finish' => 'Paint, powder, galvanized',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('storage-racking', $p);
        }
    }

    private function seedLifting(): void
    {
        $products = [
            [
                'sku' => 'LF-001',
                'name' => 'Hand Jack',
                'description' => 'Manual hydraulic hand jack for lifting and positioning. Compact, portable. Workshop, automotive, general maintenance.',
                'specifications' => [
                    'capacity' => '2–5 tons typical',
                    'lift_height' => 'Variable',
                    'operation' => 'Manual hydraulic',
                ],
            ],
            [
                'sku' => 'LF-002',
                'name' => 'Hand Pallet Jack',
                'description' => 'Manual pallet truck for moving palletized loads. Self-contained hydraulic pump, ergonomic handle. Fork lengths 36", 42", or 48". Capacity 5,500–8,000 lbs. Warehouse and distribution.',
                'specifications' => [
                    'capacity' => '5,500–8,000 lbs',
                    'fork_length' => '36", 42", 48"',
                    'lowered_height' => '2.9–3 inches',
                    'steering' => '180–210° radius',
                    'load_wheels' => 'Polyurethane',
                ],
            ],
            [
                'sku' => 'LF-003',
                'name' => 'Manual Stacker',
                'description' => 'Manual pallet stacker for lifting and stacking. Capacity up to 1,000 kg. Lift height to 2.5 m. Hand/foot levers, fixed drop-forged forks, parking brake. C-section steel construction.',
                'specifications' => [
                    'capacity' => '1,000 kg',
                    'lift_height' => 'Up to 2.5 m',
                    'operation' => 'Hand and foot levers',
                    'construction' => 'Heavy-duty C-section steel',
                    'forks' => 'Fixed drop-forged',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('lifting', $p);
        }
    }

    private function seedForkliftMaterialHandling(): void
    {
        $products = [
            [
                'sku' => 'FM-001',
                'name' => 'Forklift',
                'description' => 'General-purpose forklift for material handling. Engine types: diesel, LPG, electric. Lifting capacity 2–10 tons typical. Indoor/outdoor applications.',
                'specifications' => [
                    'capacity' => '2–10 tons',
                    'engine' => 'Diesel, LPG, or electric',
                    'applications' => 'Warehouse, yard, manufacturing',
                ],
            ],
            [
                'sku' => 'FM-002',
                'name' => 'Diesel Forklift',
                'description' => 'Diesel-powered forklift for outdoor and heavy-duty use. Higher torque, longer runtime. Suitable for yard, construction, logistics. Brands: Toyota, Komatsu, Hyster, Yale, Caterpillar.',
                'specifications' => [
                    'power' => 'Diesel engine',
                    'capacity' => '2.5–52 tons',
                    'applications' => 'Outdoor, yard, rough terrain',
                    'fuel' => 'Diesel',
                ],
            ],
            [
                'sku' => 'FM-003',
                'name' => 'Electric Forklift',
                'description' => 'Electric forklift for indoor and emission-free environments. Quiet, zero emissions. Battery-powered. Ideal for warehouse, food, pharmaceutical.',
                'specifications' => [
                    'power' => 'Electric battery',
                    'capacity' => '1–8 tons typical',
                    'emissions' => 'Zero',
                    'applications' => 'Indoor, warehouse',
                ],
            ],
            [
                'sku' => 'FM-004',
                'name' => 'Reach Truck',
                'description' => 'Narrow-aisle reach truck for high-density storage. Telescopic forks extend to access double-deep racking. Stand-up operator. Brands: BT, Jungheinrich, Crown.',
                'specifications' => [
                    'aisle_width' => 'Narrow aisle',
                    'fork_type' => 'Telescopic reach',
                    'operator' => 'Stand-up',
                    'capacity' => '1–2.5 tons',
                ],
            ],
            [
                'sku' => 'FM-005',
                'name' => 'Electric Stacker',
                'description' => 'Electric pallet stacker for vertical stacking. Walk-behind or ride-on. Lifting heights to 3+ m. Warehouse order picking and stacking.',
                'specifications' => [
                    'power' => 'Electric',
                    'capacity' => '1–2 tons',
                    'lift_height' => 'Up to 3+ m',
                    'type' => 'Walk-behind or ride-on',
                ],
            ],
            [
                'sku' => 'FM-006',
                'name' => 'Scale Pallet Truck',
                'description' => 'Pallet truck with integrated weighing scale. Weigh while moving. Ideal for shipping, receiving, inventory. Capacities 2,000–5,000 lbs.',
                'specifications' => [
                    'capacity' => '2,000–5,000 lbs',
                    'weighing' => 'Integrated scale',
                    'display' => 'Digital',
                    'application' => 'Shipping, receiving, inventory',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('forklift-material-handling', $p);
        }
    }

    private function seedParkingSystem(): void
    {
        $products = [
            [
                'sku' => 'PS-001',
                'name' => 'Auto Barrier',
                'description' => 'Automatic boom barrier for access control. Boom length 3–10 m. Aluminum or stainless steel. Electromechanical or hydraulic motor. RFID, card reader, push-button, mobile app integration.',
                'specifications' => [
                    'boom_length' => '3–10 m',
                    'material' => 'Aluminum alloy, stainless steel',
                    'motor' => 'Electromechanical or hydraulic',
                    'power' => 'AC 230V or DC 24V',
                    'speed' => '1.5–15 seconds',
                ],
            ],
            [
                'sku' => 'PS-002',
                'name' => 'Parking System',
                'description' => 'Complete parking management system: barriers, access control, ticket/lpr integration. Centralized software. Suitable for car parks, toll plazas, gated communities.',
                'specifications' => [
                    'components' => 'Barriers, readers, software',
                    'integration' => 'LPR, ticket, RFID',
                    'duty_cycle' => '100% continuous',
                    'temperature' => '-20°C to +55°C',
                ],
            ],
            [
                'sku' => 'PS-003',
                'name' => 'Car Parking System',
                'description' => 'Automated car park barrier. Obstacle detection, soft start/stop, electronic deceleration. IP44–IP55. Remote, RFID, push-button operation.',
                'specifications' => [
                    'safety' => 'Obstacle detection, reverse sensor',
                    'protection' => 'IP44–IP55',
                    'control' => 'Remote, RFID, push-button',
                    'power' => '165–300W',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('parking-system', $p);
        }
    }

    private function seedStoragePallets(): void
    {
        $products = [
            [
                'sku' => 'SP-001',
                'name' => 'Plastic Pallet',
                'description' => 'Heavy-duty plastic pallet for warehousing and distribution. HDPE/PP construction. Static load to 30,000 lbs. Open or closed deck. 3 or 6 runner base. Stackable, rackable.',
                'specifications' => [
                    'static_load' => 'Up to 30,000 lbs',
                    'dynamic_load' => '3,500–5,500 lbs',
                    'racking_load' => '1,100–2,800 lbs',
                    'dimensions' => '48x40" (US), 48x32" (Euro)',
                    'material' => 'HDPE, PP, virgin polyethylene',
                ],
            ],
            [
                'sku' => 'SP-002',
                'name' => 'Plastic Bin',
                'description' => 'Industrial plastic storage bin. Stackable, FDA-compliant. Capacities 190–375 gallons. Two-piece construction, replaceable pallet base. Nestable. Rounded edges.',
                'specifications' => [
                    'capacity' => '190–375 gallons',
                    'dimensions' => '44" W x 48" L x 30.5"–56" H',
                    'material' => 'FDA polyethylene',
                    'design' => 'Stackable, nestable',
                ],
            ],
            [
                'sku' => 'SP-003',
                'name' => 'Storage Pallet',
                'description' => 'General-purpose storage pallet for warehouse and manufacturing. Stackable design. Multiple footprint options 24x24" to 48x72". Suitable for static storage, racking.',
                'specifications' => [
                    'footprint' => '24x24" to 48x72"',
                    'stackable' => 'Yes',
                    'configurations' => 'Open/closed deck',
                ],
            ],
            [
                'sku' => 'SP-004',
                'name' => 'Stackable Pallet',
                'description' => 'Stackable plastic pallet for double-stacking loaded pallets. Maximizes vertical storage. Heavy-duty for warehouse and distribution. Rackable options.',
                'specifications' => [
                    'stacking' => 'Double-stack loaded',
                    'dynamic_load' => '3,500–5,500 lbs',
                    'applications' => 'Warehouse, distribution',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('storage-pallets', $p);
        }
    }

    private function seedStorageBaskets(): void
    {
        $products = [
            [
                'sku' => 'SB-001',
                'name' => 'Plastic Basket',
                'description' => 'Industrial plastic storage basket. Stackable, injection-molded PP/HDPE. Load capacity to 340 kg. Reinforced ribs, wide stacking ledges. Options: open-fronted, dividers, labels.',
                'specifications' => [
                    'capacity' => 'Up to 340 kg',
                    'material' => 'PP, HDPE',
                    'design' => 'Stackable, reinforced ribs',
                    'volume' => '44–80 liters typical',
                ],
            ],
            [
                'sku' => 'SB-002',
                'name' => 'Storage Basket',
                'description' => 'Heavy-duty plastic stackable storage basket. Ergonomic handles, molded label slots. Chemical and oil resistant. Temperature -2°F to 120°F. Warehouse, in-process, automotive, retail.',
                'specifications' => [
                    'construction' => 'Heavy-duty molded',
                    'resistance' => 'Chemical, oil',
                    'temperature' => '-2°F to 120°F',
                    'applications' => 'Warehouse, automotive, retail',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('storage-baskets', $p);
        }
    }

    private function seedDockMaterialLift(): void
    {
        $products = [
            [
                'sku' => 'DM-001',
                'name' => 'Cargo Lift',
                'description' => 'Vertical cargo lift for loading docks. Ground-to-dock height adjustment. Capacities 25,000–50,000 lbs. Pit-mount or surface-mount. ANSI MHI 30.1 compliant.',
                'specifications' => [
                    'capacity' => '25,000–50,000 lbs',
                    'travel_height' => 'Up to 59"',
                    'width' => '6\'–7\'',
                    'length' => '6\'–10\'',
                    'mounting' => 'Pit or surface',
                ],
            ],
            [
                'sku' => 'DM-002',
                'name' => 'Material Lift',
                'description' => 'Industrial material lift for vertical transport of pallets and goods. Scissor or vertical mast. Applications: receiving, production, multi-level storage.',
                'specifications' => [
                    'type' => 'Scissor or vertical mast',
                    'capacity' => '2,000–10,000 lbs',
                    'applications' => 'Receiving, production',
                ],
            ],
            [
                'sku' => 'DM-003',
                'name' => 'Dock Ramp',
                'description' => 'Yard ramp or dock ramp for loading/unloading at ground level. Bridges truck bed to dock. Aluminum or steel. Portable or fixed. Capacity ratings to 25,000+ lbs.',
                'specifications' => [
                    'material' => 'Aluminum or steel',
                    'type' => 'Portable or fixed',
                    'applications' => 'Ground-level loading',
                ],
            ],
            [
                'sku' => 'DM-004',
                'name' => 'Dock Leveler',
                'description' => 'Dock leveler bridges height and gap between truck bed and loading dock. Service range typically ±12" from dock level. Semi-automatic or air-powered. ¼" tread-plate steel. Lip 16–20". Toe guards, night locks. ANSI MHI 30.1.',
                'specifications' => [
                    'service_range' => '±12" from dock level',
                    'platform' => '¼" tread-plate steel',
                    'lip_length' => '16", 18", or 20"',
                    'cant_compensation' => 'Up to 4"',
                    'operation' => 'Semi-automatic or air-powered',
                ],
            ],
        ];

        foreach ($products as $p) {
            $this->createProduct('dock-material-lift', $p);
        }
    }
}
