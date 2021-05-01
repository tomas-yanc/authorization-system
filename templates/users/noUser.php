<?php include __DIR__ . '/../header.php'; ?>
    <div style="text-align: center;">
        <h1>Ошибка!</h1>
        <?php if (!empty($noUser)): ?>
            <div style="background-color: red;padding: 5px;margin: 15px"><?= $noUser ?></div>
        <?php endif; ?>
    </div>
<?php include __DIR__ . '/../footer.php'; ?>