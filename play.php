<?php
// play.php
if (!isset($_GET['stream'])) {
    die("<h3 style='color:white;text-align:center;margin-top:20%;'>No Stream Source</h3>");
}

$streamUrl = base64_decode($_GET['stream']);
$title = isset($_GET['title']) ? $_GET['title'] : 'Live TV';
$logo = isset($_GET['logo']) ? $_GET['logo'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js"></script>
    <style>
        body { margin: 0; background: #000; overflow: hidden; color: white; font-family: sans-serif; }
        #player { width: 100%; height: 100vh; }
        .back-btn {
            position: absolute; top: 15px; left: 15px; z-index: 100;
            background: rgba(255, 0, 0, 0.7); color: white; padding: 8px 15px;
            text-decoration: none; border-radius: 4px; font-weight: bold;
        }
        .warning {
            position: absolute; bottom: 10px; left: 0; width: 100%; text-align: center;
            background: rgba(0,0,0,0.7); padding: 10px; font-size: 12px; color: #aaa; z-index: 90;
        }
    </style>
</head>
<body>
    <a href="index.php" class="back-btn">← Back</a>
    
    <div id="player"></div>

   

    <script>
        var player = new Clappr.Player({
            source: "<?= $streamUrl ?>",
            parentId: "#player",
            width: '100%',
            height: '100%',
            autoPlay: true,
            poster: "<?= $logo ?>",
            playback: {
                playInline: true,
            }
        });
    </script>
</body>
</html>