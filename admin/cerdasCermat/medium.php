<?php
session_start();
// Cek login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../../config/config.php';

// =====================================================================
// DATA PERTANYAAN (BANK SOAL - LEVEL MEDIUM)
// =====================================================================
$questions = [
    [
        "question" => "Ibukota Provinsi Jawa Tengah adalah...",
        "options" => ["A. Solo", "B. Semarang"],
        "answer" => 1 // 0=A, 1=B
    ],
    [
        "question" => "Hasil dari 25 x 4 adalah...",
        "options" => ["A. 100", "B. 125"],
        "answer" => 0
    ],
    [
        "question" => "Hewan pemakan daging disebut...",
        "options" => ["A. Herbivora", "B. Karnivora"],
        "answer" => 1
    ],
    [
        "question" => "Lambang sila pertama Pancasila adalah...",
        "options" => ["A. Bintang", "B. Rantai"],
        "answer" => 0
    ],
    [
        "question" => "Bahasa Inggris dari 'Kupu-kupu' adalah...",
        "options" => ["A. Dragonfly", "B. Butterfly"],
        "answer" => 1
    ],
    [
        "question" => "Candi Borobudur terletak di daerah...",
        "options" => ["A. Yogyakarta", "B. Magelang"],
        "answer" => 1
    ],
    [
        "question" => "Alat pernapasan utama pada ikan adalah...",
        "options" => ["A. Paru-paru", "B. Insang"],
        "answer" => 1
    ],
    [
        "question" => "Gunung tertinggi di Pulau Jawa adalah...",
        "options" => ["A. Gunung Semeru", "B. Gunung Rinjani"],
        "answer" => 0
    ],
    [
        "question" => "Lagu daerah 'Gundul-Gundul Pacul' berasal dari...",
        "options" => ["A. Jawa Barat", "B. Jawa Tengah"],
        "answer" => 1
    ],
    [
        "question" => "Patung Liberty berada di negara...",
        "options" => ["A. Amerika Serikat", "B. Inggris"],
        "answer" => 0
    ]
];

$json_questions = json_encode($questions);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerdas Cermat - Level Medium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background-color: #fff3cd; }
        
        .timer-bar {
            height: 15px;
            width: 100%;
            background-color: #dee2e6;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .timer-fill {
            height: 100%;
            background-color: #0d6efd;
            width: 100%;
            transition: width 1s linear, background-color 1s linear;
        }
        
        .question-card {
            min-height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fd7e14 0%, #dc3545 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            font-size: 2rem;
            text-align: center;
            padding: 2.5rem;
            margin-bottom: 2rem;
            font-weight: bold;
        }

        .option-btn {
            font-size: 1.5rem;
            padding: 20px;
            border-radius: 15px;
            border: 3px solid #dee2e6;
            background: white;
            transition: all 0.2s;
            width: 100%;
            text-align: center;
            margin-bottom: 15px;
            color: #333;
            font-weight: 600;
        }
        
        .option-btn:hover:not(:disabled) {
            background-color: #f8f9fa; 
            transform: translateY(-3px);
            border-color: #fd7e14;
        }

        /* STYLE VISUAL JAWABAN */
        .btn-correct {
            background-color: #198754 !important; /* Hijau */
            color: white !important;
            border-color: #198754 !important;
        }
        
        .btn-wrong {
            background-color: #dc3545 !important; /* Merah */
            color: white !important;
            border-color: #dc3545 !important;
        }

        .level-badge {
            background-color: #fd7e14;
            color: white;
            padding: 5px 20px;
            border-radius: 20px;
            font-size: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: bold;
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
                <h2 class="fw-bold text-dark mb-0">🔥 Cerdas Cermat</h2>
                <span class="level-badge">Level: MEDIUM (20 Detik)</span>
            </div>
            <div class="d-flex align-items-center gap-3">
                <h4 class="fw-bold mb-0 text-secondary" id="questionCounter">Soal 1 / 10</h4>
            </div>
        </div>

        <div id="gameArea" style="display: none;">
            
            <div class="timer-bar shadow-sm">
                <div class="timer-fill" id="timerFill"></div>
            </div>
            <div class="text-center mb-3 fw-bold fs-3" id="timerText">20</div>

            <div class="question-card">
                <span id="questionText">Memuat pertanyaan...</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <button class="btn option-btn" id="opt0" onclick="checkAnswer(0)">A</button>
                </div>
                <div class="col-md-6">
                    <button class="btn option-btn" id="opt1" onclick="checkAnswer(1)">B</button>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary px-4 fw-bold" onclick="resetGame()">🔄 Reset Game</button>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-warning fw-bold px-4 shadow text-white" id="skipBtn" onclick="skipQuestion()">
                        ⏭ Lewati
                    </button>
                    <button class="btn btn-primary fw-bold px-5 shadow" id="nextBtn" onclick="nextQuestion()" style="display: none;">
                        Soal Selanjutnya ➝
                    </button>
                </div>
            </div>
        </div>

        <div id="startScreen" class="text-center mt-5">
            <div class="card shadow-sm border-0 p-5" style="border-radius: 25px; background: white;">
                <h1 class="display-4 fw-bold text-warning mb-3">Level Medium</h1>
                <p class="fs-5 text-muted mb-4">
                    Kategori: <strong>Campuran (SD - SMP)</strong><br>
                    Waktu: <strong>20 Detik</strong> | Opsi: <strong>A & B</strong><br>
                </p>
                <div id="resumeAlert" class="alert alert-info d-none shadow-sm">
                    <strong>Permainan Tersimpan!</strong> Melanjutkan soal terakhir...
                </div>
                <button class="btn btn-warning btn-lg px-5 py-3 rounded-pill fw-bold shadow text-white" onclick="startGame()">
                    ▶ MULAI
                </button>
            </div>
        </div>

        <div id="endScreen" class="text-center mt-5" style="display: none;">
            <div class="card shadow border-0 p-5" style="border-radius: 25px;">
                <h1 class="display-1 mb-2">🏁</h1>
                <h2 class="fw-bold mb-3">Permainan Selesai!</h2>
                <p class="text-muted fs-5">Terima kasih telah berpartisipasi.</p>
                <div class="mt-4">
                    <a href="../dashboard.php" class="btn btn-outline-dark me-2">Kembali ke Dashboard</a>
                    <button onclick="resetGame()" class="btn btn-primary fw-bold">Main Lagi</button>
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
    const TIME_LIMIT = 20; 
    const STORAGE_INDEX = 'cerdasCermat_medium_index';
    
    let currentQIndex = 0;
    let timerInterval;
    let timeLeft;
    let isAnswered = false;

    // === DOM ELEMENTS ===
    const startScreen = document.getElementById('startScreen');
    const gameArea = document.getElementById('gameArea');
    const endScreen = document.getElementById('endScreen');
    const resumeAlert = document.getElementById('resumeAlert');
    
    const questionText = document.getElementById('questionText');
    const questionCounter = document.getElementById('questionCounter');
    const timerFill = document.getElementById('timerFill');
    const timerText = document.getElementById('timerText');
    
    const nextBtn = document.getElementById('nextBtn');
    const skipBtn = document.getElementById('skipBtn');
    
    const optionsBtn = [
        document.getElementById('opt0'),
        document.getElementById('opt1')
    ];

    // === AUDIO CONTROL ===
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
        isAnswered = false;
        
        localStorage.setItem(STORAGE_INDEX, currentQIndex);

        nextBtn.style.display = 'none';
        skipBtn.style.display = 'inline-block';
        skipBtn.disabled = false;

        timerFill.style.width = '100%';
        timerFill.style.backgroundColor = '#0d6efd';
        timerText.innerText = TIME_LIMIT;
        
        let q = questions[currentQIndex];
        questionText.innerText = q.question;
        questionCounter.innerText = `Soal ${currentQIndex + 1} / ${questions.length}`;

        // Reset Tombol
        optionsBtn.forEach((btn, index) => {
            btn.innerHTML = q.options[index]; 
            btn.className = 'btn option-btn'; 
            btn.disabled = false;
        });

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

            // LOGIKA AUDIO & WARNA
            if (timeLeft === 10) {
                playTick();
                timerFill.style.backgroundColor = '#ffc107'; 
            }
            if (timeLeft === 5) {
                playAlarm();
                timerFill.style.backgroundColor = '#dc3545'; 
            }
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                handleTimeOut();
            }
        }, 1000);
    }

    function checkAnswer(selectedIndex) {
        if (isAnswered) return;
        isAnswered = true;
        clearInterval(timerInterval);
        stopTick(); audioAlarm.pause(); 

        let correctIndex = questions[currentQIndex].answer;
        
        if (selectedIndex === correctIndex) {
            // BENAR
            optionsBtn[selectedIndex].classList.add('btn-correct');
            playCorrect();
        } else {
            // SALAH
            optionsBtn[selectedIndex].classList.add('btn-wrong');
            // Tampilkan jawaban yang benar juga
            optionsBtn[correctIndex].classList.add('btn-correct');
        }

        finishTurn();
    }

    function handleTimeOut() {
        isAnswered = true;
        // Waktu habis, kasih tau jawaban benar
        let correctIndex = questions[currentQIndex].answer;
        optionsBtn[correctIndex].classList.add('btn-correct');
        
        finishTurn();
    }

    function skipQuestion() {
        if (isAnswered) return;
        stopAllAudio();
        clearInterval(timerInterval);
        nextQuestion();
    }

    function finishTurn() {
        optionsBtn.forEach(btn => btn.disabled = true);
        skipBtn.style.display = 'none';
        nextBtn.style.display = 'inline-block';
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