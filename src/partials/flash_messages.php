<?php
require_once 'includes/FlashMessage.php';

if(FlashMessage::hasMessages()) {
    $messages = FlashMessage::flush();

    $twbs_class_map = [
        FlashMessage::SEVERITY_NEUTRAL => 'default',
        FlashMessage::SEVERITY_SUCCESS => 'success',
        FlashMessage::SEVERITY_WARNING => 'warning',
        FlashMessage::SEVERITY_ERROR => 'danger'
    ];

    ?>
    <div class="flash-message-container mt-3">
    <?php
        foreach ($messages as $severity => $severity_messages) {
            $twbs_class = $twbs_class_map[$severity];
            foreach ($severity_messages as $message) {
                echo sprintf('<div class="alert alert-%s">%s</div>', $twbs_class, $message);
            }
        }
    ?>
    </div>
    <?php
}
