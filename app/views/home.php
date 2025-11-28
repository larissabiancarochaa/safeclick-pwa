<?php
// app/views/home.php
?><!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SafeClick - Verificador Avançado de Links</title>
<link rel="manifest" href="/public/manifest.json">
<link rel="stylesheet" href="/public/css/style.css">
<meta name="theme-color" content="#8B5CF6">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
</head>
<body>
<div class="app-container">
  <!-- Header Flutuante -->
  <header class="app-header">
    <div class="header-content">
      <div class="logo-section">
        <div class="logo-icon">
          <span class="material-symbols-rounded">shield</span>
        </div>
        <div class="logo-text">
          <h1>SafeClick</h1>
          <p>Proteção inteligente</p>
        </div>
      </div>
      <div class="header-actions">
        <button class="icon-btn" id="themeToggle">
          <span class="material-symbols-rounded">contrast</span>
        </button>
      </div>
    </div>
  </header>

  <!-- Conteúdo Principal -->
  <main class="app-main">
    <!-- Hero Section -->
    <section class="hero-section">
      <div class="hero-content">
        <div class="hero-badge">
          <span class="material-symbols-rounded">verified</span>
          Proteção em tempo real
        </div>
        <h2 class="hero-title">
          Verifique links com
          <span class="gradient-text">segurança total</span>
        </h2>
        <p class="hero-subtitle">
          Analise URLs suspeitas e proteja seus dados com tecnologia avançada
        </p>
      </div>
      <div class="hero-visual">
        <div class="floating-card card-1">
          <span class="material-symbols-rounded">security</span>
        </div>
        <div class="floating-card card-2">
          <span class="material-symbols-rounded">shield</span>
        </div>
        <div class="floating-card card-3">
          <span class="material-symbols-rounded">verified</span>
        </div>
      </div>
    </section>

    <!-- Card de Análise -->
    <section class="analysis-card glass-card">
      <div class="card-header">
        <div class="card-icon">
          <span class="material-symbols-rounded">search</span>
        </div>
        <div class="card-title">
          <h3>Analisar URL</h3>
          <p>Verifique a segurança de qualquer link</p>
        </div>
      </div>

      <form id="analyzeForm" class="modern-form">
        <div class="input-group">
          <div class="input-icon">
            <span class="material-symbols-rounded">link</span>
          </div>
          <input type="url" id="urlInput" placeholder="https://exemplo.com" autocomplete="off">
        </div>
        
        <div class="input-group">
          <div class="input-icon">
            <span class="material-symbols-rounded">description</span>
          </div>
          <input type="text" id="noteInput" placeholder="Adicione uma observação (opcional)">
        </div>

        <div class="action-buttons">
          <button type="button" id="btnAnalyze" class="btn btn-primary gradient-bg">
            <span class="material-symbols-rounded">security</span>
            Analisar Segurança
          </button>
          <button type="button" id="btnSave" class="btn btn-secondary">
            <span class="material-symbols-rounded">save</span>
            Salvar
          </button>
        </div>
      </form>

      <div id="resultBox" class="result-container hidden">
        <!-- Resultado dinâmico será inserido aqui -->
      </div>
    </section>

    <!-- Card de Histórico -->
    <section class="history-card glass-card">
      <div class="card-header">
        <div class="card-icon">
          <span class="material-symbols-rounded">history</span>
        </div>
        <div class="card-title">
          <h3>Histórico</h3>
          <p>Suas análises recentes</p>
        </div>
        <button class="icon-btn" id="refreshHistory">
          <span class="material-symbols-rounded">refresh</span>
        </button>
      </div>

      <div id="listBox" class="history-list">
        <div class="loading-state">
          <div class="loading-spinner"></div>
          <p>Carregando histórico...</p>
        </div>
      </div>
    </section>
  </main>

  <!-- Footer Moderno -->
  <footer class="app-footer">
    <div class="footer-content">
      <div class="footer-info">
        <p>SafeClick PWA © 2024</p>
        <div class="footer-status">
          <span class="status-dot"></span>
          Sistema ativo e protegido
        </div>
      </div>
      <div class="tech-badges">
        <span class="tech-badge">SSL</span>
        <span class="tech-badge">PWA</span>
        <span class="tech-badge">HTTPS</span>
      </div>
    </div>
  </footer>
</div>

<script src="/public/js/app.js" defer></script>
</body>
</html>