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
                <h3 class="mb-0">Search Results (<i>{{ $searchTerm }}</i>):</h3>
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
                      <th scope="col">-</th>
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
                            <i class="fas fa-pencil text-success mr-3"></i> <i class="fas fa-trash text-error mr-3"></i> 
                        </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
        </div>
      
    </div>
</div>
@endsection