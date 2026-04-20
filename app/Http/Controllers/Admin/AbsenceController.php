<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absence;
use Illuminate\Http\Request;

class AbsenceController extends Controller
{
    public function index()
    {
        $absences = Absence::paginate(10);
        return view('admin.absences.index', compact('absences'));
    }

    public function create()
    {
        return view('admin.absences.create');
    }

    public function store(Request $request)
    {
        Absence::create($request->all());
        return redirect()->route('admin.absences.index');
    }

    public function show(Absence $absence)
    {
        return view('admin.absences.show', compact('absence'));
    }

    public function edit(Absence $absence)
    {
        return view('admin.absences.edit', compact('absence'));
    }

    public function update(Request $request, Absence $absence)
    {
        $absence->update($request->all());
        return redirect()->route('admin.absences.index');
    }

    public function destroy(Absence $absence)
    {
        $absence->delete();
        return back();
    }
}