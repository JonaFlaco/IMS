<?php
    $email = \App\Core\Application::getInstance()->settings->get('APP_EMAIL');
?>

<p>Access form your IP address has been blocked, please contact <strong><?= $email ?></strong> if you think there is a mistake.</p>

</p>Your IP is <?= $data['ip_address'] ?>.</p>
