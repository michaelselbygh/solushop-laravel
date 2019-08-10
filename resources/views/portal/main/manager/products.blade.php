@extends('portal.layouts.manager.master')

@section('page-title')Products @endsection

@section('content-body')
    <section id="configuration">
        <div class="row">
            <div class="col-12">
                <h5 class="card-title">Products</h5>
                @include('portal.main.success-and-error.message')
                <div class="card">
                    <div class="card-content collapse show">
                        <div class="card-body card-dashboard">
                            <table class="table table-striped table-bordered zero-configuration" id="delivery-items">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Preview</th>
                                        <th>Name</th>
                                        <th>State</th>
                                        <th>Sold By</th>
                                        <th>Main Phone</th>
                                        <th>Alt. Phone</th>
                                        <th>Last Updated</th>
                                        <th style="min-width: 150px">Action</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    @for($i=0; $i<sizeof($products); $i++) 
                                        <tr>
                                            <td>{{ $products[$i]["id"] }}</td>
                                            <td>
                                                <ul class="list-unstyled users-list m-0">
                                                    <li data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="{{ $products[$i]["product_name"] }}" class="avatar avatar-sm pull-up">
                                                        <img class="media-object rounded-circle no-border-top-radius no-border-bottom-radius"
                                                        src="{{ url("app/assets/img/products/thumbnails/".$products[$i]["images"][0]["pi_path"].".jpg") }}"
                                                        alt="{{ $products[$i]["product_name"] }}">
                                                    </li>
                                                </ul>
                                            </td>
                                            <td>{{ $products[$i]["product_name"] }}</td>
                                            <td>{!! $products[$i]["state"]["ps_html"] !!}</td>
                                            <td>{{ $products[$i]["vendor"]["name"] }}</td>
                                            <td>{{ "0".substr($products[$i]["vendor"]["phone"], 3) }}</td>
                                            <td>{{ "0".substr($products[$i]["vendor"]["alt_phone"], 3) }}</td>
                                            <td>{{ $products[$i]["updated_at"] }}</td>
                                            <td>
                                                <a href="{{ url('portal/manager/product/'.$products[$i]["id"]) }}">
                                                    <button data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="View {{ $products[$i]["product_name"] }}"  style="margin-top: 3px;" class="btn btn-info btn-sm round">
                                                        <i class="ft-eye"></i>
                                                    </button>
                                                </a>
                                                @switch($products[$i]["state"]["id"])
                                                    @case(1)
                                                        {{-- Live | Disapprove, Delete--}}
                                                        <button onclick="submitProductAction('disapprove|{{$products[$i]['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Disapprove {{ $products[$i]["product_name"] }}" style="margin-top: 3px;" class="btn btn-warning btn-sm round">
                                                            <i class="ft-alert-triangle"></i>
                                                        </button>
                                                        <button onclick="submitProductAction('delete|{{$products[$i]['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Delete {{ $products[$i]["product_name"] }}" style="margin-top: 3px;" class="btn btn-danger btn-sm round">
                                                            <i class="ft-trash"></i>
                                                        </button>
                                                        @break
                                                    @case(2)
                                                        {{-- Pending Approval | Approve, Reject, Delete--}}
                                                        <button onclick="submitProductAction('reject|{{$products[$i]['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Reject {{ $products[$i]["product_name"] }}" style="margin-top: 3px;" class="btn btn-warning btn-sm round">
                                                            <i class="ft-x"></i>
                                                        </button>
                                                        <button onclick="submitProductAction('delete|{{$products[$i]['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Delete {{ $products[$i]["product_name"] }}" style="margin-top: 3px;" class="btn btn-danger btn-sm round">
                                                            <i class="ft-trash"></i>
                                                        </button>
                                                        
                                                        @break
                                                    @case(3)
                                                        {{-- Rejected | Approve, Delete--}}
                                                        <button onclick="submitProductAction('delete|{{$products[$i]['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Delete {{ $products[$i]["product_name"] }}" style="margin-top: 3px;" class="btn btn-danger btn-sm round">
                                                            <i class="ft-trash"></i>
                                                        </button>
                                                        @break
                                                    @case(5)
                                                        {{-- Inactive | Approve, Reject, Delete--}}
                                                        <button onclick="submitProductAction('reject|{{$products[$i]['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Reject {{ $products[$i]["product_name"] }}" style="margin-top: 3px;" class="btn btn-warning btn-sm round">
                                                            <i class="ft-x"></i>
                                                        </button>
                                                        <button onclick="submitProductAction('delete|{{$products[$i]['id']}}')" data-toggle="tooltip" data-popup="tooltip-custom" data-original-title="Delete {{ $products[$i]["product_name"] }}" style="margin-top: 3px;" class="btn btn-danger btn-sm round">
                                                            <i class="ft-trash"></i>
                                                        </button>
                                                        @break
                                                    @default
                                                        
                                                @endswitch
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

    <form id="product-action-form" method="POST" action="{{ route("manager.process.products") }}">
        @csrf
        <input type="hidden" name="product_id" id="product_id"/>
        <input type="hidden" name="product_action" id="product_action"/>
    </form>

    <script>
    $(document).ready(function(){
        $('#delivery-items').dataTable( {
            "order": [
                [3, 'desc'],
                [2, 'asc']
            ]
        } );
    })

    function submitProductAction(product_do)
    {
        product = product_do.split("|");
        document.getElementById('product_id').value = product[1];
        document.getElementById('product_action').value = product[0];
        document.getElementById('product-action-form').submit(); 
    } 
    </script>
@endsection

