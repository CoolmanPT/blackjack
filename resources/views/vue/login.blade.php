@section('title', 'Login Page')
@include('template._header')

                <section  id="login_app">
                    <router-view></router-view>
                </section>

@section('customScripts')
    <script src="{{ asset("js/loginVue.js") }}"></script>
@stop
@include('template._footer')
