<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bulletin;

class BulletinController extends Controller
{
    public function index()
    {
        $bulletins = Bulletin::paginate(10);
        return view('admin.bulletins.index', compact('bulletins'));
    }

    public function show(Bulletin $bulletin)
    {
        return view('admin.bulletins.show', compact('bulletin'));
    }

    public function destroy(Bulletin $bulletin)
    {
        $bulletin->delete();
        return back();
    }
}