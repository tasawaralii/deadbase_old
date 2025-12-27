<?php
session_start();

// ==========================================
// CONFIGURATION & LOGIN LOGIC
// ==========================================
$apiKey = 'e758ca7bb1f8114f001228ca519fd9cb';
$adminUser = 'Rumman8888';
$adminPass = '714212835rR*';
$faviconUrl = 'https://animeinfinite.com/wp-content/uploads/2025/03/cropped-anime-infinity-2-1-1-1.png';

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$loginError = '';
if (isset($_POST['login_btn'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];
    if ($user === $adminUser && $pass === $adminPass) {
        $_SESSION['logged_in'] = true;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        $loginError = "❌ Wrong Credentials!";
    }
}

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Anime Infinity</title>
        <link rel="icon" href="<?php echo $faviconUrl; ?>" type="image/png">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            /* EYE RELAXATION LOGIN */
            body {
                margin: 0;
                padding: 0;
                font-family: 'Poppins', sans-serif;
                background: #0f172a;
                height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .login-card {
                background: #1e293b;
                padding: 40px;
                border-radius: 16px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
                width: 100%;
                max-width: 380px;
                text-align: center;
                border: 1px solid #334155;
            }

            .login-card img {
                width: 70px;
                margin-bottom: 15px;
                opacity: 0.9;
            }

            .login-card h2 {
                color: #cbd5e1;
                margin-bottom: 25px;
                font-weight: 500;
                font-size: 18px;
                letter-spacing: 0.5px;
            }

            .input-group {
                position: relative;
                margin-bottom: 20px;
                text-align: left;
            }

            .input-group i {
                position: absolute;
                left: 15px;
                top: 14px;
                color: #64748b;
                font-size: 14px;
            }

            .input-group input {
                width: 100%;
                padding: 12px 10px 12px 40px;
                border: 1px solid #334155;
                background: #0f172a;
                border-radius: 8px;
                color: #e2e8f0;
                box-sizing: border-box;
                font-size: 14px;
                transition: 0.2s;
            }

            .input-group input:focus {
                border-color: #38bdf8;
                outline: none;
                box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.1);
            }

            .btn-login {
                width: 100%;
                padding: 12px;
                border: none;
                border-radius: 8px;
                background: #38bdf8;
                color: #0f172a;
                font-weight: 600;
                font-size: 14px;
                cursor: pointer;
                transition: 0.2s;
                margin-top: 10px;
            }

            .btn-login:hover {
                background: #0ea5e9;
            }

            .error {
                color: #f87171;
                font-size: 13px;
                margin-bottom: 15px;
                display: block;
                background: rgba(248, 113, 113, 0.1);
                padding: 5px;
                border-radius: 4px;
            }
        </style>
    </head>

    <body>
        <div class="login-card">
            <img src="<?php echo $faviconUrl; ?>" alt="Logo">
            <h2>Restricted Access</h2>
            <?php if ($loginError)
                echo "<span class='error'>$loginError</span>"; ?>
            <form method="POST">
                <div class="input-group"><i class="fas fa-user"></i><input type="text" name="username"
                        placeholder="Username" required autocomplete="off"></div>
                <div class="input-group"><i class="fas fa-key"></i><input type="password" name="password"
                        placeholder="Password" required></div>
                <button type="submit" name="login_btn" class="btn-login">Unlock Panel</button>
            </form>
        </div>
    </body>

    </html>
    <?php exit;
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anime Infinity Generator</title>
    <link rel="icon" href="<?php echo $faviconUrl; ?>" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* EYE RELAXATION VARIABLES (Slate/Grey Tones) */
        :root {
            --bg-body: #0f172a;
            /* Very Deep Slate */
            --bg-panel: #1e293b;
            /* Slate 800 */
            --bg-input: #334155;
            /* Slate 700 */
            --border-subtle: #475569;
            /* Slate 600 */
            --text-main: #e2e8f0;
            /* Slate 200 */
            --text-muted: #94a3b8;
            /* Slate 400 */
            --accent: #38bdf8;
            /* Sky Blue (Calm) */
            --success: #10b981;
            /* Emerald */
            --danger: #ef4444;
            /* Soft Red */
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            line-height: 1.6;
        }

        .container {
            max-width: 1050px;
            margin: 0 auto;
            background: var(--bg-panel);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #1e293b;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-subtle);
            padding-bottom: 20px;
        }

        .header-title {
            text-align: left;
            color: var(--text-main);
            margin: 0;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }

        .header-title span {
            color: var(--text-muted);
            font-weight: 400;
            font-size: 0.8em;
        }

        .logout-btn {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: 0.2s;
            font-size: 13px;
            background: rgba(255, 255, 255, 0.05);
        }

        .logout-btn:hover {
            background: var(--danger);
            color: #fff;
        }

        .hidden {
            display: none !important;
        }

        .search-panel {
            background: #0f172a;
            padding: 8px;
            border-radius: 8px;
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 25px;
            border: 1px solid var(--border-subtle);
        }

        .search-panel input,
        .search-panel select {
            flex: 1;
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--text-main);
            font-weight: 500;
            outline: none;
            transition: 0.2s;
            font-size: 14px;
        }

        .search-panel input:focus {
            background: var(--bg-panel);
        }

        .search-panel select {
            background: var(--bg-panel);
            cursor: pointer;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            color: white;
            transition: 0.2s;
            font-size: 14px;
        }

        .btn-primary {
            background: var(--accent);
            color: #0f172a;
        }

        .btn-success {
            background: var(--success);
            color: #fff;
        }

        .btn-danger {
            background: var(--danger);
            color: #fff;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .card {
            background: var(--bg-body);
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: 0.2s;
            position: relative;
            border: 1px solid transparent;
        }

        .card:hover {
            transform: translateY(-4px);
            border-color: var(--accent);
        }

        .card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            opacity: 0.85;
        }

        .card:hover img {
            opacity: 1;
        }

        .card-body {
            padding: 10px;
        }

        .card-title {
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-date {
            font-size: 10px;
            color: var(--text-muted);
        }

        .badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(15, 23, 42, 0.9);
            padding: 2px 6px;
            font-size: 9px;
            border-radius: 4px;
            color: var(--accent);
            font-weight: 700;
            text-transform: uppercase;
        }

        /* ACCORDION */
        .accordion-item {
            margin-bottom: 15px;
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            overflow: hidden;
            background: #253045;
        }

        .accordion-header {
            background: #334155;
            padding: 15px 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 600;
            color: #fff;
            font-size: 14px;
            transition: 0.2s;
        }

        .accordion-header:hover {
            background: #475569;
        }

        .accordion-header i {
            color: var(--accent);
            transition: transform 0.3s;
        }

        .accordion-header.active i {
            transform: rotate(180deg);
        }

        .accordion-content {
            display: none;
            padding: 20px;
            border-top: 1px solid var(--border-subtle);
            animation: slideDown 0.3s ease;
        }

        .accordion-content.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .config-panel {
            margin-top: 20px;
        }

        .config-row {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-size: 13px;
        }

        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-subtle);
            background: var(--bg-body);
            color: var(--text-main);
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: var(--accent);
            outline: none;
        }

        .links-textarea {
            width: 100%;
            height: 120px;
            padding: 12px;
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            font-family: 'Consolas', monospace;
            font-size: 12px;
            color: #93c5fd;
            background: #0f172a;
            resize: vertical;
        }

        .lang-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            background: var(--bg-body);
            padding: 15px;
            border-radius: 6px;
            border: 1px solid var(--border-subtle);
        }

        .lang-item label {
            font-size: 13px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-muted);
        }

        input[type="checkbox"] {
            accent-color: var(--accent);
            cursor: pointer;
        }

        /* SCREENSHOT MANAGER STYLES (Selection Mode) */
        .ss-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }

        .ss-item {
            position: relative;
            border: 2px solid transparent;
            border-radius: 8px;
            overflow: hidden;
            transition: 0.2s;
            cursor: pointer;
            opacity: 0.6;
        }

        .ss-item:hover {
            opacity: 0.8;
        }

        .ss-item.selected {
            border-color: var(--success);
            opacity: 1;
            box-shadow: 0 0 10px rgba(16, 185, 129, 0.3);
        }

        .ss-item img {
            width: 100%;
            height: auto;
            display: block;
        }

        .ss-check {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--success);
            color: #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .ss-item.selected .ss-check {
            display: flex;
        }

        .output-area {
            margin-top: 30px;
            background: var(--bg-body);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--border-subtle);
        }

        textarea#finalCode,
        textarea#tagsOutput {
            width: 100%;
            padding: 15px;
            background: #0b1120;
            color: var(--accent);
            font-family: 'Consolas', monospace;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid #1e293b;
            font-size: 12px;
        }

        textarea#finalCode {
            height: 250px;
        }

        .seo-box-container {
            background: #1e293b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #334155;
        }

        .seo-input-wrapper {
            display: flex;
            gap: 10px;
        }

        #seoFilename {
            background: #0f172a;
            border: 1px solid var(--border-subtle);
            color: #fbbf24;
            font-weight: bold;
        }

        .loader {
            display: none;
            text-align: center;
            padding: 40px;
            color: var(--accent);
        }

        .btn-load-more {
            background: var(--bg-input);
            border: 1px solid var(--border-subtle);
            color: var(--text-muted);
            width: 100%;
            padding: 12px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
        }

        .btn-load-more:hover {
            background: var(--border-subtle);
            color: #fff;
        }

        hr {
            border: 0;
            height: 1px;
            background: var(--border-subtle);
            margin: 20px 0;
            opacity: 0.5;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header-top">
            <h2 class="header-title">
                <img src="<?php echo $faviconUrl; ?>" style="width: 36px; height: 36px; border-radius: 5px;">
                Anime Infinity <span>Generator</span>
            </h2>
            <a href="?action=logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <div class="search-panel">
            <input type="text" id="searchInput" placeholder="Search...">
            <select id="typeSelect" style="max-width: 120px;">
                <option value="multi">All</option>
                <option value="tv">TV</option>
                <option value="movie">Movies</option>
            </select>
            <button class="btn btn-primary" onclick="performSearch()"><i class="fas fa-search"></i></button>
        </div>

        <div id="loader" class="loader"><i class="fas fa-circle-notch fa-spin"></i> Loading...</div>
        <div id="resultsGrid" class="grid-container"></div>
        <div id="loadMoreSection" class="hidden" style="text-align:center;"><button class="btn-load-more"
                onclick="loadMoreResults()">Load More</button></div>

        <div id="seasonSection" class="hidden" style="margin-top:20px;">
            <h3 style="border-bottom: 1px solid var(--border-subtle); padding-bottom: 15px; font-size:16px;">Select
                Season <button class="btn btn-danger" style="float:right; padding:4px 10px; font-size:11px;"
                    onclick="resetToSearch()">Back</button></h3>
            <div id="seasonsGrid" class="grid-container"></div>
        </div>

        <div id="configSection" class="config-panel hidden">
            <h3
                style="margin-top:0; border-bottom:1px solid var(--border-subtle); padding-bottom:15px; color:var(--text-main); font-size:18px;">
                <i class="fas fa-sliders-h"></i> Configuration</h3>

            <input type="hidden" id="storeTmdbId"><input type="hidden" id="storeMediaType"><input type="hidden"
                id="storePoster">
            <input type="hidden" id="storeTitle"><input type="hidden" id="storePlot"><input type="hidden"
                id="storeReleaseDate">
            <input type="hidden" id="storeRating"><input type="hidden" id="storeGenres">
            <input type="hidden" id="storeEpisodeCount"> <input type="hidden" id="storeTags"><input type="hidden"
                id="storeTagline">
            <input type="hidden" id="storeTrailerId"><input type="hidden" id="storeDirector"><input type="hidden"
                id="storeCast">
            <input type="hidden" id="storeAgeRating"><input type="hidden" id="storeRuntime">

            <div class="accordion-item">
                <div class="accordion-header active" onclick="toggleAccordion(this)">
                    <span><i class="fas fa-info-circle" style="margin-right:8px;"></i> Basic Information</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-content show">
                    <div class="config-row">
                        <div class="form-group">
                            <label style="color: #f87171;">Header Image URL (Manual):</label>
                            <input type="text" id="manualBackdropUrl" class="form-control"
                                placeholder="Leave empty for NO image">
                        </div>
                        <div class="form-group">
                            <label style="color: #60a5fa;">Stream / Dubbed By (Check Multi):</label>
                            <div class="lang-grid dub-checks" style="max-height:150px; overflow-y:auto;">
                                <div class="lang-item"><label><input type="checkbox" value="Official" checked>
                                        Official</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Crunchyroll">
                                        Crunchyroll</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Netflix"> Netflix</label>
                                </div>
                                <div class="lang-item"><label><input type="checkbox" value="Disney"> Disney</label>
                                </div>
                                <div class="lang-item"><label><input type="checkbox" value="Sony Yay"> Sony Yay</label>
                                </div>
                                <div class="lang-item"><label><input type="checkbox" value="Muse India"> Muse
                                        India</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Anime Times"> Anime
                                        Times</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Nickelodeon">
                                        Nickelodeon</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Amazon Prime"> Amazon
                                        Prime</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Disney+"> Disney+</label>
                                </div>
                                <div class="lang-item"><label><input type="checkbox" value="Hulu"> Hulu</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="HBO"> HBO</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Sony LIV"> Sony LIV</label>
                                </div>
                                <div class="lang-item"><label><input type="checkbox" value="Jio Cinema"> Jio
                                        Cinema</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Zee5"> Zee5</label></div>
                                <div class="lang-item"><label><input type="checkbox" value="Fan Dub"> Fan Dub</label>
                                </div>
                                <div class="lang-item"><label><input type="checkbox" value="BluRay"> BluRay</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="config-row">
                        <div class="form-group" id="manualSeasonGroup">
                            <label>Season (Display):</label>
                            <input type="number" id="displaySeason" class="form-control" value="1">
                        </div>
                        <div class="form-group" id="manualEpisodesGroup">
                            <label>Episodes (Display):</label>
                            <input type="text" id="displayEpisodes" class="form-control" placeholder="Total Eps">
                        </div>
                    </div>
                    <div class="config-row">
                        <div class="form-group" id="statusGroup">
                            <label>Status:</label>
                            <select id="animeStatus" class="form-control">
                                <option value="Completed" selected>Completed</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Stopped">Stopped</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Source Type:</label>
                            <select id="sourceType" class="form-control">
                                <option value="WEB-DL" selected>WEB-DL</option>
                                <option value="BluRay">BluRay</option>
                                <option value="HDRip">HDRip</option>
                                <option value="TV-DL">TV-DL</option>
                                <option value="DVD-Rip">DVD-Rip</option>
                                <option value="WEB-TVRip">WEB-TVRip</option>
                                <option value="VODRip">VODRip</option>
                                <option value="HDTC">HDTC</option>
                                <option value="DVB">DVB</option>
                                <option value="CAM">CAM</option>
                                <option value="TS">TS</option>
                                <option value="R5">R5</option>
                                <option value="PPV">PPV</option>
                                <option value="SDTV">SDTV</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Video Format:</label>
                            <select id="videoFormat" class="form-control">
                                <option value="Mkv" selected>Mkv</option>
                                <option value="Mp4">Mp4</option>
                                <option value="Avi">Avi</option>
                                <option value="Zip">Zip</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span><i class="fas fa-images" style="margin-right:8px;"></i> Manage Screenshots</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-content">
                    <p style="font-size:12px; color:#94a3b8; margin-top:0;">Click images to <b>Select/Deselect</b>. Only
                        Green highlighted images will be in post.</p>
                    <div id="screenshotsGrid" class="ss-grid"></div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span><i class="fas fa-language" style="margin-right:8px;"></i> Languages & Quality</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-content">
                    <div class="form-group" style="margin-bottom: 20px;">
                        <label style="color:#fbbf24;">Qualities:</label>
                        <div class="lang-grid quality-checks">
                            <div class="lang-item"><label><input type="checkbox" value="360p"> 360p</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="480p" checked> 480p</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="576p"> 576p</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="720p" checked> 720p</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="1080p" checked> 1080p</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="2160p"> 2160p</label></div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 20px;">
                        <label>Audio Languages:</label>
                        <div class="lang-grid audio-checks">
                            <div class="lang-item"><label><input type="checkbox" value="Hindi" checked> Hindi</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="English"> English</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Japanese"> Japanese</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="Tamil" checked> Tamil</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="Telugu" checked> Telugu</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="Malayalam"> Malayalam</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="Kannada"> Kannada</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Bengali"> Bengali</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Urdu"> Urdu</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Marathi"> Marathi</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Chinese"> Chinese</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Gujrati"> Gujrati</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Punjabi"> Punjabi</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Spanish"> Spanish</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="French"> French</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Korean"> Korean</label></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label style="color:#2dd4bf;">Subtitle Languages:</label>
                        <div class="lang-grid sub-checks">
                            <div class="lang-item"><label><input type="checkbox" value="English" checked>
                                    English</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Hindi"> Hindi</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Japanese"> Japanese</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="Tamil"> Tamil</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Telugu"> Telugu</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Malayalam"> Malayalam</label>
                            </div>
                            <div class="lang-item"><label><input type="checkbox" value="Kannada"> Kannada</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Bengali"> Bengali</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Urdu"> Urdu</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Marathi"> Marathi</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Chinese"> Chinese</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Gujrati"> Gujrati</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Punjabi"> Punjabi</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Spanish"> Spanish</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="French"> French</label></div>
                            <div class="lang-item"><label><input type="checkbox" value="Korean"> Korean</label></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <div class="accordion-header" onclick="toggleAccordion(this)">
                    <span><i class="fas fa-link" style="margin-right:8px;"></i> Download Links</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="accordion-content">
                    <div id="movieLinksGroup" class="hidden">
                        <div class="form-group" style="width:100%;">
                            <label>Movie Links Code:</label>
                            <textarea id="manualMovieLinks" class="links-textarea"
                                placeholder="Paste movie [mks_accordion] code here..."></textarea>
                        </div>
                    </div>

                    <div id="tvLinksGroup" class="hidden">
                        <div class="config-row">
                            <div class="form-group">
                                <label>Episode Links Code:</label>
                                <textarea id="manualEpisodeLinks" class="links-textarea"
                                    placeholder="Paste episodes [mks_accordion] code here..."></textarea>
                            </div>
                            <div class="form-group">
                                <label>Zip/Pack Links Code:</label>
                                <textarea id="manualPackLinks" class="links-textarea"
                                    placeholder="Paste season pack [mks_accordion] code here..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div style="text-align: right; margin-top:20px;">
                <button class="btn btn-danger" onclick="window.location.href=window.location.href">Reset</button>
                <button class="btn btn-success" onclick="generateCode()">Generate Code <i
                        class="fas fa-magic"></i></button>
            </div>

            <div id="outputContainer" class="output-area hidden">

                <label style="color:#fbbf24; display:block; margin-bottom:10px;"><i class="fas fa-file-signature"></i>
                    Smart Release Title:</label>
                <div class="seo-box-container">
                    <div class="seo-input-wrapper">
                        <input type="text" id="seoFilename" class="form-control" readonly onclick="this.select()">
                        <button id="copySeoBtn" class="btn btn-primary" style="padding: 10px 20px;"
                            onclick="copyToClipboard('seoFilename', 'copySeoBtn')">Copy Title</button>
                    </div>
                </div>

                <label style="color:#e2e8f0; display:block; margin-bottom:10px;">Post HTML Code:</label>
                <textarea id="finalCode" readonly onclick="this.select()"></textarea>
                <button id="copyBtn" class="btn btn-primary" onclick="copyToClipboard('finalCode', 'copyBtn')"
                    style="width:100%">Copy HTML</button>

                <br><br>
                <label style="color:#e2e8f0; display:block; margin-bottom:10px;">Tags (Keywords):</label>
                <textarea id="tagsOutput" class="tags-box" readonly onclick="this.select()"></textarea>
                <button id="copyTagsBtn" class="btn btn-primary" onclick="copyToClipboard('tagsOutput', 'copyTagsBtn')"
                    style="width:100%">Copy Tags</button>

                <h3 style="margin-top:30px; border-bottom:1px solid #334155; padding-bottom:10px; color:#fff;">Visual
                    Preview:</h3>
                <div id="livePreview"
                    style="background:#fff; border:1px dashed #ccc; padding:20px; margin-top:10px; color:#000;"></div>
            </div>
        </div>
    </div>

    <script>
        const apiKey = '<?php echo $apiKey; ?>';
        let currentPage = 1;

        function toggleAccordion(header) {
            header.classList.toggle('active');
            var content = header.nextElementSibling;
            if (content.style.display === "block") { content.style.display = "none"; }
            else { content.style.display = "block"; }
        }
        document.addEventListener("DOMContentLoaded", () => {
            const firstContent = document.querySelector('.accordion-content.show');
            if (firstContent) firstContent.style.display = 'block';
        });

        document.getElementById('searchInput').addEventListener('keyup', (e) => {
            if (e.key === 'Enter') performSearch();
        });
        document.getElementById('typeSelect').addEventListener('change', () => {
            if (document.getElementById('searchInput').value.length > 0) performSearch();
        });

        async function performSearch(isLoadMore = false) {
            const query = document.getElementById('searchInput').value;
            const type = document.getElementById('typeSelect').value;
            if (!query) return alert("Please enter a name!");
            if (!isLoadMore) {
                currentPage = 1;
                document.getElementById('resultsGrid').innerHTML = '';
                document.getElementById('loadMoreSection').classList.add('hidden');
                resetUI();
            }
            toggleLoader(true);
            let url = `https://api.themoviedb.org/3/search/${type}?api_key=${apiKey}&query=${encodeURIComponent(query)}&include_adult=false&page=${currentPage}`;
            try {
                const res = await fetch(url);
                const data = await res.json();
                renderResults(data.results);
                if (data.page < data.total_pages) document.getElementById('loadMoreSection').classList.remove('hidden');
                else document.getElementById('loadMoreSection').classList.add('hidden');
            } catch (e) { alert("API Error"); console.error(e); }
            toggleLoader(false);
        }

        function loadMoreResults() { currentPage++; performSearch(true); }

        function renderResults(results) {
            const grid = document.getElementById('resultsGrid');
            if (results.length === 0 && currentPage === 1) { grid.innerHTML = '<p style="color:#fff">No results found.</p>'; return; }
            results.forEach(item => {
                if (item.media_type === 'person') return;
                const isTv = item.media_type === 'tv' || !item.title;
                const title = item.title || item.name;
                const date = item.release_date || item.first_air_date || 'N/A';
                const img = item.poster_path ? `https://image.tmdb.org/t/p/w342${item.poster_path}` : 'https://via.placeholder.com/200x300?text=No+Image';
                const id = item.id;
                const type = isTv ? 'tv' : 'movie';
                const card = document.createElement('div');
                card.className = 'card';
                card.innerHTML = `<div class="badge">${type}</div><img src="${img}"><div class="card-body"><div class="card-title">${title}</div><div class="card-date">${date}</div></div>`;
                card.onclick = () => { if (type === 'tv') fetchSeasons(id, title); else prepareConfig(id, 'movie', 1); };
                grid.appendChild(card);
            });
        }

        async function fetchSeasons(tvId, tvName) {
            toggleLoader(true);
            document.getElementById('resultsGrid').classList.add('hidden');
            document.getElementById('loadMoreSection').classList.add('hidden');
            try {
                const res = await fetch(`https://api.themoviedb.org/3/tv/${tvId}?api_key=${apiKey}`);
                const data = await res.json();
                const seasonGrid = document.getElementById('seasonsGrid');
                seasonGrid.innerHTML = '';
                document.getElementById('seasonSection').classList.remove('hidden');
                data.seasons.forEach(season => {
                    const img = season.poster_path ? `https://image.tmdb.org/t/p/w342${season.poster_path}` : 'https://via.placeholder.com/200x300?text=No+Image';
                    const card = document.createElement('div');
                    card.className = 'card';
                    card.innerHTML = `<div class="badge">S-${season.season_number}</div><img src="${img}"><div class="card-body"><div class="card-title">${season.name}</div><div class="card-date">${season.air_date || 'N/A'} | ${season.episode_count} Eps</div></div>`;
                    card.onclick = () => prepareConfig(tvId, 'tv', season.season_number);
                    seasonGrid.appendChild(card);
                });
            } catch (e) { console.error(e); }
            toggleLoader(false);
        }

        async function prepareConfig(id, type, seasonNum) {
            toggleLoader(true);
            document.getElementById('resultsGrid').classList.add('hidden');
            document.getElementById('seasonSection').classList.add('hidden');
            document.getElementById('loadMoreSection').classList.add('hidden');

            document.getElementById('manualBackdropUrl').value = '';
            document.getElementById('manualMovieLinks').value = '';
            document.getElementById('manualEpisodeLinks').value = '';
            document.getElementById('manualPackLinks').value = '';
            document.getElementById('screenshotsGrid').innerHTML = ''; // CLEAR OLD SCREENSHOTS

            if (type === 'movie') {
                document.getElementById('statusGroup').classList.add('hidden');
                document.getElementById('movieLinksGroup').classList.remove('hidden');
                document.getElementById('tvLinksGroup').classList.add('hidden');
            } else {
                document.getElementById('statusGroup').classList.remove('hidden');
                document.getElementById('movieLinksGroup').classList.add('hidden');
                document.getElementById('tvLinksGroup').classList.remove('hidden');
                document.getElementById('animeStatus').value = 'Completed';
            }

            try {
                const mainRes = await fetch(`https://api.themoviedb.org/3/${type}/${id}?api_key=${apiKey}`);
                const data = await mainRes.json();

                let contentTitle = data.title || data.name;
                let finalPlot = data.overview || "Storyline not available.";

                document.getElementById('storePoster').value = data.poster_path ? `https://image.tmdb.org/t/p/w500${data.poster_path}` : '';
                document.getElementById('storeTagline').value = data.tagline || "";
                document.getElementById('storeRating').value = data.vote_average ? data.vote_average.toFixed(2) : '8.50';

                try {
                    const kwRes = await fetch(`https://api.themoviedb.org/3/${type}/${id}/keywords?api_key=${apiKey}`);
                    const kwData = await kwRes.json();
                    let rawKeywords = kwData.keywords || kwData.results || [];
                    let tagList = rawKeywords.map(k => k.name.toLowerCase().split(' ').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ')).join(', ');
                    if (type === 'tv') tagList += `, Season ${seasonNum}`;
                    document.getElementById('storeTags').value = tagList;
                } catch (e) { }

                try {
                    const credRes = await fetch(`https://api.themoviedb.org/3/${type}/${id}/credits?api_key=${apiKey}`);
                    const credData = await credRes.json();
                    let castArr = credData.cast ? credData.cast.slice(0, 6).map(c => c.name) : [];
                    document.getElementById('storeCast').value = castArr.join(', ');
                    let directorStr = "";
                    if (type === 'movie') {
                        let directors = credData.crew ? credData.crew.filter(c => c.job === 'Director').map(d => d.name) : [];
                        directorStr = directors.join(', ');
                    } else {
                        if (data.created_by && data.created_by.length > 0) directorStr = data.created_by.map(c => c.name).join(', ');
                    }
                    document.getElementById('storeDirector').value = directorStr;
                } catch (e) { }

                let ageRating = 'N/A';
                try {
                    let ratingUrl = type === 'movie' ? `https://api.themoviedb.org/3/movie/${id}/release_dates?api_key=${apiKey}` : `https://api.themoviedb.org/3/tv/${id}/content_ratings?api_key=${apiKey}`;
                    const rateRes = await fetch(ratingUrl);
                    const rateData = await rateRes.json();
                    if (type === 'movie') {
                        let usRelease = rateData.results.find(r => r.iso_3166_1 === 'US');
                        if (usRelease && usRelease.release_dates.length > 0) ageRating = usRelease.release_dates[0].certification;
                    } else {
                        let usRating = rateData.results.find(r => r.iso_3166_1 === 'US');
                        if (usRating) ageRating = usRating.rating;
                    }
                } catch (e) { }
                if (ageRating === '') ageRating = 'N/A';
                document.getElementById('storeAgeRating').value = ageRating;

                try {
                    let trailerKey = '';
                    let vidUrl = type === 'movie' ? `https://api.themoviedb.org/3/movie/${id}/videos?api_key=${apiKey}` : `https://api.themoviedb.org/3/tv/${id}/season/${seasonNum}/videos?api_key=${apiKey}`;
                    const vidRes = await fetch(vidUrl);
                    const vidData = await vidRes.json();
                    const trailer = vidData.results.find(v => v.type === 'Trailer' && v.site === 'YouTube');
                    if (trailer) trailerKey = trailer.key;
                    document.getElementById('storeTrailerId').value = trailerKey;
                } catch (e) { }

                let imageList = [];
                let episodeCount = 'N/A';
                let specificReleaseDate = data.release_date || data.first_air_date || 'N/A';
                let runtimeText = "N/A";

                if (type === 'tv') {
                    if (data.episode_run_time && data.episode_run_time.length > 0) runtimeText = data.episode_run_time[0] + " min per episode";
                    try {
                        const seasonRes = await fetch(`https://api.themoviedb.org/3/tv/${id}/season/${seasonNum}?api_key=${apiKey}`);
                        const seasonData = await seasonRes.json();
                        if (seasonData.overview && seasonData.overview.trim() !== "") finalPlot = seasonData.overview;
                        if (seasonData.poster_path) document.getElementById('storePoster').value = `https://image.tmdb.org/t/p/w500${seasonData.poster_path}`;
                        if (seasonData.air_date) specificReleaseDate = seasonData.air_date;
                        if (seasonData.episodes) episodeCount = seasonData.episodes.length;
                        if (seasonData.episodes) seasonData.episodes.forEach(ep => { if (ep.still_path) imageList.push({ file_path: ep.still_path }); });
                    } catch (err) { }
                } else {
                    if (data.runtime) runtimeText = `${Math.floor(data.runtime / 60)}h ${data.runtime % 60}m`;
                    try {
                        const imgRes = await fetch(`https://api.themoviedb.org/3/movie/${id}/images?api_key=${apiKey}`);
                        const imgData = await imgRes.json();
                        if (imgData.backdrops) imageList = imgData.backdrops;
                    } catch (err) { }
                }

                document.getElementById('storePlot').value = finalPlot;
                document.getElementById('storeEpisodeCount').value = episodeCount;
                document.getElementById('displayEpisodes').value = episodeCount;
                document.getElementById('storeRuntime').value = runtimeText;
                document.getElementById('storeReleaseDate').value = specificReleaseDate;

                // POPULATE SCREENSHOTS GRID (SELECTION MODE)
                const ssGrid = document.getElementById('screenshotsGrid');
                imageList.forEach((img, index) => {
                    let url = `https://image.tmdb.org/t/p/w780${img.file_path}`;
                    let div = document.createElement('div');
                    let isSelected = index < 4 ? 'selected' : '';
                    div.className = `ss-item ${isSelected}`;
                    div.onclick = function () { this.classList.toggle('selected'); };
                    div.innerHTML = `<img src="${url}"><span class="ss-check"><i class="fas fa-check"></i></span>`;
                    ssGrid.appendChild(div);
                });

                document.getElementById('storeTmdbId').value = id;
                document.getElementById('storeMediaType').value = type;
                document.getElementById('storeTitle').value = contentTitle;
                document.getElementById('storeGenres').value = data.genres ? data.genres.map(g => g.name).join(', ') : 'Animation';

                if (type === 'tv') {
                    document.getElementById('manualSeasonGroup').classList.remove('hidden');
                    document.getElementById('manualEpisodesGroup').classList.remove('hidden');
                    document.getElementById('displaySeason').value = seasonNum;
                } else {
                    document.getElementById('manualSeasonGroup').classList.add('hidden');
                    document.getElementById('manualEpisodesGroup').classList.add('hidden');
                }

                document.getElementById('configSection').classList.remove('hidden');
                window.scrollTo(0, document.body.scrollHeight);

            } catch (e) { console.error(e); alert("Failed to fetch details."); }
            toggleLoader(false);
        }

        function generateCode() {
            try {
                // REAL SOLID COLORS LOGIC (EXPANDED LIST)
                const solidColors = [
                    '#000000', // Real Black
                    '#8B4513', // Saddle Brown
                    '#A52A2A', // Brown
                    '#006400', // Dark Green
                    '#8B0000', // Dark Red
                    '#00008B', // Dark Blue
                    '#4B0082', // Indigo
                    '#2F4F4F', // Dark Slate Gray
                    '#FF4500', // Orange Red
                    '#D2691E', // Chocolate
                    '#800000', // Maroon
                    '#DC143C', // Crimson
                    '#191970', // Midnight Blue
                    '#556B2F', // Dark Olive Green
                    '#800080', // Purple
                    '#008080', // Teal
                    '#A0522D'  // Sienna
                ];

                const pickedColor = solidColors[Math.floor(Math.random() * solidColors.length)];

                // Hex to RGB for box styling
                const hexToRgb = hex => hex.replace(/^#?([a-f\d])([a-f\d])([a-f\d])$/i, (m, r, g, b) => '#' + r + r + g + g + b + b)
                    .substring(1).match(/.{2}/g).map(x => parseInt(x, 16)).join(', ');
                const rgbVal = hexToRgb(pickedColor);

                const niceHr = `<hr style="border: 0; height: 1px; background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(${rgbVal}, 0.8), rgba(0, 0, 0, 0)); margin: 25px 0;">`;

                const title = document.getElementById('storeTitle').value;
                const type = document.getElementById('storeMediaType').value;
                const plot = document.getElementById('storePlot').value;
                const releaseDate = document.getElementById('storeReleaseDate').value;
                const year = releaseDate && releaseDate !== 'N/A' ? releaseDate.split('-')[0] : '';

                const rating = document.getElementById('storeRating').value;
                const poster = document.getElementById('storePoster').value;
                const epCount = document.getElementById('displayEpisodes').value;
                const trailerId = document.getElementById('storeTrailerId').value;
                const tagline = document.getElementById('storeTagline').value;
                const director = document.getElementById('storeDirector').value;
                const cast = document.getElementById('storeCast').value;
                const ageRating = document.getElementById('storeAgeRating').value;
                const runtime = document.getElementById('storeRuntime').value;
                const status = document.getElementById('animeStatus').value;
                const manualBackdrop = document.getElementById('manualBackdropUrl').value.trim();
                const genres = document.getElementById('storeGenres').value;
                const tags = document.getElementById('storeTags').value;

                let displaySeason = parseInt(document.getElementById('displaySeason').value);
                const videoFormat = document.getElementById('videoFormat').value;
                const sourceType = document.getElementById('sourceType').value;

                const dubChecks = document.querySelectorAll('.dub-checks input[type="checkbox"]:checked');
                let selectedDubs = []; dubChecks.forEach((c) => { selectedDubs.push(c.value); });
                let dubbedString = "";

                if (selectedDubs.includes("Official") && selectedDubs.length === 1) {
                    dubbedString = "Official Dubbed";
                } else {
                    if (selectedDubs.length > 0) {
                        if (selectedDubs.length > 1) {
                            let last = selectedDubs.pop();
                            dubbedString = "Dubbed by " + selectedDubs.join(", ") + " & " + last;
                            selectedDubs.push(last);
                        } else {
                            dubbedString = "Dubbed by " + selectedDubs[0];
                        }
                    }
                }

                const audioChecks = document.querySelectorAll('.audio-checks input[type="checkbox"]:checked');
                let selectedLangs = []; audioChecks.forEach((c) => { selectedLangs.push(c.value); });
                const langString = selectedLangs.length > 0 ? selectedLangs.join('-') : 'English';
                let audioType = selectedLangs.length >= 3 ? "Multi Audio" : (selectedLangs.length === 2 ? "Dual Audio" : "Single Audio");
                let finalLanguageFormat = selectedLangs.length > 0 ? `${audioType} (${langString})` : audioType;

                const qualityChecks = document.querySelectorAll('.quality-checks input[type="checkbox"]:checked');
                let selectedQualities = []; qualityChecks.forEach((c) => { selectedQualities.push(c.value); });
                let qualityString = '480p, 720p & 1080p';
                if (selectedQualities.length > 1) {
                    const lastQ = selectedQualities.pop();
                    qualityString = selectedQualities.join(', ') + " & " + lastQ;
                    selectedQualities.push(lastQ);
                } else if (selectedQualities.length === 1) qualityString = selectedQualities[0];

                const subChecks = document.querySelectorAll('.sub-checks input[type="checkbox"]:checked');
                let selectedSubs = []; subChecks.forEach((c) => { selectedSubs.push(c.value); });
                const subString = selectedSubs.length > 0 ? "Yes (" + selectedSubs.join(', ') + ")" : "No";

                let formattedSeason = displaySeason < 10 ? '0' + displaySeason : displaySeason;

                // INTRO LINE CONSTRUCTION WITH COLORS
                let introLine = `✅ Download <b style="color: ${pickedColor};">${title}</b>`;
                if (year) introLine += ` (<b style="color: ${pickedColor};">${year}</b>)`;

                let contentLabel = type === 'tv' ? "Series" : "Movie";
                let extraInfo = "";

                // SMART TITLE CONSTRUCTION
                let smartTitle = title;
                if (year) smartTitle += ` (${year})`;
                if (type === 'tv') smartTitle += ` Season ${formattedSeason}`;
                if (dubbedString !== "") smartTitle += ` ${dubbedString}`;

                if (selectedLangs.length > 0) {
                    let shortLangs = selectedLangs.map(l => {
                        if (l === 'English') return 'Eng'; if (l === 'Japanese') return 'Jap'; if (l === 'Hindi') return 'Hindi';
                        if (l === 'Tamil') return 'Tamil'; if (l === 'Telugu') return 'Telugu'; return l.substring(0, 3);
                    });
                    smartTitle += ` ${audioType} [${shortLangs.join('-')}]`;
                }
                smartTitle += ` ${qualityString} ${sourceType}`;

                if (selectedSubs.length > 0) {
                    if (selectedSubs.length === 1 && selectedSubs[0] === 'English') smartTitle += ` | ESub`;
                    else {
                        let shortSubs = selectedSubs.map(s => { if (s === 'English') return 'Eng'; if (s === 'Hindi') return 'Hin'; return s.substring(0, 3); });
                        smartTitle += ` | [${shortSubs.join('-')} Sub]`;
                    }
                }

                if (type === 'tv') {
                    introLine += ` Season <b style="color: ${pickedColor};">${formattedSeason}</b>`;
                    if (dubbedString !== "") introLine += ` <b style="color: ${pickedColor};">${dubbedString}</b>`;
                    introLine += ` <b style="color: ${pickedColor};">${audioType} [${langString}]</b>`;
                    if (status === 'Completed') introLine += ` <b style="color: ${pickedColor};">Complete All Episodes</b>`;
                    introLine += ` <b style="color: ${pickedColor};">${sourceType} HD in ${qualityString}</b>.`;
                    if (epCount && epCount !== 'N/A') extraInfo = `This Season has <b style="color: ${pickedColor};">${epCount} Episodes</b>.`;
                } else {
                    if (dubbedString !== "") introLine += ` <b style="color: ${pickedColor};">${dubbedString}</b>`;
                    introLine += ` <b style="color: ${pickedColor};">${audioType} [${langString}]</b>`;
                    introLine += ` in <b style="color: ${pickedColor};">${qualityString}</b>.`;
                }

                introLine += `It was released on <b style="color: ${pickedColor};">${releaseDate}</b>. It is based on <b style="color: ${pickedColor};">${genres}</b>. ${extraInfo}`;

                let specificLang = null;
                if (selectedLangs.includes("Hindi")) specificLang = "Hindi";
                else if (selectedLangs.includes("English")) specificLang = "English";
                if (specificLang) introLine += ` This ${contentLabel} is now available in <b style="color: ${pickedColor};">${specificLang} Dubbed</b> at Anime Infinity.`;

                let sloganBlockHtml = (tagline && tagline.trim() !== "") ? `<p></p><center><span class="slogan-text">"${tagline}"</span></center><p></p>` : "";
                let seriesInfoExtra = type === 'tv' ? `<li><i class="fas fa-layer-group" style="color:#ff9f43;"></i> Season: <strong>${formattedSeason}</strong></li><li><i class="fas fa-list-ol" style="color:#ff9f43;"></i> Episodes: <strong>${epCount}</strong></li>` : "";
                let directorInfo = (director && director !== "") ? `<li><i class="fas fa-user-tie" style="color:#74b9ff;"></i> Director: <strong>${director}</strong></li>` : "";
                let castInfo = (cast && cast !== "") ? `<li><i class="fas fa-users" style="color:#a29bfe;"></i> Stars: <strong>${cast}</strong></li>` : "";
                let backdropHtml = (manualBackdrop && manualBackdrop !== "") ? `<div style="text-align:center; width: 100%; margin-bottom: 20px;"><img src="${manualBackdrop}" alt="${title} HD Wallpaper" style="width:100%; border-radius:10px; box-shadow:0 4px 8px rgba(0,0,0,0.3);"></div>` : "";

                let trailerHtml = '';
                if (trailerId && trailerId !== '') trailerHtml = `<h2 style="text-align:center;">Watch Trailer</h2>${niceHr}<div style="width: 100%; margin: 20px auto;"><div style="position: relative; padding-bottom: 56.25%; height: 0; overflow: hidden; border-radius: 10px; border: 2px solid #333; box-shadow: 0 10px 40px rgba(0,0,0,0.6);"><iframe style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; border: 0;" src="https://www.youtube.com/embed/${trailerId}" allowfullscreen></iframe></div></div>${niceHr}`;

                let downloadSectionHtml = "";
                if (type === 'movie') {
                    const mCode = document.getElementById('manualMovieLinks').value.trim();
                    downloadSectionHtml = (mCode && mCode !== "") ? mCode : '<p style="text-align:center; font-family: \'Montserrat\', sans-serif; font-weight:700; color: #ff4757;">PLEASE WAIT, LINKS GENERATING...</p>';
                } else {
                    const eCode = document.getElementById('manualEpisodeLinks').value.trim();
                    const pCode = document.getElementById('manualPackLinks').value.trim();
                    if (eCode === "" && pCode === "") downloadSectionHtml = '<p style="text-align:center; font-family: \'Montserrat\', sans-serif; font-weight:700; color: #ff4757;">PLEASE WAIT, LINKS GENERATING...</p>';
                    else {
                        if (eCode !== "") downloadSectionHtml += eCode;
                        if (pCode !== "") {
                            if (eCode !== "") downloadSectionHtml += niceHr;
                            downloadSectionHtml += '<h3 style="text-align:center;">Full Season Pack</h3>' + niceHr + pCode;
                        }
                    }
                }

                // BUILD SCREENSHOTS CODE FROM SELECTED IMAGES ONLY
                let screenshotsHtml = '';
                const selectedImages = document.querySelectorAll('#screenshotsGrid .ss-item.selected img');
                if (selectedImages.length > 0) {
                    screenshotsHtml += '<p style="text-align: center;">[mks_accordion]\n[mks_accordion_item title="⇒ SCREENSHOTS HERE"]</p>\n<p style="text-align: center;">';
                    selectedImages.forEach(img => {
                        screenshotsHtml += `<img src="${img.src}" alt="${title}" title="${title}" style="width:100%; margin-bottom:15px; border-radius:10px; box-shadow:0 5px 15px rgba(0,0,0,0.3);"><br>\n`;
                    });
                    screenshotsHtml += '[/mks_accordion_item]\n[/mks_accordion]</p>';
                }

                let footerMsg = type === 'movie' ? "Enjoy This Movie." : (status === 'Completed' ? "This Season is Completed." : "More Episodes Coming Soon...");
                const footerHtml = `<div style="font-family: 'Montserrat', sans-serif; font-size: 24px; font-weight: 800; text-align: center; color: #2ecc71; text-transform: uppercase; letter-spacing: 2px; text-shadow: 1px 1px 2px rgba(0,0,0,0.1);">${footerMsg}</div>${niceHr}`;
                const styleBlock = `<style>@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@800&display=swap'); @keyframes rainbowText { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} } .slogan-text { background: linear-gradient(to right, #ff6be7, #54dcff, #9d72ff, #ff6be7); -webkit-background-clip: text; background-clip: text; color: transparent; background-size: 200% auto; animation: rainbowText 3s linear infinite; font-family: 'Montserrat', sans-serif; font-size: 20px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; text-shadow: 1px 1px 3px rgba(0,0,0,0.15); }</style>`;

                const html = `${styleBlock}\n${backdropHtml}\n<p>${introLine}</p>\n${niceHr}\n<h3>Series Info:</h3>\n<ul style="list-style: none; padding: 0; line-height: 1.8;">\n<li><i class="fas fa-check-circle" style="color:#00d2d3;"></i> Full Name: <strong>${title}</strong></li>\n<li><i class="fas fa-user-shield" style="color:#ff6b6b;"></i> Age Rating: <strong>${ageRating}</strong></li>\n<li><i class="fas fa-star" style="color:#feca57;"></i> Rating: <strong>${rating}</strong></li>\n${seriesInfoExtra}\n<li><i class="fas fa-calendar-alt" style="color:#54a0ff;"></i> Release Date: <strong>${releaseDate}</strong></li>\n<li><i class="fas fa-clock" style="color:#1dd1a1;"></i> Runtime: <strong>${runtime}</strong></li>\n<li><i class="fas fa-dragon" style="color:#5f27cd;"></i> Genre: <strong>${genres}</strong></li>\n${directorInfo}\n${castInfo}\n<li><i class="fas fa-volume-up" style="color:#ff9ff3;"></i> Language: <strong>${finalLanguageFormat}</strong></li>\n<li><i class="fas fa-closed-captioning" style="color:#c8d6e5;"></i> Subtitles: <strong>${subString}</strong></li>\n<li><i class="fas fa-compact-disc" style="color:#ff6b81;"></i> Source: <strong>${sourceType}</strong></li>\n<li><i class="fas fa-video" style="color:#48dbfb;"></i> Format: <strong>${videoFormat}</strong></li>\n<li><i class="fas fa-satellite-dish" style="color:#2ecc71;"></i> Status: <strong>${status}</strong></li>\n<li><i class="fas fa-highlighter" style="color:#f39c12;"></i> Quality: <strong>${qualityString}</strong></li>\n</ul>\n<h2>Storyline:</h2>\n${niceHr}\n<div style="background-color: rgba(${rgbVal}, 0.05); border-left: 6px solid ${pickedColor}; padding: 20px; margin: 20px 0; font-weight: 500; line-height: 1.6; color: #333; border-radius: 0 8px 8px 0; box-shadow: 0 5px 15px rgba(${rgbVal}, 0.2);">\n${plot}\n</div>\n${niceHr}\n<p><strong style="color: ${pickedColor};">Anime Infinity </strong><em>is The Best Website/Platform For Japanese And Hollywood HD Animes. We Provide Direct Google Drive Download Links For Fast And Secure Downloading. Just Click On the Download Button And Follow the Steps To Download And Watch Movies Online For Free.</em></p>\n${niceHr}\n<h3 style="text-align:center; color: ${pickedColor};">Download ${title} (${year}) - Anime Infinity</h3>\n${niceHr}\n<div style="width: 300px; margin: 20px auto; padding: 8px; background: #fff; border-radius: 4px; box-shadow: 0 15px 30px rgba(0,0,0,0.5), 0 5px 15px rgba(0,0,0,0.3);">\n<img src="${poster}" alt="${title} poster" style="width:100%; display:block; border-radius:2px;">\n</div>\n${sloganBlockHtml}\n${niceHr}\n${screenshotsHtml}\n${niceHr}\n<h2 style="text-align:center;">Download Links</h2>\n${niceHr}\n${downloadSectionHtml}\n${niceHr}\n${trailerHtml}\n${footerHtml}`;

                document.getElementById('outputContainer').classList.remove('hidden');
                document.getElementById('finalCode').value = html;
                document.getElementById('tagsOutput').value = tags + ", " + title + ", Anime Infinity";
                document.getElementById('seoFilename').value = smartTitle;
                document.getElementById('livePreview').innerHTML = html;

                resetButton('copyBtn', 'Copy HTML');
                resetButton('copyTagsBtn', 'Copy Tags');
                resetButton('copySeoBtn', 'Copy Title');
            } catch (e) {
                alert("Error Generating Code: " + e.message);
                console.error(e);
            }
        }

        function copyToClipboard(elementId, btnId) {
            const textarea = document.getElementById(elementId);
            textarea.select();
            document.execCommand('copy');
            const btn = document.getElementById(btnId);
            btn.innerText = "Copied !"; btn.className = "btn btn-success";
        }
        function resetButton(btnId, text) { const btn = document.getElementById(btnId); btn.innerText = text; btn.className = "btn btn-primary"; }
        function toggleLoader(show) { document.getElementById('loader').style.display = show ? 'block' : 'none'; }
        function resetUI() { document.getElementById('seasonSection').classList.add('hidden'); document.getElementById('configSection').classList.add('hidden'); document.getElementById('outputContainer').classList.add('hidden'); }
        function resetToSearch() { document.getElementById('seasonSection').classList.add('hidden'); document.getElementById('resultsGrid').classList.remove('hidden'); document.getElementById('loadMoreSection').classList.remove('hidden'); }
    </script>

</body>

</html>