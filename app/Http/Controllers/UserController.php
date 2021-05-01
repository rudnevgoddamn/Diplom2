<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;


class UserController extends Controller
{
    public function signup(Request $request){
      $userData = $request->all();

      $validator = Validator::make($userData,[
         'nickname' => 'required|max:10|unique:users',
         'email' => 'required|unique:users|email:rfc,filter',
         'password' => 'required|confirmed|min:6',
         'about' => 'nullable|max:200'
      ]);

      if($validator->fails()) {
          return redirect(route('signup-page'))
              ->withErrors($validator)
              ->withInput();
      }

      $user = new User();
      $user->nickname = $userData['nickname'];
      $user->email = $userData['email'];
      if (isset($userData['about']))
      {
          $user->about = $userData['about'];
      }
      else
          {
              $user->about = 'Пользователь еще не рассказал о себе';
          }
      $user->password = bcrypt($userData['password']);
      if ($request->hasFile('user_picture'))
      {
          $user->picture = $userData['user_picture']->store('img/users');
      }
      if ($user->nickname == 'admin')
      {
          $user->role = 2;
      }

      $user->save();

      return redirect(route('login-page'));
    }
    public function login(Request $request)
    {
        $userInfo=$request->only('email','password');

        $validator= Validator::make($userInfo, [
            'email'=>'required|email:rfc,filter',
            'password'=>'required',
        ]);

        if ($validator->fails())
        {
            return redirect(route('login-page'))
                ->withErrors($validator)
                ->withInput();
        }

        if (Auth::attempt($userInfo)) {
            Auth::user()->status = 'Онлайн';
            Auth::user()->save();
            return redirect('/');
        }

        return redirect(route('login-page'))
            ->withErrors(['auth_error'=>'Email или пароль введены некорректно'])
            ->withInput();
    }
    public function logout() {
        Auth::user()->status = 'Оффлайн';
        Auth::user()->save();
        Auth::logout();
        return redirect('/');
    }
    public function edit(Request $request){
        $user = Auth::user();
        $userData = $request->all();

        $validator = Validator::make($userData,[
            'email' => [
            'email:rfc,dns',
            Rule::unique('users')->ignore($user->id),
        ],
            'nickname' => ['max:20',
                Rule::unique('users')->ignore($user->id)],
            'about' => 'nullable|max:200',
            'password' => 'nullable|min:6',
            'user_picture' => 'mimes:jpeg,png'

        ]);

        if($validator->fails()) {
            return redirect(route('edit-page'))
                ->withErrors($validator)
                ->withInput();
        }

        $user->nickname = $userData['nickname'];
        $user->email = $userData['email'];
        $user->about = $userData['about'];

        if (isset($userData['password'])) {
            $user->password = bcrypt($userData['password']);
        }

        if (isset($userData['user_picture']))
        $user->picture = $userData['user_picture']->store('img/users');

        $user->save();

        return redirect(route('main-page'));
    }

    public function checkFilter($id)
    {
        $userFilter = Game::findOrFail($id)->userFilters->where('user_id', '=', Auth::user()->id)->first();

        if (!isset($userFilter))
        {
            return redirect(route('setFilter-page', ['id' => $id]));
        }
        else
            {
                Auth::user()->status = Game::findOrFail($id)->name;
                Auth::user()->save();
                return redirect()->back();
            }
    }

    public function setFilter(Request $request)
    {
        $formData = $request->all();

        $game = Game::findOrFail($formData['game_id']);


        $validator= Validator::make($formData, [
            'filter'=>'required',
            'comment'=>'required|max:150',
        ]);

        if ($validator->fails())
        {
            return redirect(route('setFilter-page', ['id' => $game->id]))
                ->withErrors($validator)
                ->withInput();
        }


        $userFilter = new UserFilter();
        $userFilter->user_id = Auth::user()->id;
        $userFilter->game_id = $game->id;
        $userFilter->filter_id = $formData['filter'];
        $userFilter->comment = $formData['comment'];
        $userFilter->save();

        $favorite = $game->favorites->where('user_id', '=', Auth::user()->id);

        if ($favorite->isEmpty())
        {
            $fav = new Favorite();
            $fav->game_id=$game->id;
            $fav->user_id=Auth::user()->id;
            $fav->save();
        }

        Auth::user()->status = $game->name;
        Auth::user()->save();

        return redirect('/');
    }

    public function deleteFilter($id)
    {
        DB::table('userfilters')->where('id' ,'=', $id)->delete();
        return redirect()->back();
    }

    public function editFilter(Request $request)
    {
        $formData = $request->all();
        $userFilter = UserFilter::findOrFail($formData['userFilter_id']);
        $filter_id = Filter::where('value', '=', $formData['filter'])->where('game_id', '=', $userFilter['game_id'])->first()->id;

        $validator= Validator::make($formData, [
            'filter'=>'required',
            'comment'=>'required|max:150',
        ]);

        if ($validator->fails())
        {
            return redirect(route('editFilter-page'))
                ->withErrors($validator)
                ->withInput();
        }

        $userFilter->filter_id = $filter_id;
        $userFilter->comment = $formData['comment'];
        $userFilter->save();

        return redirect(route('profile'));
    }

    public function createTeam(Request $request)
    {
        $teamInfo = $request->all();

        $validator= Validator::make($teamInfo, [
            'team_picture' => 'nullable|mimes:jpeg,png',
            'name' => 'required|max:12|unique:teams',
            'about' => 'required|max:150',
            'games' => 'required',
        ]);

        if ($validator->fails())
        {
            return redirect(route('createTeam-page'))
                ->withErrors($validator)
                ->withInput();
        }

        $team = new Team();
        $team->leader_id = Auth::user()->id;
        if ($request->hasFile('team_picture')) {
            $team->picture = $teamInfo['team_picture']->store('img/teams');
        }
        else{
            $team->picture = 'img/teams/team-default.jpeg';
        }
        $team->name = $teamInfo['name'];
        $team->about = $teamInfo['about'];
        $team->save();

        foreach ($teamInfo['games'] as $game)
        {
            $teamGame = new TeamGame();
            $teamGame->team_id = $team->id;
            $teamGame->game_id = $game;
            $teamGame->save();
        }

        $teammember = new TeamMember();
        $teammember->user_id = Auth::user()->id;
        $teammember->team_id = $team->id;

        $teammember->save();

        return redirect(route('social-page'));
    }

    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);
        foreach ($team->teamMembers as $teamMember)
        {
            DB::table('teammembers')->where('team_id', '=', $id)->delete();
        }

        foreach ($team->teamGames as $teamGame)
        {
            DB::table('teamgames')->where('team_id', '=', $id)->delete();
        }

        DB::table('teams')->where('id', '=', $id)->delete();

        return redirect()->back();
    }

    public function editTeam(Request $request)
    {
        $teamInfo = $request->all();

        $team = Team::findOrFail($teamInfo['team_id']);

        $validator= Validator::make($teamInfo, [
            'team_picture' => 'nullable|mimes:jpeg,png',
            'name' => ['required','max:12',Rule::unique('teams')->ignore($team->id)],
            'about' => 'required|max:150',
            'games' => 'required',
        ]);

        if ($validator->fails())
        {
            return redirect(route('editTeam-page', ['id' => $team->id]))->withErrors($validator);
        }

        $team = Team::findOrFail($teamInfo['team_id']);

        foreach ($team->teamGames as $teamGame)
        {
            DB::table('teamgames')->where('team_id', '=', $team->id)->delete();
        }

        foreach ($teamInfo['games'] as $game)
        {
            $teamGame = new TeamGame();
            $teamGame->team_id = $team->id;
            $teamGame->game_id = $game;
            $teamGame->save();
        }

        if (isset($teamInfo['team_picture'])) {
            $team->picture = $teamInfo['team_picture']->store('img/teams');
        }

        $team->name = $teamInfo['name'];
        $team->about = $teamInfo['about'];
        $team->save();

        return redirect(route('social-page'));
    }

    public function sendRequest(Request $request)
    {
        $reqInfo = $request->all();
        $req = new Req();
        $user = Auth::user();



        $validator= Validator::make($reqInfo, [
            'comment' => 'nullable|max:150'
        ]);

        if ($validator->fails())
        {
            return redirect(route('createTeam-page'))
                ->withErrors($validator)
                ->withInput();
        }

        $req->user_id = $user->id;
        $req->team_id = $reqInfo['team_id'];
        if (isset($reqInfo['comment'])) {
            $req->comment = $reqInfo['comment'];
        }
        else{$req->comment = 'Пользователь не оставил комментария';}
        $req->status = 'Отправлен';

        $req->save();

        return redirect(route('social-page'));
    }

    public function leaveTeam($id)
    {
        $user = Auth::user();

        DB::table('teammembers')->where('team_id', '=', $id)->where('user_id', '=', $user->id)->delete();

        return redirect()->back();
    }

    public function deleteRequest($id)
    {
        DB::table('requests')->where('id', '=', $id)->delete();
        return redirect()->back();
    }

    public function acceptRequest($id)
    {
        $req = Req::findOrFail($id);

        $req->status = 'Запрос одобрен!';
        $req->save();

        $newMember = new TeamMember();
        $newMember->user_id = $req->user_id;
        $newMember->team_id = $req->team_id;

        $newMember->save();

        return redirect()->back();
    }

    public function declineRequest($id)
    {
        $req = Req::findOrFail($id);
        $req->status = 'Запрос отклонен!';
        $req->save();

        return redirect()->back();
    }

    public function kickMember($id)
    {
        DB::table('teammembers')->where('id', '=', $id)->delete();

        return redirect()->back();
    }

    public function sendReport(Request $request)
    {
        $reportData = $request->all();

        $report = new Report();

        $validator = Validator::make($reportData,
            ['comment' => 'required|max:150',]);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator);
        }

        $report->user_id = $reportData['user_id'];
        $report->author_id = Auth::user()->id;
        $report->comment = $reportData['comment'];

        $report->save();

        return redirect('/');
    }

    public function rejectReport($id)
    {
        DB::table('reports')->where('id', '=', $id)->delete();

        return redirect()->back();
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);

        #favorites
        DB::table('favorites')->where('user_id', $id)->delete();
        #userFilters
        DB::table('userfilters')->where('user_id', $id)->delete();
        #teams
        foreach ($user->teams as $team) {
            DB::table('teammembers')->where('team_id', $team->id)->delete();
            DB::table('teamgames')->where('team_id', $team->id)->delete();
            DB::table('requests')->where('team_id', $team->id)->delete();
        }
        DB::table('teams')->where('leader_id', $id)->delete();
        #comments
        DB::table('comments')->where('author_id', $id)->delete();
        DB::table('comments')->where('profile_id', $id)->delete();
        #report
        DB::table('reports')->where('user_id', $id)->delete();
        #user as himself
        DB::table('users')->where('id', $id)->delete();
        return redirect()->back();
    }

    public function sendComment(Request $request)
    {
        $commentData = $request->all();

        $validator = Validator::make($commentData,
            ['text' => 'required|max:150',]);

        if ($validator->fails())
        {
            return redirect()->back()->withErrors($validator);
        }

        $comment = new Comment();
        $comment->author_id = Auth::user()->id;
        $comment->profile_id = $commentData['profile_id'];
        $comment->text = $commentData['text'];

        $comment->save();

        return redirect(route('profile', ['id' => $commentData['profile_id']]));
    }

    public function searchUsers(Request $request)
    {
        $search = $request->all();


        $userFilters = UserFilter::where('game_id', '=', $search['game_id'])->get();

        return view('index', ['userFilters' => $userFilters]);
    }

    public function searchTeams(Request $request)
    {
        $search = $request->all();


        $teamGames = TeamGame::where('game_id', '=', $search['game_id'])->get();

        return view('index', ['teamResult' => $teamGames]);
    }

    public function searchGames(Request $request)
    {
        $search = $request->all();
        $validator = Validator::make($search, ['name' => 'required']);
        if($validator->fails())
        {
            return redirect()->back();
        }
        $like = $search['name'];
        if (Game::where('name', 'LIKE', "%{$like}%")->first() != null)
        {
            $result = Game::where('name', 'LIKE', "%{$like}%")->first();
            return redirect(route('game', ['id' => $result->id]));
        }
        else
        {
            return redirect()->back();
        }
    }

    public function resetPassword(Request $request)
    {
        $userInfo = $request->all();

        $user = User::where('email', $userInfo['email'])->first();

        $new_password = Str::random(10);

        Mail::send();
    }

}
