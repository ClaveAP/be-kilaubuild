// public/js/calendar.js
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('clientCalendar');
    
    // Inisialisasi FullCalendar
    var clientCalendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: '' // Kosongkan untuk menyembunyikan tombol ganti view
        },
        locale: 'id', // Bahasa Indonesia
        firstDay: 1, // Senin sebagai hari pertama
        timeZone: 'Asia/Jakarta',
        showNonCurrentDates: true,
        fixedWeekCount: false,
        displayEventTime: false,
        
        // Sumber data events - mengambil dari endpoint Laravel (free/busy)
        events: function(fetchInfo, successCallback, failureCallback) {
            console.log('🔍 Fetching busy slots for:', fetchInfo.startStr, 'to', fetchInfo.endStr);
            
            const loadingEl = document.getElementById('loading');
            if (loadingEl) loadingEl.style.display = 'block';
            
            fetch('/calendar/events?start=' + encodeURIComponent(fetchInfo.startStr) + '&end=' + encodeURIComponent(fetchInfo.endStr))
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Busy slots loaded:', data.length, 'slots');
                    successCallback(data);
                })
                .catch(error => {
                    console.error('❌ Error:', error);
                    alert('Gagal memuat data kalender');
                    successCallback([]);
                })
                .finally(() => {
                    if (loadingEl) loadingEl.style.display = 'none';
                });
        },
        
        // Handle klik tanggal
        dateClick: function(info) {
            const clickedDate = new Date(info.dateStr + 'T00:00:00');
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset waktu ke 00:00:00 untuk perbandingan yang akurat
            
            // 1. Cek apakah tanggal yang diklik adalah hari ini atau kemarin
            if (clickedDate <= today) {
                alert('Maaf, hanya tanggal besok dan seterusnya yang dapat dipilih.');
                return;
            }
            
            // 2. Cek apakah tanggal tersebut memiliki event apapun (tidak peduli free/busy)
            const clickedDateStr = clickedDate.toISOString().split('T')[0];
            const hasEvent = clientCalendar.getEvents().some(event => {
                const eventStart = new Date(event.start);
                eventStart.setDate(eventStart.getDate() - 1); // bila tidak di -1 maka tanggal yang besok dari tanggal dengan jadwal yang tidak bisa di klik
                const eventStartStr = eventStart.toISOString().split('T')[0];
                return eventStartStr === clickedDateStr;
            });
            
            if (hasEvent) {
                alert('Maaf, tanggal tersebut sudah memiliki jadwal. Silakan pilih tanggal lain.');
                return;
            }
            
            // 3. Jika lolos kedua pengecekan, buka WhatsApp
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            const formattedDate = clickedDate.toLocaleDateString('id-ID', options);
            
            // Template pesan WhatsApp
            const message = `Saya ingin survei ditanggal ${formattedDate}`;
            
            fetch('/contact/phone')
                .then(response => response.json())
                .then(data => {
                    const phoneNumber = data.no_telp;
                    const formattedPhone = formatPhoneForWhatsApp(phoneNumber);

                    if (formattedPhone) {
                        const whatsappUrl = `https://wa.me/${formattedPhone}?text=${encodeURIComponent(message)}`;
                        window.open(whatsappUrl, '_blank');
                        console.log('📱 Membuka WhatsApp untuk tanggal:', formattedDate);
                    } else {
                        alert('Nomor WhatsApp tidak valid. Silakan hubungi administrator.');
                    }
                })
                .catch(error => {
                    console.error('Gagal memuat nomor WhatsApp:', error);
                    alert('Tidak dapat memuat nomor WhatsApp dari server.');
                });
        },

        dayCellDidMount: function(info) {
            const cellDate = new Date(info.date);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Cek jika tanggal sudah lewat atau hari ini
            if (cellDate <= today) {
                info.el.style.backgroundColor = '#f5f5f5';
                info.el.style.cursor = 'not-allowed';
                info.el.style.opacity = '0.6';
                info.el.style.pointerEvents = 'none'; // Tambahkan ini untuk benar-benar menonaktifkan klik
                return; // Langsung return karena tanggal sudah lewat
            }
            
            // Cek jika tanggal memiliki event apapun (tidak peduli free/busy)
            const dateStr = cellDate.toISOString().split('T')[0];
            const hasEvent = clientCalendar.getEvents().some(event => {
                const eventStart = new Date(event.start);
                eventStart.setDate(eventStart.getDate() - 1);
                const eventStartStr = eventStart.toISOString().split('T')[0];
                return eventStartStr === dateStr;
            });
            
            if (hasEvent) {
                info.el.style.backgroundColor = '#fff5f5';
                info.el.style.cursor = 'not-allowed';
                info.el.style.opacity = '0.7';
                info.el.style.pointerEvents = 'none'; // Tambahkan ini untuk benar-benar menonaktifkan klik
            }
        },
        
        // Tambahkan event render untuk menangani selektor yang lebih spesifik
        eventDidMount: function(info) {
            const eventDate = new Date(info.event.start);
            const dateStr = eventDate.toISOString().split('T')[0];
            const dateCell = document.querySelector(`[data-date="${dateStr}"]`);
            
            if (dateCell) {
                dateCell.style.pointerEvents = 'none';
                dateCell.style.cursor = 'not-allowed';
            }
        },
        
        loading: function(isLoading) {
            const loadingEl = document.getElementById('loading');
            if (loadingEl) {
                loadingEl.style.display = isLoading ? 'block' : 'none';
            }
        }
    });

    function formatPhoneForWhatsApp(phoneNumber) {
        if (!phoneNumber) return null;
        
        // Hapus semua karakter non-digit kecuali tanda +
        let cleaned = phoneNumber.replace(/[^\d+]/g, '');
        
        // Jika diawali dengan 0, ganti dengan 62 (kode Indonesia)
        if (cleaned.startsWith('0')) {
            cleaned = '62' + cleaned.substring(1);
        }
        
        // Jika diawali dengan +62, hapus tanda +
        if (cleaned.startsWith('+62')) {
            cleaned = cleaned.substring(1);
        }
        
        // Pastikan diawali dengan 62 (kode Indonesia)
        if (!cleaned.startsWith('62')) {
            cleaned = '62' + cleaned;
        }
        
        return cleaned;
    }

    // Render calendar
    clientCalendar.render();
});