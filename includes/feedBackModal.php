<div class="modal fade" id="feedback" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header" style="padding:35px 50px;">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4>Оставте нам ваше сообщение и мы непременно свяжемся с вами!</h4>
            </div>
            <div class="modal-body" style="padding:40px 50px;">
                <form role="form" class="contact-form special_form" id="special1" method="Post">
                    <div class="form-group">
                        <input type="text" id="feedback_name" placeholder="Как вас зовут?" class="form-control">
                        <div id="feedback_name_errors" class="response_errors"></div>
                        <input type="text" id="feedback_email" placeholder="Введите ваш e-mail для связи с вами" class="form-control">
                        <div id="feedback_email_errors" class="response_errors"></div>
                        <textarea style="max-width: 100%; min-width: 100%" id="feedback_message" class="form-control">Введите ваше сообщение</textarea>
                        <div id="feedback_message_errors" class="response_errors"></div>
                        <div id="confirm" class="modalbtn">Отправить сообщение!</div>
                        <div id="messages_success" class="response_errors"></div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>