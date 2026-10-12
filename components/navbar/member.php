<?php require_once("components/modals/user/logout.php"); ?>
<?php require_once("components/scls/session.php"); ?>
<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbarCollapse" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                <img alt="PSS Icon" src="assets/icon.png" height="64px">
            </a>
            <?php if (!scls_session_user()): ?>
                <p class="navbar-text text-uppercase"><strong>Computerized Library System</strong></p>
            <?php endif; ?>
        </div>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <?php if (scls_session_user()): ?>
                <ul class="nav navbar-nav navbar-left">
                    <li><a class="text-uppercase" href="member_books.php"><strong>Books</strong></a></li>
                    <li><a class="text-uppercase" href="member_borrows.php"><strong>Borrows</strong></a></li>
                    <li><a class="text-uppercase" href="member_transactions.php"><strong>Transactions</strong></a></li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <?php $user = scls_session_user(); ?>
                    <li>
                        <p class="navbar-text" id="username">
                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                            <?php echo $user["name"]; ?>
                        </p>
                    </li>
                    <li>
                        <button type="button" class="btn btn-link navbar-btn" data-toggle="modal" data-target="#scls-user-modal-logout">
                            <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
                            Logout
                            </a>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a href="index.php"> <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Return to Index</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>