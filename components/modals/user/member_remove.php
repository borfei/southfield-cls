<div class="modal centered fade" id="scls-user-member-modal-remove-<?php echo $member["name"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-user-member-modal-remove-<?php echo $member["name"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-user-member-modal-remove-<?php echo $member["name"]; ?>-label"><strong>Remove Member</strong></h4>
            </div>
            <div class="modal-body">
                <form id="scls-form-user-member-remove-<?php echo $member["name"]; ?>" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="name" value="<?php echo $member["name"]; ?>">
                </form>
                <p>Do you want to remove this member? <span class="text-danger">This action cannot be undone.</span></p>
                <div class="media">
                    <div class="media-body">
                        <h4 class="media-heading">
                            <strong><?php echo $member["name"]; ?></strong>
                            <br>
                            <small><?php echo $member["student_reference"]; ?></small>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-user-member-remove-<?php echo $member["name"]; ?>">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>