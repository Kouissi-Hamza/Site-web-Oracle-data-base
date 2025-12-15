<?php
session_start();
include 'connexion.php';

// Check if user is trying to fix the issue
$fixed = false;
$error = null;
$info = [];

try {
    // First, let's check the current column definition
    $checkColumn = $conn->query("DESCRIBE users password");
    $columnInfo = $checkColumn->fetch(PDO::FETCH_ASSOC);
    
    if ($columnInfo) {
        $info['current_type'] = $columnInfo['Type'];
        $info['current_null'] = $columnInfo['Null'];
        $info['current_key'] = $columnInfo['Key'];
        
        // Check if it's large enough
        if (strpos($columnInfo['Type'], 'VARCHAR') !== false) {
            preg_match('/\d+/', $columnInfo['Type'], $matches);
            $currentSize = $matches[0] ?? 0;
            $info['current_size'] = $currentSize;
            
            if ($currentSize < 255) {
                $info['needs_fix'] = true;
                $info['recommended_size'] = 255;
                
                // Fix the column
                if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fix_now'])) {
                    $conn->exec("ALTER TABLE users MODIFY COLUMN password VARCHAR(255) NOT NULL");
                    $fixed = true;
                    $info['fixed'] = true;
                }
            } else {
                $info['needs_fix'] = false;
            }
        }
    }
    
    // Get some debug info about existing passwords
    $userCheck = $conn->query("SELECT id, email, password FROM users LIMIT 3");
    $users = $userCheck->fetchAll(PDO::FETCH_ASSOC);
    $info['sample_users'] = [];
    
    foreach ($users as $user) {
        $info['sample_users'][] = [
            'id' => $user['id'],
            'email' => $user['email'],
            'password_length' => strlen($user['password']),
            'password_preview' => substr($user['password'], 0, 20) . '...'
        ];
    }
    
} catch (Exception $e) {
    $error = "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnostic & Fix - Password Issue</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Roboto, Arial, sans-serif;
            background: linear-gradient(135deg, #1e88e5 0%, #1565c0 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            max-width: 600px;
            padding: 2rem;
            margin: 20px;
        }
        
        h1 {
            color: #1e88e5;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }
        
        .info-box {
            background: #f8f9fa;
            border-left: 4px solid #1e88e5;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .info-box p {
            margin: 8px 0;
            color: #333;
        }
        
        .success {
            background: #e8f5e9;
            border-left-color: #4caf50;
            color: #2e7d32;
        }
        
        .warning {
            background: #fff3e0;
            border-left-color: #ff9800;
            color: #e65100;
        }
        
        .error {
            background: #ffebee;
            border-left-color: #f44336;
            color: #c62828;
        }
        
        .debug-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        
        .debug-table th, .debug-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .debug-table th {
            background: #1e88e5;
            color: white;
            font-weight: bold;
        }
        
        .debug-table tr:hover {
            background: #f5f5f5;
        }
        
        .btn {
            background: linear-gradient(135deg, #1e88e5, #1565c0);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: transform 0.3s ease;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(30, 136, 229, 0.3);
        }
        
        .btn-secondary {
            background: #666;
        }
        
        .btn-secondary:hover {
            box-shadow: 0 8px 20px rgba(100, 100, 100, 0.3);
        }
        
        code {
            background: #f0f0f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Diagnostic de Base de Données</h1>
        
        <?php if ($error): ?>
            <div class="info-box error">
                <strong>Erreur:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($fixed): ?>
            <div class="info-box success">
                <strong>✅ Succès!</strong> La colonne password a été modifiée à VARCHAR(255).
                <p>Vous pouvez maintenant vous reconnecter sans problème.</p>
                <p><a href="login.php" style="color: #2e7d32; text-decoration: none; font-weight: bold;">→ Aller à la connexion</a></p>
            </div>
        <?php endif; ?>
        
        <?php if (!$error && !empty($info)): ?>
            <div class="info-box">
                <h2 style="font-size: 1.2rem; color: #333; margin-bottom: 10px;">État Actuel de la Colonne Password:</h2>
                <p><strong>Type:</strong> <code><?php echo htmlspecialchars($info['current_type']); ?></code></p>
                <p><strong>Nullable:</strong> <code><?php echo htmlspecialchars($info['current_null']); ?></code></p>
                <?php if (isset($info['current_size'])): ?>
                    <p><strong>Taille Actuelle:</strong> <code><?php echo $info['current_size']; ?> caractères</code></p>
                    <p><strong>Taille Requise:</strong> <code>255 caractères (minimum)</code></p>
                <?php endif; ?>
            </div>
            
            <?php if ($info['needs_fix'] ?? false): ?>
                <div class="info-box warning">
                    <strong>⚠️ Problème Détecté!</strong>
                    <p>La colonne password est trop petite (<?php echo $info['current_size']; ?> caractères).</p>
                    <p>Les mots de passe hachés nécessitent 255 caractères minimum.</p>
                    <p>Quand vous enregistrez un utilisateur, le hash est tronqué, ce qui cause l'erreur "Email ou mot de passe incorrect" à la reconnexion.</p>
                </div>
                
                <form method="POST">
                    <button type="submit" name="fix_now" value="1" class="btn">🔧 Réparer Maintenant</button>
                </form>
            <?php elseif ($info['needs_fix'] === false): ?>
                <div class="info-box success">
                    <strong>✅ Colonne OK!</strong> La taille est correcte (<?php echo $info['current_size']; ?> caractères).
                </div>
            <?php endif; ?>
            
            <?php if (!empty($info['sample_users'])): ?>
                <h2 style="font-size: 1.2rem; color: #333; margin-top: 30px; margin-bottom: 10px;">Exemples de Mots de Passe Stockés:</h2>
                <table class="debug-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Longueur du Hash</th>
                            <th>Aperçu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($info['sample_users'] as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo $user['password_length']; ?></td>
                                <td><code><?php echo htmlspecialchars($user['password_preview']); ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            
            <div class="info-box" style="margin-top: 30px; background: #e3f2fd;">
                <h3 style="color: #1e88e5; margin-bottom: 10px;">📝 Explication du Problème:</h3>
                <p style="margin-bottom: 10px;">Les hashes de mot de passe générés par <code>password_hash()</code> avec l'algorithme par défaut (bcrypt) produisent des chaînes de 60 caractères.</p>
                <p style="margin-bottom: 10px;">Cependant, PHP peut changer l'algorithme par défaut, ce qui pourrait générer des hashes plus longs (jusqu'à 255 caractères).</p>
                <p>Si votre colonne est définie en <code>VARCHAR(50)</code> ou <code>VARCHAR(100)</code>, elle tronquera le hash, ce qui rendra la vérification impossible.</p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center;">
            <a href="index.php" class="btn btn-secondary" style="display: inline-block; text-decoration: none;">← Retour à l'accueil</a>
        </div>
    </div>
</body>
</html>
