<?php require_once("components/modals/user/logout.php"); ?>
<?php require_once("components/scls/session.php"); ?>
<nav class="navbar navbar-default admin">
    <div class="container-fluid">
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
        </div>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <ul class="nav navbar-nav navbar-left">
                <li><a class="text-uppercase" href="admin_books.php"><strong>Books</strong></a></li>
                <li><a class="text-uppercase" href="admin_transactions.php"><strong>Transactions</strong></a></li>
                <li><a class="text-uppercase" href="admin_users.php"><strong>Users</strong></a></li>
                <li><a class="text-uppercase" href="admin_students.php"><strong>Students</strong></a></li>
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
                    </button>
                </li>
            </ul>
        </div>
    </div>
</nav>