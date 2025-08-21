@extends('client.master')

@section('content')
<div class="container">
    <!-- modal -->
    <div class="modal-dialog modal-dialog-centered modal-sm py-5 py-15">
        <div class="modal-content text-center">
            <div class="modal-body">
                <h2 class="mb-3 text-start">Login to Dashboard</h2>
                <form class="text-start mb-3" method="POST"
                      action="{{ route('login', ['locale' => app()->getLocale()]) }}" id="login-form">
                    @csrf
                    <div class="form-floating mb-4">
                        <input type="email" class="form-control" placeholder="Email" id="email" name="email" required
                               value="{{ old('email') }}" autofocus>
                        <label for="email">Email</label>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="form-floating password-field mb-4">
                        <input type="password" class="form-control" placeholder="Password" id="password" name="password"
                               required>
                        <span class="password-toggle"><i class="uil uil-eye"></i></span>
                        <label for="password">Password</label>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            {{ $message }}
                        </span>
                        @enderror
                    </div>
                    <div class="form-floating password-field mb-4 d-flex justify-content-between align-items-center">
                        <div class="form-check">
                            <input class="form-check-input amt-6" type="checkbox" name="remember" id="remember" {{ old('remember')
                            ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember" style="user-select: none;">{!!
                                __('welcome.remember-me') !!}</label>
                        </div>
                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request', ['locale' => app()->getLocale()]) }}">{!!
                            __('welcome.forgot-password') !!}</a>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill btn-login w-100 mb-2">Login</button>
                </form>
                <!-- /form -->
                <p class="mb-0">Already have an account? <a href="{{ route('register', ['locale' => app()->getLocale()]) }}"
                                                            class="hover">Register now</a></p>
                <div class="divider-icon my-4">or</div>
                <nav class="nav social justify-content-center text-center">
                    <a href="#" class="btn btn-circle btn-sm btn-google"><i class="uil uil-google"></i></a>
                    <a href="#" class="btn btn-circle btn-sm btn-facebook-f"><i class="uil uil-facebook-f"></i></a>
                    <a href="#" class="btn btn-circle btn-sm btn-twitter"><i class="uil uil-twitter"></i></a>
                </nav>
                <!--/.social -->
            </div>
            <!--/.modal-content -->
        </div>
        <!--/.modal-body -->
    </div>
    <!--/.modal-dialog -->
    <!--/.modal -->
</div>
@endsection
