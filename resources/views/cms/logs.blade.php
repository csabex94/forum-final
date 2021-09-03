@extends('cms.layout')

@section('content')
<div class="header bg-primary pb-6">
  <div class="container-fluid">
    <div class="header-body">
      <div class="row align-items-center py-4">
        <div class="col-lg-12 col-12">
            {{-- <a href="/cms/register" class="btn btn-sm btn-neutral">New User</a> --}}
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
              <h3 class="mb-0">Logs:</h3>
            </div>
          </div>
        </div>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
              <a class="nav-link tabb active" id="in-out-tab" data-toggle="tab" href="#in-out" role="tab" aria-controls="in-out" aria-selected="true">In-Out</a>
            </li>
            <li class="nav-item">
              <a class="nav-link tabb" id="created-tab" data-toggle="tab" href="#created" role="tab" aria-controls="created" aria-selected="false">Created</a>
            </li>
            <li class="nav-item">
                <a class="nav-link tabb" id="updated-tab" data-toggle="tab" href="#updated" role="tab" aria-controls="updated" aria-selected="false">Updated</a>
              </li>
            <li class="nav-item">
              <a class="nav-link tabb" id="deleted-tab" data-toggle="tab" href="#deleted" role="tab" aria-controls="deleted" aria-selected="false">Deleted</a>
            </li>
          </ul>

          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="in-out" role="tabpanel" aria-labelledby="in-out-tab">
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Email</th>
                            <th scope="col">Action</th>
                            <th scope="col">Created At</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($in_out as $log)
                        <tr>
                            <td>
                                {{ $log->subject->name }}
                            </td>
                            <td>
                                {{ $log->subject->email }}
                            </td>
                            <td>
                                {{ $log->description }}
                            </td>
                            <td>
                                {{ $log->created_at->format('d-m-Y H:m:s') }}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="created" role="tabpanel" aria-labelledby="created-tab">
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                      <thead class="thead-light">
                        <tr>
                          <th scope="col">Name</th>
                          <th scope="col">Email</th>
                          <th scope="col">Action</th>
                          <th scope="col">Created At</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($created as $log)
                        <tr>
                            <td>
                              {{ $log->causer->name }}
                            </td>
                            <td>
                              {{ $log->causer->email }}
                            </td>
                            <td>
                              {{ $log->description }}
                            </td>
                            <td>
                              {{ $log->created_at->format('d-m-Y H:m:s') }}
                            </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
            </div>

            <div class="tab-pane fade" id="updated" role="tabpanel" aria-labelledby="updated-tab">
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                      <thead class="thead-light">
                        <tr>
                          <th scope="col">Name</th>
                          <th scope="col">Email</th>
                          <th scope="col">Action</th>
                          <th scope="col">Created At</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($updated as $log)
                        <tr>
                            <td>
                                {{ $log->causer->name }}
                            </td>
                            <td>
                                {{ $log->causer->email }}
                            </td>
                            <td>
                                {{ $log->description }}
                            </td>
                            <td>
                                {{ $log->created_at->format('d-m-Y H:m:s') }}
                            </td>
                        </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
            </div>


            <div class="tab-pane fade" id="deleted" role="tabpanel" aria-labelledby="deleted-tab">
                <div class="table-responsive">
                    <!-- Projects table -->
                    <table class="table align-items-center table-flush">
                      <thead class="thead-light">
                        <tr>
                          <th scope="col">Description</th>
                          <th scope="col">User</th>
                          <th scope="col">Action</th>
                          <th scope="col">Created At</th>
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($deleted as $log)
                        <tr>
                            <td>
                                {{ $log->causer->name }}
                            </td>
                            <td>
                                {{ $log->causer->email }}
                            </td>
                            <td>
                                {{ $log->description }}
                            </td>
                            <td>  
                                {{ $log->created_at->format('d-m-Y H:m:s') }}
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
  </div>
</div>

@endsection

@push('scripts')
  
@endpush

@push('css')
    <style>
        .tabb {
            padding: 12px;
        }
    </style>
@endpush