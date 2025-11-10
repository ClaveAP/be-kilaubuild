<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Free/Busy Viewer</title>
    
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .calendar-container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        
        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .calendar-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        
        .status-legend {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 4px;
        }
        
        .status-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-color {
            width: 16px;
            height: 16px;
            border-radius: 3px;
        }
        
        .busy-color {
            background-color: #ff4444;
        }
        
        .loading {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 20px;
            border-radius: 5px;
            z-index: 1000;
        }

        /* Layout baru dengan dua kolom */
        .calendar-layout {
            display: flex;
            gap: 20px;
        }
        
        .calendar-section {
            flex: 2;
        }
        
        .schedule-section {
            flex: 1;
            min-width: 300px;
            background: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
        }
        
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .schedule-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .selected-date {
            font-size: 14px;
            color: #666;
        }
        
        .no-events {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 20px;
        }
        
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        .schedule-table th {
            background-color: #e9ecef;
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
            font-weight: 600;
        }
        
        .schedule-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        
        .schedule-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .event-detail {
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #4285f4;
        }
        
        .event-time {
            font-weight: bold;
            color: #4285f4;
        }
        
        .event-title {
            font-weight: bold;
            margin: 5px 0;
        }
        
        .event-description {
            color: #666;
            font-size: 14px;
        }
        
        .whatsapp-link {
            color: #25D366;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 4px;
            background-color: rgba(37, 211, 102, 0.1);
            transition: background-color 0.2s;
        }
        
        .whatsapp-link:hover {
            background-color: rgba(37, 211, 102, 0.2);
            text-decoration: none;
            color: #128C7E;
        }
        
        .whatsapp-icon {
            font-size: 16px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .calendar-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-legend {
                flex-direction: column;
                gap: 10px;
            }
            
            .calendar-layout {
                flex-direction: column;
            }
            
            .schedule-section {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <div class="calendar-container">
        <div class="calendar-header">
            <div class="calendar-title">📅 Kalender - Status Sibuk/Tersedia</div>
        </div>

        <div class="status-legend">
            <div class="status-item">
                <div class="status-color busy-color"></div>
                <span><strong>Sibuk:</strong> Waktu tidak tersedia</span>
            </div>
            <div class="status-item">
                <div class="status-color" style="background-color: transparent; border: 1px dashed #ddd"></div>
                <span><strong>Tersedia:</strong> Waktu kosong</span>
            </div>
        </div>

        <!-- Loading indicator -->
        <div id="loading" class="loading">
            ⏳ Memuat data kalender...
        </div>

        <!-- Layout baru dengan kalender dan tabel jadwal -->
        <div class="calendar-layout">
            <!-- Kolom kiri: Kalender -->
            <div class="calendar-section">
                <div id="calendar"></div>
            </div>
            
            <!-- Kolom kanan: Tabel Jadwal -->
            <div class="schedule-section">
                <div class="schedule-header">
                    <div class="schedule-title">Jadwal Harian</div>
                    <div class="selected-date" id="selectedDate">Pilih tanggal di kalender</div>
                </div>
                <div id="scheduleContent">
                    <p class="no-events">Silakan pilih tanggal di kalender untuk melihat jadwal.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/id.js"></script>
    
    <!-- File JavaScript terpisah -->
    <script src="{{ asset('js/calendar.js') }}"></script>
</body>
</html>