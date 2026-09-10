<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Godong Ijo Admin</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #ffffff;
            overflow-x: hidden;
            background: linear-gradient(135deg, rgba(20, 40, 30, 0.7), rgba(10, 25, 20, 0.85)),
                        url('{{ asset('images/wisata edukasi/haloo.webp') }}');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        
        .login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
        }
        
        /* Glassmorphism Card - Dark Theme */
        .glass-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            background: rgba(20, 30, 40, 0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            padding: 40px 32px;
        }
        
        .form-header {
            margin-bottom: 28px;
            text-align: center;
        }
        
        .brand-logo {
            width: 80px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.3));
        }
        
        .form-title {
            font-size: 24px;
            font-weight: 600;
            color: #ffffff;
            margin-bottom: 6px;
            letter-spacing: -0.3px;
            text-align: center;
        }
        
        .form-subtitle {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
            text-align: center;
            margin-bottom: 24px;
        }
        
        /* Alert Messages */
        .alert {
            padding: 12px 14px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #86efac;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .alert-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
        }
        
        /* Form Elements */
        .form-group {
            margin-bottom: 18px;
        }
        
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .form-input {
            width: 100%;
            height: 44px;
            padding: 0 14px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 6px;
            font-size: 14px;
            color: #ffffff;
            transition: all 0.2s;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }
        
        .form-input:focus {
            outline: none;
            border-color: rgba(255, 255, 255, 0.3);
            background: rgba(255, 255, 255, 0.08);
        }
        
        .form-input.error {
            border-color: rgba(239, 68, 68, 0.5);
        }
        
        .form-input.error:focus {
            border-color: rgba(239, 68, 68, 0.7);
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            color: rgba(255, 255, 255, 0.5);
            transition: color 0.2s;
        }
        
        .password-toggle:hover {
            color: rgba(255, 255, 255, 0.8);
        }
        
        .password-toggle svg {
            width: 18px;
            height: 18px;
        }
        
        .error-message {
            font-size: 12px;
            color: #fca5a5;
            margin-top: 6px;
            display: block;
        }
        
        /* Checkbox */
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        
        /* Submit Button */
        .btn-submit {
            width: 100%;
            height: 44px;
            background: #ffffff;
            color: #1a1a1a;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-submit:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-1px);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .btn-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(0, 0, 0, 0.2);
            border-top-color: #1a1a1a;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Footer */
        .form-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.4);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .glass-card {
                padding: 32px 24px;
                border-radius: 20px;
            }
            
            .brand-logo {
                width: 100px;
                height: 100px;
            }
            
            .form-title {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="glass-card">
            <!-- Logo -->
            <div class="brand-logo">
                <img src="{{ asset('images/placeholders/The-Waterfall-Logo-removebg-preview.png') }}" alt="Godong Ijo Logo">
            </div>
            
            <!-- Form Header -->
            <div class="form-header">
                <h2 class="form-title">Login</h2>
                <p class="form-subtitle">Masuk ke admin panel untuk mengelola sistem</p>
            </div>
                
                @if(session('success'))
                <div class="alert alert-success">
                    <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-error">
                    <svg class="alert-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                @endif
                
                <form action="{{ route('admin.login.post') }}" method="POST" id="loginForm">
                    @csrf
                    
                    <!-- Username -->
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <div class="input-wrapper">
                            <input 
                                type="text" 
                                id="username" 
                                name="username" 
                                value="{{ old('username') }}"
                                class="form-input @error('username') error @enderror"
                                placeholder="Masukkan username"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>
                        @error('username')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-wrapper">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input @error('password') error @enderror"
                                placeholder="Masukkan password"
                                required
                                autocomplete="current-password"
                            >
                            <button type="button" class="password-toggle" onclick="togglePassword()">
                                <svg id="eyeIcon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-submit" id="submitBtn">
                        <span id="btnText">Log in</span>
                        <div id="btnSpinner" class="btn-spinner" style="display: none;"></div>
                    </button>                
                <div class="form-footer">
                    © {{ date('Y') }} Godong Ijo Ecotainment & Resto
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Password Toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
            }
        }
        
        // Form Submit Loading State
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');
            
            submitBtn.disabled = true;
            btnText.textContent = 'Memproses...';
            btnSpinner.style.display = 'block';
        });
        
        // Keyboard Shortcut: Enter on username → focus password
        document.getElementById('username').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('password').focus();
            }
        });
    </script>
</body>
</html>
