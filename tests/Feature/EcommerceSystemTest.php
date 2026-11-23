<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Address;
use App\Models\DeliveryAgent;
use App\Models\OrderPayment;
use App\Models\ProductReview;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EcommerceSystemTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected User $customerUser;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test Admin
        $this->adminUser = User::create([
            'name'     => 'System Admin QA',
            'email'    => 'qa_admin_' . Str::random(8) . '@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
            'status'   => 1,
        ]);

        // Create test Customer
        $this->customerUser = User::create([
            'name'     => 'John Customer',
            'email'    => 'qa_cust_' . Str::random(8) . '@example.com',
            'phone'    => '01711' . rand(100000, 999999),
            'password' => Hash::make('password123'),
            'role'     => 'customer',
            'status'   => 1,
        ]);

        // Create test Category
        $this->category = Category::create([
            'name'        => 'Electronics QA',
            'slug'        => 'electronics-qa-' . Str::random(6),
            'description' => 'Test Category Description',
            'status'      => 1,
        ]);

        // Create test Product
        $this->product = Product::create([
            'name'            => 'Wireless Earbuds Pro',
            'sku'             => 'SKU-' . strtoupper(Str::random(6)),
            'category_id'     => $this->category->id,
            'qty'             => 25,
            'price'           => 1200.00,
            'cost_price'      => 800.00,
            'min_stock_level' => 5,
            'status'          => 1,
            'featured'        => 1,
            'description'     => 'High quality wireless earbuds with active noise cancellation.',
        ]);
    }

    /**
     * Test 1: Customer Registration & Role Assignment Security
     */
    public function test_customer_registration_and_authentication(): void
    {
        $uniqueEmail = 'newbuyer_' . Str::random(6) . '@example.com';
        $uniquePhone = '01811' . rand(100000, 999999);

        $response = $this->post('/register', [
            'name'                  => 'Rahim Ahmed',
            'email'                 => $uniqueEmail,
            'phone'                 => $uniquePhone,
            'password'              => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
            'role'                  => 'admin', // Malicious attempt to self-assign admin
            'terms'                 => '1',
        ]);

        $response->assertRedirect('/account');

        $user = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($user);
        // Security check: Must always be customer, role parameter in post must be ignored
        $this->assertEquals('customer', $user->role);
        $this->assertTrue(Hash::check('SecurePass123!', $user->password));
    }

    /**
     * Test 2: Storefront Catalog Browsing & Filter Responses
     */
    public function test_storefront_and_catalog_navigation(): void
    {
        // 1. Home page loads
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee($this->product->name);

        // 2. Shop page with search and category filter
        $shopResponse = $this->get('/shop?search=Wireless&category=' . $this->category->id);
        $shopResponse->assertStatus(200);
        $shopResponse->assertSee($this->product->name);

        // 3. Product details page
        $detailsResponse = $this->get('/product/' . $this->product->slug);
        $detailsResponse->assertStatus(200);
        $detailsResponse->assertSee('Wireless Earbuds Pro');
        $detailsResponse->assertSee('1,200.00');
    }

    /**
     * Test 3: Shopping Cart CRUD & Coupon Engine
     */
    public function test_shopping_cart_workflow(): void
    {
        // Add item to cart as customer
        $addResponse = $this->actingAs($this->customerUser)->post('/cart/add/' . $this->product->id, [
            'quantity' => 2,
        ]);
        $addResponse->assertRedirect();
        $addResponse->assertSessionHas('success');

        // Check cart page
        $cartResponse = $this->actingAs($this->customerUser)->get('/cart');
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee('Wireless Earbuds Pro');

        // Create a discount coupon
        $coupon = Coupon::create([
            'code'             => 'SAVE10_' . strtoupper(Str::random(4)),
            'type'             => 'percent',
            'value'            => 10,
            'min_order_amount' => 500,
            'status'           => 1,
        ]);

        // Apply Coupon
        $applyResponse = $this->actingAs($this->customerUser)->post('/cart/coupon/apply', [
            'coupon_code' => $coupon->code,
        ]);
        $applyResponse->assertRedirect('/cart');
        $applyResponse->assertSessionHas('success');
    }

    /**
     * Test 4: Checkout Order Placement & Stock Deduction
     */
    public function test_checkout_order_placement_and_inventory_deduction(): void
    {
        $initialStock = $this->product->qty; // 25

        // Save delivery address for customer
        $address = Address::create([
            'user_id'    => $this->customerUser->id,
            'full_name'  => 'John Customer',
            'phone'      => '01711000000',
            'address_line' => 'House 12, Road 5, Block D',
            'area'       => 'Dhanmondi',
            'city'       => 'Dhaka',
            'district'   => 'Dhaka',
            'postal_code'=> '1209',
            'type'       => 'home',
            'is_default' => true,
        ]);

        // Add 3 units to cart as customer
        $this->actingAs($this->customerUser)->post('/cart/add/' . $this->product->id, [
            'quantity' => 3,
        ]);

        // Place Order via COD
        $orderResponse = $this->actingAs($this->customerUser)->post('/checkout/order', [
            'address_id'      => $address->id,
            'payment_method'  => 'cod',
        ]);

        // Expect redirect to success screen
        $orderResponse->assertSessionHasNoErrors();
        $orderResponse->assertRedirect();
        $this->assertTrue(session()->has('order_number') || str_contains($orderResponse->headers->get('Location'), '/checkout/success'));

        // Verify order in database
        $order = Order::where('user_id', $this->customerUser->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('online', $order->order_source);
        $this->assertEquals('cod', $order->payment_method);
        $this->assertEquals('pending', $order->payment_status);

        // Verify product stock decremented from 25 to 22
        $this->product->refresh();
        $this->assertEquals($initialStock - 3, $this->product->qty);

        // Verify StockMovement audit trail created
        $stockMovement = StockMovement::where('product_id', $this->product->id)
            ->where('reference_type', 'Order')
            ->where('reference_id', $order->id)
            ->first();

        $this->assertNotNull($stockMovement);
        $this->assertEquals(-3, $stockMovement->quantity);
    }

    /**
     * Test 5: Customer Order Cancellation & Inventory Restoration
     */
    public function test_customer_order_cancellation_and_inventory_restoration(): void
    {
        $startStock = $this->product->qty;

        // Place an online order with 2 units
        $order = Order::create([
            'order_number'    => 'SD-TEST-' . rand(10000, 99999),
            'user_id'         => $this->customerUser->id,
            'order_source'    => 'online',
            'subtotal_amount' => 2400.00,
            'delivery_charge' => 60.00,
            'discount_amount' => 0.00,
            'total_amount'    => 2460.00,
            'paid_amount'     => 0.00,
            'due_amount'      => 2460.00,
            'payment_method'  => 'cod',
            'payment_status'  => 'pending',
            'status'          => 'pending',
            'shipping_name'   => 'John Customer',
            'shipping_phone'  => '01711000000',
            'shipping_address'=> 'Dhanmondi, Dhaka',
        ]);

        OrderItem::create([
            'order_id'     => $order->id,
            'product_id'   => $this->product->id,
            'product_name' => $this->product->name,
            'quantity'     => 2,
            'price'        => 1200.00,
            'subtotal'     => 2400.00,
        ]);

        // Decrement product stock to simulate placed order
        $this->product->decrement('qty', 2);
        $this->product->refresh();
        $currentStock = $this->product->qty;

        // Cancel order through customer controller
        $cancelResponse = $this->actingAs($this->customerUser)
            ->post("/account/orders/{$order->id}/cancel", [
                'reason' => 'Changed my mind',
            ]);

        $cancelResponse->assertRedirect();
        $cancelResponse->assertSessionHas('success');

        // Verify order status
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // Verify stock is restored
        $this->product->refresh();
        $this->assertEquals($currentStock + 2, $this->product->qty);

        // Verify cancellation stock movement
        $restoreMovement = StockMovement::where('product_id', $this->product->id)
            ->where('type', 'cancellation')
            ->where('reference_id', $order->id)
            ->first();

        $this->assertNotNull($restoreMovement);
        $this->assertEquals(2, $restoreMovement->quantity);
    }

    /**
     * Test 6: Address Management & Data Isolation Between Customers
     */
    public function test_customer_addresses_crud_and_isolation(): void
    {
        // Customer A creates an address
        $addressA = Address::create([
            'user_id'      => $this->customerUser->id,
            'full_name'    => 'Customer A',
            'phone'        => '01711111111',
            'address_line' => '123 Gulshan Ave',
            'city'         => 'Dhaka',
            'type'         => 'home',
        ]);

        // Create Customer B
        $customerB = User::create([
            'name'     => 'Customer B',
            'email'    => 'custb_' . Str::random(6) . '@example.com',
            'password' => Hash::make('password123'),
            'role'     => 'customer',
            'status'   => 1,
        ]);

        // Customer B tries to delete Customer A's address -> Must receive 403 Forbidden
        $unauthorizedResponse = $this->actingAs($customerB)
            ->delete("/account/addresses/{$addressA->id}");

        $unauthorizedResponse->assertStatus(403);

        // Customer A can delete their own address
        $authorizedResponse = $this->actingAs($this->customerUser)
            ->delete("/account/addresses/{$addressA->id}");

        $authorizedResponse->assertRedirect('/account/addresses');
        $this->assertDatabaseMissing('addresses', ['id' => $addressA->id]);
    }

    /**
     * Test 7: Customer Wishlist and Reviews
     */
    public function test_customer_wishlist_and_reviews(): void
    {
        // 1. Toggle wishlist
        $wishlistResponse = $this->actingAs($this->customerUser)
            ->post("/wishlist/toggle/{$this->product->id}");

        $wishlistResponse->assertRedirect();
        $this->assertDatabaseHas('wishlists', [
            'user_id'    => $this->customerUser->id,
            'product_id' => $this->product->id,
        ]);

        // 2. Submit product review
        $reviewResponse = $this->actingAs($this->customerUser)
            ->post("/product/{$this->product->id}/review", [
                'rating'  => 5,
                'title'   => 'Exceptional Audio Quality!',
                'comment' => 'Great battery life and seamless Bluetooth pairing.',
            ]);

        $reviewResponse->assertRedirect();
        $this->assertDatabaseHas('product_reviews', [
            'user_id'    => $this->customerUser->id,
            'product_id' => $this->product->id,
            'rating'     => 5,
        ]);
    }

    /**
     * Test 8: Admin Panel Security & Operational Management
     */
    public function test_admin_security_and_management_access(): void
    {
        // Customer cannot access admin routes -> Forbidden / 403
        $custResponse = $this->actingAs($this->customerUser)->get('/admin/dashboard');
        $custResponse->assertStatus(403);

        // Admin can access admin dashboard
        $adminDashboard = $this->actingAs($this->adminUser)->get('/admin/dashboard');
        $adminDashboard->assertStatus(200);

        // Admin can access inventory and movements
        $inventoryResponse = $this->actingAs($this->adminUser)->get('/inventory');
        $inventoryResponse->assertStatus(200);

        // Admin can register a delivery rider
        $riderResponse = $this->actingAs($this->adminUser)->post('/deliveries/agents/store', [
            'name'         => 'Karim Rider',
            'phone'        => '01911223344',
            'vehicle_type' => 'Motorcycle',
            'status'       => 'available',
        ]);
        $riderResponse->assertRedirect('/deliveries');
        $this->assertDatabaseHas('delivery_agents', [
            'name'  => 'Karim Rider',
            'phone' => '01911223344',
        ]);

        // Admin can view reports
        $reportResponse = $this->actingAs($this->adminUser)->get('/reports?type=sales');
        $reportResponse->assertStatus(200);
    }
}
