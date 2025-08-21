@extends('client.master')

@section('css')
<style>
    .form-check-bill-2 .input-check-bill-2 {
        color: #60697b !important;
    }
    .input-check-bill-2 {
        border-color: rgba(84, 168, 199, 0.5) !important;
    }
    .label-journey-time {
        display: inline-block;
        min-width: 80px;
    }
    .block-result-checkbill p,
    .block-result-checkbill h6 {
        margin: 0;
    }
    .block-result-checkbill p,
    .block-result-checkbill .accordion-wrapper .card-header button {
        font-size: 0.7rem;
    }
    .block-result-checkbill .accordion-wrapper .card.plain .card-header {
        padding: 0;
    }
</style>
@endsection

@section('content')
<section class="wrapper bg-light wrapper-border">
    <div class="container py-14 py-md-16">
        <!-- input check bill-->
        <div class="row gy-10 gy-sm-13 gx-lg-3 align-items-center mb-5">
            <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3">
                <form action="" id="mc-embedded-subscribe-form2" name="" class="validate dark-fields form-check-bill-2" novalidate>
                    <div id="mc_embed_signup_scroll2">
                        <div class="mc-field-group input-group form-floating">
                            <input type="text" id="mce-EMAIL2" name="billCode" class="required email form-control input-check-bill-2"
                                   value="{{!empty($billCode) ? $billCode : ''}}" placeholder="Bill code">
                            <label for="mce-EMAIL2">Bill code</label>
                            <button type="submit" id="mc-embedded-subscribe2" class="btn btn-primary ">Check Bill</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- /input check bill-->

        <div class="row gy-10 gy-sm-13 gx-lg-3 align-items-center">
            <div class="col-12 col-md-8 offset-md-2 col-lg-6 offset-lg-3 block-result-checkbill">
                @if(empty($billData))
                <h6 class=""><span class="text-red">No data, please check again!</span></h6>
                @else
                <h6 class="">
                    From [ <span class="text-red">{{!empty($billData['generalInfo']->sender_address) ? $billData['generalInfo']->sender_address : 'N/A'}}</span> ]
                    to [ <span class="text-red">{{!empty($billData['generalInfo']->receiver_address) ? $billData['generalInfo']->receiver_address : 'N/A'}}</span> ]
                </h6>
                <div class="row">
                    <div class="col-12 col-lg-5">
                        <p class="">
                            <span>Courier name:</span>
                            <span>{{!empty($billData['generalInfo']->bill_courier) ? $billData['generalInfo']->bill_courier : ''}}</span>
                        </p>
                    </div>
                    <div class="col-12 col-lg-7">
                        <p class="">
                            <span>Status:</span>
                            <span>{{!empty($billData['generalInfo']->journey_status) ? $bill_status[$billData['generalInfo']->journey_status] : 'N/A'}}</span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-5">
                        <p class="">
                            <span>Sender:</span>
                            <span>{{!empty($billData['generalInfo']->sender_name) ? $billData['generalInfo']->sender_name : ''}}</span>
                        </p>
                    </div>
                    <div class="col-12 col-lg-7">
                        <p class="">
                            <span>Sender address:</span>
                            <span>{{!empty($billData['generalInfo']->sender_address) ? $billData['generalInfo']->sender_address : ''}}</span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-5">
                        <p class="">
                            <span>Receiver:</span>
                            <span>{{!empty($billData['generalInfo']->receiver_name) ? $billData['generalInfo']->receiver_name : ''}}</span>
                        </p>
                    </div>
                    <div class="col-12 col-lg-7">
                        <p class="">
                            <span>Receiver address:</span>
                            <span>{{!empty($billData['generalInfo']->receiver_address) ? $billData['generalInfo']->receiver_address : ''}}</span>
                        </p>
                    </div>
                </div>
                <h6 class="">Journey:</span></h6>
                <div class="accordion accordion-wrapper" id="accordionExample-2">
                    @if(!empty($billData['groupJourney']))
                    @php
                    $i = 0;
                    @endphp
                    @foreach($billData['groupJourney'] as $k => $v)
                    <div class="card plain accordion-item">
                        <div class="card-header" id="accordion-heading-3-{{$i}}">
                            <button class="{{ $i!==0 ? 'collapsed' : '' }}" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-3-{{$i}}" aria-expanded="false" aria-controls="accordion-collapse-3-{{$i}}">
                                {{$k}}
                            </button>
                        </div>
                        <!--/.card-header -->
                        <div id="accordion-collapse-3-{{$i}}" class="accordion-collapse collapse {{ $i===0 ? 'show' : '' }}" aria-labelledby="accordion-heading-3-{{$i}}" data-bs-target="#accordion-{{$i}}">
                            <div class="card-body">
                                @if(count($v))
                                @foreach($v as $v2)
                                <p>
                                    <span class="label-journey-time">{{date('H:i:s', strtotime($v2->journey_date))}}</span>
                                    <span class="fw-bold">{{$v2->city}}, {{$v2->country}} [ {{$v2->journey_details}} ]</span></span>
                                </p>
                                @endforeach
                                @endif
                            </div>
                            <!--/.card-body -->
                        </div>
                        <!--/.accordion-collapse -->
                    </div>
                    <!--/.accordion-item -->
                    @php
                    $i++;
                    @endphp
                    @endforeach
                    @endif
                </div>
                <!--/.accordion -->
                @endif
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
    </div>
    <!-- /.container -->
</section>
<!-- /section -->
@endsection
