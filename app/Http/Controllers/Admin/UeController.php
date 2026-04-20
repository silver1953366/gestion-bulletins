<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ue;
use Illuminate\Http\Request;

class UeController extends Controller
{
    public function index()
    {
        $ues = Ue::paginate(10);
        return view('admin.ues.index', compact('ues'));
    }

    public function create()
    {
        return view('admin.ues.create');
    }

    public function store(Request $request)
    {
        Ue::create($request->all());
        return redirect()->route('admin.ues.index');
    }

    public function show(Ue $ue)
    {
        return view('admin.ues.show', compact('ue'));
    }

    public function edit(Ue $ue)
    {
        return view('admin.ues.edit', compact('ue'));
    }

    public function update(Request $request, Ue $ue)
    {
        $ue->update($request->all());
        return redirect()->route('admin.ues.index');
    }

    public function destroy(Ue $ue)
    {
        $ue->delete();
        return back();
    }
}