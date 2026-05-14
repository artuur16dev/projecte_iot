/**
 * SmartSchool IoT – Dashboard JavaScript
 */

const API_BASE = './api';

// Navegació per pestanyes
const navBtns = document.querySelectorAll('.nav-btn');
const tabs    = document.querySelectorAll('.tab');

navBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    navBtns.forEach(b => b.classList.remove('active'));
    tabs.forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
  });
});

// Canvi de tema
const themeBtn = document.querySelector('[data-theme-toggle]');
const html     = document.documentElement;
let tema = 'light';

themeBtn.addEventListener('click', () => {
  tema = tema === 'light' ? 'dark' : 'light';
  html.setAttribute('data-theme', tema);
  themeBtn.textContent = tema === 'dark' ? '☀️' : '🌙';
});

// Helpers
function formatHora(ts) {
  if (!ts) return '–';
  const d = new Date(ts.replace(' ', 'T'));
  return d.toLocaleTimeString('ca-ES', { hour: '2-digit', minute: '2-digit' });
}

function classeMetric(valor, llindar1, llindar2) {
  if (valor >= llindar2) return 'err';
  if (valor >= llindar1) return 'warn';
  return 'ok';
}

// Aules
async function carregarAules() {
  const grid = document.getElementById('aules-grid');
  try {
    const res  = await fetch(API_BASE + '/get_data.php?accio=ambient');
    const data = await res.json();

    if (!data.ok || !data.dades.length) {
      grid.innerHTML = '<p class="page-desc">Sense dades disponibles.</p>';
      return;
    }

    grid.innerHTML = data.dades.map(aula => {
      const alerta = aula.alerta !== 'cap';
      const ocup   = parseInt(aula.presencia) === 1;
      const badgeClass = alerta ? 'badge-alerta' : ocup ? 'badge-ok' : 'badge-buit';
      const badgeText  = alerta ? '⚠ Alerta'    : ocup ? '● Ocupada' : '○ Buida';
      const clsTemp = classeMetric(parseFloat(aula.temperatura), 25, 28);
      const clsCo2  = classeMetric(parseInt(aula.co2), 700, 1000);
      const clsHum  = classeMetric(parseFloat(aula.humitat), 60, 75);

      return `
      <div class="card">
        <div class="card-header">
          <span class="card-title">Aula ${aula.aula_id}</span>
          <span class="card-badge ${badgeClass}">${badgeText}</span>
        </div>
        <div class="card-metrics">
          <div class="metric">
            <span class="metric-label">Temperatura</span>
            <span class="metric-value ${clsTemp}">${parseFloat(aula.temperatura).toFixed(1)} °C</span>
          </div>
          <div class="metric">
            <span class="metric-label">Humitat</span>
            <span class="metric-value ${clsHum}">${parseFloat(aula.humitat).toFixed(1)} %</span>
          </div>
          <div class="metric">
            <span class="metric-label">CO₂ (aprox.)</span>
            <span class="metric-value ${clsCo2}">${aula.co2} ppm</span>
          </div>
          <div class="metric">
            <span class="metric-label">Llum</span>
            <span class="metric-value">${aula.llum}</span>
          </div>
        </div>
        <p style="font-size:0.75rem;color:var(--muted);margin-top:0.75rem">Actualitzat: ${formatHora(aula.timestamp)}</p>
      </div>`;
    }).join('');

  } catch (e) {
    grid.innerHTML = '<p style="color:var(--err)">Error carregant dades.</p>';
  }
}

// Assistència
async function carregarAssistencia() {
  const tbody = document.querySelector('#taula-assistencia tbody');
  try {
    const res  = await fetch(API_BASE + '/get_data.php?accio=assistencia');
    const data = await res.json();

    if (!data.ok || !data.dades.length) {
      tbody.innerHTML = '<tr><td colspan="4" style="color:var(--muted)">Cap registre avui.</td></tr>';
      return;
    }

    tbody.innerHTML = data.dades.map(r => `
      <tr>
        <td>${formatHora(r.timestamp)}</td>
        <td>${r.aula_id}</td>
        <td>${r.alumne || '<span style="color:var(--muted)">Desconegut</span>'}</td>
        <td style="font-family:monospace;font-size:0.8rem">${r.uid_rfid}</td>
      </tr>
    `).join('');

  } catch (e) {
    tbody.innerHTML = '<tr><td colspan="4" style="color:var(--err)">Error carregant dades.</td></tr>';
  }
}

// Alertes
async function carregarAlertes() {
  const div = document.getElementById('llista-alertes');
  try {
    const res  = await fetch(API_BASE + '/get_data.php?accio=alertes');
    const data = await res.json();

    if (!data.ok || !data.dades.length) {
      div.innerHTML = '<p class="page-desc">Cap alerta recent. ✅</p>';
      return;
    }

    div.innerHTML = data.dades.map(a => {
      const isCo2  = a.alerta.includes('co2');
      const icon   = isCo2 ? '🫁' : '🌡️';
      const text   = isCo2 ? 'CO₂ alt' : 'Temperatura alta';
      return `
      <div class="alerta-item">
        <span class="alerta-icon">${icon}</span>
        <div class="alerta-text">
          <strong>${text} – Aula ${a.aula_id}</strong>
          <span>${formatHora(a.timestamp)}</span>
        </div>
      </div>`;
    }).join('');

  } catch (e) {
    div.innerHTML = '<p style="color:var(--err)">Error carregant alertes.</p>';
  }
}

// Inici i actualització automàtica
carregarAules();
carregarAssistencia();
carregarAlertes();

setInterval(carregarAules,       10000);
setInterval(carregarAssistencia, 15000);
setInterval(carregarAlertes,     20000);
