<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - INPTIC</title>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
   <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --accent-color: #198754;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --card-hover-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            /* Image de fond */
            background-image: url('/images/inptic.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
        }

        /* Overlay sombre pour améliorer la lisibilité du formulaire */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 0;
        }

        /* Contenu principal au-dessus de l'overlay */
        .login-container, .header-logos {
            position: relative;
            z-index: 1;
        }

        /* Conteneur des logos en haut */
        .header-logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background: transparent;
        }

        .logo-inptic img, .flag-gabon img {
            height: 90px;  /* Augmenté de 60px à 90px */
            width: auto;
            transition: transform 0.3s ease;
        }

        .logo-inptic img:hover, .flag-gabon img:hover {
            transform: scale(1.05);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(2px);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0a58ca 100%);
            border-bottom: none;
            padding: 1.5rem;
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #e1e5e9;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15);
            border-color: var(--primary-color);
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0a58ca 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .login-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem 0 2rem 0;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
        }

        .login-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin: 0;
        }

        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .forgot-password:hover {
            color: #0a58ca;
        }

        .invalid-feedback {
            display: block;
            font-weight: 500;
        }

        .form-check-label {
            color: #2c3e50;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .header-logos {
                padding: 0.75rem 1rem;
            }
            .logo-inptic img, .flag-gabon img {
                height: 65px;  /* Augmenté de 45px à 65px pour mobile */
            }
            .card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Logos en haut à gauche et à droite (plus de navbar blanche) -->
    <div class="header-logos">
        <div class="logo-inptic">
            <img src="/logo/logoinptic.png" alt="Logo INPTIC">
        </div>
        <div class="flag-gabon">
            <img src="/logo/Drapeau_du_Gabon.png" alt="Drapeau du Gabon">
        </div>
    </div>

    <div class="login-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card login-card">
                        <div class="card-header text-center">
                            <h4 class="login-title">
                                <i class="bi bi-person-circle me-2"></i>Connexion
                            </h4>
                        </div>
                        <div class="card-body">
                            <!-- Formulaire login -->
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <!-- Email -->
                                <div class="mb-3">
                                    <label for="email" class="form-label">Adresse e-mail</label>
                                    <input type="email"
                                           name="email"
                                           id="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           value="{{ old('email') }}"
                                           required
                                           autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password -->
                                <div class="mb-3">
                                    <label for="password" class="form-label">Mot de passe</label>
                                    <input type="password"
                                           name="password"
                                           id="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                               <!-- Remember Me -->
                              <div class="form-check mb-3">
                                  <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                  <label class="form-check-label" for="remember">Se souvenir de moi</label>
                             </div>

                                <!-- Bouton Connexion -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
                                    </button>
                                </div>
                            </form>

                            <!-- Mot de passe oublié -->
                            <div class="mt-3 text-center">
                                <a href="{{ route('password.request') }}" class="forgot-password">Mot de passe oublié ?</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>