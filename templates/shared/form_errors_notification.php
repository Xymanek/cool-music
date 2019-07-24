<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var string[]         $errors
 */
?>

<?php if (count($errors) === 1): ?>
    <?= h($errors[0]) ?>
<?php else: ?>
    Errors found, please correct:
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= h($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif ?>
