@if (session()->has('error_message')) 
    <div class="alert alert-danger alert-dismissible mb-2" role="alert" style='border-radius: 10px; text-align: left; margin-bottom: 0px;'>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        {{ session()->get('error_message') }}
    </div>
@elseif(session()->has('success_message'))
    <div class="alert alert-success alert-dismissible mb-2" role="alert" style='border-radius: 10px; text-align: left; margin-bottom: 0px;'>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
        {{ session()->get('success_message') }}
    </div>
@endif