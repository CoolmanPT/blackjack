@section('title', 'Administration Page')
@include('template._header')

                <div  id="admin_app">
					@include('template._mainNav')

                    <router-view></router-view>
                </div>

@section('customScripts')
    <script src="{{ asset("js/adminVue.js") }}"></script>
@stop

@include('template._footer')