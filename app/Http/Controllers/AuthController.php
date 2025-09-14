<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|same:confirmed_password',
        ]);

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()->all()]);
        }

        //CREATE USER LOGIC HERE

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);


        \Log::INFO($user);

        $input['name'] = $user->name;
        $input['email'] = $user->email;
        $input['token'] = $user->createToken('App')->plainTextToken;

        return response()->json($input);
    }

    public function login(Request $request)
    {
        if(!Auth::attempt($request->only('email', 'password'))){
            return response()->json(['errors' => ['The provided credentials are incorrect.']]);
        }

        //LOGIN USER LOGIC HERE

        $user = Auth::user();

        $input['name'] = $user->name;
        $input['email'] = $user->email;
        $input['token'] = $user->createToken('App')->plainTextToken;

        return response()->json($input);
    }

    public function profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return response()->json(["errors" => $validator->errors()->all()]);
        }

        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;

        if($request->password){
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        $input['name'] = $user->name;
        $input['email'] = $user->email;

        return response()->json($input);
    }
}
