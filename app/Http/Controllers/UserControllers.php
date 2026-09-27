<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserControllers extends Controller
{
    public function usersList(Request $request)
    {
        $page_title = 'All Users List';
        $search = $request->input('search');

        // Base query
        $query = User::orderBy('status')->orderBy('role')->orderBy('name');

        // Apply search filter if provided
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('wpnumber', 'like', "%{$search}%");
            });
        }

        // Paginate results
        $users = $query->paginate(15);
        $data = compact('users', 'page_title','search');
        return view('admin.users.usersList')->with($data);
    }
    public function managersList()
    {
        $users = User::where('role', 'manager')->orderBy('status')->paginate(15);
        $page_title = 'Managers List';
        $data = compact('users', 'page_title');
        return view('admin.users.usersList')->with($data);
    }
    public function salesmansList()
    {
        $users = User::where('role', 'salesman')->orderBy('status')->paginate(15);
        $page_title = 'Salesmans List';
        $data = compact('users', 'page_title');
        return view('admin.users.usersList')->with($data);
    }
    public function subDealersList()
    {
        $users = User::where('role', 'subdealer')->orderBy('status')->paginate(15);
        $page_title = 'Sub Dealers List';
        $data = compact('users', 'page_title');
        return view('admin.users.usersList')->with($data);
    }
    public function userShow($id)
    {
        $page_title = 'User Details';
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('users.list')->with('error', 'User not found');
        }
        $data = compact('user', 'page_title');
        return view('admin.users.userShow')->with($data);
    }

    public function userDelete($id)
    {
        if (auth()->user()->id == $id) {
            return redirect()->back()->with('fail', 'Admin role can\'t deleted');
        }
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('users.list')->with('error', 'User not found');
        }
        try {
            $user->delete();
            return redirect()->route('users.list')->with('success', 'User deleted successfully');
        } catch (\Throwable $th) {
            //throw $th;
            return redirect()->back()->with('fail', 'User can\'t deleted');
        }
    }

    public function userCreate()
    {
        $url = route('user.store');
        $page_title = 'Create User';
        $data = compact('page_title', 'url');
        return view('admin.users.userForm')->with($data);
    }

    public function userstore(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'phone' => ['required', 'string', 'regex:/^[0-9]{10}$/', 'unique:users'], // 10 digits, only 0-9
            'password' => ['required', 'string', 'min:6',],
            'wpnumber' => ['nullable', 'string', 'regex:/^[0-9]{10}$/', 'unique:users'],
            'role' => ['required', 'in:admin,manager,salesman,seller,subdealer']
        ]);
        User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'wpnumber' => $request->wpnumber,
            'password' => $request->password,
            'role' => $request->role,
        ]);
        return redirect()->route('users.list')->with('success', 'New ' . $request->role . ' created successfully');
    }

    public function userEdit($id)
    {
        $url = route('user.update', $id);
        $page_title = 'Edit User';
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('users.list')->with('error', 'User not found');
        }
        $data = compact('page_title', 'user', 'url');
        return view('admin.users.userForm')->with($data);
    }

    public function userUpdate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:60',
            'phone' => 'required|string|regex:/^[0-9]{10}$/|unique:users,phone,' . $id, // 10 digits, only 0-9
            'wpnumber' => 'nullable|string|regex:/^[0-9]{10}$/|unique:users,wpnumber,' . $id,
            'role' => ['required', 'in:admin,manager,salesman,seller,subdealer'],
        ]);
        $user = User::find($id);
        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'wpnumber' => $request->wpnumber,
        ]);
        return redirect()->route('users.list')->with('success', 'User details updated successfully');
    }

    public function changeStatus($id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('users.list')->with('fail', 'User not found');
        }
        if (auth()->user()->id == $id) {
            return redirect()->back()->with('fail', 'Admin role status can\'t changed');
        }
        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->status = $newStatus;
        $user->save();
        $user->tokens()->delete();
        return redirect()->back()->with('success', 'User status changed to ' . $newStatus);
    }

    public function changePassword(Request $request,$id)
    {
        $request->validate([
            'password' => ['required', 'string', 'min:6'],
        ]);
        $user = User::find($id);
        if (!$user) {
            return redirect()->back()->with('fail', 'User not found');
        }
        $user->password = $request->password;
        $user->save();
        $user->tokens()->delete();
        return redirect()->back()->with('success','Password change success');
    }
}
