<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var \Entity\Offer[]  $offers
 * @var string           $username
 * @var string           $offerId
 */
$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'register';

$username = $username ?? '';
$offerId = $offerId ?? -1;
?>
<div class="content-box">
    <form method="post">
        <div class="form-row">
            <label for="register-username">Username</label>
            <input type="text" id="register-username" name="username" value="<?= $username ?>">
        </div>
        <div class="form-row">
            <label for="register-password">Password</label>
            <input type="password" id="register-password" name="password">
        </div>
        <div class="form-row">
            <label for="register-offer">Offer</label>
            <select name="offer_id" id="register-offer">
                <?php foreach ($offers as $offer): ?>
                    <option value="<?= $offer->id ?>" <?php if ($offerId == $offer->id): ?>selected<?php endif; ?>>
                        <?= h($offer->title) ?> ($<?= h($offer->price) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button class="btn btn-success full-width btn-big" type="submit" disabled id="submit-button">Register</button>
    </form>
</div>

<?php $renderInfo->prepareBlock('scripts', function () { ?>
    <script>
        var submitButton = document.getElementById('submit-button');
        var username = document.getElementById('register-username');
        var password = document.getElementById('register-password');

        var check = function () {
            if (username.value.length > 2 && password.value.length > 3) {
                submitButton.removeAttribute('disabled');
            } else {
                submitButton.setAttribute('disabled', 'disabled');
            }
        };

        username.addEventListener('keyup', check);
        password.addEventListener('keyup', check);
    </script>
<?php }); ?>