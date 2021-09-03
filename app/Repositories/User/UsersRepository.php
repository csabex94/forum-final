<?php

namespace App\Repositories\User;


use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Jenssegers\Agent\Agent;

class UserRepository implements UserRepositoryInterface {
    
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }
    
    public function getOnlineUsers(){
        $response = array();
        $onlineUserIds =  collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->orderBy('last_activity', 'desc')
                ->where('user_id', '<>', "")
                ->get()
        )->map(function ($session) {
//            $agent = $this->createAgent($session);
            return $session->user_id;
//            return (object) [
//                'agent' => [
//                    'is_desktop' => $agent->isDesktop(),
//                    'platform' => $agent->platform(),
//                    'browser' => $agent->browser(),
//                ],
//                'user_id' => $session->user_id,
//                'ip_address' => $session->ip_address,
//                'is_current_device' => $session->id === $request->session()->getId(),
//                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
//            ];
        });
        foreach ($onlineUserIds as $userId) {
            array_push($response, [
                [
                    'user' => User::where('id', $userId)->get(),
                    // 'tasks' => Task::where('user_id', $userId)->whereNotNull('spec_date')->get()
                ]
            ]);
        }
        return $response;
    }

    protected function createAgent($session) {
        return tap(new Agent, function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }

    public function onlineUsers() {
        return $this->getOnlineUsers();
    }

}