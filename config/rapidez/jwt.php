<?php
return [
    // What magento signs the JWT with, visible under `configuration > Services > Magento Web API > JWT Authentication`
    'signed_with' => \Lcobucci\JWT\Signer\Hmac\Sha256::class,
];
