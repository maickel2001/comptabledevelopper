<?php
require_once __DIR__ . '/../src/lib/auth.php';
require_once __DIR__ . '/../src/lib/helpers.php';

logout();
redirect('index.php');
