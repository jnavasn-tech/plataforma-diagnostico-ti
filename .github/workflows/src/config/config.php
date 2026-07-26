<?php
require_once __DIR__ . '/env.php';

define('NOMBRE_PLATAFORMA', 'Plataforma de Diagnóstico y Capacitación Técnica');
define('IA_PROVIDER', getenv('IA_PROVIDER') ?: 'openai');
define('IA_API_KEY', getenv('IA_API_KEY') ?: '');
define('IA_MODEL', getenv('IA_MODEL') ?: 'gpt-4o-mini');

session_start();
