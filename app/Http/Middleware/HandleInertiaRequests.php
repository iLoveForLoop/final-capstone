<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {

        $user = Auth::user();

        $hasCatering = false;
        $avatar = null;
        $clientId = null;

        if($user){
            $user->load(['roles']);
            $avatar = $user->getFirstMediaUrl('avatar');
        }

        if($user && $user->hasRole('vendor')){
            $user->load(['client']);

        }



        if ($user && $user->hasRole('vendor')) {


            $user->load('vendor.serviceCategories');
            $hasCatering = optional($user->vendor)->serviceCategories
                ? $user->vendor->serviceCategories->contains(
                    fn($category) => strtolower($category->name) == 'catering'
                )
                : false;


        }

        if ($user && $user->hasRole('client')) {


            $user->load('client');
            $clientId = $user->client->id;

        }



        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
                'clientId' => $clientId,
                'avatar' => $avatar,
                'hasCatering' => $hasCatering,
                'routeName' => \Route::currentRouteName()

            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'info' => fn () => $request->session()->get('info'),
                'test'  => 'It works!',
            ],
        ];
    }
}
