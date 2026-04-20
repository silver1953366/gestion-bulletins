<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Vérification - INPTIC</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    font-family: 'Segoe UI',sans-serif;
    background:url('/images/inptic.jpg') center/cover fixed;
    min-height:100vh;
    display:flex;
    flex-direction:column;
}
body::before{
    content:"";
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.5);
}

.header{
    position:relative;
    z-index:1;
    display:flex;
    justify-content:space-between;
    padding:1rem 2rem;
}
.header img{height:80px}

.container-box{
    position:relative;
    z-index:1;
    flex:1;
    display:flex;
    align-items:center;
    justify-content:center;
}

.card{
    border:none;
    border-radius:12px;
    background:rgba(255,255,255,.95);
}

.card-header{
    background:linear-gradient(135deg,#0d6efd,#0a58ca);
    color:#fff;
    text-align:center;
    font-weight:bold;
}

.code-box{
    display:flex;
    gap:8px;
    justify-content:center;
}

.code-box input{
    width:45px;
    height:55px;
    text-align:center;
    font-size:1.5rem;
    border:2px solid #ddd;
    border-radius:8px;
}

.code-box input:focus{
    border-color:#0d6efd;
    outline:none;
}

.toast-box{
    position:fixed;
    top:20px;
    right:20px;
    display:none;
    z-index:9999;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
    <img src="/logo/logoinptic.png">
    <img src="/logo/Drapeau_du_Gabon.png">
</div>

<!-- CONTENT -->
<div class="container-box">
<div class="card p-3" style="max-width:400px;width:100%">
    
    <div class="card-header p-3">
        <i class="bi bi-shield-check"></i> Vérification
    </div>

    <div class="card-body text-center">

        <p class="text-muted">Code envoyé par email</p>

        <form method="POST" action="{{ route('verify.check') }}" id="form">
            @csrf
            <input type="hidden" name="code" id="code">

            <div class="code-box mb-3">
                @for($i=0;$i<6;$i++)
                    <input type="text" maxlength="1">
                @endfor
            </div>

            @error('code')
                <div class="alert alert-danger">{{ $message }}</div>
            @enderror
        </form>

        <a href="{{ route('login') }}" class="small">← Retour</a>

    </div>
</div>
</div>

<!-- TOAST -->
<div id="toast" class="toast-box card p-3 shadow">
    <strong>Connexion réussie 🎉</strong>
</div>

<script>
const inputs = document.querySelectorAll('.code-box input');
const codeInput = document.getElementById('code');
const form = document.getElementById('form');
const toast = document.getElementById('toast');

inputs.forEach((input,i)=>{
    input.addEventListener('input',()=>{
        if(input.value && i<5) inputs[i+1].focus();
        check();
    });

    input.addEventListener('keydown',e=>{
        if(e.key==="Backspace" && !input.value && i>0){
            inputs[i-1].focus();
        }
    });
});

function check(){
    let code=[...inputs].map(i=>i.value).join('');
    codeInput.value=code;

    if(code.length===6){
        toast.style.display="block";
        form.submit();
    }
}
</script>

</body>
</html>