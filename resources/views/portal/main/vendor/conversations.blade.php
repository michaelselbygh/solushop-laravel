@extends('portal.layouts.vendor.master')

@section('page-title')Conversations @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <h5 class="card-title">Manage Conversations</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="conversations">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Customer</th>
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
                                                    @if(isset($conversations[$i]["unread_messages"]) and $conversations[$i]["unread_messages"]>0)
                                                    <span style='color:white; background-color: red; padding: 4px 8px; border-radius:20px; margin-left:5px;'>
                                                        {{ $conversations[$i]["unread_messages"] }}
                                                    </span>
                                                @endif
                                                @endif
                                            </td>
                                            <td>{{ date('g:ia, l jS F Y', strtotime($conversations[$i]["updated_at"])) }}</td>
                                            <td>{{ $conversations[$i]["updated_at"] }}</td>
                                            <td>
                                                <a href="{{ route("vendor.show.conversation", $conversations[$i]['id']) }}">
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
                [3, 'desc']
            ]
        } );
    })

    
    </script>
@endsection

