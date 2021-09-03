@extends('cms.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-8">
          <div class="card bg-secondary border-0 mt-5">
            <div class="card-body px-lg-5 py-lg-5">
              <div class="text-left text-muted mb-4">
                <small>Edit User ({{ $user->name }})</small>
              </div>
              <form method="POST" action="{{ route('cms.user.save')  }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="form-group">
                  <div class="input-group input-group-merge input-group-alternative mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                    </div>
                    <input class="form-control" name="name" value="{{ $user->name }}" placeholder="Name" type="text">
                  </div>
                </div>
                <div class="form-group">
                  <div class="input-group input-group-merge input-group-alternative mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                    </div>
                    <input class="form-control" autocomplete="off" value="{{ $user->email }}" name="email" placeholder="Email" type="email">
                  </div>
                </div>
                <div class="text-center">
                    <input type="submit" class="btn btn-primary mt-4" value="Save" />
                  </div>
                </form>

                <div class="text-left text-muted mb-4">
                    <small>Change Password ({{ $user->name }})</small>
                </div>
                <form method="POST" action="/cms/update-password">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id }}">
                <div class="form-group">
                  <div class="input-group input-group-merge input-group-alternative">
                    <div class="input-group-prepend">
                      <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                    </div>
                    <input class="form-control" name="password" autocomplete="off" placeholder="Password" type="password">
                  </div>
                </div>
                <div class="form-group">
                    <div class="input-group input-group-merge input-group-alternative">
                      <div class="input-group-prepend">
                        <span class="input-group-text"><i class="ni ni-lock-circle-open"></i></span>
                      </div>
                      <input class="form-control" name="password_confirmation" autocomplete="off" placeholder="Confirm Password" type="password">
                    </div>
                  </div>
                <div class="text-center">
                  <input type="submit" class="btn btn-primary mt-4" value="Update Password" />
                </div>
              </form>
            </div>
          </div>
        </div>
    </div>
</div>
@endsection
