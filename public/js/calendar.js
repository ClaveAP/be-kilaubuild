// public/js/calendar.js
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var scheduleContent = document.getElementById('scheduleContent');
    var selectedDateEl = document.getElementById('selectedDate');
    
    // Inisialisasi FullCalendar
    var calendar = new FullCalendar.Calendar(calendarEl, {
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
            loadScheduleForDate(info.dateStr);
        },
        
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        
        loading: function(isLoading) {
            const loadingEl = document.getElementById('loading');
            if (loadingEl) {
                loadingEl.style.display = isLoading ? 'block' : 'none';
            }
        }
    });

    // Render calendar
    calendar.render();
    
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