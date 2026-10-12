<div class="modal fade" id="scls-user-member-modal-edit-<?php echo $member["name"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-user-member-modal-edit-<?php echo $member["name"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-user-member-modal-edit-<?php echo $member["name"]; ?>-label"><strong>Edit Member Info</strong></h4>
            </div>
            <div class="modal-body">
                <div class="well well-lg">
                    <form id="scls-form-user-member-edit-<?php echo $member["name"]; ?>" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="name" value="<?php echo $member["name"]; ?>">
                        <div class="form-group">
                            <label>Learner Reference Number</label>
                            <input type="text" class="form-control" name="reference" value="<?php echo $member["student_reference"]; ?>" readonly>
                            <p class="help-block">Editing LRN is impossible, re-create the member if you wish to change it.</p>
                        </div>
                        <div class="form-group">
                            <label>Username</label>
                            <input type="username" class="form-control" name="username" minlength="6" value="<?php echo $member["name"]; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" data-field="password" minlength="4" maxlength="32" required>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-user-member-edit-<?php echo $member["name"]; ?>">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>