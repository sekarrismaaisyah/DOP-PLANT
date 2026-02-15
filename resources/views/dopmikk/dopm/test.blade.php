<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading Calendar Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding: 20px 0;
        }

        .dashboard-header {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .calendar-wrapper {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-top: 20px;
        }

        .day-header {
            text-align: center;
            font-weight: 600;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .day-cell {
            aspect-ratio: 1;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .day-cell:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .day-cell.positive {
            background: linear-gradient(135deg, #00c853 0%, #00e676 100%);
            border: 2px solid #00ff7f;
        }

        .day-cell.negative {
            background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);
            border: 2px solid #ff1744;
        }

        .day-cell.neutral {
            background: linear-gradient(135deg, #ff6f00 0%, #ff9800 100%);
            border: 2px solid #ffa726;
        }

        .day-cell.empty {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            cursor: default;
        }

        .day-cell.empty:hover {
            transform: none;
            box-shadow: none;
        }

        .day-number {
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-info {
            margin-top: auto;
        }

        .stock-ticker {
            font-size: 0.75rem;
            font-weight: 600;
            opacity: 0.9;
            margin-bottom: 3px;
        }

        .stock-value {
            font-size: 1.3rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stock-change {
            font-size: 0.8rem;
            font-weight: 600;
            opacity: 0.9;
        }

        .direction-icon {
            font-size: 1rem;
        }

        .legend {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 30px;
            height: 30px;
            border-radius: 5px;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 10px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .stats-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .btn-month {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            padding: 8px 20px;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .btn-month:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        @media (max-width: 768px) {
            .calendar-grid {
                gap: 5px;
            }
            
            .day-cell {
                min-height: 100px;
                padding: 5px;
            }
            
            .stock-value {
                font-size: 1rem;
            }
            
            .day-number {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="calendar-wrapper">
            <div class="calendar-header">
                <button class="btn btn-month" id="prevMonth">
                    <i class="bi bi-chevron-left"></i> Bulan Sebelumnya
                </button>
                <h3 class="mb-0" id="currentMonth"></h3>
                <button class="btn btn-month" id="nextMonth">
                    Bulan Berikutnya <i class="bi bi-chevron-right"></i>
                </button>
            </div>

            <div class="calendar-grid">
                <div class="day-header">Minggu</div>
                <div class="day-header">Senin</div>
                <div class="day-header">Selasa</div>
                <div class="day-header">Rabu</div>
                <div class="day-header">Kamis</div>
                <div class="day-header">Jumat</div>
                <div class="day-header">Sabtu</div>
            </div>

            <div class="calendar-grid" id="calendarDays"></div>

            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #00c853 0%, #00e676 100%);"></div>
                    <span>Naik (Positif)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);"></div>
                    <span>Turun (Negatif)</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: linear-gradient(135deg, #ff6f00 0%, #ff9800 100%);"></div>
                    <span>Flat (0%)</span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample stock tickers for Indonesian market
        const stockTickers = ['BBCA', 'BBRI', 'BMRI', 'TLKM', 'ASII', 'UNVR', 'INDF', 'INCI', 'INCO', 'INKP', 'INDO', 'INDR', 'INDS', 'INDX', 'INDY'];

        let currentDate = new Date();
        let currentMonth = currentDate.getMonth();
        let currentYear = currentDate.getFullYear();

        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = 
                now.toLocaleDateString('id-ID', options);
        }

        function generateRandomStockData() {
            const change = (Math.random() - 0.5) * 10; // -5% to +5%
            const value = Math.floor(Math.random() * 10000) + 100;
            const ticker = stockTickers[Math.floor(Math.random() * stockTickers.length)];
            
            return {
                ticker: ticker,
                value: value,
                change: change,
                changePercent: change.toFixed(2)
            };
        }

        function renderCalendar(month, year) {
            const calendarDays = document.getElementById('calendarDays');
            calendarDays.innerHTML = '';

            const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            let posCount = 0, negCount = 0, neuCount = 0;
            let totalChange = 0;

            // Empty cells before first day
            for (let i = 0; i < firstDay; i++) {
                const emptyCell = document.createElement('div');
                emptyCell.className = 'day-cell empty';
                calendarDays.appendChild(emptyCell);
            }

            // Days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dayCell = document.createElement('div');
                const stockData = generateRandomStockData();
                
                const isWeekend = new Date(year, month, day).getDay() === 0 || 
                                 new Date(year, month, day).getDay() === 6;
                
                if (isWeekend) {
                    dayCell.className = 'day-cell empty';
                    dayCell.innerHTML = `
                        <div class="day-number">${day}</div>
                        <div class="text-center mt-3 opacity-50">
                            <small>Weekend</small>
                        </div>
                    `;
                } else {
                    let cellClass = 'day-cell ';
                    let icon = '';
                    
                    if (stockData.change > 0.5) {
                        cellClass += 'positive';
                        icon = '<i class="bi bi-arrow-up-short direction-icon"></i>';
                        posCount++;
                    } else if (stockData.change < -0.5) {
                        cellClass += 'negative';
                        icon = '<i class="bi bi-arrow-down-short direction-icon"></i>';
                        negCount++;
                    } else {
                        cellClass += 'neutral';
                        icon = '<i class="bi bi-dash direction-icon"></i>';
                        neuCount++;
                    }

                    totalChange += stockData.change;

                    dayCell.className = cellClass;
                    dayCell.innerHTML = `
                        <div class="day-number">${day} ${icon}</div>
                        <div class="stock-info">
                            <div class="stock-ticker">${stockData.ticker}</div>
                            <div class="stock-value">
                                ${stockData.value.toLocaleString('id-ID')}
                            </div>
                            <div class="stock-change">
                                ${stockData.change > 0 ? '+' : ''}${stockData.changePercent}%
                            </div>
                        </div>
                    `;

                    dayCell.onclick = () => {
                        alert(`Detail ${day} ${monthNames[month]} ${year}\n\n` +
                              `Ticker: ${stockData.ticker}\n` +
                              `Nilai: ${stockData.value.toLocaleString('id-ID')}\n` +
                              `Perubahan: ${stockData.changePercent}%`);
                    };
                }

                calendarDays.appendChild(dayCell);
            }

            // Update statistics
            document.getElementById('positiveDays').textContent = posCount;
            document.getElementById('negativeDays').textContent = negCount;
            document.getElementById('neutralDays').textContent = neuCount;
            document.getElementById('monthPerformance').textContent = 
                (totalChange >= 0 ? '+' : '') + totalChange.toFixed(2) + '%';
        }

        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        });

        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
        });

        // Initialize
        updateDateTime();
        setInterval(updateDateTime, 60000); // Update every minute
        renderCalendar(currentMonth, currentYear);
    </script>
</body>
</html>