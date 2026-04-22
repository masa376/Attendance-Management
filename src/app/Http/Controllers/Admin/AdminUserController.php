<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    public function list()
    {
        // role=0（一般ユーザー）のみ取得
        $users = User::where('role', 0)
            ->orderBy('id', 'asc')
            ->get();

        return view('admin.staff.list', [
            'users' => $users,
        ]);
    }
}
