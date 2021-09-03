@extends('cms.layout')

@section('content')
<div class="container-fluid">
<div class="row justify-content-center">
    <div class="col-lg-8 col-md-8">
      <div class="card bg-secondary border-0 mt-5">
        <div class="card-body px-lg-5 py-lg-5">
          <div class="text-center text-muted mb-4">
            <small>Register new user</small>
              @if($errors->any())
                  <div class="alert alert-danger">
                      <p><strong>Opps Something went wrong</strong></p>
                      <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                      </ul>
                  </div>
              @endif
              @if(session('success'))
                  <div class="alert alert-success">{{session('success')}}</div>
              @endif

              @if(session('error'))
                  <div class="alert alert-danger">{{session('error')}}</div>
              @endif
          </div>
          <form method="POST" action="/cms/register">
            @csrf
            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="ni ni-hat-3"></i></span>
                </div>
                <input class="form-control" name="name" placeholder="Name" type="text">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="ni ni-email-83"></i></span>
                </div>
                <input class="form-control" autocomplete="off" name="email" placeholder="Email" type="email">
              </div>
            </div>
            <div class="form-group">
              <div class="input-group input-group-merge input-group-alternative">
                <div class="input-group-prepend">
                  <span class="input-group-text"><i class="ni ni-badge"></i></span>
                </div>
                <select name="team_id" class="form-control">
                  <option value="">Add in group</option>
                  @foreach($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }} (created by - {{ $team->user->name }}) (Users count: {{ count($team->allUsers()) }})</option>
                  @endforeach
                </select>
              </div>
            </div>
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
            <!-- <div class="row my-4">
              <div class="col-12">
                <div class="custom-control custom-control-alternative custom-checkbox">
                  <input class="custom-control-input" id="customCheckRegister" type="checkbox" >
                  <label class="custom-control-label" for="customCheckRegister">
                    <span class="text-muted">I agree with the <a href="#!">Privacy Policy</a></span>
                  </label>
                </div>
              </div>
            </div> -->
            <div class="text-center">
              <input type="submit" class="btn btn-primary mt-4" value="Create Account" />
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
