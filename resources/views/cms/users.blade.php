@extends('cms.layout')

@section('content')
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-12 col-12">
            <a href="/cms/register" class="btn btn-sm btn-neutral">New User</a>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Page content -->
<div class="container-fluid mt--6">
  <div class="row">
    <div class="col-xl-12">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">Subscribed Users:</h3>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th scope="col">Name</th>
                <th scope="col">Email</th>
                <th scope="col">Created at</th>
                <th scope="col">Deleted at</th>
                <th scope="col">Options</th>
              </tr>
            </thead>
            <tbody>
              @foreach($users as $user)
              <tr>
                  <th scope="row">
                        {{ $user->name }}
                  </th>
                  <td>
                      {{ $user->email }}
                  </td>
                  <td>
                      {{ $user->created_at->format('d-m-Y') }}
                  </td>
                  <td>
                    {{ ($user->deleted_at) ? $user->deleted_at->format('d-m-Y') : '-' }}
                  </td>
                  <td>
                      <a href="/cms/edit/{{$user->id}}"><i class="fas fa-pencil-alt text-green mr-3"></i></a> <a href="#" class="openModal" data-toggle="modal" data-target="#modal" data-id="{{ $user->id }}" data-name={{ $user->name }}><i class="fas fa-trash text-red mr-3"></i></a> 
                  </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form action="/cms/delete" method="POST">
        @csrf
        <input type="hidden" name="user_id" id="userId" value="">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Delete User</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body"> Do you want to delete this user? </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" id="cancel" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Delete</a> 
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('scripts')
  <script>
    $(document).on("click", ".openModal", function() {
      var name = $(this).data('name');
      var user_id = $(this).data('id');
      $('#modalLabel').html("Delete User ("+name+")");
      $('#userId').val(user_id);
    });
    $(document).on("click", "#cancel", function() {
      $('#modal').modal('hide');
    });
  </script>
@endpush