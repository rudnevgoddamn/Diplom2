<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Filter;
use App\Models\Game;
use App\Models\Report;
use App\Models\Req;
use App\Models\Team;
use App\Models\User;
use App\Models\UserFilter;
use Carbon\Carbon;

use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use function GuzzleHttp\Promise\all;


class PageController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function signin(){
        return view('sign-in');
    }

}
