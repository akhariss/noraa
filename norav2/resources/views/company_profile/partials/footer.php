<?php
/**
 * Footer Partial (Final Production)
 * Updated: 11 March 2026 - Copyright now database-driven (LAW 0.2 Single Source of Truth)
 */
$footerBrand   = cmsContent($homepageData, 'footer', 'brand', APP_NAME);
$footerDesc    = cmsContent($homepageData, 'footer', 'description', 'Pendamping hukum terpercaya.');
$footerAddress = cmsContent($homepageData, 'footer', 'address', 'Cirebon, Jawa Barat');
$footerPhone   = cmsContent($homepageData, 'footer', 'phone', '0857-4789-8811');
$footerEmail   = cmsContent($homepageData, 'footer', 'email', 'info@example.com');
$footerCopyright = cmsContent($homepageData, 'footer', 'copyright_text', '© 2024 ' . APP_NAME . '. Hak Cipta Dilindungi.');

// Operational Hours - from CMS database (section_id = 8)
// Format 2 baris untuk footer (lebih mudah dibaca)
$footerWorkDays     = cmsContent($homepageData, 'footer', 'work_days', 'Senin - Jumat');
$footerWorkHours    = cmsContent($homepageData, 'footer', 'work_hours', '08:00 - 16:00');
$footerWorkDaysSat  = cmsContent($homepageData, 'footer', 'work_days_sat', 'Sabtu');
$footerWorkHoursSat = cmsContent($homepageData, 'footer', 'work_hours_sat', '08:00 - 12:00');

// Strip any existing <br> tags from DB data first
$footerWorkDays     = strip_tags($footerWorkDays);
$footerWorkHours    = strip_tags($footerWorkHours);
$footerWorkDaysSat  = strip_tags($footerWorkDaysSat);
$footerWorkHoursSat = strip_tags($footerWorkHoursSat);

// Build operational hours string - only add Saturday if it has values
$operationalHours = htmlspecialchars($footerWorkDays) . ': ' . htmlspecialchars($footerWorkHours);
if (!empty($footerWorkDaysSat) && !empty($footerWorkHoursSat)) {
    $operationalHours .= '<br>' . htmlspecialchars($footerWorkDaysSat) . ': ' . htmlspecialchars($footerWorkHoursSat);
}

// Quick Links (Layanan Cepat) - from CMS database
$footerQuickLinks = cmsItems($homepageData, 'footer', 'quick_link');
?>
</main>
<footer>
    <div class="container">
        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-brand"><?= htmlspecialchars($footerBrand) ?></div>
                <p><?= htmlspecialchars($footerDesc) ?></p>
            </div>
            <div class="footer-col">
                <h4>Layanan Cepat</h4>
                <ul class="footer-links">
                    <?php if (!empty($footerQuickLinks)): ?>
                        <?php foreach ($footerQuickLinks as $link): ?>
                            <?php 
                            $extra = json_decode($link['extra_data'] ?? '{}', true);
                            $url = $extra['url'] ?? '#';
                            
                            // If URL contains wa.me, replace number with dynamic phone (LAW 0.2)
                            if (strpos($url, 'wa.me/') !== false) {
                                $url = 'https://wa.me/' . sanitizePhoneForWa($footerPhone);
                            }
                            ?>
                            <li>
                                <a href="<?= htmlspecialchars($url) ?>" aria-label="<?= htmlspecialchars($link['title'] ?? '') ?> Tautan Layanan Cepat">
                                    <?= htmlspecialchars($link['title'] ?? '') ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback jika tidak ada data - use dynamic phone from CMS -->
                        <li><a href="<?= APP_URL ?>/index.php?gate=lacak">Lacak Registrasi</a></li>
                        <li><a href="https://wa.me/<?= sanitizePhoneForWa($footerPhone) ?>">Hubungi Kami</a></li>
                        <li><a href="#testimoni">Testimoni</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Hubungi Kami</h4>
                <ul class="footer-contact">
                    <li><?= htmlspecialchars($footerAddress) ?></li>
                    <li>WA: <?= htmlspecialchars($footerPhone) ?></li>
                    <li>Email: <?= htmlspecialchars($footerEmail) ?></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Jam Operasional</h4>
                <p><?= $operationalHours ?></p>
            </div>
        </div>
        <div class="footer-bottom">
            <p><?= htmlspecialchars($footerCopyright) ?></p>
        </div>
    </div>
</footer>
<script>const APP_URL = '<?= APP_URL ?>';</script>
<script src="<?= APP_URL ?>/assets/js/company-profile.js"></script>

</body>
</html>
