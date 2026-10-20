<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/login.css">

<div class="login-box">
    <div class="card card-outline card-primary">
        <div class="login-split">

            
            <div class="login-art">
                <img src="<?= BASE_URL ?>/public/img/logo.png" alt="The Play Ground" class="art-logo">
                <svg viewBox="0 0 320 200" class="art-svg" aria-hidden="true">
                    <rect class="court" x="20" y="30" width="280" height="150" rx="8"/>
                    <path class="court" d="M160 30v150M20 75h280M20 135h280"/>
                    <g class="paddle paddle-l"><rect x="38" y="92" width="26" height="34" rx="12"/><rect x="48" y="124" width="6" height="18" rx="3"/></g>
                    <g class="paddle paddle-r"><rect x="256" y="92" width="26" height="34" rx="12"/><rect x="266" y="124" width="6" height="18" rx="3"/></g>
                    <g class="ball">
                        <circle cx="160" cy="70" r="13"/>
                        <circle cx="155" cy="66" r="2.2"/><circle cx="165" cy="66" r="2.2"/>
                        <circle cx="160" cy="75" r="2.2"/><circle cx="160" cy="70" r="1.6"/>
                    </g>
                    <ellipse class="shadow" cx="160" cy="165" rx="14" ry="3"/>
                </svg>
                <p class="art-text">Game on. Sign in and book your court.</p>
            </div>

            <!-- Form -->
            <div class="login-form">
                <div class="card-header text-center">
                    <a href="<?= BASE_URL ?>/" class="h1"><b>The </b> Play Ground</a>
                </div>

                <div class="card-body">
                    <p class="login-box-msg">Sign in to your account</p>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?= htmlspecialchars($_SESSION['success']) ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form id="login-form" method="post" action="<?= BASE_URL ?>/Auth/login">
                        <div class="input-group mb-3">
                            <input type="email" name="email" class="form-control"
                                   placeholder="Email" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="password" class="form-control"
                                   placeholder="Password" required>
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            Sign In
                        </button>
                    </form>

                    <p class="mb-0 mt-3 text-center">
                        <a href="<?= BASE_URL ?>/Users/register">Register a new account</a>
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/public/js/login.js"></script>