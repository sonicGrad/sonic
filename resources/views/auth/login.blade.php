
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8" />
		<title>{{__('cp.Login_Into_Panel')}}</title>
		<meta name="description" content="Login page example" />
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
		
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link href="{{asset('/public/assets/css/pages/login/login-4.css')}}" rel="stylesheet" type="text/css" />
        <link href="{{asset('/public/assets/plugins/global/plugins.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('/public/assets/plugins/custom/prismjs/prismjs.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('/public/assets/css/style.bundle.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('/public/assets/css/themes/layout/header/base/light.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('/public/assets/css/themes/layout/header/menu/light.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('/public/assets/css/themes/layout/brand/dark.css')}}" rel="stylesheet" type="text/css" />
		<link href="{{asset('/public/assets/css/themes/layout/aside/dark.css')}}" rel="stylesheet" type="text/css" />
        <link rel="shortcut icon" href="{{asset('/public/assets/images/sonic.png')}}" />
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="BASE_URL" content="{{ url('/') }}">
	</head>

    <body id="kt_body" class="header-fixed header-mobile-fixed subheader-enabled subheader-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
		<!--begin::Main-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Login-->
			<div class="login login-4 login-signin-on d-flex flex-column flex-lg-row flex-row-fluid bg-white" id="kt_login">
				<!--begin::Aside-->
				<div class="login-aside order-2 order-lg-1 d-flex flex-column-fluid flex-lg-row-auto bgi-size-cover bgi-no-repeat p-7 p-lg-10">
					<!--begin: Aside Container-->
					<div class="d-flex flex-row-fluid flex-column justify-content-between">
						<!--begin::Aside body-->
						<div class="d-flex flex-column-fluid flex-column flex-center mt-5 mt-lg-0">
							<a href="#" class="mb-15 text-center">
								<img src="{{asset('/public/assets/images/sonic.png')}}" class="max-h-75px" alt="" />
							</a>
							<!--begin::Signin-->
							<div class="login-form login-signin">
								<div class="text-center mb-10 mb-lg-20">
									<h2 class="font-weight-bold">{{__('Sign In')}}</h2>
									<p class="text-muted font-weight-bold">{{__('Enter your Phone Number and password')}}</p>
								</div>
							   @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <strong>{{'Error'}}!</strong>{{__('Wrong data entry')}}<br>
                                        <ul class="list-unstyled">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if (session('status'))
                                    <div class="alert alert-success">
                                        {{ session('status') }}
                                    </div>
                                @endif
        
								<!--begin::Form-->
								<form id="login" class="form" role="form" novalidate="novalidate" id="kt_login_signin_form"  action="{{url(app()->getLocale().'/admin/login')}}" method="post">
								    @csrf
									<div class="form-group py-3 m-0">
										<input class="form-control h-auto border-0 px-0 placeholder-dark-75" placeholder="{{__('Mobile Number')}}" name="mobile_no" required autocomplete="off" />
									</div>
									<div class="form-group py-3 border-top m-0">
										<input class="form-control h-auto border-0 px-0 placeholder-dark-75" type="Password" placeholder="{{__('password')}}" required name="password" />
									</div>
									<div class="form-group d-flex flex-wrap justify-content-between align-items-center mt-2">
										<button id="btn-submit" type="submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3">{{__('Sign_in')}}</button>
									</div>
								</form>
								<!--end::Form-->
							</div>
							<!--end::Signin-->
							
						</div>
						<!--end::Aside body-->
						
					</div>
					<!--end: Aside Container-->
				</div>
				<!--begin::Aside-->
				<!--begin::Content-->
				<div class="order-1 order-lg-2 flex-column-auto flex-lg-row-fluid d-flex flex-column p-7" style="background-image: url({{url('public/assets/media/bg/bg-4.jpg')}});">
					<!--begin::Content body-->
					<div class="d-flex flex-column-fluid flex-lg-center">
						<div class="d-flex flex-column justify-content-center">
							<h3 class="display-3 font-weight-bold my-7 text-white">{{__('Welcome Sonic to admin panel')}}</h3>
							{{-- <p class="font-weight-bold font-size-lg text-white opacity-80">The ultimate Bootstrap, Angular 8, React &amp; VueJS admin theme
							<br />framework for next generation web apps.</p> --}}
						</div>
					</div>
					<!--end::Content body-->
				</div>
				<!--end::Content-->
			</div>
			<!--end::Login-->
		</div>
		<!--end::Main-->
		<script>var HOST_URL = "https://keenthemes.com/metronic/tools/preview";</script>
		<!--begin::Global Config(global config for global JS scripts)-->
		{{-- <script>var KTAppSettings = { "breakpoints": { "sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1200 }, "colors": { "theme": { "base": { "white": "#ffffff", "primary": "#3699FF", "secondary": "#E5EAEE", "success": "#1BC5BD", "info": "#8950FC", "warning": "#FFA800", "danger": "#F64E60", "light": "#F3F6F9", "dark": "#212121" }, "light": { "white": "#ffffff", "primary": "#E1F0FF", "secondary": "#ECF0F3", "success": "#C9F7F5", "info": "#EEE5FF", "warning": "#FFF4DE", "danger": "#FFE2E5", "light": "#F3F6F9", "dark": "#D6D6E0" }, "inverse": { "white": "#ffffff", "primary": "#ffffff", "secondary": "#212121", "success": "#ffffff", "info": "#ffffff", "warning": "#ffffff", "danger": "#ffffff", "light": "#464E5F", "dark": "#ffffff" } }, "gray": { "gray-100": "#F3F6F9", "gray-200": "#ECF0F3", "gray-300": "#E5EAEE", "gray-400": "#D6D6E0", "gray-500": "#B5B5C3", "gray-600": "#80808F", "gray-700": "#464E5F", "gray-800": "#1B283F", "gray-900": "#212121" } }, "font-family": "Poppins" };</script> --}}
		<!--end::Global Config-->
		<!--begin::Global Theme Bundle(used by all pages)-->
		<script src="{{asset('/public/assets/plugins/global/plugins.bundle.js')}}"></script>
		<script src="{{asset('/public/assets/plugins/custom/prismjs/prismjs.bundle.js')}}"></script>
		<!--<script src="{{asset('/public/assets/js/scripts.bundle.js')}}"></script>-->
		<!--end::Global Theme Bundle-->
		<!--begin::Page Scripts(used by this page)-->
		{{-- <script src="{{asset('/public/assets/js/pages/custom/login/login-general.js')}}"></script> --}}
		<script src="{{ asset('/resources/js/http.js') }}"></script>
		<script src="{{ asset('/resources/js/login.js') }}"></script>
		<script src="{{ asset('/resources/js/bootstrap.min.js') }}"></script>
		<script src="{{ asset('/resources/js/sweetalert.min.js') }}"></script>
		<!--end::Page Scripts-->
	</body>
	<!--end::Body-->
</html>