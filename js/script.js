document.addEventListener('DOMContentLoaded', function() {
    // --- 1. Fungsi untuk Menampilkan Notifikasi ---
    function showNotification(message, type = 'info') {
        const notificationContainer = document.createElement('div');
        notificationContainer.className = `message ${type}`;
        notificationContainer.textContent = message;

        const mainContainer = document.querySelector('.container, .form-container');
        if (mainContainer) {
            mainContainer.prepend(notificationContainer); // Tampilkan di atas konten utama
            setTimeout(() => {
                notificationContainer.remove(); // Hapus notifikasi setelah 5 detik
            }, 5000);
        } else {
            console.warn("Tidak ada container utama (.container atau .form-container) untuk menampilkan notifikasi.");
        }
    }

    // --- 2. Penanganan Form Login (Demo Validasi Frontend) ---
    const loginForm = document.querySelector('.form-container form');
    if (loginForm && window.location.pathname.includes('login.html')) {
        loginForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit default

            const username = loginForm.querySelector('#username').value;
            const password = loginForm.querySelector('#password').value;
            const role = loginForm.querySelector('#role').value;

            // Validasi sederhana
            if (username === '' || password === '') {
                showNotification('Username dan Password harus diisi!', 'error');
            } else {
                // Simulasi login sukses berdasarkan role
                if (username === 'admin' && password === 'admin123' && role === 'admin') {
                    showNotification('Login Admin Berhasil (Demo)! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard_admin.html';
                    }, 1500);
                } else if (username === 'mahasiswa' && password === 'user123' && role === 'user') {
                    showNotification('Login Pengguna Berhasil (Demo)! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'dashboard_user.html';
                    }, 1500);
                } else {
                    showNotification('Username, Password, atau Role salah (Demo)!', 'error');
                }
            }
        });
    }

    // --- 3. Penanganan Form Tambah Data (Demo Notifikasi) ---
    const addDataForm = document.querySelector('form'); // Seleksi form pertama yang ditemukan
    if (addDataForm && window.location.pathname.includes('add_data.html')) {
        addDataForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit

            // Ambil nilai input
            const nama = addDataForm.querySelector('#nama').value;
            const nimNip = addDataForm.querySelector('#nim_nip').value;
            const prodiJabatan = addDataForm.querySelector('#prodi_jabatan').value;

            if (nama === '' || nimNip === '' || prodiJabatan === '') {
                showNotification('Nama, NIM/NIP, dan Prodi/Jabatan wajib diisi!', 'error');
            } else {
                showNotification('Data berhasil ditambahkan (Demo)!', 'success');
                // Simulasi reset form setelah sukses
                addDataForm.reset();
            }
        });
    }

    // --- 4. Penanganan Form Edit Data (Demo Notifikasi) ---
    const editDataForm = document.querySelector('form');
    if (editDataForm && window.location.pathname.includes('edit_data.html')) {
        editDataForm.addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form submit

            const nama = editDataForm.querySelector('#nama').value;
            const nimNip = editDataForm.querySelector('#nim_nip').value;
            const prodiJabatan = editDataForm.querySelector('#prodi_jabatan').value;

            if (nama === '' || nimNip === '' || prodiJabatan === '') {
                showNotification('Nama, NIM/NIP, dan Prodi/Jabatan wajib diisi!', 'error');
            } else {
                showNotification('Data berhasil diperbarui (Demo)!', 'success');
                // Dalam skenario nyata, ini akan redirect ke list_data.html
                // setTimeout(() => { window.location.href = 'list_data.html'; }, 1500);
            }
        });
    }

    // --- 5. Penanganan Tombol Hapus (Demo Konfirmasi) ---
    const deleteLinks = document.querySelectorAll('.action-link.delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Mencegah navigasi default

            if (confirm('Apakah Anda yakin ingin menghapus data ini? (Demo)')) {
                showNotification('Data dihapus secara DEMO. Perlukan backend untuk fungsi sesungguhnya.', 'info');
                // Dalam aplikasi nyata, Anda akan mengirim permintaan DELETE ke backend
                // dan kemudian menghapus baris dari tabel atau me-reload halaman.
                this.closest('tr').remove(); // Hapus baris dari DOM untuk demo visual
            }
        });
    });

    // --- 6. Scroll to Top Button (Contoh fitur tambahan UI) ---
    // Tambahkan tombol scroll-to-top di body jika halaman panjang
    // const scrollToTopBtn = document.createElement('button');
    // scrollToTopBtn.textContent = '↑';
    // scrollToTopBtn.className = 'scroll-to-top';
    // document.body.appendChild(scrollToTopBtn);

    // window.addEventListener('scroll', () => {
    //     if (window.pageYOffset > 300) { // Tampilkan setelah scroll 300px
    //         scrollToTopBtn.style.display = 'block';
    //     } else {
    //         scrollToTopBtn.style.display = 'none';
    //     }
    // });

    // scrollToTopBtn.addEventListener('click', () => {
    //     window.scrollTo({
    //         top: 0,
    //         behavior: 'smooth'
    //     });
    // });
    // Anda perlu menambahkan CSS untuk .scroll-to-top jika mengaktifkan ini
});