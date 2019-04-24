<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxwebController extends Controller
{
    public function hd(){
   	  dd($_GET['redirect_uri']);
    }
}
