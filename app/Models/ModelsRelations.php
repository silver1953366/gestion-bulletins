<?php
/*
DOCUMENTATION DES RELATIONS ENTRE MODÈLES

1. AnneeAcademique
   - hasMany Inscription
   - hasMany ResultatSemestre
   - hasMany ResultatAnnuel
   - hasMany Bulletin

2. Role
   - hasMany User

3. User
   - belongsTo Role
   - hasOne StudentProfile
   - hasOne TeacherProfile
   - hasMany Evaluation (created_by)
   - hasMany Absence (created_by)
   - hasMany ImportNote (created_by)
   - hasMany AuditLog

4. Departement
   - hasMany Filiere

5. Filiere
   - belongsTo Departement
   - hasMany Classe

6. Niveau
   - hasMany Classe

7. Classe
   - belongsTo Filiere
   - belongsTo Niveau
   - hasMany Inscription
   - hasMany Semestre

8. Etudiant
   - hasMany Inscription
   - hasOne StudentProfile
   - hasMany Evaluation
   - hasMany Absence
   - hasMany ResultatMatiere
   - hasMany ResultatUe
   - hasMany ResultatSemestre
   - hasMany ResultatAnnuel
   - hasMany Bulletin

9. Inscription
   - belongsTo Etudiant
   - belongsTo Classe
   - belongsTo AnneeAcademique

10. StudentProfile
    - belongsTo User
    - belongsTo Etudiant

11. TeacherProfile
    - belongsTo User
    - hasMany EnseignantMatiere
    - belongsToMany Matiere (via enseignant_matiere)

12. Semestre
    - belongsTo Classe
    - hasMany Ue
    - hasMany ResultatSemestre

13. Ue
    - belongsTo Semestre
    - hasMany Matiere
    - hasMany ResultatUe

14. Matiere
    - belongsTo Ue
    - hasMany EnseignantMatiere
    - belongsToMany TeacherProfile (via enseignant_matiere)
    - hasMany Evaluation
    - hasMany Absence
    - hasMany ResultatMatiere

15. EnseignantMatiere
    - belongsTo TeacherProfile
    - belongsTo Matiere

16. Evaluation
    - belongsTo Etudiant
    - belongsTo Matiere
    - belongsTo User (created_by)

17. Absence
    - belongsTo Etudiant
    - belongsTo Matiere
    - belongsTo User (created_by)

18. ResultatMatiere
    - belongsTo Etudiant
    - belongsTo Matiere

19. ResultatUe
    - belongsTo Etudiant
    - belongsTo Ue

20. ResultatSemestre
    - belongsTo Etudiant
    - belongsTo Semestre
    - belongsTo AnneeAcademique

21. ResultatAnnuel
    - belongsTo Etudiant
    - belongsTo AnneeAcademique

22. Bulletin
    - belongsTo Etudiant
    - belongsTo AnneeAcademique

23. ImportNote
    - belongsTo User (created_by)

24. Parametre
    - (pas de relations)

25. AuditLog
    - belongsTo User
*/