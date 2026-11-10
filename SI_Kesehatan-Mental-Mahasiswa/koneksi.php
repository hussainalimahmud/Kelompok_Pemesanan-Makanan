<?php
/**
 * File Koneksi Database & Konfigurasi Sistem
 * Sistem Pendukung Keputusan Skrining Kesehatan Mental Mahasiswa (SPK AI)
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'db_spk_mental';

$pdo = null;
$db_error = null;

try {
    $pdo = new PDO("mysql:host={$db_host};charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `{$db_name}`");

} catch (PDOException $e) {
    $db_error = $e->getMessage();
    $pdo = null;
}

/**
 * Dual Engine ML Predictor: Flask API + Python CLI Fallback
 */
function panggil_model_ml($data_input) {
    $api_url = 'http://127.0.0.1:5000/predict';
    $json_payload = json_encode($data_input);

    // 1. Coba hubungi Flask API via cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($json_payload)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response && $http_code === 200) {
        $result = json_decode($response, true);
        if (isset($result['status']) && $result['status'] === 'success') {
            return $result;
        }
    }

    // 2. Fallback: Eksekusi Python CLI langsung via STDIN
    $script_path = __DIR__ . DIRECTORY_SEPARATOR . 'predict_cli.py';
    $descriptorspec = [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
        2 => ["pipe", "w"]
    ];

    $process = proc_open("python \"{$script_path}\"", $descriptorspec, $pipes, __DIR__);
    if (is_resource($process)) {
        fwrite($pipes[0], $json_payload);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        proc_close($process);

        $cli_result = json_decode(trim($stdout), true);
        if ($cli_result && isset($cli_result['status']) && $cli_result['status'] === 'success') {
            return $cli_result;
        }
    }

    return [
        'status' => 'error',
        'message' => 'Gagal memproses model ML. Pastikan Python terinstall.'
    ];
}

function is_dosen_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'dosen';
}

function is_mahasiswa_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'mahasiswa';
}
?>
