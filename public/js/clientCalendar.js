// public/js/calendar.js
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
            
            // 2. Cek apakah tanggal tersebut memiliki event sibuk
            const clickedDateStr = clickedDate.toISOString().split('T')[0];
            const hasBusyEvent = clientCalendar.getEvents().some(event => {
                const eventStart = new Date(event.start);
                const eventStartStr = eventStart.toISOString().split('T')[0];
                return eventStartStr === clickedDateStr;
            });
            
            if (hasBusyEvent) {
                alert('Maaf, tanggal tersebut sudah ditandai sibuk. Silakan pilih tanggal lain.');
                return;
            }
            
            // 3. Jika lolos kedua pengecekan, buka WhatsApp
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
            
            // Format nomor untuk WhatsApp
            // const formattedPhone = formatPhoneForWhatsApp(phoneNumber);
            
            // if (formattedPhone) {
            //     // Buat link WhatsApp
            //     const whatsappUrl = `https://wa.me/${formattedPhone}?text=${encodeURIComponent(message)}`;
                
            //     // Buka WhatsApp di tab baru
            //     window.open(whatsappUrl, '_blank');
                
            //     console.log('📱 Membuka WhatsApp untuk tanggal:', formattedDate);
            // } else {
            //     alert('Nomor WhatsApp tidak valid. Silakan hubungi administrator.');
            // }
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
            
            // Cek jika tanggal memiliki event sibuk
            const dateStr = cellDate.toISOString().split('T')[0];
            const hasBusyEvent = clientCalendar.getEvents().some(event => {
                const eventStart = new Date(event.start);
                const eventStartStr = eventStart.toISOString().split('T')[0];
                return eventStartStr === dateStr;
            });
            
            if (hasBusyEvent) {
                info.el.style.backgroundColor = '#fff5f5';
                info.el.style.cursor = 'not-allowed';
                info.el.style.opacity = '0.7';
            }
        },
        
        // eventTimeFormat: {
        //     hour: '2-digit',
        //     minute: '2-digit',
        //     hour12: false
        // },
        
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
    
    // Fungsi untuk memuat jadwal berdasarkan tanggal yang dipilih
    function loadScheduleForDate(dateStr) {
        // Format tanggal untuk ditampilkan (tetap sama)
        const clickedDate = new Date(dateStr + 'T00:00:00');
        const options = { 
            weekday: 'long', 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        };
        selectedDateEl.textContent = clickedDate.toLocaleDateString('id-ID', options);
        
        // Tampilkan loading di tabel jadwal
        scheduleContent.innerHTML = '<p>Memuat data jadwal...</p>';
        
        // Ambil data event detail untuk tanggal yang diklik
        const start = new Date(clickedDate).toISOString();
        const end = new Date(clickedDate.getTime() + 24 * 60 * 60 * 1000).toISOString();

        fetch(`/calendar/event-details?start=${start}&end=${end}`)
            .then(response => response.json())
            .then(events => {
                if (events.length > 0) {
                    let eventsHTML = `
                        <table class="schedule-table">
                            <thead>
                                <tr>
                                    <th>Kegiatan</th>
                                    <th>Waktu</th>
                                    <th>Lokasi</th>  <!-- UBAH: WhatsApp → Lokasi -->
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    
                    events.forEach(event => {
                        const startTime = event.start ? new Date(event.start).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '00:00';
                        const endTime = event.end ? new Date(event.end).toLocaleTimeString('id-ID', {
                            hour: '2-digit',
                            minute: '2-digit'
                        }) : '00:00';
                        
                        // UBAH: Ganti WhatsApp dengan lokasi
                        let locationDisplay = event.location || '-';
                        if (locationDisplay === '-') {
                            locationDisplay = '<span style="color: #999; font-style: italic;">Tidak ada lokasi</span>';
                        }
                        
                        eventsHTML += `
                            <tr>
                            <td>
                                    <div class="event-title">${event.title || 'Tidak ada judul'}</div>
                                    
                                </td>
                                <td>${startTime} - ${endTime}</td>
                                <td>${locationDisplay}</td>  <!-- UBAH: whatsappDisplay → locationDisplay -->
                            </tr>
                        `;
                    });
                    
                    eventsHTML += '</tbody></table>';
                    scheduleContent.innerHTML = eventsHTML;
                } else {
                    scheduleContent.innerHTML = '<p class="no-events">Tidak ada jadwal untuk hari ini.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching event details:', error);
                scheduleContent.innerHTML = '<p class="no-events">Error memuat jadwal.</p>';
            });
    }
    
    // Memuat jadwal untuk hari ini secara default
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    loadScheduleForDate(todayStr);
});