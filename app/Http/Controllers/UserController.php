<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:user-list', ['only' => ['index','show']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }

    /*
    |--------------------------------------------------------------------------
    | User List
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $users = User::with('roles','parent')->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $roles = Role::all();
        $parents = User::all();

        return view('users.create', compact('roles','parents'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:190',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required',
            'parent_id' => 'nullable|exists:users,id'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'parent_id' => $request->parent_id,
            'password' => Hash::make($request->password)
        ]);

        $user->assignRole($request->roles);

        return redirect()->route('users.index')
            ->with('success','کاربر با موفقیت ایجاد شد');
    }

    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    public function edit(User $user)
    {
        $roles = Role::all();
        $parents = User::where('id','!=',$user->id)->get();

        return view('users.edit', compact('user','roles','parents'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:190',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'roles' => 'required',
            'parent_id' => 'nullable|exists:users,id'
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'parent_id' => $request->parent_id
        ];

        if($request->filled('password')){
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        $user->syncRoles($request->roles);

        return redirect()->route('users.index')
            ->with('success','کاربر بروزرسانی شد');
    }

    /*
    |--------------------------------------------------------------------------
    | Delete
    |--------------------------------------------------------------------------
    */

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success','کاربر حذف شد');
    }
}
