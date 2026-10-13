<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
   public function showHello()
    {
        $value = 15;
        $users = ['أحمد', 'محمد', 'زين'];
        
      
        return view('hello', compact('value', 'users'));
    }
}
