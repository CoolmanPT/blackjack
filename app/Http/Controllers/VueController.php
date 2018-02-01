<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VueController extends Controller
{

    //ACCESS LOGIN COMPONENTS
    public function login()
    {
        return view('vue.login');
    }

    //ACCESS ADMIN COMPONENTS
    public function admin()
    {
        return view('vue.admin');
    }

    //ACCESS GAME COMPONENTS
    public function game()
    {
        return view('vue.game');
    }
}
