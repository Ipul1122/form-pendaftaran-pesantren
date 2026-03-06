<?php
session_start();
// Cek login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../../config/config.php';

// =====================================================================
// DATA PERTANYAAN (BANK SOAL - LEVEL HARD - LOGIKA & CAMPURAN)
// =====================================================================
$questions = [
    [
        "question" => "Ayah Rara punya 5 anak: Rere, Riri, Ruru, dan Roro. Siapakah nama anak kelima?",
        "answer" => "Rara" // Logika sederhana tapi sering mengecoh
    ],
    [
        "question" => "Shalat sunnah yang dikerjakan tanpa sujud dan tanpa rukuk adalah shalat...",
        "answer" => "Jenazah" // Agama
    ],
    [
        "question" => "Planet terpanas di tata surya kita, meskipun bukan yang terdekat dengan Matahari, adalah...",
        "answer" => "Venus" // IPA (Banyak yang jawab Merkurius, padahal Venus karena efek rumah kaca)
    ],
    [
        "question" => "Jika kamu sedang lomba lari, lalu kamu menyalip orang di posisi kedua, sekarang kamu di posisi ke berapa?",
        "answer" => "Kedua" // Logika (Banyak yang jawab kesatu)
    ],
    [
        "question" => "Perjanjian yang menandai pengakuan kedaulatan Indonesia oleh Belanda pada tahun 1949 adalah...",
        "answer" => "KMB (Konferensi Meja Bundar)" // IPS Sejarah
    ],
    [
        "question" => "Lanjutkan pola angka berikut: 2, 3, 5, 7, 11, ... Berapakah angka selanjutnya?",
        "answer" => "13" // MTK (Bilangan Prima)
    ],
    [
        "question" => "Negara di Asia Tenggara yang tidak memiliki wilayah laut (terkurung daratan) adalah...",
        "answer" => "Laos" // IPS Geografi
    ],
    [
        "question" => "Benda manakah yang massa jenisnya lebih besar: 1 kg Kapas atau 1 kg Besi?",
        "answer" => "Besi" // IPA Fisika (Berat sama, tapi Besi lebih padat/massa jenis tinggi)
    ],
    [
        "question" => "Siapakah nama paman Nabi Muhammad SAW yang melindunginya berdakwah namun tidak memeluk Islam hingga wafat?",
        "answer" => "Abu Thalib" // Agama Sejarah
    ],
    [
        "question" => "Sebuah toko memberi diskon 50%, lalu ditambah diskon lagi 50% dari sisa harga. Berapa total diskon sebenarnya?",
        "answer" => "75%" // MTK Logika (Bukan 100%. Sisa 50, diskon 50% dari 50 adalah 25. 50+25=75)
    ]
];

$json_questions = json_encode($questions);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerdas Cermat - Level Hard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background-color: #212529; color: white; /* Tema Gelap utk Hard */ }
        
        .timer-bar {
            height: 15px;
            width: 100%;
            background-color: #495057;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .timer-fill {
            height: 100%;
            background-color: #dc3545; /* Merah start */
            width: 100%;
            transition: width 1s linear, background-color 1s linear;
        }
        
        .question-card {
            min-height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #b02a37 0%, #6a1a21 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            font-size: 1.8rem;
            text-align: center;
            padding: 2.5rem;
            margin-bottom: 2rem;
            font-weight: bold;
            border: 2px solid #dc3545;
        }

        /* Kotak Jawaban (Awalnya Tersembunyi) */
        .answer-box {
            display: none;
            background-color: #198754;
            color: white;
            padding: 20px;
            border-radius: 15px;
            font-size: 2rem;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 0 20px rgba(25, 135, 84, 0.8);
            animation: popIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            margin-bottom: 20px;
            border: 2px solid #20c997;
        }

        @keyframes popIn {
            0% { transform: scale(0.5); opacity: 0; }
            100% { transform: scale(1); opacity: 1; }
        }

        .level-badge {
            background-color: #dc3545;
            color: white;
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
            box-shadow: 0 0 10px rgba(220, 53, 69, 0.5);
        }
    </style>
</head>
<body>

    <?php include '../../layouts/sidebarAdmin.php'; ?>

    <audio id="soundTick" loop>
        <source src="../../audio/clock-ticking-sound-effect_tAETVYt.mp3" type="audio/mpeg">
    </audio>
    <audio id="soundAlarm">
        <source src="../../audio/electronic-alarm-clock-buzz-sfx-sound-effect-17-audiotrimmer.mp3" type="audio/mpeg">
    </audio>
    <audio id="soundCorrect">
        <source src="../../audio/correct.mp3" type="audio/mpeg">
    </audio>

    <div class="container-fluid py-4">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-white mb-0">💀 Cerdas Cermat</h2>
                <span class="level-badge">Level: HARD (15 Detik)</span>
            </div>
            <div class="text-end">
                <h4 class="fw-bold mb-0 text-secondary" id="questionCounter">Soal 1 / 10</h4>
            </div>
        </div>

        <div id="gameArea" style="display: none;">
            
            <div class="timer-bar shadow-sm">
                <div class="timer-fill" id="timerFill"></div>
            </div>
            <div class="text-center mb-3 fw-bold fs-3 text-danger" id="timerText">15</div>

            <div class="question-card shadow">
                <span id="questionText">Memuat pertanyaan...</span>
            </div>

            <div id="answerArea" class="answer-box">
                JAWABAN: <br><span id="answerText" class="text-warning">...</span>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-light px-4 fw-bold" onclick="resetGame()">🔄 Reset Game</button>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-warning fw-bold px-4 shadow text-dark" id="skipBtn" onclick="revealAnswer()">
                        👀 Lihat Jawaban
                    </button>
                    <button class="btn btn-primary fw-bold px-5 shadow" id="nextBtn" onclick="nextQuestion()" style="display: none;">
                        Soal Selanjutnya ➝
                    </button>
                </div>
            </div>
        </div>

        <div id="startScreen" class="text-center mt-5">
            <div class="card shadow-sm border-0 p-5 bg-dark text-white" style="border-radius: 25px; border: 1px solid #495057;">
                <h1 class="display-4 fw-bold text-danger mb-3">🔥 Level Hard 🔥</h1>
                <p class="fs-5 text-muted mb-4">
                    Soal Logika, Pengetahuan Umum & Agama.<br>
                    Waktu: <strong>15 Detik</strong> | Mode: <strong>Flashcard</strong><br>
                    <small>Jawaban muncul otomatis saat waktu habis.</small>
                </p>
                <div id="resumeAlert" class="alert alert-info d-none shadow-sm text-dark">
                    <strong>Permainan Tersimpan!</strong> Melanjutkan soal terakhir...
                </div>
                <button class="btn btn-danger btn-lg px-5 py-3 rounded-pill fw-bold shadow" onclick="startGame()">
                    ▶ MULAI TANTANGAN
                </button>
            </div>
        </div>

        <div id="endScreen" class="text-center mt-5" style="display: none;">
            <div class="card shadow border-0 p-5 bg-dark text-white" style="border-radius: 25px;">
                <h1 class="display-1 mb-2">💀</h1>
                <h2 class="fw-bold mb-3">Selesai!</h2>
                <p class="text-muted fs-5">Semua soal level Hard telah ditampilkan.</p>
                <div class="mt-4">
                    <a href="../dashboard.php" class="btn btn-outline-light me-2">Kembali ke Dashboard</a>
                    <button onclick="resetGame()" class="btn btn-danger fw-bold">Main Lagi</button>
                </div>
            </div>
        </div>

    </div>
    </div> 
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // === CONFIG & STATE ===
    const questions = <?= $json_questions; ?>;
    const TIME_LIMIT = 15; // 15 Detik
    const STORAGE_INDEX = 'cerdasCermat_hard_index';
    
    let currentQIndex = 0;
    let timerInterval;
    let timeLeft;
    let isRevealed = false;

    // === DOM ELEMENTS ===
    const startScreen = document.getElementById('startScreen');
    const gameArea = document.getElementById('gameArea');
    const endScreen = document.getElementById('endScreen');
    const resumeAlert = document.getElementById('resumeAlert');
    
    const questionText = document.getElementById('questionText');
    const questionCounter = document.getElementById('questionCounter');
    const timerFill = document.getElementById('timerFill');
    const timerText = document.getElementById('timerText');
    
    const answerArea = document.getElementById('answerArea');
    const answerText = document.getElementById('answerText');
    
    const nextBtn = document.getElementById('nextBtn');
    const skipBtn = document.getElementById('skipBtn');

    // === AUDIO ===
    const audioTick = document.getElementById('soundTick');
    const audioAlarm = document.getElementById('soundAlarm');
    const audioCorrect = document.getElementById('soundCorrect');

    function playTick() { audioTick.currentTime = 0; audioTick.play().catch(e => {}); }
    function stopTick() { audioTick.pause(); audioTick.currentTime = 0; }
    function playAlarm() { stopTick(); audioAlarm.currentTime = 0; audioAlarm.play().catch(e => {}); }
    function playCorrect() { audioCorrect.currentTime = 0; audioCorrect.play().catch(e => {}); }
    function stopAllAudio() { stopTick(); audioAlarm.pause(); audioAlarm.currentTime = 0; audioCorrect.pause(); audioCorrect.currentTime = 0; }

    // === LOAD STORAGE ===
    document.addEventListener("DOMContentLoaded", function() {
        if(localStorage.getItem(STORAGE_INDEX)) {
            currentQIndex = parseInt(localStorage.getItem(STORAGE_INDEX));
            if (currentQIndex >= questions.length) {
                clearStorage();
                currentQIndex = 0; 
            } else {
                resumeAlert.classList.remove('d-none');
            }
        }
        fixSidebarLinks();
    });

    // === GAME LOGIC ===
    function startGame() {
        startScreen.style.display = 'none';
        gameArea.style.display = 'block';
        loadQuestion();
    }

    function loadQuestion() {
        stopAllAudio();
        isRevealed = false;
        localStorage.setItem(STORAGE_INDEX, currentQIndex);

        // Reset UI
        nextBtn.style.display = 'none';
        skipBtn.style.display = 'inline-block';
        skipBtn.disabled = false;
        
        // Sembunyikan Jawaban
        answerArea.style.display = 'none';
        
        // Reset Timer Visual
        timerFill.style.width = '100%';
        timerFill.style.backgroundColor = '#dc3545';
        timerText.innerText = TIME_LIMIT;
        
        // Tampilkan Soal
        let q = questions[currentQIndex];
        questionText.innerText = q.question;
        questionCounter.innerText = `Soal ${currentQIndex + 1} / ${questions.length}`;

        startTimer();
    }

    function startTimer() {
        timeLeft = TIME_LIMIT;
        clearInterval(timerInterval);

        timerInterval = setInterval(() => {
            timeLeft--;
            timerText.innerText = timeLeft;
            let percentage = (timeLeft / TIME_LIMIT) * 100;
            timerFill.style.width = percentage + "%";

            // AUDIO LOGIC
            if (timeLeft === 10) {
                playTick();
                timerFill.style.backgroundColor = '#ffc107'; // Kuning
            }
            if (timeLeft === 5) {
                playAlarm();
            }
            
            // WAKTU HABIS
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                revealAnswer();
            }
        }, 1000);
    }

    function revealAnswer() {
        if (isRevealed) return;
        isRevealed = true;
        
        // Hentikan timer & suara latar
        clearInterval(timerInterval);
        stopAllAudio();
        
        // Mainkan suara 'Ting' (Correct)
        playCorrect();

        // Tampilkan Jawaban
        let q = questions[currentQIndex];
        answerText.innerText = q.answer;
        answerArea.style.display = 'block';

        // Ganti tombol
        skipBtn.style.display = 'none';
        nextBtn.style.display = 'inline-block';
        nextBtn.focus();
    }

    function nextQuestion() {
        currentQIndex++;
        if (currentQIndex < questions.length) {
            loadQuestion();
        } else {
            endGame();
        }
    }

    function endGame() {
        clearStorage();
        gameArea.style.display = 'none';
        endScreen.style.display = 'block';
    }

    function resetGame() {
        if(confirm("Ulangi permainan dari awal?")) {
            stopAllAudio();
            clearInterval(timerInterval);
            clearStorage();
            window.location.reload();
        }
    }

    function clearStorage() {
        localStorage.removeItem(STORAGE_INDEX);
    }

    function fixSidebarLinks() {
        const sidebarLinks = document.querySelectorAll('#sidebar a');
        sidebarLinks.forEach(link => {
            let href = link.getAttribute('href');
            if (href && !href.startsWith('http') && !href.startsWith('#') && !href.startsWith('../')) {
                link.setAttribute('href', '../' + href);
            }
        });
        document.getElementById('btnToggleSidebar').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('toggled');
        });
    }
</script>
</body>
</html>