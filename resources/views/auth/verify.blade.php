<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification Sécurisée | INPTIC</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .bg-verify {
            background-image: url('/images/inptic.jpg');
            background-size: cover;
            background-position: center;
        }
        /* Animation pour le toast */
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .animate-slide-in { animation: slideIn 0.5s ease-out forwards; }
    </style>
</head>
<body class="h-full overflow-hidden antialiased font-sans bg-verify">
    
    <div class="fixed inset-0 bg-gradient-to-br from-blue-900/50 to-black/80 z-0"></div>

    <div class="relative z-10 h-full flex flex-col">
        
        <header class="flex justify-between items-center px-6 py-4 md:px-10">
            <img src="/logo/logoinptic.png" alt="Logo INPTIC" class="h-12 md:h-16 w-auto drop-shadow-lg">
            <img src="/logo/Drapeau_du_Gabon.png" alt="Drapeau du Gabon" class="h-10 md:h-14 w-auto shadow-md rounded">
        </header>

        <main class="flex-grow flex items-center justify-center p-4">
            <div 
                x-data="{ show: false }" 
                x-init="setTimeout(() => show = true, 50)"
                x-show="show"
                class="w-full max-w-md"
            >
                <div class="glass-card rounded-3xl shadow-2xl overflow-hidden">
                    
                    <div class="bg-indigo-600/90 p-5 text-center">
                        <div class="inline-flex items-center justify-center w-12 h-12 bg-white/20 rounded-full mb-2">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white tracking-tight">Double Authentification</h2>
                    </div>

                    <div class="p-8 text-center">
                        <p class="text-sm text-gray-500 mb-6">
                            Saisissez le code de vérification à 6 chiffres envoyé à votre adresse email.
                        </p>

                        <form method="POST" action="{{ route('verify.check') }}" id="otp-form" class="space-y-6">
                            @csrf
                            <input type="hidden" name="code" id="final-code">

                            <div class="flex justify-between gap-2 md:gap-3">
                                @for($i=0; $i<6; $i++)
                                    <input type="text" maxlength="1" 
                                        class="otp-input w-11 h-14 md:w-12 md:h-16 text-center text-2xl font-bold text-indigo-600 bg-white/50 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring-0 transition-all outline-none"
                                        inputmode="numeric" pattern="[0-9]*">
                                @endfor
                            </div>

                            @error('code')
                                <div class="p-3 rounded-lg bg-red-50 text-red-600 text-xs font-medium">
                                    {{ $message }}
                                </div>
                            @enderror

                            <button type="submit" id="submit-btn" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-lg transform active:scale-95 transition-all opacity-50 cursor-not-allowed" disabled>
                                VÉRIFIER LE CODE
                            </button>
                        </form>

                        <div class="mt-6">
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-400 hover:text-indigo-600 transition-colors">
                                ← Retour à la connexion
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="success-toast" class="hidden fixed top-6 right-6 z-50 bg-white border-l-4 border-green-500 p-4 rounded-xl shadow-2xl animate-slide-in">
        <div class="flex items-center">
            <span class="text-2xl mr-3">🎉</span>
            <div>
                <p class="text-sm font-bold text-gray-800">Code correct !</p>
                <p class="text-xs text-gray-500">Connexion en cours...</p>
            </div>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const finalInput = document.getElementById('final-code');
        const form = document.getElementById('otp-form');
        const submitBtn = document.getElementById('submit-btn');
        const toast = document.getElementById('success-toast');

        inputs.forEach((input, i) => {
            // Gestion de la saisie
            input.addEventListener('input', (e) => {
                // On s'assure que c'est un chiffre
                if (e.inputType === "deleteContentBackward") return;
                
                const val = input.value;
                if (!/^\d$/.test(val)) {
                    input.value = "";
                    return;
                }

                if (val && i < inputs.length - 1) {
                    inputs[i + 1].focus();
                }
                updateCode();
            });

            // Gestion des touches spéciales
            input.addEventListener('keydown', (e) => {
                if (e.key === "Backspace" && !input.value && i > 0) {
                    inputs[i - 1].focus();
                }
            });

            // Coller le code complet (ex: depuis un email)
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text').slice(0, 6).split('');
                pasteData.forEach((char, index) => {
                    if (inputs[index] && /^\d$/.test(char)) {
                        inputs[index].value = char;
                    }
                });
                if (pasteData.length > 0) {
                    inputs[Math.min(pasteData.length, 5)].focus();
                }
                updateCode();
            });
        });

        function updateCode() {
            let code = Array.from(inputs).map(i => i.value).join('');
            finalInput.value = code;

            if (code.length === 6) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                
                // Auto-submit avec animation
                toast.classList.remove('hidden');
                setTimeout(() => form.submit(), 800);
            } else {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            }
        }
    </script>
</body>
</html>