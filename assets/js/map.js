async function reverseGeocode(lat, lon) {
            const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;
            try {
                const response = await fetch(url, {
                    headers: {
                        'User-Agent': 'LaporanBullyingApp/1.0 (your-email@example.com)'
                    }
                });
                if (!response.ok) throw new Error('Gagal mengambil data lokasi');
                const data = await response.json();
                return data.display_name || '';
            } catch (error) {
                console.error('Error reverse geocoding:', error);
                return '';
            }
        }

        function getLocation() {
            const lokasiInput = document.getElementById('lokasi');
            const statusText = document.getElementById('lokasi-status');
            const mapDiv = document.getElementById('map');

            if (!navigator.geolocation) {
                statusText.textContent = 'Geolocation tidak didukung oleh browser Anda.';
                return;
            }

            navigator.geolocation.getCurrentPosition(async (position) => {
                const lat = position.coords.latitude;
                const lon = position.coords.longitude;

                statusText.textContent = 'Mendapatkan alamat dari koordinat...';

                const address = await reverseGeocode(lat, lon);

                if (address) {
                    lokasiInput.value = address;
                    statusText.textContent = 'Lokasi berhasil dideteksi dan diisi otomatis.';
                } else {
                    statusText.textContent = 'Gagal mendapatkan alamat dari koordinat.';
                }

                const map = L.map('map').setView([lat, lon], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© Open StreetMap'
                }).addTo(map);

                L.marker([lat, lon]).addTo(map)
                    .bindPopup(`<strong>Lokasi Laporan</strong><br>${address}`)
                    .openPopup();

            }, (error) => {
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        statusText.textContent = 'Izin lokasi ditolak. Silakan isi lokasi secara manual.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        statusText.textContent = 'Informasi lokasi tidak tersedia.';
                        break;
                    case error.TIMEOUT:
                        statusText.textContent = 'Permintaan lokasi timeout.';
                        break;
                    default:
                        statusText.textContent = 'Terjadi kesalahan saat mendapatkan lokasi.';
                        break;
                }
            });
        }

        window.addEventListener('load', getLocation);