<?php
$featured_offerwalls = array();
    $not_offerwalls = array("checkin", "spin", "refer", "redeem", "instructions", "transactions", "share", "rate", "about");

    if (isset($dbo)) {
        $ow = new offerwalls($dbo);
        $result = $ow->getOfferwalls(0);
        if (isset($result['offerwalls']) && is_array($result['offerwalls'])) {
            foreach ($result['offerwalls'] as $owall) {
                if (!in_array($owall['offer_type'], $not_offerwalls) && isset($owall['offer_status']) && $owall['offer_status'] === 'Active') {
                    $featured_offerwalls[] = $owall;
                }
            }
        }
    }

?>
<div class="card-modern">
    <div class="card-modern-header">
        <h3>Featured Offers</h3>
    </div>
    <div class="offers-horizontal-scroll">
        <?php if (!empty($featured_offerwalls)): ?>
            <?php foreach($featured_offerwalls as $offer):
                $otype = isset($offer['offer_type']) ? $offer['offer_type'] : '';
                $otitle = isset($offer['offer_title']) ? $offer['offer_title'] : '';
                $osub = isset($offer['offer_subtitle']) ? $offer['offer_subtitle'] : '';
                $oimg = isset($offer['offer_thumbnail']) ? $offer['offer_thumbnail'] : '';
            ?>
                <div class="offer-horizontal-card" onclick="show_offerwall('<?php echo htmlspecialchars($otype, ENT_QUOTES, 'UTF-8'); ?>')">
                    <?php if ($oimg): ?>
                        <img src="admin/images/<?php echo htmlspecialchars($oimg, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($otitle, ENT_QUOTES, 'UTF-8'); ?>" loading="lazy">
                    <?php endif; ?>
                    <h5><?php echo htmlspecialchars($otitle, ENT_QUOTES, 'UTF-8'); ?></h5>
                    <p><?php echo htmlspecialchars($osub, ENT_QUOTES, 'UTF-8'); ?></p>
                    <span class="btn btn-sm btn-primary">Open</span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted text-center" style="padding:2rem;">No featured offers available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

<style>
.offers-horizontal-scroll {
    display: flex;
    overflow-x: auto;
    gap: 16px;
    padding: 16px;
    scroll-snap-type: x mandatory;
    -webkit-overflow-scrolling: touch;
}
.offers-horizontal-scroll::-webkit-scrollbar {
    height: 6px;
}
.offers-horizontal-scroll::-webkit-scrollbar-track {
    background: var(--gray-100, #f1f1f1);
    border-radius: 3px;
}
.offers-horizontal-scroll::-webkit-scrollbar-thumb {
    background: var(--gray-300, #ccc);
    border-radius: 3px;
}
.offer-horizontal-card {
    flex: 0 0 auto;
    width: 170px;
    scroll-snap-align: start;
    background: var(--card-bg, #fff);
    border: 1px solid var(--border, #e8e8e8);
    border-radius: 12px;
    padding: 16px 12px;
    text-align: center;
    cursor: pointer;
    transition: transform 0.2s, box-shadow 0.2s;
}
.offer-horizontal-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.offer-horizontal-card img {
    width: 60px;
    height: 60px;
    object-fit: contain;
    margin-bottom: 8px;
    border-radius: 8px;
}
.offer-horizontal-card h5 {
    font-size: 0.85rem;
    margin: 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.offer-horizontal-card p {
    font-size: 0.7rem;
    color: #888;
    margin: 4px 0 10px;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
