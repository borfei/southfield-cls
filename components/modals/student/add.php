<div class="modal fade" id="scls-student-modal-add" tabindex="-1" role="dialog" aria-labelledby="scls-student-modal-add-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-student-modal-add-label"><strong>Add New Student</strong></h4>
            </div>
            <div class="modal-body">
                <div class="well well-lg">
                    <form id="scls-form-student-add" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>">
                        <input type="hidden" name="action" value="add">
                        <div class="form-group">
                            <label>Learner Reference Number</label>
                            <input type="text" class="form-control" name="reference" pattern="\d*" minlength="12" maxlength="12" required>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Given Name</label>
                                    <input type="text" class="form-control" name="given_name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Initial Name</label>
                                    <input type="text" class="form-control" name="initial_name">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Family Name</label>
                                    <input type="text" class="form-control" name="family_name" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Grade Level</label>
                                    <select class="form-control" name="grade_level" required>
                                        <option value="12" selected>12</option>
                                        <option value="11">11</option>
                                        <option value="10">10</option>
                                        <option value="9">9</option>
                                        <option value="8">8</option>
                                        <option value="7">7</option>
                                        <option value="6">6</option>
                                        <option value="5">5</option>
                                        <option value="4">4</option>
                                        <option value="3">3</option>
                                        <option value="2">2</option>
                                        <option value="1">1</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Grade Section</label>
                                    <input type="text" class="form-control" name="grade_section">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-student-add">Add</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>