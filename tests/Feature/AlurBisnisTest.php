<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Commission;
use App\Models\CommissionItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurBisnisTest extends TestCase
{
    use RefreshDatabase;

    private User $karyawan;
    private User $admin;
    private Product $produk;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test', 'email' => 'admin@test.local',
            'password' => 'password123', 'role' => 'admin',
        ]);
        $this->karyawan = User::create([
            'name' => 'Karyawan Test', 'email' => 'karyawan@test.local',
            'password' => 'password123', 'role' => 'employee',
        ]);
        $this->produk = Product::create([
            'name' => 'Kopi Test', 'harga_modal' => 25000,
            'harga_jual' => 40000, 'stok' => 100,
        ]);
    }

    public function test_alur_ambil_sampai_retur_dan_bagi_hasil_transparan(): void
    {
        $this->actingAs($this->karyawan);

        // Ambil gerobak
        $this->post(route('gerobak.ambil.proses'), [
            'produk' => [$this->produk->id],
            'qty' => [$this->produk->id => 10],
        ])->assertRedirect(route('dashboard'));

        $cart = Cart::where('user_id', $this->karyawan->id)->where('status', 'active')->first();
        $this->assertNotNull($cart);
        $this->assertSame(90, (int) $this->produk->fresh()->stok);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->karyawan->id, 'action' => 'ambil_gerobak',
        ]);

        // Kembalikan gerobak: 4 sisa, 6 terjual
        $item = $cart->items->first();
        $this->post(route('gerobak.retur.proses'), [
            'sisa' => [$item->id => 4],
        ])->assertRedirect(route('komisi.index'));

        $this->assertSame(94, (int) $this->produk->fresh()->stok); // 90 + 4 sisa
        $komisi = Commission::where('cart_id', $cart->id)->first();
        $this->assertNotNull($komisi);
        $this->assertSame(240000.0, (float) $komisi->total_penjualan); // 6 x 40000
        $this->assertSame(150000.0, (float) $komisi->total_modal);     // 6 x 25000
        $this->assertSame(90000.0, (float) $komisi->total_untung);
        $this->assertSame(18000.0, (float) $komisi->upah_20persen);   // 20% x 90000

        // Rincian per produk tersimpan (transparansi)
        $this->assertSame(1, CommissionItem::where('commission_id', $komisi->id)->count());
        $this->assertDatabaseHas('commission_items', [
            'commission_id' => $komisi->id,
            'product_name' => 'Kopi Test',
            'qty_ambil' => 10, 'qty_sisa' => 4, 'qty_terjual' => 6,
            'subtotal_penjualan' => 240000, 'subtotal_modal' => 150000,
            'keuntungan' => 90000, 'upah_item' => 18000,
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->karyawan->id, 'action' => 'retur_gerobak',
        ]);

        // Karyawan melihat komisi dengan rincian transparan
        $this->get(route('komisi.index'))
            ->assertOk()
            ->assertSee('Kopi Test')
            ->assertSee('18.000');

        // Ambil penghasilan -> saldo + log
        $this->post(route('komisi.ambil', $komisi))->assertRedirect(route('komisi.index'));
        $this->assertSame(18000.0, (float) $this->karyawan->fresh()->balance);
        $this->assertSame('paid', $komisi->fresh()->status);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->karyawan->id, 'action' => 'ambil_komisi',
        ]);
    }

    public function test_admin_monitoring_dan_log_read_only(): void
    {
        // Siapkan 1 gerobak sudah dikembalikan
        $this->actingAs($this->karyawan);
        $this->post(route('gerobak.ambil.proses'), [
            'produk' => [$this->produk->id],
            'qty' => [$this->produk->id => 5],
        ]);
        $cart = Cart::where('user_id', $this->karyawan->id)->first();
        $item = $cart->items->first();
        $this->post(route('gerobak.retur.proses'), ['sisa' => [$item->id => 1]]);

        $this->actingAs($this->admin);

        // Monitoring: tab aktif & dikembalikan + detail
        $this->get(route('admin.gerobak.index', ['status' => 'returned']))
            ->assertOk()->assertSee('Karyawan Test');
        $this->get(route('admin.gerobak.index', ['status' => 'active']))->assertOk();
        $this->get(route('admin.gerobak.show', $cart))
            ->assertOk()->assertSee('Kopi Test')->assertSee('4');

        // Dashboard admin memuat data chart 7 hari
        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Karyawan Test');

        // Log aktivitas read-only: data muncul, tidak ada route create/update/delete
        $this->get(route('admin.log.index'))
            ->assertOk()
            ->assertSee('ambil_gerobak')
            ->assertSee('retur_gerobak');

        $routes = app('router')->getRoutes();
        foreach (['store', 'update', 'destroy'] as $aksi) {
            $this->assertFalse($routes->hasNamedRoute("admin.log.$aksi"));
        }
        $this->assertCount(3, ActivityLog::where('user_id', $this->karyawan->id)->get());
    }

    public function test_login_logout_tercatat_dan_employee_tidak_akses_admin(): void
    {
        $this->post(route('login.proses'), [
            'email' => $this->karyawan->email, 'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->karyawan->id, 'action' => 'login',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $this->karyawan->id, 'action' => 'logout',
        ]);

        $this->actingAs($this->karyawan);
        $this->get(route('admin.log.index'))->assertForbidden();
        $this->get(route('admin.gerobak.index'))->assertForbidden();
    }
}