<?php
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se há uma sessão ativa
session_start();

$usuario_nome = isset($_SESSION['usuario_nome']) ? $_SESSION['usuario_nome'] : (isset($_SESSION['usuario']['nome']) ? $_SESSION['usuario']['nome'] : 'Usuário');

// Busca os locais
$stmt = $pdo->query("SELECT id, nome FROM locais ORDER BY nome");
$locais = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema de Atendimento - Painel do Atendente</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome para ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    /*
      Variáveis CSS para fácil customização de cores e layout.
      Altere os valores em :root para mudar rapidamente o visual do sistema.
    */
    :root {
      /* Cores principais */
      --cor-primaria: #1976d2; /* Cor principal do sistema (azul) */
      --cor-primaria-escura: #1565c0; /* Azul mais escuro para destaques */
      --cor-secundaria: #42a5f5; /* Azul claro para botões secundários */
      --cor-sucesso: #28a745; /* Verde para sucesso */
      --cor-perigo: #ef5350; /* Vermelho para ações perigosas */
      --cor-fundo: #f0f4fa; /* Cor de fundo geral */
      --cor-branco: #fff;
      --cor-texto: #222;
      --cor-texto-sidebar: #e3f2fd;
      --cor-borda-fila: #90caf9;
      --cor-badge-fila: #1976d2;
      /* Sidebar */
      --sidebar-width: 280px; /* Largura do menu lateral */
      --sidebar-padding: 32px 24px 24px 24px; /* Padding do menu lateral */
      /* Espaçamentos */
      --main-padding: 0px 10px 10px 10px; /* Padding do conteúdo principal */
      --main-padding-mobile: 16px 8px; /* Padding do conteúdo no mobile */
    }
    body {
      background-color: var(--cor-fundo);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--cor-texto);
    }
    .sidebar {
      min-height: 100vh;
      width: var(--sidebar-width);
      background: linear-gradient(145deg, var(--cor-primaria) 80%, var(--cor-primaria-escura) 100%);
      color: var(--cor-branco);
      box-shadow: 2px 0 10px rgba(25, 118, 210, 0.08);
      position: fixed;
      left: 0;
      top: 0;
      z-index: 1030;
      padding: var(--sidebar-padding);
      display: flex;
      flex-direction: column;
      gap: 24px;
    }
    .sidebar h4 {
      font-weight: bold;
      margin-bottom: 24px;
      color: var(--cor-branco);
    }
    .sidebar label {
      margin-top: 12px;
      margin-bottom: 4px;
      color: var(--cor-texto-sidebar);
      font-weight: 500;
    }
    .sidebar .form-select,
    .sidebar .form-check-input {
      border-radius: 8px;
      border: none;
      margin-bottom: 8px;
    }
    .sidebar .form-select:focus,
    .sidebar .form-check-input:focus {
      border-color: var(--cor-primaria);
      box-shadow: 0 0 0 0.2rem rgba(25, 118, 210, 0.15);
    }
    .sidebar .form-check-label {
      color: var(--cor-texto-sidebar);
    }
    .sidebar .form-check-input:checked {
      background-color: var(--cor-primaria-escura);
      border-color: var(--cor-primaria-escura);
    }
    .sidebar .sidebar-footer {
      margin-top: auto;
      color: #bbdefb;
      font-size: 0.95rem;
      text-align: center;
    }
    .sidebar .btn-liberar-guiche {
      background: var(--cor-branco);
      color: var(--cor-primaria);
      border: none;
      border-radius: 20px;
      font-weight: bold;
      margin-top: 10px;
      margin-bottom: 10px;
      width: 100%;
      transition: background 0.2s;
    }
    .sidebar .btn-liberar-guiche:hover {
      background: #e3f2fd;
      color: var(--cor-primaria-escura);
    }
    .sidebar .btn-sair {
      background: var(--cor-perigo);
      color: var(--cor-branco);
      border: none;
      border-radius: 20px;
      font-weight: bold;
      width: 100%;
      margin-bottom: 10px;
      transition: background 0.2s;
    }
    .sidebar .btn-sair:hover {
      background: #c62828;
      color: var(--cor-branco);
    }
    .sidebar .usuario-logado {
      font-size: 1.1em;
      font-weight: bold;
      color: var(--cor-branco);
      margin-bottom: 10px;
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .main-content {
      margin-left: var(--sidebar-width);
      padding: var(--main-padding);
      min-height: 100vh;
      background: var(--cor-fundo);
    }
    .atendente-header {
      background: var(--cor-primaria);
      color: var(--cor-branco);
      padding: 18px 24px;
      margin-bottom: 32px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(25, 118, 210, 0.08);
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .info-guiche {
      background: var(--cor-primaria-escura);
      color: var(--cor-branco);
      padding: 5px 15px;
      border-radius: 20px;
      font-weight: bold;
      display: inline-block;
      margin-left: 10px;
    }
    .card-atendimento, .card-filas {
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(25, 118, 210, 0.10);
      border: none;
      margin-bottom: 30px;
      overflow: hidden;
      background: var(--cor-branco);
    }
    .card-atendimento .card-header, .card-filas .card-header {
      background: var(--cor-primaria);
      color: var(--cor-branco);
      font-weight: bold;
      padding: 15px;
      border-radius: 15px 15px 0 0;
    }
    .senha-atual {
      background: #e3f2fd;
      border-left: 5px solid var(--cor-primaria);
      padding: 20px;
      margin: 20px 0;
      border-radius: 8px;
      font-size: 1.2rem;
      box-shadow: 0 4px 10px rgba(25, 118, 210, 0.05);
      transition: all 0.3s ease;
    }
    .senha-numero {
      font-size: 2.5rem;
      font-weight: bold;
      color: var(--cor-primaria);
      margin-bottom: 15px;
    }
    .senha-info {
      color: var(--cor-primaria);
      font-size: 1rem;
    }
    .btn-chamar {
      background: var(--cor-primaria);
      border: none;
      border-radius: 50px;
      padding: 15px 30px;
      font-size: 1.2rem;
      font-weight: bold;
      margin-top: 20px;
      box-shadow: 0 4px 15px rgba(25, 118, 210, 0.15);
      transition: all 0.3s ease;
      width: 100%;
      color: var(--cor-branco);
    }
    .btn-chamar:hover {
      background: var(--cor-primaria-escura);
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(25, 118, 210, 0.18);
    }
    .btn-controle {
      border-radius: 30px;
      padding: 8px 20px;
      margin: 5px;
      font-weight: 500;
      box-shadow: 0 3px 8px rgba(25, 118, 210, 0.10);
      transition: all 0.3s ease;
    }
    .btn-iniciar {
      background: var(--cor-secundaria);
      border: none;
      color: var(--cor-branco);
    }
    .btn-iniciar:hover {
      background: var(--cor-primaria);
    }
    .btn-reconvocar {
      background: #90caf9;
      border: none;
      color: var(--cor-primaria-escura);
    }
    .btn-reconvocar:hover {
      background: #64b5f6;
    }
    .btn-finalizar {
      background: var(--cor-perigo);
      border: none;
      color: var(--cor-branco);
    }
    .btn-finalizar:hover {
      background: #c62828;
    }
    .senha-fila {
      background: #f5faff;
      border-left: 5px solid var(--cor-borda-fila);
      padding: 12px 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      font-weight: 500;
      box-shadow: 0 2px 5px rgba(25, 118, 210, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.3s ease;
    }
    .senha-fila:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 8px rgba(25, 118, 210, 0.10);
    }
    .badge-fila {
      background: var(--cor-badge-fila);
      color: var(--cor-branco);
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
    }
    .sem-senha {
      text-align: center;
      padding: 30px;
      color: #90caf9;
      font-style: italic;
    }
    .spinner-border {
      width: 1rem;
      height: 1rem;
      margin-right: 10px;
    }
    @media (max-width: 991.98px) {
      .sidebar {
        display: none;
      }
      .main-content {
        margin-left: 0;
        padding: var(--main-padding-mobile);
      }
      .offcanvas.offcanvas-start {
        background: linear-gradient(145deg, var(--cor-primaria) 80%, var(--cor-primaria-escura) 100%);
        color: var(--cor-branco);
      }
    }
    .btn-menu-mobile {
      display: none;
      position: fixed;
      top: 16px;
      left: 16px;
      z-index: 2000;
      background: var(--cor-primaria);
      color: var(--cor-branco);
      border: none;
      border-radius: 8px;
      padding: 8px 12px;
      font-size: 1.5em;
    }
    @media (max-width: 991.98px) {
      .btn-menu-mobile {
        display: block;
      }
    }
  </style>
</head>
<body>
<!-- Botão menu mobile -->
<button class="btn-menu-mobile" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
  <i class="fas fa-bars"></i>
</button>
<!-- Sidebar Desktop -->
<div class="sidebar d-none d-lg-flex flex-column" id="sidebarDesktop">
  <div class="usuario-logado"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($usuario_nome) ?></div>
  <h4><i class="fas fa-cogs me-2"></i>Configuração</h4>
  <label for="localSelect">Local de Atendimento</label>
  <select id="localSelect" class="form-select form-select-lg mb-2" required></select>
  <label for="guicheSelect">Guichê de Atendimento</label>
  <select id="guicheSelect" class="form-select form-select-lg mb-2" required disabled></select>
  <label>Filas para Atendimento</label>
  <div id="filasContainer" class="card p-2 bg-transparent border-0 text-white">
    <p class="text-white-50">Selecione o local para ver as filas disponíveis</p>
  </div>
  <button class="btn-liberar-guiche" id="btn-liberar-guiche"><i class="fas fa-unlock"></i> Liberar Guichê</button>
  <a href="../logout.php" class="btn-sair"><i class="fas fa-sign-out-alt"></i> Sair</a>
  <div class="sidebar-footer mt-4">
    <span style="font-size:0.9em;">Sistema de Senhas</span>
  </div>
</div>
<!-- Sidebar Offcanvas Mobile -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="sidebarOffcanvasLabel"><i class="fas fa-cogs me-2"></i>Configuração</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body d-flex flex-column">
    <div class="usuario-logado mb-2"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($usuario_nome) ?></div>
    <label for="localSelectMobile">Local de Atendimento</label>
    <select id="localSelectMobile" class="form-select form-select-lg mb-2" required></select>
    <label for="guicheSelectMobile">Guichê de Atendimento</label>
    <select id="guicheSelectMobile" class="form-select form-select-lg mb-2" required disabled></select>
    <label>Filas para Atendimento</label>
    <div id="filasContainerMobile" class="card p-2 bg-transparent border-0 text-white">
      <p class="text-white-50">Selecione o local para ver as filas disponíveis</p>
    </div>
    <button class="btn-liberar-guiche" id="btn-liberar-guiche-mobile"><i class="fas fa-unlock"></i> Liberar Guichê</button>
    <a href="../logout.php" class="btn-sair"><i class="fas fa-sign-out-alt"></i> Sair</a>
    <div class="sidebar-footer mt-4">
      <span style="font-size:0.9em;">Sistema de Senhas</span>
    </div>
  </div>
</div>
<div class="main-content flex-grow-1">
  <div class="atendente-header">
    <div>
      <h1 class="m-0 fs-3"><i class="fas fa-headset me-2"></i>Atendimento</h1>
    </div>
    <div>
      <span id="info-display" class="fs-5"></span>
      <span id="guiche-display" class="info-guiche"></span>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <!-- Painel de Atendimento Atual -->
      <div class="col-lg-6 mb-4">
        <div class="card card-atendimento">
          <div class="card-header">
            <i class="fas fa-user-clock me-2"></i>Atendimento Atual
          </div>
          <div class="card-body p-4">
            <div id="sem-senha-atual" class="sem-senha">
              <i class="fas fa-exclamation-circle me-2"></i>Nenhuma senha em atendimento
            </div>
            <div id="senha-em-atendimento" class="senha-atual" style="display: none;">
              <div class="senha-numero" id="numero-atual">Senha A001</div>
              <div class="senha-info" id="info-atual">
                Fila: Atendimento Geral<br>
                Emitida: 10:30 - Chamada: 10:45
              </div>
              <div class="mt-4">
                <div class="d-flex flex-wrap justify-content-start">
                  <button id="btn-iniciar" class="btn btn-controle btn-iniciar">
                    <i class="fas fa-play me-2"></i>Iniciar Atendimento
                  </button>
                  <button id="btn-reconvocar" class="btn btn-controle btn-reconvocar">
                    <i class="fas fa-bullhorn me-2"></i>Reconvocar
                  </button>
                  <button id="btn-finalizar" class="btn btn-controle btn-finalizar">
                    <i class="fas fa-check-circle me-2"></i>Finalizar
                  </button>
                </div>
              </div>
            </div>
            <div class="text-center mt-4">
              <button id="btn-chamar" class="btn btn-chamar">
                <i class="fas fa-bell me-2"></i>Chamar Próxima Senha
              </button>
            </div>
          </div>
        </div>
      </div>
      <!-- Painel de Filas -->
      <div class="col-lg-6 mb-4">
        <div class="card card-filas">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list-ol me-2"></i>Próximas Senhas</span>
            <button id="btn-atualizar-filas" class="btn btn-sm btn-light">
              <i class="fas fa-sync-alt"></i>
            </button>
          </div>
          <div class="card-body p-4">
            <div id="filas-container">
              <div class="sem-senha">
                <i class="fas fa-exclamation-circle me-2"></i>Carregando senhas...
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- Toast para notificações -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
  <div id="notificacao" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <strong class="me-auto" id="toast-titulo">Notificação</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toast-mensagem">
      Mensagem de notificação
    </div>
  </div>
</div>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Variáveis globais
  let localId = null;
  let localNome = "";
  let guicheId = null;
  let guicheNome = "";
  let filasSelecionadas = [];
  let senhaAtual = null;
  let senhasUltimaLista = [];
  let timerSenhas = null;

  // Elementos DOM (desktop)
  const localSelect = document.getElementById('localSelect');
  const guicheSelect = document.getElementById('guicheSelect');
  const filasContainer = document.getElementById('filasContainer');
  // Mobile
  const localSelectMobile = document.getElementById('localSelectMobile');
  const guicheSelectMobile = document.getElementById('guicheSelectMobile');
  const filasContainerMobile = document.getElementById('filasContainerMobile');
  // Comum
  const infoDisplay = document.getElementById('info-display');
  const guicheDisplay = document.getElementById('guiche-display');
  const semSenhaAtual = document.getElementById('sem-senha-atual');
  const senhaEmAtendimento = document.getElementById('senha-em-atendimento');
  const numeroAtual = document.getElementById('numero-atual');
  const infoAtual = document.getElementById('info-atual');
  const btnIniciar = document.getElementById('btn-iniciar');
  const btnReconvocar = document.getElementById('btn-reconvocar');
  const btnFinalizar = document.getElementById('btn-finalizar');
  const btnChamar = document.getElementById('btn-chamar');
  const filasContainerEl = document.getElementById('filas-container');
  const btnAtualizarFilas = document.getElementById('btn-atualizar-filas');
  const btnLiberarGuiche = document.getElementById('btn-liberar-guiche');
  const btnLiberarGuicheMobile = document.getElementById('btn-liberar-guiche-mobile');

  // Toast para notificações
  const toastEl = document.getElementById('notificacao');
  const toast = new bootstrap.Toast(toastEl);
  const toastTitulo = document.getElementById('toast-titulo');
  const toastMensagem = document.getElementById('toast-mensagem');

  // Utilitário para salvar/restaurar config
  function salvarConfig() {
    localStorage.setItem('atd_localId', localId || '');
    localStorage.setItem('atd_guicheId', guicheId || '');
    localStorage.setItem('atd_filas', JSON.stringify(filasSelecionadas));
  }
  function restaurarConfig() {
    localId = localStorage.getItem('atd_localId') || '';
    guicheId = localStorage.getItem('atd_guicheId') || '';
    try {
      filasSelecionadas = JSON.parse(localStorage.getItem('atd_filas')) || [];
    } catch { filasSelecionadas = []; }
  }

  // Sincronizar selects/checkboxes entre desktop e mobile
  function syncSelects(from, to) {
    to.value = from.value;
    to.dispatchEvent(new Event('change'));
  }
  function syncCheckboxes(fromContainer, toContainer) {
    const fromChecks = fromContainer.querySelectorAll('.fila-check');
    const toChecks = toContainer.querySelectorAll('.fila-check');
    fromChecks.forEach((cb, i) => {
      if (toChecks[i]) toChecks[i].checked = cb.checked;
    });
  }

  // Mostrar notificação
  function mostrarNotificacao(titulo, mensagem, tipo = 'success') {
    toastTitulo.innerText = titulo;
    toastMensagem.innerText = mensagem;
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'text-white');
    if (tipo === 'success') {
      toastEl.classList.add('bg-success', 'text-white');
    } else if (tipo === 'error') {
      toastEl.classList.add('bg-danger', 'text-white');
    } else if (tipo === 'warning') {
      toastEl.classList.add('bg-warning');
    }
    toast.show();
  }

  // Preencher selects de locais (desktop e mobile)
  function preencherLocais() {
    fetch('api_locais.php')
      .then(r => r.json())
      .then(data => {
        localSelect.innerHTML = '<option value="" disabled>Selecione o local</option>';
        localSelectMobile.innerHTML = '<option value="" disabled>Selecione o local</option>';
        data.forEach(local => {
          const opt1 = document.createElement('option');
          opt1.value = local.id;
          opt1.textContent = local.nome;
          localSelect.appendChild(opt1);
          const opt2 = document.createElement('option');
          opt2.value = local.id;
          opt2.textContent = local.nome;
          localSelectMobile.appendChild(opt2);
        });
        // Restaurar seleção
        restaurarConfig();
        if (localId) {
          localSelect.value = localId;
          localSelectMobile.value = localId;
          localSelect.dispatchEvent(new Event('change'));
          localSelectMobile.dispatchEvent(new Event('change'));
        }
      });
  }

  // Carregar guichês (desktop e mobile)
  function carregarGuiches(localIdSel, guicheSelectEl, callback) {
    guicheSelectEl.innerHTML = '<option value="" selected disabled>Carregando guichês...</option>';
    guicheSelectEl.disabled = true;
    fetch(`api_guiches.php?local_id=${localIdSel}`)
      .then(response => response.json())
      .then(data => {
        if (data.length === 0) {
          guicheSelectEl.innerHTML = '<option value="" selected disabled>Nenhum guichê disponível</option>';
        } else {
          guicheSelectEl.innerHTML = '<option value="" selected disabled>Selecione o guichê</option>';
          data.forEach(guiche => {
            const option = document.createElement('option');
            option.value = guiche.id;
            option.textContent = guiche.nome;
            guicheSelectEl.appendChild(option);
          });
          guicheSelectEl.disabled = false;
          // Restaurar seleção do guichê salvo
          if (guicheId) {
            guicheSelectEl.value = guicheId;
            guicheSelectEl.dispatchEvent(new Event('change'));
          }
        }
        if (callback) callback();
      });
  }

  // Carregar filas (desktop e mobile)
  function carregarFilas(localIdSel, filasContainerEl, callback) {
    filasContainerEl.innerHTML = '<p class="text-center"><div class="spinner-border text-light" role="status"></div> Carregando filas...</p>';
    fetch(`api_filas.php?local_id=${localIdSel}`)
      .then(response => response.json())
      .then(data => {
        if (data.length === 0) {
          filasContainerEl.innerHTML = '<p class="text-white-50">Nenhuma fila disponível para este local</p>';
        } else {
          filasContainerEl.innerHTML = '';
          data.forEach(fila => {
            const div = document.createElement('div');
            div.className = 'form-check mb-2';
            div.innerHTML = `
              <input class="form-check-input fila-check" type="checkbox" value="${fila.id}" id="fila-${fila.id}-${filasContainerEl.id}">
              <label class="form-check-label" for="fila-${fila.id}-${filasContainerEl.id}">
                ${fila.nome} <span class="badge bg-secondary">${fila.sigla || ''}</span>
              </label>
            `;
            filasContainerEl.appendChild(div);
          });
        }
        if (callback) callback();
      });
  }

  // Sincronizar eventos dos selects/checkboxes entre desktop e mobile
  function setupSync() {
    // Local
    localSelect.addEventListener('change', function() {
      localId = this.value;
      localSelectMobile.value = localId;
      salvarConfig();
      carregarGuiches(localId, guicheSelect, function() {
        if (guicheId) guicheSelect.value = guicheId;
      });
      carregarGuiches(localId, guicheSelectMobile, function() {
        if (guicheId) guicheSelectMobile.value = guicheId;
      });
      carregarFilas(localId, filasContainer, function() {
        marcarFilasSelecionadas(filasContainer);
      });
      carregarFilas(localId, filasContainerMobile, function() {
        marcarFilasSelecionadas(filasContainerMobile);
      });
    });
    localSelectMobile.addEventListener('change', function() {
      localId = this.value;
      localSelect.value = localId;
      salvarConfig();
      carregarGuiches(localId, guicheSelect, function() {
        if (guicheId) guicheSelect.value = guicheId;
      });
      carregarGuiches(localId, guicheSelectMobile, function() {
        if (guicheId) guicheSelectMobile.value = guicheId;
      });
      carregarFilas(localId, filasContainer, function() {
        marcarFilasSelecionadas(filasContainer);
      });
      carregarFilas(localId, filasContainerMobile, function() {
        marcarFilasSelecionadas(filasContainerMobile);
      });
    });
    // Guichê
    guicheSelect.addEventListener('change', function() {
      guicheId = this.value;
      guicheSelectMobile.value = guicheId;
      salvarConfig();
      marcarGuicheEmUso();
    });
    guicheSelectMobile.addEventListener('change', function() {
      guicheId = this.value;
      guicheSelect.value = guicheId;
      salvarConfig();
      marcarGuicheEmUso();
    });
    // Filas
    filasContainer.addEventListener('change', function() {
      atualizarFilasSelecionadas(filasContainer);
      marcarFilasSelecionadas(filasContainerMobile);
      salvarConfig();
    });
    filasContainerMobile.addEventListener('change', function() {
      atualizarFilasSelecionadas(filasContainerMobile);
      marcarFilasSelecionadas(filasContainer);
      salvarConfig();
    });
  }
  function atualizarFilasSelecionadas(container) {
    const checkboxes = container.querySelectorAll('.fila-check:checked');
    filasSelecionadas = Array.from(checkboxes).map(cb => cb.value);
  }
  function marcarFilasSelecionadas(container) {
    const checks = container.querySelectorAll('.fila-check');
    checks.forEach(cb => {
      cb.checked = filasSelecionadas.includes(cb.value);
    });
  }

  // Marcar guichê como em uso
  function marcarGuicheEmUso() {
    if (guicheId) {
      fetch('api_atualizar_guiche.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ guiche_id: guicheId, status: 'em_uso' })
      });
      fetch('salvar_guiche_sessao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ guiche_id: guicheId })
      });
    }
  }
  // Liberar guichê manual
  function liberarGuiche() {
    if (guicheId) {
      fetch('api_atualizar_guiche.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ guiche_id: guicheId, status: 'disponivel' })
      });
      localStorage.removeItem('atd_guicheId');
      guicheId = null;
      guicheSelect.value = '';
      guicheSelectMobile.value = '';
      mostrarNotificacao('Guichê', 'Guichê liberado com sucesso!', 'success');
    }
  }
  btnLiberarGuiche.addEventListener('click', liberarGuiche);
  btnLiberarGuicheMobile.addEventListener('click', liberarGuiche);

  // Atualizar info do topo
  function atualizarInfoTopo() {
    infoDisplay.textContent = localNome || '';
    guicheDisplay.textContent = guicheNome ? `Guichê ${guicheNome}` : '';
  }

  // Atualização automática da lista de senhas (sem piscar)
  function listasIguais(a, b) {
    if (a.length !== b.length) return false;
    for (let i = 0; i < a.length; i++) {
      if (a[i].id !== b[i].id) return false;
    }
    return true;
  }
  function carregarProximasSenhasAuto() {
    if (!localId || !guicheId || filasSelecionadas.length === 0) {
      filasContainerEl.innerHTML = '<div class="sem-senha"><i class="fas fa-exclamation-circle me-2"></i>Selecione local, guichê e pelo menos uma fila</div>';
      return;
    }
    const params = new URLSearchParams({
      local_id: localId,
      filas: filasSelecionadas.join(',')
    });
    fetch(`api_proximas_senhas.php?${params}`)
      .then(response => response.json())
      .then(data => {
        if (!listasIguais(data, senhasUltimaLista)) {
          senhasUltimaLista = data;
          if (data.length === 0) {
            filasContainerEl.innerHTML = '<div class="sem-senha"><i class="fas fa-exclamation-circle me-2"></i>Não há senhas aguardando nas filas selecionadas</div>';
          } else {
            filasContainerEl.innerHTML = '';
            // Agrupar por fila
            const filasSenhas = {};
            data.forEach(senha => {
              if (!filasSenhas[senha.fila_nome]) {
                filasSenhas[senha.fila_nome] = [];
              }
              filasSenhas[senha.fila_nome].push(senha);
            });
            for (const [fila, senhas] of Object.entries(filasSenhas)) {
              const filaDiv = document.createElement('div');
              filaDiv.className = 'mb-4';
              const filaHeader = document.createElement('h5');
              filaHeader.className = 'mb-3 border-bottom pb-2';
              filaHeader.textContent = fila;
              filaDiv.appendChild(filaHeader);
              senhas.slice(0, 5).forEach(senha => {
                const senhaEl = document.createElement('div');
                senhaEl.className = 'senha-fila';
                senhaEl.innerHTML = `
                  <span>${senha.numero}</span>
                  <span class="badge-fila">${formatarHorario(senha.criada_em)}</span>
                `;
                senhaEl.addEventListener('click', () => {
                  if (confirm(`Deseja chamar a senha ${senha.numero}?`)) {
                    chamarSenhaEspecifica(senha.id);
                  }
                });
                filaDiv.appendChild(senhaEl);
              });
              filasContainerEl.appendChild(filaDiv);
            }
          }
        }
      });
  }

  // Iniciar tudo
  document.addEventListener('DOMContentLoaded', function() {
    preencherLocais();
    setupSync();
    // Atualização automática da lista de senhas
    timerSenhas = setInterval(carregarProximasSenhasAuto, 1000);
    // Restaurar config ao carregar
    restaurarConfig();
  });

  // Liberar guichê ao sair da página ou recarregar
  window.addEventListener('beforeunload', liberarGuiche);

  // Função para formatar timestamp em horário
  function formatarHorario(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
  }

  // Função para formatar data completa
  function formatarData(timestamp) {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    return date.toLocaleDateString('pt-BR') + ' ' + date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
  }

  // Chamar próxima senha
  btnChamar.addEventListener('click', function() {
    if (senhaAtual && !confirm('Já existe uma senha em atendimento. Deseja chamar outra senha?')) {
      return;
    }
    btnChamar.disabled = true;
    btnChamar.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Chamando...';
    const params = new URLSearchParams({
      local_id: localId,
      guiche_id: guicheId,
      filas: filasSelecionadas.join(',')
    });
    fetch(`api_proximo.php?${params}`)
      .then(response => response.json())
      .then(data => {
        btnChamar.disabled = false;
        btnChamar.innerHTML = '<i class="fas fa-bell me-2"></i>Chamar Próxima Senha';
        if (data.error) {
          mostrarNotificacao('Erro', data.error, 'error');
          return;
        }
        if (!data.id) {
          mostrarNotificacao('Aviso', 'Não há senhas aguardando nas filas selecionadas', 'warning');
          return;
        }
        senhaAtual = data;
        atualizarTelaSenhaAtual();
        carregarProximasSenhasAuto();
        mostrarNotificacao('Sucesso', `Senha ${data.numero} chamada com sucesso!`);
      })
      .catch(error => {
        console.error('Erro ao chamar próxima senha:', error);
        btnChamar.disabled = false;
        btnChamar.innerHTML = '<i class="fas fa-bell me-2"></i>Chamar Próxima Senha';
        mostrarNotificacao('Erro', 'Falha ao chamar próxima senha', 'error');
      });
  });

  // Chamar senha específica
  function chamarSenhaEspecifica(senhaId) {
    if (senhaAtual && !confirm('Já existe uma senha em atendimento. Deseja chamar outra senha?')) {
      return;
    }
    const params = new URLSearchParams({
      senha_id: senhaId,
      guiche_id: guicheId
    });
    fetch(`api_chamar_especifica.php?${params}`)
      .then(response => response.json())
      .then(data => {
        if (data.error) {
          mostrarNotificacao('Erro', data.error, 'error');
          return;
        }
        senhaAtual = data;
        atualizarTelaSenhaAtual();
        carregarProximasSenhasAuto();
        mostrarNotificacao('Sucesso', `Senha ${data.numero} chamada com sucesso!`);
      })
      .catch(error => {
        console.error('Erro ao chamar senha específica:', error);
        mostrarNotificacao('Erro', 'Falha ao chamar senha específica', 'error');
      });
  }

  // Atualizar tela com a senha atual
  function atualizarTelaSenhaAtual() {
    if (!senhaAtual) {
      semSenhaAtual.style.display = 'block';
      senhaEmAtendimento.style.display = 'none';
      return;
    }
    semSenhaAtual.style.display = 'none';
    senhaEmAtendimento.style.display = 'block';
    numeroAtual.textContent = `Senha ${senhaAtual.numero}`;
    let infoText = `Fila: ${senhaAtual.fila_nome}<br>`;
    infoText += `Emitida: ${formatarHorario(senhaAtual.criada_em)}`;
    if (senhaAtual.chamada_em) {
      infoText += ` - Chamada: ${formatarHorario(senhaAtual.chamada_em)}`;
    }
    if (senhaAtual.atendimento_iniciado_em) {
      infoText += `<br>Início atendimento: ${formatarHorario(senhaAtual.atendimento_iniciado_em)}`;
    }
    infoAtual.innerHTML = infoText;
    btnIniciar.disabled = !!senhaAtual.atendimento_iniciado_em;
    btnFinalizar.disabled = !senhaAtual.atendimento_iniciado_em;
  }

  // Iniciar atendimento
  btnIniciar.addEventListener('click', function() {
    if (!senhaAtual) return;
    btnIniciar.disabled = true;
    btnIniciar.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Iniciando...';
    fetch(`api_iniciar_atendimento.php?senha_id=${senhaAtual.id}`)
      .then(response => response.json())
      .then(data => {
        btnIniciar.innerHTML = '<i class="fas fa-play me-2"></i>Iniciar Atendimento';
        if (data.error) {
          btnIniciar.disabled = false;
          mostrarNotificacao('Erro', data.error, 'error');
          return;
        }
        senhaAtual.atendimento_iniciado_em = data.atendimento_iniciado_em;
        atualizarTelaSenhaAtual();
        mostrarNotificacao('Sucesso', 'Atendimento iniciado!');
      })
      .catch(error => {
        console.error('Erro ao iniciar atendimento:', error);
        btnIniciar.disabled = false;
        btnIniciar.innerHTML = '<i class="fas fa-play me-2"></i>Iniciar Atendimento';
        mostrarNotificacao('Erro', 'Falha ao iniciar atendimento', 'error');
      });
  });

  // Reconvocar senha
  btnReconvocar.addEventListener('click', function() {
    if (!senhaAtual) return;
    btnReconvocar.disabled = true;
    btnReconvocar.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Reconvocando...';
    fetch(`api_reconvocar.php?senha_id=${senhaAtual.id}`)
      .then(response => response.json())
      .then(data => {
        btnReconvocar.disabled = false;
        btnReconvocar.innerHTML = '<i class="fas fa-bullhorn me-2"></i>Reconvocar';
        if (data.error) {
          mostrarNotificacao('Erro', data.error, 'error');
          return;
        }
        senhaAtual.chamada_em = data.chamada_em;
        atualizarTelaSenhaAtual();
        mostrarNotificacao('Sucesso', `Senha ${senhaAtual.numero} reconvocada!`);
      })
      .catch(error => {
        console.error('Erro ao reconvocar senha:', error);
        btnReconvocar.disabled = false;
        btnReconvocar.innerHTML = '<i class="fas fa-bullhorn me-2"></i>Reconvocar';
        mostrarNotificacao('Erro', 'Falha ao reconvocar senha', 'error');
      });
  });

  // Finalizar atendimento
  btnFinalizar.addEventListener('click', function() {
    if (!senhaAtual) return;
    btnFinalizar.disabled = true;
    btnFinalizar.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div> Finalizando...';
    fetch(`api_finalizar_atendimento.php?senha_id=${senhaAtual.id}`)
      .then(response => response.json())
      .then(data => {
        btnFinalizar.disabled = false;
        btnFinalizar.innerHTML = '<i class="fas fa-check-circle me-2"></i>Finalizar';
        if (data.error) {
          mostrarNotificacao('Erro', data.error, 'error');
          return;
        }
        mostrarNotificacao('Sucesso', `Atendimento da senha ${senhaAtual.numero} finalizado!`);
        senhaAtual = null;
        atualizarTelaSenhaAtual();
        carregarProximasSenhasAuto();
      })
      .catch(error => {
        console.error('Erro ao finalizar atendimento:', error);
        btnFinalizar.disabled = false;
        btnFinalizar.innerHTML = '<i class="fas fa-check-circle me-2"></i>Finalizar';
        mostrarNotificacao('Erro', 'Falha ao finalizar atendimento', 'error');
      });
  });
</script>
</body>
</html>