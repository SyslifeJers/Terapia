<?php
include_once 'includes/head.php';
?>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
            <?php
                include_once 'includes/menu_superior.php';
                ?>
                <!-- main header @e -->
                <!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-xl">
                        <div class="nk-content-body">
                            <div class="nk-block-head nk-block-head-sm">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h3 class="nk-block-title page-title">Expendientes</h3>
                                        <div class="nk-block-des text-soft">
                                            <p>Bienvenido al panel de gestión de expedientes.</p>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                    <div class="nk-block-head-content">
                                        <div class="toggle-wrap nk-block-tools-toggle">
                                            <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                            <div class="toggle-expand-content" data-content="pageMenu">
                                                <ul class="nk-block-tools g-3">
                                                   <!-- <li class="nk-block-tools-opt"><a href="#" class="btn btn-primary"><em class="icon ni ni-reports"></em><span>Reports</span></a></li>-->
                                                </ul>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head-content -->
                                </div><!-- .nk-block-between -->
                            </div><!-- .nk-block-head -->
                            <div class="nk-block">
                                <div class="row g-gs">
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-primary">
                                            <div class="nk-cmwg nk-cmwg1">
                                                <div class="card-inner pt-3">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="flex-item">
                                                            <div class="text-white d-flex flex-wrap">
                                                                <span class="fs-2 me-1">56.8K</span>
                                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                                            </div>
                                                            <h6 class="text-white">Running Campaign</h6>
                                                        </div>
                                                        <div class="card-tools me-n1">
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- .card-inner -->
                                                <div class="nk-ck-wrap mt-auto overflow-hidden rounded-bottom">
                                                    <div class="nk-cmwg1-ck">
                                                        <canvas class="campaign-line-chart-s1 rounded-bottom" id="runningCampaign"></canvas>
                                                    </div>
                                                </div>
                                            </div><!-- .nk-cmwg -->
                                        </div><!-- .card -->
                                    </div><!-- .col -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-info">
                                            <div class="nk-cmwg nk-cmwg1">
                                                <div class="card-inner pt-3">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="flex-item">
                                                            <div class="text-white d-flex flex-wrap">
                                                                <span class="fs-2 me-1">857.6K</span>
                                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                                            </div>
                                                            <h6 class="text-white">Total Audience</h6>
                                                        </div>
                                                        <div class="card-tools me-n1">
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- .card-inner -->
                                                <div class="nk-cmwg1-ck mt-auto">
                                                    <canvas class="campaign-line-chart-s1 rounded-bottom" id="totalAudience"></canvas>
                                                </div>
                                            </div><!-- .nk-cmwg -->
                                        </div><!-- .card -->
                                    </div><!-- .col -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-warning">
                                            <div class="nk-cmwg nk-cmwg1">
                                                <div class="card-inner pt-3">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="flex-item">
                                                            <div class="text-white d-flex flex-wrap">
                                                                <span class="fs-2 me-1">9.3K</span>
                                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                                            </div>
                                                            <h6 class="text-white">Avg. Rating</h6>
                                                        </div>
                                                        <div class="card-tools me-n1">
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- .card-inner -->
                                                <div class="nk-ck-wrap mt-auto overflow-hidden rounded-bottom">
                                                    <div class="nk-cmwg1-ck">
                                                        <canvas class="campaign-bar-chart-s1 rounded-bottom" id="avgRating"></canvas>
                                                    </div>
                                                </div>
                                            </div><!-- .nk-cmwg -->
                                        </div><!-- .card -->
                                    </div><!-- .col -->
                                    <div class="col-lg-3 col-sm-6">
                                        <div class="card h-100 bg-danger">
                                            <div class="nk-cmwg nk-cmwg1">
                                                <div class="card-inner pt-3">
                                                    <div class="d-flex justify-content-between">
                                                        <div class="flex-item">
                                                            <div class="text-white d-flex flex-wrap">
                                                                <span class="fs-2 me-1">175.2K</span>
                                                                <span class="align-self-end fs-14px pb-1"><em class="icon ni ni-arrow-long-up"></em>12.4%</span>
                                                            </div>
                                                            <h6 class="text-white">Subscriber</h6>
                                                        </div>
                                                        <div class="card-tools me-n1">
                                                            <div class="dropdown">
                                                                <a href="#" class="dropdown-toggle btn btn-icon btn-sm btn-trigger on-dark" data-bs-toggle="dropdown"><em class="icon ni ni-more-v"></em></a>
                                                                <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                                    <ul class="link-list-opt no-bdr">
                                                                        <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                                        <li><a href="#"><span>30 Days</span></a></li>
                                                                        <li><a href="#"><span>3 Months</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div><!-- .card-inner -->
                                                <div class="nk-ck-wrap mt-auto overflow-hidden rounded-bottom">
                                                    <div class="nk-cmwg1-ck">
                                                        <canvas class="campaign-line-chart-s1 rounded-bottom" id="newSubscriber"></canvas>
                                                    </div>
                                                </div>
                                            </div><!-- .nk-cmwg -->
                                        </div><!-- .card -->
                                    </div><!-- .col -->
                                   
                                    <div class="col-xxl-8">
                                        <div class="card card-full">
                                            <div class="card-inner">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title">Active Campaign</h6>
                                                    </div>
                                                    <div class="card-tools">
                                                        <a href="#" class="link">View All</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-inner py-0 mt-n2">
                                                <div class="nk-tb-list nk-tb-flush nk-tb-dashed">
                                                    <div class="nk-tb-item nk-tb-head">
                                                        <div class="nk-tb-col"><span>Subject</span></div>
                                                        <div class="nk-tb-col tb-col-mb"><span>Channels</span></div>
                                                        <div class="nk-tb-col tb-col-sm"><span>Status</span></div>
                                                        <div class="nk-tb-col tb-col-md"><span>Assignee</span></div>
                                                        <div class="nk-tb-col text-end"><span>Date Range</span></div>
                                                    </div><!-- .nk-tb-head -->
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col">
                                                            <span class="tb-lead">Happy Christmas <span class="dot dot-success d-sm-none ms-1"></span></span>
                                                            <span class="tb-sub">Created on 01 Dec 22</span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-mb">
                                                            <ul class="d-flex gx-1">
                                                                <li class="text-facebook"><em class="icon ni ni-facebook-f"></em></li>
                                                                <li class="text-instagram"><em class="icon ni ni-instagram"></em></li>
                                                                <li class="text-linkedin"><em class="icon ni ni-linkedin"></em></li>
                                                                <li class="text-twitter"><em class="icon ni ni-twitter"></em></li>
                                                                <li class="text-youtube"><em class="icon ni ni-youtube-fill"></em></li>
                                                            </ul>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-sm">
                                                            <div class="badge badge-dim bg-success">Live Now</div>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <div class="user-avatar-group">
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/e-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/f-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/g-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <span>2+</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nk-tb-col text-end"><span>01 Dec - 07 Dec</span></div>
                                                    </div><!-- .nk-tb-item -->
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col">
                                                            <span class="tb-lead">Black Friday <span class="dot dot-success d-sm-none ms-1"></span></span>
                                                            <span class="tb-sub">Created on 01 Dec 22</span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-mb">
                                                            <ul class="d-flex gx-1">
                                                                <li class="text-linkedin"><em class="icon ni ni-linkedin"></em></li>
                                                                <li class="text-facebook"><em class="icon ni ni-facebook-f"></em></li>
                                                                <li class="text-instagram"><em class="icon ni ni-instagram"></em></li>
                                                                <li class="text-youtube"><em class="icon ni ni-youtube-fill"></em></li>
                                                            </ul>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-sm">
                                                            <div class="badge badge-dim bg-success">Live Now</div>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <div class="user-avatar-group">
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/h-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/i-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/j-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <span>7+</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nk-tb-col text-end"><span>01 Dec - 07 Dec</span></div>
                                                    </div><!-- .nk-tb-item -->
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col">
                                                            <span class="tb-lead">Tree Plantation <span class="dot dot-warning d-sm-none ms-1"></span></span>
                                                            <span class="tb-sub">Created on 01 Jan 23</span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-mb">
                                                            <ul class="d-flex gx-1">
                                                                <li class="text-twitter"><em class="icon ni ni-twitter"></em></li>
                                                                <li class="text-instagram"><em class="icon ni ni-instagram"></em></li>
                                                                <li class="text-linkedin"><em class="icon ni ni-linkedin"></em></li>
                                                            </ul>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-sm">
                                                            <div class="badge badge-dim bg-warning">Paused</div>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <div class="user-avatar-group">
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/k-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs bg-pink">
                                                                    <span>AE</span>
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/e-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <span>3+</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nk-tb-col text-end"><span>01 Dec - 07 Dec</span></div>
                                                    </div><!-- .nk-tb-item -->
                                                    <div class="nk-tb-item">
                                                        <div class="nk-tb-col">
                                                            <span class="tb-lead">Getaway Trailer <span class="dot dot-success d-sm-none ms-1"></span></span>
                                                            <span class="tb-sub">Created on 12 Dec 22</span>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-mb">
                                                            <ul class="d-flex gx-1">
                                                                <li class="text-linkedin"><em class="icon ni ni-linkedin"></em></li>
                                                                <li class="text-twitter"><em class="icon ni ni-twitter"></em></li>
                                                                <li class="text-facebook"><em class="icon ni ni-facebook-f"></em></li>
                                                                <li class="text-youtube"><em class="icon ni ni-youtube-fill"></em></li>
                                                            </ul>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-sm">
                                                            <div class="badge badge-dim bg-success">Live Now</div>
                                                        </div>
                                                        <div class="nk-tb-col tb-col-md">
                                                            <div class="user-avatar-group">
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/i-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/k-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/e-sm.jpg" alt="">
                                                                </div>
                                                                <div class="user-avatar xs">
                                                                    <img src="./images/avatar/g-sm.jpg" alt="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="nk-tb-col text-end"><span>01 Dec - 07 Dec</span></div>
                                                    </div><!-- .nk-tb-item -->
                                                </div><!-- .nk-tb-list -->
                                            </div>
                                        </div><!-- .card -->
                                    </div><!-- .col -->
                                    <div class="col-xxl-4 col-md-6">
                                        <div class="card card-full">
                                            <div class="card-inner">
                                                <div class="card-title-group">
                                                    <div class="card-title">
                                                        <h6 class="title">Key Statistics</h6>
                                                    </div>
                                                    <div class="card-tools me-n1 mt-n1">
                                                        <div class="dropdown">
                                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown"><em class="icon ni ni-more-h"></em></a>
                                                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-end">
                                                                <ul class="link-list-opt no-bdr">
                                                                    <li><a href="#" class="active"><span>15 Days</span></a></li>
                                                                    <li><a href="#"><span>30 Days</span></a></li>
                                                                    <li><a href="#"><span>3 Months</span></a></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-inner pt-0">
                                                <ul class="gy-4">
                                                    <li class="border-bottom border-0 border-dashed">
                                                        <div class="mb-1">
                                                            <span class="fs-2 lh-1 mb-1 text-head">85.6K</span>
                                                            <div class="sub-text">Average Like</div>
                                                        </div>
                                                        <div class="align-center">
                                                            <div class="small text-primary me-2">54%</div>
                                                            <div class="progress progress-md rounded-pill w-100 bg-primary-dim">
                                                                <div class="progress-bar bg-primary rounded-pill" data-progress="54"></div>
                                                            </div>
                                                            <div class="dropdown ms-3">
                                                                <a class="dropdown-toggle dropdown-indicator sub-text" href="#" type="button" data-bs-toggle="dropdown" data-bs-offset="0, 10">Dec 22 - Feb 22</a>
                                                                <div class="dropdown-menu dropdown-menu-end text-right">
                                                                    <ul class="link-list-opt">
                                                                        <li><a href="#"><span>Dec 22 - Feb 22</span></a></li>
                                                                        <li><a href="#"><span>Oct 22 - Dec 22</span></a></li>
                                                                        <li><a href="#"><span>Aug 22 - Oct 22</span></a></li>
                                                                        <li><a href="#"><span>Jun 22 - Aug 22</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li><!-- li -->
                                                    <li class="border-bottom border-0 border-dashed">
                                                        <div class="mb-1">
                                                            <span class="fs-2 lh-1 mb-1 text-head">42.7K</span>
                                                            <div class="sub-text">Average Comments</div>
                                                        </div>
                                                        <div class="align-center">
                                                            <div class="small text-danger me-2">84%</div>
                                                            <div class="progress progress-md rounded-pill w-100 bg-danger-dim">
                                                                <div class="progress-bar bg-danger rounded-pill" data-progress="84"></div>
                                                            </div>
                                                            <div class="dropdown ms-3">
                                                                <a class="dropdown-toggle dropdown-indicator sub-text" href="#" type="button" data-bs-toggle="dropdown" data-bs-offset="0, 10">Dec 22 - Feb 22</a>
                                                                <div class="dropdown-menu dropdown-menu-end text-right">
                                                                    <ul class="link-list-opt">
                                                                        <li><a href="#"><span>Dec 22 - Feb 22</span></a></li>
                                                                        <li><a href="#"><span>Oct 22 - Dec 22</span></a></li>
                                                                        <li><a href="#"><span>Aug 22 - Oct 22</span></a></li>
                                                                        <li><a href="#"><span>Jun 22 - Aug 22</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li><!-- li -->
                                                    <li>
                                                        <div class="mb-1">
                                                            <span class="fs-2 lh-1 mb-1 text-head">25.4K</span>
                                                            <div class="sub-text">Average Shares</div>
                                                        </div>
                                                        <div class="align-center">
                                                            <div class="small text-success me-2">62%</div>
                                                            <div class="progress progress-md rounded-pill w-100 bg-success-dim">
                                                                <div class="progress-bar bg-success rounded-pill" data-progress="62"></div>
                                                            </div>
                                                            <div class="dropdown ms-3">
                                                                <a class="dropdown-toggle dropdown-indicator sub-text" href="#" type="button" data-bs-toggle="dropdown" data-bs-offset="0, 10">Dec 22 - Feb 22</a>
                                                                <div class="dropdown-menu dropdown-menu-end text-right">
                                                                    <ul class="link-list-opt">
                                                                        <li><a href="#"><span>Dec 22 - Feb 22</span></a></li>
                                                                        <li><a href="#"><span>Oct 22 - Dec 22</span></a></li>
                                                                        <li><a href="#"><span>Aug 22 - Oct 22</span></a></li>
                                                                        <li><a href="#"><span>Jun 22 - Aug 22</span></a></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li><!-- li -->
                                                </ul>
                                            </div>
                                        </div><!-- .card -->
                                    </di                                             v><!-- .col -->
                                   

                                </div><!-- .row -->
                            </div><!-- .nk-block -->
                        </div>
                    </div>
                </div>
                <!-- content @e -->
              
            </div>
            <!-- wrap @e -->
       <?php
include_once 'includes/footer.php'; 
?>
