<?php

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Events\ReadMessagesEvent;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\TopicController;
use App\Models\File;
use App\Models\PrivateMessage;
use App\Models\PrivateRead;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamUser;
use App\Models\Topic;
use App\Models\TopicConversation;
use App\Models\TopicFile;
use App\Models\TopicRead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TopicConversationController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PrivateMessageController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Laravel\Jetstream\Jetstream;

// For Postman - dev
Route::get('/token', function() {
    return csrf_token();
});
// Generate API Key and secret for pusher
Route::get('/get-keys', function () {
    return [
        'apiKey' => md5(rand(1, 99999)),
        'secretKey' => md5(uniqid(rand(), true)),
        'appId' => uniqid()
    ];
});

Route::post('/log/login', [UsersController::class, 'loggedIn'])->name('log.login');

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    // Get Videos
    Route::get("/video-get/{videoName}", function (String $videoName){
        $mediaBlob = Storage::disk('local')->get('public/'.$videoName);
        $headers = [
            "Content-Disposition" => 'attachment; filename="'.$videoName.'"',
            "Content-Transfer-Encoding" =>  "binary",
            "Accept-Ranges" => "bytes",
            "Content-length" => strlen($mediaBlob)
        ];
        return response()->stream(function () use ($mediaBlob) {
            echo $mediaBlob;
        }, 200, $headers);
    });


    // Get Image from Storage(icons: pdf, excel, word, etc...)
    Route::get('/image-get/{filename}', function(String $filename) {
        $path = Storage::disk('local')->get('public/'.$filename);
        return response()->stream(function() use ($path) {
            echo $path;
        }, 200);
    });
    // Get Thumbnails from Storage
    Route::get('/thumbnail-get/{filename}', function(String $filename) {
        $path = Storage::disk('local')->get('public/thumbnails/'.$filename);
        return response()->stream(function() use ($path) {
            echo $path;
        }, 200);
    });
    // Get Profile photos for teams and users
    Route::get('/get-profile-photos/{folder}/{name}', function(String $folder, String $name) {
        $path = Storage::disk('local')->get('public/'.$folder.'/'.$name);
        return response()->stream(function() use ($path) {
            echo $path;
        }, 200);
    });
    // Get Audio file (notification sound)
    Route::get('/get-audio/{name}', function(String $name) {
        $file = Storage::disk('local')->get('public/app/'.$name);
        $filesize = Storage::disk('local')->size('public/app/'.$name);
        $headers = [
            'Content-Description' => 'File Transfer',
            'Content-Type' => 'audio/wav',
            'Content-Disposition' => "attachment; filename*=UTF-8''".asset(''),
            'Content-Length' => $filesize,
            'Pragma' => 'public',
            'Cache-Control' => 'must-revalidate',
            'Expires' => '0',
        ];
        return new Response($file, 200, $headers);
    });


    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::post('/topic-conversation', [TopicConversationController::class, 'store'])->name('conversation.store');
    Route::post('/private-conversation', [PrivateMessageController::class, 'store'])->name('private.message.store');
    Route::post('/delete-private-message', [PrivateMessageController::class, 'deletePrivate'])->name('delete.private.message');
    Route::post('/delete-topic-message', [TopicConversationController::class, 'deleteTopicMessage'])->name('delete.topic.message');
    Route::post('/topic/post', [TopicController::class, 'store'])->name('topic.store');
    Route::put('/topic/update', [TopicController::class, 'update'])->name('topic.update');
    Route::post('/update-group-photo', [TopicController::class, 'updateGroupPhoto'])->name('group.photo');
    Route::post('/user/update/photo', [UpdateUserProfileInformation::class, 'updatePhoto'])->name('user-profile-information.update-photo');
    Route::post('/topic/delete', [TopicController::class, 'deleteTopic'])->name('delete.topic');
    Route::get('/get-user-notifications', [UsersController::class, 'getUserNotifications'])->name('user.notifications');
    Route::put('/mark-user-notification-read', [UsersController::class, 'markNotificationsAsRead'])->name('mark.notification.read');
    Route::get('/read-all-notifications', [UsersController::class, 'readAllNotifications'])->name('read.all.notifications');
    Route::post('/leave-conversation', [GroupController::class, 'leaveConversation'])->name('leave.conversation');
    Route::post('/leave-group', [GroupController::class, 'leaveGroup'])->name('leave.group');
    Route::post('/log/logout', [UsersController::class, 'loggedOut'])->name('log.logout');
    Route::post('/topic/file', [TopicController::class, 'storeTopicFileConversation'])->name('conversation.store.file');
    Route::get('/users', [HomeController::class, 'searchUser'])->name('search.user');
    Route::post('/add-contact', [ContactController::class, 'store'])->name('add.contact');
    Route::post('/delete-contact-invitation', [ContactController::class, 'deleteInvitation'])->name('delete.contact.invitation');
    Route::post('/add-group-member', [GroupController::class, 'addGroupMember'])->name('add.group.member');
    Route::put('/users/conversations', [UsersController::class, 'switchConversation'])->name('switch.conversations');
    Route::get('/download/{filename}', [HomeController::class, 'download'])->name('download');
    Route::get('/download-topic-file/{filename}', [HomeController::class, 'downloadTopicFile'])->name('download.topic.file');
    Route::post('/accept-group-invitation', [GroupController::class, 'acceptGroupInvitation'])->name('accept.group.invitation');
    Route::delete('/test-delete-group', [GroupController::class, 'deleteGroup'])->name('test.delete.group');
    Route::delete('/team-invitations/destroy/{id}', [UsersController::class, 'deleteInvitation'])->where('id', '\d+')->name('team-invitations.delete');
    Route::get('/get-private-messages-paginate', [HomeController::class, 'privateMessagesPaginate'])->name('private.message.paginate');
    Route::get('/get-topic-conversations-paginate', [HomeController::class, 'topicConversationsPaginate'])->name('topic.conversation.paginate');
    Route::get('/topics-paginate', [TopicController::class, 'paginateTopics'])->name('paginate.topics');
    // Custom delete team
    Route::delete('/delete-group', [GroupController::class, 'destroy'])->name('custom.delete.group');
    // Custom Logout
    Route::post('/custom-logout', [UsersController::class, 'customLogout'])->name('custom.logout');
    // Custom Switch Team
    Route::put('/custom-switch-team', [HomeController::class, 'customSwitchTeam'])->name('custom.sw');
    Route::get('/get-user-to-invite', [GroupController::class, 'getUserToInvite'])->name('invite.user.group');
    Route::get('/find-contacts', [ContactController::class, 'findContacts'])->name('find.contacts');
    Route::post('/decline-invitation', [GroupController::class, 'declineInvitation'])->name('decline.invitation');
    Route::get('/get-created-topic', [HomeController::class, 'getCreatedTopic'])->name('get.created.topic');
    Route::delete('/delete-topic-conversation-permanently', [TopicConversationController::class, 'deleteTopicMessagePermanently'])->name('delete.topic.message.permanently');
    Route::delete('/delete-private-message-permanently', [PrivateMessageController::class, 'deletePrivatePermanently'])->name('delete.private.message.permanently');

    // Redirects ...
    Route::get('/topic/post', function() {
       return redirect('/');
    });
    Route::get('/custom-switch-team', function() {
       return redirect('/');
    });
    Route::get('/topic/delete', function() {
        return redirect('/');
    });
    Route::get('/topic/update', function() {
        return redirect('/');
     });
    Route::get('/add-contact', function() {
        return redirect('/');
    });
    Route::get('/users/conversations', function() {
        return redirect('/');
    });
    Route::get('/private-conversation', function() {
        return redirect('/');
    });
    Route::get('/accept-group-invitation', function() {
        return redirect('/');
    });
    /////////////

    Route::get('/get-invitation', function(Request $request) {
         $invitation = \App\Models\TeamInvitation::where('id', $request->invitationId)->with(['team.owner'])->first();
         return response()->json($invitation, 200);
    });


    Route::get('/get-groups', function(Request $request) {
        return response()->json(
            [
                'teams' => $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'),
                'invitationSent' => TeamInvitation::where('email', '<>', $request->user()->email)->with(['team' => function($q) use ($request) {
                    $q->where([
                        ['user_id', $request->user()->id],
                        ['team_type', 'private-message']
                    ])->get();
                }])->get()
            ],
            200
        );
    });

    Route::get('/get-current-group', function(Request $request) {
        return response()->json($request->user()->currentTeam->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'), 200);
    });

    Route::post('/change-user-status-online', function(Request $request) {
        $user = User::where('id', $request->userId)->first();
        if ($user->status === 'offline') {
            $user->setStatus('online');
            event(new \App\Events\UserOnlineEvent($user));
        }
        return response('', 200);
    });

    Route::post('/change-user-status-offline', function(Request $request) {
        $user = User::where('id', $request->userId)->first();
        if ($user->status === 'online') {
            $user->setStatus('offline');
            event(new \App\Events\UserOfflineEvent($user));
        }
        return response('', 200);
    });



    Route::get('/search-messages', function(Request $request) {
        $search = $request->search;
        $type  = $request->type;
        $records = NULL;

        switch($type) {
            case 1:
                if ($request->user()->currentTeam ->team_type == 'group') {
                    $records = TopicFile::where('team_id', $request->user()->current_team_id)->where('original_filename', 'LIKE', "%$search%")->whereIn('extension', ['pdf', 'xlsx', 'rtf', 'txt', 'docx', 'doc'])->get();
                } else if ($request->user()->currentTeam ->team_type == 'private-message') {
                    $records = File::where('team_id', $request->user()->current_team_id)->where('original_filename', 'LIKE', "%$search%")->whereIn('extension', ['pdf', 'xlsx', 'rtf', 'txt', 'docx', 'doc'])->get();
                }
                break;
            case 2:
                if ($request->user()->currentTeam ->team_type == 'group') {
                    $records = TopicFile::where('team_id', $request->user()->current_team_id)->where('original_filename', 'LIKE', "%$search%")->whereIn('extension', ['png', 'jpeg', 'jpg', 'jfit'])->get();
                } else if ($request->user()->currentTeam ->team_type == 'private-message') {
                    $records = File::where('team_id', $request->user()->current_team_id)->where('original_filename', 'LIKE', "%$search%")->whereIn('extension', ['png', 'jpeg', 'jpg', 'jfit'])->get();
                }
                break;
            case 3:
                if ($request->user()->currentTeam ->team_type == 'group') {
                    $records = TopicConversation::where('team_id', $request->user()->current_team_id)->where('message', 'LIKE', "%$search%")->get();
                } else if ($request->user()->currentTeam ->team_type == 'private-message') {
                    $records = PrivateMessage::where('team_id', $request->user()->current_team_id)->where('message', 'LIKE', "%$search%")->get();
                }
                break;
            case 4:
               $records = Topic::where('title', 'LIKE', "%$search%")->orWhere('description', 'LIKE', "%$search%")->with('createdBy')->get();
        }

//        $response = array();
//        foreach ($records as $record) {
//            array_push($response, $record);
//        }
        return response()->json([
            'result' => $records,
            'type' => $type
        ],
            200
        );
    });

    Route::get('/get-private-message-seen-by', function (Request $request) {
        $message = PrivateMessage::where('id', $request->pmid)->with(['seenBy', 'createdBy', 'files'])->first();
        return response()->json($message, 200);
    });

    Route::get('/get-topic-message-seen-by', function(Request $request) {
        $message = TopicConversation::where([
            ['id', $request->tcid],
            ['topic_id', $request->tid]
        ])->with(['seenBy', 'files', 'createdBy'])->first();
        return response()->json($message, 200);
    });

    Route::post('/read-message-private', function(Request $request) {
        $messages = PrivateMessage::with('seenBy')->where('team_id', $request->user()->current_team_id)->get();
        foreach ($messages as $message) {
            \App\Models\PrivateRead::where([
                ['user_id', $request->user()->id],
                ['private_message_id', $message->id],
                ['wseen', NULL]
            ])->update([
               'wseen' => \Carbon\Carbon::now()
            ]);
        }
        $totalUnreadPrivateMessages = PrivateRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();
        $totalUnreadTopicConversations = TopicRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();

        return response()->json([
            'teams' => $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'),
            'totalUnreadPrivateMessages' => count($totalUnreadPrivateMessages),
            'totalUnreadTopicConversations' => count($totalUnreadTopicConversations)
        ], 200);
    })->name('read.message');

    Route::post('/read-message-topic', function(Request $request) {
        if (isset($request->tid)) {
            $messages = \App\Models\TopicConversation::with('seenBy')
                ->where([
                    ['team_id', $request->user()->current_team_id],
                    [ 'topic_id', $request->tid]
                ])->get();
            foreach ($messages as $message) {
                \App\Models\TopicRead::where([
                    ['user_id', $request->user()->id],
                    ['topic_conversation_id', $message->id],
                    ['wseen', NULL]
                ])->update([
                    'wseen' => \Carbon\Carbon::now()
                ]);
            }
        }
        $totalUnreadPrivateMessages = PrivateRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();
        $totalUnreadTopicConversations = TopicRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();

        return response()->json([
            'teams' => $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'),
            'totalUnreadPrivateMessages' => count($totalUnreadPrivateMessages),
            'totalUnreadTopicConversations' => count($totalUnreadTopicConversations)
        ], 200);
    })->name('read.message.topic');

    // Get back all teams after message sent to update left side teams with last message
    Route::get('/get-new-private-messages', function(Request $request) {
        $groupMedia = NULL;
        $groupFiles = NULL;

        $totalUnreadPrivateMessages = PrivateRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();
        $totalUnreadTopicConversations = TopicRead::where([
            ['user_id', $request->user()->id],
            ['wseen', NULL]
        ])->get();

        if ($request->user()->currentTeam->team_type === 'private-message') {
            $groupFiles = File::where([['team_id', $request->user()->current_team_id], ['extension', 'pdf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'xlsx']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'doc']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'rtf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                ->get();
            $groupMedia = File::where([['team_id', $request->user()->current_team_id], ['extension', 'jpg']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'png']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jpeg']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jfif']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                ->get();
        }

        if ($request->user()->currentTeam->team_type === 'group') {
            $groupFiles = TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'pdf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'xlsx']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'doc']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'rtf']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                ->get();
            $groupMedia = TopicFile::where([['team_id', $request->user()->current_team_id], ['extension', 'jpg']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'png']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jpeg']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'jfif']])
                ->orWhere([['team_id', $request->user()->current_team_id], ['extension', 'txt']])
                ->get();
        }

       if (isset($request->pmid)) {
           return response()->json([
               'teams' => $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'),
               'message' => PrivateMessage::where('id', $request->pmid)->with(['createdBy', 'seenBy', 'files'])->first(),
               'user' => [],
               'groupFiles' => $groupFiles,
               'groupMedia' => $groupMedia,
               'totalUnreadPrivateMessages' => count($totalUnreadPrivateMessages),
               'totalUnreadTopicConversations' => count($totalUnreadTopicConversations),
           ], 200);
       }
        if (isset($request->tcid) && isset($request->tid)) {
            return response()->json([
                'teams' => $request->user()->allTeams()->load('owner', 'users', 'teamInvitations', 'receiver', 'privateMessages', 'topicConversations'),
                'message' => \App\Models\TopicConversation::where([['id', $request->tcid], ['topic_id', $request->tid]])->with(['createdBy', 'seenBy', 'files'])->first(),
                'user' => [],
                'groupFiles' => $groupFiles,
                'groupMedia' => $groupMedia,
                'totalUnreadPrivateMessages' => count($totalUnreadPrivateMessages),
                'totalUnreadTopicConversations' => count($totalUnreadTopicConversations),
                'currentTopics' => Topic::where('team_id', $request->user()->current_team_id)->with(['createdBy', 'topicConversations'])->orderBy('created_at', 'DESC')->get(),
            ], 200);
        }
    });

});

// CMS
Route::middleware(['auth:sanctum', 'verified', 'admin'])->group(function() {
    Route::get('/cms', [CmsController::class, 'dashboard'])->name('cms.dashboard');
    Route::get('/cms/register', [CmsController::class, 'create'])->name('cms.create');
    Route::post('/cms/register', [CmsController::class, 'register'])->name('cms.register');
    Route::get('/cms/search', [CmsController::class, 'search'])->name('cms.search');
    Route::get('/cms/edit/{id}', [CmsController::class, 'edit'])->where('id', '\d+')->name('cms.user.edit');
    Route::post('/cms/update', [CmsController::class, 'update'])->name('cms.user.save');
    Route::post('/cms/update-password', [CmsController::class, 'updatePassword'])->name('cms.password.change');
    Route::post('/cms/delete', [CmsController::class, 'deleteUser'])->name('cms.delete.user');
    Route::get('/cms/logs', [CmsController::class, 'logs'])->name('cms.logs');
});


// Dashboard - Jetstream
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');
