<?php
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
                                                    
                                                    $sql = "SELECT * FROM redemptions WHERE status = 'completed' ORDER BY id DESC LIMIT 5";
                                                    $result = $dbo->prepare($sql);
                                                    $result->execute();
                                                    $recentRows = $result->fetchAll(PDO::FETCH_ASSOC);
                                                    
                                                    if (count($recentRows) === 0) { ?>
                                                    <div class="notif-empty" style="padding:48px 16px;text-align:center;">
                                                        <strong>No payouts yet</strong>
                                                        <p style="margin:8px 0 0;color:var(--gray-400);font-size:13px;">Once you redeem points, completed payouts will appear here.</p>
                                                    </div>
                                                    <?php } else {
                                                        foreach ($recentRows as $recentPayouts) {?>
                                                     
                                                     <div class="kt-widget4__item">
                                                        <div class="kt-widget4__info">
                                                            <p class="kt-widget4__username"><?php echo esc_attr($recentPayouts['gift_name']); ?></p>
                                                            <p class="kt-widget4__text">To : <?php echo esc_attr($configs->mask_payoutTo($recentPayouts['request_from'])); ?></p>
                                                        </div>
                                                        <div class="btn btn-sm btn-label-brand btn-bold"><?php echo esc_attr($recentPayouts['req_amount']); ?></div>
                                                    </div>
                                                    
                                                    <?php } } ?>
                                                </div>
                                            </div>
                                        </div>