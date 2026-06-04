<?php
header('Content-Type: application/json');
include "db.php";
include "config.php"; // pastikan $tinggi_toren & $volume_penuh sudah didefinisikan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil data dari ESP8266 dengan validasi
    $tinggi = isset($_POST['tinggi_air']) ? floatval($_POST['tinggi_air']) : 0;
    $volume = isset($_POST['volume_air']) ? floatval($_POST['volume_air']) : 0;
    $persen = isset($_POST['persen_air']) ? floatval($_POST['persen_air']) : 0;
    $biaya  = isset($_POST['total_pemakaian']) ? floatval($_POST['total_pemakaian']) : 0;

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO sensor_data (tinggi_air, volume_air, persen_air, total_pemakaian) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("dddd", $tinggi, $volume, $persen, $totalLitres);

    if ($stmt->execute()) {

        // --- Notifikasi Telegram ---
        $token = "8239880836:AAEJ8pzj7lQI8cSsuM-e48hMCDvN-Y3-O3s"; // ganti dengan token bot kamu
        $list_chat_ids = [
            "6897736925", // rozaq 2
            "5804929016", // rozaq 1
            "1510161527", // dini
            "6779481253", // afe
        ];

        $pesan = "";
        if ($persen >= 95) {
            $pesan = "🚨 Toren PENUH ({$persen}%). Segera matikan pompa!";
        } elseif ($persen <= 10) {
            $pesan = "🔴 Toren KOSONG ({$persen}%). Segera isi ulang!";
        } elseif ($persen >= 40 && $persen <= 60) {
            $pesan = "🟡 Toren SETENGAH ({$persen}%). Status normal.";
        }

        if (!empty($pesan)) {
            $url = "https://api.telegram.org/bot$token/sendMessage";

            foreach ($list_chat_ids as $chat_id) {
                $data = [
                    'chat_id' => $chat_id,
                    'text' => $pesan
                ];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);

                if (curl_errno($ch)) {
                    error_log("Curl error: " . curl_error($ch));
                } else {
                    error_log("Telegram response: " . $response);
                }

                curl_close($ch);
            }
        }

        echo json_encode([
            "status" => "OK",
            "tinggi_toren" => $tinggi_toren ?? 0,
            "volume_penuh" => $volume_penuh ?? 0
        ]);

    } else {
        echo json_encode([
            "status" => "ERROR",
            "message" => $stmt->error
        ]);
    }

    $stmt->close();
} else {
    echo json_encode(["status" => "Invalid Request"]);
}
?>
