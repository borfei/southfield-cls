<div class="modal fade" id="scls-user-admin-modal-edit-<?php echo $admin["name"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-user-admin-modal-edit-<?php echo $admin["name"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-user-admin-modal-edit-<?php echo $admin["name"]; ?>-label"><strong>Edit Admin Info</strong></h4>
            </div>
            <div class="modal-body">
                <div class="well well-lg">
                    <form id="scls-form-user-admin-edit-<?php echo $admin["name"]; ?>" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="name" value="<?php echo $admin["name"]; ?>">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="username" class="form-control" name="username" minlength="6" value="<?php echo $admin["name"]; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" data-field="password" minlength="4" maxlength="32" required>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-user-admin-edit-<?php echo $admin["name"]; ?>">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>