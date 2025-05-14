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
  <title>Exibição de Senhas</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background: #000;
      color: #fff;
    }
    #seletor-local {
      padding: 50px;
      text-align: center;
    }
    #tela-exibicao {
      display: none;
      flex-direction: row;
      justify-content: space-around;
      padding: 40px;
      height: 100vh;
    }
    #lista-senhas, #publicidades {
      width: 45%;
    }
    #lista-senhas h2, #publicidades h2 {
      text-align: center;
    }
    .senha {
      font-size: 2em;
      margin: 10px;
      padding: 10px;
      background: #222;
      border-radius: 10px;
      text-align: center;
    }
    .midia {
      margin: 10px;
      text-align: center;
    }
    .midia img, .midia video {
      max-width: 100%;
      max-height: 300px;
    }
  </style>
</head>
<body>

<div id="seletor-local">
  <h1>Selecione o Local</h1>
  <select id="localSelect">
    <?php foreach ($locais as $local): ?>
      <option value="<?= $local['id'] ?>"><?= htmlspecialchars($local['nome']) ?></option>
    <?php endforeach; ?>
  </select>
  <br><br>
  <button onclick="iniciarExibicao()">Confirmar</button>
</div>

<div id="tela-exibicao">
  <div id="lista-senhas">
    <h2>Senhas Chamadas</h2>
    <div id="senhas-container"></div>
  </div>
  <div id="publicidades">
    <h2>Publicidade</h2>
    <div id="publicidade-container"></div>
  </div>
</div>

<script>
  let localId = null;
  let publicidadesAtuais = [];
  let indexAtual = 0;
  let timeoutId = null;

  function iniciarExibicao() {
    localId = document.getElementById('localSelect').value;

    document.getElementById('seletor-local').style.display = 'none';
    document.getElementById('tela-exibicao').style.display = 'flex';

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
        dados.forEach(s => {
          container.innerHTML += `<div class="senha">Senha ${s.numero} - Guichê ${s.guiche}</div>`;
        });
      });
  }

  function exibirProximaPublicidade() {
    const container = document.getElementById('publicidade-container');
    container.innerHTML = '';

    if (publicidadesAtuais.length === 0) return;

    const pub = publicidadesAtuais[indexAtual];
    const duracaoMs = (parseInt(pub.duracao) || 5) * 1000; // default: 5 segundos

    if (pub.tipo_midia === 'imagem') {
      container.innerHTML = `<div class="midia"><img src="${pub.media_path}" alt="${pub.titulo}"></div>`;
      timeoutId = setTimeout(exibirProximaPublicidade, duracaoMs);
    } else if (pub.tipo_midia === 'texto') {
      container.innerHTML = `<div class="midia"><p>${pub.titulo}</p></div>`;
      timeoutId = setTimeout(exibirProximaPublicidade, duracaoMs);
    } else if (pub.tipo_midia === 'video') {
      container.innerHTML = `
        <div class="midia">
          <video 
            src="${pub.media_path}" 
            autoplay 
            muted 
            style="width: 100%; height: 100%; object-fit: contain;"
          ></video>
        </div>`;
      
      const video = container.querySelector('video');
      video.onended = () => exibirProximaPublicidade();
      video.onerror = () => setTimeout(exibirProximaPublicidade, duracaoMs); // fallback
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
      .catch(error => console.error('Erro ao carregar publicidades:', error));
  }
</script>

</body>
</html>
