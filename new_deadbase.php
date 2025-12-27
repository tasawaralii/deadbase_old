<?php
session_start();

// --- LOGOUT LOGIC ---
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// --- CONFIGURATION ---
$valid_username = "Rumman8888";
$valid_password = "714212835rR*"; 
// ---------------------

// --- LOGIN LOGIC ---
if (isset($_POST['login_user']) && isset($_POST['login_pass'])) {
    if ($_POST['login_user'] === $valid_username && $_POST['login_pass'] === $valid_password) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $error = "ACCESS DENIED: INVALID CREDENTIALS";
    }
}

// --- LOAD CONFIGURATIONS ---
$domainConfigFile = 'domain_config.json';
// Default Config
$configData = [
    'drive_domain' => 'deaddrive.icu'       // Infinity Drive Domain
];

if (file_exists($domainConfigFile)) {
    $loaded = json_decode(file_get_contents($domainConfigFile), true);
    if ($loaded) {
        $configData = array_merge($configData, $loaded);
    }
}

$adsConfigFile = 'ads_config.json';
$serverAdsConfig = ['enabled' => false, 'apis' => []];
if (file_exists($adsConfigFile)) {
    $loadedAds = json_decode(file_get_contents($adsConfigFile), true);
    if ($loadedAds) { $serverAdsConfig = $loadedAds; }
}

// --- INFINITY DRIVE API PROXY ---
if (isset($_POST['action']) && $_POST['action'] == 'get_infinity_link' && isset($_SESSION['admin_logged_in'])) {
    error_reporting(0);
    ini_set('display_errors', 0);
    header('Content-Type: application/json');

    $driveId = $_POST['drive_id'] ?? '';
    $apiKey = "0a55d6bd89c7863a039bb71fa7e90255"; 
    
    $currentDriveDomain = $configData['drive_domain']; 
    $apiHost = "api." . $currentDriveDomain;

    if (!$driveId) {
        echo json_encode(['status' => 'error', 'message' => 'No Drive ID']);
        exit();
    }

    $apiUrl = "https://{$apiHost}/upload?api={$apiKey}&drive_id={$driveId}";
    
    $ch = curl_init();
    // Removed duplicate curl_init line here
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20); 
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        echo json_encode(['status' => 'error', 'message' => 'CURL Failed: ' . $curlError]);
    } elseif ($httpCode >= 400) {
        echo json_encode(['status' => 'error', 'message' => 'HTTP Error: ' . $httpCode, 'raw' => $response]);
    } else {
        $jsonCheck = json_decode($response);
        if ($jsonCheck === null) {
             echo json_encode(['status' => 'error', 'message' => 'Invalid JSON from API', 'raw' => $response]);
        } else {
             echo $response;
        }
    }
    exit();
}

// --- SAVE ADS CONFIG ---
if (isset($_POST['action']) && $_POST['action'] == 'save_ads_config' && isset($_SESSION['admin_logged_in'])) {
    $jsonConfig = $_POST['config_data'] ?? '';
    if ($jsonConfig) {
        file_put_contents('ads_config.json', $jsonConfig);
        echo json_encode(['status' => 'success']);
    } else { echo json_encode(['status' => 'error']); }
    exit();
}

// --- DB CONNECTION HELPER ---
function getDbConnection() {
    if (file_exists('db_connect.php')) { include 'db_connect.php'; return $pdo; } 
    else {
        $host = "localhost"; $username = "animeinf_hero"; $password = "714212835rR*"; $dbname = "animeinf_archive";
        try { 
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password); 
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            return $pdo;
        } catch(PDOException $e) { return null; }
    }
}

// --- RESTORE HANDLER ---
if (isset($_POST['action']) && $_POST['action'] == 'restore_from_db' && isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    $pdo = getDbConnection();
    if(!$pdo) { echo json_encode(['status' => 'error', 'message' => 'DB Connection Failed']); exit; }
    try {
        $stmt = $pdo->query("SELECT slug, type FROM pages ORDER BY id DESC");
        $pages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['status' => 'success', 'data' => $pages]);
    } catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
    exit(); 
}

// --- DELETE HANDLER ---
if (isset($_POST['action']) && $_POST['action'] == 'delete_from_db' && isset($_SESSION['admin_logged_in'])) {
    header('Content-Type: application/json');
    $pdo = getDbConnection();
    if(!$pdo) { echo json_encode(['status' => 'error', 'message' => 'DB Connection Failed']); exit; }
    $slug = $_POST['slug'] ?? '';
    if ($slug) {
        try { $stmt = $pdo->prepare("DELETE FROM pages WHERE slug = :slug"); $stmt->execute([':slug' => $slug]); echo json_encode(['status' => 'success']); } 
        catch (Exception $e) { echo json_encode(['status' => 'error', 'message' => $e->getMessage()]); }
    } else { echo json_encode(['status' => 'error', 'message' => 'No slug provided']); }
    exit(); 
}

// --- LOGIN FORM ---
if (!isset($_SESSION['admin_logged_in'])) {
?>
<!doctype html>
<html lang="en">
<head>
    <title>Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://animeinfinite.com/wp-content/uploads/2025/03/cropped-anime-infinity-2-1-1-1.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        :root { --primary: #ff0050; --bg: #0b0c15; --card: #151621; --text: #fff; --accent: #00f2ea; }
        body { background: var(--bg); color: var(--text); display: flex; height: 100vh; justify-content: center; align-items: center; font-family: 'Poppins', sans-serif; margin: 0; overflow: hidden; }
        body::before { content: ""; position: absolute; width: 150%; height: 150%; background: radial-gradient(circle at center, rgba(255,0,80,0.1) 0%, transparent 60%); z-index: -1; animation: pulse 10s infinite alternate; }
        .login-box { background: rgba(21, 22, 33, 0.95); padding: 50px 40px; border-radius: 16px; border: 1px solid #333; text-align: center; box-shadow: 0 0 40px rgba(0,0,0,0.7); width: 350px; position: relative; backdrop-filter: blur(10px); }
        .login-box::after { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, var(--primary), var(--accent)); border-radius: 16px 16px 0 0; }
        h2 { color: var(--text); margin-bottom: 30px; letter-spacing: 2px; text-transform: uppercase; font-weight: 700; }
        input { width: 100%; padding: 15px; background: #0b0c15; border: 1px solid #333; color: #fff; margin-bottom: 20px; border-radius: 8px; box-sizing: border-box; font-family: 'Poppins', sans-serif; transition: 0.3s; outline: none; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 15px rgba(255, 0, 80, 0.2); }
        .password-container { position: relative; margin-bottom: 20px; }
        .password-container input { margin-bottom: 0; }
        .toggle-password { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer; color: #aaa; transition: 0.3s; }
        .toggle-password:hover { color: var(--primary); }
        button { width: 100%; padding: 15px; background: linear-gradient(45deg, var(--primary), #d90043); color: white; border: none; font-weight: 700; cursor: pointer; border-radius: 8px; text-transform: uppercase; letter-spacing: 1px; transition: 0.3s; box-shadow: 0 5px 20px rgba(255, 0, 80, 0.4); }
        button:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(255, 0, 80, 0.6); }
        .error { color: var(--primary); font-size: 13px; margin-bottom: 15px; font-weight: 600; text-shadow: 0 0 10px rgba(255,0,80,0.5); }
        @keyframes pulse { 0% { transform: scale(1); opacity: 0.5; } 100% { transform: scale(1.1); opacity: 0.8; } }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>System Login</h2>
        <?php if(isset($error)){echo "<p class='error'>$error</p>";}?>
        <form method="post">
            <input type="text" name="login_user" placeholder="Username" required autocomplete="off">
            <div class="password-container">
                <input type="password" name="login_pass" id="id_password" placeholder="Password" required>
                <i class="far fa-eye toggle-password" id="togglePassword"></i>
            </div>
            <button type="submit">Authenticate</button>
        </form>
    </div>
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#id_password');
        togglePassword.addEventListener('click', function (e) {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
            this.classList.toggle('fa-eye');
        });
    </script>
</body>
</html>
<?php exit(); } ?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admin Panel</title>
<link rel="icon" href="https://animeinfinite.com/wp-content/uploads/2025/03/cropped-anime-infinity-2-1-1-1.png" type="image/png">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
<style>
/* --- PROFESSIONAL ANIME THEME CSS --- */
:root {
    --bg-dark: #0b0c15;
    --bg-panel: #151621;
    --bg-input: #0f111a;
    --primary: #ff0050; /* Hot Pink */
    --secondary: #00f2ea; /* Cyber Cyan */
    --purple: #bf00ff;
    --green: #00e676; /* Infinity Green */
    --text-main: #e0e6ed;
    --text-muted: #7e8a9e;
    --border: #2a2e3d;
    --success: #00e676;
    --shadow: 0 10px 30px rgba(0,0,0,0.5);
}

body { margin: 0; padding: 0; background: var(--bg-dark); color: var(--text-main); font-family: 'Poppins', sans-serif; height: 100vh; overflow: hidden; }
::-webkit-scrollbar { width: 8px; }
::-webkit-scrollbar-track { background: var(--bg-dark); }
::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
::-webkit-scrollbar-thumb:hover { background: var(--primary); }

/* --- LAYOUT: FLEX CONTAINER --- */
.app-container {
    display: flex;
    width: 100%;
    height: 100vh;
    overflow: hidden;
}

/* SIDEBAR */
.sidebar { width: 260px; background: rgba(21, 22, 33, 0.9); border-right: 1px solid var(--border); display: flex; flex-direction: column; padding: 30px 20px 40px 20px; backdrop-filter: blur(10px); z-index: 10; box-shadow: 5px 0 30px rgba(0,0,0,0.2); height: 100%; overflow-y: auto; flex-shrink: 0; box-sizing: border-box; }
.sidebar-header { margin-bottom: 40px; text-align: center; }
.sidebar-header h3 { color: var(--text-main); font-weight: 800; font-size: 20px; letter-spacing: 2px; margin: 0; text-transform: uppercase; text-shadow: 0 0 15px rgba(255,255,255,0.1); }
.sidebar-header span { color: var(--primary); }

.menu-item { padding: 14px 20px; margin-bottom: 8px; cursor: pointer; border-radius: 12px; transition: all 0.3s ease; font-weight: 500; color: var(--text-muted); display: flex; align-items: center; gap: 15px; font-size: 14px; position: relative; overflow: hidden; }
.menu-item i { width: 20px; text-align: center; transition: 0.3s; }
.menu-item:hover { background: rgba(255, 255, 255, 0.05); color: #fff; transform: translateX(5px); }
.menu-item:hover i { color: var(--secondary); text-shadow: 0 0 10px var(--secondary); }
.menu-item.active { background: linear-gradient(90deg, rgba(255, 0, 80, 0.15), transparent); color: #fff; border-left: 3px solid var(--primary); border-radius: 4px 12px 12px 4px; }
.menu-item.active i { color: var(--primary); }

/* Custom Button Styles for Sidebar */
.action-item { margin-bottom: 10px; border: 1px solid transparent; }

/* MAIN CONTENT */
.main-content { flex: 1; padding: 40px; overflow-y: auto; background-image: radial-gradient(circle at 90% 10%, rgba(191, 0, 255, 0.05) 0%, transparent 40%), radial-gradient(circle at 10% 90%, rgba(0, 242, 234, 0.05) 0%, transparent 40%); height: 100%; }
.container-admin { width: 100%; max-width: 1000px; margin: 0 auto; animation: fadeIn 0.5s ease-out; padding-bottom: 50px; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.box { background: var(--bg-panel); padding: 35px; border-radius: 16px; box-shadow: var(--shadow); border: 1px solid var(--border); margin-bottom: 30px; position: relative; overflow: hidden; }
.box::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 3px; background: linear-gradient(90deg, var(--primary), var(--purple), var(--secondary)); }

.input-group { margin-bottom: 25px; }
.input-label { display: block; font-size: 12px; color: var(--secondary); margin-bottom: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.input-label.red { color: var(--primary); }
.input-label.gold { color: #ffd700; }

/* INPUTS & BUTTONS */
input, textarea, select { width: 100%; padding: 14px 18px; background: var(--bg-input); border: 1px solid var(--border); color: #fff; border-radius: 8px; box-sizing: border-box; font-family: 'Poppins', sans-serif; font-size: 14px; transition: 0.3s; outline: none; }
textarea { font-family: 'JetBrains Mono', monospace; font-size: 13px; line-height: 1.5; }
input:focus, textarea:focus, select:focus { border-color: var(--secondary); box-shadow: 0 0 15px rgba(0, 242, 234, 0.1); background: #13151f; }
::placeholder { color: #444a5a; }

button { padding: 14px 25px; background: linear-gradient(45deg, var(--primary), #d90043); border: none; color: white; font-weight: 700; cursor: pointer; border-radius: 8px; transition: 0.3s; text-transform: uppercase; letter-spacing: 1px; font-size: 13px; box-shadow: 0 4px 15px rgba(255, 0, 80, 0.3); }
button:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 0, 80, 0.5); filter: brightness(1.1); }
button:active { transform: translateY(0); }

button.secondary { background: #2a2e3d; color: #fff; box-shadow: none; border: 1px solid #333; }
button.secondary:hover { background: #353a4d; border-color: #555; }
button.cyan { background: linear-gradient(45deg, #00d2ff, #00f2ea); box-shadow: 0 4px 15px rgba(0, 242, 234, 0.3); color: #000; }
button.cyan:hover { box-shadow: 0 8px 25px rgba(0, 242, 234, 0.5); }
button.green { background: linear-gradient(45deg, #00e676, #00c853); box-shadow: 0 4px 15px rgba(0, 230, 118, 0.3); color: #000; }
button.green:hover { box-shadow: 0 8px 25px rgba(0, 230, 118, 0.5); }

/* LAYOUT HELPERS */
.flex-row { display: flex; gap: 15px; align-items: stretch; }
.flex-row input { margin-bottom: 0; }
.result-box { height: 200px; background: #0a0b10; border: 1px dashed var(--border); color: var(--secondary); }

/* FILE LIST HISTORY */
.filter-bar { background: #1a1c29; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--border); }
.file-list { background: var(--bg-input); border-radius: 12px; border: 1px solid var(--border); overflow: hidden; }
.file-item { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; transition: 0.2s; }
.file-item:hover { background: #1a1c29; }
.file-item:last-child { border-bottom: none; }
.file-name { color: #fff; font-weight: 600; font-size: 14px; margin-bottom: 4px; display: block; }
.file-meta { font-size: 11px; color: #666; }

.copy-btn { padding: 6px 14px; font-size: 11px; background: rgba(0, 242, 234, 0.1); border: 1px solid var(--secondary); color: var(--secondary); border-radius: 6px; margin: 0; width: auto; box-shadow: none; }
.copy-btn:hover { background: var(--secondary); color: #000; box-shadow: 0 0 10px rgba(0,242,234,0.5); transform: none; }
.checkbox-custom { accent-color: var(--primary); width: 18px; height: 18px; margin-right: 15px; cursor: pointer; }

/* API LIST & ADS */
.switch { position: relative; display: inline-block; width: 50px; height: 26px; }
.switch input { opacity: 0; width: 0; height: 0; }
.slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #333; transition: .4s; border-radius: 34px; }
.slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
input:checked + .slider { background-color: var(--success); }
input:checked + .slider:before { transform: translateX(24px); }

.api-item { background: #13151f; border: 1px solid var(--border); padding: 15px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; transition: 0.2s; }
.api-item:hover { border-color: var(--purple); transform: translateX(5px); }
.api-badge { background: linear-gradient(135deg, var(--purple), #8a2be2); color: #fff; font-weight: 700; padding: 4px 10px; border-radius: 4px; font-size: 11px; margin-right: 12px; letter-spacing: 0.5px; }
.api-key-text { font-family: 'JetBrains Mono', monospace; color: #aaa; font-size: 13px; }
.action-btn { width: 32px; height: 32px; padding: 0; display: inline-flex; justify-content: center; align-items: center; border-radius: 6px; margin-left: 5px; font-size: 12px; }
.btn-delete { background: rgba(255, 0, 80, 0.1); border: 1px solid var(--primary); color: var(--primary); }
.btn-delete:hover { background: var(--primary); color: #fff; }

/* PAGINATION */
.pagination-bar { display: flex; justify-content: center; gap: 10px; margin-top: 25px; }
.page-btn { background: var(--bg-input); border: 1px solid var(--border); width: auto; padding: 8px 20px; font-size: 12px; color: var(--text-muted); }
.page-btn:hover:not(:disabled) { border-color: var(--secondary); color: var(--secondary); background: rgba(0, 242, 234, 0.05); }
.page-indicator-box { background: #000; border: 1px solid #333; padding: 8px 15px; border-radius: 6px; font-family: 'JetBrains Mono', monospace; font-size: 13px; color: var(--secondary); display: flex; align-items: center; }

.d-none { display: none !important; }

/* TMDB SEARCH MODAL STYLES */
.modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.85); backdrop-filter: blur(8px); justify-content:center; align-items:center; }
.modal-content { 
    background-color: #151621; 
    margin: auto; 
    padding: 30px; 
    border: 1px solid var(--secondary); 
    width: 95%; 
    max-width: 900px; 
    border-radius: 20px; 
    position: relative; 
    box-shadow: 0 0 50px rgba(0,0,0,0.8); 
}
.close-modal { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; transition:0.3s; line-height: 20px; }
.close-modal:hover { color: var(--primary); }

.tmdb-results { display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 15px; margin-top: 20px; max-height: 500px; overflow-y: auto; padding-right: 5px; }
.tmdb-item { cursor: pointer; background: #0f111a; border: 1px solid #333; border-radius: 8px; overflow: hidden; transition: 0.2s; position: relative; }
.tmdb-item:hover { border-color: var(--secondary); transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.5); }
.tmdb-poster { width: 100%; height: 195px; object-fit: cover; }
.tmdb-info { padding: 10px; font-size: 11px; text-align: center; }

/* MODIFIED: ALLOW FULL TEXT WRAP FOR SEARCH RESULTS */
.tmdb-title { 
    font-weight: bold; 
    color: #fff; 
    white-space: normal;  
    overflow: visible;      
    display: block; 
    font-size: 11px; 
    line-height: 1.3;                
    margin-bottom: 5px; 
}

.tmdb-year { color: #888; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; }
.tmdb-type-badge { position: absolute; top: 5px; right: 5px; background: rgba(0,0,0,0.8); color: var(--secondary); font-size: 9px; padding: 2px 6px; border-radius: 4px; font-weight: bold; border: 1px solid var(--secondary); }

/* --- UPDATED SEASON HEADER WITH GAP --- */
.season-view-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #333;
    margin-top: 30px; 
}
.season-view-header h3 {
    margin: 0;
    font-size: 20px;
    color: #fff;
    font-weight: 600;
}
.back-btn-styled {
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    border: 1px solid #444;
    padding: 10px 20px;
    border-radius: 30px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}
.back-btn-styled:hover {
    background: var(--primary);
    border-color: var(--primary);
    box-shadow: 0 0 15px rgba(255, 0, 80, 0.4);
}
.season-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); 
    gap: 20px;
    max-height: 500px;
    overflow-y: auto;
    padding-right: 10px;
    padding-bottom: 10px;
}
.season-grid::-webkit-scrollbar { width: 6px; }
.season-grid::-webkit-scrollbar-track { background: #0f111a; border-radius: 4px; }
.season-grid::-webkit-scrollbar-thumb { background: #333; border-radius: 4px; }
.season-grid::-webkit-scrollbar-thumb:hover { background: var(--secondary); }

.season-card {
    background: #0f111a;
    border: 1px solid #2a2e3d;
    border-radius: 12px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    display: flex;
    flex-direction: column;
}
.season-card:hover {
    transform: translateY(-5px);
    border-color: var(--secondary);
    box-shadow: 0 10px 30px rgba(0, 242, 234, 0.15);
}
.season-poster-wrap {
    width: 100%;
    height: 250px;
    overflow: hidden;
    position: relative;
}
.season-poster-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.3s;
}
.season-card:hover .season-poster-img {
    transform: scale(1.05);
    filter: brightness(1.1);
}
.season-details {
    padding: 15px;
    text-align: center;
    background: linear-gradient(0deg, #151621 0%, #0f111a 100%);
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

/* MODIFIED: ALLOW FULL TEXT WRAP FOR SEASON TITLES */
.season-title {
    font-size: 15px;
    font-weight: 700;
    color: #fff;
    margin-bottom: 5px;
    white-space: normal;  
    line-height: 1.3;                
}

.season-meta {
    font-size: 12px;
    color: #888;
    background: rgba(255,255,255,0.05);
    padding: 4px 8px;
    border-radius: 4px;
    display: inline-block;
    margin-top: 5px;
}
.ep-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--primary);
    color: white;
    font-size: 11px;
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 4px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.5);
}

/* CUSTOM RADIOS FOR GENERATOR MODE */
.gen-mode-wrapper {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    justify-content: center;
    background: rgba(0,0,0,0.2);
    padding: 10px;
    border-radius: 50px;
    width: fit-content;
    margin-left: auto;
    margin-right: auto;
}
.gen-mode-label {
    cursor: pointer;
    font-weight: 700;
    color: #777;
    transition: 0.3s;
    display: flex;
    align-items: center;
    gap: 8px;
}
.gen-mode-label:hover { color: #fff; }
.gen-mode-radio { display: none; }
.gen-mode-radio:checked + span {
    color: var(--secondary);
    text-shadow: 0 0 10px rgba(0, 242, 234, 0.4);
}

/* Select styling fix */
select { appearance: none; background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3e%3cpath d='M7 10l5 5 5-5z'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 15px center; background-size: 12px; }
</style>
</head>
<body>

<div class="app-container">
    <div class="sidebar">
        <div class="sidebar-header">
            <h3>Anime <span>Infinity</span></h3>
            <p style="font-size:10px; color:#666; margin-top:5px; letter-spacing:1px;">ADMINISTRATION</p>
        </div>
        <div class="menu-item active" onclick="switchTab('share')" id="tab-share"><i class="fas fa-rocket"></i> Generator</div>
        <div class="menu-item" onclick="switchTab('accordion')" id="tab-accordion"><i class="fas fa-layer-group"></i> Accordion Codes</div>
        <div class="menu-item" onclick="switchTab('files')" id="tab-files"><i class="fas fa-database"></i> Database History</div>
        <div class="menu-item" onclick="switchTab('ads')" id="tab-ads"><i class="fas fa-ad"></i> Ads Configuration</div>

        <hr style="border: 0; border-top: 1px solid rgba(255,255,255,0.1); margin: 10px 0;">
        
        <div class="menu-item" onclick="window.open('https://archive.animeinfinite.com/generator.php', '_blank')">
            <i class="fas fa-code"></i> Post Code <i class="fas fa-external-link-alt" style="font-size:10px; margin-left:auto;"></i>
        </div>

        <div class="menu-item" onclick="window.location.reload()">
            <i class="fas fa-sync-alt"></i> Refresh Panel
        </div>
    </div>

    <div class="main-content">
        <div class="container-admin">
              
            <div id="section-share" class="box">
                <input id="serverApiUrl" type="hidden" value="https://archive.animeinfinite.com/save.php">
                
                <div class="gen-mode-wrapper">
                    <label class="gen-mode-label">
                        <input type="radio" name="genMode" class="gen-mode-radio" value="tmdb" checked onclick="toggleGenMode('tmdb')">
                        <span><i class="fas fa-magic"></i> TMDB Auto</span>
                    </label>
                    <div style="width: 1px; height: 20px; background: #555;"></div>
                    <label class="gen-mode-label">
                        <input type="radio" name="genMode" class="gen-mode-radio" value="manual" onclick="toggleGenMode('manual')">
                        <span><i class="fas fa-keyboard"></i> Manual Input</span>
                    </label>
                </div>

                <div id="mode-tmdb-area">
                    <div class="input-group">
                      <label class="input-label gold">Step 1: TMDB Source</label>
                      <div class="flex-row">
                          <div style="flex:3; display:flex; gap:5px;">
                              <input id="tmdbId" placeholder="Paste ID or click Search..." style="flex: 1;">
                              <button onclick="openTmdbSearch()" class="cyan" style="padding: 0 15px;" title="Search TMDB"><i class="fas fa-search"></i></button>
                          </div>
                          <select id="tmdbType" style="flex: 1;">
                              <option value="tv">TV Series</option>
                              <option value="movie">Movie</option>
                          </select>
                          <input id="seasonNum" type="number" value="1" placeholder="TMDB S#" style="flex: 0.8; text-align:center;">
                      </div>
                    </div>
                    
                    <div class="input-group">
                      <label class="input-label" style="color:#bf00ff;">Step 2: Custom Mapping (Optional)</label>
                      <div class="flex-row">
                          <div style="flex:1;">
                              <small style="display:block; color:#777; margin-bottom:4px;">TMDB Source Ep</small>
                              <input id="tmdbStartOffset" type="number" placeholder="Default: Same as File" style="border-color:#bf00ff;">
                          </div>
                          <div style="flex:1;">
                              <small style="display:block; color:#777; margin-bottom:4px;">Label Start #</small>
                              <input id="labelStartNum" type="number" value="1" placeholder="Default: 1" style="border-color:#bf00ff;">
                          </div>
                          <div style="flex:1;">
                              <small style="display:block; color:#777; margin-bottom:4px;">Output Season #</small>
                              <input id="displaySeason" type="number" placeholder="e.g. 2" style="border-color:#bf00ff;">
                          </div>
                      </div>
                      <small style="color:#777; font-size:11px; margin-top:5px; display:block;">
                          <i class="fas fa-info-circle"></i> <b>TMDB Source:</b> Which TMDB Ep matches File 1? (Data Source)<br>
                          <i class="fas fa-info-circle"></i> <b>Label Start:</b> What number should File 1 be labeled as? (Visual/Link)<br>
                      </small>
                    </div>
                </div>

                <div id="mode-manual-area" class="d-none">
                    <div class="input-group">
                        <label class="input-label gold">Step 1: Manual Metadata</label>
                        <input id="manualTitle" placeholder="Page Title (e.g. Naruto Shippuden)" style="margin-bottom: 10px; border-color: #ffd700;">
                        
                        <div class="flex-row" style="margin-bottom: 10px;">
                            <input id="manualPoster" placeholder="Header/Poster Image URL" style="flex:1;">
                            <input id="manualBackdrop" placeholder="Background Image URL" style="flex:1;">
                        </div>

                        <div class="flex-row" style="margin-bottom: 10px;">
                            <input id="manualDate" type="date" style="flex:1;" title="Release Date">
                            <input id="manualRating" type="number" step="0.1" placeholder="Rating (e.g. 8.5)" style="flex:1;">
                            <input id="manualRuntime" type="number" placeholder="Duration (min)" style="flex:1;">
                            <input id="manualSeasonNum" type="number" value="1" placeholder="Season #" style="flex:0.5; text-align:center;">
                        </div>

                        <input id="manualTrailer" placeholder="YouTube Trailer URL (e.g. https://youtu.be/...)" style="margin-bottom: 10px;">
                        <textarea id="manualOverview" placeholder="Synopsis / Plot Summary..." style="height: 80px;"></textarea>
                        <small style="color:var(--text-muted);">* Leave fields empty to hide them on the generated page.</small>
                    </div>
                </div>

                <hr style="border: 0; border-top: 1px dashed #333; margin: 25px 0;">

                <div class="input-group">
                  <label class="input-label red">Method A: GDrive Folder Scan</label>
                  <div class="flex-row">
                      <input id="folderInput" placeholder="Paste Google Drive Folder Link...">
                      <button onclick="processFolder()" style="flex:1;">SCAN FOLDER</button>
                  </div>
                </div>

                <div class="input-group">
                  <label class="input-label red">Method B: Direct Links</label>
                  <textarea id="fileInput" placeholder="Paste Google Drive file links here (one per line)..." style="height: 100px;"></textarea>
                  <button onclick="processFiles()" class="secondary" style="margin-top: 5px;">PROCESS FILES</button>
                </div>

                <div class="input-group" style="margin-bottom:0;">
                  <label class="input-label" style="color: var(--primary);">Final Generated Links</label>
                  <textarea id="finalLinks" class="result-box" placeholder="Generated links will appear here..." readonly></textarea>
                  <div style="display:flex; justify-content: space-between; margin-top: 10px;">
                      <span id="statusLog" style="font-family:'JetBrains Mono'; font-size:12px; color:#7e8a9e; align-self:center;">Ready to process...</span>
                      <button onclick="copyBulk('finalLinks', this)" class="cyan" style="width:auto;">COPY ALL</button>
                  </div>
                  <div id="fileList" class="file-list" style="display:none; margin-top:20px;"></div>
                </div>
            </div>

            <div id="section-accordion" class="box d-none">
                <h2 style="color: var(--text-main); margin-top: 0; font-size: 24px;">Accordion Codes <small style="font-size:14px; color:var(--text-muted)">(Auto-Generated)</small></h2>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label class="input-label" style="color: #03c1ef;">1. Episodes</label>
                        <textarea id="accEpisodes" class="result-box" style="height:150px; border-color:#03c1ef;" readonly></textarea>
                        <button onclick="copyBulk('accEpisodes', this)" class="secondary" style="margin-top:5px; border-color:#03c1ef; color:#03c1ef;">COPY EPISODES</button>
                    </div>
                    <div>
                        <label class="input-label" style="color: #dd3333;">2. Packs / Zips</label>
                        <textarea id="accZips" class="result-box" style="height:150px; border-color:#dd3333;" readonly></textarea>
                        <button onclick="copyBulk('accZips', this)" class="secondary" style="margin-top:5px; border-color:#dd3333; color:#dd3333;">COPY ZIP PACKS</button>
                    </div>
                </div>
                <br>
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label class="input-label" style="color: #ffd700;">3. Movies</label>
                        <textarea id="accMovies" class="result-box" style="height:150px; border-color:#ffd700;" readonly></textarea>
                        <button onclick="copyBulk('accMovies', this)" class="secondary" style="margin-top:5px; border-color:#ffd700; color:#ffd700;">COPY MOVIES</button>
                    </div>
                </div>
            </div>

            <div id="section-files" class="box d-none">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; padding-bottom: 15px; border-bottom: 1px solid var(--border);">
                    <div>
                        <h2 style="color: var(--text-main); margin: 0;">Database History</h2>
                        <small style="color: var(--text-muted);">Manage your generated pages</small>
                    </div>
                    <div style="display:flex; gap:10px;">
                        <button onclick="restoreHistory()" class="secondary" style="width: auto; font-size: 11px;"><i class="fas fa-sync"></i> SYNC FROM DB</button>
                        <button onclick="deleteSelected()" class="btn-delete" style="width: auto; background: rgba(255,0,80,0.1); font-size: 11px;"><i class="fas fa-trash"></i> DELETE SELECTED</button>
                    </div>
                </div>
                
                <div class="filter-bar">
                    <div style="display:flex; align-items:center; gap:15px;">
                        <input type="number" id="itemsPerPage" onchange="updateItemsPerPage()" value="10" min="1" style="width: 70px; padding: 10px; text-align:center;">
                        <select id="historyFilter" onchange="resetToFirstPageAndRender()" style="width: 150px;">
                            <option value="all">All Types</option>
                            <option value="/movie/">Movies</option>
                            <option value="/episode/">Episodes</option>
                            <option value="/zip/">Packs</option>
                        </select>
                    </div>
                    <input type="text" id="fileSearch" placeholder="Search history..." onkeyup="resetToFirstPageAndRender()" style="width: 40%;">
                </div>

                <div style="padding: 10px 20px; background: #1a1c29; border: 1px solid var(--border); border-bottom:none; border-radius: 12px 12px 0 0; display: flex; align-items: center;">
                    <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="checkbox-custom">
                    <span style="font-weight:600; font-size:13px; color:var(--text-muted);">SELECT ALL</span>
                </div>
                <div id="savedFilesList" class="file-list" style="border-radius: 0 0 12px 12px; min-height: 200px;">
                    <p style="text-align:center; color:#555; padding: 30px;">Loading...</p>
                </div>
                
                <div class="pagination-bar" id="paginationControls">
                    <button class="page-btn" onclick="changePage(-1)" id="btnPrev"><i class="fas fa-chevron-left"></i></button>
                    <span class="page-indicator-box">Page <b id="pageIndicator" style="color: #fff; margin-left:5px;">1</b></span>
                    <button class="page-btn" onclick="changePage(1)" id="btnNext"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div id="section-ads" class="box d-none">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 20px; margin-bottom: 25px;">
                    <div>
                        <h2 style="color: var(--text-main); margin: 0;">Ads Configuration</h2>
                        <small style="color: var(--text-muted);">Manage URL Shorteners Chain</small>
                    </div>
                    <div style="display:flex; align-items:center; gap:15px; background: #000; padding: 10px 20px; border-radius: 30px; border: 1px solid #333;">
                        <span id="adsStatusText" style="font-weight:bold; font-size:12px; color:#aaa;">DISABLED</span>
                        <label class="switch"><input type="checkbox" id="adsMasterSwitch" onchange="toggleAdsSystem()"><span class="slider"></span></label>
                    </div>
                </div>

                <div style="background: rgba(0, 242, 234, 0.05); padding: 25px; border-radius: 12px; border: 1px solid rgba(0, 242, 234, 0.2); margin-bottom: 30px;">
                    <label class="input-label" style="color: var(--secondary);">ADD NEW API</label>
                    <div class="flex-row">
                        <input id="shortenerService" list="shortenersList" placeholder="Domain (e.g. publicearn.com)" style="flex: 1;">
                        <datalist id="shortenersList">
                            <option value="publicearn.com">
                            <option value="gplinks.com">
                            <option value="cuty.io">
                            <option value="adrinolinks.in">
                            <option value="exe.io">
                        </datalist>
                        <input type="text" id="shortenerApiKey" placeholder="API Token" style="flex: 2;">
                        <button onclick="saveAdApi()" class="cyan" style="flex: 0.5;">ADD</button>
                    </div>
                    <small style="color: var(--text-muted); margin-top:5px; display:block;"><i class="fas fa-info-circle"></i> Use the exact website domain name.</small>
                </div>

                <label class="input-label">ACTIVE CHAIN</label>
                <div id="apiListContainer" class="api-list"><p style="text-align:center; color:#555;">No APIs configured.</p></div>
            </div>

        </div>
    </div>
</div>

<div id="tmdbModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeTmdbSearch()">&times;</span>
        <h3 style="color:var(--secondary); margin-top:0;">Search TMDB</h3>
        <div class="flex-row">
            <select id="tmdbSearchType" style="flex:1; margin-right:5px; background:#0f111a; border:1px solid #333;" onchange="if(document.getElementById('tmdbSearchQuery').value.trim() !== '') runTmdbSearch(false)">
                <option value="multi">All</option>
                <option value="movie">Movies</option>
                <option value="tv">TV Shows</option>
            </select>
            <input id="tmdbSearchQuery" placeholder="Enter movie or show name..." style="flex:3;" onkeypress="if(event.key === 'Enter') runTmdbSearch(false)">
            <button onclick="runTmdbSearch(false)" class="cyan" style="flex:1;">SEARCH</button>
        </div>
        <div id="tmdbResults" class="tmdb-results"></div>
        <button id="loadMoreBtn" onclick="runTmdbSearch(true)" class="secondary" style="width:100%; margin-top:15px; display:none;">LOAD MORE RESULTS</button>
    </div>
</div>

<script>
// --- CORE LOGIC & STORAGE ---
const STORAGE_KEY = 'animeInf_saved_files';
let globalAdsConfig = <?php echo json_encode($serverAdsConfig); ?>;

let currentPage = 1;
let itemsPerPage = 10;
const API_KEY = "AIzaSyDofuuajSX6ePr9lwKBlLmAdCADX2mznDA"; 
const TMDB_API_KEY = "e758ca7bb1f8114f001228ca519fd9cb";
let masterFileList = [];
let currentTmdbData = null;
let currentSeasonData = null; 
let episodeDataMap = {}; 

// Search State
let currentSearchPage = 1;
let isSearching = false;

// --- NEW: GENERATOR MODE TOGGLE ---
function toggleGenMode(mode) {
    if(mode === 'manual') {
        document.getElementById('mode-tmdb-area').classList.add('d-none');
        document.getElementById('mode-manual-area').classList.remove('d-none');
    } else {
        document.getElementById('mode-manual-area').classList.add('d-none');
        document.getElementById('mode-tmdb-area').classList.remove('d-none');
    }
}

// --- NEW: GET MANUAL DATA OBJECT ---
function getManualData() {
    const title = document.getElementById('manualTitle').value.trim();
    if(!title) { alert("Page Title is required for Manual Mode."); return null; }
    
    return {
        title: title,
        name: title,
        overview: document.getElementById('manualOverview').value.trim(),
        poster_path: document.getElementById('manualPoster').value.trim(), // Full URL expected
        backdrop_path: document.getElementById('manualBackdrop').value.trim(), // Full URL expected
        release_date: document.getElementById('manualDate').value,
        first_air_date: document.getElementById('manualDate').value,
        vote_average: document.getElementById('manualRating').value,
        runtime: document.getElementById('manualRuntime').value,
        manual_trailer: document.getElementById('manualTrailer').value.trim(), // Full URL
        is_manual: true
    };
}

function loadStoredFiles(){const data=localStorage.getItem(STORAGE_KEY);return data?JSON.parse(data):[]}
function saveFileRecord(name,link){const files=loadStoredFiles();if(!files.some(f=>f.name===name)){const newFile={id:Date.now()+Math.random().toString(36).substr(2,9),name:name,link:link,date:new Date().toLocaleString()};files.unshift(newFile);localStorage.setItem(STORAGE_KEY,JSON.stringify(files));if(!document.getElementById('section-files').classList.contains('d-none')){renderSavedFiles()}}}
function renderSavedFiles(){let files=loadStoredFiles();const container=document.getElementById('savedFilesList');const searchVal=document.getElementById('fileSearch').value.toLowerCase();if(searchVal){files=files.filter(f=>f.name.toLowerCase().includes(searchVal))}const filterType=document.getElementById('historyFilter').value;if(filterType!=='all'){files=files.filter(f=>f.link.includes(filterType))}const totalItems=files.length;let totalPages=1;itemsPerPage=parseInt(document.getElementById('itemsPerPage').value)||10;if(totalItems>0){totalPages=Math.ceil(totalItems/itemsPerPage);if(currentPage>totalPages)currentPage=totalPages||1;if(currentPage<1)currentPage=1;const startIndex=(currentPage-1)*itemsPerPage;const endIndex=startIndex+itemsPerPage;files=files.slice(startIndex,endIndex)}else{currentPage=1}const selectAllBox=document.getElementById('selectAllCheckbox');if(selectAllBox)selectAllBox.checked=false;if(files.length===0){container.innerHTML='<p style="text-align:center; color:#555; padding:20px;">No matching files found.</p>'}else{let html='';files.forEach(file=>{html+=`<div class="file-item"><div style="display:flex; align-items:center;"><input type="checkbox" class="checkbox-custom item-checkbox" value="${file.id}"><div><span class="file-name">${file.name}</span><small class="file-meta">${file.date}</small></div></div><div><a href="${file.link}" target="_blank" class="copy-btn" style="text-decoration:none; margin-right:5px;">OPEN</a><button class="copy-btn" onclick="copySingle('${file.link}', this)">COPY</button></div></div>`});container.innerHTML=html}updatePaginationUI(totalPages)}
function updatePaginationUI(totalPages){const controls=document.getElementById('paginationControls');const indicator=document.getElementById('pageIndicator');const btnPrev=document.getElementById('btnPrev');const btnNext=document.getElementById('btnNext');if(totalPages<=1&&loadStoredFiles().length>0){controls.style.display='flex'}else if(loadStoredFiles().length===0){controls.style.display='none'}else{controls.style.display='flex'}indicator.innerText=`${currentPage} / ${totalPages}`;btnPrev.disabled=(currentPage===1);btnNext.disabled=(currentPage===totalPages)}
function toggleSelectAll(source){const checkboxes=document.querySelectorAll('.item-checkbox');checkboxes.forEach(cb=>cb.checked=source.checked)}
function resetToFirstPageAndRender(){currentPage=1;renderSavedFiles()}
function updateItemsPerPage(){currentPage=1;renderSavedFiles()}
function changePage(delta){currentPage+=delta;renderSavedFiles()}
async function deleteSelected(){const checkboxes=document.querySelectorAll('.item-checkbox:checked');if(checkboxes.length===0){alert("Please select files to delete.");return}if(!confirm(`WARNING: This will PERMANENTLY delete ${checkboxes.length} pages from the Database (Server) and History. Are you sure?`))return;let files=loadStoredFiles();const idsToDelete=Array.from(checkboxes).map(cb=>cb.value);const filesToDelete=files.filter(f=>idsToDelete.includes(f.id.toString()));logStatus(`Deleting ${filesToDelete.length} pages from server...`);for(const file of filesToDelete){try{const urlParts=file.link.split('/');const cleanParts=urlParts.filter(p=>p!=="");const slug=cleanParts[cleanParts.length-1];if(slug){const formData=new FormData();formData.append('action','delete_from_db');formData.append('slug',slug);await fetch(window.location.href,{method:'POST',body:formData});console.log("Deleted from DB: "+slug)}}catch(e){console.error("Error deleting "+file.name,e)}}files=files.filter(f=>!idsToDelete.includes(f.id.toString()));localStorage.setItem(STORAGE_KEY,JSON.stringify(files));logStatus("Deletion Complete.");renderSavedFiles()}

function formatSlugToName(slug) { 
    try { 
        const epMatch = slug.match(/^(.*?)-(\d+)x(\d+)$/); 
        if (epMatch) { 
            const title = epMatch[1].replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); 
            const season = epMatch[2]; 
            const episode = epMatch[3]; 
            return `Episode ${episode}: ${title} (S${season})`; 
        } 
        const seasonMatch = slug.match(/^(.*?)-(\d+)$/); 
        if (seasonMatch) { 
            const title = seasonMatch[1].replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); 
            const season = seasonMatch[2].toString().padStart(2, '0'); 
            return `${title} - Season ${season}`; 
        } 
        return slug.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase()); 
    } catch(e) { return slug; } 
}

async function restoreHistory(){
    if(!confirm("Are you sure? This will fetch all pages from the Database and restore them to your History list.")) return;
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...';
    btn.disabled = true;
    try {
        const formData = new FormData();
        formData.append('action', 'restore_from_db');
        const res = await fetch(window.location.href, { method: 'POST', body: formData });
        const result = await res.json();
        if(result.status === 'success') {
            const dbPages = result.data;
            if(dbPages.length === 0) { alert("Database is empty. No pages to restore."); } 
            else {
                let currentFiles = loadStoredFiles();
                let newCount = 0;
                dbPages.forEach(page => {
                    const slug = page.slug;
                    const type = page.type;
                    
                    let urlPath = slug;
                    if(type === 'movie') urlPath = `movie/${slug}`;
                    else if(type === 'episode') urlPath = `episode/${slug}`;
                    else if(type === 'zip') urlPath = `zip/${slug}`;
                    
                    const fullLink = `https://archive.animeinfinite.com/${urlPath}`; 
                    
                    const prettyName = formatSlugToName(slug);
                    if(!currentFiles.some(f => f.link === fullLink)) {
                        currentFiles.push({ id: Date.now() + Math.random().toString(36).substr(2, 9), name: prettyName, link: fullLink, date: new Date().toLocaleString() });
                        newCount++;
                    }
                });
                localStorage.setItem(STORAGE_KEY, JSON.stringify(currentFiles));
                renderSavedFiles();
                alert(`Successfully restored ${newCount} pages from Database!`);
            }
        } else { alert("Error: " + result.message); }
    } catch(e) { console.error(e); alert("Connection Error while restoring."); }
    btn.innerHTML = originalText;
    btn.disabled = false;
}

function switchTab(tabName){['share','accordion','files','ads'].forEach(id=>{const el=document.getElementById('section-'+id);const tab=document.getElementById('tab-'+id);if(el)el.classList.add('d-none');if(tab)tab.classList.remove('active')});const targetSection=document.getElementById('section-'+tabName);const targetTab=document.getElementById('tab-'+tabName);if(targetSection)targetSection.classList.remove('d-none');if(targetTab)targetTab.classList.add('active');if(tabName==='files')renderSavedFiles()}

function extractId(url){const match=url.match(/[-\w]{25,}/);return match?match[0]:null}
function formatSize(bytes){if(!bytes)return"Unknown";if(bytes===0)return'0 B';const k=1024;const sizes=['B','KB','MB','GB','TB'];const i=Math.floor(Math.log(bytes)/Math.log(k));return parseFloat((bytes/Math.pow(k,i)).toFixed(2))+' '+sizes[i]}

function formatDuration(minutes){if(!minutes)return"N/A";const h=Math.floor(minutes/60);const m=minutes%60;if(h>0)return`${h}h ${m}m`;return`${m}m`}

function getFileDetails(filename){
    const n = filename.toLowerCase();
    
    // 1. Resolution Logic (Score & Label)
    let resScore = 5; 
    let resLabel = "SD";

    if(n.match(/2160p|4k/)) { resScore = 60; resLabel = "4K"; }
    else if(n.includes("1080p")) { resScore = 50; resLabel = "1080p"; }
    else if(n.includes("720p")) { resScore = 40; resLabel = "720p"; }
    else if(n.includes("576p")) { resScore = 30; resLabel = "576p"; }
    else if(n.includes("480p")) { resScore = 20; resLabel = "480p"; }
    else if(n.includes("360p")) { resScore = 10; resLabel = "360p"; }
    else if(n.includes("hd")) { resScore = 35; resLabel = "HD"; } // Fallback for HD if no number
    else { resScore = 5; resLabel = "SD"; }

    // 2. Codec Logic
    let codecScore = 0;
    let codecLabel = "";
    
    if(n.match(/x265|hevc|h265/)) { codecScore = 2; codecLabel = "HEVC"; }
    else if(n.match(/x264|h264|avc/)) { codecScore = 1; codecLabel = "x264"; }
    else { codecScore = 0; codecLabel = ""; }

    // 3. Quality Tag
    let qualityTag = "";
    if(n.includes("hq")) { qualityTag += " (HQ)"; }
    
    if(n.includes("hd") && resLabel !== "HD") {
        qualityTag += " HD"; 
    }

    // 4. SMART Part / Range Extraction
    let partNum = null;
    let startEp = 9999; 

    // A. Check for "E01-06" or "Episode 1-12" style ranges first
    const rangeMatch = n.match(/(?:e|ep|episode)\s?\.?_?(\d+)\s?(?:-|~)\s?(\d+)/);
    if (rangeMatch) {
        startEp = parseInt(rangeMatch[1]);
    }

    // B. Check for Explicit "Part X" or "Vol X"
    const explicitPartMatch = n.match(/(?:\bpart\b|\bvol\b|\bpt\b)[\s._-]?(\d+)/);
    if(explicitPartMatch) {
        partNum = parseInt(explicitPartMatch[1]);
    }

    return { resScore, resLabel, codecScore, codecLabel, qualityTag, partNum, startEp };
}

async function fetchTmdbById(id,type){
    if(!id)return null;
    try{
        const url=`https://api.themoviedb.org/3/${type}/${id}?api_key=${TMDB_API_KEY}&append_to_response=videos,keywords,images`;
        const res=await fetch(url);
        if(!res.ok)throw new Error("TMDB ID not found");
        return await res.json()
    }catch(e){ console.error("TMDB Error:",e); alert("Error: Could not find TMDB ID. Check if it is correct."); return null }
}
async function fetchSeasonDetails(tvId,seasonNumber=1){
    try{
        const url=`https://api.themoviedb.org/3/tv/${tvId}/season/${seasonNumber}?api_key=${TMDB_API_KEY}&append_to_response=videos,images`;
        const res=await fetch(url);
        if(res.ok){
            const data=await res.json();
            currentSeasonData = data; 
            if(data.episodes){
                data.episodes.forEach(ep=>{
                    episodeDataMap[ep.episode_number]={
                        name:ep.name,
                        overview:ep.overview,
                        air_date:ep.air_date,
                        vote_average:ep.vote_average,
                        still_path:ep.still_path,
                        runtime: ep.runtime 
                    }
                })
            }
        } 
    }catch(e){console.error("Error fetching season details:",e)}
}

async function prepareTmdb(){
    const mode = document.querySelector('input[name="genMode"]:checked').value;
    
    if (mode === 'manual') {
        const title = document.getElementById('manualTitle').value.trim();
        if(!title) { alert("Please enter a Title in Manual Metadata."); return false; }
        
        currentTmdbData = {
            is_manual: true,
            title: title,
            name: title, 
            overview: document.getElementById('manualOverview').value.trim(),
            poster_path: document.getElementById('manualPoster').value.trim(),
            backdrop_path: document.getElementById('manualBackdrop').value.trim(),
            release_date: document.getElementById('manualDate').value,
            first_air_date: document.getElementById('manualDate').value,
            vote_average: document.getElementById('manualRating').value,
            runtime: document.getElementById('manualRuntime').value,
            manual_trailer: document.getElementById('manualTrailer').value.trim(), // Full URL
            is_manual: true
        };
        
        episodeDataMap = {}; 
        currentSeasonData = null;
        logStatus(`Manual Data Loaded: ${title}`);
        return true;
    }

    let inputVal=document.getElementById("tmdbId").value.trim();
    let typeVal=document.getElementById("tmdbType").value;
    const seasonNum=document.getElementById("seasonNum").value||1;
    if(!inputVal){alert("Please enter a TMDB ID or URL.");return false}
    const urlMatch=inputVal.match(/(?:themoviedb\.org\/)(movie|tv)\/(\d+)/);
    let finalId=inputVal;
    let finalType=typeVal;
    if(urlMatch){ finalType=urlMatch[1]; finalId=urlMatch[2]; document.getElementById("tmdbType").value=finalType }
    resetUI("Fetching TMDB Data...");
    currentTmdbData=await fetchTmdbById(finalId,finalType);
    if(!currentTmdbData){ resetUI("Error: Invalid TMDB ID or URL."); return false }
    episodeDataMap={};
    currentSeasonData=null; 
    if(finalType==='tv'){ logStatus(`Fetching Episode Details for Season ${seasonNum}...`); await fetchSeasonDetails(finalId,seasonNum) }
    logStatus(`Metadata Loaded: ${currentTmdbData.title||currentTmdbData.name}`);
    return true
}

async function processFolder(){const folderInput=document.getElementById("folderInput").value;const folderId=extractId(folderInput);if(!folderId){alert("Invalid Folder Link");return}const ready=await prepareTmdb();if(!ready)return;logStatus("Scanning Folder... (This might take a moment)");masterFileList=[];try{await scanFolderRecursive(folderId);finishProcessing()}catch(error){logStatus("Error scanning folder: "+error.message);alert("Error: "+error.message)}}
async function processFiles(){const rawInput=document.getElementById("fileInput").value;if(!rawInput.trim()){alert("Please paste file links.");return}const ready=await prepareTmdb();if(!ready)return;const readyTmdb=await prepareTmdb();if(!readyTmdb)return;const lines=rawInput.split(/\n/);const fileIds=[];lines.forEach(line=>{const id=extractId(line);if(id)fileIds.push(id)});if(fileIds.length===0){alert("No valid IDs found.");return}logStatus(`Processing ${fileIds.length} file links...`);masterFileList=[];
const uniqueIds = [...new Set(fileIds)];
for(const id of uniqueIds){try{const url=`https://www.googleapis.com/drive/v3/files/${id}?key=${API_KEY}&fields=id,name,mimeType,size,createdTime`;const response=await fetch(url);if(response.ok){const item=await response.json();masterFileList.push(item)}}catch(err){console.log("Skipped ID",id)}}finishProcessing()}
function finishProcessing(){logStatus(`Found ${masterFileList.length} files. Generating pages...`);if(masterFileList.length===0){logStatus("Error: No accessible files found.");alert("No files found!");return}renderSmartResults()}
async function scanFolderRecursive(parentId){const query=`'${parentId}' in parents and trashed = false`;const url=`https://www.googleapis.com/drive/v3/files?q=${encodeURIComponent(query)}&key=${API_KEY}&fields=files(id,name,mimeType,size,createdTime)&pageSize=1000`;const response=await fetch(url);if(!response.ok)throw new Error("GDrive API Error: "+response.status);const data=await response.json();if(data.files){for(const item of data.files){if(item.mimeType==='application/vnd.google-apps.folder'){await scanFolderRecursive(item.id)}else{
if(!masterFileList.some(f=>f.id===item.id)){ masterFileList.push(item); }
}}}}

function groupFilesByEpisode(files){
    const groups={};
    const mode = document.querySelector('input[name="genMode"]:checked').value;
    let sNum = 1;
    
    // Check Manual Season Override first, else Normal Input
    const displayS = document.getElementById("displaySeason").value;
    if(displayS) {
        sNum = parseInt(displayS);
    } else if(mode === 'manual') {
        sNum = parseInt(document.getElementById("manualSeasonNum").value || 1);
    } else {
        const sInput=document.getElementById("seasonNum");
        if(sInput&&sInput.value){sNum=parseInt(sInput.value)}
    }
    
    const showTitle=currentTmdbData?(currentTmdbData.name||currentTmdbData.title).toUpperCase():"UNKNOWN SHOW";
    const tmdbType=document.getElementById("tmdbType").value;
    
    files.forEach(file=>{
        let key=file.name;
        const mime=file.mimeType.toLowerCase();
        const name=file.name.toLowerCase();
        const isArchive=mime.includes("zip")||mime.includes("rar")||mime.includes("compressed")||mime.includes("archive")||name.endsWith(".zip")||name.endsWith(".rar")||name.endsWith(".7z")||name.endsWith(".tar")||name.endsWith(".iso");
        
        if(tmdbType==='movie' && mode !== 'manual'){
            key=showTitle
        } else if(isArchive){
            let sLabel = "Season " + sNum.toString().padStart(2, '0');
            // If we are using Display Season override, just use that.
            if(displayS) {
                key = sLabel;
            } else if (currentSeasonData && currentSeasonData.name) {
                 let tmdbName = currentSeasonData.name.trim();
                 if (tmdbName === "Season " + sNum || tmdbName === "Season " + parseInt(sNum)) {
                      key = sLabel; 
                 } else {
                      key = sLabel + ": " + tmdbName;
                 }
            } else {
                 key = sLabel;
            }
        } else {
            const epMatch=file.name.match(/(?:ep|episode|e)\s?\.?_?(\d+)/i);
            if(epMatch){
                const epNum=parseInt(epMatch[1]);
                const epStr=epNum.toString().padStart(2,'0');
                
                // --- NEW LOGIC: Label Start Index ---
                // We re-map the episode number for Display/Key purposes
                const labelStartInput = document.getElementById("labelStartNum").value;
                let displayEpNum = epNum;
                
                if(labelStartInput) {
                    const startVal = parseInt(labelStartInput) || 1;
                    // Formula: NewEp = OriginalEp + (StartVal - 1)
                    // If start is 1, offset is 0. If start is 13, offset is 12.
                    displayEpNum = epNum + (startVal - 1);
                }
                
                // Now use displayEpNum to fetch Name if possible
                // BUT wait, TMDB data lookup depends on TMDB Offset, not Label.
                // We just need a Label here.
                
                // Let's check if we can get a name for this label number
                // Note: The actual TMDB data binding happens in createVideoReplicaPage
                // Here we just set the Key string.
                
                // We try to find a name just for display purposes
                // We use the same TMDB offset logic to try and find a name match
                const tmdbOffsetInput = document.getElementById("tmdbStartOffset").value;
                let tmdbLookupIndex = epNum; 
                if(tmdbOffsetInput) {
                      // If TMDB offset is set, use it to find the name
                      tmdbLookupIndex = epNum + (parseInt(tmdbOffsetInput) - 1);
                } else {
                      // Fallback: If no TMDB offset, maybe use the Display Number?
                      // Usually if renumbering, one likely sets TMDB offset too.
                      tmdbLookupIndex = displayEpNum;
                }

                if(episodeDataMap[tmdbLookupIndex]){
                    const tmdbEpName = episodeDataMap[tmdbLookupIndex].name;
                    if (tmdbEpName.match(/^Episode\s+\d+$/i)) {
                         key = `Episode ${displayEpNum.toString().padStart(2, '0')}`;
                    } else {
                         key = `Episode ${displayEpNum.toString().padStart(2, '0')}: ${tmdbEpName}`;
                    }
                } else {
                    key=`Episode ${displayEpNum.toString().padStart(2, '0')}`
                }
            } else {
                key=showTitle
            }
        }
        if(!groups[key])groups[key]=[];
        groups[key].push(file)
    });
    return groups
}

function generateSlug(tmdbData, groupName, type) {
    let rawTitle = (tmdbData.name || tmdbData.title || "").toString();
    let showSlug = rawTitle.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase().replace(/'/g, "").replace(/[^a-z0-9]+/g, "-").replace(/^-+|-+$/g, "");

    const mode = document.querySelector('input[name="genMode"]:checked').value;
    
    // --- UPDATED SEASON SLUG LOGIC ---
    let sNum = 1;
    
    // Check for Display Season Override
    const displayS = document.getElementById("displaySeason").value;
    
    if (displayS) {
        sNum = parseInt(displayS);
    } else {
        if(mode === 'manual') sNum = parseInt(document.getElementById("manualSeasonNum").value || 1);
        else sNum = parseInt(document.getElementById("seasonNum").value || 1);
    }

    if (type === 'zip') {
        return `${showSlug}-${sNum}`;
    }
    
    const tmdbType = document.getElementById("tmdbType").value;
    if (tmdbType === 'movie' && mode !== 'manual') {
        return showSlug;
    } else {
        // We need to extract the mapped episode number from the GroupName now
        // Because groupFilesByEpisode already renamed the key to "Episode 13" etc.
        const epMatch = groupName.match(/(?:Episode|Ep|e)\s?\.?_?(\d+)/i);
        const epNum = epMatch ? parseInt(epMatch[1]) : 1;
        
        return `${showSlug}-${sNum}x${epNum}`;
    }
}
async function uploadToServer(slug,type,htmlContent){const apiUrl=document.getElementById("serverApiUrl").value;const formData=new FormData();formData.append('slug',slug);formData.append('type',type);formData.append('content',htmlContent);try{const req=await fetch(apiUrl,{method:'POST',body:formData});const res=await req.json();if(res.status==='success')return res.url;else throw new Error(res.message)}catch(e){console.error("Upload failed",e);return null}}

function obfuscateString(str) {
    try {
        const rev = str.split('').reverse().join('');
        const b64 = btoa(rev);
        const rev2 = b64.split('').reverse().join('');
        return btoa(rev2);
    } catch(e) { return btoa(str); }
}

async function fetchInfinityLinkFromServer(driveId) {
    if(!driveId) return null;
    const formData = new FormData();
    formData.append('action', 'get_infinity_link');
    formData.append('drive_id', driveId);
    try {
        const req = await fetch(window.location.href, { method: 'POST', body: formData });
        const res = await req.json();
        if (res.status === 'success' || res.status === 'AlreadyExist' || res.fileStatus === 'AlreadyExist') {
            return res.download || res.watch || null;
        } else { return null; }
    } catch(e) { return null; }
}

// --- TMDB SEARCH FUNCTIONS ---
function openTmdbSearch() { 
    document.getElementById('tmdbModal').style.display = 'flex'; 
    document.getElementById('tmdbSearchQuery').focus(); 
    document.getElementById('tmdbResults').innerHTML = ''; 
    document.getElementById('loadMoreBtn').style.display = 'none'; 
    currentSearchPage = 1;
}
function closeTmdbSearch() { document.getElementById('tmdbModal').style.display = 'none'; }

async function runTmdbSearch(loadMore = false) {
    if(isSearching) return;
    const query = document.getElementById('tmdbSearchQuery').value.trim();
    const searchType = document.getElementById('tmdbSearchType').value;
    const resultsContainer = document.getElementById('tmdbResults');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    
    resultsContainer.classList.add('tmdb-results'); 
    resultsContainer.style.display = 'grid'; 

    if(!query) return;
    if (!loadMore) { currentSearchPage = 1; resultsContainer.innerHTML = '<p style="color:#aaa; text-align:center;">Searching...</p>'; loadMoreBtn.style.display = 'none'; } else { currentSearchPage++; loadMoreBtn.innerText = 'Loading...'; }
    isSearching = true;
    
    try {
        const url = `https://api.themoviedb.org/3/search/${searchType}?api_key=${TMDB_API_KEY}&query=${encodeURIComponent(query)}&include_adult=false&page=${currentSearchPage}`;
        const res = await fetch(url);
        const data = await res.json();
        if (!loadMore) resultsContainer.innerHTML = '';
        if (data.results && data.results.length > 0) {
            let html = '';
            data.results.forEach(item => {
                if (searchType === 'multi' && item.media_type === 'person') return;
                let type = item.media_type || searchType;
                if(type === 'multi') type = item.title ? 'movie' : 'tv';
                const title = item.title || item.name;
                const year = (item.release_date || item.first_air_date || '').split('-')[0];
                const poster = item.poster_path ? `https://image.tmdb.org/t/p/w200${item.poster_path}` : 'https://via.placeholder.com/200x300?text=No+Image';
                html += `<div class="tmdb-item" onclick="selectTmdbItem('${item.id}', '${type}')"><div class="tmdb-type-badge">${type.toUpperCase()}</div><img src="${poster}" class="tmdb-poster" alt="${title}"><div class="tmdb-info"><span class="tmdb-title">${title}</span><div class="tmdb-year">${year}</div></div></div>`;
            });
            if(loadMore) { resultsContainer.insertAdjacentHTML('beforeend', html); } else { resultsContainer.innerHTML = html; }
            if (data.page < data.total_pages) { loadMoreBtn.style.display = 'block'; loadMoreBtn.innerText = 'LOAD MORE RESULTS'; } else { loadMoreBtn.style.display = 'none'; }
        } else {
            if(!loadMore) resultsContainer.innerHTML = '<p style="color:#aaa; text-align:center;">No results found.</p>';
            loadMoreBtn.style.display = 'none';
        }
    } catch (e) { console.error(e); if(!loadMore) resultsContainer.innerHTML = '<p style="color:red; text-align:center;">Error searching TMDB.</p>'; } finally { isSearching = false; if(loadMore) loadMoreBtn.innerText = 'LOAD MORE RESULTS'; }
}

function selectTmdbItem(id, type) { if (type === 'tv') { showTvSeasons(id); } else { document.getElementById('tmdbId').value = id; document.getElementById('tmdbType').value = type; closeTmdbSearch(); } }

async function showTvSeasons(id) {
    const resultsContainer = document.getElementById('tmdbResults');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    resultsContainer.classList.remove('tmdb-results'); 
    resultsContainer.style.display = 'block'; 
    loadMoreBtn.style.display = 'none';
    resultsContainer.innerHTML = '<div style="height:300px; display:flex; align-items:center; justify-content:center;"><i class="fas fa-spinner fa-spin fa-3x" style="color:var(--secondary)"></i></div>';
    try {
        const url = `https://api.themoviedb.org/3/tv/${id}?api_key=${TMDB_API_KEY}`;
        const res = await fetch(url);
        if(!res.ok) throw new Error("Failed to fetch details");
        const data = await res.json();
        const showTitle = data.name;
        let html = `<div class="season-view-header"><button onclick="runTmdbSearch(false)" class="back-btn-styled"><i class="fas fa-chevron-left"></i> Back</button><h3>Select Season: <span style="color:var(--secondary)">${showTitle}</span></h3><div style="width:80px"></div> </div><div class="season-grid">`;
        if(data.seasons && data.seasons.length > 0) {
            data.seasons.forEach(season => {
                const poster = season.poster_path ? `https://image.tmdb.org/t/p/w342${season.poster_path}` : 'https://via.placeholder.com/342x513?text=No+Cover';
                const sName = season.name; const epCount = season.episode_count; const airDate = season.air_date ? season.air_date.split('-')[0] : 'N/A'; const sNum = season.season_number;
                html += `<div class="season-card" onclick="selectSeason('${id}', '${sNum}')"><div class="season-poster-wrap"><span class="ep-badge">${epCount} EP</span><img src="${poster}" class="season-poster-img" alt="${sName}"></div><div class="season-details"><div class="season-title">${sName}</div><div><span class="season-meta">${airDate}</span></div></div></div>`;
            });
        } else { html += '<p style="text-align:center; width:100%; padding:20px; color:#aaa;">No seasons found for this show.</p>'; }
        html += '</div>'; resultsContainer.innerHTML = html;
    } catch(e) { console.error(e); resultsContainer.innerHTML = '<p style="color:var(--primary); text-align:center; padding:50px;">Error loading seasons. Please try again.</p>'; }
}

function selectSeason(tvId, seasonNum) { document.getElementById('tmdbId').value = tvId; document.getElementById('tmdbType').value = 'tv'; document.getElementById('seasonNum').value = seasonNum; closeTmdbSearch(); }

// --- MAIN PAGE GENERATOR (UPDATED FOR SORTING & LABELING & OFFSET) ---
function createVideoReplicaPage(groupName, files, tmdbData, adsConfig) {
    try {
        const uniqueFiles = [];
        const seenIds = new Set();
        files.forEach(f => {
            if(!seenIds.has(f.id)){
                seenIds.add(f.id);
                uniqueFiles.push(f);
            }
        });
        files = uniqueFiles;
        
        files.sort((a, b) => {
            const infoA = getFileDetails(a.name);
            const infoB = getFileDetails(b.name);
            if (infoA.resScore !== infoB.resScore) { return infoA.resScore - infoB.resScore; }
            if (infoA.codecScore !== infoB.codecScore) { return infoA.codecScore - infoB.codecScore; }
            if (infoA.partNum !== null && infoB.partNum !== null) { return infoA.partNum - infoB.partNum; } 
            else if (infoA.partNum !== null && infoB.partNum === null) { return -1; } 
            else if (infoA.partNum === null && infoB.partNum !== null) { return 1; }
            if (infoA.startEp !== 9999 && infoB.startEp !== 9999) { return infoA.startEp - infoB.startEp; }
            return a.name.localeCompare(b.name, undefined, { numeric: true, sensitivity: 'base' });
        });

        let labelCounts = {};
        files.forEach(file => {
             const d = getFileDetails(file.name);
             let baseKey = (d.resLabel + " " + d.codecLabel + d.qualityTag).trim().replace(/\s+/g, ' ');
             if (!labelCounts[baseKey]) labelCounts[baseKey] = 0;
             labelCounts[baseKey]++;
        });
        
        let currentLabelIteration = {};

        const pageTitle = tmdbData.title || tmdbData.name || "Anime Infinity";
        let headerTitle = groupName; 
        
        const tType = document.getElementById("tmdbType").value;
        if(tType === 'movie') { headerTitle = pageTitle; }

        let overview = tmdbData.overview || "No synopsis available.";
        let date = tmdbData.release_date || tmdbData.first_air_date || "0000-00-00";
        let ratingVal = tmdbData.vote_average || 0;
        
        let backgroundImageUrl = "";
        let posterUrl = "";
        
        if (tmdbData.is_manual) {
            posterUrl = tmdbData.poster_path || 'https://via.placeholder.com/1920x1080/000000/FFFFFF/?text=No+Poster';
            backgroundImageUrl = tmdbData.backdrop_path || posterUrl;
        } else {
            backgroundImageUrl = tmdbData.backdrop_path ? `https://image.tmdb.org/t/p/original${tmdbData.backdrop_path}` : 'https://via.placeholder.com/1920x1080/000000/FFFFFF/?text=No+Backdrop';
            posterUrl = tmdbData.poster_path ? `https://image.tmdb.org/t/p/w780${tmdbData.poster_path}` : backgroundImageUrl;

            if (currentSeasonData && currentSeasonData.poster_path) {
                posterUrl = `https://image.tmdb.org/t/p/w780${currentSeasonData.poster_path}`;
            }
        }

        let trailerBtn = "";
        let trailerKey = "";
        
        if (tmdbData.is_manual) {
            if (tmdbData.manual_trailer) {
                if(tmdbData.manual_trailer.includes('youtube.com') || tmdbData.manual_trailer.includes('youtu.be')) {
                    trailerBtn = `<a href="${tmdbData.manual_trailer}" target="_blank" class="btn btn-sm btn-outline-warning mt-2"><i class="fab fa-youtube"></i> Watch Trailer</a>`;
                }
            }
        } else {
            if(tmdbData.videos && tmdbData.videos.results) {
                const trailer = tmdbData.videos.results.find(v => (v.type === "Trailer" || v.type === "Teaser") && v.site === "YouTube");
                if(trailer) trailerKey = trailer.key;
            }
            if (trailerKey) {
                 trailerBtn = `<a href="https://www.youtube.com/watch?v=${trailerKey}" target="_blank" class="btn btn-sm btn-outline-warning mt-2"><i class="fab fa-youtube"></i> Watch Trailer</a>`;
            }
        }

        const isAnyArchive = files.some(f => {
            const n = f.name.toLowerCase();
            return n.endsWith(".zip") || n.endsWith(".rar") || n.endsWith(".7z") || n.endsWith(".tar") || n.endsWith(".iso");
        });

        if (isAnyArchive && currentSeasonData && !tmdbData.is_manual) {
            if(currentSeasonData.overview && currentSeasonData.overview.trim() !== "") { overview = currentSeasonData.overview; }
            if(currentSeasonData.air_date) { date = currentSeasonData.air_date; }
            if(currentSeasonData.vote_average && currentSeasonData.vote_average > 0) { ratingVal = currentSeasonData.vote_average; }
            if(currentSeasonData.videos && currentSeasonData.videos.results) {
                 const sTrailer = currentSeasonData.videos.results.find(v => (v.type === "Trailer" || v.type === "Teaser") && v.site === "YouTube");
                 if(sTrailer) {
                      trailerKey = sTrailer.key;
                      trailerBtn = `<a href="https://www.youtube.com/watch?v=${trailerKey}" target="_blank" class="btn btn-sm btn-outline-warning mt-2"><i class="fab fa-youtube"></i> Watch Trailer</a>`;
                 }
            }
        }

        if (!tmdbData.is_manual) {
             // Extract Ep Num from GroupName (which has already been labeled offset in groupFilesByEpisode)
             const epMatch = groupName.match(/(?:Episode|Ep|e)\s?\.?_?(\d+)/i);
             const currentEpNum = epMatch ? parseInt(epMatch[1]) : null;

             if(!isAnyArchive && currentEpNum) {
                // Determine Original File Number to calculate TMDB Offset
                // Logic: groupName has 'currentEpNum'. This number comes from (FileEp + (LabelStart-1))
                // We need to revert to FileEp to apply TMDB Offset?
                // NO. Wait.
                // We have 'files' array. We can just check the name of the first file to know its true Ep number.
                
                let originalFileEp = currentEpNum; // Fallback
                if(files.length > 0) {
                    const fMatch = files[0].name.match(/(?:ep|episode|e)\s?\.?_?(\d+)/i);
                    if(fMatch) originalFileEp = parseInt(fMatch[1]);
                }
                
                const tmdbOffsetInput = document.getElementById("tmdbStartOffset").value;
                let tmdbLookupIndex = originalFileEp;
                
                if(tmdbOffsetInput) {
                    // If file is 1, and TMDB Offset is 13. We want to lookup 13.
                    // 1 + (13-1) = 13.
                    tmdbLookupIndex = originalFileEp + (parseInt(tmdbOffsetInput) - 1);
                }

                if(episodeDataMap[tmdbLookupIndex]) {
                    const epData = episodeDataMap[tmdbLookupIndex];
                    if(epData.overview) overview = epData.overview;
                    if(epData.air_date) date = epData.air_date;
                    if(epData.vote_average) ratingVal = epData.vote_average;
                    
                    if(epData.name) {
                        if (epData.name.match(/^Episode\s+\d+$/i)) { 
                            headerTitle = `Episode ${currentEpNum.toString().padStart(2, '0')}`; 
                        } 
                        else { 
                            headerTitle = `Episode ${currentEpNum.toString().padStart(2, '0')} - ${epData.name}`; 
                        }
                    }
                    if(epData.still_path) {
                        posterUrl = `https://image.tmdb.org/t/p/w780${epData.still_path}`;
                        backgroundImageUrl = `https://image.tmdb.org/t/p/original${epData.still_path}`;
                    }
                 }
             }
        }

        const rating = ratingVal ? (Math.round(ratingVal * 10) / 10) : "N/A";
        let runtimeMinutes = tmdbData.runtime || (tmdbData.episode_run_time ? tmdbData.episode_run_time[0] : 0);
        const durationStr = formatDuration(runtimeMinutes);

        let linksHtml = "";
        
        files.forEach(file => {
            const downloadUrl = `https://drive.google.com/uc?export=download&id=${file.id}`;
            const watchUrl = `https://drive.google.com/file/d/${file.id}/preview`; 
            const safeDownloadUrl = obfuscateString(downloadUrl);
            const safeWatchUrl = obfuscateString(watchUrl);
            const size = formatSize(file.size);
            const details = getFileDetails(file.name);
            const displayName = file.name.toLowerCase();
            const isArchive = displayName.endsWith(".zip") || displayName.endsWith(".rar") || displayName.endsWith(".7z") || displayName.endsWith(".tar") || displayName.endsWith(".iso");
            
            let displayLabel = "";
            let baseKey = (details.resLabel + " " + details.codecLabel + details.qualityTag).trim().replace(/\s+/g, ' ');
            
            let mainPart = baseKey;
            let suffix = "";
            
            if (details.partNum) {
                suffix = " Part " + details.partNum.toString().padStart(2, '0');
            }
            else if (isArchive) {
                 if (labelCounts[baseKey] > 1) {
                      if (!currentLabelIteration[baseKey]) currentLabelIteration[baseKey] = 0;
                      currentLabelIteration[baseKey]++;
                      suffix = " Part " + currentLabelIteration[baseKey].toString().padStart(2, '0');
                 }
            }
            
            displayLabel = mainPart + suffix + " - " + size;
            
            let dlAttr = `data-s="${safeDownloadUrl}" href="javascript:void(0)"`;
            let watchAttr = `data-s="${safeWatchUrl}" href="javascript:void(0)"`;
            let triggerClass = "ads-trigger";
            
            let watchOnlineBtn = isArchive ? "" : `<a class='btn btn-outline-warning mr-2 mt-1 ${triggerClass}' ${watchAttr}>Watch Online</a>`;
            let infinityDriveBtn = "";
            if (file.infinityLink) {
                 const safeInfDrive = obfuscateString(file.infinityLink);
                 infinityDriveBtn = `<a class='btn btn-outline-info mr-2 mt-1 ads-trigger' data-s="${safeInfDrive}" href="javascript:void(0)">Infinity Drive</a>`;
            }

            linksHtml += `
            <div class="card" style="opacity:95%">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h5>${displayLabel}</h5>
                            <a class='btn btn-outline-primary mr-2 mt-1 ${triggerClass}' ${dlAttr}>GDrive</a>
                            ${watchOnlineBtn}
                            ${infinityDriveBtn}
                        </div>
                    </div>
                </div>
            </div>
            <br>`;
        });

        // Redirect UI Code (Popup)
        let redirectUiCode = `
<div id="redirectModal" class="redirect-overlay" style="display:none;">
    <div class="redirect-box">
        <div class="rb-header">
            <h3 id="modalTitle" style="color:#fff; margin:0; text-align:left; font-size:16px; font-weight:bold;">Select Your Shortener Redirect System</h3>
            <div style="display:flex; align-items:center; gap:10px;">
                <select id="langSelect" onchange="changeLang()" style="background:#000; color:#fff; border:1px solid #333; border-radius:4px; padding:4px 8px; font-size:11px; cursor:pointer;"><option value="en">English</option><option value="hi">Hindi</option><option value="hinglish" selected>Hinglish</option><option value="ta">Tamil</option><option value="te">Telugu</option></select>
                <div class="close-circle" onclick="closeModal()"><i class="fas fa-times"></i></div>
            </div>
        </div>
        <div class="rb-body">
            <div style="margin-bottom:15px; margin-top:10px;"><label id="destLabel" style="color:#aaa; font-size:11px; display:block; margin-bottom:5px; font-weight:bold;">Destination URL</label><div class="url-display-box"><span id="urlDomain" style="color:#03c1ef; margin-right:5px;"></span><span id="urlPath" class="blurred-text" style="color:#555;"></span></div></div>
            <div class="option-card active" id="opt24" onclick="selectOption('24')"><div style="display:flex; justify-content:space-between; align-items:center;"><span style="font-weight:bold; font-size:14px;"><span id="title24">24 Hours System</span> <span class="badge-rec">Recommended</span></span><div class="toggle-circle active"></div></div><div id="progressContainer" style="display:none; margin-top:10px; background:#000; padding:8px; border-radius:4px; border:1px dashed #333;"><span style="font-size:12px; color:#03c1ef; font-weight:bold;" id="progressText">Checking Steps...</span><div style="width:100%; background:#333; height:4px; border-radius:2px; margin-top:5px;"><div id="progressBar" style="width:0%; background:#03c1ef; height:100%; border-radius:2px; transition:0.3s;"></div></div></div><p id="desc24" style="font-size:12px; color:#aaa; margin-top:5px; line-height:1.4;">Har file download par aapko ek shortener ad dikhega. Saare ads solve karne ke baad, 24 ghante ka unlimited access milega!</p></div>
            <div class="option-card" id="optManual" onclick="selectOption('manual')"><div style="display:flex; justify-content:space-between; align-items:center;"><span id="titleManual" style="font-weight:bold; font-size:14px;">Manual System</span><div class="toggle-circle"></div></div><p id="descManual" style="font-size:12px; color:#aaa; margin-top:5px; line-height:1.4;">Purana method. Page refresh karein ad badalne ke liye. No 24h Access.</p></div>
            <button id="goBtn" class="go-btn" onclick="processRedirect()">Go to Destination <i class="fas fa-external-link-alt"></i></button>
            <p id="noteText" style="font-size:13px; color:#e0e0e0; text-align:center; margin-top:15px; font-weight:600;">Note: You can also change your selection later on the download page.</p>
        </div>
    </div>
</div>
<style>
    .redirect-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 9999; display: flex; justify-content: center; align-items: center; backdrop-filter: blur(5px); }
    .redirect-box { background: #171722; border-radius: 12px; border: 1px solid #333; width: 90%; max-width: 450px; font-family: 'Segoe UI', sans-serif; box-shadow: 0 20px 50px rgba(0,0,0,0.9); overflow: hidden; color: #fff !important; }
    .rb-header { background: #1f1f2e; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #333; border-radius: 12px 12px 0 0; }
    .close-circle { width: 24px; height: 24px; background: #d32f2f; border-radius: 50%; display: flex; align-items: center; justify-content: center; cursor: pointer; color: white; font-size: 12px; transition: 0.2s; }
    .close-circle:hover { background: #b71c1c; transform: scale(1.1); }
    .rb-body { padding: 20px; }
    .url-display-box { width: 100%; background: #000; border: 1px dashed #444; padding: 12px; border-radius: 6px; font-family: 'Courier New', monospace; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-size: 13px; display: flex; align-items: center; }
    .blurred-text { color: #999; filter: blur(5px); user-select: none; pointer-events: none; opacity: 0.7; }
    .option-card { background: #0f0f14; border: 1px solid #333; border-radius: 8px; padding: 15px; margin-bottom: 12px; cursor: pointer; transition: 0.2s; }
    .option-card:hover { border-color: #555; background: #1a1a24; }
    .option-card.active { border-color: #03c1ef; background: rgba(3, 193, 239, 0.05); }
    .badge-rec { background: #00e676; color: #000; padding: 2px 6px; border-radius: 4px; font-size: 10px; margin-left: 5px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .toggle-circle { width: 18px; height: 18px; border-radius: 50%; border: 2px solid #555; position: relative; }
    .toggle-circle.active { border-color: #03c1ef; }
    .toggle-circle.active::after { content: ''; position: absolute; top: 3px; left: 3px; width: 8px; height: 8px; background: #03c1ef; border-radius: 50%; }
    .go-btn { width: 100%; background: #fff; color: #000; font-weight: bold; padding: 14px; border: none; border-radius: 6px; margin-top: 5px; cursor: pointer; transition: 0.2s; font-size: 15px; letter-spacing: 0.5px; }
    .go-btn.locked { background: #03c1ef; color: white; }
</style>
<script>
    let activeApis = []; let selectedSystem = '24'; let currentDestUrl = ''; let globalAdsEnabled = false;
    fetch('/ads_config.json?v=' + Date.now()).then(response => response.json()).then(data => { if(data) { if(data.enabled === true || data.enabled === "true") { globalAdsEnabled = true; } if(data.apis) { activeApis = data.apis; } checkUrlForVerification(); } }).catch(err => console.log('Config Load Error:', err));
    const translations = { en: { title: "Select Redirect System", dest: "Destination URL", t24: "24 Hours System", d24: "Solve all steps to unlock 24h ad-free access.", tMan: "Manual System", dMan: "One ad per click. No 24h pass.", go: "Go to Destination", step: "Complete Step", note: "Note: You can also change your selection later on the download page." }, hi: { title: "सिस्टम चुनें", dest: "गंतव्य URL", t24: "24 घंटे सिस्टम", d24: "24 घंटे का विज्ञापन-मुक्त एक्सेस पाने के लिए सभी स्टेप्स पूरे करें।", tMan: "मैनुअल सिस्टम", dMan: "हर क्लिक पर एक विज्ञापन। कोई पास नहीं।", go: "गंतव्य पर जाएं", step: "पूरा करें स्टेप", note: "Note: आप डाउनलोड पेज पर बाद में अपना चयन बदल भी सकते हैं।" }, hinglish: { title: "System Choose Karein", dest: "Destination URL", t24: "24 Hours System", d24: "Saare shortners step-by-step solve karein aur 24 ghante ka free access paayein.", tMan: "Manual System", dMan: "Har baar ad dikhega. Koi 24h pass nahi.", go: "Go to Destination", step: "Complete Step", note: "Note: Aap download page par baad mein apna selection change bhi kar sakte hain." }, ta: { title: "அமைப்பைத் தேர்ந்தெடுக்கவும்", dest: "Destination URL", t24: "24 மணிநேர அமைப்பு", d24: "24 மணிநேர அணுகலைப் பெற அனைத்து படிகளையும் முடிக்கவும்.", tMan: "கையேடு அமைப்பு", dMan: "விளம்பரம் ஒவ்வொரு முறையும் தோன்றும்.", go: "செல்லவும்", step: "படி முடிக்க", note: "Note: பதிவிறக்கப் பக்கத்தில் உங்கள் தேர்வை பின்னர் மாற்றிக்கொள்ளலாம்." }, te: { title: "సిస్టమ్‌ని ఎంచుకోండి", dest: "గమ్యం URL", t24: "24 గంటల విధానం", d24: "24 గంటల యాక్సెస్ కోసం అన్ని దశలను పూర్తి చేయండి.", tMan: "మాన్యువల్ విధానం", dMan: "ప్రతి క్లిక్‌లో ప్రకటన కనిపిస్తుంది.", go: "వెళ్ళండి", step: "దశను పూర్తి చేయండి", note: "Note: మీరు డౌన్‌లోడ్ పేజీలో తర్వాత మీ ఎంపికను మార్చుకోవచ్చు." } };
    function deobfuscateString(str) { try { const rev2 = atob(str); const b64 = rev2.split('').reverse().join(''); const rev = atob(b64); return rev.split('').reverse().join(''); } catch(e) { return atob(str); } }
    function isUnlocked() { const unlockTime = localStorage.getItem('ads_unlock_until'); return (unlockTime && parseInt(unlockTime) > Date.now()); }
    function getCurrentStep() { return parseInt(localStorage.getItem('ads_current_step') || '0'); }
    function getTargetUrl() { return localStorage.getItem('ads_pending_dest') || ''; }
    function checkUrlForVerification() { const urlParams = new URLSearchParams(window.location.search); const verifyStep = urlParams.get('ads_verify_step'); if (verifyStep !== null && globalAdsEnabled && activeApis.length > 0) { const stepInt = parseInt(verifyStep); const storedStep = getCurrentStep(); if (stepInt === storedStep) { const nextStep = storedStep + 1; localStorage.setItem('ads_current_step', nextStep); if (nextStep >= activeApis.length) { const expiry = Date.now() + (24 * 60 * 60 * 1000); localStorage.setItem('ads_unlock_until', expiry); localStorage.setItem('ads_current_step', 0); alert("Congratulations! 24 Hours Access Unlocked."); const savedDest = getTargetUrl(); if (savedDest) { window.location.href = savedDest; } else { window.history.replaceState({}, document.title, window.location.pathname); closeModal(); } } else { alert("Step " + (stepInt + 1) + " Verified! Please click 'Next Step' to continue."); window.history.replaceState({}, document.title, window.location.pathname); if (getTargetUrl()) { currentDestUrl = getTargetUrl(); document.getElementById('redirectModal').style.display = 'flex'; updateProgressUI(); } } } } }
    document.addEventListener('DOMContentLoaded', function() { setTimeout(function(){ const triggers = document.querySelectorAll('.ads-trigger'); triggers.forEach(btn => { btn.addEventListener('click', function(e) { e.preventDefault(); const encryptedLink = this.getAttribute('data-s'); if(!encryptedLink) return; currentDestUrl = deobfuscateString(encryptedLink); if(!globalAdsEnabled || isUnlocked()) { window.open(currentDestUrl, '_blank'); return; } try { const urlObj = new URL(currentDestUrl); document.getElementById('urlDomain').innerText = urlObj.origin; let safePath = urlObj.pathname; if(safePath.length < 15) { safePath += "/s" + Math.random().toString(36).substring(7) + "x" + Math.random().toString(36).substring(7); } document.getElementById('urlPath').innerText = safePath + "..........[PROTECTED]"; } catch(e) { document.getElementById('urlDomain').innerText = "Encrypted Link"; document.getElementById('urlPath').innerText = "/secure/......................"; } document.getElementById('redirectModal').style.display = 'flex'; changeLang(); updateProgressUI(); localStorage.setItem('ads_pending_dest', currentDestUrl); }); }); const directTriggers = document.querySelectorAll('.direct-trigger'); directTriggers.forEach(btn => { btn.addEventListener('click', function(e) { e.preventDefault(); const encryptedLink = this.getAttribute('data-s'); if(!encryptedLink) return; const dest = deobfuscateString(encryptedLink); window.open(dest, '_blank'); }); }); }, 500); });
    function closeModal() { document.getElementById('redirectModal').style.display = 'none'; }
    function changeLang() { const lang = document.getElementById('langSelect').value; const t = translations[lang] || translations['en']; document.getElementById('modalTitle').innerText = t.title; document.getElementById('destLabel').innerText = t.dest; document.getElementById('title24').innerText = t.t24; document.getElementById('desc24').innerText = t.d24; document.getElementById('titleManual').innerText = t.tMan; document.getElementById('descManual').innerText = t.dMan; document.getElementById('noteText').innerText = t.note || t.title; updateProgressUI(); }
    function selectOption(opt) { selectedSystem = opt; document.getElementById('opt24').classList.remove('active'); document.getElementById('optManual').classList.remove('active'); document.querySelector('#opt24 .toggle-circle').classList.remove('active'); document.querySelector('#optManual .toggle-circle').classList.remove('active'); if(opt === '24') { document.getElementById('opt24').classList.add('active'); document.querySelector('#opt24 .toggle-circle').classList.add('active'); document.getElementById('progressContainer').style.display = 'block'; } else { document.getElementById('optManual').classList.add('active'); document.querySelector('#optManual .toggle-circle').classList.add('active'); document.getElementById('progressContainer').style.display = 'none'; } updateProgressUI(); }
    function updateProgressUI() { const btn = document.getElementById('goBtn'); const lang = document.getElementById('langSelect').value; const t = translations[lang] || translations['en']; if (selectedSystem === '24') { if (isUnlocked()) { btn.innerHTML = t.go + ' <i class="fas fa-check-circle"></i>'; btn.classList.remove('locked'); document.getElementById('progressText').innerText = "UNLOCKED (24 Hours)"; document.getElementById('progressBar').style.width = "100%"; } else { const step = getCurrentStep(); const total = activeApis.length; if (total > 0) { btn.innerHTML = \`\${t.step} \${step + 1} / \${total} <i class="fas fa-arrow-right"></i>\`; btn.classList.add('locked'); document.getElementById('progressText').innerText = \`Step \${step + 1} of \${total} Pending\`; document.getElementById('progressBar').style.width = ((step / total) * 100) + "%"; } else { btn.innerHTML = "Configuration Error (No APIs)"; } } } else { btn.innerHTML = t.go + ' <i class="fas fa-external-link-alt"></i>'; btn.classList.remove('locked'); } }
    async function processRedirect() { if (!currentDestUrl) return; if (isUnlocked()) { window.open(currentDestUrl, '_blank'); closeModal(); return; } if (selectedSystem === 'manual') { if (activeApis.length === 0) { window.open(currentDestUrl, '_blank'); closeModal(); return; } const randIdx = Math.floor(Math.random() * activeApis.length); generateAndOpen(activeApis[randIdx], currentDestUrl, false); return; } if (selectedSystem === '24') { if (activeApis.length === 0) { window.open(currentDestUrl, '_blank'); closeModal(); return; } const step = getCurrentStep(); if (step >= activeApis.length) { localStorage.setItem('ads_unlock_until', Date.now() + 86400000); window.open(currentDestUrl, '_blank'); closeModal(); return; } const currentApi = activeApis[step]; const baseUrl = window.location.origin + window.location.pathname; const verifyUrl = baseUrl + '?ads_verify_step=' + step; generateAndOpen(currentApi, verifyUrl, true); } }
    async function generateAndOpen(apiConfig, targetUrl, isStep) { const btn = document.getElementById('goBtn'); const originalText = btn.innerHTML; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating Link...'; btn.disabled = true; let apiUrl = ''; const serviceName = apiConfig.service.toLowerCase().trim(); const encUrl = encodeURIComponent(targetUrl); if (serviceName.includes('gplinks')) apiUrl = \`https://api.gplinks.com/api?api=\${apiConfig.key}&url=\${encUrl}&format=json\`; else if (serviceName.includes('cuty.io')) apiUrl = \`https://api.cuty.io/quick?token=\${apiConfig.key}&url=\${encUrl}\`; else if (serviceName.includes('adrinolinks')) apiUrl = \`https://adrinolinks.in/api?api=\${apiConfig.key}&url=\${encUrl}\`; else if (serviceName.includes('exe.io')) apiUrl = \`https://exe.io/api?api=\${apiConfig.key}&url=\${encUrl}\`; else if (serviceName.includes('publicearn')) apiUrl = \`https://publicearn.com/api?api=\${apiConfig.key}&url=\${encUrl}\`; else { apiUrl = \`https://\${apiConfig.service}/api?api=\${apiConfig.key}&url=\${encUrl}\`; } try { let response = await fetch(apiUrl); let data = await response.json(); let shortUrl = ''; if (data.shortenedUrl) shortUrl = data.shortenedUrl; else if (data.short_url) shortUrl = data.short_url; else if (data.url) shortUrl = data.url; if (shortUrl) { if (isStep) { window.location.href = shortUrl; } else { window.open(shortUrl, '_blank'); closeModal(); } } else { alert('Error creating link with ' + serviceName + '. Check API Key.'); } } catch (e) { console.error(e); alert('Connection Error with Shortener ' + serviceName); } btn.innerHTML = originalText; btn.disabled = false; }
<\/script>

`;

        const pageHtml = `<!DOCTYPE html><html lang="en">
<head>    <meta charset="UTF-8">    <title>${pageTitle} | Anime Infinity</title>    <meta name="viewport" content="width=device-width, initial-scale=1.0">    <meta property="og:title" content="Anime Infinity">    <meta name="robots" content="noindex">
    <link rel="icon" href="https://animeinfinite.com/wp-content/uploads/2025/03/cropped-anime-infinity-2-1-1-1.png" />    <link href="https://cdn.jsdelivr.net/gh/animenow/files@main/style.css" rel="stylesheet">    <script src="https://cdn.jsdelivr.net/gh/animenow/files@main/jquery.min.js"><\/script>    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.14.0/css/all.min.css" />    <style>        @import url('https://fonts.googleapis.com/css2?family=Russo+One&display=swap');
        body {            background: url(${backgroundImageUrl});            background-size: cover;            background-position: center top;            background-repeat: no-repeat;            font-family: 'Russo One', cursive !important;            background-attachment: fixed;        }
        [data-theme=dark] .card-body {            background-color: rgba(31, 35, 45, 0.9);        } 
        /* FIX FOR SETTINGS MODAL IN DARK MODE */
        [data-theme="dark"] .modal-content, 
        [data-theme="dark"] .modal-header,
        [data-theme="dark"] .modal-body {
            background-color: #ffffff !important;
            color: #333333 !important;
            border-bottom-color: #dee2e6 !important;
        }
        [data-theme="dark"] .close {
            color: #333333 !important;
            opacity: 1 !important;
            text-shadow: none !important;
        }
   </style></head>
<body class="min-vh-100 d-flex flex-column" data-theme="dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom border-light fixed-top">        <div class="container-fluid">            <a class="navbar-brand" href="https://animeinfinite.com/" title="Anime Infinity">                <img src="https://animeinfinite.com/wp-content/uploads/2025/03/cropped-anime-infinity-2-1-1-1.png" height="30" class="lazyload d-inline-block align-top"                    alt="Anime Infinity">            </a>            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive">                <span class="navbar-toggler-icon"></span>            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">                <div class="navbar-nav my-3 my-lg-0 order-2">                    <form id="quick_search_form" class="navbar-form input-group input-group-sm"                        action="https://animeinfinite.com/" method="get">                        <input type="text" name="s" class="form-control"                            placeholder="[Anime Infinity] Search..." autocomplete="off">                    </form>                </div>                <ul class="nav navbar-nav mr-auto order-1">                    <li class="nav-item"><a class="nav-link" href="https://animeinfinite.com/"><i class="fas fa-home fa-fw"></i> Home</a></li>                </ul>                <ul class="navbar-nav my-2 my-lg-0 mx-lg-2 order-3">                    <li class="nav-item">                        <a class="nav-link" href="#" data-toggle="modal" data-target="#settings" title="Settings">                            <i class="fas fa-cloud-moon fa-fw"></i> <span class="d-lg-none">Choose Theme</span>                        </a>                    </li>                </ul>            </div>        </div>    </nav>
    <br><br><br>
    <div class="container">    <div class="card" style="opacity:95%">        <div class="row g-0">            <div class="col-md-3" style="max-width:200px">
                <img src="${posterUrl}" width="100%" style="margin:20px;">
            </div>            <div class="col-md-6">                <div class="card-body" style="background:transparent">                    <h5 class="card-title">${headerTitle}</h5>                                        <p class="card-text">${overview}</p>                </div>            </div>            <div class="col-md-3"><div class="card-body" style="background:transparent"><p><i class="fas fa-calendar-alt fa-fw"></i> ${date}</p><p><i class="fas fa-star fa-fw"></i> ${rating}/10.0</p><p><i class="fas fa-clock fa-fw"></i> ${durationStr}</p>${trailerBtn}</div></div>        </div>    </div>
    <br>
    ${linksHtml}
    </div>    <script src="https://cdn.jsdelivr.net/gh/animenow/files@main/script.js" async><\/script>
    <div class="modal fade" id="settings" tabindex="-1" aria-hidden="true">        <div class="modal-dialog">            <div class="modal-content">                <div class="modal-header">                    <h6 class="modal-title">Settings</h6>                </div>                <div class="modal-body">                    <div class="d-flex align-items-center justify-content-between">                        <div class="font-weight-bold">Dark Theme</div>                        <div>                            <div class="custom-control custom-switch">                                <input type="checkbox" class="custom-control-input" id="darkSwitch">                                <label class="custom-control-label" for="darkSwitch" id="darkSwitchText">on</label>                            </div>                        </div>                    </div>                    <small>Dark theme turns the light surfaces of the page dark, creating an experience ideal for                        night.</small>                </div>            </div>        </div>    </div>
    <br><br>    <footer class="mt-auto bg-light">        <div class="container py-3">            <p class="text-center text-muted">                <a href="https://animeinfinite.com">Anime Infinity</a> does not host any files on its own. All files are hosted on third-party                websites.            </p>            <p class="text-center text-muted">Made by Team Anime Infinity</p>        </div>    </footer>
${redirectUiCode}</body>
</html>`;

        return pageHtml; 
    } catch(e) { console.error("Error creating page for " + groupName, e); return "error"; }
}

function resetUI(message){
    masterFileList=[];
    document.getElementById("finalLinks").value=message;
    document.getElementById("fileList").style.display="none";
    document.getElementById("statusLog").innerText="";
    document.getElementById('accEpisodes').value='';
    document.getElementById('accZips').value='';
    document.getElementById('accMovies').value='';
}

function logStatus(text){console.log(text);document.getElementById("statusLog").innerText=text}

async function renderSmartResults(){
    const finalLinksArea=document.getElementById("finalLinks");
    const listContainer=document.getElementById("fileList");
    if(masterFileList.length===0){finalLinksArea.value="No files found.";return}
    const groupedFiles=groupFilesByEpisode(masterFileList);
    const entries=Object.entries(groupedFiles);
    entries.sort((a,b)=>{const nameA=a[0];const nameB=b[0];const epMatchA=nameA.match(/Episode\s+(\d+)/i);const epMatchB=nameB.match(/Episode\s+(\d+)/i);if(epMatchA&&epMatchB)return parseInt(epMatchA[1])-parseInt(epMatchB[1]);return nameA.localeCompare(nameB)});
    const adsConfig=globalAdsConfig;
    let finalText="";
    let htmlList="";
    let accEpStr="";
    let accZipStr="";
    let accMovStr="";
    
    const tmdbType=document.getElementById("tmdbType").value;
    const baseLinkType=tmdbType==='movie'?'movie':'episode';
    
    for(const[groupName,files]of entries){
        logStatus("Fetching Infinity Drive links for: " + groupName + "...");
        await Promise.all(files.map(async (file) => {
            if (!file.infinityLink) { 
                const link = await fetchInfinityLinkFromServer(file.id);
                if (link) { file.infinityLink = link; }
            }
        }));

        logStatus("Generating: "+groupName);
        const isArchive=files.some(f=>f.name.toLowerCase().match(/\.(zip|rar|7z|tar|iso)$/));
        const currentLinkType=isArchive?'zip':baseLinkType;
        const pageHtml=createVideoReplicaPage(groupName,files,currentTmdbData,adsConfig);
        if(pageHtml!=="error"){
            const slug=generateSlug(currentTmdbData,groupName,currentLinkType);
            logStatus(`Uploading ${slug}...`);
            const serverLink=await uploadToServer(slug,currentLinkType,pageHtml);
            if(serverLink){
                finalText+=`${groupName}\n${serverLink}\n\n`;
                htmlList+=`<div class="file-item"><span class="file-name" style="color: #ff0050;">${groupName}</span><div><a href="${serverLink}" target="_blank" class="copy-btn" style="text-decoration:none; margin-right:5px;">OPEN</a><button class="copy-btn" onclick="copySingle('${serverLink}', this)">COPY</button></div></div>`;
                
                if(currentLinkType==='zip'){
                    accZipStr+=`\n[mks_accordion_item title="${groupName}"]</p>\n<p style="text-align: center;">[mks_button size="medium" title="Get Download Links" style="squared" url="${serverLink}" target="_self" bg_color="#dd3333" txt_color="#FFFFFF" icon="" icon_type="" nofollow="0"]\n[/mks_accordion_item]`
                }else if(currentLinkType==='episode'){
                    accEpStr+=`\n[mks_accordion_item title="${groupName}"]</p>\n<p style="text-align: center;">[mks_button size="medium" title="Watch/Download" style="squared" url="${serverLink}" target="_self" bg_color="#1e73be" txt_color="#FFFFFF" icon="" icon_type="" nofollow="0"]\n[/mks_accordion_item]`
                }else if(currentLinkType==='movie'){
                    accMovStr+=`\n[mks_accordion_item title="${groupName}"]</p>\n<p style="text-align: center;">[mks_button size="medium" title="Watch/Download" style="squared" url="${serverLink}" target="_self" bg_color="#00722b" txt_color="#000000" icon="" icon_type="" nofollow="0"]\n[/mks_accordion_item]`
                }
                saveFileRecord(groupName,serverLink)
            }else{
                finalText+=`${groupName} (Error Uploading)\n`
            }
        }
    }
    
    if(accEpStr) document.getElementById('accEpisodes').value = '<p style="text-align: center;">[mks_accordion]' + accEpStr + '\n[/mks_accordion]</p>';
    if(accZipStr) document.getElementById('accZips').value = '<p style="text-align: center;">[mks_accordion]' + accZipStr + '\n[/mks_accordion]</p>';
    if(accMovStr) document.getElementById('accMovies').value = '<p style="text-align: center;">[mks_accordion]' + accMovStr + '\n[/mks_accordion]</p>';

    logStatus("All done! Links generated and Saved.");
    finalLinksArea.value=finalText;
    listContainer.innerHTML=htmlList;
    listContainer.style.display="block"
}

// --- MODIFIED COPY FUNCTIONS FOR UI FEEDBACK ---
function copyBulk(elementId, btnElement) {
    const copyText = document.getElementById(elementId);
    copyText.select();
    document.execCommand("copy");
    
    // UI Feedback: Change button text and color
    const originalText = btnElement.innerText;
    const originalColor = btnElement.style.color;
    const originalBorder = btnElement.style.borderColor;

    btnElement.innerText = "COPIED!";
    btnElement.style.color = "#00e676"; // Success Green
    btnElement.style.borderColor = "#00e676";

    // Revert after 2 seconds
    setTimeout(() => {
        btnElement.innerText = originalText;
        btnElement.style.color = originalColor;
        btnElement.style.borderColor = originalBorder;
    }, 2000);
}

function copySingle(text, btnElement) {
    navigator.clipboard.writeText(text);
    
    // UI Feedback for individual items
    const originalText = btnElement.innerText;
    btnElement.innerText = "COPIED";
    btnElement.style.background = "#00e676"; 
    btnElement.style.color = "#000";

    // Revert after 2 seconds
    setTimeout(() => {
        btnElement.innerText = originalText;
        btnElement.style.background = ""; 
        btnElement.style.color = "";
    }, 2000);
}

async function syncAdsWithServer(config){
    globalAdsConfig = config;
    const btnAdd = document.querySelector('#section-ads button[onclick="saveAdApi()"]');
    if(btnAdd) { btnAdd.innerText = "Saving..."; btnAdd.disabled = true; }
    const formData=new FormData();
    formData.append('action','save_ads_config');
    formData.append('config_data',JSON.stringify(config));
    try{
        const req = await fetch(window.location.href,{method:'POST',body:formData});
        const res = await req.json();
        if(btnAdd) { btnAdd.innerText = "ADD"; btnAdd.disabled = false; }
    }catch(e){
        console.error("Failed to sync config:",e);
        alert("Error saving settings to Server. Check connection.");
        if(btnAdd) { btnAdd.innerText = "ADD"; btnAdd.disabled = false; }
    }
}

function loadAdSettings(){
    const config = globalAdsConfig || {enabled:false, apis:[]};
    const switchEl=document.getElementById('adsMasterSwitch');
    const statusText=document.getElementById('adsStatusText');
    if(switchEl){
        switchEl.checked=config.enabled;
        statusText.innerText=config.enabled?"ENABLED":"DISABLED";
        statusText.style.color=config.enabled?"#fff":"#aaa";
    }
    renderApiList(config.apis);
}

function toggleAdsSystem(){
    const switchEl=document.getElementById('adsMasterSwitch');
    const statusText=document.getElementById('adsStatusText');
    const isEnabled=switchEl.checked;
    statusText.innerText=isEnabled?"ENABLED":"DISABLED";
    statusText.style.color=isEnabled?"#fff":"#aaa";
    let config = JSON.parse(JSON.stringify(globalAdsConfig));
    config.enabled = isEnabled;
    syncAdsWithServer(config);
}

function saveAdApi(){
    const serviceInput=document.getElementById('shortenerService');
    const service=serviceInput.value.trim();
    const key=document.getElementById('shortenerApiKey').value.trim();
    if(!service||!key){alert("Please enter both Domain and API Key.");return}
    let config = JSON.parse(JSON.stringify(globalAdsConfig));
    if(!config.apis) config.apis = [];
    if(config.apis.some(api=>api.service===service)){alert("An API key for "+service+" is already added.");return}
    config.apis.push({service:service,key:key});
    document.getElementById('shortenerApiKey').value='';
    renderApiList(config.apis);
    syncAdsWithServer(config);
}

function renderApiList(apis){
    const container=document.getElementById('apiListContainer');
    if(!apis||apis.length===0){container.innerHTML='<p style="text-align:center; color:#555; padding:20px;">No Shortener APIs added yet.</p>';return}
    let html='';
    apis.forEach((api,index)=>{
        const isFirst=index===0;
        const isLast=index===apis.length-1;
        const upBtn=isFirst?'':`<button onclick="moveAdApi(${index}, -1)" class="action-btn secondary" title="Move Up"><i class="fas fa-arrow-up"></i></button>`;
        const downBtn=isLast?'':`<button onclick="moveAdApi(${index}, 1)" class="action-btn secondary" title="Move Down"><i class="fas fa-arrow-down"></i></button>`;
        html+=`<div class="api-item"><div style="display:flex; align-items:center; flex:1; overflow:hidden;"><span class="api-badge">${api.service}</span><span class="api-key-text" title="${api.key}">${api.key}</span></div><div style="display:flex;">${upBtn}${downBtn}<button onclick="deleteAdApi(${index})" class="action-btn btn-delete" title="Delete"><i class="fas fa-trash"></i></button></div></div>`;
    });
    container.innerHTML=html;
}

function moveAdApi(index,direction){
    let config = JSON.parse(JSON.stringify(globalAdsConfig));
    const apis=config.apis;
    if(direction===-1&&index===0)return;
    if(direction===1&&index===apis.length-1)return;
    const temp=apis[index];
    apis[index]=apis[index+direction];
    apis[index+direction]=temp;
    config.apis=apis;
    renderApiList(config.apis);
    syncAdsWithServer(config);
}

function deleteAdApi(index){
    if(!confirm("Permanently delete this API Key?"))return;
    let config = JSON.parse(JSON.stringify(globalAdsConfig));
    config.apis.splice(index,1);
    renderApiList(config.apis);
    syncAdsWithServer(config);
}

loadAdSettings();
renderSavedFiles();
</script>
</body>
</html>