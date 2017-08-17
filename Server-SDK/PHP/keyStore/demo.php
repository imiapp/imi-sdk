<?php
require_once 'lib/KeyStore.class.php';

$filename = KeyStore::createEncryptFile('123456','5854756c674c6b326a4143725354413769574472353158457243656a31535253', '.');
print_r(KeyStore::decrypt('123456',$filename));
