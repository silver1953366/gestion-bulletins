<?php 
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use Illuminate\Http\Request;

class NiveauController extends Controller
{
    public function index()
    {
    // On récupère les niveaux avec le compte des classes
        $niveaux = Niveau::with('classes')->withCount('classes')->orderBy('code')->get();
    
    // Vérifiez que le nom de la variable dans compact('niveaux') 
    // correspond exactement à $niveaux dans la vue blade.
        return view('admin.niveaux.index', compact('niveaux'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:niveaux,code',
        ]);

        Niveau::create($validated);

        return redirect()->route('admin.niveaux.index')
            ->with('success', 'Échelon académique ajouté au registre.');
    }

    public function update(Request $request, Niveau $niveau)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:10|unique:niveaux,code,' . $niveau->id,
        ]);

        $niveau->update($validated);

        return redirect()->route('admin.niveaux.index')
            ->with('success', 'Informations de l\'échelon mises à jour.');
    }

    public function destroy(Niveau $niveau)
    {
        if ($niveau->classes()->count() > 0) {
            return back()->with('error', 'Action impossible : des classes sont encore liées à ce niveau.');
        }

        $niveau->delete();
        return back()->with('success', 'L\'échelon a été retiré du système.');
    }
}