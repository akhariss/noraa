<?php
require 'config/app.php';
require 'app/Adapters/Database.php';
require 'app/Adapters/Logger.php';
require 'app/Domain/Repositories/BaseRepository.php';
require 'app/Domain/Entities/Registrasi.php';
require 'app/Domain/Entities/Klien.php';
require 'app/Domain/Entities/AuditLog.php';
require 'modules/Main/Controller.php';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST['nomor_registrasi'] = 'NP-20260411-7653';
$c = new Modules\Main\Controller();
$c->tracking();
