<div class="message">
    @if (session('success'))
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="alert auto-hide alert-success alert-dismissible fade show">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>
                            <b> Success - </b>
                            {{ session('success') }}
                        </span>
                </div>
            </div>
        </div>
    @endif
    @if (session('warning'))
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="alert alert-warning alert-dismissible fade show">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>
                            <b> Warning - </b>
                            {{ session('warning') }}
                        </span>
                </div>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="row justify-content-end">
            <div class="col-md-4">
                <div class="alert alert-danger alert-dismissible fade show">
                    <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                        <i class="nc-icon nc-simple-remove"></i>
                    </button>
                    <span>
                            <b> Error - </b>
                            {{ session('error') }}
                        </span>
                </div>
            </div>
        </div>
    @endif
    @if ($errors->any())
        <div style="max-width: 500px; float: right;"
             class="alert alert-danger alert-dismissible fade show" id="alert-danger"
             role="alert">
            <ul class="m-0">
                @foreach ($errors->all() as $error)
                    <li class="alert-text text-white">{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" aria-hidden="true" class="close" data-dismiss="alert" aria-label="Close">
                <i class="nc-icon nc-simple-remove"></i>
            </button>
        </div>
    @endif
    <div style="clear:both"></div>
</div>
