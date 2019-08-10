@extends('portal.layouts.manager.master')

@section('page-title')Conversations @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h5 class="card-title">Manage Conversations</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="conversations">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
                                        <th>Vendor</th>
                                        <th>Last Updated</th>
                                        <th>L.U. Timestamp</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($conversations); $i++)
                                        <tr>
                                            <td>{{ $conversations[$i]["id"] }}</td>
                                            <td>
                                                @if (isset($conversations[$i]["customer"][0]))
                                                    {{ $conversations[$i]["customer"][0]["first_name"]." ".$conversations[$i]["customer"][0]["last_name"] }}
                                                @endif
                                            </td>
                                            <td>{{ $conversations[$i]["vendor"]["name"] }}</td>
                                            <td>{{ date('g:ia, l jS F Y', strtotime($conversations[$i]["updated_at"])) }}</td>
                                            <td>{{ $conversations[$i]["updated_at"] }}</td>
                                            <td>
                                                <a href="{{ route("manager.show.conversation", $conversations[$i]['id']) }}">
                                                    <button  data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View Conversation" style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <script>
    $(document).ready(function(){
        $('#conversations').dataTable( {
            "order": [
                [4, 'desc']
            ]
        } );
    })

    
    </script>
@endsection

