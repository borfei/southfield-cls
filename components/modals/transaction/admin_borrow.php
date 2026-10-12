<div class="modal fade" id="scls-transaction-modal-borrow-<?php echo $transaction["id"]; ?>" tabindex="-1" role="dialog" aria-labelledby="scls-transaction-modal-borrow-<?php echo $transaction["id"]; ?>-label">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header horizontal">
                <span class=" glyphicon glyphicon-time" aria-hidden="true"></span>
                <h4 class="modal-title" id="scls-transaction-modal-borrow-<?php echo $transaction["id"]; ?>-label"><strong>Mark Transaction As Borrowed</strong></h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="post" action="<?php echo basename($_SERVER["SCRIPT_FILENAME"]); ?>" id="scls-form-transaction-borrow-<?php echo $transaction["id"]; ?>">
                    <input type="hidden" name="action" value="borrow">
                    <input type="hidden" name="id" value="<?php echo $transaction["id"]; ?>">
                    <!-- Book -->
                    <div class="media">
                        <div class="media-left">
                            <img class="media-object img-thumbnail" width="48px" height="48px" src="<?php echo $image; ?>" alt="<?php echo $transaction["book_title"]; ?>">
                        </div>
                        <div class="media-body media-middle">
                            <h4 class="media-heading">
                                <?php echo $transaction["book_title"]; ?>
                                <small><?php echo $transaction["book_year"]; ?></small>
                                <br>
                                <small><?php echo $transaction["book_author"]; ?></small>
                            </h4>
                        </div>
                    </div>
                    <br>
                    <!-- Input -->
                    <div class="well well-lg">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Due Date</label>
                            <div class="col-sm-10">
                                <input type="date" class="form-control" name="due_date" min="<?php echo date("Y-m-d"); ?>" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary" form="scls-form-transaction-borrow-<?php echo $transaction["id"]; ?>">Confirm</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>