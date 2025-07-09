<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(){

        $users = User::whereDoesntHave('vendor', function ($query) {
            $query->where('is_approved', false);
        })->get();
        $bookings = Booking::all();
        $labels = [];
        $data = [];

        $vendors = Vendor::where('is_approved', true);
        $pending_applications = Vendor::with('serviceCategories')
            ->where('is_approved', false)
            ->get();



        // FOR MYSQL
        $monthlyBookings = DB::table('bookings')
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->whereYear('created_at', now()->year)
        ->groupBy(DB::raw('MONTH(created_at)'))
        ->orderBy('month')
        ->get();


        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('F');
            $matching = $monthlyBookings->firstWhere('month', $i);
            $data[] = $matching ? $matching->total : 0;
        }
        // END FOR MYSQL

        // FOR SQLITE
        // $monthlyBookings = DB::table('bookings')
        // ->selectRaw("strftime('%m', created_at) as month, COUNT(*) as total")
        // ->whereRaw("strftime('%Y', created_at) = ?", [now()->year])
        // ->groupByRaw("strftime('%m', created_at)")
        // ->orderBy('month')
        // ->get();

        // for ($i = 1; $i <= 12; $i++) {
        //     $monthStr = str_pad($i, 2, '0', STR_PAD_LEFT); // ensures '01', '02', etc.
        //     $labels[] = Carbon::create()->month($i)->format('F');
        //     $matching = $monthlyBookings->firstWhere('month', $monthStr);
        //     $data[] = $matching ? $matching->total : 0;
        // }
        // END FOR MYSQLITE


        $topVendors = Vendor::withAvg('reviews', 'rating')
        ->where('is_approved', true)
        ->withCount('bookings')
        ->with(['serviceCategories', 'user']) // get user.name, user.email if needed
        ->orderByDesc('reviews_avg_rating')
        ->take(5)
        ->get();


        $stats = [
            'total_users' => $users->count(),
            'total_vendors' => $vendors->count(),
            'pending_applications' => $pending_applications->count(),
            'pending_vendors' => $pending_applications,
            'total_bookings' => $bookings->count(),
            'labels' => $labels,
            'data' => $data,
            'top_vendors' => $topVendors
        ];





        return inertia('Admin/Index', compact('stats'));
    }


    public function servicesPage() {

        return inertia('Admin/Services/Index');
    }


}
