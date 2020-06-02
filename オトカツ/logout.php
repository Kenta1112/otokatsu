<?php

require('function.php');

debug('「「「「「「「「「「「「「「「「「「「「「「「「「「「');
debug('ログアウト処理');
debug('」」」」」」」」」」」」」」」」」」」」」」」」」」」');
debugLogStart();

debug('ログアウトします');

session_destroy();

header('Location:login.php');