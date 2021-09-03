<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Laravel\Jetstream\Jetstream;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => function () use ($request) {
                return [
                    'success' => $request->session()->get('success'),
                    'error' => $request->session()->get('error')
                ];
            },
            'all_teams' => function () use ($request) {
                if ($request->user()) {
                    return $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations');
                }
                return;
            },
            'privateMessages' => function() use ($request) {
                return [];
            },
            'user' => function () use ($request) {
                if (! $request->user()) {
                    return;
                }

                if (Jetstream::hasTeamFeatures() && $request->user()) {
                    if ($request->user()->currentTeam) {
                        $request->user()->currentTeam->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations');
                    }
                }

                return array_merge($request->user()->toArray(), [
                    'two_factor_enabled' => ! is_null($request->user()->two_factor_secret)
                ]);
            },
        ]);
    }
}
