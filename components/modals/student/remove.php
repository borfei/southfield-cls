<div class="modal centered fade" id="scls-student-modal-remove-<?php echo $student["reference"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-student-modal-remove-<?php echo $student["reference"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-student-modal-remove-<?php echo $student["reference"]; ?>-label"><strong>Remove Student</strong></h4>
            </div>
            <div class="modal-body">
                <form id="scls-form-student-remove-<?php echo $student["reference"]; ?>" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="reference" value="<?php echo $student["reference"]; ?>">
                </form>
                <p>Do you want to remove this student? <span class="text-danger">This action cannot be undone.</span></p>
                <div class="media">
                    <div class="media-body">
                        <h4 class="media-heading">
                            <strong><?php echo "$student[family_name], $student[given_name]"; ?></strong>
                            <br>
                            <small><?php echo $student["reference"]; ?></small>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-student-remove-<?php echo $student["reference"]; ?>">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>