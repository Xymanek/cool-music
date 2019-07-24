<?php
/**
 * @var \View\RenderInfo $renderInfo
 * @var string           $username
 */
$renderInfo->parent = 'shared/layout.php';
$renderInfo->parentVars['currentPage'] = 'login';

$username = $username ?? '';
?>
<div class="content-box">
    <form method="post">
        <div class="form-row">
            <label for="login-username">Username</label>
            <input type="text" id="login-username" name="username" value="<?= $username ?>">
        </div>
        <div class="form-row">
            <label for="login-password">Password</label>
            <input type="password" id="login-password" name="password">
        </div>

        <button class="btn btn-success full-width btn-big" type="submit" disabled id="submit-button">Login</button>
    </form>
</div>

<?php $renderInfo->prepareBlock('scripts', function () { ?>
    <script>
        var submitButton = document.getElementById('submit-button');
        var username = document.getElementById('login-username');
        var password = document.getElementById('login-password');

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