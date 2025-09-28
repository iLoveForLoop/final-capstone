<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendor = auth()->user()->vendor;

        $vendor->load(['user', 'serviceCategories']);

        $vendor->avatar = $vendor->user->getFirstMediaUrl('avatar') ?? null;
        $vendor->categories = $vendor->serviceCategories()->pluck('name');
        $portfolioImages = $vendor->getMedia('portfolioImages')->map(fn ($media) => [
            'url' => $media->getUrl(),
            'id' => $media->id
        ]) ?? [];
        // $portfolioImages = $vendor->getMedia('portfolioImages')->map(fn ($media) => [
        //     'url' => $media->getUrl(),
        //     'id' => $media->id
        // ]) ?? [];

        $showcaseVideos = $vendor->getMedia('showcaseVideos')->map(fn ($media) => [
            'id' => $media->id,
            'title' => $media->getCustomProperty('title'),
            'url' => $media->getUrl()
        ]);


        return inertia('Vendor/Profile/Index', compact('vendor', 'portfolioImages', 'showcaseVideos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $vendor = auth()->user()->vendor;
        $user = auth()->user();


        // 1. Validate only fields from vendors table
        $validated = $request->validate([
            'contactPerson'      => 'required|string|max:255',   // maps to full_name
            'businessName'       => 'required|string|max:255',   // maps to business_name
            'businessDescription'=> 'nullable|string',           // maps to description
            'businessAddress'    => 'required|string|max:255',   // maps to location
            'phone'              => 'required|string|max:20',    // maps to contact_number

            // Social media
            'socialMedia.instagram' => 'nullable|string|max:255',
            'socialMedia.facebook'  => 'nullable|string|max:255',
            'socialMedia.twitter'   => 'nullable|string|max:255',


            // Specialties stored as JSON
            'specialties'    => 'nullable|array',
            // 'specialties.*'  => 'string|max:255',

            // Files
            'profileImageFile'      => 'nullable|image|max:2048',
            'portfolioImages'       => 'nullable|array',
            'portfolioImages.*'     => 'file|mimes:jpg,jpeg,png|max:2048',
            'removedImagedIds'      => 'nullable|array',

            'showcaseVideos' => 'nullable|array',
            'showcaseVideos.*.title' => 'required|string|max:255',
            'showcaseVideos.*.file'  => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:51200',
        ]);




        // 2. Update vendor (ignore media fields)
        $vendor->update([
            'full_name'      => $validated['contactPerson'],
            'business_name'  => $validated['businessName'],
            'description'    => $validated['businessDescription'] ?? null,
            'location'       => $validated['businessAddress'],
            'contact_number' => $validated['phone'],

            // Social media stored individually
            'instagram'      => $validated['socialMedia']['instagram'] ?? null,
            'facebook'       => $validated['socialMedia']['facebook'] ?? null,
            'twitter'        => $validated['socialMedia']['twitter'] ?? null,

            // Store specialties as JSON
            'specialties' => $validated['specialties'] ?? null

        ]);


        if($request->hasFile('profileImageFile')){
            $user->clearMediaCollection('images');
            $user->addMediaFromRequest('profileImageFile')->toMediaCollection('avatar', 'public');
        }

        // 3. Handle portfolio images with Spatie Media Library
       // Remove selected images

        if ($request->has('removedImageIds')) {
            foreach ($request->removedImageIds as $id) {
                $media = $vendor->media()->where('id', $id)->first();
                if ($media) {
                    $media->delete();
                }
            }
        }

        // Add new uploads
        if ($request->hasFile('portfolioImages')) {
            foreach ($request->file('portfolioImages') as $image) {
                $vendor->addMedia($image)->toMediaCollection('portfolioImages');
            }
        }


        // 4. Handle showcase videos with Spatie Media Library
        if ($request->has('showcaseVideos')) {

            $vendor->clearMediaCollection('showcaseVideos'); // optional
            foreach ($validated['showcaseVideos'] as $videoData) {
                if (isset($videoData['file'])) {
                    $media = $vendor->addMedia($videoData['file'])
                                    ->toMediaCollection('showcaseVideos');

                    $media->setCustomProperty('title', $videoData['title']);
                    $media->save();
                }
            }
        }



        return back()->with('success', 'Profile updated successfully.');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getVendorPortfolio(){


        $vendor = auth()->user()->vendor;
        $vendor->load('media');

        $portfolio = $vendor->getMedia('portfolioImages')->map(fn ($media) => [
            'url' => $media->getUrl()
        ]) ?? [];

        return response()->json([
            'data' => $portfolio,
            'success' => true
        ]);

    }
}
