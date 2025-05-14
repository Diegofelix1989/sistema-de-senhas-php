<?php
include_once __DIR__ . '/../includes/conexao.php';

// Busca os locais
$stmt = $pdo->query("SELECT id, nome FROM locais");
$locais = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sistema de Exibição de Senhas</title>
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
    }
    
    .seletor-card {
      max-width: 600px;
      margin: 100px auto;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      border-radius: 15px;
      border: none;
      background: linear-gradient(145deg, #ffffff, #f0f0f0);
    }
    
    .seletor-card .card-header {
      background: #007bff;
      color: white;
      border-radius: 15px 15px 0 0;
      padding: 15px;
    }
    
    .tela-exibicao {
      display: none;
      min-height: 100vh;
    }
    
    .painel-header {
      background: #007bff;
      color: white;
      padding: 15px 0;
      margin-bottom: 20px;
      border-radius: 0 0 10px 10px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    
    .painel-titulo {
      font-size: 1.8rem;
      font-weight: bold;
      margin: 0;
    }
    
    .card-senhas, .card-publicidade {
      height: calc(100vh - 150px);
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      border: none;
      margin-bottom: 20px;
      overflow: hidden;
    }
    
    .card-senhas .card-header, .card-publicidade .card-header {
      background: #007bff;
      color: white;
      font-weight: bold;
      padding: 15px;
      border-radius: 15px 15px 0 0;
    }
    
    .card-body-scroll {
      overflow-y: auto;
      height: calc(100% - 60px);
      padding: 20px;
    }
    
    .senha {
      background: #fff;
      border-left: 5px solid #007bff;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 8px;
      font-size: 1.6rem;
      font-weight: bold;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
      display: flex;
      justify-content: space-between;
      transition: all 0.3s ease;
    }
    
    .senha:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
    
    .senha-numero {
      color: #007bff;
    }
    
    .senha-guiche {
      background: #007bff;
      color: white;
      padding: 3px 12px;
      border-radius: 20px;
    }
    
    .midia-container {
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
      padding: 20px;
    }
    
    .midia {
      width: 100%;
      border-radius: 12px;
      overflow: hidden;
      background: #fff;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
      padding: 10px;
    }
    
    .midia img, .midia video {
      width: 100%;
      max-height: calc(100vh - 250px);
      object-fit: contain;
      border-radius: 8px;
    }
    
    .midia p {
      font-size: 2rem;
      padding: 30px;
      text-align: center;
      font-weight: bold;
      color: #333;
    }
    
    .btn-primary {
      background: #007bff;
      border: none;
      border-radius: 30px;
      padding: 10px 30px;
      font-weight: bold;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background: #0069d9;
      transform: translateY(-2px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }
    
    .select-local {
      border-radius: 30px;
      padding: 10px 20px;
      border: 2px solid #ddd;
      box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
      font-size: 1.1rem;
    }
    
    @media (max-width: 992px) {
      .card-senhas, .card-publicidade {
        height: auto;
      }
    }
  </style>
</head>
<body>

<!-- Tela de Seleção de Local -->
<div id="seletor-local" class="container main-container">
  <div class="card seletor-card">
    <div class="card-header text-center">
      <h1 class="m-0"><i class="fas fa-th-list me-2"></i>Sistema de Senhas</h1>
    </div>
    <div class="card-body text-center p-5">
      <h2 class="mb-4">Selecione o Local</h2>
      <select id="localSelect" class="form-select select-local mb-4">
        <?php foreach ($locais as $local): ?>
          <option value="<?= $local['id'] ?>"><?= htmlspecialchars($local['nome']) ?></option>
        <?php endforeach; ?>
      </select>
      <button class="btn btn-primary btn-lg" onclick="iniciarExibicao()">
        <i class="fas fa-play me-2"></i>Iniciar Exibição
      </button>
    </div>
  </div>
</div>

<!-- Tela de Exibição -->
<div id="tela-exibicao" class="tela-exibicao">
  <div class="painel-header">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-md-6">
          <h1 class="painel-titulo"><i class="fas fa-desktop me-2"></i>Painel de Senhas</h1>
        </div>
        <div class="col-md-6 text-end">
          <span id="local-display" class="fs-4"></span>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <!-- Painel de Senhas -->
      <div class="col-lg-6 mb-4">
        <div class="card card-senhas">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-ticket-alt me-2"></i>Senhas Chamadas</span>
            <span id="contagem-senhas" class="badge bg-light text-dark">0</span>
          </div>
          <div class="card-body-scroll" id="senhas-container">
            <!-- As senhas serão inseridas aqui -->
          </div>
        </div>
      </div>
      
      <!-- Painel de Publicidade -->
      <div class="col-lg-6 mb-4">
        <div class="card card-publicidade">
          <div class="card-header">
            <i class="fas fa-ad me-2"></i>Publicidade
          </div>
          <div class="midia-container" id="publicidade-container">
            <!-- Publicidade será exibida aqui -->
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let localId = null;
  let localNome = "";
  let publicidadesAtuais = [];
  let indexAtual = 0;
  let timeoutId = null;

  function iniciarExibicao() {
    const localSelect = document.getElementById('localSelect');
    localId = localSelect.value;
    localNome = localSelect.options[localSelect.selectedIndex].text;
    
    document.getElementById('seletor-local').style.display = 'none';
    document.getElementById('tela-exibicao').style.display = 'block';
    document.getElementById('local-display').textContent = localNome;

    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    }

    carregarSenhas();
    carregarPublicidades();
    setInterval(carregarSenhas, 5000);
    setInterval(carregarPublicidades, 30000); // atualiza a lista a cada 30s
  }

  function carregarSenhas() {
    fetch('api_senhas.php?local_id=' + localId)
      .then(res => res.json())
      .then(dados => {
        const container = document.getElementById('senhas-container');
        container.innerHTML = '';
        
        // Atualiza o contador de senhas
        document.getElementById('contagem-senhas').textContent = dados.length;
        
        dados.forEach(s => {
          container.innerHTML += `
            <div class="senha">
              <div class="senha-numero">Senha ${s.numero}</div>
              <div class="senha-guiche">Guichê ${s.guiche}</div>
            </div>`;
        });
        
        // Efeito de animação nas novas senhas
        const senhas = document.querySelectorAll('.senha');
        senhas.forEach((senha, index) => {
          senha.style.opacity = '0';
          senha.style.transform = 'translateY(20px)';
          setTimeout(() => {
            senha.style.opacity = '1';
            senha.style.transform = 'translateY(0)';
          }, index * 100);
        });
      });
  }

  function exibirProximaPublicidade() {
    const container = document.getElementById('publicidade-container');
    container.innerHTML = '';

    if (publicidadesAtuais.length === 0) {
      container.innerHTML = `
        <div class="midia">
          <p class="text-muted">Nenhuma publicidade disponível</p>
        </div>`;
      return;
    }

    const pub = publicidadesAtuais[indexAtual];
    const duracaoMs = (parseInt(pub.duracao) || 5) * 1000; // default: 5 segundos

    if (pub.tipo_midia === 'imagem') {
      container.innerHTML = `
        <div class="midia">
          <img src="${pub.media_path}" alt="${pub.titulo}" class="img-fluid">
        </div>`;
      timeoutId = setTimeout(exibirProximaPublicidade, duracaoMs);
    } else if (pub.tipo_midia === 'texto') {
      container.innerHTML = `
        <div class="midia">
          <p>${pub.titulo}</p>
        </div>`;
      timeoutId = setTimeout(exibirProximaPublicidade, duracaoMs);
    } else if (pub.tipo_midia === 'video') {
      container.innerHTML = `
        <div class="midia">
          <video 
            src="${pub.media_path}" 
            autoplay 
            muted 
            style="width: 100%; height: 100%; object-fit: contain;"
            class="video-fluid"
          ></video>
        </div>`;
      
      const video = container.querySelector('video');
      video.onended = () => exibirProximaPublicidade();
      video.onerror = () => {
        container.innerHTML = `
          <div class="midia">
            <p class="text-danger">Erro ao carregar o vídeo</p>
          </div>`;
        setTimeout(exibirProximaPublicidade, 3000);
      };
    }

    indexAtual = (indexAtual + 1) % publicidadesAtuais.length;
  }

  function carregarPublicidades() {
    fetch('api_publicidades.php?local_id=' + localId)
      .then(res => res.json())
      .then(novas => {
        const mudou = JSON.stringify(novas) !== JSON.stringify(publicidadesAtuais);
        if (mudou) {
          publicidadesAtuais = novas;
          indexAtual = 0;
          clearTimeout(timeoutId);
          exibirProximaPublicidade();
        }
      })
      .catch(error => {
        console.error('Erro ao carregar publicidades:', error);
        document.getElementById('publicidade-container').innerHTML = `
          <div class="midia">
            <p class="text-danger">Erro ao carregar publicidades</p>
          </div>`;
      });
  }
</script>

</body>
</html>