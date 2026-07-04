<?php

    /*!
	 * POCKET v3.7
	 *
	 * http://www.aym.com
	 * support@aym.com
	 *
	 * Copyright 2020 AYM ( http://www.aym.com )
	 */

?>
                                        <div class="row row-full-height">
                                            
                                            <div class="col-lg-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Current Points</h6>
                                                        <h2><?php echo esc_attr($userCurrentPoints); ?></h2>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Total Earned</h6>
                                                        <h2><?php echo esc_attr($userTotalPoints); ?></h2>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Redeemed Points</h6>
                                                        <h2><?php echo esc_attr($userRedeemedPoints); ?></h2>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <h6>Members Referred</h6>
                                                        <h2><?php echo esc_attr($userreferredMembers); ?></h2>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>