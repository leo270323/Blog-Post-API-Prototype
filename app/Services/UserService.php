<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function store(array $userData)
    {
        $userData['password'] = \Illuminate\Support\Facades\Hash::make(\Illuminate\Support\Str::password(12));

        return User::create($userData);
    }

    public function delete(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();
    }

    public function update(string $id, array $userData)
    {
        $user = User::findOrFail($id);
        $user->fill($userData);
        $user->save();

        return $user;
    }

    public function getUserById(string $id)
    {
        return User::findOrFail($id);
    }

    public function getUsersList($request)
    {
        $users = User::select('users.*');
        $name = $request->query('name');
        $email = $request->query('email');
        $limit = $request->query('limit', 3);
        $orderBy = $request->query('order_by', 'id');
        $sortType = $request->query('sort_type', 'desc');

        if (!empty($name)) {
            $users = $users->where('users.name', 'like', '%' . $name . '%');
        }

        if (!empty($email)) {
            $users = $users->where('users.email', 'like', '%' . $email . '%');
        }

        return $users->orderBy($orderBy, $sortType)->paginate($limit)->appends($request->all());
    }
}
