<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - INPTIC</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0d6efd;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('/images/inptic.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        /* Overlay sombre */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 0;
        }

        .header-logos, .main-container {
            position: relative;
            z-index: 1;
        }

        .header-logos {
            display: flex;
            justify-content: space-between;
            padding: 1rem 2rem;
        }

        .header-logos img {
            height: 90px;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            border-radius: 12px;
            background: rgba(255,255,255,0.95);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .card-header {
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 1.5rem;
            padding: 1.5rem;
        }

        .form-control {
            border-radius: 8px;
        }

        .btn-primary {
            border-radius: 8px;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <!-- Logos -->
    <div class="header-logos">
        <img src="/logo/logoinptic.png" alt="Logo INPTIC">
        <img src="/logo/Drapeau_du_Gabon.png" alt="Drapeau Gabon">
    </div>

    <!-- Contenu -->
    <div class="main-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">

                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-envelope"></i> Mot de passe oublié
                        </div>

                        <div class="card-body">

                            <p class="text-muted text-center mb-4">
                                Entrez votre adresse email pour recevoir un lien de réinitialisation.
                            </p>

                            <!-- Status -->
                            @if (session('status'))
                                <div class="alert alert-success">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <!-- Formulaire -->
                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <!-- Email -->
                                <div class="mb-3">
                                    <label class="form-label">Adresse e-mail</label>
                                    <input type="email"
                                           name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           required>

                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Bouton -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-send"></i> Envoyer le lien
                                    </button>
                                </div>
                            </form>

                            <!-- Retour login -->
                            <div class="text-center mt-3">
                                <a href="{{ route('login') }}">← Retour à la connexion</a>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

</body>
</html>