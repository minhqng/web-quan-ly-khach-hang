        <?php require __DIR__ . '/chan-trang.php'; ?>
    </main>
</div>
<?php require __DIR__ . '/hop-thoai-xac-nhan.php'; ?>
<script>
window.APP_BASE_URL = <?= json_encode(duong_dan(''), JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
window.APP_CSRF_TOKEN = <?= json_encode(csrf_token(), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
</script>
<script src="<?= e(duong_dan('tai-nguyen/vendor/bootstrap/bootstrap.bundle.min.js')) ?>"></script>
<script src="<?= e(duong_dan('tai-nguyen/js/ung-dung.js')) ?>"></script>
<script src="<?= e(duong_dan('tai-nguyen/js/ajax-khach-hang-trung-lap.js')) ?>"></script>
<script src="<?= e(duong_dan('tai-nguyen/js/ajax-khach-hang-dong.js')) ?>"></script>
<script src="<?= e(duong_dan('tai-nguyen/js/ajax-khach-hang-danh-sach.js')) ?>"></script>
<script src="<?= e(duong_dan('tai-nguyen/js/ajax-khach-hang.js')) ?>"></script>
<script src="<?= e(duong_dan('tai-nguyen/js/ajax-cong-viec-theo-doi.js')) ?>"></script>
</body>
</html>
