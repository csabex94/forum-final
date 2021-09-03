<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Activitylog\Models\Activity;

class CmsController extends Controller {

    protected $user, $team, $log;
    protected $data = [];

    public function __construct(User $user, Team $team, Activity $log) {
        $this->user = $user;
        $this->team = $team;
        $this->log = $log;
    }

    public function dashboard() {
        $users = $this->user->where('admin', 0)->get();
        
        return view('cms.users')->with('users', $users);
    }

    public function edit($id) {
        $user = $this->user->find($id);

        return view('cms.edit-user')->with('user', $user);
    }

    public function logs() {
        $this->data['in_out'] = $this->log->where('log_name', 'in-out')->with('causer')->get();
        $this->data['created'] = $this->log->where('log_name', 'created')->with('causer')->get();
        $this->data['updated'] = $this->log->where('log_name', 'updated')->with('causer')->get();
        $this->data['deleted'] = $this->log->where('log_name', 'deleted')->with('causer')->get();

        return view('cms.logs', $this->data);
    } 

    public function create() {
        $teams = $this->team->where('team_type', 'group')->where('name', '!=', 'not-showing')->with('user')->get();

        return view('cms.create-user')->with('teams', $teams);
    }

    public function update(Request $request) {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = $this->user->find($request->user_id);
        $user->name = $request->name;
        $user->email = $request->email;

        $user->save();

        return redirect('/cms/edit/'. $user->id);
    }

    public function changePassword(Request $request) {
        Validator::make($request->all(), [
            'password' => ['min:6', 'required_with:password_confirmation', 'same:password_confirmation'],
        ]);

        $user = $this->user->find($request->user_id);
        $user->password = Hash::make($request['password']);
        $user->save();

        return redirect('/cms/edit/'. $user->id);
    }

    public function register(Request $request) { // POST
        $user = new CreateNewUser();
        $team = ($request->team_id) ?? 0;
        $user->create(["name" => $request->name, "email" => $request->email, "password" => $request->password, "password_confirmation" => $request->password_confirmation, "team_id" => $team]);
        
        return redirect('/cms')->with('success', 'User created succesfully!');
    }

    public function search(Request $request) {
        if($request->search) {
            $users = $this->user->where('name', 'LIKE', "%$request->search%")->orWhere('email', 'LIKE', "%$request->search%")->get();
        } else {
            $users = [];
        }
        $this->data['users'] = $users;
        $this->data['searchTerm'] = $request->search;

        return view('cms.search-results', $this->data);
    }

    public function deleteUser(Request $request) {
        $user = $this->user->find($request->user_id);

        $user->is_deleted = true;
        $user->deleted_at = Carbon::now();

        $user->save();

        return redirect('/cms');
    }

    public function revert(Request $request) {
        $user = $this->user->find($request->user_id);

        $user->is_deleted = false;
        $user->deleted_at = NULL;

        $user->save();

        return redirect('/cms');
    }

}
