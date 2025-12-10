<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ArticleLocationController extends Controller
{
    public function index()
    {
        return view('articles.location.index');
    }
}
