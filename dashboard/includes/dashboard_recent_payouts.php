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
                                        <div class="kt-portlet">
                                            <div class="kt-portlet__head">
                                                <div class="kt-portlet__head-label">
                                                    <h3 class="kt-portlet__head-title">
                                                        Recent Payouts
                                                    </h3>
                                                </div>
                                            </div>
                                            <div class="kt-portlet__body">
                                                <div class="kt-widget4">
                                                    
                                                    <?php 
                                                    
                                                    $sql = "SELECT * FROM Completed ORDER BY rid DESC LIMIT 5";
                                                    $result = $dbo->prepare($sql);
                                                    $result->execute();
                                                    
                                                    while ($recentPayouts = $result->fetch()) {?>
                                                     
                                                     <div class="kt-widget4__item">
                                                        <div class="kt-widget4__info">
                                                            <p class="kt-widget4__username"><?php echo esc_attr($recentPayouts['gift_name']); ?></p>
                                                            <p class="kt-widget4__text">To : <?php echo esc_attr($configs->mask_payoutTo($recentPayouts['request_from'])); ?></p>
                                                        </div>
                                                        <div class="btn btn-sm btn-label-brand btn-bold"><?php echo esc_attr($recentPayouts['req_amount']); ?></div>
                                                    </div>
                                                    
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>