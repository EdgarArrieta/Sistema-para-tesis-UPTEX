<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - UPTEX Tickets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .register-container {
            max-width: 1100px;
            width: 100%;
        }
        
        .register-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .register-left {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            color: white;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .register-logo {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }
        
        .register-left h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        
        .register-left p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }
        
        .benefit-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .benefit-item i {
            font-size: 1.5rem;
            color: #A7F3D0;
        }
        
        .register-right {
            padding: 3rem;
        }
        
        .register-right h3 {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .register-right p {
            color: #64748B;
            margin-bottom: 2rem;
        }
        
        .form-floating {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border-radius: 10px;
            border: 2px solid #E2E8F0;
            padding: 1rem;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: #10B981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }
        
        .btn-register {
            width: 100%;
            padding: 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
            border: none;
            color: white;
            transition: all 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #64748B;
        }
        
        .login-link a {
            color: #10B981;
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="row g-0 register-card">
            <!-- LEFT SIDE -->
            <div class="col-lg-5 register-left">
                <div class="register-logo">
                    <i class="bi bi-person-plus-fill"></i>
                </div>
                <h2>Crea tu cuenta</h2>
                <p>Únete al sistema de tickets UPTEX</p>
                <div class="benefits">
                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Crea tickets de soporte rápidamente</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Seguimiento de tus solicitudes</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Asistente IA para ayudarte</span>
                    </div>
                    <div class="benefit-item">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Respuestas rápidas del equipo técnico</span>
                    </div>
                </div>
            </div>
            
            <!-- RIGHT SIDE -->
            <div class="col-lg-7 register-right">
                <h3>Registro de Usuario</h3>
                <p>Completa el formulario para crear tu cuenta</p>
                
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                
                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control @error('nombre') is-invalid @enderror" 
                                       id="nombre" 
                                       name="nombre" 
                                       placeholder="Nombre"
                                       value="{{ old('nombre') }}"
                                       required>
                                <label for="nombre">Nombre</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" 
                                       class="form-control @error('apellido') is-invalid @enderror" 
                                       id="apellido" 
                                       name="apellido" 
                                       placeholder="Apellido"
                                       value="{{ old('apellido') }}"
                                       required>
                                <label for="apellido">Apellido</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-floating">
                        <input type="email" 
                               class="form-control @error('correo') is-invalid @enderror" 
                               id="correo" 
                               name="correo" 
                               placeholder="correo@uptex.edu.mx"
                               value="{{ old('correo') }}"
                               required>
                        <label for="correo">Correo Electrónico</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control @error('password') is-invalid @enderror" 
                               id="password" 
                               name="password" 
                               placeholder="Contraseña"
                               required>
                        <label for="password">Contraseña</label>
                        <small class="text-muted">Mínimo 6 caracteres</small>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" 
                               class="form-control" 
                               id="password_confirmation" 
                               name="password_confirmation" 
                               placeholder="Confirmar contraseña"
                               required>
                        <label for="password_confirmation">Confirmar Contraseña</label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            Acepto los términos y condiciones del servicio
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-register">
                        <i class="bi bi-person-check me-2"></i>
                        Crear Cuenta
                    </button>
                </form>
                
                <div class="login-link">
                    ¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión aquí</a>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>