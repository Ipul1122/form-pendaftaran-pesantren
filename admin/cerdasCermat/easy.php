<?php
session_start();
// Cek login
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

require_once '../../config/config.php';

// =====================================================================
// DATA PERTANYAAN (BANK SOAL - LEVEL EASY)
// =====================================================================
$questions = [
    [
        "question" => "Ibukota provinsi Jawa Timur adalah...",
        "options" => ["A. Bandung", "B. Surabaya", "C. Semarang", "D. Malang"],
        "answer" => 1
    ],
    [
        "question" => "Hasil dari 8 x 7 adalah...",
        "options" => ["A. 54", "B. 58", "C. 56", "D. 64"],
        "answer" => 2
    ],
    [
        "question" => "Hewan yang dapat hidup di darat dan di air disebut...",
        "options" => ["A. Mamalia", "B. Reptil", "C. Amfibi", "D. Aves"],
        "answer" => 2
    ],
    [
        "question" => "Planet terbesar dalam tata surya kita adalah...",
        "options" => ["A. Bumi", "B. Mars", "C. Saturnus", "D. Jupiter"],
        "answer" => 3
    ],
    [
        "question" => "Lagu kebangsaan negara Indonesia adalah...",
        "options" => ["A. Indonesia Pusaka", "B. Garuda Pancasila", "C. Padamu Negeri", "D. Indonesia Raya"],
        "answer" => 3
    ],
    [
        "question" => "Candi Buddha terbesar di Indonesia adalah...",
        "options" => ["A. Prambanan", "B. Borobudur", "C. Mendut", "D. Penataran"],
        "answer" => 1
    ],
    [
        "question" => "Berapa derajat besar sudut siku-siku?",
        "options" => ["A. 45 Derajat", "B. 90 Derajat", "C. 180 Derajat", "D. 360 Derajat"],
        "answer" => 1
    ],
    [
        "question" => "Mata uang negara Jepang adalah...",
        "options" => ["A. Yen", "B. Won", "C. Dollar", "D. Ringgit"],
        "answer" => 0
    ],
    [
        "question" => "Indera perasa pada manusia adalah...",
        "options" => ["A. Kulit", "B. Hidung", "C. Lidah", "D. Telinga"],
        "answer" => 2
    ],
    [
        "question" => "Siapakah penemu bola lampu pijar?",
        "options" => ["A. Alexander Graham Bell", "B. Thomas Alva Edison", "C. Albert Einstein", "D. Isaac Newton"],
        "answer" => 1
    ]
];

$json_questions = json_encode($questions);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cerdas Cermat - Level Easy</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Fredoka', sans-serif; background-color: #eef2f5; }
        
        .timer-bar {
            height: 15px;
            width: 100%;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .timer-fill {
            height: 100%;
            background-color: #198754; 
            width: 100%;
            transition: width 1s linear, background-color 1s linear;
        }
        
        .question-card {
            min-height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
            color: white;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            font-size: 1.8rem;
            text-align: center;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .option-btn {
            font-size: 1.3rem;
            padding: 15px;
            border-radius: 12px;
            border: 2px solid #dee2e6;
            background: white;
            transition: all 0.2s;
            width: 100%;
            text-align: left;
            margin-bottom: 15px;
            position: relative;
            overflow: hidden;
            color: #333;
        }
        
        .option-btn.correct-answer {
            background-color: #198754 !important;
            color: white !important;
            border-color: #198754 !important;
            font-weight: bold;
            box-shadow: 0 0 15px rgba(25, 135, 84, 0.5);
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        .level-badge {
            background-color: #20c997;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
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
                <h2 class="fw-bold text-dark mb-0">🎮 Cerdas Cermat</h2>
                <span class="level-badge">Level: EASY (30 Detik)</span>
            </div>
            <div class="text-end">
                <h4 class="fw-bold" id="questionCounter">Soal 1 / 10</h4>
            </div>
        </div>

        <div id="gameArea" style="display: none;">
            
            <div class="timer-bar shadow-sm">
                <div class="timer-fill" id="timerFill"></div>
            </div>
            <div class="text-center mb-3 fw-bold fs-4" id="timerText">30</div>

            <div class="question-card">
                <span id="questionText">Memuat pertanyaan...</span>
            </div>

            <div class="row">
                <div class="col-md-6"><button class="btn option-btn" id="opt0" disabled>A</button></div>
                <div class="col-md-6"><button class="btn option-btn" id="opt1" disabled>B</button></div>
                <div class="col-md-6"><button class="btn option-btn" id="opt2" disabled>C</button></div>
                <div class="col-md-6"><button class="btn option-btn" id="opt3" disabled>D</button></div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                <button class="btn btn-outline-secondary px-4" onclick="resetGame()">🔄 Ulangi dari Awal</button>
                
                <div class="d-flex gap-2">
                    <button class="btn btn-warning fw-bold px-4 shadow" id="skipBtn" onclick="nextQuestion()">
                        ⏭ Lewati Soal
                    </button>

                    <button class="btn btn-primary fw-bold px-5 shadow" id="nextBtn" onclick="nextQuestion()" style="display: none;">
                        Soal Selanjutnya ➝
                    </button>
                </div>
            </div>
        </div>

        <div id="startScreen" class="text-center mt-5">
            <div class="card shadow-sm border-0 p-5" style="border-radius: 20px;">
                <h1 class="display-4 fw-bold text-primary mb-3">Siap Bermain?</h1>
                <p class="fs-4 text-muted mb-4">
                    Kategori: <strong>Easy (SD - SMP)</strong><br>
                    Waktu per soal: <strong>30 Detik</strong>
                </p>
                <div id="resumeAlert" class="alert alert-info d-none">
                    Anda memiliki permainan yang belum selesai. Melanjutkan...
                </div>
                <button class="btn btn-success btn-lg px-5 py-3 rounded-pill fw-bold shadow hover-scale" onclick="startGame()">
                    ▶ MULAI PERMAINAN
                </button>
            </div>
        </div>

        <div id="endScreen" class="text-center mt-5" style="display: none;">
            <div class="card shadow border-0 p-5" style="border-radius: 20px;">
                <h1 class="display-1 mb-3">🎉</h1>
                <h2 class="fw-bold mb-3">Permainan Selesai!</h2>
                <p class="fs-5 text-muted">Semua soal telah terjawab.</p>
                <div class="mt-4">
                    <a href="../dashboard.php" class="btn btn-outline-dark me-2">Kembali ke Dashboard</a>
                    <button onclick="resetGame()" class="btn btn-primary">Main Lagi</button>
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
    const TIME_LIMIT = 30; 
    const STORAGE_KEY = 'cerdasCermat_easy_index';
    
    let currentQIndex = 0;
    let timerInterval;
    let timeLeft;

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
        document.getElementById('opt1'),
        document.getElementById('opt2'),
        document.getElementById('opt3')
    ];

    // === AUDIO ELEMENTS ===
    const audioTick = document.getElementById('soundTick');
    const audioAlarm = document.getElementById('soundAlarm');
    const audioCorrect = document.getElementById('soundCorrect');

    // === FUNGSI KONTROL AUDIO ===
    function playTick() {
        audioTick.currentTime = 0;
        audioTick.play().catch(e => console.log("Audio Error:", e));
    }
    
    function stopTick() {
        audioTick.pause();
        audioTick.currentTime = 0;
    }

    function playAlarm() {
        stopTick(); // Pastikan detak berhenti dulu
        audioAlarm.currentTime = 0;
        audioAlarm.play().catch(e => console.log("Audio Error:", e));
    }

    function playCorrect() {
        // Jangan stop alarm jika ingin suara overlap (opsional), 
        // tapi biasanya lebih bersih jika alarm stop saat jawaban muncul.
        // Uncomment baris di bawah jika ingin Alarm mati saat jawaban muncul:
        // stopAllAudio(); 
        
        audioCorrect.currentTime = 0;
        audioCorrect.play().catch(e => console.log("Audio Error:", e));
    }

    function stopAllAudio() {
        stopTick();
        audioAlarm.pause();
        audioAlarm.currentTime = 0;
        audioCorrect.pause();
        audioCorrect.currentTime = 0;
    }

    // === CEK STORAGE SAAT LOAD ===
    document.addEventListener("DOMContentLoaded", function() {
        if(localStorage.getItem(STORAGE_KEY)) {
            currentQIndex = parseInt(localStorage.getItem(STORAGE_KEY));
            
            if (currentQIndex >= questions.length) {
                currentQIndex = 0;
                localStorage.removeItem(STORAGE_KEY);
            } else {
                resumeAlert.classList.remove('d-none');
                resumeAlert.innerText = `Melanjutkan dari soal nomor ${currentQIndex + 1}...`;
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

        localStorage.setItem(STORAGE_KEY, currentQIndex);

        // Reset UI
        nextBtn.style.display = 'none';
        skipBtn.style.display = 'inline-block';
        skipBtn.disabled = false;

        timerFill.style.width = '100%';
        timerFill.style.backgroundColor = '#198754';
        timerText.innerText = TIME_LIMIT;
        
        let q = questions[currentQIndex];
        questionText.innerText = q.question;
        questionCounter.innerText = `Soal ${currentQIndex + 1} / ${questions.length}`;

        optionsBtn.forEach((btn, index) => {
            btn.innerText = q.options[index];
            btn.classList.remove('correct-answer'); 
            btn.disabled = true;
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

            // LOGIKA AUDIO
            if (timeLeft === 20) {
                playTick();
                timerFill.style.backgroundColor = '#ffc107'; 
            }
            
            if (timeLeft === 5) {
                playAlarm();
                timerFill.style.backgroundColor = '#dc3545'; 
            }

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                revealAnswer();
            }
        }, 1000);
    }

    function revealAnswer() {
        let correctIndex = questions[currentQIndex].answer;
        optionsBtn[correctIndex].classList.add('correct-answer');
        
        // Mainkan suara Correct
        playCorrect();
        
        skipBtn.style.display = 'none';
        nextBtn.style.display = 'inline-block';
    }

    function nextQuestion() {
        stopAllAudio();
        clearInterval(timerInterval);

        currentQIndex++;
        localStorage.setItem(STORAGE_KEY, currentQIndex);

        if (currentQIndex < questions.length) {
            loadQuestion();
        } else {
            localStorage.removeItem(STORAGE_KEY);
            gameArea.style.display = 'none';
            endScreen.style.display = 'block';
        }
    }

    function resetGame() {
        if(confirm("Yakin ingin mengulang dari awal? Progress akan dihapus.")) {
            stopAllAudio();
            clearInterval(timerInterval);
            localStorage.removeItem(STORAGE_KEY);
            window.location.reload();
        }
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