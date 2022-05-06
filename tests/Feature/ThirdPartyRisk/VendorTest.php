<?php

use Inertia\Testing\Assert;
use App\Models\ThirdPartyRisk\Vendor;
use \App\Models\ThirdPartyRisk\Industry;

beforeEach(function () {
    // before each test, seed the role and industries
    // and login as a Third Party Risk Admin
    $this->seed([
        \Database\Seeders\Admin\AddNewAdminRoleSeeder::class,
        \Database\Seeders\ThirdPartyRisk\IndustriesSeeder::class
    ]);

    $this->admin = loginWithRole('Third Party Risk Administrator');
    $this->data_scope = getScope($this->admin);
});

it('shows the vendors page with industries options', function () {
    $response = $this->get(route('third-party-risk.vendors.index', ['data_scope' => $this->data_scope]));
    $response->assertOk();
    $response->assertInertia(function (Assert $page) {
        return $page
            ->component('third-party-risk/vendor/Index')
            ->has('industries', Industry::count());
    });
});

it('shows the vendors page with one vendor', function () {
    $industry = Industry::first();
    $vendor = Vendor::factory()->for($industry)->create();
    setScope($vendor, $this->data_scope);

    // we can see the page
    $this->get(route('third-party-risk.vendors.index', ['data_scope' => $this->data_scope]))
        ->assertInertia(function (Assert $page) {
            return $page->component('third-party-risk/vendor/Index');
        });
    // the api returns data
    $this->get(route('third-party-risk.vendors.get-json-data', ['data_scope' => $this->data_scope]))
        ->assertJsonPath('data.data.0.name', $vendor->name)
        ->assertJsonPath('data.data.0.industry.id', $industry->id);
});

it('allows the admin to add a new vendor and returns a success message when the data is valid', function () {
    $vendor = Vendor::factory()->for(Industry::first())->make();

    $this
        ->from(route('third-party-risk.vendors.index'))
        ->post(route('third-party-risk.vendors.store', ['data_scope' => $this->data_scope]), $vendor->toArray())
        ->assertRedirect(route('third-party-risk.vendors.index'))
        ->assertSessionHas('success', 'Vendor added successfully.');

    $this->assertDatabaseHas('third_party_vendors', ['name' => $vendor->name]);
});

it('allows the admin to add a new vendor and returns an error message when the data is invalid', function ($invalidVendor, $invalidField) {
    $this
        ->from(route('third-party-risk.vendors.index',))
        ->post(route('third-party-risk.vendors.store', ['data_scope' => $this->data_scope]), $invalidVendor)
        ->assertRedirect(route('third-party-risk.vendors.index'))
        ->assertSessionHasErrors($invalidField);
})->with([
    [
        [
            'contact_name' => 'Contact',
            'email' => 'test@domain.com',
        ],
        ['name']
    ],
    [
        [
            'name' => 'My name',
            'email' => 'test@domain.com',
        ],
        ['contact_name']
    ],
    [
        [
            'name' => 'My name',
            'contact_name' => 'Contact',
        ],
        ['email']
    ],
    [
        [
            'name' => 'My name',
            'contact_name' => 'Contact',
            'email' => 'test'
        ],
        ['email']
    ],
    [
        [
            'name' => 'My name',
            'contact_name' => 'Contact',
            'email' => 'test@domain.com',
            'industry_id' => '999'
        ],
        ['industry_id']
    ]
]);

it('allows the admin to edit the vendor and returns a success message when the data is valid', function () {
    $industry = Industry::first();
    $data = [
        'name' => 'My vendor',
        'contact_name' => 'Contact',
        'email' => 'test@domain.com',
        'status' => 'active',
        'industry_id' => $industry->id
    ];
    $vendor = Vendor::create($data);
    setScope($vendor, $this->data_scope);

    $data['name'] = 'New vendor';
    $data['data_scope'] = $this->data_scope;
    $this
        ->from(route('third-party-risk.vendors.index'))
        ->assertDatabaseHas('third_party_vendors', ['name' => 'My vendor'])
        ->put(route('third-party-risk.vendors.update', $vendor->id), $data)
        ->assertRedirect(route('third-party-risk.vendors.index'))
        ->assertSessionHas('success', 'Vendor updated successfully.');
    $this
        ->assertDatabaseMissing('third_party_vendors', ['name' => 'My vendor'])
        ->assertDatabaseHas('third_party_vendors', ['name' => 'New vendor']);
});

it('allows the admin to remove a vendor', function () {
    loginWithRole('Third Party Risk Administrator');
    $vendor = Vendor::factory()->for(Industry::first())->create();

    $this
        ->from(route('third-party-risk.vendors.index'))
        ->delete(route('third-party-risk.vendors.destroy', $vendor->id))
        ->assertRedirect(route('third-party-risk.vendors.index'))
        ->assertSessionHas('success', 'Vendor deleted successfully.');
    $this->assertDatabaseMissing('third_party_vendors', ['name' => $vendor->name]);
});
