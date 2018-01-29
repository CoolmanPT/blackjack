@section('title', 'Blackjack Game')
@include('template._header')

<section  id="frontend">
    <div>
        <router-link to="/game">Lobby</router-link> -
        <router-link to="/statistics">Estatisticas</router-link> -
        <router-link to="/account">Gestão de Conta</router-link> -
        @{{ userName }}, <a v-on:click="logout">Logout</a>
    </div>
    <div>
        <router-view></router-view>
    </div>
</section>

@section('customScripts')
    <script src="{{ URL::asset('js/frontend.js') }}"></script>
@stop
@include('template._footer')