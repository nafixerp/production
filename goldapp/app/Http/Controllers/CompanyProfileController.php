<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $profile = CompanyProfile::first() ?? new CompanyProfile();
        return view('settings.company-profile.index', compact('profile'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:180',
            'legal_name' => 'nullable|string|max:180',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:80',
            'state' => 'nullable|string|max:80',
            'pincode' => 'nullable|string|max:10',
            'country' => 'nullable|string|max:80',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:150',
            'website' => 'nullable|string|max:100',
            'gst_no' => 'nullable|string|max:30',
            'pan_no' => 'nullable|string|max:20',
            'cin_no' => 'nullable|string|max:30',
            'fssai_no' => 'nullable|string|max:50',
            'financial_year_start' => 'nullable|integer|between:1,12',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:5',
            'date_format' => 'nullable|string|max:20',
            'decimal_places' => 'nullable|integer|between:0,4',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company', 'public');
            $data['logo_path'] = $path;
        }
        unset($data['logo']);

        $profile = CompanyProfile::first();
        if ($profile) {
            $profile->update($data);
        } else {
            CompanyProfile::create($data);
        }

        return redirect()->route('company-profile.index')->with('success', 'Company profile updated.');
    }
}
