<div class="modal fade" id="scls-user-admin-modal-add" tabindex="-1" role="dialog" aria-labelledby="scls-user-admin-modal-add-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-user-admin-modal-add-label"><strong>Add New Admin</strong></h4>
            </div>
            <div class="modal-body">
                <div class="well well-lg">
                    <form id="scls-form-user-admin-add" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="type" value="admin">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="username" class="form-control" name="username" minlength="6" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" data-field="password" minlength="4" maxlength="32" required>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-user-admin-add">Add</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>