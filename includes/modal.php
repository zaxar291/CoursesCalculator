<div class="modal-window" id="admin_modal" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" id="append_modal_content">

        </div>
    </div>
</div>

<div class="modal-window" id="add_admin_modal" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" id="add_admin_modal_content">

        </div>
    </div>
</div>

<div class="modal-window" id="site-state-modal" role="dialog">
    <div class="modal-dialog" >
        <div class="modal-content" id="add_admin_modal_content">
            <?php
                $jsonStr = _getSiteStateJson();
                if(gettype($jsonStr == "string"))
                {

                }
            ?>
        </div>
    </div>
</div>

<div class="custom-modal-fade"></div>


