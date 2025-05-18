<?php
include_once __DIR__ . '/../includes/conexao.php';

// Verificar se há uma sessão ativa
session_start();

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
    body {
      background-color: #f0f2f5;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .main-container {
      min-height: 100vh;
      padding: 30px 0;
    }
    
    .config-card {
      max-width: 700px;
      margin: 50px auto;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      border-radius: 15px;
      border: none;
      background: linear-gradient(145deg, #ffffff, #f0f0f0);
    }
    
    .config-card .card-header {
      background: #28a745;
      color: white;
      border-radius: 15px 15px 0 0;
      padding: 15px;
    }
    
    .atendimento-container {
      display: none;
      padding: 30px 0;
    }
    
    .atendente-header {
      background: #28a745;
      color: white;
      padding: 15px 0;
      margin-bottom: 30px;
      border-radius: 0 0 10px 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .card-atendimento, .card-filas {
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      border: none;
      margin-bottom: 30px;
      overflow: hidden;
    }
    
    .card-atendimento .card-header, .card-filas .card-header {
      background: #28a745;
      color: white;
      font-weight: bold;
      padding: 15px;
      border-radius: 15px 15px 0 0;
    }
    
    .senha-atual {
      background: #f8f9fa;
      border-left: 5px solid #28a745;
      padding: 20px;
      margin: 20px 0;
      border-radius: 8px;
      font-size: 1.2rem;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
    }
    
    .senha-numero {
      font-size: 2.5rem;
      font-weight: bold;
      color: #28a745;
      margin-bottom: 15px;
    }
    
    .senha-info {
      color: #6c757d;
      font-size: 1rem;
    }
    
    .btn-chamar {
      background: #28a745;
      border: none;
      border-radius: 50px;
      padding: 15px 30px;
      font-size: 1.2rem;
      font-weight: bold;
      margin-top: 20px;
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
      transition: all 0.3s ease;
      width: 100%;
    }
    
    .btn-chamar:hover {
      background: #218838;
      transform: translateY(-2px);
      box-shadow: 0 6px 18px rgba(40, 167, 69, 0.4);
    }
    
    .btn-controle {
      border-radius: 30px;
      padding: 8px 20px;
      margin: 5px;
      font-weight: 500;
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }
    
    .btn-controle:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 12px rgba(0, 0, 0, 0.15);
    }
    
    .btn-iniciar {
      background: #17a2b8;
      border: none;
      color: white;
    }
    
    .btn-iniciar:hover {
      background: #138496;
    }
    
    .btn-reconvocar {
      background: #ffc107;
      border: none;
      color: #212529;
    }
    
    .btn-reconvocar:hover {
      background: #e0a800;
    }
    
    .btn-finalizar {
      background: #dc3545;
      border: none;
      color: white;
    }
    
    .btn-finalizar:hover {
      background: #c82333;
    }
    
    .senha-fila {
      background: #fff;
      border-left: 5px solid #6c757d;
      padding: 12px 15px;
      margin-bottom: 10px;
      border-radius: 8px;
      font-weight: 500;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      display: flex;
      justify-content: space-between;
      align-items: center;
      transition: all 0.3s ease;
    }
    
    .senha-fila:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .badge-fila {
      background: #6c757d;
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
    }
    
    .form-select, .form-check-input {
      border-radius: 8px;
      padding: 10px 15px;
      border: 1px solid #ced4da;
      box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.075);
    }
    
    .form-select:focus, .form-check-input:focus {
      border-color: #28a745;
      box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25);
    }
    
    .sem-senha {
      text-align: center;
      padding: 30px;
      color: #6c757d;
      font-style: italic;
    }
    
    .info-guiche {
      background: #28a745;
      color: white;
      padding: 5px 15px;
      border-radius: 20px;
      font-weight: bold;
      display: inline-block;
      margin-left: 10px;
    }
    
    .spinner-border {
      width: 1rem;
      height: 1rem;
      margin-right: 10px;
    }
    
    @media (max-width: 768px) {
      .config-card {
        margin: 20px;
      }
    }
  </style>
</head>
<body>

<!-- Configuração Inicial -->
<div id="config-inicial" class="container main-container">
  <div class="card config-card">
    <div class="card-header text-center">
      <h1 class="m-0"><i class="fas fa-headset me-2"></i>Painel do Atendente</h1>
    </div>
    <div class="card-body p-4">
      <form id="form-config">
        <div class="mb-4">
          <label for="localSelect" class="form-label fw-bold">Local de Atendimento</label>
          <select id="localSelect" class="form-select form-select-lg mb-3" required>
            <option value="" selected disabled>Selecione o local</option>
            <?php foreach ($locais as $local): ?>
              <option value="<?= $local['id'] ?>"><?= htmlspecialchars($local['nome']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div class="mb-4">
          <label for="guicheSelect" class="form-label fw-bold">Guichê de Atendimento</label>
          <select id="guicheSelect" class="form-select form-select-lg mb-3" required disabled>
            <option value="" selected disabled>Selecione o local primeiro</option>
          </select>
        </div>
        
        <div class="mb-4">
          <label class="form-label fw-bold">Filas para Atendimento</label>
          <div id="filasContainer" class="card p-3">
            <p class="text-muted">Selecione o local para ver as filas disponíveis</p>
          </div>
        </div>
        
        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-primary btn-lg">
            <i class="fas fa-play-circle me-2"></i>Iniciar Atendimento
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Tela de Atendimento -->
<div id="tela-atendimento" class="atendimento-container">
  <div class="atendente-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="m-0"><i class="fas fa-headset me-2"></i>Atendimento</h1>
        </div>
        <div class="col-md-6 text-end">
          <span id="info-display" class="fs-5"></span>
          <span id="guiche-display" class="info-guiche"></span>
          <button id="btn-config" class="btn btn-outline-light ms-3">
            <i class="fas fa-cog"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
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
              <!-- As filas serão exibidas aqui -->
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
  
  // Elementos DOM
  const configInicial = document.getElementById('config-inicial');
  const telaAtendimento = document.getElementById('tela-atendimento');
  const localSelect = document.getElementById('localSelect');
  const guicheSelect = document.getElementById('guicheSelect');
  const filasContainer = document.getElementById('filasContainer');
  const formConfig = document.getElementById('form-config');
  const infoDisplay = document.getElementById('info-display');
  const guicheDisplay = document.getElementById('guiche-display');
  const btnConfig = document.getElementById('btn-config');
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
  
  // Toast para notificações
  const toastEl = document.getElementById('notificacao');
  const toast = new bootstrap.Toast(toastEl);
  const toastTitulo = document.getElementById('toast-titulo');
  const toastMensagem = document.getElementById('toast-mensagem');
  
  // Mostrar notificação
  function mostrarNotificacao(titulo, mensagem, tipo = 'success') {
    toastTitulo.innerText = titulo;
    toastMensagem.innerText = mensagem;
    
    // Remover classes antigas
    toastEl.classList.remove('bg-success', 'bg-danger', 'bg-warning', 'text-white');
    
    // Adicionar classe baseada no tipo
    if (tipo === 'success') {
      toastEl.classList.add('bg-success', 'text-white');
    } else if (tipo === 'error') {
      toastEl.classList.add('bg-danger', 'text-white');
    } else if (tipo === 'warning') {
      toastEl.classList.add('bg-warning');
    }
    
    toast.show();
  }
  
  // Carregar guichês quando o local for selecionado
  localSelect.addEventListener('change', function() {
    localId = this.value;
    localNome = this.options[this.selectedIndex].text;
    guicheSelect.innerHTML = '<option value="" selected disabled>Carregando guichês...</option>';
    guicheSelect.disabled = true;
    
    fetch(`api_guiches.php?local_id=${localId}`)
      .then(response => response.json())
      .then(data => {
        if (data.length === 0) {
          guicheSelect.innerHTML = '<option value="" selected disabled>Nenhum guichê disponível</option>';
        } else {
          guicheSelect.innerHTML = '<option value="" selected disabled>Selecione o guichê</option>';
          data.forEach(guiche => {
            const option = document.createElement('option');
            option.value = guiche.id;
            option.textContent = guiche.nome;
            guicheSelect.appendChild(option);
          });
          guicheSelect.disabled = false;
        }
      })
      .catch(error => {
        console.error('Erro ao carregar guichês:', error);
        guicheSelect.innerHTML = '<option value="" selected disabled>Erro ao carregar guichês</option>';
      });
      
    // Carregar filas disponíveis
    filasContainer.innerHTML = '<p class="text-center"><div class="spinner-border text-success" role="status"></div> Carregando filas...</p>';
    
    fetch(`api_filas.php?local_id=${localId}`)
      .then(response => response.json())
      .then(data => {
        if (data.length === 0) {
          filasContainer.innerHTML = '<p class="text-muted">Nenhuma fila disponível para este local</p>';
        } else {
          filasContainer.innerHTML = '';
          data.forEach(fila => {
            const div = document.createElement('div');
            div.className = 'form-check mb-2';
            div.innerHTML = `
              <input class="form-check-input fila-check" type="checkbox" value="${fila.id}" id="fila-${fila.id}">
              <label class="form-check-label" for="fila-${fila.id}">
                ${fila.nome} <span class="badge bg-secondary">${fila.sigla || ''}</span>
              </label>
            `;
            filasContainer.appendChild(div);
          });
        }
      })
      .catch(error => {
        console.error('Erro ao carregar filas:', error);
        filasContainer.innerHTML = '<p class="text-danger">Erro ao carregar filas</p>';
      });
  });
  
  // Iniciar atendimento
  formConfig.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validar seleção de local e guichê
    if (!localId || !localSelect.value) {
      mostrarNotificacao('Atenção', 'Selecione um local de atendimento', 'warning');
      return;
    }
    
    if (!guicheSelect.value) {
      mostrarNotificacao('Atenção', 'Selecione um guichê de atendimento', 'warning');
      return;
    }
    
    // Obter guichê selecionado
    guicheId = guicheSelect.value;
    guicheNome = guicheSelect.options[guicheSelect.selectedIndex].text;
    
    // Obter filas selecionadas
    const checkboxes = document.querySelectorAll('.fila-check:checked');
    filasSelecionadas = Array.from(checkboxes).map(cb => cb.value);
    
    if (filasSelecionadas.length === 0) {
      mostrarNotificacao('Atenção', 'Selecione pelo menos uma fila para atendimento', 'warning');
      return;
    }
    
    // Iniciar tela de atendimento
    configInicial.style.display = 'none';
    telaAtendimento.style.display = 'block';
    
    // Atualizar informações na tela
    infoDisplay.textContent = localNome;
    guicheDisplay.textContent = `Guichê ${guicheNome}`;
    
    // Carregar próximas senhas
    carregarProximasSenhas();
  });
  
  
  // Voltar para configuração
  btnConfig.addEventListener('click', function() {
    if (senhaAtual) {
      if (!confirm('Existe uma senha em atendimento. Deseja realmente sair?')) {
        return;
      }
    }
    
    configInicial.style.display = 'block';
    telaAtendimento.style.display = 'none';
  });
  
  // Função para carregar próximas senhas nas filas
  function carregarProximasSenhas() {
    filasContainerEl.innerHTML = '<p class="text-center"><div class="spinner-border text-success" role="status"></div> Carregando senhas...</p>';
    
    const params = new URLSearchParams({
      local_id: localId,
      filas: filasSelecionadas.join(',')
    });
    
    fetch(`api_proximas_senhas.php?${params}`)
      .then(response => response.json())
      .then(data => {
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
          
          // Criar elementos para cada fila
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
              
              // Adicionar evento para chamar senha específica
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
      })
      .catch(error => {
        console.error('Erro ao carregar próximas senhas:', error);
        filasContainerEl.innerHTML = '<div class="text-danger p-3">Erro ao carregar senhas</div>';
      });
  }
  
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
  
  // Atualizar filas ao clicar no botão
  btnAtualizarFilas.addEventListener('click', carregarProximasSenhas);
  
  // Chamar próxima senha
  btnChamar.addEventListener('click', function() {
    // Verificar se já há uma senha em atendimento
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
        
        // Atualizar senha atual
        senhaAtual = data;
        atualizarTelaSenhaAtual();
        
        // Recarregar próximas senhas
        carregarProximasSenhas();
        
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
    // Verificar se já há uma senha em atendimento
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
        
        // Atualizar senha atual
        senhaAtual = data;
        atualizarTelaSenhaAtual();
        
        // Recarregar próximas senhas
        carregarProximasSenhas();
        
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
    
    // Atualizar estado dos botões
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
        
        // Atualizar dados da senha atual
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
        
        // Atualizar dados da senha atual
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
        
        // Limpar senha atual
        mostrarNotificacao('Sucesso', `Atendimento da senha ${senhaAtual.numero} finalizado!`);
        senhaAtual = null;
        atualizarTelaSenhaAtual();
        
        // Recarregar próximas senhas
        carregarProximasSenhas();
      })
      .catch(error => {
        console.error('Erro ao finalizar atendimento:', error);
        btnFinalizar.disabled = false;
        btnFinalizar.innerHTML = '<i class="fas fa-check-circle me-2"></i>Finalizar';
        mostrarNotificacao('Erro', 'Falha ao finalizar atendimento', 'error');
      });
  });
  
  // Inicializar - senha atual vazia
  atualizarTelaSenhaAtual();
</script>

</body>
</html>