<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <router-link to="/" class="navbar-brand font-weight-bold text-capitalize"> Blackjack</router-link>
  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item font-weight-bold ">
        <router-link to="/users" class="nav-link"> <i class="fa fa-users fa-align-center fa-fw mr-2"></i>Users</router-link>
      </li>
        <li class="nav-item font-weight-bold ">
            <router-link to="/decks" class="nav-link"> <i class="fa fa-briefcase fa-align-center fa-fw mr-2"></i>Decks</router-link>
      </li>
      
    </ul>
    <ul class="navbar-nav pull-right">
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          @{{ nameOfUser }}
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
          <router-link to="/settings" class="dropdown-item"> Settings</router-link>
          <div class="dropdown-divider"></div>
          <a v-on:click="logout" href="#" class="dropdown-item cursor-pointer">Log Out</a>
        </div>
      </li>
    </ul>
  </div>
</nav>