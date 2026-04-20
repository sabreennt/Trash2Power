<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat - Trash2Power</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-green: #34C759;
            --dark-green: #2BAF4D;
            --light-green: #E8F5E9;
            --pale-green: #F1F8F4;
            --text-dark: #1C1C1E;
            --text-gray: #6B7280;
            --text-light: #9CA3AF;
            --bg-gray: #F5F5F7;
            --white: #FFFFFF;
            --border-color: #E5E7EB;
            --shadow: rgba(0, 0, 0, 0.08);
            --accent-blue: #E8F3FC;
            --accent-yellow: #FFF9E6;
        }

        body {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-gray);
            color: var(--text-dark);
            line-height: 1.6;
            padding-bottom: 80px;
        }

        /* Header */
        .header {
            background: var(--white);
            padding: 20px 24px;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 8px var(--shadow);
            animation: slideDown 0.4s ease-out;
        }

        .header-content {
            display: flex;
            align-items: center;
            gap: 16px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-button {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: var(--pale-green);
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: var(--light-green);
            transform: translateX(-2px);
        }

        .header h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-dark);
        }

        /* Filter Tabs */
        .filter-tabs {
            background: var(--white);
            padding: 16px 24px;
            display: flex;
            gap: 12px;
            overflow-x: auto;
            scrollbar-width: none;
            animation: fadeIn 0.5s ease-out 0.1s both;
        }

        .filter-tabs::-webkit-scrollbar {
            display: none;
        }

        .tab {
            padding: 10px 20px;
            border-radius: 20px;
            background: var(--bg-gray);
            border: none;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            color: var(--text-gray);
            cursor: pointer;
            transition: all 0.3s ease;
            white-space: nowrap;
            position: relative;
            overflow: hidden;
        }

        .tab::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s ease;
        }

        .tab:hover::before {
            left: 100%;
        }

        .tab.active {
            background: var(--primary-green);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(52, 199, 89, 0.3);
        }

        /* Content Container */
        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        /* Summary Cards */
        .summary-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
            animation: fadeIn 0.6s ease-out 0.2s both;
        }

        .summary-card {
            background: var(--white);
            padding: 24px;
            border-radius: 16px;
            box-shadow: 0 2px 8px var(--shadow);
            transition: all 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .summary-card h3 {
            font-size: 14px;
            color: var(--text-gray);
            font-weight: 500;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-green);
        }

        .summary-card .subtext {
            font-size: 12px;
            color: var(--text-light);
            margin-top: 4px;
        }

        /* History Items */
        .history-section {
            animation: fadeIn 0.7s ease-out 0.3s both;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title::before {
            content: '';
            width: 4px;
            height: 20px;
            background: var(--primary-green);
            border-radius: 2px;
        }

        .history-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .history-item {
            background: var(--white);
            padding: 20px;
            border-radius: 16px;
            box-shadow: 0 2px 8px var(--shadow);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            animation: slideUp 0.5s ease-out both;
        }

        .history-item:nth-child(1) { animation-delay: 0.1s; }
        .history-item:nth-child(2) { animation-delay: 0.15s; }
        .history-item:nth-child(3) { animation-delay: 0.2s; }
        .history-item:nth-child(4) { animation-delay: 0.25s; }
        .history-item:nth-child(5) { animation-delay: 0.3s; }

        .history-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: var(--primary-green);
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .history-item:hover::before {
            transform: scaleY(1);
        }

        .history-item:hover {
            transform: translateX(4px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
        }

        .history-header {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 12px;
        }

        .icon-container {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 24px;
            transition: transform 0.3s ease;
        }

        .history-item:hover .icon-container {
            transform: scale(1.1) rotate(5deg);
        }

        .icon-scan {
            background: linear-gradient(135deg, #E8F5E9 0%, #C8E6C9 100%);
        }

        .icon-redeem {
            background: linear-gradient(135deg, #FFF9E6 0%, #FFE082 100%);
        }

        .history-info {
            flex: 1;
        }

        .history-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 4px;
        }

        .history-date {
            font-size: 13px;
            color: var(--text-light);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .history-details {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .detail-label {
            font-size: 11px;
            color: var(--text-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .detail-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
        }

        .detail-value.points {
            color: var(--primary-green);
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            align-self: flex-start;
        }

        .status-success {
            background: var(--light-green);
            color: var(--dark-green);
        }

        .status-pending {
            background: var(--accent-yellow);
            color: #F59E0B;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            animation: fadeIn 0.5s ease-out;
        }

        .empty-icon {
            font-size: 64px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .empty-text {
            font-size: 14px;
            color: var(--text-gray);
        }

        /* Animations */
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content {
                padding: 16px;
            }

            .summary-section {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 20px;
            }

            .history-details {
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <button class="back-button" onclick="history.back()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
            </button>
            <h1>Riwayat Aktivitas</h1>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
        <button class="tab active" data-filter="all">Semua</button>
        <button class="tab" data-filter="scan">Hasil Scan</button>
        <button class="tab" data-filter="redeem">Penukaran</button>
        <button class="tab" data-filter="month">Bulan Ini</button>
        <button class="tab" data-filter="week">Minggu Ini</button>
    </div>

    <!-- Content -->
    <div class="content">
        <!-- Summary Section -->
        <div class="summary-section">
            <div class="summary-card">
                <h3>Total Scan</h3>
                <div class="value">23</div>
                <div class="subtext">+5 minggu ini</div>
            </div>
            <div class="summary-card">
                <h3>Total Penukaran</h3>
                <div class="value">8 kali</div>
                <div class="subtext">15 item ditukar</div>
            </div>
            <div class="summary-card">
                <h3>Poin Terkumpul</h3>
                <div class="value">Rp 300</div>
                <div class="subtext">Siap ditukar</div>
            </div>
        </div>

        <!-- History Section -->
        <div class="history-section">
            <h2 class="section-title">Riwayat Terbaru</h2>
            <div class="history-list" id="historyList">
                <!-- Scan Items -->
                <div class="history-item" data-type="scan" data-date="2026-04-20">
                    <div class="history-header">
                        <div class="icon-container icon-scan">♻️</div>
                        <div class="history-info">
                            <div class="history-title">Scan Sampah Botol Plastik</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                20 April 2026, 14:30
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Sampah</span>
                            <span class="detail-value">Botol Plastik PET</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jumlah</span>
                            <span class="detail-value">3 botol</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Didapat</span>
                            <span class="detail-value points">+Rp 45</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-value">Jakarta Selatan</span>
                        </div>
                    </div>
                </div>

                <div class="history-item" data-type="redeem" data-date="2026-04-19">
                    <div class="history-header">
                        <div class="icon-container icon-redeem">🎁</div>
                        <div class="history-info">
                            <div class="history-title">Penukaran Pulsa Rp 50.000</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                19 April 2026, 10:15
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Reward</span>
                            <span class="detail-value">Pulsa Telkomsel</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nilai</span>
                            <span class="detail-value">Rp 50.000</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Terpakai</span>
                            <span class="detail-value points">-Rp 250</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nomor HP</span>
                            <span class="detail-value">0812****5678</span>
                        </div>
                    </div>
                </div>

                <div class="history-item" data-type="scan" data-date="2026-04-18">
                    <div class="history-header">
                        <div class="icon-container icon-scan">♻️</div>
                        <div class="history-info">
                            <div class="history-title">Scan Sampah Kaleng Aluminium</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                18 April 2026, 16:45
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Sampah</span>
                            <span class="detail-value">Kaleng Minuman</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jumlah</span>
                            <span class="detail-value">5 kaleng</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Didapat</span>
                            <span class="detail-value points">+Rp 75</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-value">Jakarta Pusat</span>
                        </div>
                    </div>
                </div>

                <div class="history-item" data-type="scan" data-date="2026-04-17">
                    <div class="history-header">
                        <div class="icon-container icon-scan">♻️</div>
                        <div class="history-info">
                            <div class="history-title">Scan Sampah Kardus</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                17 April 2026, 09:20
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Sampah</span>
                            <span class="detail-value">Kardus Bekas</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Berat</span>
                            <span class="detail-value">2.5 kg</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Didapat</span>
                            <span class="detail-value points">+Rp 60</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-value">Jakarta Barat</span>
                        </div>
                    </div>
                </div>

                <div class="history-item" data-type="redeem" data-date="2026-04-15">
                    <div class="history-header">
                        <div class="icon-container icon-redeem">🎁</div>
                        <div class="history-info">
                            <div class="history-title">Penukaran Token Listrik</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                15 April 2026, 13:00
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Reward</span>
                            <span class="detail-value">Token PLN</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nilai</span>
                            <span class="detail-value">Rp 20.000</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Terpakai</span>
                            <span class="detail-value points">-Rp 100</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">ID Pelanggan</span>
                            <span class="detail-value">1234****8901</span>
                        </div>
                    </div>
                </div>

                <div class="history-item" data-type="scan" data-date="2026-04-14">
                    <div class="history-header">
                        <div class="icon-container icon-scan">♻️</div>
                        <div class="history-info">
                            <div class="history-title">Scan Sampah Botol Kaca</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                14 April 2026, 11:30
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Sampah</span>
                            <span class="detail-value">Botol Kaca Bening</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Jumlah</span>
                            <span class="detail-value">2 botol</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Didapat</span>
                            <span class="detail-value points">+Rp 40</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-value">Jakarta Timur</span>
                        </div>
                    </div>
                </div>

                <div class="history-item" data-type="scan" data-date="2026-04-13">
                    <div class="history-header">
                        <div class="icon-container icon-scan">♻️</div>
                        <div class="history-info">
                            <div class="history-title">Scan Sampah Kertas</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                13 April 2026, 15:10
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Sampah</span>
                            <span class="detail-value">Kertas Koran</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Berat</span>
                            <span class="detail-value">1.8 kg</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Didapat</span>
                            <span class="detail-value points">+Rp 35</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-value">Jakarta Utara</span>
                        </div>
                    </div>
                </div>

                <div class="history-item" data-type="redeem" data-date="2026-04-10">
                    <div class="history-header">
                        <div class="icon-container icon-redeem">🎁</div>
                        <div class="history-info">
                            <div class="history-title">Penukaran E-Wallet GoPay</div>
                            <div class="history-date">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 6v6l4 2"/>
                                </svg>
                                10 April 2026, 08:45
                            </div>
                        </div>
                        <span class="status-badge status-success">Berhasil</span>
                    </div>
                    <div class="history-details">
                        <div class="detail-item">
                            <span class="detail-label">Jenis Reward</span>
                            <span class="detail-value">Saldo GoPay</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nilai</span>
                            <span class="detail-value">Rp 30.000</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Poin Terpakai</span>
                            <span class="detail-value points">-Rp 150</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Nomor HP</span>
                            <span class="detail-value">0812****5678</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab filtering
        const tabs = document.querySelectorAll('.tab');
        const historyItems = document.querySelectorAll('.history-item');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Update active tab
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                const filter = tab.dataset.filter;
                
                historyItems.forEach(item => {
                    const itemType = item.dataset.type;
                    const itemDate = new Date(item.dataset.date);
                    const now = new Date();
                    const weekAgo = new Date(now.getTime() - 7 * 24 * 60 * 60 * 1000);
                    const monthAgo = new Date(now.getFullYear(), now.getMonth() - 1, now.getDate());
                    
                    let show = false;
                    
                    switch(filter) {
                        case 'all':
                            show = true;
                            break;
                        case 'scan':
                            show = itemType === 'scan';
                            break;
                        case 'redeem':
                            show = itemType === 'redeem';
                            break;
                        case 'week':
                            show = itemDate >= weekAgo;
                            break;
                        case 'month':
                            show = itemDate >= monthAgo;
                            break;
                    }
                    
                    if (show) {
                        item.style.display = 'block';
                        setTimeout(() => {
                            item.style.opacity = '1';
                            item.style.transform = 'translateY(0)';
                        }, 10);
                    } else {
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            item.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });

        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add loading animation on page load
        window.addEventListener('load', () => {
            document.body.classList.add('loaded');
        });
    </script>
</body>
</html>