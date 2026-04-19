<!DOCTYPE html>
<html>
<head>
    <title>EcoScan</title>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #f0fdf4;
            color: #065f46;
            transition: background 0.2s;
        }

        h1 {
            text-align: center;
            margin-top: 15px;
        }

        .points {
            font-size: 22px;
            font-weight: bold;
            color: #16a34a;
            text-align: center;
            margin-bottom: 10px;
        }

        .container {
            display: flex;
            justify-content: center;
            gap: 20px;
            padding: 10px;
        }

        .card {
            background: white;
            padding: 15px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 320px;
        }

        #reader {
            position: relative;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
        }

        .scan-line {
            position: absolute;
            width: 100%;
            height: 3px;
            background: #22c55e;
            animation: scan 2s infinite;
        }

        @keyframes scan {
            0% { top: 0; }
            50% { top: 90%; }
            100% { top: 0; }
        }

        #reader__dashboard_section,
        #reader__dashboard_section_swaplink,
        #reader button {
            display: none !important;
        }

        .result {
            margin-top: 10px;
            font-size: 14px;
        }

        .history {
            max-height: 250px;
            overflow-y: auto;
        }

        .history-item {
            background: #f0fdf4;
            padding: 8px;
            border-radius: 10px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        /* 🔥 RESPONSIVE (HP) */
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 90%;
            }

            h1 {
                font-size: 20px;
            }

            .points {
                font-size: 18px;
            }
        }
    </style>
</head>

<body>

<h1>♻️ EcoScan</h1>

<div class="points">
    Total Poin: <span id="totalPoin">0</span>
</div>

<div class="container">

    <div class="card">
        <h3>Scan Sampah</h3>

        <div id="reader">
            <div class="scan-line"></div>
        </div>

        <div id="result" class="result">Arahkan barcode ke kamera</div>
    </div>

    <div class="card">
        <h3>Riwayat</h3>
        <div id="history" class="history"></div>
    </div>

</div>

<script>
let total = localStorage.getItem("totalPoin") ? parseInt(localStorage.getItem("totalPoin")) : 0;
let scannedBarcodes = localStorage.getItem("barcodes") ? JSON.parse(localStorage.getItem("barcodes")) : [];
let historyData = localStorage.getItem("history") ? JSON.parse(localStorage.getItem("history")) : [];
let lastScanTime = 0;

document.getElementById("totalPoin").innerText = total;

let historyDiv = document.getElementById("history");
historyData.forEach(item => {
    let div = document.createElement("div");
    div.className = "history-item";
    div.innerHTML = item;
    historyDiv.appendChild(div);
});

function onScanSuccess(decodedText) {

    let now = new Date().getTime();
    if (now - lastScanTime < 2000) return;
    lastScanTime = now;

    let jenis = decodedText.startsWith("8") ? "Botol Plastik" : "Kaleng";
    let poin = decodedText.startsWith("8") ? 10 : 15;

    total += poin;

    document.getElementById("result").innerHTML =
        "✅ " + jenis + "<br>+" + poin + " poin";

    document.getElementById("totalPoin").innerText = total;

    let itemHTML = jenis + "<br>+" + poin + " poin";

    let div = document.createElement("div");
    div.className = "history-item";
    div.innerHTML = itemHTML;
    historyDiv.prepend(div);

    localStorage.setItem("totalPoin", total);
    localStorage.setItem("history", JSON.stringify(historyData));
}

let scanner = new Html5QrcodeScanner(
    "reader",
    {
        fps: 10,
        qrbox: { width: 300, height: 150 }
    }
);

scanner.render(onScanSuccess);
</script>

</body>
</html>