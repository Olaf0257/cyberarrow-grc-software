<!-- Custom Modal -->
<div id="custom-modal-<?php echo $i; ?>" class="modal-demo">
    <div class="custom-modal-text">
        Do you want to delete this item? If yes please click the delete button.
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light waves-effect" onclick="Custombox.modal.close();">Cancel</button>
        <a href="?del=1" class="btn btn-danger waves-effect waves-light">Delete Item</a>
    </div>
</div>