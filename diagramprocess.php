<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flujo de Procesos del Sistema - AutoMercado Total</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/leader-line-new@1.1.9/leader-line.min.js"></script>
    <style>
        :root {
            --user-color: #28a745;
            --seller-color: #17a2b8;
            --advisor-color: #ffc107;
            --admin-color: #dc3545;
            --bg-light: #f8f9fa;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .process-container {
            display: flex;
            justify-content: space-around;
            padding: 2rem;
            gap: 1rem;
            overflow-x: auto;
        }

        .swimlane {
            flex: 1;
            min-width: 300px;
            background-color: #fff;
            border-radius: 1rem;
            padding: 1rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .swimlane-header {
            text-align: center;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
            border-bottom: 4px solid;
        }
        
        #user-lane .swimlane-header { border-color: var(--user-color); }
        #seller-lane .swimlane-header { border-color: var(--seller-color); }
        #advisor-lane .swimlane-header { border-color: var(--advisor-color); }
        #admin-lane .swimlane-header { border-color: var(--admin-color); }

        .swimlane-header h4 {
            font-weight: 700;
        }

        .process-step {
            background-color: #fff;
            border: 2px solid #dee2e6;
            border-radius: 0.75rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            cursor: pointer;
        }

        .process-step:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .process-step i {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            display: block;
        }

        .process-step h6 {
            font-size: 0.9rem;
            font-weight: 600;
            margin: 0;
        }

        /* Leader-line styles */
        .leader-line {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="text-center my-5">
            <h1 class="display-4">Flujo de Procesos del Sistema</h1>
            <p class="lead">Una guía visual e interactiva de las funcionalidades por rol.</p>
        </div>

        <div class="process-container">
            <!-- User Swimlane -->
            <div id="user-lane" class="swimlane">
                <div class="swimlane-header">
                    <h4><i class="bi bi-person-circle me-2"></i>Usuario</h4>
                </div>
                <div class="process-step" id="user-step-1">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <h6>Registro / Login</h6>
                </div>
                <div class="process-step" id="user-step-2">
                    <i class="bi bi-speedometer2"></i>
                    <h6>Escritorio</h6>
                </div>
                <div class="process-step" id="user-step-3">
                    <i class="bi bi-search"></i>
                    <h6>Buscar Vehículos</h6>
                </div>
                <div class="process-step" id="user-step-4">
                    <i class="bi bi-heart-fill"></i>
                    <h6>Gestionar Favoritos</h6>
                </div>
                <div class="process-step" id="user-step-5">
                    <i class="bi bi-person-gear"></i>
                    <h6>Configurar Cuenta</h6>
                </div>
            </div>

            <!-- Client/Seller Swimlane -->
            <div id="seller-lane" class="swimlane">
                <div class="swimlane-header">
                    <h4><i class="bi bi-person-badge me-2"></i>Cliente/Vendedor</h4>
                </div>
                <div class="process-step" id="seller-step-1">
                    <i class="bi bi-cloud-upload"></i>
                    <h6>Publicar Vehículo</h6>
                </div>
                <div class="process-step" id="seller-step-2">
                    <i class="bi bi-pencil-square"></i>
                    <h6>Gestionar Vehículos</h6>
                </div>
                <div class="process-step" id="seller-step-3">
                    <i class="bi bi-calendar-check"></i>
                    <h6>Gestionar Citas</h6>
                </div>
            </div>

            <!-- Advisor Swimlane -->
            <div id="advisor-lane" class="swimlane">
                <div class="swimlane-header">
                    <h4><i class="bi bi-briefcase-fill me-2"></i>Asesor</h4>
                </div>
                 <div class="process-step" id="advisor-step-1">
                    <i class="bi bi-headset"></i>
                    <h6>Asistir a Clientes</h6>
                </div>
                <div class="process-step" id="advisor-step-2">
                    <i class="bi bi-graph-up-arrow"></i>
                    <h6>Gestión Activa de Citas</h6>
                </div>
            </div>

            <!-- Admin Swimlane -->
            <div id="admin-lane" class="swimlane">
                <div class="swimlane-header">
                    <h4><i class="bi bi-shield-lock-fill me-2"></i>Administrador</h4>
                </div>
                <div class="process-step" id="admin-step-1">
                    <i class="bi bi-check-circle-fill"></i>
                    <h6>Aprobar Vehículos</h6>
                </div>
                <div class="process-step" id="admin-step-2">
                    <i class="bi bi-tags-fill"></i>
                    <h6>Gestionar Catálogos</h6>
                </div>
                <div class="process-step" id="admin-step-3">
                    <i class="bi bi-people-fill"></i>
                    <h6>Gestionar Usuarios</h6>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Connections mapping
            const connections = [
                // User Flow
                { from: 'user-step-1', to: 'user-step-2' },
                { from: 'user-step-2', to: 'user-step-3' },
                { from: 'user-step-2', to: 'user-step-4' },
                { from: 'user-step-2', to: 'user-step-5' },
                
                // Seller Flow (includes user flow implicitly)
                { from: 'user-step-2', to: 'seller-step-1' },
                { from: 'seller-step-1', to: 'admin-step-1', color: 'rgba(220, 53, 69, 0.7)', path: 'fluid' },
                { from: 'seller-step-1', to: 'seller-step-2' },
                { from: 'user-step-3', to: 'seller-step-3' },

                // Advisor Flow
                { from: 'seller-step-3', to: 'advisor-step-1' },
                { from: 'advisor-step-1', to: 'advisor-step-2' },

                // Admin Flow
                { from: 'admin-step-1', to: 'seller-step-2', color: 'rgba(25, 135, 84, 0.7)', path: 'fluid' }
            ];

            const lines = [];

            function drawLines() {
                // Clear existing lines
                lines.forEach(line => line.remove());
                lines.length = 0;

                connections.forEach(conn => {
                    const fromEl = document.getElementById(conn.from);
                    const toEl = document.getElementById(conn.to);

                    if (fromEl && toEl) {
                        const line = new LeaderLine(
                            fromEl,
                            toEl,
                            {
                                color: conn.color || 'rgba(108, 117, 125, 0.7)',
                                size: 4,
                                path: conn.path || 'grid',
                                startSocket: 'auto',
                                endSocket: 'auto',
                                endPlug: 'arrow1'
                            }
                        );
                        lines.push(line);
                    }
                });
            }

            // Initial draw
            drawLines();

            // Redraw on window resize
            window.addEventListener('resize', drawLines);

            // Animate on scroll
            const steps = document.querySelectorAll('.process-step');
            const observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.5 });

            steps.forEach(step => {
                step.style.opacity = '0';
                step.style.transform = 'translateY(20px)';
                step.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(step);
            });
        });
    </script>
</body>
</html>
