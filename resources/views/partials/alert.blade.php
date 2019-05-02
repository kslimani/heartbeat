    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-{!! $type !!} alert-dismissible fade show" role="alert">
                <span class="{{ $iconClass }}" aria-hidden="true"> {!! $message !!}
                <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('app.close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
