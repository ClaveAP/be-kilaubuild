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
        locale: 'id',
        firstDay: 1,
        timeZone: 'Asia/Jakarta',
        showNonCurrentDates: true,
        fixedWeekCount: false,
        
        // Sumber data events - mengambil dari endpoint Laravel (free/busy)
        events: function(fetchInfo, successCallback) {
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

    // Fungsi untuk menampilkan modal detail event
    function showEventDetails(title, startTime, endTime, location, description) {
        // Buat modal
        const modal = document.createElement('div');
        modal.style.position = 'fixed';
        modal.style.top = '0';
        modal.style.left = '0';
        modal.style.width = '100%';
        modal.style.height = '100%';
        modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
        modal.style.display = 'flex';
        modal.style.justifyContent = 'center';
        modal.style.alignItems = 'center';
        modal.style.zIndex = '1000';
        
        const modalContent = document.createElement('div');
        modalContent.style.backgroundColor = 'white';
        modalContent.style.padding = '20px';
        modalContent.style.borderRadius = '8px';
        modalContent.style.maxWidth = '500px';
        modalContent.style.width = '90%';
        modalContent.style.maxHeight = '80vh';
        modalContent.style.overflowY = 'auto';
        
        modalContent.innerHTML = `
            <div style="margin-bottom: 15px;">
                <h3 style="margin: 0 0 10px 0; color: #333;">${title}</h3>
                <div style="color: #666; font-size: 14px; margin-bottom: 5px;">
                    <strong>Waktu:</strong> ${startTime} - ${endTime}
                </div>
                <div style="color: #666; font-size: 14px; margin-bottom: 15px;">
                    <strong>Lokasi:</strong> ${location}
                </div>
            </div>
            <div style="margin-bottom: 20px;">
                <h4 style="margin: 0 0 8px 0; color: #333;">Deskripsi:</h4>
                <div style="background: #f8f9fa; padding: 12px; border-radius: 4px; border-left: 4px solid #4285f4;">
                    ${description}
                </div>
            </div>
            <button id="closeModal" style="padding: 8px 16px; background: #dc3545; color: white; border: none; border-radius: 4px; cursor: pointer;">
                Tutup
            </button>
        `;
        
        modal.appendChild(modalContent);
        document.body.appendChild(modal);
        
        // Event listener untuk tombol tutup
        document.getElementById('closeModal').addEventListener('click', function() {
            document.body.removeChild(modal);
        });
        
        // Tutup modal ketika klik di luar konten
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    // Render calendar
    calendar.render();
    
    // Fungsi untuk memuat jadwal berdasarkan tanggal yang dipilih
    function loadScheduleForDate(dateStr) {
        // Format tanggal untuk ditampilkan
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
                                    <th>Lokasi</th>
                                    <th>Aksi</th>
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
                        
                        // Buat link lokasi dapat diklik
                        let locationDisplay = event.location || '-';
                        if (locationDisplay === '-') {
                            locationDisplay = '<span style="color: #999; font-style: italic;">Tidak ada lokasi</span>';
                        } else {
                            // Cek apakah lokasi adalah URL
                            if (isValidUrl(locationDisplay)) {
                                locationDisplay = `<a href="${locationDisplay}" target="_blank" rel="noopener noreferrer" style="color: #4285f4; text-decoration: underline;">Buka Lokasi</a>`;
                            }
                        }
                        
                        // TOMBOL DETAIL: Selalu tampilkan tombol detail untuk setiap event
                        const detailButton = `
                            <button class="detail-btn" 
                                    data-title="${event.title || 'Tidak ada judul'}" 
                                    data-start="${startTime}" 
                                    data-end="${endTime}"
                                    data-location="${event.location || 'Tidak ada lokasi'}"
                                    data-description="${event.description || 'Tidak ada deskripsi'}">
                                📋 Detail
                            </button>`;
                        
                        eventsHTML += `
                            <tr>
                                <td>
                                    <div class="event-title">${event.title || 'Tidak ada judul'}</div>
                                </td>
                                <td>${startTime} - ${endTime}</td>
                                <td>${locationDisplay}</td>
                                <td>${detailButton}</td>
                            </tr>
                        `;
                    });
                    
                    eventsHTML += '</tbody></table>';
                    scheduleContent.innerHTML = eventsHTML;
                    
                    // Event listener untuk tombol detail di tabel
                    document.querySelectorAll('.detail-btn').forEach(button => {
                        button.addEventListener('click', function() {
                            const title = this.getAttribute('data-title');
                            const start = this.getAttribute('data-start');
                            const end = this.getAttribute('data-end');
                            const location = this.getAttribute('data-location');
                            const description = this.getAttribute('data-description');
                            
                            showEventDetails(title, start, end, location, description);
                        });
                    });
                } else {
                    scheduleContent.innerHTML = '<p class="no-events">Tidak ada jadwal untuk hari ini.</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching event details:', error);
                scheduleContent.innerHTML = '<p class="no-events">Error memuat jadwal.</p>';
            });
    }
    
    // Fungsi untuk memvalidasi URL
    function isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }
    
    // Memuat jadwal untuk hari ini secara default
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];
    loadScheduleForDate(todayStr);
});