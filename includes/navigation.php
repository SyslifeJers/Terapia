<?php
function isActive($page) {
    return (basename($_SERVER['PHP_SELF']) == $page) ? ' active' : '';
}

$mascotaChistes = [];
$mascotaChistesPath = __DIR__ . '/../assets/chistes.json';
if (is_readable($mascotaChistesPath)) {
    $mascotaChistesJson = json_decode((string) file_get_contents($mascotaChistesPath), true);
    if (isset($mascotaChistesJson['chistes']) && is_array($mascotaChistesJson['chistes'])) {
        foreach ($mascotaChistesJson['chistes'] as $chiste) {
            $textoChiste = trim((string) ($chiste['texto'] ?? ''));
            if ($textoChiste !== '') {
                $mascotaChistes[] = $textoChiste;
            }
        }
    }
}
?>
           <div class="nk-sidebar is-light nk-sidebar-fixed is-light " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="/index.php" class="logo-link nk-sidebar-logo">
                            <img class="logo-light logo-img" src="/assets/imagen/logoC.png"  alt="logo">
                            <img class="logo-dark logo-img" src="/assets/imagen/logoC.png"  alt="logo-dark">
                            <img class="logo-small logo-img logo-img-small" src="/assets/imagen/logoC.png" srcset="/assets/imagen/logoC.png 2x" alt="logo-small">
                        </a>
                    </div>
                    <div class="nk-menu-trigger me-n2">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                    </div>
                </div><!-- .nk-sidebar-element -->
                <div class="nk-sidebar-element">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu">
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt ">Início</h6>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-item" id="menu-casa">
                                    <a href="/index.php" class="nk-menu-link ">
                                        <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
                                        <span class="nk-menu-text">Inicio</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Menú</h6>
                                </li><!-- .nk-menu-item -->
                                                                            <?php if ($_SESSION['rol'] != 2): ?>
                                <li class="nk-menu-item" id="menu-pacientes">
                                    <a href="/pacientes/pacientes.php" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-group-fill"></em></span>
                                        <span class="nk-menu-text">Pacientes</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <?php endif; ?>

                                <li class="nk-menu-item" id="menu-areas">
                                    <a href="/areas/index.php" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-color-palette"></em></span>
                                        <span class="nk-menu-text">Áreas</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                 <li class="nk-menu-item" id="menu-evaluaciones">
                                     <a href="/evaluaciones.php" class="nk-menu-link">
                                         <span class="nk-menu-icon"><em class="icon ni ni-clipboad-check"></em></span>
                                         <span class="nk-menu-text">Evaluaciones</span>
                                     </a>
                                 </li><!-- .nk-menu-item -->
                                <li class="nk-menu-item" id="menu-flujos">
                                    <a href="/flujos/mis_tareas.php" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-check-circle"></em></span>
                                        <span class="nk-menu-text">Mis tareas</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                 <li class="nk-menu-heading">
                                     <h6 class="overline-title text-primary-alt">Configuración</h6>
                                 </li><!-- .nk-menu-heading -->
                                <li class="nk-menu-item">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><em class="icon ni ni-edit-profile-fill"></em></span>
                                        <span class="nk-menu-text">Mi perfil</span>
                                    </a>
                                    <ul class="nk-menu-sub">
                                        <li class="nk-menu-item">
                                            <a href="./perfil_usuario.php" class="nk-menu-link"><span class="nk-menu-text">Perfil</span></a>
                                        </li><!-- .nk-menu-item -->
                                        <li class="nk-menu-item">
                                            <a href="./configuracion_usuario.php" class="nk-menu-link"><span class="nk-menu-text">Configuración</span></a>
                                        </li><!-- .nk-menu-item -->
                                        <li class="nk-menu-item">
                                            <a href="./salir.php" class="nk-menu-link"><span class="nk-menu-text">
                                                Salir
                                            </span></a>
                                        </li><!-- .nk-menu-item -->
                                    </ul><!-- .nk-menu-sub -->
                                
                                </li><!-- .nk-menu-item -->


                            </ul><!-- .nk-menu -->
                            <div class="nk-sidebar-logo-extra px-4 py-3 text-center">
                                <button type="button" id="masconta-meme-trigger" class="border-0 bg-transparent p-0">
                                    <img src="/assets/mascota.png" alt="DinoMaster" class="img-fluid masconta-sidebar-mascot" style="max-height: 10em; object-fit: contain;">
                                </button>
                            </div>
                        </div><!-- .nk-sidebar-menu -->
                    </div><!-- .nk-sidebar-content -->
                </div><!-- .nk-sidebar-element -->
                <style>
                    .masconta-meme-overlay {
                        position: fixed;
                        inset: 0;
                        z-index: 99999;
                        display: none;
                        align-items: center;
                        justify-content: center;
                        background: rgba(15, 23, 42, 0.28);
                        pointer-events: none;
                    }

                    .masconta-sidebar-mascot {
                        animation: masconta-sidebar-breathe 2.4s ease-in-out infinite;
                        transform-origin: center bottom;
                    }

                    @keyframes masconta-sidebar-breathe {
                        0%, 100% {
                            transform: scale(1);
                            filter: brightness(1);
                        }
                        50% {
                            transform: scale(1.035);
                            filter: brightness(1.06);
                        }
                    }

                    .masconta-meme-overlay.is-visible {
                        display: flex;
                        animation: masconta-meme-backdrop 6s ease-in-out forwards;
                    }

                    .masconta-meme-image {
                        max-width: min(420px, 82vw);
                        max-height: 58vh;
                        border-radius: 18px;
                        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.35);
                        animation: masconta-meme-pop 6s ease-in-out forwards;
                    }

                    .masconta-meme-content {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        max-width: min(560px, 88vw);
                    }

                    .masconta-meme-joke {
                        margin-top: 14px;
                        padding: 12px 16px;
                        border-radius: 16px;
                        background: #fff;
                        color: #111827;
                        font-size: clamp(1rem, 2.2vw, 1.35rem);
                        font-weight: 800;
                        line-height: 1.25;
                        text-align: center;
                        box-shadow: 0 14px 36px rgba(15, 23, 42, 0.22);
                        animation: masconta-meme-pop 6s ease-in-out forwards;
                    }

                    @keyframes masconta-meme-pop {
                        0% {
                            opacity: 0;
                            transform: scale(0.2) rotate(-18deg);
                        }
                        8% {
                            opacity: 1;
                            transform: scale(1.15) rotate(8deg);
                        }
                        14% {
                            transform: scale(0.96) rotate(-5deg);
                        }
                        20% {
                            transform: scale(1.04) rotate(3deg);
                        }
                        26%, 72% {
                            opacity: 1;
                            transform: scale(1) rotate(0deg);
                        }
                        86% {
                            opacity: 1;
                            transform: scale(1.04) rotate(-3deg);
                        }
                        100% {
                            opacity: 0;
                            transform: scale(0.85) translateY(18px) rotate(6deg);
                        }
                    }

                    @keyframes masconta-meme-backdrop {
                        0% {
                            background: rgba(15, 23, 42, 0);
                        }
                        8%, 72% {
                            background: rgba(15, 23, 42, 0.28);
                        }
                        100% {
                            background: rgba(15, 23, 42, 0);
                        }
                    }
                </style>
                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var mascotaChistes = <?php echo json_encode($mascotaChistes, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
                    var trigger = document.getElementById('masconta-meme-trigger');
                    var overlay = null;
                    var hideTimeout = null;

                    if (!trigger) {
                        return;
                    }

                    function escapeHtml(value) {
                        return String(value || '').replace(/[&<>'"]/g, function (character) {
                            return {
                                '&': '&amp;',
                                '<': '&lt;',
                                '>': '&gt;',
                                "'": '&#039;',
                                '"': '&quot;'
                            }[character];
                        });
                    }

                    function getDailyJoke() {
                        if (!Array.isArray(mascotaChistes) || mascotaChistes.length === 0) {
                            return '';
                        }

                        var today = new Date();
                        var seed = today.getFullYear() + (today.getMonth() + 1) + today.getDate();
                        var index = seed % mascotaChistes.length;
                        return mascotaChistes[index] || '';
                    }

                    function getMemeOverlay() {
                        if (overlay) {
                            return overlay;
                        }

                        overlay = document.createElement('div');
                        overlay.id = 'masconta-meme-overlay';
                        overlay.className = 'masconta-meme-overlay';
                        overlay.setAttribute('aria-hidden', 'true');
                        document.body.appendChild(overlay);

                        return overlay;
                    }

                    trigger.addEventListener('click', function () {
                        var memeOverlay = getMemeOverlay();
                        var joke = getDailyJoke();

                        window.clearTimeout(hideTimeout);
                        memeOverlay.classList.remove('is-visible');
                        memeOverlay.innerHTML = '<div class="masconta-meme-content"><img src="/assets/Enojado.png" alt="Meme" class="masconta-meme-image">' + (joke ? '<div class="masconta-meme-joke">Chiste del día: ' + escapeHtml(joke) + '</div>' : '') + '</div>';
                        void memeOverlay.offsetWidth;
                        memeOverlay.classList.add('is-visible');
                        memeOverlay.setAttribute('aria-hidden', 'false');

                        hideTimeout = window.setTimeout(function () {
                            memeOverlay.classList.remove('is-visible');
                            memeOverlay.setAttribute('aria-hidden', 'true');
                        }, 6000);
                    });
                });
                </script>
            </div>
