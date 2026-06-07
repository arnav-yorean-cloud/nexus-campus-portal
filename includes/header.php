<?php require_once 'config/db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexus Campus Hub</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #0f172a; color: #f8fafc; }
    </style>
</head>
<body class="min-h-screen flex flex-col bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-indigo-950 to-slate-950">
    <nav class="border-b border-slate-800/60 bg-slate-950/40 backdrop-blur-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-16 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-indigo-500 to-violet-500 flex items-center justify-center font-bold shadow-lg shadow-indigo-500/20">Ω</div>
                <a href="index.php" class="text-lg font-bold bg-gradient-to-r from-white via-slate-200 to-slate-400 bg-clip-text text-transparent tracking-tight">NEXUS ERP</a>
            </div>
            <div class="flex items-center space-x-6">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/50 text-indigo-300 uppercase tracking-wider">⚡ <?= $_SESSION['role'] ?></span>
                    <a href="dashboard.php" class="text-sm text-slate-300 hover:text-white transition">Dashboard</a>
                    <?php if ($_SESSION['role'] === 'student'): ?>
                        <a href="profile.php" class="text-sm text-slate-300 hover:text-white transition">My Profile</a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-sm bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 px-3 py-1.5 rounded-lg transition font-medium">Sign Out</a>
                <?php else: ?>
                    <a href="login.php" class="text-sm bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl font-medium transition shadow-lg shadow-indigo-600/10">Access Portal</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    <main class="flex-grow max-w-7xl w-full mx-auto px-6 py-10 flex flex-col justify-start">
