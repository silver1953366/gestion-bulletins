<?php
// database/seeders/TestDataSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Matiere;
use App\Models\Classe;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Departement;
use App\Models\Semestre;
use App\Models\Ue;
use App\Models\Inscription;
use App\Models\Evaluation;
use App\Models\TeacherProfile;
use App\Models\EnseignantMatiere;
use App\Models\Absence;
use App\Models\AnneeAcademique;
use App\Models\ResultatMatiere;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Créer les rôles
        $this->createRoles();
        
        // 2. Créer les utilisateurs
        $this->createUsers();
        
        // 3. Créer la structure académique (Départements, Filières, Niveaux)
        $this->createAcademicStructure();
        
        // 4. Créer les classes
        $this->createClasses();
        
        // 5. Créer les semestres
        $this->createSemestres();
        
        // 6. Créer les UE et Matières
        $this->createUeAndMatieres();
        
        // 7. Créer les étudiants
        $this->createEtudiants();
        
        // 8. Inscrire les étudiants
        $this->createInscriptions();
        
        // 9. Créer les profils enseignants
        $this->createTeacherProfiles();
        
        // 10. Assigner les matières aux enseignants
        $this->assignMatieresToEnseignants();
        
        // 11. Créer une année académique active
        $this->createAnneeAcademique();
        
        // 12. Créer des évaluations (notes)
        $this->createEvaluations();
        
        // 13. Créer des absences
        $this->createAbsences();
        
        // 14. Calculer les résultats par matière
        $this->calculateResultatsMatieres();
        
        $this->command->info('✅ Base de données remplie avec succès !');
    }

    private function createRoles(): void
    {
        $roles = ['admin', 'enseignant', 's.pedagogique', 'etudiant'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['nom' => $role]);
        }
        $this->command->info('Rôles créés');
    }

    private function createUsers(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@inptic.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'System',
                'password' => Hash::make('password'),
                'role_id' => Role::where('nom', 'admin')->first()->id,
            ]
        );

        // Secrétariat pédagogique
        User::updateOrCreate(
            ['email' => 'secretariat@inptic.com'],
            [
                'first_name' => 'Marie',
                'last_name' => 'Claire',
                'password' => Hash::make('password'),
                'role_id' => Role::where('nom', 's.pedagogique')->first()->id,
            ]
        );

        // Enseignants
        $enseignants = [
            ['first_name' => 'Jean', 'last_name' => 'Dupont', 'email' => 'jean.dupont@inptic.com', 'specialite' => 'Informatique', 'grade' => 'Maître de Conférences'],
            ['first_name' => 'Marie', 'last_name' => 'Laurent', 'email' => 'marie.laurent@inptic.com', 'specialite' => 'Réseaux', 'grade' => 'Professeur'],
            ['first_name' => 'Pierre', 'last_name' => 'Martin', 'email' => 'pierre.martin@inptic.com', 'specialite' => 'Systèmes Embarqués', 'grade' => 'Assistant'],
            ['first_name' => 'Sophie', 'last_name' => 'Bernard', 'email' => 'sophie.bernard@inptic.com', 'specialite' => 'Sécurité', 'grade' => 'Maître de Conférences'],
        ];

        $roleEnseignant = Role::where('nom', 'enseignant')->first();
        foreach ($enseignants as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'password' => Hash::make('password'),
                    'role_id' => $roleEnseignant->id,
                ]
            );
        }
        $this->command->info('Utilisateurs créés');
    }

    private function createAcademicStructure(): void
    {
        // Départements
        $departements = ['Informatique', 'Réseaux et Télécommunications', 'Génie Logiciel'];
        foreach ($departements as $dept) {
            Departement::firstOrCreate(['nom' => $dept]);
        }

        // Filieres
        $departementInfo = Departement::where('nom', 'Informatique')->first();
        $filieres = [
            ['nom' => 'Licence Professionnelle ASUR', 'departement_id' => $departementInfo->id],
            ['nom' => 'Master MIAGE', 'departement_id' => $departementInfo->id],
            ['nom' => 'Licence Informatique', 'departement_id' => $departementInfo->id],
        ];
        
        foreach ($filieres as $filiere) {
            Filiere::firstOrCreate(['nom' => $filiere['nom']], $filiere);
        }

        // Niveaux
        $niveaux = ['L1', 'L2', 'L3', 'M1', 'M2'];
        foreach ($niveaux as $code) {
            Niveau::firstOrCreate(['code' => $code]);
        }
        $this->command->info('Structure académique créée');
    }

    private function createClasses(): void
    {
        $filiereAsur = Filiere::where('nom', 'Licence Professionnelle ASUR')->first();
        $niveauL3 = Niveau::where('code', 'L3')->first();
        
        $classes = [
            ['nom' => 'ASUR 1', 'filiere_id' => $filiereAsur->id, 'niveau_id' => $niveauL3->id],
            ['nom' => 'ASUR 2', 'filiere_id' => $filiereAsur->id, 'niveau_id' => $niveauL3->id],
        ];
        
        foreach ($classes as $classe) {
            Classe::firstOrCreate(['nom' => $classe['nom']], $classe);
        }
        $this->command->info('Classes créées');
    }

    private function createSemestres(): void
    {
        $classeAsur1 = Classe::where('nom', 'ASUR 1')->first();
        $classeAsur2 = Classe::where('nom', 'ASUR 2')->first();
        
        $semestres = [
            ['libelle' => 'S5', 'classe_id' => $classeAsur1->id],
            ['libelle' => 'S6', 'classe_id' => $classeAsur2->id],
        ];
        
        foreach ($semestres as $semestre) {
            Semestre::firstOrCreate(['libelle' => $semestre['libelle'], 'classe_id' => $semestre['classe_id']], $semestre);
        }
        $this->command->info('Semestres créés');
    }

    private function createUeAndMatieres(): void
    {
        $semestreS5 = Semestre::where('libelle', 'S5')->first();
        $semestreS6 = Semestre::where('libelle', 'S6')->first();
        
        // UEs pour S5
        $uesS5 = [
            ['code' => 'UE101', 'libelle' => 'Fondamentaux du Développement Web', 'semestre_id' => $semestreS5->id, 'coefficient' => 2, 'credits' => 6],
            ['code' => 'UE102', 'libelle' => 'Bases de Données Avancées', 'semestre_id' => $semestreS5->id, 'coefficient' => 2, 'credits' => 6],
            ['code' => 'UE103', 'libelle' => 'Programmation Orientée Objet', 'semestre_id' => $semestreS5->id, 'coefficient' => 2, 'credits' => 6],
            ['code' => 'UE104', 'libelle' => 'Anglais Technique', 'semestre_id' => $semestreS5->id, 'coefficient' => 1, 'credits' => 3],
        ];
        
        // UEs pour S6
        $uesS6 = [
            ['code' => 'UE201', 'libelle' => 'Frameworks JavaScript', 'semestre_id' => $semestreS6->id, 'coefficient' => 2, 'credits' => 6],
            ['code' => 'UE202', 'libelle' => 'Sécurité des Applications Web', 'semestre_id' => $semestreS6->id, 'coefficient' => 2, 'credits' => 6],
            ['code' => 'UE203', 'libelle' => 'Projet Professionnel', 'semestre_id' => $semestreS6->id, 'coefficient' => 2, 'credits' => 6],
            ['code' => 'UE204', 'libelle' => 'Communication', 'semestre_id' => $semestreS6->id, 'coefficient' => 1, 'credits' => 3],
        ];
        
        foreach ($uesS5 as $ue) {
            $createdUe = Ue::firstOrCreate(['code' => $ue['code']], $ue);
            $this->createMatieresForUe($createdUe);
        }
        
        foreach ($uesS6 as $ue) {
            $createdUe = Ue::firstOrCreate(['code' => $ue['code']], $ue);
            $this->createMatieresForUe($createdUe);
        }
        $this->command->info('UE et Matières créées');
    }

    private function createMatieresForUe($ue): void
    {
        $matieresByUe = [
            'UE101' => [
                ['code' => 'INF101', 'libelle' => 'HTML/CSS', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'INF102', 'libelle' => 'JavaScript', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'INF103', 'libelle' => 'PHP', 'coefficient' => 2, 'credits' => 2],
            ],
            'UE102' => [
                ['code' => 'BD101', 'libelle' => 'MySQL', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'BD102', 'libelle' => 'Optimisation SQL', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'BD103', 'libelle' => 'NoSQL', 'coefficient' => 2, 'credits' => 2],
            ],
            'UE103' => [
                ['code' => 'POO101', 'libelle' => 'Java', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'POO102', 'libelle' => 'Design Patterns', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'POO103', 'libelle' => 'UML', 'coefficient' => 2, 'credits' => 2],
            ],
            'UE104' => [
                ['code' => 'ANG101', 'libelle' => 'Anglais Technique', 'coefficient' => 2, 'credits' => 3],
            ],
            'UE201' => [
                ['code' => 'JS101', 'libelle' => 'React.js', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'JS102', 'libelle' => 'Vue.js', 'coefficient' => 2, 'credits' => 2],
                ['code' => 'JS103', 'libelle' => 'Node.js', 'coefficient' => 2, 'credits' => 2],
            ],
            'UE202' => [
                ['code' => 'SEC101', 'libelle' => 'Sécurité Web', 'coefficient' => 2, 'credits' => 3],
                ['code' => 'SEC102', 'libelle' => 'Cryptographie', 'coefficient' => 2, 'credits' => 3],
            ],
            'UE203' => [
                ['code' => 'PROJ101', 'libelle' => 'Projet Web', 'coefficient' => 3, 'credits' => 6],
            ],
            'UE204' => [
                ['code' => 'COM101', 'libelle' => 'Communication', 'coefficient' => 2, 'credits' => 3],
            ],
        ];
        
        $matieres = $matieresByUe[$ue->code] ?? [];
        foreach ($matieres as $matiere) {
            Matiere::firstOrCreate(
                ['code' => $matiere['code']],
                [
                    'libelle' => $matiere['libelle'],
                    'coefficient' => $matiere['coefficient'],
                    'credits' => $matiere['credits'],
                    'ue_id' => $ue->id,
                ]
            );
        }
    }

    private function createEtudiants(): void
    {
        $etudiants = [
            ['nom' => 'Minko', 'prenom' => 'Marc', 'date_naissance' => '2000-05-15', 'lieu_naissance' => 'Libreville', 'bac' => 'Série C', 'provenance' => 'Lycée National'],
            ['nom' => 'Ndong', 'prenom' => 'Alice', 'date_naissance' => '2001-08-22', 'lieu_naissance' => 'Port-Gentil', 'bac' => 'Série C', 'provenance' => 'Lycée Pierre'],
            ['nom' => 'Moussavou', 'prenom' => 'Thomas', 'date_naissance' => '1999-12-10', 'lieu_naissance' => 'Franceville', 'bac' => 'Série D', 'provenance' => 'Collège Saint-Jean'],
            ['nom' => 'Nguema', 'prenom' => 'Sarah', 'date_naissance' => '2000-03-03', 'lieu_naissance' => 'Oyem', 'bac' => 'Série C', 'provenance' => 'Lycée d\'Oyem'],
            ['nom' => 'Bekale', 'prenom' => 'Kevin', 'date_naissance' => '2001-07-18', 'lieu_naissance' => 'Libreville', 'bac' => 'Série C', 'provenance' => 'Lycée Français'],
            ['nom' => 'Mouity', 'prenom' => 'Laura', 'date_naissance' => '2000-11-25', 'lieu_naissance' => 'Port-Gentil', 'bac' => 'Série D', 'provenance' => 'Lycée Nelson'],
            ['nom' => 'Obiang', 'prenom' => 'David', 'date_naissance' => '1999-09-30', 'lieu_naissance' => 'Libreville', 'bac' => 'Série C', 'provenance' => 'Lycée Augustin'],
            ['nom' => 'Mbadinga', 'prenom' => 'Esther', 'date_naissance' => '2001-02-14', 'lieu_naissance' => 'Mouila', 'bac' => 'Série C', 'provenance' => 'Lycée de Mouila'],
        ];
        
        foreach ($etudiants as $data) {
            Etudiant::firstOrCreate(
                ['nom' => $data['nom'], 'prenom' => $data['prenom']],
                $data
            );
        }
        $this->command->info('Étudiants créés');
    }

    private function createInscriptions(): void
    {
        $classeAsur1 = Classe::where('nom', 'ASUR 1')->first();
        $classeAsur2 = Classe::where('nom', 'ASUR 2')->first();
        $etudiants = Etudiant::all();
        $anneeActive = AnneeAcademique::where('active', true)->first();
        
        if (!$anneeActive) {
            $anneeActive = AnneeAcademique::create(['libelle' => '2024-2025', 'active' => true]);
        }
        
        // Inscrire les 4 premiers étudiants en ASUR 1 (S5) - SANS matricule
        foreach ($etudiants->take(4) as $index => $etudiant) {
            Inscription::firstOrCreate(
                [
                    'etudiant_id' => $etudiant->id, 
                    'classe_id' => $classeAsur1->id,
                    'annee_academique_id' => $anneeActive->id
                ],
                [
                    'statut' => 'inscrit',
                ]
            );
        }
        
        // Inscrire les 4 derniers étudiants en ASUR 2 (S6)
        foreach ($etudiants->skip(4) as $index => $etudiant) {
            Inscription::firstOrCreate(
                [
                    'etudiant_id' => $etudiant->id, 
                    'classe_id' => $classeAsur2->id,
                    'annee_academique_id' => $anneeActive->id
                ],
                [
                    'statut' => 'inscrit',
                ]
            );
        }
        $this->command->info('Inscriptions créées');
    }

    private function createTeacherProfiles(): void
    {
        $enseignants = User::where('role_id', Role::where('nom', 'enseignant')->first()->id)->get();
        $specialites = ['Informatique', 'Réseaux', 'Systèmes Embarqués', 'Sécurité'];
        $grades = ['Assistant', 'Maître de Conférences', 'Professeur'];
        
        foreach ($enseignants as $index => $user) {
            TeacherProfile::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialite' => $specialites[$index % count($specialites)],
                    'grade' => $grades[$index % count($grades)],
                ]
            );
        }
        $this->command->info('Profils enseignants créés');
    }

    private function assignMatieresToEnseignants(): void
    {
        $enseignants = TeacherProfile::all();
        $matieres = Matiere::all();
        
        foreach ($enseignants as $index => $enseignant) {
            // Assigner 3-4 matières par enseignant
            $matieresToAssign = $matieres->skip($index * 3)->take(3);
            foreach ($matieresToAssign as $matiere) {
                EnseignantMatiere::firstOrCreate([
                    'teacher_profile_id' => $enseignant->id,
                    'matiere_id' => $matiere->id,
                ]);
            }
        }
        $this->command->info('Matières assignées aux enseignants');
    }

    private function createAnneeAcademique(): void
    {
        AnneeAcademique::updateOrCreate(
            ['libelle' => '2024-2025'],
            ['active' => true]
        );
        $this->command->info('Année académique active créée');
    }

    private function createEvaluations(): void
    {
        $etudiants = Etudiant::all();
        $matieres = Matiere::all();
        $enseignant = User::where('role_id', Role::where('nom', 'enseignant')->first()->id)->first();
        
        foreach ($etudiants as $etudiant) {
            foreach ($matieres->random(min(8, $matieres->count())) as $matiere) {
                // CC (Contrôle Continu)
                Evaluation::firstOrCreate([
                    'etudiant_id' => $etudiant->id,
                    'matiere_id' => $matiere->id,
                    'type' => 'CC'
                ], [
                    'note' => rand(8, 18) + rand(0, 100) / 100,
                    'created_by' => $enseignant->id,
                ]);
                
                // Examen
                Evaluation::firstOrCreate([
                    'etudiant_id' => $etudiant->id,
                    'matiere_id' => $matiere->id,
                    'type' => 'EXAMEN'
                ], [
                    'note' => rand(6, 16) + rand(0, 100) / 100,
                    'created_by' => $enseignant->id,
                ]);
                
                // Parfois Rattrapage (30% des cas)
                if (rand(1, 100) <= 30) {
                    Evaluation::firstOrCreate([
                        'etudiant_id' => $etudiant->id,
                        'matiere_id' => $matiere->id,
                        'type' => 'RATTRAPAGE'
                    ], [
                        'note' => rand(10, 15) + rand(0, 100) / 100,
                        'created_by' => $enseignant->id,
                    ]);
                }
            }
        }
        $this->command->info('Évaluations créées');
    }

    private function calculateResultatsMatieres(): void
    {
        $etudiants = Etudiant::all();
        $matieres = Matiere::all();
        
        foreach ($etudiants as $etudiant) {
            foreach ($matieres as $matiere) {
                $cc = Evaluation::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiere->id)
                    ->where('type', 'CC')
                    ->value('note');
                    
                $examen = Evaluation::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiere->id)
                    ->where('type', 'EXAMEN')
                    ->value('note');
                
                $rattrapage = Evaluation::where('etudiant_id', $etudiant->id)
                    ->where('matiere_id', $matiere->id)
                    ->where('type', 'RATTRAPAGE')
                    ->value('note');
                
                $moyenne = null;
                if ($rattrapage) {
                    $moyenne = $rattrapage;
                } elseif ($cc || $examen) {
                    $notes = 0;
                    $ponderation = 0;
                    if ($cc) {
                        $notes += $cc * 0.4;
                        $ponderation += 0.4;
                    }
                    if ($examen) {
                        $notes += $examen * 0.6;
                        $ponderation += 0.6;
                    }
                    if ($ponderation > 0) {
                        $moyenne = round($notes / $ponderation, 2);
                    }
                }
                
                if ($moyenne) {
                    ResultatMatiere::updateOrCreate(
                        [
                            'etudiant_id' => $etudiant->id,
                            'matiere_id' => $matiere->id,
                        ],
                        [
                            'moyenne' => $moyenne,
                            'utilise_rattrapage' => $rattrapage ? true : false,
                        ]
                    );
                }
            }
        }
        $this->command->info('Résultats matières calculés');
    }

    private function createAbsences(): void
    {
        $etudiants = Etudiant::all();
        $matieres = Matiere::all();
        $enseignant = User::where('role_id', Role::where('nom', 'enseignant')->first()->id)->first();
        
        foreach ($etudiants as $etudiant) {
            // Créer quelques absences aléatoires
            $nbAbsences = rand(0, 5);
            for ($i = 0; $i < $nbAbsences; $i++) {
                Absence::create([
                    'etudiant_id' => $etudiant->id,
                    'matiere_id' => $matieres->random()->id,
                    'heures' => rand(1, 4),
                    'justification' => rand(1, 100) <= 50 ? 'Maladie' : null,
                    'created_by' => $enseignant->id,
                    'created_at' => now()->subDays(rand(1, 60)),
                ]);
            }
        }
        $this->command->info('Absences créées');
    }
}