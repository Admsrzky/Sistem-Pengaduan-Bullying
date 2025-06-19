"use strict";

// Babel-generated helper functions (biarkan ini di bagian atas jika skrip asli Anda menggunakannya)
function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = len.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }


document.addEventListener('DOMContentLoaded', function () {
  'use strict';

  // Inisialisasi Feather Icons
  feather.replace();

  // --- Logika Pengalih Tema (Dark Mode) ---
  const themeSwitcher = document.getElementById("theme-switcher");
  const htmlElement = document.documentElement; // Target elemen <html> untuk Tailwind CSS

  // Variabel terkait Chart untuk pembaruan dinamis
  var charts = {}; // Objek untuk menyimpan instance Chart.js
  var gridLine;
  var titleColor;

  // Fungsi untuk mengatur kelas tema pada elemen HTML dan menyimpannya ke localStorage
  const setTheme = (theme) => {
    if (theme === "dark") {
      htmlElement.classList.add("dark");
      localStorage.theme = "dark"; // Gunakan kunci 'theme' untuk konsistensi
    } else {
      htmlElement.classList.remove("dark");
      localStorage.theme = "light"; // Atur ke 'light'
    }
    // Perbarui warna chart segera setelah perubahan tema
    updateChartColors();
  };

  // Fungsi untuk memperbarui warna chart berdasarkan tema saat ini
  function updateChartColors() {
    const isDarkMode = htmlElement.classList.contains('dark');
    gridLine = isDarkMode ? '#37374F' : '#EEEEEE'; // Warna garis grid mode gelap/terang
    titleColor = isDarkMode ? '#EFF0F6' : '#171717'; // Warna teks/judul mode gelap/terang

    // Perbarui Chart Pengunjung
    if (charts.hasOwnProperty('visitors') && charts.visitors) {
      charts.visitors.options.scales.x.grid.color = gridLine;
      charts.visitors.options.scales.y.ticks.color = titleColor;
      charts.visitors.options.scales.x.ticks.color = titleColor;
      charts.visitors.options.plugins.title.color = titleColor;
      charts.visitors.update();
    }
    // Perbarui Chart Pelanggan (sesuaikan warna sesuai kebutuhan dark mode)
    if (charts.hasOwnProperty('customers') && charts.customers) {
      // Jika Anda ingin ini dinamis, ubah '#fff' menjadi `titleColor` atau variabel lain
      charts.customers.options.plugins.legend.labels.color = '#fff'; // Tetap putih keras untuk chart ini
      charts.customers.options.plugins.title.color = '#fff'; // Tetap putih keras untuk chart ini
      charts.customers.update();
    }
  }

  // Terapkan tema saat memuat halaman berdasarkan localStorage atau preferensi sistem
  if (
    localStorage.theme === "dark" ||
    (!("theme" in localStorage) &&
      window.matchMedia("(prefers-color-scheme: dark)").matches)
  ) {
    setTheme("dark");
  } else {
    setTheme("light");
  }

  // Tambahkan event listener untuk tombol pengalih tema
  if (themeSwitcher) {
    themeSwitcher.addEventListener("click", () => {
      if (htmlElement.classList.contains("dark")) {
        setTheme("light");
      } else {
        setTheme("dark");
      }
    });
  }

  // --- Logika Toggle Sidebar untuk Mobile dan Desktop ---
  const sidebar = document.getElementById("sidebar");
  const sidebarToggleDesktop = document.getElementById("sidebar-toggle"); // Tombol toggle desktop
  const sidebarToggleMobile = document.getElementById("sidebar-toggle-mobile"); // Tombol toggle mobile

  if (sidebarToggleDesktop) {
    sidebarToggleDesktop.addEventListener("click", () => {
      sidebar.classList.toggle("-translate-x-full");
    });
  }
  if (sidebarToggleMobile) {
    sidebarToggleMobile.addEventListener("click", () => {
      sidebar.classList.toggle("-translate-x-full");
    });
  }

  // --- Logika Menu Dropdown ---
  // Menggunakan setupDropdown dari respons sebelumnya, untuk dropdown umum
  const setupDropdown = (buttonId, menuId) => {
    const button = document.getElementById(buttonId);
    const menu = document.getElementById(menuId);
    if (button && menu) {
      button.addEventListener("click", (e) => {
        e.stopPropagation(); // Mencegah klik dokumen agar tidak segera menutup
        menu.classList.toggle("hidden");
      });
    }
  };

  // Fungsi khusus untuk dropdown sidebar bersarang (Siswa, Guru) dengan rotasi ikon
  const setupNestedSidebarDropdown = (buttonId, menuId) => {
    const button = document.getElementById(buttonId);
    const menu = document.getElementById(menuId);
    if (button && menu) {
      button.addEventListener('click', (e) => {
        e.preventDefault(); // Mencegah perilaku tautan default
        menu.classList.toggle('hidden');
        const icon = button.querySelector('i[data-feather="chevron-down"]');
        if (icon) {
          icon.classList.toggle('rotate-180'); // Membutuhkan utilitas Tailwind CSS `rotate-180`
        }
      });
    }
  };

  setupNestedSidebarDropdown("siswa-btn", "siswa-menu");
  setupNestedSidebarDropdown("guru-btn", "guru-menu");
  setupDropdown("notification-btn", "notification-dropdown");
  setupDropdown("profile-btn", "profile-dropdown");
  setupDropdown("action-btn-1", "action-dropdown-1"); // Untuk tombol aksi baris tabel

  // Tutup semua dropdown spesifik saat mengklik di luar
  document.addEventListener("click", (e) => {
    const specificDropdowns = [
      { btn: "notification-btn", menu: "notification-dropdown" },
      { btn: "profile-btn", menu: "profile-dropdown" },
      { btn: "action-btn-1", menu: "action-dropdown-1" },
    ];

    specificDropdowns.forEach((item) => {
      const button = document.getElementById(item.btn);
      const menu = document.getElementById(item.menu);

      if (
        menu &&
        button &&
        !menu.contains(e.target) &&
        !button.contains(e.target)
      ) {
        menu.classList.add("hidden");
      }
    });

    // Tangani penutupan untuk menu sidebar bersarang (siswa, guru)
    const nestedMenus = [
        { btn: "siswa-btn", menu: "siswa-menu" },
        { btn: "guru-btn", menu: "guru-menu" }
    ];

    nestedMenus.forEach((item) => {
        const button = document.getElementById(item.btn);
        const menu = document.getElementById(item.menu);
        if (menu && button && !menu.contains(e.target) && !button.contains(e.target)) {
            menu.classList.add('hidden');
            const icon = button.querySelector('i[data-feather="chevron-down"]');
            if (icon) {
                icon.classList.remove('rotate-180');
            }
        }
    });
  });

  // --- Logika Kotak Centang (Pilih Semua) ---
  (function () {
    var checkAll = document.querySelector('.check-all');
    var checkers = document.querySelectorAll('.form-checkbox'); // Menyesuaikan untuk menargetkan kelas umum
    var checkedSum = document.querySelector('.checked-sum'); // Asumsi elemen ini ada (misalnya, span untuk menampilkan hitungan)

    if (checkAll && checkers.length > 0) { // Pastikan ada checkers untuk didengarkan
      checkAll.addEventListener('change', function () {
        checkers.forEach(function (checker) {
          checker.checked = checkAll.checked;
          if (checkAll.checked) {
            checker.closest('tr').classList.add('active');
          } else {
            checker.closest('tr').classList.remove('active');
          }
        });
        updateCheckedCount();
      });

      checkers.forEach(function (checker) {
        checker.addEventListener('change', function () {
          checker.closest('tr').classList.toggle('active');
          updateCheckedCount();
          // Jika ada checker individu yang tidak dicentang, hapus centang "pilih semua"
          if (!this.checked) {
            checkAll.checked = false;
          } else {
            // Jika semua checker individu dicentang, centang "pilih semua"
            const allChecked = Array.from(checkers).every(chk => chk.checked);
            checkAll.checked = allChecked;
          }
        });
      });

      function updateCheckedCount() {
        if (checkedSum) {
          const totalChecked = document.querySelectorAll('.form-checkbox:checked').length;
          checkedSum.textContent = totalChecked;
        }
      }
      updateCheckedCount(); // Pembaruan awal saat memuat
    }
  })();


  // --- Inisialisasi Chart dan Pembaruan Data/Warna Dinamis ---
  (function () {
    /* Tambahkan gradien ke chart */
    var width, height, gradient; // Dicakup ke IIFE ini

    function getGradient(ctx, chartArea) {
      var chartWidth = chartArea.right - chartArea.left;
      var chartHeight = chartArea.bottom - chartArea.top;

      if (gradient === null || width !== chartWidth || height !== chartHeight) {
        width = chartWidth;
        height = chartHeight;
        gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        gradient.addColorStop(0, 'rgba(255, 255, 255, 0)');
        gradient.addColorStop(1, 'rgba(255, 255, 255, 0.4)');
      }
      return gradient;
    }

    /* Chart Pengunjung */
    var ctxVisitors = document.getElementById('myChart');

    if (ctxVisitors) {
      var myCanvasVisitors = ctxVisitors.getContext('2d');
      // Inisialisasi `charts.visitors` langsung di sini
      charts.visitors = new Chart(myCanvasVisitors, {
        type: 'line',
        data: {
          labels: ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: 'Last 6 months',
            data: [35, 27, 40, 15, 30, 25, 45],
            cubicInterpolationMode: 'monotone',
            tension: 0.4,
            backgroundColor: ['rgba(95, 46, 234, 1)'],
            borderColor: ['rgba(95, 46, 234, 1)'],
            borderWidth: 2
          }, {
            label: 'Previous',
            data: [20, 36, 16, 45, 29, 32, 10],
            cubicInterpolationMode: 'monotone',
            tension: 0.4,
            backgroundColor: ['rgba(75, 222, 151, 1)'],
            borderColor: ['rgba(75, 222, 151, 1)'],
            borderWidth: 2
          }]
        },
        options: {
          scales: {
            y: {
              min: 0,
              max: 100,
              ticks: {
                stepSize: 25,
                color: titleColor // Ini akan diatur pada panggilan `updateChartColors` awal
              },
              grid: {
                display: false
              }
            },
            x: {
              ticks: {
                color: titleColor // Ini akan diatur pada panggilan `updateChartColors` awal
              },
              grid: {
                color: gridLine // Ini akan diatur pada panggilan `updateChartColors` awal
              }
            }
          },
          elements: {
            point: {
              radius: 2
            }
          },
          plugins: {
            legend: {
              position: 'top',
              align: 'end',
              labels: {
                boxWidth: 8,
                boxHeight: 8,
                usePointStyle: true,
                font: {
                  size: 12,
                  weight: '500'
                }
              }
            },
            title: {
              display: true,
              text: ['Visitor statistics', 'Nov - July'],
              align: 'start',
              color: titleColor, // Ini akan diatur pada panggilan `updateChartColors` awal
              font: {
                size: 16,
                family: 'Inter',
                weight: '600',
                lineHeight: 1.4
              }
            }
          },
          tooltips: {
            mode: 'index',
            intersect: false
          },
          hover: {
            mode: 'nearest',
            intersect: true
          }
        }
      });
    }

    /* Chart Pelanggan */
    var customersChartCanvas = document.getElementById('customersChart');

    if (customersChartCanvas) {
      var myCustomersChartContext = customersChartCanvas.getContext('2d');
      // Inisialisasi `charts.customers` langsung di sini
      charts.customers = new Chart(myCustomersChartContext, {
        type: 'line',
        data: {
          labels: ['Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
          datasets: [{
            label: '+958',
            data: [90, 10, 80, 20, 70, 30, 50],
            tension: 0.4,
            backgroundColor: function backgroundColor(context) {
              var chart = context.chart;
              var ctx = chart.ctx,
                chartArea = chart.chartArea;

              if (!chartArea) {
                return null;
              }
              return getGradient(ctx, chartArea);
            },
            borderColor: ['#fff'], // Tetap putih keras atau ubah ke titleColor jika diinginkan
            borderWidth: 2,
            fill: true
          }]
        },
        options: {
          scales: {
            y: {
              display: false
            },
            x: {
              display: false
            }
          },
          elements: {
            point: {
              radius: 1
            }
          },
          plugins: {
            legend: {
              position: 'top',
              align: 'end',
              labels: {
                color: '#fff', // Tetap putih keras atau ubah ke titleColor jika diinginkan
                boxWidth: 0
              }
            },
            title: {
              display: true,
              text: ['New Customers', '28 Daily Avg.'],
              align: 'start',
              color: '#fff', // Tetap putih keras atau ubah ke titleColor jika diinginkan
              font: {
                size: 16,
                family: 'Inter',
                weight: '600',
                lineHeight: 1.4
              },
              padding: {
                top: 20
              }
            }
          },
          maintainAspectRatio: false
        }
      });
    }
    // Panggil updateChartColors di awal setelah semua chart dibuat
    updateChartColors();
  })();
});