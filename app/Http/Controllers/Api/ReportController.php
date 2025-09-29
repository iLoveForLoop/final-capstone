<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function store(Request $request)
    {

        // dd('here');
        // Determine reporter type based on authenticated user's role
        $reporterType = auth()->user()->is_vendor ? 'vendor' : 'client';

        $validator = Validator::make($request->all(), [
            'reported_id' => 'required|exists:users,id',
            'reported_type' => 'required|in:vendor,client',
            'reason' => 'required|string|max:255',
            'description' => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {

            return redirect()->back()->with('error', $validator->errors());

        }

        try {
            // Check if user has already reported this person today
            $existingReport = Report::where('reporter_id', auth()->id())
                ->where('reported_id', $request->reported_id)
                ->whereDate('created_at', today())
                ->first();

            if ($existingReport) {
                return redirect()->back()->with('error', 'You can only report this user once per day. Please try again tomorrow.');
            }

            Report::create([
                'reporter_id' => auth()->id(),
                'reporter_type' => $reporterType,
                'reported_id' => $request->reported_id,
                'reported_type' => $request->reported_type,
                'reason' => $request->reason,
                'description' => $request->description,
                'status' => 'pending',
            ]);

            return redirect()->back()->with('success', 'Report submitted successfully! We will review it within 24 hours.');

        } catch (\Exception $e) {
            \Log::error('Report submission failed: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to submit report. Please try again.');

        }
    }
}
