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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

    .card-publicidade, .card-historico { /* removido .card-senhas pois agora é um div simples */
      /*height: calc(100vh - 400px); /* Ajuste conforme a necessidade para ocupar a altura restante */
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      border: none;
      margin-bottom: 20px;
      overflow: hidden;
      display: flex; /* Para controlar o layout interno */
      flex-direction: column; /* Para cabeçalho e corpo */
    }

    .card-publicidade {
  height: calc(100vh - 110px); /* Exemplo: altura específica para publicidade */
  /* Você pode copiar outras propriedades que deseja manter específicas,
     ou deixar as comuns na regra compartilhada */
}

.card-historico {
  height: calc(100vh - 400px); /* Exemplo: altura específica para histórico */
  /* Você pode copiar outras propriedades que deseja manter específicas */
}

    .card-publicidade .card-header, .card-historico .card-header {
      background: #007bff;
      color: white;
      font-weight: bold;
      padding: 15px;
      border-radius: 15px 15px 0 0;
      flex-shrink: 0; /* Impede que o cabeçalho encolha */
    }

    .card-body-scroll {
      overflow-y: auto;
      flex-grow: 1; /* Permite que o corpo ocupe o espaço restante */
      padding: 20px;
    }
    
    /* Estilo para a senha em destaque */
    #senha-destaque-container {
      background: #ffffff;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      margin-bottom: 20px;
      padding: 30px;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      min-height: 180px; /* Altura mínima para o destaque */
      overflow: hidden;
    }

    .senha-destaque {
      font-size: 5rem; /* Senha grande */
      font-weight: bold;
      color: #dc3545; /* Cor de destaque (vermelho) */
      margin-bottom: 10px;
      text-shadow: 2px 2px 5px rgba(0,0,0,0.1);
      animation: fadeInZoom 0.8s ease-out; /* Animação ao aparecer */
    }

    .guiche-destaque {
      font-size: 2.5rem;
      font-weight: bold;
      color: #007bff;
      background-color: #e9ecef;
      padding: 10px 25px;
      border-radius: 50px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    @keyframes fadeInZoom {
      from {
        opacity: 0;
        transform: scale(0.8);
      }
      to {
        opacity: 1;
        transform: scale(1);
      }
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
      max-height: calc(100vh - 250px); /* Ajuste a altura máxima para a publicidade */
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

    /* Estilo para a tabela de histórico */
    .table-historico th, .table-historico td {
      vertical-align: middle;
      font-size: 0.95rem;
    }

    .table-historico th {
      background-color: #f8f9fa;
      color: #343a40;
    }

    .table-historico tbody tr:hover {
      background-color: #e9ecef;
    }
    
    .status-badge {
        padding: .35em .65em;
        border-radius: .375rem;
        font-size: 0.75em;
        font-weight: 700;
        line-height: 1;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        display: inline-block;
    }
    .status-em_atendimento { background-color: #28a745; color: white; } /* Verde para em atendimento */
    .status-atendida { background-color: #6c757d; color: white; } /* Cinza para atendida */

    /* Ajuste para telas menores */
    @media (max-width: 991.98px) {
      .card-publicidade, .card-historico {
        height: auto; /* Permite que cards se ajustem em telas menores */
      }
      .midia img, .midia video {
        max-height: 400px; /* Ajuste para não ocupar muito espaço em mobile */
      }
    }
  </style>
</head>
<body>

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
      <div class="col-lg-6">
        <div id="senha-destaque-container">
          <div class="senha-destaque" id="senha-destaque-numero">--</div>
          <div class="guiche-destaque" id="senha-destaque-guiche">Aguardando...</div>
        </div>

        <div class="card card-historico mb-4">
          <div class="card-header">
            <i class="fas fa-history me-2"></i>Últimas Senhas Chamadas
          </div>
          <div class="card-body-scroll">
            <table class="table table-striped table-hover table-historico">
              <thead>
                <tr>
                  <th>Senha</th>
                  <th>Guichê</th>
                  <th>Status</th>
                  <th>Hora</th>
                </tr>
              </thead>
              <tbody id="historico-senhas-container">
                </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <div class="col-lg-6">
        <div class="card card-publicidade mb-4">
          <div class="card-header">
            <i class="fas fa-ad me-2"></i>Publicidade
          </div>
          <div class="midia-container" id="publicidade-container">
            </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let localId = null;
  let localNome = "";
  let publicidadesAtuais = [];
  let indexAtual = 0;
  let timeoutId = null;
  let ultimaSenhaChamada = null; // Para controlar a chamada de áudio

  const audio = new Audio('ding.mp3'); // Certifique-se de ter um arquivo ding.mp3 no mesmo diretório

  function iniciarExibicao() {
    const localSelect = document.getElementById('localSelect');
    localId = localSelect.value;
    localNome = localSelect.options[localSelect.selectedIndex].text;
    
    document.getElementById('seletor-local').style.display = 'none';
    document.getElementById('tela-exibicao').style.display = 'block';
    document.getElementById('local-display').textContent = localNome;

    // Tenta entrar em modo tela cheia
    if (document.documentElement.requestFullscreen) {
      document.documentElement.requestFullscreen();
    } else if (document.documentElement.mozRequestFullScreen) { /* Firefox */
      document.documentElement.mozRequestFullScreen();
    } else if (document.documentElement.webkitRequestFullscreen) { /* Chrome, Safari & Opera */
      document.documentElement.webkitRequestFullscreen();
    } else if (document.documentElement.msRequestFullscreen) { /* IE/Edge */
      document.documentElement.msRequestFullscreen();
    }

    carregarSenhas();
    carregarPublicidades();
    setInterval(carregarSenhas, 3000); // Atualiza as senhas a cada 3 segundos
    setInterval(carregarPublicidades, 30000); // atualiza a lista de publicidades a cada 30s
  }

  function carregarSenhas() {
    // A API api_senhas.php precisa ser adaptada para retornar o histórico e o status.
    // A consulta ideal seria as últimas N senhas chamadas (ex: 6) com o status e a hora.
    fetch('api_senhas2.php?local_id=' + localId)
      .then(res => res.json())
      .then(dados => {
        // Filtrar as senhas 'em_atendimento' para o destaque
        const senhasEmAtendimento = dados.filter(s => s.status === 'em_atendimento');
        const historicoSenhas = dados; // Assumindo que 'dados' já vem com o histórico completo

        // Destaque da Última Senha Chamada
        const senhaDestaqueNumero = document.getElementById('senha-destaque-numero');
        const senhaDestaqueGuiche = document.getElementById('senha-destaque-guiche');
        
        let novaUltimaSenha = null;
        if (senhasEmAtendimento.length > 0) {
            // A senha mais recente 'em atendimento' é a que deve ser destacada
            novaUltimaSenha = senhasEmAtendimento[0]; // Assumindo que a API retorna ordenado por chamada_em DESC
        } else if (historicoSenhas.length > 0) {
            // Se não houver 'em atendimento', mostra a última atendida/chamada do histórico
            novaUltimaSenha = historicoSenhas[0];
        }


        if (novaUltimaSenha && (ultimaSenhaChamada === null || novaUltimaSenha.numero !== ultimaSenhaChamada.numero || novaUltimaSenha.guiche !== ultimaSenhaChamada.guiche)) {
            // Nova senha chamada ou alteração no guichê da última
            senhaDestaqueNumero.textContent = novaUltimaSenha.numero;
            senhaDestaqueGuiche.textContent = `Guichê ${novaUltimaSenha.guiche}`;
            
            // Adiciona classe para animação e remove depois de um tempo
            senhaDestaqueNumero.classList.remove('fadeInZoom'); // Remove para resetar a animação
            void senhaDestaqueNumero.offsetWidth; // Trigger reflow
            senhaDestaqueNumero.classList.add('fadeInZoom');
            
            audio.play().catch(e => console.error("Erro ao tocar áudio:", e)); // Toca o som

            ultimaSenhaChamada = novaUltimaSenha;
        } else if (!novaUltimaSenha && ultimaSenhaChamada !== null) {
            // Se não há mais senhas em atendimento e havia uma antes
            senhaDestaqueNumero.textContent = '--';
            senhaDestaqueGuiche.textContent = 'Aguardando...';
            ultimaSenhaChamada = null;
        } else if (ultimaSenhaChamada === null && novaUltimaSenha === null) {
             // Estado inicial ou sem senhas
            senhaDestaqueNumero.textContent = '--';
            senhaDestaqueGuiche.textContent = 'Aguardando...';
        }


        // Histórico de Senhas
        const historicoContainer = document.getElementById('historico-senhas-container');
        historicoContainer.innerHTML = '';
        
        // Exibir apenas as 6 últimas senhas do histórico
        historicoSenhas.slice(0, 6).forEach(s => {
          const statusClass = `status-${s.status}`; // Use o status diretamente na classe
          const statusText = s.status === 'em_atendimento' ? 'Em Atendimento' : 'Atendida';
          const horaChamada = new Date(s.chamada_em).toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

          historicoContainer.innerHTML += `
            <tr>
              <td>${s.numero}</td>
              <td>${s.guiche}</td>
              <td><span class="status-badge ${statusClass}">${statusText}</span></td>
              <td>${horaChamada}</td>
            </tr>`;
        });
      })
      .catch(error => {
        console.error('Erro ao carregar senhas:', error);
        document.getElementById('senha-destaque-numero').textContent = 'Erro';
        document.getElementById('senha-destaque-guiche').textContent = 'API';
        document.getElementById('historico-senhas-container').innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar histórico de senhas.</td></tr>`;
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

    // Limpa o timeout anterior antes de configurar um novo
    if (timeoutId) {
        clearTimeout(timeoutId);
    }

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
        setTimeout(exibirProximaPublicidade, 3000); // Tenta a próxima publicidade após 3 segundos em caso de erro
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
          indexAtual = 0; // Reinicia o índice para começar a exibir as novas publicidades do início
          clearTimeout(timeoutId); // Limpa qualquer timeout de publicidade anterior
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