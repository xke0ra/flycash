<?php
?>
		<!--Header Fixed-->
        <div class="header fixed-header">
            <div class="container-fluid" style="padding: 10px 25px">
                <div class="row">
                    <div class="col-9 col-md-6 d-lg-none">
                        <a id="toggle-navigation" href="javascript:void(0);" class="icon-btn mr-3"><i class="fa fa-bars"></i></a>
                        <span class="logo">Dashboard</span>
                    </div>
                    <div class="col-lg-8 d-none d-lg-block">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="admin.php">Dashboard</a></li>
                        </ol>
                    </div>
                    <div class="col-3 col-md-6 col-lg-4">
						<a href="logout.php/?access_token=<?php echo admin::getAccessToken(); ?>" class="btn btn-primary pull-right" style="padding: .5rem .75rem;">
							<i class="ion-log-out"></i>Logout
						</a>
                    </div>
                </div>
            </div>
        </div>