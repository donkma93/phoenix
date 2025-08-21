<style>
    .dropdown-option .dropdown .dropdown-menu:after,
    .dropdown-option .dropdown .dropdown-menu:before {
        display: none !important;
    }

    .dropdown-option .fixed-plugin {
        position: static !important;
    }

    .dropdown-option {
        position: relative;
    }

    .dropdown-option .fixed-plugin .dropdown-menu {
        top: 0 !important;
        right: 64px !important;
    }

    @media print {
        #showPackinglistModal table {
            border-collapse: collapse;
        }

        #showPackinglistModal table td,
        #showPackinglistModal table th
        {
            border-top: 1px solid #000;
            border-bottom: 1px solid #000;
        }
    }
</style>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
    <div class="container-fluid">
        <div class="navbar-wrapper">
            <div class="navbar-minimize">
                <button id="minimizeSidebar" class="btn btn-icon btn-round">
                    <i class="nc-icon nc-minimal-right text-center visible-on-sidebar-mini"></i>
                    <i class="nc-icon nc-minimal-left text-center visible-on-sidebar-regular"></i>
                </button>
            </div>
            <div class="navbar-toggle">
                <button type="button" class="navbar-toggler">
                    <span class="navbar-toggler-bar bar1"></span>
                    <span class="navbar-toggler-bar bar2"></span>
                    <span class="navbar-toggler-bar bar3"></span>
                </button>
            </div>
            <a class="navbar-brand" href="#pablo">{{ __('Dashboard') }}</a>
        </div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation"
            aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navigation">
            {{--
            <a class="btn btn-danger mt-2 mr-3" target="_blank"
               href="https://www.creative-tim.com/product/paper-dashboard-pro-laravel"><i
                    class="tim-icons icon-cart"></i> Buy Now</a>
            <a class="btn btn-success mt-2 mr-3" id="docs" target="_blank"
               href="https://paper-dashboard-pro-laravel.creative-tim.com/docs/getting-started/laravel-setup.html?_ga=2.124096394.1444048996.1606126698-1702452109.1586172448"><i
                    class="tim-icons icon-book-bookmark"></i> Docs</a>
            --}}
            <form>
                <div class="input-group no-border">
                    <input id="txt_search_header" type="text" value="" class="form-control"
                        placeholder="Search bill..." onkeypress="searchKeypress(event)">
                    <div class="input-group-append">

                        <div class="input-group-text">
                            <div class="nav-item btn-rotate dropdown">
                                <a class="nav-link dropdown-toggle" href="" id="navbarDropdownMenuLink"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                                    style="padding: 0">
                                    <i class="nc-icon nc-zoom-split"></i>
                                    <p>
                                        <span class="d-lg-none d-md-block">{{ __('Some Actions') }}</span>
                                    </p>
                                </a>


                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                                    <a id="btn_billnumber" class="dropdown-item"
                                        href="#">{{ __('Bill number') }}</a>
                                    <a id="btn_packinglist" class="dropdown-item"
                                        href="#">{{ __('Packinglist') }}</a>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </form>


            <ul class="navbar-nav dropdown-option">


                <li class="nav-item btn-rotate dropdown" id="logout">
                    <a class="nav-link dropdown-toggle" href="http://example.com" id="navbarDropdownMenuLink2"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="nc-icon nc-settings-gear-65"></i>
                        <p>
                            <span class="d-lg-none d-md-block">{{ __('Account') }}</span>
                        </p>
                    </a>

                    {{--
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink2">
                        <form id="logout-form" action="{{ route('logout', ['locale' => app()->getLocale()]) }}"
                            method="POST" style="display: none;">
                            @csrf
                        </form>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                            <a class="dropdown-item" href="@{{ route('profile.edit') }}">{{ __('My profile') }}</a>
                            <div id="logout-btn">
                                <a style="cursor:pointer" class="dropdown-item"
                                    href="{{ route('logout', ['locale' => app()->getLocale()]) }}"
                                    onclick="event.preventDefault();
                                   document.getElementById('logout-form').submit();">{{ __('Log out') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    --}}


                    <div class="fixed-plugin">
                        <div class="dropdown show-dropdown">
                            {{--
                            <a href="#" data-toggle="dropdown">
                                <i class="fa fa-cog fa-2x"> </i>
                            </a>
                            --}}
                            <ul class="dropdown-menu">
                                <li class="header-title"> Sidebar Background</li>
                                <li class="adjustments-line">
                                    <a href="javascript:void(0)" class="switch-trigger background-color">
                                        <div class="badge-colors text-center">
                                            <span class="badge filter badge-light" data-color="white"></span>
                                            <span class="badge filter badge-default active" data-color="brown"></span>
                                        </div>
                                        <div class="clearfix"></div>
                                    </a>
                                </li>
                                <li class="header-title"> Sidebar Active Color</li>
                                <li class="adjustments-line text-center">
                                    <a href="javascript:void(0)" class="switch-trigger active-color">
                                        <span class="badge filter badge-primary" data-color="primary"></span>
                                        <span class="badge filter badge-info" data-color="info"></span>
                                        <span class="badge filter badge-success" data-color="success"></span>
                                        <span class="badge filter badge-warning" data-color="warning"></span>
                                        <span class="badge filter badge-danger active" data-color="danger"></span>
                                    </a>
                                </li>
                                <li class="header-title">{{ __('language') }}</li>
                                <li class="button-container text-center">
                                    @php
                                        $available_locales = config('app.available_locales');
                                        $locale = app()->getLocale();
                                    @endphp

                                    @foreach ($available_locales as $locale_name => $available_locale)
                                        @if ($available_locale === $locale)
                                            <button class="btn btn-success disabled btn-round btn-sm">
                                                {{ $locale_name }} </button>
                                        @else
                                            <a class="btn btn-success btn-round btn-sm"
                                                href="{{ route('lang.change', ['locale' => $available_locale]) }}">
                                                <span> {{ $locale_name }} </span>
                                            </a>
                                        @endif
                                    @endforeach
                                </li>
                                {{--
                                <li class="button-container">
                                    <a href="https://www.creative-tim.com/product/paper-dashboard-pro-laravel" target="_blank"
                                       class="btn btn-primary btn-block btn-round">Buy Now</a>
                                </li>
                                <li class="button-container">
                                    <a href="https://paper-dashboard-pro-laravel.creative-tim.com/docs/getting-started/laravel-setup.html"
                                       target="_blank" class="btn btn-outline-default btn-block btn-round">
                                        <i class="nc-icon nc-paper"></i> Documentation
                                    </a>
                                </li>
                                <li class="header-title">Thank you for 95 shares!</li>
                                <li class="button-container text-center">
                                    <button id="twitter" class="btn btn-outline-default btn-round btn-sm"><i class="fa fa-twitter"></i>
                                        &middot; 45</button>
                                    <button id="facebook" class="btn btn-outline-default btn-round btn-sm"><i class="fa fa-facebook-f"></i>
                                        &middot; 50</button>
                                    <br>
                                    <br>
                                    <a class="github-button" target="_blank" href="https://github.com/creativetimofficial/ct-paper-dashboard-pro-laravel"
                                       data-icon="octicon-star" data-size="large" data-show-count="true"
                                       aria-label="Star ntkme/github-buttons on GitHub">Star</a>
                                </li>
                                --}}
                            </ul>
                        </div>
                    </div>

                </li>
            </ul>
        </div>
    </div>
</nav>



<div class="modal fade " id="showPackinglistModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header justify-content-start" style="text-align: left">
                <div class="d-flex justify-content-between">
                    <div class="left-side">
                        <h4 id="modalTitle" class="title title-up m-0">{{ __('PACKING LIST: ') }} </h4>
                    </div>
                    <div>
                        <button class="btn btn-success btn-round m-0 btn_print_packing_list" style="display: none;">
                            {{ __('Print') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div class="form-group search-form-group">

                    <div class="search-input position-relative">
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <div class="btn btn-round btn-success" data-dismiss="modal">
                    CLOSE
                </div>
            </div>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        document.addEventListener(`click`, e => {

            if (e.altKey) {


                if (e.target.title == 'BILL') {
                    search_handle(e.target.innerHTML.trim(), 'BILL');
                }


                if (e.target.title == 'PACKINGLIST') {
                    search_handle(e.target.innerHTML.trim(), 'PACKINGLIST');
                }

            }


        });

        $(document).ready(function() {


            $('#btn_packinglist').on("click", function() {
                localStorage.setItem('search_type', 'PACKINGLIST');
            });


            $('#btn_billnumber').on("click", function() {
                localStorage.setItem('search_type', 'BILL');
            });


            $('.btn_print_packing_list').on('click', function () {
                PrintElem('#showPackinglistModal .modal-body');
            });

        });

        function searchKeypress(e) {
            if (e.keyCode == 13) {
                e.preventDefault();

                search_handle($('#txt_search_header').val(), localStorage.getItem('search_type').trim());

            }


        }


        function search_handle(a, b) {
            // debugger;

            // let search_value = $('#txtSearchHeader').val().trim();
            // let search_type = localStorage.getItem('search_type').trim();

            let search_value = a;
            let search_type = b;


            if (search_type == 'BILL') {

                $.ajax({
                    url: '/api/util/bill/' + search_value,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + $.cookie("api-token")
                    },
                    success: function(res) {

                        debugger;
                        let html = '';
                        html += `
                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <i class="nc-icon nc-circle-10"></i>
                                    &nbsp&nbsp
                                ${ res.bill_info.partner_code }
                                </p>
                            </div>
                            <div class="col-md-6"><p>
                                    <i class="nc-icon nc-pin-3"></i>
                                    &nbsp&nbsp
                                ${ res.bill_info.TO_ADD }
                                </p>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-md-6">
                                <p>
                                    <i class="nc-icon nc-app"></i>
                                    &nbsp&nbsp
                                ${ res.bill_info.DWS ? res.bill_info.DWS : '' }
                            </p>
                            </div>
                            <div class="col-md-6"><p>
                                <i class="nc-icon nc-box-2"></i>
                                    &nbsp&nbsp
                                ${ res.bill_packinglist.packing_list_code ? res.bill_packinglist.packing_list_code : '' }
                                </p>
                            </div>
                        </div>


                        `

                        if (res.bill_journey.length == 0) {

                            html += 'No results were found';

                        } else {

                            html += `
                                <hr/>
                                <table style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-sm font-weight-bold" style="width: 180px;">DATE</th>
                                            <th class="text-sm font-weight-bold" style="width: ">NOTE</th>
                                            <th class="text-sm font-weight-bold" style="width: ;">STATUS</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    `



                            res.bill_journey.forEach(function(v, i) {

                                html += (`
                                        <tr>
                                            <td class="text-xs" style="vertical-align: top;">📅 ${v.date_journey}</td>
                                            <td >${v.note} <br><span class="text-sm">-${v.location}</span> </td>

                                            <td class="text-xs font-weight-bold">${  (v.status === null) ? '': v.status}</td>
                                        </tr>
                                    `);
                            })

                            html += `</tbody></table>`

                        };

                        $('.btn_print_packing_list').hide();
                        $('#showPackinglistModal .modal-body').html(html);
                        $('#modalTitle').html('BILL : ' + search_value);
                    },
                    error: function(err) {
                        let html = '';
                        html += `
                        <div class="row">
                            <div class="col-md-12" style="text-align:center"><h4>No results were found</h4></div>
                        </div>

                        `;
                        $('#showPackinglistModal .modal-body').html(html);
                        $('#modalTitle').html('BILL : ' + search_value);
                    }
                });





            } else {

                $.ajax({
                    url: '/api/util/packinglist/' + search_value,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        "Content-Type": "application/json",
                        "Authorization": "Bearer " + $.cookie("api-token")
                    },
                    success: function(res) {
                        let html = '';
                        html += `
                            <table style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-sm font-weight-bold" style="width: ;">ORDER PNX</th>
                                        <th class="text-sm font-weight-bold" style="width: ;">ORDER NUMBER</th>
                                        <th class="text-sm font-weight-bold" style="text-transform: uppercase;">Tracking provider</th>
                                        <th class="text-sm font-weight-bold" style="width: ;">CREATE AT</th>
                                    </tr>
                                </thead>
                                <tbody>
                            `;

                        if (res.length === 0) {
                            html = `
                                    <div class="row">
                                        <div class="col-md-12" style="text-align:center"><h4>No results were found</h4></div>
                                    </div>`;

                        } else {
                            res.forEach(function(v, i) {
                                let tt = (v.tracking === null) ? '' : v.tracking;
                                html += (`
                                        <tr>
                                            <td class="text-xs font-weight-bold">${v.order_code}</td>
                                            <td class="text-xs">${v.order_number}</td>
                                            <td class="text-xs">${(v.tracking_provider === null) ? '' : v.tracking_provider} </td>
                                            <td class="text-xs ">${v.created_at}</td>
                                        </tr>
                                    `);
                            })

                            html += `</tbody></table>`

                        }


                        $('.btn_print_packing_list').show();
                        $('#showPackinglistModal .modal-body').html(html);
                        $('#modalTitle').html('PACKING LIST : ' + search_value);


                    },
                    error: function(err) {
                        let html = '';
                        html += `
                        <div class="row">
                            <div class="col-md-12" style="text-align:center"><h4>No results were found</h4></div>
                        </div>

                        `;
                        $('#showPackinglistModal .modal-body').html(html);
                        $('#modalTitle').html('PACKING LIST : ' + search_value);
                    }
                })

            }






            $("#showPackinglistModal").modal('toggle');

        }


        function PrintElem(selector)
        {
            var divContents = document.querySelector(selector).innerHTML;
            var a = window.open('', '', '');
            a.document.write('<html>');
            a.document.write('<body>');
            a.document.write('<style>');
            a.document.write(`table {
                border-collapse: collapse;
            }
            table td,
            table th {
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
            }`);
            a.document.write('</style>');
            a.document.write('<h2 style="text-align: center;">PACKING LIST</h2>');
            a.document.write(divContents);
            a.document.write('</body></html>');
            a.document.close();
            a.print();
        }


        // Sidebar
        $('.visible-on-sidebar-mini').click(function () {
            localStorage.removeItem('mini_sidebar');
        })

        $('.visible-on-sidebar-regular').click(function () {
            localStorage.setItem('mini_sidebar', 'true');
        })
    </script>
@endpush
<!-- End Navbar -->
