<?php

namespace App\Http\Controllers\ThirdPartyRisk;

use App\Http\Controllers\Controller;
use App\Models\ThirdPartyRisk\Industry;
use App\Models\ThirdPartyRisk\Vendor;
use App\Rules\common\UniqueWithinDataScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class VendorController extends Controller
{

    public function __construct() {
        $this->middleware('data_scope')->except('index', 'destroy');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Inertia\Response
     */
    public function index()
    {

        $industries = Industry::all()->map(function ($industry) {
            return [
                'label' => $industry->name,
                'value' => $industry->id
            ];
        });

        return Inertia::render('third-party-risk/vendor/Index', compact('industries'));
    }

    public function getJsonData(Request $request)
    {
        $vendorsQuery = Vendor::query()->with('industry');

        if ($request->has('search')) {
            $vendorsQuery->where('name', 'LIKE', '%' . $request->search . '%');
        }

        $vendors = $vendorsQuery->orderByDesc('id')->paginate($request->per_page ?? 10);
        return response()->json(['data' => $vendors]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'bail',
                'required',
                'string'
            ],
            'contact_name' => [
                'bail',
                'required',
                'string'
            ],
            'email' => [
                'bail',
                'required',
                'email',
                new UniqueWithinDataScope(new Vendor, 'email'),
            ],
            'country' => 'bail|string|nullable',
            'industry_id' => 'bail|nullable|exists:third_party_industries,id'
        ]);

        $vendor = Vendor::create($request->all());
        if (!$vendor) {
            return redirect()->back()->withErrors('Unable to add vendor');
        }

        Log::info("User has created a new vendor.", ['vendor_id' => $vendor->id]);
        return redirect()->back()->withSuccess('Vendor added successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\ThirdPartyRisk\Vendor $vendor
     * @return Vendor[]
     */
    public function edit(Request $request, Vendor $vendor)
    {
        return ['vendor' => $vendor];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\ThirdPartyRisk\Vendor $vendor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendor $vendor)
    {
        $request->validate([
            'name' => [
                'bail',
                'required',
                'string'
            ],
            'contact_name' => [
                'bail',
                'required',
                'string'
            ],
            'email' => [
                'bail',
                'required',
                'email',
                new UniqueWithinDataScope(new Vendor, 'email', $vendor->id),
            ],
            'country' => 'bail|string|nullable',
            'industry_id' => 'bail|nullable|exists:third_party_industries,id'
        ]);

        if (!$vendor) {
            return redirect()->back()->withErrors('Unable to add vendor');
        }

        $vendorUpdated = $vendor->update($request->all());

        if (!$vendorUpdated) {
            return redirect()->back()->withErrors('Oops something went wrong!');
        }

        Log::info('User has updated a vendor.', ['vendor' => $vendor->id]);
        return redirect()->back()->withSuccess('Vendor updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\ThirdPartyRisk\Vendor $vendor
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendor $vendor)
    {
        $vendorId = $vendor->id;
        $vendorDeleted = $vendor->delete();

        if ($vendorDeleted) {
            Log::info('User has deleted a vendor.', ['vendor_id' => $vendorId]);
            return redirect()->back()->withSuccess("Vendor deleted successfully.");
        }

        Log::info('User tried to delete a vendor but it was not deleted', ['vendor_id' => $vendorId]);
        return redirect()->back()->withErrors("Could not delete vendor.");
    }
}
