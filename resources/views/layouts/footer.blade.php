<footer class="footer footer-black footer-white">
    <div class="container-fluid">
        <div class="row">
            
            <div class="credits ml-auto">
                <span class="copyright">
                    ©
                    <script>
                        document.write(new Date().getFullYear())
                    </script>{{ __(', made with ') }}<i class="fa fa-heart heart"></i>{{ __(' by ') }}<a class="@if(Auth::guest()) text-white @endif" href="#" target="_blank">Phoenix</a>
                </span>
            </div>
        </div>
    </div>
</footer>