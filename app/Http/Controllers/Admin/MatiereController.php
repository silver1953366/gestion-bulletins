<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Matiere;
use Illuminate\Http\Request;

class MatiereController extends Controller
{
    public function index()
    {
        $matieres = Matiere::paginate(10);
        return view('admin.matieres.index', compact('matieres'));
    }

    public function create()
    {
        return view('admin.matieres.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'libelle' => 'required',
            'code' => 'required',
        ]);

        Matiere::create($data);

        return redirect()->route('admin.matieres.index');
    }

    public function show(Matiere $matiere)
    {
        return view('admin.matieres.show', compact('matiere'));
    }

    public function edit(Matiere $matiere)
    {
        return view('admin.matieres.edit', compact('matiere'));
    }

    public function update(Request $request, Matiere $matiere)
    {
        $matiere->update($request->all());
        return redirect()->route('admin.matieres.index');
    }

    public function destroy(Matiere $matiere)
    {
        $matiere->delete();
        return back();
    }
}