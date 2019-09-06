@extends('portal.layouts.manager.master')

@section('page-title')SMS Report @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-md-12">
                <h5 class="card-title">SMS Report - {{ date('l jS \of F Y')}} as at {{ date("g:i a") }}</h5>
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="sms">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Content</th>
                                        <th>Phone</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($sms); $i++) 
                                        <tr>
                                            <td>{{ $sms[$i]["id"] }}</td>
                                            <td>{{ $sms[$i]["sms_message"] }}</td>
                                            <td>{{ "0".substr($sms[$i]["sms_phone"], 3) }}</td>
                                            <td style="font-weight:450">{!! $sms[$i]["state"]["sms_state_html"] !!}</td>
                                            <td>{{ $sms[$i]["created_at"] }}</td>
                                            <td>{{ $sms[$i]["updated_at"] }}</td>
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
        $('#sms').dataTable( {
            "order": [
                [0, 'desc']
            ]
        } );
    })
    </script>
@endsection

