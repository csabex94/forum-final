<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\StatefulGuard;
use App\Repositories\File\FileRepositoryInterface;
use App\Repositories\Message\MessageRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Pipeline\Pipeline;
use Inertia\Inertia;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Http\Requests\LoginRequest;
use Laravel\Jetstream\TeamInvitation;
use Spatie\Activitylog\Models\Activity;
use Laravel\Fortify\Contracts\LogoutResponse;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

class UsersController extends Controller {


    protected $message, $file, $user, $guard, $authController;

    /**
     * Create a new controller instance.
     *
     * @param User $user
     * @param MessageRepositoryInterface $message
     * @param FileRepositoryInterface $file
     * @param StatefulGuard $guard
     */
    public function __construct(User $user, MessageRepositoryInterface $message, FileRepositoryInterface $file, StatefulGuard $guard) {
        $this->message = $message;
        $this->file = $file;
        $this->user = $user;
        $this->guard = $guard;
    }

    public function dashboard(): \Inertia\Response
    {
        return Inertia::render('Dashboard');
    }

    public function loggedIn(Request $request) {
        $user = $this->user->where('email', $request->email)->first();
        $user->setStatus('online');
        event(new \App\Events\UserOnlineEvent($user));
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->tap(function(Activity $activity) use ($user) {
                $activity->subject_id = $user->id;
                $activity->causer_id = $user->id;
                $activity->log_name = 'in-out';
                $activity->description = 'Logged in';
            })
            ->log('users');
    }

    public function loggedOut(Request $request) {
        $user = $this->user->find($request->user()->id);
        activity()
            ->causedBy($user)
            ->performedOn($user)
            ->tap(function(Activity $activity) use ($user) {
                $activity->subject_id = $user->id;
                $activity->causer_id = $user->id;
                $activity->log_name = 'in-out';
                $activity->description = 'Logged out';
            })
            ->log('users');
    }

    public function sendPrivateMessage(Request $request): \Illuminate\Http\JsonResponse
    {
        $validation = $this->request->validate(['message' => 'required']);

        if ($validation) {
            return $this->message->addPrivateMessage($request->user()->id, $request->receiver_id, $request->message);
        } else {
            return response()->json(['error' => 'Message could not be sent.'], 500);
        }

    }

    public function deleteInvitation($id) {
        $invitation = TeamInvitation::find($id);
        if(!$invitation) {
            return false;
        }
        $invitation->delete();

        return redirect('/');
    }

    public function unsendPrivateMessage($message_id) {
        return $this->message->unsendPrivateMessage($message_id);
    }

    public function unsendTopicMessage($message_id) {
        return $this->message->unsendTeamMessage($message_id);
    }

    public function getFavouriteFiles(Request $request) {
        return $this->file->getFavouriteFiles($request->user()->id);
    }

    public function getTeamFavouriteFiles(Request $request) {
        return $this->file->getTeamFavouriteFiles(1);
    }

//    Csabi
    public function switchConversation(Request $request): \Inertia\Response
    {
        $request->validate([
           'pm_id' => 'required|integer'
        ]);
        $request->user()->setConversation($request->pm_id);

        return Inertia::render('Home', [
            'middleSection' => 'private-messages'
        ]);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Laravel\Fortify\Contracts\LogoutResponse
     */
    public function customLogout(Request $request): LogoutResponse
    {

        $request->user()->setStatus('offline');

        event(new \App\Events\UserOfflineEvent($request->user()));

        $this->loggedOut($request);

        $this->guard->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return app(LogoutResponse::class);
    }

    public function getUserNotifications(Request $request): \Illuminate\Http\JsonResponse
    {
        return response()->json($request->user()->notifications, 200);
    }

    public function markNotificationsAsRead(Request $request)
    {
        Notification::where('id', $request->notificationId)->first()->update([
            'read_at' => now()
        ]);
        return response('', 200);
    }

    public function readAllNotifications(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return response('', 303);
    }

    public function customLogin(LoginRequest $request)
    {
        return $this->loginPipeline($request)->then(function ($request) {
            return app(LoginResponse::class);
        });
    }

    /**
     * Get the authentication pipeline instance.
     *
     * @param LoginRequest $request
     * @return Pipeline
     */
    protected function loginPipeline(LoginRequest $request): Pipeline
    {
        if (Fortify::$authenticateThroughCallback) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                call_user_func(Fortify::$authenticateThroughCallback, $request)
            ));
        }

        if (is_array(config('fortify.pipelines.login'))) {
            return (new Pipeline(app()))->send($request)->through(array_filter(
                config('fortify.pipelines.login')
            ));
        }

        return (new Pipeline(app()))->send($request)->through(array_filter([
            config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
            Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,
            AttemptToAuthenticate::class,
            PrepareAuthenticatedSession::class,
        ]));
    }
}
