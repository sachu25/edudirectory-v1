<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="shortcut icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <title>{{ config('app.name', 'CIMS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --primary-color: #4F46E5;
                --primary-gradient: linear-gradient(135deg, #4F46E5 0%, #EC4899 100%);
                --secondary-color: #EC4899;
                --accent-gradient: linear-gradient(135deg, #EC4899 0%, #8B5CF6 100%);
            }
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background: radial-gradient(circle at 10% 20%, rgba(243, 244, 246, 1) 0%, rgba(224, 231, 255, 0.4) 90%);
                position: relative;
                overflow-x: hidden;
                overflow-y: auto;
            }
            .auth-card {
                border: 1px solid rgba(255, 255, 255, 0.8);
                background: rgba(255, 255, 255, 0.85);
                backdrop-filter: blur(16px);
                -webkit-backdrop-filter: blur(16px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                border-radius: 20px;
                overflow: hidden;
            }
            .auth-header {
                background: var(--primary-gradient);
                color: white;
                padding: 30px;
                text-align: center;
            }
            .btn-primary {
                background: var(--primary-gradient);
                border: none;
                box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
                transition: all 0.2s ease;
            }
            .btn-primary:hover {
                background: linear-gradient(135deg, #4338CA 0%, #6D28D9 100%);
                transform: translateY(-1px);
                box-shadow: 0 6px 16px rgba(79, 70, 229, 0.3);
            }
            .text-primary {
                color: var(--primary-color) !important;
            }
            /* Decorative animated blobs */
            .blob {
                position: absolute;
                border-radius: 50%;
                filter: blur(60px);
                z-index: -1;
                opacity: 0.5;
                animation: float 10s infinite alternate;
            }
            .blob-1 {
                top: -10%;
                left: -10%;
                width: 300px;
                height: 300px;
                background: #818CF8;
            }
            .blob-2 {
                bottom: -10%;
                right: -10%;
                width: 350px;
                height: 350px;
                background: #F472B6;
            }
            @keyframes float {
                0% { transform: translate(0, 0) scale(1); }
                100% { transform: translate(50px, 30px) scale(1.1); }
            }
            .interactive-glow {
                position: absolute;
                width: 450px;
                height: 450px;
                background: radial-gradient(circle, rgba(129, 140, 248, 0.22) 0%, rgba(244, 114, 182, 0.04) 50%, transparent 80%);
                border-radius: 50%;
                pointer-events: none;
                transform: translate(-50%, -50%);
                z-index: 0;
                transition: opacity 0.3s ease;
            }
            @media (max-width: 400px) {
                .login-options {
                    flex-direction: column !important;
                    align-items: flex-start !important;
                    gap: 0.5rem !important;
                }
            }
        </style>
        
    </head>
    <body class="d-flex align-items-center py-4 min-vh-100">
        <!-- Background elements isolated in a fixed wrapper to prevent horizontal/vertical scrollbars -->
        <div style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; overflow: hidden; z-index: -1; pointer-events: none;">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
            <div class="interactive-glow" id="interactiveGlow"></div>
        </div>
        
        <main class="w-100 m-auto px-3" style="max-width: 450px; z-index: 1;">
            <!-- Logo above the card -->
            <div class="text-center mb-4 px-3">
                <img src="{{ asset('images/logo_home.png') }}" alt="EduDirectory" style="max-width: 100%; height: auto;">
            </div>
            
            <div class="card auth-card">
                <div class="auth-header">
                    <h5 class="mb-0 fw-bold text-uppercase" style="letter-spacing: 1px; font-size: 0.9rem; opacity: 0.9;">Login to your account</h5>
                </div>
                <div class="card-body p-4">
                    <!-- Flash Messages -->
                    @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif
                    
                    {{ $slot }}
                </div>
            </div>
        </main>
        
        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Glow Follower
                const glow = document.getElementById('interactiveGlow');
                
                // Canvas Constellation Particle Animation
                const canvas = document.createElement('canvas');
                canvas.id = 'particleCanvas';
                canvas.style.position = 'fixed';
                canvas.style.top = '0';
                canvas.style.left = '0';
                canvas.style.width = '100vw';
                canvas.style.height = '100vh';
                canvas.style.pointerEvents = 'none';
                canvas.style.zIndex = '0';
                document.body.appendChild(canvas);

                const ctx = canvas.getContext('2d');
                let particles = [];
                const mouse = { x: null, y: null, radius: 160 };

                function resizeCanvas() {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                    init();
                }
                window.addEventListener('resize', resizeCanvas);

                class Particle {
                    constructor() {
                        this.x = Math.random() * canvas.width;
                        this.y = Math.random() * canvas.height;
                        this.vx = (Math.random() - 0.5) * 0.5;
                        this.vy = (Math.random() - 0.5) * 0.5;
                        this.radius = Math.random() * 2 + 1;
                    }
                    update() {
                        this.x += this.vx;
                        this.y += this.vy;
                        
                        if (this.x < 0 || this.x > canvas.width) this.vx = -this.vx;
                        if (this.y < 0 || this.y > canvas.height) this.vy = -this.vy;
                    }
                    draw() {
                        ctx.beginPath();
                        ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                        ctx.fillStyle = 'rgba(129, 140, 248, 0.45)';
                        ctx.fill();
                    }
                }

                function init() {
                    particles = [];
                    const count = Math.min(90, Math.floor((canvas.width * canvas.height) / 16000));
                    for (let i = 0; i < count; i++) {
                        particles.push(new Particle());
                    }
                }

                function animate() {
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    
                    particles.forEach(p => {
                        p.update();
                        p.draw();
                    });
                    
                    // Draw lines between nearby particles
                    for (let i = 0; i < particles.length; i++) {
                        for (let j = i + 1; j < particles.length; j++) {
                            const dx = particles[i].x - particles[j].x;
                            const dy = particles[i].y - particles[j].y;
                            const dist = Math.sqrt(dx * dx + dy * dy);
                            
                            if (dist < 110) {
                                ctx.beginPath();
                                ctx.moveTo(particles[i].x, particles[i].y);
                                ctx.lineTo(particles[j].x, particles[j].y);
                                ctx.strokeStyle = `rgba(129, 140, 248, ${0.12 * (1 - dist / 110)})`;
                                ctx.lineWidth = 0.5;
                                ctx.stroke();
                            }
                        }
                        
                        // Dynamic lines connecting mouse to nearby particles
                        if (mouse.x !== null && mouse.y !== null) {
                            const dx = particles[i].x - mouse.x;
                            const dy = particles[i].y - mouse.y;
                            const dist = Math.sqrt(dx * dx + dy * dy);
                            
                            if (dist < mouse.radius) {
                                ctx.beginPath();
                                ctx.moveTo(particles[i].x, particles[i].y);
                                ctx.lineTo(mouse.x, mouse.y);
                                ctx.strokeStyle = `rgba(236, 72, 153, ${0.28 * (1 - dist / mouse.radius)})`;
                                ctx.lineWidth = 0.75;
                                ctx.stroke();
                            }
                        }
                    }
                    
                    requestAnimationFrame(animate);
                }

                document.addEventListener('mousemove', (e) => {
                    mouse.x = e.clientX;
                    mouse.y = e.clientY;
                    
                    if (glow) {
                        glow.style.left = e.clientX + 'px';
                        glow.style.top = e.clientY + 'px';
                        glow.style.opacity = '1';
                    }
                });

                document.addEventListener('mouseleave', () => {
                    mouse.x = null;
                    mouse.y = null;
                    if (glow) {
                        glow.style.opacity = '0';
                    }
                });

                resizeCanvas();
                animate();
            });
        </script>
    </body>
</html>
