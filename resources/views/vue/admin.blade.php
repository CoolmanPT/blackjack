@section('title', 'Administration Page')
@include('template._header')
@include('template._mainNav')

                <section  id="admin_app">
                    <router-view></router-view>
                </section>

@section('customScripts')
    <script src="{{ asset("js/adminVue.js") }}"></script>
@stop
@include('template._footer')