<?php
/**
 * Created by PhpStorm.
 * Author: Wai Yan Aung
 * Date: 6/20/2016
 * Time: 3:56 PM
 */

namespace App\Http\Controllers\Auth;

use App\Core\User\UserRepository;
use App\User;
use Event;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Auth;
use App\Backend\Infrastructure\Forms\LoginFormRequest;
use App\Http\Requests;
use Illuminate\Support\Facades\Lang;
use App\Session;
use App\Core\Check;
use App\Core\Redirect\AceplusRedirect;
use App\Events\UserAction;
use Carbon\Carbon;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/backend';
    protected $guard='User';
    protected $redirectAfterLogout='backend/login';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'doLogout']);
        $this->validSession = Check::validSession();
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:core_users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }

    public function showLogin()
    {
        if(!$this->validSession) {

            return view('auth.login');
        }
        $aplusRedirect = new AceplusRedirect();
        return $aplusRedirect->firstRedirect();
    }

    public function doLogin(LoginFormRequest $request){
        $request->validate();
            $validation = Auth::guard('User')->attempt([
            'user_name'=>$request->user_name,
            'password'=>$request->password,
        ]);
        if(!$validation){
            return redirect()->back()->withErrors($this->getFailedLoginMessage());
        }
        else{

            $id = Auth::guard('User')->id();
            $status = Auth::guard('User')->user()->status;
            $role_id  = Auth::guard('User')->user()->role_id;
            Check::createSession($id);
            $cur_time   = Carbon::now();
            Event::fire(new UserAction('Login', 'user logged in', $id, $cur_time));
            if ($status==2 && $role_id==6) {
              return redirect('/frontend/pending');
            }elseif ($status==3 && $role_id==6){
              return redirect('/frontend/post/'. $id);
            }else{
              return redirect('/backend/userAuth');
            }
        }
    }
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
            ? Lang::get('auth.failed')
            : 'These credentials do not match our records.';
    }

    public function doLogout() //before logout, flush the session data
    {

        $id = Auth::guard('User')->id();
        $cur_time   = Carbon::now();
        Event::fire(new UserAction('Logout', 'user logout', $id, $cur_time));
          session()->flush();
        return redirect('/backend');
    }
}
