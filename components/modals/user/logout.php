<div class="modal centered fade" id="scls-user-modal-logout" tabindex="-1" role="dialog" aria-labelledby="scls-user-modal-logout-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-log-out" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-user-modal-logout-label"><strong>Log Out</strong></h4>
            </div>
            <div class="modal-body">Are you sure you want to log out?</div>
            <div class="modal-footer no-border">
                <form method="post" action="login.php">
                    <input type="hidden" name="action" value="sign_out">
                    <button type="submit" class="btn btn-primary">Confirm</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>