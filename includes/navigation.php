           
           <?php
function isActive($page) {
    return (basename($_SERVER['PHP_SELF']) == $page) ? ' active' : '';
}
?>
           <div class="nk-sidebar is-light nk-sidebar-fixed is-light " data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-sidebar-brand">
                        <a href="./index.php" class="logo-link nk-sidebar-logo">
                            <img class="logo-light logo-img" src="./assets/imagen/logoC.png"  alt="logo">
                            <img class="logo-dark logo-img" src="./assets/imagen/logoC.png"  alt="logo-dark">
                            <img class="logo-small logo-img logo-img-small" src="./assets/imagen/logoC.png" srcset="./assets/imagen/logoC.png 2x" alt="logo-small">
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
                                    <a href="index.php" class="nk-menu-link ">
                                        <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
                                        <span class="nk-menu-text">Casa</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-heading">
                                    <h6 class="overline-title text-primary-alt">Menú</h6>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-item">
                                    <a href="/pacientes/pacientes.php" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-user-group-fill"></em></span>
                                        <span class="nk-menu-text">Pacientes</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-item">
                                    <a href="citas.php" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-calendar-alt"></em></span>
                                        <span class="nk-menu-text">Cítas</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-item">
                                    <a href="areas.php" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-color-palette"></em></span>
                                        <span class="nk-menu-text">Áreas</span>
                                    </a>
                                </li><!-- .nk-menu-item -->
                                <li class="nk-menu-item">
                                    <a href="evaluaciones.php" class="nk-menu-link">
                                        <span class="nk-menu-icon"><em class="icon ni ni-clipboad-check"></em></span>
                                        <span class="nk-menu-text">Evaluaciones</span>
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
                                            <a href="html/index-profile.html" class="nk-menu-link"><span class="nk-menu-text">Perfil</span></a>
                                        </li><!-- .nk-menu-item -->
                                        <li class="nk-menu-item">
                                            <a href="html/index-profile-activity.html" class="nk-menu-link"><span class="nk-menu-text">Configuración</span></a>
                                        </li><!-- .nk-menu-item -->
                                        <li class="nk-menu-item">
                                            <a href="html/index-profile-settings.html" class="nk-menu-link"><span class="nk-menu-text">
                                                Salir
                                            </span></a>
                                        </li><!-- .nk-menu-item -->
                                    </ul><!-- .nk-menu-sub -->
                                
                                </li><!-- .nk-menu-item -->


                            </ul><!-- .nk-menu -->
                        </div><!-- .nk-sidebar-menu -->
                    </div><!-- .nk-sidebar-content -->
                </div><!-- .nk-sidebar-element -->
            </div>

