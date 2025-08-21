@extends('client.master')

@section('content')
    {{--wrapper image-wrapper bg-image bg-overlay--}}
<section class="wrapper bg-dark angled lower-start home-block-1">
    <div class="container pt-7 pt-md-11 pb-8">
        <div class="row gx-0 gy-10 align-items-center">
            <div class="col-lg-8" data-cues="slideInDown" data-group="page-title" data-delay="600">
                <h1 class="display-1 text-white mb-4">Better solution, to a better you. <br />
                    <span class="typer text-primary text-nowrap" data-delay="100" data-words="customer satisfaction,business needs,creative ideas"></span>
                    <span class="cursor text-primary" data-owner="typer"></span>
                </h1>
                <p class="lead fs-24 lh-sm text-white mb-7 pe-md-18 pe-lg-0 pe-xxl-15">We carefully consider our solutions to support each and every stage of your growth.</p>
                {{--<div>
                    <a class="btn btn-lg btn-primary rounded">Get Started</a>
                </div>--}}
            </div>
            <!-- /column -->
        </div>
        <!-- /.row -->



        <div class="row block-check-tracking">
            <div class="col-md-8 col-xl-6 col-12 mx-auto">
                <div class="newsletter-wrapper">
                    <!-- Begin Mailchimp Signup Form -->
                    <div id="mc_embed_signup2">
                        <p class="m-0">Track your order journey</p>
                        <form action="" method="post" id="mc-embedded-subscribe-form2" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                            <div id="mc_embed_signup_scroll2">
                                <div class="mc-field-group input-group form-floating">
                                    <input type="text" value="" name="" class="required email form-control" placeholder="" id="txt_search_header"
                                           onkeypress="searchKeypress(event)" autofocus
                                    >
                                    <label for="mce-EMAIL2">Please enter tracking code</label>
                                    <input type="button" value="Check" name="subscribe" id="mc-embedded-subscribe2" class="btn btn-primary client-check-tracking">
                                </div>
                                <div id="mce-responses2" class="clear">
                                    <div class="response" id="mce-error-response2" style="display:none"></div>
                                    <div class="response" id="mce-success-response2" style="display:none"></div>
                                </div> <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                                <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_ddc180777a163e0f9f66ee014_4b1bcfa0bc" tabindex="-1" value=""></div>
                                <div class="clear"></div>
                            </div>
                        </form>
                    </div>
                    <!--End mc_embed_signup-->
                </div>
                <!-- /.newsletter-wrapper -->
            </div>
            <!-- /column -->
        </div>



    </div>
    <!-- /.container -->
</section>
<!-- /section -->
<section class="wrapper bg-light home-block-2" id="block-services">
    <div class="container pt-20 pb-20">
        <div class="row">
            <div class="col-md-10 offset-md-1 col-lg-8 offset-lg-2">
                <h3 class="display-4 mb-10 px-xl-10 text-uppercase text-center">Services</h3>
            </div>
            <!-- /column -->
        </div>
        <!--/row -->
        <div class="row gy-10 gy-sm-13 gx-lg-3 mb-10 mb-md-12 align-items-center">
            <div class="col-md-8 col-lg-4 position-relative">
                <div class="shape bg-dot primary rellax w-17 h-21" data-rellax-speed="1" style="top: -2rem; left: -1.9rem;"></div>
                <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; right: -1.5rem; width: 85%; height: 90%; "></div>
                <figure class="rounded"><img src="{{ asset('assets/template_1/img/sv-2.png') }}" srcset="{{ asset('assets/template_1/img/sv-2.png 2x') }}" alt="" /></figure>
            </div>
            <!--/column -->
            <div class="col-lg-7 offset-lg-1">
                <div class="d-flex flex-column mb-6">
                    <h3 class="mb-1">Order Fulfillment</h3>
                    <p class="mb-0">Helping you streamline your operations and get your products to market faster and more efficiently. Our e-powerhouse inventory management technology and automated ordering system ensure accuracy and provide you with real-time reporting and a 24/7 view of your inventory.</p>
                </div>
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
        <div class="row gy-10 gy-sm-13 gx-lg-3 mb-10 mb-md-12 align-items-center">
            <div class="col-md-8 col-lg-4 offset-lg-1 order-lg-2 position-relative">
                <div class="shape rounded-circle bg-line primary rellax w-18 h-18" data-rellax-speed="1" style="top: -2rem; right: -1.9rem;"></div>
                <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; left: -1.5rem; width: 85%; height: 90%; "></div>
                <figure class="rounded"><img src="{{ asset('assets/template_1/img/sv-3.png') }}" srcset="{{ asset('assets/template_1/img/sv-3.png 2x') }}" alt=""></figure>
            </div>
            <!--/column -->
            <div class="col-lg-7">
                <div class="d-flex flex-column mb-6">
                    <h3 class="mb-1">Storage</h3>
                    <p class="mb-0">We provide a warehouse for customers to have a card to store goods and will make the transfer if required.</p>
                </div>
                <!--/.accordion -->
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
        <div class="row gy-10 gy-sm-13 gx-lg-3 mb-10 mb-md-12 align-items-center">
            <div class="col-md-8 col-lg-4 position-relative">
                <div class="shape bg-dot primary rellax w-17 h-21" data-rellax-speed="1" style="top: -2rem; left: -1.9rem;"></div>
                <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; right: -1.5rem; width: 85%; height: 90%; "></div>
                <figure class="rounded"><img src="{{ asset('assets/template_1/img/sv-4.jpg') }}" srcset="{{ asset('assets/template_1/img/sv-4.jpg 2x') }}" alt="" /></figure>
            </div>
            <!--/column -->
            <div class="col-lg-7 offset-lg-1">
                <div class="d-flex flex-column mb-6">
                    <h3 class="mb-1">Relabel</h3>
                    <p class="mb-0">Service for sellers. We will gather goods shipped from amazon and re-labeled as soon as the quantity provided by the customer is received.</p>
                </div>
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
        <div class="row gy-10 gy-sm-13 gx-lg-3 mb-10 mb-md-12 align-items-center">
            <div class="col-md-8 col-lg-4 offset-lg-1 order-lg-2 position-relative">
                <div class="shape rounded-circle bg-line primary rellax w-18 h-18" data-rellax-speed="1" style="top: -2rem; right: -1.9rem;"></div>
                <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; left: -1.5rem; width: 85%; height: 90%; "></div>
                <figure class="rounded"><img src="{{ asset('assets/template_1/img/sv-5.jpg') }}" srcset="{{ asset('assets/template_1/img/sv-5.jpg 2x') }}" alt=""></figure>
            </div>
            <!--/column -->
            <div class="col-lg-7">
                <div class="d-flex flex-column mb-6">
                    <h3 class="mb-1">Repack</h3>
                    <p class="mb-0">While fulfilling the customer's requirements, if the product or the box has a problem (torn, not intact), we will re-pack it to help the customer to have a satisfactory product or box before leaving the warehouse.</p>
                </div>
                <!--/.accordion -->
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
        <div class="row gy-10 gy-sm-13 gx-lg-3 mb-10 mb-md-12 align-items-center">
            <div class="col-md-8 col-lg-4 position-relative">
                <div class="shape bg-dot primary rellax w-17 h-21" data-rellax-speed="1" style="top: -2rem; left: -1.9rem;"></div>
                <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; right: -1.5rem; width: 85%; height: 90%; "></div>
                <figure class="rounded"><img src="{{ asset('assets/template_1/img/sv-6.png') }}" srcset="{{ asset('assets/template_1/img/sv-6.png 2x') }}" alt="" /></figure>
            </div>
            <!--/column -->
            <div class="col-lg-7 offset-lg-1">
                <div class="d-flex flex-column mb-6">
                    <h3 class="mb-1">Returns & removal & repack processing</h3>
                    <p class="mb-0">We set up custom return processing for each customer. We understand that each customer has Inspection Requirements, Quality Control Procedures, and Product Refurbishment and Restoration Requirements. We can repair and repackage your products to ensure that you have minimal waste. Our handling is extremely careful and strict to restore your goods to salable condition. Or throw away unusable goods.</p>
                </div>
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
        <div class="row gy-10 gy-sm-13 gx-lg-3 mb-10 mb-md-12 align-items-center">
            <div class="col-md-8 col-lg-4 offset-lg-1 order-lg-2 position-relative">
                <div class="shape rounded-circle bg-line primary rellax w-18 h-18" data-rellax-speed="1" style="top: -2rem; right: -1.9rem;"></div>
                <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; left: -1.5rem; width: 85%; height: 90%; "></div>
                <figure class="rounded"><img src="{{ asset('assets/template_1/img/sv-7.png') }}" srcset="{{ asset('assets/template_1/img/sv-7.png 2x') }}" alt=""></figure>
            </div>
            <!--/column -->
            <div class="col-lg-7">
                <div class="d-flex flex-column mb-6">
                    <h3 class="mb-1">Drop shipping</h3>
                    <p class="mb-0">We manage your Dropshipping professionally. Run your business seamlessly from anywhere in the world with our door-to-door shipping services. Save time, deliver faster and allow us to deliver an outstanding buying experience to your customers. We handle the order-to-delivery process, and you get 24/7 access to your inventory in real time.</p>
                </div>
                <!--/.accordion -->
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
        <div class="row gy-10 gy-sm-13 gx-lg-3 align-items-center">
            <div class="col-md-8 col-lg-4 position-relative">
                <div class="shape bg-dot primary rellax w-17 h-21" data-rellax-speed="1" style="top: -2rem; left: -1.9rem;"></div>
                <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; right: -1.5rem; width: 85%; height: 90%; "></div>
                <figure class="rounded"><img src="{{ asset('assets/template_1/img/sv-8.png') }}" srcset="{{ asset('assets/template_1/img/sv-8.png 2x') }}" alt="" /></figure>
            </div>
            <!--/column -->
            <div class="col-lg-7 offset-lg-1">
                <div class="d-flex flex-column mb-6">
                    <h3 class="mb-1">Shipping solution</h3>
                    <p class="mb-0">
                        Phoenix Logistics offers comprehensive transportation solutions to support the needs of your business. We save our money for customers by using discounted shipping rates and strategic shipping partnerships. We develop the most efficient shipping strategy for your business and can reduce shipping costs for domestic and international shipments.
                        Meanwhile, you will not have any problems.
                        We offer same-day delivery, helping you deliver a special experience for your customers.
                    </p>
                    <p class="mb-0">
                        Our shipping services include:
                    </p>
                    <div class="row gy-3">
                        <div class="col-xl-6">
                            <ul class="icon-list bullet-bg bullet-soft-aqua mb-0">
                                <li><span><i class="uil uil-check"></i></span><span>Small parcel shipping services</span></li>
                                <li class="mt-3"><span><i class="uil uil-check"></i></span><span>LTL and bulk shipping</span></li>
                                <li class="mt-3"><span><i class="uil uil-check"></i></span><span>International package shipping</span></li>
                            </ul>
                        </div>
                        <!--/column -->
                        <div class="col-xl-6">
                            <ul class="icon-list bullet-bg bullet-soft-aqua mb-0">
                                <li><span><i class="uil uil-check"></i></span><span>International freight broker</span></li>
                                <li class="mt-3"><span><i class="uil uil-check"></i></span><span>Intermodal and rail solutions</span></li>
                                <li class="mt-3"><span><i class="uil uil-check"></i></span><span>Customs clearance service and handling personal documents</span></li>
                            </ul>
                        </div>
                        <!--/column -->
                    </div>
                </div>
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
    </div>
    <!-- /.container -->
</section>
<!-- /section -->




<section class="wrapper bg-dark home-block-3">
    <div class="container py-14 py-md-16">
        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
            <div class="col-lg-8 order-lg-2">
                <div class="row gx-md-5 gy-5">
                    <div class="col-md-5 offset-md-1 align-self-end">
                        <div class="card bg-pale-yellow">
                            <div class="card-body">
                                <img src="{{ asset('assets/template_1/img/icons/lineal/clock.svg') }}" class="svg-inject icon-svg icon-svg-md text-yellow mb-3" alt="" />
                                <h4>Same-day delivery</h4>
                                <p class="mb-0">Reduce costs and deliver orders faster.</p>
                            </div>
                            <!--/.card-body -->
                        </div>
                        <!--/.card -->
                    </div>
                    <!--/column -->
                    <div class="col-md-6 align-self-end">
                        <div class="card bg-pale-red">
                            <div class="card-body">
                                <img src="{{ asset('assets/template_1/img/icons/lineal/speedometer.svg') }}" class="svg-inject icon-svg icon-svg-md text-red mb-3" alt="" />
                                <h4>Operational improvement</h4>
                                <p class="mb-0">Improve your operations by using our advanced warehouse automation and automated order processing systems.</p>
                            </div>
                            <!--/.card-body -->
                        </div>
                        <!--/.card -->
                    </div>
                    <!--/column -->
                    <div class="col-md-5">
                        <div class="card bg-pale-leaf">
                            <div class="card-body">
                                <img src="{{ asset('assets/template_1/img/icons/lineal/team.svg') }}" class="svg-inject icon-svg icon-svg-md text-leaf mb-3" alt="" />
                                <h4>Reach more customers faster</h4>
                                <p class="mb-0">Whatever your quantity or seasonal needs, we process your orders quickly with 100% accuracy. Reach customers anywhere in the world, faster and at a lower cost.</p>
                            </div>
                            <!--/.card-body -->
                        </div>
                        <!--/.card -->
                    </div>
                    <!--/column -->
                    <div class="col-md-6 align-self-start">
                        <div class="card bg-pale-primary">
                            <div class="card-body">
                                <img src="{{ asset('assets/template_1/img/icons/lineal/truck.svg') }}" class="svg-inject icon-svg icon-svg-md text-primary mb-3" alt="" />
                                <h4>Guaranteed special service</h4>
                                <p class="mb-0">With our caring and knowledgeable staff, warehouse staff, IT team and account managers, we're always accessible and focused on your business, helping you deliver special service to your customers as well as reacting to situations before they become problems.</p>
                            </div>
                            <!--/.card-body -->
                        </div>
                        <!--/.card -->
                    </div>
                    <!--/column -->
                    <div class="col-md-11 offset-md-1 align-self-end">
                        <div class="card bg-pale-yellow">
                            <div class="card-body">
                                <img src="{{ asset('assets/template_1/img/icons/lineal/user.svg') }}" class="svg-inject icon-svg icon-svg-md text-yellow mb-3" alt="" />
                                <h4>Customer Satisfaction Guarantee</h4>
                                <div class="row gy-3">
                                    <div class="col-xl-6">
                                        <ul class="icon-list bullet-bg bullet-soft-aqua mb-0">
                                            <li><span><i class="uil uil-check"></i></span><span>Lower cost</span></li>
                                            <li class="mt-3"><span><i class="uil uil-check"></i></span><span>Improved performance</span></li>
                                        </ul>
                                    </div>
                                    <!--/column -->
                                    <div class="col-xl-6">
                                        <ul class="icon-list bullet-bg bullet-soft-aqua mb-0">
                                            <li><span><i class="uil uil-check"></i></span><span>Real-time reporting</span></li>
                                            <li class="mt-3"><span><i class="uil uil-check"></i></span><span>Fast growth</span></li>
                                        </ul>
                                    </div>
                                    <!--/column -->
                                </div>
                            </div>
                            <!--/.card-body -->
                        </div>
                        <!--/.card -->
                    </div>
                    <!--/column -->
                </div>
                <!--/.row -->
            </div>
            <!--/column -->
            <div class="col-lg-4">
                <h3 class="display-4 text-white mb-5">Why choose Phoenix?</h3>
                <p class="text-white">Our passion for providing unparalleled personalized attention and special service to our customers.</p>
                {{--<a href="#" class="btn btn-navy rounded-pill mt-3">More Details</a>--}}
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
    </div>
    <!-- /.container -->
</section>
<!-- /section -->




{{--<section class="wrapper bg-light home-block-3">
    <div class="container pt-15 pb-15">
        <div class="row">
            <div class="col-lg-8 col-xl-7 col-xxl-6">
                <h2 class="fs-16 text-uppercase text-line text-primary mb-3">What We Do?</h2>
                <h3 class="display-4 mb-9">The service we offer is specifically designed to meet your needs.</h3>
            </div>
            <!-- /column -->
        </div>
        <!-- /.row -->
        <div class="row gx-md-8 gy-8 mb-14 mb-md-18">
            <div class="col-md-6 col-lg-3">
                <div class="icon btn btn-block btn-lg btn-soft-primary pe-none mb-6"> <i class="uil uil-phone-volume"></i> </div>
                <h4>24/7 Support</h4>
                <p class="mb-3">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus. Cras justo.</p>
                <a href="#" class="more hover link-primary">Learn More</a>
            </div>
            <!--/column -->
            <div class="col-md-6 col-lg-3">
                <div class="icon btn btn-block btn-lg btn-soft-primary pe-none mb-6"> <i class="uil uil-shield-exclamation"></i> </div>
                <h4>Secure Payments</h4>
                <p class="mb-3">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus. Cras justo.</p>
                <a href="#" class="more hover link-primary">Learn More</a>
            </div>
            <!--/column -->
            <div class="col-md-6 col-lg-3">
                <div class="icon btn btn-block btn-lg btn-soft-primary pe-none mb-6"> <i class="uil uil-laptop-cloud"></i> </div>
                <h4>Daily Updates</h4>
                <p class="mb-3">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus. Cras justo.</p>
                <a href="#" class="more hover link-primary">Learn More</a>
            </div>
            <!--/column -->
            <div class="col-md-6 col-lg-3">
                <div class="icon btn btn-block btn-lg btn-soft-primary pe-none mb-6"> <i class="uil uil-chart-line"></i> </div>
                <h4>Market Research</h4>
                <p class="mb-3">Nulla vitae elit libero, a pharetra augue. Donec id elit non mi porta gravida at eget metus. Cras justo.</p>
                <a href="#" class="more hover link-primary">Learn More</a>
            </div>
            <!--/column -->
        </div>
        <!--/.row -->
    </div>
    <!-- /.container -->
</section>--}}
<!-- /section -->




    {{--
    <section class="wrapper image-wrapper bg-image bg-overlay" data-image-src="{{ asset('assets/template_1/img/photos/bg1.jpg') }}">
        <div class="container py-18">
            <div class="row">
                <div class="col-lg-8">
                    <h2 class="fs-16 text-uppercase text-line text-white mb-3">Join Our Community</h2>
                    <h3 class="display-4 mb-6 text-white pe-xxl-18">We are trusted by over 5000+ clients. Join them by using our services and grow your business.</h3>
                    <a href="#" class="btn btn-white rounded mb-0 text-nowrap">Join Us</a>
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
    <!-- /section -->
    <section class="wrapper bg-light angled upper-end">
        <div class="container py-14 py-md-16">
            <div class="row">
                <div class="col-lg-9 col-xl-8 col-xxl-7">
                    <h2 class="fs-16 text-uppercase text-line text-primary mb-3">Case Studies</h2>
                    <h3 class="display-4 mb-9">Check out some of our awesome projects with creative ideas and great design.</h3>
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
            <div class="swiper-container blog grid-view mb-10" data-margin="30" data-dots="true" data-items-xl="3" data-items-md="2" data-items-xs="1">
                <div class="swiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <article>
                                <figure class="overlay overlay-1 hover-scale rounded mb-6"><a href="#"> <img src="{{ asset('assets/template_1/img/photos/b4.jpg') }}" alt="" /></a>
                                    <figcaption>
                                        <h5 class="from-top mb-0">Read More</h5>
                                    </figcaption>
                                </figure>
                                <div class="post-header">
                                    <h2 class="post-title h3 mb-3"><a class="link-dark" href="./blog-post.html">Ligula tristique quis risus</a></h2>
                                </div>
                                <!-- /.post-header -->
                                <div class="post-footer">
                                    <ul class="post-meta">
                                        <li class="post-date"><i class="uil uil-calendar-alt"></i><span>14 Apr 2022</span></li>
                                        <li class="post-comments"><a href="#"><i class="uil uil-file-alt fs-15"></i>Coding</a></li>
                                    </ul>
                                    <!-- /.post-meta -->
                                </div>
                                <!-- /.post-footer -->
                            </article>
                            <!-- /article -->
                        </div>
                        <!--/.swiper-slide -->
                        <div class="swiper-slide">
                            <article>
                                <figure class="overlay overlay-1 hover-scale rounded mb-6"><a href="#"> <img src="{{ asset('assets/template_1/img/photos/b5.jpg') }}" alt="" /></a>
                                    <figcaption>
                                        <h5 class="from-top mb-0">Read More</h5>
                                    </figcaption>
                                </figure>
                                <div class="post-header">
                                    <h2 class="post-title h3 mb-3"><a class="link-dark" href="./blog-post.html">Nullam id dolor elit id nibh</a></h2>
                                </div>
                                <!-- /.post-header -->
                                <div class="post-footer">
                                    <ul class="post-meta">
                                        <li class="post-date"><i class="uil uil-calendar-alt"></i><span>29 Mar 2022</span></li>
                                        <li class="post-comments"><a href="#"><i class="uil uil-file-alt fs-15"></i>Workspace</a></li>
                                    </ul>
                                    <!-- /.post-meta -->
                                </div>
                                <!-- /.post-footer -->
                            </article>
                            <!-- /article -->
                        </div>
                        <!--/.swiper-slide -->
                        <div class="swiper-slide">
                            <article>
                                <figure class="overlay overlay-1 hover-scale rounded mb-6"><a href="#"> <img src="{{ asset('assets/template_1/img/photos/b6.jpg') }}" alt="" /></a>
                                    <figcaption>
                                        <h5 class="from-top mb-0">Read More</h5>
                                    </figcaption>
                                </figure>
                                <div class="post-header">
                                    <h2 class="post-title h3 mb-3"><a class="link-dark" href="./blog-post.html">Ultricies fusce porta elit</a></h2>
                                </div>
                                <!-- /.post-header -->
                                <div class="post-footer">
                                    <ul class="post-meta">
                                        <li class="post-date"><i class="uil uil-calendar-alt"></i><span>26 Feb 2022</span></li>
                                        <li class="post-comments"><a href="#"><i class="uil uil-file-alt fs-15"></i>Meeting</a></li>
                                    </ul>
                                    <!-- /.post-meta -->
                                </div>
                                <!-- /.post-footer -->
                            </article>
                            <!-- /article -->
                        </div>
                        <!--/.swiper-slide -->
                        <div class="swiper-slide">
                            <article>
                                <figure class="overlay overlay-1 hover-scale rounded mb-6"><a href="#"> <img src="{{ asset('assets/template_1/img/photos/b7.jpg') }}" alt="" /></a>
                                    <figcaption>
                                        <h5 class="from-top mb-0">Read More</h5>
                                    </figcaption>
                                </figure>
                                <div class="post-header">
                                    <h2 class="post-title h3 mb-3"><a class="link-dark" href="./blog-post.html">Morbi leo risus porta eget</a></h2>
                                </div>
                                <div class="post-footer">
                                    <ul class="post-meta">
                                        <li class="post-date"><i class="uil uil-calendar-alt"></i><span>7 Jan 2022</span></li>
                                        <li class="post-comments"><a href="#"><i class="uil uil-file-alt fs-15"></i>Business Tips</a></li>
                                    </ul>
                                    <!-- /.post-meta -->
                                </div>
                                <!-- /.post-footer -->
                            </article>
                            <!-- /article -->
                        </div>
                        <!--/.swiper-slide -->
                    </div>
                    <!-- /.swiper-wrapper -->
                </div>
                <!-- /.swiper -->
            </div>
            <!-- /.swiper-container -->
        </div>
        <!-- /.container -->
    </section>
    <!-- /section -->
    <section class="wrapper bg-soft-primary">
        <div class="container py-14 pt-md-17 pb-md-21">
            <div class="row gx-lg-8 gx-xl-12 gy-10 gy-lg-0 mb-2 align-items-end">
                <div class="col-lg-4">
                    <h2 class="fs-16 text-uppercase text-line text-primary mb-3">Company Facts</h2>
                    <h3 class="display-4 mb-0 pe-xxl-15">We are proud of our works</h3>
                </div>
                <!-- /column -->
                <div class="col-lg-8 mt-lg-2">
                    <div class="row align-items-center counter-wrapper gy-6 text-center">
                        <div class="col-md-4">
                            <h3 class="counter counter-lg">1000+</h3>
                            <p>Completed Projects</p>
                        </div>
                        <!--/column -->
                        <div class="col-md-4">
                            <h3 class="counter counter-lg">500+</h3>
                            <p>Happy Clients</p>
                        </div>
                        <!--/column -->
                        <div class="col-md-4">
                            <h3 class="counter counter-lg">150+</h3>
                            <p>Awards Won</p>
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
    <!-- /section -->
    <section class="wrapper bg-light angled upper-end lower-start">
        <div class="container py-16 py-md-18 position-relative">
            <div class="position-relative mt-n18 mt-md-n23 mb-16 mb-md-18">
                <div class="shape rounded-circle bg-line primary rellax w-18 h-18" data-rellax-speed="1" style="top: -2rem; right: -2.7rem; z-index:0;"></div>
                <div class="shape rounded-circle bg-soft-primary rellax w-18 h-18" data-rellax-speed="1" style="bottom: -1rem; left: -3rem; z-index:0;"></div>
                <div class="card shadow-lg">
                    <div class="row gx-0">
                        <div class="col-lg-6 image-wrapper bg-image bg-cover rounded-top rounded-lg-start" data-image-src="{{ asset('assets/template_1/img/photos/tm1.jpg') }}">
                        </div>
                        <!--/column -->
                        <div class="col-lg-6">
                            <div class="p-10 p-md-11 p-lg-13">
                                <div class="swiper-container dots-closer mb-4" data-margin="30" data-dots="true">
                                    <div class="swiper">
                                        <div class="swiper-wrapper">
                                            <div class="swiper-slide">
                                                <blockquote class="icon icon-top fs-lg text-center">
                                                    <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum ligula porta felis euismod semper. Cras justo odio.”</p>
                                                    <div class="blockquote-details justify-content-center text-center">
                                                        <div class="info ps-0">
                                                            <h5 class="mb-1">Coriss Ambady</h5>
                                                            <p class="mb-0">Financial Analyst</p>
                                                        </div>
                                                    </div>
                                                </blockquote>
                                            </div>
                                            <!--/.swiper-slide -->
                                            <div class="swiper-slide">
                                                <blockquote class="icon icon-top fs-lg text-center">
                                                    <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum ligula porta felis euismod semper. Cras justo odio.”</p>
                                                    <div class="blockquote-details justify-content-center text-center">
                                                        <div class="info ps-0">
                                                            <h5 class="mb-1">Cory Zamora</h5>
                                                            <p class="mb-0">Marketing Specialist</p>
                                                        </div>
                                                    </div>
                                                </blockquote>
                                            </div>
                                            <!--/.swiper-slide -->
                                            <div class="swiper-slide">
                                                <blockquote class="icon icon-top fs-lg text-center">
                                                    <p>“Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Vestibulum ligula porta felis euismod semper. Cras justo odio.”</p>
                                                    <div class="blockquote-details justify-content-center text-center">
                                                        <div class="info ps-0">
                                                            <h5 class="mb-1">Nikolas Brooten</h5>
                                                            <p class="mb-0">Sales Manager</p>
                                                        </div>
                                                    </div>
                                                </blockquote>
                                            </div>
                                            <!--/.swiper-slide -->
                                        </div>
                                        <!-- /.swiper-wrapper -->
                                    </div>
                                    <!-- /.swiper -->
                                </div>
                                <!-- /.swiper-container -->
                            </div>
                            <!--/div -->
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /div -->
            <div class="row gy-6 mb-16 mb-md-18">
                <div class="col-lg-4">
                    <h2 class="fs-16 text-uppercase text-line text-primary mt-lg-18 mb-3">Our Pricing</h2>
                    <h3 class="display-4 mb-3">We offer great and premium prices.</h3>
                    <p>Enjoy a <a href="#" class="hover">free 30-day trial</a> and experience the full service. No credit card required!</p>
                    <a href="#" class="btn btn-primary rounded mt-2">See All Prices</a>
                </div>
                <!--/column -->
                <div class="col-lg-7 offset-lg-1 pricing-wrapper">
                    <div class="pricing-switcher-wrapper switcher justify-content-start justify-content-lg-end">
                        <p class="mb-0 pe-3">Monthly</p>
                        <div class="pricing-switchers">
                            <div class="pricing-switcher pricing-switcher-active"></div>
                            <div class="pricing-switcher"></div>
                            <div class="switcher-button bg-primary"></div>
                        </div>
                        <p class="mb-0 ps-3">Yearly <span class="text-red">(Save 30%)</span></p>
                    </div>
                    <div class="row gy-6 position-relative mt-5">
                        <div class="shape bg-dot primary rellax w-16 h-18" data-rellax-speed="1" style="bottom: -0.5rem; right: -1.6rem;"></div>
                        <div class="shape rounded-circle bg-soft-primary rellax w-18 h-18" data-rellax-speed="1" style="top: -1rem; left: -2rem;"></div>
                        <div class="col-md-6">
                            <div class="pricing card shadow-lg">
                                <div class="card-body pb-12">
                                    <div class="prices text-dark">
                                        <div class="price price-show justify-content-start"><span class="price-currency">$</span><span class="price-value">19</span> <span class="price-duration">mo</span></div>
                                        <div class="price price-hide price-hidden justify-content-start"><span class="price-currency">$</span><span class="price-value">199</span> <span class="price-duration">yr</span></div>
                                    </div>
                                    <!--/.prices -->
                                    <h4 class="card-title mt-2">Premium Plan</h4>
                                    <ul class="icon-list bullet-bg bullet-soft-primary mt-7 mb-8">
                                        <li><i class="uil uil-check"></i><span><strong>5</strong> Projects </span></li>
                                        <li><i class="uil uil-check"></i><span><strong>100K</strong> API Access </span></li>
                                        <li><i class="uil uil-check"></i><span><strong>200MB</strong> Storage </span></li>
                                        <li><i class="uil uil-check"></i><span> Weekly <strong>Reports</strong></span></li>
                                        <li><i class="uil uil-times bullet-soft-red"></i><span> 7/24 <strong>Support</strong></span></li>
                                    </ul>
                                    <a href="#" class="btn btn-primary rounded">Choose Plan</a>
                                </div>
                                <!--/.card-body -->
                            </div>
                            <!--/.pricing -->
                        </div>
                        <!--/column -->
                        <div class="col-md-6 popular">
                            <div class="pricing card shadow-lg">
                                <div class="card-body pb-12">
                                    <div class="prices text-dark">
                                        <div class="price price-show justify-content-start"><span class="price-currency">$</span><span class="price-value">49</span> <span class="price-duration">mo</span></div>
                                        <div class="price price-hide price-hidden justify-content-start"><span class="price-currency">$</span><span class="price-value">499</span> <span class="price-duration">yr</span></div>
                                    </div>
                                    <!--/.prices -->
                                    <h4 class="card-title mt-2">Corporate Plan</h4>
                                    <ul class="icon-list bullet-bg bullet-soft-primary mt-7 mb-8">
                                        <li><i class="uil uil-check"></i><span><strong>20</strong> Projects </span></li>
                                        <li><i class="uil uil-check"></i><span><strong>300K</strong> API Access </span></li>
                                        <li><i class="uil uil-check"></i><span><strong>500MB</strong> Storage </span></li>
                                        <li><i class="uil uil-check"></i><span> Weekly <strong>Reports</strong></span></li>
                                        <li><i class="uil uil-check"></i><span> 7/24 <strong>Support</strong></span></li>
                                    </ul>
                                    <a href="#" class="btn btn-primary rounded">Choose Plan</a>
                                </div>
                                <!--/.card-body -->
                            </div>
                            <!--/.pricing -->
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!--/column -->
            </div>
            <!--/.row -->
            <div class="row gy-10 gy-sm-13 gx-lg-3 align-items-center">
                <div class="col-md-8 col-lg-6 position-relative">
                    <div class="shape bg-dot primary rellax w-17 h-21" data-rellax-speed="1" style="top: -2rem; left: -1.9rem;"></div>
                    <div class="shape rounded bg-soft-primary rellax d-md-block" data-rellax-speed="0" style="bottom: -1.8rem; right: -1.5rem; width: 85%; height: 90%; "></div>
                    <figure class="rounded"><img src="{{ asset('assets/template_1/img/photos/about14.jpg') }}" srcset="{{ asset('assets/template_1/img/photos/about14@2x.jpg 2x') }}" alt="" /></figure>
                </div>
                <!--/column -->
                <div class="col-lg-5 col-xl-4 offset-lg-1">
                    <h2 class="fs-16 text-uppercase text-line text-primary mb-3">Get In Touch</h2>
                    <h2 class="display-4 mb-8">Convinced yet? Let's make something great together.</h2>
                    <div class="d-flex flex-row">
                        <div>
                            <div class="icon text-primary fs-28 me-6 mt-n1"> <i class="uil uil-location-pin-alt"></i> </div>
                        </div>
                        <div>
                            <h5 class="mb-1">Address</h5>
                            <address>Moonshine St. 14/05 Light City, <br class="d-none d-md-block" />London, United Kingdom</address>
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div>
                            <div class="icon text-primary fs-28 me-6 mt-n1"> <i class="uil uil-phone-volume"></i> </div>
                        </div>
                        <div>
                            <h5 class="mb-1">Phone</h5>
                            <p>00 (123) 456 78 90</p>
                        </div>
                    </div>
                    <div class="d-flex flex-row">
                        <div>
                            <div class="icon text-primary fs-28 me-6 mt-n1"> <i class="uil uil-envelope"></i> </div>
                        </div>
                        <div>
                            <h5 class="mb-1">E-mail</h5>
                            <p class="mb-0"><a href="mailto:sandbox@email.com" class="link-body">sandbox@email.com</a></p>
                        </div>
                    </div>
                </div>
                <!--/column -->
            </div>
            <!--/.row -->
        </div>
        <!-- /.container -->
    </section>
    <!-- /section -->
    --}}

<section class="wrapper bg-light" id="block-contact">
    <div class="container py-14 py-md-16">
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="row gx-0">
                        <div class="col-lg-6 align-self-stretch">
                            <div class="map map-full rounded-top rounded-lg-start">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d7448.565778119083!2d105.76177700000001!3d21.021364!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x313454a17ed8509f%3A0x4b6c683c8cfd1226!2zMSBOZ8O1IDE2IMSQ4buXIFh1w6JuIEjhu6NwLCBN4bu5IMSQw6xuaCwgVOG7qyBMacOqbSwgSMOgIE7hu5lpLCBWaWV0bmFt!5e0!3m2!1sen!2sus!4v1690542621204!5m2!1sen!2sus" style="width:100%; height: 100%; border:0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                            <!-- /.map -->
                        </div>
                        <!--/column -->
                        <div class="col-lg-6">
                            <div class="p-10 p-md-11 p-lg-14">
                                <div class="d-flex flex-row">
                                    <div>
                                        <div class="icon text-primary fs-28 me-4 mt-n1"> <i class="uil uil-location-pin-alt"></i> </div>
                                    </div>
                                    <div class="align-self-start justify-content-start">
                                        <h5 class="mb-1">Address</h5>
                                        <address>Số 1 ngõ 16 Đỗ Xuân Hợp, phường Mỹ Đình 1, Nam Từ Liêm, Hà Nội, Việt Nam</address>
                                    </div>
                                </div>
                                <!--/div -->
                                <div class="d-flex flex-row">
                                    <div>
                                        <div class="icon text-primary fs-28 me-4 mt-n1"> <i class="uil uil-phone-volume"></i> </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Phone</h5>
                                        <p>
                                            <a href="tel:0868413333">086 841 3333</a>
                                            <br class="d-none d-md-block" />
                                        </p>
                                    </div>
                                </div>
                                <!--/div -->
                                <div class="d-flex flex-row">
                                    <div>
                                        <div class="icon text-primary fs-28 me-4 mt-n1"> <i class="uil uil-envelope"></i> </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">E-mail</h5>
                                        <p class="mb-0"><a href="mailto:info@phoenix.com" class="link-body">info@phoenix.com</a></p>
                                    </div>
                                </div>
                                <!--/div -->
                            </div>
                            <!--/div -->
                        </div>
                        <!--/column -->
                    </div>
                    <!--/.row -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /column -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</section>
<!-- /section -->



    <div class="modal fade custom-modal" id="showPackinglistModal" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header justify-content-start" style="text-align: left">
                    <div class="d-flex justify-content-between">
                        <div class="left-side">
                            <h4 id="modalTitle" class="title title-up m-0">{{ __('BILL : ') }} </h4>
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
                    <div class="btn btn-primary btn-sm rounded-pill" data-bs-dismiss="modal">
                        CLOSE
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
