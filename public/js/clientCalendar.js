document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('clientCalendar');
    var scheduleContent = document.getElementById('scheduleContent');
    var selectedDateEl = document.getElementById('selectedDate');
    
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
        events: function(fetchInfo, successCallback) {
            console.log('🔍 Fetching busy slots for:', fetchInfo.startStr, 'to', fetchInfo.endStr);
            
            const loadingEl = document.getElementById('loading');
            if (loadingEl) loadingEl.style.display = 'block';
            
            fetch('/calendar/events?start=' + encodeURIComponent(fetchInfo.startStr) + '&end=' + encodeURIComponent(fetchInfo.endStr))
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Busy slots loaded:', data.length, 'slots');
                    
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    // filter hanya event di tanggal mendatang
                    const futureEvents = data.filter(event => {
                        const eventStart = new Date(event.start);
                        return eventStart >= today;
                    });
                    
                    // Ubah semua event menjadi "SIBUK" tanpa detail
                    const busyEvents = futureEvents.map(event => ({
                        id: event.id,
                        title: '🟥 Ada Jadwal',
                        start: event.start,
                        end: event.end,
                        allDay: event.allDay,
                        color: '#ff4444',
                        textColor: '#ffffff',
                        borderColor: '#cc0000',
                        // Hapus extendedProps agar tidak ada detail yang terbaca
                        extendedProps: {}
                    }));
                    
                    successCallback(busyEvents);
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
            today.setHours(0, 0, 0, 0);
            
            // Cek apakah tanggal yang diklik adalah hari ini atau kemarin
            if (clickedDate <= today) {
                alert('Maaf, hanya tanggal besok dan seterusnya yang dapat dipilih.');
                return;
            }
            
            // Cek apakah tanggal tersebut memiliki event sibuk
            const clickedDateStr = clickedDate.toISOString().split('T')[0];
            const hasBusyEvent = clientCalendar.getEvents().some(event => {
                const eventStart = new Date(event.start);
                eventStart.setDate(eventStart.getDate() - 1);
                const eventStartStr = eventStart.toISOString().split('T')[0];
                return eventStartStr === clickedDateStr;
            });
            
            if (hasBusyEvent) {
                alert('Maaf, tanggal tersebut sudah ditandai sibuk. Silakan pilih tanggal lain.');
                return;
            }
            
            // Jika lolos kedua pengecekan, buka WhatsApp
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            const formattedDate = clickedDate.toLocaleDateString('id-ID', options);
            
            // Template pesan WhatsApp
            const message = `Saya ingin survei ditanggal ${formattedDate}`;
            
            // Nomor WhatsApp tujuan
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
            }
            
            // Cek jika tanggal memiliki event sibuk (hanya untuk tanggal mendatang)
            if (cellDate > today) {
                const dateStr = cellDate.toISOString().split('T')[0];
                const hasBusyEvent = clientCalendar.getEvents().some(event => {
                    const eventStart = new Date(event.start);
                    eventStart.setDate(eventStart.getDate() - 1);
                    const eventStartStr = eventStart.toISOString().split('T')[0];
                    return eventStartStr === dateStr;
                });
                
                if (hasBusyEvent) {
                    info.el.style.backgroundColor = '#fff5f5';
                    info.el.style.cursor = 'not-allowed';
                    info.el.style.opacity = '0.7';
                }
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
    
    // Fungsi sederhana untuk menampilkan status tanggal
    function loadScheduleForDate(dateStr) {
        const clickedDate = new Date(dateStr + 'T00:00:00');
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        selectedDateEl.textContent = clickedDate.toLocaleDateString('id-ID', options);
        
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        // Cek jika tanggal sudah lewat
        if (clickedDate <= today) {
            scheduleContent.innerHTML = '<p class="past-day">Tanggal ini sudah lewat</p>';
            return;
        }
        
        // Cek apakah tanggal memiliki event
        const dateStrFormatted = clickedDate.toISOString().split('T')[0];
        const hasBusyEvent = clientCalendar.getEvents().some(event => {
            const eventStart = new Date(event.start);
            const eventStartStr = eventStart.toISOString().split('T')[0];
            return eventStartStr === dateStrFormatted;
        });
        
        if (hasBusyEvent) {
            scheduleContent.innerHTML = '<p class="busy-day">Tanggal ini sudah penuh (SIBUK)</p>';
        } else {
            scheduleContent.innerHTML = '<p class="available-day">Tanggal ini tersedia untuk survei</p>';
        }
    }
    
    // Memuat info untuk hari ini secara default
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    loadScheduleForDate(todayStr);
});