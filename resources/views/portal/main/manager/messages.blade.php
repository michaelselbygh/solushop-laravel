@extends('portal.layouts.manager.master')

@section('page-title'){{ $messages["type"] }} Messages @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h5 class="card-title">{{ $messages["type"] }} Messages</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="conversations">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Sender</th>
                                        <th>Message</th>
                                        <th>Last Updated</th>
                                        <th>L.U. Timestamp</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @if ($messages["type"] == "Flagged")
                                        @for($i=0; $i<sizeof($messages['all']); $i++)
                                            <tr>
                                                <td>{{ $messages['all'][$i]['message']["id"] }}</td>
                                                <td>
                                                    @if (isset($messages['all'][$i]['message']["sender"]))
                                                        {{ $messages['all'][$i]['message']["sender"] }}
                                                    @endif
                                                </td>
                                                <td>{{ $messages['all'][$i]['message']["message_content"] }}</td>
                                                <td>{{ date('g:ia, l jS F Y', strtotime($messages['all'][$i]['message']["updated_at"])) }}</td>
                                                <td>{{ $messages['all'][$i]['message']["updated_at"] }}</td>
                                                <td>
                                                    <a href="{{ route("manager.show.conversation", $messages['all'][$i]['message']['message_conversation_id']) }}">
                                                        <button  data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View Conversation" style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                            <i class="ft-eye"></i>
                                                        </button>
                                                    </a>
                                                    
                                                    <button onclick="submitMessageAction('delete|{{$messages['all'][$i]['message']['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Delete Message" style="margin-top: 3px;" class="btn btn-danger btn-sm round">
                                                        <i class="ft-x"></i>
                                                    </button>
                                                    <button onclick="submitMessageAction('approve|{{$messages['all'][$i]['message']['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Approve message as safe" style="margin-top: 3px;" class="btn btn-success btn-sm round">
                                                        <i class="ft-check"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endfor
                                    @else
                                        @for($i=0; $i<sizeof($messages['all']); $i++)
                                            <tr>
                                                <td>{{ $messages['all'][$i]["id"] }}</td>
                                                <td>
                                                    @if (isset($messages['all'][$i]["sender"]))
                                                        {{ $messages['all'][$i]["sender"] }}
                                                    @endif
                                                </td>
                                                <td>{{ $messages['all'][$i]["message_content"] }}</td>
                                                <td>{{ date('g:ia, l jS F Y', strtotime($messages['all'][$i]["updated_at"])) }}</td>
                                                <td>{{ $messages['all'][$i]["updated_at"] }}</td>
                                                <td>
                                                    <a href="{{ route("manager.show.conversation", $messages['all'][$i]['message_conversation_id']) }}">
                                                        <button  data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View Conversation" style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                            <i class="ft-eye"></i>
                                                        </button>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endfor
                                    @endif
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="message-action-form" method="POST" action="{{ route("manager.process.flagged.messages") }}">
            @csrf
            <input type="hidden" name="message_id" id="message_id"/>
            <input type="hidden" name="message_action" id="message_action"/>
        </form>
        
    </section>


    <script>
    $(document).ready(function(){
        $('#conversations').dataTable( {
            "order": [
                [4, 'desc']
            ]
        } );
    })

    function submitMessageAction(message_do)
    {
        message = message_do.split("|");
        document.getElementById('message_id').value = message[1];
        document.getElementById('message_action').value = message[0];
        document.getElementById('message-action-form').submit(); 
    } 
    </script>
@endsection

