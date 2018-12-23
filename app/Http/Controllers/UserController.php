<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use Hash;
use DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $data=User::orderBy('id','DESC')->paginate(5);
        return view('users.index',compact('data'))->with('i',($request->input('page',1)-1)*5);
    }

    public function create()
    {
        $role=Role::lists('display_name','id');
        return view('users.create',compact('roles'));
    }

    public function store(Request $request)
    {
        $this->validator($request,[
            'name' => 'required',
            'email' => 'required|email|unique:user,email',
            'password' => 'required|same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password']=Hash::make($input['password']);
        $user=User::create($input);
        foreach($request->input('roles') as $key => $value){
            $user ->attachRole($value);
        }

        return redirect()->route('users.index')->with('succes','User created succesfully');
    }

    public function edit($id)
    {
        $user=User::find($id);
        $roles =Role::lists('display_name','id');
        $userRole = $user->roles->lists('id','id')->toArray();

        return view('users.edit',compact('user','roles','userRoles'));
    }
}
