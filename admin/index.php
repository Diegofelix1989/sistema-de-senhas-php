

<?php include 'header.php'; ?>
<?php include 'sidebar.php'; ?>

<!-- Dashboard Moderno -->
    <div class="flex-grow-1">
    <div class="p-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <h4 class="mb-0">Dashboard Administrativo</h4>
            <div>
                <label for="dashboard-local-select" class="form-label mb-0 me-2">Filtrar por Local:</label>
                <select id="dashboard-local-select" class="form-select d-inline-block w-auto">
                    <option value="">Todos os Locais</option>
                </select>
            </div>
        </div>
        <div id="dashboard-filas-cards" class="row g-3 mb-4">
            <!-- Cards de filas agrupados por tipo -->
        </div>
        <div id="dashboard-filas-tabelas" class="row g-3">
            <!-- Tabelas de últimas senhas por fila -->
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>

<!-- Ícones Bootstrap -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function formatarDataHora(dataStr) {
    if (!dataStr) return '';
    const d = new Date(dataStr.replace(' ', 'T'));
    return d.toLocaleString('pt-BR');
}

function renderDashboard(localId = '') {
    fetch('dashboard_controller.php' + (localId ? ('?local_id=' + localId) : ''))
        .then(res => res.json())
        .then(data => {
            // Preencher seletor de local
            const select = document.getElementById('dashboard-local-select');
            if (select.options.length <= 1) {
                data.locais.forEach(l => {
                    const opt = document.createElement('option');
                    opt.value = l.id;
                    opt.textContent = l.nome;
                    select.appendChild(opt);
                });
            }
            if (localId) select.value = localId;

            // Agrupar filas por tipo
            const tipos = { 'comum': 'Normal', 'prioritaria': 'Prioritária' };
            const filasPorTipo = { comum: [], prioritaria: [] };
            data.filas.forEach(f => filasPorTipo[f.tipo].push(f));

            // Cards de filas agrupados
            let cardsHtml = '';
            Object.entries(filasPorTipo).forEach(([tipo, filas]) => {
                if (filas.length === 0) return;
                cardsHtml += `<div class='col-12'><h5 class='mt-3 mb-2 text-${tipo === 'comum' ? 'primary' : 'warning'}'>${tipos[tipo]}</h5></div>`;
                filas.forEach(fila => {
                    cardsHtml += `
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-dark me-2">${fila.prefixo}</span>
                                        <h5 class="mb-0">${fila.nome}</h5>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="text-center flex-fill">
                                            <div class="fw-bold fs-4 text-warning">${fila.aguardando}</div>
                                            <div class="small text-muted">Aguardando</div>
                                        </div>
                                        <div class="vr mx-3"></div>
                                        <div class="text-center flex-fill">
                                            <div class="fw-bold fs-4 text-info">${fila.em_atendimento}</div>
                                            <div class="small text-muted">Em Atendimento</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
            });
            document.getElementById('dashboard-filas-cards').innerHTML = cardsHtml;

            // Tabelas de últimas senhas por fila
            let tabelasHtml = '';
            data.filas.forEach(fila => {
                const ultimas = data.ultimasPorFila[fila.id] || [];
                tabelasHtml += `
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-3">Últimas Senhas Atendidas - <span class="badge bg-dark">${fila.prefixo}</span> ${fila.nome}</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Senha</th>
                                                <th>Guichê</th>
                                                <th>Finalizada em</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${ultimas.length === 0 ? `<tr><td colspan='4' class='text-center'>Nenhuma senha atendida</td></tr>` : ultimas.map(s => `
                                                <tr>
                                                    <td>${s.id}</td>
                                                    <td><span class="badge bg-dark">${fila.prefixo}${String(s.numero).padStart(3, '0')}</span></td>
                                                    <td>${s.guiche_nome || '-'}</td>
                                                    <td>${formatarDataHora(s.atendimento_finalizado_em)}</td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            document.getElementById('dashboard-filas-tabelas').innerHTML = tabelasHtml;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    renderDashboard();
    document.getElementById('dashboard-local-select').addEventListener('change', function() {
        renderDashboard(this.value);
    });
});
</script>
</body>
</html>
